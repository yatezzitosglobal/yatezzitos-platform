# Workflow: Master Content Processor with Language Model (MCP-LM) for "Cuadernos"

## 1. Propósito

Este documento define el concepto de "Master Content Processor with Language Model" (MCP-LM) dentro del ecosistema de Yatezzitos Global y establece un flujo de trabajo para el procesamiento de "cuadernos" (entendidos como borradores de contenido generados o gestionados por agentes IA o humanos).

El objetivo es asegurar que todo el contenido generado cumpla con los estándares de calidad, SEO y seguridad del proyecto antes de ser entregado para revisión humana o publicación.

## 2. Definiciones Clave

-   **MCP-LM (Master Content Processor with Language Model):** Un sistema conceptual o un conjunto de workflows automatizados que utiliza modelos de lenguaje (LM) para procesar, validar y preparar borradores de contenido ("cuadernos"). Su función principal es actuar como un intermediario inteligente entre la generación de contenido y la revisión/publicación, aplicando las reglas del proyecto de forma consistente.
-   **"Cuadernos":** Se refiere a cualquier pieza de contenido en estado de borrador o pre-publicación dentro del repositorio. Esto incluye, pero no se limita a:
    -   Artículos de blog SEO (`docs/seo/{ciudad}/{slug}.md` y `{slug}.html`).
    -   Descripciones de fichas de yates (según `docs/ai/houzez_fleet_skills.md` outputs).
    -   Borradores de mensajes para CRM (GoHighLevel) (según `docs/ai/ghl_crm_skills.md` outputs).
    -   Documentación interna o reportes generados.

## 3. Flujo de Trabajo del MCP-LM para "Cuadernos"

El siguiente flujo describe cómo el MCP-LM procesaría los "cuadernos" recién creados o actualizados:

### 3.1. Identificación de "Cuadernos" Recientes

1.  **Monitoreo del Repositorio:** El MCP-LM monitorea las carpetas de contenido (`docs/seo/`, `data/`, etc.) en busca de nuevos archivos `.md` o `.html` o modificaciones recientes.
2.  **Detección de Estado `BORRADOR`:** Prioriza archivos marcados explícitamente como `> **Estado:** 🟡 BORRADOR (Draft)` en su encabezado.

### 3.2. Procesamiento y Validación (Según Tipo de Contenido)

Una vez identificado un "cuaderno", el MCP-LM aplica las reglas de validación pertinentes:

#### A. Para Contenido SEO (Blog Posts, Descripciones Largas)

El MCP-LM ejecuta un checklist automatizado basado en la Sección 6 ("Reglas SEO — Redacción de Contenido") de las Instrucciones Maestras:

-   **Keyword:**
    -   Verificar presencia al **INICIO** del título SEO, meta descripción y slug (si aplica).
    -   Contar menciones y asegurar distribución equitativa (~1 cada 400 caracteres, **máx. absoluto:** 11 total).
    -   Verificar que menos del 50% de H2 contengan la keyword (mínimo 3 H2 con keyword).
-   **Naturalidad del Texto:**
    -   Realizar un análisis de fluidez y naturalidad (no keyword stuffing, no frases robóticas).
-   **Legibilidad (Yoast SEO):**
    -   Calcular porcentaje de palabras de transición (>30%).
    -   Verificar longitud de párrafos bajo subtítulos (<300 palabras).
    -   Analizar complejidad de palabras (<10% complejas).
-   **Formato de Entrega:**
    -   Confirmar que el HTML es puro y listo para WordPress (sin Markdown residual).
    -   Validar uso correcto de etiquetas estructurales (`<h1>`, `<h2>`, `<p>`, `<ul>`, etc.).
-   **Enlaces Obligatorios:**
    -   Verificar enlaces internos a guías de ciudad (inicio y fin).
    -   Confirmar enlaces internos a playas/destinos mencionados.
    -   Asegurar al menos 1 enlace externo de autoridad.
    -   Validar cross-linking en series de contenido.
-   **Organización de Archivos:**
    -   Confirmar ubicación correcta en `docs/seo/{ciudad}/`.
    -   Verificar formato dual (`.md` y `.html`).
-   **Elementos Adicionales:**
    -   Revisar presencia de CTA con WhatsApp, correo y catálogo al final.
    -   Sugerir ubicación para 2-3 imágenes con atribución en comentarios HTML.

#### B. Para Fichas de Yates (Houzez Fleet Manager)

El MCP-LM consulta `docs/ai/houzez_fleet_skills.md` y aplica:

-   Transformación de datos crudos a descripción HTML premium.
-   Asignación de taxonomías (Destino, Tipo).
-   Llenado de metadatos Yoast (`_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw`).
-   Verificación de naturalidad y tono premium.

#### C. Para Borradores de CRM (GoHighLevel)

El MCP-LM consulta `docs/ai/ghl_crm_skills.md` y aplica:

-   Análisis de contexto para mensajes hiper-personalizados.
-   Verificación de tono de marca (premium, cálido, claro).
-   Asegurar que el mensaje sea un "Borrador de SMS/Email" o "Nota Interna" y **NO** se envíe directamente.

### 3.3. Generación de Reporte y Actualización de Estado

1.  **Reporte de Validación:** El MCP-LM genera un reporte detallado con los resultados de la validación, incluyendo:
    -   Elementos que cumplen los requisitos.
    -   Elementos que requieren atención o corrección.
    -   Sugerencias de mejora.
2.  **Actualización de Estado:** Si el "cuaderno" pasa la validación inicial, el MCP-LM puede sugerir cambiar el estado a `> **Estado:** 🟢 LISTO PARA REVISIÓN (Ready for Review)` o similar, o simplemente dejar el reporte como un comentario en el PR.
3.  **Creación de PR:** El MCP-LM puede crear un Pull Request con los cambios sugeridos o el reporte de validación, etiquetando al equipo humano para revisión.

## 4. Guardrails de Seguridad y Operación

-   **No Publicación Directa:** El MCP-LM **NUNCA** publica contenido directamente en WordPress o envía mensajes a clientes. Todo pasa por revisión humana.
-   **No Modificación Destructiva:** El MCP-LM no borra archivos masivamente ni realiza cambios destructivos sin confirmación explícita.
-   **Transparencia:** Todos los análisis y sugerencias son transparentes y se documentan claramente.

Este flujo de trabajo permite al MCP-LM optimizar la calidad y el cumplimiento del contenido, liberando tiempo al equipo humano para tareas de mayor valor añadido.