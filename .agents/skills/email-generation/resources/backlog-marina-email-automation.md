# Tarea Pendiente: Marina — Automatización de Respuestas de Email

> **Estado:** Pendiente · **Prioridad:** Media · **Dependencias:** Issue #16 (Asistente IA turistas) + Skill `email-generation`

## Descripción

Implementar un sistema donde **Marina** (asistente IA para turistas) pueda leer correos electrónicos entrantes y generar respuestas automáticas en formato HTML profesional.

## Flujo

```
Cliente envía email → GHL inbox → Webhook → Endpoint → IA (Marina + SKILL.md) → HTML → GHL API envía respuesta
```

## Fases

### Fase 1 — Semi-automático (MVP)
- Webhook recibe email → IA genera respuesta → **guarda como borrador** para revisión humana

### Fase 2 — Automático
- Respuestas automáticas para FAQ (disponibilidad, precios, destinos)
- Escalamiento a humano para temas complejos (pagos, cancelaciones, quejas)

## Piezas necesarias

| Pieza | Estado |
|---|---|
| SKILL.md | ✅ Listo |
| Catálogo yates/ciudades/blog | ✅ Listo |
| Marina spec | ✅ en `ai/assistants/turista.md` |
| GHL Webhook trigger | ❌ Por configurar |
| Endpoint receptor | ❌ Por construir |
| Integración IA + skill | ❌ Por construir |
| GHL API envío | ⚠️ Disponible via MCP |

> **Nota:** Crear issue en GitHub cuando las credenciales estén disponibles.
