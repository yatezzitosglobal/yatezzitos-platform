<?php
/**
 * Plugin Name: Yatezzitos — SEO Noindex de páginas utilitarias + sanea sitemap Yoast
 * Description: Marca `noindex, follow` y excluye del sitemap Yoast las ~24 URLs de cuenta, dashboard, pagos y gracias. También elimina el duplicado `/es/user-dashboard-2/` si existe. Libera crawl budget. Ver backlog tareas #5, #7, #12.
 * Version:     1.0.0
 * Author:      Yatezzitos Dev Team
 * License:     GPL-2.0-or-later
 *
 * Fuente del listado:
 *  - `page-sitemap.xml` inspeccionado el 15 abr 2026 (sección 3 auditoría, fila #25).
 *  - 24+ URLs utilitarias indexables que deberían ser noindex.
 *
 * Estrategia:
 *  - `wp_robots`: añade `noindex, follow` al HTML (Yoast respeta esta señal).
 *  - `wpseo_exclude_from_sitemap_by_post_ids`: excluye del sitemap de Yoast.
 *  - `robots_txt`: añade Disallow complementario (cinturón + tirantes).
 *
 * NO aplica a:
 *  - Páginas comerciales (brokers, ser-charter, subir-embarcacion)
 *  - Páginas informacionales con tráfico (blogs, docs con impresiones)
 *  - Páginas de ciudad / yate / tipo-embarcación (el core SEO del site)
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Paths de páginas utilitarias a desindexar.
 * Se matchea por `$wp->request` o slug de la query principal.
 */
function yzz_seo_utility_paths() {
    return array(
        // Cuenta / perfil
        'es/login',
        'es/mi-perfil',
        'es/mi-cotizacion',
        'es/mi-reserva',
        'es/mis-embarcaciones',
        'es/embarcaciones-favoritas',
        'es/reiniciar-contrasena',

        // Dashboard / backoffice de usuario
        'es/crm',
        'es/trafico-y-visitas',
        'es/facturas',
        'es/administracion-de-membresia',
        'es/mensajes',
        'es/user-dashboard',
        'es/user-dashboard-2', // Fila duplicada detectada en page-sitemap.xml
        'es/gastos',
        'es/feedback',
        'es/board',

        // Flujo de pago / conversión
        'es/pago-exitoso',
        'es/gracias',
        'es/gracias-contacto',
        'es/gracias-cotizacion',
        'es/pagos-con-tarjeta',
        'es/pagos-con-transferencia',

        // Búsqueda / comparativas (thin content)
        'es/busqueda-con-mapa',
        'es/comparar-embarcaciones',
        'es/search-results',
    );
}

/**
 * Detecta si la request actual cae en una URL utilitaria.
 *
 * WPML elimina el prefijo de idioma de $wp->request (ej. "es/gracias" → "gracias"),
 * por eso comparamos contra REQUEST_URI completo (que sí incluye "/es/gracias/")
 * y también contra $wp->request como fallback.
 */
function yzz_seo_is_utility_request() {
    $paths = yzz_seo_utility_paths();

    // Comparar usando REQUEST_URI (incluye prefijo de idioma WPML, ej. /es/gracias/).
    if ( isset( $_SERVER['REQUEST_URI'] ) ) {
        $uri_path  = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
        $uri_path  = trim( $uri_path, '/' );
        if ( in_array( $uri_path, $paths, true ) ) {
            return true;
        }
    }

    // Fallback: $wp->request (sin prefijo de idioma en instalaciones WPML).
    global $wp;
    if ( isset( $wp->request ) ) {
        $current = untrailingslashit( $wp->request );
        if ( in_array( $current, $paths, true ) ) {
            return true;
        }
        // También comparar las rutas sin su segmento de idioma inicial.
        foreach ( $paths as $path ) {
            $slug = preg_replace( '#^[a-z]{2}/#', '', $path );
            if ( $slug === $current ) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Inyectar meta robots "noindex, follow" en las utilitarias.
 * Yoast lee `wp_robots` y lo respeta.
 */
add_filter( 'wp_robots', function ( $robots ) {
    if ( yzz_seo_is_utility_request() ) {
        $robots['index']  = false;
        $robots['follow'] = true;
        // Control fino adicional (afecta apariencia en SERP si apareciera).
        $robots['max-image-preview'] = 'none';
        $robots['max-snippet']       = 0;
    } else {
        // Política global: asegurar snippets ricos en el resto del site.
        if ( ! isset( $robots['max-image-preview'] ) ) {
            $robots['max-image-preview'] = 'large';
        }
        if ( ! isset( $robots['max-snippet'] ) ) {
            $robots['max-snippet'] = -1;
        }
    }
    return $robots;
}, 20 );

/**
 * Excluir del sitemap de Yoast las páginas utilitarias.
 * Yoast expone `wpseo_exclude_from_sitemap_by_post_ids` — resolvemos los slugs a IDs al vuelo.
 */
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', function ( $excluded_ids ) {
    $paths = yzz_seo_utility_paths();
    foreach ( $paths as $path ) {
        // Los paths están sin slash inicial; convertir a URL absoluta.
        $page = get_page_by_path( $path );
        if ( $page && ! in_array( $page->ID, $excluded_ids, true ) ) {
            $excluded_ids[] = $page->ID;
        }
    }
    return $excluded_ids;
} );

/**
 * Además, filtrar URLs del sitemap que puedan venir de un CPT (por si alguna es post type distinto).
 */
add_filter( 'wpseo_sitemap_entry', function ( $url, $type, $post ) {
    if ( empty( $url['loc'] ) ) {
        return $url;
    }
    $path = wp_parse_url( $url['loc'], PHP_URL_PATH );
    $path = trim( $path, '/' );
    if ( in_array( $path, yzz_seo_utility_paths(), true ) ) {
        return false; // Excluye la entrada del sitemap.
    }
    return $url;
}, 10, 3 );

/**
 * Complemento robots.txt — añade Disallow para las utilitarias.
 * Yoast genera robots.txt dinámicamente; este filtro agrega nuestras reglas después.
 */
add_filter( 'robots_txt', function ( $output, $public ) {
    if ( ! $public ) {
        return $output;
    }
    $lines = array( '', '# Yatezzitos — Disallow de páginas utilitarias (auditoría abril 2026)' );
    foreach ( yzz_seo_utility_paths() as $path ) {
        $lines[] = 'Disallow: /' . $path . '/';
    }
    $lines[] = '';
    return $output . implode( "\n", $lines );
}, 10, 2 );

/**
 * Endpoint REST para auditar qué IDs está resolviendo y excluyendo.
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'yatezzitos/v1', '/seo-noindex-audit', array(
        'methods'             => 'GET',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        },
        'callback'            => function () {
            $result = array();
            foreach ( yzz_seo_utility_paths() as $path ) {
                $page       = get_page_by_path( $path );
                $result[]   = array(
                    'path'    => $path,
                    'post_id' => $page ? $page->ID : null,
                    'status'  => $page ? $page->post_status : 'not_found',
                );
            }
            return rest_ensure_response( array(
                'resolved' => $result,
                'total_paths' => count( yzz_seo_utility_paths() ),
            ) );
        },
    ) );
} );
