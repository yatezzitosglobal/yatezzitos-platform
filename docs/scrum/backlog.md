# Backlog maestro del proyecto

## Objetivo del backlog
Este backlog organiza las prioridades reales de Yatezzitos para que el proyecto avance con orden, enfoque y sentido comercial.

No todo lo importante debe hacerse al mismo tiempo.  
La prioridad actual es ejecutar primero lo que más impacto tiene en:

- reservas,
- operación,
- SEO,
- orden del CRM,
- automatización,
- y escalabilidad.

---

## Regla general de priorización
Toda tarea debe evaluarse según estas preguntas:

1. ¿Ayuda a generar más reservas?
2. ¿Reduce trabajo manual?
3. ¿Mejora el SEO o la conversión?
4. ¿Ordena mejor el CRM o la base de datos?
5. ¿Aporta a la futura plataforma sin romper lo actual?

Si una tarea no ayuda en al menos una de estas áreas, no debe tener prioridad alta.

---

## Prioridades maestras del proyecto

### Prioridad 1 — No romper lo que ya funciona
Antes de construir cosas nuevas, el sistema actual debe mantenerse funcional.

Incluye:
- WordPress activo
- Houzez activo
- Elementor activo
- GoHighLevel como CRM principal
- flujos actuales de cotización y reserva
- páginas ya posicionadas
- integraciones activas con webhooks, Twilio y páginas dinámicas

### Prioridad 2 — Completar lo que ya existe
Antes de abrir nuevas líneas, se debe terminar bien lo que ya está abierto.

Incluye:
- rediseño pendiente
- SEO incompleto en ciudades activas
- fichas de yates ya publicadas sin keyword clara
- automatizaciones incompletas
- estructura documental
- orden del CRM

### Prioridad 3 — Construir la base para escalar
Una vez optimizado lo actual, se construye la base de:
- disponibilidad en tiempo real
- onboarding de propietarios
- mejor estructura de datos
- paneles
- futura web app
- IA aplicada

---

## Backlog por niveles

# Nivel 1 — Crítico e inmediato
Estas tareas tienen impacto directo en ventas, orden y operación actual.

## 1. Completar documentación base del proyecto
**Estado:** ✅ Completada (marzo 2026)

### Objetivo
Tener claridad total de visión, stack, CRM, SEO, decisiones y prioridades.

### Tareas
- [x] completar carpeta `docs/` (29 documentos organizados en 6 subcarpetas)
- [x] dejar README sólido
- [x] dejar AGENTS.md sólido
- [x] dejar backlog ordenado
- [x] dejar arquitectura documentada

---

## 2. Terminar el rediseño web de Figma a WordPress
**Estado:** 🔄 En progreso

### Objetivo
Actualizar visualmente el sitio y mejorar percepción premium, claridad y conversión.

### Assets disponibles
> Los diseños de Figma (desktop + responsive) y el CSS personalizado actual ya están versionados en `redesign/`. Ver [`redesign/README.md`](../../redesign/README.md) para detalle completo.

### Tareas
- [ ] terminar home
- [x] **Contact Us** — Código completo en `redesign/02-contact-us.md`
- [x] **Help / FAQ** — Código completo en `redesign/03-help.md` (5 secciones: hero+buscador+pills, FAQ accordion+CTA equipo, bloques informativos, footer)
- [ ] adaptar diseño a páginas clave (Blog, Blog Details, Blog Category)
- [x] mejorar responsive (mobile + tablet + desktop validado en Help y Contact Us)
- [ ] validar que el diseño no rompa SEO ni formularios
- [x] usar `redesign/figma/` como referencia visual de los diseños aprobados
- [x] revisar CSS actual documentado en `redesign/css/` antes de hacer cambios

### Impacto
Muy alto en:
- percepción de marca
- conversión
- confianza
- experiencia del usuario

---

## 3. Completar SEO de las ciudades ya abiertas
**Estado:** 🔄 En progreso

### Objetivo
Optimizar todas las ciudades activas y todas las embarcaciones ya publicadas antes de abrir nuevos destinos.

### Tareas
- [x] auditoría SEO completa (`docs/seo/auditoria-seo-completa.md`)
- [x] análisis de oportunidades de keywords (`docs/seo/oportunidades-keywords-2026.md`)
- [x] framework de asignación de keywords (`docs/seo/keyword-assignment-framework.md`)
- [x] guía Search Console 2026 (`docs/seo/guia-search-console-2026.md`)
- [x] reglas de producción de contenido (`docs/seo/content-production-rules.md`)
- [ ] revisar ciudad por ciudad e implementar optimizaciones
- [ ] asignar keyword madre a cada ciudad
- [ ] asignar keyword única a cada yate
- [ ] evitar canibalización
- [ ] completar páginas faltantes
- [ ] reforzar enlazado interno
- [ ] completar ciudades urgentes: Acapulco, Ixtapa, Huatulco, Nuevo Vallarta, Playa del Carmen

### Impacto
Muy alto en:
- tráfico orgánico
- leads
- reservas

---

## 4. Ordenar el CRM actual
**Estado:** Pendiente

### Objetivo
Limpiar y ordenar la estructura actual de GoHighLevel para que el negocio escale con menos caos.

### Tareas
- revisar campos actuales
- definir campos críticos
- separar mejor perfiles
- ordenar base de datos
- clarificar uso de pipelines
- documentar automatizaciones reales
- revisar datos mezclados entre clientes y socios comerciales

### Impacto
Muy alto en:
- operación
- seguimiento
- automatización futura

---

## 5. Automatizar mejor el flujo comercial actual
**Estado:** Pendiente

### Objetivo
Reducir dependencias manuales en cotización, reserva, recibos y seguimiento.

### Tareas
- revisar automatización de cotización
- revisar automatización de recibo de depósito
- revisar transición entre etapas del pipeline
- automatizar feedback post-viaje
- mejorar seguimiento antes de fecha de viaje
- mejorar seguimiento después del viaje
- mejorar envío de métodos de pago

### Impacto
Muy alto en:
- productividad interna
- experiencia del cliente
- orden comercial

---

# Nivel 2 — Alto valor y siguiente fase
Estas tareas son la base de la siguiente etapa de crecimiento.

## 6. Crear etapa Feedback dentro del pipeline de turistas
**Estado:** Pendiente

### Objetivo
Mover clientes que ya realizaron su viaje a una etapa enfocada en:
- reseñas
- feedback
- testimonios
- reputación

### Tareas
- crear etapa “Feedback”
- definir criterio de entrada
- automatizar paso después de fecha de viaje
- crear secuencia de solicitud de reseña
- enlazar con Google Business Profile

---

## 7. Mejorar captación de propietarios
**Estado:** Pendiente

### Objetivo
Hacer más simple, clara y efectiva la captación de nuevos propietarios y embarcaciones.

### Tareas
- mejorar landing de propietarios
- mejorar formulario
- definir campos obligatorios
- simplificar experiencia
- preparar revisión y aprobación interna
- conectar mejor con CRM

### Impacto
Muy alto en:
- expansión de inventario
- crecimiento geográfico
- oferta disponible

---

## 8. Diseñar flujo de onboarding de propietarios
**Estado:** Pendiente

### Objetivo
Convertir el proceso de alta de embarcaciones en un flujo estructurado y profesional.

### Tareas
- definir datos mínimos del propietario
- definir datos mínimos de embarcación
- definir documentos necesarios
- definir revisión interna
- definir aprobación antes de publicar

---

## 9. Diseñar la lógica del calendario de disponibilidad
**Estado:** Pendiente

### Objetivo
Crear la base funcional del módulo más importante para propietarios y operación.

### Tareas
- definir casos de uso
- definir quién actualiza disponibilidad
- definir cómo se comparte
- definir estatus
- definir conexión futura con reservas
- definir si se integra primero en WordPress o fuera

### Impacto
Altísimo en:
- escalabilidad
- control de inventario
- automatización futura

---

## 10. Crear mapa de integraciones actuales
**Estado:** Pendiente

### Objetivo
Tener un documento claro de cómo conversa hoy:
- WordPress
- GoHighLevel
- Twilio
- páginas dinámicas
- webhooks
- formularios
- páginas de cotización / reserva

---

# Nivel 3 — Plataforma y producto
Estas tareas construyen la siguiente gran fase del negocio.

## 11. Definir estructura funcional del marketplace de yates
**Estado:** Pendiente

### Objetivo
Diseñar cómo funcionará el marketplace como producto principal futuro.

### Tareas
- definir filtros
- definir lógica de ciudades
- definir lógica de embarcaciones
- definir UX de búsqueda
- definir relación con disponibilidad
- definir relación con cotización y reserva

---

## 12. Definir cuenta del cliente
**Estado:** Pendiente

### Objetivo
Diseñar la futura cuenta del cliente para que vea:
- cotización
- reserva
- pagos
- viaje
- estatus
- soporte

---

## 13. Definir panel de propietarios
**Estado:** Pendiente

### Objetivo
Diseñar el panel donde propietarios y socios comerciales puedan:
- subir embarcaciones
- compartir disponibilidad
- ver actividad
- cargar documentos
- operar mejor

---

## 14. Definir panel interno
**Estado:** Pendiente

### Objetivo
Diseñar una interfaz más clara para el equipo interno.

### Tareas
- seguimiento comercial
- seguimiento operativo
- lectura de reservas
- lectura de disponibilidad
- gestión de socios comerciales

---

## 15. Diseñar arquitectura inicial de la web app
**Estado:** Pendiente

### Objetivo
Definir la estructura mínima viable de la futura web app sin romper WordPress.

### Tareas
- definir módulos
- definir prioridades
- definir flujos
- definir integración con CRM
- definir fases de desarrollo

---

# Nivel 4 — IA aplicada
Estas tareas aceleran marketing, atención y operación.

## 16. Crear asistente IA para atención al turista
**Estado:** Pendiente

### Objetivo
Atender mejor WhatsApp, redes, chat web y dudas frecuentes.

### Tareas
- definir conocimiento base
- definir tono
- definir fuentes de datos
- integrar con CRM
- definir límites y escalamiento a humano

### Impacto
Muy alto en:
- tiempo de respuesta
- conversión
- atención

---

## 17. Crear asistente IA para propietarios
**Estado:** Pendiente

### Objetivo
Ayudar a captar y acompañar propietarios en onboarding, requisitos y operación.

---

## 18. Crear IA de soporte interno
**Estado:** Pendiente

### Objetivo
Apoyar internamente:
- SEO
- copywriting
- marketing
- diseño
- desarrollo
- operaciones

---

## 19. Definir reglas de IA y AGENTS.md
**Estado:** ✅ Completada (marzo 2026)

### Objetivo
Dejar documentado cómo deben trabajar los agentes IA dentro del proyecto.

### Resultado
- `AGENTS.md` en la raíz del repositorio define reglas completas de seguridad, privacidad, tono, escalamiento y control de cambios.
- 5 specs de agentes IA documentadas en `ai/assistants/` (orquestador, turista, propietario, soporte interno, README).

---

# Nivel 5 — Expansión futura
Estas tareas son importantes, pero no deben distraer la fase actual.

## 20. Abrir nuevos destinos dentro de México
**Estado:** Futuro

Ejemplos:
- Veracruz
- Ensenada
- Manzanillo
- San Carlos
- otros

## 21. Abrir nuevo país hispanohablante
**Estado:** Futuro

## 22. Construir membresía vacacional
**Estado:** Futuro

## 23. Convertir herramientas internas en SaaS
**Estado:** Futuro

## 24. Escuela digital de turismo náutico de lujo
**Estado:** Futuro

## 25. App móvil completa
**Estado:** Futuro

---

## Orden recomendado de ejecución

### Fase 1
- documentación
- rediseño
- SEO de ciudades abiertas
- orden del CRM
- automatizaciones críticas

### Fase 2
- feedback
- captación de propietarios
- onboarding
- mapa de integraciones
- calendario de disponibilidad

### Fase 3
- marketplace
- paneles
- cuenta del cliente
- estructura de web app

### Fase 4
- asistentes IA
- soporte interno IA
- automatización avanzada

### Fase 5
- expansión internacional
- SaaS
- membresía
- app móvil

---

## Qué no debe distraernos ahora
En esta etapa, no deben quitarnos foco:

- abrir demasiados destinos nuevos sin terminar los actuales,
- construir una app móvil demasiado pronto,
- rehacer todo el stack de golpe,
- crear productos nuevos antes de optimizar la renta de yates,
- dispersarnos en ideas sin backlog ni documentación.

---

## Criterio de éxito del backlog
Este backlog estará funcionando bien si nos ayuda a:

- tomar decisiones más rápido,
- saber qué sigue,
- priorizar lo importante,
- reducir caos,
- avanzar por fases,
- y convertir visión en ejecución real.

---

## Nota final
Este backlog no es estático.

Debe actualizarse conforme:
- se completen tareas,
- cambien prioridades,
- aparezcan nuevas necesidades,
- o el negocio entre en una nueva fase.
