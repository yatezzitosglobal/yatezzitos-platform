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
        "${WP_BASE_URL}/es/wp-json/${endpoint}"
}

resolve_term() {
    local var_name="$1" slug="$2"
    local id
    id=$(wp_get "wp/v2/property_city?slug=${slug}" | jq -r '.[0].id // empty' 2>/dev/null || true)
    if [[ -n "${id}" ]]; then
        echo "   ✅ ${var_name}=${id}  (slug: ${slug})"
    else
        id="0"
        echo "   ⚠️  ${var_name}=0  (no encontrado — slug: ${slug})"
    fi
    printf '%s' "${id}"
}

resolve_page() {
    local var_name="$1" slug="$2"
    local id
    id=$(wp_get "wp/v2/pages?slug=${slug}" | jq -r '.[0].id // empty' 2>/dev/null || true)
    if [[ -z "${id}" ]]; then
        id=$(wp_get "wp/v2/posts?slug=${slug}" | jq -r '.[0].id // empty' 2>/dev/null || true)
    fi
    if [[ -n "${id}" ]]; then
        echo "   ✅ ${var_name}=${id}  (slug: ${slug})"
    else
        id="0"
        echo "   ⚠️  ${var_name}=0  (no encontrado — slug: ${slug})"
    fi
    printf '%s' "${id}"
}

echo "━━━ Descubriendo IDs de WordPress en ${WP_BASE_URL} ━━━"
echo

echo "→ Consultando términos de property_city..."
CITY_CANCUN_TERM_ID=$(resolve_term          CITY_CANCUN_TERM_ID          "renta-de-yates-cancun")
CITY_PUERTO_VALLARTA_TERM_ID=$(resolve_term CITY_PUERTO_VALLARTA_TERM_ID  "renta-de-yates-en-puerto-vallarta")
CITY_HUATULCO_TERM_ID=$(resolve_term        CITY_HUATULCO_TERM_ID        "renta-de-yates-huatulco")
CITY_MAZATLAN_TERM_ID=$(resolve_term        CITY_MAZATLAN_TERM_ID        "renta-de-yates-mazatlan")
CITY_LA_PAZ_TERM_ID=$(resolve_term          CITY_LA_PAZ_TERM_ID          "renta-de-yates-en-la-paz")
CITY_ACAPULCO_TERM_ID=$(resolve_term        CITY_ACAPULCO_TERM_ID        "renta-de-yates-en-acapulco")
CITY_LOS_CABOS_TERM_ID=$(resolve_term       CITY_LOS_CABOS_TERM_ID       "yates-cabos")
CITY_IXTAPA_TERM_ID=$(resolve_term          CITY_IXTAPA_TERM_ID          "yates-ixtapa")
CITY_NUEVO_VALLARTA_TERM_ID=$(resolve_term  CITY_NUEVO_VALLARTA_TERM_ID  "yates-en-nuevo-vallarta")
CITY_PLAYA_DEL_CARMEN_TERM_ID=$(resolve_term CITY_PLAYA_DEL_CARMEN_TERM_ID "yates-playa-del-carmen")

echo
echo "→ Consultando páginas..."
PAGE_PLAYAS_PV_ID=$(resolve_page      PAGE_PLAYAS_PV_ID     "playas-en-puerto-vallarta")
PAGE_YATES_EN_MEXICO_ID=$(resolve_page PAGE_YATES_EN_MEXICO_ID "yates-en-mexico")

# Homepage: intentar slug "inicio", luego page_on_front
HOME_PAGE_ID=$(resolve_page HOME_PAGE_ID "inicio")
if [[ "${HOME_PAGE_ID}" == "0" ]]; then
    FRONT_ID=$(wp_get "wp/v2/settings" | jq -r '.page_on_front // empty' 2>/dev/null || true)
    if [[ -n "${FRONT_ID}" && "${FRONT_ID}" != "0" ]]; then
        HOME_PAGE_ID="${FRONT_ID}"
        echo "   ✅ HOME_PAGE_ID=${FRONT_ID}  (via page_on_front setting)"
    fi
fi

# ── Generar ids.env ──────────────────────────────────────────────────────────
OUTPUT="${SCRIPT_DIR}/ids.env"

cat > "${OUTPUT}" <<EOF
# Generado automáticamente por fetch-wp-ids.sh — $(date -u '+%Y-%m-%d %H:%M UTC')
# NO commitear — contiene IDs reales de producción

HOME_PAGE_ID=${HOME_PAGE_ID}
CITY_CANCUN_TERM_ID=${CITY_CANCUN_TERM_ID}
CITY_PUERTO_VALLARTA_TERM_ID=${CITY_PUERTO_VALLARTA_TERM_ID}
CITY_HUATULCO_TERM_ID=${CITY_HUATULCO_TERM_ID}
CITY_MAZATLAN_TERM_ID=${CITY_MAZATLAN_TERM_ID}
CITY_LA_PAZ_TERM_ID=${CITY_LA_PAZ_TERM_ID}
CITY_ACAPULCO_TERM_ID=${CITY_ACAPULCO_TERM_ID}
CITY_LOS_CABOS_TERM_ID=${CITY_LOS_CABOS_TERM_ID}
CITY_IXTAPA_TERM_ID=${CITY_IXTAPA_TERM_ID}
CITY_NUEVO_VALLARTA_TERM_ID=${CITY_NUEVO_VALLARTA_TERM_ID}
CITY_PLAYA_DEL_CARMEN_TERM_ID=${CITY_PLAYA_DEL_CARMEN_TERM_ID}
PAGE_PLAYAS_PV_ID=${PAGE_PLAYAS_PV_ID}
PAGE_YATES_EN_MEXICO_ID=${PAGE_YATES_EN_MEXICO_ID}
EOF

echo
echo "━━━ ids.env generado en ${OUTPUT} ━━━"
echo
MISSING=$(grep -c "=0$" "${OUTPUT}" || true)
if [[ "${MISSING}" -gt 0 ]]; then
    echo "⚠️  ${MISSING} IDs quedaron en 0 — revisa los slugs en WordPress admin."
else
    echo "✅ Todos los IDs resueltos. Listo para ejecutar rewrite-yoast-priority-pages.sh"
fi
echo
echo "Siguiente paso:"
echo "  bash scripts/seo/snapshot-yoast-before.sh"
echo "  DRY_RUN=1 bash scripts/seo/rewrite-yoast-priority-pages.sh   ← verificar sin cambios"
echo "  bash scripts/seo/rewrite-yoast-priority-pages.sh              ← aplicar cambios"
