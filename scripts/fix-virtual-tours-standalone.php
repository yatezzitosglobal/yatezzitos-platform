<?php
/**
 * INSTRUCCIONES:
 * 1. Subir este archivo a la raíz de WordPress (donde está wp-config.php)
 * 2. Visitarlo en el navegador: https://yatezzitos.com/fix-virtual-tours-standalone.php
 * 3. Ver el resultado en pantalla
 * 4. ELIMINAR el archivo inmediatamente después
 */

// Cargar WordPress
require_once __DIR__ . '/wp-load.php';

// Seguridad básica: solo puede correr si estás logueado como admin

if (!current_user_can('manage_options')) {
    wp_die('Acceso denegado. Debes estar logueado como administrador.');
}

$ids = get_posts([
    'post_type'      => 'property',
    'post_status'    => ['publish', 'draft', 'private'],
    'posts_per_page' => -1,
    'fields'         => 'ids',
]);

$fixed   = 0;
$already = 0;
$skipped = 0;
$log     = [];

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
        $log[] = ['status' => 'ok', 'id' => $post_id, 'title' => get_the_title($post_id), 'url' => $clean_url];
        $fixed++;
    } else {
        $log[] = ['status' => 'warn', 'id' => $post_id, 'title' => get_the_title($post_id), 'raw' => substr($value, 0, 150)];
        $skipped++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Fix Virtual Tours</title>
<style>
  body { font-family: monospace; padding: 30px; background: #f1f1f1; }
  h2 { color: #2c3e50; }
  .ok { color: #27ae60; }
  .warn { color: #e67e22; }
  .summary { background: #fff; padding: 20px; border-left: 5px solid #27ae60; margin-bottom: 20px; }
  .entry { background: #fff; padding: 10px 15px; margin: 5px 0; border-radius: 3px; }
  .delete-warning { background: #e74c3c; color: #fff; padding: 15px; margin-top: 30px; border-radius: 5px; font-size: 14px; }
</style>
</head>
<body>
<h2>Fix Virtual Tours — Yatezzitos</h2>

<div class="summary">
  <strong>Total properties:</strong> <?= count($ids) ?><br>
  <strong class="ok">✓ Actualizados:</strong> <?= $fixed ?><br>
  <strong>Ya correctos (sin cambios):</strong> <?= $already ?><br>
  <strong class="warn">⚠ Omitidos (vacíos o formato no reconocido):</strong> <?= $skipped ?>
</div>

<?php foreach ($log as $entry): ?>
  <div class="entry">
    <?php if ($entry['status'] === 'ok'): ?>
      <span class="ok">✓</span> [<?= $entry['id'] ?>] <?= esc_html($entry['title']) ?><br>
      <small>→ <?= esc_html($entry['url']) ?></small>
    <?php else: ?>
      <span class="warn">⚠</span> [<?= $entry['id'] ?>] <?= esc_html($entry['title']) ?> — script no reconocido<br>
      <small><?= esc_html($entry['raw']) ?></small>
    <?php endif; ?>
  </div>
<?php endforeach; ?>

<div class="delete-warning">
  ⚠️ <strong>IMPORTANTE:</strong> Elimina este archivo del servidor ahora que ya corrió.<br>
  Ruta: <code><?= __FILE__ ?></code>
</div>
</body>
</html>
