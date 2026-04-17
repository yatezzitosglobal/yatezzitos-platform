# Guía técnica WordPress + Yoast SEO — operación de yatezzitos.com

> Fuente de verdad técnica para operar el sitio WordPress de Yatezzitos, el plugin Yoast REST API propio y los JSONs locales de yates. Consolida el contexto ganado entre marzo–abril 2026.
>
> Leer junto con:
> - [`docs/memory/anti-gravity-migration.md`](anti-gravity-migration.md) — resumen del trabajo hecho y convenciones.
> - [`docs/memory/claude-mcp-setup.md`](claude-mcp-setup.md) — cómo conectar el MCP de WordPress (§3).
> - [`.agents/workflows/seo-yacht-content.md`](../../.agents/workflows/seo-yacht-content.md) — reglas de redacción de fichas de yates.
> - [`.agents/workflows/seo-blog-posts.md`](../../.agents/workflows/seo-blog-posts.md) — reglas de redacción de blog.
> - [`.agents/workflows/seo-wordpress-mcp.md`](../../.agents/workflows/seo-wordpress-mcp.md) — flujo paso a paso de edición vía WordPress MCP.

---

## 1. El problema que resuelve el plugin

WordPress tiene una REST API estándar. El problema es que el sitio de Yatezzitos usa **Houzez Theme** con el custom post type `property` (no `post`) para las fichas de yates. Además, las páginas están construidas con **Elementor**, que guarda su layout en el campo `_elementor_data` como un JSON enorme.

### Lo que NO funciona (y por qué)

```http
POST /wp/v2/properties/{id}
{
  "meta": {
    "_yoast_wpseo_title": "Mi Título SEO",
    "_yoast_wpseo_metadesc": "Mi meta descripción",
    "_yoast_wpseo_focuskw": "mi keyword"
  }
}
```

Los campos `_yoast_wpseo_*` **NO están registrados** por Yoast en la REST API estándar para el CPT `property`. WordPress simplemente ignora los campos que no están explícitamente registrados con `register_post_meta(..., ['show_in_rest' => true])`. El request devuelve 200 OK pero los datos **nunca se guardan** en la base de datos.

### Lo que TAMPOCO funciona para Elementor

Intentar usar `property_meta` o `meta` para actualizar el `content` de la ficha corrompe el layout de Elementor, porque Elementor usa `post_content` como contenedor de su JSON serializado. Cuando sobrescribes `content` con HTML plano, Elementor pierde su estructura.

**Solución pragmática:** el campo de "descripción larga" en Houzez/Elementor no es `content` estrictamente — es una meta field propia de Elementor llamada `_elementor_data`. Para el campo de descripción del yate, usamos el campo `content` del CPT `property`, que Elementor usa pero no sobrescribe completamente. Hay que tener cuidado y verificar en cada implementación.

---

## 2. El plugin yatezzitos-yoast-rest-api

### Ubicación en el repositorio
```
plugins/yatezzitos-yoast-rest-api/yatezzitos-yoast-rest-api.php
```

> ⚠️ Este plugin debe estar instalado y **activo** en WordPress para que todo funcione. Si el endpoint devuelve 404, el plugin no está activo.

### Lo que hace el plugin
1. **Registra** los campos `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw` como editables vía REST API para los post types: `post`, `page`, `property`.
2. **Registra** esos mismos campos para las taxonomías: `property_city`, `property_category`, `property_feature`.
3. **Invalida el caché de Yoast Indexable** — Yoast Premium guarda los datos SEO renderizados en `wp_yoast_indexable`. Si escribes directamente en `post_meta`, Yoast no se entera. El plugin hace UPDATE directo en esa tabla para forzar la sincronización.
4. **Expone el endpoint custom** `POST /yatezzitos/v1/update-yoast` como punto de entrada simplificado.

---

## 3. Endpoints de la REST API que usamos

### 3.1 Descripción larga del yate

```http
POST /wp/v2/properties/{wp_id}
Authorization: Basic {base64(user:app_password)}
Content-Type: application/json

{
  "content": "<h1>Keyword Aquí: Nombre del Yate</h1>\n<p>...</p>"
}
```

**Regla crítica:** el **primer tag del `content` SIEMPRE es `<h1>`** con la keyword focus. Nunca `<h2>` como primer título. Los demás encabezados son `<h2>` y `<h3>`.

**Formato:** HTML puro. Nunca Markdown.

### 3.2 Metadata Yoast (título SEO, meta descripción, focus keyword)

```http
POST /yatezzitos/v1/update-yoast
Authorization: Basic {base64(user:app_password)}
Content-Type: application/json

{
  "id": 52886,
  "type": "post",
  "title": "Mega Yates Acapulco | Yate Noon Sunseeker Manhattan 95ft con Chef",
  "desc": "El megayate más exclusivo de Acapulco. 18 pasajeros, chef, bartender, internet, kayaks...",
  "focuskw": "mega yates Acapulco"
}
```

**Respuesta esperada:**
```json
{
  "success": true,
  "message": "Yoast SEO fields updated successfully for post 52886"
}
```

**Para páginas de ciudad (términos de taxonomía):**
```json
{
  "id": {term_id},
  "type": "term",
  "title": "...",
  "desc": "...",
  "focuskw": "..."
}
```

### 3.3 Blog posts

```http
POST /wp/v2/posts/{post_id}
Authorization: Basic {base64(user:app_password)}
Content-Type: application/json

{
  "content": "<h1>...</h1><p>...</p>",
  "title": "Título del Post",
  "status": "publish"
}
```

El Yoast de blogs también usa el endpoint `/yatezzitos/v1/update-yoast` con `"type": "post"`.

### 3.4 Búsqueda por slug

```http
GET /wp/v2/properties?slug={slug-del-yate}
```

Devuelve array. El `id` está en `[0].id`. El estado de Yoast está en `[0].yoast_head_json`.

---

## 4. Uso vía WordPress MCP

El MCP activo (`wordpress-mcp` npm + plugin Automattic en WP — ver [`claude-mcp-setup.md §3`](claude-mcp-setup.md)) expone el tool `run_api_function` como punto de entrada genérico. **No lleva parámetro `site`** (el MCP está atado a un único sitio vía env vars).

```python
# Actualizar descripción larga del yate
mcp__wordpress-mcp__run_api_function(
  route="/wp/v2/properties/52886",
  method="POST",
  data={"content": "<h1>...</h1>"}
)

# Actualizar metadata Yoast (plugin propio)
mcp__wordpress-mcp__run_api_function(
  route="/yatezzitos/v1/update-yoast",
  method="POST",
  data={"id": 52886, "type": "post", "title": "...", "desc": "...", "focuskw": "..."}
)

# Buscar yate por slug
mcp__wordpress-mcp__run_api_function(
  route="/wp/v2/properties?slug=yate-noon-sunseeker-manhattan-95ft",
  method="GET"
)
```

> ℹ️ Antes de POST a un endpoint nuevo, usar `get_function_details(route=..., method="POST")` para confirmar los campos aceptados.

---

## 5. JSONs locales de yates

Los metadatos de cada yate se mantienen localmente en:
```
data/yachts/Destinos/{Ciudad}/{Tipo}/{nombre-yate}.json
```

Ejemplo: `data/yachts/Destinos/Yates Acapulco/Yate/yate-noon-sunseeker-manhattan-95ft.json`.

### Schema

```json
{
  "Nombre del Yate": "Yate Noon – Sunseeker Manhattan 95ft",
  "Ciudad/Ubicación": "Yates Acapulco",
  "Categoría": "Yate",
  "URL de Reserva": "https://yatezzitos.com/es/embarcacion/yate-noon-sunseeker-manhattann-95ft/",
  "Precio": "Por 5 horas $170,000/MXN",
  "Capacidad": "18",
  "Amenidades/Incluye": "AGUA NATURAL, BARTENDER, CAPITÁN, CHEF, ...",
  "Año Construcción": "2010",
  "Ubicación de Abordaje": "Club de Yates de Acapulco, ...",
  "Descripción": "<h1>...</h1>...",
  "yoast_title": "Mega Yates Acapulco | Yate Noon Sunseeker Manhattan 95ft con Chef",
  "yoast_focuskw": "mega yates Acapulco",
  "yoast_metadesc": "El megayate más exclusivo de Acapulco. 18 pasajeros, chef...",
  "wp_id": 52886
}
```

> ⚠️ Los JSONs están en `.gitignore` — no se suben a GitHub. Son **fuente de verdad local**.

**Siempre leer los JSONs antes de proponer keywords** para detectar canibalizaciones entre yates del mismo destino.

---

## 6. Anti-canibalización de keywords

Cada yate debe tener una keyword **semánticamente única**. En Acapulco, por ejemplo:

| Yate | Keyword asignada |
|---|---|
| Noon – Sunseeker 95ft | `mega yates Acapulco` |
| Princesa Jiannas – Azimut 42ft | `renta de barcos Acapulco` |
| Dubai – Sundancer 36ft | `paseo en yate Acapulco` |
| Quimbumba – Sunseeker 52ft | `yates de pesca Acapulco` |
| Pantera – Mangusta 50ft | `yate de lujo Acapulco` |
| Dali – Sunseeker 75ft | `yates exclusivos Acapulco` |

### Diferenciadores válidos
- **Tipo:** `yate` vs `barco` vs `lancha` vs `bote`
- **Uso:** `pesca` vs `paseo` vs `celebración` vs `evento`
- **Segmento:** `lujo` vs `exclusivo` vs `económico` vs `familiar`
- **Marca:** `Sunseeker` vs `Azimut` vs `Mangusta`
- **Formato:** `mega yates` vs `yate privado` vs `renta de barcos`

---

## 7. Flujo end-to-end de optimización

1. **Leer el JSON local** del yate → obtener `wp_id`, amenidades, precio, capacidad, keyword actual.
2. **Verificar en WP** si el Yoast está bien: `GET /wp/v2/properties?slug={slug}` → revisar `yoast_head_json`.
3. **Consultar GSC** para ideas de keywords reales con impresiones.
4. **Proponer al usuario** la keyword + título + meta desc (esperar aprobación).
5. **Actualizar content** vía `POST /wp/v2/properties/{id}` con la descripción HTML (recordar `<h1>` primero).
6. **Actualizar Yoast** vía `POST /yatezzitos/v1/update-yoast`.
7. **Actualizar JSON local** con los campos `yoast_title`, `yoast_focuskw`, `yoast_metadesc`.
8. **Marcar en task.md** como completado.

---

## 8. Google Search Console (nota)

- **Propiedad correcta:** `https://yatezzitos.com/` (con `/` al final).
- **NO usar:** `sc-domain:yatezzitos.com`.
- **Uso típico:** investigar keywords reales con impresiones/clics antes de asignar focus keyword.

Setup del MCP de GSC en [`claude-mcp-setup.md §2`](claude-mcp-setup.md).

---

## 9. Errores conocidos

| Error | Síntoma | Solución |
|---|---|---|
| Yoast no se guarda | 200 OK pero el campo queda vacío en WP | Usar SIEMPRE `/yatezzitos/v1/update-yoast`, nunca `meta` directo |
| Plugin no activo | 404 en `/yatezzitos/v1/update-yoast` | Activar el plugin en WP Admin → Plugins |
| Elementor roto | El frontend del yate muestra layout vacío | No sobrescribir `_elementor_data`; solo actualizar `content` |
| Keyword canibalizada | Dos yates compiten por la misma query | Leer JSONs locales antes de proponer; usar diferenciadores (§6) |
| H1 incorrecto | Yoast marca "no H1" o Google tiene H1 genérico | El primer tag del `content` SIEMPRE es `<h1>` |
| Meta desc muy larga | Se muestra truncada en Google | Máx 160 chars, idealmente 142–155 |
| Cache de Yoast desactualizado | El cambio no aparece en el frontend | El plugin invalida automáticamente; si persiste, vaciar cache del servidor (Cloudflare, WP Rocket) |

---

## 10. Referencias cruzadas

| Archivo | Para qué |
|---|---|
| [`CLAUDE.md`](../../CLAUDE.md) | Contexto general, reglas globales, teaser de la regla Yoast |
| [`AGENTS.md`](../../AGENTS.md) | Reglas operativas para agentes IA |
| [`.agents/workflows/seo-wordpress-mcp.md`](../../.agents/workflows/seo-wordpress-mcp.md) | Flujo detallado de edición de yates vía WordPress MCP |
| [`.agents/workflows/seo-yacht-content.md`](../../.agents/workflows/seo-yacht-content.md) | Reglas de contenido SEO de fichas de yates |
| [`.agents/workflows/seo-blog-posts.md`](../../.agents/workflows/seo-blog-posts.md) | Reglas de contenido SEO para blog |
| `plugins/yatezzitos-yoast-rest-api/yatezzitos-yoast-rest-api.php` | Código del plugin que habilita el endpoint |
| `docs/seo/_blog_posts_manifest.json` | Índice de posts de blog por ciudad para enlaces internos |
| `data/yachts/Destinos/` | JSONs locales de cada yate (en `.gitignore`) |

---

**Última actualización:** 17 de abril 2026
**Generado por:** Claude (Cowork) durante la consolidación técnica post-migración de Anti Gravity
