# Estado futuro del CRM

## Resumen
El CRM futuro de Yatezzitos debe evolucionar de una base funcional pero parcialmente desordenada hacia una estructura mucho más clara, segmentada, automatizada y conectada con el ecosistema digital completo.

El objetivo no es reemplazar de inmediato GoHighLevel, sino convertirlo en una operación más sólida mientras se prepara la futura arquitectura híbrida de:

- WordPress como capa comercial y SEO,
- CRM como núcleo operativo,
- web app como capa de producto,
- y Yatezzitos IA como capa de asistencia y automatización.

---

## Visión del CRM futuro
El CRM de Yatezzitos debe convertirse en el sistema central para organizar la relación entre:

- turistas / clientes,
- socios comerciales,
- propietarios,
- administradores,
- brokers,
- agencias,
- reservas,
- documentos,
- seguimiento comercial,
- postventa,
- y automatizaciones internas.

Debe dejar de ser solo un lugar donde viven contactos y convertirse en una estructura clara de operación.

---

## Objetivos principales del CRM futuro
El estado futuro ideal del CRM debe permitir:

- separar correctamente perfiles,
- ordenar mejor la base de datos,
- reducir procesos manuales,
- automatizar transiciones de etapas,
- dar mejor visibilidad al equipo interno,
- conectar mejor con WordPress,
- alimentar mejor la futura web app,
- aprovechar mejor los custom fields existentes,
- y preparar la expansión internacional.

---

## Principios de diseño del CRM futuro

### 1. Un contacto no debe ser un caos de roles mezclados
Cada contacto debe tener una clasificación clara dentro del sistema.

### 2. El CRM debe servir a la operación real
La estructura debe reflejar la lógica real del negocio, no solo una organización genérica.

### 3. Lo importante debe estar visible
Reservas, estatus, pagos, etapas, documentos y tipo de perfil deben poder consultarse con claridad.

### 4. Menos dependencia de procesos manuales
El equipo no debería tener que repetir tareas que pueden automatizarse.

### 5. Mejor segmentación = mejor automatización
Sin segmentación clara, no puede haber buen seguimiento ni escalabilidad.

---

## Estructura deseada de perfiles

## 1. Turistas / clientes
Este perfil seguirá siendo el principal generador actual de ingresos.

### Qué debe tener este perfil
- datos de contacto,
- etapa comercial,
- ciudad o destino de interés,
- fecha de viaje,
- embarcación cotizada o reservada,
- estatus de cotización,
- estatus de reserva,
- método de pago,
- links de cotización o reserva,
- seguimiento antes del viaje,
- seguimiento después del viaje,
- historial de interacción.

---

## 2. Socios comerciales
Bajo esta categoría general deben entrar:

- propietarios,
- administradores,
- brokers,
- agencias,
- capitanes,
- aliados comerciales.

### Qué debe tener este perfil
- tipo de socio comercial,
- embarcaciones relacionadas,
- ciudad / zonas de operación,
- estatus de onboarding,
- disponibilidad de embarcaciones,
- documentación cargada,
- estado comercial,
- notas internas,
- y potencial de integración futura al panel de propietarios.

---

## 3. Equipo interno
Aunque no necesariamente todo debe vivir como contacto tradicional, el sistema debe contemplar mejor soporte para operación interna.

### Qué debe poder consultarse desde la lógica interna
- quién gestionó la cotización,
- quién envió el recibo,
- quién aprobó embarcación,
- quién dio seguimiento,
- qué vendedor o asesor está asignado.

---

## Segmentación recomendada del CRM futuro

### Segmentación principal por tipo de contacto
El CRM futuro debería tener al menos una clasificación clara para diferenciar:

- cliente turista,
- propietario,
- administrador,
- broker,
- agencia,
- capitán,
- aliado,
- usuario interno relacionado.

### Recomendación
Esta clasificación puede vivir en:
- un campo de rol,
- etiquetas bien diseñadas,
- o una combinación de ambos.

La regla es que ningún contacto importante quede sin clasificación clara.

---

## Pipelines recomendados

## Pipeline 1: Turistas — renta de yates
Este pipeline ya existe y debe mantenerse, pero mejor documentado y automatizado.

### Etapas recomendadas
1. Bienvenidos a bordo
2. Cotización enviada
3. Envío de métodos de pago
4. Recibos pendientes
5. Ganada - El cliente ha pagado
6. Feedback
7. En espera - Prórroga
8. Pérdidas - No realizadas

### Nota sobre el orden
A nivel visual o de interfaz pueden mantenerse como te resulte más cómodo, pero a nivel documental la lógica debe quedar clara:

- un lead entra,
- se cotiza,
- puede pedir métodos de pago,
- si paga pero falta recibo, queda en recibos pendientes,
- cuando ya pagó y ya recibió recibo, entra en ganada,
- después del viaje pasa a feedback,
- si no reserva y sigue vigente, queda en espera,
- si ya pasó la fecha, va a pérdidas.

---

## Pipeline 2: Captación de propietarios
Este pipeline ya existe y debe formalizarse mejor.

### Objetivo
Ordenar la captación, seguimiento, evaluación y publicación de nuevos socios comerciales y embarcaciones.

### Etapas recomendadas base
- Bienvenida / entrada de lead
- Encuesta enviada
- Seguimiento pendiente
- No contestó / recontacto
- En espera
- No interesado
- Aprobado
- Subido al sitio web

### Nota
Los nombres exactos pueden refinarse después, pero la lógica debe quedar orientada a captación, validación y publicación.

---

## Pipeline 3 recomendado a futuro: Operación post-reserva
Este pipeline no es obligatorio todavía, pero a futuro puede ser útil separar mejor la operación postventa de la etapa comercial.

### Posible uso
- reserva confirmada,
- documentación validada,
- viaje próximo,
- viaje realizado,
- feedback solicitado,
- feedback recibido,
- remarketing.

### Recomendación actual
Por ahora no es necesario abrirlo.  
Primero conviene agregar la etapa **Feedback** dentro del pipeline actual de turistas.

---

## Automatizaciones futuras prioritarias

## 1. Automatización de cotización
Hoy la cotización depende de una encuesta interna del equipo.

### Futuro deseado
- reducir pasos repetitivos,
- mantener control del vendedor,
- mejorar consistencia,
- actualizar campos y etapas automáticamente.

---

## 2. Automatización de recibo de depósito
Cuando el cliente paga y el equipo envía el recibo, hoy existe una lógica operativa clara.

### Futuro deseado
- que el sistema valide mejor cuándo pasa de “Recibos pendientes” a “Ganada”,
- que se actualicen campos de forma más confiable,
- que el equipo vea claramente qué falta.

---

## 3. Automatización de seguimiento por fecha de viaje
Este es uno de los puntos más importantes del CRM futuro.

### Futuro deseado
Que el sistema use `fecha_de_viaje` para disparar automatizaciones como:

- recordatorios previos al viaje,
- mensajes importantes antes de abordar,
- solicitud de saldo restante cuando aplique,
- seguimiento después del viaje,
- solicitud de feedback,
- salida automática a etapa de pérdidas si no reservó y ya pasó la fecha.

---

## 4. Automatización de etapa Feedback
Esta nueva etapa debe dispararse de forma automática una vez que:
- el cliente sí reservó,
- el viaje ya pasó,
- y corresponda solicitar review y feedback.

### Qué debería disparar
- agradecimiento,
- solicitud de reseña en Google,
- encuesta de satisfacción,
- solicitud de testimonio,
- y eventual remarketing futuro.

---

## 5. Automatización de captación de propietarios
Hoy este flujo todavía depende mucho de formularios y seguimiento manual.

### Futuro deseado
- mejor formulario,
- campos más claros,
- creación automática del perfil de socio comercial,
- avance por etapas,
- revisión y aprobación interna,
- conexión futura con el panel de propietarios.

---

## Relación entre CRM y WordPress
El CRM futuro debe seguir conectado con WordPress, pero con más claridad estructural.

### WordPress debe seguir usando el CRM para:
- cotizaciones,
- reservas,
- tokens,
- páginas dinámicas,
- automatizaciones visibles al cliente,
- formularios,
- y flujos de captación.

### Pero el CRM no debe depender solo de WordPress
Debe poder operar también como base conectada a:
- futura web app,
- futuras APIs,
- asistentes IA,
- y otros sistemas.

---

## Relación entre CRM y futura web app
En el futuro, la web app no debería duplicar caóticamente la información del CRM.

La relación ideal es esta:

- el CRM sigue siendo núcleo operativo de contacto, seguimiento y automatización,
- la web app se convierte en la interfaz más organizada para cliente, propietario y equipo interno,
- ambos sistemas conversan mediante integraciones claras.

---

## Relación entre CRM y Yatezzitos IA
Yatezzitos IA debe aprovechar el CRM como fuente de contexto.

### Casos de uso deseados
- responder con base en la etapa del cliente,
- leer destino, fecha y embarcación,
- clasificar leads,
- actualizar etiquetas o campos,
- asistir a propietarios según su estado,
- ayudar al equipo con mejor contexto.

### Regla
La IA no debe operar a ciegas.  
Debe apoyarse en la estructura del CRM.

---

## Uso recomendado de custom fields
El CRM futuro debe aprovechar mejor los campos personalizados ya creados.

### Recomendación
No crear nuevos campos por impulso si ya existe uno que cumple esa función.

### Qué conviene hacer
- auditar campos existentes,
- eliminar duplicados innecesarios,
- estandarizar nombres,
- definir cuáles son críticos,
- y documentar su uso real.

---

## Carpetas / grupos de campos que ya deben considerarse estratégicos
Las siguientes estructuras ya tienen valor real y deben tratarse como base del sistema:

- Recibo de deposito
- Cotizaciones enviadas
- Datos de reserva
- User Wordpress
- Contacto
- General Info

Estas carpetas deben servir como punto de partida para la limpieza y formalización del CRM.

---

## Estructura deseada del ciclo de vida del turista
El CRM futuro debe permitir leer con claridad este recorrido:

1. lead nuevo,
2. interés validado,
3. cotización enviada,
4. negociación / método de pago,
5. anticipo pagado,
6. recibo enviado,
7. reserva activa,
8. viaje próximo,
9. viaje realizado,
10. feedback solicitado,
11. remarketing / recompra.

Esto ayudará a automatizar mejor y a medir mejor el negocio.

---

## Estructura deseada del ciclo de vida del socio comercial
También debe poder leerse claramente algo como:

1. lead nuevo,
2. interés del propietario,
3. encuesta o captación enviada,
4. seguimiento,
5. documentación,
6. aprobación,
7. embarcación publicada,
8. operación activa,
9. seguimiento de rendimiento.

---

## Qué debe dejar de pasar en el CRM futuro
El estado futuro ideal debe evitar:

- mezcla caótica de perfiles,
- campos duplicados sin criterio,
- procesos críticos manuales innecesarios,
- falta de visibilidad del estatus real,
- seguimientos sin automatización,
- desconexión entre CRM y web,
- pérdida de contexto entre ventas y operación.

---

## Qué debería medirse mejor
El CRM futuro debería permitir medir mejor:

### Turistas
- leads por canal,
- cotizaciones enviadas,
- reservas logradas,
- pagos pendientes,
- feedback recibido,
- repetición de clientes.

### Socios comerciales
- propietarios captados,
- embarcaciones aprobadas,
- embarcaciones publicadas,
- tiempo de onboarding,
- rendimiento por socio.

### Operación interna
- tiempos de respuesta,
- tiempos de cotización,
- tiempos de envío de recibo,
- seguimiento cumplido,
- automatizaciones activas vs manuales.

---

## Decisión estratégica
El futuro del CRM no consiste en tirar lo que existe.

Consiste en:
- ordenar,
- clasificar,
- estandarizar,
- automatizar,
- conectar mejor,
- y preparar la infraestructura para crecimiento real.

---

## Conclusión
El CRM futuro de Yatezzitos debe convertirse en una estructura clara, segmentada y escalable, capaz de sostener:

- ventas más ordenadas,
- reservas mejor controladas,
- captación de propietarios,
- disponibilidad futura,
- mejor integración con WordPress,
- conexión con la futura web app,
- y automatizaciones más inteligentes apoyadas por Yatezzitos IA.

El CRM ya no debe funcionar solo como una colección de contactos.  
Debe funcionar como el sistema operativo comercial y relacional del negocio.