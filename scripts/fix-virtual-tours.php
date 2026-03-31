<?php
/**
 * fix-virtual-tours.php
 *
 * Extrae la URL del campo virtual_tour en todos los yates.
 * Convierte el formato antiguo (<script data-kuula="URL">) al nuevo formato (solo URL).
 *
 * USO — WP-CLI:
 *   wp eval-file fix-virtual-tours.php
 *
 * USO — Code Snippets (WP Admin):
 *   Pegar en Code Snippets, activar, recargar WP Admin.
 *   Verás el resultado como un aviso en la parte superior.
 *   Desactivar y eliminar después de correrlo.
 */

// ─── Lógica principal ────────────────────────────────────────────────────────

function ytz_fix_virtual_tours_run() {
    $args = [
        'post_type'      => 'property',
        'post_status'    => ['publish', 'draft', 'private'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ];

    $ids = get_posts($args);

    if (empty($ids)) {
        return ['error' => 'No se encontraron propiedades.'];
    }

    $log     = [];
    $fixed   = 0;
    $already = 0;
    $skipped = 0;

    foreach ($ids as $post_id) {
        $value = get_post_meta($post_id, 'virtual_tour', true);

        if (empty($value)) {
            $skipped++;
            continue;
        }

        if (strpos($value, '<script') === false) {
            $already++;
            continue;
        }

        if (preg_match('/data-kuula=["\']([^"\']+)["\']/', $value, $matches)) {
            $clean_url = trim($matches[1]);
            update_post_meta($post_id, 'virtual_tour', $clean_url);
            $log[] = '✓ [' . $post_id . '] ' . get_the_title($post_id) . '<br>&nbsp;&nbsp;→ ' . esc_html($clean_url);
            $fixed++;
        } else {
            $log[] = '⚠ [' . $post_id . '] ' . get_the_title($post_id) . ' — script no reconocido, revisar manualmente';
            $skipped++;
        }
    }

    return [
        'fixed'   => $fixed,
        'already' => $already,
        'skipped' => $skipped,
        'log'     => $log,
    ];
}

// ─── Mostrar resultado en WP Admin (admin_notices) ───────────────────────────

function ytz_fix_virtual_tours_notice() {
    // Solo correr una vez: si ya hay resultado guardado, mostrarlo y no volver a ejecutar
    $result = get_transient('ytz_fix_vt_result');

    if ($result === false) {
        $result = ytz_fix_virtual_tours_run();
        set_transient('ytz_fix_vt_result', $result, HOUR_IN_SECONDS * 2);
    }

    if (isset($result['error'])) {
        echo '<div class="notice notice-error"><p><strong>Fix Virtual Tours:</strong> ' . esc_html($result['error']) . '</p></div>';
        return;
    }

    $details = implode('<br>', $result['log']);
    echo '<div class="notice notice-success" style="max-height:400px;overflow:auto;">';
    echo '<p><strong>✅ Fix Virtual Tours completado</strong></p>';
    echo '<p>Actualizados: <strong>' . $result['fixed'] . '</strong> &nbsp;|&nbsp; ';
    echo 'Ya correctos: <strong>' . $result['already'] . '</strong> &nbsp;|&nbsp; ';
    echo 'Omitidos: <strong>' . $result['skipped'] . '</strong></p>';
    if (!empty($result['log'])) {
        echo '<p style="font-family:monospace;font-size:12px;">' . $details . '</p>';
    }
    echo '<p style="color:#666;font-size:12px;">⚠ Desactiva y elimina este snippet ahora que ya corrió.</p>';
    echo '</div>';
}

// ─── Ejecutar ────────────────────────────────────────────────────────────────

if (defined('WP_CLI') && WP_CLI) {
    // Modo WP-CLI: output directo
    $r = ytz_fix_virtual_tours_run();
    if (isset($r['error'])) {
        WP_CLI::error($r['error']);
    } else {
        foreach ($r['log'] as $line) {
            WP_CLI::line(strip_tags($line));
        }
        WP_CLI::success("Actualizados: {$r['fixed']} | Ya correctos: {$r['already']} | Omitidos: {$r['skipped']}");
    }
} else {
    // Modo Code Snippets: mostrar en WP Admin
    add_action('admin_notices', 'ytz_fix_virtual_tours_notice');
}
