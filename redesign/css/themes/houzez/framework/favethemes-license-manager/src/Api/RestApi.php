<?php

namespace FavethemesLicenseManager\Api;

use FavethemesLicenseManager\Core\Options;
use WP_REST_Request;
use WP_REST_Response;

/**
 * REST API endpoints for remote management
 */
class RestApi
{
    private Options $options;

    public function __construct(Options $options)
    {
        $this->options = $options;
        add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    public function registerRoutes(): void
    {
        register_rest_route('favethemes-license/v1', '/remote-deactivate', [
            'methods' => 'POST',
            'callback' => [$this, 'handleRemoteDeactivate'],
            'permission_callback' => '__return_true', // Token-based auth
        ]);
    }

    /**
     * Handle remote deactivation request from the portal.
     *
     * This endpoint is called by the Favethemes Store portal when a user
     * deactivates their site from their account. It clears the local
     * license data so the site reflects the deactivated state.
     */
    public function handleRemoteDeactivate(WP_REST_Request $request): WP_REST_Response
    {
        // Rate limiting: 5 attempts per minute per IP
        $ip = $this->getClientIp($request);
        $transientKey = 'flm_rd_'.md5($ip);
        $attempts = (int) get_transient($transientKey);

        if ($attempts >= 5) {
            return new WP_REST_Response([
                'success' => false,
                'error' => 'rate_limited',
            ], 429);
        }

        $token = $request->get_param('token');
        $action = $request->get_param('action');

        if ($action !== 'deactivate' || empty($token) || ! is_string($token)) {
            set_transient($transientKey, $attempts + 1, 60);

            return new WP_REST_Response([
                'success' => false,
                'error' => 'invalid_request',
            ], 400);
        }

        // Timing-safe token comparison to prevent timing attacks
        $activation = $this->options->getActivation();
        if (empty($activation['token']) || ! hash_equals($activation['token'], $token)) {
            set_transient($transientKey, $attempts + 1, 60);

            return new WP_REST_Response([
                'success' => false,
                'error' => 'invalid_token',
            ], 403);
        }

        // Valid request — clear local data, update caches, and reset rate limit
        $this->options->clearAll();
        delete_transient('flm_plugin_updates_cache');
        delete_transient('flm_update_cache');
        delete_transient('flm_public_plugin_versions');
        delete_transient('flm_public_theme_version');
        delete_site_transient('update_plugins');
        delete_site_transient('update_themes');
        delete_transient($transientKey);

        return new WP_REST_Response(['success' => true]);
    }

    /**
     * Get client IP address from request headers.
     */
    private function getClientIp(WP_REST_Request $request): string
    {
        $forwarded = $request->get_header('X-Forwarded-For');
        if ($forwarded) {
            return sanitize_text_field(explode(',', $forwarded)[0]);
        }

        return sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
    }
}
