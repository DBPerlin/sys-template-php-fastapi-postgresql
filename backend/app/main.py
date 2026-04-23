from fastapi import FastAPI

from core.router import router as core_router
from vivencias.router import router as vivencias_router


app = FastAPI(
    title="Vivências em Nutrição — API",
    description="Backend do sistema de Experiências Exitosas na Nutrição (CFN)",
    version="1.0"
)

app.include_router(core_router)
app.include_router(vivencias_router)


@app.get("/")
def root():
    return {
        "status": "ok",
        "servico": "Vivências em Nutrição API"
    }
