<?php
/**
 * Plugin Name: Yatezzitos — SEO 301 Redirects (auditoría abril 2026)
 * Description: Redirige permanentemente URLs 404 canibalizadoras y slugs históricos a su canónica. Recupera ~319 clics/90d detectados por GSC en URLs muertas. Ver docs/seo/estrategia/backlog-auditoria-abril-2026.md (tareas #1, #6, #13).
 * Version:     1.0.0
 * Author:      Yatezzitos Dev Team
 * License:     GPL-2.0-or-later
 *
 * MU-plugin: se carga automáticamente al estar en wp-content/mu-plugins/.
 * NO requiere activación. Si hay que deshabilitar, renombrar/eliminar el archivo.
 *
 * Fuente del mapeo:
 *  - GSC 90d (17 ene – 15 abr 2026): las URLs `from` son las que reciben clics pero devuelven 404.
 *  - Crawl HTTP en vivo: confirmó los 404 antes de diseñar el fix.
 *  - Auditoría integral, sección 6 (canibalización) y sección 8 (problemas técnicos).
 *
 * Comportamiento:
 *  - Se ejecuta en `template_redirect` con prioridad 1 (antes que 404).
 *  - Hace `wp_safe_redirect()` con status 301 (permanente, transfiere link equity).
 *  - Soporta trailing slash tolerante.
 *  - Loguea en `wp_options['yzz_seo_redirects_log']` (últimos 200) para verificar que la regla se dispara.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Mapa de redirecciones. Clave = path entrante (normalizado, con slashes). Valor = path destino canónico.
 *
 * Mantener este array en el plugin (no en DB) para que el mapeo esté versionado en git y sea auditable.
 */
function yzz_seo_redirects_map() {
    return array(
        // Tarea #1 — URLs 404 canibalizadoras detectadas por GSC (Sección 6 de la auditoría).
        '/es/ciudad/yates-acapulco/'         => '/es/ciudad/renta-de-yates-en-acapulco/',   // 77 clics/90d
        '/es/ciudad/yates-huatulco/'         => '/es/ciudad/renta-de-yates-huatulco/',      // 121 clics/90d
        '/es/ciudad/renta-yates-en-ixtapa/'  => '/es/ciudad/yates-ixtapa/',                 // 47 clics/90d
        '/es/ciudad/en-ciudad-yates-cabos/'  => '/es/ciudad/yates-cabos/',                  // 74 clics/90d — bug de slug

        // Tarea #6 — Typo "brookers" → "brokers" (slug correcto). La página destino debe existir en WP.
        '/es/brookers-de-yates-en-mexico/'   => '/es/brokers-de-yates-en-mexico/',

        // Tarea #13 — Previsión para futura normalización de slugs de ciudad al patrón canónico
        // `renta-de-yates-en-{ciudad}/`. Estas reglas quedan listas para activarse en cuanto se
        // renombre el term_slug en WP admin. Mientras los slugs actuales sigan vivos, estas reglas
        // NO disparan (no hay match contra la URL entrante real).
        // — Los Cabos: se renombrará a renta-de-yates-en-los-cabos/
        '/es/ciudad/yates-cabos-legacy/'     => '/es/ciudad/renta-de-yates-en-los-cabos/',
        // — Playa del Carmen
        '/es/ciudad/yates-playa-del-carmen-legacy/' => '/es/ciudad/renta-de-yates-en-playa-del-carmen/',
        // — Nuevo Vallarta
        '/es/ciudad/yates-en-nuevo-vallarta-legacy/' => '/es/ciudad/renta-de-yates-en-nuevo-vallarta/',
        // — Ixtapa (consolidar con Zihuatanejo en el nuevo slug)
        '/es/ciudad/yates-ixtapa-legacy/'    => '/es/ciudad/renta-de-yates-en-ixtapa-zihuatanejo/',
    );
}

add_action( 'template_redirect', 'yzz_seo_maybe_redirect', 1 );

function yzz_seo_maybe_redirect() {

    // No interferir con admin, AJAX, REST, feeds, CLI.
    if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
        return;
    }

    $request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
    if ( empty( $request_path ) ) {
        return;
    }

    // Normalizar: asegurar trailing slash para coincidir con el mapa.
    $normalized = trailingslashit( $request_path );

    $map = yzz_seo_redirects_map();

    if ( ! isset( $map[ $normalized ] ) ) {
        return;
    }

    $target = $map[ $normalized ];

    // Evitar loops: si ya estamos en el destino, no redirigir.
    if ( $normalized === $target ) {
        return;
    }

    yzz_seo_log_redirect( $normalized, $target );

    wp_safe_redirect( home_url( $target ), 301 );
    exit;
}

/**
 * Log liviano para verificar que las reglas se disparan.
 * Guarda los últimos 200 redirects en wp_options. Útil para auditar en cronjob.
 */
function yzz_seo_log_redirect( $from, $to ) {
    $log = get_option( 'yzz_seo_redirects_log', array() );
    array_unshift( $log, array(
        'from' => $from,
        'to'   => $to,
        'ts'   => current_time( 'mysql' ),
        'ref'  => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : '',
    ) );
    if ( count( $log ) > 200 ) {
        $log = array_slice( $log, 0, 200 );
    }
    update_option( 'yzz_seo_redirects_log', $log, false );
}

/**
 * Endpoint GET de solo lectura para inspeccionar el log desde fuera.
 * Requiere capability `manage_options`.
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'yatezzitos/v1', '/seo-redirects-log', array(
        'methods'             => 'GET',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        },
        'callback'            => function () {
            return rest_ensure_response( array(
                'map' => yzz_seo_redirects_map(),
                'log' => get_option( 'yzz_seo_redirects_log', array() ),
            ) );
        },
    ) );
} );
