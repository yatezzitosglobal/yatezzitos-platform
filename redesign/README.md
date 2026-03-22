# Redesign — Assets y código del rediseño web

> **Yatezzitos Global** · Última actualización: 14 de marzo 2026

---

## Qué contiene esta carpeta

Esta carpeta contiene todos los assets necesarios para ejecutar el rediseño del sitio web de Yatezzitos: diseños de Figma, CSS personalizado actual, y **código fuente del rediseño** listo para implementar en WordPress via Elementor.

---

## Estructura

```
redesign/
├── 02-contact-us.md       ← Código HTML/CSS/JS de Contact Us (✅ completado)
├── 03-help.md             ← Código HTML/CSS/JS de Help/FAQ (✅ completado)
├── README.md
├── css/
│   ├── CSS ADICIONAL AÑADIDO CON YELLOW PENCIL EN TODAS LAS PAGINAS/
│   ├── CSS INSERTADO DESDE PERSONALIZAR/
│   ├── CSS PERSONALIZADO EN PAGINA DE HOME A TRAVES DE YELLOW PENCIL/
│   ├── css adicional insertado desde housez thema/
│   ├── css/               ← Archivos CSS compilados del tema Houzez
│   └── themes/
│       └── houzez/        ← Código fuente completo del tema Houzez
├── figma/                 ← 12 diseños PNG (desktop + responsive)
├── paginas-redisenadas/   ← Páginas rediseñadas
└── assets/                ← Assets adicionales (pendiente)
```

---

## Código de Rediseño (Páginas Completadas)

Estos archivos `.md` contienen el HTML, CSS y JavaScript listo para copiar y pegar en bloques de código HTML de Elementor.

### Páginas completadas

| # | Página | Archivo | Estado | Secciones |
|---|--------|---------|--------|-----------|
| 2 | **Contact Us** | `02-contact-us.md` | ✅ Completada | Hero, formulario, mapa, info de contacto |
| 3 | **Help / FAQ** | `03-help.md` | ✅ Completada | Hero+buscador+pills, FAQ accordion+CTA equipo con estrella de mar, bloques informativos |

### Páginas pendientes

| # | Página | Archivo | Diseño Figma |
|---|--------|---------|-------------|
| 1 | **Home** | `01-home.md` (pendiente) | ✅ Disponible |
| 4 | **Blog** | `04-blog.md` (pendiente) | ✅ Disponible |
| 5 | **Blog Details** | `05-blog-details.md` (pendiente) | ✅ Disponible |
| 6 | **Blog Category** | `06-blog-category.md` (pendiente) | ✅ Disponible |

### Cómo implementar en WordPress

1. Abrir la página en Elementor
2. Agregar un bloque de **HTML personalizado**
3. Copiar la sección correspondiente del archivo `.md` (cada sección está separada con comentarios `<!-- SECCIÓN X -->`)
4. Guardar y previsualizar
5. Cada archivo incluye CSS inline (dentro de `<style>`) y JavaScript (dentro de `<script>`) — no requiere archivos externos

### Convenciones del código

- **Prefijo de clases CSS:** `yz-` para evitar conflictos con Houzez/Elementor
- **Variables CSS:** Definidas en `:root` con prefijo `--yz-`
- **Responsive:** Mobile-first con breakpoints en `768px` (mobile), `1025px` (desktop)
- **Imágenes:** Todas hosteadas en `yatezzitos.com/wp-content/uploads/`

---

## Diseños de Figma disponibles

Los diseños incluyen versiones **desktop y responsive (mobile)** de las siguientes páginas:

| # | Página | Desktop | Responsive |
|---|--------|---------|------------|
| 1 | **Home Page** | ✅ | ✅ |
| 2 | **Help (Ayuda)** | ✅ | ✅ |
| 3 | **Contact Us** | ✅ | ✅ |
| 4 | **Blog** | ✅ | ✅ |
| 5 | **Blog Details** | ✅ | ✅ |
| 6 | **Blog Category** | ✅ | — |

---

## CSS personalizado documentado

Actualmente existen **4 fuentes de CSS personalizado** en el sitio:

| Fuente | Alcance | Ubicación |
|--------|---------|-----------|
| Yellow Pencil (global) | Todas las páginas | `css/CSS ADICIONAL AÑADIDO CON YELLOW PENCIL...` |
| Yellow Pencil (Home) | Solo página Home | `css/CSS PERSONALIZADO EN PAGINA DE HOME...` |
| Personalizar WordPress | Global | `css/CSS INSERTADO DESDE PERSONALIZAR/` |
| Tema Houzez | Global | `css/css adicional insertado desde housez thema/` |

---

## Tema Houzez

El código fuente completo del tema Houzez se encuentra en `css/themes/houzez/`. Este es el tema base de WordPress sobre el que opera el sitio actual.

> **Nota:** El archivo `houzez.zip` no está versionado en el repositorio (ignorado por `.gitignore`), pero el tema completo está disponible descomprimido.

---

## Relación con el backlog

| Tarea del backlog | Relación |
|-------------------|----------|
| #2 — Terminar rediseño web de Figma a WordPress | Los diseños de Figma en esta carpeta son la fuente de verdad para el rediseño |
| DEC-039 — El rediseño de Figma a WordPress es prioridad inmediata | Esta carpeta contiene los assets necesarios para ejecutar esta decisión |
| DEC-037 / DEC-038 — Houzez se mantiene durante la transición | El tema Houzez versionado aquí sirve como referencia |

---

## Cómo usar estos assets

1. **Para implementar el rediseño** → Usa los PNGs de `figma/` como referencia visual
2. **Para entender el CSS actual** → Revisa las carpetas de CSS personalizado
3. **Para modificar el tema** → El código fuente de Houzez está en `css/themes/houzez/`
4. **Para desarrollo futuro** → Las páginas rediseñadas estarán en `paginas-redisenadas/`

---

*Los cambios al tema o CSS en producción deben probarse en staging antes de publicarse (ver DEC-004 y DEC-005).*
