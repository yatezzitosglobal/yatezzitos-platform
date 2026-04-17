#!/usr/bin/env python3
"""
Merge de MCPs en claude_desktop_config.json sin pisar preferences existentes.

Uso:
    python3 scripts/update-claude-mcp-config.py

Hace:
    1. Backup timestamped del claude_desktop_config.json actual.
    2. Lee el JSON actual (conservando bloque `preferences` y lo demas).
    3. Agrega/reemplaza la seccion `mcpServers` con los 3 MCPs del proyecto:
       GHL, GSC, NotebookLM.
    4. Escribe el resultado y muestra un resumen.

Idempotente: si ya corriste este script antes, solo reescribe mcpServers.

Todas las rutas a MCPs vendoreados se resuelven desde la raiz del repo
(`integrations/*`), no desde directorios externos.

NOTA sobre WordPress: el MCP de WordPress (paquete npm `wordpress-mcp` + plugin
`WordPress MCP` de Automattic en el sitio) NO se configura aqui. Vive en
`~/.claude/settings.json` porque corre bajo Claude Code, no Claude Desktop.
Ver `docs/memory/claude-mcp-setup.md` seccion 3.

Prerequisitos (correr una vez por MCP):
    integrations/ghl-mcp/      -> npm install && npm run build && docker build -t ghl-mcp-server .
    integrations/mcp-gsc/      -> uv venv && uv pip install -r requirements.txt
                                  + copiar client_secrets.json (no commitear)
    notebooklm-mcp             -> curl -LsSf https://astral.sh/uv/install.sh | sh  (si falta uvx)
"""

import json
import os
import shutil
import sys
from datetime import datetime
from pathlib import Path

CFG_PATH = os.path.expanduser(
    "~/Library/Application Support/Claude/claude_desktop_config.json"
)

# Raiz del repo (este script esta en scripts/ asi que subimos un nivel)
REPO_ROOT = Path(__file__).resolve().parent.parent

# Paths vendoreados dentro del repo
GHL_ENV_FILE = str(REPO_ROOT / ".env.ghl-mcp")
GSC_VENV_PYTHON = str(REPO_ROOT / "integrations/mcp-gsc/.venv/bin/python")
GSC_SERVER_SCRIPT = str(REPO_ROOT / "integrations/mcp-gsc/gsc_server.py")
GSC_CLIENT_SECRETS = str(REPO_ROOT / "integrations/mcp-gsc/client_secrets.json")

# uvx path (Astral). Cambia si lo instalaste en otro lugar
UVX_PATH = os.path.expanduser("~/.local/bin/uvx")

MCP_SERVERS = {
    "go-high-level": {
        "command": "docker",
        "args": [
            "run",
            "-i",
            "--rm",
            "--env-file",
            GHL_ENV_FILE,
            "ghl-mcp-server",
            "node",
            "dist/server.js",
        ],
    },
    "gscServer": {
        "command": GSC_VENV_PYTHON,
        "args": [GSC_SERVER_SCRIPT],
        "env": {
            "GSC_OAUTH_CLIENT_SECRETS_FILE": GSC_CLIENT_SECRETS,
            "GSC_DATA_STATE": "all",
        },
    },
    "notebooklm-mcp": {
        "command": UVX_PATH,
        "args": ["--from", "notebooklm-mcp-cli", "notebooklm-mcp"],
    },
}


def check_prerequisites():
    """Valida que los archivos/binarios referenciados existen. Avisa pero no aborta."""
    missing = []
    if not os.path.exists(GHL_ENV_FILE):
        missing.append(f"  - {GHL_ENV_FILE} (GHL env file)")
    if not os.path.exists(GSC_VENV_PYTHON):
        missing.append(f"  - {GSC_VENV_PYTHON} (correr: cd integrations/mcp-gsc && uv venv && uv pip install -r requirements.txt)")
    if not os.path.exists(GSC_CLIENT_SECRETS):
        missing.append(f"  - {GSC_CLIENT_SECRETS} (copiar desde ~/.gemini/antigravity/scratch/mcp-gsc/)")
    if not os.path.exists(UVX_PATH):
        missing.append(f"  - {UVX_PATH} (instalar uv: curl -LsSf https://astral.sh/uv/install.sh | sh)")

    if missing:
        print("AVISO: faltan archivos/binarios referenciados en la config:")
        for m in missing:
            print(m)
        print("La config se escribira igual, pero los MCPs faltantes no conectaran hasta que existan esos paths.\n")


def main():
    check_prerequisites()

    if not os.path.exists(CFG_PATH):
        print(f"ERROR: no existe {CFG_PATH}")
        print("Creando archivo vacio con solo mcpServers...")
        cfg = {}
    else:
        # Backup
        ts = datetime.now().strftime("%Y%m%d-%H%M%S")
        backup_path = f"{CFG_PATH}.bak.{ts}"
        shutil.copy2(CFG_PATH, backup_path)
        print(f"Backup creado: {backup_path}")

        with open(CFG_PATH, "r", encoding="utf-8") as f:
            try:
                cfg = json.load(f)
            except json.JSONDecodeError as e:
                print(f"ERROR parseando JSON: {e}")
                sys.exit(1)

    # Merge: reemplaza solo mcpServers, respeta el resto del JSON
    cfg["mcpServers"] = MCP_SERVERS

    with open(CFG_PATH, "w", encoding="utf-8") as f:
        json.dump(cfg, f, indent=2, ensure_ascii=False)

    print("OK: claude_desktop_config.json actualizado.")
    print(f"  Claves en root: {list(cfg.keys())}")
    print(f"  MCPs configurados: {list(cfg['mcpServers'].keys())}")
    print()
    print("Siguiente paso: reiniciar Claude Desktop (Cmd+Q completo, luego reabrir).")
    print("Cowork recarga la config en caliente - no necesita reinicio.")


if __name__ == "__main__":
    main()
