# Arquitectura — Índice de documentos

Este directorio contiene la documentación técnica y de arquitectura de **Yatezzitos Global**.

---

## Documentos de arquitectura

| Documento | Descripción | Issue | Estado |
|---|---|---|---|
| [current-stack.md](current-stack.md) | Stack tecnológico actual (WordPress, GHL, Twilio) | — | ✅ Completo |
| [future-stack.md](future-stack.md) | Arquitectura futura (web app, paneles, IA) | — | ✅ Completo |
| [decisions-log.md](decisions-log.md) | Registro de decisiones (50+ decisiones) | — | ✅ Completo |
| [integrations.md](integrations.md) | Mapa de integraciones entre sistemas | [#10](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/10) | ✅ Completo |
| [web-app.md](web-app.md) | Arquitectura técnica de la futura web app | [#15](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/15) | ✅ Completo |

## Diseños funcionales de módulos

| Documento | Descripción | Issue | Estado |
|---|---|---|---|
| [calendario-disponibilidad.md](calendario-disponibilidad.md) | Calendario de disponibilidad de embarcaciones | [#9](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/9) | ✅ Completo |
| [marketplace.md](marketplace.md) | Marketplace de yates: búsqueda, filtros, fichas | [#11](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/11) | ✅ Completo |
| [cliente.md](cliente.md) | Cuenta del cliente: cotizaciones, reservas, pagos | [#12](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/12) | ✅ Completo |
| [propietarios.md](propietarios.md) | Panel de propietarios y socios comerciales | [#13](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/13) | ✅ Completo |
| [panel-interno.md](panel-interno.md) | Panel interno del equipo Yatezzitos | [#14](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/14) | ✅ Completo |

---

## Documentos de otras áreas

### CRM
| Documento | Descripción | Estado |
|---|---|---|
| [docs/crm/index.md](../crm/index.md) | Índice de documentación CRM | ✅ |
| [docs/crm/current-state.md](../crm/current-state.md) | Estado actual del CRM | ✅ |
| [docs/crm/future-state.md](../crm/future-state.md) | Visión futura del CRM | ✅ |
| [docs/crm/onboarding-propietarios.md](../crm/onboarding-propietarios.md) | Flujo de onboarding con campos reales de WordPress | ✅ |

### SEO
| Documento | Descripción | Estado |
|---|---|---|
| [docs/seo/master-plan.md](../seo/master-plan.md) | Plan maestro de SEO | ✅ |
| [docs/seo/keyword-assignment-framework.md](../seo/keyword-assignment-framework.md) | Framework de asignación de keywords | ✅ |
| [docs/seo/content-production-rules.md](../seo/content-production-rules.md) | Reglas de producción de contenido SEO | ✅ |

### Scrum / Planes de implementación
| Documento | Descripción | Issue |
|---|---|---|
| [docs/scrum/backlog.md](../scrum/backlog.md) | Backlog completo del proyecto (25 items) | — |
| [docs/scrum/plan-issue-3-seo.md](../scrum/plan-issue-3-seo.md) | Plan de implementación SEO por ciudad | [#3](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/3) |
| [docs/scrum/plan-issue-4-crm.md](../scrum/plan-issue-4-crm.md) | Plan de ordenar CRM en GHL | [#4](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/4) |
| [docs/scrum/plan-issue-5-automatizaciones.md](../scrum/plan-issue-5-automatizaciones.md) | Plan de automatizaciones por etapa | [#5](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/5) |
| [docs/scrum/plan-issue-6-feedback.md](../scrum/plan-issue-6-feedback.md) | Plan de etapa Feedback | [#6](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/6) |
| [docs/scrum/plan-issue-7-captacion.md](../scrum/plan-issue-7-captacion.md) | Plan de captación de propietarios | [#7](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/7) |

### Agentes IA
| Documento | Descripción | Estado |
|---|---|---|
| [ai/assistants/README.md](../../ai/assistants/README.md) | Índice y arquitectura de agentes IA | ✅ |
| [ai/assistants/orchestrator.md](../../ai/assistants/orchestrator.md) | Spec del orquestador | ✅ |
| [ai/assistants/turista.md](../../ai/assistants/turista.md) | Spec de Marina (turista) | ✅ |
| [ai/assistants/propietario.md](../../ai/assistants/propietario.md) | Spec de Timón (propietario) | ✅ |
| [ai/assistants/soporte-interno.md](../../ai/assistants/soporte-interno.md) | Spec de soporte interno | ✅ |

---

## Cómo usar esta documentación

1. **Si necesitas entender qué tenemos hoy** → lee `current-stack.md`
2. **Si necesitas entender hacia dónde vamos** → lee `future-stack.md` y `web-app.md`
3. **Si necesitas saber por qué se tomó una decisión** → busca en `decisions-log.md`
4. **Si necesitas entender cómo se conectan los sistemas** → lee `integrations.md`
5. **Si necesitas entender un módulo específico** → lee el diseño funcional correspondiente
6. **Si necesitas implementar algo** → busca el plan en `docs/scrum/`
7. **Si estás construyendo algo nuevo** → revisa `web-app.md` para la arquitectura

---

## Principios de arquitectura

- **No romper lo que funciona** — Siempre proteger ventas, SEO y operación actual
- **Integrar antes de reemplazar** — La evolución es progresiva, no destructiva
- **Documentar antes de construir** — Todo módulo nuevo debe tener su spec antes de desarrollarse
- **Modular e integrable** — La arquitectura futura debe permitir APIs y webhooks

---

*Última actualización: 13 de marzo 2026*
