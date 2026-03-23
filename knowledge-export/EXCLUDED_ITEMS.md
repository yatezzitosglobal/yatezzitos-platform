# Elementos Excluidos del Export a NotebookLM

> Fecha de generación: 2026-03-22
> Lote: BATCH-001

---

## Exclusiones por Política de Seguridad

| Ruta / Patrón | Razón |
|---|---|
| `**/node_modules/` | Dependencias de terceros. Sin valor documental. |
| `**/vendor/` | Dependencias PHP. Sin valor documental. |
| `**/.git/` | Control de versiones interno. |
| `**/dist/`, `**/build/`, `**/.next/` | Artefactos generados. |
| `**/__pycache__/` | Cache de Python. |
| `**/venv/` | Entorno virtual de Python. |
| `**/*.pyc` | Bytecode compilado de Python. |
| `**/*.lock` | Lockfiles de dependencias. |
| Credenciales en `mcp_config.json` | Contiene tokens de GitHub, Figma y configuración. **Excluido por seguridad.** |

---

## Exclusiones por Bajo Valor Documental

| Ruta / Patrón | Razón |
|---|---|
| `redesign/css/css/` (~90 archivos) | CSS core de WordPress admin (about.css, admin-menu.css, colors/, etc.). Es código del CMS, no del proyecto. |
| `redesign/css/themes/houzez/` (~300+ archivos PHP) | Tema Houzez completo. Es un producto de terceros; su función general se documenta en WEBSITE_STACK pero no se sube file-by-file. |
| `redesign/assets/` (imágenes) | Assets gráficos binarios. Sin valor textual para NotebookLM. |
| `redesign/figma/` (imágenes de diseño) | Capturas de Figma. Se referencian en docs pero los binarios no aportan a NLM. |
| `redesign/paginas-redisenadas/` | Capturas de páginas rediseñadas. Mismo caso. |
| `scripts/yatezzitos_crawler/venv/` | Entorno virtual del crawler. |
| `scripts/yatezzitos_crawler/logs/` | Logs de ejecución del crawler. |
| `scripts/yatezzitos_crawler/core/__pycache__/` | Cache de Python. |
| `scripts/yatezzitos_crawler/config/__pycache__/` | Cache de Python. |

---

## Exclusiones por Duplicidad o Redundancia

| Ruta / Patrón | Razón |
|---|---|
| `redesign/css/04-blog.scss` | Versión SCSS del blog. El CSS compilado ya está. |
| Archivos `*.min.css` en `redesign/css/css/` | Versiones minificadas de CSS admin. Ya excluidos por bajo valor. |
| `redesign/css/CSS INSERTADO DESDE PERSONALIZAR/` | Contenido ya consolidado en los snippets de WordPress. |

---

## Nota de Auditoría

Todos los elementos excluidos fueron evaluados individualmente. Ningún archivo con información de negocio, flujos operativos, automatizaciones, configuraciones críticas o documentación relevante fue omitido.

Los contenidos SEO por ciudad (HTML) no se excluyen sino que se **resumen** en fuentes consolidadas por destino para evitar sobrecarga del notebook sin perder el conocimiento.
