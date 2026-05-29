"""
RYTMIA – Microservicio Python para generación de PDFs
Expone endpoints que consumen la API Laravel y devuelven PDFs listos para imprimir.
"""

from fastapi import FastAPI, HTTPException, Query
from fastapi.responses import StreamingResponse
import requests
import io
from pdf_generator import generar_pdf_lista_conjunto, generar_pdf_lista_gimnastas_club

app = FastAPI(
    title="RYTMIA PDF Service",
    description="Genera PDFs de listas de conjuntos y gimnastas",
    version="1.0.0",
)

# ──────────────────────────────────────────────────────────────
# Configuración: base URL de la API Laravel y token Sanctum
# ──────────────────────────────────────────────────────────────

LARAVEL_BASE_URL = "http://localhost:8000/api"  # Cambia según tu entorno


def get_headers(token: str) -> dict:
    return {
        "Authorization": f"Bearer {token}",
        "Accept": "application/json",
    }


# ──────────────────────────────────────────────────────────────
# Helper: llama a la API Laravel y lanza 502 si falla
# ──────────────────────────────────────────────────────────────

def api_get(path: str, token: str) -> dict:
    url = f"{LARAVEL_BASE_URL}/{path}"
    try:
        resp = requests.get(url, headers=get_headers(token), timeout=10)
    except requests.RequestException as e:
        raise HTTPException(502, detail=f"No se pudo conectar con la API Laravel: {e}")

    if resp.status_code == 401:
        raise HTTPException(401, detail="Token inválido o expirado.")
    if resp.status_code == 403:
        raise HTTPException(403, detail="Sin permiso para este recurso.")
    if resp.status_code == 404:
        raise HTTPException(404, detail="Recurso no encontrado en la API Laravel.")
    if not resp.ok:
        raise HTTPException(502, detail=f"Error de la API Laravel ({resp.status_code}): {resp.text[:300]}")

    return resp.json()


# ──────────────────────────────────────────────────────────────
# ENDPOINT 1 – Lista de un conjunto (clase)
#   GET /pdf/conjunto/{conjunto_id}?token=...
# ──────────────────────────────────────────────────────────────

@app.get(
    "/pdf/conjunto/{conjunto_id}",
    summary="PDF con la lista de gimnastas de un conjunto",
    response_class=StreamingResponse,
)
def pdf_lista_conjunto(
    conjunto_id: int,
    token: str = Query(..., description="Token Sanctum del usuario autenticado"),
):
    data = api_get(f"conjuntos/{conjunto_id}", token)
    conjunto = data.get("data", data)  # Soporta con o sin wrapper "data"

    pdf_bytes = generar_pdf_lista_conjunto(conjunto)

    nombre_archivo = f"lista_conjunto_{conjunto_id}.pdf"
    return StreamingResponse(
        io.BytesIO(pdf_bytes),
        media_type="application/pdf",
        headers={"Content-Disposition": f'attachment; filename="{nombre_archivo}"'},
    )


# ──────────────────────────────────────────────────────────────
# ENDPOINT 2 – Lista de todas las gimnastas de un club
#   GET /pdf/club/{club_id}/gimnastas?token=...
# ──────────────────────────────────────────────────────────────

@app.get(
    "/pdf/club/{club_id}/gimnastas",
    summary="PDF con todas las gimnastas de un club agrupadas por conjunto",
    response_class=StreamingResponse,
)
def pdf_lista_club(
    club_id: int,
    token: str = Query(..., description="Token Sanctum del usuario autenticado"),
):
    # Obtenemos todos los conjuntos del club con sus gimnastas
    data = api_get(f"conjuntos/por-club/{club_id}", token)
    conjuntos_resumen = data.get("data", data)

    # Cargamos el detalle completo de cada conjunto (con gimnastas y entrenadores)
    conjuntos_detalle = []
    for c in conjuntos_resumen:
        detalle = api_get(f"conjuntos/{c['id']}", token)
        conjuntos_detalle.append(detalle.get("data", detalle))

    pdf_bytes = generar_pdf_lista_gimnastas_club(club_id, conjuntos_detalle)

    nombre_archivo = f"lista_club_{club_id}_gimnastas.pdf"
    return StreamingResponse(
        io.BytesIO(pdf_bytes),
        media_type="application/pdf",
        headers={"Content-Disposition": f'attachment; filename="{nombre_archivo}"'},
    )


# ──────────────────────────────────────────────────────────────
# ENDPOINT 3 – Health check
# ──────────────────────────────────────────────────────────────

@app.get("/health")
def health():
    return {"status": "ok", "service": "RYTMIA PDF Service"}
