---
description: Flujo completo para actualizar fichas de yates (contenido H1 + metadatos Yoast SEO) via WordPress MCP. Usar este workflow siempre que se optimicen páginas de embarcaciones o ciudades.
---

# SEO WordPress MCP — Flujo de Trabajo Obligatorio

> **Usa este workflow** cuando necesites actualizar descripciones largas, títulos SEO, meta descripciones o palabras clave en fichas de yates o páginas de ciudad en yatezzitos.com.

---

## 1. Herramientas disponibles

### 1.1 Plugin propio (PRINCIPAL para Yoast SEO)
- **Archivo**: `plugins/yatezzitos-yoast-rest-api/yatezzitos-yoast-rest-api.php`
- **Endpoint**: `POST /yatezzitos/v1/update-yoast`
- **Parámetros**:
  ```json
  {
    "id":      123,
    "type":    "post",
    "title":   "Título SEO aquí",
    "desc":    "Meta descripción aquí",
    "focuskw": "palabra clave aquí"
  }
  ```
- **Para páginas de ciudad** usar `"type": "term"` y el `id` del término de taxonomía.
- **Acciones internamente**: llama a `update_post_meta()` + invalida caché de Yoast Indexable.
- ⚠️ **NUNCA uses** `meta: {_yoast_wpseo_*}` ni `property_meta: {_yoast_wpseo_*}` directamente en el endpoint `/wp/v2/properties/{id}` — esos campos NO están expuestos en la REST API estándar y los datos no se guardarán.

### 1.2 WordPress MCP (para contenido del post)
- **Endpoint**: `POST /wp/v2/properties/{id}`
- **Usar para**: actualizar el campo `content` (descripción larga HTML)
- **No usar para**: campos Yoast SEO (usar plugin propio)

### 1.3 Google Search Console MCP
- **Endpoint GSC**: `sc-domain:yatezzitos.com` si tienes permisos — usar `https://yatezzitos.com/`
- **Usar para**: investigar keywords reales con impresiones/clics antes de asignar focus keyword

---

## 2. Flujo completo paso a paso

### Paso 1 — Localizar el yate en WordPress
```
GET /wp/v2/properties?slug={slug-del-yate}
```
Obtener el `id` del post. Verificar en `yoast_head_json.title` el estado actual de Yoast.

### Paso 2 — Consultar GSC para keywords (si disponible)
```
mcp_gscServer_get_advanced_search_analytics(
  site_url="https://yatezzitos.com/",
  dimensions="query",
  filter_dimension="query",
  filter_expression="[ciudad]"
)
```
Identificar queries long-tail con impresiones reales.

### Paso 3 — Proponer lista de SEO al usuario (ANTES de enviar)
Presentar tabla con:
| Yate | Keyword | Título SEO | Meta Descripción |
Esperar aprobación explícita antes de enviar a WordPress.

**Reglas de la propuesta**:
- La meta descripción **DEBE comenzar con la keyword exacta**
- Sin canibalización entre yates: cada uno en su carril semántico único
- Keyword debe estar presente en el texto largo del yate

### Paso 4 — Actualizar descripción larga (si aplica)
```
POST /wp/v2/properties/{id}
{ "content": "<h1>Keyword aquí</h1><p>...</p>..." }
```
Regla crítica: el **primer título de la descripción larga SIEMPRE es `<h1>`**. Los demás son `<h2>` y `<h3>`.

### Paso 5 — Actualizar Yoast SEO (SIEMPRE con el plugin propio)
```
POST /yatezzitos/v1/update-yoast
{
  "id": {wp_id},
  "type": "post",
  "title": "{Título SEO aprobado}",
  "desc": "{Meta descripción aprobada}",
  "focuskw": "{Keyword aprobada}"
}
```
Respuesta esperada: `{"success": true, "message": "Yoast SEO fields updated successfully for post {id}"}`

### Paso 6 — Verificar que se guardó
```
GET /wp/v2/properties/{id}
```
Revisar `yoast_head_json.title` y `yoast_head_json.description`. Si están en blanco o son auto-generados, el plugin puede no estar activo — verificar con el usuario.

### Paso 7 — Actualizar el JSON local de referencia
Actualizar el archivo correspondiente en `data/yachts/Destinos/{Ciudad}/{Tipo}/{nombre}.json` con los campos SEO:
```json
{
  "yoast_focuskw": "...",
  "yoast_title": "...",
  "yoast_metadesc": "...",
  "wp_id": 56362
}
```

### Paso 8 — Enviar URLs a Google Search Console (tras cambios de slug o contenido nuevo)
```
mcp_gscServer_manage_sitemaps(
  site_url="https://yatezzitos.com/",
  action="submit",
  sitemap_url="https://yatezzitos.com/sitemap.xml"
)
```
O inspeccionar URLs individuales actualizadas.

---

## 3. Procesamiento en lote

Para lotes de yates, siempre:
1. Hacer **primero la propuesta completa** — una tabla con todos los yates
2. Esperar **aprobación del usuario**
3. Enviar en **lotes paralelos de 5** usando llamadas MCP simultáneas
4. Verificar al final con GET de una muestra para confirmar que se guardó

---

## 4. Anti-canibalización

Cada yate debe tener una keyword **semánticamente única**:
- **Lujo**: usar `yate de lujo`, `Azimut`, `Sea Ray` (diferente por marca)
- **Precio**: `económico` vs `barato` (diferentes términos)
- **Tipo**: `yate` vs `barco` vs `lancha` vs `bote` (intención distinta)
- **Uso**: `fiestas` vs `eventos` vs `pesca` vs `paseo` (distintos propósitos)
- **Ciudad base**: `en Cabo San Lucas` vs `en Los Cabos` (más específico vs más general)

---

## 5. Datos del JSON local sirven como fuente de verdad

El JSON en `data/yachts/Destinos/` es la referencia canónica para:
- Identificar el `wp_id` sin llamar a la API
- Ver la keyword asignada previamente (evitar canibalización)
- Ver si el yate ya fue optimizado o está pendiente

---

## 6. Errores comunes y cómo evitarlos

| Error | Causa | Solución |
|-------|-------|----------|
| Yoast fields no se guardan | Usar `meta` o `property_meta` directo | Usar siempre `/yatezzitos/v1/update-yoast` |
| URLs inventadas que no existen | No verificar en WP primero | Siempre `GET /wp/v2/properties?slug=...` antes |
| Keyword canibalizada | No revisar los otros yates | Revisar JSON local o propuesta anterior antes de asignar |
| Meta descripción rechazada | No empieza con la keyword | La meta SIEMPRE debe comenzar con la keyword exacta |
| H1 incorrecto | Poner H2 como primer título | El **primer título del content SIEMPRE es `<h1>`** |
| Cambio de slug no indexado | Olvidar enviar a GSC | Tras cualquier cambio de URL, enviar a Search Console |