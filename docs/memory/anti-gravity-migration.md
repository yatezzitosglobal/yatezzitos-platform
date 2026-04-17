# Migración de Anti Gravity a Claude — Memoria consolidada del agente anterior

> Documento generado el 16 de abril 2026 durante la migración del proyecto desde Google Anti Gravity hacia Claude (Cowork mode).
> Consolida la memoria operativa del agente anterior almacenada en `~/.gemini/antigravity/brain/` (38 conversaciones activas de marzo–abril 2026).
> Sirve como **contexto de arranque** para Claude: qué se hizo, qué está en curso, qué convenciones rigen.

---

## 1. Qué era Anti Gravity en este proyecto

Anti Gravity (Google Gemini Agents) fue el agente principal de Yatezzitos Global entre marzo y abril 2026. Trabajó con 6 MCPs conectados directamente sobre `yatezzitos-platform` y dejó 53 conversaciones registradas. Esta migración lo reemplaza por Claude sin perder el trabajo hecho ni las convenciones adoptadas.

**Ubicaciones en disco del agente anterior (informativo, no tocar):**
- Proyecto real: `/Users/luisvelazquez/Projects/yatezzitos-platform/` ← este repo
- Memoria del agente: `/Users/luisvelazquez/.gemini/antigravity/`
  - `conversations/*.pb` — historial binario (Protocol Buffers) de 53 chats
  - `brain/{uuid}/` — artefactos por conversación (task.md, walkthrough.md, implementation_plan.md)
  - `mcp_config.json` — inventario de MCPs usados
  - `scratch/` — código fuente de los MCPs custom (GHL, WordPress, GSC)

---

## 2. Inventario de MCPs usados en Anti Gravity

| MCP | Transport | Para qué | Estado en Claude |
|---|---|---|---|
| `go-high-level` | Docker local (`ghl-mcp-server`) | CRM Yatezzitos: leads, pipelines, campos, tags, workflows, plantillas, social planner | Portar `docker run` al desktop de Claude |
| `gscServer` | Python local (`.venv` en `scratch/mcp-gsc/`) | Google Search Console para estrategia Striking Distance | Portar comando Python |
| `wordpress` | Node local (`server-wp-mcp` en `scratch/wp-mcp/`) | CRUD posts, yates, Yoast SEO en `yatezzitos.com` | Portar comando Node |
| `figma` | `npx @jayarrowz/mcp-figma` | Diseño — rediseño web (Home, Blog, Blog Details) | ✅ Figma oficial ya disponible en Claude |
| `notebooklm-mcp` | `uvx notebooklm-mcp-cli` | Ingesta, creación y organización de libretas NotebookLM | Portar comando uvx |
| `github-mcp-server` | Docker (`ghcr.io/github/github-mcp-server`) | Issues, PRs, commits | Claude trae GitHub vía `gh` CLI; portar MCP igual si se quiere paridad |

**Plan de portado:** todos los MCPs locales funcionan en el desktop de Claude configurando el mismo comando en `~/Library/Application Support/Claude/claude_desktop_config.json`. Los comandos y rutas ya existen en la Mac — se replica el JSON eliminando cualquier secret inline y usando variables de entorno.

> ⚠️ **Tokens expuestos en Anti Gravity** — rotar antes de portar:
> - `GITHUB_PERSONAL_ACCESS_TOKEN` estaba en texto plano en `mcp_config.json`
> - `YZZ_GHL_API_KEY_CONST` (`pit-...`) aparece en `task.md` de varias conversaciones
> - `.env.ghl-mcp` existe en el repo — verificar que esté en `.gitignore` (lo está)

---

## 3. Trabajo completado en Anti Gravity (por área)

### 3.1 SEO on-page (yatezzitos.com)

**Páginas ciudad — metadatos y H1 optimizados vía plugin Yoast REST API propio:**
- 6 ciudades principales con meta título / descripción / focus keyword al día
- Estrategia "Striking Distance" basada en Google Search Console (Catamaranes, Colomitos, Chachacual, etc.)
- Páginas: Cancún, Los Cabos, Puerto Vallarta, Ixtapa-Zihuatanejo, Mazatlán, La Paz

**Fichas de yates — 45+ yates optimizados** (ver `data/yachts/Destinos/{Ciudad}/{Categoria}/*.json` como fuente de verdad local de metadata):
- Los Cabos: 14 yates (Patron, King Fisher, Varuna, La Morrita, La Dolce Vita, Quantum, Canija, Emma, Seacret, Tonterías, Meco, Cabo Life, El Diablo, El Socio)
- Cancún: 8 yates (Sea Ray 27ft, Oasis, Dolce Far Niente, Island Gypsy, Dolce Vita Fly, Mr Happy, Escapada, Cuervo)
- Playa del Carmen: 10 yates (Triple Net, Blue Ray, Good Fellas, Cherry, Exile, Mint, Manhattan 60, Azimut 43, Roma, Sea Ray 27)
- Nuevo Vallarta: 6 yates (Suits, Nicole, Piquis, Mer Sea, Isabella II, Isabella)
- Acapulco: 6 yates (Noon, Princesa Jiannas, Dubai, Quimbumba, Pantera, Dalí)
- Huatulco: 1 lancha (Arroqueño)

**Convención clave aplicada:** primer encabezado de descripción larga siempre `<h1>` con la keyword focus; los demás `<h2>`/`<h3>`. Nunca empezar con `<h2>`.

**Contenido blog creado:**
- Los Cabos: 8 borradores de ~1,500+ palabras con Yoast meta completo
- Ixtapa-Zihuatanejo: 10 posts originales organizados con meta y enlaces internos
- Total exportado para NotebookLM: 136 archivos markdown (publicados + drafts + descripciones largas)

**Plugin SEO propio:**
- `plugins/yatezzitos-yoast-rest-api/` v1.2.0 — endpoint `POST /yatezzitos/v1/update-yoast`
- Soporta posts **y términos de taxonomía**
- Evita corromper el JSON de Elementor al inyectar metadata/HTML
- Regla: **nunca** usar `meta: {_yoast_wpseo_*}` en el endpoint estándar de WP REST — no se guarda

**Workflow permanente:** `.agents/workflows/seo-blog-posts.md` con las reglas obligatorias de redacción SEO (naturalidad > densidad, HTML puro, enlaces internos/externos, legibilidad Yoast estricta).

### 3.2 Rediseño web

**Completado:**
- Contact Us (rediseño completo)
- Help / FAQ (rediseño completo — estrella de mar decorativa, pills responsive, scroll en tablet 769–1024px)
- Fix de color de texto en blog posts individuales (scope bajo `#yatezzitos-blog-redesign`)

**Pendiente (Issue #2):**
- Home (pendiente — diseños en Figma, ver `01-home.md`)
- Blog (index, detalle, categorías)

Carpeta `redesign/` contiene CSS, tokens, assets del rediseño y exports de Figma.

### 3.3 CRM GoHighLevel — Agente Marina y operaciones

**Completado:**
- Fase 0–7: Pipeline de extracción (175 yates, 10 destinos) listos como fuentes RAG
- Fase 8–9: 10 RAGs segmentados < 25k chars cada uno
- Fase 10: Prompt engineering Marina (7 documentos)
- Fase 11: Blueprint arquitectura GHL Agent Studio
- Fase 12–13: Documentación + merge a `main` (PR #49)
- Master Routing Workflow para transiciones de reserva (Confirmada / Cancelada / Pospuesta / Pendiente)
- Plantillas de email HTML alineadas a tokens de marca premium
- Campaña "Rescate de Ventas" (`Rescatista-30-Dias`): filtro de contactos con fecha de viaje < 30 días no ganadas, plantilla HTML subida vía `create_email_template` en carpeta `Generados / Agente`

**En curso (sesiones marzo–abril 2026):**
- Flujo Marina v2 en GHL Agent Studio (8 nodos: AI Agent + API Call alternados)
  - Nodos 1–2 completos (captura ciudad + etiqueta automática)
  - Nodos 3–4 en progreso (captura pasajeros + guardar en campo)
  - Nodos 5–8 pendientes (tipo embarcación, recomendación KB, cotización automática)
- Custom fields pendientes de crear en GHL: `num_pasajeros`, `tipo_embarcacion`, `yate_recomendado`
- Configurar Router con rutas de intención (ventas / FAQ / escalar)
- Activar "Buscar en la web" como herramienta del AI Agent

**Infra-as-Code para GHL** (carpeta `ghl-data/`):
- `scripts/ghl_pull` — descarga estado actual (emails, pipelines, tags, custom fields, custom values, workflows, snippets SMS/WA, social posts)
- `scripts/ghl_push` — inyecta cambios locales (emails, tags, custom-values, custom-fields)
- **Bloqueos conocidos:** API Node.js de GHL no tiene POST/PUT para estructura de pipelines; Push de SMS templates bloqueado por visibilidad de IDs en API

### 3.4 Plugin yzz-quotes-api-plus-shortcode (cotización / reserva)

**v2.4.0 desplegada** — conexión GHL tiempo real para las páginas Mi Cotización / Mi Reserva / Gracias:
- Endpoint proxy `POST /wp-json/yzz/v1/contact-live` que consulta `/contacts/{id}` de GHL y normaliza 19 custom fields
- Plugin pasó de 1 archivo PHP de 2,909 líneas (123 KB) → 1 PHP de 380 líneas + 4 assets separados (HTML/JS/CSS)
- Fallback a BD si GHL no responde (`_meta.source: db_ghl_error` / `db_no_api_key`)
- Animación confetti (canvas-confetti) en página Gracias
- Mapeo de 19 custom fields GHL documentado en `brain/c5a65c13.../task.md` (sección "Mapeo GHL confirmado")

**Pendiente:** JS de polling cada 30s en Mi Cotización / Mi Reserva para refresco automático sin recargar la página.

### 3.5 Integraciones MCP y NotebookLM

**GHL MCP integrado** (basado en `mastanley13/GoHighLevel-MCP`):
- Dockerfile personalizado con dependencias `typescript`
- Imagen `ghl-mcp-server` construida
- `mcp_config.json` apunta a `docker run -i --rm --env-file .env.ghl-mcp`
- Guardrails de seguridad documentados en `docs/integraciones/mcp-notebooklm-knowledge.md`
- Parche para inyectar `parentId` en integraciones de Plantillas (para crear en carpeta `Generados / Agente`)
- 253 herramientas evaluadas en `ghl_mcp_integration_guide.md`

**NotebookLM — 8 libretas temáticas con 84 fuentes cargadas:**
1. Yatezzitos — Visión General
2. Arquitectura y Sistema
3. Flujos de Negocio
4. CRM y Automatizaciones
5. Website y Stack Técnico
6. SEO y Contenido (136 archivos md consolidados)
7. Integraciones MCP
8. Riesgos / Backlog / Open Questions

Manifest maestro en `knowledge-export/SOURCE_MANIFEST.json`, mapa del repo en `knowledge-export/REPO_MAP.md`.

**Pendiente NotebookLM:**
- Subir documentación canónica a las libretas (READMEs por dominio)
- Generar `NOTEBOOKLM_REGISTRY.json` y `SYNC_LOG.md`
- Definir modo de actualización continua

### 3.6 Integración WhatsApp → GitHub Issues

**Flujo:** WhatsApp → GoHighLevel → Webhook (Cloudflare Worker) → GitHub Issue con etiqueta `from-whatsapp`.

**En progreso (Fase 9):**
- Webhook serverless `index.js` (en curso)
- Template de GitHub Issue para WhatsApp (pendiente)
- Guía de configuración del workflow GHL (pendiente)
- Commit + push + verificación con curl (pendiente)

---

## 4. Convenciones operativas heredadas

### 4.1 SEO — reglas inquebrantables
- Naturalidad sobre optimización: prohibido hacer "ensaladas de palabras"
- HTML puro (nunca Markdown) para contenido que va a WordPress
- Primer heading de toda descripción larga = `<h1>` con keyword focus
- Mínimo un enlace externo de utilidad a sitio de alta autoridad (Wikipedia, TripAdvisor)
- Enlaces internos obligatorios entre ciudades y experiencias
- Legibilidad Yoast estricta: transición > 30% de oraciones, subtítulos < 300 palabras, palabras complejas < 10%
- Leer JSONs en `data/yachts/Destinos/{Ciudad}/{Tipo}/*.json` **antes** de proponer keywords para evitar canibalización

### 4.2 GHL — etiquetado IA
**Toda plantilla o correo generado por IA en GHL debe llevar `(IA)` al final del nombre/título.** Ejemplo: `"Recordatorio de Pago (IA)"`. Esto garantiza trazabilidad visual para el equipo humano.

### 4.3 WordPress — endpoint Yoast
Siempre usar `POST /yatezzitos/v1/update-yoast` con params `{id, type, title, desc, focuskw}`. **Nunca** `meta: {_yoast_wpseo_*}` en el endpoint estándar. Para términos de taxonomía: `type: "term"`.

### 4.4 Git / ramas
- `fix/` `feat/` `docs/` `seo/` `ai/` como prefijos de rama
- Commits en inglés técnico con prefijo convencional
- PR obligatorio — nunca push directo a `main`
- Actualizar `CLAUDE.md` **y** `AGENTS.md` juntos cuando cambian reglas globales

### 4.5 Comunicación
- Código y commits: inglés técnico
- Contenido de usuario: español mexicano profesional, cálido, marca de lujo náutico

### 4.6 Guardrails no-negociables
- No inventar precios, disponibilidad, capacidad, fechas, horarios
- No exponer PII
- No publicar secretos
- No modificar producción WP sin staging
- No borrar archivos en masa sin confirmación
- No enviar mensajes a clientes ni propietarios
- No ejecutar cobros, reembolsos ni cancelaciones

---

## 5. Trabajo abierto al momento de la migración

| Área | Tarea | Estado |
|---|---|---|
| Rediseño web | Home (Figma → WP) | Pendiente |
| Rediseño web | Blog + Blog Details + Categorías | Pendiente |
| GHL Marina v2 | Nodos 3–8 del flujo Agent Studio | En progreso |
| GHL Marina v2 | Crear custom fields `num_pasajeros`, `tipo_embarcacion`, `yate_recomendado` | Pendiente (acción manual) |
| yzz-quotes plugin | JS de polling cada 30s en Mi Cotización/Mi Reserva | Pendiente |
| SEO | Auditoría Cancún (siguiente lote) | Pendiente |
| SEO | Optimizar Home | Pendiente |
| NotebookLM | Subir documentación canónica a las 8 libretas | Pendiente |
| WhatsApp → Issues | Webhook serverless + template + guía | En progreso |
| IaC GHL | Push de social posts (bloqueo por visibilidad de IDs) | Bloqueado |
| IaC GHL | Push de SMS templates | Bloqueado por API GHL |

**Backlog GitHub al cierre:** ~17 issues abiertos. SEO concentra la mayoría (#38, #40, #41, #42, #43, #44, #45, #46, #47, #48). Rediseño en #2. Expansión futura en #20–#25.

---

## 6. Índice de conversaciones de Anti Gravity (referencia)

Conversaciones con contenido en `brain/` — ordenadas por cantidad de archivos (proxy de importancia):

| UUID | Archivos | Tema principal |
|---|---|---|
| `a3a68a18` | 124 | WP↔Repo Sync + SEO Los Cabos (14 yates) |
| `bafd2e8a` | 109 | SEO 31 yates (Cancún, PDC, Huatulco, NV, Acapulco) |
| `189db853` | 95 | SEO ciudades + Yoast plugin + blogs Los Cabos/Ixtapa |
| `71b537f1` | 71 | Plugin yzz-quotes (Mi Cotización/Mi Reserva) |
| `92e3ce35` | 65 | Flujo Marina v2 GHL Agent Studio |
| `09fef01f` | 64 | GHL MCP + IaC + Campaña Rescate + NotebookLM |
| `04895fb0` | 57 | Documentación empresarial para NotebookLM (8 notebooks) |
| `5b3aa636` | 53 | Rediseño Help/FAQ + auditoría docs |
| `1d85d8d2` | 52 | (sin task.md — revisar artefactos) |
| `908c1d0c` | 49 | Backlog general + WP MCP + Marina |
| `bfb9a8a3` | 36 | (sin walkthrough — revisar) |
| `cd0c843e` | 23 | (task.md solo — revisar) |
| `c20a2587` | 29 | (task.md solo — revisar) |
| `2ef96cf9` | 29 | Fix blog post text color |
| `a53780be` | 25 | GHL MCP integration + security plan |
| `c5a65c13` | 25 | Separación de tecnologías plugin + GHL tiempo real (última sesión activa: 16 abr 2026) |
| `15777ea9` | 24 | (revisar) |
| `990e445b` | 15 | (revisar) |
| `85631cff` | 15 | (revisar) |
| `6935b698` | 18 | (revisar) |

Los archivos originales están en `/Users/luisvelazquez/.gemini/antigravity/brain/{uuid}/` por si Claude necesita bucear en el detalle.

---

## 7. Cómo usar este documento (para Claude)

1. **Al arrancar una sesión:** leer este archivo + `CLAUDE.md` + `AGENTS.md` + `.agents/workflows/*.md`.
2. **Antes de proponer cambios en SEO:** consultar sección 3.1 y los JSON de `data/yachts/...`.
3. **Antes de tocar GHL:** consultar sección 3.3, respetar etiquetado `(IA)`, respetar guardrails 4.6.
4. **Antes de tocar WordPress:** consultar sección 3.4 (plugin yzz-quotes) o 3.1 (Yoast endpoint).
5. **Si la tarea toca NotebookLM:** consultar sección 3.5; el MCP se porta desde `mcp_config.json` original.
6. **Si no encuentras algo:** bucea en los artefactos originales del cerebro de Anti Gravity (ubicaciones en sección 1).

---

**Última actualización:** 16 de abril 2026
**Generado por:** Claude (Cowork) durante migración desde Anti Gravity
