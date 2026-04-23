from pathlib import Path
from typing import Optional

from fastapi import APIRouter, HTTPException, UploadFile, File, Form
from fastapi.responses import FileResponse
from pydantic import BaseModel, EmailStr

from vivencias.nova_exp import (
    salvar_nova_experiencia,
    listar_experiencias,
    listar_experiencias_por_crn,
    alterar_status_experiencia,
    buscar_arquivo_experiencia,
)


router = APIRouter(prefix="/vivencias", tags=["Vivências"])


class AlterarStatusRequest(BaseModel):
    id: int
    status: int


@router.get("/")
def listar_rotas_vivencias():
    return {
        "modulo": "vivencias",
        "rotas": [
            "POST /vivencias/nova_experiencia",
            "POST /vivencias/alterar_status",
            "GET  /vivencias/lista_experiencias",
            "GET  /vivencias/lista_experiencias/{crn_id}",
            "GET  /vivencias/arquivo_experiencia/{id}",
        ]
    }


@router.post("/nova_experiencia")
async def nova_experiencia(
    crn_id: int = Form(...),
    nome_completo: str = Form(...),
    cpf: str = Form(...),
    inscricao: str = Form(...),
    estado_id: int = Form(...),
    titulo_trabalho: str = Form(...),
    area_nutricao: str = Form(...),
    telefone: str = Form(...),
    email: EmailStr = Form(...),
    objetivo_trabalho: Optional[str] = Form(None),
    acoes_trabalho: Optional[str] = Form(None),
    resultados_trabalho: Optional[str] = Form(None),
    arquivo: Optional[UploadFile] = File(None)
):
    try:
        return await salvar_nova_experiencia(
            crn_id=crn_id,
            nome_completo=nome_completo,
            cpf=cpf,
            inscricao=inscricao,
            estado_id=estado_id,
            titulo_trabalho=titulo_trabalho,
            area_nutricao=area_nutricao,
            telefone=telefone,
            email=str(email),
            objetivo_trabalho=objetivo_trabalho,
            acoes_trabalho=acoes_trabalho,
            resultados_trabalho=resultados_trabalho,
            arquivo=arquivo
        )
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao criar experiência: {str(e)}")


@router.post("/alterar_status")
def alterar_status(body: AlterarStatusRequest):
    try:
        return alterar_status_experiencia(body.id, body.status)
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao alterar status: {str(e)}")


@router.get("/arquivo_experiencia/{id}")
def arquivo_experiencia(id: int):
    try:
        info = buscar_arquivo_experiencia(id)
        caminho = Path(info["caminho_arquivo"])

        if not caminho.exists():
            raise HTTPException(status_code=404, detail="Arquivo não encontrado no servidor.")

        return FileResponse(
            path=str(caminho),
            media_type=info["content_type"],
            filename=info["nome_original"]
        )
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao baixar arquivo: {str(e)}")


@router.get("/lista_experiencias")
def lista_experiencias():
    try:
        return listar_experiencias()
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao listar experiências: {str(e)}")


@router.get("/lista_experiencias/{crn_id}")
def lista_experiencias_por_crn_route(crn_id: int):
    try:
        return listar_experiencias_por_crn(crn_id)
    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Erro ao listar experiências do CRN {crn_id}: {str(e)}")
