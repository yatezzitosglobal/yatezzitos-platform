# Guía para la Integración de Contenido Generado por IA ("Cuadernos")

Este documento describe el proceso y las capacidades del Copilot Coding Agent para integrar ("subir") contenido o documentación generada por otras tareas de inteligencia artificial o procesos externos, referidos coloquialmente como "cuadernos".

## 1. Interpretación de "Cuadernos"

Para el Copilot Coding Agent, un "cuaderno" se interpreta como cualquier pieza de contenido, documentación, informe o borrador textual (en formato Markdown, HTML o texto plano) que ha sido generado por un agente de IA o un sistema externo (como el "mcp de notebook lm" mencionado en el Issue #110) y que necesita ser incorporado al repositorio de Yatezzitos Global.

Ejemplos de "cuadernos" incluyen:
*   Artículos de blog SEO.
*   Descripciones de fichas de yates.
*   Borradores de mensajes para CRM (GoHighLevel).
*   Análisis o reportes SEO.
*   Documentación técnica o de procesos.

## 2. Proceso de Integración (Subida)

Cuando se le solicita al agente "subir" un "cuaderno", se sigue el siguiente flujo de trabajo:

1.  **Recepción del Contenido:** El agente recibe el contenido del "cuaderno" (directamente en el issue, como un archivo adjunto o una referencia a un recurso).
2.  **Identificación del Tipo de Contenido:** El agente analiza el contenido y las instrucciones de la tarea para determinar su tipo y propósito (ej. ¿Es un blog post SEO? ¿Una ficha de yate? ¿Documentación interna?). Para esto, consulta las secciones relevantes de las `Instrucciones Maestras` y los archivos de contexto en `docs/ai/`.
    *   **Contenido SEO:** Se aplican las reglas de la Sección 6 (`Reglas SEO — Redacción de Contenido`) y se consulta `.agents/workflows/seo-blog-posts.md`.
    *   **Fichas de Yates:** Se aplican las reglas de la Sección 7 (`Fichas de Yates (Houzez Fleet Manager)`) y se consulta `docs/ai/houzez_fleet_skills.md`.
    *   **Contenido CRM/GoHighLevel:** Se aplican las reglas de la Sección 11 (`CRM y GoHighLevel`) y se consulta `docs/ai/ghl_crm_skills.md`.
    *   **Documentación General:** Se sigue la estructura de `docs/` y las convenciones de Markdown.
3.  **Aplicación de Reglas y Formato:** El agente formatea el contenido según las reglas específicas del tipo identificado (ej. HTML puro para WordPress, Markdown para documentación, aplicación de checklist SEO).
4.  **Selección de Ubicación en el Repositorio:** El agente determina la ubicación correcta dentro de la estructura del repositorio (ej. `docs/seo/{ciudad}/`, `data/templates/`, `docs/ai/`).
5.  **Creación/Actualización de Archivos:** El agente crea nuevos archivos o actualiza existentes con el contenido procesado.
6.  **Creación de Rama y Pull Request:** Se crea una rama siguiendo las convenciones (`seo/`, `docs/`, `feat/`) y se abre un Pull Request con una descripción clara de los cambios y el origen del "cuaderno".

## 3. Información Requerida para el Agente

Para que el agente pueda integrar un "cuaderno" de manera efectiva, es crucial proporcionar la siguiente información en la tarea:

*   **El Contenido del "Cuaderno":** El texto completo, HTML o Markdown que se desea "subir".
*   **Tipo y Propósito:** Una descripción clara de qué tipo de contenido es (ej. "Este es un artículo de blog sobre 'Renta de Yates en Cancún'") y su objetivo.
*   **Destino Deseado (Opcional pero Recomendado):** Si se conoce, la ruta específica dentro del repositorio donde debe guardarse el archivo (ej. `docs/seo/cancun/renta-yates-cancun.md`).
*   **Instrucciones Adicionales:** Cualquier requisito específico de formato, enlaces, keywords, etc., que no estén cubiertos por las reglas generales.

Al seguir esta guía, el Copilot Coding Agent puede actuar como un integrador eficiente de los resultados generados por otros sistemas o agentes de IA, asegurando que el contenido se incorpore correctamente y cumpla con los estándares del proyecto Yatezzitos Global.
