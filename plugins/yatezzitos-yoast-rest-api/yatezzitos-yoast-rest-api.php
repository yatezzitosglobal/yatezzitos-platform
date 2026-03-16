<?php
/**
 * Plugin Name: Yatezzitos — Yoast SEO REST API para Taxonomías
 * Plugin URI:  https://github.com/YatezzitosMexico/yatezzitos-platform
 * Description: Habilita la escritura de campos SEO de Yoast vía REST API para taxonomías y sincroniza con wp_yoast_indexable.
 * Version:     1.2.0
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
// 2. Sincronizar cambios con wp_yoast_indexable
//
//    Yoast Premium renderiza el SEO desde su tabla wp_yoast_indexable,
//    NO desde wp_termmeta. Al escribir en termmeta vía API, debemos
//    actualizar también la fila correspondiente en la tabla indexable
//    para que los cambios aparezcan inmediatamente en el frontend.
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
