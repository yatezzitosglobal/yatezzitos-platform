# Contexto General del Proyecto Yatezzitos Global

Este archivo provee un contexto de alto nivel sobre Yatezzitos Global, su misión, stack tecnológico y flujos operativos clave.

## 1. Identidad y Misión
Yatezzitos Global es una plataforma de turismo náutico privado de lujo con 8 años de operación, activa en 10+ destinos de México. Nuestra misión es ofrecer experiencias náuticas premium y facilitar la gestión de flotas para propietarios.

## 2. Stack Tecnológico Principal
-   **Sitio web:** WordPress + Elementor + Houzez Theme (yatezzitos.com)
-   **CRM:** GoHighLevel (leads, pipelines, cotizaciones, automatizaciones)
-   **SEO:** Yoast SEO (plugin REST API custom: `plugins/yatezzitos-yoast-rest-api/`)
-   **Integraciones:** Cloudflare Workers, Twilio, WhatsApp Business
-   **Repositorio:** Este (`yatezzitos-platform`)
-   **Idioma principal:** Español (México)

## 3. Destinos Operativos
Cancún, Los Cabos, Puerto Vallarta, La Paz, Mazatlán, Acapulco, Huatulco, Ixtapa-Zihuatanejo, Nuevo Vallarta / Riviera Nayarit, Playa del Carmen.

## 4. Flujos de Trabajo y Automatizaciones Clave

### 4.1. Gestión de Tareas vía WhatsApp
Hemos implementado un flujo automatizado que permite la creación de issues en GitHub directamente desde mensajes de WhatsApp. Este proceso se realiza a través de GoHighLevel (que recibe el mensaje de WhatsApp) y un webhook que lo transforma en un issue de GitHub.

**Propósito:** Agilizar la asignación de tareas al equipo de desarrollo y a los agentes IA, especialmente para solicitudes rápidas o urgentes que surgen en la operación diaria.

**Identificación:** Los issues generados de esta forma llevarán automáticamente la etiqueta `from-whatsapp`.