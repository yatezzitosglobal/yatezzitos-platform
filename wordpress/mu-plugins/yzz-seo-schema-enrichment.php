<?php
/**
 * Plugin Name: Yatezzitos — SEO Schema Enrichment (Organization + LocalBusiness + FAQ por ciudad)
 * Description: Añade al `<head>` JSON-LD `Organization` + `WebSite` con `SearchAction` en el home, y `LocalBusiness` + `FAQPage` en cada página de ciudad. Complementa el schema mínimo que Yoast ya entrega. Ver backlog tareas #10, #18, #19.
 * Version:     1.0.0
 * Author:      Yatezzitos Dev Team
 * License:     GPL-2.0-or-later
 *
 * Inyecta JSON-LD extra sin colisionar con el `@graph` de Yoast (cada bloque es un `<script>` separado
 * con tipo específico; Google consolida igualmente).
 *
 * FAQ real extraída de queries GSC 90d: preguntas formuladas por usuarios con clics reales.
 * LocalBusiness: direcciones y teléfonos por ciudad (PLACEHOLDERS — actualizar desde CLAUDE.md /
 * tabla de operaciones antes de deploy).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Mapeo de slugs de ciudad → datos LocalBusiness + FAQ.
 * Slugs actuales en WP (no los canónicos futuros). Ajustar al renombrar.
 */
function yzz_seo_city_schema_data() {
    return array(
        'renta-de-yates-cancun' => array(
            'name'       => 'Yatezzitos Cancún',
            'region'     => 'Quintana Roo',
            'latitude'   => 21.1619,
            'longitude'  => -86.8515,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en Cancún?',
                       'a' => 'La renta de yates en Cancún parte desde alrededor de $8,000 MXN por hora, con paquetes de mínimo 4 horas. El precio varía según el tamaño del yate, temporada y si incluye tripulación, combustible y alimentos.' ),
                array( 'q' => '¿Qué incluye la renta de un yate en Cancún?',
                       'a' => 'La renta estándar incluye tripulación certificada, combustible, hieleras, bebidas no alcohólicas y salvavidas. Extras: chef a bordo, open bar, decoración, fotografía y tours de snorkel.' ),
                array( 'q' => '¿Cuántas personas caben en un yate en Cancún?',
                       'a' => 'Nuestra flota en Cancún va desde 6 personas (yates íntimos) hasta 40 personas (yates grandes para eventos). Lo más solicitado: 10-15 personas.' ),
                array( 'q' => '¿Qué playas se pueden visitar en yate desde Cancún?',
                       'a' => 'Desde Cancún se visitan Isla Mujeres, Playa Tortugas, Punta Sam, Playa del Niño, Isla Contoy (con permiso) y los arrecifes del Caribe.' ),
            ),
        ),
        'renta-de-yates-en-puerto-vallarta' => array(
            'name'       => 'Yatezzitos Puerto Vallarta',
            'region'     => 'Jalisco',
            'latitude'   => 20.6534,
            'longitude'  => -105.2253,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en Puerto Vallarta?',
                       'a' => 'La renta en Puerto Vallarta comienza desde $6,500 MXN por hora con mínimo de 4 horas. Puerto Vallarta suele tener mejor relación calidad-precio que Los Cabos o Cancún.' ),
                array( 'q' => '¿Qué playas visitar en yate desde Puerto Vallarta?',
                       'a' => 'Las más populares: Playa Madagascar, Colomitos, Las Ánimas, Yelapa, Quimixto, Majahuitas e Islas Marietas (con permiso).' ),
                array( 'q' => '¿Se puede rentar un yate para boda o despedida en Puerto Vallarta?',
                       'a' => 'Sí. Ofrecemos paquetes dedicados para bodas, aniversarios y despedidas de soltero/a con decoración, DJ, chef y coordinación a bordo.' ),
                array( 'q' => '¿Cuál es la diferencia entre Puerto Vallarta y Nuevo Vallarta para rentar un yate?',
                       'a' => 'Puerto Vallarta (Jalisco) sale de la Marina Vallarta y da acceso rápido a Mismaloya y la costa sur. Nuevo Vallarta (Nayarit) sale de Paradise Village Marina y está más cerca de Islas Marietas.' ),
            ),
        ),
        'renta-de-yates-en-acapulco' => array(
            'name'       => 'Yatezzitos Acapulco',
            'region'     => 'Guerrero',
            'latitude'   => 16.8531,
            'longitude'  => -99.8237,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en Acapulco?',
                       'a' => 'La renta en Acapulco parte desde $5,500 MXN por hora con paquetes de 4 horas mínimo. Incluye tripulación y combustible.' ),
                array( 'q' => '¿Qué se puede hacer en un yate en Acapulco?',
                       'a' => 'Recorridos por la Bahía de Acapulco, visita a La Roqueta, snorkel, paseo al Clavadistas de La Quebrada, pesca deportiva y fiestas privadas al atardecer.' ),
                array( 'q' => '¿Es seguro rentar un yate en Acapulco?',
                       'a' => 'Sí. Todas nuestras embarcaciones cuentan con certificación de Capitanía de Puerto, tripulación con licencia, chalecos salvavidas y seguro vigente.' ),
            ),
        ),
        'renta-de-yates-en-la-paz' => array(
            'name'       => 'Yatezzitos La Paz',
            'region'     => 'Baja California Sur',
            'latitude'   => 24.1426,
            'longitude'  => -110.3128,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en La Paz?',
                       'a' => 'Los yates en La Paz BCS parten desde $6,000 MXN por hora con mínimo 4 horas. Incluye tripulación y combustible. Temporada alta (dic-abr) puede variar.' ),
                array( 'q' => '¿Se puede visitar Isla Espíritu Santo en yate?',
                       'a' => 'Sí. Es nuestro tour estrella. Incluye nado con lobos marinos, snorkel en los arrecifes y playas vírgenes de la isla declarada Patrimonio Natural UNESCO.' ),
                array( 'q' => '¿Hay renta de yates económicos en La Paz BCS?',
                       'a' => 'Sí. Tenemos opciones desde 25 pies ideales para grupos pequeños y presupuesto ajustado, desde $4,800 MXN las 4 horas.' ),
            ),
        ),
        'renta-de-yates-huatulco' => array(
            'name'       => 'Yatezzitos Huatulco',
            'region'     => 'Oaxaca',
            'latitude'   => 15.7597,
            'longitude'  => -96.1397,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en Huatulco?',
                       'a' => 'La renta en Huatulco parte desde $5,200 MXN por hora con paquete de 4 horas mínimo. Incluye recorrido por las 36 bahías.' ),
                array( 'q' => '¿Qué bahías visitar en yate desde Huatulco?',
                       'a' => 'Santa Cruz, Maguey, Organo, Chahué, Conejos, San Agustín, Chachacual y la icónica Playa La India (solo accesible por mar).' ),
                array( 'q' => '¿Se puede hacer avistamiento de ballenas en Huatulco?',
                       'a' => 'Sí, de diciembre a marzo. Las ballenas jorobadas cruzan por Huatulco. Ofrecemos tours guiados con observación responsable.' ),
            ),
        ),
        'renta-de-yates-mazatlan' => array(
            'name'       => 'Yatezzitos Mazatlán',
            'region'     => 'Sinaloa',
            'latitude'   => 23.2494,
            'longitude'  => -106.4111,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en Mazatlán?',
                       'a' => 'Desde $5,000 MXN por hora con mínimo 4 horas. Incluye tripulación, combustible y bebidas frías.' ),
                array( 'q' => '¿Se puede hacer avistamiento de ballenas en Mazatlán?',
                       'a' => 'Sí, de noviembre a abril. Ballenas jorobadas pasan frente a Mazatlán en su ruta migratoria.' ),
                array( 'q' => '¿Qué es el combate naval en Mazatlán y se hace en yate?',
                       'a' => 'El combate naval es un espectáculo nocturno conmemorativo que ofrecemos desde yate privado con vista privilegiada al malecón iluminado y los fuegos pirotécnicos.' ),
            ),
        ),
        'yates-cabos' => array(
            'name'       => 'Yatezzitos Los Cabos',
            'region'     => 'Baja California Sur',
            'latitude'   => 22.8905,
            'longitude'  => -109.9167,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en Los Cabos?',
                       'a' => 'Los Cabos es el destino premium. La renta parte desde $9,500 MXN por hora con mínimo 4 horas. Yates de lujo desde $15,000 MXN/hora.' ),
                array( 'q' => '¿Qué ver en yate en Los Cabos / Cabo San Lucas?',
                       'a' => 'El Arco de Cabo San Lucas, Playa del Amor, Playa del Divorcio, Lover\'s Beach, colonia de lobos marinos y el faro de Cabo Falso.' ),
                array( 'q' => '¿Hay yates con chef a bordo en Los Cabos?',
                       'a' => 'Sí. Nuestra flota premium incluye opción de chef privado con menús de mariscos, mexicana gourmet y fusiones del Pacífico.' ),
            ),
        ),
        'yates-playa-del-carmen' => array(
            'name'       => 'Yatezzitos Playa del Carmen',
            'region'     => 'Quintana Roo',
            'latitude'   => 20.6296,
            'longitude'  => -87.0739,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en Playa del Carmen?',
                       'a' => 'Desde $6,800 MXN por hora con mínimo 4 horas. Salidas desde Marina Puerto Aventuras y Playa del Carmen centro.' ),
                array( 'q' => '¿Se puede ir a Cozumel en yate desde Playa del Carmen?',
                       'a' => 'Sí. El tour a Cozumel incluye snorkel en los arrecifes Palancar y Paraíso, reconocidos mundialmente.' ),
                array( 'q' => '¿Qué mejor: yate en Cancún o en Playa del Carmen?',
                       'a' => 'Playa del Carmen ofrece acceso más directo a arrecifes. Cancún es mejor para Isla Mujeres y vida nocturna.' ),
            ),
        ),
        'yates-en-nuevo-vallarta' => array(
            'name'       => 'Yatezzitos Nuevo Vallarta',
            'region'     => 'Nayarit',
            'latitude'   => 20.7082,
            'longitude'  => -105.2916,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en Nuevo Vallarta?',
                       'a' => 'Desde $6,500 MXN por hora con mínimo 4 horas desde Marina Paradise Village o Marina Riviera Nayarit.' ),
                array( 'q' => '¿Se puede ir a Islas Marietas desde Nuevo Vallarta?',
                       'a' => 'Sí. Es el punto más cercano a las Islas Marietas. El acceso a Playa del Amor (La Escondida) requiere permiso de CONANP.' ),
            ),
        ),
        'yates-ixtapa' => array(
            'name'       => 'Yatezzitos Ixtapa-Zihuatanejo',
            'region'     => 'Guerrero',
            'latitude'   => 17.6479,
            'longitude'  => -101.5517,
            'faqs'       => array(
                array( 'q' => '¿Cuánto cuesta rentar un yate en Ixtapa?',
                       'a' => 'Desde $5,400 MXN por hora con paquete mínimo 4 horas. Salidas desde Marina Ixtapa.' ),
                array( 'q' => '¿Qué playas visitar en yate en Ixtapa-Zihuatanejo?',
                       'a' => 'Playa Las Gatas, Playa La Ropa, Isla Ixtapa, Playa Linda, Playa Majahua y bahías vírgenes.' ),
            ),
        ),
    );
}

/**
 * JSON-LD Organization + WebSite con SearchAction para la home.
 * Se inyecta solo en la portada `/es/` y `/`.
 */
add_action( 'wp_head', function () {
    if ( ! is_front_page() && ! is_home() ) {
        // Permitir también ruta literal /es/
        global $wp;
        if ( ! isset( $wp->request ) || $wp->request !== 'es' ) {
            return;
        }
    }

    $home_url = home_url( '/' );

    $organization = array(
        '@context'     => 'https://schema.org',
        '@type'        => 'Organization',
        '@id'          => $home_url . '#organization',
        'name'         => 'Yatezzitos',
        'alternateName' => 'Yatezzitos Global',
        'url'          => $home_url,
        'logo'         => array(
            '@type' => 'ImageObject',
            'url'   => $home_url . 'wp-content/uploads/2024/01/yatezzitos-logo.png',
            'width' => 400,
            'height' => 400,
        ),
        'description'  => 'Plataforma de renta de yates, catamaranes, lanchas y veleros en los 10 principales destinos de playa de México.',
        'sameAs'       => array(
            'https://www.facebook.com/yatezzitos',
            'https://www.instagram.com/yatezzitos',
            'https://www.youtube.com/@yatezzitos',
            'https://www.tiktok.com/@yatezzitos',
        ),
        'areaServed'   => array_values( array_map( function( $slug ) {
            $data = yzz_seo_city_schema_data();
            return isset( $data[ $slug ] ) ? array(
                '@type' => 'City',
                'name'  => $data[ $slug ]['name'],
            ) : null;
        }, array_keys( yzz_seo_city_schema_data() ) ) ),
    );

    $website = array(
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        '@id'             => $home_url . '#website',
        'url'             => $home_url,
        'name'            => 'Yatezzitos',
        'publisher'       => array( '@id' => $home_url . '#organization' ),
        'inLanguage'      => 'es-MX',
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => array(
                '@type'       => 'EntryPoint',
                'urlTemplate' => $home_url . '?s={search_term_string}',
            ),
            'query-input' => 'required name=search_term_string',
        ),
    );

    echo "\n<!-- Yatezzitos SEO Schema Enrichment -->\n";
    echo '<script type="application/ld+json">' . wp_json_encode( $organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
    echo '<script type="application/ld+json">' . wp_json_encode( $website, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}, 20 );

/**
 * LocalBusiness + FAQPage en cada página de ciudad (taxonomía `property_city`).
 */
add_action( 'wp_head', function () {
    if ( ! is_tax( 'property_city' ) ) {
        return;
    }

    $term = get_queried_object();
    if ( ! $term || is_wp_error( $term ) ) {
        return;
    }

    $data_map = yzz_seo_city_schema_data();
    if ( ! isset( $data_map[ $term->slug ] ) ) {
        return;
    }

    $data = $data_map[ $term->slug ];
    $url  = get_term_link( $term );
    if ( is_wp_error( $url ) ) {
        return;
    }

    $local_business = array(
        '@context'       => 'https://schema.org',
        '@type'          => 'TravelAgency', // Subtipo apropiado para una agencia de yachting
        '@id'            => $url . '#localbusiness',
        'name'           => $data['name'],
        'url'            => $url,
        'areaServed'     => array(
            '@type' => 'City',
            'name'  => $data['name'],
        ),
        'address'        => array(
            '@type'           => 'PostalAddress',
            'addressRegion'   => $data['region'],
            'addressCountry'  => 'MX',
        ),
        'geo'            => array(
            '@type'     => 'GeoCoordinates',
            'latitude'  => $data['latitude'],
            'longitude' => $data['longitude'],
        ),
        'priceRange'     => '$$$',
        'parentOrganization' => array( '@id' => home_url( '/' ) . '#organization' ),
    );

    $faq_entities = array();
    foreach ( $data['faqs'] as $faq ) {
        $faq_entities[] = array(
            '@type'          => 'Question',
            'name'           => $faq['q'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => $faq['a'],
            ),
        );
    }

    $faq_page = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        '@id'        => $url . '#faq',
        'mainEntity' => $faq_entities,
    );

    echo "\n<!-- Yatezzitos SEO Schema Enrichment (city) -->\n";
    echo '<script type="application/ld+json">' . wp_json_encode( $local_business, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
    echo '<script type="application/ld+json">' . wp_json_encode( $faq_page, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}, 20 );
