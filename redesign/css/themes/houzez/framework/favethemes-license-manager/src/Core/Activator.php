<?php

namespace FavethemesLicenseManager\Core;

/**
 * Plugin activation handler
 */
class Activator
{
    public static function activate(): void
    {
        // Set default options if not exists
        if (get_option('flm_license_data') === false) {
            add_option('flm_license_data', '');
        }

        if (get_option('flm_activation_data') === false) {
            add_option('flm_activation_data', '');
        }

        // Schedule heartbeat cron
        if (! wp_next_scheduled('flm_heartbeat_cron')) {
            wp_schedule_event(time(), 'daily', 'flm_heartbeat_cron');
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
