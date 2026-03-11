# Stack actual

## Resumen
Actualmente, Yatezzitos opera sobre una base tecnológica funcional pero todavía en evolución.

La operación digital actual combina una web construida en WordPress con un CRM en GoHighLevel, automatizaciones parciales, integraciones vía webhooks y una capa inicial de personalización técnica desarrollada para resolver necesidades específicas del negocio.

## Componentes principales del stack actual

### 1. WordPress
WordPress es la plataforma principal del sitio web actual.

Hoy funciona como:
- sitio comercial,
- base SEO,
- estructura de páginas,
- fichas de embarcaciones,
- páginas informativas,
- algunas páginas dinámicas relacionadas con cotizaciones y reservas.

### 2. Elementor
Elementor es el constructor visual principal del sitio.

Se utiliza para:
- diseño y edición de páginas,
- construcción de secciones visuales,
- personalización de contenido sin depender totalmente de código,
- implementación progresiva del rediseño basado en Figma.

### 3. Tema Houzez
El sitio actual está construido sobre el tema **Houzez**, originalmente pensado para bienes raíces, pero adaptado al negocio de renta de yates.

Actualmente, Houzez aporta:
- estructura de listings,
- paneles base,
- formularios y lógica base de propiedades,
- módulos que pueden reutilizarse para embarcaciones,
- funciones útiles como paneles, documentos y analítica interna.

Limitación importante:
Houzez no fue creado específicamente para turismo náutico, por lo que muchas partes requieren personalización o adaptación.

### 4. GoHighLevel
GoHighLevel es actualmente el **centro operativo del CRM**.

Hoy se usa para:
- almacenar contactos,
- centralizar la base de datos comercial,
- manejar automatizaciones,
- enviar correos,
- enviar WhatsApp,
- enviar SMS,
- formularios conectados al sitio,
- seguimiento comercial y operativo.

## Integraciones actuales relevantes

### 1. Twilio
Twilio se utiliza para la verificación OTP del buscador principal del sitio.

Esto permite validar usuarios antes de entregar ciertos resultados y ayuda a filtrar mejor leads calificados.

### 2. Webhooks
Se utilizan webhooks para comunicar formularios, flujos y automatizaciones entre WordPress, GoHighLevel y otros componentes del sistema.

### 3. Endpoints / lógica personalizada
Actualmente existen desarrollos personalizados para mostrar información dinámica de:
- cotizaciones,
- reservas,
- páginas de gracias,
- páginas con información personalizada del cliente mediante tokens o parámetros.

Estas implementaciones permiten que WordPress lea información del CRM y la muestre en páginas específicas del sitio.

## Flujo comercial actual resumido
El flujo actual funciona, pero todavía depende parcialmente del trabajo manual del equipo.

Hoy el proceso incluye:

1. envío de cotización,
2. solicitud de anticipo del 50%,
3. generación o envío del recibo / reserva,
4. seguimiento comercial y operativo.

Parte del proceso ya está automatizado, pero algunos pasos todavía requieren que un miembro del equipo llene formularios o dispare acciones manuales.

## Qué sí funciona hoy
Actualmente sí funcionan estas piezas del sistema:

- sitio web comercial activo,
- páginas de embarcaciones,
- posicionamiento SEO activo,
- CRM operativo en GoHighLevel,
- automatizaciones parciales,
- buscador con OTP,
- flujo de cotización,
- flujo de reserva con anticipo,
- páginas dinámicas de cotización / reserva,
- integración básica entre web y CRM.

## Principales limitaciones actuales
Aunque la base actual funciona, todavía hay limitaciones importantes:

- WordPress está muy cargado y requiere orden,
- el diseño todavía no está completamente actualizado,
- el CRM necesita mejor estructura,
- propietarios y clientes no están organizados idealmente,
- la disponibilidad en tiempo real todavía no existe de forma sólida,
- el onboarding de propietarios no está resuelto correctamente,
- varios procesos siguen siendo manuales,
- la arquitectura actual necesita mayor claridad documental.

## Decisión estratégica actual
Por ahora, este stack **no se reemplaza de golpe**.

La decisión es:
- mantener WordPress vivo,
- mantener GoHighLevel como CRM principal,
- seguir usando la operación actual,
- y construir de forma progresiva una arquitectura más sólida, sin romper el negocio existente.

## Rol de este stack en la transición
El stack actual es la base operativa sobre la que se construirá la siguiente etapa del proyecto.

En lugar de destruir lo existente, la estrategia es:
- documentarlo,
- ordenarlo,
- optimizarlo,
- y usarlo como puente hacia la futura web app, la futura app móvil y la integración más profunda con inteligencia artificial.

## Conclusión
El stack actual de Yatezzitos ya permite operar, vender, cotizar y captar clientes, pero todavía necesita orden, documentación, automatización y evolución técnica para convertirse en una plataforma verdaderamente escalable a nivel internacional.