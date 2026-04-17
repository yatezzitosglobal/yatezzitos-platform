# Configuración de MCPs en Claude — Portado desde Anti Gravity

> Guía operativa para replicar los 6 MCPs que usaba Anti Gravity (`~/.gemini/antigravity/mcp_config.json`) dentro del cliente desktop de Claude.
> **Este archivo no contiene secretos.** Todos los tokens deben vivir en variables de entorno o archivos `.env` locales fuera de Git.

---

## Dónde va la config en Claude

En macOS el archivo es:

```
~/Library/Application Support/Claude/claude_desktop_config.json
```

Estructura base:

```json
{
  "mcpServers": {
    "nombre-del-mcp": {
      "command": "...",
      "args": ["..."],
      "env": { "VAR": "${VAR}" }
    }
  }
}
```

Se cambia, se guarda, y se reinicia Claude Desktop (Cmd+Q completo, no solo cerrar ventana).

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

**Transport:** Node local (`server-wp-mcp` instalado en un `node_modules` dedicado).

**Ubicación:** `integrations/wp-mcp/` (vendoreado — sólo un `package.json` que referencia `server-wp-mcp@^1.0.1` de npm).
- Entry: `node_modules/server-wp-mcp/dist/index.js` (crear con `npm install`)
- Config de sitios: `wp-sites.json` (contiene Application Passwords — gitignored)

**Config:**

```json
"wordpress": {
  "command": "node",
  "args": [
    "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/wp-mcp/node_modules/server-wp-mcp/dist/index.js"
  ],
  "env": {
    "WP_SITES_PATH": "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/wp-mcp/wp-sites.json"
  }
}
```

**Setup inicial:**
```bash
cd integrations/wp-mcp
npm install
cp wp-sites.example.json wp-sites.json  # luego editar con credenciales reales
```

**Regla crítica heredada:** para Yoast siempre usar el endpoint propio `POST /yatezzitos/v1/update-yoast`, nunca `meta: {_yoast_wpseo_*}` del endpoint estándar.

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

## Archivo `claude_desktop_config.json` completo (plantilla)

> ⚡ En la práctica esta plantilla no se edita a mano. El script `scripts/update-claude-mcp-config.py` genera/actualiza la sección `mcpServers` sin tocar las `preferences` de Cowork. Corriendo `python3 scripts/update-claude-mcp-config.py` desde la raíz del repo alcanza.

```json
{
  "mcpServers": {
    "go-high-level": {
      "command": "docker",
      "args": [
        "run", "-i", "--rm",
        "--env-file", "/Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp",
        "ghl-mcp-server",
        "node", "dist/server.js"
      ]
    },
    "gscServer": {
      "command": "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/mcp-gsc/.venv/bin/python",
      "args": [
        "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/mcp-gsc/gsc_server.py"
      ],
      "env": {
        "GSC_OAUTH_CLIENT_SECRETS_FILE": "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/mcp-gsc/client_secrets.json",
        "GSC_DATA_STATE": "all"
      }
    },
    "wordpress": {
      "command": "node",
      "args": [
        "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/wp-mcp/node_modules/server-wp-mcp/dist/index.js"
      ],
      "env": {
        "WP_SITES_PATH": "/Users/luisvelazquez/Projects/yatezzitos-platform/integrations/wp-mcp/wp-sites.json"
      }
    },
    "notebooklm-mcp": {
      "command": "/Users/luisvelazquez/.local/bin/uvx",
      "args": ["--from", "notebooklm-mcp-cli", "notebooklm-mcp"]
    },
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
  }
}
```

(Figma queda fuera porque el MCP oficial ya está conectado en Claude.)

---

## Verificación post-setup

Después de pegar la config y reiniciar Claude Desktop:

1. **Abrir Claude → Settings → Developer → MCP Servers** — los 5 deben aparecer como `connected` ✅
2. **Probar GHL:** pedir "lista los pipelines de Yatezzitos"
3. **Probar WordPress:** pedir "lista los posts más recientes de yatezzitos.com"
4. **Probar GSC:** pedir "dame el top 10 de queries con más impresiones en yatezzitos.com últimos 28 días"
5. **Probar NotebookLM:** pedir "lista mis libretas de NotebookLM"
6. **Probar GitHub:** pedir "muéstrame los issues abiertos de YatezzitosMexico/yatezzitos-platform"

Si alguno falla:
- Verificar en Terminal que el comando corre a mano (`docker ps`, `.venv/bin/python --version`, etc.)
- Ver logs de Claude: `~/Library/Logs/Claude/mcp-server-{nombre}.log`
- Verificar que las variables de entorno se ven desde el proceso de Claude (`source` + `open -a Claude`)

---

## Estructura final (post-migración)

Los MCPs custom ahora viven dentro del repo — ya no dependen del directorio heredado de Anti Gravity:

- `integrations/ghl-mcp/` — fork vendoreado `mastanley13/GoHighLevel-MCP` + 4 parches Yatezzitos (email builder)
- `integrations/mcp-gsc/` — copia vendoreada upstream `AminForou/mcp-gsc@v0.2.1` (sin parches locales)
- `integrations/wp-mcp/` — wrapper de `server-wp-mcp@^1.0.1` (npm)

Cada carpeta tiene su propio `README.md` con setup y un `.gitignore` que bloquea sus secretos específicos (`.env`, `wp-sites.json`, `client_secrets.json`, `token.json`, `.venv/`, `node_modules/`, `dist/`).

El `.gitignore` raíz del repo tiene excepciones para permitir commitear los JSONs de config bajo `integrations/**/*.json` sin dejar pasar los secretos, que están explícitamente re-ignorados.

Cualquier colaborador puede clonar el repo y levantar los MCPs sin tener Anti Gravity instalado siguiendo el README de cada carpeta.

---

**Última actualización:** 16 de abril 2026 — MCPs migrados a `integrations/`
**Relacionado:** `docs/memory/anti-gravity-migration.md`
