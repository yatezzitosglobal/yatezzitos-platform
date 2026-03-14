<?php

namespace FavethemesLicenseManager\Core;

/**
 * Plugin deactivation handler
 */
class Deactivator
{
    public static function deactivate(): void
    {
        // Clear scheduled cron events
        $timestamp = wp_next_scheduled('flm_heartbeat_cron');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'flm_heartbeat_cron');
        }

        // Note: We don't delete options on deactivation
        // Options are only deleted on uninstall
    }
}
