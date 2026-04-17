# GHL MCP Server — Fork Yatezzitos

Servidor MCP de GoHighLevel usado por Claude Desktop + Cowork para operar el CRM de Yatezzitos.

Fork de [`mastanley13/GoHighLevel-MCP`](https://github.com/mastanley13/GoHighLevel-MCP) con parches locales Yatezzitos (ver sección "Parches" abajo).

---

## Origen

- **Upstream:** `https://github.com/mastanley13/GoHighLevel-MCP` (commit base: `db18a89` — Initial release 269+ tools)
- **Snapshot vendor:** `22 mar 2026` — copiado desde `~/.gemini/antigravity/scratch/GoHighLevel-MCP/` al mover los MCPs custom fuera del directorio heredado de Anti Gravity.

## Parches Yatezzitos aplicados

1. `97914a5` — **feat: add parentId support to create_email_template and update_email_template**
2. `6e0fb1d` — **fix(email): wrap custom html inside templateData to avoid GHL default boilerplate**
3. `a1de489` — **fix(email): force editorType html on create to prevent GHL from injecting default builder boilerplate**
4. `89bb40e` — **fix(email): support raw html and updatedBy in emails/builder/data to prevent 422 error and override GHL boilerplate**

Estos 4 parches son la razón por la que vendoreamos el código en lugar de instalar el paquete npm. Tocan `src/services/` y cambian cómo se envía el HTML a `emails/builder/data` en la API de GHL para evitar que GHL reemplace el HTML personalizado con el builder por defecto.

---

## Build & run local (Docker)

El MCP corre en un contenedor local. La imagen se construye una vez y se referencia desde `~/Library/Application Support/Claude/claude_desktop_config.json`.

```bash
# Desde la raíz del repo
cd integrations/ghl-mcp

# Instalar deps y compilar TypeScript (primera vez o después de cambios)
npm install
npm run build

# Construir la imagen Docker
docker build -t ghl-mcp-server .
```

Después reiniciar Claude Desktop (o re-correr `scripts/update-claude-mcp-config.py`).

## Credenciales

El archivo `.env.ghl-mcp` vive en la **raíz del repo** (no aquí adentro), gitignored. Formato:

```bash
GHL_API_KEY=pit-...           # Private Integration Token de GHL
GHL_BASE_URL=https://services.leadconnectorhq.com
GHL_LOCATION_ID=4vhgNiuaT3jtuf3hWj35
```

Docker lo lee vía `--env-file ../../.env.ghl-mcp` cuando arranca el contenedor (ver `scripts/update-claude-mcp-config.py`).

## Desarrollo

- TypeScript source en `src/`
- Compila a `dist/` (gitignored — se genera con `npm run build`)
- Tests: `npm test`
- Lint: `npm run lint`

Si agregas un parche nuevo, actualiza la sección "Parches Yatezzitos aplicados" arriba con el commit hash + descripción.

## Traer cambios de upstream

Cuando `mastanley13/GoHighLevel-MCP` publique features nuevas:

```bash
# Opción pragmática: diff manual y port selectivo
git clone --depth=50 https://github.com/mastanley13/GoHighLevel-MCP.git /tmp/ghl-upstream
diff -r /tmp/ghl-upstream/src integrations/ghl-mcp/src
# aplicar lo que interese con parches manuales
```

No tenemos remote upstream aquí porque quitamos `.git/` al vendorear. Si se vuelve frecuente, considerar `git subtree pull`.

---

**Upstream:** MIT licensed — `mastanley13/GoHighLevel-MCP`
**Yatezzitos patches:** documentados en este repo
