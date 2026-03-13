# Plan de implementación — Ordenar el CRM (Issue #4)

> Plan de acción · Issue [#4](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/4)

---

## Objetivo

Ordenar GoHighLevel para que funcione como una operación clara, segmentada y productiva. Sin esto, ninguna automatización futura funcionará bien.

---

## Tareas paso a paso

### Bloque 1 — Auditoría de la base actual (2-3 hrs)

- [ ] Exportar lista completa de contactos de GHL
- [ ] Contar: ¿cuántos contactos hay en total?
- [ ] Identificar cuántos son turistas vs propietarios vs otros
- [ ] Detectar contactos duplicados
- [ ] Detectar contactos sin clasificar (sin tag ni pipeline)
- [ ] Anotar campos custom que están vacíos en la mayoría de contactos

### Bloque 2 — Crear campo `rol_de_usuario` (30 min)

- [ ] Crear campo personalizado en GHL: `rol_de_usuario`
- [ ] Valores posibles:
  - `turista`
  - `propietario`
  - `administrador`
  - `broker`
  - `agencia`
  - `capitan`
  - `aliado`
  - `equipo_interno`
- [ ] Asignar el rol correcto a todos los contactos existentes (puede hacerse en bloques)

### Bloque 3 — Limpiar pipeline de turistas (1 hr)

- [ ] Revisar que todos los leads estén en la etapa correcta
- [ ] Mover a "Pérdidas" los leads cuya fecha de viaje ya pasó y no reservaron
- [ ] Mover a "Ganada" los que sí pagaron y tienen recibo
- [ ] Identificar leads estancados en "Bienvenidos" por más de 30 días
- [ ] Documentar cuántos leads hay en cada etapa

### Bloque 4 — Limpiar pipeline de propietarios (1 hr)

- [ ] Revisar que todos los propietarios estén en la etapa correcta
- [ ] Agregar etapa **"Documentos recibidos"** al pipeline
- [ ] Agregar etapa **"En revisión"** al pipeline
- [ ] Mover propietarios a la etapa correcta
- [ ] Verificar que cada propietario tenga embarcación vinculada

### Bloque 5 — Estandarizar campos críticos (1 hr)

- [ ] Verificar que estos campos estén llenos en contactos activos:

| Campo | Para turistas | Para propietarios |
|---|---|---|
| `first_name` + `last_name` | ✅ | ✅ |
| `email` | ✅ | ✅ |
| `phone` | ✅ | ✅ |
| `rol_de_usuario` | ✅ | ✅ |
| `city` / destino | ✅ | ✅ |
| `fecha_de_viaje` | ✅ | N/A |
| `yacht_name` | ✅ (si cotizó) | N/A |
| `nombre_empresa` | N/A | Recomendado |

- [ ] Eliminar o fusionar campos duplicados que no se usan
- [ ] Documentar qué campos quedan activos y para qué sirven

### Bloque 6 — Crear tags de segmentación (30 min)

- [ ] Crear tags útiles para filtrar rápido:
  - `ciudad:cancun`, `ciudad:vallarta`, `ciudad:mazatlan`, etc.
  - `estado:activo`, `estado:inactivo`
  - `fuente:seo`, `fuente:ads`, `fuente:referido`, `fuente:whatsapp`
  - `prioridad:alta`, `prioridad:media`

### Bloque 7 — Documentar el resultado (30 min)

- [ ] Actualizar `docs/crm/current-state.md` con los cambios realizados
- [ ] Registrar: cuántos contactos por tipo, campos activos, pipelines ordenados

---

## Tiempo estimado total: **6-7 horas**

---

## Resultado esperado

Después de ejecutar este plan:
- ✅ Cada contacto tiene un `rol_de_usuario` claro
- ✅ Los pipelines están limpios y cada lead en su etapa correcta
- ✅ Los campos críticos están estandarizados
- ✅ No hay duplicados significativos
- ✅ El pipeline de propietarios tiene las etapas nuevas
- ✅ El CRM está listo para automatizaciones (Issue #5)

---

*Última actualización: 13 de marzo 2026*
