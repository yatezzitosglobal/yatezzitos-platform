<?php
/**
 * Favethemes License Manager - Theme Integration Loader
 * Loads FLM from within the theme instead of as a standalone plugin.
 */
defined('ABSPATH') || exit;

// Define FLM constants for theme context
define('FLM_VERSION', '1.0.0');
define('FLM_PLUGIN_FILE', __FILE__);
define('FLM_PLUGIN_DIR', get_template_directory() . '/framework/favethemes-license-manager/');
define('FLM_PLUGIN_URL', get_template_directory_uri() . '/framework/favethemes-license-manager/');
define('FLM_PLUGIN_BASENAME', 'favethemes-license-manager/favethemes-license-manager.php');
define('FLM_PARENT_MENU_SLUG', 'houzez_dashboard');
define('FLM_THEME_MODE', true);

// API Configuration (can be overridden in wp-config.php)
if (!defined('FLM_API_URL')) {
    define('FLM_API_URL', 'https://app.favethemes.com/api/v1');
}
if (!defined('FLM_PORTAL_URL')) {
    define('FLM_PORTAL_URL', 'https://app.favethemes.com');
}

// FLM_PRODUCT_SLUG is already defined in functions.php
if (!defined('FLM_THEME_SLUG')) {
    define('FLM_THEME_SLUG', FLM_PRODUCT_SLUG);
}

// Load autoloader
require_once FLM_PLUGIN_DIR . 'src/Autoloader.php';
\FavethemesLicenseManager\Autoloader::register();

// Run activator on first load (replaces plugin activation hook)
if (!get_option('flm_activated')) {
    \FavethemesLicenseManager\Core\Activator::activate();
    update_option('flm_activated', true);
}

// Initialize FLM
\FavethemesLicenseManager\Core\Plugin::getInstance();
