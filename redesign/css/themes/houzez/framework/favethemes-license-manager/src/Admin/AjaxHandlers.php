<?php

namespace FavethemesLicenseManager\Admin;

use FavethemesLicenseManager\Api\ApiClient;
use FavethemesLicenseManager\Core\Options;

/**
 * AJAX request handlers
 *
 * Simplified flow: Single "Connect & Activate" button that redirects to portal.
 * License source selection (Envato/Direct) and environment selection happen on the portal.
 */
class AjaxHandlers
{
    private Options $options;

    private ApiClient $apiClient;

    public function __construct(Options $options, ApiClient $apiClient)
    {
        $this->options = $options;
        $this->apiClient = $apiClient;

        $this->registerHandlers();
    }

    private function registerHandlers(): void
    {
        add_action('wp_ajax_flm_get_oauth_url', [$this, 'handleGetOAuthUrl']);
        add_action('wp_ajax_flm_deactivate', [$this, 'handleDeactivation']);
        add_action('wp_ajax_flm_force_deactivate', [$this, 'handleForceDeactivation']);
        add_action('wp_ajax_flm_verify_status', [$this, 'handleVerifyStatus']);
    }

    /**
     * Verify AJAX request nonce and capability
     */
    private function verifyRequest(): bool
    {
        if (! check_ajax_referer('flm_admin_nonce', 'nonce', false)) {
            wp_send_json_error([
                'message' => __('Security verification failed. Please refresh the page and try again.', 'favethemes-license-manager'),
            ], 403);

            return false;
        }

        if (! current_user_can('manage_options')) {
            wp_send_json_error([
                'message' => __('You do not have permission to perform this action.', 'favethemes-license-manager'),
            ], 403);

            return false;
        }

        return true;
    }

    /**
     * Get OAuth URL for license activation
     *
     * Creates a connect session and returns the portal URL.
     * License source and environment selection happen on the portal side.
     */
    public function handleGetOAuthUrl(): void
    {
        $this->verifyRequest();

        // Auto-detect environment as a hint for the portal
        $environmentHint = $this->options->detectStagingEnvironment() ? 'staging' : 'live';

        // Create connect session - portal will handle source/environment selection
        $response = $this->apiClient->createConnectSession(null, [
            'environment_hint' => $environmentHint,
        ]);

        if (! $response['success']) {
            wp_send_json_error([
                'message' => $response['error'] ?? __('Failed to connect to portal. Please try again.', 'favethemes-license-manager'),
                'code' => $response['code'] ?? 'CONNECTION_FAILED',
            ]);
        }

        $data = $response['data'];

        if (empty($data['token'])) {
            wp_send_json_error([
                'message' => __('Invalid response from server. Please try again.', 'favethemes-license-manager'),
            ]);
        }

        // Build portal URL - callback URL is already included in the session
        $portalUrl = $this->apiClient->getPortalConnectUrl($data['token']);

        wp_send_json_success([
            'url' => $portalUrl,
        ]);
    }

    /**
     * Handle license deactivation
     */
    public function handleDeactivation(): void
    {
        $this->verifyRequest();

        if (! $this->options->isActivated()) {
            wp_send_json_error([
                'message' => __('No active license found.', 'favethemes-license-manager'),
            ]);
        }

        $activation = $this->options->getActivation();

        // Call API to deactivate
        $response = $this->apiClient->deactivate($activation['token']);

        if (! $response['success']) {
            // If activation not found on server, allow local deactivation
            if ($response['code'] === 'ACTIVATION_NOT_FOUND') {
                $this->options->clearAll();
                $this->clearUpdateCaches();
                wp_send_json_success([
                    'message' => __('License deactivated successfully.', 'favethemes-license-manager'),
                ]);
            }

            // For other errors, suggest force deactivate option
            wp_send_json_error([
                'message' => $response['error'] ?? __('Failed to deactivate license. Please try again.', 'favethemes-license-manager'),
                'show_force_option' => true,
            ]);
        }

        // Clear local data
        $this->options->clearAll();
        $this->clearUpdateCaches();

        wp_send_json_success([
            'message' => __('License deactivated successfully.', 'favethemes-license-manager'),
        ]);
    }

    /**
     * Handle force deactivation (local only, no API call)
     *
     * Use this when the portal API is unreachable or the activation
     * has already been removed server-side but local data persists.
     */
    public function handleForceDeactivation(): void
    {
        $this->verifyRequest();

        if (! $this->options->isActivated()) {
            wp_send_json_error([
                'message' => __('No active license found.', 'favethemes-license-manager'),
            ]);
        }

        // Clear local data without calling API
        $this->options->clearAll();
        $this->clearUpdateCaches();

        wp_send_json_success([
            'message' => __('License data cleared successfully.', 'favethemes-license-manager'),
        ]);
    }

    /**
     * Clear all update-related caches so public/authenticated checks refresh.
     */
    private function clearUpdateCaches(): void
    {
        delete_transient('flm_plugin_updates_cache');
        delete_transient('flm_update_cache');
        delete_transient('flm_public_plugin_versions');
        delete_transient('flm_public_theme_version');
        delete_site_transient('update_plugins');
        delete_site_transient('update_themes');
    }

    /**
     * Verify current activation status
     */
    public function handleVerifyStatus(): void
    {
        $this->verifyRequest();

        if (! $this->options->isActivated()) {
            wp_send_json_error([
                'message' => __('No active license found.', 'favethemes-license-manager'),
                'status' => 'inactive',
            ]);
        }

        $activation = $this->options->getActivation();
        $response = $this->apiClient->verifyActivation($activation['token']);

        if (! $response['success']) {
            // If verification fails, the license might be revoked/expired
            if (in_array($response['code'] ?? '', ['LICENSE_EXPIRED', 'LICENSE_REVOKED', 'ACTIVATION_NOT_FOUND', 'ACTIVATION_DEACTIVATED'])) {
                $this->options->clearAll();
                $this->clearUpdateCaches();
            }

            wp_send_json_error([
                'message' => $response['error'] ?? __('License verification failed.', 'favethemes-license-manager'),
                'code' => $response['code'] ?? 'VERIFICATION_FAILED',
                'status' => 'invalid',
            ]);
        }

        wp_send_json_success([
            'message' => __('License is valid and active.', 'favethemes-license-manager'),
            'status' => 'active',
            'data' => $response['data'] ?? [],
        ]);
    }
}
