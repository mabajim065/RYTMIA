"""
RYTMIA – Microservicio Python para generación de PDFs
Expone endpoints que consumen la API Laravel y devuelven PDFs listos para imprimir.
"""

# Importamos FastAPI para crear la API y sus herramientas de respuesta
from fastapi import FastAPI, HTTPException, Query
from fastapi.responses import StreamingResponse  # Para devolver archivos directamente al navegador

import requests  # Para hacer llamadas HTTP a la API de Laravel
import io        # Para manejar los bytes del PDF en memoria (sin guardar en disco)

# Importamos las funciones que generan los PDFs desde el otro archivo
from pdf_generator import generar_pdf_lista_conjunto, generar_pdf_lista_gimnastas_club

# Creamos la aplicación FastAPI (equivalente al app = express() en Node o Laravel en PHP)
app = FastAPI(
    title="RYTMIA PDF Service",
    description="Genera PDFs de listas de conjuntos y gimnastas",
    version="1.0.0",
)

# ──────────────────────────────────────────────────────────────
# URL base de la API de Laravel — aquí es donde Python pide los datos
# ──────────────────────────────────────────────────────────────

import os
LARAVEL_BASE_URL = os.getenv("LARAVEL_BASE_URL", "http://rytmia_nginx/api")

# Construye las cabeceras HTTP con el token del usuario autenticado
# Así Python puede llamar a la API de Laravel con los permisos correctos
def get_headers(token: str) -> dict:
    return {
        "Authorization": f"Bearer {token}",  # Token Sanctum igual que en el frontend
        "Accept": "application/json",
    }


# Función helper: llama a la API de Laravel y gestiona los errores

def api_get(path: str, token: str) -> dict:
    # Construimos la URL completa combinando la base con el endpoint concreto
    url = f"{LARAVEL_BASE_URL}/{path}"

    try:
        # Hacemos la petición GET a Laravel con un tiempo límite de 10 segundos
        resp = requests.get(url, headers=get_headers(token), timeout=10)
    except requests.RequestException as e:
        # Si no se puede conectar con Laravel, devolvemos error 502
        raise HTTPException(502, detail=f"No se pudo conectar con la API Laravel: {e}")

    # Comprobamos el código de respuesta y lanzamos el error adecuado
    if resp.status_code == 401:
        raise HTTPException(401, detail="Token inválido o expirado.")
    if resp.status_code == 403:
        raise HTTPException(403, detail="Sin permiso para este recurso.")
    if resp.status_code == 404:
        raise HTTPException(404, detail="Recurso no encontrado en la API Laravel.")
    if not resp.ok:
        raise HTTPException(502, detail=f"Error de la API Laravel ({resp.status_code}): {resp.text[:300]}")

    # Si todo fue bien, devolvemos el JSON con los datos
    return resp.json()


# ENDPOINT 1 – PDF de un conjunto concreto

@app.get(
    "/pdf/conjunto/{conjunto_id}",
    summary="PDF con la lista de gimnastas de un conjunto",
    response_class=StreamingResponse,
)
def pdf_lista_conjunto(
    conjunto_id: int,                                            # ID del conjunto que viene en la URL
    token: str = Query(..., description="Token Sanctum del usuario autenticado"),  # Token en la URL ?token=...
):
    # 1. Pedimos a Laravel los datos del conjunto (gimnastas, entrenadores, etc.)
    data = api_get(f"conjuntos/{conjunto_id}", token)
    conjunto = data.get("data", data)  # Soporta respuesta con o sin wrapper "data"

    # 2. Generamos el PDF en memoria con esos datos
    pdf_bytes = generar_pdf_lista_conjunto(conjunto)

    # 3. Devolvemos el PDF directamente como descarga (sin guardar en disco)
    nombre_archivo = f"lista_conjunto_{conjunto_id}.pdf"
    return StreamingResponse(
        io.BytesIO(pdf_bytes),                   # El PDF en bytes, en memoria
        media_type="application/pdf",            # Tipo de archivo para que el navegador lo descargue
        headers={"Content-Disposition": f'attachment; filename="{nombre_archivo}"'},
    )


# ENDPOINT 2 – PDF de todas las gimnastas de un club agrupadas por conjunto

@app.get(
    "/pdf/club/{club_id}/gimnastas",
    summary="PDF con todas las gimnastas de un club agrupadas por conjunto",
    response_class=StreamingResponse,
)
def pdf_lista_club(
    club_id: int,
    token: str = Query(..., description="Token Sanctum del usuario autenticado"),
):
    # 1. Pedimos a Laravel todos los conjuntos que pertenecen a este club
    data = api_get(f"conjuntos/por-club/{club_id}", token)
    conjuntos_resumen = data.get("data", data)

    # 2. Por cada conjunto, pedimos su detalle completo (con gimnastas y entrenadores)
    conjuntos_detalle = []
    for c in conjuntos_resumen:
        detalle = api_get(f"conjuntos/{c['id']}", token)
        conjuntos_detalle.append(detalle.get("data", detalle))

    # 3. Generamos el PDF con todos los conjuntos (cada uno en su propia página)
    pdf_bytes = generar_pdf_lista_gimnastas_club(club_id, conjuntos_detalle)

    # 4. Devolvemos el PDF como descarga directa
    nombre_archivo = f"lista_club_{club_id}_gimnastas.pdf"
    return StreamingResponse(
        io.BytesIO(pdf_bytes),
        media_type="application/pdf",
        headers={"Content-Disposition": f'attachment; filename="{nombre_archivo}"'},
    )



# ENDPOINT 3 – Health check (comprueba que el servicio está vivo)
# saber si el microservicio está arrancado y funcionando


@app.get("/health")
def health():
    return {"status": "ok", "service": "RYTMIA PDF Service"}
