# Política de Publicación y Ejecución de Agentes IA

Este documento clarifica el rol y las limitaciones de los agentes de codificación de Yatezzitos Global en cuanto a la publicación de contenido y la ejecución de acciones en sistemas de producción.

## Principio Fundamental

Los agentes IA de Yatezzitos Global operan bajo un modelo de **asistencia y preparación**, no de **ejecución directa** en entornos de producción. Su función principal es generar borradores, análisis, recomendaciones y código, que luego deben ser revisados y aprobados por un humano antes de ser publicados o ejecutados.

## 1. Generación de Contenido (SEO, Fichas de Yates, Documentación)

### 1.1 Rol del Agente IA
- **Generar:** Crear borradores de artículos de blog, descripciones de yates, contenido SEO, documentación técnica, etc., siguiendo las reglas y formatos especificados (ej. HTML para WordPress, Markdown para documentación).
- **Optimizar:** Aplicar reglas SEO (keywords, legibilidad, enlaces) y de formato.
- **Organizar:** Guardar el contenido generado en las rutas de repositorio designadas (ej. `docs/seo/{ciudad}/`).

### 1.2 Limitaciones de Publicación
- **Prohibido:** Los agentes IA **NO DEBEN** publicar contenido directamente en el sitio web de WordPress (yatezzitos.com) ni en ninguna otra plataforma de producción.
- **Proceso:** El contenido generado se entrega en el repositorio como un archivo listo para ser "copiar-y-pegar" por un editor humano en WordPress, o para ser revisado y fusionado en el caso de la documentación.
- **Estado de Borrador:** Todo contenido generado para publicación externa (ej. fichas de yates) debe ser marcado explícitamente como `draft` o borrador.

## 2. Acciones en CRM (GoHighLevel)

### 2.1 Rol del Agente IA
- **Analizar:** Escanear pipelines, identificar oportunidades (ej. leads fríos, leads de alta prioridad).
- **Redactar:** Generar borradores de mensajes (SMS, email) para seguimiento de leads o notas internas.
- **Clasificar:** Aplicar *lead scoring* y sugerir movimientos de leads entre etapas del pipeline.

### 2.2 Limitaciones de Ejecución
- **Prohibido:** Los agentes IA **NO DEBEN** enviar mensajes directos a clientes o propietarios a través de GoHighLevel, ni ejecutar cobros, reembolsos o cancelaciones.
- **Proceso:** Los mensajes generados se guardan como "Borradores de SMS/Email" o "Notas Internas" en el perfil del contacto en GHL. Las sugerencias de clasificación o movimiento de leads se comunican al equipo humano para su aprobación y ejecución manual.
- **Alertas:** La IA puede generar alertas internas (ej. por WhatsApp o Slack) para el equipo comercial sobre leads de alta prioridad, pero no interactúa directamente con el cliente.

## 3. Modificaciones de Código y Mantenimiento

### 3.1 Rol del Agente IA
- **Desarrollar:** Escribir código para nuevas funcionalidades, correcciones de bugs, mejoras de UI/UX.
- **Documentar:** Crear o actualizar documentación técnica.
- **Recomendar:** Sugerir optimizaciones SEO o mejoras de rendimiento.

### 3.2 Limitaciones de Despliegue
- **Prohibido:** Los agentes IA **NO DEBEN** hacer push directo a la rama `main` ni desplegar cambios directamente en entornos de producción.
- **Proceso:** Todo cambio de código debe realizarse en una rama aislada, seguido de un Pull Request que será revisado y aprobado por un desarrollador humano antes de su fusión y despliegue.

## Conclusión

Esta política asegura que todas las acciones que impactan directamente a clientes, propietarios o la infraestructura de producción pasen por un filtro de revisión humana, manteniendo la calidad, la seguridad y el tono de marca de Yatezzitos Global.
