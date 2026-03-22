# CLAUDE.md - Contexto General del Proyecto Yatezzitos Global

Este documento sirve como la fuente principal de contexto y visión general para todos los agentes de IA y colaboradores del proyecto Yatezzitos Global.

## Visión General del Proyecto
Yatezzitos Global es una plataforma de turismo náutico privado de lujo con 8 años de operación en más de 10 destinos de México. Nuestro objetivo es ofrecer experiencias náuticas premium, conectando a clientes con una flota diversa de yates y embarcaciones.

## Stack Tecnológico Principal
- **Sitio web:** WordPress + Elementor + Houzez Theme (yatezzitos.com)
- **CRM:** GoHighLevel (gestión de leads, pipelines, cotizaciones, automatizaciones)
- **SEO:** Yoast SEO (con plugin REST API custom)
- **Integraciones:** Cloudflare Workers, Twilio, WhatsApp Business
- **Repositorio:** `yatezzitos-platform` (este)

## Destinos Operativos
Cancún, Los Cabos, Puerto Vallarta, La Paz, Mazatlán, Acapulco, Huatulco, Ixtapa-Zihuatanejo, Nuevo Vallarta / Riviera Nayarit, Playa del Carmen.

## Principios Operativos Clave
- **Lectura primero, escritura después:** Entender la tarea y el contexto antes de actuar.
- **Seguridad y privacidad:** No inventar datos, no exponer PII, no publicar secretos.
- **Colaboración:** Trabajar en ramas aisladas y usar Pull Requests para revisión.
- **Naturalidad del contenido:** Prioridad máxima a la calidad y naturalidad del lenguaje sobre la optimización técnica.

## Últimos Trabajos y Desarrollos Recientes (Actualización: 22 de marzo 2026)

Hemos estado enfocados en fortalecer la infraestructura y las capacidades operativas de Yatezzitos Global, con un énfasis particular en la integración de agentes de IA para optimizar nuestros flujos de trabajo.

### 1. Establecimiento de Instrucciones Maestras para Agentes de IA
Se han definido y documentado las "Instrucciones Maestras del Proyecto Yatezzitos Global" (`README.md`), que guían el comportamiento, las convenciones y los guardrails de seguridad para todos los agentes de codificación autónomos. Esto incluye:
- Definición de identidad y contexto del proyecto.
- Principios operativos globales y guardrails de seguridad no negociables.
- Convenciones de trabajo (ramas, commits, PRs).
- Enrutamiento de tareas y archivos de contexto específicos.
- Reglas detalladas para SEO, fichas de yates, auditoría SEO, frontend, mantenimiento y CRM.
- Estructura del repositorio y tono de comunicación.

### 2. Integración de Agentes de IA a través de WhatsApp
Una mejora clave ha sido la implementación de un flujo de trabajo que permite la creación automática de issues en GitHub directamente desde mensajes de WhatsApp. Este proceso utiliza:
- **WhatsApp → GoHighLevel (GHL) → Webhook → GitHub Issue.**
Esta integración facilita la asignación de tareas y la comunicación con los agentes de IA de manera más fluida y directa, permitiendo que las solicitudes operativas se conviertan en tareas de desarrollo o contenido de forma eficiente.

### 3. Avances en Prioridades del Proyecto (Fase 1)
Continuamos progresando en las prioridades de la Fase 1, que incluyen:
- Rediseño web (Home, Blog, Blog Details) de Figma a WordPress.
- Optimización SEO de ciudades activas y asignación de keywords a la flota.
- Organización y optimización del CRM (GoHighLevel).
- Automatización de procesos clave como cotizaciones, recibos de depósito y seguimiento.

Estos desarrollos buscan mejorar la eficiencia operativa, la calidad del contenido y la experiencia del usuario, preparando el terreno para futuras fases de expansión y optimización.