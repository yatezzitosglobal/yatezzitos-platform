---
description: Orquestador maestro de Skills. Lee esto cuando el usuario haga una petición general para saber a qué Skill exacta (workflow) enrutar y qué reglas de negocio obedecer.
---

# Orquestador de Skills IA (Router)

Cuando el usuario (ej. Luis Velázquez) ingrese un prompt en lenguaje natural solicitando trabajo general y no proporcione todos los detalles del formato, **debes seguir este flujo obligatoriamente** para identificar qué **Skill** (Workflow) debes leer y ejecutar a continuación.

## 1. Mapa de Enrutamiento (Intención -> Skill)

Analiza el "prompt" del usuario y compáralo con este mapa. Si encaja con alguna de estas áreas, **tu primera acción técnica OBLIGATORIA debe ser hacer un `view_file` sobre la ruta del archivo correspondiente**.

- ✍️ **Si pide redactar, optimizar o estructurar un Blog Post / Descripción Larga:**
  - 👉 **Skill destino:** `.agents/workflows/seo-blog-posts.md`
  - *Comportamiento esperado:* Entrar en modo redacción SEO natural sin "ensaladas de palabras". Formato HTML puro.

- 🛥️ **Si pide subir, actualizar o redactar la ficha de un Yate / Embarcación en Houzez:**
  - 👉 **Skill destino:** `docs/ai/houzez_fleet_skills.md` (Astillero Redactor)
  - *Comportamiento esperado:* Procesar datos crudos, armar formato HTML impecable y cargar en estado de "Borrador" usando WordPress MCP.

- 💬 **Si pide revisar oportunidades, enviar correos a leads fríos o puntuar clientes en GHL:**
  - 👉 **Skill destino:** `docs/ai/ghl_crm_skills.md` (Paramédico / Rescatista)
  - *Comportamiento esperado:* Nunca disparar acciones destructivas; priorizar crear borradores de emails o mover/etiquetar pipelines en GoHighLevel de manera inteligente.

- 📈 **Si pide auditar canibalización, ranking en página 2 o verificar CTR:**
  - 👉 **Skill destino:** `docs/ai/gsc_seo_skills.md` (Cazador / Inspector cruzado)
  - *Comportamiento esperado:* Leer el entorno analítico de Google Search Console (GSC) sin modificar producción. Identificar las piezas fáciles de subir a primera página modificando títulos/metas.

- 🎨 **Si pide transformar un Figma a código, ajustar variables CSS o auditar UI:**
  - 👉 **Skill destino:** `docs/ai/figma_frontend_skills.md` (Sincronizador / Linter)
  - *Comportamiento esperado:* Cumplir meticulosamente con los tokens del sistema de diseño (redesign/). Prohibido hacer estilos CSS "inline". Respeto total al Figma original.

- 🛠️ **Si pide solucionar un bug de GitHub, arreglar enlaces 404/rotos o revisar el código de otro:**
  - 👉 **Skill destino:** `docs/ai/github_maintenance_skills.md` (El Mecánico Auto-fixer)
  - *Comportamiento esperado:* Para arreglos masivos o reportes de issues, trabajar en una rama aislada (`fix/...`) y siempre, siempre enviar el arreglo mediante un Pull Request limpio vía MCP, nunca a main.

## 2. Contrato de Ejecución Forzada
1. **Identifica y Avisa:** Si ubicas la intención en el listado superior, respóndele al usuario con una confirmación clara, ejemplo: *"Entendido, el objetivo es subir un yate. Activando y enrutando a la Skill de Houzez Fleet Manager..."*
2. **Auto-Llamada al Contexto:** No intentes adivinar cómo la plataforma (Yatezzitos) estructura sus páginas o correos. Haz el `view_file` de la Skill Destino al 100% antes de generar tu primera línea de código o texto.
3. **Pide Clarificación:** Si la solicitud es demasiado vaga y pisa dos o más Skills (ej. "Mejora las ventas"), detén todo y pregunta: *"¿Prefieres que enfoque esto hacia la recuperación de correos en CRM (Skill 2) o a buscar oportunidades de tráfico en Google (Skill 3)?"*

## 3. Seguridad y Límites
Nunca asumas permisos destructivos. Si una instrucción del usuario implica borrar datos o enviar correos masivos sin revisión, debes pausar el flujo y pedir confirmación humana explícita citando este Orquestador.
