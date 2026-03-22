# Reglas Operativas Globales para Agentes IA de Yatezzitos Global

> Última actualización: 22 de marzo 2026

Este archivo define las reglas y directrices operativas que todos los agentes de IA deben seguir al interactuar con el ecosistema de Yatezzitos Global. Es una fuente de verdad para la conducta, capacidades y limitaciones de los agentes.

---

## 1. Principio Operativo Fundamental

**Lectura primero, escritura después.**

Antes de ejecutar cualquier acción o generar contenido, los agentes DEBEN leer y comprender completamente el contexto de la tarea, las instrucciones maestras del proyecto (`INSTRUCCIONES_MAESTRAS.md`) y los archivos de contexto específicos (`docs/ai/`).

---

## 2. Guardrails de Seguridad (No Negociables)

Los agentes están estrictamente prohibidos de:
*   Inventar datos críticos (precios, disponibilidad, capacidad de embarcaciones, fechas, horarios).
*   Exponer Información de Identificación Personal (PII) en cualquier salida (commits, logs, código, borradores de contenido).
*   Publicar secretos (llaves API, tokens, credenciales) de forma hardcodeada o en comentarios.
*   Hacer push directo a `main`.
*   Modificar archivos de producción de WordPress sin indicación explícita y supervisión.
*   Borrar archivos masivamente sin confirmación explícita.
*   Enviar mensajes directos a clientes o propietarios.
*   Ejecutar cobros, reembolsos o cancelaciones de cualquier tipo.

---

## 3. Tono y Estilo de Comunicación

*   **En código y commits:** Inglés técnico estándar.
*   **En contenido de usuario (blog, fichas, descripciones):** Español (México), profesional, premium, cálido, claro y natural. Evitar jerga técnica innecesaria y traducciones forzadas. La naturalidad humana tiene prioridad sobre la optimización SEO.

---

## 4. Fuentes de Verdad

Los agentes DEBEN consultar las fuentes de verdad designadas para obtener información precisa:
*   **Leads, pipeline, cotizaciones:** GoHighLevel (CRM).
*   **Fichas de yates, URLs, SEO:** WordPress (yatezzitos.com).
*   **Disponibilidad real:** Propietario / calendario.
*   **Pagos confirmados:** Pasarela de pago / banco.
*   **Documentación técnica:** Este repositorio (GitHub).
*   **Rendimiento SEO:** Google Search Console.

---

## 5. Gestión de Archivos de Memoria Persistente

Este repositorio utiliza dos archivos de memoria persistente:
*   `CLAUDE.md` — Contexto general del proyecto.
*   `AGENTS.md` — Reglas operativas de todos los agentes IA.

Si una tarea requiere actualizar reglas o contexto global, los agentes DEBEN actualizar AMBOS archivos. Nunca actualizar solo uno.

---

## 🛠️ Actualizaciones Operativas y Nuevas Capacidades de Agentes (2026-03)

### 1. Integración de Flujo de Tareas desde WhatsApp
Se ha implementado un nuevo flujo de trabajo para la creación de tareas:
`WhatsApp → GoHighLevel → Webhook → GitHub Issue`.
Esto significa que los agentes deben estar preparados para recibir y procesar issues que se originan directamente desde mensajes de WhatsApp, con la etiqueta `from-whatsapp`. Al completar estas tareas, se debe considerar el contexto de origen y la necesidad de una comunicación clara en el Pull Request y el comentario del issue.

### 2. Desarrollo de Habilidades CRM en GoHighLevel
Los agentes IA ahora tienen acceso a las siguientes habilidades para interactuar con GoHighLevel, siempre bajo los guardrails de seguridad (no enviar mensajes directos, solo preparar borradores o mover etapas):

*   **El Rescatista de Ventas (Abandoned Pipeline Recovery):**
    *   **Función:** Identificar contactos en la etapa "Cotización Enviada" sin actividad reciente (7 días).
    *   **Acción del Agente:** Redactar borradores de SMS/Email hiper-personalizados de rescate y dejarlos como "Borrador de SMS/Email" o "Nota Interna" en GHL, etiquetando a un cerrador.
    *   **Referencia:** `docs/ai/ghl_crm_skills.md`

*   **El Paramédico de Leads (Lead Scoring & Triager):**
    *   **Función:** Analizar metadatos de nuevos leads o interacciones para aplicar un Lead Scoring.
    *   **Acción del Agente:** Mover leads a "Alta Prioridad" en GHL si son "Hot Leads" (vía Webhook) o asignarlos a secuencias de "Nurture" si son "Cold Leads". No hay interacción directa con el cliente.
    *   **Referencia:** `docs/ai/ghl_crm_skills.md`

Estas habilidades están diseñadas para apoyar al equipo humano, optimizando la gestión de leads y la recuperación de oportunidades de venta.

---

*Este archivo es parte de la memoria persistente del proyecto Yatezzitos Global.*