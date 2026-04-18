# Scripts SEO — Ejecución del Grupo A sobre WordPress en vivo

Conjunto de scripts bash que consumen el plugin propio `yatezzitos-yoast-rest-api`
(`POST /yatezzitos/v1/update-yoast` + `GET /yatezzitos/v1/read-yoast`) para aplicar
las mejoras prioritarias de la **auditoría SEO integral de abril 2026**.

## Requisitos

1. Variable de entorno `WP_USER` — usuario WP con capability `edit_posts`
2. Variable de entorno `WP_APP_PASSWORD` — Application Password generado en Mi Perfil → Application Passwords
3. Variable de entorno `WP_BASE_URL` (opcional, default `https://yatezzitos.com`)
4. `curl` y `jq` instalados

```bash
export WP_USER="deploy-bot"
export WP_APP_PASSWORD="xxxx xxxx xxxx xxxx xxxx xxxx"
export WP_BASE_URL="https://yatezzitos.com"
```

## Orden de ejecución recomendado

```bash
# 0. Deploy previo de los MU-plugins (subir wordpress/mu-plugins/*.php al servidor).
# 1. Snapshot de seguridad (rollback)
bash scripts/seo/snapshot-yoast-before.sh

# 2. Reescritura prioritaria de titles/metas (tareas #2, #3, #4, #8, #9 del backlog)
bash scripts/seo/rewrite-yoast-priority-pages.sh

# 3. Verificar el log de redirects tras 24-48h desde consola admin:
#    GET /wp-json/yatezzitos/v1/seo-redirects-log
```

## Rollback

Si necesitas revertir:

```bash
# Reutiliza el snapshot guardado en scripts/seo/snapshots/<timestamp>.json
bash scripts/seo/rollback-yoast.sh scripts/seo/snapshots/2026-04-17_14-30.json
```

## Mapeo tareas → scripts

| Tarea del backlog | Script | Comentario |
|---|---|---|
| #1 Redirects 301 | MU-plugin `yzz-seo-301-redirects.php` | No requiere script — activo al desplegar |
| #2 Title/meta Cancún | `rewrite-yoast-priority-pages.sh` | Sección `rewrite_cancun` |
| #3 Title/meta playas PV | `rewrite-yoast-priority-pages.sh` | Sección `rewrite_playas_pv` |
| #4 Title/meta home | `rewrite-yoast-priority-pages.sh` | Sección `rewrite_home` |
| #5 Noindex utilitarias | MU-plugin `yzz-seo-noindex-utility.php` | No requiere script |
| #7 Fix `user-dashboard-2` | MU-plugin `yzz-seo-noindex-utility.php` | Cubierto por exclusión sitemap |
| #8 PV city | `rewrite-yoast-priority-pages.sh` | Sección `rewrite_puerto_vallarta` |
| #9 Huatulco + Mazatlán + La Paz + Acapulco | `rewrite-yoast-priority-pages.sh` | Sección `rewrite_batch_ciudades` |
| #10 FAQ schema | MU-plugin `yzz-seo-schema-enrichment.php` | No requiere script |
| #18 LocalBusiness | MU-plugin `yzz-seo-schema-enrichment.php` | No requiere script |
| #19 Organization home | MU-plugin `yzz-seo-schema-enrichment.php` | No requiere script |

## Configuración de IDs

Los scripts usan IDs de posts/terms que hay que sembrar antes del primer deploy.
Actualizar `scripts/seo/ids.env` con los WP IDs reales (GET `/wp/v2/pages?slug=...`
o `/wp/v2/property_city?slug=...`). Sin esos IDs los scripts fallan con mensaje claro.

```bash
# Ejemplo ids.env — no commitear datos reales si son sensibles
HOME_PAGE_ID=123
CITY_CANCUN_TERM_ID=456
CITY_PUERTO_VALLARTA_TERM_ID=457
CITY_HUATULCO_TERM_ID=458
CITY_MAZATLAN_TERM_ID=459
CITY_LA_PAZ_TERM_ID=460
CITY_ACAPULCO_TERM_ID=461
PAGE_PLAYAS_PV_ID=789
```
