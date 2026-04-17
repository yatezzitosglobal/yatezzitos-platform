#!/usr/bin/env bash
# Rollback: toma un snapshot JSON generado por snapshot-yoast-before.sh y
# reenvía los valores capturados al endpoint /yatezzitos/v1/update-yoast.
#
# Uso:
#   bash scripts/seo/rollback-yoast.sh scripts/seo/snapshots/2026-04-17_14-30-00.json

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_BASE_URL="${WP_BASE_URL:-https://yatezzitos.com}"
SNAPSHOT="${1:-}"

if [[ -z "${SNAPSHOT}" || ! -f "${SNAPSHOT}" ]]; then
    echo "Uso: $0 <ruta-al-snapshot.json>" >&2
    exit 1
fi

if [[ -z "${WP_USER:-}" || -z "${WP_APP_PASSWORD:-}" ]]; then
    echo "ERROR: exporta WP_USER y WP_APP_PASSWORD antes de ejecutar." >&2
    exit 1
fi

echo "⚠️  ROLLBACK desde: ${SNAPSHOT}"
echo "    Revertirá títulos, descripciones y focus keywords al estado capturado."
read -p "¿Continuar? (escribe 'SI' para confirmar): " confirm
if [[ "${confirm}" != "SI" ]]; then
    echo "Cancelado."
    exit 0
fi

jq -c '.[] | select(.skipped == null)' "${SNAPSHOT}" | while IFS= read -r row; do
    id=$(echo "${row}" | jq -r '.id')
    type=$(echo "${row}" | jq -r '.type')
    title=$(echo "${row}" | jq -r '.title // ""')
    desc=$(echo "${row}" | jq -r '.desc // ""')
    focuskw=$(echo "${row}" | jq -r '.focuskw // ""')

    echo "→ Restaurando ${type} #${id}"

    payload=$(jq -n \
        --arg id "${id}" --arg type "${type}" \
        --arg title "${title}" --arg desc "${desc}" --arg focuskw "${focuskw}" \
        '{id: ($id|tonumber), type: $type, title: $title, desc: $desc, focuskw: $focuskw}')

    curl -sS -u "${WP_USER}:${WP_APP_PASSWORD}" \
        -H "Content-Type: application/json" \
        -X POST -d "${payload}" \
        "${WP_BASE_URL}/wp-json/yatezzitos/v1/update-yoast" | jq -c .
done

echo "✓ Rollback completado"
