# Copilot Coding Agent — Instrucciones Maestras del Proyecto Yatezzitos Global

> Última actualización: 22 de marzo 2026
> Este archivo es leído automáticamente por GitHub Copilot al asignarse a un issue.

---

## 1. Identidad y Contexto del Proyecto

Eres un agente de codificación autónomo trabajando en **Yatezzitos Global** — una plataforma de turismo náutico privado de lujo con 8 años de operación, activa en 10+ destinos de México. Tu trabajo es completar tareas de desarrollo, SEO, documentación y mantenimiento dentro de este repositorio.

### Stack Tecnológico
- **Sitio web:** WordPress + Elementor + Houzez Theme (yatezzitos.com)
- **CRM:** GoHighLevel (leads, pipelines, cotizaciones, automatizaciones)
- **SEO:** Yoast SEO (plugin REST API custom: `plugins/yatezzitos-yoast-rest-api/`)
- **Integraciones:** Cloudflare Workers, Twilio, WhatsApp Business
- **Repositorio:** Este (`yatezzitos-platform`)
- **Idioma principal:** Español (México)

### Destinos Operativos
Cancún, Los Cabos, Puerto Vallarta, La Paz, Mazatlán, Acapulco, Huatulco, Ixtapa-Zihuatanejo, Nuevo Vallarta / Riviera Nayarit, Playa del Carmen.

---

## 2. Principio Operativo Global

**Lectura primero, escritura después.**

1. Lee el issue completo y entiende la tarea antes de escribir una sola línea de código.
2. Consulta los archivos de contexto relevantes listados en la Sección 5 según el tipo de tarea.
3. Trabaja siempre en una rama aislada — nunca hagas push directo a `main`.
4. Abre un Pull Request con descripción clara al terminar.

---

## 3. Guardrails de Seguridad (No Negociables)

### ❌ Prohibido — No debes:
- Inventar datos: precios, disponibilidad, capacidad de embarcaciones, fechas, horarios.
- Exponer PII (teléfonos, correos, datos de pago) en commits, logs o código.
- Publicar secretos (llaves API, tokens, credenciales) — ni hardcodeados ni en comentarios.
- Hacer push directo a `main` — todo pasa por Pull Request.
- Modificar archivos de producción de WordPress sin indicación explícita.
- Borrar archivos masivamente sin confirmación.
- Enviar mensajes a clientes o propietarios.
- Ejecutar cobros, reembolsos o cancelaciones.

### ✅ Permitido:
- Crear ramas y abrir Pull Requests.
- Generar borradores de contenido, documentación, código.
- Leer cualquier archivo del repositorio para contexto.
- Crear archivos nuevos dentro de la estructura existente.

---

## 4. Convenciones de Trabajo

### Ramas
```
fix/[descripción]     → correcciones de bugs
feat/[descripción]    → nuevas funcionalidades
docs/[descripción]    → documentación
seo/[descripción]     → cambios de contenido SEO
ai/[descripción]      → specs y configs de agentes IA
```

### Commits
- Mensajes en inglés con prefijo convencional: `fix:`, `feat:`, `docs:`, `seo:`, `chore:`.
- Ejemplo: `seo: add blog post for renta yates cancun`.

### Pull Requests
- Título descriptivo en español o inglés.
- Body con resumen de cambios, archivos modificados y criterios de aceptación.
- Labels apropiados: `seo`, `bug`, `enhancement`, `documentation`, `ai-task`, `from-whatsapp`.

---

## 5. Enrutamiento de Tareas — Archivos de Contexto por Tipo

Antes de ejecutar cualquier tarea, **DEBES leer** el archivo de contexto correspondiente:

| Tipo de Tarea | Archivo que DEBES leer PRIMERO |
|---|---|
| Blog post / contenido SEO / descripción larga | `.agents/workflows/seo-blog-posts.md` |
| Ficha de yate / embarcación en Houzez | `docs/ai/houzez_fleet_skills.md` |
| CRM / leads / seguimiento / GoHighLevel | `docs/ai/ghl_crm_skills.md` |
| Auditoría SEO / keywords / Google Search Console | `docs/ai/gsc_seo_skills.md` |
| CSS / Figma / tokens de diseño / UI | `docs/ai/figma_frontend_skills.md` |
| Bug fix / mantenimiento / GitHub Issues | `docs/ai/github_maintenance_skills.md` |
| Reglas globales de agentes IA | `AGENTS.md` |
| Contexto general del proyecto | `CLAUDE.md` |
| Backlog y prioridades | `docs/scrum/backlog.md` |

---

## 6. Reglas SEO — Redacción de Contenido

Estas reglas aplican SIEMPRE que crees o edites contenido del blog, fichas de yates o descripciones de destinos. Consulta el archivo completo en `.agents/workflows/seo-blog-posts.md`.

### 6.1 Keyword (Frase Clave de Enfoque)
- La keyword debe ir al **INICIO** (primera palabra) del título SEO, la meta descripción y el slug.
- Distribución equitativa en el texto: ~1 mención cada 400 caracteres.
- **Máximo absoluto:** 11 menciones totales (incluyendo H1, H2, párrafos).
- Menos del 50% de los H2 deben contener la keyword. Mínimo 3 H2 con keyword.

### 6.2 Naturalidad del Texto (PRIORIDAD MÁXIMA)
- **Regla de Oro:** La naturalidad humana SIEMPRE tiene prioridad sobre la optimización SEO.
- **Prohibido:** Keyword stuffing, ensaladas de palabras, traducciones forzadas del inglés.
- **Verificación:** Si el texto suena robótico o extraño al leerlo en voz alta, reescríbelo de forma 100% natural. Nunca sacrifiques calidad textual por SEO.
- Usa vocabulario sencillo y común: *barcos* en vez de *embarcaciones*, *hermosos* en vez de *espectaculares*.

### 6.3 Legibilidad (Yoast SEO Semáforo Verde)
1. **Palabras de transición (>30%):** Mínimo 1 de cada 3 oraciones usa transiciones: sin embargo, además, primero, después, por otro lado, de hecho, también, porque.
2. **Subtítulos (<300 palabras):** Ningún bloque de texto después de un encabezado supera 300 palabras. Usa `<h2>` y `<h3>` para dividir.
3. **Complejidad de palabras (<10%):** Menos del 10% de las palabras deben ser complejas (largas, técnicas, poco familiares).

### 6.4 Formato de Entrega
- **HTML puro** para todo contenido destinado a WordPress. NO Markdown.
- Etiquetas estructurales: `<h1>`, `<h2>`, `<h3>`, `<p>`, `<ul>`, `<ol>`, `<li>`, `<strong>`, `<a>`, `<table>`.
- El HTML debe ser copiar-y-pegar listo para WordPress.

### 6.5 Enlaces Obligatorios
- **Internos (inicio y fin):** Enlace a la guía de la ciudad al principio y al final del texto.
- **Internos (playas/destinos):** Cada playa, bahía, isla o punto de interés mencionado DEBE tener un enlace a su artículo de blog. El enlace se integra de forma natural en el párrafo.
- **Externos (mínimo 1):** Al menos un enlace saliente hacia un sitio de autoridad (Wikipedia, TripAdvisor, instituto de turismo oficial).
- **Cross-linking en series:** Si es parte de un grupo de entradas (ej. playas de Nuevo Vallarta), cada entrada enlaza a al menos 1 otra de la serie + al hub/pilar.

### 6.6 Organización de Archivos SEO
```
docs/seo/
├── acapulco/
├── cancun/
├── huatulco/
├── ixtapa/
├── la-paz/
├── los-cabos/
├── mazatlan/
├── nuevo-vallarta/
├── playa-del-carmen/
├── puerto-vallarta/
└── tips-y-recomendaciones/
```

- Categoría = Carpeta. Sin excepciones.
- Formato dual: `{slug}.md` (Markdown limpio) + `{slug}.html` (HTML original de WP).
- Cada ciudad tiene su `descripcion_larga_{ciudad}.md`.
- Cero subcarpetas innecesarias. Cero duplicados.
- Borradores: marcar con `> **Estado:** 🟡 BORRADOR (Draft)` en el header.
- Contenido general va en `tips-y-recomendaciones/`.

### 6.7 Checklist Pre-Publicación
Antes de finalizar cualquier pieza de contenido SEO, verifica:
- [ ] Keyword al INICIO del título SEO, meta descripción y slug
- [ ] Texto 100% natural — sin ensaladas de palabras ni frases robóticas
- [ ] Menciones de keyword dentro del rango según tabla de caracteres
- [ ] Menos del 50% de H2 con keyword; mínimo 3 H2 con keyword
- [ ] Distribución equitativa de menciones en todo el texto
- [ ] >30% de oraciones con palabras de transición
- [ ] <10% de palabras complejas
- [ ] Subtítulos cada <300 palabras
- [ ] Formato HTML puro listo para WordPress
- [ ] Enlace a guía de ciudad al inicio y al final
- [ ] Playas y destinos enlazados a su artículo de blog
- [ ] Mínimo 1 enlace externo de autoridad
- [ ] Categoría/carpeta correcta según la ciudad
- [ ] 2-3 imágenes con atribución en comentarios HTML
- [ ] CTA con WhatsApp, correo y catálogo al final
- [ ] Archivo guardado en `docs/seo/{ciudad}/`

---

## 7. Fichas de Yates (Houzez Fleet Manager)

Cuando la tarea involucre crear o actualizar fichas de embarcaciones:

1. Lee `docs/ai/houzez_fleet_skills.md` para entender el proceso completo.
2. Transforma datos crudos del propietario en descripción HTML premium con SEO natural.
3. TODO se publica con status `draft` (borrador) — nunca publicar directamente.
4. Asigna taxonomías correctas: Destino (ciudad), Tipo (Yates Básicos, Premium, etc.).
5. Llena metadatos Yoast: `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw`.
6. Evita traducciones literales del inglés y frases genéricas de marketing.

---

## 8. Auditoría SEO y Google Search Console

Cuando la tarea involucre análisis SEO o datos de Google:

1. Lee `docs/ai/gsc_seo_skills.md` para el contexto.
2. **Cazador de Oportunidades:** Identificar URLs con alta impresión pero posición 11-30 (página 2-3 de Google). Sugerir cambios concretos en títulos, H2 y meta descripciones.
3. **Inspector de Canibalización:** Detectar si dos URLs del sitio compiten por la misma query. Recomendar cuál es la página pilar y cuál la secundaria.
4. Todo análisis es en modo lectura — genera reportes y recomendaciones, no cambios directos.

---

## 9. Frontend y Diseño (Figma → CSS)

Cuando la tarea involucre CSS, diseño o tokens de Figma:

1. Lee `docs/ai/figma_frontend_skills.md` y revisa la carpeta `redesign/`.
2. **Prohibido:** Estilos CSS inline (`style="color: #333"`). Todo debe usar clases o variables CSS.
3. Respetar los tokens del sistema de diseño existentes.
4. Verificar que haya un solo `<h1>` por página, contrastes accesibles y atributos `alt` en imágenes.
5. Cualquier nuevo componente debe ser responsivo.

---

## 10. Mantenimiento y Bugs (GitHub)

Cuando la tarea sea un fix, bug, enlace roto o tarea de mantenimiento:

1. Lee `docs/ai/github_maintenance_skills.md`.
2. Crea una rama aislada (`fix/...`).
3. Haz el cambio mínimo necesario — no refactorizar más de lo pedido.
4. Abre un PR limpio con título descriptivo.
5. Si el fix afecta SEO, verifica que no rompa estructura de enlaces.

---

## 11. CRM y GoHighLevel

Cuando la tarea involucre CRM, leads o seguimiento:

1. Lee `docs/ai/ghl_crm_skills.md`.
2. **NUNCA** enviar mensajes directos a clientes o propietarios.
3. Si se pide redactar un follow-up, crear borrador de mensaje — no enviarlo.
4. No disparar acciones destructivas en pipelines.
5. Todo queda como borrador/nota para revisión humana.

---

## 12. Fuentes de Verdad

Cuando un dato dependa de un sistema externo, no lo inventes:

| Dato | Fuente de verdad | NUNCA inventar |
|---|---|---|
| Leads, pipeline, cotizaciones | GoHighLevel (CRM) | Estado de reserva |
| Fichas de yates, URLs, SEO | WordPress (yatezzitos.com) | Precios, capacidades |
| Disponibilidad real | Propietario / calendario | Fechas disponibles |
| Pagos confirmados | Pasarela de pago / banco | Confirmación de pago |
| Documentación técnica | Este repositorio (GitHub) | Decisiones aprobadas |
| Rendimiento SEO | Google Search Console | Posiciones de keywords |

---

## 13. Estructura del Repositorio

```
.github/                    → Templates, workflows CI, estas instrucciones
.agents/workflows/           → Workflows operativos (SEO, orquestador)
ai/assistants/               → Specs de agentes IA (Marina, Timón, Capitán, Ola)
docs/
  architecture/              → Stack técnico, decisiones, módulos
  brand/                     → Identidad de marca
  business/                  → Visión y modelo de negocio
  crm/                       → GoHighLevel: pipelines, automatizaciones
  scrum/                     → Backlog maestro y planes de ejecución
  seo/{ciudad}/              → Contenido SEO por destino (.md + .html)
  ai/                        → Skills de IA (fleet, CRM, GSC, Figma, GitHub)
  tools/                     → Guías de herramientas externas
integrations/                → Webhooks y servicios (Cloudflare Workers)
plugins/                     → Plugins WordPress custom
redesign/                    → CSS, tokens de diseño, assets del rediseño
wordpress/                   → Temas, mu-plugins, snippets
data/                        → Catálogo de yates, destinos, FAQs, templates
scripts/                     → Scripts de automatización
```

---

## 14. Tono de Comunicación

### En código y commits:
- Inglés técnico estándar para nombres de variables, funciones y commits.

### En contenido de usuario (blog, fichas, descripciones):
- **Profesionalismo premium** — Reflejar marca de lujo náutico.
- **Calidez** — Amigable sin ser informal.
- **Claridad** — Directo, sin jerga técnica innecesaria.
- **Español mexicano** como idioma principal. Inglés solo cuando el contexto lo requiera.
- **Naturalidad** — Si suena como una máquina lo escribió, reescríbelo.

---

## 15. Prioridades Actuales del Proyecto

### Fase 1 (en curso):
1. Terminar rediseño web (Home, Blog, Blog Details — Figma → WordPress)
2. SEO: optimizar ciudades activas y asignar keywords a todos los yates
3. Ordenar CRM (GoHighLevel)
4. Automatizar cotización, recibo de depósito y seguimiento

### Fase 2 (siguiente):
5. Feedback en pipeline de turistas
6. Captación y onboarding de propietarios
7. Mapa de integraciones
8. Calendario de disponibilidad en tiempo real

---

## 16. Memoria del Proyecto

Este repositorio usa dos archivos de memoria persistente:
- **`CLAUDE.md`** — Contexto general del proyecto.
- **`AGENTS.md`** — Reglas operativas de todos los agentes IA.

Si tu tarea requiere actualizar reglas o contexto global, **DEBES actualizar AMBOS archivos**. Nunca actualizar solo uno.

---

*Este archivo es la fuente de verdad para las instrucciones del Copilot coding agent.*
*Para reglas completas de SEO, consulta `.agents/workflows/seo-blog-posts.md`.*
*Para reglas globales de agentes, consulta `AGENTS.md`.*
