# Skills de Flota Náutica (MCP) — Enfoque en Creación de Yates (Skill #1)

Llegamos a la corona de las automatizaciones propuestas para Yatezzitos Global: la **Skill #1 (Houzez Fleet Manager)**.

El proceso actual de dar de alta una nueva embarcación exige recolectar los datos técnicos del propietario, redactar descripciones atractivas, llenar metadatos SEO y rellenar decenas de metacampos ("custom fields") en el tema Houzez de WordPress. Esta habilidad resuelve ese enorme cuello de botella operativo.

---

## 1. El Astillero Redactor (Yacht Draft Generator) 📝
**Por qué es útil:** Cuando un propietario manda la información cruda de su yate (ej: *"Es un SeaRay de 40 pies, caben 12, cuesta $1500 USD por 4 horas, incluye ceviche"*), toma entre 30 y 60 minutos transformarlo en una ficha publicable con SEO natural y llenar todos los campos repetitivos del backend.
**Cómo funciona:**
- Tu equipo alimenta a la IA con los datos crudos del propietario (vía chat o leyendo un PDF proporcionado).
- La IA aplica el formato de "tono humano" y redacta una descripción de lujo orientada a SEO, evitando las traducciones literales o frases cliché como establecen las reglas de Yatezzitos.
- Usando el MCP de WordPress (`create_or_update_file` o mediante endpoints REST para *Custom Post Types*), la IA inyecta directamente la descripción, el título SEO y la palabra clave objetivo en WordPress.
- Asigna de manera automática las taxonomías (ej. Destino: Ixtapa, Tipo: Yates Básicos) basándose en su comprensión del catálogo.
**Seguridad:** **Inquebrantable.** Todo contenido se publica estrictamente con el estatus `draft` (borrador). Tu equipo solo debe entrar, subir las fotografías manuales (o arrastrarlas a la galería) y presionar "Publicar" cuando estén satisfechos.

## 2. El Auditor de Fichas (Listing Optimizer & Sync) 🔍
**Por qué es útil:** De los cientos de embarcaciones que Yatezzitos maneja, muchas tienen descripciones desactualizadas, precios obsoletos o carecen de campos vitales (amenities completas) que bloquean la decisión de compra del turista.
**Cómo funciona:**
- Periódicamente, la IA extrae el listado completo de yates live desde tu WordPress MCP y compara sus metadatos (precios, capacidad) contra una fuente de verdad (por ejemplo, actualizando contra una hoja de cálculo maestra provista por los armadores).
- Si detecta que a una embarcación de Los Cabos le falta la etiqueta "Paddleboard" o su descripción es demasiado corta (menos de 300 palabras), alerta sobre este *Content Gap* comercial.
- Te abre un panel de propuestas: *"La ficha del Yate Azimut 55ft en Vallarta tiene una descripción muy pobre. Aquí tienes una propuesta de redacción HTML SEO-optimizada. ¿Aceptar?"*
- Si aceptas, inyecta la actualización mediante API y recalcula el SEO Score de Yoast.
**Seguridad:** **Alta.** Acelera las auditorías de consistencia en el catálogo. Nunca borra un yate ni publica datos de precios invisibles por tu equipo. Requiere un "Ok" humano antes de reescribir un listado vivo.

---

> [!TIP]
> Empezaríamos programando **El Astillero Redactor (Opción 1)**. Como desarrollador AI, yo (o uno de los agentes de Soporte Interno) puedo convertir los mensajes de texto del dueño del barco directamente en entradas de Houzez con todo el formato HTML listo, cortando un proceso de una hora a tan solo 3 minutos.
