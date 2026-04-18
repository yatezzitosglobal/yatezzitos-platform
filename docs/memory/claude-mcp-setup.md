# Configuración de MCPs (Claude Desktop + Claude Code + Antigravity)

> Guía operativa del sistema de sincronización de MCPs del proyecto. Una fuente única de verdad committed al repo se propaga automáticamente a los 3 clientes cada vez que el usuario inicia sesión en macOS.
> **Este archivo no contiene secretos.** Todos los tokens viven en archivos `.env.*` locales gitignored, cargados por wrappers shell — nunca aparecen en el `mcpServers` de ningún config.

---

## Arquitectura de sincronización

```
┌──────────────────────────────────────────────────────────┐
│  integrations/mcp-servers.json  (source of truth, commit)│
│  - catalogo de MCPs con command/args/env-refs            │
│  - placeholders {{REPO_ROOT}} y {{HOME}}                 │
│  - lista de targets con estrategia merge/replace         │
└──────────────────────────────────────────────────────────┘
                         │
                         ▼
                scripts/sync-mcp.py
     (idempotente, backups 600, escritura atómica)
                         │
        ┌────────────────┼────────────────┐
        ▼                ▼                ▼
 Claude Desktop     .mcp.json        Antigravity
 claude_desktop_   (repo root,      ~/.gemini/antigravity/
  config.json     gitignored)         mcp_config.json

    ── Disparado al login via LaunchAgent ──
  ~/Library/LaunchAgents/com.yatezzitos.mcp-sync.plist
```

**Fuente única de verdad:** [`integrations/mcp-servers.json`](../../integrations/mcp-servers.json). Ahí se declaran todos los MCPs con sus `command`, `args`, `env` (sin secretos — solo paths a archivos gitignored) y `requires`.

**Sync script:** [`scripts/sync-mcp.py`](../../scripts/sync-mcp.py). Resuelve placeholders, valida `requires`, y escribe a cada target con estrategia `merge` (preserva MCPs existentes que no declare el source) o `replace`. Hace backup timestamped 600 antes de cada escritura.

**LaunchAgent:** `~/Library/LaunchAgents/com.yatezzitos.mcp-sync.plist`. Ejecuta el sync al login del usuario, con PATH ampliado (`/opt/homebrew/bin`, `~/.local/bin`, etc.) para que detecte `docker`, `node`, `uvx`. Log en `/tmp/yzz-mcp-sync.log`.

### Flujo diario

| Acción | Qué hacer |
|---|---|
| **Añadir MCP nuevo** | Editar `integrations/mcp-servers.json` → si requiere credenciales, crear wrapper `integrations/<nombre>/run.sh` + `.env.<nombre>` gitignored → correr `python3 scripts/sync-mcp.py` → reiniciar clientes |
| **Rotar credencial** | Editar el `.env.*` privado. Reiniciar el cliente. No hay que tocar configs |
| **Sincronización forzada** | `python3 scripts/sync-mcp.py` |
| **Ver el último sync** | `tail -30 /tmp/yzz-mcp-sync.log` |
| **Deshabilitar auto-sync** | `launchctl bootout gui/$(id -u)/com.yatezzitos.mcp-sync` |

### Dónde vive cada config (sólo para referencia — el script los maneja)

| Cliente | Ruta | Notas |
|---|---|---|
| Claude Desktop | `~/Library/Application Support/Claude/claude_desktop_config.json` | Preserva `preferences` (Cowork, etc.) |
| Claude Code | `<repo>/.mcp.json` | Gitignored (paths absolutos no-portables). Se usa al abrir el proyecto |
| Antigravity | `~/.gemini/antigravity/mcp_config.json` | Merge con `figma` y `github-mcp-server` heredados |

---

## Formato del source of truth

Estructura de `integrations/mcp-servers.json`:

```json
{
  "version": 1,
  "mcpServers": {
    "nombre-del-mcp": {
      "description": "breve descripción (no se propaga al config)",
      "command": "{{REPO_ROOT}}/integrations/.../run.sh",
      "args": ["..."],
      "env": { "VAR": "{{REPO_ROOT}}/ruta/a/archivo" },
      "requires": ["docker", "{{REPO_ROOT}}/.env.mi-mcp"]
    }
  },
  "targets": [
    { "name": "claude-desktop", "path": "...", "strategy": "merge", "preserve_keys": ["preferences"] }
  ]
}
```

- `description`, `requires` son solo para gestión interna — el script los filtra antes de escribir.
- `requires`: lista de paths (absolutos o con placeholder) y/o binarios en PATH. El script avisa si falta algo pero no aborta.
- `env` no debe contener valores sensibles — solo paths a archivos gitignored o flags no-sensibles.

---

## 0. Antes de tocar nada: rotar secretos

Los siguientes valores estaban en texto plano en `mcp_config.json` y/o en archivos `task.md` de Anti Gravity. **Rotar antes de reutilizar:**

- [ ] **GitHub Personal Access Token** — revocar el PAT viejo (`ghp_***`) en https://github.com/settings/tokens y generar uno nuevo con scope `repo` (+ `workflow` si hace falta)
- [x] **GHL API Key / Private Integration Token** — rotado 16 abr 2026 en GoHighLevel → Settings → Private Integrations. Token viejo revocado, nuevo PIT activo y validado vía `/wp-json/yzz/v1/contact-live` (`_meta.source: ghl_live`).
- [ ] **Figma API Key** — si existe, revisar en Figma → Settings → Personal access tokens
- [ ] Revisar que `.env.ghl-mcp` sigue en `.gitignore` (lo está, pero confirmar que nunca se haya commiteado)

Los nuevos tokens se guardan en `~/.yatezzitos-secrets.env` (fuera del repo) con formato:

```bash
export GITHUB_PERSONAL_ACCESS_TOKEN="ghp_..."
export FIGMA_API_KEY="figd_..."
export GHL_PRIVATE_INTEGRATION_TOKEN="pit-..."
```

Y se cargan antes de arrancar Claude Desktop (o se meten en un Launch Agent que los exporta al entorno del usuario).

---

## 1. GoHighLevel (CRM)

**Transport:** Docker local (imagen propia `ghl-mcp-server`, forkeada de `mastanley13/GoHighLevel-MCP` + 4 parches Yatezzitos).

**Ubicación del fuente:** `integrations/ghl-mcp/` (vendoreado dentro del repo — antes vivía en `~/.gemini/antigravity/scratch/GoHighLevel-MCP/`).

**Prerequisitos:**
- Docker Desktop corriendo
- Imagen `ghl-mcp-server` construida desde `integrations/ghl-mcp/`
- `.env.ghl-mcp` con credenciales en la raíz del repo (`/Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp`) — gitignored

**Config:**

```json
"go-high-level": {
  "command": "docker",
  "args": [
    "run", "-i", "--rm",
    "--env-file", "/Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp",
    "ghl-mcp-server",
    "node", "dist/server.js"
  ]
}
```

**Build inicial / reconstrucción tras parches:**

```bash
cd /Users/luisvelazquez/Projects/yatezzitos-platform/integrations/ghl-mcp
npm install
npm run build
docker build -t ghl-mcp-server .
```

Los 4 parches Yatezzitos vigentes (todos sobre email builder API) están documentados en `integrations/ghl-mcp/YATEZZITOS.md`.

---

## 2. Google Search Console

**Transport:** Python local dentro de un venv.

**Ubicación del server:** `integrations/mcp-gsc/` (vendoreado — copia upstream `AminForou/mcp-gsc@v0.2.1` sin parches locales).
- Ejecutable: `.venv/bin/python` (crear con `uv venv && uv pip install -r requirements.txt`)
- Script: `gsc_server.py`
- Credenciales OAuth: `client_secrets.json` + `token.json` (gitignored)

**Config:**

```json
"gscServer": {
  "command": "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/mcp-gsc/.venv/bin/python",
  "args": [
    "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/mcp-gsc/gsc_server.py"
  ],
  "env": {
    "GSC_OAUTH_CLIENT_SECRETS_FILE": "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/mcp-gsc/client_secrets.json",
    "GSC_DATA_STATE": "all"
  }
}
```

**Migración:** copiar `client_secrets.json` + `token.json` desde `~/.gemini/antigravity/scratch/mcp-gsc/` a `integrations/mcp-gsc/`. El venv se recrea local con `cd integrations/mcp-gsc && uv venv && uv pip install -r requirements.txt`.

**Futuro:** cuando upstream publique `[project.scripts]` en su `pyproject.toml`, podremos migrar a `uvx --from mcp-gsc <cmd>` y eliminar la copia vendoreada. Ver `integrations/mcp-gsc/README.md`.

**Regla crítica heredada:** usar `https://yatezzitos.com/` como property, NO `sc-domain:yatezzitos.com`.

---

## 3. WordPress (yatezzitos.com self-hosted)

**Cambio desde Anti Gravity (abril 2026):** la ruta activa ya **no** es el Node local `server-wp-mcp` de `integrations/wp-mcp/`. Ahora es el paquete npm `wordpress-mcp` invocado vía `npx`, que habla con el plugin **WordPress MCP v0.2.5** (by Automattic AI) instalado en el sitio WordPress.

**Transport:** `npx -y wordpress-mcp` (stdio).

**Ubicación del config:** `~/.claude/settings.json` → bloque `mcpServers.wordpress`. Claude Code lee este archivo automáticamente; **no es el mismo archivo** que `~/Library/Application Support/Claude/claude_desktop_config.json` (ese es solo Claude Desktop).

**Config:**

```json
"wordpress": {
  "command": "npx",
  "args": ["-y", "wordpress-mcp"],
  "env": {
    "WORDPRESS_HOST_URL": "https://yatezzitos.com",
    "WORDPRESS_API_USERNAME": "Yatezzitos",
    "WORDPRESS_API_PASSWORD": "${WORDPRESS_API_PASSWORD}",
    "WORDPRESS_POST_AUTHOR_ID": "1"
  }
}
```

> ⚠️ **Secreto:** `WORDPRESS_API_PASSWORD` es una **Application Password** de WordPress (formato `XXXX XXXX XXXX XXXX XXXX XXXX`) generada desde WP Admin → **Users → Profile → Application Passwords**. Referenciarla por variable de entorno, nunca inline. Rotable sin cerrar sesión del usuario.

**Plugin servidor en WP:** `WordPress MCP v0.2.5` — debe estar **instalado y activo** en el sitio. Verificar con `mcp__wordpress-mcp__get_site_info` que aparece en la lista de plugins activos.

**Tools expuestos (5):**

| Tool | Para qué |
|---|---|
| `get_site_info` | Info del sitio, plugins, temas, roles de usuario |
| `list_api_functions` | Lista todos los endpoints REST con soporte CRUD |
| `get_function_details` | Metadata de un endpoint (params, auth requerido) |
| `run_api_function` | Ejecuta GET/POST/PATCH/DELETE sobre cualquier ruta |
| `wp_get_media_file` | Descarga archivo de media por ID |

**Regla crítica heredada:** para Yoast siempre usar el endpoint propio `POST /yatezzitos/v1/update-yoast` vía `run_api_function`, nunca `meta: {_yoast_wpseo_*}` del endpoint estándar.

> Detalles del plugin Yoast REST API propio, los 4 endpoints que usamos (posts, properties, taxonomías, búsqueda por slug) y ejemplos de `run_api_function` en [`docs/memory/wordpress-yoast-guide.md`](wordpress-yoast-guide.md).

### Legacy: `integrations/wp-mcp/` + `server-wp-mcp` (inactivo)

El repo todavía contiene `integrations/wp-mcp/` (wrapper de `server-wp-mcp@^1.0.1` de npm + `wp-sites.json`) y el script `scripts/update-claude-mcp-config.py` sigue generando la entrada `"wordpress"` apuntando a ese Node local en `claude_desktop_config.json`. **Ya no es la ruta activa.** Queda como fallback potencial si algún día el paquete `wordpress-mcp` deja de mantenerse. Decidir si borrarlo o mantenerlo es trabajo abierto.

---

## 3.5 WordPress (Automattic, `wordpress-mcp`)

**Transport:** `npx @automattic/mcp-wordpress-remote` lanzado por un wrapper bash que carga credenciales desde `.env.wp-mcp-remote` gitignored. Así ninguna credencial aparece en el config JSON.

**Ubicación:** [`integrations/wp-mcp-remote/`](../../integrations/wp-mcp-remote/)
- `run.sh` — wrapper ejecutable (755)
- `.env.wp-mcp-remote.example` — plantilla (committed)
- `.env.wp-mcp-remote` — credenciales reales, en la raíz del repo (600, gitignored)

**Setup inicial:**
```bash
cp integrations/wp-mcp-remote/.env.wp-mcp-remote.example .env.wp-mcp-remote
# editar .env.wp-mcp-remote con WP_API_URL, WP_API_USERNAME, WP_API_PASSWORD (con comillas dobles)
```

**Gotcha conocido:** la Application Password de WordPress trae espacios. En `.env.wp-mcp-remote` el valor **debe ir entre comillas dobles**, si no `bash source` interpreta las palabras post-espacio como comandos y el wrapper aborta con `orden no encontrada`.

**Coexiste con `wordpress`** (server-wp-mcp local) — el local es multi-site y usa `wp-sites.json`; éste es el cliente oficial de Automattic con capabilities completas (tools/resources/prompts).

---

## 4. Figma

**Ya disponible nativamente en Claude.** No requiere configuración adicional — el MCP oficial de Figma ya aparece conectado (`mcp.figma.com/mcp`).

Si además se quiere mantener el MCP legacy `@jayarrowz/mcp-figma` (usado por Anti Gravity):

```json
"figma": {
  "command": "npx",
  "args": ["-y", "@jayarrowz/mcp-figma"],
  "env": { "FIGMA_API_KEY": "${FIGMA_API_KEY}" }
}
```

Pero **recomendación: usar el oficial** (más features, mejor soporte, ya está conectado).

---

## 5. NotebookLM

**Transport:** `uvx` (Python package runner de Astral).

**Config:**

```json
"notebooklm-mcp": {
  "command": "/Users/luisvelazquez/.local/bin/uvx",
  "args": ["--from", "notebooklm-mcp-cli", "notebooklm-mcp"]
}
```

**Prerequisito:** tener `uv` / `uvx` instalado:

```bash
curl -LsSf https://astral.sh/uv/install.sh | sh
```

Identico a Anti Gravity.

---

## 6. GitHub (opcional — Claude ya tiene `gh` CLI)

**Decisión:** Claude Desktop puede operar contra GitHub de dos formas:
1. **Sin MCP** — vía `gh` CLI disponible en Bash (recomendado para paridad con Claude Code)
2. **Con MCP** — réplica exacta de Anti Gravity, si se quiere uniformidad

Si se elige mantener el MCP:

```json
"github-mcp-server": {
  "command": "docker",
  "args": [
    "run", "-i", "--rm",
    "-e", "GITHUB_PERSONAL_ACCESS_TOKEN",
    "ghcr.io/github/github-mcp-server"
  ],
  "env": {
    "GITHUB_PERSONAL_ACCESS_TOKEN": "${GITHUB_PERSONAL_ACCESS_TOKEN}"
  }
}
```

La variable se resuelve desde el entorno del proceso de Claude, así que antes de abrir la app:

```bash
source ~/.yatezzitos-secrets.env
open -a Claude
```

---

## Fuente de verdad (referencia)

El contenido real de cada target se genera desde [`integrations/mcp-servers.json`](../../integrations/mcp-servers.json). **No editar los `claude_desktop_config.json`, `.mcp.json` ni `mcp_config.json` a mano** — el próximo login los sobrescribe vía LaunchAgent.

El script mantiene siempre backups timestamped (`*.bak.YYYYMMDD-HHMMSS`, permisos 600) del archivo anterior, así que un rollback es copiar el backup encima.

---

## Verificación post-setup

Después de correr `python3 scripts/sync-mcp.py` (o esperar al próximo login) y reiniciar los clientes:

1. **Claude Desktop → Settings → Conectores** — los 5 MCPs aparecen como "DESARROLLO LOCAL" ✅
2. **Probar GHL:** "lista los pipelines de Yatezzitos"
3. **Probar WordPress (local):** "qué sitios tengo en wordpress"
4. **Probar WordPress (Automattic):** "dame el site info de yatezzitos.com"
5. **Probar GSC:** "dame el top 10 de queries con más impresiones en yatezzitos.com últimos 28 días"
6. **Probar NotebookLM:** "lista mis libretas de NotebookLM"

Si alguno falla:
- `tail -30 /tmp/yzz-mcp-sync.log` — ver último sync
- `~/Library/Logs/Claude/mcp-server-{nombre}.log` — logs de Claude Desktop por MCP
- Correr el handshake manual: `echo '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"probe","version":"1.0"}}}' | <command del MCP>`

---

## Estructura vendoreada

Los MCPs custom viven dentro del repo — ya no dependen del directorio heredado de Anti Gravity:

- `integrations/ghl-mcp/` — fork de `mastanley13/GoHighLevel-MCP` + 4 parches Yatezzitos (email builder)
- `integrations/mcp-gsc/` — copia upstream `AminForou/mcp-gsc@v0.2.1` (sin parches locales)
- `integrations/wp-mcp/` — **legacy**: wrapper de `server-wp-mcp@^1.0.1` (multi-site). No activo; queda como fallback
- `integrations/wp-mcp-remote/` — wrapper de `@automattic/mcp-wordpress-remote` con `.env.wp-mcp-remote` (ruta activa)

Cada carpeta tiene su propio `README.md` y `.gitignore` específico.

El `.gitignore` raíz tiene excepciones para permitir commitear JSONs bajo `integrations/**/*.json` sin dejar pasar los secretos (`.env.*`, `wp-sites.json`, `client_secrets.json`, `token.json`, `.venv/`, `node_modules/`, `dist/` siguen bloqueados).

---

## Notas de seguridad

1. **Ningún secreto en el source of truth ni en los configs de los clientes.** Las credenciales viven exclusivamente en `.env.*` gitignored en la raíz del repo, cargados por wrappers:
   - `.env.ghl-mcp` — GHL (docker `--env-file`)
   - `.env.wp-mcp-remote` — Automattic WP (wrapper `run.sh` con `set -a; source; set +a`)
   - `wp-sites.json` — server-wp-mcp local (lo lee el MCP directamente)
   - `client_secrets.json` + `token.json` — GSC OAuth (los lee el MCP directamente)
2. **Rotación:** si una credencial entra al contexto por accidente (incluyendo al leer un config), revocarla inmediatamente y generar nueva. Coste de revocar: minutos. Coste de asumir: incalculable.
3. **Permisos:** `.env.*` y backups de configs quedan en `600` por `umask 077` en el sync script.
4. **Known leak (aceptado):** Antigravity preserva `figma.env.FIGMA_API_KEY` y `github-mcp-server.env.GITHUB_PERSONAL_ACCESS_TOKEN` en texto plano (no managed por el source of truth). Decisión: dejarlos así por ahora — migración futura al patrón wrapper + `.env` si se requiere.

---

**Última actualización:** 18 de abril 2026 — sistema de sync único + LaunchAgent + 5to MCP (Automattic)
**Relacionado:** `docs/memory/anti-gravity-migration.md`, `docs/memory/wordpress-yoast-guide.md`, [`integrations/mcp-servers.json`](../../integrations/mcp-servers.json), [`scripts/sync-mcp.py`](../../scripts/sync-mcp.py)
