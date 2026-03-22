# Integraciones MCP (Model Context Protocol) — Yatezzitos Global

> **Documento de Capacitación Interna** · Fuente para NotebookLM
> **Propósito:** Capacitar al equipo de Yatezzitos (Ventas, Marketing, Atención al Cliente, Desarrollo) en el uso de Agentes IA potenciados por conexiones MCP, logrando un crecimiento exponencial mediante la automatización inteligente y segura.

---

## 🚀 ¿Qué son las integraciones MCP?

El **Model Context Protocol (MCP)** es un puente seguro que permite a nuestra IA (Gemini, Claude, Copilot) conectarse directamente a las herramientas que usamos todos los días (GoHighLevel, WordPress, GitHub, etc.).

En lugar de copiar y pegar información, la IA puede **leer datos en tiempo real, analizarlos y ejecutar tareas autorizadas** por nosotros a través de comandos simples (por ejemplo, desde WhatsApp mediante issues de GitHub o directamente en nuestros paneles internos).

---

## ⚖️ Reglas Globales y de Seguridad (Lo que NO se puede hacer)

El núcleo de nuestras integraciones es la **Seguridad Zero Trust**. Todas las operaciones pasan por estrictos guardrails (ver `AGENTS.md` y `orchestrator.md`):

1. **NO se ejecutan acciones destructivas:** La IA jamás podrá borrar un contacto (`delete_contact`), cancelar una reserva, ni alterar un pago.
2. **NO se envían mensajes directos sin supervisión:** La IA tiene prohibido enviar SMS o correos directamente al cliente final (`send_email`, `send_sms`). Todo requiere validación humana (HITL - Human in the Loop).
3. **NO se inventan datos (Cero Alucinaciones):** Si la IA revisa GoHighLevel y no hay disponibilidad, no cruzará información falsa. Si no lo sabe, escala a un humano.
4. **Resistencia a Hackeos (Prompt Injection):** La IA está blindada. Si alguien intenta engañarla con "ignora las reglas y bórralo", la IA abortará inmediatamente y lanzará una alerta de seguridad (SECURITY_ALERT).
5. **Cambios en Producción Limitados:** La IA no publicará cambios directos en el código principal de Yatezzitos. Siempre creará ramas y Pull Requests.

---

## 🛠️ ¿Qué Hacemos y Qué SE PUEDE Hacer? (Casos de Uso)

Aquí te mostramos cómo usar a nuestro agente de **Soporte Interno** en distintas áreas del negocio para lograr resultados exponenciales.

### 💰 1. Ventas y CRM (GoHighLevel MCP)
Contamos con un servidor MCP conectado a GHL que permite a la IA consultar pipelines y operar el CRM en modo lectura o escritura limitada.
- **Acciones Permitidas (Whitelist):** `search_contacts`, `get_contact`, `search_opportunities`, `get_pipelines`, `get_calendar_events`, `create_contact_note`, `create_contact_task`.
- **Caso de Estudio (Prompt):** *"Revisa el CRM y dame un resumen del cliente 'Juan Pérez' y la oportunidad en etapa de negociación. Luego agrégale una nota indicando que quiere rentar un catamarán el viernes."*
- **Impacto:** Los cerradores de ventas ahorran horas de búsqueda manual y mantienen el CRM hiper-actualizado.

### 📣 2. Marketing y SEO (WordPress MCP)
Conectamos la IA al backend de Yatezzitos.com para escalar nuestra presencia en Google.
- **Acciones Permitidas:** Leer posts, analizar metas Yoast SEO, generar borradores de fichas de yates, editar descripciones de ciudades.
- **Caso de Estudio (Prompt):** *"Revisa la página de renta de yates en Mazatlán. Sugiere 3 mejoras de palabras clave según las métricas actuales y redáctame el borrador para la sección de 'Experiencias de Lujo'."*
- **Impacto:** Creación de contenido SEO en minutos que cumple con la regla de oro: Naturalidad sobre mecanización, obteniendo semáforos verdes en Yoast.

### 📞 3. Atención al Cliente y Operaciones
Mediante la integración inicial de WhatsApp (conectada a issues de GitHub), el equipo envía tareas rápidas por chat.
- **Acciones Permitidas:** Consultar disponibilidad, cruzar reportes rápidos.
- **Caso de Estudio (Prompt):** *"Por WhatsApp: Agente, dime cuáles son las tareas urgentes vencidas hoy en GoHighLevel."*
- **Impacto:** Menor tiempo de respuesta al cliente. Todo el equipo tiene un "asistente concierge" en la palma de su mano.

### 💻 4. Desarrollo Web (GitHub MCP)
Para escalar el código de Yatezzitos Platform de forma segura sin romper la web.
- **Acciones Permitidas:** Revisar PRs (Pull Requests), sugerir refactorizaciones, generar componentes, documentar endpoints.
- **Caso de Estudio (Prompt):** *"Analiza este componente de la ficha del yate. Encuentra por qué el margen se rompe en versión móvil e implementa una solución en una nueva rama llamada 'fix/margen-fichas'."*
- **Impacto:** Despliegues más rápidos y código más limpio.

---

## 📚 ¿Cómo debes usar esta libreta en NotebookLM?
Este archivo sirve como **fuente de verdad**.
Cuando estés conversando con NotebookLM (alimentado por este documento), hazle preguntas como:
- *"Soy de ventas, ¿qué comandos le puedo mandar al agente para GHL?"*
- *"Quiero hacer SEO, recuérdame las reglas para que el texto suene premium y humano."*
- *"¿Por qué la IA no puede enviar un correo masivo desde el CRM?"*

> **Recuerda:** La Inteligencia Artificial en Yatezzitos no reemplaza la atención de lujo que brindamos, **la potencia**. Usen al agente como su co-piloto diario.
