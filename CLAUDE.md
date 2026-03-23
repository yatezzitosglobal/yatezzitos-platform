# CLAUDE.md - Contexto General del Proyecto Yatezzitos Global

Este documento sirve como la fuente principal de contexto y visión general para todos los agentes de IA y colaboradores del proyecto Yatezzitos Global.

## Visión General del Proyecto
Yatezzitos Global es una plataforma de turismo náutico privado de lujo con 8 años de operación en más de 10 destinos de México. Nuestro objetivo es ofrecer experiencias náuticas premium, conectando a clientes con una flota diversa de yates y embarcaciones.

## Stack Tecnológico Principal
- **Sitio web:** WordPress + Elementor + Houzez Theme (yatezzitos.com)
- **CRM:** GoHighLevel (gestión de leads, pipelines, cotizaciones, automatizaciones)
- **SEO:** Yoast SEO (con plugin REST API custom)
- **Integraciones:** Cloudflare Workers, Twilio, WhatsApp Business
- **Repositorio:** `yatezzitos-platform` (este)

## Destinos Operativos
Cancún, Los Cabos, Puerto Vallarta, La Paz, Mazatlán, Acapulco, Huatulco, Ixtapa-Zihuatanejo, Nuevo Vallarta / Riviera Nayarit, Playa del Carmen.

## Principios Operativos Clave
- **Lectura primero, escritura después:** Entender la tarea y el contexto antes de actuar.
- **Seguridad y privacidad:** No inventar datos, no exponer PII, no publicar secretos.
- **Colaboración:** Trabajar en ramas aisladas y usar Pull Requests para revisión.
- **Naturalidad del contenido:** Prioridad máxima a la calidad y naturalidad del lenguaje sobre la optimización técnica.

## Últimos Trabajos y Desarrollos Recientes (Actualización: 22 de marzo 2026)

Hemos estado enfocados en fortalecer la infraestructura y las capacidades operativas de Yatezzitos Global, con un énfasis particular en la integración de agentes de IA para optimizar nuestros flujos de trabajo.

### 1. Establecimiento de Instrucciones Maestras para Agentes de IA
Se han definido y documentado las "Instrucciones Maestras del Proyecto Yatezzitos Global" (`README.md`), que guían el comportamiento, las convenciones y los guardrails de seguridad para todos los agentes de codificación autónomos. Esto incluye:
- Definición de identidad y contexto del proyecto.
- Principios operativos globales y guardrails de seguridad no negociables.
- Convenciones de trabajo (ramas, commits, PRs).
- Enrutamiento de tareas y archivos de contexto específicos.
- Reglas detalladas para SEO, fichas de yates, auditoría SEO, frontend, mantenimiento y CRM.
- Estructura del repositorio y tono de comunicación.

### 2. Integración de Agentes de IA a través de WhatsApp
Una mejora clave ha sido la implementación de un flujo de trabajo que permite la creación automática de issues en GitHub directamente desde mensajes de WhatsApp. Este proceso utiliza:
- **WhatsApp → GoHighLevel (GHL) → Webhook → GitHub Issue.**
Esta integración facilita la asignación de tareas y la comunicación con los agentes de IA de manera más fluida y directa, permitiendo que las solicitudes operativas se conviertan en tareas de desarrollo o contenido de forma eficiente.

### 3. Avances en Prioridades del Proyecto (Fase 1)
Continuamos progresando en las prioridades de la Fase 1, que incluyen:
- Rediseño web (Home, Blog, Blog Details) de Figma a WordPress.
- Optimización SEO de ciudades activas y asignación de keywords a la flota.
- Organización y optimización del CRM (GoHighLevel).
- Automatización de procesos clave como cotizaciones, recibos de depósito y seguimiento.

### Antes de modificar cualquier cosa
1. Leer el archivo antes de editarlo
2. No romper lo que ya funciona (ventas, formularios, SEO, automatizaciones)
3. Probar en staging antes de aplicar en producción

---

## Estructura del repo

```
docs/          → documentación del proyecto (ver docs/README.md para índice completo)
  architecture/  → stack, decisiones técnicas, módulos de producto
  brand/         → identidad de marca
  business/      → visión y modelo de negocio
  crm/           → GoHighLevel: pipelines, automatizaciones
  seo/           → estrategia SEO, auditorías, frameworks de keywords
  scrum/         → backlog maestro y planes de ejecución por issue
  tools/         → guías de setup de herramientas externas
ai/
  assistants/    → specs de los 6 agentes IA del ecosistema (ver README.md)
  prompts/       → prompts reutilizables
  workflows/     → reglas y workflows para agentes (ej. seo-blog-posts.md)
.agents/
  workflows/     → workflows para Claude Code (cargados automáticamente)
redesign/        → CSS, diseños Figma, tokens y assets del rediseño web
wordpress/       → temas, plugins, mu-plugins, snippets del sitio
plugins/         → plugins standalone (ej. yatezzitos-yoast-rest-api)
data/            → yates, destinos, FAQs, templates
scripts/         → scripts de automatización
```

---

## Prioridades actuales del proyecto

Ver [docs/scrum/backlog.md](docs/scrum/backlog.md) para el detalle completo.

**Fase 1 (en curso):**
1. Terminar rediseño web (Home, Blog, Blog Details — Figma → WordPress)
2. SEO: optimizar ciudades activas y asignar keywords a todos los yates
3. Ordenar CRM (GoHighLevel)
4. Automatizar cotización, recibo de depósito y seguimiento

**Fase 2 (siguiente):**
5. Etapa Feedback en pipeline de turistas
6. Captación y onboarding de propietarios
7. Mapa de integraciones actual
8. Calendario de disponibilidad en tiempo real

---

## Fuentes de verdad

| Dato | Fuente |
|---|---|
| Leads, pipeline, reservas | GoHighLevel |
| Fichas de yates, URLs, SEO | WordPress |
| Disponibilidad | Propietario / calendario (futuro) |
| Pagos confirmados | Pasarela / banco |
| Documentación y decisiones | Este repositorio (GitHub) |

**Nunca inventar:** precios, disponibilidad, capacidad de embarcaciones, fechas.

---

## Reglas SEO

## Reglas SEO

Las reglas de contenido SEO están en [`.agents/workflows/seo-blog-posts.md`](.agents/workflows/seo-blog-posts.md).
Se aplican siempre que se cree o edite contenido del blog o fichas de yates.

Reglas principales de redacción y formato:
1. **Naturalidad sobre optimización:** Nunca usar "ensaladas de palabras" o traducciones robóticas. La fluidez humana tiene prioridad absoluta sobre la densidad de la palabra clave.
2. **Formato:** Todo el contenido para WordPress debe generarse siempre en **HTML puro** listo para copiar/pegar o enviar vía API. No usar Markdown.
3. **Enlaces Internos:** Obligatorio enlazar a nuestros posts de blog de destinos/experiencias dentro de las descripciones largas de ciudades.
4. **Enlaces Externos:** Todo archivo SEO debe contener, mínimo, **un enlace saliente de utilidad** hacia un sitio con alta autoridad (e.g. Wikipedia, TripAdvisor).
5. **Legibilidad Estricta (Yoast):** Palabras de transición en >30% de las oraciones, subtítulos cada <300 palabras, y complejidad de palabras en <10%. Priorizar frases cortas y claras.

---

## Integraciones e Inteligencia (MCP)

Tenemos acceso a servidores MCP para operar directamente con las plataformas:

- **WordPress MCP:** Lo utilizamos para leer, crear y actualizar entradas del blog o páginas en vivo. Es vital para actualizar los metadatos de **Yoast SEO** (usando los campos `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, y `_yoast_wpseo_focuskw`) de manera rápida y masiva sin salir de la terminal.
- **Google Search Console (GSC):** Lo usamos para extraer datos reales de rendimiento, impresiones y clics para descubrir oportunidades de palabras clave y optimizar URLs con base en datos verificados.
- **NotebookLM MCP (`jacob-bd/notebooklm-mcp-cli`):** Herramienta autorizada para ingestar, organizar, consultar y gestionar programáticamente la base de conocimiento del proyecto dentro de Google NotebookLM. Se usa para mantener actualizados los documentos canónicos en libretas de estudio.
- **GoHighLevel (GHL) MCP:** Herramienta para conectar agentes IA al CRM de Yatezzitos mediante comandos de WhatsApp / GitHub Issues. Opera bajo Whitelist estricta, previniendo alucinaciones y con protección contra "Prompt Injection".
  - **REGLA DE ETIQUETADO:** Todo correo o plantilla generada de manera autónoma o asistida por un agente de IA en GoHighLevel, **debe llevar obligatoriamente la etiqueta `(IA)` al final de su nombre/título** (Ej. "Recordatorio de Pago (IA)") para garantizar la trazabilidad visual por el equipo humano.

---

## Agentes IA del ecosistema

Ver [ai/assistants/README.md](ai/assistants/README.md) para el catálogo completo.

| Agente | Para quién |
|---|---|
| Marina | Turistas / clientes |
| Timón | Propietarios |
| Capitán | Brokers / agencias B2B |
| Ola | Afiliados |
| Soporte Interno | Equipo Yatezzitos |

---

## Lo que no se debe hacer

- Push directo a `main`
- Cambios en producción de WordPress sin staging previo
- Modificar automatizaciones en GHL sin aprobación
- Crear archivos de documentación innecesarios
- Abrir nuevos destinos SEO antes de completar los actuales
- Subir credenciales, tokens o datos sensibles al repo

Estos desarrollos buscan mejorar la eficiencia operativa, la calidad del contenido y la experiencia del usuario, preparando el terreno para futuras fases de expansión y optimización.
