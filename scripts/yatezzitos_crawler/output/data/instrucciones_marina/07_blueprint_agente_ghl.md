# 🏗️ Blueprint: Agente Marina — Arquitectura Profesional en GHL Agent Studio

> Guía paso a paso para construir un agente IA de clase mundial para Yatezzitos.

---

## 🧭 Visión General

Marina no será un simple chatbot de FAQ. Será un **agente de ventas autónomo** que:
1. Cualifica leads como un vendedor experto
2. Recomienda embarcaciones con datos reales
3. Captura información de contacto automáticamente
4. Busca en sus bases de conocimiento inteligentemente
5. Genera contenido visual y de texto personalizado
6. Escala a humanos en el momento correcto

---

## FASE 1: ESTRUCTURA BASE (Lo que ya tienes ✅)

Ya completado en esta conversación:
- ✅ Estímulo Global (identidad + seguridad)
- ✅ Prompt del nodo AI Agent (flujo de cualificación completo)
- ✅ 3 Knowledge Bases cargadas (2 catálogos + FAQ)
- ✅ Custom Values de URLs por destino
- ✅ Variable `user_satisfied` (Booleano)

---

## FASE 2: FLUJO INTELIGENTE CON ROUTER

### ¿Qué es el Router?
El Router detecta la **intención del usuario** y dirige la conversación al nodo correcto. En vez de que Marina haga todo en un solo nodo, dividimos las responsabilidades.

### Paso a paso:

**1. Agrega un nodo Router después del Disparador de inicio**

Configura estas rutas de intención:

| Ruta | Intención detectada | Nodo destino |
|---|---|---|
| Ruta 1 | El usuario quiere **buscar/recomendar un yate** | → Nodo AI Agent (Marina - Ventas) |
| Ruta 2 | El usuario tiene una **pregunta frecuente** (pagos, cancelaciones, seguridad) | → Nodo Buscar KB "Preguntas frecuentes" → Nodo AI Agent (Marina - FAQ) |
| Ruta 3 | El usuario quiere **contactar a un humano** o tiene una queja | → Nodo Final (transferir a equipo) |
| Ruta 4 | El usuario quiere saber sobre un **evento especial** (boda, corporativo) | → Nodo AI Agent (Marina - Eventos) |
| Fallback | No se detecta intención clara | → Nodo AI Agent (Marina - General) |

**2. Beneficio:** Cada ruta puede tener su propio prompt especializado, lo que hace a Marina mucho más precisa en cada tipo de conversación.

---

## FASE 3: CAPTURA INTELIGENTE DE LEADS

### ¿Por qué?
Actualmente Marina recomienda yates pero no captura datos del lead automáticamente. Con los nodos de captura, puedes crear un **flujo secuencial** que recopile la información y la guarde en el CRM.

### Paso a paso:

**1. Crea un nodo Secuencial llamado "Captura de Lead"**

Dentro del secuencial, agrega estos nodos en orden:

```
[Entrada de texto] → Nombre completo
        ↓
[Número de teléfono] → Teléfono
        ↓
[Dirección de correo] → Email
        ↓
[Entrada de texto] → Fecha deseada del viaje
        ↓
[Selección individual] → Horario preferido
   Opciones: Amanecer (7-11 AM) | Atardecer (1-5 PM) | Anochecer (7-11 PM)
        ↓
[Entrada de texto] → Número de pasajeros
        ↓
[Entrada de texto] → Solicitud especial (opcional)
```

**2. Conecta este Secuencial al flujo principal:**
Cuando Marina termine de recomendar un yate y el cliente diga "sí, me interesa", el Router lo envía al nodo Secuencial de captura.

**3. Después de la captura**, usa una **Llamada a la API** para crear el contacto/lead en tu pipeline de GHL automáticamente.

---

## FASE 4: BÚSQUEDA WEB EN TIEMPO REAL

### ¿Para qué?
El nodo "Busque en la web" permite que Marina busque información actualizada que NO está en sus bases de conocimiento.

### Casos de uso ideales:

| Situación | Qué busca en la web |
|---|---|
| Cliente pregunta por el clima actual | "clima hoy en Cancún" |
| Cliente pregunta por vuelos | "vuelos baratos a Los Cabos" |
| Cliente pregunta por hoteles cercanos | "hoteles cerca de Marina Mazatlán" |
| Temporada de ballenas | "temporada avistamiento ballenas La Paz 2026" |

### Configuración:
- Agrega el nodo "Busque en la web" como herramienta del AI Agent
- En el prompt, instruye a Marina: "Si el cliente pregunta sobre clima, vuelos, hoteles o actividades turísticas que no están en tu base de conocimientos, usa la herramienta de búsqueda web para obtener información actualizada."

---

## FASE 5: LLAMADAS A API (Automatización Avanzada)

### ¿Para qué?
El nodo "Llamada a la API" conecta a Marina con sistemas externos.

### Integraciones recomendadas:

| API | Qué hace Marina con ella | Prioridad |
|---|---|---|
| **GHL Contacts API** | Crear lead automáticamente después de capturar datos | ⭐⭐⭐⭐⭐ |
| **GHL Pipeline API** | Mover lead a la etapa correcta del pipeline | ⭐⭐⭐⭐⭐ |
| **GHL Conversations API** | Registrar la conversación como nota interna | ⭐⭐⭐⭐ |
| **WhatsApp API** | Enviar cotización por WhatsApp | ⭐⭐⭐ |
| **Google Calendar API** | Verificar disponibilidad real de fechas | ⭐⭐⭐ |

### Ejemplo práctico — Crear Lead:
```
POST https://rest.gohighlevel.com/v1/contacts/
Headers: Authorization: Bearer {{tu_api_key}}
Body:
{
  "firstName": "{{nombre_capturado}}",
  "phone": "{{telefono_capturado}}",
  "email": "{{email_capturado}}",
  "tags": ["marina-ai", "lead-automatico"],
  "customField": {
    "destino": "{{destino_elegido}}",
    "tipo_embarcacion": "{{tipo}}",
    "pasajeros": "{{num_pasajeros}}",
    "fecha_viaje": "{{fecha}}"
  }
}
```

---

## FASE 6: IA GENERATIVA (Diferenciación Premium)

### Generación de Texto
- **Uso:** Generar cotizaciones preliminares personalizadas, mensajes de seguimiento, o descripciones de experiencias.
- **Ejemplo:** Después de que Marina recomienda un yate, genera un texto de "preview de experiencia" personalizado: *"Imagina zarpar al atardecer desde Marina Mazatlán a bordo del Yate Sunset con tus 12 invitados, disfrutando de ceviche fresco y cervezas heladas mientras navegan hacia la Isla de los Venados..."*

### Generación de Imágenes
- **Uso:** Crear imágenes personalizadas del destino para impactar al cliente.
- **Ejemplo:** Si el cliente dice "quiero un yate para mi boda en Cancún", Marina podría generar y enviar una imagen conceptual de una boda en yate al atardecer en Cancún.

### Generación de Audio
- **Uso:** Enviar notas de voz con tono premium para momentos clave (bienvenida, confirmación de reserva).
- **Ejemplo:** Un audio diciendo: "¡Hola! Soy Marina de Yatezzitos. Tu cotización ya está lista, échale un vistazo y si tienes dudas estoy aquí para ti."

---

## FASE 7: SERVIDOR MCP (Conexión con Herramientas Externas)

### ¿Qué puedes conectar vía MCP?
Si GHL soporta MCP servers personalizados, podrías conectar:
- Un servidor que consulte tu base de datos de WordPress en tiempo real
- Un servidor que verifique disponibilidad en calendario
- Un servidor que consulte cotizaciones previas del CRM

> **Nota:** Esta fase es avanzada. Primero completa las fases 1-6 y luego evalúa si necesitas un MCP personalizado.

---

## 📋 PLAN DE IMPLEMENTACIÓN (Orden de prioridad)

| Orden | Fase | Tiempo estimado | Impacto |
|---|---|---|---|
| 1 | ✅ Estructura base (prompts + KBs) | Ya completado | Alto |
| 2 | Router con intenciones | 1-2 horas | Alto |
| 3 | Captura de leads (Secuencial) | 1-2 horas | Muy alto |
| 4 | API para crear lead en CRM | 1 hora | Muy alto |
| 5 | Búsqueda web | 30 min | Medio |
| 6 | IA Generativa (texto/imágenes) | 1 hora | Diferenciación |
| 7 | MCP Server personalizado | Variable | Avanzado |

---

## 🎯 ARQUITECTURA VISUAL DEL FLUJO FINAL

```
[Disparador de inicio]
         ↓
    [Router] ←── Detecta intención
     ↓    ↓    ↓    ↓
     │    │    │    │
     │    │    │    └── Intención: "Hablar con humano" / Queja
     │    │    │              ↓
     │    │    │         [Nodo Final → Transferir]
     │    │    │
     │    │    └── Intención: "Evento especial"
     │    │              ↓
     │    │         [AI Agent - Eventos]
     │    │              ↓
     │    │         [Secuencial: Captura Lead Evento]
     │    │              ↓
     │    │         [API: Crear Lead en Pipeline "Eventos"]
     │    │
     │    └── Intención: "Pregunta general / FAQ"
     │              ↓
     │         [KB Search: Preguntas frecuentes]
     │              ↓
     │         [AI Agent - FAQ]
     │
     └── Intención: "Buscar / Recomendar yate" (PRINCIPAL)
               ↓
          [KB Search: Base del destino correcto]
               ↓
          [AI Agent - Marina Ventas]
               ↓
          ¿Cliente interesado?
          ├── Sí → [Secuencial: Captura Lead]
          │              ↓
          │         [API: Crear Lead en Pipeline "Cotizaciones"]
          │              ↓
          │         [Generación de Texto: Preview de experiencia]
          │              ↓
          │         [Nodo Final: "Tu cotización está en camino 🎉"]
          │
          └── No / Más opciones → [Loop: Siguiente recomendación]
```

---

## 🔑 VARIABLES RUNTIME RECOMENDADAS

Además de `user_satisfied`, agrega estas variables al nodo AI Agent:

| Variable | Tipo | Para qué |
|---|---|---|
| `user_satisfied` | Booleano | Ya la tienes ✅ |
| `destino_elegido` | Texto | Guardar la ciudad seleccionada |
| `tipo_embarcacion` | Texto | Yate, lancha, velero, catamarán |
| `num_pasajeros` | Texto | Cantidad de personas |
| `intent_detected` | Texto | Intención detectada por el Router |
| `lead_captured` | Booleano | Si ya se capturaron los datos |

---

## ⚡ QUICK WINS (Hazlo hoy mismo)

1. **Activa "Busque en la web"** como herramienta del AI Agent → Marina podrá responder sobre clima, vuelos y hoteles.
2. **Agrega un Router** antes del AI Agent con al menos 3 rutas (ventas, FAQ, escalar).
3. **Agrega el Secuencial de captura** después del AI Agent para no perder leads.

Con estas 3 acciones tu agente pasa de ser un chatbot básico a un **sistema de ventas autónomo**. 🚀
