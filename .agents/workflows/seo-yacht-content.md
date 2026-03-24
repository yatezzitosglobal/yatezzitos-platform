---
description: Reglas obligatorias para optimización SEO de fichas de yates en yatezzitos.com
---

# SEO de Fichas de Yates — Reglas Obligatorias

> ⚠️ **Para enviar datos a Yoast SEO via API**, consulta primero el workflow completo en `.agents/workflows/seo-wordpress-mcp.md`. Ese documento define las herramientas y el endpoint correcto a usar.

## 1. Meta Descripción SEO
- **Máximo 142 caracteres** (estricto, sin excepción)
- **DEBE comenzar con la keyword exacta** (requerimiento de Yoast)
- Incluir modelo, capacidad y un CTA corto

## 2. Título SEO (Yoast)
- Debe incluir la **palabra clave principal**
- Formato recomendado: `[Keyword] con el [Nombre] [Modelo]`

## 3. Focus Keyword (Yoast)
- Elegir una keyword **long-tail única** para cada yate (sin canibalización)
- Basarse en datos reales de Google Search Console (`https://yatezzitos.com/`)
- La keyword debe aparecer ya en el texto largo (en `<strong>`)
- Patrón: `renta de yate [diferenciador] en [ciudad]`

## 4. Envío a Yoast — Plugin obligatorio
- **Usar SIEMPRE**: `POST /yatezzitos/v1/update-yoast` (plugin `yatezzitos-yoast-rest-api`)
- **NUNCA usar** `meta: {_yoast_wpseo_*}` en el endpoint estándar de WP REST API — no funciona

## 5. Slug SEO
- **El agente NO puede modificar el slug directamente** — solo sugerirlo al usuario
- El slug DEBE incluir la **palabra clave principal**
- Formato: `renta-de-yate-en-[ciudad]-[nombre-yate]-[modelo]`
- Después de que el usuario confirme el cambio del slug, **enviar la nueva URL a Google Search Console** para indexación

## 6. Descripción Larga (Content)
- Formato: **HTML puro** (nunca Markdown)
- El **primer encabezado SIEMPRE es `<h1>`** y DEBE incluir la **keyword focus** — ⚠️ NUNCA H2 como primer título
- Los encabezados subsiguientes usan jerarquía: H2 → H3

### 5.1 Fuente de datos
- Extraer TODA la información de los campos existentes del yate:
  - Amenidades (del campo `property_feature`)
  - Precio (de `fave_property_price`)
  - Capacidad (de `fave_property_size`)
  - Ubicación (de `fave_property_address`)
  - Baños/Camarotes (de `fave_property_bathrooms`, `fave_property_bedrooms`)
- **NUNCA inventar datos** que no estén en la ficha

### 5.2 Tablas
- **Siempre** poner un separador `<hr>` ANTES de cada tabla
- **Siempre** poner un separador `<hr>` DESPUÉS de cada tabla

### 5.3 Enlaces Internos
- **Mínimo 5 enlaces internos** salientes hacia el blog de yatezzitos.com
- Buscar TODAS las entradas del blog en la categoría de la ciudad del yate
- Tipos de enlaces a incluir:
  - Guía completa de la ciudad/destino
  - Guía de playas e islas del destino
  - Guía de pesca deportiva del destino
  - Artículos sobre destinos visitables en el recorrido (El Arco, bahías, playas, etc.)
  - Avistamiento de ballenas (si aplica por temporada)
  - Catálogo de yates de la ciudad (`/es/ciudad/yates-[ciudad]/`)
  - Guía general de renta de yates en México
- Si la ciudad tiene más entradas en el blog, **usar la mayoría** para darles autoridad

### 5.4 Estructura recomendada
1. H2 con keyword + nombre del yate
2. Párrafo introductorio con keyword en bold + enlace a guía de la ciudad
3. H3 — ¿Qué incluye? (amenidades reales de la ficha)
4. H3 — Precios (tabla con `<hr>` antes y después)
5. H3 — Recorridos disponibles (con H4 por tipo de tour + enlaces internos a destinos)
6. H3 — Experiencias especiales (ballenas, pesca, etc. con enlaces)
7. H3 — Especificaciones técnicas (tabla con `<hr>` antes y después)
8. H3 — ¿Cómo reservar? (pasos + WhatsApp + enlaces a catálogo)
9. H3 — Preguntas Frecuentes (H4 por pregunta)

### 5.5 Legibilidad (Yoast SEO verde)
- Palabras de transición: >30% de las oraciones
- Bloques de texto: <300 palabras entre subtítulos
- Vocabulario sencillo: <10% palabras complejas
- Tono: premium, profesional, natural — cero "ensalada de palabras"
