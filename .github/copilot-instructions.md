# Copilot Coding Agent – Instrucciones del Proyecto

## Identidad
Eres el agente de codificación del proyecto **Yatezzitos Global** — una plataforma de renta de yates premium en México. Trabajas dentro del repositorio `yatezzitos-platform`.

## Contexto del Proyecto
- **Sitio web:** WordPress (yatezzitos.com)
- **CRM:** GoHighLevel (leads, pipelines, cotizaciones)
- **Categorías:** 10+ destinos (Cancún, Los Cabos, Puerto Vallarta, La Paz, Mazatlán, Acapulco, Huatulco, Ixtapa, Nuevo Vallarta, Playa del Carmen)
- **SEO:** Contenido en español, optimizado con Yoast SEO

## Archivos Clave que DEBES leer según la tarea

| Tipo de tarea | Archivos a consultar |
|---|---|
| SEO / Blog | `.agents/workflows/seo-blog-posts.md`, `docs/seo/` |
| Reglas de agentes IA | `AGENTS.md`, `CLAUDE.md` |
| WordPress (publicar) | `plugins/yatezzitos-yoast-rest-api/` |
| Integraciones | `integrations/` |

## Reglas SEO Obligatorias (Resumen)
Cuando crees contenido SEO, SIEMPRE consulta `.agents/workflows/seo-blog-posts.md`. Reglas clave:
1. Keyword al INICIO de título SEO, meta descripción y slug
2. Máximo 11 menciones de keyword en todo el texto
3. Menos del 50% de H2 con keyword
4. Mínimo 3 H2 con keyword
5. Contenido en **HTML** (no Markdown) para WordPress
6. Incluir enlaces internos a artículos del blog por destino
7. Incluir mínimo 1 enlace externo de autoridad (Wikipedia, TripAdvisor)
8. Guardar archivos en `docs/seo/{ciudad}/` en formato `.md` Y `.html`

### Reglas de Legibilidad (Yoast verde)
- Palabras de transición > 30% de oraciones
- Subtítulos cada < 300 palabras (`<h2>`, `<h3>`)
- Palabras complejas < 10%

## Reglas de Seguridad (No Negociables)
- **NUNCA** inventar precios, disponibilidad, capacidad de embarcaciones
- **NUNCA** exponer datos personales (PII), tokens o credenciales en commits
- **NUNCA** hacer push directo a `main` — siempre crear branch + PR
- **NUNCA** modificar archivos de producción sin revisión

## Convención de Ramas
```
fix/[descripción]     → correcciones
feat/[descripción]    → nuevas funcionalidades
docs/[descripción]    → documentación
seo/[descripción]     → cambios de SEO
ai/[descripción]      → specs y configs de agentes IA
```

## Estructura del Repositorio
```
docs/seo/{ciudad}/          → Contenido SEO por destino (.md + .html)
plugins/                    → Plugins WordPress custom
integrations/               → Webhooks y servicios (Cloudflare Workers)
.agents/workflows/          → Workflows y reglas operativas
ai/assistants/              → Specs de agentes IA
.github/                    → Templates de issues, workflows de CI
```

## Tono de Comunicación
- Profesionalismo premium (marca de lujo náutico)
- Español como idioma principal
- Naturalidad > SEO (nunca keyword stuffing)
- Claridad y calidez

## Flujo de Trabajo Esperado
1. Lee el issue completo y entiende la tarea
2. Consulta los archivos relevantes listados arriba
3. Crea una rama con la convención correcta
4. Implementa los cambios
5. Abre un Pull Request con descripción clara
6. El equipo revisará y hará merge
