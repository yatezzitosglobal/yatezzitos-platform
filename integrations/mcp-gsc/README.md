# Google Search Console MCP — Vendorizado

Servidor MCP de Google Search Console usado por Claude Desktop + Cowork para consultar analítica, indexación y sitemaps de `yatezzitos.com`.

Copia vendorizada de [`AminForou/mcp-gsc`](https://github.com/AminForou/mcp-gsc) v0.2.1 — **sin parches Yatezzitos**. Vendorizamos una copia local en lugar de instalar desde PyPI porque el `pyproject.toml` upstream no declara `[project.scripts]`, así que `uvx mcp-gsc` no tiene un entry point resoluble. Cuando upstream publique uno, podemos migrar a `uvx --from mcp-gsc <cmd>` y eliminar esta carpeta.

---

## Setup (una sola vez)

Requiere **Python 3.11+** y [`uv`](https://docs.astral.sh/uv/).

```bash
cd integrations/mcp-gsc

# Crear venv + instalar deps
uv venv
uv pip install -r requirements.txt
```

Esto crea `.venv/` local (gitignored).

## Credenciales OAuth

El servidor usa OAuth de Google (cuenta del propietario de yatezzitos.com en GSC). Dos archivos deben existir localmente — **nunca committearlos**:

- `client_secrets.json` — OAuth Client ID descargado de Google Cloud Console (Credentials → Create OAuth client ID → Desktop app). Formato:
  ```json
  {
    "installed": {
      "client_id": "...",
      "client_secret": "...",
      "redirect_uris": ["http://localhost"]
    }
  }
  ```
- `token.json` — se genera automáticamente la primera vez que el servidor abre el flujo OAuth en el navegador.

Ambos archivos están gitignored.

**Migración desde Anti Gravity:** copiar los archivos existentes del viejo path:

```bash
cp ~/.gemini/antigravity/scratch/mcp-gsc/client_secrets.json integrations/mcp-gsc/
cp ~/.gemini/antigravity/scratch/mcp-gsc/token.json integrations/mcp-gsc/
```

## Claude Desktop config

El comando y argumentos viven en `scripts/update-claude-mcp-config.py` (sección `gscServer`). Apuntan a:

- Python: `integrations/mcp-gsc/.venv/bin/python`
- Script: `integrations/mcp-gsc/gsc_server.py`
- Env vars:
  - `GSC_OAUTH_CLIENT_SECRETS_FILE` → `integrations/mcp-gsc/client_secrets.json`
  - `GSC_DATA_STATE` → `all`

Después de crear el venv, correr:

```bash
python3 scripts/update-claude-mcp-config.py
```

Y reiniciar Claude Desktop (o Cowork recargará la config en caliente).

## Regla crítica heredada

Al usar este MCP con Claude: **siempre referirse al property como `https://yatezzitos.com/`** (con barra final, protocolo HTTPS). **NUNCA `sc-domain:yatezzitos.com`** — los dos existen en la cuenta y dan resultados distintos; la correcta para Yatezzitos es la `https://`.

## Verificación rápida

Una vez configurado:

```bash
# Test directo del script (fuera de Claude)
source .venv/bin/activate
python gsc_server.py
# Ctrl+C para cerrar
```

Debería arrancar sin errores y quedarse escuchando stdio.

## Traer cambios de upstream

Cuando `AminForou/mcp-gsc` publique v0.3+:

```bash
# Diff rápido
curl -s https://raw.githubusercontent.com/AminForou/mcp-gsc/main/gsc_server.py > /tmp/upstream-gsc.py
diff integrations/mcp-gsc/gsc_server.py /tmp/upstream-gsc.py

# Si hay entry point nuevo, migrar a uvx y borrar esta carpeta
```

---

**Upstream:** MIT licensed — `AminForou/mcp-gsc`
**Snapshot vendor:** 16 abr 2026 (commit upstream `ef516cd` — chore: bump to v0.2.1)
