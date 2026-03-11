# Estado actual del CRM

## Resumen
Actualmente, el centro operativo del CRM de Yatezzitos está en **GoHighLevel**.

GoHighLevel concentra gran parte de la operación comercial, automatizada y documental del negocio. No funciona solo como una libreta de contactos, sino como una base operativa que hoy participa en:

- captación de leads,
- seguimiento comercial,
- envío de cotizaciones,
- seguimiento de reservas,
- automatizaciones por WhatsApp, SMS y correo,
- formularios,
- webhooks,
- páginas dinámicas de cotización y reserva,
- y manejo de campos personalizados para documentos, pagos, reservas y usuarios.

Aunque ya existe una base funcional real, el sistema todavía necesita más orden, mejor segmentación, mejor estructura de datos y una separación más clara entre clientes, socios comerciales, propietarios, reservas y operación interna.

---

## Rol actual de GoHighLevel
GoHighLevel funciona hoy como el **núcleo operativo del CRM** para Yatezzitos.

Se utiliza para:
- almacenar contactos,
- centralizar información comercial,
- activar automatizaciones,
- enviar mensajes,
- recibir formularios,
- coordinar parte del seguimiento de ventas,
- guardar datos clave del cliente,
- sostener parte del flujo de cotización y reserva,
- y alimentar páginas dinámicas en WordPress.

También funciona como una fuente importante de datos para:
- cotizaciones,
- reservas,
- recibos de depósito,
- estatus comerciales,
- y algunos campos relacionados con usuarios de WordPress.

---

## Cómo está estructurada hoy la información del CRM

### 1. Campos estándar de contacto
Actualmente se usan campos base de contacto como:

- `first_name`
- `last_name`
- `email`
- `phone`
- `date_of_birth`
- `source`
- `type`
- `message`
- `terminos_y_condiciones`

### 2. Campos generales / general info
También existen campos generales como:

- `company_name`
- `address1`
- `city`
- `country`
- `state`
- `postal_code`
- `timezone`
- `areadepartamento`

### 3. Carpeta: Recibo de deposito
Esta carpeta reúne campos clave para la operación de la reserva y el recibo de depósito, entre ellos:

- `duration_hours`
- `yacht_name`
- `departure_time`
- `return_time`
- `number_of_passengers`
- `google_maps_link`
- `marina_name`
- `deposit_amount`
- `balance_due`
- `servicios_adicionales_incluidos`
- `experiencia_reservada`
- `caracteristicas_y_amenidades_del_yate`
- `total_cost`
- `fecha_de_viaje`
- `id_frente`
- `id_reverso`
- `selfie_sosteniendo_tu_id`
- `firma_de_pago_con_tarjeta`
- `destinos`
- `deposit_admin`
- `inclusiones_adicionales`

Estos campos permiten llenar automáticamente páginas, recibos, reservas y elementos del flujo comercial.

### 4. Carpeta: Cotizaciones enviadas
Actualmente existen campos para manejar la cotización y su representación en web:

- `imagen_principal_del_yate_upload`
- `quote_token`
- `quote_data`
- `quote_url`
- `url_del_yate`
- `fecha_de_compromiso_de_pago`
- `status_cotizacion`

### 5. Carpeta: Datos de reserva
Hoy también existe una estructura específica para reservas:

- `estado_de_la_reserva`
- `payment_method`
- `reservacion_id`
- `reservacion_url`
- `captura_de_pantalla_de_la_transferencia`

### 6. Carpeta: User Wordpress
Existen además campos pensados para usuarios y operación relacionada con WordPress:

- `rol_de_usuario`
- `user_name`
- `cargo_dentro_de_la_empresa`
- `licencia`
- `whatsapp`
- `clave_rfc_impuesto`
- `telefono`
- `numero_de_fax`
- `idioma`
- `nombre_de_la_empresa`
- `direccion`
- `areas_de_servicio`
- `especialidades`
- `mensaje_sobre_mi`
- `password`
- `mensaje_wordpress`

Esto confirma que el CRM actual ya está siendo usado también como apoyo para lógica de usuarios, perfiles o integraciones relacionadas con WordPress.

---

## Integración actual entre web y CRM
La web actual en WordPress sí tiene conexión con el CRM, aunque todavía no de forma completamente ordenada.

Actualmente existen integraciones como:

- formularios conectados a GoHighLevel,
- webhooks que envían información entre sistemas,
- páginas dinámicas que leen datos de contactos,
- páginas personalizadas para cotización, gracias y reserva,
- integración con OTP mediante Twilio en el buscador principal.

También existe una lógica personalizada para que WordPress lea datos del CRM y los muestre en páginas específicas mediante tokens o parámetros.

Esto convierte a GoHighLevel no solo en CRM, sino en una fuente de datos que alimenta experiencias visibles para el cliente.

---

## Pipeline principal de turistas

### Nombre del pipeline
**renta de yates**

Este pipeline organiza hoy el proceso comercial principal de clientes turistas interesados en rentar una embarcación.

### Etapas actuales del pipeline

#### 1. Bienvenidos a bordo
Es la etapa de lead nuevo.

Todo lead nuevo entra primero aquí.

#### 2. Cotización enviada
El lead pasa a esta etapa cuando el equipo de ventas llena la encuesta interna y envía la cotización al cliente.

Es decir, el cambio no ocurre automáticamente por intención del cliente, sino por una acción del asesor al completar el flujo interno de cotización.

#### 3. Recibos pendientes
Esta etapa representa clientes que ya pagaron el anticipo / ya reservaron, pero todavía no han recibido su recibo de depósito por parte del equipo de ventas.

#### 4. Envío de métodos de pago
Se usa cuando el cliente ya quiere completar su reserva y solicita pagar con un método de pago específico.

Está antes de la confirmación de la reserva.

#### 5. Ganada - El cliente ha pagado
Aquí entra el cliente cuando ya pagó el anticipo correspondiente a la reserva.

En la lógica actual del negocio, normalmente:
- el cliente paga el 50% para reservar,
- y el otro 50% suele liquidarse a bordo.

#### 6. En espera - Prórroga
Esta etapa se usa para clientes a quienes ya se les envió cotización, pero todavía no reservan y su fecha de viaje aún no ha pasado.

Son leads que siguen vivos comercialmente.

#### 7. Pérdidas - No realizadas
Aquí se mueven los clientes cuya fecha de viaje ya pasó y no reservaron.

### Etapa futura deseada
Se desea agregar una etapa nueva llamada:

#### 8. Feedback
Esta etapa serviría para mover a clientes que sí realizaron su viaje y luego pedirles:
- reseña,
- calificación,
- feedback,
- y potencialmente testimonios o contenido.

La recomendación actual es crear esta etapa dentro del mismo pipeline, no en uno separado.

---

## Pipeline de propietarios
Además del pipeline principal de turistas, también existe un pipeline distinto para captación de propietarios / socios comerciales.

En la evidencia actual se observa al menos una estructura de captación de propietarios con etapas como:

- bienvenida por canal de entrada,
- encuesta enviada,
- no contestó llamada / seguimiento,
- en espera,
- no interesado,
- subido al sitio web.

Esto confirma que el CRM actual ya maneja por lo menos dos lógicas de embudo:
- una para turistas,
- y otra para propietarios o captación de embarcaciones.

---

## Qué sí está automatizado hoy
Actualmente sí existen automatizaciones útiles, especialmente en procesos como:

- envío de cotización,
- envío de parte de la información de reserva,
- flujo relacionado con el anticipo del 50%,
- uso de formularios para disparar acciones,
- envío de información al cliente cuando el equipo llena ciertos datos,
- lectura de datos desde WordPress para páginas personalizadas.

---

## Qué sigue siendo manual hoy
A pesar de la automatización parcial, todavía hay una dependencia fuerte del trabajo manual del equipo.

Hoy siguen siendo manuales o parcialmente manuales cosas como:

- llenado de encuestas internas para disparar cotizaciones,
- llenado de formularios internos para generar recibos o reservas,
- parte del seguimiento operativo,
- organización de propietarios,
- organización de embarcaciones,
- validación de datos,
- captura de nueva oferta,
- parte del control de pagos y confirmaciones,
- transición correcta entre algunas etapas del pipeline.

---

## Problemas actuales del CRM

### 1. Desorden en la base de datos
Clientes, propietarios, socios comerciales y otros perfiles no siempre están estructurados de forma ideal.

### 2. Falta de separación clara de perfiles
Aún no existe una estructura suficientemente limpia para distinguir y operar correctamente entre:
- clientes turistas,
- propietarios,
- brokers,
- administradores,
- capitanes,
- agencias,
- aliados.

### 3. Dependencia de procesos manuales
Muchos procesos importantes siguen dependiendo de formularios internos o acciones repetitivas del equipo.

### 4. Integración incompleta con WordPress
Aunque ya existe conexión entre WordPress y GoHighLevel, todavía no está resuelta de forma sólida para:
- cuentas de usuario,
- disponibilidad,
- onboarding de propietarios,
- estructura ordenada de embarcaciones,
- operación más avanzada.

### 5. Estructura valiosa, pero todavía dispersa
El CRM ya contiene mucha información útil y campos personalizados bien pensados, pero la operación general todavía no aprovecha toda esa estructura con el orden y la claridad necesarios.

---

## Qué está funcionando bien
Actualmente sí están funcionando estas bases:

- GoHighLevel como núcleo operativo,
- pipeline comercial activo para turistas,
- pipeline de captación de propietarios,
- automatizaciones parciales útiles,
- formularios y secuencias conectadas,
- flujo comercial funcional,
- integración básica entre CRM y sitio,
- almacenamiento de contactos,
- páginas dinámicas alimentadas por el CRM,
- seguimiento de clientes reales.

---

## Qué necesita mejorar con urgencia
Las prioridades de mejora del CRM son:

- ordenar mejor la base de datos,
- separar correctamente perfiles,
- automatizar más pasos del embudo,
- organizar propietarios y embarcaciones,
- mejorar secuencias y seguimiento,
- reducir trabajo manual,
- mejorar conexión entre CRM y sitio web,
- aprovechar mejor los custom fields ya existentes,
- preparar el sistema para disponibilidad en tiempo real,
- preparar la futura web app,
- y agregar una etapa de feedback post-viaje.

---

## Decisión estratégica actual
GoHighLevel no se va a reemplazar de inmediato.

La estrategia correcta es:
- documentar mejor el estado actual,
- ordenarlo,
- estructurarlo mejor,
- automatizar lo que falta,
- y usarlo como base operativa mientras la arquitectura evoluciona.

---

## Rol del CRM en la siguiente etapa
En la siguiente etapa del proyecto, el CRM debe convertirse en una operación mucho más clara para tres grupos:

### 1. Clientes
Para vender mejor, dar seguimiento, automatizar y acompañar la experiencia.

### 2. Socios comerciales
Para captar y ordenar propietarios, agencias, brokers y oferta disponible.

### 3. Equipo interno
Para operar mejor, reducir errores, aumentar productividad y tener mejor visibilidad del estado real de cada proceso.

---

## Conclusión
El CRM actual de Yatezzitos ya sostiene una parte importante del negocio y ya contiene una estructura de campos personalizados mucho más rica de lo que aparenta a simple vista.

Sin embargo, todavía necesita:
- orden estructural,
- mejor segmentación,
- más automatización,
- mejor integración con la web,
- y una arquitectura más clara entre clientes, reservas, propietarios y operación interna.

Su estado actual ya es funcional, pero todavía no está optimizado para la escala internacional que busca Yatezzitos Global.