# Plan de implementación — Etapa Feedback (Issue #6)

> Plan de acción · Issue [#6](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/6)

---

## Objetivo

Agregar la etapa "Feedback" al pipeline de turistas en GHL para capturar reseñas, testimonios y contenido post-viaje de forma sistemática.

---

## Tareas paso a paso

### Paso 1 — Crear la etapa en GHL (5 min)

- [ ] Abrir pipeline "Renta de Yates" en GoHighLevel
- [ ] Agregar nueva etapa después de "Ganada": **"Feedback"**
- [ ] Posición: entre "Ganada" y "En espera"

### Paso 2 — Crear automatización de movimiento (15 min)

- [ ] Crear workflow en GHL:
  - **Trigger:** 24-48 horas después de `fecha_de_viaje`
  - **Condición:** Lead está en etapa "Ganada"
  - **Acción:** Mover a "Feedback"

### Paso 3 — Crear secuencia de mensajes (30 min)

**Mensaje 1 — WhatsApp (24h post-viaje):**
> ¡Hola [nombre]! 🌊 Esperamos que hayas disfrutado tu experiencia con Yatezzitos. ¿Cómo estuvo tu viaje? Nos encantaría saber tu opinión.

**Mensaje 2 — Email (48h post-viaje):**
> Asunto: ¿Cómo fue tu experiencia, [nombre]?
> 
> Gracias por elegir Yatezzitos. Tu opinión nos ayuda a seguir mejorando.
> 
> 🌟 **Déjanos tu reseña en Google:** [link directo]
> 
> Si tienes fotos o videos de tu viaje y quieres compartirlos, responde este correo. ¡Nos encantaría publicarlos!

**Mensaje 3 — WhatsApp (5 días post-viaje, si no respondió):**
> Hola [nombre], ¿tendrías un minuto para dejarnos tu reseña? 🙏 Tu experiencia inspira a otros viajeros. [link Google Reviews]

### Paso 4 — Obtener link directo de Google Reviews (10 min)

- [ ] Ir a Google Business Profile de Yatezzitos
- [ ] Copiar el link directo para dejar reseña
- [ ] Acortarlo con bit.ly o similar para WhatsApp
- [ ] Guardarlo en las plantillas de GHL

### Paso 5 — Crear campo para tracking (10 min)

- [ ] Crear campo en GHL: `feedback_recibido` (Sí / No)
- [ ] Crear campo: `resena_google` (Sí / No)
- [ ] Crear campo: `testimonio_disponible` (Sí / No)

### Paso 6 — Probar el flujo (15 min)

- [ ] Crear contacto de prueba
- [ ] Mover a "Ganada" con una `fecha_de_viaje` = ayer
- [ ] Verificar que se mueve automáticamente a "Feedback"
- [ ] Verificar que los mensajes se envían correctamente

---

## Tiempo estimado total: **1.5 horas**

---

## Métricas de éxito

| Métrica | Objetivo |
|---|---|
| % de clientes que llegan a Feedback | > 80% de los que viajaron |
| % de reseñas en Google | > 20% de los que reciben la solicitud |
| Testimonios recibidos | Al menos 2 por mes |

---

*Última actualización: 13 de marzo 2026*
