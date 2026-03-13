# Arquitectura de la web app — Diseño técnico

> Documento de arquitectura · Issue [#15](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/15)

---

## Objetivo

Definir la arquitectura técnica de la futura web app de Yatezzitos Global: una plataforma separada de WordPress que concentre la operación avanzada del negocio (marketplace, paneles, disponibilidad, reservas, IA).

---

## Principios de arquitectura

| Principio | Descripción |
|---|---|
| **No romper producción** | WordPress sigue activo durante y después de la transición (DEC-004) |
| **Modular** | Cada módulo funciona independiente pero se conecta con los demás |
| **API-first** | Todo dato se expone vía API REST para ser consumido por web, app móvil e IA |
| **Integrable** | Debe conectarse con WordPress, GHL, Twilio y servicios futuros (DEC-029) |
| **Progressive** | Se construye por fases, entregando valor desde el día 1 |
| **Multi-idioma/moneda** | Preparado para español/inglés y MXN/USD desde el inicio |

---

## Diagrama de alto nivel

```mermaid
flowchart TB
    subgraph "Capa Pública"
        WP["🌐 WordPress<br/>SEO + Comercial<br/>(se mantiene)"]
        WEBAPP["💻 Web App<br/>Marketplace + Paneles<br/>(nueva)"]
        MOBILE["📱 App Móvil<br/>(futuro)"]
    end

    subgraph "Capa de Servicios / API"
        API["🔌 API REST<br/>Backend central"]
        AUTH["🔐 Auth<br/>JWT + OTP"]
        NOTI["📧 Notificaciones<br/>Email + WhatsApp + Push"]
    end

    subgraph "Capa de Datos"
        DB["🗄️ Base de datos<br/>PostgreSQL / MySQL"]
        CACHE["⚡ Cache<br/>Redis"]
        FILES["📁 Archivos<br/>S3 / Storage"]
    end

    subgraph "Integraciones Externas"
        GHL["📊 GoHighLevel<br/>CRM"]
        TWILIO["📱 Twilio<br/>OTP + WhatsApp"]
        OPENAI["🤖 OpenAI<br/>Yatezzitos IA"]
        GMAPS["🗺️ Google Maps<br/>Ubicaciones"]
        PAYMENT["💳 Pasarela de pago<br/>(futuro)"]
    end

    WP --> API
    WEBAPP --> API
    MOBILE --> API
    API --> AUTH
    API --> DB
    API --> CACHE
    API --> FILES
    API --> NOTI
    NOTI --> TWILIO
    API --> GHL
    API --> OPENAI
    WEBAPP --> GMAPS
    API --> PAYMENT

    style WEBAPP fill:#3498db,color:#fff,stroke-width:3px
    style API fill:#2ecc71,color:#fff,stroke-width:3px
    style WP fill:#0073aa,color:#fff
```

---

## Módulos de la web app

Cada módulo tiene su propio spec detallado:

| Módulo | Issue | Spec | Prioridad |
|---|---|---|---|
| Marketplace de yates | [#11](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/11) | [marketplace.md](marketplace.md) | 🔴 Alta |
| Cuenta del cliente | [#12](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/12) | [cliente.md](cliente.md) | 🔴 Alta |
| Panel de propietarios | [#13](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/13) | [propietarios.md](propietarios.md) | 🔴 Alta |
| Panel interno | [#14](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/14) | [panel-interno.md](panel-interno.md) | 🟠 Media |
| Calendario de disponibilidad | [#9](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/9) | [calendario-disponibilidad.md](calendario-disponibilidad.md) | 🔴 Alta |
| Yatezzitos IA | [#16-18](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/16) | [ai/assistants/](../../ai/assistants/) | 🟠 Media |

---

## Modelo de datos principal

```mermaid
erDiagram
    USUARIO {
        string id PK
        string wp_user_id FK
        string crm_contact_id FK
        string rol "propietario|admin|broker|agencia|turista|equipo"
        string nombre
        string apellido
        string email
        string whatsapp
        string idioma
        datetime created_at
    }

    EMBARCACION {
        string id PK
        string wp_listing_id FK
        string titulo
        string tipo "yate|catamaran|velero|lancha|panga"
        string ciudad
        number precio
        string divisa
        number pasajeros
        number duracion_hrs
        string estado "publicado|borrador|revision|pausado"
        datetime created_at
    }

    DISPONIBILIDAD {
        string id PK
        string embarcacion_id FK
        date fecha_inicio
        date fecha_fin
        string estatus "disponible|cotizado|reservado|bloqueado|mantenimiento"
        string motivo
        string creado_por
        datetime created_at
    }

    COTIZACION {
        string id PK
        string turista_id FK
        string embarcacion_id FK
        string crm_quote_id FK
        date fecha_viaje
        number pasajeros
        number precio_total
        number anticipo
        string estatus "enviada|aceptada|vencida|modificada"
        datetime validez
        datetime created_at
    }

    RESERVA {
        string id PK
        string cotizacion_id FK
        string turista_id FK
        string embarcacion_id FK
        string crm_reserva_id FK
        date fecha_viaje
        time hora_salida
        time hora_regreso
        number duracion_hrs
        number pasajeros
        string marina
        string google_maps_link
        number monto_total
        number anticipo_pagado
        number saldo_pendiente
        string metodo_pago
        string estatus "pendiente|anticipo_recibido|confirmada|completada|cancelada"
        datetime created_at
    }

    DOCUMENTO {
        string id PK
        string usuario_id FK
        string embarcacion_id FK
        string tipo "ine|permiso_navegacion|seguro|fotos|acta_constitutiva|contrato"
        string url_archivo
        date fecha_vencimiento
        string estatus "vigente|por_vencer|vencido|faltante"
        datetime created_at
    }

    USUARIO ||--o{ EMBARCACION : "es propietario de"
    USUARIO ||--o{ COTIZACION : "solicita"
    USUARIO ||--o{ RESERVA : "tiene"
    USUARIO ||--o{ DOCUMENTO : "sube"
    EMBARCACION ||--o{ DISPONIBILIDAD : "tiene calendario"
    EMBARCACION ||--o{ COTIZACION : "es cotizada"
    EMBARCACION ||--o{ RESERVA : "es reservada"
    EMBARCACION ||--o{ DOCUMENTO : "requiere"
    COTIZACION ||--o| RESERVA : "se convierte en"
```

---

## API REST — Endpoints principales

### Autenticación
| Método | Endpoint | Descripción |
|---|---|---|
| POST | `/api/v1/auth/otp/request` | Solicitar OTP por teléfono/email |
| POST | `/api/v1/auth/otp/verify` | Verificar OTP |
| POST | `/api/v1/auth/login` | Login con credenciales |
| GET | `/api/v1/auth/me` | Datos del usuario autenticado |

### Embarcaciones
| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/v1/embarcaciones` | Listar con filtros (ciudad, tipo, pasajeros, precio) |
| GET | `/api/v1/embarcaciones/:id` | Detalle de una embarcación |
| POST | `/api/v1/embarcaciones` | Crear (requiere rol propietario/admin) |
| PUT | `/api/v1/embarcaciones/:id` | Editar (pasa por revisión) |
| GET | `/api/v1/embarcaciones/:id/disponibilidad` | Calendario de disponibilidad |

### Disponibilidad
| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/v1/disponibilidad/:embarcacion_id` | Consultar disponibilidad por rango |
| POST | `/api/v1/disponibilidad` | Bloquear fechas |
| DELETE | `/api/v1/disponibilidad/:id` | Desbloquear (si no está reservada) |
| GET | `/api/v1/disponibilidad/publica/:slug` | Link compartible (sin auth) |

### Cotizaciones
| Método | Endpoint | Descripción |
|---|---|---|
| POST | `/api/v1/cotizaciones` | Solicitar cotización |
| GET | `/api/v1/cotizaciones/:id` | Ver detalle de cotización |
| GET | `/api/v1/mis-cotizaciones` | Cotizaciones del usuario |

### Reservas
| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/v1/reservas/:id` | Detalle de reserva |
| GET | `/api/v1/mis-reservas` | Reservas del usuario |
| GET | `/api/v1/admin/reservas` | Todas las reservas (admin) |

### Usuarios
| Método | Endpoint | Descripción |
|---|---|---|
| GET | `/api/v1/usuarios/me` | Mi perfil |
| PUT | `/api/v1/usuarios/me` | Editar mi perfil |
| GET | `/api/v1/admin/usuarios` | Listar usuarios (admin) |

---

## Stack técnico recomendado

| Capa | Tecnología recomendada | Alternativa | Razón |
|---|---|---|---|
| **Frontend** | Next.js (React) | Nuxt.js (Vue) | SSR para SEO, componentes reutilizables |
| **Backend** | Node.js (Express/Fastify) | Python (FastAPI) | Rápido, ecosistema amplio, integración IA |
| **Base de datos** | PostgreSQL | MySQL | Relacional, robusto, escalable |
| **Cache** | Redis | — | Sesiones, cache de disponibilidad |
| **Archivos** | AWS S3 / Cloudflare R2 | Google Cloud Storage | Fotos, documentos |
| **Autenticación** | JWT + OTP (Twilio) | Auth0 / Clerk | Consistente con lo que ya funciona |
| **Hosting** | Vercel (frontend) + Railway/Render (backend) | AWS | Rápido de desplegar, escalable |
| **CI/CD** | GitHub Actions | — | Ya está en GitHub |

> **Importante:** Esta es una recomendación. La decisión final de stack se tomará cuando comience el desarrollo, evaluando recursos y equipo disponible.

---

## Estrategia de migración WordPress → Web App

```mermaid
flowchart LR
    subgraph "Fase 1 — Hoy"
        WP1["🌐 WordPress<br/>Todo en uno"]
        GHL1["📊 GHL<br/>CRM"]
    end

    subgraph "Fase 2 — Coexistencia"
        WP2["🌐 WordPress<br/>SEO + Comercial"]
        APP2["💻 Web App<br/>Paneles + Operación"]
        API2["🔌 API<br/>Conecta ambos"]
        GHL2["📊 GHL"]

        WP2 --> API2
        APP2 --> API2
        API2 --> GHL2
    end

    subgraph "Fase 3 — Plataforma"
        WP3["🌐 WordPress<br/>Solo SEO"]
        APP3["💻 Web App<br/>Plataforma completa"]
        MOB3["📱 App Móvil"]
        API3["🔌 API"]
        IA3["🤖 IA"]

        WP3 --> API3
        APP3 --> API3
        MOB3 --> API3
        API3 --> IA3
    end

    WP1 -.->|"Evolución<br/>progresiva"| WP2
    WP2 -.-> WP3

    style APP2 fill:#3498db,color:#fff
    style APP3 fill:#3498db,color:#fff,stroke-width:3px
    style API2 fill:#2ecc71,color:#fff
    style API3 fill:#2ecc71,color:#fff,stroke-width:3px
```

### Fase 1 — Actual (WordPress + GHL)
- WordPress maneja todo: SEO, fichas, formularios
- GHL maneja CRM, pipeline, automatizaciones
- Webhooks conectan ambos

### Fase 2 — Coexistencia
- WordPress sigue para SEO y capa comercial
- Web app nueva maneja paneles y operación
- API central conecta WordPress, web app y GHL
- Disponibilidad y reservas migran a la web app

### Fase 3 — Plataforma completa
- WordPress se reduce a capa SEO
- Web app es la plataforma principal
- App móvil consume la misma API
- IA integrada como capa transversal

---

## Sincronización de datos

| Dato | Fuente de verdad | Se sincroniza con |
|---|---|---|
| Embarcaciones (fichas) | WordPress (WP) | Web app (vía API o sync) |
| Leads y pipeline | GoHighLevel | Web app (lectura) |
| Disponibilidad | Web app (nueva) | WordPress (widget/badge) |
| Reservas | GHL → Web app | Ambos |
| Usuarios/propietarios | WordPress | Web app + GHL |
| Documentos | Web app (nueva) | GHL (referencia) |
| SEO/Contenido | WordPress | No se migra |

### IDs de sincronización

Toda entidad debe tener estos IDs cruzados:

| Campo | Descripción |
|---|---|
| `wp_user_id` / `wp_listing_id` | ID en WordPress |
| `crm_contact_id` / `crm_quote_id` | ID en GoHighLevel |
| `app_id` | ID en la web app |

---

## Seguridad

| Aspecto | Implementación |
|---|---|
| Autenticación | JWT + refresh tokens + OTP |
| Autorización | RBAC (Role-Based Access Control) por `rol_de_usuario` |
| Datos sensibles | Encriptación en reposo y en tránsito (HTTPS) |
| PII | No se expone en logs ni en respuestas públicas de API |
| Rate limiting | Límite de requests por IP y por usuario |
| CORS | Restringido a dominios autorizados |
| Backups | Diarios automáticos de base de datos |

---

## Issues relacionados

| Issue | Relación |
|---|---|
| [#9](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/9) | Calendario de disponibilidad |
| [#11](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/11) | Marketplace |
| [#12](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/12) | Cuenta del cliente |
| [#13](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/13) | Panel de propietarios |
| [#14](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/14) | Panel interno |
| [#16-18](https://github.com/YatezzitosMexico/yatezzitos-platform/issues/16) | Yatezzitos IA |

---

*Última actualización: 13 de marzo 2026*
