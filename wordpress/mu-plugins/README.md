# Yatezzitos — MU-Plugins SEO

Colección de **Must-Use plugins** que resuelven automáticamente las tareas del **Grupo A**
de la auditoría SEO integral (abril 2026). Se llaman *must-use* porque WordPress los carga
en cada request sin necesidad de activación desde el admin; ideal para cambios
infraestructurales que queremos versionar en git.

## Ruta de deploy

Sube los `*.php` al directorio `wp-content/mu-plugins/` del servidor (crear si no existe).
Hostinger admite subida vía File Manager o SFTP.

```
wp-content/
├── mu-plugins/            ← subir aquí
│   ├── yzz-seo-301-redirects.php
│   ├── yzz-seo-noindex-utility.php
│   ├── yzz-seo-schema-enrichment.php
│   └── yzz-seo-cta-beach-blogs.php
├── plugins/
└── themes/
```

## Qué hace cada uno

| Plugin | Tareas backlog | Acción automática |
|---|---|---|
| `yzz-seo-301-redirects.php` | #1, #6, #13 | Redirige 301 las 4 URLs 404 canibalizadoras + el typo "brookers" + slugs legacy previstos |
| `yzz-seo-noindex-utility.php` | #5, #7, #12 | Marca noindex + excluye del sitemap de Yoast las ~24 URLs utilitarias (login, perfil, pagos, gracias, dashboards) + añade Disallow al robots.txt |
| `yzz-seo-schema-enrichment.php` | #10, #18, #19 | Inyecta Organization + WebSite+SearchAction en home; LocalBusiness + FAQPage en cada página de ciudad |
| `yzz-seo-cta-beach-blogs.php` | #11 | Expone shortcode `[yzz_cta_ciudad ciudad="puerto-vallarta" playa="Madagascar"]` para monetizar blogs informacionales de playas |

## Validación post-deploy

1. **Redirects activos**:
   ```bash
   curl -I https://yatezzitos.com/es/ciudad/yates-acapulco/
   # Esperado: HTTP/2 301, Location: /es/ciudad/renta-de-yates-en-acapulco/
   ```

2. **Noindex en utilitarias**:
   ```bash
   curl -sSL https://yatezzitos.com/es/login/ | grep -i "noindex"
   # Esperado: "noindex, follow"
   ```

3. **Sitemap limpio**:
   ```bash
   curl -sSL https://yatezzitos.com/page-sitemap.xml | grep -E "login|mi-perfil|user-dashboard"
   # Esperado: vacío (0 matches)
   ```

4. **Schema FAQ presente en ciudades**:
   ```bash
   curl -sSL https://yatezzitos.com/es/ciudad/renta-de-yates-cancun/ | grep -c "FAQPage"
   # Esperado: >= 1
   ```

5. **Endpoints de auditoría internos**:
   - `GET /wp-json/yatezzitos/v1/seo-redirects-log` — últimos redirects disparados
   - `GET /wp-json/yatezzitos/v1/seo-noindex-audit` — IDs que está excluyendo del sitemap

## Rollback

Renombrar o eliminar el archivo `.php` correspondiente. Los redirects, noindex y schema
desaparecen en la siguiente request. **No hay datos en DB que limpiar** (el único state
es el log de redirects en `wp_options['yzz_seo_redirects_log']`, irrelevante).

## Notas de compatibilidad

- Probado conceptualmente contra: WP 6.9.x · Yoast SEO Premium · Houzez + HousezHijo · Elementor 4 · WP Rocket 3.29.
- El filtro `wp_robots` requiere WP ≥ 5.7 (cumple con 6.9).
- `wpseo_exclude_from_sitemap_by_post_ids` requiere Yoast ≥ 14.x.
- El endpoint `/yatezzitos/v1/*` está definido por el plugin `yatezzitos-yoast-rest-api` —
  debe estar activo para que funcionen los endpoints de auditoría y los scripts de `/scripts/seo/`.

## Dependencias entre tareas

- La tarea **#6 (fix typo brookers)** requiere que exista en WP la página destino
  `/es/brokers-de-yates-en-mexico/`. El redirect falla con 301 a una URL que devuelve 404
  si la página destino no existe. Crear la página antes de deploy, o aceptar temporalmente
  que el 301 dejará el destino roto — el Grupo B cubre esto.
- La tarea **#13 (renombrar slugs ciudad)** solo entra en efecto cuando se renombran
  los `term_slug` en WP admin. Las reglas `*-legacy` del `yzz-seo-301-redirects.php`
  esperan ese renombrado — hasta entonces son no-op.
