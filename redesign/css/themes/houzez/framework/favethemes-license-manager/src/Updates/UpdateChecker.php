<?php

namespace FavethemesLicenseManager\Updates;

use FavethemesLicenseManager\Api\ApiClient;
use FavethemesLicenseManager\Core\Options;

/**
 * Theme update checker - integrates with WordPress update system
 *
 * When a license is activated, full update data (including download package) is fetched.
 * When unlicensed, only the latest version number is shown so users know an update exists,
 * with a prompt to activate their license.
 */
class UpdateChecker
{
    private Options $options;

    private ApiClient $apiClient;

    private const CACHE_DURATION = DAY_IN_SECONDS * 7; // 7-day fail-open cache

    private const PUBLIC_CACHE_KEY = 'flm_public_theme_version';

    private const PUBLIC_CACHE_DURATION = HOUR_IN_SECONDS * 12; // 12-hour cache

    public function __construct(Options $options, ApiClient $apiClient)
    {
        $this->options = $options;
        $this->apiClient = $apiClient;

        $this->initHooks();
    }

    private function initHooks(): void
    {
        // Hook into theme update check (SET filter — fires when wp_update_themes() writes the transient)
        add_filter('pre_set_site_transient_update_themes', [$this, 'checkForUpdates']);

        // Hook into theme update transient reads (GET filter — fires on every get_site_transient() call)
        add_filter('site_transient_update_themes', [$this, 'injectPublicUpdates']);

        // Hook into theme information
        add_filter('themes_api', [$this, 'getThemeInfo'], 10, 3);

        // Add after-update cleanup
        add_action('upgrader_process_complete', [$this, 'afterUpdate'], 10, 2);

        // Append license notice to theme update description for unlicensed users
        add_filter('wp_prepare_themes_for_js', [$this, 'appendThemeLicenseNotice']);

        // Allow local URLs for development environments
        if ($this->isLocalEnvironment()) {
            add_filter('http_request_host_is_external', [$this, 'allowLocalHosts'], 10, 3);
            add_filter('http_request_args', [$this, 'disableSslForLocalHosts'], 10, 2);
        }
    }

    /**
     * Check if running in a local development environment.
     */
    private function isLocalEnvironment(): bool
    {
        $apiHost = parse_url($this->apiClient->getApiUrl(), PHP_URL_HOST) ?? '';

        $localPatterns = ['.test', '.local', '.localhost', '.dev', 'localhost', '127.0.0.1'];

        foreach ($localPatterns as $pattern) {
            if (str_contains($apiHost, $pattern) || $apiHost === $pattern) {
                return true;
            }
        }

        return false;
    }

    /**
     * Allow WordPress to make HTTP requests to local development hosts.
     *
     * WordPress blocks requests to .test, .local, .localhost, .dev domains
     * by default. This filter allows them in development environments.
     *
     * @param  bool  $isExternal  Whether the host is external.
     * @param  string  $host  The requested host.
     * @param  string  $url  The requested URL.
     */
    public function allowLocalHosts(bool $isExternal, string $host, string $url): bool
    {
        // Allow requests to the API URL's host
        $apiHost = parse_url($this->apiClient->getApiUrl(), PHP_URL_HOST) ?? '';

        if ($host === $apiHost) {
            return true;
        }

        // Check if the download URL points to a known local development pattern
        $localPatterns = ['.test', '.local', '.localhost', '.dev'];
        foreach ($localPatterns as $pattern) {
            if (str_contains($host, $pattern)) {
                return true;
            }
        }

        return $isExternal;
    }

    /**
     * Disable SSL verification for local development hosts.
     *
     * Local development environments typically use self-signed SSL certificates
     * which WordPress cannot verify. This filter disables SSL verification
     * for requests to known local development domains.
     *
     * @param  array  $args  HTTP request arguments.
     * @param  string  $url  The requested URL.
     */
    public function disableSslForLocalHosts(array $args, string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';

        // Check if the URL points to a local development domain
        $localPatterns = ['.test', '.local', '.localhost', '.dev'];
        foreach ($localPatterns as $pattern) {
            if (str_contains($host, $pattern)) {
                $args['sslverify'] = false;

                return $args;
            }
        }

        return $args;
    }

    /**
     * Check for theme updates
     */
    public function checkForUpdates(object $transient): object
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        if ($this->options->isActivated()) {
            return $this->checkForUpdatesAuthenticated($transient);
        }

        return $this->checkForUpdatesPublic($transient);
    }

    /**
     * Authenticated update check — full update data with download package
     */
    private function checkForUpdatesAuthenticated(object $transient): object
    {
        $activation = $this->options->getActivation();
        $currentVersion = $this->getCurrentThemeVersion();

        // Try API first
        $response = $this->apiClient->checkForUpdates($activation['token']);

        if ($response['success'] && ! empty($response['data'])) {
            $updateData = $response['data'];

            // Only process if update_available is true and version exists
            if (! empty($updateData['update_available']) && isset($updateData['version'])) {
                // Cache the successful response
                $this->options->setUpdateCache($updateData, self::CACHE_DURATION);

                if (version_compare($updateData['version'], $currentVersion, '>')) {
                    $transient->response[FLM_THEME_SLUG] = [
                        'theme' => FLM_THEME_SLUG,
                        'new_version' => $updateData['version'],
                        'url' => $updateData['changelog_url'] ?? '',
                        'package' => $updateData['download_url'] ?? '',
                        'requires' => $updateData['requires_wp'] ?? '6.0',
                        'requires_php' => $updateData['requires_php'] ?? '8.0',
                    ];
                }
            }
        } else {
            // API failed - use cached data (fail-open)
            $cached = $this->options->getUpdateCache();
            if ($cached && version_compare($cached['version'], $currentVersion, '>')) {
                $transient->response[FLM_THEME_SLUG] = [
                    'theme' => FLM_THEME_SLUG,
                    'new_version' => $cached['version'],
                    'url' => $cached['changelog_url'] ?? '',
                    'package' => $cached['download_url'] ?? '',
                    'requires' => $cached['requires_wp'] ?? '6.0',
                    'requires_php' => $cached['requires_php'] ?? '8.0',
                ];
            }
        }

        return $transient;
    }

    /**
     * Inject public updates on transient GET (site_transient_update_themes).
     *
     * This fires every time any code reads the update_themes transient via get_site_transient().
     * It's the reliable injection point for unlicensed sites — the SET filter (pre_set_site_transient_)
     * fires inside wp_update_themes() during a batch HTTP context where our API call may fail.
     *
     * @param  mixed  $transient  The theme update transient (may be false on first call)
     * @return mixed Modified transient with public update injected
     */
    public function injectPublicUpdates($transient)
    {
        if (! is_object($transient)) {
            return $transient;
        }

        if ($this->options->isActivated()) {
            return $transient;
        }

        return $this->injectPublicThemeData($transient);
    }

    /**
     * Public update check for unlicensed sites (SET filter path).
     *
     * Calls getPublicThemeVersion() (no auth needed) and injects into the transient
     * with an empty package so WordPress shows "update available" but prevents download.
     */
    private function checkForUpdatesPublic(object $transient): object
    {
        return $this->injectPublicThemeData($transient);
    }

    /**
     * Shared logic for injecting public theme update data into the transient.
     *
     * Used by both the SET filter (checkForUpdatesPublic) and the GET filter (injectPublicUpdates).
     * Fetches latest theme version from cache or API, compares against installed version,
     * and injects update entry with empty package URL.
     */
    private function injectPublicThemeData(object $transient): object
    {
        // Try cached version first
        $latestVersion = get_transient(self::PUBLIC_CACHE_KEY);

        if ($latestVersion === false) {
            $response = $this->apiClient->getPublicThemeVersion();

            if ($response['success'] && ! empty($response['data']['version'])) {
                $latestVersion = $response['data']['version'];
                set_transient(self::PUBLIC_CACHE_KEY, $latestVersion, self::PUBLIC_CACHE_DURATION);
            } else {
                return $transient;
            }
        }

        $currentVersion = $this->getCurrentThemeVersion();

        if (version_compare($latestVersion, $currentVersion, '>')) {
            $transient->response[FLM_THEME_SLUG] = [
                'theme' => FLM_THEME_SLUG,
                'new_version' => $latestVersion,
                'url' => '',
                'package' => '', // Empty — prevents download
                'requires' => '6.0',
                'requires_php' => '8.0',
            ];
        }

        return $transient;
    }

    /**
     * Append license activation notice to theme update description.
     *
     * Hooks into wp_prepare_themes_for_js to add a message on the
     * Appearance > Themes overlay telling unlicensed users to activate.
     *
     * @param  array  $themes  Prepared theme data for JS
     * @return array Modified theme data
     */
    public function appendThemeLicenseNotice(array $themes): array
    {
        if ($this->options->isActivated()) {
            return $themes;
        }

        if (! isset($themes[FLM_THEME_SLUG]['hasUpdate']) || ! $themes[FLM_THEME_SLUG]['hasUpdate']) {
            return $themes;
        }

        $licensePageUrl = admin_url('admin.php?page=favethemes-license');
        $notice = sprintf(
            ' '.__('To update directly, %1$sactivate your license%2$s.', 'favethemes-license-manager'),
            '<a href="'.esc_url($licensePageUrl).'">',
            '</a>'
        );

        $themes[FLM_THEME_SLUG]['update'] = ($themes[FLM_THEME_SLUG]['update'] ?? '').$notice;

        return $themes;
    }

    /**
     * Get theme information for the WordPress themes API
     */
    public function getThemeInfo($result, string $action, object $args)
    {
        if ($action !== 'theme_information') {
            return $result;
        }

        if (! isset($args->slug) || $args->slug !== FLM_THEME_SLUG) {
            return $result;
        }

        if (! $this->options->isActivated()) {
            return $result;
        }

        $activation = $this->options->getActivation();
        $response = $this->apiClient->getLatestRelease($activation['token']);

        if (! $response['success'] || empty($response['data'])) {
            return $result;
        }

        $data = $response['data'];

        return (object) [
            'name' => ucfirst(FLM_THEME_SLUG),
            'slug' => FLM_THEME_SLUG,
            'version' => $data['version'] ?? '',
            'author' => '<a href="https://favethemes.com">Favethemes</a>',
            'author_profile' => 'https://favethemes.com',
            'requires' => $data['requires_wp'] ?? '6.0',
            'requires_php' => $data['requires_php'] ?? '8.0',
            'tested' => $data['tested_wp'] ?? '',
            'last_updated' => $data['published_at'] ?? '',
            'download_link' => $data['download_url'] ?? '',
            'sections' => [
                'description' => $data['description'] ?? 'Favethemes Premium WordPress Theme',
                'changelog' => $data['changelog'] ?? '',
            ],
        ];
    }

    /**
     * Cleanup after successful update
     */
    public function afterUpdate(object $upgrader, array $options): void
    {
        if ($options['type'] !== 'theme') {
            return;
        }

        if (! isset($options['themes']) || ! in_array(FLM_THEME_SLUG, $options['themes'])) {
            return;
        }

        // Clear update caches after successful theme update
        delete_transient('flm_update_cache');
        delete_transient('flm_plugin_updates_cache');
        delete_transient(self::PUBLIC_CACHE_KEY);

        // One-time: clear stale update_plugins transient from pre-FLM update system
        if (! get_option('flm_v430_plugins_transient_cleared')) {
            delete_site_transient('update_plugins');
            update_option('flm_v430_plugins_transient_cleared', true, false);
        }

        // Send heartbeat with new version
        if ($this->options->isActivated()) {
            $activation = $this->options->getActivation();
            $this->apiClient->sendHeartbeat($activation['token']);
        }
    }

    /**
     * Get current theme version
     */
    private function getCurrentThemeVersion(): string
    {
        $theme = wp_get_theme(FLM_THEME_SLUG);
        if ($theme->exists()) {
            return $theme->get('Version');
        }

        $theme = wp_get_theme();
        if ($theme->parent()) {
            return $theme->parent()->get('Version');
        }

        return $theme->get('Version');
    }
}
