---
name: email-generation
description: Skill para generar correos electrónicos HTML compatibles con GHL/Outlook que cumplen con los estándares de diseño y marca de Yatezzitos México. Incluye tokens de diseño, estructura, variables GHL, recursos de marca y plantillas de referencia.
---

# Email Generation Skill — Yatezzitos México

> **Propósito:** Cada vez que se necesite crear, editar o adaptar un correo electrónico para Yatezzitos, este documento es la fuente de verdad obligatoria de diseño y recursos.

---

## 1 · Sistemas de Diseño

Yatezzitos emplea **dos sistemas de diseño** según el tipo de correo:

### 1.1 — LIGHT theme (transaccional / cotizaciones / pagos)
Usado en: Cotización Enviada, Pago recibido, Fecha de pago, Correos internos operativos.

| Token | Valor |
|---|---|
| Background body | `#f5f7fb` |
| Card background | `#ffffff` |
| Card border | `1px solid #e5e7eb` ó `#e6e8ee` |
| Card border-radius | `14px` |
| Ribbon (banner) bg | `#000000` |
| Ribbon text color | `#ffffff` |
| Ribbon font-size | `13px`, `font-weight:700`, `letter-spacing:.4px`, `text-transform:uppercase` |
| Heading (H1) | `24px–28px`, `font-weight:800`, color `#000000` ó `#0b1939` |
| Body text | `14px–15px`, `line-height:22px`, color `#111827` |
| Muted text | `12px`, color `#6b7280` |
| Info box (green) | bg `#ecfdf5`, border `1px solid #b7ebd5`, text `#007a4d` ó `#0a6e4a` |
| Info box (blue) | bg `#eef4ff`, border `1px solid #dbe7ff`, text `#194395` |
| Info box (red/pending) | bg `#fff4f7`, border `1px solid #ffd8e4`, text `#ca003d` |
| Detail table | bg `#f9fbff`, border `1px solid #e8ecf4`, radius `10px` |
| Detail table label | `13px`, color `#4b5563` |
| Detail table value | `13px`, `font-weight:700`, color `#111827` |
| CTA button | bg `#194395`, radius `8px`, color `#ffffff`, font `15px`, `font-weight:800`, `letter-spacing:.6px`, `text-transform:uppercase`, width `320px–330px` |
| Footer text | `12px`, color `#6b7280` |
| Footer copy | `Yatezzitos México · Experiencias premium en yate` |

### 1.2 — DARK theme (notificaciones / campañas / día del viaje)
Usado en: Notificaciones 24h, Día del viaje, Combate Naval, Campañas masivas.

| Token | Valor |
|---|---|
| Background body | `#000000` ó `#060606` |
| Card background | `#0b0b0b` |
| Card border | `1px solid rgba(255,255,255,.10)` ó `.12` |
| Card border-radius | `16px` ó `18px` |
| Glassmorphic panels | bg `rgba(255,255,255,.06)`, border `rgba(255,255,255,.12)`, radius `14px` |
| Dark panels | bg `rgba(0,0,0,.24)` ó `.35`, border `rgba(255,255,255,.10)–.14`, radius `14px` |
| Badge/Tag | bg `rgba(255,255,255,.92)`, color `#0b2b3a`, radius `999px`, `12px`, `font-weight:900`, `uppercase` |
| Indicator dot (green) | `10px` circle, `#00945E`, box-shadow `0 0 0 4px rgba(0,148,94,.18)` |
| Indicator dot (red) | `10px` circle, `#CA003D`, box-shadow `0 0 0 4px rgba(202,0,61,.18)` |
| Heading (H1) | `28px–32px`, `font-weight:900`, color `#ffffff` |
| Accent highlight | color `#F6EA69` (amarillo Yatezzitos) |
| Body text | `14px–14.5px`, `line-height:1.6`, color `rgba(255,255,255,.92)` |
| Muted text | `color:rgba(255,255,255,.78)` ó `.70` |
| Labels (KV) | `11px–12px`, `letter-spacing:.7px`, `uppercase`, `font-weight:900`, `rgba(255,255,255,.78)` |
| Values (KV) | `14px`, `font-weight:800`, `rgba(255,255,255,.95)` |
| Chip | radius `999px`, border `rgba(255,255,255,.18)`, bg `rgba(0,0,0,.25)`, `12px`, `font-weight:800` |
| CTA Primary | bg `#194395`, radius `10px–12px`, color `#ffffff`, `13px`, `font-weight:900`, `letter-spacing:.8px`, `uppercase` |
| CTA Ghost | bg `rgba(255,255,255,.10)`, border `1px solid rgba(255,255,255,.20)`, same text |
| CTA WhatsApp | bg `rgba(0,148,94,.18)`, border `1px solid rgba(0,148,94,.40)` |
| Divider | `height:1px;background:rgba(255,255,255,.10–.14)` |
| Footer text | `12px`, `line-height:1.6`, `rgba(255,255,255,.70)` |
| Footer copy | `Yatezzitos México · Turismo náutico premium` |
| Footer links | color `#F6EA69`, `font-weight:900` |

---

## 2 · Recursos de Marca (URLs fijas)

### 2.1 — Logotipos

| Variante | URL | Uso |
|---|---|---|
| **Logo oscuro** (fondo blanco) | `https://assets.cdn.filesafe.space/4vhgNiuaT3jtuf3hWj35/media/656acf3f45e5a0601e677d1a.png` | LIGHT theme — centrado, `width:100px`, `height:auto` |
| **Logo blanco** (fondo oscuro) | `https://storage.googleapis.com/msgsndr/4vhgNiuaT3jtuf3hWj35/media/66e3c2fa3622c4a8958a615d.png` | DARK theme — centrado o alineado izquierda, `width:75px`, `height:auto` |

### 2.2 — Redes Sociales

| Red | URL |
|---|---|
| Instagram | `https://www.instagram.com/yatezzitos_mx` |
| Facebook | `https://www.facebook.com/Yatezzitos` |
| TikTok | `https://www.tiktok.com/@yatezzitosmexico?lang=es` |
| X (Twitter) | `https://x.com/yatezzitos` |
| YouTube | `https://www.youtube.com/channel/UCbuxpBSUvfAs0XqMV-HD7AA` |
| Pinterest | `https://mx.pinterest.com/yatezzitosm/?invite_code=000c8d57908a401b9ee47815918b21af&sender=924293660932145144` |
| Google Maps (negocio) | `https://www.google.com.mx/maps/place/Yatezzitos+M%C3%A9xico/@23.2708575,-106.5798378,12z/data=!4m7!3m6!1s0x869f5387b04c0f39:0xe45cf2b7af23dd6e!8m2!3d23.2708575!4d-106.4274025!15sCgp5YXRlenppdG9zkgETYm9hdF9yZW50YWxfc2VydmljZeABAA!16s%2Fg%2F11nn47p1bh?entry=tts` |
| Google Reviews | `https://g.page/r/CW7dI6-38lzkEBM/review` |
| WhatsApp contacto | `https://wa.me/526691324073` |

### 2.3 — URLs de Producto

| Recurso | URL / Variable |
|---|---|
| Sitio web | `https://yatezzitos.com/` |
| Cotización (dinámica) | `{{ contact.quote_url }}` |
| Página de gracias (reservas yates) | `https://yatezzitos.com/es/gracias/?qt={{contact.quote_token}}` |
| Página gracias (avistamientos) | `https://yatezzitos.com/gracias-avistamientos-de-ballena/?full_name={{contact.name}}&email={{contact.email}}&phone={{contact.phone}}&pasajeros={{contact.number_of_passengers}}&fecha_de_viaje={{contact.fecha_de_viaje}}` |

---

## 3 · Variables GHL (GoHighLevel)

### 3.1 — Variables del contacto (más usadas en emails)

| Variable | Descripción |
|---|---|
| `{{contact.name}}` | Nombre completo |
| `{{contact.first_name}}` | Primer nombre |
| `{{contact.full_name}}` | Nombre completo (alternativa) |
| `{{contact.email}}` | Email |
| `{{contact.phone}}` | Teléfono |
| `{{contact.yacht_name}}` | Nombre de la embarcación reservada |
| `{{contact.destinos}}` | Destino del viaje |
| `{{contact.fecha_de_viaje}}` | Fecha del viaje (campo DATE) |
| `{{contact.departure_time}}` | Hora de salida |
| `{{contact.hora_de_salida}}` | Hora de salida (alternativa) |
| `{{contact.return_time}}` | Hora de regreso |
| `{{contact.duration_hours}}` | Duración del viaje |
| `{{contact.number_of_passengers}}` | Número de pasajeros |
| `{{contact.marina_name}}` | Nombre de la marina |
| `{{contact.google_maps_link}}` | URL ubicación de abordaje (Google Maps) |
| `{{contact.total_cost}}` | Costo total del servicio |
| `{{contact.balance_due}}` | Balance restante |
| `{{contact.quote_url}}` | URL de la cotización (largo, dinámico) |
| `{{contact.quote_token}}` | Token de la cotización |
| `{{contact.experiencia_reservada}}` | Tipo de experiencia |
| `{{contact.inclusiones_adicionales}}` | Inclusiones adicionales |
| `{{contact.imagen_principal_del_yate_upload}}` | Imagen del yate |
| `{{contact.fecha_pago_anticipo_50}}` | Fecha compromiso pago anticipo |
| `{{contact.estado_de_la_reserva}}` | Estado: Confirmada/Cancelada/Pospuesta/Pendiente |
| `{{contact.reservacion_id}}` | ID de reservación |
| `{{contact.reservacion_url}}` | URL de la reservación |

### 3.2 — Custom values (location-level)

| Variable | Valor |
|---|---|
| `{{custom_values.url_cancn}}` | https://yatezzitos.com/es/ciudad/renta-de-yates-cancun/ |
| `{{custom_values.url_mazatln}}` | https://yatezzitos.com/es/ciudad/renta-de-yates-mazatlan/ |
| `{{custom_values.url__playa_del_carmen}}` | https://yatezzitos.com/es/ciudad/yates-playa-del-carmen/ |
| `{{custom_values.url_la_paz}}` | https://yatezzitos.com/es/ciudad/renta-de-yates-en-la-paz/ |
| `{{custom_values.url__ixtapa}}` | https://yatezzitos.com/es/ciudad/yates-ixtapa/ |
| `{{custom_values.url__puerto_vallarta}}` | https://yatezzitos.com/es/ciudad/renta-de-yates-en-puerto-vallarta/ |
| `{{custom_values.url__nuevo_vallarta}}` | https://yatezzitos.com/es/ciudad/yates-en-nuevo-vallarta/ |
| `{{custom_values.url__huatulco}}` | https://yatezzitos.com/es/ciudad/yates-huatulco/ |
| `{{custom_values.url__los_cabos}}` | https://yatezzitos.com/es/ciudad/yates-los-cabos/ |
| `{{custom_values.url__acapulco}}` | https://yatezzitos.com/es/ciudad/yates-acapulco/ |
| `{{custom_values.cinco_estrellas}}` | https://g.page/r/CW7dI6-38lzkEBM/review |
| `{{custom_values.quote_url}}` | URL dinámica completa con datos de cotización |

---

## 4 · Estructura HTML Obligatoria

### 4.1 — Anatomía de un correo Yatezzitos

```
┌────────────────────────────────────────┐
│ PREHEADER (oculto, 90 chars max)       │
├────────────────────────────────────────┤
│ BODY bg (light: #f5f7fb / dark: #000)  │
│ ┌──────────────────────────────────┐   │
│ │ CARD (640px max, centered)       │   │
│ │ ┌──────────────────────────────┐ │   │
│ │ │ LOGO (centrado)              │ │   │
│ │ ├──────────────────────────────┤ │   │
│ │ │ RIBBON (banner tipo barra)   │ │   │
│ │ ├──────────────────────────────┤ │   │
│ │ │ CONTENIDO PRINCIPAL          │ │   │
│ │ │ • Heading (H1)               │ │   │
│ │ │ • Saludo personal            │ │   │
│ │ │ • Info box (status/alert)    │ │   │
│ │ │ • Tabla de detalles          │ │   │
│ │ │ • CTA principal              │ │   │
│ │ │ • Fallback link              │ │   │
│ │ │ • Sección "Próximos pasos"   │ │   │
│ │ ├──────────────────────────────┤ │   │
│ │ │ SOCIAL ICONS (dark only)     │ │   │
│ │ ├──────────────────────────────┤ │   │
│ │ │ FOOTER                       │ │   │
│ │ └──────────────────────────────┘ │   │
│ └──────────────────────────────────┘   │
└────────────────────────────────────────┘
```

### 4.2 — Reglas técnicas obligatorias (STRICT Email-Safe & Mobile-Safe)

1. **Tabla-based layout 100% puro** — Usar únicamente `<table role="presentation">` para toda la estructura. **Cero `div` para maquetación.**
2. **Wrapper externo y contenedor fijo** — Wrapper exterior al 100% de ancho. Contenedor interno fijo de 640px oblitagorio: `width="640"` + `style="width:640px; max-width:640px; table-layout:fixed;"`.
3. **Cero scroll horizontal (Mobile-safe)** — En móvil, **elimina el padding lateral** del contenedor exterior; el padding limitante debe controlarse solo del lado interno de las tablas. Nunca uses anchos que superen el contenedor ni bordes que se sumen al 100% de ancho.
4. **Imágenes fluidas seguras** — Todas las imágenes deben llevar estrictamente `display:block; max-width:100%; height:auto;`.
5. **Botones CTA Bulletproof (Estrictos)** — NUNCA hagas botones con `<a>` inline-block + borde + ancho fijo. Los CTA deben construirse insertando una tabla anidada de ancho fijo, y el `<a>` dentro con `display:block; width:100%; max-width:100%; box-sizing:border-box; text-decoration:none;`.
6. **Desbordes y rgba()** — Evita elementos que rompan viewport (SVG sueltos, anchos fijos anidados mal calculados). Evita `rgba()` en elementos de alto contraste porque Outlook no lo soporta; usa HEX `#XXXXXX`.
7. **Font family** — `font-family:"Barlow", Arial, Helvetica, sans-serif` (LIGHT) ó `font-family:Arial, Helvetica, sans-serif` (DARK). Siempre con fallback seguro.
8. **Google Fonts** — Solo en LIGHT: `<link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;700;800&display=swap">`.
9. **Outlook VML** — Botones con fallback `<!--[if mso]>...<![endif]-->` usando `v:roundrect`.
10. **Outlook color fix** — Links con `mso-style-textfill-fill-color:` + `<!--[if mso]><font color="">...<![endif]-->`.
11. **Cero Modern CSS** — Nunca JavaScript. Nunca CSS Grid / Flexbox. Siempre pensar en MS Word rendering engine (Outlook).
12. **Preheader y Alt text** — Texto oculto de previsualización arriba del email. Atributo `alt` obligatorio en todas las imágenes.

### 4.3 — Preheader snippet (copiar/pegar)

```html
<!-- LIGHT theme -->
<td style="display:none;visibility:hidden;mso-hide:all;opacity:0;color:transparent;height:0;width:0;overflow:hidden;line-height:1px;font-size:1px;">
  TEXTO PREHEADER AQUÍ
</td>

<!-- DARK theme -->
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;font-size:1px;line-height:1px;">
  TEXTO PREHEADER AQUÍ
</div>
```

### 4.4 — CTA Button snippet (copiar/pegar)

```html
<!-- Bulletproof CTA (Mobile Safe & Anti-scroll) -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td align="center" style="padding: 10px 0;">
      <!-- La tabla interna controla el ancho del botón para compatibilidad universal -->
      <table role="presentation" width="330" cellpadding="0" cellspacing="0" border="0" style="width:330px;max-width:100%;">
        <tr>
          <td align="center" bgcolor="#194395" style="border-radius:8px;">
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
              href="URL_AQUI" style="height:48px;v-text-anchor:middle;width:330px;" arcsize="12%" stroke="f" fillcolor="#194395">
              <w:anchorlock/>
              <center style="color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;">
                TEXTO CTA
              </center>
            </v:roundrect>
            <![endif]-->
            <!--[if !mso]><!-- -->
            <a href="URL_AQUI" target="_blank" rel="noopener"
               style="background:#194395;border-radius:8px;color:#ffffff;display:block;font-family:'Barlow',Arial,Helvetica,sans-serif;font-size:15px;font-weight:800;line-height:48px;text-align:center;text-transform:uppercase;letter-spacing:.6px;width:100%;max-width:100%;box-sizing:border-box;text-decoration:none;">
              Texto CTA
            </a>
            <!--<![endif]-->
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
```

### 4.5 — Social Icons Block (DARK theme, copiar/pegar)

```html
<!-- Social Icons Row (DARK theme, inline SVG, fondo negro, iconos blancos) -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <!-- Instagram -->
    <td style="padding:0 6px;">
      <a href="https://www.instagram.com/yatezzitos_mx" target="_blank" rel="noopener" style="text-decoration:none;">
        <span style="display:inline-block;width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);text-align:center;line-height:40px;">
          <svg width="18" height="18" viewBox="0 0 24 24" style="vertical-align:middle;">
            <path fill="#FFFFFF" d="M7 2C4.24 2 2 4.24 2 7v10c0 2.76 2.24 5 5 5h10c2.76 0 5-2.24 5-5V7c0-2.76-2.24-5-5-5H7zm10 2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3h10zm-5 3.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5zm0 7.4A2.9 2.9 0 1 1 14.9 12 2.9 2.9 0 0 1 12 14.9zM17.6 6.4a1 1 0 1 0 1 1 1 1 0 0 0-1-1z"/>
          </svg>
        </span>
      </a>
    </td>
    <!-- Facebook -->
    <td style="padding:0 6px;">
      <a href="https://www.facebook.com/Yatezzitos" target="_blank" rel="noopener" style="text-decoration:none;">
        <span style="display:inline-block;width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);text-align:center;line-height:40px;">
          <svg width="18" height="18" viewBox="0 0 24 24" style="vertical-align:middle;">
            <path fill="#FFFFFF" d="M22 12a10 10 0 1 0-11.56 9.87v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.62.77-1.62 1.56V12h2.76l-.44 2.88h-2.32v6.99A10 10 0 0 0 22 12z"/>
          </svg>
        </span>
      </a>
    </td>
    <!-- TikTok -->
    <td style="padding:0 6px;">
      <a href="https://www.tiktok.com/@yatezzitosmexico?lang=es" target="_blank" rel="noopener" style="text-decoration:none;">
        <span style="display:inline-block;width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);text-align:center;line-height:40px;">
          <svg width="18" height="18" viewBox="0 0 24 24" style="vertical-align:middle;">
            <path fill="#FFFFFF" d="M16.5 2h-2.3v13.2a3.3 3.3 0 1 1-3.3-3.3c.3 0 .6 0 .9.1V9.7a5.7 5.7 0 1 0 5.7 5.7V8.2c1 .8 2.3 1.3 3.7 1.3V7.2c-2.2-.1-4-1.9-4.4-5.2z"/>
          </svg>
        </span>
      </a>
    </td>
    <!-- X (Twitter) -->
    <td style="padding:0 6px;">
      <a href="https://x.com/yatezzitos" target="_blank" rel="noopener" style="text-decoration:none;">
        <span style="display:inline-block;width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);text-align:center;line-height:40px;">
          <svg width="18" height="18" viewBox="0 0 24 24" style="vertical-align:middle;">
            <path fill="#FFFFFF" d="M18.9 2H22l-6.8 7.8L23 22h-6.7l-5.2-6.7L5.6 22H2l7.3-8.4L1 2h6.9l4.7 6.1L18.9 2zm-1.2 18h1.7L7.1 3.9H5.3L17.7 20z"/>
          </svg>
        </span>
      </a>
    </td>
    <!-- YouTube -->
    <td style="padding:0 6px;">
      <a href="https://www.youtube.com/channel/UCbuxpBSUvfAs0XqMV-HD7AA" target="_blank" rel="noopener" style="text-decoration:none;">
        <span style="display:inline-block;width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);text-align:center;line-height:40px;">
          <svg width="18" height="18" viewBox="0 0 24 24" style="vertical-align:middle;">
            <path fill="#FFFFFF" d="M21.6 7.2a2.7 2.7 0 0 0-1.9-1.9C18 4.9 12 4.9 12 4.9s-6 0-7.7.4A2.7 2.7 0 0 0 2.4 7.2 28.3 28.3 0 0 0 2 12a28.3 28.3 0 0 0 .4 4.8 2.7 2.7 0 0 0 1.9 1.9c1.7.4 7.7.4 7.7.4s6 0 7.7-.4a2.7 2.7 0 0 0 1.9-1.9A28.3 28.3 0 0 0 22 12a28.3 28.3 0 0 0-.4-4.8zM10.2 15.4V8.6L16 12l-5.8 3.4z"/>
          </svg>
        </span>
      </a>
    </td>
    <!-- Google Maps -->
    <td style="padding:0 6px;">
      <a href="https://www.google.com.mx/maps/place/Yatezzitos+M%C3%A9xico" target="_blank" rel="noopener" style="text-decoration:none;">
        <span style="display:inline-block;width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);text-align:center;line-height:40px;">
          <svg width="18" height="18" viewBox="0 0 24 24" style="vertical-align:middle;">
            <path fill="#FFFFFF" d="M12 2a7 7 0 0 0-7 7c0 5.2 7 13 7 13s7-7.8 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/>
          </svg>
        </span>
      </a>
    </td>
    <!-- Pinterest -->
    <td style="padding:0 6px;">
      <a href="https://mx.pinterest.com/yatezzitosm/" target="_blank" rel="noopener" style="text-decoration:none;">
        <span style="display:inline-block;width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);text-align:center;line-height:40px;">
          <svg width="18" height="18" viewBox="0 0 24 24" style="vertical-align:middle;">
            <path fill="#FFFFFF" d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2.1 0-3l1.5-6.2s-.4-.8-.4-2c0-1.9 1.1-3.3 2.5-3.3 1.2 0 1.7.9 1.7 1.9 0 1.2-.8 3-1.2 4.6-.3 1.4.7 2.5 2.1 2.5 2.5 0 4.4-2.6 4.4-6.4 0-3.4-2.4-5.7-5.8-5.7-4 0-6.3 3-6.3 6.1 0 1.2.5 2.5 1.1 3.2.1.1.1.3.1.4l-.4 1.6c-.1.3-.3.4-.6.3-1.7-.8-2.7-3.2-2.7-5.2 0-4.2 3-8.1 8.8-8.1 4.6 0 8.2 3.3 8.2 7.7 0 4.6-2.9 8.3-6.9 8.3-1.3 0-2.6-.7-3.1-1.5l-.8 3c-.3 1-.9 2.2-1.3 2.9.9.3 1.9.5 2.9.5A10 10 0 0 0 12 2z"/>
          </svg>
        </span>
      </a>
    </td>
  </tr>
</table>
```

---

## 5 · Reglas de Redacción y Tono

1. **Español mexicano**, profesional, cálido, con toques de exclusividad premium.
2. **Tuteo** — usar "tú" con el cliente, nunca "usted".
3. **Emojis con moderación** — 1–3 por correo, usados estratégicamente (⚓, 🐋, 🔥, 🎆, 😎).
4. **Urgencia sutil** — crear sentido de importancia sin ser agresivo.
5. **Lenguaje aspiracional** — "experiencia", "premium", "exclusivo", "adventure".
6. **Llamada a la acción clara** — un solo CTA principal por correo.
7. **Correos internos** — tono directo, operativo, orientado a acción inmediata.
8. **Soporte** — siempre ofrecer responder el correo o contactar por WhatsApp.

---

## 6 · Cuándo usar cada tema

| Tipo de correo | Tema | Logo | Social Icons |
|---|---|---|---|
| Cotización enviada | LIGHT | Oscuro, 100px | No |
| Recordatorio de pago | LIGHT | Oscuro, 100px | No |
| Confirmación de pago | LIGHT | Oscuro, 100px | No |
| Correo interno operativo | LIGHT | Oscuro, 100px | No |
| Notificación pre-viaje (7+ días) | LIGHT | Oscuro, 100px | No |
| Notificación pre-viaje (3 días o menos) | DARK | Blanco, 75px | Sí |
| Día del viaje | DARK | Blanco, 75px | Sí |
| Campañas masivas / promociones | DARK | Blanco, 75px | Opcional |
| Reseñas / post-viaje | DARK | Blanco, 75px | Sí |

---

## 7 · Checklist de Calidad (antes de entregar cualquier correo)

- [ ] ¿HTML válido con `<!doctype html>` y `lang="es"`?
- [ ] ¿Preheader oculto incluido?
- [ ] ¿Logo correcto según el tema (dark vs. light)?
- [ ] ¿Ribbon/banner con texto descriptivo?
- [ ] ¿Heading (H1) visible y prominente?
- [ ] ¿Saludo con `{{contact.name}}` o `{{contact.first_name}}`?
- [ ] ¿Variables GHL correctas (sin typos)?
- [ ] ¿CTA con fallback MSO/Outlook?
- [ ] ¿Fallback link debajo del CTA?
- [ ] ¿Responsive con media query?
- [ ] ¿Footer con copyright de Yatezzitos?
- [ ] ¿Social icons (si aplica)?
- [ ] ¿Sin JavaScript ni CSS Grid/Flexbox?
- [ ] ¿Colores consistentes con la tabla de tokens?
- [ ] ¿Texto legible (mínimo 12px)?

---

## 8 · Referencia rápida de archivos existentes

Los correos de referencia están en:
```
ghl-data/emails/
├── Avistamientos de ballenas/     🌊 dark theme, notificaciones
├── Combate Naval/                 🎆 dark theme, notificaciones
├── Correos Internos/              📋 light theme, operativo
├── Cotización enviada/            💼 light theme, transaccional
├── Definio fecha de pago/         💳 light theme, transaccional
├── Email - Masivos/               📢 dark theme, campañas
├── Generados IA Agente/           🤖 legacy (NO usar como referencia)
├── Plantilla_Rescate_-_30_Dias_IA/ ⚠️ legacy (NO usar como referencia)
├── Recibo de deposito/            🧾 light theme, transaccional
└── Renta de yates/                ⛵ light theme, transaccional
```

> **⚠️ NOTA:** Las plantillas dentro de `Generados IA Agente/` y `Plantilla_Rescate_-_30_Dias_IA/` son templates legacy antiguos que **NO siguen** el estándar de diseño actual. **Nunca usarlos como referencia de diseño.** Siempre tomar como modelo los correos de `Cotización enviada/`, `Renta de yates/`, `Correos Internos/` (LIGHT) y `Avistamientos de ballenas/`, `Combate Naval/`, `Email - Masivos/` (DARK).

---

## 9 · Catálogo de Contenido para Nutrir Emails

Para enriquecer los correos con enlaces a embarcaciones, destinos y artículos del blog, consultar el archivo:

📁 **[`resources/catalog.md`](resources/catalog.md)**

Este catálogo contiene:

| Recurso | Cantidad | Uso en emails |
|---|---|---|
| **Embarcaciones** | 85 yates en 10 ciudades | Link a la ficha del yate reservado, sugerencias de otros yates |
| **Ciudades** | 10 destinos con URL + variable GHL | Link "Explora tu destino", CTAs secundarios |
| **Blog posts** | 50+ artículos por destino | Nutrición pre-viaje ("Descubre qué hacer en…"), tips, guías |

### Uso recomendado por tipo de email:

| Tipo de correo | Contenido del catálogo a incluir |
|---|---|
| **60–30 días antes** | Link a guía de la ciudad + 1–2 blog posts del destino |
| **15–7 días antes** | Link a blog de "qué empacar" o "mejores playas" del destino |
| **3–0 días antes** | Solo datos operativos (marina, mapa, hora), sin blog |
| **Internos** | Link a ficha del yate + datos del contacto |
| **Campañas masivas** | Links a múltiples destinos y embarcaciones |
| **Post-viaje / Reseñas** | Link a Google Reviews + yate específico |

### Datos de cada embarcación disponibles via JSON:

Los datos completos de cada yate se leen de:
```
data/yachts/Destinos/{Ciudad}/{Tipo}/{slug}.json
```

Campos disponibles: `Nombre del Yate`, `Ciudad/Ubicación`, `Categoría`, `URL de Reserva`, `Precio`, `Capacidad`, `Amenidades/Incluye`, `Año Construcción`, `Ubicación de Abordaje`, `Descripción`.
