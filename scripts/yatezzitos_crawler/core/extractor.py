import re
import time
from bs4 import BeautifulSoup

from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager

from config.settings import setup_logger
from models.schemas import YateData

logger = setup_logger("Extractor")

class YateExtractor:
    """Extrae la información de la ficha de un yate individual."""
    
    def __init__(self, use_selenium: bool = True):
        self.use_selenium = use_selenium
        self.driver = None
        if self.use_selenium:
            self.setup_selenium()

    def setup_selenium(self):
        try:
            chrome_options = Options()
            chrome_options.add_argument("--headless")
            chrome_options.add_argument("--no-sandbox")
            chrome_options.add_argument("--disable-dev-shm-usage")
            # Usa webdriver_manager para no depender de ruteos fijos
            service = Service(ChromeDriverManager().install())
            self.driver = webdriver.Chrome(service=service, options=chrome_options)
            logger.info("Selenium web driver iniciado correctamente.")
        except Exception as e:
            logger.error(f"Error al iniciar Selenium. Se continuará sin extracción de PDF. Error: {e}")
            self.use_selenium = False
            self.driver = None

    def extract_from_html(self, url: str, html_content: str, puerto: str = "", categoria: str = "") -> YateData:
        """Parsea el HTML de la página de la embarcación y extrae datos."""
        soup = BeautifulSoup(html_content, "html.parser")
        
        yate = YateData(url=url, puerto=puerto, categoria=categoria)
        
        # 1. Título
        title_tag = soup.find("h1", class_="page-title") or soup.find("h1")
        if title_tag:
            yate.nombre = title_tag.get_text(strip=True)
            
        # 2. Precio
        price_tag = soup.find("li", class_="item-price") or soup.find("span", class_="item-price")
        if price_tag:
            yate.precio = price_tag.get_text(strip=True)

        # 3. Detalles Generales (capacidad, categoría específica, año)
        lista_items = soup.find_all("li", class_="property-overview-item")
        for item in lista_items:
            strong_tag = item.find("strong")
            if strong_tag:
                texto_fuerte = strong_tag.text.strip().upper()
                if "PASAJEROS" in texto_fuerte or "CAPACIDAD" in texto_fuerte:
                    yate.capacidad = texto_fuerte.split(" ")[0]
                if any(tipo in texto_fuerte for tipo in ["VELEROS", "LANCHAS", "YATES", "CATAMARÁN", "LUJO", "ACCESIBLES", "PESCA DEPORTIVA"]):
                    if yate.categoria == "No especificado": # Solo si no lo trajimos del router
                        yate.categoria = texto_fuerte
                # Año Regex
                match = re.search(r'\b(19\d{2}|20\d{2})\b', texto_fuerte)
                if match:
                    yate.anio_construccion = match.group()

        # 4. Amenidades / Incluye
        features_div = soup.find(string=lambda t: t and "Servicios incluidos" in t)
        if features_div:
            parent_wrap = features_div.find_parent("div", class_="block-content-wrap")
            if parent_wrap:
                ul = parent_wrap.find("ul")
                if ul:
                    yate.amenidades = [li.get_text(strip=True) for li in ul.find_all("li") if li.get_text(strip=True)]

        # 5. Ubicación
        ubicacion_tag = soup.find("address", class_="item-address")
        if ubicacion_tag:
            yate.ubicacion_abordaje = ubicacion_tag.get_text(strip=True)

        # 6. Descripción
        descripcion_tag = soup.find("div", class_="block-content-wrap")
        if descripcion_tag:
            yate.descripcion = descripcion_tag.get_text(strip=True)[:1000] # Limitamos a los primeros 1000 char

        # 7 y 8. URLs de Descarga (PDF y Drive) via Selenium si es necesario
        pdf_tag = soup.select_one("ul.item-tools li.houzez-download a")
        if pdf_tag and pdf_tag.has_attr("href"):
            yate.url_descarga_pdf = pdf_tag["href"]

        drive_tag = soup.select_one("li.yzz-download-tool a")
        if drive_tag and drive_tag.has_attr("href"):
            yate.url_descarga_imagenes = drive_tag["href"]

        if self.use_selenium and self.driver and (yate.url_descarga_pdf == "No disponible" or yate.url_descarga_imagenes == "No disponible"):
            dynamic_links = self.extract_dynamic_links_with_selenium(url)
            if yate.url_descarga_pdf == "No disponible" and dynamic_links.get("pdf"):
                yate.url_descarga_pdf = dynamic_links["pdf"]
            if yate.url_descarga_imagenes == "No disponible" and dynamic_links.get("drive"):
                yate.url_descarga_imagenes = dynamic_links["drive"]

        return yate
        
    def extract_dynamic_links_with_selenium(self, url: str) -> dict:
        """Intenta extraer las URLs de los botones usando Selenium."""
        links = {"pdf": "No disponible", "drive": "No disponible"}
        try:
            self.driver.get(url)
            time.sleep(1.5) # Wait for JS to attach href
            
            try:
                boton_pdf = self.driver.find_element(By.CSS_SELECTOR, "ul.item-tools li.houzez-download a")
                links["pdf"] = boton_pdf.get_attribute("href") or "No disponible"
            except:
                pass
                
            try:
                boton_drive = self.driver.find_element(By.CSS_SELECTOR, "li.yzz-download-tool a")
                links["drive"] = boton_drive.get_attribute("href") or "No disponible"
            except:
                pass
                
            return links
        except Exception:
            return links

    def close(self):
        """Cierra la sesión de Selenium."""
        if self.driver:
            self.driver.quit()
