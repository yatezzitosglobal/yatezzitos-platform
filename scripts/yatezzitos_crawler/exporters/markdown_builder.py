import os
from pathlib import Path
from typing import List, Dict
from models.schemas import YateData
from config.settings import OUTPUT_DIR, setup_logger

logger = setup_logger("MarkdownBuilder")

class MarkdownBuilder:
    def __init__(self):
        self.output_dir = OUTPUT_DIR / 'data' / 'knowledge_bases'
        
    def build_dataset(self, dataset: List[YateData]):
        """Genera un archivo Markdown estructurado para RAG por cada Destino."""
        logger.info(f"Generando Bases de Conocimiento en {self.output_dir}")
        self.output_dir.mkdir(parents=True, exist_ok=True)
        
        # Agrupar por destino y luego por categoría
        grouped_data: Dict[str, Dict[str, List[YateData]]] = {}
        for yate in dataset:
            if yate.puerto not in grouped_data:
                grouped_data[yate.puerto] = {}
            if yate.categoria not in grouped_data[yate.puerto]:
                grouped_data[yate.puerto][yate.categoria] = []
            grouped_data[yate.puerto][yate.categoria].append(yate)
            
        from config.settings import DESTINOS_OFICIALES
        archivos_generados = 0
        
        # Iterar sobre Puertos y crear un archivo individual para cada uno
        for puerto, categorias in grouped_data.items():
            md_content = []
            
            # Cabecera minimalista
            md_content.append(f"# Catálogo: {puerto}")
            md_content.append(f"Resumen de flota por categorías: " + ", ".join([f"`{c}`" for c in categorias.keys()]))
            
            # Iterar sobre las Categorías de este puerto
            for categoria, yates in categorias.items():
                md_content.append(f"\n## {categoria.capitalize()}s")
                for yate in yates:
                    md_content.append(f"- **{yate.nombre}**: URL: {yate.url}")
                    
                    # Precio y Modalidad
                    precio_mod = []
                    if yate.precio and yate.precio != "No especificado":
                        precio_mod.append(f"💸 {yate.precio.replace(chr(10), ' ')}")
                    if yate.modalidad and yate.modalidad != "No especificado":
                        precio_mod.append(f"⏱️ {yate.modalidad.replace(chr(10), ' ')}")
                    if precio_mod:
                        md_content.append("  " + " | ".join(precio_mod))
                        
                    # Capacidad y Año
                    cap_year = []
                    if yate.capacidad and str(yate.capacidad) != "No especificado":
                        cap_year.append(f"👥 {yate.capacidad} pax")
                    if yate.anio_construccion and str(yate.anio_construccion) != "No especificado":
                        cap_year.append(f"📅 Año {yate.anio_construccion}")
                    if cap_year:
                        md_content.append("  " + " | ".join(cap_year))
                    
                    # Descripción
                    desc = yate.descripcion
                    if desc and desc != "No especificado":
                        md_content.append(f"  📝 {desc[:100]}..." if len(desc) > 100 else f"  📝 {desc}")
                    
                    # Amenidades
                    if yate.amenidades:
                        valid_am = [a for a in yate.amenidades if a != "No especificado" and a != "No aplicable"]
                        if valid_am:
                            amenidades_str = ", ".join(valid_am[:3])
                            md_content.append(f"  ✨ {amenidades_str}...")
            
            # Unir todo con un salto de línea simple para ahorrar caracteres
            final_content = "\n".join(md_content)
            
            # Escribir el log file para la ciudad actual
            file_name = f"marina_rag_{puerto.lower().replace(' ', '_')}.md"
            city_file = self.output_dir / file_name
            
            with open(city_file, "w", encoding="utf-8") as f:
                f.write(final_content)
                
            logger.info(f"[{puerto}] Gen: {len(final_content)} chars")
            archivos_generados += 1

        logger.info(f"✅ {archivos_generados} bases generadas en {self.output_dir}")
        return self.output_dir
