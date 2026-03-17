# Propuestas de Skills y Workflows para Yatezzitos Global

Tras una investigación profunda de las capacidades actuales del proyecto (WordPress, Houzez, Yoast, GoHighLevel, GitHub y GSC) y las mejores prácticas de desarrollo con agentes de Inteligencia Artificial mediante Model Context Protocol (MCP), he seleccionado **5 Skills (Workflows) de alto impacto**.

Estas opciones fueron elegidas bajo dos criterios estrictos:
1. **Seguridad:** Control absoluto de la IA (lectura/preparación prioritaria, y confirmación humana para escrituras críticas).
2. **Utilidad Inmediata:** Abordan las prioridades del backlog actual (Fase 1 y Fase 2).

---

## 1. Skill de Creación y Optimización de Yates (Houzez Fleet Manager) 🚤
**Por qué es útil:** La Fase 1 incluye "optimizar fichas de embarcaciones". Subir un yate en Houzez con todos los campos personalizados (capacidad, eslora, amenities, precios por hora) y redactarlo de forma seductora y optimizada al SEO toma mucho tiempo manualmente.
**Cómo funciona:**
- Toma datos crudos proporcionados por el propietario (ej. un PDF o un mensaje de WhatsApp).
- Estructura la información rellenando los campos correctos (precio, cuartos, capacidad).
- Genera una descripción larga en **HTML puro** aplicando las reglas **antibot/ensalada de palabras** establecidas en `AGENTS.md`.
- Vía WordPress MCP, genera un *Borrador* de la ficha del yate en la plataforma con Yoast SEO completo, listo para tu revisión.
**Seguridad:** Alta. Solo crea borradores y nunca elimina yates existentes.

## 2. Skill de Sincronización y Triaje de Leads (GHL Lead Analyzer) 📊
**Por qué es útil:** GoHighLevel es el corazón de las ventas. Necesitas que los agentes (como Marina o Capitán) no solo vendan, sino que sistematicen la data.
**Cómo funciona:**
- Un workflow donde la IA audita las oportunidades estancadas en el CRM de GHL o analiza los últimos correos/mensajes para detectar intenciones de compra (scoring).
- Redacta secuencias de correos hiper-personalizadas al contexto del lead.
- Recomienda en qué etapa del Pipeline debe ir el turista basándose en el historial de GHL.
**Seguridad:** Moderada-Alta. En su versión segura, emite recomendaciones u hojas de cálculo exportadas y no dispara correos automáticos sin revisión.

## 3. Skill de Auditoría SEO y Content Gap (GSC Discovery) 🔎
**Por qué es útil:** Para seguir escalando después de Los Cabos e Ixtapa, necesitamos saber qué busca la gente sin adivinar.
**Cómo funciona:**
- Usa el MCP de Google Search Console para analizar páginas de Yatezzitos que tienen impresiones pero no clics (CTR bajo).
- Realiza búsquedas web simulando a un usuario para escanear qué está haciendo la competencia en esos destinos.
- Te genera un reporte marcando qué palabras clave exactas debemos incluir o en qué artículos debemos mejorar la densidad natural para robarles el lugar en Google.
**Seguridad:** Muy Alta. Es una herramienta de inteligencia y lectura de datos, no modifica tu sistema.

## 4. Skill de Control de Calidad Frontend y Fidelidad Figma (Linter UI) 🎨
**Por qué es útil:** Dado que están haciendo el rediseño web de Figma a WordPress, asegurar la consistencia es complicado.
**Cómo funciona:**
- Un workflow que lee los archivos CSS/HTML en el proyecto (`redesign/`).
- Comprueba automáticamente la accesibilidad (a11y), que no haya estilos hardcodeados o "inline", y asegura que los colores/fuentes provengan del sistema de diseño (variables).
- Si hay errores, genera las correcciones exactas antes de permitir un PR.
**Seguridad:** Muy Alta. Se ejecuta solo localmente durante el desarrollo y audita tu código para que no rompas visualmente nada en staging.

## 5. Skill de Reportería y Mantenimiento Periódico (Site Health Check) 🛠️
**Por qué es útil:** Un sitio de comercio y turismo requiere estar rápido, sin enlaces rotos y con plugins al día.
**Cómo funciona:**
- Usando WordPress MCP y GitHub, analiza los logs recientes y busca respuestas 404 en el contenido.
- Rastrea tu propio sitio web (las descripciones gigantes de WP) asegurando que no haya enlaces internos muertos.
- Revisa el GSC para buscar y reportar anomalías repentinas de drops de tráfico.
**Seguridad:** Muy Alta. Funciona como un agente centinela que te mantiene informado todas las semanas.

---

> [!TIP]
> Mi sugerencia es que iniciemos creando el archivo **`houzez-fleet-manager.md` (La Skill No. 1)** en la carpeta `.agents/workflows/`, ya que encaja inmediatamente con la "Tarea 3: Optimizar fichas de embarcaciones" que tenemos pendiente en este momento en `task.md`.
