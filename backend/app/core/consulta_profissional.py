"""
Consulta de profissional para validação no formulário de cadastro do Vivências.

TODO (migração futura): este endpoint deve ser absorvido por `api.cfn.org.br`
(projeto api-cfn). Por ora, vive aqui e consulta o MySQL `db_list_nutricionistas`
(migrado do CNN) via rede Docker interna.

O frontend (`enviar_valida_profissional.php`) envia:
    regional (int), registro (str), nome (str maiúsculo), cpf (só dígitos)

E espera como resposta um ARRAY JSON com 0 ou 1 profissionais. O PHP
encapsula em {"sucesso": true/false, "dados": result[0] ?? null}.
"""
import hashlib
import re
from fastapi import HTTPException

from db_cnn import conectar


MD5_REGEX = re.compile(r"^[a-fA-F0-9]{32}$")


def _normalizar_cpf(cpf: str) -> str:
    return re.sub(r"\D", "", cpf or "")


def _gerar_md5(texto: str) -> str:
    return hashlib.md5(texto.encode("utf-8")).hexdigest()


def consulta_profissional(regional: int, registro: str, nome: str, cpf: str):
    if not (1 <= regional <= 11):
        raise HTTPException(
            status_code=400,
            detail="Regional inválida. Informe CRN entre 1 e 11."
        )

    cpf_numerico = _normalizar_cpf(cpf)
    if len(cpf_numerico) != 11 and not MD5_REGEX.fullmatch(cpf or ""):
        raise HTTPException(
            status_code=400,
            detail="CPF inválido. Informe CPF com 11 dígitos ou hash MD5."
        )

    cpf_md5 = cpf if MD5_REGEX.fullmatch(cpf or "") else _gerar_md5(cpf_numerico)
    sigla = f"CRN-{regional}"

    conn = None
    cur = None

    try:
        conn = conectar()
        cur = conn.cursor()

        cur.execute("""
            SELECT
                sigla       AS regional_sigla,
                estado,
                situacao,
                tipo_registro
            FROM nutricionista_implanta
            WHERE sigla = %s
              AND cpf_decodificado = %s
            LIMIT 1
        """, (sigla, cpf_md5))
        implanta = cur.fetchone()

        cur.execute("""
            SELECT
                regional,
                cep,
                data_inscricao,
                situacao,
                tipo_registro
            FROM nutricionista_incorp
            WHERE regional = %s
              AND cpf_decodificado = %s
            LIMIT 1
        """, (regional, cpf_md5))
        incorp = cur.fetchone()

        if not implanta and not incorp:
            return []

        dados = {
            "regional": regional,
            "registro": registro,
            "nome": nome,
            "situacao": (implanta or {}).get("situacao") or (incorp or {}).get("situacao"),
            "tipo_registro": (implanta or {}).get("tipo_registro") or (incorp or {}).get("tipo_registro"),
            "estado": (implanta or {}).get("estado"),
            "data_inscricao": str((incorp or {}).get("data_inscricao")) if incorp and incorp.get("data_inscricao") else None,
            "origem": {
                "implanta": bool(implanta),
                "incorp": bool(incorp)
            }
        }

        return [dados]

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao consultar profissional: {str(e)}")

    finally:
        if cur:
            cur.close()
        if conn:
            conn.close()
