<?php
/**
 * Plugin Name: Yatezzitos — Shortcode CTA ciudad para blogs de playas
 * Description: Expone el shortcode `[yzz_cta_ciudad ciudad="puerto-vallarta" playa="Madagascar"]` que inserta un bloque de CTA coherente con el diseño para monetizar blogs informacionales de playas. Ver backlog tarea #11.
 * Version:     1.0.0
 * Author:      Yatezzitos Dev Team
 * License:     GPL-2.0-or-later
 *
 * Uso:
 *   [yzz_cta_ciudad ciudad="puerto-vallarta" playa="Madagascar"]
 *   [yzz_cta_ciudad ciudad="huatulco" playa="La India"]
 *
 * El bloque linkea a /es/ciudad/renta-de-yates-en-{ciudad}/ y muestra un CTA de cotización.
 * Redirección a la URL canónica correcta se maneja en el MU-plugin de 301 redirects.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'yzz_cta_ciudad', function ( $atts ) {

    $atts = shortcode_atts( array(
        'ciudad' => '',
        'playa'  => '',
    ), $atts, 'yzz_cta_ciudad' );

    $ciudad_slug = sanitize_title( $atts['ciudad'] );
    $playa       = sanitize_text_field( $atts['playa'] );

    if ( empty( $ciudad_slug ) ) {
        return '';
    }

    // Mapeo de slug → nombre visible + URL canónica de ciudad.
    $ciudad_map = array(
        'cancun'             => array( 'nombre' => 'Cancún',             'url' => '/es/ciudad/renta-de-yates-cancun/' ),
        'puerto-vallarta'    => array( 'nombre' => 'Puerto Vallarta',    'url' => '/es/ciudad/renta-de-yates-en-puerto-vallarta/' ),
        'los-cabos'          => array( 'nombre' => 'Los Cabos',          'url' => '/es/ciudad/yates-cabos/' ),
        'la-paz'             => array( 'nombre' => 'La Paz',             'url' => '/es/ciudad/renta-de-yates-en-la-paz/' ),
        'mazatlan'           => array( 'nombre' => 'Mazatlán',           'url' => '/es/ciudad/renta-de-yates-mazatlan/' ),
        'acapulco'           => array( 'nombre' => 'Acapulco',           'url' => '/es/ciudad/renta-de-yates-en-acapulco/' ),
        'huatulco'           => array( 'nombre' => 'Huatulco',           'url' => '/es/ciudad/renta-de-yates-huatulco/' ),
        'ixtapa'             => array( 'nombre' => 'Ixtapa-Zihuatanejo', 'url' => '/es/ciudad/yates-ixtapa/' ),
        'nuevo-vallarta'     => array( 'nombre' => 'Nuevo Vallarta',     'url' => '/es/ciudad/yates-en-nuevo-vallarta/' ),
        'playa-del-carmen'   => array( 'nombre' => 'Playa del Carmen',   'url' => '/es/ciudad/yates-playa-del-carmen/' ),
    );

    if ( ! isset( $ciudad_map[ $ciudad_slug ] ) ) {
        return '';
    }

    $ciudad = $ciudad_map[ $ciudad_slug ];

    $titulo = $playa
        ? sprintf( 'Renta un yate para visitar %s desde %s', esc_html( $playa ), esc_html( $ciudad['nombre'] ) )
        : sprintf( 'Renta un yate en %s', esc_html( $ciudad['nombre'] ) );

    $bajada = $playa
        ? sprintf( '¿Te gustaría llegar a %s con total privacidad? Organiza tu día perfecto a bordo de un yate privado en %s.', esc_html( $playa ), esc_html( $ciudad['nombre'] ) )
        : sprintf( 'Descubre nuestra flota de yates, catamaranes y lanchas disponibles en %s.', esc_html( $ciudad['nombre'] ) );

    ob_start();
    ?>
    <aside class="yzz-cta-ciudad" style="margin:2em 0;padding:1.5em;border-radius:10px;background:linear-gradient(135deg,#0a2540 0%,#1a4480 100%);color:#fff;text-align:center;">
        <h3 style="margin:0 0 .5em;color:#fff;font-size:1.4em;"><?php echo $titulo; ?></h3>
        <p style="margin:0 0 1em;color:rgba(255,255,255,.9);line-height:1.5;"><?php echo $bajada; ?></p>
        <a href="<?php echo esc_url( $ciudad['url'] ); ?>"
           style="display:inline-block;padding:.8em 2em;background:#ffc93c;color:#0a2540;border-radius:6px;text-decoration:none;font-weight:700;"
           rel="noopener">
            Ver yates disponibles en <?php echo esc_html( $ciudad['nombre'] ); ?> →
        </a>
    </aside>
    <?php
    return ob_get_clean();
} );
