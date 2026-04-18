#!/usr/bin/env bash
# Wrapper para @automattic/mcp-wordpress-remote.
# Carga credenciales desde .env.wp-mcp-remote (en la raiz del repo) y lanza el MCP.
# Mantiene credenciales fuera del claude_desktop_config.json.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/../.." && pwd)"
ENV_FILE="$REPO_ROOT/.env.wp-mcp-remote"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "ERROR: falta $ENV_FILE" >&2
  echo "Copia $SCRIPT_DIR/.env.wp-mcp-remote.example al archivo anterior y pega las credenciales." >&2
  exit 1
fi

set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

exec npx -y @automattic/mcp-wordpress-remote@latest
