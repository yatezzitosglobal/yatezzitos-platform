import os
from config.settings import setup_logger
from models.schemas import YateData
from exporters.gsheets_exporter import GoogleSheetsExporter
from exporters.markdown_builder import MarkdownBuilder

logger = setup_logger("GenerateMD")

def main():
    logger.info("Iniciando generación de Master Markdown desde Google Sheets...")
    
    # 1. Bajar datos de Google Sheets
    exporter = GoogleSheetsExporter()
    if not exporter.sheet:
        logger.error("No se pudo conectar a GSheets.")
        return
        
    registros = exporter.sheet.get_all_values()
    if len(registros) <= 1:
        logger.error("No hay registros en la hoja de cálculo.")
        return
        
    dataset = []
    # Omitir header (fila 0)
    for fila in registros[1:]:
        # Evitar filas vacías
        if not any(fila):
            continue
            
        # Asegurar que la fila tenga 12 columnas (nuestro export las crea)
        fila = fila + [""] * (12 - len(fila))
        
        yate = YateData(
            nombre=fila[0].strip(),
            puerto=fila[1].strip(),
            categoria=fila[2].strip() if fila[2].strip() else "Otros",
            url=fila[3].strip(),
            precio=fila[4].strip(),
            capacidad=fila[5].strip(),
            modalidad="No especificado",  # No está en gsheets
            amenidades=[a.strip() for a in fila[6].split(' | ') if a.strip() and a.strip() != "N/A"],
            anio_construccion=fila[7].strip() if fila[7].strip() else "-",
            ubicacion_abordaje=fila[8].strip(),
            descripcion=fila[9].strip(),
            url_descarga_pdf=fila[10].strip(),
            url_descarga_imagenes=fila[11].strip()
        )
        dataset.append(yate)
        
    logger.info(f"Se recuperaron {len(dataset)} yates desde Sheets.")
    
    # 2. Generar Markdown
    builder = MarkdownBuilder()
    builder.build_dataset(dataset)
    
if __name__ == '__main__':
    main()
