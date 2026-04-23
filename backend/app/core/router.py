from fastapi import APIRouter, HTTPException
from pydantic import BaseModel

from core.auth import validar_usuario_senha, cadastrar_usuario
from core.consulta_profissional import consulta_profissional


router = APIRouter(prefix="/core", tags=["Core"])


class ValidarUsuarioRequest(BaseModel):
    nome: str
    senha: str


class CadastrarUsuarioRequest(BaseModel):
    nome: str
    senha: str


@router.get("/")
def listar_rotas_core():
    return {
        "modulo": "core",
        "rotas": [
            "POST /core/validar_usuario",
            "POST /core/cadastrar_usuario",
            "GET /core/consulta-profissional",
        ]
    }


@router.post("/validar_usuario")
def validar_usuario(payload: ValidarUsuarioRequest):
    try:
        return validar_usuario_senha(nome=payload.nome, senha=payload.senha)
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao validar usuário: {str(e)}")


@router.post("/cadastrar_usuario")
def cadastrar_usuario_route(payload: CadastrarUsuarioRequest):
    try:
        return cadastrar_usuario(nome=payload.nome, senha=payload.senha)
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao cadastrar usuário: {str(e)}")


@router.get("/consulta-profissional")
def consulta_profissional_route(
    regional: int,
    registro: str = "",
    nome: str = "",
    cpf: str = ""
):
    try:
        return consulta_profissional(
            regional=regional,
            registro=registro,
            nome=nome,
            cpf=cpf
        )
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao consultar profissional: {str(e)}")
