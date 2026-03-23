# Guía de Integración y Casos de Uso: GoHighLevel (GHL) MCP Server

Este documento sirve como la **fuente de verdad canónica** sobre la integración del Agente Anti-Gravity (y ecosistema de Yatezzitos) con GoHighLevel mediante el Model Context Protocol (MCP). Detalla las herramientas disponibles y la investigación de beneficios corporativos tras la conexión exitosa en Marzo de 2026.

## 1. Resumen de la Arquitectura de Conexión

La integración con GoHighLevel se construyó bajo un enfoque de **"Zero Trust"** (Cero Confianza), utilizando una imagen de Docker (`ghl-mcp-server`) ejecutada estrictamente de manera local.
- **Acceso a Claves:** Gestionadas aislando el archivo `.env.ghl-mcp` del control de versiones.
- **Protocolos Activos:** `stdio` nativo para comunicación interna rápida con Anti-Gravity, y `SSE` (Server-Sent Events) en el puerto `8000` para futuras expansiones y front-ends.

## 2. Inventario de Poderes Desbloqueados (253 Herramientas MCP)

La conexión exitosa extrajo un inventario autorizado de **253 herramientas** operativas bidireccionales en tiempo real. 

### 📇 Gestión de Contactos (31 Herramientas)
Búsqueda, creación, y deduplicación inteligente de contactos.
- **Beneficio Yatezzitos:** Si un cliente ingresa un lead por WhatsApp o Facebook, los agentes IA podrán consultar la base de datos de GHL para ver si es un turista recurrente o un prospecto nuevo, pre-cargando su historial y asistiéndole con un contexto hiper-personalizado que aumenta la probabilidad de conversión. También pueden etiquetar prospectos como "VIP" de forma autónoma.

### 💬 Mensajería y Conversaciones (20 Herramientas)
Manejo multicanal (SMS, Email), historial de chat y estado de lectura.
- **Beneficio Yatezzitos:** Los agentes IA pueden revisar si un cliente leyó nuestro correo con el "boarding pass", o si ignoró un SMS. Adicionalmente, pueden programar alertas y mensajes de confirmación horas previas al zarpe o enviar la ubicación exacta de la Marina por SMS desde GHL de forma automatizada.

### 💰 Gestión de Oportunidades y Pipelines (10 Herramientas)
Lectura y escritura en el tablero de ventas kanban (ej: Pipeline "Bienvenida - Facebook").
- **Beneficio Yatezzitos:** Un chatbot inteligente puede clasificar la "temperatura" de un cliente conversando en WhatsApp y arrastrar su nombre autónomamente desde "Lead Nuevo" hacia "Cotización Enviada" o "Pagado" en el CRM, manteniendo limpio e impecable el dashboard del departamento de Ventas en GHL sin intervención humana.

### 🗓 Calendarios y Reservas (39 Herramientas)
Bloqueo de horarios, creación de citas, validación de franjas libres.
- **Beneficio Yatezzitos:** Un IA "Concierge" puede ver qué horarios tiene bloqueado un yate, sugerir franjas al cliente por chat, y **efectuar la reserva en GHL de inmediato**. Si de pronto llueve o falla una máquina, la IA puede buscar clientes agendados y cancelar automáticamente sus citas, avisando vía SMS.

### 📝 Blogs, SEO y Redes Sociales (24 Herramientas Combinadas)
Publicar y editar artículos (`create_blog_post`), actualizar posteos en redes sociales (IG, FB, TikTok).
- **Beneficio Yatezzitos:** Gracias a la herramienta "blog management", la IA puede redactar descripciones largas de destinos siguiendo lineamientos Yoast SEO, e inyectarlas directo al CRM GHL para que se publiquen automáticamente con sus slugs validados, optimizando el ecosistema SEO de la plataforna de inmediato.

### 💳 Pagos y Facturación (38 Herramientas Combinadas)
Cobros recurrentes, comprobantes, integraciones bancarias e Invoices.
- **Beneficio Yatezzitos:** Al recibir reportes de una transferencia, la IA puede validar dentro de GoHighLevel el pago en el Invoice y emitir un recibo o facturar de manera autónoma al turista, acortando el ciclo de cierre administrativo a tan solo minutos en lugar de días laborables. Todo sin exponer datos de las tarjetas directamente.

## 3. Retorno de Inversión y Eficiencia Operativa Esperada

Al tener Anti-Gravity conectado como **Agente Co-piloto de Yatezzitos**, las tareas operativas rutinarias se eliminarán del cuello de botella humano:
1. **Reducción del TMO (Tiempo Medio de Operación):** La cualificación de leads de Facebook o WhatsApp dejará de ser manual.
2. **Centralización Documental:** Ninguna cita doble o error en pipelines por fatiga del intermediario ocurrirá de nuevo.
3. **Escalabilidad Inmediata:** Ya sean 100 reservaciones en temporada alta o 1 en baja, la conexión de lectura/escritura de MCP responderá a la misma velocidad sin contratar personal operativo extra.

## Conclusión

La suma de estas herramientas rompe el silo de datos en la compañía. Yatezzitos ahora posee una **arquitectura autónoma inteligente**, donde el contexto no vive en la cabeza de los empleados, sino que navega fluidamente a través de GoHighLevel bajo el comando y la asistencia predictiva de Agentes de IA regidos por estrictas reglas de negocio (`AGENTS.md`).
