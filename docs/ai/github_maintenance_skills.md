# Skills de GitHub (MCP) — Enfoque en Mantenimiento y Salud (Skill #5)

Dado que la Skill #5 propuesta anteriormente era el "Site Health Check" (mantenimiento continuo y reporte de errores), y tomando en cuenta la máxima prioridad de **seguridad y utilidad**, aquí tienes las **dos mejores Skills** que podemos implementar utilizando el servidor MCP oficial de GitHub.

Estas Skills automatizan la vigilancia del código, evitan errores en producción (como que se publiquen post con "ensaladas de palabras") y mantienen tu Backlog limpio sin que tú interactúes más allá de un clic.

---

## 1. El Centinela del Repositorio (PR Reviewer y Auditor SEO) 🛡️
**Por qué es útil:** Cuando tu equipo, un redactor externo, o incluso otro Agente IA crea un nuevo artículo o hace cambios masivos, Luis Velázquez tiene que perder tiempo leyendo cada línea para asegurarse de que no haya enlaces rotos, o que no se viole la regla de redacción natural.
**Cómo funciona:**
- Este workflow se activa automáticamente cada que hay un nuevo *Pull Request* (PR).
- La IA usa la herramienta MCP `pull_request_read` para extraer las diferencias exactas del código (diff).
- Comprueba línea por línea que todo el nuevo código HTML cumpla con las reglas de `seo-blog-posts.md` (formato, enlaces salientes, no keyword stuffing).
- Si encuentra un error, usa MCP `add_comment_to_pending_review` para dejarte un comentario exacto en la línea de código afectada (Ej: *"Línea 45: Este enlace a Wikipedia está roto, sugiero cambiarlo a X"*).
- Al final, bloquea o aprueba el PR temporalmente usando `pull_request_review_write`.
**Seguridad:** **Máxima.** La IA actúa como un inspector de control de calidad. Nunca edita la rama principal (main), sino que audita el trabajo de otros (o el suyo propio en ramas secundarias) y solo te dejas notas valiosas.

## 2. El Mecánico Automático (Auto-Fixer e Issue Triager) 🔧
**Por qué es útil:** Constantemente surgen pequeños problemas técnicos, faltas de ortografía reportadas, o *warnings* en Google Search Console sobre 404s que se acumulan en tu muro de tareas. Resolverlos uno por uno roba foco a las tareas de Fase 1.
**Cómo funciona:**
- La IA escanea periódicamente el repositorio usando el MCP `search_issues` buscando etiquetas como "bug rápido" o "arreglar link".
- Si es una tarea de baja complejidad (ej. "Quitar la sección de contactos antigua del HTML"), la IA:
  1. Usa `create_branch` para crear una rama aislada (Ej: `fix/actualizar-contactos`).
  2. Descarga el archivo, hace el cambio y usa `create_or_update_file` para guardar.
  3. Ejecuta `create_pull_request` enviándote el código ya corregido con un título descriptivo.
  4. Incluso puede asignarle la revisión a un colaborador del equipo con otras herramientas nativas.
- Todo esto ocurre en segundo plano de manera autónoma.
**Seguridad:** **Alta.** Aunque es un agente que "escribe" código de forma autónoma, nunca empuja cambios a producción ni a `main`. Lo enjaulamos para que todo su trabajo termine en un *Pull Request*. Luis sigue siendo quien da el clic final verde (`Merge`).

---

> [!TIP]
> Ambas Skills explotan a la perfección el potencial del MCP de GitHub sin arriesgar tu rama principal. La Skill 1 (El Centinela) sería un complemento perfecto si decides subir a más redactores, mientras que la Skill 2 (Mecánico) solucionaría el dolor de cabeza de tener una lista infinita de pequeñas tareas pendientes.
