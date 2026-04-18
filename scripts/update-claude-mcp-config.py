#!/usr/bin/env python3
"""
[DEPRECADO] Redirige a scripts/sync-mcp.py.

Este script solo actualizaba el config de Claude Desktop. Ahora la fuente unica
de verdad vive en integrations/mcp-servers.json y el nuevo script sync-mcp.py
propaga a los 3 clientes (Claude Desktop, Claude Code, Antigravity) en un solo paso.
"""

import os
import sys
from pathlib import Path

HERE = Path(__file__).resolve().parent
NEW_SCRIPT = HERE / "sync-mcp.py"

print("=" * 70)
print("  AVISO: este script esta deprecado. Usando scripts/sync-mcp.py.")
print("  (Fuente de verdad: integrations/mcp-servers.json)")
print("=" * 70)
print()

os.execv(sys.executable, [sys.executable, str(NEW_SCRIPT)] + sys.argv[1:])
