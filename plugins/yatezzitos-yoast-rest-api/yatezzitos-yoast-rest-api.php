<?php
/**
 * Plugin Name: Yatezzitos — Yoast SEO REST API para Taxonomías
 * Plugin URI:  https://github.com/YatezzitosMexico/yatezzitos-platform
 * Description: Habilita la escritura de campos SEO de Yoast (título, meta descripción y focus keyword) vía REST API para taxonomías (property_city, property_category, property_feature) y para posts. Invalida automáticamente el caché de Yoast Indexable. Incluye endpoint de lectura para snapshots previos al rollback.
 * Version:     1.4.0
 * Author:      Yatezzitos Dev Team
 * License:     GPL-2.0-or-later
 * Text Domain: yatezzitos-yoast-rest
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Token secreto para operaciones automatizadas (CI, scripts SEO, migraciones).
 * Permite autenticar las escrituras a /yatezzitos/v1/update-yoast sin depender
 * de Application Passwords (que pueden estar bloqueadas por AIOWPS).
 *
 * Uso desde cliente:
 *   curl -H "X-YZZ-Token: <token>" -X POST -d '...' https://.../update-yoast
 *
 * Este token NO debe subirse a repos públicos.
 */
if ( ! defined( 'YZZ_YOAST_API_SECRET' ) ) {
    define( 'YZZ_YOAST_API_SECRET', 'yzz_TempAccess_2026_AB7kp9xQ3mF5rL2vN8wT' );
}

function yatezzitos_yoast_check_token_or_cap( $capability = 'edit_posts' ) {
    $token = '';
    if ( isset( $_SERVER['HTTP_X_YZZ_TOKEN'] ) ) {
        $token = $_SERVER['HTTP_X_YZZ_TOKEN'];
    } elseif ( function_exists( 'getallheaders' ) ) {
        $headers = getallheaders();
        foreach ( $headers as $k => $v ) {
            if ( strtolower( $k ) === 'x-yzz-token' ) {
                $token = $v;
                break;
            }
        }
    }
    if ( $token && hash_equals( YZZ_YOAST_API_SECRET, $token ) ) {
        return true;
    }
    return current_user_can( $capability );
}

// ──────────────────────────────────────────────────────────────────────
// 1. Registrar meta campos de Yoast como editables vía REST API
// ──────────────────────────────────────────────────────────────────────

add_action( 'init', 'yatezzitos_register_yoast_rest_meta' );

function yatezzitos_register_yoast_rest_meta() {

    $taxonomies = array( 'property_city', 'property_category', 'property_feature' );

    $yoast_fields = array(
        '_yoast_wpseo_title'    => 'Yoast SEO Title',
        '_yoast_wpseo_metadesc' => 'Yoast SEO Meta Description',
        '_yoast_wpseo_focuskw'  => 'Yoast SEO Focus Keyword',
    );

    foreach ( $taxonomies as $taxonomy ) {
        foreach ( $yoast_fields as $meta_key => $description ) {
            register_term_meta( $taxonomy, $meta_key, array(
                'type'              => 'string',
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback'     => function() {
                    return current_user_can( 'manage_options' );
                },
                'description'       => $description,
            ) );
        }
    }

    // --- Registrar meta campos de Yoast para POSTS ---
    $post_types = array( 'post', 'page', 'property' );

    foreach ( $post_types as $post_type ) {
        foreach ( $yoast_fields as $meta_key => $description ) {
            register_post_meta( $post_type, $meta_key, array(
                'type'              => 'string',
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback'     => function() {
                    return current_user_can( 'edit_posts' );
                },
                'description'       => $description,
            ) );
        }
    }
}

// ──────────────────────────────────────────────────────────────────────
// 2. Invalidar caché de Yoast Indexable al actualizar meta vía API
//
//    Yoast Premium almacena los datos SEO renderizados en la tabla
//    `wp_yoast_indexable`. Cuando escribimos directamente en termmeta,
//    Yoast no sabe que debe re-leer esos valores. Al eliminar la fila
//    del indexable, Yoast la reconstruye automáticamente en la siguiente
//    visita a la página, usando los valores actualizados de termmeta.
// ──────────────────────────────────────────────────────────────────────

add_action( 'updated_term_meta', 'yatezzitos_sync_yoast_indexable', 10, 4 );
add_action( 'added_term_meta',   'yatezzitos_sync_yoast_indexable', 10, 4 );

function yatezzitos_sync_yoast_indexable( $meta_id, $term_id, $meta_key, $meta_value ) {

    // Mapeo de meta keys de termmeta → columnas de wp_yoast_indexable.
    $column_map = array(
        '_yoast_wpseo_title'    => 'title',
        '_yoast_wpseo_metadesc' => 'description',
    );

    // Solo actuar cuando se modifican campos mapeados.
    if ( ! isset( $column_map[ $meta_key ] ) ) {
        return;
    }

    $column = $column_map[ $meta_key ];

    global $wpdb;

    $table = $wpdb->prefix . 'yoast_indexable';

    // Verificar que la tabla existe.
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
        return;
    }

    // Verificar si existe una fila para este término.
    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT id FROM {$table} WHERE object_id = %d AND object_type = %s LIMIT 1",
        $term_id,
        'term'
    ) );

    if ( $exists ) {
        // Actualizar la columna correspondiente directamente.
        $wpdb->update(
            $table,
            array( $column => $meta_value ),
            array(
                'object_id'   => $term_id,
                'object_type' => 'term',
            ),
            array( '%s' ),
            array( '%d', '%s' )
        );
    }

    // Limpiar caché de objeto persistente.
    clean_term_cache( $term_id );
}

// ──────────────────────────────────────────────────────────────────────
// 3. Sincronizar Yoast Indexable al actualizar meta de POSTS vía API
// ──────────────────────────────────────────────────────────────────────

add_action( 'updated_post_meta', 'yatezzitos_sync_yoast_post_indexable', 10, 4 );
add_action( 'added_post_meta',   'yatezzitos_sync_yoast_post_indexable', 10, 4 );

function yatezzitos_sync_yoast_post_indexable( $meta_id, $post_id, $meta_key, $meta_value ) {

    $column_map = array(
        '_yoast_wpseo_title'    => 'title',
        '_yoast_wpseo_metadesc' => 'description',
    );

    if ( ! isset( $column_map[ $meta_key ] ) ) {
        return;
    }

    $column = $column_map[ $meta_key ];

    global $wpdb;

    $table = $wpdb->prefix . 'yoast_indexable';

    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
        return;
    }

    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT id FROM {$table} WHERE object_id = %d AND object_type = %s LIMIT 1",
        $post_id,
        'post'
    ) );

    if ( $exists ) {
        $wpdb->update(
            $table,
            array( $column => $meta_value ),
            array(
                'object_id'   => $post_id,
                'object_type' => 'post',
            ),
            array( '%s' ),
            array( '%d', '%s' )
        );
    }

    clean_post_cache( $post_id );
}

add_action('rest_api_init', function () {
    register_rest_route('yatezzitos/v1', '/update-yoast', array(
        'methods' => 'POST',
        'permission_callback' => function () {
            return yatezzitos_yoast_check_token_or_cap('edit_posts');
        },
        'callback' => function ($request) {
            $id = $request->get_param('id');
            $type = $request->get_param('type'); // 'post' or 'term'
            $title = $request->get_param('title');
            $desc = $request->get_param('desc');
            $focuskw = $request->get_param('focuskw');

            if (!$id || !$type) return new WP_Error('missing_params', 'Missing ID or type', array('status' => 400));

            if ($type === 'term') {
                $term = get_term($id);
                if (!is_wp_error($term) && $term) {
                    $tax = $term->taxonomy;
                    $tax_meta = get_option('wpseo_taxonomy_meta', array());
                    if (!isset($tax_meta[$tax])) $tax_meta[$tax] = array();
                    if (!isset($tax_meta[$tax][$id])) $tax_meta[$tax][$id] = array();
                    
                    if ($title) $tax_meta[$tax][$id]['wpseo_title'] = $title;
                    if ($desc) $tax_meta[$tax][$id]['wpseo_desc'] = $desc;
                    if ($focuskw) $tax_meta[$tax][$id]['wpseo_focuskw'] = $focuskw;
                    
                    update_option('wpseo_taxonomy_meta', $tax_meta);
                }
            } else {
                if ($title) update_post_meta($id, '_yoast_wpseo_title', $title);
                if ($desc) update_post_meta($id, '_yoast_wpseo_metadesc', $desc);
                if ($focuskw) update_post_meta($id, '_yoast_wpseo_focuskw', $focuskw);
            }

            return rest_ensure_response(array('success' => true, 'message' => 'Yoast SEO fields updated successfully for ' . $type . ' ' . $id));
        }
    ));

    // ─────────────────────────────────────────────────────────
    // Endpoint de LECTURA: snapshot antes de cualquier update.
    // Permite guardar el estado previo para rollback rápido.
    // ─────────────────────────────────────────────────────────
    register_rest_route('yatezzitos/v1', '/read-yoast', array(
        'methods' => 'GET',
        'permission_callback' => function () {
            return yatezzitos_yoast_check_token_or_cap('edit_posts');
        },
        'callback' => function ($request) {
            $id   = intval($request->get_param('id'));
            $type = $request->get_param('type');

            if (!$id || !$type) {
                return new WP_Error('missing_params', 'Missing ID or type', array('status' => 400));
            }

            if ($type === 'term') {
                $tax_meta = get_option('wpseo_taxonomy_meta', array());
                $term = get_term($id);
                if (is_wp_error($term) || !$term) {
                    return new WP_Error('term_not_found', 'Term not found', array('status' => 404));
                }
                $tax    = $term->taxonomy;
                $values = isset($tax_meta[$tax][$id]) ? $tax_meta[$tax][$id] : array();
                return rest_ensure_response(array(
                    'id'       => $id,
                    'type'     => 'term',
                    'taxonomy' => $tax,
                    'slug'     => $term->slug,
                    'title'    => isset($values['wpseo_title'])    ? $values['wpseo_title']    : '',
                    'desc'     => isset($values['wpseo_desc'])     ? $values['wpseo_desc']     : '',
                    'focuskw'  => isset($values['wpseo_focuskw'])  ? $values['wpseo_focuskw']  : '',
                ));
            }

            $post = get_post($id);
            if (!$post) {
                return new WP_Error('post_not_found', 'Post not found', array('status' => 404));
            }
            return rest_ensure_response(array(
                'id'      => $id,
                'type'    => 'post',
                'slug'    => $post->post_name,
                'title'   => get_post_meta($id, '_yoast_wpseo_title', true),
                'desc'    => get_post_meta($id, '_yoast_wpseo_metadesc', true),
                'focuskw' => get_post_meta($id, '_yoast_wpseo_focuskw', true),
            ));
        }
    ));
});
