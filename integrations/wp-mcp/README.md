# WordPress MCP — Yatezzitos

Conecta Claude Desktop / Cowork a la REST API de WordPress de `yatezzitos.com`.

Wrapper del paquete npm [`server-wp-mcp`](https://www.npmjs.com/package/server-wp-mcp) v1.0.1 — **sin código custom**, sólo un `package.json` + archivo de sitios + config.

---

## Setup (una sola vez)

Requiere **Node 18+**.

```bash
cd integrations/wp-mcp
npm install
```

Esto instala `server-wp-mcp` en `node_modules/` (gitignored).

## Configurar credenciales

1. Copiar el ejemplo:
   ```bash
   cp wp-sites.example.json wp-sites.json
   ```

2. Editar `wp-sites.json` con las credenciales reales. Formato:
   ```json
   {
     "yatezzitos": {
       "URL": "https://yatezzitos.com",
       "USER": "Yatezzitos",
       "PASS": "XXXX XXXX XXXX XXXX XXXX XXXX"
     }
   }
   ```

3. Generar el `PASS` desde WordPress:
   - Login → **Users → Profile → Application Passwords**
   - Nombre: "Claude MCP"
   - Copiar la clave de 24 caracteres con espacios (formato `XXXX XXXX XXXX XXXX XXXX XXXX`)
   - **No commitear.** El `.gitignore` local bloquea `wp-sites.json`.

## Claude Desktop config

El comando vive en `scripts/update-claude-mcp-config.py` (sección `wordpress`):

- Command: `node`
- Script: `integrations/wp-mcp/node_modules/server-wp-mcp/dist/index.js`
- Env: `WP_SITES_PATH` → `integrations/wp-mcp/wp-sites.json`

Después de `npm install` y de crear `wp-sites.json`, correr:

```bash
python3 scripts/update-claude-mcp-config.py
```

## Reglas críticas heredadas

1. **Yoast SEO:** nunca actualizar campos Yoast vía el endpoint estándar (`meta: {_yoast_wpseo_*}` no persiste). Usar siempre el plugin propio `POST /yatezzitos/v1/update-yoast` (código en `plugins/yatezzitos-yoast-rest-api/`). Ver detalles en `.agents/workflows/seo-wordpress-mcp.md`.

2. **Application Password ≠ contraseña de login.** La App Password sólo sirve para la REST API y se puede revocar sin cerrar sesión. Si rotas la del MCP, hay que actualizar `wp-sites.json` local y reiniciar Claude.

## Verificación rápida

```bash
cd integrations/wp-mcp
WP_SITES_PATH=./wp-sites.json node node_modules/server-wp-mcp/dist/index.js
```

Debería quedarse escuchando stdio sin errores. `Ctrl+C` para salir.

---

**Upstream npm:** [server-wp-mcp](https://www.npmjs.com/package/server-wp-mcp) (MIT)
**Vendor snapshot:** 16 abr 2026 — v1.0.1
