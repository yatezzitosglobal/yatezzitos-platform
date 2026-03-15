import time
import os
from typing import Dict, List
import gspread
from oauth2client.service_account import ServiceAccountCredentials

from config.settings import setup_logger, GSHEETS_CREDENTIALS_PATH, GSHEETS_SPREADSHEET_ID
from models.schemas import YateData

logger = setup_logger("GSheetsExporter")

class GoogleSheetsExporter:
    """Maneja la exportación de datos de Yatezzitos a Google Sheets."""

    def __init__(self):
        self.client = None
        self.sheet = None
        self._authenticate()

    def _authenticate(self):
        try:
            scopes = ['https://www.googleapis.com/auth/spreadsheets']
            if not os.path.exists(GSHEETS_CREDENTIALS_PATH):
                logger.error(f"Archivo de credenciales JSON no encontrado en {GSHEETS_CREDENTIALS_PATH}")
                return

            creds = ServiceAccountCredentials.from_json_keyfile_name(GSHEETS_CREDENTIALS_PATH, scopes)
            self.client = gspread.authorize(creds)
            self.sheet = self.client.open_by_key(GSHEETS_SPREADSHEET_ID).sheet1
            logger.info("Autenticado exitosamente con Google Sheets API.")
        except Exception as e:
            logger.error(f"Error conectando a Google Sheets: {e}")

    def export_data(self, dataset: List[YateData]):
        """Borra la hoja y escribe los nuevos datos."""
        if not self.sheet:
            logger.error("No se puede exportar sin inicializar la hoja.")
            return

        encabezados = [
            "Nombre del Yate", "Ciudad/Ubicación", "Categoría", "URL de Reserva", 
            "Precio", "Capacidad", "Amenidades/Incluye", 
            "Año Construcción", "Ubicación de Abordaje", 
            "Descripción", "URL Descarga Brochure", "URL Descarga Imágenes"
        ]
        
        filas = []
        for yate in dataset:
            amenidades_str = ", ".join(yate.amenidades)
            filas.append([
                yate.nombre,
                yate.puerto,
                yate.categoria,
                yate.url,
                yate.precio,
                yate.capacidad,
                amenidades_str,
                yate.anio_construccion,
                yate.ubicacion_abordaje,
                yate.descripcion,
                yate.url_descarga_pdf,
                yate.url_descarga_imagenes
            ])

        try:
            logger.info("Limpiando hoja actual...")
            self.sheet.clear()
            
            logger.info("Escribiendo encabezados y batch de datos...")
            # Escribimos los encabezados primero
            self.sheet.append_row(encabezados)
            
            # Subimos en batch
            if filas:
                rango = f'A2:L{len(filas) + 1}'
                self.sheet.update(rango, filas)

            logger.info(f"✅ Google Sheets actualizado correctamente con {len(filas)} yates.")
        except Exception as e:
            logger.error(f"Error subiendo datos a Google Sheets: {e}")
