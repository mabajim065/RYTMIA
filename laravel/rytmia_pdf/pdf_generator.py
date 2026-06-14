
import io
from datetime import date

from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.units import cm
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle

from reportlab.lib.enums import TA_CENTER, TA_RIGHT

# elementos para construir el pdf
from reportlab.platypus import (
    SimpleDocTemplate,
    Paragraph,
    Spacer,
    Table,
    TableStyle,
    HRFlowable,
    PageBreak,
)

# colores principales
ROSA = colors.HexColor("#E91E8C")
ROSA_CLARO = colors.HexColor("#FCE4F3")
GRIS_TEXTO = colors.HexColor("#444444")
GRIS_CLARO = colors.HexColor("#F5F5F5")
GRIS_BORDE = colors.HexColor("#E0E0E0")
BLANCO = colors.white
NEGRO = colors.black

# estilos base
styles = getSampleStyleSheet()

# tiitulo principal
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

# subtitulo
ESTILO_SUBTITULO = ParagraphStyle(
    "subtitulo",
    parent=styles["Normal"],
    fontSize=10,
    textColor=GRIS_TEXTO,
    alignment=TA_CENTER,
    spaceAfter=2,
)

# fecha del documento
ESTILO_FECHA = ParagraphStyle(
    "fecha",
    parent=styles["Normal"],
    fontSize=8,
    textColor=GRIS_TEXTO,
    alignment=TA_RIGHT,
)

# titulo de seccion
ESTILO_SECCION = ParagraphStyle(
    "seccion",
    parent=styles["Normal"],
    fontSize=11,
    fontName="Helvetica-Bold",
    textColor=ROSA,
    spaceBefore=12,
    spaceAfter=4,
)

# texto normal
ESTILO_NORMAL = ParagraphStyle(
    "normal",
    parent=styles["Normal"],
    fontSize=9,
    textColor=GRIS_TEXTO,
)

# pie de pagina
ESTILO_PIE = ParagraphStyle(
    "pie",
    parent=styles["Normal"],
    fontSize=7,
    textColor=GRIS_TEXTO,
    alignment=TA_CENTER,
)

# calcular edad
def _calcular_edad(fecha_str: str | None) -> str:
    if not fecha_str:
        return "–"

    try:
        # convertir texto a fecha
        fn = date.fromisoformat(fecha_str)

        # fecha de hoy
        today = date.today()

        # calcular edad real
        edad = today.year - fn.year - ((today.month, today.day) < (fn.month, fn.day))

        # devolver edad como texto
        return str(edad)

    except ValueError:
        # si la fecha no vale
        return "–"


# formar nombre completo
def _nombre_completo(g: dict) -> str:
    # coger nombre
    nombre = (g.get("nombre") or "").strip()

    # coger apellidos
    apellidos = (g.get("apellidos") or "").strip()

    # devolver apellidos, nombre
    return f"{apellidos}, {nombre}" if apellidos else nombre or "–"


# crear tabla de gimnastas
def _tabla_gimnastas(gimnastas: list) -> Table:
    # cabecera de la tabla
    encabezados = ["#", "Apellidos, Nombre", "Licencia", "F. Nacimiento", "Edad"]

    # primera fila
    filas = [encabezados]

    # añadir gimnastas
    for i, g in enumerate(gimnastas, start=1):
        filas.append([
            str(i),
            _nombre_completo(g),
            g.get("numero_licencia") or "–",
            g.get("fecha_nacimiento") or "–",
            _calcular_edad(g.get("fecha_nacimiento")),
        ])

    # ancho de columnas
    col_widths = [0.8*cm, 6.5*cm, 3*cm, 3.2*cm, 1.5*cm]

    # crear tabla
    tabla = Table(filas, colWidths=col_widths, repeatRows=1)

    # estilo de tabla
    tabla.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), ROSA),
        ("TEXTCOLOR", (0, 0), (-1, 0), BLANCO),
        ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, 0), 8.5),
        ("ALIGN", (0, 0), (-1, 0), "CENTER"),
        ("VALIGN", (0, 0), (-1, 0), "MIDDLE"),
        ("TOPPADDING", (0, 0), (-1, 0), 5),
        ("BOTTOMPADDING", (0, 0), (-1, 0), 5),

        ("FONTNAME", (0, 1), (-1, -1), "Helvetica"),
        ("FONTSIZE", (0, 1), (-1, -1), 9),
        ("ALIGN", (0, 1), (0, -1), "CENTER"),
        ("ALIGN", (2, 1), (4, -1), "CENTER"),
        ("VALIGN", (0, 1), (-1, -1), "MIDDLE"),
        ("TOPPADDING", (0, 1), (-1, -1), 4),
        ("BOTTOMPADDING", (0, 1), (-1, -1), 4),

        # filas alternas
        *[("BACKGROUND", (0, r), (-1, r), ROSA_CLARO)
          for r in range(2, len(filas), 2)],

        # bordes
        ("GRID", (0, 0), (-1, -1), 0.4, GRIS_BORDE),
        ("LINEBELOW", (0, 0), (-1, 0), 1.0, ROSA),
    ]))

    # devolver tabla
    return tabla


# crear tabla de entrenadores
def _tabla_entrenadores(entrenadores: list) -> Table:
    # cabecera
    encabezados = ["Entrenador/a", "Titulación"]

    # primera fila
    filas = [encabezados]

    # añadir entrenadores
    for e in entrenadores:
        nombre = (e.get("nombre") or "").strip()
        apellidos = (e.get("apellidos") or "").strip()
        nc = f"{apellidos}, {nombre}" if apellidos else nombre or "–"
        filas.append([nc, e.get("titulacion") or "–"])

    # crear tabla
    tabla = Table(filas, colWidths=[8*cm, 6*cm])

    # estilo de tabla
    tabla.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), ROSA),
        ("TEXTCOLOR", (0, 0), (-1, 0), BLANCO),
        ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), 9),
        ("ALIGN", (0, 0), (-1, 0), "CENTER"),
        ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
        ("TOPPADDING", (0, 0), (-1, -1), 4),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
        ("GRID", (0, 0), (-1, -1), 0.4, GRIS_BORDE),
        ("LINEBELOW", (0, 0), (-1, 0), 1.0, ROSA),
        *[("BACKGROUND", (0, r), (-1, r), ROSA_CLARO) for r in range(2, len(filas), 2)],
    ]))

    # devolver tabla
    return tabla


# pdf de un conjunto
def generar_pdf_lista_conjunto(conjunto: dict) -> bytes:
    # crear archivo en memoria
    buffer = io.BytesIO()

    # fecha actual
    hoy = date.today().strftime("%d/%m/%Y")

    # crear documento pdf
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

    # elementos del pdf
    story = []

    # cabecera
    story.append(Paragraph("RYTMIA", ESTILO_TITULO))
    story.append(Paragraph("Lista de clase", ESTILO_SUBTITULO))
    story.append(HRFlowable(width="100%", thickness=1.5, color=ROSA, spaceAfter=6))
    story.append(Paragraph(f"Generado el {hoy}", ESTILO_FECHA))
    story.append(Spacer(1, 8))

    # datos del conjunto
    nombre = conjunto.get("nombre") or "–"
    horario = conjunto.get("horario") or "–"
    club = (conjunto.get("club") or {}).get("nombre") or "–"
    categoria = (conjunto.get("categoria") or {}).get("nombre") or "–"
    gimnastas = conjunto.get("gimnastas") or []
    entrenadores = conjunto.get("entrenadores") or []

    # tabla de información
    info_data = [
        ["Conjunto", nombre],
        ["Club", club],
        ["Categoría", categoria],
        ["Horario", horario],
        ["Gimnastas", str(len(gimnastas))],
    ]

    # crear tabla de datos
    info_tabla = Table(info_data, colWidths=[3.5*cm, 13*cm])

    # estilo de la tabla
    info_tabla.setStyle(TableStyle([
        ("FONTNAME", (0, 0), (0, -1), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), 9),
        ("TEXTCOLOR", (0, 0), (0, -1), ROSA),
        ("TEXTCOLOR", (1, 0), (1, -1), GRIS_TEXTO),
        ("BACKGROUND", (0, 0), (-1, -1), GRIS_CLARO),
        ("TOPPADDING", (0, 0), (-1, -1), 3),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 3),
        ("LEFTPADDING", (0, 0), (-1, -1), 6),
        ("GRID", (0, 0), (-1, -1), 0.3, GRIS_BORDE),
    ]))

    # añadir tabla al pdf
    story.append(info_tabla)
    story.append(Spacer(1, 14))

    # seccion gimnastas
    story.append(Paragraph("Gimnastas", ESTILO_SECCION))

    # añadir tabla o mensaje
    if gimnastas:
        story.append(_tabla_gimnastas(gimnastas))
    else:
        story.append(Paragraph("No hay gimnastas asignadas a este conjunto.", ESTILO_NORMAL))

    story.append(Spacer(1, 14))

    # seccion entrenadores
    if entrenadores:
        story.append(Paragraph("Entrenadores/as", ESTILO_SECCION))
        story.append(_tabla_entrenadores(entrenadores))
        story.append(Spacer(1, 14))

    # firmas
    story.append(Spacer(1, 20))
    story.append(HRFlowable(width="100%", thickness=0.5, color=GRIS_BORDE))
    story.append(Spacer(1, 10))

    # tabla de firmas
    firmas = Table(
        [["Firma entrenador/a:", "", "Firma responsable:"],
         ["_______________________", "", "_______________________"]],
        colWidths=[7*cm, 4*cm, 7*cm]
    )

    # estilo firmas
    firmas.setStyle(TableStyle([
        ("FONTSIZE", (0, 0), (-1, -1), 8),
        ("TEXTCOLOR", (0, 0), (-1, -1), GRIS_TEXTO),
        ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
        ("TEXTCOLOR", (0, 0), (-1, 0), ROSA),
        ("TOPPADDING", (0, 0), (-1, -1), 4),
    ]))

    # añadir firmas
    story.append(firmas)

    # pie de página
    story.append(Spacer(1, 16))
    story.append(Paragraph("RYTMIA · Gestión de gimnasia rítmica", ESTILO_PIE))

    # construir pdf
    doc.build(story)

    # devolver pdf en bytes
    return buffer.getvalue()


# pdf de un club
def generar_pdf_lista_gimnastas_club(club_id: int, conjuntos: list) -> bytes:
    # crear archivo en memoria
    buffer = io.BytesIO()

    # fecha actual
    hoy = date.today().strftime("%d/%m/%Y")

    # nombre del club
    nombre_club = "–"

    # buscar nombre del club
    for c in conjuntos:
        nc = (c.get("club") or {}).get("nombre")
        if nc:
            nombre_club = nc
            break

    # crear documento
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

    # elementos del pdf
    story = []

    # portada
    story.append(Spacer(1, 1.5*cm))
    story.append(Paragraph("RYTMIA", ESTILO_TITULO))
    story.append(Paragraph("Listas de clases", ESTILO_SUBTITULO))
    story.append(Paragraph(f"Club: <b>{nombre_club}</b>", ESTILO_SUBTITULO))
    story.append(HRFlowable(width="100%", thickness=1.5, color=ROSA, spaceAfter=8))
    story.append(Paragraph(f"Generado el {hoy}", ESTILO_FECHA))
    story.append(Spacer(1, 1*cm))

    # contar gimnastas
    total_g = sum(len(c.get("gimnastas") or []) for c in conjuntos)

    # tabla resumen
    resumen = Table(
        [["Conjuntos", str(len(conjuntos))], ["Gimnastas total", str(total_g)]],
        colWidths=[6*cm, 3*cm]
    )

    # estilo resumen
    resumen.setStyle(TableStyle([
        ("FONTNAME", (0, 0), (0, -1), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), 10),
        ("TEXTCOLOR", (0, 0), (0, -1), ROSA),
        ("TEXTCOLOR", (1, 0), (1, -1), GRIS_TEXTO),
        ("BACKGROUND", (0, 0), (-1, -1), GRIS_CLARO),
        ("TOPPADDING", (0, 0), (-1, -1), 5),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
        ("LEFTPADDING", (0, 0), (-1, -1), 8),
        ("GRID", (0, 0), (-1, -1), 0.3, GRIS_BORDE),
    ]))

    # añadir resumen
    story.append(resumen)

    # una página por conjunto
    for c in conjuntos:
        story.append(PageBreak())

        # datos del conjunto
        nombre = c.get("nombre") or "–"
        horario = c.get("horario") or "–"
        categoria = (c.get("categoria") or {}).get("nombre") or "–"
        gimnastas = c.get("gimnastas") or []
        entrenadores = c.get("entrenadores") or []

        # cabecera
        story.append(Paragraph("RYTMIA", ESTILO_TITULO))
        story.append(HRFlowable(width="100%", thickness=1.5, color=ROSA, spaceAfter=6))
        story.append(Paragraph(f"Generado el {hoy}", ESTILO_FECHA))
        story.append(Spacer(1, 6))

        # datos del conjunto
        info_data = [
            ["Conjunto", nombre],
            ["Club", nombre_club],
            ["Categoría", categoria],
            ["Horario", horario],
            ["Gimnastas", str(len(gimnastas))],
        ]

        # tabla de datos
        info_tabla = Table(info_data, colWidths=[3.5*cm, 13*cm])

        # estilo tabla
        info_tabla.setStyle(TableStyle([
            ("FONTNAME", (0, 0), (0, -1), "Helvetica-Bold"),
            ("FONTSIZE", (0, 0), (-1, -1), 9),
            ("TEXTCOLOR", (0, 0), (0, -1), ROSA),
            ("TEXTCOLOR", (1, 0), (1, -1), GRIS_TEXTO),
            ("BACKGROUND", (0, 0), (-1, -1), GRIS_CLARO),
            ("TOPPADDING", (0, 0), (-1, -1), 3),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 3),
            ("LEFTPADDING", (0, 0), (-1, -1), 6),
            ("GRID", (0, 0), (-1, -1), 0.3, GRIS_BORDE),
        ]))

        # añadir tabla
        story.append(info_tabla)
        story.append(Spacer(1, 12))

        # sección gimnastas
        story.append(Paragraph("Gimnastas", ESTILO_SECCION))

        # añadir gimnastas
        if gimnastas:
            story.append(_tabla_gimnastas(gimnastas))
        else:
            story.append(Paragraph("Sin gimnastas asignadas.", ESTILO_NORMAL))

        # añadir entrenadores
        if entrenadores:
            story.append(Spacer(1, 10))
            story.append(Paragraph("Entrenadores/as", ESTILO_SECCION))
            story.append(_tabla_entrenadores(entrenadores))

        # pie
        story.append(Spacer(1, 16))
        story.append(Paragraph("RYTMIA · Gestión de gimnasia rítmica", ESTILO_PIE))

    # construir pdf
    doc.build(story)

    # devolver pdf
    return buffer.getvalue()