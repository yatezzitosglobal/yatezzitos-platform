# Plan de implementación — Automatizar flujo comercial (Issue #5)

> Plan de acción · Issue [#5](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/5)
> 
> **Prerequisito:** Issue #4 (Ordenar CRM) debe completarse primero.

---

## Objetivo

Automatizar los pasos repetitivos del flujo comercial en GoHighLevel para reducir trabajo manual, acelerar respuestas y mejorar la conversión de leads a reservas.

---

## Automatizaciones por etapa del pipeline

### Automatización 1 — Lead nuevo entra (Bienvenidos a bordo)

**Trigger:** Contacto nuevo entra al pipeline "Renta de Yates"

**Acciones automáticas:**
- [ ] Enviar WhatsApp de bienvenida (plantilla)
- [ ] Enviar email de bienvenida con link al sitio
- [ ] Asignar tag `fuente:[canal]`
- [ ] Notificar al vendedor asignado
- [ ] Si no hay contacto en 24 hrs → recordatorio automático al vendedor

**Mensaje sugerido (WhatsApp):**
> ¡Hola [nombre]! 🌊 Gracias por tu interés en Yatezzitos. Recibimos tu solicitud y en breve un asesor te contactará para ayudarte a encontrar la embarcación perfecta. 🚢

---

### Automatización 2 — Cotización enviada

**Trigger:** Lead se mueve a "Cotización enviada"

**Acciones automáticas:**
- [ ] Enviar email con cotización personalizada (`quote_url`)
- [ ] Enviar WhatsApp con link a la cotización
- [ ] Programar seguimiento: si no responde en 48 hrs → WhatsApp automático
- [ ] Programar seguimiento: si no responde en 5 días → email de recordatorio

**Mensaje sugerido (seguimiento 48h):**
> Hola [nombre], ¿pudiste revisar tu cotización? Estoy disponible para resolver cualquier duda. 📩

---

### Automatización 3 — Envío de métodos de pago

**Trigger:** Lead se mueve a "Envío de métodos de pago"

**Acciones automáticas:**
- [ ] Enviar email/WhatsApp con métodos de pago aceptados
- [ ] Incluir monto del anticipo (50%)
- [ ] Incluir fecha límite de pago
- [ ] Programar recordatorio si no paga en 72 hrs

---

### Automatización 4 — Anticipo recibido (Recibos pendientes)

**Trigger:** Lead se mueve a "Recibos pendientes"

**Acciones automáticas:**
- [ ] Enviar WhatsApp: "Recibimos tu pago. Estamos preparando tu recibo."
- [ ] Notificar al equipo interno para preparar recibo de depósito
- [ ] Si recibo no se envía en 24 hrs → alerta al equipo

---

### Automatización 5 — Reserva confirmada (Ganada)

**Trigger:** Lead se mueve a "Ganada"

**Acciones automáticas:**
- [ ] Enviar email de confirmación con recibo de depósito
- [ ] Enviar WhatsApp con resumen de la reserva
- [ ] Actualizar página dinámica de reserva en WordPress
- [ ] Bloquear fecha en calendario de disponibilidad (futuro, Issue #9)
- [ ] Programar recordatorio pre-viaje (48 hrs antes)

---

### Automatización 6 — Recordatorio pre-viaje

**Trigger:** 48 horas antes de `fecha_de_viaje`

**Acciones automáticas:**
- [ ] Enviar WhatsApp con detalles del viaje:
  - Marina + Google Maps
  - Hora de salida
  - Qué llevar
  - Contacto del capitán/equipo
- [ ] Enviar email de respaldo con la misma info

---

### Automatización 7 — Post-viaje → Feedback (Issue #6)

**Trigger:** 24-48 horas después de `fecha_de_viaje` (solo si está en "Ganada")

**Acciones automáticas:**
- [ ] Mover lead a etapa "Feedback"
- [ ] Enviar WhatsApp de agradecimiento
- [ ] Enviar email pidiendo reseña en Google
- [ ] Incluir link directo a Google Reviews

---

### Automatización 8 — Lead vencido → Pérdidas

**Trigger:** `fecha_de_viaje` ya pasó + lead NO está en "Ganada"

**Acciones automáticas:**
- [ ] Mover a "Pérdidas — No realizadas"
- [ ] Enviar email: "¿Podemos ayudarte para tu próximo viaje?"
- [ ] Agregar tag `remarketing`

---

## Prioridad de implementación

| Prioridad | Automatización | Impacto |
|---|---|---|
| 🔴 1 | Bienvenida (lead nuevo) | Reduce tiempo de primer contacto |
| 🔴 2 | Seguimiento post-cotización | Reduce leads olvidados |
| 🔴 3 | Confirmación de reserva | Mejora experiencia del cliente |
| 🟠 4 | Recordatorio pre-viaje | Reduce no-shows y dudas |
| 🟠 5 | Métodos de pago | Acelera conversión |
| 🟡 6 | Post-viaje / feedback | Genera reseñas |
| 🟡 7 | Lead vencido | Limpia pipeline |
| 🟡 8 | Alerta recibo pendiente | Mejora operación interna |

---

## Tiempo estimado total: **8-12 horas en GHL**

(Incluye crear workflows, escribir plantillas, probar y activar)

---

*Última actualización: 13 de marzo 2026*
