<?php

namespace FavethemesLicenseManager\Api;

use FavethemesLicenseManager\Core\Options;
use FavethemesLicenseManager\Core\ThemeDetector;

/**
 * API client for Favethemes Portal communication
 */
class ApiClient
{
    private Options $options;

    private string $apiUrl;

    public function __construct(Options $options)
    {
        $this->options = $options;
        $this->apiUrl = defined('FLM_API_URL') ? FLM_API_URL : 'https://app.favethemes.com/api/v1';
    }

    /**
     * Make an API request
     */
    private function request(string $method, string $endpoint, array $data = [], ?string $token = null): array
    {
        $url = $this->apiUrl.$endpoint;

        $args = [
            'method' => $method,
            'timeout' => 30,
            'sslverify' => $this->shouldVerifySsl(),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-Plugin-Version' => FLM_VERSION,
            ],
        ];

        if ($token) {
            $args['headers']['Authorization'] = 'Bearer '.$token;
        }

        if (! empty($data) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $args['body'] = wp_json_encode($data);
        }

        if ($method === 'GET' && ! empty($data)) {
            $url = add_query_arg($data, $url);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return [
                'success' => false,
                'error' => $response->get_error_message(),
                'code' => 'REQUEST_FAILED',
            ];
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);

        if ($statusCode >= 400) {
            return [
                'success' => false,
                'error' => $decoded['message'] ?? $decoded['error'] ?? 'Unknown error',
                'code' => $decoded['code'] ?? 'API_ERROR',
                'status' => $statusCode,
            ];
        }

        return [
            'success' => true,
            'data' => $decoded,
            'status' => $statusCode,
        ];
    }

    /**
     * Create a connect session for handshake
     *
     * When $source is null, the portal will handle source selection.
     * This enables the simplified "Connect & Activate" flow where users
     * choose their license source (Envato/Direct) on the portal side.
     */
    public function createConnectSession(?string $source, array $payload = []): array
    {
        $data = [
            'product_slug' => ThemeDetector::getSlug(),
            'wp_site_url' => get_site_url(),
            'wp_callback_url' => $this->getCallbackUrl(),
        ];

        // Include source only if specified (for backwards compatibility)
        if ($source !== null) {
            $data['source'] = $source;
        }

        // Build payload - environment hint helps portal pre-select the right option
        $payloadData = [
            'environment_hint' => $payload['environment_hint'] ?? ($this->options->detectStagingEnvironment() ? 'staging' : 'live'),
            'wp_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'theme_version' => $this->getThemeVersion(),
            'is_multisite' => is_multisite(),
        ];

        // Include any additional payload data (for backwards compatibility with Envato flow)
        if (! empty($payload['purchase_code'])) {
            $payloadData['purchase_code'] = $payload['purchase_code'];
        }
        if (! empty($payload['envato_username'])) {
            $payloadData['envato_username'] = $payload['envato_username'];
        }
        if (! empty($payload['environment'])) {
            $payloadData['requested_environment'] = $payload['environment'];
        }

        $data['payload'] = $payloadData;

        return $this->request('POST', '/connect/sessions', $data);
    }

    /**
     * Get the callback URL for connect flow
     */
    private function getCallbackUrl(): string
    {
        return admin_url('admin.php?page=favethemes-license&flm_callback=1');
    }

    /**
     * Finalize activation after portal callback
     */
    public function finalizeActivation(string $callbackToken): array
    {
        $data = [
            'callback_token' => $callbackToken,
            'fingerprint' => $this->options->generateFingerprint(),
        ];

        return $this->request('POST', '/connect/finalize', $data);
    }

    /**
     * Verify current activation status
     */
    public function verifyActivation(string $token): array
    {
        $data = [
            'token' => $token,
            'fingerprint_hash' => $this->options->generateFingerprint(),
            'theme_version' => $this->getThemeVersion(),
            'wp_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
        ];

        return $this->request('POST', '/activations/verify', $data);
    }

    /**
     * Deactivate current site
     */
    public function deactivate(string $token): array
    {
        $data = [
            'token' => $token,
        ];

        return $this->request('POST', '/activations/deactivate', $data);
    }

    /**
     * Send heartbeat to update last_seen_at
     */
    public function sendHeartbeat(string $token): array
    {
        $data = [
            'token' => $token,
            'fingerprint_hash' => $this->options->generateFingerprint(),
            'wp_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'theme_version' => $this->getThemeVersion(),
        ];

        return $this->request('POST', '/activations/heartbeat', $data);
    }

    /**
     * Check for theme updates
     */
    public function checkForUpdates(string $token): array
    {
        $data = [
            'token' => $token,
            'current_version' => $this->getThemeVersion(),
        ];

        return $this->request('GET', '/updates/check', $data);
    }

    /**
     * Get latest theme version info
     */
    public function getLatestRelease(string $token): array
    {
        return $this->request('GET', '/downloads/theme/latest', [], $token);
    }

    /**
     * Check for plugin updates
     *
     * @param  string  $token  Activation token
     * @param  array  $plugins  List of installed plugins [['file' => 'slug/file.php', 'version' => '1.0.0'], ...]
     * @return array API response
     */
    public function checkForPluginUpdates(string $token, array $plugins): array
    {
        $data = [
            'token' => $token,
            'plugins' => $plugins,
            'theme_version' => $this->getThemeVersion(),
        ];

        return $this->request('POST', '/plugins/updates/check', $data);
    }

    /**
     * Get plugin information for WordPress plugins_api
     *
     * @param  string  $token  Activation token
     * @param  string  $slug  Plugin slug
     * @return array API response
     */
    public function getPluginInfo(string $token, string $slug): array
    {
        $data = [
            'token' => $token,
            'slug' => $slug,
            'theme_version' => $this->getThemeVersion(),
        ];

        return $this->request('GET', '/plugins/info', $data);
    }

    /**
     * Get the latest published theme version (no auth required).
     *
     * @return array API response with version string
     */
    public function getPublicThemeVersion(): array
    {
        return $this->request('GET', '/updates/latest-version', [
            'product_slug' => ThemeDetector::getSlug(),
        ]);
    }

    /**
     * Get list of bundled plugin slugs from Portal
     *
     * @return array API response with plugin slugs
     */
    public function getPluginSlugs(): array
    {
        $data = [
            'product_slug' => ThemeDetector::getSlug(),
        ];

        return $this->request('GET', '/plugins/slugs', $data);
    }

    /**
     * Get all available plugins for the in-theme installer
     *
     * @param  string  $token  Activation token
     * @param  array  $installed  List of installed plugins [['slug' => 'name', 'version' => '1.0.0'], ...]
     * @return array API response with plugins list
     */
    public function getPortalPlugins(string $token, array $installed = []): array
    {
        $data = [
            'token' => $token,
            'theme_version' => $this->getThemeVersion(),
        ];

        if (! empty($installed)) {
            $data['installed'] = $installed;
        }

        return $this->request('POST', '/plugins/list', $data);
    }

    /**
     * Get current theme version (parent theme if child is active)
     */
    public function getThemeVersion(): string
    {
        $theme = wp_get_theme(FLM_THEME_SLUG);
        if ($theme->exists()) {
            return $theme->get('Version');
        }

        // Try parent theme
        $theme = wp_get_theme();
        if ($theme->parent()) {
            return $theme->parent()->get('Version');
        }

        return $theme->get('Version');
    }

    /**
     * Get the API base URL.
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Get portal URL for connect flow
     */
    public function getPortalConnectUrl(string $sessionToken): string
    {
        $portalUrl = defined('FLM_PORTAL_URL') ? FLM_PORTAL_URL : 'https://app.favethemes.com';

        return $portalUrl.'/connect?token='.urlencode($sessionToken);
    }

    /**
     * Get portal URL for managing license
     */
    public function getPortalManageUrl(): string
    {
        $portalUrl = defined('FLM_PORTAL_URL') ? FLM_PORTAL_URL : 'https://app.favethemes.com';

        return $portalUrl.'/dashboard/licenses';
    }

    /**
     * Determine if SSL should be verified based on environment
     */
    private function shouldVerifySsl(): bool
    {
        // Allow manual override
        if (defined('FLM_SSL_VERIFY')) {
            return FLM_SSL_VERIFY;
        }

        // Auto-detect localhost/development environments
        $localPatterns = [
            '.test',
            '.local',
            '.localhost',
            '.dev',
            'localhost',
            '127.0.0.1',
            '::1',
        ];

        // Check API URL
        $apiHost = parse_url($this->apiUrl, PHP_URL_HOST) ?? '';
        foreach ($localPatterns as $pattern) {
            if (str_contains($apiHost, $pattern) || $apiHost === $pattern) {
                return false;
            }
        }

        // Check WordPress site URL
        $siteHost = parse_url(get_site_url(), PHP_URL_HOST) ?? '';
        foreach ($localPatterns as $pattern) {
            if (str_contains($siteHost, $pattern) || $siteHost === $pattern) {
                return false;
            }
        }

        return true;
    }
}
