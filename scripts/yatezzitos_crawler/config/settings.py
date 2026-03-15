import os
import logging
from pathlib import Path

# Directorios principales
BASE_DIR = Path(__file__).resolve().parent.parent
OUTPUT_DIR = BASE_DIR / 'output'
LOGS_DIR = BASE_DIR / 'logs'

# Crear directorios si no existen
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
(OUTPUT_DIR / 'data').mkdir(parents=True, exist_ok=True)
(OUTPUT_DIR / 'puertos').mkdir(parents=True, exist_ok=True)
LOGS_DIR.mkdir(parents=True, exist_ok=True)

# Configuración del Crawler
BASE_URL = "https://yatezzitos.com"
SITEMAP_URL = f"{BASE_URL}/sitemap_index.xml"
REQUEST_DELAY = 1.0  # Segundos entre peticiones normales
TIMEOUT = 10.0       # Segundos máximos de espera
MAX_RETRIES = 3

# Configuración de Headers por defecto
DEFAULT_HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
    "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8",
    "Accept-Language": "es-ES,es;q=0.9,en;q=0.8",
}

# Límites de pruebas (0 = sin límite)
MAX_ITEMS_PER_CATEGORY = 0 
MAX_CATEGORIES_PER_PORT = 0

DESTINOS_OFICIALES = {
    "Mazatlán": "https://yatezzitos.com/es/ciudad/renta-de-yates-mazatlan/",
    "Cancún": "https://yatezzitos.com/es/ciudad/renta-de-yates-cancun/",
    "Playa del Carmen": "https://yatezzitos.com/es/ciudad/yates-playa-del-carmen/",
    "La Paz": "https://yatezzitos.com/es/ciudad/renta-de-yates-en-la-paz/",
    "Ixtapa": "https://yatezzitos.com/es/ciudad/yates-ixtapa/",
    "Puerto Vallarta": "https://yatezzitos.com/es/ciudad/renta-de-yates-en-puerto-vallarta/",
    "Nuevo Vallarta": "https://yatezzitos.com/es/ciudad/yates-en-nuevo-vallarta/",
    "Huatulco": "https://yatezzitos.com/es/ciudad/yates-huatulco/",
    "Los Cabos": "https://yatezzitos.com/es/ciudad/yates-los-cabos/",
    "Acapulco": "https://yatezzitos.com/es/ciudad/yates-acapulco/"
}

# Configuración de Logging
LOG_FORMAT = "%(asctime)s - %(name)s - %(levelname)s - %(message)s"
LOG_FILE = LOGS_DIR / "crawler.log"

def setup_logger(name: str) -> logging.Logger:
    """Configura y retorna un logger estandarizado."""
    logger = logging.getLogger(name)
    logger.setLevel(logging.INFO)

    # Evitar duplicar handlers subyacentes
    if not logger.handlers:
        # Handler de consola
        console_handler = logging.StreamHandler()
        console_handler.setFormatter(logging.Formatter(LOG_FORMAT))
        
        # Handler de archivo
        file_handler = logging.FileHandler(LOG_FILE, encoding='utf-8')
        file_handler.setFormatter(logging.Formatter(LOG_FORMAT))

        logger.addHandler(console_handler)
        logger.addHandler(file_handler)

    return logger

# Google Sheets Config
# Asegúrate de colocar tu path real aquí cuando se ejecute, o usar variable de entorno
GSHEETS_CREDENTIALS_PATH = os.getenv("GSHEETS_CREDENTIALS_PATH", "/Users/luisvelazquez/Desktop/yatezzitos-371522-02dbd98bdc5f.json")
GSHEETS_SPREADSHEET_ID = os.getenv("GSHEETS_SPREADSHEET_ID", "1_kJ6OxzcvMDNfcrBGwLmX_x6pOULy5CfptL-YMCdclE")
