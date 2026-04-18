#!/usr/bin/env python3
"""
sync-mcp.py - Sync de MCPs desde integrations/mcp-servers.json a todos los clientes.

Lee la fuente unica de verdad y propaga el bloque `mcpServers` a los configs nativos
de Claude Desktop, Claude Code (.mcp.json) y Antigravity, siguiendo la estrategia
declarada por cada target (merge por defecto).

Uso manual:
    python3 scripts/sync-mcp.py

Invocado automaticamente por el LaunchAgent com.yatezzitos.mcp-sync al login.

Seguridad:
- No toca credenciales (viven en .env.* gitignored + wrappers).
- Placeholders {{REPO_ROOT}} y {{HOME}} se resuelven en runtime.
- Backup timestamped con permisos 600 antes de cada escritura.
- Archivos escritos con umask 077.
- Preserva keys no-mcpServers (ej. `preferences` en Claude Desktop).
- Preserva MCPs extra ya presentes en cada target que no esten declarados aqui.
"""

import json
import os
import shutil
import stat
import sys
from datetime import datetime
from pathlib import Path

# --- Constantes ------------------------------------------------------------

REPO_ROOT = Path(__file__).resolve().parent.parent
SOURCE = REPO_ROOT / "integrations/mcp-servers.json"
LOG_PREFIX = "[sync-mcp]"

PLACEHOLDERS = {
    "REPO_ROOT": str(REPO_ROOT),
    "HOME": os.path.expanduser("~"),
}

# Campos internos del source of truth que no deben propagarse al config de clientes
MANAGEMENT_FIELDS = {"description", "requires"}


# --- Helpers ---------------------------------------------------------------

def log(msg):
    print(f"{LOG_PREFIX} {msg}")


def resolve_placeholders(value):
    """Reemplaza {{REPO_ROOT}} y {{HOME}} recursivamente."""
    if isinstance(value, str):
        for k, v in PLACEHOLDERS.items():
            value = value.replace("{{" + k + "}}", v)
        return value
    if isinstance(value, list):
        return [resolve_placeholders(v) for v in value]
    if isinstance(value, dict):
        return {k: resolve_placeholders(v) for k, v in value.items()}
    return value


def strip_management(entry):
    """Quita description/requires antes de escribir al config del cliente."""
    return {k: v for k, v in entry.items() if k not in MANAGEMENT_FIELDS}


def check_requires(servers_raw):
    """Verifica requires y avisa si algo falta. No aborta."""
    missing = []
    for name, entry in servers_raw.items():
        for req in entry.get("requires", []):
            req_resolved = resolve_placeholders(req)
            # Si parece path (absoluto), chequeamos existencia
            if req_resolved.startswith("/"):
                if not os.path.exists(req_resolved):
                    missing.append(f"[{name}] falta archivo: {req_resolved}")
            else:
                # Asumimos binario en PATH
                if not shutil.which(req_resolved):
                    missing.append(f"[{name}] falta binario en PATH: {req_resolved}")
    if missing:
        log("AVISO: prerequisites incompletos, los MCPs afectados no conectaran:")
        for m in missing:
            log(f"  - {m}")
        log("(sync continua de todas formas)")
    return len(missing) == 0


def backup(path):
    """Copia path a path.bak.YYYYMMDD-HHMMSS con permisos 600."""
    ts = datetime.now().strftime("%Y%m%d-%H%M%S")
    b = path.with_name(path.name + f".bak.{ts}")
    shutil.copy2(path, b)
    os.chmod(b, 0o600)
    return b


def read_existing(path):
    """Lee JSON existente o devuelve dict vacio."""
    if not path.exists():
        return {}
    try:
        with open(path, encoding="utf-8") as f:
            return json.load(f)
    except json.JSONDecodeError as e:
        log(f"WARN: JSON invalido en {path} ({e}); se reemplaza")
        return {}
    except Exception as e:
        log(f"WARN: no se pudo leer {path} ({e}); se reemplaza")
        return {}


def sync_target(target, managed_servers):
    """Aplica managed_servers al config de un target. Devuelve tupla (cambio, resumen)."""
    name = target["name"]
    path = Path(resolve_placeholders(target["path"]))
    strategy = target.get("strategy", "merge")

    path.parent.mkdir(parents=True, exist_ok=True)

    existing = read_existing(path) if path.exists() else {}
    existing_servers = existing.get("mcpServers", {}) if isinstance(existing, dict) else {}

    if strategy == "replace":
        new_servers = dict(managed_servers)
        preserved = []
    else:  # merge
        new_servers = dict(existing_servers)
        preserved = [k for k in existing_servers.keys() if k not in managed_servers]
        new_servers.update(managed_servers)

    # Snapshot para decidir si hay cambios
    if existing_servers == new_servers:
        changed = False
    else:
        changed = True

    if not changed and path.exists():
        log(f"[{name}] sin cambios ({len(new_servers)} MCPs totales)")
        return False, {"name": name, "total": len(new_servers), "preserved": preserved}

    # Escribir (con backup si existia)
    if path.exists():
        b = backup(path)
        log(f"[{name}] backup: {b.name}")

    if not isinstance(existing, dict):
        existing = {}
    existing["mcpServers"] = new_servers

    # Escritura atomica: tmp file + rename
    tmp = path.with_name(path.name + ".tmp")
    with open(tmp, "w", encoding="utf-8") as f:
        json.dump(existing, f, indent=2, ensure_ascii=False)
        f.write("\n")
    os.chmod(tmp, 0o600)
    os.replace(tmp, path)

    log(f"[{name}] escrito -> {path}")
    log(f"  MCPs managed: {list(managed_servers.keys())}")
    if preserved:
        log(f"  MCPs preservados (no-managed): {preserved}")
    return True, {"name": name, "total": len(new_servers), "preserved": preserved}


# --- Main ------------------------------------------------------------------

def main():
    os.umask(0o077)

    if not SOURCE.exists():
        log(f"ERROR: no existe source of truth en {SOURCE}")
        sys.exit(1)

    try:
        with open(SOURCE, encoding="utf-8") as f:
            data = json.load(f)
    except json.JSONDecodeError as e:
        log(f"ERROR: {SOURCE} no es JSON valido: {e}")
        sys.exit(1)

    servers_raw = data.get("mcpServers", {})
    if not servers_raw:
        log(f"ERROR: {SOURCE} no declara mcpServers")
        sys.exit(1)

    targets = data.get("targets", [])
    if not targets:
        log(f"ERROR: {SOURCE} no declara targets")
        sys.exit(1)

    log(f"Source: {SOURCE}")
    log(f"MCPs declarados: {list(servers_raw.keys())}")
    log(f"Targets: {[t.get('name', '?') for t in targets]}")
    log("")

    # Resolver placeholders y quitar campos de gestion
    managed_servers = {
        name: strip_management(resolve_placeholders(entry))
        for name, entry in servers_raw.items()
    }

    # Prerequisites (aviso, no bloqueante)
    check_requires(servers_raw)
    log("")

    # Sync por target
    any_changed = False
    for target in targets:
        try:
            changed, _ = sync_target(target, managed_servers)
            any_changed = any_changed or changed
        except Exception as e:
            log(f"[{target.get('name','?')}] ERROR: {e}")
        log("")

    if any_changed:
        log("Sync completo. Reinicia los clientes para tomar los cambios:")
        log("  - Claude Desktop: Cmd+Q y reabrir")
        log("  - Antigravity: cerrar ventana y reabrir")
        log("  - Claude Code: los proyectos nuevos toman .mcp.json al abrir")
    else:
        log("Sin cambios. Todos los targets ya estaban sincronizados.")


if __name__ == "__main__":
    main()
