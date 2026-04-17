#!/usr/bin/env bash
# Reescritura batch de titles/metas/focus keywords sobre URLs prioritarias
# identificadas en la auditoría SEO integral (abril 2026).
#
# Cobertura:
#   #2 — Cancún city (CTR 2.45% → objetivo >5%)
#   #3 — /es/playas-en-puerto-vallarta/ (38k imp, CTR 0.09%)
#   #4 — Homepage (pos 9.5 → top 3)
#   #8 — Puerto Vallarta city (cluster pos 7-9)
#   #9 — Huatulco, Mazatlán, La Paz, Acapulco (micro-tuning)
#
# Uso:
#   WP_USER=... WP_APP_PASSWORD=... bash scripts/seo/rewrite-yoast-priority-pages.sh
#
# Modo dry-run:
#   DRY_RUN=1 bash scripts/seo/rewrite-yoast-priority-pages.sh

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_BASE_URL="${WP_BASE_URL:-https://yatezzitos.com}"
DRY_RUN="${DRY_RUN:-0}"
YZZ_TOKEN="${YZZ_TOKEN:-yzz_TempAccess_2026_AB7kp9xQ3mF5rL2vN8wT}"

if [[ ! -f "${SCRIPT_DIR}/ids.env" ]]; then
    echo "ERROR: falta ${SCRIPT_DIR}/ids.env — copia desde ids.env.example y rellena." >&2
    exit 1
fi
# shellcheck disable=SC1091
source "${SCRIPT_DIR}/ids.env"

update_yoast() {
    local type="$1"     # post | term
    local id="$2"
    local title="$3"
    local desc="$4"
    local focuskw="$5"
    local label="$6"

    if [[ -z "${id}" || "${id}" == "0" ]]; then
        echo "⚠️  SKIP [${label}] — id no configurado en ids.env"
        return
    fi

    echo "→ [${label}] (${type} #${id})"
    echo "    title   : ${title}"
    echo "    desc    : ${desc}"
    echo "    focuskw : ${focuskw}"

    if [[ "${DRY_RUN}" == "1" ]]; then
        echo "    [DRY RUN] no se envía"
        return
    fi

    local payload
    payload=$(jq -n \
        --arg id "${id}" \
        --arg type "${type}" \
        --arg title "${title}" \
        --arg desc "${desc}" \
        --arg focuskw "${focuskw}" \
        '{id: ($id|tonumber), type: $type, title: $title, desc: $desc, focuskw: $focuskw}')

    local response
    response=$(curl -sS \
        -H "X-YZZ-Token: ${YZZ_TOKEN}" \
        -H "Content-Type: application/json" \
        -X POST \
        -d "${payload}" \
        "${WP_BASE_URL}/es/wp-json/yatezzitos/v1/update-yoast")

    echo "    response: ${response}"
}

echo "─── Rewrite priority pages — auditoría abril 2026 ───"
echo

# ─────────────────────────────────────────────────────────
# #4 — HOMEPAGE — Pos 9.5 GSC, CTR 5.61% (razonable, posición débil)
# ─────────────────────────────────────────────────────────
update_yoast post "${HOME_PAGE_ID:-0}" \
    "Renta de yates en México %%currentyear%% · 10 destinos | Yatezzitos" \
    "Renta de yates, catamaranes y lanchas en México. Flota privada en Cancún, Puerto Vallarta, Los Cabos y 7 destinos más. Cotización en minutos." \
    "renta de yates" \
    "Homepage"

# ─────────────────────────────────────────────────────────
# #2 — CANCÚN — Mayor volumen del site (30,863 imp), CTR 2.45% a pos 4.1
# Target: duplicar CTR replicando patrón La Paz (8.66%)
# ─────────────────────────────────────────────────────────
update_yoast term "${CITY_CANCUN_TERM_ID:-0}" \
    "Renta de yates en Cancún %%currentyear%% · Todo incluido | Yatezzitos" \
    "Renta de yates en Cancún con tripulación, combustible y bebidas. Isla Mujeres, Playa Tortugas y el Caribe. Yates para 6-40 personas. Cotiza en 2 minutos." \
    "renta de yates en cancun" \
    "Cancún city"

# ─────────────────────────────────────────────────────────
# #8 — PUERTO VALLARTA — Página #1 del site pero cluster en pos 7-9
# ─────────────────────────────────────────────────────────
update_yoast term "${CITY_PUERTO_VALLARTA_TERM_ID:-0}" \
    "Renta de yates en Puerto Vallarta %%currentyear%% · Desde \$6,500/hr" \
    "Renta de yates en Puerto Vallarta desde Marina Vallarta. Recorre Playa Madagascar, Yelapa, Mismaloya y las Islas Marietas. Cotización 24/7." \
    "renta de yates en puerto vallarta" \
    "Puerto Vallarta city"

# ─────────────────────────────────────────────────────────
# #9 — HUATULCO — Ya rinde muy bien (CTR 8.52%), defender posición
# ─────────────────────────────────────────────────────────
update_yoast term "${CITY_HUATULCO_TERM_ID:-0}" \
    "Yates en Huatulco %%currentyear%% · Tour 36 bahías | Yatezzitos" \
    "Yates en Huatulco para recorrer las 36 bahías y Playa La India. Avistamiento de ballenas, snorkel y chef a bordo. Flota privada en Oaxaca." \
    "yates huatulco" \
    "Huatulco city"

# ─────────────────────────────────────────────────────────
# #9 — MAZATLÁN — Pos 4.2 / CTR 3.56%
# ─────────────────────────────────────────────────────────
update_yoast term "${CITY_MAZATLAN_TERM_ID:-0}" \
    "Yates en Mazatlán %%currentyear%% · Combate Naval y ballenas" \
    "Yates en Mazatlán con Combate Naval iluminado, avistamiento de ballenas jorobadas (nov-abr) y playas vírgenes de Sinaloa. Desde \$5,000/hr." \
    "yates mazatlan" \
    "Mazatlán city"

# ─────────────────────────────────────────────────────────
# #9 — LA PAZ — Template ganador (CTR 8.66% — el mejor del sitio)
# Mantener + microfine-tune
# ─────────────────────────────────────────────────────────
update_yoast term "${CITY_LA_PAZ_TERM_ID:-0}" \
    "Yates en La Paz BCS %%currentyear%% · Isla Espíritu Santo UNESCO" \
    "Yates en La Paz BCS para visitar Isla Espíritu Santo (UNESCO), nadar con lobos marinos y snorkel en arrecifes. Opciones para todo presupuesto." \
    "renta de yates en la paz" \
    "La Paz city"

# ─────────────────────────────────────────────────────────
# #9 — ACAPULCO — Ya rinde (CTR 7.90%), empujar a top 3
# ─────────────────────────────────────────────────────────
update_yoast term "${CITY_ACAPULCO_TERM_ID:-0}" \
    "Renta de yates en Acapulco %%currentyear%% · La Roqueta | Yatezzitos" \
    "Renta de yates en Acapulco para recorrer la Bahía, La Roqueta y ver a los Clavadistas de La Quebrada desde el mar. Tripulación certificada." \
    "renta de yates en acapulco" \
    "Acapulco city"

# ─────────────────────────────────────────────────────────
# #3 — /es/playas-en-puerto-vallarta/ — 38k imp, CTR 0.09%
# Mayor oportunidad "informacional → transaccional" del site
# ─────────────────────────────────────────────────────────
update_yoast post "${PAGE_PLAYAS_PV_ID:-0}" \
    "Las 12 mejores playas de Puerto Vallarta en yate %%currentyear%%" \
    "Guía completa de las mejores playas de Puerto Vallarta: Madagascar, Las Ánimas, Yelapa, Colomitos. Cómo llegar en yate privado con cotización inmediata." \
    "playas en puerto vallarta" \
    "Playas PV (blog)"

# ─────────────────────────────────────────────────────────
# #14 — /es/yates-en-mexico/ — huérfana a reconvertir en pilar país
# Solo reescribimos meta aquí; el contenido es del Grupo C
# ─────────────────────────────────────────────────────────
update_yoast post "${PAGE_YATES_EN_MEXICO_ID:-0}" \
    "Renta de yates en México %%currentyear%% · Guía por 10 destinos" \
    "Guía completa de renta de yates en México: precios, flota y playas en Cancún, Puerto Vallarta, Los Cabos, La Paz, Mazatlán, Acapulco, Huatulco y Ixtapa." \
    "renta de yates en mexico" \
    "Yates en México (hub país)"

echo
echo "─── Rewrite completado ───"
echo "Tip: validar resultado con 'Inspeccionar URL' en Google Search Console"
echo "     y 'Force Refresh' del caché de Yoast si los cambios tardan en verse."
