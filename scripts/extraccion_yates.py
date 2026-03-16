import requests
from bs4 import BeautifulSoup
import gspread
from google.oauth2.service_account import Credentials
import time
import re

from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By

# Ruta a tu archivo de credenciales JSON
RUTA_JSON = '/Users/luisvelazquez/Desktop/yatezzitos-371522-584b9f178fda.json'
SPREADSHEET_ID = "1_kJ6OxzcvMDNfcrBGwLmX_x6pOULy5CfptL-YMCdclE"

# Configuración de Google Sheets
def conectar_google_sheets():
    SCOPES = ['https://www.googleapis.com/auth/spreadsheets']
    creds = Credentials.from_service_account_file(RUTA_JSON, scopes=SCOPES)
    cliente = gspread.authorize(creds)
    hoja = cliente.open_by_key(SPREADSHEET_ID).sheet1
    return hoja

# Configuración de Selenium
CHROMEDRIVER_PATH = '/Users/luisvelazquez/Desktop/chromedriver'
chrome_options = Options()
chrome_options.add_argument("--headless")
chrome_options.add_argument("--no-sandbox")
chrome_options.add_argument("--disable-dev-shm-usage")
servicio = Service(CHROMEDRIVER_PATH)
driver = webdriver.Chrome(service=servicio, options=chrome_options)

# Extraer los detalles de un yate individual
def obtener_detalle_yate(url):
    headers = {
        "User-Agent": "Mozilla/5.0"
    }
    respuesta = requests.get(url, headers=headers)
    if respuesta.status_code != 200:
        return ["No especificado"] * 7

    soup = BeautifulSoup(respuesta.content, "html.parser")

    pasajeros = "No especificado"
    tipo_embarcacion = "No especificado"
    incluye_alquiler = "No especificado"
    anio_construccion = "-"
    ubicacion_abordaje = "No especificado"
    descripcion_yate = "No especificado"
    url_descarga = "No disponible"

    lista_items = soup.find_all("li", class_="property-overview-item")
    for item in lista_items:
        strong_tag = item.find("strong")
        if strong_tag:
            texto_fuerte = strong_tag.text.strip().upper()
            if "PASAJEROS" in texto_fuerte or "CAPACIDAD" in texto_fuerte:
                pasajeros = texto_fuerte.split(" ")[0]
            if any(tipo in texto_fuerte for tipo in ["VELEROS", "LANCHAS", "YATES", "CATAMARÁN", "LUJO", "ACCESIBLES", "PESCA DEPORTIVA"]):
                tipo_embarcacion = texto_fuerte
            match = re.search(r'\b(19\d{2}|20\d{2})\b', texto_fuerte)
            if match:
                anio_construccion = match.group()

    incluye_section = soup.find("ul", class_="list-2-cols list-unstyled")
    if incluye_section:
        items_incluidos = [li.text.strip() for li in incluye_section.find_all("li")]
        incluye_alquiler = ", ".join(items_incluidos) if items_incluidos else "No especificado"

    ubicacion_tag = soup.find("address", class_="item-address")
    if ubicacion_tag:
        ubicacion_abordaje = ubicacion_tag.get_text(strip=True)

    descripcion_tag = soup.find("div", class_="block-content-wrap")
    if descripcion_tag:
        descripcion_yate = descripcion_tag.get_text(strip=True)

    # 🟡 Extrae el enlace de descarga usando Selenium
    try:
        driver.get(url)
        time.sleep(2)
        boton = driver.find_element(By.CSS_SELECTOR, "ul.item-tools li.houzez-download a")
        if boton:
            url_descarga = boton.get_attribute("href")
    except Exception:
        url_descarga = "No disponible"

    return [
        pasajeros,
        tipo_embarcacion,
        incluye_alquiler,
        anio_construccion,
        ubicacion_abordaje,
        descripcion_yate,
        url_descarga
    ]

# Recolecta todos los yates por ciudad
def obtener_yates_por_ciudad(destino, index_url):
    yates = []
    page = 1

    while True:
        url = f"{index_url}page/{page}/" if page > 1 else index_url
        headers = {
            "User-Agent": "Mozilla/5.0"
        }
        respuesta = requests.get(url, headers=headers)
        if respuesta.status_code != 200:
            break

        soup = BeautifulSoup(respuesta.content, "html.parser")
        tarjetas = soup.find_all("div", class_="item-body flex-grow-1")
        if not tarjetas:
            break

        for tarjeta in tarjetas:
            nombre_tag = tarjeta.find("h2", class_="item-title")
            nombre = nombre_tag.text.strip() if nombre_tag else "No disponible"
            enlace_tag = tarjeta.find_previous("a", class_="listing-featured-thumb hover-effect")
            enlace = enlace_tag["href"] if enlace_tag else "No disponible"
            precio_tag = tarjeta.find("li", class_="item-price")
            precio = precio_tag.text.strip() if precio_tag else "No disponible"

            detalles = obtener_detalle_yate(enlace)
            yates.append([nombre, destino, enlace, precio] + detalles)

        page += 1
        time.sleep(1)

    return yates

# Actualiza la hoja de cálculo de Google
def actualizar_hoja():
    hoja = conectar_google_sheets()
    encabezados = [
        "Nombre del Yate", "Ciudad/Ubicación", "Enlace / URL del yate", "Precio",
        "Capacidad de Pasajeros", "Tipo de Embarcación", "¿Qué se incluye durante el alquiler?",
        "Año de Construcción", "Ubicación de Abordaje", "Descripción del Yate", "URL de Descarga"
    ]
    hoja.clear()
    hoja.append_row(encabezados)

    destinos_urls = {
        "Mazatlán": "https://yatezzitos.com/es/ciudad/renta-de-yates-mazatlan/",
        "Cancún": "https://yatezzitos.com/es/ciudad/renta-de-yates-cancun/",
        "Playa del Carmen": "https://yatezzitos.com/es/ciudad/renta-de-yates-en-playa-del-carmen/",
        "La Paz": "https://yatezzitos.com/es/ciudad/renta-de-yates-en-la-paz/",
        "Ixtapa": "https://yatezzitos.com/es/ciudad/renta-yates-en-ixtapa/",
        "Puerto Vallarta": "https://yatezzitos.com/es/ciudad/renta-de-yates-en-puerto-vallarta/",
        "Nuevo Vallarta": "https://yatezzitos.com/es/ciudad/yates-en-nuevo-vallarta/",
        "Huatulco": "https://yatezzitos.com/es/ciudad/renta-de-yates-huatulco/",
        "Los Cabos": "https://yatezzitos.com/es/ciudad/en-ciudad-yates-cabos/",
        "Acapulco": "https://yatezzitos.com/es/ciudad/renta-de-yates-en-acapulco/"
    }

    fila_inicial = 2
    for destino, index_url in destinos_urls.items():
        yates = obtener_yates_por_ciudad(destino, index_url)
        if yates:
            hoja.batch_update([{
                'range': f'A{fila_inicial}:L{fila_inicial + len(yates) - 1}',
                'values': yates
            }])
            fila_inicial += len(yates)
            time.sleep(2)

    print("✅ Hoja de Google Sheets actualizada correctamente.")

# Punto de entrada
if __name__ == "__main__":
    actualizar_hoja()
    driver.quit()
