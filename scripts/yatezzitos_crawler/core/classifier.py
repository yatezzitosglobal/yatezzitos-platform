import re
from typing import Dict, Optional

class URLClassifier:
    """Clasifica URLs de Yatezzitos para enrutarlas al scraper adecuado."""

    @staticmethod
    def is_valid_internal_url(url: str) -> bool:
        """Verifica si es un enlace interno válido procesable."""
        if not url or "yatezzitos.com" not in url:
            return False
        
        # Ignorar recursos o páginas administrativas
        ignore_patterns = [
            r'/wp-', r'/cart', r'/checkout', r'/my-account',
            r'\.jpg$', r'\.png$', r'\.pdf$', r'\.xml$', r'/contacto/', 
            r'/nosotros/', r'/terminos', r'/privacidad', r'/docs/'
        ]
        
        for pattern in ignore_patterns:
            if re.search(pattern, url, re.IGNORECASE):
                return False
        return True

    @staticmethod
    def classify_url(url: str) -> str:
        """
        Devuelve el tipo de página según la URL (heurística básica):
        - 'destination': Página lista de destino (ej. /ciudad/renta-de-yates-cancun/)
        - 'category': Página de categoría
        - 'product': Ficha de embarcación (ej. /yates/nombre-yate/)
        - 'unknown': Otros
        """
        url = url.strip('/').lower()

        # Detección de Destinos (viendo el script original usan /ciudad/)
        if "/ciudad/" in url or "/en-ciudad-" in url:
            return "destination"
        
        # Detección de Categorías (/tipo/ o /status/ o /yates/ como listado)
        if "/tipo/" in url or "/status/" in url or "/renta-de-" in url:
            return "category"
        
        # Detección de Productos (usualmente /embarcacion/, /propiedad/)
        if "/embarcacion/" in url or "/propiedad/" in url:
            parts = [p for p in url.split("/") if p]
            if len(parts) >= 3: # ej: https://yatezzitos.com/es/embarcacion/nombre-del-yate
                return "product"

        return "unknown"
