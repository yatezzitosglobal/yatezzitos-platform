# Cuaderno de Integraciones MCP (My Custom Platform)

Este documento compila y describe las integraciones clave que forman parte de la 'My Custom Platform' (MCP) de Yatezzitos Global, así como aquellas orquestadas por nuestros agentes de IA. El objetivo es ofrecer una visión centralizada de cómo los diferentes sistemas interactúan para optimizar nuestras operaciones de turismo náutico de lujo.

## 1. Identidad y Contexto General del Proyecto

Yatezzitos Global es una plataforma de turismo náutico privado de lujo con 8 años de operación. El stack tecnológico incluye WordPress, Elementor, Houzez Theme, GoHighLevel (CRM), Yoast SEO (con un plugin REST API custom), Cloudflare Workers, Twilio y WhatsApp Business. Los agentes de IA operan dentro de este ecosistema, utilizando MCP como su marco de integración.

**Fuente:** `Instrucciones Maestras del Proyecto Yatezzitos Global`

## 2. Integraciones con GoHighLevel (CRM)

Las habilidades de los agentes de IA en el ámbito del CRM se centran en GoHighLevel (GHL), utilizando MCP para interactuar con sus endpoints y datos, siempre con un enfoque en la preparación para el equipo humano, no en la ejecución directa de acciones críticas.

### 2.1 El Rescatista de Ventas (Abandoned Pipeline Recovery)

**Propósito:** Recuperar oportunidades de venta que se han enfriado en el pipeline.

**Funcionamiento:**
- La IA se conecta periódicamente a los endpoints de GHL usando MCP.
- Lista contactos en la etapa 'Cotización Enviada' sin actividad en los últimos 7 días.
- Escanea el contexto de la última conversación (yate, presupuesto, fechas).
- **Redacta mensajes hiper-personalizados de rescate**.
- **Acción de la IA:** Deja el mensaje como 'Borrador de SMS/Email' o 'Nota Interna' en GHL y etiqueta a un cerrador para su envío manual.

**Seguridad:** Alta. La IA redacta, el humano envía.

**Fuente:** `CONTEXTO CRM` (Skills de CRM y Ventas — Enfoque en GoHighLevel)

### 2.2 El Paramédico de Leads (Lead Scoring & Triager)

**Propósito:** Clasificar y priorizar leads entrantes para el equipo de ventas.

**Funcionamiento:**
- Cada nuevo lead o interacción con el embudo de GHL activa este workflow asíncrono.
- Analiza metadatos (origen, formularios, páginas visitadas, palabras clave).
- Aplica una fórmula de *Lead Scoring* pre-aprobada.
- Si es un 'Hot Lead' (alta probabilidad de cierre inmediato), usa un Webhook (posiblemente orquestado por MCP) para moverlo a 'Alta Prioridad' en GHL y envía una alerta al equipo comercial.
- Si es un 'Cold Lead', lo asigna a una secuencia de 'Nurture' automatizado.

**Seguridad:** Muy Alta. La IA opera en *background* como un router inteligente, sin interacción directa con el cliente.

**Fuente:** `CONTEXTO CRM` (Skills de CRM y Ventas — Enfoque en GoHighLevel)

## 3. Integraciones Frontend y Figma

Estas integraciones buscan asegurar la fidelidad del diseño y la calidad del código frontend, utilizando MCP para conectar las decisiones de diseño en Figma con la implementación en WordPress/CSS.

### 3.1 Sincronizador del Sistema de Diseño (Design Token Bridge)

**Propósito:** Mantener la coherencia entre el sistema de diseño de Figma y el código CSS.

**Funcionamiento:**
- La IA usa el MCP de Figma para leer el archivo maestro del 'Design System'.
- Extrae 'Tokens de Diseño' (colores, fuentes, márgenes).
- Compara estos valores con el archivo `variables.css` (o el global del tema de WordPress).
- Si detecta una discrepancia, la IA crea un *Pull Request* automático modificando el archivo CSS para que coincida con Figma.

**Seguridad:** Máxima. La IA propone cambios, no sobrescribe directamente en producción.

**Fuente:** `CONTEXTO FRONTEND` (Skills Frontend y Figma — Enfoque en Calidad UI)

### 3.2 El Inspector de Píxeles (UI Fidelity Linter)

**Propósito:** Auditar la fidelidad del UI y la semántica del HTML en el código frontend.

**Funcionamiento:**
- Se activa cada vez que se sube código nuevo a la carpeta de rediseño (`redesign/`).
- La IA cruza la información visual de Figma (vía MCP) con la estructura del DOM (HTML).
- Escanea la ausencia de estilos inline (`style="..."`), el uso correcto de clases/variables CSS, la presencia de un solo `<h1>` por página, contrastes accesibles y atributos `alt` en imágenes.
- **Acción de la IA:** Genera un reporte como 'comentario en el código' o en el issue tracker, indicando las fallas de fidelidad al diseño.

**Seguridad:** Alta. La IA actúa como QA pasivo, sin permisos de escritura destructiva.

**Fuente:** `CONTEXTO FRONTEND` (Skills Frontend y Figma — Enfoque en Calidad UI)

## 4. Otras Integraciones Relevantes del Repositorio

Además de las habilidades específicas de los agentes de IA, el repositorio contiene otras integraciones y componentes que forman parte del ecosistema de Yatezzitos Global y pueden interactuar con MCP o ser gestionadas por los agentes.

-   **Plugin Yoast REST API Custom:** `plugins/yatezzitos-yoast-rest-api/`
    *   Un plugin personalizado para extender la funcionalidad de Yoast SEO, permitiendo a los agentes o sistemas externos interactuar con los metadatos SEO de WordPress.
    **Fuente:** `Instrucciones Maestras del Proyecto Yatezzitos Global` (Stack Tecnológico)

-   **Directorio de Integraciones:** `integrations/`
    *   Contiene configuraciones para webhooks y servicios externos como Cloudflare Workers, Twilio y WhatsApp Business. Estos son puntos de conexión clave para la automatización y comunicación, a menudo orquestados por MCP o GHL.
    **Fuente:** `Instrucciones Maestras del Proyecto Yatezzitos Global` (Stack Tecnológico, Estructura del Repositorio)

-   **Archivos de Memoria de Agentes IA:** `AGENTS.md` y `CLAUDE.md`
    *   Estos archivos son fundamentales para la operación de los agentes de IA, conteniendo reglas operativas y contexto general del proyecto. Son la 'memoria' que guía las interacciones de MCP.
    **Fuente:** `Instrucciones Maestras del Proyecto Yatezzitos Global` (Memoria del Proyecto)

## 5. Conclusión

La 'My Custom Platform' (MCP) y los agentes de IA son el cerebro detrás de la automatización y la eficiencia en Yatezzitos Global. Al integrar sistemas como GoHighLevel y Figma, y al aprovechar componentes personalizados y servicios externos, MCP asegura que la plataforma opere de manera cohesiva, manteniendo la calidad, la coherencia y la capacidad de respuesta en todos los frentes.