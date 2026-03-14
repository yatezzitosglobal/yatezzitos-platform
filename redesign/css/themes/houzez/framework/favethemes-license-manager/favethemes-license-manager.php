<?php

/**
 * Plugin Name: Favethemes License Manager
 * Plugin URI: https://favethemes.com
 * Description: License management and automatic updates for Favethemes products (Houzez, Homey, and more)
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Favethemes
 * Author URI: https://favethemes.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: favethemes-license-manager
 * Domain Path: /languages
 */
defined('ABSPATH') || exit;

// Plugin constants
define('FLM_VERSION', '1.0.0');
define('FLM_PLUGIN_FILE', __FILE__);
define('FLM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FLM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FLM_PLUGIN_BASENAME', plugin_basename(__FILE__));

// API Configuration - can be overridden in wp-config.php
if (! defined('FLM_API_URL')) {
    define('FLM_API_URL', 'https://app.favethemes.com/api/v1');
}
if (! defined('FLM_PORTAL_URL')) {
    define('FLM_PORTAL_URL', 'https://app.favethemes.com');
}

// Autoloader (must be loaded before ThemeDetector)
require_once FLM_PLUGIN_DIR.'src/Autoloader.php';
\FavethemesLicenseManager\Autoloader::register();

// Theme slug detection - reads FLM_PRODUCT_SLUG from theme's functions.php
if (! defined('FLM_THEME_SLUG')) {
    define('FLM_THEME_SLUG', \FavethemesLicenseManager\Core\ThemeDetector::getSlug());
}

// Initialize plugin
function flm_init(): void
{
    \FavethemesLicenseManager\Core\Plugin::getInstance();
}
add_action('plugins_loaded', 'flm_init');

// Activation hook
register_activation_hook(__FILE__, function (): void {
    \FavethemesLicenseManager\Core\Activator::activate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function (): void {
    \FavethemesLicenseManager\Core\Deactivator::deactivate();
});
