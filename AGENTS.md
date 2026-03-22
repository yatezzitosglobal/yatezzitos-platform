# AGENTS.md - Reglas Operativas Globales para Agentes de IA

Este documento establece las reglas y directrices operativas que todos los agentes de IA (como el Copilot Coding Agent) deben seguir dentro del ecosistema de Yatezzitos Global.

## 1. Principios Fundamentales del Agente
- **Autonomía Responsable:** Actuar de forma autónoma dentro de los límites definidos, priorizando la seguridad y la calidad.
- **Contexto Primero:** Siempre buscar y comprender el contexto completo de una tarea antes de iniciar cualquier acción.
- **Transparencia:** Documentar claramente las acciones, decisiones y resultados en los Pull Requests y comentarios de issues.
- **Aprendizaje Continuo:** Adaptarse y mejorar basándose en el feedback y las nuevas instrucciones.

## 2. Guardrails de Seguridad (No Negociables)
- **❌ Prohibido inventar datos:** Precios, disponibilidad, capacidad, fechas, horarios.
- **❌ Prohibido exponer PII:** Teléfonos, correos, datos de pago en logs o código.
- **❌ Prohibido publicar secretos:** Llaves API, tokens, credenciales.
- **❌ Prohibido push directo a `main`:** Todo por Pull Request.
- **❌ Prohibido modificar archivos de producción sin indicación explícita.**
- **❌ Prohibido borrar archivos masivamente sin confirmación.
- **❌ Prohibido enviar mensajes a clientes o propietarios.**
- **❌ Prohibido ejecutar cobros, reembolsos o cancelaciones.**

## 3. Canales de Ingreso de Tareas (Task Ingestion)

Los agentes de IA reciben tareas a través de los siguientes canales:

### 3.1 GitHub Issues
El canal principal para la asignación de tareas estructuradas. Los issues pueden ser creados manualmente por el equipo o automáticamente a través de integraciones.

### 3.2 Integración WhatsApp → GitHub Issues
Una nueva capacidad permite que los mensajes de WhatsApp se conviertan automáticamente en GitHub Issues. Este flujo es:
**WhatsApp → GoHighLevel (GHL) → Webhook → GitHub Issue.**
Cuando una tarea proviene de este canal, el issue incluirá la etiqueta `from-whatsapp` y una nota indicando su origen. Los agentes deben procesar estas tareas como cualquier otro issue, siguiendo las instrucciones y guardrails.

## 4. Convenciones de Trabajo
- **Ramas:** `fix/[descripción]`, `feat/[descripción]`, `docs/[descripción]`, `seo/[descripción]`, `ai/[descripción]`.
- **Commits:** Mensajes en inglés con prefijo convencional (`fix:`, `feat:`, `docs:`, `seo:`, `chore:`).
- **Pull Requests:** Título descriptivo, body con resumen de cambios, archivos modificados y criterios de aceptación. Labels apropiados.

## 5. Enrutamiento de Tareas y Contexto
Los agentes DEBEN consultar los archivos de contexto específicos para cada tipo de tarea, según lo detallado en la Sección 5 de las "Instrucciones Maestras del Proyecto Yatezzitos Global" (`README.md`).

## 6. Actualización de la Memoria del Proyecto
Si una tarea requiere actualizar reglas o contexto global, el agente DEBE actualizar **AMBOS** archivos `CLAUDE.md` y `AGENTS.md`. Nunca actualizar solo uno.

## 7. Tono de Comunicación
- **Código y Commits:** Inglés técnico estándar.
- **Contenido de Usuario:** Español mexicano, profesional, cálido, claro, natural, reflejando la marca de lujo náutico.

---
*Este archivo es la fuente de verdad para las reglas operativas de los agentes IA.*