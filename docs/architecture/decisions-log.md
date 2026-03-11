# Registro de decisiones técnicas y estratégicas

Este documento registra decisiones importantes del proyecto **Yatezzitos Global**.

Su objetivo es:
- conservar contexto,
- evitar confusión,
- documentar por qué se eligieron ciertas rutas,
- alinear negocio, tecnología, SEO, CRM, automatización e IA,
- y evitar que en el futuro se tomen decisiones que contradigan la estrategia principal.

---

# Cómo leer este documento

Cada decisión incluye:
- **ID**
- **Estado**
- **Categoría**
- **Decisión**
- **Motivo**
- **Implicación**

Estados posibles:
- **Aprobada**
- **Vigente**
- **Pendiente**
- **Por revisar**
- **Reemplazada**

---

## DEC-001 — Mantener WordPress como parte activa del ecosistema
**Estado:** Aprobada  
**Categoría:** Arquitectura / Plataforma

### Decisión
WordPress seguirá vivo como capa comercial, editorial y SEO del ecosistema Yatezzitos.

### Motivo
La web actual ya opera, ya genera tráfico, ya tiene posicionamiento, ya sostiene páginas indexadas y ya forma parte importante del sistema comercial.

### Implicación
No se hará una migración total inmediata fuera de WordPress.

---

## DEC-002 — Construir la nueva capa operativa fuera de WordPress
**Estado:** Aprobada  
**Categoría:** Arquitectura / Producto

### Decisión
La futura web app operativa se construirá separada de WordPress.

### Motivo
WordPress es útil para SEO y marketing, pero no es la mejor base para escalar de forma limpia:
- reservas,
- disponibilidad,
- paneles,
- automatización operativa,
- cuentas de usuario,
- lógica de producto más compleja.

### Implicación
La arquitectura futura será híbrida:
- WordPress para SEO y marketing,
- web app para operación y producto.

---

## DEC-003 — Mantener GoHighLevel como CRM principal por ahora
**Estado:** Aprobada  
**Categoría:** CRM / Operación

### Decisión
GoHighLevel seguirá siendo el centro operativo del CRM en esta etapa.

### Motivo
Ya concentra:
- automatizaciones,
- contactos,
- formularios,
- mensajes,
- secuencias,
- parte importante de la operación comercial.

### Implicación
No se reemplazará de inmediato.  
La estrategia será integrarlo mejor mientras el sistema evoluciona.

---

## DEC-004 — No romper producción
**Estado:** Aprobada  
**Categoría:** Seguridad / Operación

### Decisión
No se deben hacer cambios que rompan:
- la operación actual del sitio,
- el SEO,
- los formularios,
- las automatizaciones,
- el flujo comercial,
- o la experiencia del cliente.

### Motivo
El negocio ya está funcionando y no conviene sacrificar ventas ni posicionamiento por cambios bruscos.

### Implicación
Primero se documenta, luego se prueba y después se publica.

---

## DEC-005 — Primero pruebas, luego producción
**Estado:** Aprobada  
**Categoría:** Operación / Calidad

### Decisión
Toda mejora importante deberá probarse antes de aplicarse en el sitio en vivo.

### Motivo
El sitio actual combina:
- diseño,
- SEO,
- formularios,
- webhooks,
- CRM,
- lógica comercial.

Un cambio incorrecto puede afectar demasiadas áreas al mismo tiempo.

### Implicación
Se recomienda trabajar con entornos de pruebas o staging antes de tocar producción.

---

## DEC-006 — GitHub será el centro documental y técnico del proyecto
**Estado:** Aprobada  
**Categoría:** Organización / Repositorio

### Decisión
El repositorio de GitHub será la fuente principal de:
- documentación técnica,
- decisiones,
- backlog,
- estructura del proyecto,
- y código versionado.

### Motivo
El proyecto necesita orden, claridad y una base que permita crecer con más colaboradores en el futuro.

### Implicación
La carpeta `docs/` es prioritaria antes de escalar el código.

---

## DEC-007 — Documentar antes de escalar
**Estado:** Aprobada  
**Categoría:** Organización / Estrategia

### Decisión
Antes de hacer cambios grandes en código, automatizaciones o arquitectura, se deberá documentar:
- visión,
- prioridades,
- stack,
- CRM,
- SEO,
- backlog,
- decisiones clave.

### Motivo
Actualmente existe desorden en varias áreas y escalar sin documentación aumentaría la complejidad.

### Implicación
La primera fase del proyecto es orden documental.

---

## DEC-008 — Priorizar marketplace, propietarios y disponibilidad
**Estado:** Aprobada  
**Categoría:** Producto / Roadmap

### Decisión
La prioridad funcional del producto será:
1. marketplace de yates,
2. panel de propietarios,
3. calendario de disponibilidad en tiempo real,
4. motor de reservas,
5. concierge / asistente inteligente,
6. cuenta de clientes y panel interno,
7. app móvil más adelante.

### Motivo
Estos módulos son los que más impacto tendrán en:
- reservas,
- control operativo,
- captación de nuevos socios comerciales,
- expansión.

### Implicación
El roadmap y backlog deben seguir esta prioridad.

---

## DEC-009 — Yatezzitos IA será una capa transversal
**Estado:** Aprobada  
**Categoría:** IA / Arquitectura

### Decisión
La inteligencia artificial no será un módulo aislado, sino una capa transversal del ecosistema.

### Motivo
La IA debe apoyar:
- atención al cliente,
- asistencia a propietarios,
- marketing,
- SEO,
- desarrollo,
- diseño,
- operaciones,
- automatización comercial.

### Implicación
La IA deberá integrarse en múltiples flujos, no solo en un chatbot.

---

## DEC-010 — Mantener privada la estrategia interna de agentes
**Estado:** Aprobada  
**Categoría:** IA / Seguridad / Operación

### Decisión
La estrategia detallada de agentes, flujos internos y automatización avanzada no se documentará públicamente en README ni en materiales externos.

### Motivo
Se trata de una ventaja operativa y estratégica del proyecto.

### Implicación
Ese detalle se manejará en documentación interna controlada.

---

## DEC-011 — El SEO es estructural, no secundario
**Estado:** Aprobada  
**Categoría:** SEO / Crecimiento

### Decisión
SEO será uno de los pilares centrales del proyecto.

### Motivo
Yatezzitos busca expandirse por:
- países,
- ciudades,
- embarcaciones,
- experiencias,
- páginas comerciales orientadas a intención de compra.

### Implicación
WordPress y la arquitectura de contenido seguirán siendo estratégicos.

---

## DEC-012 — La expansión será progresiva y sin perder orden
**Estado:** Aprobada  
**Categoría:** Expansión / Operación

### Decisión
La expansión internacional no se hará solo por presencia visual, sino cuando existan mejores bases operativas, SEO, captación de propietarios y oferta confiable.

### Motivo
Expandirse sin orden puede dañar la reputación y la operación.

### Implicación
Primero se consolida el sistema, luego se escala geográficamente.

---

## DEC-013 — Seguridad documental y del repositorio
**Estado:** Aprobada  
**Categoría:** Seguridad / Repositorio

### Decisión
No se deben subir al repositorio:
- credenciales,
- llaves API,
- tokens,
- accesos,
- bases de datos sensibles,
- respaldos completos,
- información privada de clientes.

### Motivo
El proyecto maneja información comercial y operativa sensible.

### Implicación
La seguridad del repositorio es una regla base.

---

## DEC-014 — El producto debe servir tanto al turista como al socio comercial
**Estado:** Aprobada  
**Categoría:** Producto / Modelo de negocio

### Decisión
La plataforma futura no solo atenderá turistas, sino también:
- propietarios,
- administradores,
- brokers,
- agencias,
- capitanes,
- aliados.

### Motivo
La expansión del negocio depende tanto de la demanda como de la calidad y orden de la oferta.

### Implicación
La arquitectura debe considerar ambos lados del mercado.

---

## DEC-015 — El proyecto se trabajará con Scrum
**Estado:** Aprobada  
**Categoría:** Organización / Metodología

### Decisión
La organización del trabajo seguirá un enfoque Scrum con:
- backlog,
- sprints,
- prioridades,
- tareas claras,
- seguimiento por fases.

### Motivo
El proyecto tiene muchas líneas simultáneas y necesita orden para avanzar sin caos.

### Implicación
La carpeta `docs/scrum/` será parte fundamental del repositorio.

---

## DEC-016 — Primero optimizar lo existente, luego diversificar
**Estado:** Aprobada  
**Categoría:** Estrategia / Negocio

### Decisión
Antes de convertir Yatezzitos en:
- software para propietarios,
- SaaS más amplio,
- membresía vacacional,
- escuela digital,

primero se optimizará el sistema actual de renta de yates.

### Motivo
El negocio principal debe estar más sólido antes de expandirse a nuevas líneas.

### Implicación
La fase actual sigue enfocada en:
- reservas,
- SEO,
- propietarios,
- CRM,
- automatización.

---

## DEC-017 — Mantener la marca técnica bajo el nombre Yatezzitos Global
**Estado:** Aprobada  
**Categoría:** Marca / Organización

### Decisión
El nombre oficial del proyecto técnico y del repositorio será **Yatezzitos Global**.

### Motivo
Refleja la visión internacional del proyecto y ordena la identidad del ecosistema digital.

### Implicación
La documentación técnica debe usar este nombre como referencia principal del proyecto.

---

## DEC-018 — La marca madre sigue siendo Yatezzitos
**Estado:** Aprobada  
**Categoría:** Marca

### Decisión
La marca comercial principal sigue siendo **Yatezzitos**.

### Motivo
Es la marca reconocida por el mercado actual y la que sostiene la operación comercial.

### Implicación
El ecosistema se organiza así:
- marca principal: **Yatezzitos**
- proyecto técnico: **Yatezzitos Global**
- capa de IA: **Yatezzitos IA**

---

## DEC-019 — Yatezzitos IA será el nombre oficial de la capa de inteligencia artificial
**Estado:** Aprobada  
**Categoría:** IA / Marca

### Decisión
La capa de inteligencia artificial del proyecto se llamará **Yatezzitos IA**.

### Motivo
Permite integrar la IA dentro de la marca sin volverla un producto desconectado del negocio.

### Implicación
Toda documentación futura sobre IA debe usar ese nombre.

---

## DEC-020 — El tono de la marca será premium turística con componente startup
**Estado:** Aprobada  
**Categoría:** Marca / Comunicación

### Decisión
La comunicación de marca seguirá una mezcla aproximada de:
- 70% premium turística,
- 30% startup tecnológica.

### Motivo
Yatezzitos necesita comunicar lujo, confianza y exclusividad, sin perder visión moderna, orden tecnológico y ambición de crecimiento.

### Implicación
La documentación, el copy y la UX writing deben reflejar ese balance.

---

## DEC-021 — Los atributos dominantes de la marca serán premium, tecnológica y visionaria
**Estado:** Aprobada  
**Categoría:** Marca

### Decisión
Los atributos principales de la marca serán:
- premium,
- tecnológica,
- visionaria.

### Motivo
Es la combinación que mejor representa el posicionamiento deseado de Yatezzitos.

### Implicación
Los materiales del proyecto deben reforzar estas tres percepciones.

---

## DEC-022 — La experiencia del cliente debe sentirse privada, clara y confiable
**Estado:** Aprobada  
**Categoría:** Experiencia / Producto

### Decisión
La experiencia del cliente debe diseñarse para sentirse:
- privada,
- clara,
- confiable,
- bien acompañada,
- con mínima fricción.

### Motivo
El principal problema que resuelve Yatezzitos es la desconfianza y el desorden en la reserva de experiencias náuticas privadas.

### Implicación
Toda decisión de UX, contenido, automatización y atención debe apoyar esa promesa.

---

## DEC-023 — La atención personalizada sigue siendo una ventaja competitiva
**Estado:** Aprobada  
**Categoría:** Operación / Marca

### Decisión
Aunque el proyecto avance hacia automatización e IA, la atención personalizada seguirá siendo una parte importante del valor diferencial.

### Motivo
El mercado de experiencias premium exige acompañamiento humano en momentos clave.

### Implicación
La automatización no debe eliminar por completo la posibilidad de apoyo humano.

---

## DEC-024 — El anticipo estándar de reserva se mantiene en 50%
**Estado:** Aprobada  
**Categoría:** Ventas / Operación / Pagos

### Decisión
La lógica comercial de reserva seguirá usando un anticipo del 50% como regla operativa principal.

### Motivo
Ese modelo ya forma parte del flujo actual y ayuda a formalizar las reservas.

### Implicación
Los sistemas de reserva, pagos, cotización y seguimiento deben respetar esta lógica.

---

## DEC-025 — El sistema actual de cotizaciones se mantiene por ahora
**Estado:** Aprobada  
**Categoría:** Producto / Ventas

### Decisión
El sistema actual de cotizaciones dinámicas se mantendrá en esta etapa.

### Motivo
Ya existe, ya funciona y todavía puede seguir siendo útil mientras se construye el ecosistema más avanzado.

### Implicación
No se rediseñará desde cero de inmediato.  
En cambio, se integrará mejor con cuentas de usuario y futuras capas del sistema.

---

## DEC-026 — La cuenta del cliente deberá integrar cotización y reserva
**Estado:** Aprobada  
**Categoría:** Producto / UX

### Decisión
En la futura web app, el cliente deberá tener una cuenta donde pueda ver:
- su cotización,
- su reserva,
- su información de viaje,
- sus pagos y estatus.

### Motivo
Hoy esa información está fragmentada y se puede mejorar la experiencia del cliente.

### Implicación
La futura arquitectura de cuentas debe contemplar esta integración desde el diseño.

---

## DEC-027 — El calendario de propietarios será una herramienta central del negocio
**Estado:** Aprobada  
**Categoría:** Producto / Operación / Oferta

### Decisión
El calendario de disponibilidad en tiempo real para propietarios será uno de los activos tecnológicos más importantes del proyecto.

### Motivo
La disponibilidad es una de las piezas más valiosas del negocio y hoy no está estructurada correctamente.

### Implicación
Este módulo deberá diseñarse como prioridad alta.

---

## DEC-028 — La disponibilidad deberá poder compartirse mediante enlace
**Estado:** Aprobada  
**Categoría:** Producto / Integraciones

### Decisión
El calendario de propietarios deberá poder compartirse mediante enlace simple.

### Motivo
Esto facilitará que vendedores, brokers y aliados comerciales vean disponibilidad sin fricción.

### Implicación
El diseño del calendario deberá contemplar permisos, enlaces compartibles y actualización en tiempo real.

---

## DEC-029 — La arquitectura futura debe permitir integraciones vía API y webhooks
**Estado:** Aprobada  
**Categoría:** Integraciones / Arquitectura

### Decisión
Los módulos nuevos deberán diseñarse para integrarse con otras herramientas mediante APIs y webhooks.

### Motivo
Yatezzitos quiere apalancarse de brokers, marketplaces y herramientas externas.

### Implicación
La arquitectura futura debe ser modular e integrable.

---

## DEC-030 — Los propietarios deben poder registrarse y subir embarcaciones de forma estructurada
**Estado:** Aprobada  
**Categoría:** Producto / Captación

### Decisión
Se construirá un flujo mejorado de onboarding para propietarios y socios comerciales.

### Motivo
Hoy la captación de propietarios es complicada y depende demasiado de procesos manuales o formularios poco optimizados.

### Implicación
Se debe priorizar una landing y formulario de captación mejor resueltos.

---

## DEC-031 — La publicación de nuevas embarcaciones requerirá revisión y autorización
**Estado:** Aprobada  
**Categoría:** Operación / Calidad / Seguridad

### Decisión
Toda nueva embarcación cargada por un propietario deberá pasar por revisión y aprobación antes de publicarse en línea.

### Motivo
Se necesita validar calidad, confiabilidad, documentación y conveniencia comercial.

### Implicación
El flujo futuro debe incluir estado de borrador, revisión y aprobación.

---

## DEC-032 — La documentación del socio comercial será importante para confianza y publicación
**Estado:** Aprobada  
**Categoría:** Operación / Verificación

### Decisión
El sistema futuro deberá facilitar la carga y verificación de documentos relevantes de embarcaciones y socios comerciales.

### Motivo
Esto es útil para:
- verificar confiabilidad,
- operar con más seguridad,
- y publicar también en marketplaces externos.

### Implicación
El flujo de onboarding deberá contemplar documentos y validaciones.

---

## DEC-033 — La IA debe priorizar primero la atención al turista
**Estado:** Aprobada  
**Categoría:** IA / Prioridades

### Decisión
La primera prioridad operativa de IA será la atención al cliente turista.

### Motivo
Es el perfil que hoy genera el ingreso principal y el que más consultas produce.

### Implicación
Los primeros asistentes deben conectarse a canales como:
- WhatsApp,
- Instagram,
- redes,
- sitio web.

---

## DEC-034 — La IA también deberá asistir a propietarios
**Estado:** Aprobada  
**Categoría:** IA / Producto

### Decisión
Después de la atención al turista, la IA deberá apoyar a propietarios y socios comerciales.

### Motivo
La expansión depende de captar y ordenar mejor la oferta.

### Implicación
La IA futura debe ayudar en onboarding, requisitos, documentación y uso del sistema.

---

## DEC-035 — La IA apoyará SEO, marketing y desarrollo interno
**Estado:** Aprobada  
**Categoría:** IA / Operación interna

### Decisión
Yatezzitos IA también tendrá funciones internas en:
- marketing,
- SEO,
- copywriting,
- diseño,
- desarrollo,
- operaciones.

### Motivo
La IA es parte central de la estrategia de aceleración del negocio.

### Implicación
El ecosistema deberá permitir usos internos y externos de IA.

---

## DEC-036 — La integración Twilio OTP actual se conserva
**Estado:** Aprobada  
**Categoría:** Integraciones / Operación

### Decisión
La integración actual de Twilio para OTP en el buscador se mantiene como parte útil del sistema.

### Motivo
Ayuda a filtrar leads calificados y forma parte del flujo actual.

### Implicación
No se elimina en esta etapa salvo que exista una sustitución clara y mejor.

---

## DEC-037 — La operación actual se apoya en WordPress + Houzez + Elementor
**Estado:** Aprobada  
**Categoría:** Stack actual

### Decisión
La operación actual seguirá apoyándose en:
- WordPress,
- Elementor,
- Houzez,
- GoHighLevel,
- webhooks y personalizaciones.

### Motivo
Es la base que hoy sostiene el negocio.

### Implicación
La evolución debe trabajar con esa realidad, no ignorarla.

---

## DEC-038 — Houzez se mantiene mientras dure la transición
**Estado:** Aprobada  
**Categoría:** Stack actual / Transición

### Decisión
El tema Houzez se mantendrá mientras se desarrolla la transición tecnológica.

### Motivo
Aunque no fue creado para turismo náutico, hoy aporta estructura útil.

### Implicación
Se seguirá personalizando mientras convenga y no rompa la operación.

---

## DEC-039 — El rediseño de Figma a WordPress es una prioridad inmediata
**Estado:** Aprobada  
**Categoría:** UI / UX / Prioridades

### Decisión
Terminar el rediseño del sitio desde Figma hacia WordPress es una prioridad alta.

### Motivo
La web actual necesita mejorar diseño, claridad y percepción de marca.

### Implicación
El backlog debe incluir esta tarea como frente principal.

---

## DEC-040 — El botón de reserva debe integrarse mejor al embudo
**Estado:** Aprobada  
**Categoría:** Conversión / Automatización

### Decisión
Cada producto o ficha de embarcación deberá evolucionar hacia una integración mejor del botón de reserva con automatizaciones y seguimiento comercial.

### Motivo
Esto puede aumentar conversión y reducir fricción comercial.

### Implicación
La automatización del flujo de reserva es prioridad.

---

## DEC-041 — El SEO crecerá por país, ciudad, embarcación y experiencia
**Estado:** Aprobada  
**Categoría:** SEO / Arquitectura de contenido

### Decisión
La estructura SEO del proyecto crecerá por:
- país,
- ciudad,
- tipo de embarcación,
- tipo de experiencia,
- páginas transaccionales por yate y keyword.

### Motivo
Esa estructura permite escalar el posicionamiento con intención comercial.

### Implicación
La documentación SEO y el roadmap de contenidos deben seguir esa arquitectura.

---

## DEC-042 — El contenido programático formará parte de la estrategia SEO
**Estado:** Aprobada  
**Categoría:** SEO / Escalabilidad

### Decisión
El proyecto sí utilizará contenido programático de forma estratégica.

### Motivo
Permite escalar páginas comerciales y de destino con estructura ordenada.

### Implicación
Debe hacerse con control de calidad, no como producción caótica de páginas.

---

## DEC-043 — El proyecto se documentará primero en español
**Estado:** Aprobada  
**Categoría:** Documentación / Idioma

### Decisión
La documentación base del proyecto se trabajará primero en español y después podrá traducirse al inglés cuando convenga.

### Motivo
El idioma natural de trabajo actual del proyecto es el español.

### Implicación
La documentación fundacional prioriza claridad interna.

---

## DEC-044 — El ecosistema futuro será multimoneda y multiidioma
**Estado:** Aprobada  
**Categoría:** Producto / Internacionalización

### Decisión
La arquitectura futura deberá contemplar:
- español e inglés,
- pesos mexicanos y dólares.

### Motivo
Es necesario para expansión y para atender mejor destinos internacionales.

### Implicación
Los nuevos módulos deben considerar internacionalización desde temprano.

---

## DEC-045 — La expansión debe apoyar la visión de México al mundo
**Estado:** Aprobada  
**Categoría:** Estrategia / Visión

### Decisión
El proyecto se construye con visión de escalar de México al mundo.

### Motivo
La ambición del negocio es convertirse en referente global del turismo náutico privado.

### Implicación
Las decisiones técnicas y de marca deben sostener una visión internacional desde ahora.

---

## DEC-046 — El repositorio es interno y confidencial
**Estado:** Aprobada  
**Categoría:** Seguridad / Organización

### Decisión
El repositorio y su documentación se consideran de uso interno y confidencial.

### Motivo
Contienen visión, estructura, procesos y decisiones sensibles del negocio.

### Implicación
No se publicará como repositorio abierto en esta etapa.

---

## DEC-047 — La documentación debe servir para futuros colaboradores
**Estado:** Aprobada  
**Categoría:** Organización / Escalabilidad

### Decisión
La documentación no debe escribirse solo para el fundador actual, sino para facilitar incorporación futura de:
- desarrolladores,
- diseñadores,
- asistentes,
- operadores,
- nuevos colaboradores.

### Motivo
El proyecto necesita poder crecer sin depender de memoria informal.

### Implicación
Los documentos deben ser claros, estructurados y accionables.

---

## DEC-048 — La automatización del CRM y bases de datos es prioridad urgente
**Estado:** Aprobada  
**Categoría:** CRM / Prioridades

### Decisión
Ordenar automatizaciones del CRM y bases de datos de clientes / propietarios es una prioridad alta.

### Motivo
Ese desorden hoy limita eficiencia, seguimiento y escalabilidad.

### Implicación
La documentación y backlog deben reflejar esta urgencia.

---

## DEC-049 — La escuela digital y el SaaS son líneas futuras, no foco inmediato
**Estado:** Aprobada  
**Categoría:** Negocio / Prioridades

### Decisión
La escuela digital y la expansión SaaS se reconocen como líneas futuras importantes, pero no son el foco principal de esta fase.

### Motivo
Primero debe optimizarse la operación central de renta de yates.

### Implicación
No deben distraer recursos críticos del roadmap inmediato.

---

## DEC-050 — La membresía vacacional es una visión futura, no un producto inmediato
**Estado:** Aprobada  
**Categoría:** Negocio / Producto futuro

### Decisión
La membresía vacacional se documenta como visión estratégica futura.

### Motivo
Es una idea poderosa, pero depende primero de consolidar:
- rentabilidad,
- cobertura geográfica,
- disponibilidad,
- operación más madura.

### Implicación
Se registra como dirección de largo plazo, no como objetivo inmediato de construcción.

---

## DEC-051 — La reputación y la confiabilidad son más importantes que crecer rápido sin control
**Estado:** Aprobada  
**Categoría:** Marca / Operación / Expansión

### Decisión
La expansión no debe sacrificar reputación, calidad de atención ni confiabilidad.

### Motivo
Yatezzitos compite en una categoría donde la confianza define gran parte del valor percibido.

### Implicación
La calidad operativa debe acompañar al crecimiento.

---

## DEC-052 — La arquitectura debe prepararse para publicar también en marketplaces externos
**Estado:** Aprobada  
**Categoría:** Integraciones / Distribución

### Decisión
La estructura futura debe facilitar publicar o sincronizar embarcaciones hacia marketplaces externos cuando sea útil.

### Motivo
Hoy ya se usan plataformas externas como referencia y canal de captación complementario.

### Implicación
Conviene diseñar datos, formularios y estructuras con interoperabilidad en mente.

---

## DEC-053 — El éxito del proyecto se medirá por expansión real, no solo por actividad interna
**Estado:** Aprobada  
**Categoría:** Métricas / Estrategia

### Decisión
El éxito se evaluará principalmente por:
- nuevos destinos activos,
- nuevos socios comerciales,
- reservas en nuevas ciudades y países,
- tráfico SEO,
- ticket promedio,
- recurrencia de clientes,
- ciudades activas.

### Motivo
Estas métricas reflejan crecimiento real y no solo movimiento interno.

### Implicación
Los dashboards y reportes futuros deben alinearse a estas métricas.

---

## DEC-054 — La documentación debe crecer por versiones
**Estado:** Aprobada  
**Categoría:** Documentación / Gobernanza

### Decisión
Los documentos del proyecto se pueden mejorar por versiones sucesivas y no necesitan esperar perfección absoluta para existir.

### Motivo
Esperar perfección retrasa el avance.

### Implicación
Se acepta trabajar con versión 1, versión 2, etc., siempre que el contenido sea útil y claro.

---

## Nota final
Este registro seguirá creciendo.

Cada vez que se tome una decisión importante sobre:
- tecnología,
- arquitectura,
- CRM,
- SEO,
- automatización,
- seguridad,
- expansión,
- producto,
- IA,
- marca,
- operación,

deberá agregarse una nueva entrada a este documento.