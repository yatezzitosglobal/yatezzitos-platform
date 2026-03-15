from pydantic import BaseModel, Field, field_validator
from typing import List, Optional

class YateData(BaseModel):
    nombre: str = Field(default="No disponible")
    url: str
    puerto: str = Field(default="No especificado")
    categoria: str = Field(default="No especificado")
    precio: str = Field(default="No especificado")
    capacidad: str = Field(default="No especificado")
    modalidad: str = Field(default="No especificado") # Horas minimas etc.
    amenidades: List[str] = Field(default_factory=list)
    descripcion: str = Field(default="No especificado")
    anio_construccion: str = Field(default="-")
    ubicacion_abordaje: str = Field(default="No especificado")
    url_descarga_pdf: str = Field(default="No disponible")
    url_descarga_imagenes: str = Field(default="No disponible")
    
    # Insights comerciales inferidos
    perfil_ideal: str = Field(default="No especificado")
    observaciones: str = Field(default="")

    @field_validator('amenidades')
    @classmethod
    def set_amenidades_default(cls, v):
        if not v:
            return ["No especificadas"]
        return v

    @field_validator('puerto', 'categoria')
    @classmethod
    def clean_text(cls, v):
        if not v:
            return "No especificado"
        return v.strip().title()

class CategoriaData(BaseModel):
    nombre: str
    yates_representativos: List[YateData] = Field(default_factory=list)
    
class PuertoData(BaseModel):
    nombre: str
    url_origen: str = ""
    categorias: List[CategoriaData] = Field(default_factory=list)
