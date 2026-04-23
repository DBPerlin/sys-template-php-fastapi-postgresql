import os
import uuid
import hmac
import hashlib
from pathlib import Path
from typing import Optional

from fastapi import HTTPException, UploadFile

from db_sistemas import conectar


UPLOAD_DIR = Path("/app/uploads/vivencias")
TIPOS_PERMITIDOS = {
    "application/pdf": ".pdf",
    "image/jpeg": ".jpg",
    "image/png": ".png",
}
TAMANHO_MAXIMO = 10 * 1024 * 1024  # 10 MB


def _normalizar_cpf(cpf: str) -> str:
    return "".join(ch for ch in cpf if ch.isdigit())


def _gerar_hash_cpf(cpf: str) -> str:
    cpf_normalizado = _normalizar_cpf(cpf)

    chave = os.getenv("CPF_HASH_SECRET")
    if not chave:
        raise HTTPException(
            status_code=500,
            detail="Variável de ambiente CPF_HASH_SECRET não configurada."
        )

    return hmac.new(
        chave.encode("utf-8"),
        cpf_normalizado.encode("utf-8"),
        hashlib.sha256
    ).hexdigest()


def _garantir_pasta_upload():
    UPLOAD_DIR.mkdir(parents=True, exist_ok=True)


async def _salvar_arquivo_upload(arquivo: UploadFile) -> dict:
    if not arquivo.content_type or arquivo.content_type not in TIPOS_PERMITIDOS:
        raise HTTPException(
            status_code=400,
            detail="Arquivo inválido. Envie apenas PDF, JPG ou PNG."
        )

    _garantir_pasta_upload()

    conteudo = await arquivo.read()

    if len(conteudo) > TAMANHO_MAXIMO:
        raise HTTPException(
            status_code=400,
            detail="Arquivo excede o tamanho máximo permitido de 10 MB."
        )

    extensao = TIPOS_PERMITIDOS[arquivo.content_type]
    nome_arquivo = f"{uuid.uuid4()}{extensao}"
    caminho_arquivo = UPLOAD_DIR / nome_arquivo

    with open(caminho_arquivo, "wb") as f:
        f.write(conteudo)

    return {
        "nome_original": arquivo.filename or nome_arquivo,
        "nome_arquivo": nome_arquivo,
        "caminho_arquivo": str(caminho_arquivo),
        "content_type": arquivo.content_type,
        "tamanho_bytes": len(conteudo)
    }


async def salvar_nova_experiencia(
    crn_id: int,
    nome_completo: str,
    cpf: str,
    inscricao: str,
    estado_id: int,
    titulo_trabalho: str,
    area_nutricao: str,
    telefone: str,
    email: str,
    objetivo_trabalho: Optional[str] = None,
    acoes_trabalho: Optional[str] = None,
    resultados_trabalho: Optional[str] = None,
    arquivo: Optional[UploadFile] = None
):
    conn = None
    cur = None

    try:
        cpf_hash = _gerar_hash_cpf(cpf)
        arquivo_info = None

        if arquivo:
            arquivo_info = await _salvar_arquivo_upload(arquivo)

        conn = conectar()
        cur = conn.cursor()

        cur.execute("""
            INSERT INTO vivencias_experiencia (
                crn_id, nome_completo, cpf, inscricao, estado_id,
                titulo_trabalho, area_nutricao, telefone, email,
                objetivo_trabalho, acoes_trabalho, resultados_trabalho
            )
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            RETURNING id
        """, (
            crn_id, nome_completo, cpf_hash, inscricao, estado_id,
            titulo_trabalho, area_nutricao, telefone, email,
            objetivo_trabalho, acoes_trabalho, resultados_trabalho
        ))
        id_experiencia = cur.fetchone()[0]

        id_arquivo = None
        if arquivo_info:
            cur.execute("""
                INSERT INTO vivencias_experiencia_arquivo (
                    id_vivencia_experiencia, nome_original, nome_arquivo,
                    caminho_arquivo, content_type, tamanho_bytes
                )
                VALUES (%s, %s, %s, %s, %s, %s)
                RETURNING id
            """, (
                id_experiencia,
                arquivo_info["nome_original"],
                arquivo_info["nome_arquivo"],
                arquivo_info["caminho_arquivo"],
                arquivo_info["content_type"],
                arquivo_info["tamanho_bytes"]
            ))
            id_arquivo = cur.fetchone()[0]

        conn.commit()

        return {
            "status": "ok",
            "mensagem": "Experiência cadastrada com sucesso",
            "id": id_experiencia,
            "arquivo": {
                "enviado": bool(arquivo_info),
                "id_arquivo": id_arquivo
            }
        }

    except HTTPException:
        if conn:
            conn.rollback()
        raise

    except Exception as e:
        if conn:
            conn.rollback()
        raise HTTPException(status_code=500, detail=f"Erro ao salvar experiência: {str(e)}")

    finally:
        if cur:
            cur.close()
        if conn:
            conn.close()


def buscar_arquivo_experiencia(id: int):
    conn = None
    cur = None

    try:
        conn = conectar()
        cur = conn.cursor()

        cur.execute("""
            SELECT nome_original, nome_arquivo, caminho_arquivo, content_type
            FROM vivencias_experiencia_arquivo
            WHERE id_vivencia_experiencia = %s
        """, (id,))
        row = cur.fetchone()

        if not row:
            raise HTTPException(status_code=404, detail="Arquivo não encontrado para esta experiência.")

        return {
            "nome_original": row[0],
            "nome_arquivo": row[1],
            "caminho_arquivo": row[2],
            "content_type": row[3]
        }

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao buscar arquivo: {str(e)}")

    finally:
        if cur:
            cur.close()
        if conn:
            conn.close()


def alterar_status_experiencia(id: int, status: int):
    if status not in (0, 1, 2):
        raise HTTPException(status_code=400, detail="Status inválido. Use 0, 1 ou 2.")

    conn = None
    cur = None

    try:
        conn = conectar()
        cur = conn.cursor()

        cur.execute("SELECT status FROM vivencias_experiencia WHERE id = %s", (id,))
        row = cur.fetchone()

        if not row:
            raise HTTPException(status_code=404, detail="Experiência não encontrada.")

        status_anterior = row[0]

        if status_anterior == status:
            return {"sucesso": True, "mensagem": "Status já é o mesmo, nenhuma alteração feita."}

        cur.execute(
            "UPDATE vivencias_experiencia SET status = %s, atualizado_em = CURRENT_TIMESTAMP WHERE id = %s",
            (status, id)
        )

        cur.execute("""
            INSERT INTO vivencias_experiencia_status_historico
                (id_vivencia_experiencia, status_anterior, status_novo)
            VALUES (%s, %s, %s)
        """, (id, status_anterior, status))

        conn.commit()

        return {"sucesso": True, "mensagem": "Status atualizado com sucesso."}

    except HTTPException:
        if conn:
            conn.rollback()
        raise
    except Exception as e:
        if conn:
            conn.rollback()
        raise HTTPException(status_code=500, detail=f"Erro ao alterar status: {str(e)}")

    finally:
        if cur:
            cur.close()
        if conn:
            conn.close()


def listar_experiencias():
    conn = None
    cur = None

    try:
        conn = conectar()
        cur = conn.cursor()

        cur.execute("""
            SELECT
                ve.id, ve.crn_id, ve.nome_completo, ve.cpf, ve.inscricao,
                ve.estado_id, ve.titulo_trabalho, ve.area_nutricao,
                ve.telefone, ve.email, ve.objetivo_trabalho, ve.acoes_trabalho,
                ve.resultados_trabalho, ve.status, ve.criado_em, ve.atualizado_em,
                CASE WHEN vea.id IS NOT NULL THEN TRUE ELSE FALSE END AS possui_arquivo
            FROM vivencias_experiencia ve
            LEFT JOIN vivencias_experiencia_arquivo vea
                ON vea.id_vivencia_experiencia = ve.id
            ORDER BY ve.id DESC
        """)

        colunas = [desc[0] for desc in cur.description]
        linhas = cur.fetchall()

        experiencias = []
        for linha in linhas:
            item = dict(zip(colunas, linha))
            if item.get("criado_em"):
                item["criado_em"] = item["criado_em"].isoformat()
            if item.get("atualizado_em"):
                item["atualizado_em"] = item["atualizado_em"].isoformat()
            experiencias.append(item)

        return {
            "status": "ok",
            "total": len(experiencias),
            "dados": experiencias
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao consultar experiências: {str(e)}")

    finally:
        if cur:
            cur.close()
        if conn:
            conn.close()


def listar_experiencias_por_crn(crn_id: int):
    conn = None
    cur = None

    try:
        conn = conectar()
        cur = conn.cursor()

        cur.execute("""
            SELECT
                ve.id, ve.crn_id, ve.nome_completo, ve.cpf, ve.inscricao,
                ve.estado_id, ve.titulo_trabalho, ve.area_nutricao,
                ve.telefone, ve.email, ve.objetivo_trabalho, ve.acoes_trabalho,
                ve.resultados_trabalho, ve.status, ve.criado_em, ve.atualizado_em,
                EXISTS (
                    SELECT 1 FROM vivencias_experiencia_arquivo vea
                    WHERE vea.id_vivencia_experiencia = ve.id
                ) AS possui_arquivo
            FROM vivencias_experiencia ve
            WHERE ve.crn_id = %s
            ORDER BY ve.id DESC
        """, (crn_id,))

        colunas = [desc[0] for desc in cur.description]
        linhas = cur.fetchall()

        experiencias = []
        for linha in linhas:
            item = dict(zip(colunas, linha))
            if item.get("criado_em"):
                item["criado_em"] = item["criado_em"].isoformat()
            if item.get("atualizado_em"):
                item["atualizado_em"] = item["atualizado_em"].isoformat()
            experiencias.append(item)

        return {
            "status": "ok",
            "crn_id": crn_id,
            "total": len(experiencias),
            "dados": experiencias
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao consultar experiências por CRN: {str(e)}")

    finally:
        if cur:
            cur.close()
        if conn:
            conn.close()
