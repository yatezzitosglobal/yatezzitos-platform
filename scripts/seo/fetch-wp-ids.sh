#!/usr/bin/env bash
# Descubre los IDs de WordPress necesarios para ids.env y genera el archivo listo para usar.
#
# Uso:
#   WP_USER=... WP_APP_PASSWORD=... bash scripts/seo/fetch-wp-ids.sh
#
# Salida: scripts/seo/ids.env (listo para usar con rewrite-yoast-priority-pages.sh)
#
# Requiere: curl, jq

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_BASE_URL="${WP_BASE_URL:-https://yatezzitos.com}"

if [[ -z "${WP_USER:-}" || -z "${WP_APP_PASSWORD:-}" ]]; then
    echo "ERROR: exporta WP_USER y WP_APP_PASSWORD antes de ejecutar." >&2
    echo "  export WP_USER='Yatezzitos'" >&2
    echo "  export WP_APP_PASSWORD='XXXX XXXX XXXX XXXX XXXX XXXX'" >&2
    exit 1
fi

wp_get() {
    local endpoint="$1"
    curl -sS --fail \
        -u "${WP_USER}:${WP_APP_PASSWORD}" \
        -H "Accept: application/json" \
        "${WP_BASE_URL}/wp-json/${endpoint}"
}

echo "━━━ Descubriendo IDs de WordPress en ${WP_BASE_URL} ━━━"
echo

# ── Taxonomía property_city ─────────────────────────────────────────────────
declare -A CITY_SLUGS
CITY_SLUGS=(
    [CITY_CANCUN_TERM_ID]="renta-de-yates-cancun"
    [CITY_PUERTO_VALLARTA_TERM_ID]="renta-de-yates-en-puerto-vallarta"
    [CITY_HUATULCO_TERM_ID]="renta-de-yates-huatulco"
    [CITY_MAZATLAN_TERM_ID]="renta-de-yates-mazatlan"
    [CITY_LA_PAZ_TERM_ID]="renta-de-yates-en-la-paz"
    [CITY_ACAPULCO_TERM_ID]="renta-de-yates-en-acapulco"
    [CITY_LOS_CABOS_TERM_ID]="yates-cabos"
    [CITY_IXTAPA_TERM_ID]="yates-ixtapa"
    [CITY_NUEVO_VALLARTA_TERM_ID]="yates-en-nuevo-vallarta"
    [CITY_PLAYA_DEL_CARMEN_TERM_ID]="yates-playa-del-carmen"
)

declare -A RESOLVED_IDS

echo "→ Consultando términos de property_city..."
for VAR_NAME in "${!CITY_SLUGS[@]}"; do
    SLUG="${CITY_SLUGS[$VAR_NAME]}"
    TERM_ID=$(wp_get "wp/v2/property_city?slug=${SLUG}" | jq -r '.[0].id // empty' 2>/dev/null || true)
    if [[ -n "${TERM_ID}" ]]; then
        RESOLVED_IDS[$VAR_NAME]="${TERM_ID}"
        echo "   ✅ ${VAR_NAME}=${TERM_ID}  (slug: ${SLUG})"
    else
        RESOLVED_IDS[$VAR_NAME]="0"
        echo "   ⚠️  ${VAR_NAME}=0  (no encontrado — slug: ${SLUG})"
    fi
done

# ── Páginas / posts ──────────────────────────────────────────────────────────
echo
echo "→ Consultando páginas..."

declare -A PAGE_SLUGS
PAGE_SLUGS=(
    [HOME_PAGE_ID]="inicio"
    [PAGE_PLAYAS_PV_ID]="playas-en-puerto-vallarta"
    [PAGE_YATES_EN_MEXICO_ID]="yates-en-mexico"
)

for VAR_NAME in "${!PAGE_SLUGS[@]}"; do
    SLUG="${PAGE_SLUGS[$VAR_NAME]}"

    # Intentar como página
    POST_ID=$(wp_get "wp/v2/pages?slug=${SLUG}" | jq -r '.[0].id // empty' 2>/dev/null || true)

    # Si no hay resultado, intentar como post
    if [[ -z "${POST_ID}" ]]; then
        POST_ID=$(wp_get "wp/v2/posts?slug=${SLUG}" | jq -r '.[0].id // empty' 2>/dev/null || true)
    fi

    if [[ -n "${POST_ID}" ]]; then
        RESOLVED_IDS[$VAR_NAME]="${POST_ID}"
        echo "   ✅ ${VAR_NAME}=${POST_ID}  (slug: ${SLUG})"
    else
        RESOLVED_IDS[$VAR_NAME]="0"
        echo "   ⚠️  ${VAR_NAME}=0  (no encontrado — slug: ${SLUG})"
    fi
done

# ── Intentar homepage como front page estática ──────────────────────────────
if [[ "${RESOLVED_IDS[HOME_PAGE_ID]}" == "0" ]]; then
    FRONT_ID=$(wp_get "wp/v2/settings" | jq -r '.page_on_front // empty' 2>/dev/null || true)
    if [[ -n "${FRONT_ID}" && "${FRONT_ID}" != "0" ]]; then
        RESOLVED_IDS[HOME_PAGE_ID]="${FRONT_ID}"
        echo "   ✅ HOME_PAGE_ID=${FRONT_ID}  (via page_on_front setting)"
    fi
fi

# ── Generar ids.env ──────────────────────────────────────────────────────────
OUTPUT="${SCRIPT_DIR}/ids.env"

{
    echo "# Generado automáticamente por fetch-wp-ids.sh — $(date -u '+%Y-%m-%d %H:%M UTC')"
    echo "# NO commitear — contiene IDs reales de producción"
    echo
    echo "HOME_PAGE_ID=${RESOLVED_IDS[HOME_PAGE_ID]:-0}"
    echo "CITY_CANCUN_TERM_ID=${RESOLVED_IDS[CITY_CANCUN_TERM_ID]:-0}"
    echo "CITY_PUERTO_VALLARTA_TERM_ID=${RESOLVED_IDS[CITY_PUERTO_VALLARTA_TERM_ID]:-0}"
    echo "CITY_HUATULCO_TERM_ID=${RESOLVED_IDS[CITY_HUATULCO_TERM_ID]:-0}"
    echo "CITY_MAZATLAN_TERM_ID=${RESOLVED_IDS[CITY_MAZATLAN_TERM_ID]:-0}"
    echo "CITY_LA_PAZ_TERM_ID=${RESOLVED_IDS[CITY_LA_PAZ_TERM_ID]:-0}"
    echo "CITY_ACAPULCO_TERM_ID=${RESOLVED_IDS[CITY_ACAPULCO_TERM_ID]:-0}"
    echo "CITY_LOS_CABOS_TERM_ID=${RESOLVED_IDS[CITY_LOS_CABOS_TERM_ID]:-0}"
    echo "CITY_IXTAPA_TERM_ID=${RESOLVED_IDS[CITY_IXTAPA_TERM_ID]:-0}"
    echo "CITY_NUEVO_VALLARTA_TERM_ID=${RESOLVED_IDS[CITY_NUEVO_VALLARTA_TERM_ID]:-0}"
    echo "CITY_PLAYA_DEL_CARMEN_TERM_ID=${RESOLVED_IDS[CITY_PLAYA_DEL_CARMEN_TERM_ID]:-0}"
    echo "PAGE_PLAYAS_PV_ID=${RESOLVED_IDS[PAGE_PLAYAS_PV_ID]:-0}"
    echo "PAGE_YATES_EN_MEXICO_ID=${RESOLVED_IDS[PAGE_YATES_EN_MEXICO_ID]:-0}"
} > "${OUTPUT}"

echo
echo "━━━ ids.env generado en ${OUTPUT} ━━━"
echo
MISSING=$(grep "=0$" "${OUTPUT}" | wc -l)
if [[ "${MISSING}" -gt 0 ]]; then
    echo "⚠️  ${MISSING} IDs quedaron en 0 — revisa los slugs en WordPress admin."
else
    echo "✅ Todos los IDs resueltos. Listo para ejecutar rewrite-yoast-priority-pages.sh"
fi
echo
echo "Siguiente paso:"
echo "  DRY_RUN=1 bash scripts/seo/rewrite-yoast-priority-pages.sh   ← verificar sin cambios"
echo "  bash scripts/seo/rewrite-yoast-priority-pages.sh              ← aplicar cambios"
