# 🛥️ Yatezzitos AI Dataset Crawler

Motor automatizado de extracción (Web Crawler) diseñado para recorrer `yatezzitos.com`, clasificar su taxonomía de 10 destinos y múltiples categorías de embarcaciones, y **generar datasets estructurados para Inteligencia Artificial.**

---

## 📊 Resultados del Pipeline

| Métrica | Valor |
|---|---|
| Yates indexados | **175** |
| Destinos cubiertos | **10** (Cancún, Mazatlán, Vallarta, Acapulco, Huatulco, Ixtapa, Los Cabos, Playa del Carmen, Nuevo Vallarta, La Paz) |
| Bases de Conocimiento (RAG) | **10** archivos segmentados por destino (< 25k chars c/u) |
| Instrucciones del Agente | **7** documentos de prompt engineering |
| Base FAQ | **1** documento con 10 secciones |

---

## 🏗️ Arquitectura del Pipeline

```
main.py (Orquestador)
├── config/settings.py       → Variables de configuración
├── models/schemas.py        → Validación con Pydantic (YateData)
├── core/
│   ├── crawler.py           → Descubrimiento vía sitemap.xml
│   ├── selector.py          → Selección de embarcaciones por categoría
│   └── extractor.py         → Extracción híbrida (BS4 + Selenium)
├── exporters/
│   ├── gsheets_exporter.py  → Exportación a Google Sheets
│   └── markdown_builder.py  → Generador de Knowledge Bases (RAG)
└── generate_md.py           → Regeneración rápida de RAGs desde Sheets
```

---

## 📁 Estructura de Salida (`output/data/`)

```
output/data/
├── yatezzitos_knowledge_base.md          → Base monolítica (legacy)
├── knowledge_bases/                       → 📚 RAGs segmentados por destino
│   ├── marina_rag_cancun.md
│   ├── marina_rag_mazatlan.md
│   ├── marina_rag_puerto_vallarta.md
│   ├── marina_rag_la_paz.md
│   ├── marina_rag_yates_los_cabos.md
│   ├── marina_rag_yates_acapulco.md
│   ├── marina_rag_yates_huatulco.md
│   ├── marina_rag_yates_ixtapa.md
│   ├── marina_rag_yates_playa_del_carmen.md
│   └── marina_rag_yates_en_nuevo_vallarta.md
└── instrucciones_marina/                  → 🤖 Prompt Engineering del Agente
    ├── 01_personalidad.md
    ├── 02_objetivos.md
    ├── 03_faq_informacion_adicional.md
    ├── 04_seguridad_guardrails.md
    ├── 05_enrutamiento_knowledge_bases.md
    ├── 06_configuracion_ghl.md
    └── 07_blueprint_agente_ghl.md
```

---

## ⚙️ Requisitos e Instalación

1. **Python 3.11+**
2. Entorno virtual:
   ```bash
   python3 -m venv venv
   source venv/bin/activate
   ```
3. Instalar dependencias:
   ```bash
   pip install -r requirements.txt
   ```
4. **Google Sheets Config:** Coloca tu llave de servicio `.json` y apunta a ella en `config/settings.py`:
   - `GSHEETS_CREDENTIALS_PATH`
   - `GSHEETS_SPREADSHEET_ID`

---

## 🚀 Cómo Ejecutar

### 1. Extracción Completa (Crawling + Sheets + Markdown)
```bash
source venv/bin/activate
python main.py
```
*Duración: ~10-20 minutos según `REQUEST_DELAY`.*

### 2. Regeneración Rápida de RAGs (sin crawling)
Si ya hiciste el crawling y solo necesitas regenerar las bases de conocimiento:
```bash
python generate_md.py
```
*Genera los 10 archivos RAG segmentados en `output/data/knowledge_bases/`.*

---

## 🤖 Agente Marina — Sistema de IA

El crawler alimenta al agente **Marina**, concierge virtual de Yatezzitos implementado en GoHighLevel. La documentación completa del agente se encuentra en `output/data/instrucciones_marina/` e incluye:

| Archivo | Contenido |
|---|---|
| `01_personalidad.md` | Identidad, tono premium, saludo inicial, reglas de comunicación |
| `02_objetivos.md` | Flujo de cualificación (6 pasos), custom values GHL, negociación, CTA |
| `03_faq_informacion_adicional.md` | Base de Preguntas Frecuentes (10 secciones, fuente: Google Doc oficial) |
| `04_seguridad_guardrails.md` | Anti-prompt injection, protección PII, data leakage, límites de acción |
| `05_enrutamiento_knowledge_bases.md` | Instrucción de routing entre las 3 Knowledge Bases de GHL |
| `06_configuracion_ghl.md` | Textos listos para copiar en GHL: Estímulo Global + Prompt del AI Agent |
| `07_blueprint_agente_ghl.md` | Arquitectura completa de 7 fases con Router, APIs, IA Generativa |

Ver documentación detallada: [`output/data/instrucciones_marina/README.md`](output/data/instrucciones_marina/README.md)

---

## 🔒 Seguridad

- Prevención de Prompt Injection y jailbreaks
- Protección de PII (nunca solicitar datos bancarios en chat)
- Zero hallucination: no inventar precios, disponibilidad ni URLs
- Información de comisiones y márgenes protegida
- Cumple con `AGENTS.md` del repositorio principal

---

**Última actualización:** 15 de marzo 2026
**Status:** Pipeline completo. Extracción, segmentación RAG y prompt engineering finalizados.
