# Cuaderno de Integración: Gemini, WhatsApp y Agentes IA en Yatezzitos Global

> **Estado:** 🟢 FINALIZADO (Ready for Review)
> **Fecha de Creación:** 22 de marzo de 2026
> **Autor:** Agente Copilot de Yatezzitos Global

---

## 1. Introducción: La Revolución de la Automatización con WhatsApp y Gemini

Este documento detalla la integración de nuestro canal de WhatsApp con los agentes de Inteligencia Artificial de Yatezzitos Global, potenciados por Gemini. El objetivo principal es optimizar la asignación y ejecución de tareas de desarrollo, SEO y documentación, permitiendo que los agentes IA trabajen de forma autónoma a partir de solicitudes generadas directamente desde WhatsApp, sin intervención humana en la creación de issues en GitHub.

Esta integración representa un paso crucial hacia una operación más eficiente y reactiva, transformando las conversaciones de WhatsApp en acciones concretas dentro de nuestro repositorio.

---

## 2. Contexto y Flujo de Trabajo Automatizado

Tradicionalmente, las tareas se creaban manualmente en GitHub. Con esta nueva integración, el proceso se simplifica drásticamente:

1.  **Mensaje en WhatsApp:** Un miembro del equipo envía un mensaje a nuestro número de WhatsApp Business con una solicitud o tarea.
2.  **GoHighLevel (GHL) como Puente:** El mensaje es recibido por GoHighLevel, nuestro CRM. GHL, a través de un workflow configurado, identifica que el mensaje es una solicitud de tarea.
3.  **Webhook a GitHub:** GHL dispara un Webhook hacia GitHub. Este Webhook contiene la información del mensaje de WhatsApp.
4.  **Creación Automática de Issue en GitHub:** El Webhook crea automáticamente un nuevo issue en este repositorio (`yatezzitos-platform`), utilizando el contenido del mensaje de WhatsApp como título y descripción. Se le asignan etiquetas como `from-whatsapp` y `ai-task`.
5.  **Asignación al Agente IA (Copilot):** GitHub Copilot (nuestro agente de codificación autónomo) detecta el nuevo issue con la etiqueta `ai-task`.
6.  **Ejecución de la Tarea:** El agente Copilot lee las instrucciones maestras del proyecto (este archivo), el issue, y los archivos de contexto relevantes para entender y ejecutar la tarea.
7.  **Entrega y Pull Request:** Una vez completada la tarea, el agente Copilot crea una nueva rama, realiza los cambios necesarios y abre un Pull Request (PR) en GitHub, listo para revisión humana.

Este flujo permite que las solicitudes se conviertan en acciones de desarrollo de manera casi instantánea y sin fricciones.

---

## 3. Beneficios Clave para el Equipo de Yatezzitos Global

Esta integración está diseñada para hacer el trabajo de nuestro equipo más fácil y eficiente:

*   **Agilidad Operativa:** Convierte ideas y necesidades en tareas ejecutables en minutos, directamente desde la plataforma de comunicación más usada.
*   **Reducción de Carga Administrativa:** Elimina la necesidad de crear issues manualmente en GitHub, liberando tiempo para tareas de mayor valor.
*   **Documentación Centralizada:** Todas las solicitudes y su progreso quedan registrados en GitHub, proporcionando un historial claro y auditable.
*   **Escalabilidad:** Permite manejar un mayor volumen de solicitudes sin sobrecargar al equipo humano.
*   **Consistencia:** Asegura que las tareas se asignen y ejecuten siguiendo las convenciones y guardrails del proyecto.
*   **Empoderamiento del Agente IA:** El agente Copilot puede trabajar de forma más autónoma, recibiendo instrucciones de manera directa y procesable.

---

## 4. Fuentes y Referencias para el Equipo

Para entender a fondo cómo funciona esta integración y cómo interactuar con los agentes IA, consulta los siguientes recursos dentro de este mismo repositorio:

*   **Instrucciones Maestras del Proyecto (este archivo):** Define el rol del agente Copilot, sus guardrails, convenciones y el enrutamiento de tareas.
*   **`AGENTS.md`:** Contiene las reglas operativas globales para todos los agentes IA, incluyendo cómo interpretan y ejecutan las tareas.
*   **`CLAUDE.md`:** Proporciona el contexto general y la memoria persistente del proyecto para los agentes IA.
*   **`integrations/`:** Esta carpeta puede contener scripts o configuraciones específicas relacionadas con los webhooks y la conexión entre GHL y GitHub. (Se recomienda revisar si hay archivos específicos aquí).
*   **`ai/assistants/`:** Aquí se encuentran las especificaciones y configuraciones de otros agentes IA (Marina, Timón, Capitán, Ola) que podrían interactuar con este flujo.

---

## 5. Próximos Pasos: Integración con GoHighLevel MCP

Como se mencionó en la solicitud original, la siguiente fase de esta evolución será la integración del **MCP (Master Control Program) de GoHighLevel**. Esto permitirá a nuestros agentes IA no solo recibir tareas, sino también interactuar directamente con los datos y flujos de GHL, como:

*   **Rescatista de Ventas (Abandoned Pipeline Recovery):** Identificar leads fríos y redactar mensajes de seguimiento. (Ver `docs/ai/ghl_crm_skills.md`)
*   **Paramédico de Leads (Lead Scoring & Triager):** Puntuar leads y moverlos automáticamente en el pipeline. (Ver `docs/ai/ghl_crm_skills.md`)

Cuando esta integración esté lista, este cuaderno será actualizado con las fuentes y detalles de cómo el MCP de GHL potencia aún más la capacidad de nuestros agentes IA para optimizar las operaciones de ventas y CRM.

---
