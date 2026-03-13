# CRM — Índice de documentos

Este directorio contiene la documentación del CRM (GoHighLevel) de **Yatezzitos Global**.

---

## Documentos existentes

| Documento | Descripción | Estado |
|---|---|---|
| [current-state.md](current-state.md) | Estado actual del CRM: estructura, campos, pipelines, integraciones | ✅ Completo |
| [future-state.md](future-state.md) | Visión futura del CRM: segmentación, automatizaciones, métricas | ✅ Completo |
| [onboarding-propietarios.md](onboarding-propietarios.md) | Flujo completo de onboarding de propietarios y embarcaciones | ✅ Completo |

---

## Resumen de estado

### CRM actual (GoHighLevel)
- **Pipeline de turistas:** Activo con 7 etapas (Bienvenida → Ganada / Pérdidas)
- **Pipeline de propietarios:** Activo con etapas de captación
- **Automatizaciones:** Parciales (cotización, recibos, seguimiento)
- **Integración con WordPress:** Vía webhooks y endpoints personalizados

### Mejoras prioritarias
- Agregar etapa **Feedback** al pipeline de turistas ([Issue #6](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/6))
- Agregar etapas **Documentos recibidos** y **En revisión** al pipeline de propietarios
- Crear campo **`rol_de_usuario`** para clasificar propietarios, agencias, brokers
- Ordenar base de datos y separar perfiles ([Issue #4](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/4))
- Automatizar flujo comercial completo ([Issue #5](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/5))

---

## Campos principales del CRM por carpeta

| Carpeta GHL | Uso principal |
|---|---|
| Contacto (estándar) | Identidad: nombre, email, teléfono, source |
| General Info | Ubicación: empresa, dirección, ciudad, país |
| Recibo de depósito | Reserva: yate, fecha, monto, marina, pasajeros, servicios |
| Cotizaciones enviadas | Cotización: imagen, token, URL, fecha compromiso |
| Datos de reserva | Reserva: estado, método pago, ID, URL, captura |
| User WordPress | WP: rol, username, cargo, licencia, RFC |

---

## Relación con otros documentos

- **Arquitectura:** Ver [docs/architecture/integrations.md](../architecture/integrations.md) para mapa de integraciones WP ↔ GHL
- **Backlog:** Ver [docs/scrum/backlog.md](../scrum/backlog.md) para prioridades del CRM
- **Agentes IA:** Ver [ai/assistants/README.md](../../ai/assistants/README.md) para cómo los agentes usan el CRM

---

*Última actualización: 13 de marzo 2026*
