# Arquitectura — Índice de documentos

Este directorio contiene la documentación técnica y de arquitectura de **Yatezzitos Global**.

---

## Documentos existentes

| Documento | Descripción | Estado |
|---|---|---|
| [current-stack.md](current-stack.md) | Stack tecnológico actual (WordPress, GHL, Twilio, etc.) | ✅ Completo |
| [future-stack.md](future-stack.md) | Arquitectura futura (web app, paneles, IA) | ✅ Completo |
| [decisions-log.md](decisions-log.md) | Registro de decisiones técnicas y estratégicas (50+ decisiones) | ✅ Completo |
| [integrations.md](integrations.md) | Mapa de integraciones entre sistemas | ✅ Completo |

---

## Documentos de otras áreas

### CRM
| Documento | Descripción | Estado |
|---|---|---|
| [docs/crm/current-state.md](../crm/current-state.md) | Estado actual del CRM y campos | ✅ Completo |
| [docs/crm/future-state.md](../crm/future-state.md) | Visión futura del CRM | ✅ Completo |
| [docs/crm/onboarding-propietarios.md](../crm/onboarding-propietarios.md) | Flujo de onboarding de propietarios con campos reales de WordPress | ✅ Completo |

### SEO
| Documento | Descripción | Estado |
|---|---|---|
| [docs/seo/master-plan.md](../seo/master-plan.md) | Plan maestro de SEO | ✅ Completo |
| [docs/seo/keyword-assignment-framework.md](../seo/keyword-assignment-framework.md) | Framework de asignación de keywords | ✅ Completo |
| [docs/seo/content-production-rules.md](../seo/content-production-rules.md) | Reglas de producción de contenido SEO | ✅ Completo |

---

## Documentos planificados

| Documento | Descripción | Issue relacionado |
|---|---|---|
| `marketplace.md` | Diseño funcional del marketplace de yates | [#11](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/11) |
| `cliente.md` | Diseño del portal / cuenta del cliente | [#12](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/12) |
| `propietarios.md` | Diseño del panel de propietarios | [#13](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/13) |
| `panel-interno.md` | Diseño del panel interno del equipo | [#14](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/14) |
| `web-app.md` | Arquitectura de la futura web app | [#15](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/15) |

---

## Cómo usar esta documentación

1. **Si necesitas entender qué tenemos hoy** → lee `current-stack.md`
2. **Si necesitas entender hacia dónde vamos** → lee `future-stack.md`
3. **Si necesitas saber por qué se tomó una decisión** → busca en `decisions-log.md`
4. **Si necesitas entender cómo se conectan los sistemas** → lee `integrations.md`
5. **Si necesitas entender el flujo de propietarios** → lee `docs/crm/onboarding-propietarios.md`
6. **Si estás construyendo algo nuevo** → revisa los documentos planificados y sus issues

---

## Principios de arquitectura

- **No romper lo que funciona** — Siempre proteger ventas, SEO y operación actual
- **Integrar antes de reemplazar** — La evolución es progresiva, no destructiva
- **Documentar antes de construir** — Todo módulo nuevo debe tener su spec antes de desarrollarse
- **Modular e integrable** — La arquitectura futura debe permitir APIs y webhooks

---

*Última actualización: 13 de marzo 2026*
