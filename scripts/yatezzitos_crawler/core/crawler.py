import httpx
from bs4 import BeautifulSoup
import xml.etree.ElementTree as ET
from typing import List, Set, Dict

from config.settings import DEFAULT_HEADERS, TIMEOUT, setup_logger
from core.classifier import URLClassifier

logger = setup_logger("Crawler")

class YatezzitosCrawler:
    def __init__(self, base_url: str):
        self.base_url = base_url
        self.client = httpx.Client(headers=DEFAULT_HEADERS, timeout=TIMEOUT, follow_redirects=True)

    def fetch_sitemap_urls(self, sitemap_url: str) -> List[str]:
        """Obtiene todas las URLs de un sitemap.xml."""
        urls = []
        try:
            logger.info(f"Obteniendo sitemap principal: {sitemap_url}")
            response = self.client.get(sitemap_url)
            response.raise_for_status()
            
            # Parsear XML (puede ser sitemap_index o urlset)
            root = ET.fromstring(response.content)
            
            # Un sitemap a menudo usa namespaces
            namespace = ""
            if "}" in root.tag:
                namespace = root.tag.split("}")[0] + "}"

            # Si es un sitemap_index, parsear sub-sitemaps
            if "sitemapindex" in root.tag:
                sub_sitemaps = [elem.text for elem in root.findall(f".//{namespace}loc") if elem.text]
                for sub in sub_sitemaps:
                    # Opcionalmente, filtrar sitemaps que sabemos no sirven (como el de categorias de blog)
                    if "post-sitemap" in sub or "author-sitemap" in sub:
                         continue
                    urls.extend(self.fetch_sitemap_urls(sub))
            else:
                # Es un sitemap final con URLs
                urls = [elem.text for elem in root.findall(f".//{namespace}loc") if elem.text]

        except Exception as e:
            logger.error(f"Error parseando sitemap {sitemap_url}: {e}")
            
        return urls

    def discover_all_urls(self) -> Dict[str, Set[str]]:
        """Descubre URLs usando el sitemap como base principal."""
        sitemap_entry = f"{self.base_url}/sitemap_index.xml"
        all_urls = self.fetch_sitemap_urls(sitemap_entry)
        logger.info(f"Se encontraron un total de {len(all_urls)} URLs crudas en el Sitemap.")

        categorized_urls = {
            "destinations": set(),
            "categories": set(),
            "products": set(),
            "unknown": set()
        }

        for url in all_urls:
            if not URLClassifier.is_valid_internal_url(url):
                continue
            
            url_type = URLClassifier.classify_url(url)
            if url_type == "destination":
                categorized_urls["destinations"].add(url)
            elif url_type == "category":
                categorized_urls["categories"].add(url)
            elif url_type == "product":
                categorized_urls["products"].add(url)
            else:
                categorized_urls["unknown"].add(url)
        
        logger.info(f"Clasificadas: {len(categorized_urls['destinations'])} destinos, "
                    f"{len(categorized_urls['categories'])} categorias, "
                    f"{len(categorized_urls['products'])} productos.")
        
        return categorized_urls

    def close(self):
        self.client.close()
