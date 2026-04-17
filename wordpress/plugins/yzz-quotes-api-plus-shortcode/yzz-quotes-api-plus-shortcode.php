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

// ── GHL Live Proxy Endpoint ──────────────────────────────────────────────────
require_once plugin_dir_path(__FILE__) . 'assets/ghl-live.php';

// ── Modal compartido: Cargos Adicionales ─────────────────────────────────────
// (El modal ahora se incluye directamente como HTML plano en los archivos mi-cotizacion.html y mi-reserva.html)

// ── Shortcodes ───────────────────────────────────────────────────────────────
add_shortcode('yzz_quote_page', 'yzzq_shortcode');
add_shortcode('YZZ_QUOTE_PAGE', 'yzzq_shortcode');
add_shortcode('yzz_reservation_page', 'yzzq_reservation_shortcode');
add_shortcode('YZZ_RESERVATION_PAGE', 'yzzq_reservation_shortcode');
add_shortcode('yzz_thankyou_page', 'yzzq_thankyou_shortcode');
add_shortcode('YZZ_THANKYOU_PAGE', 'yzzq_thankyou_shortcode');

// HTML/CSS/JS se carga desde assets/mi-cotizacion.html
function yzzq_shortcode()
{
  ob_start();
  include plugin_dir_path(__FILE__) . 'assets/mi-cotizacion.html';
  return ob_get_clean();
}

// HTML/CSS/JS se carga desde assets/mi-reserva.html
function yzzq_reservation_shortcode()
{
  ob_start();
  include plugin_dir_path(__FILE__) . 'assets/mi-reserva.html';
  return ob_get_clean();
}

// HTML/CSS/JS se carga desde assets/gracias.html
function yzzq_thankyou_shortcode()
{
  ob_start();
  include plugin_dir_path(__FILE__) . 'assets/gracias.html';
  return ob_get_clean();
}
