# Reglas Operativas Globales para Agentes IA de Yatezzitos Global

Este archivo define las directrices y reglas operativas que todos los agentes de inteligencia artificial deben seguir al interactuar con el repositorio y los sistemas de Yatezzitos Global.

## 1. Principios Fundamentales
-   **Seguridad Primero:** Adherirse estrictamente a los guardrails de seguridad (no inventar datos, no exponer PII, no publicar secretos).
-   **Lectura Primero, Escritura Después:** Comprender completamente la tarea y el contexto antes de cualquier acción.
-   **Transparencia:** Documentar claramente todas las acciones y decisiones en Pull Requests y comentarios.

## 2. Convenciones de Trabajo
-   **Ramas:** Seguir el formato `tipo/[descripción]` (ej. `fix/`, `feat/`, `docs/`, `seo/`, `ai/`).
-   **Commits:** Mensajes en inglés con prefijo convencional (ej. `feat: add new feature`).
-   **Pull Requests:** Título descriptivo, cuerpo con resumen de cambios, archivos modificados y criterios de aceptación.

## 3. Procesamiento de Tareas y Origen

Los agentes IA de Yatezzitos Global procesan tareas asignadas a través de GitHub Issues. Es crucial entender el origen y el contexto de cada tarea.

### 3.1. Tareas Originadas por WhatsApp

Algunas tareas pueden ser generadas automáticamente desde mensajes de WhatsApp, pasando por GoHighLevel antes de convertirse en un issue de GitHub.

**Características:**
-   Se identifican con la etiqueta `from-whatsapp`.
-   Las descripciones pueden ser concisas o informales, reflejando la naturaleza de la comunicación por chat.

**Instrucciones para Agentes:**
-   **Contexto:** Al ver la etiqueta `from-whatsapp`, el agente debe inferir que la solicitud proviene de una conversación rápida y puede requerir una interpretación más cuidadosa del objetivo.
-   **Claridad en PR:** Es fundamental que el Pull Request resultante tenga una descripción clara, detallada y formal, traduciendo la solicitud inicial a un lenguaje de desarrollo estándar.
-   **Prioridad:** Aunque el origen sea WhatsApp, la prioridad de la tarea se determinará por las etiquetas de GitHub (ej. `bug`, `enhancement`) y el backlog general.