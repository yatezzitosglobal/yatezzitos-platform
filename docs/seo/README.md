# Contenido SEO — Yatezzitos.com

Este directorio contiene **todas las descripciones largas de ciudades** y **todos los artículos de blog** organizados por destino. Es la fuente de verdad para el contenido SEO del sitio.

---

## Estructura de carpetas

```
docs/seo/
├── <ciudad>/
│   ├── descripcion_larga_<ciudad>.html   ← Descripión activa (o pendiente) en WordPress
│   └── entradas/
│       └── <slug-del-post>.html          ← Contenido HTML de cada entrada de blog
```

---

## Estado de descripciones largas por ciudad

| Ciudad | Carpeta | Estado descripción | Posts |
|---|---|---|---|
| Acapulco | `acapulco/` | ⚠️ Pendiente de redacción SEO | 1 |
| Cancún | `cancun/` | ⚠️ Pendiente de redacción SEO | 17 |
| Playa del Carmen | `playa-del-carmen/` | ⚠️ Pendiente de redacción SEO | 4 |
| Huatulco | `huatulco/` | ✅ Redactada (v1 optimizada) | 10 |
| Ixtapa Zihuatanejo | `ixtapa/` | 📋 Activa en WP (no optimizada) | 16 |
| La Paz | `la-paz/` | ✅ Redactada (v1 optimizada) | 6 |
| Los Cabos | `los-cabos/` | ⚠️ Pendiente de redacción SEO | 9 |
| Mazatlán | `mazatlan/` | ⚠️ Pendiente de redacción SEO | 15 |
| Nuevo Vallarta | `nuevo-vallarta/` | ⚠️ Pendiente de redacción SEO | 7 |
| Puerto Vallarta | `puerto-vallarta/` | ⚠️ Pendiente de redacción SEO | 17 |

**Total entradas sincronizadas: 102**

---

## Convenciones

### Estado de descripciones largas
- ✅ **Redactada** — HTML optimizado listo para publicar en WordPress
- 📋 **Activa en WP** — Existe contenido en WordPress pero sin optimización SEO completa
- ⚠️ **Pendiente** — Placeholder, hay que redactar la descripción

### Estado de entradas de blog
Cada archivo `.html` incluye en su encabezado:
- `Estado` → `✅ Publicado`, `📝 Borrador`, `🔒 Privado`, `⏳ Pendiente`
- `URL` → URL pública del post en yatezzitos.com
- `Slug` → Slug del post

---

## Workflow para agregar/actualizar contenido

1. **Nueva ciudad:** Crear carpeta `docs/seo/<ciudad>/` con su `descripcion_larga_<ciudad>.html`
2. **Nuevo post:** Al publicar en WordPress, sincronizar ejecutando el script `scripts/sync_posts.py` (próximamente)
3. **Actualizar descripción:** Editar el archivo `.html` de la ciudad → abrir PR → aprobar → pegar en WordPress pestaña HTML

---

## Importante: cómo pegar HTML en WordPress

Las descripciones largas de ciudad se pegan en:
**WordPress → Posts → Categorías → [Ciudad] → Descripción**

⚠️ Siempre cambiar a la **pestaña "Texto" o "HTML"** del editor antes de pegar. Si se pega en modo Visual, WordPress puede eliminar etiquetas HTML.

Para que WordPress conserve el HTML al guardar, el plugin `yzz-readmore-drive-download.php` incluye los filtros necesarios:
```php
remove_filter('pre_term_description', 'wp_filter_kses');
add_filter('pre_term_description', 'wp_filter_post_kses');
```

---

*Sincronizado automáticamente el 2026-03-17. Total: 102 entradas en 10 ciudades.*
