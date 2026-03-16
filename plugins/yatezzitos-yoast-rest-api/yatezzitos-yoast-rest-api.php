<?php
/**
 * Plugin Name: Yatezzitos — Yoast SEO REST API para Taxonomías
 * Plugin URI:  https://github.com/YatezzitosMexico/yatezzitos-platform
 * Description: Habilita la escritura de campos SEO de Yoast (título y meta descripción) vía REST API para las taxonomías property_city, property_category y property_feature. Invalida automáticamente el caché de Yoast Indexable.
 * Version:     1.1.0
 * Author:      Yatezzitos Dev Team
 * License:     GPL-2.0-or-later
 * Text Domain: yatezzitos-yoast-rest
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
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

add_action( 'updated_term_meta', 'yatezzitos_invalidate_yoast_indexable', 10, 4 );
add_action( 'added_term_meta',   'yatezzitos_invalidate_yoast_indexable', 10, 4 );

function yatezzitos_invalidate_yoast_indexable( $meta_id, $term_id, $meta_key, $meta_value ) {

    // Solo actuar cuando se modifican campos de Yoast SEO.
    $yoast_keys = array(
        '_yoast_wpseo_title',
        '_yoast_wpseo_metadesc',
        '_yoast_wpseo_focuskw',
    );

    if ( ! in_array( $meta_key, $yoast_keys, true ) ) {
        return;
    }

    global $wpdb;

    // Nombre de la tabla de indexables de Yoast (respeta prefijo de tabla).
    $table = $wpdb->prefix . 'yoast_indexable';

    // Verificar que la tabla existe (Yoast Premium puede no estar activo).
    if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
        return;
    }

    // Eliminar la fila del indexable para este término.
    // Yoast la reconstruirá automáticamente en el próximo request.
    $wpdb->delete(
        $table,
        array(
            'object_id'   => $term_id,
            'object_type' => 'term',
        ),
        array( '%d', '%s' )
    );

    // Limpiar cualquier caché de objeto persistente para este término.
    clean_term_cache( $term_id );
}
