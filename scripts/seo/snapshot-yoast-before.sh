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
    echo "ERROR: falta ${SCRIPT_DIR}/ids.env — copia desde ids.env.example y rellena." >&2
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
    if [[ -z "$id" || "$id" == "0" ]]; then
        echo "{\"type\":\"$type\",\"id\":null,\"skipped\":\"id no configurado\"}"
        return
    fi
    curl -sS -u "${WP_USER}:${WP_APP_PASSWORD}" \
        "${WP_BASE_URL}/wp-json/yatezzitos/v1/read-yoast?type=${type}&id=${id}"
}

echo "[" > "${OUT}"
{
    read_yoast post "${HOME_PAGE_ID:-0}"
    echo ","
    read_yoast term "${CITY_CANCUN_TERM_ID:-0}"
    echo ","
    read_yoast term "${CITY_PUERTO_VALLARTA_TERM_ID:-0}"
    echo ","
    read_yoast term "${CITY_HUATULCO_TERM_ID:-0}"
    echo ","
    read_yoast term "${CITY_MAZATLAN_TERM_ID:-0}"
    echo ","
    read_yoast term "${CITY_LA_PAZ_TERM_ID:-0}"
    echo ","
    read_yoast term "${CITY_ACAPULCO_TERM_ID:-0}"
    echo ","
    read_yoast post "${PAGE_PLAYAS_PV_ID:-0}"
    echo ","
    read_yoast post "${PAGE_YATES_EN_MEXICO_ID:-0}"
} >> "${OUT}"
echo "]" >> "${OUT}"

echo "✓ Snapshot guardado en ${OUT}"
