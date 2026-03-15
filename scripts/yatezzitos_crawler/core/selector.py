import httpx
from bs4 import BeautifulSoup
from typing import List, Dict, Set
from collections import defaultdict
import time

from config.settings import DEFAULT_HEADERS, TIMEOUT, MAX_ITEMS_PER_CATEGORY, setup_logger

logger = setup_logger("Selector")

class YateSelector:
    """Navega por las páginas de destino para descubrir y seleccionar 1-3 embarcaciones por categoría."""
    
    def __init__(self):
        self.client = httpx.Client(headers=DEFAULT_HEADERS, timeout=TIMEOUT, follow_redirects=True)

    def select_representative_boats(self, destination_urls: Set[str]) -> Dict[str, Dict[str, List[str]]]:
        """
        Retorna { "Cancun": { "Yate": [url1, url2], "Catamaran": [url3] }, ... }
        """
        selected_boats = defaultdict(lambda: defaultdict(list))
        
        for dest_url in destination_urls:
            logger.info(f"Explorando destino: {dest_url}")
            try:
                # Extraemos un nombre legible del destino desde la URL
                dest_name = dest_url.strip("/").split("/")[-1].replace("renta-de-yates-en-", "").replace("renta-de-yates-", "").replace("-", " ").title()
                
                # Paginacion basica (solo checamos hasta 3 paginas para no saturar si ya llenamos los cupos)
                for page in range(1, 4):
                    url = f"{dest_url}page/{page}/" if page > 1 else dest_url
                    import requests
                    headers = {"User-Agent": "Mozilla/5.0"}
                    # Usar requests como en el script original para evitar que CloudFlare 
                    # bloquee el cliente httpx.
                    response = requests.get(url, headers=headers)
                    
                    if response.status_code != 200:
                        logger.warning(f"Error {response.status_code} al bajar {url}")
                        break # No hay mas paginas
                        
                    soup = BeautifulSoup(response.content, "html.parser")
                    # Houzez agregó "w-100": class="item-body w-100 flex-grow-1"
                    tarjetas = soup.find_all("div", class_=lambda c: c and "item-body" in c and "flex-grow-1" in c)
                    logger.info(f"{url} | Status: {response.status_code} | Tarjetas encontradas: {len(tarjetas)}")
                    
                    if not tarjetas:
                        break
                        
                    for tarjeta in tarjetas:
                        # Extraer URL
                        enlace_tag = tarjeta.find_previous("a", class_="listing-featured-thumb")
                        if not enlace_tag:
                            continue
                        boat_url = enlace_tag["href"]
                        
                        # Extraer tipo/categoria de la tarjeta (si esta disponible)
                        # Usualmente en los listados de houzez hay un tag indicando el tipo
                        tipo = "Yate" # Default fallback
                        tipo_tag = tarjeta.find("li", class_="item-type") or tarjeta.find("span", class_="label-status")
                        if tipo_tag:
                            tipo_texto = tipo_tag.get_text(strip=True).upper()
                            # Normalizar
                            if "CATAMAR" in tipo_texto: tipo = "Catamarán"
                            elif "VELERO" in tipo_texto: tipo = "Velero"
                            elif "LANCHA" in tipo_texto: tipo = "Lancha"
                            else: tipo = "Yate"
                        else:
                            # Intentar inferir del titulo
                            nombre_tag = tarjeta.find("h2", class_="item-title")
                            nombre = nombre_tag.text.strip().upper() if nombre_tag else ""
                            if "CATAMAR" in nombre: tipo = "Catamarán"
                            elif "VELERO" in nombre: tipo = "Velero"
                            elif "LANCHA" in nombre: tipo = "Lancha"
                            
                        # Si ya tenemos suficientes de esta categoria en este destino, saltar
                        if MAX_ITEMS_PER_CATEGORY > 0 and len(selected_boats[dest_name][tipo]) >= MAX_ITEMS_PER_CATEGORY:
                            continue
                            
                        if boat_url not in selected_boats[dest_name][tipo]:
                            selected_boats[dest_name][tipo].append(boat_url)
                            logger.info(f"Seleccionado [{tipo}] en {dest_name}: {boat_url}")
                    
                    time.sleep(1.0) # Delay entre paginas
                    
            except Exception as e:
                logger.error(f"Error explorando destino {dest_url}: {e}")
                
        return selected_boats

    def close(self):
        self.client.close()
