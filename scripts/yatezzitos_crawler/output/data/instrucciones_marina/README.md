# 🤖 Instrucciones del Agente Marina — Yatezzitos

Documentación completa de prompt engineering para el agente de IA **Marina**, concierge virtual de renta de yates de lujo implementado en **GoHighLevel Agent Studio**.

---

## 📋 Índice de Documentos

| # | Archivo | Descripción | Caracteres |
|---|---|---|---|
| 1 | [`01_personalidad.md`](01_personalidad.md) | Identidad, tono premium, emojis, saludo inicial, reglas de comunicación | ~1,570 |
| 2 | [`02_objetivos.md`](02_objetivos.md) | Flujo de cualificación (6 pasos), URLs por destino con custom values GHL, negociación, CTA, escalamiento | ~3,985 |
| 3 | [`03_faq_informacion_adicional.md`](03_faq_informacion_adicional.md) | Base de Preguntas Frecuentes (10 secciones extraídas del Google Doc oficial y yatezzitos.com) | ~8,157 |
| 4 | [`04_seguridad_guardrails.md`](04_seguridad_guardrails.md) | Anti-prompt injection, protección de datos, PII, límites de acción, alcance temático, integridad | ~4,176 |
| 5 | [`05_enrutamiento_knowledge_bases.md`](05_enrutamiento_knowledge_bases.md) | Instrucción de routing para el nodo KB Search en GHL con 5 reglas | ~1,842 |
| 6 | [`06_configuracion_ghl.md`](06_configuracion_ghl.md) | Textos listos para copiar-pegar: Estímulo Global (≤1,500 chars) + Prompt del nodo AI Agent | ~6,075 |
| 7 | [`07_blueprint_agente_ghl.md`](07_blueprint_agente_ghl.md) | Blueprint de 7 fases: Router, Captura de Leads, APIs, Web Search, IA Generativa, MCP | ~9,928 |

---

## 🏗️ Arquitectura en GoHighLevel

```
Estímulo Global (aplica a todos los nodos)
  → Identidad + Seguridad (archivo 01 + 04 condensados)

Nodo AI Agent (prompt principal)
  → Flujo de cualificación completo (archivo 02 + enrutamiento 05)

Knowledge Bases conectadas:
  → "Mazatlan, Acapulco, Cancun, Ixtapa y Huatulco"
  → "Cabos, Vallarta, Playa, N. Vta y La Paz"
  → "Preguntas frecuentes"
```

---

## 🔗 Custom Values de GHL (URLs por destino)

```
{{custom_values.url_cancn}}
{{custom_values.url_mazatln}}
{{custom_values.url__puerto_vallarta}}
{{custom_values.url__los_cabos}}
{{custom_values.url_la_paz}}
{{custom_values.url__acapulco}}
{{custom_values.url__huatulco}}
{{custom_values.url__ixtapa}}
{{custom_values.url__playa_del_carmen}}
{{custom_values.url__nuevo_vallarta}}
```

---

## 📚 Knowledge Bases (RAGs)

Los catálogos de embarcaciones por destino se encuentran en `../knowledge_bases/` y se dividen en:

| Base | Destinos | Archivo(s) |
|---|---|---|
| Grupo A | Mazatlán, Acapulco, Cancún, Ixtapa, Huatulco | 5 archivos `marina_rag_*.md` |
| Grupo B | Los Cabos, Puerto Vallarta, Playa del Carmen, Nuevo Vallarta, La Paz | 5 archivos `marina_rag_*.md` |

Cada archivo contiene < 25,000 caracteres y lista: nombre, precio, capacidad, amenidades y URL de cada embarcación.

---

## 🔒 Fuentes de Investigación

La ingeniería de prompt se basó en:
1. Specs oficiales del repo: `ai/assistants/turista.md` y `orchestrator.md`
2. FAQ reales de `yatezzitos.com/preguntas-frecuentes`
3. Google Doc oficial de FAQ detalladas del negocio
4. Mejores prácticas de prompt engineering para agentes IA de ventas (2025)
5. Guardrails definidos en `AGENTS.md` del repositorio principal

---

**Última actualización:** 15 de marzo 2026
