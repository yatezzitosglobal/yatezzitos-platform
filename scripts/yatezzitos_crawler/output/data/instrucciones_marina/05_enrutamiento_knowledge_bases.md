# Instrucción de Enrutamiento de Bases de Conocimiento (GoHighLevel)

Pega esta instrucción en el campo "Instructions" del nodo "Buscar base de conocimientos" de GoHighLevel.

---

## INSTRUCCIÓN (copiar desde aquí):

Busca en la base de conocimientos correcta según el tema de la conversación del usuario. Sigue estas reglas estrictamente:

**REGLA 1 — Preguntas generales (sin destino específico):**
Si el usuario hace preguntas sobre: proceso de reserva, métodos de pago, cancelaciones, políticas, seguridad, qué incluye la renta, horarios, tipos de embarcaciones, propinas, requisitos, documentación, eventos o cualquier duda general que NO mencione una ciudad específica → Busca en: **Preguntas frecuentes**

**REGLA 2 — Destinos del Grupo A:**
Si el usuario menciona o pregunta sobre embarcaciones, precios o catálogo en alguna de estas ciudades: **Mazatlán, Acapulco, Cancún, Ixtapa o Huatulco** → Busca en: **Mazatlan, Acapulco, Cancun, Ixtapa y Huatulco**

**REGLA 3 — Destinos del Grupo B:**
Si el usuario menciona o pregunta sobre embarcaciones, precios o catálogo en alguna de estas ciudades: **Los Cabos, Puerto Vallarta, Playa del Carmen, Nuevo Vallarta o La Paz** → Busca en: **Cabos, Vallarta, Playa, N. Vta y La Paz**

**REGLA 4 — Si no se ha definido destino:**
Si el usuario aún no ha mencionado un destino, primero pregúntale en qué ciudad le gustaría navegar antes de buscar en las bases de catálogo. Mientras tanto, si tiene dudas generales, usa **Preguntas frecuentes**.

**REGLA 5 — Combinación:**
Si la pregunta involucra tanto un destino específico como una duda general (ejemplo: "¿Cuánto cuesta un yate en Cancún y qué métodos de pago aceptan?"), busca primero en la base del destino para el precio y complementa con **Preguntas frecuentes** para la política de pagos.
