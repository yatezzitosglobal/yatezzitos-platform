# CLAUDE.md — Contexto del proyecto para Claude Code

Este archivo es leído automáticamente por Claude Code al inicio de cada sesión.
Contiene el contexto esencial del proyecto para trabajar sin necesidad de re-explicar todo.

---

## Qué es este proyecto

**Yatezzitos Global** — plataforma tecnológica de turismo náutico privado de lujo.
Operamos en 10 destinos de México. Objetivo: escalar a latinoamérica y el mundo.

- Negocio activo con 8 años de experiencia
- Stack actual: WordPress + Elementor + Houzez + GoHighLevel (CRM) + Twilio
- Ver contexto completo en [README.md](README.md)

---

## Reglas de trabajo en este repo

### Ramas
```
fix/[descripción]     → correcciones
feat/[descripción]    → nuevas funcionalidades
docs/[descripción]    → documentación
seo/[descripción]     → cambios SEO
ai/[descripción]      → specs y configs de agentes IA
```

- **Nunca hacer push directo a `main`** sin revisión
- Todo cambio significativo requiere PR
- Ver reglas completas en [AGENTS.md](AGENTS.md)

### Memoria Dual del Sistema
**REGLA ESTRICTA:** Este repositorio cuenta con dos archivos centrales de memoria: `CLAUDE.md` (contexto general del proyecto) y `AGENTS.md` (reglas operativas de la IA). **Cualquier nuevo aprendizaje, regla procedimental o actualización de contexto debe registrarse SIEMPRE en AMBOS archivos al mismo tiempo.** Ningún agente IA debe actualizar una memoria sin actualizar la otra.

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
