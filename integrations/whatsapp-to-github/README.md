# Integración: WhatsApp → GitHub Issues via GoHighLevel

## Arquitectura

```
WhatsApp (tu teléfono)
    ↓ mensaje con /tarea:
GoHighLevel (Workflow)
    ↓ HTTP POST
Cloudflare Worker (webhook)
    ↓ GitHub REST API
GitHub Issue (con labels)
    ↓ auto-assign
Copilot Agent (procesa el issue)
```

## Setup paso a paso

### 1. Desplegar el Webhook en Cloudflare Workers

#### Requisitos previos
- Cuenta de Cloudflare (gratuita)
- [Wrangler CLI](https://developers.cloudflare.com/workers/wrangler/install-and-update/) instalado

#### Pasos

```bash
# 1. Instalar wrangler si no lo tienes
npm install -g wrangler

# 2. Autenticarte
wrangler auth

# 3. Ir al directorio del webhook
cd integrations/whatsapp-to-github

# 4. Configurar los secrets (uno por uno)
wrangler secret put GITHUB_TOKEN
# → Pegar tu GitHub PAT con permisos de `repo`

wrangler secret put GHL_WEBHOOK_SECRET
# → Inventar un token secreto (ej: genera uno con `openssl rand -hex 32`)

wrangler secret put ALLOWED_CONTACTS
# → Tu contact ID de GHL (o dejarlo vacío para no filtrar)

# 5. Desplegar
wrangler deploy

# 6. Anotar la URL del worker (ej: https://whatsapp-to-github.tu-cuenta.workers.dev)
```

### 2. Configurar el Workflow en GoHighLevel

1. **Ir a** Automation → Workflows → Create Workflow

2. **Trigger:** "Customer Reply" o "Inbound Message"
   - Filtro: Canal = WhatsApp
   - Filtro: Mensaje contiene `/tarea:`

3. **Acción:** "Webhook / HTTP Request"
   - **Method:** POST
   - **URL:** `https://whatsapp-to-github.tu-cuenta.workers.dev`
   - **Headers:**
     ```
     Content-Type: application/json
     X-GHL-Token: [tu_secret_token_del_paso_4]
     ```
   - **Body (JSON):**
     ```json
     {
       "message": "{{message.body}}",
       "contact_id": "{{contact.id}}",
       "contact_name": "{{contact.name}}",
       "timestamp": "{{date.now}}"
     }
     ```

4. **Guardar y activar** el workflow

### 3. Crear tu GitHub PAT

1. Ir a [GitHub Settings → Developer settings → Personal access tokens](https://github.com/settings/tokens)
2. "Generate new token (classic)"
3. Permisos mínimos: `repo` (Full control of private repositories)
4. Copiar el token y usarlo en el paso 1.4

### 4. Habilitar Copilot Agent (opcional)

Para que Copilot procese automáticamente los issues:
1. Ir a Settings del repositorio → Copilot → General
2. Habilitar "Copilot coding agent"
3. Los issues con label `ai-task` serán procesados automáticamente

---

## Uso desde WhatsApp

### Formato básico
```
/tarea: Título de la tarea
```

### Con descripción detallada
```
/tarea: Optimizar meta descriptions de Cancún
---
Revisar los 18 posts de Cancún y actualizar las meta descriptions
que tengan menos de 120 caracteres. Usar las keywords del GSC.
```

### Con hashtags (labels automáticos)
```
/tarea: Crear artículo sobre snorkel en Huatulco #seo #huatulco #contenido
---
Crear un artículo SEO de 1500 palabras sobre snorkel en las bahías
de Huatulco. Seguir las reglas del workflow seo-blog-posts.md.
```

### Hashtags disponibles

| Hashtag | Label en GitHub |
|---|---|
| `#seo` | `seo` |
| `#urgente` | `priority-high` |
| `#bug` | `bug` |
| `#feature` | `enhancement` |
| `#docs` | `documentation` |
| `#contenido` | `content` |
| `#cancun` | `dest-cancun` |
| `#mazatlan` | `dest-mazatlan` |
| `#vallarta` | `dest-puerto-vallarta` |
| `#cabos` | `dest-los-cabos` |
| `#lapaz` | `dest-la-paz` |
| `#huatulco` | `dest-huatulco` |
| `#ixtapa` | `dest-ixtapa` |
| `#acapulco` | `dest-acapulco` |
| `#playa` | `dest-playa-del-carmen` |
| `#nayarit` | `dest-nuevo-vallarta` |

---

## Seguridad

| Medida | Cómo funciona |
|---|---|
| Token compartido | GHL envía `X-GHL-Token`, el webhook lo valida |
| Contactos autorizados | Solo IDs en la lista permitida pueden crear issues |
| Rate limiting | Máximo 10 issues por hora por contacto |
| Prefijo obligatorio | Solo mensajes con `/tarea:` disparan la creación |
| Secrets seguros | Tokens almacenados como secrets de Cloudflare, nunca en código |

## Verificar que funciona

```bash
# Test rápido con curl (reemplazar URL y token)
curl -X POST https://whatsapp-to-github.tu-cuenta.workers.dev \
  -H "Content-Type: application/json" \
  -H "X-GHL-Token: tu_secret_token" \
  -d '{
    "message": "/tarea: Test de integración WhatsApp",
    "contact_id": "test123",
    "contact_name": "Luis Velazquez",
    "timestamp": "2026-03-22T02:30:00Z"
  }'
```

Respuesta esperada:
```json
{
  "status": "created",
  "issue_number": 42,
  "issue_url": "https://github.com/YatezzitosMexico/yatezzitos-platform/issues/42",
  "title": "Test de integración WhatsApp"
}
```
