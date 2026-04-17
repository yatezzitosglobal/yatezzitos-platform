<?php
/**
 * GHL Live Integration — Proxy seguro para datos en tiempo real.
 *
 * Este archivo registra el endpoint /wp-json/yzz/v1/contact-live
 * que consulta la API de GoHighLevel directamente para obtener
 * datos frescos del contacto sin depender del webhook.
 *
 * Las credenciales se leen de constantes definidas en wp-config.php:
 *   define('YZZ_GHL_API_KEY_CONST',     '...');
 *   define('YZZ_GHL_LOCATION_ID_CONST', '...');
 *
 * IMPORTANTE: Este archivo se incluye desde el plugin principal.
 * No se ejecuta de forma independiente.
 */

if (!defined('ABSPATH')) {
  exit;
}

// ── Constantes GHL ──────────────────────────────────────────────────────────
// Se leen desde wp-config.php (nunca hardcodeadas aquí).
function yzzq_ghl_api_key(): string
{
  if (defined('YZZ_GHL_API_KEY_CONST') && YZZ_GHL_API_KEY_CONST !== '') {
    return YZZ_GHL_API_KEY_CONST;
  }
  return '';
}

function yzzq_ghl_location_id(): string
{
  if (defined('YZZ_GHL_LOCATION_ID_CONST') && YZZ_GHL_LOCATION_ID_CONST !== '') {
    return YZZ_GHL_LOCATION_ID_CONST;
  }
  return '';
}

// ── Registrar endpoint ──────────────────────────────────────────────────────
add_action('rest_api_init', function () {
  register_rest_route('yzz/v1', '/contact-live', array(
    'methods'             => WP_REST_Server::READABLE,
    'callback'            => 'yzzq_get_contact_live',
    'permission_callback' => '__return_true',
  ));
});

/**
 * GET /wp-json/yzz/v1/contact-live?qt=TOKEN
 *
 * 1. Valida el token qt contra la BD local.
 * 2. Obtiene el contact_id asociado.
 * 3. Consulta GHL API: GET /contacts/{contact_id}
 * 4. Mapea los custom fields al formato YZZ existente.
 * 5. Actualiza la BD local con los datos frescos.
 * 6. Devuelve JSON al frontend.
 *
 * Si GHL no responde o falla, devuelve los datos de BD como fallback.
 */
function yzzq_get_contact_live(WP_REST_Request $request)
{
  global $wpdb;
  yzzq_nocache();

  $qt = sanitize_text_field((string)($request->get_param('qt') ?: $request->get_param('token')));
  if ($qt === '') {
    return new WP_REST_Response(array('error' => 'missing_qt'), 400);
  }

  // 1. Buscar token en BD
  $row = $wpdb->get_row($wpdb->prepare(
    "SELECT contact_id, payload, created_at FROM " . yzzq_table() . " WHERE token=%s LIMIT 1",
    $qt
  ));

  if (!$row) {
    return new WP_REST_Response(array('error' => 'not_found'), 404);
  }

  $contact_id = trim((string) $row->contact_id);

  // Sin contact_id → no podemos consultar GHL, devolvemos datos de BD.
  if ($contact_id === '') {
    $fallback = json_decode($row->payload, true);
    if (!is_array($fallback)) {
      $fallback = array();
    }
    $fallback = yzzq_enrich_payload($fallback);
    $fallback['_meta'] = array(
      'source'      => 'db_no_contact_id',
      'quote_token' => $qt,
      'created_at'  => $row->created_at,
    );
    return new WP_REST_Response($fallback, 200);
  }

  // 2. Verificar que tengamos API key configurada
  $api_key = yzzq_ghl_api_key();
  if ($api_key === '') {
    // Sin API key → fallback a BD
    $fallback = json_decode($row->payload, true);
    if (!is_array($fallback)) {
      $fallback = array();
    }
    $fallback = yzzq_enrich_payload($fallback);
    $fallback['_meta'] = array(
      'source'      => 'db_no_api_key',
      'quote_token' => $qt,
      'created_at'  => $row->created_at,
    );
    return new WP_REST_Response($fallback, 200);
  }

  // 3. Consultar GHL API
  $ghl_contact = yzzq_fetch_ghl_contact($contact_id, $api_key);

  if (is_wp_error($ghl_contact)) {
    // GHL falló → fallback graceful a BD
    $fallback = json_decode($row->payload, true);
    if (!is_array($fallback)) {
      $fallback = array();
    }
    $fallback = yzzq_enrich_payload($fallback);
    $fallback['_meta'] = array(
      'source'      => 'db_ghl_error',
      'ghl_error'   => $ghl_contact->get_error_message(),
      'quote_token' => $qt,
      'created_at'  => $row->created_at,
    );
    return new WP_REST_Response($fallback, 200);
  }

  // 4. Mapear campos GHL → formato YZZ
  $mapped = yzzq_map_ghl_to_yzz($ghl_contact, $api_key);
  $mapped['contact_id'] = $contact_id;
  $mapped = yzzq_enrich_payload($mapped);

  // 5. Merge: datos GHL frescos SOBRE los de BD (para no perder campos que solo vienen del webhook)
  $existing = json_decode($row->payload, true);
  if (!is_array($existing)) {
    $existing = array();
  }
  foreach ($mapped as $k => $v) {
    if ($v !== '' && $v !== null) {
      $existing[$k] = $v;
    }
  }

  // 6. Actualizar BD con el merge
  $wpdb->update(
    yzzq_table(),
    array('payload' => wp_json_encode($existing, JSON_UNESCAPED_UNICODE)),
    array('token' => $qt),
    array('%s'),
    array('%s')
  );

  $existing['_meta'] = array(
    'source'      => 'ghl_live',
    'quote_token' => $qt,
    'fetched_at'  => current_time('mysql'),
  );

  return new WP_REST_Response($existing, 200);
}

/**
 * Consulta la API de GHL para obtener datos de un contacto.
 *
 * @param string $contact_id ID del contacto en GHL.
 * @param string $api_key    Private Integration Token.
 * @return array|WP_Error    Datos del contacto o error.
 */
function yzzq_fetch_ghl_contact(string $contact_id, string $api_key)
{
  $url = 'https://services.leadconnectorhq.com/contacts/' . urlencode($contact_id);

  $response = wp_remote_get($url, array(
    'headers' => array(
      'Authorization' => 'Bearer ' . $api_key,
      'Version'       => '2021-07-28',
      'Accept'        => 'application/json',
    ),
    'timeout' => 10,
  ));

  if (is_wp_error($response)) {
    return $response;
  }

  $code = wp_remote_retrieve_response_code($response);
  if ($code !== 200) {
    return new WP_Error(
      'ghl_http_error',
      'GHL API returned HTTP ' . $code
    );
  }

  $body = json_decode(wp_remote_retrieve_body($response), true);
  if (!is_array($body)) {
    return new WP_Error('ghl_parse_error', 'Could not parse GHL response');
  }

  // GHL envuelve en "contact" a veces
  return isset($body['contact']) ? $body['contact'] : $body;
}

/**
 * Obtiene y cachea el mapa de custom fields de GHL.
 * Devuelve: [ 'field_id' => 'fieldKey_sin_prefijo', ... ]
 *
 * @param string $api_key Private Integration Token.
 * @return array
 */
function yzzq_get_ghl_custom_field_map(string $api_key): array
{
  $cached = get_transient('yzz_ghl_cf_map');
  if (is_array($cached) && !empty($cached)) {
    return $cached;
  }

  $location_id = yzzq_ghl_location_id();
  if ($location_id === '' || $api_key === '') {
    return array();
  }

  $url = 'https://services.leadconnectorhq.com/locations/' . urlencode($location_id) . '/customFields';

  $response = wp_remote_get($url, array(
    'headers' => array(
      'Authorization' => 'Bearer ' . $api_key,
      'Version'       => '2021-07-28',
      'Accept'        => 'application/json',
    ),
    'timeout' => 10,
  ));

  if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
    return array();
  }

  $body   = json_decode(wp_remote_retrieve_body($response), true);
  $fields = (isset($body['customFields']) && is_array($body['customFields'])) ? $body['customFields'] : array();
  $map    = array();

  foreach ($fields as $f) {
    if (!isset($f['id'])) {
      continue;
    }
    // fieldKey viene como "contact.yacht_name" → extraemos "yacht_name"
    $key = isset($f['fieldKey']) ? $f['fieldKey'] : (isset($f['name']) ? sanitize_key($f['name']) : $f['id']);
    $key = preg_replace('/^contact\\./', '', $key);
    $map[$f['id']] = $key;
  }

  // Cache por 1 hora (los custom fields no cambian frecuentemente)
  set_transient('yzz_ghl_cf_map', $map, HOUR_IN_SECONDS);

  return $map;
}

/**
 * Traduce los datos crudos de GHL Contact al formato plano que
 * el JS del plugin ya sabe consumir.
 *
 * IMPORTANTE: Solo mapea los campos que el plugin ya usa.
 * No inventa campos nuevos ni modifica la estructura existente.
 *
 * @param array  $ghl     Datos del contacto de GHL.
 * @param string $api_key Para obtener el mapa de custom fields.
 * @return array           Payload en formato YZZ.
 */
function yzzq_map_ghl_to_yzz(array $ghl, string $api_key): array
{
  // Campos estándar de GHL (no custom fields)
  $first = isset($ghl['firstName']) ? trim((string)$ghl['firstName']) : '';
  $last  = isset($ghl['lastName'])  ? trim((string)$ghl['lastName'])  : '';
  $full  = trim($first . ' ' . $last);

  $out = array(
    'name'         => $full,
    'full_name'    => $full,
    'first_name'   => $first,
    'last_name'    => $last,
    'email'        => isset($ghl['email']) ? trim((string)$ghl['email']) : '',
    'phone'        => isset($ghl['phone']) ? trim((string)$ghl['phone']) : '',
    'contact_id'   => isset($ghl['id'])    ? trim((string)$ghl['id'])    : '',
  );

  // Mapear custom fields
  $cf_map = yzzq_get_ghl_custom_field_map($api_key);
  $custom_fields = (isset($ghl['customFields']) && is_array($ghl['customFields'])) ? $ghl['customFields'] : array();

  foreach ($custom_fields as $cf) {
    $field_id = isset($cf['id']) ? $cf['id'] : '';
    if ($field_id === '') {
      continue;
    }

    // Obtener el valor
    $value = '';
    if (isset($cf['value'])) {
      $value = $cf['value'];
    } elseif (isset($cf['field_value'])) {
      $value = $cf['field_value'];
    }

    // Si el valor es array (ej. checkbox amenidades), serializar a JSON
    if (is_array($value)) {
      $value = wp_json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    $value = trim((string)$value);
    if ($value === '') {
      continue;
    }

    // Resolver la clave usando el mapa
    $key = isset($cf_map[$field_id]) ? $cf_map[$field_id] : $field_id;
    $out[$key] = $value;
  }

  return $out;
}
