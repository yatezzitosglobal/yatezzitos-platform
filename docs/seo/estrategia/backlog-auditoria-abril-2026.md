# Backlog ejecutable de la auditoría SEO integral — Abril 2026

> Fuente: auditoría SEO integral de `yatezzitos.com` recibida el 17/abr/2026 (GSC 90d + Ahrefs + crawl HTTP en vivo).
> Convierte los hallazgos en tareas operativas priorizadas, clasificadas por ejecutabilidad y listas para deploy.

---

## Clasificación operativa

- **Grupo A — Ejecutable automáticamente desde el repositorio** (MU-plugins + scripts REST API).
- **Grupo B — Parcialmente ejecutable / requiere decisión de negocio o acceso admin WP**.
- **Grupo C — No ejecutable desde repo / requiere trabajo de contenido, diseño, traducción u off-site**.

---

## Tabla priorizada (alto → bajo impacto)

| # | Tarea | URLs afectadas | Impacto esperado (GSC 90d) | Beneficio SEO | Dificultad | Grupo | Dependencia | Riesgo | Rollback |
|---|---|---|---|---|---|---|---|---|---|
| 1 | 301 de URLs 404 canibalizadoras a sus canónicas | `/es/ciudad/yates-acapulco/`, `/es/ciudad/yates-huatulco/`, `/es/ciudad/renta-yates-en-ixtapa/`, `/es/ciudad/en-ciudad-yates-cabos/` | +250 a +320 clics/90d | Recupera tráfico 404 + consolida cluster | Baja | **A** | Subir MU-plugin de redirects | Bajo (301 es reversible) | Desactivar MU-plugin |
| 2 | Reescribir title + meta Cancún city | `/es/ciudad/renta-de-yates-cancun/` | +500 a +1,500 clics/90d | Sube CTR 2.45% → >5% | Baja | **A** | Script curl al endpoint Yoast | Bajo | Guardar título anterior en JSON local |
| 3 | Reescribir title + meta `/es/playas-en-puerto-vallarta/` | esa URL | +300 a +600 clics/90d | 38k impresiones, CTR 0.09% | Baja | **A** | Script curl al endpoint Yoast | Bajo | Snapshot previo |
| 4 | Reescribir title + meta homepage con `%%currentyear%%` + "México" | `/es/` | +duplicar clics home | Home en top 3 para queries cabeza | Media | **A** | Script curl al endpoint Yoast | Medio (página más visible) | Snapshot previo |
| 5 | Noindex + excluir sitemap ~24 URLs utilitarias | `/es/login/`, `/es/mi-perfil/`, `/es/mi-reserva/`, `/es/gracias*`, `/es/pagos-con-*`, `/es/user-dashboard*`, etc. | Libera crawl budget · 0 pérdida tráfico | Elimina "low quality pages" | Baja | **A** | MU-plugin con filtros `wpseo_sitemap_exclude_url` + meta robots | Bajo | Desactivar MU-plugin |
| 6 | Fix typo `/es/brookers-de-yates-en-mexico/` → `/es/brokers-de-yates-en-mexico/` | esa URL | Rankear kw correcta | Captura "brokers yates mexico" | Baja | **A** (redirect) + B (crear destino en WP admin) | Crear slug destino en WP | Bajo | Remover 301 |
| 7 | Eliminar duplicado `/es/user-dashboard-2/` del sitemap | `page-sitemap.xml` | Limpieza señal | Sitemap consistente | Baja | **A** | MU-plugin | Bajo | Desactivar MU-plugin |
| 8 | Reescribir title/meta Puerto Vallarta city | `/es/ciudad/renta-de-yates-en-puerto-vallarta/` | +300 a +700 clics/90d | Empuja cluster PV de pos 7-9 a top 3 | Baja (meta) + Media (contenido) | **A** (meta) + C (contenido) | Script curl | Bajo | Snapshot previo |
| 9 | Reescribir Huatulco, Mazatlán, Acapulco, La Paz metas con FAQ hooks | 4 URLs ciudad | +200-500 clics/90d | Mejora CTR por ciudad | Baja | **A** | Script curl batch | Bajo | Snapshot previo |
| 10 | FAQ schema on-page en 10 páginas de ciudad | 10 ciudades | +CTR 1-2pp | Rich snippet FAQ | Media | **A** (MU-plugin) + B (validación) | MU-plugin genera JSON-LD desde meta term | Bajo | Desactivar MU-plugin |
| 11 | CTAs "Renta un yate para ir a {playa}" en blogs playas | `/es/playa-madagascar-puerto-vallarta/`, `/es/playa-caballo-*`, `/es/playa-tecolote-*`, `/es/isla-espiritu-santo-*`, `/es/playa-la-india-huatulco/` | Monetiza ~150 clics/mes info | Funnel info → transaccional | Media | **A** (shortcode + snippet) + B (insertar en posts) | Shortcode WP | Bajo | Remover shortcode |
| 12 | Decisión `/en/`: noindex + retirar hreflang (si no se traduce) | Todas `/en/*` | Consolida señal es-MX | Elimina hreflang roto | Baja | **A** (si no traducir) | MU-plugin que añade noindex a `/en/*` | Medio (decisión de negocio) | Desactivar MU-plugin |
| 13 | Renombrar slugs ciudad al patrón `renta-de-yates-en-{ciudad}/` | 7 ciudades con slug no canónico | +200-600 clics/90d | Cluster consistente | Media | **B** | Cambiar slug de term `property_city` en WP admin + 301 automático | Medio (URL change masivo) | 301 inverso |
| 14 | Reescribir `/es/yates-en-mexico/` como hub país | esa URL | Triplica/cuadruplica clics página | Nueva columna del enlazado interno | Media | **C** | Contenido editorial + diseño | Bajo | Snapshot previo |
| 15 | Nueva landing `/es/yates-de-lujo-en-mexico/` | nueva | Recupera 60k impresiones de Bravo | Captura query genérica | Media | **C** | Nueva página + contenido | Bajo | Despublicar |
| 16 | Nueva landing `/es/precios-renta-yate-mexico/` | nueva | Rankea "cuanto cuesta" (vol 385) | Nuevo pilar | Media | **C** | Nueva página + contenido | Bajo | Despublicar |
| 17 | `alt` descriptivos en 68 imágenes sin alt del home | homepage | Image search + a11y | Mejora técnica | Media | **B** | Acceso media library | Bajo | Revertir alts |
| 18 | Schema LocalBusiness por ciudad | 10 ciudades | SERP Local Pack reforzado | Rich snippets locales | Media | **A** (MU-plugin) | MU-plugin lee datos de ACF/term meta | Bajo | Desactivar MU-plugin |
| 19 | Organization + WebSite + SearchAction JSON-LD en home | home | Sitelink search box | Rich results | Media | **A** (MU-plugin) | MU-plugin inyecta en `wp_head` | Bajo | Desactivar MU-plugin |
| 20 | Ampliar robots.txt con Disallow de utilitarias | raíz | Cinturón + tirantes | Complemento noindex | Baja | **B/C** | Acceso root/hosting o plugin `Virtual Robots.txt` | Bajo | Revertir |
| 21 | Campaña link building 30 targets | off-site | +RD | Autoridad | Alta | **C** | PR + outreach | Bajo | — |
| 22 | 20 landings bodas/despedidas por ciudad | nuevas | +demanda cluster experiencial | Nueva demanda | Alta | **C** | Contenido | Bajo | Despublicar |

---

## Resumen ejecutable en este repo (Grupo A)

### MU-plugins a crear (todos en `wordpress/mu-plugins/`, autoload sin activación manual):

1. `yzz-seo-301-redirects.php` — cubre tareas #1, #6, #13 (redirects históricos)
2. `yzz-seo-noindex-utility.php` — cubre tareas #5, #7, #12 (noindex + sitemap exclusions)
3. `yzz-seo-schema-enrichment.php` — cubre tareas #10, #18, #19 (JSON-LD Organization/LocalBusiness/FAQPage)
4. `yzz-seo-cta-beach-blogs.php` — cubre tarea #11 (shortcode `[yzz_cta_ciudad]`)

### Scripts a crear (en `scripts/seo/`):

1. `rewrite-yoast-priority-pages.sh` — ejecuta tareas #2, #3, #4, #8, #9 vía endpoint propio Yoast
2. `snapshot-yoast-before.sh` — snapshot de seguridad antes de cualquier cambio de meta

### Enhancements al plugin existente:

1. Endpoint `GET /yatezzitos/v1/read-yoast` para hacer snapshot antes del update (requisito de rollback)

---

## Tareas del Grupo B (requieren acción adicional fuera del repo)

- #13 Renombrar slugs de ciudades: operación en WordPress admin (Propiedades → Ciudades) + Redirection plugin automático; **el MU-plugin `yzz-seo-301-redirects.php` ya captura los slugs viejos** para que cuando se ejecute el renombrado no haya gap.
- #6 Crear destino `/es/brokers-de-yates-en-mexico/`: requiere crear la página en WP admin. El MU-plugin de redirects ya captura el typo antiguo.
- #17 Alts en imágenes: requiere pasada manual o plugin tipo "ShortPixel Bulk Alt".
- #20 Robots.txt: requiere acceso a hosting o plugin `Virtual Robots.txt`.

## Tareas del Grupo C (requieren contenido / traducción / off-site)

- #14, #15, #16, #21, #22 — trabajo editorial + PR.

---

## Criterios de éxito (GSC)

| KPI | Baseline 90d | Target 30d | Target 60d | Target 90d |
|---|---|---|---|---|
| Clics totales | 8,696 | 9,500 | 11,200 | 13,500 |
| CTR medio | 1.75% | 2.20% | 2.60% | 3.00% |
| Posición media | 6.0 | 5.5 | 5.0 | 4.5 |
| URLs 404 con clics | 4 | 0 | 0 | 0 |
| Páginas utilitarias indexables en sitemap | 24+ | 0 | 0 | 0 |
| Ciudades con slug patrón canónico | 3/10 | 3/10 | 10/10 | 10/10 |

---

*Generado el 17 de abril de 2026 sobre la auditoría integral del 15 de abril. El orden de deploy va en el README de `/scripts/seo/`.*
