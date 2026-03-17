# Skills de Inteligencia SEO (MCP) — Enfoque en GSC (Skill #3)

Continuando con la propuesta original de la Skill #3 ("Auditoría SEO y Content Gap"), he diseñado las **dos mejores Skills estratégicas** utilizando la integración directa con el servidor MCP de Google Search Console (GSC).

A diferencia de las tareas creativas (como escribir el blog de Ixtapa), estas habilidades son puramente **analíticas**. Dotarán a la Inteligencia Artificial de la capacidad de tomar decisiones basadas en datos duros de Google en lugar de solo suposiciones orgánicas.

---

## 1. El Cazador de Oportunidades (GSC Low-Hanging Fruit Finder) 🎯
**Por qué es útil:** A menudo, páginas de Yatezzitos pueden estar rankeando en la página 2 de Google para búsquedas jugosas (como "yates baratos en puerto vallarta"). Estas palabras clave tienen miles de impresiones, pero pocos o ningún clic (CTR bajo).
**Cómo funciona:**
- Periódicamente, o a petición tuya, la IA ejecuta el MCP de `gscServer_get_advanced_search_analytics`.
- Filtra las URLs de Yatezzitos que tienen **alta cantidad de impresiones** y **posición media entre 11 y 30** (es decir, viven en la página 2 o 3 de Google).
- Genera un reporte identificando exactamente qué *Queries* (lo que la gente teclea) están provocando esas impresiones, y te sugiere si deberíamos modificar el Título, agregar un H2 con esa query, o ponerla en la Meta Descripción usando la conexión con WordPress.
**Seguridad:** **Máxima.** Es una auditoría en modo de "solo lectura" de GSC. Identifica la "fruta madura" (ganancias rápidas de tráfico) y te devuelve un documento planificando qué ajustes de texto exactos se necesitan en el sitio para subir a la página 1.

## 2. El Inspector de Tráfico Cruzado (Cannibalization Detector) 🚨
**Por qué es útil:** Al escalar la creación de contenidos (como los múltiples artículos que creamos sobre las playas de Ixtapa o destinos similares), corres el grave riesgo de la "canibalización SEO": que dos URLs tuyas peleen entre sí por la misma palabra clave ante los ojos de Google, haciendo que ambas bajen de posición.
**Cómo funciona:**
- Empleando la herramienta MCP `gscServer_compare_search_periods` o haciendo reportes dimensionales avanzados, la IA comprueba si diferentes hojas/entradas de tu WordPress están recibiendo clics e impresiones por la misma *Query* exacta (ej: compitiendo por "renta yate cancun").
- Si las encuentra, abre un ticket en tu Gestor de Tareas (o te alerta directamente).
- Genera recomendaciones de mitigación SEO comprobadas: sugiere definir cuál es la URL "Pilar", propone inyectar un enlace interno desde la página débil hacia la página Pilar, o incluso aconseja configurar etiquetas `rel="canonical"` si son muy repetitivas.
**Seguridad:** **Altísima.** De nuevo, funciona como un estratega técnico. Procesa grandes dimensiones de datos de Google que a un humano le tomaría horas organizar en hojas de cálculo de Excel, y entrega planes de acción masticados listos para implementarse.

---

> [!TIP]
> Estas dos implementaciones convertirán a nuestra integración con GSC en tu Analista SEO Senior. En lugar de entrar a ciegas a modificar páginas, usaremos la **Skill 1** para conseguir resultados rápidos empujando páginas con potencial, y la **Skill 2** para proteger los esfuerzos actuales evitando que tu propio contenido compita entre sí.
