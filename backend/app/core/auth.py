import hashlib
import hmac
import os
from fastapi import Header, HTTPException

from db_core import conectar


def _gerar_md5(texto: str) -> str:
    return hashlib.md5(texto.encode("utf-8")).hexdigest()


def exigir_admin_secret(x_admin_secret: str = Header(None)):
    esperado = os.getenv("ADMIN_API_SECRET", "")
    if not esperado:
        raise HTTPException(status_code=503, detail="ADMIN_API_SECRET não configurado no backend")
    if not x_admin_secret or not hmac.compare_digest(x_admin_secret, esperado):
        raise HTTPException(status_code=403, detail="Acesso negado")


def validar_usuario_senha(nome: str, senha: str):
    conn = None
    cur = None

    try:
        conn = conectar()
        cur = conn.cursor()

        senha_md5 = _gerar_md5(senha)

        cur.execute("""
            SELECT id, id_membro, nome, ativo
            FROM usuario
            WHERE nome = %s
              AND senha_hash = %s
              AND ativo = TRUE
            LIMIT 1
        """, (nome, senha_md5))

        row = cur.fetchone()

        if not row:
            return {
                "sucesso": False,
                "mensagem": "Usuário ou senha inválidos"
            }

        return {
            "sucesso": True,
            "mensagem": "Usuário validado com sucesso",
            "usuario": {
                "id": row[0],
                "id_membro": row[1],
                "nome": row[2],
                "ativo": row[3]
            }
        }

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao validar usuário: {str(e)}")

    finally:
        if cur:
            cur.close()
        if conn:
            conn.close()


def cadastrar_usuario(nome: str, senha: str):
    conn = None
    cur = None

    try:
        conn = conectar()
        cur = conn.cursor()

        cur.execute("SELECT id FROM usuario WHERE nome = %s LIMIT 1", (nome,))
        if cur.fetchone():
            return {
                "sucesso": False,
                "mensagem": "Já existe um usuário com esse nome"
            }

        cur.execute("""
            INSERT INTO membro (nome, id_tipo)
            VALUES (%s, %s)
            RETURNING id
        """, (nome, 0))
        id_membro = cur.fetchone()[0]

        senha_md5 = _gerar_md5(senha)

        cur.execute("""
            INSERT INTO usuario (id_membro, nome, senha_hash, ativo)
            VALUES (%s, %s, %s, %s)
            RETURNING id
        """, (id_membro, nome, senha_md5, True))
        id_usuario = cur.fetchone()[0]

        conn.commit()

        return {
            "sucesso": True,
            "mensagem": "Usuário cadastrado com sucesso",
            "usuario": {
                "id": id_usuario,
                "id_membro": id_membro,
                "nome": nome,
                "ativo": True
            }
        }

    except Exception as e:
        if conn:
            conn.rollback()
        raise HTTPException(status_code=500, detail=f"Erro ao cadastrar usuário: {str(e)}")

    finally:
        if cur:
            cur.close()
        if conn:
            conn.close()
