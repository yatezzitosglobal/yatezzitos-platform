# Documentación del proyecto — Yatezzitos Global

> Uso interno · Última actualización: marzo 2026

Este índice describe el contenido de cada carpeta dentro de `docs/`.
Antes de crear un documento nuevo, revisa si ya existe uno relacionado aquí.

---

## Estructura

### [architecture/](architecture/)
Decisiones técnicas, stack actual y futuro, módulos de producto y diagramas de integración.

| Documento | Contenido |
|---|---|
| [index.md](architecture/index.md) | Índice de arquitectura |
| [current-stack.md](architecture/current-stack.md) | Stack tecnológico actual (WP, GHL, Twilio, etc.) |
| [future-stack.md](architecture/future-stack.md) | Arquitectura futura planificada |
| [integrations.md](architecture/integrations.md) | Mapa de integraciones entre sistemas |
| [decisions-log.md](architecture/decisions-log.md) | Registro de decisiones técnicas importantes |
| [web-app.md](architecture/web-app.md) | Diseño de la futura web app |
| [marketplace.md](architecture/marketplace.md) | Lógica del marketplace de yates |
| [calendario-disponibilidad.md](architecture/calendario-disponibilidad.md) | Módulo de disponibilidad en tiempo real |
| [cliente.md](architecture/cliente.md) | Cuenta y panel del cliente |
| [propietarios.md](architecture/propietarios.md) | Panel de propietarios y socios comerciales |
| [panel-interno.md](architecture/panel-interno.md) | Panel interno del equipo Yatezzitos |

---

### [brand/](brand/)
Identidad de marca, valores, tono de comunicación y principios visuales.

| Documento | Contenido |
|---|---|
| [core.md](brand/core.md) | Núcleo de marca: misión, visión, propuesta de valor y tono |

---

### [business/](business/)
Visión del negocio, modelo comercial y estrategia de crecimiento.

| Documento | Contenido |
|---|---|
| [vision.md](business/vision.md) | Visión estratégica, modelos de negocio actuales y futuros |

---

### [crm/](crm/)
Estado actual y futuro del CRM (GoHighLevel), pipelines y onboarding de propietarios.

| Documento | Contenido |
|---|---|
| [index.md](crm/index.md) | Índice del CRM |
| [current-state.md](crm/current-state.md) | Estado actual de GHL: pipelines, etapas, campos |
| [future-state.md](crm/future-state.md) | Evolución deseada del CRM |
| [onboarding-propietarios.md](crm/onboarding-propietarios.md) | Flujo de alta de propietarios y embarcaciones |

---

### [seo/](seo/)
Estrategia SEO, auditorías, frameworks de keywords y reglas de producción de contenido.

| Documento | Contenido |
|---|---|
| [master-plan.md](seo/master-plan.md) | Plan maestro SEO del proyecto |
| [analisis-seo-marzo-2026.md](seo/analisis-seo-marzo-2026.md) | Análisis SEO completo — marzo 2026 |
| [auditoria-seo-completa.md](seo/auditoria-seo-completa.md) | Auditoría técnica y de contenido |
| [keyword-assignment-framework.md](seo/keyword-assignment-framework.md) | Framework de asignación de keywords por ciudad y yate |
| [oportunidades-keywords-2026.md](seo/oportunidades-keywords-2026.md) | Oportunidades de keywords identificadas para 2026 |
| [content-production-rules.md](seo/content-production-rules.md) | Reglas de producción de contenido SEO |
| [guia-search-console-2026.md](seo/guia-search-console-2026.md) | Guía de uso de Search Console para el equipo |

---

### [scrum/](scrum/)
Backlog del proyecto, planes de ejecución por issue y estado de avance.

| Documento | Contenido |
|---|---|
| [backlog.md](scrum/backlog.md) | Backlog maestro con todos los niveles de prioridad |
| [plan-issue-3-seo.md](scrum/plan-issue-3-seo.md) | Plan de ejecución — Issue #3: SEO |
| [plan-issue-4-crm.md](scrum/plan-issue-4-crm.md) | Plan de ejecución — Issue #4: CRM |
| [plan-issue-5-automatizaciones.md](scrum/plan-issue-5-automatizaciones.md) | Plan de ejecución — Issue #5: Automatizaciones |
| [plan-issue-6-feedback.md](scrum/plan-issue-6-feedback.md) | Plan de ejecución — Issue #6: Etapa Feedback |
| [plan-issue-7-captacion.md](scrum/plan-issue-7-captacion.md) | Plan de ejecución — Issue #7: Captación de propietarios |

---

### [tools/](tools/)
Guías de configuración e integración de herramientas externas.

| Documento | Contenido |
|---|---|
| [guia-wordpress-mcp.md](tools/guia-wordpress-mcp.md) | Setup del MCP server de WordPress (opciones wp-mcp-server y wordpress-mcp NPX) |

---

## Convenciones

- Cada documento debe tener un título `#` claro al inicio.
- Si un documento está en progreso o incompleto, indica su estado al inicio: `> Estado: WIP`.
- No incluir credenciales, tokens ni datos sensibles de clientes en ningún documento.
- Actualizar este índice cuando se agregue o elimine un documento.
