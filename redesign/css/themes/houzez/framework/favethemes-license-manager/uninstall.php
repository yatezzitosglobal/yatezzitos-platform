<?php

/**
 * Favethemes License Manager Uninstall
 *
 * Fired when the plugin is deleted.
 */

// If uninstall not called from WordPress, exit
if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all plugin options
delete_option('flm_license_data');
delete_option('flm_activation_data');
delete_option('flm_user_data');
delete_option('flm_v430_plugins_transient_cleared');
delete_transient('flm_update_cache');
delete_transient('flm_plugin_updates_cache');
delete_transient('flm_plugin_slugs');
delete_transient('flm_public_plugin_versions');
delete_transient('flm_public_theme_version');

// Clear scheduled cron events
$timestamp = wp_next_scheduled('flm_heartbeat_cron');
if ($timestamp) {
    wp_unschedule_event($timestamp, 'flm_heartbeat_cron');
}

// For multisite, clean up all sites
if (is_multisite()) {
    $sites = get_sites(['fields' => 'ids']);
    foreach ($sites as $site_id) {
        switch_to_blog($site_id);
        delete_option('flm_license_data');
        delete_option('flm_activation_data');
        delete_option('flm_user_data');
        delete_option('flm_v430_plugins_transient_cleared');
        delete_transient('flm_update_cache');
        delete_transient('flm_plugin_updates_cache');
        delete_transient('flm_plugin_slugs');
        delete_transient('flm_public_plugin_versions');
        delete_transient('flm_public_theme_version');
        restore_current_blog();
    }
}
