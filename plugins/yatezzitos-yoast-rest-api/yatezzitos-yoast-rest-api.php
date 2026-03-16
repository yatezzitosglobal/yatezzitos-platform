<?php
/**
 * Plugin Name: Yatezzitos — Yoast SEO REST API para Taxonomías
 * Plugin URI:  https://github.com/YatezzitosMexico/yatezzitos-platform
 * Description: Habilita la escritura de campos SEO de Yoast (título y meta descripción) vía REST API para las taxonomías property_city, property_category y property_feature.
 * Version:     1.0.0
 * Author:      Yatezzitos Dev Team
 * License:     GPL-2.0-or-later
 * Text Domain: yatezzitos-yoast-rest
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Evitar acceso directo.
}

/**
 * Registrar los meta campos de Yoast SEO como editables vía REST API
 * para las taxonomías personalizadas de properties.
 */
add_action( 'init', 'yatezzitos_register_yoast_rest_meta' );

function yatezzitos_register_yoast_rest_meta() {

    // Taxonomías donde queremos habilitar la escritura de Yoast SEO.
    $taxonomies = array( 'property_city', 'property_category', 'property_feature' );

    // Campos de Yoast SEO que queremos exponer.
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
