<?php
/**
 * Plugin Name: YZZ Quotes API + Quote Page
 * Description: API de cotizaciones + shortcode robusto para mostrar mi-cotizacion con token qt.
 * Version: 2.3.8
 */

if (!defined('ABSPATH')) {
  exit;
}

function yzzq_table()
{
  global $wpdb;
  return $wpdb->prefix . 'yzz_quotes';
}

register_activation_hook(__FILE__, 'yzzq_install');
function yzzq_install()
{
  global $wpdb;
  $table = yzzq_table();
  $sql = "CREATE TABLE {$table} (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      token VARCHAR(64) NOT NULL,
      contact_id VARCHAR(64) NULL,
      payload LONGTEXT NOT NULL,
      created_at DATETIME NOT NULL,
      PRIMARY KEY (id),
      UNIQUE KEY token (token),
      KEY created_at (created_at),
      KEY contact_id (contact_id)
    ) {$wpdb->get_charset_collate()};";
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  dbDelta($sql);
}

function yzzq_public_url($token)
{
  return add_query_arg('qt', rawurlencode($token), home_url('/mi-cotizacion/'));
}

function yzzq_nocache()
{
  nocache_headers();
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');
}

function yzzq_clean_payload($params)
{
  if (!is_array($params)) {
    return array();
  }
  $out = array();
  foreach ($params as $k => $v) {
    if (is_scalar($v) || is_null($v)) {
      $out[sanitize_key($k)] = sanitize_text_field((string)$v);
    }
    elseif (is_array($v)) {
      // Preserve multi-select/list fields coming from GHL.
      $out[sanitize_key($k)] = wp_json_encode($v, JSON_UNESCAPED_UNICODE);
    }
    elseif (is_object($v)) {
      $out[sanitize_key($k)] = wp_json_encode($v, JSON_UNESCAPED_UNICODE);
    }
  }
  return $out;
}

function yzzq_is_nonempty_value($value)
{
  if (is_array($value) || is_object($value)) {
    return !empty((array)$value);
  }
  return trim((string)$value) !== '';
}

function yzzq_find_first_value(array $data, array $keys)
{
  foreach ($keys as $key) {
    if (!array_key_exists($key, $data)) {
      continue;
    }
    if (yzzq_is_nonempty_value($data[$key])) {
      return $data[$key];
    }
  }
  return null;
}

function yzzq_to_storage_text($value)
{
  if (is_array($value) || is_object($value)) {
    return wp_json_encode($value, JSON_UNESCAPED_UNICODE);
  }
  return sanitize_text_field((string)$value);
}

function yzzq_enrich_payload(array $payload)
{
  // Canonical URL key for yacht detail page.
  $url = yzzq_find_first_value($payload, array('url_del_yate', 'yacht_url', 'url_yate'));
  if ($url !== null) {
    $payload['url_del_yate'] = yzzq_to_storage_text($url);
  }

  // Canonical amenities key with exact candidates first.
  $amenities = yzzq_find_first_value($payload, array(
    'amenities_raw',
    'amenities_raw_text',
    'caractersticas_y_amenidades_del_yate',
    'caracteristicas_y_amenidades_del_yate',
    'caracteristicas_amenidades_del_yate',
    'amenidades',
    'amenities',
    'amenity_list',
  ));

  // Fuzzy key fallback for naming drifts from CRM/GHL.
  if ($amenities === null) {
    foreach ($payload as $key => $value) {
      if (!yzzq_is_nonempty_value($value)) {
        continue;
      }
      if (preg_match('/(amenid|amenit|caracteri|caracters|feature|inclu)/i', (string)$key)) {
        $amenities = $value;
        break;
      }
    }
  }

  if ($amenities !== null) {
    $payload['amenities_raw'] = yzzq_to_storage_text($amenities);
  }

  return $payload;
}

add_action('rest_api_init', function () {
  // ── /quote  (cotizaciones) ───────────────────────────────────────────────
  register_rest_route('yzz/v1', '/quote', array(
      array(
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'yzzq_create_quote',
      'permission_callback' => '__return_true',
    ),
      array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'yzzq_get_quote',
      'permission_callback' => '__return_true',
    ),
  ));

  // ── /received  (recibo de depósito / página Mi Reserva) ─────────────────
  register_rest_route('yzz/v1', '/received', array(
      array(
      'methods' => WP_REST_Server::CREATABLE,
      'callback' => 'yzzq_create_received',
      'permission_callback' => '__return_true',
    ),
      array(
      'methods' => WP_REST_Server::READABLE,
      'callback' => 'yzzq_get_received',
      'permission_callback' => '__return_true',
    ),
  ));
});


function yzzq_create_quote(WP_REST_Request $request)
{
  global $wpdb;
  yzzq_nocache();

  $payload_arr = yzzq_clean_payload($request->get_json_params());
  $payload_arr = yzzq_enrich_payload($payload_arr);
  $payload = wp_json_encode($payload_arr, JSON_UNESCAPED_UNICODE);
  $contact_id = isset($payload_arr['contact_id']) ? $payload_arr['contact_id'] : '';

  try {
    $token = bin2hex(random_bytes(8));
  }
  catch (Exception $e) {
    $token = wp_generate_password(16, false, false);
  }

  $ok = $wpdb->insert(
    yzzq_table(),
    array(
    'token' => $token,
    'contact_id' => $contact_id,
    'payload' => $payload,
    'created_at' => current_time('mysql'),
  ),
    array('%s', '%s', '%s', '%s')
  );

  if ($ok === false) {
    return new WP_REST_Response(array('error' => 'db_insert_failed'), 500);
  }

  return new WP_REST_Response(array(
    'quote_url' => yzzq_public_url($token),
    'quote_token' => $token,
  ), 200);
}

function yzzq_get_quote(WP_REST_Request $request)
{
  global $wpdb;
  yzzq_nocache();

  $qt = sanitize_text_field((string)($request->get_param('qt') ?: $request->get_param('token')));
  if ($qt === '') {
    return new WP_REST_Response(array('error' => 'missing_qt'), 400);
  }

  $row = $wpdb->get_row($wpdb->prepare(
    "SELECT payload, created_at FROM " . yzzq_table() . " WHERE token=%s LIMIT 1",
    $qt
  ));

  if (!$row) {
    return new WP_REST_Response(array('error' => 'not_found'), 404);
  }

  $data = json_decode($row->payload, true);
  if (!is_array($data)) {
    $data = array();
  }
  $data = yzzq_enrich_payload($data);
  $data['_meta'] = array('quote_token' => $qt, 'created_at' => $row->created_at);
  return new WP_REST_Response($data, 200);
}

// ── /received handlers ──────────────────────────────────────────────────────

/**
 * POST /wp-json/yzz/v1/received
 * Recibe el webhook de GoHighLevel cuando se envía el recibo de depósito.
 * Acepta el header X-YZZ-KEY como capa extra de seguridad (opcional).
 * Devuelve el token y la URL pública de «Mi Reserva».
 */
function yzzq_create_received(WP_REST_Request $request)
{
  global $wpdb;
  yzzq_nocache();

  $payload_arr = yzzq_clean_payload($request->get_json_params());
  $payload_arr = yzzq_enrich_payload($payload_arr);
  // Marca interna: este registro proviene del flujo de recibo de depósito.
  $payload_arr['_yzz_type'] = 'received';
  $payload    = wp_json_encode($payload_arr, JSON_UNESCAPED_UNICODE);
  $contact_id = isset($payload_arr['contact_id']) ? trim((string) $payload_arr['contact_id']) : '';

  // ── UPSERT: si ya existe un registro con ese contact_id, reutilizamos
  //    el token original (quote / reservation / thank-you comparten el mismo token).
  $existing_token = '';
  if ($contact_id !== '') {
    $existing_token = $wpdb->get_var($wpdb->prepare(
      "SELECT token FROM " . yzzq_table() . " WHERE contact_id=%s ORDER BY created_at ASC LIMIT 1",
      $contact_id
    ));
  }

  if ($existing_token) {
    // Actualizar el payload del registro existente
    $wpdb->update(
      yzzq_table(),
      array('payload' => $payload, 'created_at' => current_time('mysql')),
      array('token'   => $existing_token),
      array('%s', '%s'),
      array('%s')
    );
    $token = $existing_token;
  } else {
    // Nuevo contacto → crear registro con token nuevo
    try {
      $token = bin2hex(random_bytes(8));
    } catch (Exception $e) {
      $token = wp_generate_password(16, false, false);
    }

    $ok = $wpdb->insert(
      yzzq_table(),
      array(
        'token'      => $token,
        'contact_id' => $contact_id,
        'payload'    => $payload,
        'created_at' => current_time('mysql'),
      ),
      array('%s', '%s', '%s', '%s')
    );

    if ($ok === false) {
      return new WP_REST_Response(array('error' => 'db_insert_failed'), 500);
    }
  }

  $reservation_url = add_query_arg('qt', rawurlencode($token), home_url('/mi-reserva/'));
  $thankyou_url    = add_query_arg('qt', rawurlencode($token), home_url('/gracias/'));

  return new WP_REST_Response(array(
    'reservation_url' => $reservation_url,
    'thankyou_url'    => $thankyou_url,
    'quote_url'       => $reservation_url, // alias para compatibilidad con GHL
    'quote_token'     => $token,
    'token'           => $token,
  ), 200);
}

/**
 * GET /wp-json/yzz/v1/received?qt=TOKEN
 * Permite que la página «Mi Reserva» consulte los datos del recibo de depósito.
 */
function yzzq_get_received(WP_REST_Request $request)
{
  global $wpdb;
  yzzq_nocache();

  $qt = sanitize_text_field((string)($request->get_param('qt') ?: $request->get_param('token')));
  if ($qt === '') {
    return new WP_REST_Response(array('error' => 'missing_qt'), 400);
  }

  $row = $wpdb->get_row($wpdb->prepare(
    "SELECT payload, created_at FROM " . yzzq_table() . " WHERE token=%s LIMIT 1",
    $qt
  ));

  if (!$row) {
    return new WP_REST_Response(array('error' => 'not_found'), 404);
  }

  $data = json_decode($row->payload, true);
  if (!is_array($data)) {
    $data = array();
  }
  $data = yzzq_enrich_payload($data);
  $data['_meta'] = array('quote_token' => $qt, 'created_at' => $row->created_at);
  return new WP_REST_Response($data, 200);
}

add_shortcode('yzz_quote_page', 'yzzq_shortcode');
add_shortcode('YZZ_QUOTE_PAGE', 'yzzq_shortcode');
add_shortcode('yzz_reservation_page', 'yzzq_reservation_shortcode');
add_shortcode('YZZ_RESERVATION_PAGE', 'yzzq_reservation_shortcode');
add_shortcode('yzz_thankyou_page', 'yzzq_thankyou_shortcode');
add_shortcode('YZZ_THANKYOU_PAGE', 'yzzq_thankyou_shortcode');
function yzzq_shortcode()
{
  ob_start();
?>
<div id="yzzq-root"></div>
<style>
  #yzzq-root {
    max-width: 980px;
    margin: 0 auto;
    padding: 16px;
    font-family: Arial, sans-serif;
    color: #111827
  }

  .yzzq-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 12px
  }

  .yzzq-grid {
    display: grid;
    gap: 12px;
    grid-template-columns: 1fr
  }

  @media(min-width:760px) {
    .yzzq-grid {
      grid-template-columns: 1fr 1fr
    }
  }

  .yzzq-row {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    padding: 10px 0;
    border-bottom: 1px dashed #e5e7eb
  }

  .yzzq-row:last-child {
    border-bottom: 0
  }

  .yzzq-k {
    color: #6b7280
  }

  .yzzq-v {
    font-weight: 700;
    text-align: right;
    word-break: break-word
  }

  .yzzq-btn {
    display: block;
    width: 100%;
    padding: 12px 14px;
    border-radius: 10px;
    border: 0;
    cursor: pointer;
    font-weight: 700;
    text-align: center;
    text-decoration: none;
    box-sizing: border-box;
    color: #fff;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent
  }

  .yzzq-btn-primary {
    background: #194395
  }

  .yzzq-btn-success {
    background: #00945e
  }

  .yzzq-btn-dark {
    background: #111827
  }

  .yzzq-btn-light {
    background: #4b5563;
    color: #fff;
    border: 1px solid #4b5563
  }

  .yzzq-btn:hover,
  .yzzq-btn:focus,
  .yzzq-btn:active {
    color: #fff !important;
    text-decoration: none !important;
    opacity: 1 !important;
    filter: none !important
  }

  .yzzq-status {
    display: inline-block;
    background: #e5e7eb;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 13px;
    font-weight: 700
  }

  .yzzq-status.ok {
    background: #d1fae5;
    color: #065f46
  }

  .yzzq-status.err {
    background: #fee2e2;
    color: #991b1b
  }

  .yzzq-img-wrap {
    display: none;
    background: #0b1939;
    border-radius: 14px;
    overflow: hidden;
    min-height: 220px;
    align-items: center;
    justify-content: center
  }

  .yzzq-img {
    width: 100%;
    max-height: 360px;
    object-fit: contain;
    display: block
  }

  .yzzq-timer {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-top: 12px
  }

  .yzzq-box {
    background: #0b1939;
    color: #fff;
    border-radius: 10px;
    padding: 10px;
    text-align: center
  }

  .yzzq-num {
    font-size: 24px;
    font-weight: 700
  }

  .yzzq-lbl {
    font-size: 11px;
    color: #cbd5e1;
    text-transform: uppercase
  }

  .yzzq-trip-wrap {
    opacity: .72
  }

  .yzzq-trip-wrap .yzzq-box {
    background: #334155
  }

  .yzzq-offer {
    margin-top: 12px;
    border: 2px solid #f59e0b;
    border-radius: 12px;
    background: linear-gradient(135deg, #7c2d12, #b45309);
    padding: 12px
  }

  .yzzq-offer-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px
  }

  .yzzq-offer-title {
    color: #fff;
    font-size: 16px;
    font-weight: 800
  }

  .yzzq-offer-btn {
    width: auto !important;
    display: inline-block !important;
    padding: 10px 14px !important;
    background: #16a34a !important;
    color: #fff !important;
    border-radius: 10px;
    font-weight: 800;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent
  }

  .yzzq-offer-timer {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px
  }

  .yzzq-offer-box {
    background: #111827;
    color: #fff;
    border-radius: 10px;
    padding: 10px;
    text-align: center
  }

  .yzzq-offer-num {
    font-size: 30px;
    line-height: 1;
    font-weight: 900
  }

  .yzzq-offer-lbl {
    font-size: 11px;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: #d1d5db
  }

  .yzzq-note {
    font-size: 12px;
    color: #6b7280
  }

  .yzzq-actions {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px
  }

  .yzzq-acc summary {
    cursor: pointer;
    font-weight: 700
  }

  .yzzq-map-btn {
    display: inline-block;
    padding: 8px 10px;
    border-radius: 8px;
    background: #194395;
    color: #fff !important;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent
  }

  .yzzq-map-btn:hover,
  .yzzq-map-btn:focus,
  .yzzq-map-btn:active {
    color: #fff !important;
    text-decoration: none !important;
    opacity: 1 !important;
    filter: none !important
  }

  .yzzq-toggle summary {
    cursor: pointer;
    font-size: 20px;
    font-weight: 800;
    line-height: 1.2
  }

  .yzzq-toggle-body {
    padding-top: 10px
  }

  .yzzq-toggle-body p {
    font-size: 15px;
    line-height: 1.5;
    margin: 0 0 10px
  }

  .yzzq-amen-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px
  }

  .yzzq-amen-item {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fafafa
  }

  .yzzq-amen-emo {
    font-size: 18px;
    line-height: 1.2
  }

  .yzzq-amen-txt {
    font-size: 14px;
    line-height: 1.35
  }

  .yzzq-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 999999
  }

  .yzzq-modal.open {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px
  }

  .yzzq-modal__back {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, .58);
    z-index: 0
  }

  .yzzq-modal__wrap {
    position: relative;
    z-index: 1;
    width: min(100%, 760px);
    max-width: 760px;
    margin: 0;
    padding: 0
  }

  .yzzq-modal__card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    padding: 14px;
    max-height: calc(100dvh - 24px);
    overflow: auto;
    -webkit-overflow-scrolling: touch
  }

  .yzzq-modal__head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px
  }

  .yzzq-close {
    border: 0;
    background: #f3f4f6;
    border-radius: 8px;
    padding: 8px 10px;
    cursor: pointer;
    font-weight: 700
  }

  .yzzq-survey-wrap {
    display: none;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden
  }

  .yzzq-survey-wrap.active {
    display: block
  }

  .yzzq-pay-choices {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px
  }

  @media(min-width:760px) {
    .yzzq-pay-choices {
      grid-template-columns: 1fr 1fr
    }
  }

  @media(max-width:759px) {
    .yzzq-modal.open {
      align-items: flex-end;
      padding: 0
    }

    .yzzq-modal__wrap {
      width: 100%;
      max-width: none
    }

    .yzzq-modal__card {
      border-radius: 18px 18px 0 0;
      border-left: 0;
      border-right: 0;
      border-bottom: 0;
      max-height: min(86dvh, calc(100dvh - 24px));
      padding: 16px 14px calc(16px + env(safe-area-inset-bottom))
    }
  }
</style>
<script>
  (function () {
    var root = document.getElementById('yzzq-root');
    if (!root) return;
    root.innerHTML = '' +
      '<div class="yzzq-card yzzq-img-wrap" id="imgWrap"><img class="yzzq-img" id="img" alt="Yate"></div>' +
      '<div class="yzzq-card"><h1 style="margin:0 0 8px;font-size:28px">Tu cotizacion</h1><p id="hello" style="margin:0;color:#6b7280">Cargando informacion...</p><div style="height:10px"></div><span id="status" class="yzzq-status">Cargando...</span>' +
      '<div class="yzzq-offer">' +
      '<div class="yzzq-offer-head"><div class="yzzq-offer-title">Tu cotización vence en:</div><button id="acceptOfferBtn" class="yzzq-offer-btn" type="button">🔒 Cerrar oferta</button></div>' +
      '<div class="yzzq-offer-timer" id="offerTimer">' +
      '<div class="yzzq-offer-box"><div class="yzzq-offer-num" id="od">--</div><div class="yzzq-offer-lbl">Dias</div></div>' +
      '<div class="yzzq-offer-box"><div class="yzzq-offer-num" id="oh">--</div><div class="yzzq-offer-lbl">Horas</div></div>' +
      '<div class="yzzq-offer-box"><div class="yzzq-offer-num" id="om">--</div><div class="yzzq-offer-lbl">Min</div></div>' +
      '<div class="yzzq-offer-box"><div class="yzzq-offer-num" id="os">--</div><div class="yzzq-offer-lbl">Seg</div></div>' +
      '</div>' +
      '<div id="tripDaysBadge" style="margin-top:10px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.35);border-radius:10px;padding:10px 12px;color:#fff;font-size:15px;font-weight:700">Calculando dias restantes para tu viaje...</div>' +
      '</div>' +
      '<p id="timerNote" style="font-size:12px;color:#6b7280;margin-top:8px"></p></div>' +
      '<div class="yzzq-grid"><div class="yzzq-card">' +
      '<div class="yzzq-row"><div class="yzzq-k">Cliente</div><div id="name" class="yzzq-v">-</div></div>' +
      '<div class="yzzq-row"><div class="yzzq-k">Yate</div><div id="yacht" class="yzzq-v">-</div></div>' +
      '<div class="yzzq-row"><div class="yzzq-k">Destino</div><div id="dest" class="yzzq-v">-</div></div>' +
      '<div class="yzzq-row"><div class="yzzq-k">Fecha</div><div id="date" class="yzzq-v">-</div></div>' +
      '<div class="yzzq-row"><div class="yzzq-k">Horario</div><div id="time" class="yzzq-v">-</div></div>' +
      '<div class="yzzq-row"><div class="yzzq-k">Pax</div><div id="pax" class="yzzq-v">-</div></div>' +
      '<div class="yzzq-row"><div class="yzzq-k">Marina</div><div id="marina" class="yzzq-v">-</div></div>' +
      '<div class="yzzq-row"><div class="yzzq-k">Ubicacion De Abordaje</div><div class="yzzq-v"><a id="maps" class="yzzq-map-btn" href="#" target="_blank" rel="noopener">📍 Ver ubicación</a></div></div>' +
      '</div><div class="yzzq-card">' +
      '<div class="yzzq-row"><div class="yzzq-k">Experiencia</div><div id="exp" class="yzzq-v">-</div></div>' +
      '<div class="yzzq-row"><div class="yzzq-k">Extras</div><div id="extra" class="yzzq-v">-</div></div>' +
      '<div class="yzzq-row"><div class="yzzq-k">Total</div><div id="total" class="yzzq-v">-</div></div><div style="height:12px"></div>' +
      '<div class="yzzq-actions">' +
      '<button id="reserveBtn" class="yzzq-btn yzzq-btn-success" type="button">💳 Reservar Con 50%</button>' +
      '<a id="yachtUrlBtn" class="yzzq-btn yzzq-btn-primary" target="_blank" rel="noopener">🖼️ Ver más imagenes</a>' +
      '<button id="termsBtn" class="yzzq-btn yzzq-btn-light" type="button">📘 Ver terminos y condiciones</button>' +
      '<a id="waBtn" class="yzzq-btn yzzq-btn-dark" target="_blank" rel="noopener">🟢 Contactar asesor</a>' +
      '</div>' +
      '<p id="depositNote" style="font-size:12px;color:#6b7280"></p></div></div>';

    root.innerHTML += '' +
      '<div class="yzzq-card yzzq-toggle"><details id="reco"><summary>Recomendaciones Del Viaje</summary>' +
      '<div class="yzzq-toggle-body">' +
      '<p><b>Antes:</b> llega 20 minutos antes, confirma clima, punto de abordaje y lista de pasajeros.</p>' +
      '<p><b>Durante:</b> sigue indicaciones de capitan y tripulacion, mantente hidratado y cuida tus pertenencias.</p>' +
      '<p><b>Despues:</b> comparte cualquier incidencia el mismo dia para una atencion rapida y efectiva.</p>' +
      '</div></details></div>';

    root.innerHTML += '' +
      '<div class="yzzq-card yzzq-toggle"><details id="amenitiesDetails"><summary>Caracteristicas Y Amenidades Del Yate</summary>' +
      '<div class="yzzq-toggle-body"><div id="amenitiesList" class="yzzq-amen-list"></div></div>' +
      '</details></div>';

    root.innerHTML += '' +
      '<div id="payModal" class="yzzq-modal" aria-hidden="true">' +
      '<div class="yzzq-modal__back" data-close="payModal"></div>' +
      '<div class="yzzq-modal__wrap"><div class="yzzq-modal__card">' +
      '<div class="yzzq-modal__head"><strong>Reserva con 50%</strong><button class="yzzq-close" data-close="payModal" type="button">Cerrar</button></div>' +
      '<p id="depositLabel" class="yzzq-note"></p>' +
      '<div id="payChoices" class="yzzq-pay-choices">' +
      '<button id="payCardBtn" class="yzzq-btn yzzq-btn-primary" type="button">Pagar con tarjeta credito / debito</button>' +
      '<button id="payTransferBtn" class="yzzq-btn yzzq-btn-dark" type="button">Pagar con transferencia</button>' +
      '</div>' +
      '<div id="definePayDateWrap" style="margin-top:8px;text-align:center;display:none"><button id="definePayDateBtn" class="define-pay-date-btn" type="button" style="border:0;background:#f3f4f6;border-radius:8px;padding:8px 10px;font-size:12px;font-weight:700;cursor:pointer">📅 Definir una fecha de pago</button></div>' +
      '<div id="surveyWrap" class="yzzq-survey-wrap">' +
      '<iframe src="" style="border:none;width:100%;min-height:680px;" scrolling="no" id="OXyI9rBvZVpcE877iYso" title="survey"></iframe>' +
      '</div>' +
      '</div></div></div>';

    root.innerHTML += '' +
      '<div id="termsModal" class="yzzq-modal" aria-hidden="true">' +
      '<div class="yzzq-modal__back" data-close="termsModal"></div>' +
      '<div class="yzzq-modal__wrap"><div class="yzzq-modal__card">' +
      '<div class="yzzq-modal__head"><strong>Terminos y condiciones - Yatezzitos Mexico</strong><button class="yzzq-close" data-close="termsModal" type="button">Cerrar</button></div>' +
      '<div style="line-height:1.6">' +
      '<p><b>Reembolsos:</b> no validos bajo ninguna situacion.</p>' +
      '<p><b>Cancelacion por cliente:</b> las cancelaciones no son reembolsables.</p>' +
      '<p><b>Causas ajenas:</b> si hay cierre de puerto por mal clima o falla tecnica de la embarcacion antes del abordaje, se intentara reprogramar segun disponibilidad.</p>' +
      '<p><b>Cambios de fecha o titular:</b> deben solicitarse con al menos 15 dias de anticipacion. Estan sujetos a disponibilidad y politicas vigentes.</p>' +
      '<p><b>Reservacion:</b> se requiere anticipo para bloquear fecha. El saldo restante se liquida segun instrucciones de pago confirmadas por tu asesor.</p>' +
      '<p><b>Puntualidad:</b> existe tolerancia maxima de 15 minutos. El tiempo perdido por llegada tarde no se repone ni reembolsa.</p>' +
      '<p><b>Responsabilidad por danos:</b> cualquier dano por mal uso de la embarcacion o accesorios sera responsabilidad del cliente.</p>' +
      '<p><b>Pertenencias personales:</b> cada pasajero es responsable de sus objetos personales durante y despues del servicio.</p>' +
      '<p><b>Seguridad:</b> es obligatorio seguir las indicaciones del capitan y tripulacion. Conductas de riesgo pueden limitar actividades a bordo.</p>' +
      '<p><b>Consumo responsable:</b> el consumo de alcohol debe realizarse con responsabilidad y sin poner en riesgo a los pasajeros o tripulacion.</p>' +
      '<p><b>Fuerza mayor:</b> situaciones externas fuera de control operativo se atenderan bajo politica de reprogramacion cuando aplique.</p>' +
      '</div>' +
      '</div></div></div>';

    root.innerHTML += '' +
      '<div id="payDateModal" class="yzzq-modal" aria-hidden="true">' +
      '<div class="yzzq-modal__back" data-close="payDateModal"></div>' +
      '<div class="yzzq-modal__wrap"><div class="yzzq-modal__card">' +
      '<div class="yzzq-modal__head"><strong>Definir Fecha De Pago</strong><button class="yzzq-close" data-close="payDateModal" type="button">Cerrar</button></div>' +
      '<iframe src="" style="width:100%;height:636px;border:none;border-radius:3px" id="inline-KiQhwWCcVHUnUNh94QiL" data-layout="{\'id\':\'INLINE\'}" data-trigger-type="alwaysShow" data-trigger-value="" data-activation-type="alwaysActivated" data-activation-value="" data-deactivation-type="neverDeactivate" data-deactivation-value="" data-form-name="Definir fecha de pago" data-height="636" data-layout-iframe-id="inline-KiQhwWCcVHUnUNh94QiL" data-form-id="KiQhwWCcVHUnUNh94QiL" title="Definir fecha de pago"></iframe>' +
      '</div></div></div>';

    var $ = function (id) { return document.getElementById(id); };
    function mountModalToBody(id) {
      var modal = $(id);
      if (modal && modal.parentNode !== document.body) {
        document.body.appendChild(modal);
      }
    }
    mountModalToBody('payModal');
    mountModalToBody('termsModal');
    mountModalToBody('payDateModal');
    var qt = (new URLSearchParams(location.search).get('qt') || '').trim();
    var state = { qt: qt, deposit50: NaN, data: null };
    var WA = '526691324073';
    var SURVEY_BASE = 'https://link.yatezzitos.com/widget/survey/OXyI9rBvZVpcE877iYso';
    var TRANSFER_URL = 'https://yatezzitos.com/es/pagos-con-transferencia/';

    function setStatus(msg, type) { var s = $('status'); if (!s) return; s.textContent = msg; s.className = 'yzzq-status' + (type ? ' ' + type : ''); }
    function money(v) { if (typeof v === 'number') return v; if (!v) return NaN; var n = Number(String(v).replace(/[^0-9.,-]/g, '').replace(/,/g, '')); return isFinite(n) ? n : NaN; }
    function mxn(n) { return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 }).format(n); }
    function wa(msg) { var a = $('waBtn'); if (!a) return; a.href = 'https://api.whatsapp.com/send?phone=' + WA + '&text=' + encodeURIComponent(msg || 'Hola, necesito ayuda con mi cotizacion.'); }
    function langPrefix() { var p = location.pathname || ''; if (p.indexOf('/es/') === 0) return '/es'; if (p.indexOf('/en/') === 0) return '/en'; return ''; }
    function uniq(arr) { return arr.filter(function (v, i, a) { return a.indexOf(v) === i; }); }
    function syncBodyModalState() {
      var anyOpen = document.querySelector('.yzzq-modal.open');
      document.body.style.overflow = anyOpen ? 'hidden' : '';
    }
    function openModal(id) {
      mountModalToBody(id);
      var m = $(id);
      if (!m) return;
      m.classList.add('open');
      m.setAttribute('aria-hidden', 'false');
      syncBodyModalState();
    }
    function closeModal(id) {
      var m = $(id);
      if (!m) return;
      m.classList.remove('open');
      m.setAttribute('aria-hidden', 'true');
      syncBodyModalState();
    }
    function firstName(full) { if (!full) return ''; return String(full).trim().split(/\s+/)[0] || ''; }

    var endpoints = uniq([
      location.origin + langPrefix() + '/wp-json/yzz/v1/quote',
      location.origin + '/wp-json/yzz/v1/quote',
      location.origin + '/es/wp-json/yzz/v1/quote',
      location.origin + '/en/wp-json/yzz/v1/quote'
    ]);

    function parseDT(d, t) {
      if (!d) return null;
      var ds = String(d).trim(), ts = String(t || '00:00').trim();
      var iso = ds.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/), lat = ds.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/), tm = ts.match(/^(\d{1,2}):(\d{2})/);
      var hh = tm ? Number(tm[1]) : 0, mm = tm ? Number(tm[2]) : 0;
      if (iso) return new Date(Number(iso[1]), Number(iso[2]) - 1, Number(iso[3]), hh, mm, 0);
      if (lat) { var y = lat[3].length === 2 ? Number('20' + lat[3]) : Number(lat[3]); return new Date(y, Number(lat[2]) - 1, Number(lat[1]), hh, mm, 0); }
      return null;
    }

    function updateTripDaysBadge(target) {
      var badge = $('tripDaysBadge');
      if (!badge) return;
      var diff = target.getTime() - Date.now();
      if (diff <= 0) {
        badge.textContent = 'Tu salida es hoy o ya comenzó.';
        return;
      }
      var days = Math.ceil(diff / 86400000);
      badge.textContent = 'Faltan ' + days + ' día(s) para tu fecha de viaje.';
    }
    function getOfferDeadline() {
      var key = 'yzz_offer_deadline_' + state.qt;
      var current = localStorage.getItem(key);
      var now = Date.now();
      if (current && Number(current) > now) {
        return Number(current);
      }
      var deadline = now + (72 * 60 * 60 * 1000);
      localStorage.setItem(key, String(deadline));
      return deadline;
    }
    function startOfferCountdown() {
      var deadline = getOfferDeadline();
      function tick() {
        var ms = deadline - Date.now();
        if (ms <= 0) {
          $('od').textContent = '00'; $('oh').textContent = '00'; $('om').textContent = '00'; $('os').textContent = '00';
          return;
        }
        var d = Math.floor(ms / 86400000); ms -= d * 86400000;
        var h = Math.floor(ms / 3600000); ms -= h * 3600000;
        var m = Math.floor(ms / 60000); ms -= m * 60000;
        var s = Math.floor(ms / 1000);
        $('od').textContent = String(d).padStart(2, '0');
        $('oh').textContent = String(h).padStart(2, '0');
        $('om').textContent = String(m).padStart(2, '0');
        $('os').textContent = String(s).padStart(2, '0');
      }
      tick();
      setInterval(tick, 1000);
    }

    function pick(obj, keys) {
      for (var i = 0; i < keys.length; i++) {
        var k = keys[i];
        if (obj[k] !== undefined && obj[k] !== null && String(obj[k]).trim() !== '') return obj[k];
      }
      return '';
    }
    function deepPick(obj, keys) {
      var direct = pick(obj, keys);
      if (String(direct || '').trim() !== '') return direct;
      if (obj && typeof obj.payload === 'object') {
        var nestedObj = pick(obj.payload, keys);
        if (String(nestedObj || '').trim() !== '') return nestedObj;
      }
      if (obj && typeof obj.data === 'object') {
        var nestedData = pick(obj.data, keys);
        if (String(nestedData || '').trim() !== '') return nestedData;
      }
      return '';
    }
    function pickByPattern(obj, regex) {
      if (!obj || typeof obj !== 'object') return '';
      for (var k in obj) {
        if (!Object.prototype.hasOwnProperty.call(obj, k)) continue;
        var v = obj[k];
        if (v === undefined || v === null) continue;
        if (String(v).trim() === '') continue;
        if (regex.test(String(k))) return v;
      }
      return '';
    }
    function pickAmenitiesSource(obj) {
      var value = deepPick(obj, [
        'amenities_raw',
        'amenities_raw_text',
        'caractersticas_y_amenidades_del_yate',
        'caracteristicas_y_amenidades_del_yate',
        'caracteristicas_amenidades_del_yate',
        'amenidades',
        'amenity_list',
        'amenities'
      ]);
      if (String(value || '').trim() !== '') return value;
      var fuzzy = pickByPattern(obj, /(amenid|amenit|caracteri|caracters|feature|inclu)/i);
      if (String(fuzzy || '').trim() !== '') return fuzzy;
      if (obj && typeof obj.payload === 'object') {
        var fuzzyPayload = pickByPattern(obj.payload, /(amenid|amenit|caracteri|caracters|feature|inclu)/i);
        if (String(fuzzyPayload || '').trim() !== '') return fuzzyPayload;
      }
      if (obj && typeof obj.data === 'object') {
        var fuzzyData = pickByPattern(obj.data, /(amenid|amenit|caracteri|caracters|feature|inclu)/i);
        if (String(fuzzyData || '').trim() !== '') return fuzzyData;
      }
      var maybeExtra = deepPick(obj, ['extra', 'extras', 'inclusiones_adicionales']);
      if (String(maybeExtra || '').trim() !== '' && /,|;|\n|\||\[/.test(String(maybeExtra))) return maybeExtra;
      return '';
    }
    function normalizeAmenityKey(value) {
      return String(value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[()]/g, '')
        .replace(/\s+/g, ' ')
        .trim();
    }
    var AMENITY_EMOJI_MAP = {
      'agua embotellada': '💧',
      'aire acondicionado': '❄️',
      'alfombra / tapete acuatico': '🌊',
      'alfombra / tapete acuatico': '🌊',
      'cervezas 12': '🍺',
      'cervezas 24': '🍺',
      'ceviches o alimentos de bienvenida': '🍤',
      'chalecos salvavidas': '🛟',
      'chef a bordo': '👨‍🍳',
      'cocina funcional': '🍽️',
      'conexion usb para telefonos': '🔌',
      'dona inflable': '🛟',
      'equipo de pesca deportiva': '🎣',
      'equipo de snorkel': '🤿',
      'equipo de sonido conexion bluetooth': '🔊',
      'luces subacuaticas': '💡',
      'frente acolchonado': '🛥️',
      'fruta fresca de bienvenida': '🍉',
      'gastos de peaje / impuestos de muelle': '🧾',
      'acceso a todas las playas / brazaletes': '🏝️',
      'campamento para playa': '⛱️',
      'guacamole de bienvenida': '🥑',
      'gps': '🧭',
      'guia de turismo': '🧑‍💼',
      'hielera': '🧊',
      'hielo': '🧊',
      'internet': '📶',
      'jetski / moto acuatica': '🏍️',
      'juguetes inflables': '🛟',
      'kayacs dobles': '🛶',
      'kayac individual': '🛶',
      'tabla de paddle board': '🏄',
      'kit de primeros auxilios': '⛑️',
      'lancha auxiliar / dingui': '🚤',
      'margaritas / bebidas durante el viaje': '🍹',
      'capitan y marinero certificados': '👨‍✈️',
      'mesa de comedor': '🍽️',
      'parrilla': '🔥',
      'refrescos': '🥤',
      'refrigerador': '🧊',
      'sala con tv': '📺',
      'seguro de viaje': '🛡️',
      'suite nupcial': '💍',
      'terraza / flybridge': '☀️',
      'toallas': '🧺',
      'tripulantes multilingues': '🗣️'
    };
    function amenityEmoji(item) {
      var normalized = normalizeAmenityKey(item);
      if (AMENITY_EMOJI_MAP[normalized]) return AMENITY_EMOJI_MAP[normalized];
      var t = normalized;
      if (t.indexOf('aire') > -1) return '❄️';
      if (t.indexOf('agua') > -1) return '💧';
      if (t.indexOf('cerveza') > -1 || t.indexOf('margarita') > -1 || t.indexOf('refresco') > -1 || t.indexOf('hielo') > -1) return '🍹';
      if (t.indexOf('chaleco') > -1 || t.indexOf('primeros auxilios') > -1 || t.indexOf('seguro') > -1) return '🛟';
      if (t.indexOf('capitan') > -1 || t.indexOf('marinero') > -1 || t.indexOf('tripulante') > -1 || t.indexOf('guia') > -1) return '👨‍✈️';
      if (t.indexOf('snorkel') > -1 || t.indexOf('pesca') > -1 || t.indexOf('paddle') > -1 || t.indexOf('kayak') > -1 || t.indexOf('jetski') > -1 || t.indexOf('moto acuatica') > -1 || t.indexOf('moto acu') > -1 || t.indexOf('inflable') > -1) return '🌊';
      if (t.indexOf('bluetooth') > -1 || t.indexOf('sonido') > -1) return '🔊';
      if (t.indexOf('wifi') > -1 || t.indexOf('internet') > -1 || t.indexOf('usb') > -1 || t.indexOf('gps') > -1) return '📶';
      if (t.indexOf('chef') > -1 || t.indexOf('cocina') > -1 || t.indexOf('parrilla') > -1 || t.indexOf('fruta') > -1 || t.indexOf('guacamole') > -1 || t.indexOf('ceviche') > -1 || t.indexOf('comedor') > -1) return '🍽️';
      if (t.indexOf('toalla') > -1 || t.indexOf('suite') > -1 || t.indexOf('sala') > -1 || t.indexOf('tv') > -1) return '🛋️';
      if (t.indexOf('terraza') > -1 || t.indexOf('flybridge') > -1 || t.indexOf('frente acolchonado') > -1) return '🛥️';
      return '✅';
    }
    function parseAmenities(raw) {
      if (!raw) return [];
      if (Array.isArray(raw)) return raw.map(function (x) { return String(x).trim(); }).filter(Boolean);
      var s = String(raw).trim();
      if (!s) return [];
      s = s.replace(/&quot;/g, '"').replace(/&#34;/g, '"').replace(/\\"/g, '"');
      // Try JSON decode up to 2 times (for double-encoded payloads)
      for (var i = 0; i < 2; i++) {
        if ((s[0] === '[' && s[s.length - 1] === ']') || (s[0] === '{' && s[s.length - 1] === '}') || (s[0] === '"' && s[s.length - 1] === '"')) {
          try {
            var j = JSON.parse(s);
            if (Array.isArray(j)) return j.map(function (x) { return String(x).trim(); }).filter(Boolean);
            if (j && Array.isArray(j.values)) return j.values.map(function (x) { return String(x).trim(); }).filter(Boolean);
            if (typeof j === 'string') {
              s = j.trim();
              continue;
            }
          } catch (e) { }
        }
        break;
      }
      if ((s[0] === '"' && s[s.length - 1] === '"') || (s[0] === "'" && s[s.length - 1] === "'")) {
        s = s.slice(1, -1);
      }
      return s
        .split(/\n|,|;|\|/)
        .map(function (x) { return String(x).replace(/^\s*[-•]\s*/, '').trim(); })
        .filter(Boolean);
    }
    function renderAmenities(raw) {
      var box = $('amenitiesList');
      if (!box) return;
      var items = parseAmenities(raw);
      if (!items.length) {
        box.innerHTML = '<p class="yzzq-note">No hay amenidades registradas en esta cotizacion.</p>';
        if (location.search.indexOf('debug=1') > -1) {
          box.innerHTML += '<p class="yzzq-note"><b>DEBUG amenities_raw:</b> ' + String(raw || '').replace(/</g, '&lt;') + '</p>';
        }
        return;
      }
      box.innerHTML = items.map(function (it) {
        return '<div class="yzzq-amen-item"><span class="yzzq-amen-emo">' + amenityEmoji(it) + '</span><div class="yzzq-amen-txt">' + it + '</div></div>';
      }).join('');
    }
    function buildTransferUrl() {
      var url = new URL(TRANSFER_URL);
      url.searchParams.set('qt', state.qt);
      if (isFinite(state.deposit50)) { url.searchParams.set('a', String(Math.round(state.deposit50))); }
      return url.toString();
    }
    function buildSurveyUrl() {
      var url = new URL(SURVEY_BASE);
      var d = state.data || {};
      var n = d.name || '';
      var e = d.email || '';
      var p = d.phone || '';
      var amt = isFinite(state.deposit50) ? String(Math.round(state.deposit50)) : '';
      url.searchParams.set('qt', state.qt);
      if (d.contact_id) { url.searchParams.set('contact_id', d.contact_id); }
      if (n) {
        url.searchParams.set('name', n);
        url.searchParams.set('full_name', n);
        url.searchParams.set('first_name', firstName(n));
      }
      if (e) {
        url.searchParams.set('email', e);
        url.searchParams.set('contact_email', e);
      }
      if (p) {
        url.searchParams.set('phone', p);
        url.searchParams.set('contact_phone', p);
      }
      if (amt) {
        url.searchParams.set('amount', amt);
        url.searchParams.set('deposit_50', amt);
      }
      return url.toString();
    }
    function buildPayDateFormUrl() {
      var url = new URL('https://link.yatezzitos.com/widget/form/KiQhwWCcVHUnUNh94QiL');
      var d = state.data || {};
      var n = d.name || '';
      var e = d.email || '';
      var p = d.phone || '';
      url.searchParams.set('ts', String(Date.now()));
      url.searchParams.set('qt', state.qt);
      if (d.contact_id) { url.searchParams.set('contact_id', d.contact_id); }
      if (n) {
        url.searchParams.set('name', n);
        url.searchParams.set('full_name', n);
        url.searchParams.set('first_name', firstName(n));
        url.searchParams.set('contact_name', n);
      }
      if (e) {
        url.searchParams.set('email', e);
        url.searchParams.set('contact_email', e);
        url.searchParams.set('correo', e);
      }
      if (p) {
        url.searchParams.set('phone', p);
        url.searchParams.set('contact_phone', p);
        url.searchParams.set('telefono', p);
      }
      return url.toString();
    }
    function openPayDateModalWithPrefill() {
      ensureSurveyScript();
      var payDateFrame = $('inline-KiQhwWCcVHUnUNh94QiL');
      if (payDateFrame) {
        payDateFrame.src = buildPayDateFormUrl();
      }
      openModal('payDateModal');
    }
    function ensureSurveyScript() {
      if (document.getElementById('yzz-ghl-survey-script')) return;
      var s = document.createElement('script');
      s.id = 'yzz-ghl-survey-script';
      s.src = 'https://link.yatezzitos.com/js/form_embed.js';
      document.body.appendChild(s);
    }
    function openCardSurvey() {
      var choices = $('payChoices');
      var wrap = $('surveyWrap');
      var frame = $('OXyI9rBvZVpcE877iYso');
      if (!choices || !wrap || !frame) return;
      choices.style.display = 'none';
      wrap.classList.add('active');
      frame.src = buildSurveyUrl();
      ensureSurveyScript();
    }
    function setPayDateVisibility(show) {
      var wrap = $('definePayDateWrap');
      if (!wrap) return;
      wrap.style.display = show ? 'block' : 'none';
    }
    function openPaymentModal(mode) {
      $('depositLabel').textContent = isFinite(state.deposit50)
        ? ('Anticipo para reservar: ' + mxn(state.deposit50))
        : 'Anticipo para reservar: 50% del total';
      resetPayModal();
      setPayDateVisibility(mode === 'offer');
      openModal('payModal');
    }
    function resetPayModal() {
      var choices = $('payChoices');
      var wrap = $('surveyWrap');
      if (choices) choices.style.display = 'grid';
      if (wrap) wrap.classList.remove('active');
      setPayDateVisibility(false);
    }
    var lastUiActionAt = 0;
    function allowUiAction() {
      var now = Date.now();
      if (now - lastUiActionAt < 450) return false;
      lastUiActionAt = now;
      return true;
    }
    function handleUiAction(e) {
      if (!e || !e.target || !e.target.closest) return false;

      var reserve = e.target.closest('#reserveBtn');
      if (reserve) {
        e.preventDefault();
        if (!allowUiAction()) return true;
        openPaymentModal('reserve');
        return true;
      }

      var offer = e.target.closest('#acceptOfferBtn');
      if (offer) {
        e.preventDefault();
        if (!allowUiAction()) return true;
        openPaymentModal('offer');
        return true;
      }

      var terms = e.target.closest('#termsBtn');
      if (terms) {
        e.preventDefault();
        if (!allowUiAction()) return true;
        openModal('termsModal');
        return true;
      }

      var payCard = e.target.closest('#payCardBtn');
      if (payCard) {
        e.preventDefault();
        if (!allowUiAction()) return true;
        openCardSurvey();
        return true;
      }

      var payTransfer = e.target.closest('#payTransferBtn');
      if (payTransfer) {
        e.preventDefault();
        if (!allowUiAction()) return true;
        window.location.href = buildTransferUrl();
        return true;
      }

      var payDate = e.target.closest('.define-pay-date-btn');
      if (payDate) {
        e.preventDefault();
        if (!allowUiAction()) return true;
        openPayDateModalWithPrefill();
        return true;
      }

      var close = e.target.getAttribute ? e.target.getAttribute('data-close') : null;
      if (close) {
        e.preventDefault();
        if (!allowUiAction()) return true;
        closeModal(close);
        if (close === 'payModal') { resetPayModal(); }
        return true;
      }

      return false;
    }

    if (!qt) { setStatus('No se encontro token', 'err'); $('hello').textContent = 'No encontramos tu cotizacion. Pide a tu asesor que reenvie el enlace.'; wa('Hola, mi enlace de cotizacion no trae token (qt).'); return; }

    wa('Hola, necesito ayuda con mi cotizacion. Token: ' + qt);
    startOfferCountdown();

    (async function () {
      var raw = null, lastErr = null;
      for (var i = 0; i < endpoints.length; i++) {
        try {
          var r = await fetch(endpoints[i] + '?qt=' + encodeURIComponent(qt), { method: 'GET', cache: 'no-store', credentials: 'omit' });
          var j = await r.json().catch(function () { return {}; });
          if (r.ok && j && !j.error) { raw = j; break; }
        } catch (e) { lastErr = e; }
      }
      if (!raw) { throw (lastErr || new Error('not_found')); }

      var data = {
        name: pick(raw, ['name', 'full_name', 'contact_name']),
        y: pick(raw, ['y', 'yacht', 'yacht_name']),
        d: pick(raw, ['d', 'dest', 'destino', 'destinos']),
        f: pick(raw, ['f', 'fecha', 'fecha_de_viaje', 'travel_date']),
        hs: pick(raw, ['hs', 'hora_de_salida', 'departure_time']),
        hr: pick(raw, ['hr', 'hora_de_regreso', 'return_time']),
        pax: pick(raw, ['pax', 'passengers', 'number_of_passengers']),
        mar: pick(raw, ['mar', 'marina', 'marina_name']),
        maps: pick(raw, ['maps', 'map', 'google_maps_link']),
        img: pick(raw, ['img', 'image', 'imagen_principal_del_yate_upload']),
        total: pick(raw, ['total', 'total_cost', 'precio_total']),
        exp: pick(raw, ['exp', 'experiencia', 'experiencia_reservada']),
        extra: pick(raw, ['extra', 'extras', 'inclusiones_adicionales']),
        email: pick(raw, ['email', 'correo', 'contact_email']),
        phone: pick(raw, ['phone', 'telefono', 'tel', 'contact_phone', 'mobile']),
        contact_id: pick(raw, ['contact_id']),
        yacht_url: pick(raw, ['url_del_yate', 'yacht_url', 'url_yate']),
        amenities_raw: pickAmenitiesSource(raw)
      };
      state.data = data;

      $('hello').textContent = data.name ? ('Hola ' + data.name + ', esta es tu cotizacion completa.') : 'Esta es tu cotizacion completa.';
      $('name').textContent = data.name || '-'; $('yacht').textContent = data.y || '-'; $('dest').textContent = data.d || '-'; $('date').textContent = data.f || '-';
      $('time').textContent = [data.hs, data.hr].filter(Boolean).join(' - ') || '-'; $('pax').textContent = data.pax || '-'; $('marina').textContent = data.mar || '-';
      $('exp').textContent = data.exp || '-'; $('extra').textContent = data.extra || '-';
      if (data.maps) { $('maps').href = data.maps; }

      if (data.img) {
        var img = String(data.img).split(',')[0].trim();
        if (img) { $('img').src = img; $('imgWrap').style.display = 'flex'; }
      }
      if ($('yachtUrlBtn')) {
        if (data.yacht_url) {
          $('yachtUrlBtn').href = data.yacht_url;
          $('yachtUrlBtn').style.display = 'block';
        } else {
          $('yachtUrlBtn').style.display = 'none';
        }
      }
      renderAmenities(data.amenities_raw);

      var total = money(data.total); $('total').textContent = isFinite(total) ? mxn(total) : (data.total || '-');
      state.deposit50 = isFinite(total) ? total * 0.5 : NaN;
      $('reserveBtn').textContent = isFinite(state.deposit50) ? ('💳 Reservar Con ' + mxn(state.deposit50) + ' (50%)') : '💳 Reservar Con 50%';
      $('depositNote').textContent = isFinite(state.deposit50) ? ('Anticipo para apartar: ' + mxn(state.deposit50) + ' (50%).') : 'Anticipo para apartar: 50% del total.';

      wa('Hola, estoy viendo mi cotizacion (token ' + qt + ') de ' + (data.y || 'mi yate') + ' hacia ' + (data.d || 'mi destino') + '. Me apoyas?');
      setStatus('Cotizacion cargada', 'ok');

      var dt = parseDT(data.f, data.hs);
      if (dt) { updateTripDaysBadge(dt); $('timerNote').textContent = 'Fecha y hora del viaje: ' + (data.f || '-') + ' ' + (data.hs || ''); } else { $('timerNote').textContent = 'No se pudo calcular los días restantes por formato de fecha/hora.'; }

      if (location.search.indexOf('debug=1') > -1) {
        var pre = document.createElement('pre');
        pre.style.whiteSpace = 'pre-wrap'; pre.style.fontSize = '11px'; pre.style.marginTop = '10px';
        pre.textContent = 'DEBUG RAW RESPONSE\\n' + JSON.stringify(raw, null, 2);
        root.appendChild(pre);
      }

      var payDateFramePreload = $('inline-KiQhwWCcVHUnUNh94QiL');
      if (payDateFramePreload) {
        payDateFramePreload.src = buildPayDateFormUrl();
      }

      document.addEventListener('click', handleUiAction, true);
      document.addEventListener('pointerup', function (e) {
        if (e.pointerType === 'touch') {
          handleUiAction(e);
        }
      }, true);
      document.addEventListener('touchend', handleUiAction, { passive: false, capture: true });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          closeModal('payModal');
          closeModal('termsModal');
          closeModal('payDateModal');
          resetPayModal();
        }
      });
    })().catch(function () {
      setStatus('No encontrada', 'err');
      $('hello').textContent = 'No se encontro tu cotizacion. Pide a tu asesor que te reenvie el enlace.';
      if ($('tripDaysBadge')) $('tripDaysBadge').textContent = 'No se pudo calcular la fecha de viaje.';
      wa('Hola, no pude abrir mi cotizacion. Token: ' + qt);
    });
  })();
</script>
<?php
  return ob_get_clean();
}
function yzzq_reservation_shortcode()
{
  ob_start();
?>
<div id="yzzr-root" class="yzzr">
  <div class="yzzr-card yzzr-hero">
    <h1 class="yzzr-title">Mi reserva</h1>
    <p id="yzzr-hello" class="yzzr-sub">Cargando información de tu reserva...</p>
    <div style="height:10px"></div>
    <span id="yzzr-load-status" class="yzzr-status">Consultando...</span>
    <div id="yzzr-countdown" class="yzzr-countdown">
      <div class="yzzr-timebox">
        <div id="yzzr-d" class="yzzr-time-num">--</div>
        <div class="yzzr-time-lbl">Días</div>
      </div>
      <div class="yzzr-timebox">
        <div id="yzzr-h" class="yzzr-time-num">--</div>
        <div class="yzzr-time-lbl">Horas</div>
      </div>
      <div class="yzzr-timebox">
        <div id="yzzr-m" class="yzzr-time-num">--</div>
        <div class="yzzr-time-lbl">Min</div>
      </div>
      <div class="yzzr-timebox">
        <div id="yzzr-s" class="yzzr-time-num">--</div>
        <div class="yzzr-time-lbl">Seg</div>
      </div>
    </div>
    <p id="yzzr-count-note" class="yzzr-note"></p>
  </div>

  <div id="yzzr-error" class="yzzr-card yzzr-hidden">
    <h2 id="yzzr-error-title" class="yzzr-err-title">Reserva no disponible</h2>
    <p id="yzzr-error-msg" class="yzzr-sub">No pudimos cargar la reserva.</p>
  </div>

  <div id="yzzr-img-wrap" class="yzzr-card yzzr-img-wrap yzzr-hidden">
    <img id="yzzr-img" class="yzzr-img" alt="Embarcación">
  </div>

  <div id="yzzr-content" class="yzzr-hidden">
    <div class="yzzr-grid">
      <div class="yzzr-card">
        <div class="yzzr-row">
          <div class="yzzr-k">Cliente</div>
          <div id="yzzr-name" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Embarcación</div>
          <div id="yzzr-yacht" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Tipo de experiencia reservada</div>
          <div id="yzzr-exp" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Destino</div>
          <div id="yzzr-dest" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Fecha</div>
          <div id="yzzr-date" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Horario</div>
          <div id="yzzr-time" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Pasajeros</div>
          <div id="yzzr-pax" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Marina</div>
          <div id="yzzr-marina" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Ubicación de abordaje</div>
          <div class="yzzr-v"><a id="yzzr-meeting-link" class="yzzr-map-link yzzr-disabled" href="#" target="_blank"
              rel="noopener">—</a></div>
        </div>
      </div>

      <div class="yzzr-card">
        <div class="yzzr-row">
          <div class="yzzr-k">Estado de la reserva</div>
          <div><span id="yzzr-res-status" class="yzzr-pill is-pending">Pendiente de confirmación</span></div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Reservación ID</div>
          <div id="yzzr-res-id" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Anticipo pagado</div>
          <div id="yzzr-deposit" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Saldo pendiente</div>
          <div id="yzzr-balance" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Costo total</div>
          <div id="yzzr-total" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-row">
          <div class="yzzr-k">Método de pago</div>
          <div id="yzzr-payment" class="yzzr-v">—</div>
        </div>
        <div class="yzzr-actions">
          <a id="yzzr-map-btn" class="yzzr-btn yzzr-btn-primary yzzr-hidden" href="#" target="_blank" rel="noopener">📍
            Ver ubicación</a>
          <button id="yzzr-receipt-btn" class="yzzr-btn yzzr-btn-success" type="button">🧾 Descargar recibo de
            depósito</button>
          <button id="yzzr-balance-btn" class="yzzr-btn yzzr-btn-warning yzzr-hidden" type="button">💳 Pagar
            balance</button>
          <a id="yzzr-wa-btn" class="yzzr-btn yzzr-btn-dark" href="#" target="_blank" rel="noopener">🟢 Contactar por
            WhatsApp</a>
        </div>
        <p class="yzzr-note">Si algún dato no aparece, se mostrará como “—”.</p>
      </div>
    </div>

    <div class="yzzr-card">
      <details class="yzzr-faq">
        <summary>Características y amenidades incluidas</summary>
        <div id="yzzr-amenities-list" class="yzzr-amen-list"></div>
      </details>
    </div>

    <div class="yzzr-card">
      <h2 class="yzzr-sec-title">Política de anticipo y pago</h2>
      <div class="yzzr-faq-wrap">
        <details class="yzzr-faq">
          <summary>¿Cómo funciona el anticipo?</summary>
          <p><b>Anticipo:</b> 50% del costo total para confirmar tu reservación.</p>
        </details>
        <details class="yzzr-faq">
          <summary>¿Cuándo se paga el balance restante?</summary>
          <p><b>Balance restante:</b> Se abona el día del evento antes de iniciar el viaje (efectivo, transferencia o
            tarjeta con 5% adicional).</p>
        </details>
      </div>
    </div>

    <div class="yzzr-card yzzr-inline-actions">
      <button id="yzzr-reco-btn" class="yzzr-btn yzzr-btn-primary" type="button">🧭 Recomendaciones del viaje</button>
      <button id="yzzr-terms-btn" class="yzzr-btn yzzr-btn-dark" type="button">📘 Términos y condiciones</button>
    </div>
  </div>
</div>

<div id="yzzr-receipt-modal" class="yzzr-modal" aria-hidden="true">
  <div class="yzzr-modal-back" data-yzzr-close="yzzr-receipt-modal"></div>
  <div class="yzzr-modal-wrap">
    <div class="yzzr-modal-card">
      <div class="yzzr-modal-head">
        <strong>Recibo de depósito</strong>
        <button type="button" class="yzzr-close" data-yzzr-close="yzzr-receipt-modal">Cerrar</button>
      </div>
      <p class="yzzr-note">Si el formulario no carga en el popup, usa el enlace de respaldo.</p>
      <iframe id="yzzr-receipt-frame" title="Recibo de depósito"
        style="width:100%;height:70vh;border:0;border-radius:10px;background:#fff"></iframe>
      <div style="height:8px"></div>
      <a id="yzzr-receipt-fallback" class="yzzr-btn yzzr-btn-primary" href="#" target="_blank" rel="noopener">Abrir
        formulario en nueva pestaña</a>
    </div>
  </div>
</div>

<div id="yzzr-balance-modal" class="yzzr-modal" aria-hidden="true">
  <div class="yzzr-modal-back" data-yzzr-close="yzzr-balance-modal"></div>
  <div class="yzzr-modal-wrap">
    <div class="yzzr-modal-card">
      <div class="yzzr-modal-head">
        <strong>Pagar balance pendiente</strong>
        <button type="button" class="yzzr-close" data-yzzr-close="yzzr-balance-modal">Cerrar</button>
      </div>
      <p id="yzzr-balance-amount" class="yzzr-note"></p>
      <div id="yzzr-balance-choices" class="yzzr-pay-choices">
        <button id="yzzr-balance-card-btn" class="yzzr-btn yzzr-btn-primary" type="button">Pagar con tarjeta de crédito
          / débito</button>
        <button id="yzzr-balance-transfer-btn" class="yzzr-btn yzzr-btn-dark" type="button">Pagar con transferencia
          bancaria</button>
      </div>
      <div id="yzzr-balance-survey-wrap" class="yzzr-survey-wrap">
        <iframe src="" style="border:none;width:100%;min-height:680px;" scrolling="no" id="yzzr-balance-survey-frame"
          title="survey"></iframe>
      </div>
    </div>
  </div>
</div>

<div id="yzzr-reco-modal" class="yzzr-modal" aria-hidden="true">
  <div class="yzzr-modal-back" data-yzzr-close="yzzr-reco-modal"></div>
  <div class="yzzr-modal-wrap">
    <div class="yzzr-modal-card">
      <div class="yzzr-modal-head">
        <strong>Recomendaciones para tu aventura en el mar</strong>
        <button type="button" class="yzzr-close" data-yzzr-close="yzzr-reco-modal">Cerrar</button>
      </div>
      <div class="yzzr-copy">
        <p><b>Alimentos y bebidas:</b> Le recomendamos llevar sus alimentos y bebidas favoritas para disfrutar durante
          el recorrido. Aunque muchos destinos ofrecen la opción de bajar a un restaurante o envíos a bordo del yate,
          incluir snacks saludables, bebidas refrescantes y opciones especiales mejora la experiencia.</p>
        <p><b>Toallas:</b> Lleve toallas suficientes para usted y sus acompañantes para mantenerse cómodos y secos
          durante el recorrido.</p>
        <p><b>Protección solar:</b> Lleve protector solar, sombrero, gafas con protección UV y ropa ligera para
          protegerse de los rayos solares.</p>
        <p><b>Medicamentos para el mareo:</b> Si es propenso al mareo, considere prevención antes de abordar.</p>
        <p><b>Actitud positiva:</b> Venga preparado para relajarse, explorar y crear recuerdos inolvidables.</p>
        <p><b>Hidratación:</b> Lleve suficiente agua potable, especialmente si hará snorkel, kayac o paddleboard.</p>
        <p><b>Ropa adecuada:</b> Use ropa cómoda, ligera y de secado rápido. Lleve una muda adicional.</p>
        <p><b>Calzado adecuado:</b> Evite suelas duras o tacones. Recomendamos calzado antideslizante o náutico.</p>
        <p><b>Fotografía y entretenimiento:</b> Proteja su celular o use cámara impermeable. Lleve batería portátil.</p>
        <p><b>Disfrute responsable:</b> Si consume alcohol, hágalo con moderación para mantener una experiencia segura.
        </p>
        <p><b>Respete las indicaciones de la tripulación:</b> Ellos están capacitados para su seguridad y comodidad.</p>
        <p><b>Puntualidad:</b> Llegue al menos 30 minutos antes de la salida programada.</p>
        <p>Siguiendo estas recomendaciones, su experiencia con Yatezzitos será cómoda, segura y memorable. ¡Nos vemos a
          bordo!</p>
      </div>
    </div>
  </div>
</div>

<div id="yzzr-terms-modal" class="yzzr-modal" aria-hidden="true">
  <div class="yzzr-modal-back" data-yzzr-close="yzzr-terms-modal"></div>
  <div class="yzzr-modal-wrap">
    <div class="yzzr-modal-card">
      <div class="yzzr-modal-head">
        <strong>Términos y condiciones del servicio</strong>
        <button type="button" class="yzzr-close" data-yzzr-close="yzzr-terms-modal">Cerrar</button>
      </div>
      <div class="yzzr-copy">
        <p><b>1. Reembolso</b></p>
        <p><b>1.1 Cancelaciones:</b> No se realizan reembolsos por cancelación. Se ofrece re-agendar bajo condiciones
          establecidas.</p>
        <p><b>1.2 Condiciones climáticas:</b> Si el mal clima impide navegación segura, se permite re-agendar sin costo
          adicional.</p>
        <p><b>2. Cambios</b></p>
        <p><b>2.1 Cambio de titular:</b> Debe notificarse con mínimo 24 horas de anticipación.</p>
        <p><b>2.2 Cambio de fecha:</b> Debe solicitarse con al menos 14 días de anticipación.</p>
        <p><b>2.3 Cambio de embarcación:</b> No aplica salvo autorización extraordinaria de Yatezzitos México.</p>
        <p><b>2.4 Fecha abierta:</b> Puede otorgarse en casos especiales, sujeta a disponibilidad y ajustes en precio.
        </p>
        <p><b>3. Reservación</b></p>
        <p><b>3.1</b> Se requiere anticipo de 50% para confirmar la reservación.</p>
        <p><b>3.2</b> Si el anticipo queda incompleto, se debe completar hasta 7 días antes del viaje.</p>
        <p><b>4. Responsabilidad de daños y riesgos</b></p>
        <p><b>4.1</b> Los clientes son responsables de daños ocasionados durante el uso de la embarcación.</p>
        <p><b>4.2</b> Deben seguirse todas las instrucciones de la tripulación.</p>
        <p><b>5. Horarios y puntualidad</b></p>
        <p><b>5.1</b> Tolerancia máxima de 15 minutos siempre que no afecte otras reservaciones.</p>
        <p><b>5.2</b> Retrasos mayores a 60 minutos sin justificación se consideran servicio cumplido.</p>
        <p><b>5.3</b> Se recomienda llegar 30 minutos antes de la salida.</p>
        <p><b>6. Pérdida de objetos personales</b></p>
        <p>Yatezzitos no se hace responsable de objetos extraviados durante el recorrido.</p>
        <p>Gracias por confiar en Yatezzitos México.</p>
      </div>
    </div>
  </div>
</div>

<style>
  #yzzr-root {
    max-width: 980px;
    margin: 0 auto;
    padding: 16px;
    color: #111827;
    font-family: var(--yzz-font-family, Arial, sans-serif);
    box-sizing: border-box
  }

  #yzzr-root *,
  .yzzr-modal * {
    box-sizing: border-box
  }

  .yzzr-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 12px
  }

  .yzzr-title {
    margin: 0 0 8px;
    font-size: 28px;
    line-height: 1.15
  }

  .yzzr-sub {
    margin: 0;
    color: #6b7280
  }

  .yzzr-note {
    margin: 10px 0 0;
    font-size: 12px;
    color: #6b7280;
    line-height: 1.45
  }

  .yzzr-sec-title {
    margin: 0 0 10px;
    font-size: 18px;
    line-height: 1.3
  }

  .yzzr-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px
  }

  .yzzr-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    border-bottom: 1px dashed #e5e7eb;
    padding: 10px 0
  }

  .yzzr-row:last-child {
    border-bottom: 0
  }

  .yzzr-k {
    color: #6b7280
  }

  .yzzr-v {
    font-weight: 700;
    text-align: right;
    word-break: break-word;
    overflow-wrap: anywhere;
    max-width: 62%
  }

  .yzzr-status {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    background: #e5e7eb;
    padding: 6px 10px;
    font-size: 13px;
    font-weight: 700
  }

  .yzzr-status.is-ok {
    background: #d1fae5;
    color: #065f46
  }

  .yzzr-status.is-warn {
    background: #fef3c7;
    color: #92400e
  }

  .yzzr-status.is-error {
    background: #fee2e2;
    color: #991b1b
  }

  .yzzr-countdown {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-top: 12px
  }

  .yzzr-timebox {
    background: #0b1939;
    color: #fff;
    border-radius: 10px;
    text-align: center;
    padding: 10px
  }

  .yzzr-time-num {
    font-size: 24px;
    font-weight: 800;
    line-height: 1
  }

  .yzzr-time-lbl {
    font-size: 11px;
    color: #cbd5e1;
    text-transform: uppercase;
    margin-top: 4px
  }

  .yzzr-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700
  }

  .yzzr-pill.is-confirmed {
    background: #d1fae5;
    color: #065f46
  }

  .yzzr-pill.is-pending {
    background: #fef3c7;
    color: #92400e
  }

  .yzzr-pill.is-cancelled {
    background: #fee2e2;
    color: #991b1b
  }

  .yzzr-pill.is-other {
    background: #e5e7eb;
    color: #1f2937
  }

  .yzzr-actions {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
    margin-top: 12px
  }

  .yzzr-inline-actions {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px
  }

  .yzzr-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    border: 0;
    border-radius: 10px;
    padding: 12px 14px;
    font-weight: 700;
    text-decoration: none;
    color: #fff;
    cursor: pointer;
    text-align: center;
    min-height: 44px;
    touch-action: manipulation
  }

  .yzzr-btn-primary {
    background: #194395
  }

  .yzzr-btn-success {
    background: #00945e
  }

  .yzzr-btn-dark {
    background: #111827
  }

  .yzzr-btn-warning {
    background: #d97706
  }

  .yzzr-btn:hover,
  .yzzr-btn:focus,
  .yzzr-btn:active {
    color: #fff !important;
    text-decoration: none !important
  }

  .yzzr-map-link {
    font-weight: 700;
    color: #194395;
    text-decoration: underline;
    word-break: break-all
  }

  .yzzr-map-link.yzzr-disabled {
    pointer-events: none;
    color: #9ca3af;
    text-decoration: none
  }

  .yzzr-hidden {
    display: none !important
  }

  .yzzr-img-wrap {
    padding: 0;
    overflow: hidden
  }

  .yzzr-img {
    display: block;
    width: 100%;
    height: auto;
    max-height: 420px;
    object-fit: cover
  }

  .yzzr-amen-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px
  }

  .yzzr-amen-item {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fafafa
  }

  .yzzr-amen-emo {
    font-size: 18px;
    line-height: 1.2
  }

  .yzzr-amen-txt {
    font-size: 14px;
    line-height: 1.35
  }

  .yzzr-faq-wrap {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px
  }

  .yzzr-faq {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px;
    background: #fcfcfd
  }

  .yzzr-faq summary {
    cursor: pointer;
    font-weight: 700
  }

  .yzzr-faq p {
    margin: 10px 0 0;
    line-height: 1.45
  }

  .yzzr-faq .yzzr-amen-list {
    margin-top: 10px
  }

  .yzzr-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 999999
  }

  .yzzr-modal.open {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px
  }

  .yzzr-modal-back {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, .58);
    z-index: 0
  }

  .yzzr-modal-wrap {
    position: relative;
    z-index: 1;
    width: min(100%, 900px);
    max-width: 900px;
    margin: 0;
    padding: 0
  }

  .yzzr-modal-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    padding: 14px;
    max-height: calc(100dvh - 24px);
    overflow: auto;
    -webkit-overflow-scrolling: touch
  }

  .yzzr-modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px
  }

  .yzzr-close {
    border: 0;
    background: #f3f4f6;
    border-radius: 8px;
    padding: 8px 10px;
    font-weight: 700;
    cursor: pointer;
    min-height: 40px
  }

  .yzzr-copy p {
    margin: 0 0 10px;
    line-height: 1.5
  }

  .yzzr-survey-wrap {
    display: none;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 10px
  }

  .yzzr-survey-wrap.active {
    display: block
  }

  .yzzr-pay-choices {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px
  }

  .yzzr-err-title {
    margin: 0 0 6px;
    color: #991b1b;
    font-size: 22px
  }

  @media(min-width:760px) {
    .yzzr-grid {
      grid-template-columns: 1fr 1fr
    }

    .yzzr-inline-actions {
      grid-template-columns: 1fr 1fr
    }
  }

  @media(max-width:759px) {
    .yzzr-modal.open {
      align-items: flex-end;
      padding: 0
    }

    .yzzr-modal-wrap {
      width: 100%;
      max-width: none
    }

    .yzzr-modal-card {
      border-radius: 18px 18px 0 0;
      border-left: 0;
      border-right: 0;
      border-bottom: 0;
      max-height: min(86dvh, calc(100dvh - 24px));
      padding: 16px 14px calc(16px + env(safe-area-inset-bottom))
    }
  }
</style>

<script>
  (function () {
    var root = document.getElementById('yzzr-root');
    if (!root) { return; }

    var DASH = '—';
    var PATHNAME = (location.pathname || '').toLowerCase();
    var IS_EN = /^\/en(\/|$)/.test(PATHNAME);
    function tt(es, en) { return IS_EN ? en : es; }
    var WA_NUMBER = '526691324073';
    var SURVEY_BASE = 'https://link.yatezzitos.com/widget/survey/OXyI9rBvZVpcE877iYso';
    var TRANSFER_URL = location.origin + (function () {
      var p = (location.pathname || '').toLowerCase();
      if (/^\/en(\/|$)/.test(p)) return '/en';
      if (/^\/es(\/|$)/.test(p)) return '/es';
      return '/es';
    })() + '/pagos-con-transferencia/';
    var RECEIPT_BASE = 'https://link.yatezzitos.com/documents/doc-form/6781ebb6ec779a2e3e9dcc33?locale=' + (IS_EN ? 'en' : 'es');
    var state = {
      qt: (new URLSearchParams(location.search).get('qt') || '').trim(),
      data: null,
      countdownId: null,
      balanceAmount: NaN
    };
    var debugMode = (function () {
      var v = (new URLSearchParams(location.search).get('debug') || '').trim().toLowerCase();
      return v === '1' || v === 'true' || v === 'yes';
    })();
    function applyReservationLocale() {
      if (!IS_EN) return;
      var map = {
        'Cliente': 'Customer',
        'Embarcación': 'Yacht',
        'Tipo de experiencia reservada': 'Reserved experience',
        'Destino': 'Destination',
        'Fecha': 'Date',
        'Horario': 'Schedule',
        'Pasajeros': 'Guests',
        'Marina': 'Marina',
        'Ubicación de abordaje': 'Boarding location',
        'Estado de la reserva': 'Reservation status',
        'Reservación ID': 'Reservation ID',
        'Anticipo pagado': 'Deposit paid',
        'Saldo pendiente': 'Remaining balance',
        'Costo total': 'Total cost',
        'Método de pago': 'Payment method'
      };
      var labels = root.querySelectorAll('.yzzr-row .yzzr-k');
      labels.forEach(function (el) {
        var txt = String(el.textContent || '').trim();
        if (map[txt]) el.textContent = map[txt];
      });
      if ($('yzzr-hello')) $('yzzr-hello').textContent = 'Loading your reservation details...';
      if ($('yzzr-load-status')) $('yzzr-load-status').textContent = 'Checking...';
      if ($('yzzr-error-title')) $('yzzr-error-title').textContent = 'Reservation unavailable';
      if ($('yzzr-error-msg')) $('yzzr-error-msg').textContent = 'We could not load the reservation.';
      if ($('yzzr-name')) $('yzzr-name').textContent = DASH;
      if ($('yzzr-meeting-link')) $('yzzr-meeting-link').textContent = DASH;
      if ($('yzzr-res-status')) $('yzzr-res-status').textContent = 'Pending confirmation';
      if ($('yzzr-map-btn')) $('yzzr-map-btn').textContent = '📍 View location';
      if ($('yzzr-receipt-btn')) $('yzzr-receipt-btn').textContent = '🧾 Download deposit receipt';
      if ($('yzzr-balance-btn')) $('yzzr-balance-btn').textContent = '💳 Pay balance';
      if ($('yzzr-wa-btn')) $('yzzr-wa-btn').textContent = '🟢 Contact on WhatsApp';
      var titles = root.querySelectorAll('.yzzr-title');
      titles.forEach(function (t) { t.textContent = 'My reservation'; });
      var countLbls = root.querySelectorAll('.yzzr-time-lbl');
      if (countLbls[0]) countLbls[0].textContent = 'Days';
      if (countLbls[1]) countLbls[1].textContent = 'Hours';
      if (countLbls[2]) countLbls[2].textContent = 'Min';
      if (countLbls[3]) countLbls[3].textContent = 'Sec';
      var secTitle = root.querySelector('.yzzr-sec-title');
      if (secTitle) secTitle.textContent = 'Deposit and payment policy';
      var faqSummaries = root.querySelectorAll('.yzzr-faq > summary');
      faqSummaries.forEach(function (summary) {
        var txt = String(summary.textContent || '').trim();
        if (txt === 'Características y amenidades incluidas') summary.textContent = 'Included features and amenities';
        if (txt === '¿Cómo funciona el anticipo?') summary.textContent = 'How does the deposit work?';
        if (txt === '¿Cuándo se paga el balance restante?') summary.textContent = 'When is the remaining balance due?';
      });
      if ($('yzzr-reco-btn')) $('yzzr-reco-btn').textContent = '🧭 Trip recommendations';
      if ($('yzzr-terms-btn')) $('yzzr-terms-btn').textContent = '📘 Terms and conditions';
      var note = root.querySelector('.yzzr-actions + .yzzr-note');
      if (note) note.textContent = 'If any field is missing, it will be shown as “—”.';

      var receiptHead = root.querySelector('#yzzr-receipt-modal .yzzr-modal-head strong');
      if (receiptHead) receiptHead.textContent = 'Deposit receipt';
      var receiptNote = root.querySelector('#yzzr-receipt-modal .yzzr-note');
      if (receiptNote) receiptNote.textContent = 'If the form does not load in the popup, use the backup link.';
      if ($('yzzr-receipt-fallback')) $('yzzr-receipt-fallback').textContent = 'Open form in a new tab';
      var balanceHead = root.querySelector('#yzzr-balance-modal .yzzr-modal-head strong');
      if (balanceHead) balanceHead.textContent = 'Pay pending balance';
      if ($('yzzr-balance-card-btn')) $('yzzr-balance-card-btn').textContent = 'Pay with credit/debit card';
      if ($('yzzr-balance-transfer-btn')) $('yzzr-balance-transfer-btn').textContent = 'Pay by bank transfer';
      var recoHead = root.querySelector('#yzzr-reco-modal .yzzr-modal-head strong');
      if (recoHead) recoHead.textContent = 'Recommendations for your sea adventure';
      var termsHead = root.querySelector('#yzzr-terms-modal .yzzr-modal-head strong');
      if (termsHead) termsHead.textContent = 'Service terms and conditions';
      var closeBtns = root.querySelectorAll('.yzzr-close');
      closeBtns.forEach(function (btn) { btn.textContent = 'Close'; });
    }
    applyReservationLocale();

    function $(id) { return document.getElementById(id); }
    function mountReservationModalToBody(id) {
      var modal = $(id);
      if (modal && modal.parentNode !== document.body) {
        document.body.appendChild(modal);
      }
    }
    mountReservationModalToBody('yzzr-receipt-modal');
    mountReservationModalToBody('yzzr-balance-modal');
    mountReservationModalToBody('yzzr-reco-modal');
    mountReservationModalToBody('yzzr-terms-modal');
    function nonEmpty(v) { return v !== undefined && v !== null && String(v).trim() !== ''; }
    function normalizeKey(key) { return String(key || '').toLowerCase().replace(/[^a-z0-9]/g, ''); }
    function unique(arr) { return arr.filter(function (v, i) { return arr.indexOf(v) === i; }); }
    function setText(id, value) { var el = $(id); if (!el) return; el.textContent = nonEmpty(value) ? String(value) : DASH; }
    function show(id) { var el = $(id); if (el) { el.classList.remove('yzzr-hidden'); } }
    function hide(id) { var el = $(id); if (el) { el.classList.add('yzzr-hidden'); } }

    function getScopes(raw) {
      return [raw, raw && typeof raw.payload === 'object' ? raw.payload : null, raw && typeof raw.data === 'object' ? raw.data : null];
    }

    function pick(raw, keys) {
      var scopes = getScopes(raw);
      var normalized = (keys || []).map(normalizeKey);
      for (var s = 0; s < scopes.length; s++) {
        var scope = scopes[s];
        if (!scope) { continue; }
        for (var i = 0; i < keys.length; i++) {
          var key = keys[i];
          if (Object.prototype.hasOwnProperty.call(scope, key) && nonEmpty(scope[key])) return scope[key];
        }
      }
      for (var ss = 0; ss < scopes.length; ss++) {
        var looseScope = scopes[ss];
        if (!looseScope) { continue; }
        for (var k in looseScope) {
          if (!Object.prototype.hasOwnProperty.call(looseScope, k)) continue;
          if (!nonEmpty(looseScope[k])) continue;
          if (normalized.indexOf(normalizeKey(k)) > -1) return looseScope[k];
        }
      }
      return '';
    }

    function pickByPattern(obj, regex) {
      if (!obj || typeof obj !== 'object') return '';
      for (var k in obj) {
        if (!Object.prototype.hasOwnProperty.call(obj, k)) continue;
        if (!nonEmpty(obj[k])) continue;
        if (regex.test(String(k))) return obj[k];
      }
      return '';
    }

    function pickAmenitiesSource(raw) {
      var direct = pick(raw, [
        'amenities_raw',
        'amenities_raw_text',
        'caractersticas_y_amenidades_del_yate',
        'caracteristicas_y_amenidades_del_yate',
        'caracteristicas_amenidades_del_yate',
        'amenidades',
        'amenity_list',
        'amenities'
      ]);
      if (nonEmpty(direct)) return direct;
      var fuzzy = pickByPattern(raw, /(amenid|amenit|caracteri|caracters|feature|inclu)/i);
      if (nonEmpty(fuzzy)) return fuzzy;
      if (raw && typeof raw.payload === 'object') {
        var nested = pickByPattern(raw.payload, /(amenid|amenit|caracteri|caracters|feature|inclu)/i);
        if (nonEmpty(nested)) return nested;
      }
      if (raw && typeof raw.data === 'object') {
        var nestedData = pickByPattern(raw.data, /(amenid|amenit|caracteri|caracters|feature|inclu)/i);
        if (nonEmpty(nestedData)) return nestedData;
      }
      return '';
    }

    function normalizeAmenityKey(value) {
      return String(value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[()]/g, '')
        .replace(/\s+/g, ' ')
        .trim();
    }

    var AMENITY_EMOJI_MAP = {
      'agua embotellada': '💧',
      'aire acondicionado': '❄️',
      'alfombra / tapete acuatico': '🌊',
      'cervezas 12': '🍺',
      'cervezas 24': '🍺',
      'ceviches o alimentos de bienvenida': '🍤',
      'chalecos salvavidas': '🛟',
      'chef a bordo': '👨‍🍳',
      'cocina funcional': '🍽️',
      'conexion usb para telefonos': '🔌',
      'dona inflable': '🛟',
      'equipo de pesca deportiva': '🎣',
      'equipo de snorkel': '🤿',
      'equipo de sonido conexion bluetooth': '🔊',
      'luces subacuaticas': '💡',
      'frente acolchonado': '🛥️',
      'fruta fresca de bienvenida': '🍉',
      'gastos de peaje / impuestos de muelle': '🧾',
      'acceso a todas las playas / brazaletes': '🏝️',
      'campamento para playa': '⛱️',
      'guacamole de bienvenida': '🥑',
      'gps': '🧭',
      'guia de turismo': '🧑‍💼',
      'hielera': '🧊',
      'hielo': '🧊',
      'internet': '📶',
      'jetski / moto acuatica': '🏍️',
      'juguetes inflables': '🛟',
      'kayacs dobles': '🛶',
      'kayac individual': '🛶',
      'tabla de paddle board': '🏄',
      'kit de primeros auxilios': '⛑️',
      'lancha auxiliar / dingui': '🚤',
      'margaritas / bebidas durante el viaje': '🍹',
      'capitan y marinero certificados': '👨‍✈️',
      'mesa de comedor': '🍽️',
      'parrilla': '🔥',
      'refrescos': '🥤',
      'refrigerador': '🧊',
      'sala con tv': '📺',
      'seguro de viaje': '🛡️',
      'suite nupcial': '💍',
      'terraza / flybridge': '☀️',
      'toallas': '🧺',
      'tripulantes multilingues': '🗣️'
    };

    function amenityEmoji(item) {
      var normalized = normalizeAmenityKey(item);
      if (AMENITY_EMOJI_MAP[normalized]) return AMENITY_EMOJI_MAP[normalized];
      return '✅';
    }

    function parseAmenities(raw) {
      if (!nonEmpty(raw)) return [];
      if (Array.isArray(raw)) return raw.map(function (x) { return String(x).trim(); }).filter(Boolean);
      var s = String(raw).trim().replace(/&quot;/g, '"').replace(/&#34;/g, '"').replace(/\\"/g, '"');
      for (var i = 0; i < 2; i++) {
        if ((s[0] === '[' && s[s.length - 1] === ']') || (s[0] === '{' && s[s.length - 1] === '}') || (s[0] === '"' && s[s.length - 1] === '"')) {
          try {
            var j = JSON.parse(s);
            if (Array.isArray(j)) return j.map(function (x) { return String(x).trim(); }).filter(Boolean);
            if (j && Array.isArray(j.values)) return j.values.map(function (x) { return String(x).trim(); }).filter(Boolean);
            if (typeof j === 'string') { s = j.trim(); continue; }
          } catch (e) { }
        }
        break;
      }
      if ((s[0] === '"' && s[s.length - 1] === '"') || (s[0] === "'" && s[s.length - 1] === "'")) s = s.slice(1, -1);
      return s.split(/\n|,|;|\|/).map(function (x) { return String(x).replace(/^\s*[-•]\s*/, '').trim(); }).filter(Boolean);
    }

    function renderAmenities(raw) {
      var box = $('yzzr-amenities-list');
      if (!box) return;
      var items = parseAmenities(raw);
      if (!items.length) {
        box.innerHTML = '<p class="yzzr-note">' + tt('No hay amenidades registradas en esta reservación.', 'No amenities were registered for this reservation.') + '</p>';
        return;
      }
      box.innerHTML = items.map(function (it) {
        return '<div class="yzzr-amen-item"><span class="yzzr-amen-emo">' + amenityEmoji(it) + '</span><div class="yzzr-amen-txt">' + it + '</div></div>';
      }).join('');
    }

    function normalizeUrl(value) {
      if (!nonEmpty(value)) return '';
      var txt = String(value).trim();
      if (/^www\./i.test(txt)) txt = 'https://' + txt;
      try { return new URL(txt).toString(); } catch (e) { return ''; }
    }

    function parseMaybeJson(value) {
      if (!nonEmpty(value) || typeof value !== 'string') return null;
      var txt = String(value).trim();
      if (!txt) return null;
      var first = txt.charAt(0);
      if (first !== '{' && first !== '[' && first !== '"') return null;
      try { return JSON.parse(txt); } catch (e) { return null; }
    }

    function extractFirstUrl(value, depth) {
      depth = depth || 0;
      if (depth > 6 || value == null) return '';
      if (typeof value === 'string') {
        var txt = value.trim();
        var direct = normalizeUrl(txt);
        if (direct) return direct;
        var parsed = parseMaybeJson(txt);
        if (parsed != null) return extractFirstUrl(parsed, depth + 1);
        return '';
      }
      if (Array.isArray(value)) {
        for (var i = 0; i < value.length; i++) {
          var hit = extractFirstUrl(value[i], depth + 1);
          if (hit) return hit;
        }
        return '';
      }
      if (typeof value === 'object') {
        for (var k in value) {
          if (!Object.prototype.hasOwnProperty.call(value, k)) continue;
          if (/(url|link|src|download)/i.test(String(k))) {
            var hitDirect = extractFirstUrl(value[k], depth + 1);
            if (hitDirect) return hitDirect;
          }
        }
        for (var k2 in value) {
          if (!Object.prototype.hasOwnProperty.call(value, k2)) continue;
          var hitAny = extractFirstUrl(value[k2], depth + 1);
          if (hitAny) return hitAny;
        }
      }
      return '';
    }

    function cleanDisplayText(value) {
      if (!nonEmpty(value)) return '';
      if (typeof value !== 'string') value = String(value);
      var txt = value.trim();
      if (!txt) return '';
      var parsed = parseMaybeJson(txt);
      if (parsed != null) {
        if (Array.isArray(parsed)) {
          return parsed.length ? cleanDisplayText(parsed[0]) : '';
        }
        if (typeof parsed === 'object') {
          var preferred = ['name', 'label', 'title', 'value', 'text'];
          for (var i = 0; i < preferred.length; i++) {
            var key = preferred[i];
            if (Object.prototype.hasOwnProperty.call(parsed, key)) {
              var got = cleanDisplayText(parsed[key]);
              if (got) return got;
            }
          }
        }
        return '';
      }
      if (/^https?:\/\//i.test(txt)) return '';
      if (txt.length > 180 && /(documentid|mimetype|encoding|uuid|fieldname|meta)/i.test(txt)) return '';
      return txt;
    }

    function parseAmount(value) {
      if (typeof value === 'number') return isFinite(value) ? value : NaN;
      if (!nonEmpty(value)) return NaN;
      var cleaned = String(value).replace(/[^0-9,.\-]/g, '').replace(/\s+/g, '');
      if (cleaned === '' || cleaned === '-' || cleaned === '.' || cleaned === ',') return NaN;
      var hasDot = cleaned.indexOf('.') > -1;
      var hasComma = cleaned.indexOf(',') > -1;
      if (hasDot && hasComma) {
        if (cleaned.lastIndexOf(',') > cleaned.lastIndexOf('.')) cleaned = cleaned.replace(/\./g, '').replace(',', '.');
        else cleaned = cleaned.replace(/,/g, '');
      } else if (hasComma) {
        if (/,\d{1,2}$/.test(cleaned)) cleaned = cleaned.replace(',', '.');
        else cleaned = cleaned.replace(/,/g, '');
      }
      var n = Number(cleaned);
      return isFinite(n) ? n : NaN;
    }

    function mxn(value) {
      return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 0 }).format(value);
    }

    function setLoadStatus(text, cls) {
      var el = $('yzzr-load-status');
      if (!el) return;
      el.textContent = text;
      el.className = 'yzzr-status' + (cls ? (' ' + cls) : '');
    }

    function normalizeStatus(rawStatus) {
      if (!nonEmpty(rawStatus)) return { label: tt('Pendiente de confirmación', 'Pending confirmation'), className: 'is-pending' };
      var label = String(rawStatus).trim();
      var norm = label.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      if (norm === 'open' || norm === 'won' || norm === 'lost' || norm === 'abandoned') {
        return { label: tt('Pendiente de confirmación', 'Pending confirmation'), className: 'is-pending' };
      }
      if (norm.indexOf('cancel') > -1 || norm.indexOf('rechaz') > -1) return { label: label, className: 'is-cancelled' };
      if (norm.indexOf('confirm') > -1 || norm.indexOf('reservad') > -1 || norm.indexOf('pagad') > -1) return { label: label, className: 'is-confirmed' };
      if (norm.indexOf('pend') > -1 || norm.indexOf('proceso') > -1) return { label: label, className: 'is-pending' };
      return { label: label, className: 'is-other' };
    }

    function looksLikePaymentMethod(value) {
      if (!nonEmpty(value)) return false;
      var text = String(value).toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      return /(transfer|tarjet|card|credito|debito|efectivo|cash|spei|pago)/.test(text);
    }

    function looksLikeReservationId(value) {
      if (!nonEmpty(value)) return false;
      var text = String(value).trim();
      if (/^(rsv|res|bk|booking)[-_ ]?\d+/i.test(text)) return true;
      if (/^[A-Za-z]{2,}[-_ ]?\d{2,}$/i.test(text)) return true;
      if (/^\d{4,}$/.test(text)) return true;
      return false;
    }

    function paintReservationStatus(value) {
      var st = normalizeStatus(value);
      var pill = $('yzzr-res-status');
      if (!pill) return;
      pill.textContent = st.label;
      pill.className = 'yzzr-pill ' + st.className;
    }

    function parseDateTime(dateStr, timeStr) {
      if (!nonEmpty(dateStr)) return null;
      var d = String(dateStr).trim();
      var t = nonEmpty(timeStr) ? String(timeStr).trim() : '00:00';
      if (d.indexOf('T') > -1) {
        var asIso = new Date(d);
        if (!isNaN(asIso.getTime())) return asIso;
      }
      var iso = d.match(/^(\d{4})-(\d{1,2})-(\d{1,2})$/);
      var lat = d.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})$/);
      var tm = t.match(/^(\d{1,2})(?::(\d{2}))?\s*(am|pm)?$/i);
      var hh = tm ? Number(tm[1]) : 0;
      var mm = tm ? Number(tm[2] || 0) : 0;
      var ap = tm && tm[3] ? tm[3].toLowerCase() : '';
      if (ap === 'pm' && hh < 12) hh += 12;
      if (ap === 'am' && hh === 12) hh = 0;
      if (iso) return new Date(Number(iso[1]), Number(iso[2]) - 1, Number(iso[3]), hh, mm, 0);
      if (lat) {
        var year = lat[3].length === 2 ? Number('20' + lat[3]) : Number(lat[3]);
        return new Date(year, Number(lat[2]) - 1, Number(lat[1]), hh, mm, 0);
      }
      return null;
    }

    function setCountdownNumbers(d, h, m, s) {
      $('yzzr-d').textContent = String(d).padStart(2, '0');
      $('yzzr-h').textContent = String(h).padStart(2, '0');
      $('yzzr-m').textContent = String(m).padStart(2, '0');
      $('yzzr-s').textContent = String(s).padStart(2, '0');
    }

    function stopCountdown() {
      if (state.countdownId) {
        clearInterval(state.countdownId);
        state.countdownId = null;
      }
    }

    function startCountdown(target) {
      stopCountdown();
      var note = $('yzzr-count-note');
      var tick = function () {
        var diff = target.getTime() - Date.now();
        if (diff <= 0) {
          setCountdownNumbers(0, 0, 0, 0);
          if (note) note.textContent = tt('Tu salida es hoy o ya comenzó.', 'Your departure is today or has already started.');
          setLoadStatus(tt('Reserva lista', 'Reservation ready'), 'is-ok');
          return;
        }
        var days = Math.floor(diff / 86400000); diff -= days * 86400000;
        var hours = Math.floor(diff / 3600000); diff -= hours * 3600000;
        var mins = Math.floor(diff / 60000); diff -= mins * 60000;
        var secs = Math.floor(diff / 1000);
        setCountdownNumbers(days, hours, mins, secs);
        if (note) note.textContent = IS_EN
          ? ('Your trip is in ' + days + ' day(s).')
          : ('Faltan ' + days + ' día(s) para tu viaje.');
      };
      tick();
      state.countdownId = setInterval(tick, 1000);
    }

    function langPrefix() {
      var p = (location.pathname || '').toLowerCase();
      if (/^\/es(\/|$)/.test(p)) return '/es';
      if (/^\/en(\/|$)/.test(p)) return '/en';
      return '';
    }

    // La página «Mi Reserva» consume el endpoint /received (recibo de depósito),
    // NO el endpoint /quote (cotizaciones).
    var apiCandidates = unique([
      location.origin + langPrefix() + '/wp-json/yzz/v1/received',
      location.origin + '/wp-json/yzz/v1/received',
      location.origin + '/es/wp-json/yzz/v1/received',
      location.origin + '/en/wp-json/yzz/v1/received'
    ]);

    async function fetchOne(url) {
      var ctrl = new AbortController();
      var timer = setTimeout(function () { ctrl.abort(); }, 9000);
      try {
        var response = await fetch(url, { method: 'GET', cache: 'no-store', credentials: 'omit', signal: ctrl.signal });
        var body = await response.json().catch(function () { return {}; });
        if (response.ok && body && !body.error) return { ok: true, data: body };
        return { ok: false, error: (body && body.error) ? String(body.error) : ('http_' + response.status) };
      } catch (e) {
        return { ok: false, error: 'network' };
      } finally {
        clearTimeout(timer);
      }
    }

    async function fetchReservation() {
      var notFound = false;
      var lastError = 'network';
      for (var i = 0; i < apiCandidates.length; i++) {
        var qs = 'qt=' + encodeURIComponent(state.qt) + '&_ts=' + Date.now();
        if (debugMode) { qs += '&debug=1'; }
        var result = await fetchOne(apiCandidates[i] + '?' + qs);
        if (result.ok) {
          if (debugMode && result.data && result.data._meta && result.data._meta.ghl_debug) {
            try { console.log('YZZ GHL DEBUG', result.data._meta.ghl_debug); } catch (e) { }
          }
          return result.data;
        }
        lastError = result.error || lastError;
        if (result.error === 'not_found') notFound = true;
        if (result.error === 'missing_qt') throw new Error('missing_qt');
      }
      if (notFound) throw new Error('not_found');
      throw new Error(lastError || 'network');
    }

    function setWa(message) {
      var btn = $('yzzr-wa-btn');
      if (!btn) return;
      btn.href = 'https://api.whatsapp.com/send?phone=' + WA_NUMBER + '&text=' + encodeURIComponent(message || tt('Hola, necesito ayuda con mi reserva.', 'Hi, I need help with my reservation.'));
    }

    function syncReservationBodyModalState() {
      var anyOpen = document.querySelector('.yzzr-modal.open');
      document.body.style.overflow = anyOpen ? 'hidden' : '';
    }

    function openModal(id) {
      mountReservationModalToBody(id);
      var modal = $(id);
      if (!modal) return;
      modal.classList.add('open');
      modal.setAttribute('aria-hidden', 'false');
      syncReservationBodyModalState();
    }

    function closeModal(id) {
      var modal = $(id);
      if (!modal) return;
      modal.classList.remove('open');
      modal.setAttribute('aria-hidden', 'true');
      syncReservationBodyModalState();
    }

    function ensureEmbedScript() {
      if (document.getElementById('yzz-ghl-survey-script')) return;
      var script = document.createElement('script');
      script.id = 'yzz-ghl-survey-script';
      script.src = 'https://link.yatezzitos.com/js/form_embed.js';
      document.body.appendChild(script);
    }

    function firstName(full) {
      if (!nonEmpty(full)) return '';
      return String(full).trim().split(/\s+/)[0] || '';
    }

    function assignAliasesToUrl(url, value, aliases) {
      if (!nonEmpty(value)) return;
      for (var i = 0; i < aliases.length; i++) {
        url.searchParams.set(aliases[i], String(value));
      }
    }

    function buildReceiptUrl() {
      var d = state.data || {};
      var url = new URL(RECEIPT_BASE);
      var fullName = nonEmpty(d.name) ? String(d.name).trim() : [d.first_name, d.last_name].filter(nonEmpty).join(' ');
      var first = nonEmpty(d.first_name) ? String(d.first_name).trim() : firstName(fullName);
      var last = nonEmpty(d.last_name) ? String(d.last_name).trim() : (nonEmpty(fullName) ? String(fullName).split(/\s+/).slice(1).join(' ') : '');
      var email = nonEmpty(d.email) ? String(d.email).trim() : '';
      var phone = nonEmpty(d.phone) ? String(d.phone).trim() : '';

      if (nonEmpty(state.qt)) {
        url.searchParams.set('qt', state.qt);
        url.searchParams.set('token', state.qt);
        url.searchParams.set('quote_token', state.qt);
      }
      if (nonEmpty(d.reservation_id)) {
        url.searchParams.set('reservation_id', d.reservation_id);
        url.searchParams.set('reservacion_id', d.reservation_id);
      }

      assignAliasesToUrl(url, fullName, ['name', 'nombre', 'full_name', 'fullName']);
      assignAliasesToUrl(url, first, ['first_name', 'firstname', 'firstName', 'contact.first_name', 'contact[first_name]']);
      assignAliasesToUrl(url, last, ['last_name', 'lastname', 'lastName', 'contact.last_name', 'contact[last_name]']);
      assignAliasesToUrl(url, email, ['email', 'correo', 'contact_email', 'contact.email', 'contact[email]']);
      assignAliasesToUrl(url, phone, ['phone', 'telefono', 'contact_phone', 'mobile']);
      assignAliasesToUrl(url, d.contact_id, ['contact_id', 'contactId', 'cid']);
      return url.toString();
    }

    function buildBalanceTransferUrl() {
      var url = new URL(TRANSFER_URL);
      url.searchParams.set('qt', state.qt);
      if (isFinite(state.balanceAmount)) {
        url.searchParams.set('a', String(Math.round(state.balanceAmount)));
        url.searchParams.set('amount', String(Math.round(state.balanceAmount)));
      }
      if (state.data && nonEmpty(state.data.reservation_id)) {
        url.searchParams.set('reservation_id', state.data.reservation_id);
      }
      url.searchParams.set('payment_type', 'balance');
      return url.toString();
    }

    function buildBalanceSurveyUrl() {
      var d = state.data || {};
      var url = new URL(SURVEY_BASE);
      var fullName = nonEmpty(d.name) ? String(d.name).trim() : [d.first_name, d.last_name].filter(nonEmpty).join(' ');
      var first = nonEmpty(d.first_name) ? String(d.first_name).trim() : firstName(fullName);
      var last = nonEmpty(d.last_name) ? String(d.last_name).trim() : (nonEmpty(fullName) ? String(fullName).split(/\s+/).slice(1).join(' ') : '');
      var email = nonEmpty(d.email) ? String(d.email).trim() : '';
      var phone = nonEmpty(d.phone) ? String(d.phone).trim() : '';
      var amount = isFinite(state.balanceAmount) ? String(Math.round(state.balanceAmount)) : '';

      url.searchParams.set('qt', state.qt);
      url.searchParams.set('payment_context', 'balance');
      if (nonEmpty(d.contact_id)) url.searchParams.set('contact_id', d.contact_id);
      if (nonEmpty(d.reservation_id)) {
        url.searchParams.set('reservation_id', d.reservation_id);
        url.searchParams.set('reservacion_id', d.reservation_id);
      }

      assignAliasesToUrl(url, fullName, ['name', 'nombre', 'full_name']);
      assignAliasesToUrl(url, first, ['first_name', 'contact.first_name', 'contact[first_name]']);
      assignAliasesToUrl(url, last, ['last_name', 'contact.last_name', 'contact[last_name]']);
      assignAliasesToUrl(url, email, ['email', 'correo', 'contact_email', 'contact.email', 'contact[email]']);
      assignAliasesToUrl(url, phone, ['phone', 'telefono', 'contact_phone']);
      assignAliasesToUrl(url, amount, ['amount', 'balance_due', 'remaining_balance', 'payment_amount']);
      return url.toString();
    }

    function openReceiptModal() {
      var url = buildReceiptUrl();
      $('yzzr-receipt-frame').src = url;
      $('yzzr-receipt-fallback').href = url;
      openModal('yzzr-receipt-modal');
    }

    function resetBalanceModal() {
      var choices = $('yzzr-balance-choices');
      var wrap = $('yzzr-balance-survey-wrap');
      if (choices) choices.style.display = 'grid';
      if (wrap) wrap.classList.remove('active');
      if ($('yzzr-balance-survey-frame')) $('yzzr-balance-survey-frame').src = '';
    }

    function openBalanceModal() {
      resetBalanceModal();
      $('yzzr-balance-amount').textContent = isFinite(state.balanceAmount)
        ? (tt('Saldo pendiente: ', 'Remaining balance: ') + mxn(state.balanceAmount))
        : tt('Selecciona el método para pagar el balance pendiente.', 'Select a payment method for your pending balance.');
      openModal('yzzr-balance-modal');
    }

    function openBalanceCardSurvey() {
      ensureEmbedScript();
      var choices = $('yzzr-balance-choices');
      var wrap = $('yzzr-balance-survey-wrap');
      var frame = $('yzzr-balance-survey-frame');
      if (choices) choices.style.display = 'none';
      if (wrap) wrap.classList.add('active');
      if (frame) frame.src = buildBalanceSurveyUrl();
    }

    function showError(title, message, waMessage) {
      hide('yzzr-content');
      hide('yzzr-img-wrap');
      show('yzzr-error');
      setText('yzzr-error-title', title);
      setText('yzzr-error-msg', message);
      setLoadStatus(tt('Reserva no disponible', 'Reservation unavailable'), 'is-error');
      paintReservationStatus(tt('Pendiente de confirmación', 'Pending confirmation'));
      setWa(waMessage || (IS_EN ? ('Hi, I need help with my reservation. Token: ' + (state.qt || 'no_token')) : ('Hola, necesito ayuda con mi reserva. Token: ' + (state.qt || 'sin_token'))));
    }

    function render(raw) {
      hide('yzzr-error');
      show('yzzr-content');

      var data = {
        name: pick(raw, ['name', 'full_name', 'contact_name']),
        first_name: pick(raw, ['first_name', 'firstName', 'firstname', 'nombre', 'contact_first_name', 'contactfirstname', 'contactfirst_name']),
        last_name: pick(raw, ['last_name', 'lastName', 'lastname', 'apellido', 'apellidos', 'contact_last_name', 'contactlastname', 'contactlast_name']),
        email: pick(raw, ['email', 'correo', 'contact_email', 'contactemail']),
        phone: pick(raw, ['phone', 'telefono', 'contact_phone', 'mobile', 'whatsapp', 'contactphone']),
        contact_id: pick(raw, ['contact_id', 'contactid', 'cid', 'id']),
        yacht: cleanDisplayText(pick(raw, ['y', 'yacht_name', 'yacht', 'embarcacion', 'embarcacion_nombre', 'boat_name'])),
        destination: pick(raw, ['d', 'destinos', 'destino']),
        exp: pick(raw, ['exp', 'experiencia', 'experiencia_reservada', 'tipo_de_experiencia_reservada', 'tipo_experiencia']),
        date: pick(raw, ['f', 'fecha_de_viaje', 'travel_date']),
        hs: pick(raw, ['hs', 'departure_time', 'hora_de_salida']),
        hr: pick(raw, ['hr', 'return_time', 'hora_de_regreso']),
        pax: pick(raw, ['pax', 'number_of_passengers', 'passengers']),
        marina: pick(raw, ['mar', 'marina_name', 'nombre_de_la_marina']),
        meeting_point: pick(raw, ['meeting_point', 'ubicacion_de_abordaje', 'maps', 'google_maps_link']),
        status: pick(raw, ['reservation_status', 'status', 'estado_de_la_reserva', 'estadodelareserva', 'status_booking', 'booking_status', 'status_reserva']),
        deposit_paid: pick(raw, ['deposit_paid', 'deposit_amount', 'deposito_entregado', 'anticipo_pagado', 'anticipo', 'deposito_pagado', 'abono', 'a', 'contact_deposit_amount', 'contactdepositamount']),
        remaining_balance: pick(raw, ['remaining_balance', 'balance_due', 'saldo_pendiente', 'saldo', 'restante', 'saldo_restante', 'adeudo', 'adeudo_pendiente', 'amount_due', 'contact_balance_due', 'contactbalancedue']),
        payment_method: pick(raw, ['payment_method', 'metodo_de_pago', 'metododepago', 'metodo_pago', 'metodopago', 'forma_de_pago', 'forma_pago']),
        reservation_id: pick(raw, ['reservation_id', 'reservacion_id', 'reserva_id', 'id_reserva', 'booking_id', 'id_booking', 'idbooking', 'id_booking_reserva']),
        total: pick(raw, ['total', 'total_cost', 'costo_total', 'precio_total', 'monto_total']),
        img: pick(raw, ['img', 'image', 'imagen_principal_del_yate_upload']),
        amenities_raw: pickAmenitiesSource(raw)
      };

      if (!nonEmpty(data.yacht)) {
        data.yacht = cleanDisplayText(pickByPattern(raw, /(yacht.*name|nombre.*yate|embarcacion|boat.*name)/i));
      }

      if (nonEmpty(data.reservation_id) && !nonEmpty(data.payment_method) && looksLikePaymentMethod(data.reservation_id)) {
        data.payment_method = data.reservation_id;
        data.reservation_id = '';
      }
      if (nonEmpty(data.payment_method) && !nonEmpty(data.reservation_id) && looksLikeReservationId(data.payment_method) && !looksLikePaymentMethod(data.payment_method)) {
        data.reservation_id = data.payment_method;
        data.payment_method = '';
      }
      if (nonEmpty(data.payment_method) && nonEmpty(data.reservation_id)) {
        var payLooksMethod = looksLikePaymentMethod(data.payment_method);
        var payLooksId = looksLikeReservationId(data.payment_method);
        var resLooksMethod = looksLikePaymentMethod(data.reservation_id);
        var resLooksId = looksLikeReservationId(data.reservation_id);
        if (!payLooksMethod && payLooksId && resLooksMethod && !resLooksId) {
          var tmpValue = data.payment_method;
          data.payment_method = data.reservation_id;
          data.reservation_id = tmpValue;
        }
      }

      var dep = parseAmount(data.deposit_paid);
      var bal = parseAmount(data.remaining_balance);
      var total = parseAmount(data.total);
      if (!isFinite(dep) && isFinite(total) && isFinite(bal)) dep = total - bal;
      if (!isFinite(bal) && isFinite(total) && isFinite(dep)) bal = total - dep;
      if (isFinite(dep) && isFinite(bal)) {
        var expectedTotal = dep + bal;
        if (!isFinite(total) || Math.abs(total - expectedTotal) > 1) {
          total = expectedTotal;
        }
      }

      if (isFinite(dep)) data.deposit_paid = String(Math.round(dep));
      if (isFinite(bal)) data.remaining_balance = String(Math.round(bal));
      if (isFinite(total)) data.total = String(Math.round(total));
      state.balanceAmount = isFinite(bal) ? Math.max(0, bal) : NaN;
      state.data = data;

      $('yzzr-hello').textContent = nonEmpty(data.name)
        ? (IS_EN ? ('Hi ' + data.name + ', your reservation is ready.') : ('Hola ' + data.name + ', tu reserva está lista.'))
        : tt('Aquí está la información de tu reserva.', 'Here is your reservation information.');
      setText('yzzr-name', data.name);
      setText('yzzr-yacht', data.yacht);
      setText('yzzr-exp', data.exp);
      setText('yzzr-dest', data.destination);
      setText('yzzr-date', data.date);
      setText('yzzr-time', [data.hs, data.hr].filter(nonEmpty).join(' - '));
      setText('yzzr-pax', data.pax);
      setText('yzzr-marina', data.marina);
      setText('yzzr-res-id', data.reservation_id);
      setText('yzzr-payment', data.payment_method);
      setText('yzzr-deposit', isFinite(dep) ? mxn(dep) : data.deposit_paid);
      setText('yzzr-balance', isFinite(bal) ? mxn(bal) : data.remaining_balance);
      setText('yzzr-total', isFinite(total) ? mxn(total) : data.total);
      paintReservationStatus(data.status);
      setLoadStatus(tt('Reserva cargada', 'Reservation loaded'), 'is-ok');
      renderAmenities(data.amenities_raw);

      var mapUrl = normalizeUrl(data.meeting_point);
      if (mapUrl) {
        $('yzzr-meeting-link').href = mapUrl;
        $('yzzr-meeting-link').classList.remove('yzzr-disabled');
        $('yzzr-meeting-link').textContent = tt('Ver ubicación', 'View location');
        $('yzzr-map-btn').href = mapUrl;
        show('yzzr-map-btn');
      } else {
        $('yzzr-meeting-link').removeAttribute('href');
        $('yzzr-meeting-link').classList.add('yzzr-disabled');
        $('yzzr-meeting-link').textContent = DASH;
        hide('yzzr-map-btn');
      }

      var normalizedImg = extractFirstUrl(data.img);
      if (!normalizedImg) {
        normalizedImg = extractFirstUrl(pick(raw, ['img', 'image', 'imagen_principal_del_yate_upload']));
      }
      if (nonEmpty(normalizedImg)) {
        $('yzzr-img').src = normalizedImg;
        show('yzzr-img-wrap');
      } else {
        hide('yzzr-img-wrap');
      }

      if (isFinite(state.balanceAmount) && state.balanceAmount > 0) {
        show('yzzr-balance-btn');
        $('yzzr-balance-btn').textContent = (IS_EN ? '💳 Pay balance ' : '💳 Pagar balance ') + mxn(state.balanceAmount);
      } else {
        hide('yzzr-balance-btn');
      }

      var dt = parseDateTime(data.date, data.hs);
      if (dt && !isNaN(dt.getTime())) startCountdown(dt);
      else {
        stopCountdown();
        setCountdownNumbers(0, 0, 0, 0);
        $('yzzr-count-note').textContent = tt('No se pudo calcular el contador por formato de fecha/hora.', 'Could not calculate countdown due to date/time format.');
        setLoadStatus(tt('Reserva cargada con advertencias', 'Reservation loaded with warnings'), 'is-warn');
      }

      var waMsg = (IS_EN ? 'Hi, I need help with my reservation.' : 'Hola, necesito ayuda con mi reserva.') +
        (IS_EN ? '\nToken: ' : '\nToken: ') + state.qt +
        (nonEmpty(data.reservation_id) ? (IS_EN ? ('\nReservation: ' + data.reservation_id) : ('\nReserva: ' + data.reservation_id)) : '') +
        (nonEmpty(data.status) ? (IS_EN ? ('\nStatus: ' + data.status) : ('\nEstado: ' + data.status)) : '') +
        (nonEmpty(data.date) ? (IS_EN ? ('\nDate: ' + data.date + (nonEmpty(data.hs) ? (' ' + data.hs) : '')) : ('\nFecha: ' + data.date + (nonEmpty(data.hs) ? (' ' + data.hs) : ''))) : '');
      setWa(waMsg);
    }

    document.addEventListener('click', function (e) {
      var closeTarget = e.target && e.target.getAttribute ? e.target.getAttribute('data-yzzr-close') : null;
      if (closeTarget) {
        if (closeTarget === 'yzzr-balance-modal') resetBalanceModal();
        closeModal(closeTarget);
        return;
      }
      if (e.target.closest('#yzzr-receipt-btn')) { e.preventDefault(); openReceiptModal(); return; }
      if (e.target.closest('#yzzr-balance-btn')) { e.preventDefault(); openBalanceModal(); return; }
      if (e.target.closest('#yzzr-balance-card-btn')) { e.preventDefault(); openBalanceCardSurvey(); return; }
      if (e.target.closest('#yzzr-balance-transfer-btn')) { e.preventDefault(); window.location.href = buildBalanceTransferUrl(); return; }
      if (e.target.closest('#yzzr-reco-btn')) { e.preventDefault(); openModal('yzzr-reco-modal'); return; }
      if (e.target.closest('#yzzr-terms-btn')) { e.preventDefault(); openModal('yzzr-terms-modal'); return; }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeModal('yzzr-receipt-modal');
        closeModal('yzzr-balance-modal');
        closeModal('yzzr-reco-modal');
        closeModal('yzzr-terms-modal');
        resetBalanceModal();
      }
    });

    if (!nonEmpty(state.qt)) {
      showError(
        tt('Token inválido o ausente', 'Invalid or missing token'),
        tt('Este enlace no contiene el token de reserva (qt). Solicita un enlace nuevo a tu asesor.', 'This link does not contain the reservation token (qt). Ask your advisor for a new link.'),
        tt('Hola, mi enlace de reserva no trae token (qt). ¿Me apoyas con uno nuevo?', 'Hi, my reservation link does not include a token (qt). Can you send me a new one?')
      );
      return;
    }

    setWa((IS_EN ? 'Hi, I need help with my reservation. Token: ' : 'Hola, necesito ayuda con mi reserva. Token: ') + state.qt);
    setLoadStatus(tt('Consultando reserva...', 'Checking reservation...'), '');
    fetchReservation().then(function (raw) {
      render(raw);
    }).catch(function (err) {
      var reason = err && err.message ? String(err.message) : 'network';
      if (reason === 'not_found') {
        showError(
          tt('Reserva no encontrada', 'Reservation not found'),
          tt('No encontramos una reserva asociada a este enlace. Solicita un enlace actualizado a tu asesor.', 'No reservation was found for this link. Ask your advisor for an updated link.'),
          (IS_EN ? 'Hi, I could not open my reservation. Token: ' : 'Hola, no pude abrir mi reserva. Token: ') + state.qt
        );
        return;
      }
      if (reason === 'missing_qt') {
        showError(
          tt('Token inválido', 'Invalid token'),
          tt('El endpoint respondió que falta el token. Verifica que el enlace tenga ?qt=...', 'The endpoint indicates the token is missing. Verify the link includes ?qt=...'),
          (IS_EN ? 'Hi, my reservation link seems invalid. Token: ' : 'Hola, mi enlace de reserva parece inválido. Token: ') + state.qt
        );
        return;
      }
      showError(
        tt('No pudimos cargar tu reserva', 'We could not load your reservation'),
        tt('Tuvimos un problema de conexión o de lectura. Intenta de nuevo en unos minutos.', 'We had a connection or data-read issue. Please try again in a few minutes.'),
        (IS_EN ? 'Hi, I could not load my reservation due to a connection error. Token: ' : 'Hola, no pude cargar mi reserva por error de conexión. Token: ') + state.qt
      );
    });
  })();
</script>
<?php return ob_get_clean();
}

// ── Página de Gracias (Thank You) ────────────────────────────────────────────
function yzzq_thankyou_shortcode()
{
  ob_start();
?>
<div id="yzzt-root" class="yzzt">

  <!-- Hero -->
  <div class="yzzt-card yzzt-hero">
    <h1 class="yzzt-title">¡Gracias por tu reserva!</h1>
    <p class="yzzt-sub">Tu pago fue recibido y tu fecha está asegurada.<br>En breve tu asesor se comunicará contigo.</p>
    <div style="height:10px"></div>
    <span class="yzzt-status is-ok">✓ Reserva confirmada</span>
  </div>

  <!-- Banner -->
  <div class="yzzt-banner">
    <div class="yzzt-banner-icon">🎉</div>
    <div>
      <div class="yzzt-banner-title">Pago recibido con éxito</div>
      <div class="yzzt-banner-sub">Nuestro equipo está procesando tu reservación. Revisa tu correo para el recibo de depósito con todos los detalles.</div>
    </div>
  </div>

  <!-- Próximos pasos -->
  <div class="yzzt-card">
    <p style="margin:0 0 12px;font-size:17px;font-weight:800;color:#0b1939;">¿Qué sigue ahora?</p>
    <div class="yzzt-step"><div class="yzzt-step-num">1</div><div><strong>Recibo por correo</strong><br><span style="color:#6b7280;font-size:14px;">Recibirás un correo con el resumen y recibo de depósito de tu reserva.</span></div></div>
    <div class="yzzt-step"><div class="yzzt-step-num">2</div><div><strong>Tu asesor te contacta</strong><br><span style="color:#6b7280;font-size:14px;">En las próximas horas un asesor Yatezzitos te contactará por WhatsApp para confirmar los detalles.</span></div></div>
    <div class="yzzt-step"><div class="yzzt-step-num">3</div><div><strong>Llega a tu aventura</strong><br><span style="color:#6b7280;font-size:14px;">Preséntate en la marina 15 minutos antes de tu horario de salida con identificación oficial.</span></div></div>
    <div class="yzzt-step" style="border-bottom:0;padding-bottom:0;"><div class="yzzt-step-num">4</div><div><strong>Liquida el saldo al abordar</strong><br><span style="color:#6b7280;font-size:14px;">El 50% restante se paga el día del evento, antes de abordar. Efectivo, tarjeta o transferencia.</span></div></div>
  </div>

  <!-- Qué llevar -->
  <div class="yzzt-card">
    <p style="margin:0 0 12px;font-size:17px;font-weight:800;color:#0b1939;">¿Qué llevar el día de tu experiencia?</p>
    <div class="yzzt-checklist">
      <div class="yzzt-check-item">🪪 Identificación oficial</div>
      <div class="yzzt-check-item">👙 Traje de baño y cambio de ropa</div>
      <div class="yzzt-check-item">🧴 Bloqueador solar biodegradable</div>
      <div class="yzzt-check-item">🕶️ Lentes de sol</div>
      <div class="yzzt-check-item">💵 Efectivo para extras opcionales</div>
      <div class="yzzt-check-item">📵 Protege tus electrónicos del agua y el sol</div>
      <div class="yzzt-check-item">😊 ¡Actitud positiva y ganas de disfrutar!</div>
    </div>
  </div>

  <!-- FAQ pagos -->
  <div class="yzzt-card">
    <div class="yzzt-policy-title">Política de anticipo y pago</div>
    <details class="yzzt-faq" style="margin-bottom:8px">
      <summary>► ¿Cómo funciona el anticipo?</summary>
      <div class="yzzt-faq-body">El anticipo del 50% del costo total confirma y reserva la fecha de tu experiencia. Es necesario para asegurar la embarcación y el equipo de capitanes para tu día de aventura.</div>
    </details>
    <details class="yzzt-faq">
      <summary>► ¿Cuándo se paga el balance restante?</summary>
      <div class="yzzt-faq-body">El 50% restante se liquida el día del evento, antes de abordar la embarcación. Puedes pagar en efectivo, tarjeta de débito/crédito o transferencia bancaria.</div>
    </details>
  </div>

  <!-- Contacto y modales -->
  <div class="yzzt-modal-btns">
    <a href="https://wa.me/526691324073" target="_blank" rel="noopener" class="yzzt-btn yzzt-btn-dark">🟢 Contactar a tu asesor</a>
    <button id="yzzt-reco-btn" class="yzzt-btn yzzt-btn-blue">🧭 Recomendaciones del viaje</button>
    <button id="yzzt-terms-btn" class="yzzt-btn yzzt-btn-dark" style="background:#4b5563">📋 Términos y condiciones</button>
  </div>

</div>

<!-- Modal: Recomendaciones -->
<div id="yzzt-reco-modal" class="yzzt-modal-overlay yzzt-hidden" role="dialog" aria-modal="true">
  <div class="yzzt-modal-box">
    <button class="yzzt-modal-close" data-yzzt-close="yzzt-reco-modal">✕</button>
    <h2 class="yzzt-modal-title">🧭 Recomendaciones del viaje</h2>
    <ul class="yzzt-modal-list">
      <li>🕐 Llega <strong>15 minutos antes</strong> del horario de salida al punto de abordaje.</li>
      <li>🧴 Trae <strong>protector solar</strong> (preferentemente biodegradable).</li>
      <li>👙 Usa ropa cómoda y lleva un <strong>cambio de ropa</strong>.</li>
      <li>🩴 Calzado que pueda mojarse o sandalias antiderrapantes.</li>
      <li>💧 Mantente hidratado. Lleva agua adicional si lo deseas.</li>
      <li>🎶 Si tienes música favorita, ¡no olvides tu playlist!</li>
      <li>📵 Cuida tus pertenencias electrónicas del agua y el sol.</li>
      <li>😊 Trae mucha energía y <strong>actitud positiva</strong>. ¡Esta será una experiencia increíble!</li>
    </ul>
  </div>
</div>

<!-- Modal: Términos y condiciones -->
<div id="yzzt-terms-modal" class="yzzt-modal-overlay yzzt-hidden" role="dialog" aria-modal="true">
  <div class="yzzt-modal-box">
    <button class="yzzt-modal-close" data-yzzt-close="yzzt-terms-modal">✕</button>
    <h2 class="yzzt-modal-title">📋 Términos y condiciones</h2>
    <div class="yzzt-modal-body">
      <p><strong>Política de anticipo:</strong> El anticipo del 50% es no reembolsable una vez confirmada la reserva. En caso de cancelación con más de 72 horas de anticipación, se puede reagendar sin costo adicional.</p>
      <p><strong>Cancelación:</strong> Cancelaciones con menos de 72 horas del evento no generan reembolso ni reagendamiento.</p>
      <p><strong>Clima:</strong> En caso de mal tiempo severo que impida la navegación segura, Yatezzitos reagendará la experiencia sin costo adicional.</p>
      <p><strong>Capacidad:</strong> El número de pasajeros no puede exceder el máximo establecido por capitanía y las autoridades marítimas.</p>
      <p><strong>Comportamiento:</strong> El capitán tiene autoridad para regresar al puerto si el comportamiento de los pasajeros representa un riesgo para la tripulación o los demás.</p>
      <p><strong>Menores de edad:</strong> Deben ir acompañados de un adulto responsable en todo momento.</p>
    </div>
  </div>
</div>

<style>
  #yzzt-root { max-width:700px; margin:0 auto; padding:16px; color:#111827; font-family:var(--yzz-font-family,Arial,sans-serif); box-sizing:border-box }
  #yzzt-root * { box-sizing:border-box }
  .yzzt-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:20px; margin-bottom:12px }
  .yzzt-hero { text-align:center }
  .yzzt-title { margin:0 0 8px; font-size:28px; line-height:1.15 }
  .yzzt-sub { margin:0; color:#6b7280; font-size:15px; line-height:1.5 }
  .yzzt-status { display:inline-flex; align-items:center; border-radius:999px; background:#e5e7eb; padding:6px 14px; font-size:13px; font-weight:700 }
  .yzzt-status.is-ok { background:#d1fae5; color:#065f46 }
  .yzzt-banner { display:flex; align-items:center; gap:14px; background:linear-gradient(135deg,#065f46,#00945e); color:#fff; border-radius:14px; padding:18px 20px; margin-bottom:12px }
  .yzzt-banner-icon { font-size:38px; flex-shrink:0 }
  .yzzt-banner-title { font-size:18px; font-weight:800; margin-bottom:4px }
  .yzzt-banner-sub { font-size:14px; opacity:.9; line-height:1.45 }
  .yzzt-step { display:flex; gap:14px; align-items:flex-start; padding:12px 0; border-bottom:1px dashed #e5e7eb }
  .yzzt-step-num { flex-shrink:0; width:28px; height:28px; border-radius:50%; background:#194395; color:#fff; font-weight:800; font-size:14px; display:flex; align-items:center; justify-content:center; margin-top:2px }
  .yzzt-checklist { display:grid; grid-template-columns:1fr 1fr; gap:8px }
  @media(max-width:540px){ .yzzt-checklist { grid-template-columns:1fr } }
  .yzzt-check-item { background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; font-size:14px; line-height:1.35 }
  .yzzt-policy-title { font-style:italic; font-weight:800; font-size:15px; margin-bottom:10px; color:#1f2937 }
  .yzzt-faq { border:1px solid #e5e7eb; border-radius:10px; padding:10px 14px; background:#fcfcfd }
  .yzzt-faq summary { cursor:pointer; font-weight:700; user-select:none }
  .yzzt-faq-body { margin-top:10px; font-size:14px; line-height:1.6; color:#374151 }
  .yzzt-modal-btns { display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:16px }
  @media(max-width:540px){ .yzzt-modal-btns { grid-template-columns:1fr } }
  .yzzt-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; width:100%; border:0; border-radius:10px; padding:12px 14px; font-weight:700; font-size:14px; text-decoration:none; color:#fff; cursor:pointer; min-height:44px }
  .yzzt-btn-dark { background:#111827 }
  .yzzt-btn-blue { background:#1d4ed8 }
  .yzzt-btn:hover,.yzzt-btn:focus { color:#fff !important; text-decoration:none !important; filter:brightness(1.1) }
  .yzzt-hidden { display:none !important }
  .yzzt-modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999; display:flex; align-items:center; justify-content:center; padding:16px }
  .yzzt-modal-box { background:#fff; border-radius:18px; padding:28px 24px; max-width:560px; width:100%; max-height:85vh; overflow-y:auto; position:relative; box-shadow:0 20px 60px rgba(0,0,0,.25) }
  .yzzt-modal-close { position:absolute; top:14px; right:16px; background:none; border:none; font-size:20px; cursor:pointer; color:#6b7280; line-height:1 }
  .yzzt-modal-close:hover { color:#111 }
  .yzzt-modal-title { margin:0 0 16px; font-size:22px; line-height:1.2; color:#111827 }
  .yzzt-modal-list { padding-left:20px; margin:0 }
  .yzzt-modal-list li { margin-bottom:10px; font-size:14px; line-height:1.5; color:#374151 }
  .yzzt-modal-body p { margin:0 0 12px; font-size:14px; line-height:1.6; color:#374151 }
</style>

<script>
  (function () {
    function $i(id) { return document.getElementById(id); }
    function openModal(id) { var el = $i(id); if (el) { el.classList.remove('yzzt-hidden'); document.body.style.overflow = 'hidden'; } }
    function closeModal(id) { var el = $i(id); if (el) { el.classList.add('yzzt-hidden'); document.body.style.overflow = ''; } }
    document.addEventListener('click', function (e) {
      if (e.target.closest && e.target.closest('#yzzt-reco-btn'))   { e.preventDefault(); openModal('yzzt-reco-modal');  return; }
      if (e.target.closest && e.target.closest('#yzzt-terms-btn'))  { e.preventDefault(); openModal('yzzt-terms-modal'); return; }
      var closeId = e.target.getAttribute ? e.target.getAttribute('data-yzzt-close') : null;
      if (closeId) { closeModal(closeId); return; }
      if (e.target.classList && e.target.classList.contains('yzzt-modal-overlay')) { e.target.classList.add('yzzt-hidden'); document.body.style.overflow = ''; }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { closeModal('yzzt-reco-modal'); closeModal('yzzt-terms-modal'); }
    });
  })();
</script>
<?php
  return ob_get_clean();
}

