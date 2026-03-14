# Redesign — Assets del rediseño web

> **Yatezzitos Global** · Última actualización: 14 de marzo 2026

---

## Qué contiene esta carpeta

Esta carpeta contiene todos los assets necesarios para ejecutar el rediseño del sitio web de Yatezzitos, desde los diseños de Figma hasta el código fuente del tema Houzez y el CSS personalizado actual.

---

## Estructura

```
redesign/
├── css/
│   ├── CSS ADICIONAL AÑADIDO CON YELLOW PENCIL EN TODAS LAS PAGINAS/
│   │   └── CSS global insertado via Yellow Pencil (todas las páginas)
│   ├── CSS INSERTADO DESDE PERSONALIZAR/
│   │   └── CSS insertado desde Personalizar de WordPress
│   ├── CSS PERSONALIZADO EN PAGINA DE HOME A TRAVES DE YELLOW PENCIL/
│   │   └── CSS específico de Home via Yellow Pencil
│   ├── css adicional insertado desde housez thema/
│   │   └── CSS personalizado insertado desde el tema Houzez
│   ├── css/
│   │   └── Archivos CSS compilados del tema Houzez
│   └── themes/
│       └── houzez/          ← Código fuente completo del tema Houzez
├── figma/
│   ├── 1_Yatezzitos_Home Page.png
│   ├── 1_Yatezzitos_Home Page_Responsive.png
│   ├── 2_Yatezzitos_Help.png
│   ├── 2_Yatezzitos_Help_Responsive.png
│   ├── 3_Yatezzitos_Contact Us.png
│   ├── 3_Yatezzitos_Contact_US_Responsive.png
│   ├── 4_Yatezzitos_Blog.png
│   ├── 4_Yatezzitos_Blog_Responsive.png
│   ├── 5_Yatezzitos_Blog Details.png
│   ├── 5_Yatezzitos_Blog_Details_Responsive.png
│   ├── 6_Frame 2085663335.png
│   └── 6_Yatezzitos_Blog Category.png
├── tokens/                  ← Design tokens (pendiente)
└── assets/                  ← Assets adicionales (pendiente)
```

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
4. **Para desarrollo futuro** → Los design tokens estarán en `tokens/` cuando se definan

---

*Los cambios al tema o CSS en producción deben probarse en staging antes de publicarse (ver DEC-004 y DEC-005).*
