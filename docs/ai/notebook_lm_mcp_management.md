# Gestión de Cuadernos de IA (Notebook LM) y Proceso de Control (MCP)

## 1. Definición y Contexto

En Yatezzitos Global, el término **"MCP de Notebook LM"** (Management/Control Process for Language Model Notebooks) se refiere al conjunto de directrices y el proceso para desarrollar, documentar e integrar modelos de Inteligencia Artificial (IA) basados en Language Models (LLMs) que se implementan o se definen en "cuadernos" (ej. Jupyter Notebooks, scripts de Python, definiciones de workflows de IA).

Estos cuadernos contienen la lógica, los datos de entrenamiento, las configuraciones y las interacciones que nuestros agentes de IA (como Marina, Timón, Capitán, Ola) utilizan para realizar tareas específicas, especialmente aquellas relacionadas con el CRM (GoHighLevel), SEO, y la gestión de la flota. El MCP asegura que estos cuadernos sean gestionados de forma estructurada y segura.

## 2. Propósito de este Documento

Este documento establece el marco para "subir" (integrar, documentar y hacer operativos) los cuadernos de IA recientemente creados o actualizados. Dado que la IA no puede interactuar directamente con sistemas externos para "subir" archivos, el proceso se centra en la documentación exhaustiva dentro de este repositorio, asegurando que la lógica y el propósito de cada cuaderno sean claros y accesibles para el equipo humano y otros agentes.

## 3. Proceso para "Subir" (Integrar y Documentar) un Cuaderno de IA

Para integrar un nuevo cuaderno de IA en el ecosistema de Yatezzitos Global, sigue estos pasos:

1.  **Crear el Cuaderno:** Desarrolla la lógica del modelo de IA en el formato de cuaderno o script apropiado (ej. `.ipynb`, `.py`).
2.  **Documentar el Cuaderno:**
    *   Crea un nuevo archivo Markdown en `docs/ai/notebooks/{nombre_del_cuaderno}.md` utilizando la plantilla de la Sección 4.
    *   Este archivo debe describir el propósito, la funcionalidad, las entradas, las salidas y las dependencias del cuaderno.
    *   Si el cuaderno es parte de un agente específico (ej. Marina, Timón), asegúrate de que su especificación en `ai/assistants/` o su workflow en `.agents/workflows/` haga referencia a este nuevo cuaderno o a la habilidad que implementa.
3.  **Revisión y Aprobación:** El cuaderno y su documentación deben ser revisados por un miembro del equipo para asegurar su alineación con los objetivos del proyecto y las políticas de seguridad.
4.  **Integración Lógica:** Si el cuaderno requiere ser ejecutado por un sistema externo (ej. Cloudflare Workers, un servicio de ML), asegúrate de que la configuración para su despliegue y ejecución esté documentada y que los endpoints o triggers necesarios estén configurados.
5.  **Actualizar `AGENTS.md` y `CLAUDE.md` (si aplica):** Si el nuevo cuaderno introduce una nueva capacidad global o modifica el contexto general, actualiza los archivos de memoria persistente.

## 4. Plantilla de Documentación para Cuadernos de IA

Utiliza la siguiente plantilla para documentar cada nuevo cuaderno de IA:

```markdown
# Cuaderno de IA: [Nombre del Cuaderno]

> **Estado:** 🟡 BORRADOR (Draft) / ✅ ACTIVO (Active) / 🔴 OBSOLETO (Deprecated)
> **Última Actualización:** [Fecha de la última modificación]
> **Autor/Desarrollador:** [Nombre o ID del desarrollador]

## 4.1. Propósito y Descripción General

[Describe brevemente el objetivo principal del cuaderno. ¿Qué problema resuelve? ¿Qué funcionalidad añade?]

## 4.2. Agentes de IA Relacionados

[Lista los agentes de IA (ej. Marina, Timón, Capitán) que utilizan o se benefician de este cuaderno. Si es una habilidad independiente, indícalo.]

## 4.3. Habilidades Implementadas

[Describe las habilidades específicas o flujos de trabajo que este cuaderno implementa. Haz referencia a los documentos de `docs/ai/` si aplica (ej. "Lead Scoring" de `ghl_crm_skills.md`).]

## 4.4. Entradas (Inputs)

[Detalla los datos de entrada que el cuaderno espera. Incluye formato, origen y ejemplos si es posible.]
*   **Ejemplo:**
    *   `lead_data`: JSON con información del lead de GoHighLevel (nombre, email, historial de interacción).
    *   `yacht_id`: ID numérico de la embarcación de interés.

## 4.5. Salidas (Outputs)

[Describe los resultados que produce el cuaderno. Incluye formato, destino y ejemplos si es posible.]
*   **Ejemplo:**
    *   `score`: Puntuación de lead (entero de 1 a 100).
    *   `suggested_message`: Texto de borrador de SMS/Email para el equipo de ventas.
    *   `ghl_webhook_payload`: JSON para mover el lead en el pipeline de GHL.

## 4.6. Dependencias Técnicas

[Lista cualquier librería, API externa, servicio o configuración específica que el cuaderno requiera para funcionar.]
*   Python 3.9+
*   Librerías: `pandas`, `scikit-learn`, `requests`
*   Acceso a la API de GoHighLevel
*   Credenciales de OpenAI (para modelos de lenguaje)

## 4.7. Ubicación del Código Fuente

[Proporciona la ruta relativa al archivo del cuaderno dentro del repositorio o una referencia a su ubicación de despliegue si es externo.]
*   `scripts/ai/lead_scoring_model.ipynb`
*   `integrations/cloudflare-workers/ghl-lead-triager.js`

## 4.8. Notas Adicionales y Consideraciones

[Cualquier otra información relevante: limitaciones, futuras mejoras, cómo probarlo, etc.]
```