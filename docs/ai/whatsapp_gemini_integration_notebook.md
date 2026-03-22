# Cuaderno de Integración WhatsApp-Gemini para Agentes IA

> **Estado:** 🟢 FINALIZADO (Ready for Review)
> **Fecha de Creación:** 22 de marzo de 2026
> **Autor:** Agente Copilot de Yatezzitos Global

---

## 1. Introducción: La Revolución de Tareas desde WhatsApp

Este cuaderno tiene como objetivo explicar la reciente integración de nuestro canal de WhatsApp con los agentes de IA de Yatezzitos Global, potenciados por Gemini. Esta conexión nos permite automatizar la creación de tareas de desarrollo, SEO y documentación directamente desde mensajes de WhatsApp, agilizando nuestros flujos de trabajo y liberando tiempo valioso para el equipo humano.

Aquí detallaremos cómo funciona esta integración, qué significa para el equipo y cómo podemos aprovecharla al máximo.

---

## 2. ¿Cómo Funciona la Integración WhatsApp-Gemini?

La magia de esta integración reside en un flujo de trabajo automatizado que transforma un simple mensaje de WhatsApp en una tarea actionable para nuestros agentes de IA en GitHub.

El proceso es el siguiente:

1.  **Mensaje en WhatsApp:** Un miembro del equipo (o incluso un cliente, si el flujo lo permite) envía un mensaje a nuestro número de WhatsApp Business.
2.  **GoHighLevel (GHL) como Puente:** Nuestro CRM, GoHighLevel, intercepta este mensaje. GHL está configurado para identificar ciertos patrones o simplemente para reenviar todos los mensajes entrantes relevantes.
3.  **Webhook a GitHub:** GHL dispara un webhook (una notificación automática) a nuestro repositorio de GitHub. Este webhook contiene el contenido del mensaje de WhatsApp y metadatos relevantes.
4.  **Creación de Issue en GitHub:** El webhook está configurado para crear automáticamente un nuevo "Issue" en este repositorio (`yatezzitos-platform`). Este issue incluye el texto del mensaje de WhatsApp como título y/o descripción, y se etiqueta con `from-whatsapp` y `ai-task`.
5.  **Activación del Agente IA:** GitHub Copilot (nuestro agente de codificación autónomo) monitorea los nuevos issues. Al detectar un issue con la etiqueta `ai-task`, lo asigna a sí mismo.
6.  **Procesamiento de la Tarea:** El agente de IA lee el issue, consulta las instrucciones maestras del proyecto y los archivos de contexto relevantes, y procede a ejecutar la tarea (ej. redactar contenido SEO, corregir un bug, actualizar documentación).
7.  **Pull Request y Revisión:** Una vez completada la tarea, el agente crea una rama aislada y abre un Pull Request (PR) con los cambios, listo para la revisión y aprobación humana.

Este flujo elimina la necesidad de que un humano cree manualmente issues en GitHub, permitiendo que las ideas y necesidades se transformen en tareas de desarrollo de forma casi instantánea.

---

## 3. El Agente IA en Acción: Trabajar sin Intervención Humana Directa

La principal ventaja de esta integración es que nuestros agentes de IA pueden comenzar a trabajar en tareas sin necesidad de que un humano use su computadora para asignarlas.

**¿Qué significa esto para el equipo?**

*   **Creación de Tareas Simplificada:** Cualquier miembro del equipo puede solicitar una tarea (ej. "Necesito un blog post sobre 'Renta de Yates en Cancún'") con un simple mensaje de WhatsApp.
*   **Automatización de Flujos:** Tareas repetitivas o que requieren acceso a información contextual pueden ser delegadas a la IA de forma eficiente.
*   **Centralización de la Gestión:** Todas las tareas, sin importar su origen, se gestionan a través de GitHub Issues, proporcionando un registro claro y unificado.
*   **Mayor Velocidad de Ejecución:** Desde la idea hasta el borrador de código o contenido, el tiempo se reduce drásticamente.

**Ejemplos de tareas que pueden ser disparadas desde WhatsApp:**

*   **SEO:** "Crea un borrador de blog post para 'Mejores playas para visitar en Los Cabos'."
*   **Documentación:** "Actualiza la sección de 'Destinos Operativos' en las instrucciones maestras."
*   **Mantenimiento:** "Hay un enlace roto en la página de 'Yates Premium', por favor revísalo."
*   **Contenido de Fichas:** "Genera una descripción premium para el yate 'SeaRay 500' con capacidad para 12 personas."

---

## 4. Fuentes y Referencias Clave de la Integración

Para entender a fondo cómo operan nuestros agentes y este flujo, consulta los siguientes documentos:

*   **Instrucciones Maestras del Proyecto (este archivo):** Proporciona la visión general, stack tecnológico, guardrails de seguridad y convenciones de trabajo. Especialmente la sección "1. Identidad y Contexto del Proyecto" y "5. Enrutamiento de Tareas".
*   **`AGENTS.md`:** Contiene las reglas operativas globales para todos los agentes de IA, incluyendo cómo interactúan con los issues y el repositorio.
*   **`CLAUDE.md`:** Ofrece un contexto general y de alto nivel del proyecto Yatezzitos Global.
*   **`docs/ai/github_maintenance_skills.md`:** Detalla cómo los agentes manejan bugs y tareas de mantenimiento a través de GitHub Issues.
*   **`docs/ai/ghl_crm_skills.md`:** Aunque se centrará más en GHL, ya contiene información sobre cómo GHL interactúa con los leads y pipelines, lo cual es el primer paso de la integración de WhatsApp.

---

## 5. Próximos Pasos: Integración del MCP de GoHighLevel

Como se mencionó en la solicitud original, la siguiente fase de esta evolución será la integración del **MCP (Multi-Channel Platform) de GoHighLevel** directamente con nuestros agentes de IA.

Una vez que esta conexión esté establecida, este cuaderno se actualizará para incluir:

*   **Fuentes de cómo funciona el MCP de GHL:** Explicación detallada de sus capacidades y endpoints.
*   **Nuevas funcionalidades habilitadas:** Cómo los agentes podrán interactuar directamente con pipelines, contactos, automatizaciones y campañas dentro de GHL para tareas como:
    *   Rescate de leads abandonados.
    *   Calificación y triaje de leads.
    *   Generación de borradores de seguimiento personalizados.
    *   Actualización de estados de cotización.

Esta evolución nos permitirá cerrar el ciclo de automatización, desde la captación inicial vía WhatsApp hasta el seguimiento y la gestión avanzada de leads dentro de nuestro CRM.
