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

**Transport:** Docker local (imagen propia `ghl-mcp-server`, forkeada de `mastanley13/GoHighLevel-MCP` + parches Yatezzitos).

**Prerequisitos:**
- Docker Desktop corriendo
- Imagen `ghl-mcp-server` construida (instrucciones en `docs/integraciones/mcp-notebooklm-knowledge.md`)
- `.env.ghl-mcp` con las credenciales en `/Users/luisvelazquez/Projects/yatezzitos-platform/.env.ghl-mcp`

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

**Idéntica a Anti Gravity — sin cambios.** Solo reconstruir la imagen si hay parches nuevos:

```bash
cd /Users/luisvelazquez/.gemini/antigravity/scratch/GoHighLevel-MCP
docker build -t ghl-mcp-server .
```

---

## 2. Google Search Console

**Transport:** Python local dentro de un venv.

**Ubicación del server:** `/Users/luisvelazquez/.gemini/antigravity/scratch/mcp-gsc/`
- Ejecutable: `.venv/bin/python`
- Script: `gsc_server.py`
- Credenciales OAuth: `client_secrets.json` (no commitear)

**Config:**

```json
"gscServer": {
  "command": "/Users/luisvelazquez/.gemini/antigravity/scratch/mcp-gsc/.venv/bin/python",
  "args": [
    "/Users/luisvelazquez/.gemini/antigravity/scratch/mcp-gsc/gsc_server.py"
  ],
  "env": {
    "GSC_OAUTH_CLIENT_SECRETS_FILE": "/Users/luisvelazquez/.gemini/antigravity/scratch/mcp-gsc/client_secrets.json",
    "GSC_DATA_STATE": "all"
  }
}
```

**Opcional (recomendado a mediano plazo):** mover el server de GSC dentro del repo en `integrations/mcp-gsc/` para que no dependa del directorio de Anti Gravity.

**Regla crítica heredada:** usar `https://yatezzitos.com/` como property, NO `sc-domain:yatezzitos.com`.

---

## 3. WordPress (yatezzitos.com self-hosted)

**Transport:** Node local (`server-wp-mcp` instalado en un `node_modules` dedicado).

**Ubicación:** `/Users/luisvelazquez/.gemini/antigravity/scratch/wp-mcp/`
- Entry: `node_modules/server-wp-mcp/dist/index.js`
- Config de sitios: `wp-sites.json` (contiene Application Passwords — no commitear)

**Config:**

```json
"wordpress": {
  "command": "node",
  "args": [
    "/Users/luisvelazquez/.gemini/antigravity/scratch/wp-mcp/node_modules/server-wp-mcp/dist/index.js"
  ],
  "env": {
    "WP_SITES_PATH": "/Users/luisvelazquez/.gemini/antigravity/scratch/wp-mcp/wp-sites.json"
  }
}
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
      "command": "/Users/luisvelazquez/.gemini/antigravity/scratch/mcp-gsc/.venv/bin/python",
      "args": [
        "/Users/luisvelazquez/.gemini/antigravity/scratch/mcp-gsc/gsc_server.py"
      ],
      "env": {
        "GSC_OAUTH_CLIENT_SECRETS_FILE": "/Users/luisvelazquez/.gemini/antigravity/scratch/mcp-gsc/client_secrets.json",
        "GSC_DATA_STATE": "all"
      }
    },
    "wordpress": {
      "command": "node",
      "args": [
        "/Users/luisvelazquez/.gemini/antigravity/scratch/wp-mcp/node_modules/server-wp-mcp/dist/index.js"
      ],
      "env": {
        "WP_SITES_PATH": "/Users/luisvelazquez/.gemini/antigravity/scratch/wp-mcp/wp-sites.json"
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

## Plan a mediano plazo: mover MCPs custom al repo

Hoy los MCPs custom viven en `~/.gemini/antigravity/scratch/`. Para no depender del directorio del agente anterior, conviene mover:
- `GoHighLevel-MCP/` → `integrations/ghl-mcp/` en este repo
- `mcp-gsc/` → `integrations/mcp-gsc/` en este repo
- `wp-mcp/wp-sites.json` → fuera del repo (archivo con secretos), el código queda en `integrations/wp-mcp/`

Así el proyecto es autocontenido y cualquier miembro del equipo puede clonarlo y levantar los MCPs sin tener Anti Gravity instalado.

---

**Última actualización:** 16 de abril 2026
**Relacionado:** `docs/memory/anti-gravity-migration.md`
