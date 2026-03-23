# Mapa del Repositorio — Yatezzitos Platform

> Fecha: 2026-03-22 | Lote: BATCH-001

---

## Visión General

El repositorio `yatezzitos-platform` contiene toda la documentación, código, configuración y assets del proyecto **Yatezzitos Global** — plataforma tecnológica de turismo náutico privado de lujo.

**Archivos relevantes identificados:** ~120 (excluyendo binarios, CSS admin, tema Houzez y dependencias)
**Archivos excluidos:** ~400+ (CSS core WP, tema Houzez PHP, cache, venv, binarios)

---

## Estructura por Dominio

### 01 — Empresa y Visión
| Archivo | Acción | Contenido clave |
|---|---|---|
| `README.md` | Completo | Misión, visión, modelo de negocio, stack, prioridades, público |
| `docs/business/vision.md` | Completo | Visión estratégica, metas 1-3-5 años, modelos futuros |

### 02 — Arquitectura Web y Stack Técnico
| Archivo | Acción | Contenido clave |
|---|---|---|
| `docs/architecture/index.md` | Completo | Índice de 11 documentos de arquitectura |
| `docs/architecture/current-stack.md` | Completo | WordPress + Elementor + Houzez + GHL + Twilio |
| `docs/architecture/future-stack.md` | Completo | Web app futura, IA, paneles |
| `docs/architecture/web-app.md` | Completo | Diseño técnico de la futura web app |
| `docs/README.md` | Completo | Índice maestro de toda la documentación |

### 03 — Frontend, UI, UX y Branding
| Archivo | Acción | Contenido clave |
|---|---|---|
| `docs/brand/core.md` | Completo | Identidad de marca: personalidad, tono, posicionamiento |
| `redesign/README.md` | Completo | Índice del proyecto de rediseño |
| `redesign/02-contact-us.md` | Completo | Código de Contact Us rediseñado |
| `redesign/03-help.md` | Completo | Código de Help/FAQ rediseñado |
| `redesign/04-blog.html` | Completo | Código del blog rediseñado |
| `redesign/css/04-blog-redesign.css` | Resumir | CSS del blog rediseñado |

### 04 — WordPress, Elementor, Houzez
| Archivo | Acción | Contenido clave |
|---|---|---|
| `wordpress/snippets/*.css` | Resumir | 5 archivos CSS personalizado |
| `wordpress/snippets/yzz-readmore-drive-download.php` | Resumir | Snippet PHP personalizado |
| `docs/fixes/whatsapp-contacto-fix.md` | Completo | Solución WhatsApp overflow |
| `redesign/css/CSS ADICIONAL*/` | Resumir | CSS legacy Yellow Pencil |

### 05 — CRM, GoHighLevel, Automatizaciones
| Archivo | Acción | Contenido clave |
|---|---|---|
| `docs/crm/index.md` | Completo | Índice CRM con estado y planes |
| `docs/crm/current-state.md` | Completo | Pipelines, campos, automatizaciones actuales |
| `docs/crm/future-state.md` | Completo | Visión futura del CRM |
| `docs/crm/onboarding-propietarios.md` | Completo | Flujo de alta de propietarios |
| `docs/scrum/plan-issue-4-crm.md` | Completo | Plan ordenamiento CRM |
| `docs/scrum/plan-issue-5-automatizaciones.md` | Completo | 8 automatizaciones por etapa |
| `docs/scrum/plan-issue-6-feedback.md` | Completo | Plan etapa Feedback |
| `docs/scrum/plan-issue-7-captacion.md` | Completo | Plan captación propietarios |

### 06 — Cotizaciones, Reservas, Pagos
| Archivo | Acción | Contenido clave |
|---|---|---|
| `docs/architecture/marketplace.md` | Completo | Lógica del marketplace |
| `docs/architecture/calendario-disponibilidad.md` | Completo | Módulo disponibilidad |
| `docs/architecture/cliente.md` | Completo | Cuenta del cliente |
| `docs/architecture/propietarios.md` | Completo | Panel de propietarios |
| `wordpress/plugins/yzz-quotes-api*.php` | Completo | Plugin de cotizaciones (lógica de negocio) |
| `data/faq/preguntas-frecuentes.md` | Completo | FAQs del negocio |
| `scripts/yatezzitos_crawler/output/data/knowledge_bases/*.md` | Completo | RAG knowledge bases por destino |

### 07 — SEO, Contenido y Landing Pages
| Archivo | Acción | Contenido clave |
|---|---|---|
| `docs/seo/README.md` + 6 docs SEO | Completo | Master plan, auditoría, keywords, reglas, GSC |
| `docs/seo/acapulco/*.html` (~8 archivos) | Resumir | Contenido SEO Acapulco |
| `docs/seo/cancun/*.html` (~10 archivos) | Resumir | Contenido SEO Cancún |
| `docs/scrum/plan-issue-3-seo.md` | Completo | Plan de ejecución SEO |
| `.agents/workflows/seo-blog-posts.md` | Completo | Reglas SEO para blog |
| `data/faq/seo-keywords-faq.md` | Completo | Keywords de FAQs |
| `plugins/yatezzitos-yoast-rest-api/*.php` | Completo | Plugin Yoast REST API |

### 08 — Operación Interna, SOPs y Manuales
| Archivo | Acción | Contenido clave |
|---|---|---|
| `CLAUDE.md` | Completo | Contexto operativo para agentes IA |
| `AGENTS.md` | Completo | Reglas globales de IA |
| `ai/assistants/README.md` | Completo | Arquitectura de 6 agentes |
| `ai/assistants/orchestrator.md` | Completo | Spec orquestador |
| `ai/assistants/turista.md` | Completo | Spec Marina |
| `ai/assistants/propietario.md` | Completo | Spec Timón |
| `ai/assistants/soporte-interno.md` | Completo | Spec soporte interno |
| `ai/assistants/broker.md` | Completo | Spec Capitán |
| `ai/assistants/afiliado.md` | Completo | Spec Ola |
| `.agents/workflows/00-orchestrator.md` | Completo | Workflow orquestador |
| `scripts/yatezzitos_crawler/output/data/instrucciones_marina/*.md` | Completo | 7 docs operativos de Marina |

### 09 — Integraciones Externas y APIs
| Archivo | Acción | Contenido clave |
|---|---|---|
| `docs/architecture/integrations.md` | Completo | Mapa WP-GHL-Twilio-webhooks |
| `docs/tools/guia-wordpress-mcp.md` | Completo | Setup MCP WordPress |
| `docs/ai/*.md` (6 archivos) | Completo | Skills de IA propuestas |
| `scripts/extraccion_yates.py` | Completo | Script extracción yates |
| `scripts/yatezzitos_crawler/` | Resumir | Crawler completo (arquitectura) |

### 10 — Backlog, Deuda Técnica y Decisiones
| Archivo | Acción | Contenido clave |
|---|---|---|
| `docs/scrum/backlog.md` | Completo | 25 items backlog, 5 niveles |
| `docs/architecture/decisions-log.md` | Completo | 50+ decisiones técnicas |

---

## Estadísticas

| Categoría | Cantidad |
|---|---|
| Archivos para subir completos | ~65 |
| Archivos para resumir | ~25 |
| Archivos excluidos | ~400+ |
| Dominios funcionales | 10 |
| Notebooks objetivo | 8 |
