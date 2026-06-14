
from fastapi import FastAPI, HTTPException, Query
from fastapi.responses import StreamingResponse
import requests
import io

from pdf_generator import generar_pdf_lista_conjunto, generar_pdf_lista_gimnastas_club

# creamos la api
app = FastAPI(
    title="RYTMIA PDF Service",
    description="Genera PDFs de listas de conjuntos y gimnastas",
    version="1.0.0",
)

# importamos variables de entorno
import os

# url base de laravel
LARAVEL_BASE_URL = os.getenv("LARAVEL_BASE_URL", "http://rytmia_nginx/api")

# crear cabeceras con token
def get_headers(token: str) -> dict:
    return {
        # token del usuario
        "Authorization": f"Bearer {token}",

        # pedimos respuesta json
        "Accept": "application/json",
    }

# pedir datos a laravel
def api_get(path: str, token: str) -> dict
    url = f"{LARAVEL_BASE_URL}/{path}"

    try:
        # petición get a laravel
        resp = requests.get(url, headers=get_headers(token), timeout=10)

    except requests.RequestException as e:
        # error de conexión
        raise HTTPException(502, detail=f"no se pudo conectar con la api laravel: {e}")

    # token incorrecto
    if resp.status_code == 401:
        raise HTTPException(401, detail="token inválido o expirado.")

    # sin permisos
    if resp.status_code == 403:
        raise HTTPException(403, detail="sin permiso para este recurso.")

    # no encontrado
    if resp.status_code == 404:
        raise HTTPException(404, detail="recurso no encontrado en la api laravel.")

    # otro error
    if not resp.ok:
        raise HTTPException(502, detail=f"error de la api laravel ({resp.status_code}): {resp.text[:300]}")

    # devolver datos json
    return resp.json()

# pdf de un conjunto
@app.get(
    "/pdf/conjunto/{conjunto_id}",
    summary="pdf con la lista de gimnastas de un conjunto",
    response_class=StreamingResponse,
)
def pdf_lista_conjunto(
    # id del conjunto
    conjunto_id: int,

    # token del usuario
    token: str = Query(..., description="token sanctum del usuario autenticado"),
):
    # pedir conjunto a laravel
    data = api_get(f"conjuntos/{conjunto_id}", token)

    # obtener datos del conjunto
    conjunto = data.get("data", data)

    # crear pdf
    pdf_bytes = generar_pdf_lista_conjunto(conjunto)

    # nombre del archivo
    nombre_archivo = f"lista_conjunto_{conjunto_id}.pdf"

    # devolver pdf
    return StreamingResponse(
        io.BytesIO(pdf_bytes),
        media_type="application/pdf",
        headers={"Content-Disposition": f'attachment; filename="{nombre_archivo}"'},
    )

# pdf de gimnastas de un club
@app.get(
    "/pdf/club/{club_id}/gimnastas",
    summary="pdf con todas las gimnastas de un club agrupadas por conjunto",
    response_class=StreamingResponse,
)
def pdf_lista_club(
    # id del club
    club_id: int,

    # token del usuario
    token: str = Query(..., description="token sanctum del usuario autenticado"),
):
    # pedir conjuntos del club
    data = api_get(f"conjuntos/por-club/{club_id}", token)

    # obtener lista de conjuntos
    conjuntos_resumen = data.get("data", data)

    # lista de detalles
    conjuntos_detalle = []

    # pedir detalle de cada conjunto
    for c in conjuntos_resumen:
        detalle = api_get(f"conjuntos/{c['id']}", token)
        conjuntos_detalle.append(detalle.get("data", detalle))

    # crear pdf
    pdf_bytes = generar_pdf_lista_gimnastas_club(club_id, conjuntos_detalle)

    # nombre del archivo
    nombre_archivo = f"lista_club_{club_id}_gimnastas.pdf"

    # devolver pdf
    return StreamingResponse(
        io.BytesIO(pdf_bytes),
        media_type="application/pdf",
        headers={"Content-Disposition": f'attachment; filename="{nombre_archivo}"'},
    )

# comprobar servicio
@app.get("/health")
def health():
    # servicio activo
    return {"status": "ok", "service": "RYTMIA PDF Service"}