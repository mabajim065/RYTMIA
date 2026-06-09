"""
pdf_generator.py – Genera los PDFs usando ReportLab.
Diseño simple y limpio con acento en rosa.

Funciones exportadas:
  - generar_pdf_lista_conjunto(conjunto: dict) -> bytes
  - generar_pdf_lista_gimnastas_club(club_id: int, conjuntos: list) -> bytes
"""

import io                    # Para crear el PDF en memoria sin guardarlo en disco
from datetime import date    # Para calcular edades y poner la fecha de hoy

# Importamos las herramientas de ReportLab para construir el PDF
from reportlab.lib.pagesizes import A4          # Tamaño de página A4
from reportlab.lib import colors                # Colores predefinidos
from reportlab.lib.units import cm              # Unidad de medida: centímetros
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle  # Estilos de texto
from reportlab.lib.enums import TA_CENTER, TA_LEFT, TA_RIGHT          # Alineación del texto
from reportlab.platypus import (
    SimpleDocTemplate,  # El documento principal
    Paragraph,          # Bloques de texto con estilo
    Spacer,             # Espacio en blanco entre elementos
    Table,              # Tablas con filas y columnas
    TableStyle,         # Estilos para las tablas
    HRFlowable,         # Línea horizontal separadora
    PageBreak,          # Salto de página
)

# ──────────────────────────────────────────────────────────────
# Paleta de colores de Rytmia — usamos los mismos que en la web
# ──────────────────────────────────────────────────────────────

ROSA          = colors.HexColor("#E91E8C")   # Rosa principal (cabeceras de tabla)
ROSA_CLARO    = colors.HexColor("#FCE4F3")   # Rosa suave (filas alternas)
GRIS_TEXTO    = colors.HexColor("#444444")   # Gris oscuro para el texto normal
GRIS_CLARO    = colors.HexColor("#F5F5F5")   # Gris muy claro para fondos
GRIS_BORDE    = colors.HexColor("#E0E0E0")   # Gris para bordes de tabla
BLANCO        = colors.white
NEGRO         = colors.black

# ──────────────────────────────────────────────────────────────
# Estilos de texto — definen cómo se ve cada tipo de párrafo
# ──────────────────────────────────────────────────────────────

styles = getSampleStyleSheet()  # Estilos base de ReportLab

# Título grande centrado en rosa (ej: "RYTMIA")
ESTILO_TITULO = ParagraphStyle(
    "titulo",
    parent=styles["Title"],
    fontSize=20,
    leading=26,
    textColor=ROSA,
    alignment=TA_CENTER,
    spaceAfter=2,
    fontName="Helvetica-Bold",
)

# Subtítulo más pequeño en gris (ej: "Lista de clase")
ESTILO_SUBTITULO = ParagraphStyle(
    "subtitulo",
    parent=styles["Normal"],
    fontSize=10,
    textColor=GRIS_TEXTO,
    alignment=TA_CENTER,
    spaceAfter=2,
)

# Fecha alineada a la derecha (ej: "Generado el 09/06/2026")
ESTILO_FECHA = ParagraphStyle(
    "fecha",
    parent=styles["Normal"],
    fontSize=8,
    textColor=GRIS_TEXTO,
    alignment=TA_RIGHT,
)

# Encabezado de sección en rosa (ej: "Gimnastas", "Entrenadores/as")
ESTILO_SECCION = ParagraphStyle(
    "seccion",
    parent=styles["Normal"],
    fontSize=11,
    fontName="Helvetica-Bold",
    textColor=ROSA,
    spaceBefore=12,
    spaceAfter=4,
)

# Texto normal en gris (para mensajes como "Sin gimnastas asignadas")
ESTILO_NORMAL = ParagraphStyle(
    "normal",
    parent=styles["Normal"],
    fontSize=9,
    textColor=GRIS_TEXTO,
)

# Pie de página pequeño y centrado
ESTILO_PIE = ParagraphStyle(
    "pie",
    parent=styles["Normal"],
    fontSize=7,
    textColor=GRIS_TEXTO,
    alignment=TA_CENTER,
)

# ──────────────────────────────────────────────────────────────
# Funciones auxiliares (helpers)
# ──────────────────────────────────────────────────────────────

def _calcular_edad(fecha_str: str | None) -> str:
    """Calcula la edad actual a partir de la fecha de nacimiento."""
    if not fecha_str:
        return "–"
    try:
        fn = date.fromisoformat(fecha_str)   # Convierte el texto "1995-04-12" a fecha
        today = date.today()
        # Resta años y ajusta si aún no ha pasado el cumpleaños este año
        edad = today.year - fn.year - ((today.month, today.day) < (fn.month, fn.day))
        return str(edad)
    except ValueError:
        return "–"


def _nombre_completo(g: dict) -> str:
    """Devuelve el nombre en formato 'Apellidos, Nombre'."""
    nombre    = (g.get("nombre") or "").strip()
    apellidos = (g.get("apellidos") or "").strip()
    return f"{apellidos}, {nombre}" if apellidos else nombre or "–"


def _tabla_gimnastas(gimnastas: list) -> Table:
    """Construye la tabla de gimnastas con cabecera rosa y filas alternas."""
    # Definimos los títulos de las columnas
    encabezados = ["#", "Apellidos, Nombre", "Licencia", "F. Nacimiento", "Edad"]
    filas = [encabezados]  # La primera fila es siempre el encabezado

    # Añadimos una fila por cada gimnasta de la lista
    for i, g in enumerate(gimnastas, start=1):
        filas.append([
            str(i),                                    # Número de orden
            _nombre_completo(g),                       # Apellidos, Nombre
            g.get("numero_licencia") or "–",           # Número de licencia federativa
            g.get("fecha_nacimiento") or "–",          # Fecha de nacimiento
            _calcular_edad(g.get("fecha_nacimiento")), # Edad calculada automáticamente
        ])

    # Anchos de cada columna en centímetros
    col_widths = [0.8*cm, 6.5*cm, 3*cm, 3.2*cm, 1.5*cm]

    tabla = Table(filas, colWidths=col_widths, repeatRows=1)  # repeatRows=1 repite cabecera en salto de página
    tabla.setStyle(TableStyle([
        # --- Estilo de la fila de cabecera ---
        ("BACKGROUND",    (0, 0), (-1, 0),  ROSA),         # Fondo rosa
        ("TEXTCOLOR",     (0, 0), (-1, 0),  BLANCO),       # Texto blanco
        ("FONTNAME",      (0, 0), (-1, 0),  "Helvetica-Bold"),
        ("FONTSIZE",      (0, 0), (-1, 0),  8.5),
        ("ALIGN",         (0, 0), (-1, 0),  "CENTER"),
        ("VALIGN",        (0, 0), (-1, 0),  "MIDDLE"),
        ("TOPPADDING",    (0, 0), (-1, 0),  5),
        ("BOTTOMPADDING", (0, 0), (-1, 0),  5),

        # --- Estilo de las filas de datos ---
        ("FONTNAME",      (0, 1), (-1, -1), "Helvetica"),
        ("FONTSIZE",      (0, 1), (-1, -1), 9),
        ("ALIGN",         (0, 1), (0, -1),  "CENTER"),     # Columna nº centrada
        ("ALIGN",         (2, 1), (4, -1),  "CENTER"),     # Licencia, fecha y edad centradas
        ("VALIGN",        (0, 1), (-1, -1), "MIDDLE"),
        ("TOPPADDING",    (0, 1), (-1, -1), 4),
        ("BOTTOMPADDING", (0, 1), (-1, -1), 4),

        # --- Filas alternas en rosa muy claro (mejor legibilidad) ---
        *[("BACKGROUND", (0, r), (-1, r), ROSA_CLARO)
          for r in range(2, len(filas), 2)],  # Filas pares (2, 4, 6...)

        # --- Bordes suaves en toda la tabla ---
        ("GRID",          (0, 0), (-1, -1), 0.4, GRIS_BORDE),
        ("LINEBELOW",     (0, 0), (-1, 0),  1.0, ROSA),   # Línea rosa debajo del encabezado
    ]))

    return tabla


def _tabla_entrenadores(entrenadores: list) -> Table:
    """Construye la tabla de entrenadores con el mismo estilo que la de gimnastas."""
    encabezados = ["Entrenador/a", "Titulación"]
    filas = [encabezados]
    for e in entrenadores:
        nombre = (e.get("nombre") or "").strip()
        apellidos = (e.get("apellidos") or "").strip()
        nc = f"{apellidos}, {nombre}" if apellidos else nombre or "–"
        filas.append([nc, e.get("titulacion") or "–"])

    tabla = Table(filas, colWidths=[8*cm, 6*cm])
    tabla.setStyle(TableStyle([
        ("BACKGROUND",    (0, 0), (-1, 0),  ROSA),
        ("TEXTCOLOR",     (0, 0), (-1, 0),  BLANCO),
        ("FONTNAME",      (0, 0), (-1, 0),  "Helvetica-Bold"),
        ("FONTSIZE",      (0, 0), (-1, -1), 9),
        ("ALIGN",         (0, 0), (-1, 0),  "CENTER"),
        ("VALIGN",        (0, 0), (-1, -1), "MIDDLE"),
        ("TOPPADDING",    (0, 0), (-1, -1), 4),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
        ("GRID",          (0, 0), (-1, -1), 0.4, GRIS_BORDE),
        ("LINEBELOW",     (0, 0), (-1, 0),  1.0, ROSA),
        *[("BACKGROUND", (0, r), (-1, r), ROSA_CLARO) for r in range(2, len(filas), 2)],
    ]))
    return tabla


# ──────────────────────────────────────────────────────────────
# PDF 1 – Lista de un conjunto concreto
# ──────────────────────────────────────────────────────────────

def generar_pdf_lista_conjunto(conjunto: dict) -> bytes:
    """Genera el PDF de un único conjunto y devuelve los bytes del archivo."""

    # Creamos el buffer en memoria donde se escribirá el PDF (no se guarda en disco)
    buffer = io.BytesIO()
    hoy = date.today().strftime("%d/%m/%Y")  # Fecha de hoy formateada

    # Configuramos el documento: tamaño A4 con márgenes de 2cm
    doc = SimpleDocTemplate(
        buffer,
        pagesize=A4,
        leftMargin=2*cm,
        rightMargin=2*cm,
        topMargin=2*cm,
        bottomMargin=2*cm,
        title=f"Lista – {conjunto.get('nombre', '')}",
        author="RYTMIA",
    )

    # La "story" es la lista de elementos que forman el PDF, en orden de aparición
    story = []

    # ── Cabecera del documento ────────────────────────────────
    story.append(Paragraph("RYTMIA", ESTILO_TITULO))           # Título principal
    story.append(Paragraph("Lista de clase", ESTILO_SUBTITULO)) # Subtítulo
    story.append(HRFlowable(width="100%", thickness=1.5, color=ROSA, spaceAfter=6))  # Línea rosa
    story.append(Paragraph(f"Generado el {hoy}", ESTILO_FECHA)) # Fecha a la derecha
    story.append(Spacer(1, 8))  # Espacio en blanco

    # ── Extraemos los datos del conjunto recibidos desde Laravel ──
    nombre    = conjunto.get("nombre") or "–"
    horario   = conjunto.get("horario") or "–"
    club      = (conjunto.get("club") or {}).get("nombre") or "–"
    categoria = (conjunto.get("categoria") or {}).get("nombre") or "–"
    gimnastas    = conjunto.get("gimnastas") or []
    entrenadores = conjunto.get("entrenadores") or []

    # ── Tabla de información general del conjunto ─────────────
    info_data = [
        ["Conjunto",  nombre],
        ["Club",      club],
        ["Categoría", categoria],
        ["Horario",   horario],
        ["Gimnastas", str(len(gimnastas))],  # Total de gimnastas
    ]
    info_tabla = Table(info_data, colWidths=[3.5*cm, 13*cm])
    info_tabla.setStyle(TableStyle([
        ("FONTNAME",      (0, 0), (0, -1), "Helvetica-Bold"),  # Etiquetas en negrita
        ("FONTSIZE",      (0, 0), (-1, -1), 9),
        ("TEXTCOLOR",     (0, 0), (0, -1), ROSA),              # Etiquetas en rosa
        ("TEXTCOLOR",     (1, 0), (1, -1), GRIS_TEXTO),        # Valores en gris
        ("BACKGROUND",    (0, 0), (-1, -1), GRIS_CLARO),       # Fondo gris claro
        ("TOPPADDING",    (0, 0), (-1, -1), 3),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 3),
        ("LEFTPADDING",   (0, 0), (-1, -1), 6),
        ("GRID",          (0, 0), (-1, -1), 0.3, GRIS_BORDE),
    ]))
    story.append(info_tabla)
    story.append(Spacer(1, 14))

    # ── Tabla de gimnastas ────────────────────────────────────
    story.append(Paragraph("Gimnastas", ESTILO_SECCION))
    if gimnastas:
        story.append(_tabla_gimnastas(gimnastas))  # Llamamos a la función helper
    else:
        story.append(Paragraph("No hay gimnastas asignadas a este conjunto.", ESTILO_NORMAL))

    story.append(Spacer(1, 14))

    # ── Tabla de entrenadores (solo si los hay) ───────────────
    if entrenadores:
        story.append(Paragraph("Entrenadores/as", ESTILO_SECCION))
        story.append(_tabla_entrenadores(entrenadores))
        story.append(Spacer(1, 14))

    # ── Zona de firmas al final del documento ─────────────────
    story.append(Spacer(1, 20))
    story.append(HRFlowable(width="100%", thickness=0.5, color=GRIS_BORDE))
    story.append(Spacer(1, 10))
    firmas = Table(
        [["Firma entrenador/a:", "", "Firma responsable:"],
         ["_______________________", "", "_______________________"]],
        colWidths=[7*cm, 4*cm, 7*cm]
    )
    firmas.setStyle(TableStyle([
        ("FONTSIZE",   (0, 0), (-1, -1), 8),
        ("TEXTCOLOR",  (0, 0), (-1, -1), GRIS_TEXTO),
        ("FONTNAME",   (0, 0), (-1, 0),  "Helvetica-Bold"),
        ("TEXTCOLOR",  (0, 0), (-1, 0),  ROSA),
        ("TOPPADDING", (0, 0), (-1, -1), 4),
    ]))
    story.append(firmas)

    # ── Pie de página ─────────────────────────────────────────
    story.append(Spacer(1, 16))
    story.append(Paragraph("RYTMIA · Gestión de gimnasia rítmica", ESTILO_PIE))

    # Construimos el PDF con todos los elementos de la story y lo guardamos en el buffer
    doc.build(story)

    # Devolvemos los bytes del PDF para que main.py los envíe al navegador
    return buffer.getvalue()


# ──────────────────────────────────────────────────────────────
# PDF 2 – Todas las gimnastas de un club agrupadas por conjunto
# ──────────────────────────────────────────────────────────────

def generar_pdf_lista_gimnastas_club(club_id: int, conjuntos: list) -> bytes:
    """Genera un PDF multi-página con todos los conjuntos del club, uno por página."""

    buffer = io.BytesIO()
    hoy = date.today().strftime("%d/%m/%Y")

    # Obtenemos el nombre del club leyendo el primer conjunto que lo tenga
    nombre_club = "–"
    for c in conjuntos:
        nc = (c.get("club") or {}).get("nombre")
        if nc:
            nombre_club = nc
            break

    doc = SimpleDocTemplate(
        buffer,
        pagesize=A4,
        leftMargin=2*cm,
        rightMargin=2*cm,
        topMargin=2*cm,
        bottomMargin=2*cm,
        title=f"Listas – {nombre_club}",
        author="RYTMIA",
    )

    story = []

    # ── Portada del documento con el resumen del club ─────────
    story.append(Spacer(1, 1.5*cm))
    story.append(Paragraph("RYTMIA", ESTILO_TITULO))
    story.append(Paragraph("Listas de clases", ESTILO_SUBTITULO))
    story.append(Paragraph(f"Club: <b>{nombre_club}</b>", ESTILO_SUBTITULO))
    story.append(HRFlowable(width="100%", thickness=1.5, color=ROSA, spaceAfter=8))
    story.append(Paragraph(f"Generado el {hoy}", ESTILO_FECHA))
    story.append(Spacer(1, 1*cm))

    # Tabla resumen: cuántos conjuntos y cuántas gimnastas en total
    total_g = sum(len(c.get("gimnastas") or []) for c in conjuntos)  # Suma todas las gimnastas
    resumen = Table(
        [["Conjuntos", str(len(conjuntos))], ["Gimnastas total", str(total_g)]],
        colWidths=[6*cm, 3*cm]
    )
    resumen.setStyle(TableStyle([
        ("FONTNAME",      (0, 0), (0, -1), "Helvetica-Bold"),
        ("FONTSIZE",      (0, 0), (-1, -1), 10),
        ("TEXTCOLOR",     (0, 0), (0, -1), ROSA),
        ("TEXTCOLOR",     (1, 0), (1, -1), GRIS_TEXTO),
        ("BACKGROUND",    (0, 0), (-1, -1), GRIS_CLARO),
        ("TOPPADDING",    (0, 0), (-1, -1), 5),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
        ("LEFTPADDING",   (0, 0), (-1, -1), 8),
        ("GRID",          (0, 0), (-1, -1), 0.3, GRIS_BORDE),
    ]))
    story.append(resumen)

    # ── Una página por cada conjunto del club ─────────────────
    for c in conjuntos:
        story.append(PageBreak())  # Cada conjunto empieza en una página nueva

        # Extraemos los datos de este conjunto
        nombre       = c.get("nombre") or "–"
        horario      = c.get("horario") or "–"
        categoria    = (c.get("categoria") or {}).get("nombre") or "–"
        gimnastas    = c.get("gimnastas") or []
        entrenadores = c.get("entrenadores") or []

        # Cabecera de la página del conjunto
        story.append(Paragraph("RYTMIA", ESTILO_TITULO))
        story.append(HRFlowable(width="100%", thickness=1.5, color=ROSA, spaceAfter=6))
        story.append(Paragraph(f"Generado el {hoy}", ESTILO_FECHA))
        story.append(Spacer(1, 6))

        # Tabla de información del conjunto
        info_data = [
            ["Conjunto",  nombre],
            ["Club",      nombre_club],
            ["Categoría", categoria],
            ["Horario",   horario],
            ["Gimnastas", str(len(gimnastas))],
        ]
        info_tabla = Table(info_data, colWidths=[3.5*cm, 13*cm])
        info_tabla.setStyle(TableStyle([
            ("FONTNAME",      (0, 0), (0, -1), "Helvetica-Bold"),
            ("FONTSIZE",      (0, 0), (-1, -1), 9),
            ("TEXTCOLOR",     (0, 0), (0, -1), ROSA),
            ("TEXTCOLOR",     (1, 0), (1, -1), GRIS_TEXTO),
            ("BACKGROUND",    (0, 0), (-1, -1), GRIS_CLARO),
            ("TOPPADDING",    (0, 0), (-1, -1), 3),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 3),
            ("LEFTPADDING",   (0, 0), (-1, -1), 6),
            ("GRID",          (0, 0), (-1, -1), 0.3, GRIS_BORDE),
        ]))
        story.append(info_tabla)
        story.append(Spacer(1, 12))

        # Tabla de gimnastas del conjunto
        story.append(Paragraph("Gimnastas", ESTILO_SECCION))
        if gimnastas:
            story.append(_tabla_gimnastas(gimnastas))
        else:
            story.append(Paragraph("Sin gimnastas asignadas.", ESTILO_NORMAL))

        # Tabla de entrenadores del conjunto (si los hay)
        if entrenadores:
            story.append(Spacer(1, 10))
            story.append(Paragraph("Entrenadores/as", ESTILO_SECCION))
            story.append(_tabla_entrenadores(entrenadores))

        # Pie de página de cada hoja
        story.append(Spacer(1, 16))
        story.append(Paragraph("RYTMIA · Gestión de gimnasia rítmica", ESTILO_PIE))

    # Construimos el PDF completo y devolvemos los bytes
    doc.build(story)
    return buffer.getvalue()
