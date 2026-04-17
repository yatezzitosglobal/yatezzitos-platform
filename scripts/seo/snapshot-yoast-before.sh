#!/usr/bin/env bash
# Snapshot de seguridad: guarda el estado actual de Yoast (title/desc/focuskw) de
# todas las URLs que vamos a tocar con rewrite-yoast-priority-pages.sh.
#
# Requiere: WP_USER, WP_APP_PASSWORD (env vars); ids.env con los IDs reales.
#
# Salida: scripts/seo/snapshots/<timestamp>.json

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_BASE_URL="${WP_BASE_URL:-https://yatezzitos.com}"

if [[ -z "${WP_USER:-}" || -z "${WP_APP_PASSWORD:-}" ]]; then
    echo "ERROR: exporta WP_USER y WP_APP_PASSWORD antes de ejecutar." >&2
    exit 1
fi

if [[ ! -f "${SCRIPT_DIR}/ids.env" ]]; then
    echo "ERROR: falta ${SCRIPT_DIR}/ids.env — ejecuta primero fetch-wp-ids.sh." >&2
    exit 1
fi
# shellcheck disable=SC1091
source "${SCRIPT_DIR}/ids.env"

mkdir -p "${SCRIPT_DIR}/snapshots"
TS="$(date +%Y-%m-%d_%H-%M-%S)"
OUT="${SCRIPT_DIR}/snapshots/${TS}.json"

read_yoast() {
    local type="$1"
    local id="$2"
    local label="$3"
    if [[ -z "$id" || "$id" == "0" ]]; then
        printf '{"label":"%s","type":"%s","id":null,"skipped":true}' "$label" "$type"
        return
    fi
    local result
    result=$(curl -sS -u "${WP_USER}:${WP_APP_PASSWORD}" \
        "${WP_BASE_URL}/wp-json/yatezzitos/v1/read-yoast?type=${type}&id=${id}")
    # Añadir label y type al resultado para trazabilidad
    printf '%s' "$result" | jq --arg label "$label" --arg type "$type" --arg id "$id" \
        '. + {label: $label, type: $type, id: ($id | tonumber)}'
}

echo "→ Capturando snapshot de Yoast antes de cualquier cambio..."

ENTRIES=()
ENTRIES+=( "$(read_yoast post "${HOME_PAGE_ID:-0}" "homepage")" )
ENTRIES+=( "$(read_yoast term "${CITY_CANCUN_TERM_ID:-0}" "cancun-city")" )
ENTRIES+=( "$(read_yoast term "${CITY_PUERTO_VALLARTA_TERM_ID:-0}" "puerto-vallarta-city")" )
ENTRIES+=( "$(read_yoast term "${CITY_HUATULCO_TERM_ID:-0}" "huatulco-city")" )
ENTRIES+=( "$(read_yoast term "${CITY_MAZATLAN_TERM_ID:-0}" "mazatlan-city")" )
ENTRIES+=( "$(read_yoast term "${CITY_LA_PAZ_TERM_ID:-0}" "la-paz-city")" )
ENTRIES+=( "$(read_yoast term "${CITY_ACAPULCO_TERM_ID:-0}" "acapulco-city")" )
ENTRIES+=( "$(read_yoast post "${PAGE_PLAYAS_PV_ID:-0}" "playas-puerto-vallarta")" )
ENTRIES+=( "$(read_yoast post "${PAGE_YATES_EN_MEXICO_ID:-0}" "yates-en-mexico")" )

# Construir JSON array válido uniendo con comas
printf '[\n' > "${OUT}"
for i in "${!ENTRIES[@]}"; do
    if [[ $i -lt $(( ${#ENTRIES[@]} - 1 )) ]]; then
        printf '%s,\n' "${ENTRIES[$i]}" >> "${OUT}"
    else
        printf '%s\n' "${ENTRIES[$i]}" >> "${OUT}"
    fi
done
printf ']\n' >> "${OUT}"

echo "✅ Snapshot guardado en ${OUT}"
echo "   Úsalo para rollback: bash scripts/seo/rollback-yoast.sh ${OUT}"
