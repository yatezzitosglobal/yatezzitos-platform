<?php

namespace FavethemesLicenseManager\Core;

use FavethemesLicenseManager\Admin\AdminPage;
use FavethemesLicenseManager\Admin\AjaxHandlers;
use FavethemesLicenseManager\Admin\PluginsPage;
use FavethemesLicenseManager\Admin\UpdateNotice;
use FavethemesLicenseManager\Api\ApiClient;
use FavethemesLicenseManager\Api\RestApi;
use FavethemesLicenseManager\Updates\PluginUpdateChecker;
use FavethemesLicenseManager\Updates\UpdateChecker;

/**
 * Main plugin class (Singleton)
 */
class Plugin
{
    private static ?Plugin $instance = null;

    private Options $options;

    private ApiClient $apiClient;

    private function __construct()
    {
        $this->options = new Options;
        $this->apiClient = new ApiClient($this->options);

        $this->initHooks();
    }

    public static function getInstance(): Plugin
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function initHooks(): void
    {
        // Load text domain
        add_action('init', [$this, 'loadTextDomain']);

        // Admin hooks
        if (is_admin()) {
            new AdminPage($this->options, $this->apiClient);
            new AjaxHandlers($this->options, $this->apiClient);
            new PluginsPage($this->options, $this->apiClient);
            new UpdateNotice($this->options);
        }

        // Theme update checker
        new UpdateChecker($this->options, $this->apiClient);

        // Plugin update checker (for bundled plugins)
        new PluginUpdateChecker($this->options, $this->apiClient);

        // REST API endpoints for remote management
        new RestApi($this->options);

        // Multisite compatibility
        new Multisite($this->options);

        // Heartbeat cron
        add_action('flm_heartbeat_cron', [$this, 'sendHeartbeat']);

        // Schedule heartbeat if not scheduled
        if (! wp_next_scheduled('flm_heartbeat_cron')) {
            wp_schedule_event(time(), 'daily', 'flm_heartbeat_cron');
        }
    }

    public function loadTextDomain(): void
    {
        if (defined('FLM_THEME_MODE') && FLM_THEME_MODE) {
            // In theme mode, load from the theme's FLM languages directory
            load_theme_textdomain(
                'favethemes-license-manager',
                FLM_PLUGIN_DIR.'languages'
            );
        } else {
            load_plugin_textdomain(
                'favethemes-license-manager',
                false,
                dirname(FLM_PLUGIN_BASENAME).'/languages'
            );
        }
    }

    public function sendHeartbeat(): void
    {
        $activation = $this->options->getActivation();
        if (empty($activation['token'])) {
            return;
        }

        $response = $this->apiClient->sendHeartbeat($activation['token']);

        // If the activation no longer exists on the server, clear local data
        if (! $response['success'] && in_array($response['code'] ?? '', ['ACTIVATION_NOT_FOUND', 'ACTIVATION_DEACTIVATED', 'ACTIVATION_EXPIRED'])) {
            $this->options->clearAll();
            delete_transient('flm_plugin_updates_cache');
            delete_transient('flm_update_cache');
            delete_transient('flm_public_plugin_versions');
            delete_transient('flm_public_theme_version');
            delete_site_transient('update_plugins');
            delete_site_transient('update_themes');
        }
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getApiClient(): ApiClient
    {
        return $this->apiClient;
    }
}
