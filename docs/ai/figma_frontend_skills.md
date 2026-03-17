# Skills Frontend y Figma (MCP) — Enfoque en Calidad UI (Skill #4)

Partiendo de la propuesta original de la Skill #4 ("Control de Calidad Frontend y Fidelidad Figma"), he diseñado las **dos mejores implementaciones** aprovechando la conexión entre servidores MCP y herramientas de diseño.

Dado que están en pleno proceso de rediseño (migrando vistas desde Figma hacia WordPress/Elementor o CSS puro), el mayor reto es que los desarrolladores y la IA respeten milimétricamente los márgenes, fuentes y colores.

---

## 1. Sincronizador del Sistema de Diseño (Design Token Bridge) 🎨
**Por qué es útil:** Cuando un diseñador actualiza el tono del "azul principal" o cambia la tipografía en Figma, el equipo de desarrollo suele tardar días o semanas en darse cuenta y aplicarlo en todos los archivos CSS de Yatezzitos.
**Cómo funciona:**
- La IA usa el MCP de Figma para leer directamente el archivo maestro del "Design System" de Yatezzitos.
- Extrae todos los "Tokens de Diseño" (variables de color HEX, tamaños de fuente rem, márgenes, bordes redondos).
- Compara esos valores directamente con tu archivo `variables.css` (o el global de tu tema en WordPress).
- Si detecta que Figma fue actualizado pero el código no, la IA crea un *Pull Request* automático modificando el archivo CSS para que coincida exactamente con Figma.
**Seguridad:** **Máxima.** La IA nunca sobrescribe el diseño en vivo. Simplemente traduce las decisiones del diseñador (Figma) a código (CSS) y te lo deja listo para aprobar con un clic.

## 2. El Inspector de Píxeles (UI Fidelity Linter) 📏
**Por qué es útil:** Al construir nuevos bloques HTML (como las descripciones largas de destino que hemos hecho), es fácil cometer el error de "quemar" estilos directamente en la etiqueta (ej. `<p style="color: #333">`) en lugar de usar las clases oficiales o violar reglas de accesibilidad (a11y) como olvidar el `alt` de una imagen.
**Cómo funciona:**
- Cada vez que alguien de tu equipo (o yo mismo) sube código nuevo a la carpeta de rediseño (`redesign/`), este workflow se activa.
- La IA cruza la información visual de Figma (vía MCP) con la estructura del DOM (HTML) propuesto.
- Escanea que **no existan colores hexadecimales sueltos** (todo debe ser llamado como clase o variable CSS) y verifica la semántica correcta del HTML5 (que haya solo un H1 por página, que los contrastes de color sean legibles para personas con discapacidad visual).
- En lugar de detener el trabajo o reescribir cosas sin permiso, deja un reporte como un "comentario en el código" o en tu issue tracker indicando exactamente en qué línea falló la fidelidad al diseño.
**Seguridad:** **Alta.** La IA desempeña un rol de *Quality Assurance (QA)* pasivo. Reduce las inconsistencias en el frontend drásticamente sin tener permisos de escritura destructiva en producción.

---

> [!TIP]
> Estas dos opciones crean el puente perfecto entre el equipo de Diseño y el de Desarrollo. La **Skill 1 (El Sincronizador)** te asegura que las variables globales siempre estén al día, mientras que la **Skill 2 (El Inspector)** asegura que, en el día a día, ningún programador introduzca código "sucio" que rompa la armonía del rediseño en Yatezzitos.
