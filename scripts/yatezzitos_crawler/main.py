import time
from config.settings import setup_logger, BASE_URL, DESTINOS_OFICIALES
from core.crawler import YatezzitosCrawler
from core.selector import YateSelector
from core.extractor import YateExtractor
from exporters.gsheets_exporter import GoogleSheetsExporter
from models.schemas import YateData
from typing import List

logger = setup_logger("Main")

def main():
    logger.info("Iniciando Yatezzitos AI Dataset Crawler v1.0")
    start_time = time.time()
    
    # 1. Pipeline Inicial: Descubrir URLs
    # Se reemplaza el descubrimiento automático del sitemap por los URLs directos validados 
    # por el usuario para asegurar que Houzez cargue el DOM correcto de las tarjetas.
    destinations = list(DESTINOS_OFICIALES.values())

    # 2. Pipeline Medio: Seleccionar embarcaciones usando el selector
    selector = YateSelector()
    logger.info("Fase 4: Seleccionando URLs de embarcaciones por cada destino...")
    selected_dict = selector.select_representative_boats(destinations)

    # Convertir el dic a una lista de (url, puerto, categoria)
    urls_to_scrape = []
    for puerto, categorias in selected_dict.items():
        for categoria, urls in categorias.items():
            for url in urls:
                urls_to_scrape.append((url, puerto, categoria))

    logger.info(f"Total de URLs seleccionadas para scrape profundo: {len(urls_to_scrape)}")

    # 3. Pipeline Final: Extraccion Profunda (Selenium enable/disabled configurable)
    extractor = YateExtractor(use_selenium=True)
    dataset: List[YateData] = []
    
    logger.info("Fase 5: Extrayendo informacion profunda y generando PDFs links")
    for idx, item in enumerate(urls_to_scrape, 1):
        url, puerto, categoria = item
        logger.info(f"Scrapeando [{idx}/{len(urls_to_scrape)}]: {url}")
        
        try:
            # Descargamos HTML puro via httpx para agilizar bs4
            response = extractor.driver.request("GET", url) if False else None # we need httpx or requests
            import requests # fallback inline for now
            headers = {"User-Agent": "Mozilla/5.0"}
            resp = requests.get(url, headers=headers)
            
            if resp.status_code == 200:
                yate_data = extractor.extract_from_html(url, resp.text, puerto, categoria)
                dataset.append(yate_data)
            else:
                logger.warning(f"Error {resp.status_code} al solicitar {url}")
        except Exception as e:
            logger.error(f"Falló extracción individual de {url}: {e}")
            
    extractor.close()
    
    # 4. Exportar a GSheets (Prioridad 1)
    if dataset:
        logger.info("Fase 6: Exportando Dataset a Google Sheets...")
        exporter = GoogleSheetsExporter()
        exporter.export_data(dataset)
        
        logger.info("Fase 6.1: Generando Markdown Maestro para Agente IA...")
        from exporters.markdown_builder import MarkdownBuilder
        builder = MarkdownBuilder()
        builder.build_dataset(dataset)
    else:
        logger.warning("No hay datos para exportar.")

    elapsed = time.time() - start_time
    logger.info(f"Ejecución del pipeline finalizada en {elapsed:.2f} segundos.")

if __name__ == "__main__":
    main()
