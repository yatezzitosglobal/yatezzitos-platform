# wp-mcp-remote

Wrapper para [`@automattic/mcp-wordpress-remote`](https://www.npmjs.com/package/@automattic/mcp-wordpress-remote) (MCP oficial de Automattic para WordPress).

Coexiste con `integrations/wp-mcp/` (wrapper de `server-wp-mcp`, multi-site). Se usan en paralelo: `wordpress-mcp` (este) apunta al sitio principal con el cliente oficial; `wordpress` (el otro) mantiene la configuración multi-site vía `wp-sites.json`.

## Setup

1. Copiar la plantilla al archivo real (en la raíz del repo, gitignored):

   ```bash
   cp integrations/wp-mcp-remote/.env.wp-mcp-remote.example .env.wp-mcp-remote
   ```

2. Generar una Application Password nueva:
   - `yatezzitos.com/wp-admin` → Users → Profile → Application Passwords
   - Revocar la anterior si existía
   - Generar una nueva y pegarla en `.env.wp-mcp-remote` (`WP_API_PASSWORD`)

3. Ejecutar el script de configuración para Claude Desktop:

   ```bash
   python3 scripts/update-claude-mcp-config.py
   ```

4. Reiniciar Claude Desktop (Cmd+Q completo, luego reabrir).

## Seguridad

- **Nunca** pegar credenciales directamente en `~/Library/Application Support/Claude/claude_desktop_config.json`. El config JSON queda expuesto al contexto al inspeccionarlo.
- `.env.wp-mcp-remote` va en la raíz del repo y está en `.gitignore`.
- Si una credencial entra al contexto por accidente: revocarla inmediatamente en el admin y generar una nueva.

## Por qué wrapper y no `command: "npx"` directo

Si el config JSON de Claude Desktop contiene las credenciales en `env: {}`, quedan visibles cada vez que alguien (humano o agente) lee ese archivo. El wrapper `run.sh` carga `.env.wp-mcp-remote` en tiempo de arranque y pasa las variables sólo al proceso hijo — el config JSON no las menciona.
