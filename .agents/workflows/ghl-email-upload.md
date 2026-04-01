---
description: Crear o actualizar plantillas de email HTML en GoHighLevel vía API REST (sin navegador)
---

# Workflow: Generar y Subir Emails a GoHighLevel

Uso: cuando se necesite crear un nuevo template de correo en GHL o actualizar uno existente.

---

## Variables de entorno requeridas

Están en `/Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp`:

```
GHL_API_KEY=...
GHL_LOCATION_ID=...
```

Siempre leerlas así (nunca hardcodear):
```bash
GHL_API_KEY=$(grep GHL_API_KEY /Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp | cut -d= -f2)
GHL_LOCATION_ID=$(grep GHL_LOCATION_ID /Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp | cut -d= -f2)
```

---

## Paso 1 — Generar el HTML del email

Seguir el SKILL obligatorio antes de escribir cualquier HTML:
👉 `.agents/skills/email-generation/SKILL.md`

**Reglas críticas de HTML para emails:**
- `<div style="display:none;max-height:0;overflow:hidden;">` para el **preheader** — NUNCA un `<td>` huérfano (se renderiza visible)
- `margin-bottom` en `<table>` NO funciona en clientes de email — usar filas spacer en su lugar:
  ```html
  <tr><td style="height:14px;line-height:14px;font-size:14px;">&nbsp;</td></tr>
  ```
- Bordes con `border-top:1px solid #E5E7EB` en las `<td>` directamente (no usar `<div>` con clase `.hr`)
- Todo en tablas anidadas — cero `<div>` para layout
- `rgba()` en borders puede fallar en Outlook — preferir hex opaco o `border:1px solid #E5E7EB`

**Guardar el HTML localmente en:**
```
ghl-data/emails/{Nombre-Carpeta}/{Nombre-Template}/index.html
```

---

## Paso 2 — Crear template nuevo en GHL

**Endpoint:** `POST https://services.leadconnectorhq.com/emails/builder`

**Campos que acepta:**
```json
{
  "locationId": "...",
  "title": "Nombre visible en GHL",
  "name": "Nombre visible en GHL",
  "subjectLine": "Asunto del correo",
  "fromName": "Yatezzitos México",
  "builderVersion": "2",
  "isPlainText": false
}
```

⚠️ **El HTML NO se puede inyectar en la creación.** El campo `customHtml` o `html` en el POST es ignorado por GHL. El template se crea vacío (con diseño por defecto de GHL).

✅ **El ID que retorna el POST se usa en el siguiente paso para inyectar el HTML vía PATCH.**

Respuesta exitosa:
```json
{"ok": true, "id": "...", "status": "ok"}
```

---

## Paso 3 — Actualizar el HTML del template (PATCH)

**Endpoint:** `PATCH https://services.leadconnectorhq.com/emails/builder/{templateId}`

**Campo correcto para el HTML:** `"html"` (NO `customHtml`, NO `body`)

```python
# Script completo y funcional — copiar a /tmp/upload_ghl.py y ejecutar con python3
import json, subprocess

env = {}
with open('/Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp') as f:
    for line in f:
        line = line.strip()
        if '=' in line and not line.startswith('#'):
            k, v = line.split('=', 1)
            env[k.strip()] = v.strip()

API_KEY = env['GHL_API_KEY']
LOC_ID  = env['GHL_LOCATION_ID']

templates = [
    {
        "id": "GHL_TEMPLATE_ID_AQUI",
        "file": "/ruta/al/index.html",
        "name": "Nombre del template",
        "subject": "Asunto del correo"
    }
]

for t in templates:
    html = open(t['file'], 'r').read()
    payload = json.dumps({
        "locationId": LOC_ID,
        "title": t['name'],
        "name": t['name'],
        "subjectLine": t['subject'],
        "fromName": "Yatezzitos Mexico",
        "builderVersion": "2",
        "isPlainText": False,
        "html": html
    }, ensure_ascii=False)

    tmp = f"/tmp/payload_{t['id']}.json"
    with open(tmp, 'w') as f:
        f.write(payload)

    result = subprocess.run([
        'curl', '-s', '-X', 'PATCH',
        f"https://services.leadconnectorhq.com/emails/builder/{t['id']}",
        '-H', f'Authorization: Bearer {API_KEY}',
        '-H', 'Content-Type: application/json',
        '-H', 'Version: 2021-07-28',
        '-d', f'@{tmp}'
    ], capture_output=True, text=True, timeout=30)

    print(f"Template: {t['name']}")
    print(f"Response: {result.stdout[:300]}")
```

Ejecutar con:
```bash
python3 /tmp/upload_ghl.py
```

Respuesta exitosa:
```json
{"ok": true, "id": "...", "name": "...", "builderVersion": "2", ...}
```

---

## Listado de templates existentes en GHL

Para ver todos los templates y sus IDs:
```bash
GHL_API_KEY=$(grep GHL_API_KEY /Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp | cut -d= -f2)
GHL_LOCATION_ID=$(grep GHL_LOCATION_ID /Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp | cut -d= -f2)
curl -s "https://services.leadconnectorhq.com/emails/builder?locationId=${GHL_LOCATION_ID}&limit=100" \
  -H "Authorization: Bearer ${GHL_API_KEY}" \
  -H "Version: 2021-07-28" | python3 -c "
import json,sys
d=json.loads(sys.stdin.read())
for item in d.get('builders',[]):
    print(item.get('id'), '|', item.get('name'), '|', item.get('templateType'))
"
```

---

## Templates creados en esta sesión (Marzo 2026)

| Template | GHL ID | Archivo local |
|---|---|---|
| Cliente - Bienvenida - Solicitud Reserva | `69cc585975473b2c18943a2b` | `ghl-data/emails/Solicitud - España/Cliente - Bienvenida - Solicitud España/index.html` |
| Interno - Nueva solicitud de reserva | `69cc586c3e46be40dcbb47a8` | `ghl-data/emails/Solicitud - España/Interno - Nueva solicitud - España/index.html` |

Variables GHL mapeadas del webhook de solicitud de reserva:
- `{{contact.name}}` / `{{contact.first_name}}` — Nombre del cliente
- `{{contact.phone}}` — Teléfono
- `{{contact.email}}` — Correo
- `{{contact.message}}` — Mensaje del formulario
- `{{contact.yacht_name}}` — Nombre del yate solicitado
- `{{contact.url_del_yate}}` — URL de la ficha del yate
- `{{contact.reservacion_id}}` — ID de la reserva

---

## Errores conocidos y soluciones

| Error | Causa | Solución |
|---|---|---|
| `"message": "Cannot POST /emails/templates"` | Endpoint incorrecto | Usar `/emails/builder` (no `/emails/templates`) |
| `"locationId must be a string"` | PATCH sin locationId | Siempre incluir `locationId` en el body del PATCH |
| HTML no se aplica al crear (POST) | `customHtml` ignorado en POST | Crear con POST, luego actualizar HTML con PATCH separado |
| Texto del preheader visible en el correo | `<td>` huérfano como preheader | Usar `<div style="display:none;...">` dentro del `<body>` |
| Texto se sale de la caja del email | `margin-bottom` en `<table>` | Usar filas spacer `<tr><td style="height:14px">` |
| SSL error con `urllib` de Python | Certificados macOS | Usar `subprocess + curl` en vez de `urllib.request` |
| Comando curl se cuelga sin output | Payload grande + comandos encadenados | Usar el script Python con `subprocess.run(timeout=30)` por template |
