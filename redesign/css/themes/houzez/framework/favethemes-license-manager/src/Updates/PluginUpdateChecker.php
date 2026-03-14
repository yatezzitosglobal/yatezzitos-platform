<?php

namespace FavethemesLicenseManager\Updates;

use FavethemesLicenseManager\Api\ApiClient;
use FavethemesLicenseManager\Core\Options;

/**
 * Plugin update checker - integrates with WordPress plugin update system
 *
 * WordPress identifies plugins by their file path (e.g., houzez-login-register/houzez-login-register.php).
 * This class hooks into WordPress update checks to provide updates for bundled Favethemes plugins.
 *
 * When a license is activated, full update data (including download packages) is fetched.
 * When unlicensed, only portal plugin version numbers are shown so users know updates exist,
 * with a prompt to activate their license.
 */
class PluginUpdateChecker
{
    private Options $options;

    private ApiClient $apiClient;

    private const CACHE_KEY = 'flm_plugin_updates_cache';

    private const PUBLIC_CACHE_KEY = 'flm_public_plugin_versions';

    private const CACHE_DURATION = DAY_IN_SECONDS * 7; // 7-day fail-open cache

    private const PUBLIC_CACHE_DURATION = HOUR_IN_SECONDS * 12; // 12-hour cache for public checks

    public function __construct(Options $options, ApiClient $apiClient)
    {
        $this->options = $options;
        $this->apiClient = $apiClient;

        $this->initHooks();
    }

    private function initHooks(): void
    {
        // Hook into plugin update check (SET filter — fires when wp_update_plugins() writes the transient)
        add_filter('pre_set_site_transient_update_plugins', [$this, 'checkForUpdates']);

        // Hook into plugin update transient reads (GET filter — fires on every get_site_transient() call)
        // This is more reliable than the SET filter for unlicensed sites because the SET filter
        // fires inside wp_update_plugins() during a batch HTTP context where concurrent API calls may fail.
        add_filter('site_transient_update_plugins', [$this, 'injectPublicUpdates']);

        // Hook into plugin information (for "View details" link)
        add_filter('plugins_api', [$this, 'getPluginInfo'], 10, 3);

        // Add after-update cleanup
        add_action('upgrader_process_complete', [$this, 'afterUpdate'], 10, 2);

        // Register update message hooks for unlicensed users
        add_action('admin_init', [$this, 'registerUpdateMessageHooks']);
    }

    /**
     * Check for plugin updates
     *
     * @param  object  $transient  The plugin update transient
     * @return object Modified transient with our plugin updates
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
     * Authenticated update check — full update data with download packages
     */
    private function checkForUpdatesAuthenticated(object $transient): object
    {
        $activation = $this->options->getActivation();

        // Build list of installed bundled plugins
        $installedPlugins = $this->getInstalledBundledPlugins();

        if (empty($installedPlugins)) {
            return $transient;
        }

        // Try API first
        $response = $this->apiClient->checkForPluginUpdates($activation['token'], $installedPlugins);

        if ($response['success'] && ! empty($response['data']['updates'])) {
            $updates = $response['data']['updates'];

            // Cache the successful response
            set_transient(self::CACHE_KEY, $updates, self::CACHE_DURATION);

            foreach ($updates as $pluginFile => $updateData) {
                $currentVersion = $transient->checked[$pluginFile] ?? '0';

                if (version_compare($updateData['new_version'], $currentVersion, '>')) {
                    $transient->response[$pluginFile] = (object) [
                        'id' => $updateData['id'] ?? $updateData['slug'],
                        'slug' => $updateData['slug'],
                        'plugin' => $pluginFile,
                        'new_version' => $updateData['new_version'],
                        'url' => $updateData['url'] ?? '',
                        'package' => $updateData['package'] ?? '',
                        'icons' => $updateData['icons'] ?? [],
                        'banners' => $updateData['banners'] ?? [],
                        'banners_rtl' => $updateData['banners_rtl'] ?? [],
                        'requires' => $updateData['requires'] ?? '6.0',
                        'tested' => $updateData['tested'] ?? '',
                        'requires_php' => $updateData['requires_php'] ?? '8.0',
                        'compatibility' => $updateData['compatibility'] ?? new \stdClass,
                    ];
                }
            }
        } else {
            // API failed - use cached data (fail-open)
            $cached = get_transient(self::CACHE_KEY);

            if ($cached && is_array($cached)) {
                foreach ($cached as $pluginFile => $updateData) {
                    $currentVersion = $transient->checked[$pluginFile] ?? '0';

                    if (version_compare($updateData['new_version'], $currentVersion, '>')) {
                        $transient->response[$pluginFile] = (object) [
                            'id' => $updateData['id'] ?? $updateData['slug'],
                            'slug' => $updateData['slug'],
                            'plugin' => $pluginFile,
                            'new_version' => $updateData['new_version'],
                            'url' => $updateData['url'] ?? '',
                            'package' => $updateData['package'] ?? '',
                            'icons' => $updateData['icons'] ?? [],
                            'banners' => $updateData['banners'] ?? [],
                            'banners_rtl' => $updateData['banners_rtl'] ?? [],
                            'requires' => $updateData['requires'] ?? '6.0',
                            'tested' => $updateData['tested'] ?? '',
                            'requires_php' => $updateData['requires_php'] ?? '8.0',
                            'compatibility' => $updateData['compatibility'] ?? new \stdClass,
                        ];
                    }
                }
            }
        }

        return $transient;
    }

    /**
     * Inject public updates on transient GET (site_transient_update_plugins).
     *
     * This fires every time any code reads the update_plugins transient via get_site_transient().
     * It's the reliable injection point for unlicensed sites — the SET filter (pre_set_site_transient_)
     * fires inside wp_update_plugins() during a batch HTTP context where our API call may fail.
     *
     * @param  mixed  $transient  The plugin update transient (may be false on first call)
     * @return mixed Modified transient with public updates injected
     */
    public function injectPublicUpdates($transient)
    {
        if (! is_object($transient)) {
            return $transient;
        }

        if ($this->options->isActivated()) {
            return $transient;
        }

        return $this->injectPublicPluginData($transient);
    }

    /**
     * Public update check for unlicensed sites — portal plugins only (SET filter path).
     *
     * Calls getPluginSlugs() (no auth needed) which returns latest_version per plugin.
     * Injects update entries with empty package so WordPress shows "update available"
     * but prevents the download button from working.
     */
    private function checkForUpdatesPublic(object $transient): object
    {
        return $this->injectPublicPluginData($transient);
    }

    /**
     * Shared logic for injecting public plugin update data into the transient.
     *
     * Used by both the SET filter (checkForUpdatesPublic) and the GET filter (injectPublicUpdates).
     * Fetches portal plugin versions from cache or API, compares against installed versions,
     * and injects update entries with empty package URLs.
     */
    private function injectPublicPluginData(object $transient): object
    {
        // Try cached data first
        $plugins = get_transient(self::PUBLIC_CACHE_KEY);

        if ($plugins === false) {
            $response = $this->apiClient->getPluginSlugs();

            if ($response['success'] && ! empty($response['data']['plugins'])) {
                $plugins = $response['data']['plugins'];
                set_transient(self::PUBLIC_CACHE_KEY, $plugins, self::PUBLIC_CACHE_DURATION);
            } else {
                return $transient;
            }
        }

        // Ensure the bundled-slugs transient is populated so the sidebar
        // update-count badge works for unlicensed sites too.
        if (get_transient('flm_plugin_slugs') === false) {
            $slugs = array_column($plugins, 'slug');
            if (! empty($slugs)) {
                set_transient('flm_plugin_slugs', $slugs, DAY_IN_SECONDS);
            }
        }

        // Get installed plugins to compare versions
        if (! function_exists('get_plugins')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        $allInstalled = get_plugins();

        foreach ($plugins as $plugin) {
            // Only portal plugins — WP.org plugins have their own update channel
            if (($plugin['source'] ?? 'portal') !== 'portal') {
                continue;
            }

            $latestVersion = $plugin['latest_version'] ?? null;
            if (empty($latestVersion)) {
                continue;
            }

            $pluginFile = $plugin['file'] ?? null;
            if (empty($pluginFile) || ! isset($allInstalled[$pluginFile])) {
                continue;
            }

            // Use $transient->checked when available (SET path), fall back to get_plugins() data (GET path)
            $currentVersion = $transient->checked[$pluginFile] ?? ($allInstalled[$pluginFile]['Version'] ?? '0');

            if (version_compare($latestVersion, $currentVersion, '>')) {
                $transient->response[$pluginFile] = (object) [
                    'id' => $plugin['slug'],
                    'slug' => $plugin['slug'],
                    'plugin' => $pluginFile,
                    'new_version' => $latestVersion,
                    'url' => '',
                    'package' => '', // Empty — prevents download button
                    'icons' => [],
                    'banners' => [],
                    'banners_rtl' => [],
                    'requires' => '6.0',
                    'tested' => '',
                    'requires_php' => '8.0',
                    'compatibility' => new \stdClass,
                ];
            }
        }

        return $transient;
    }

    /**
     * Register in_plugin_update_message hooks for unlicensed users.
     *
     * Appends a "activate your license" link after the standard update row
     * for each portal plugin that has an update available.
     */
    public function registerUpdateMessageHooks(): void
    {
        if ($this->options->isActivated()) {
            return;
        }

        $plugins = get_transient(self::PUBLIC_CACHE_KEY);
        if (empty($plugins) || ! is_array($plugins)) {
            return;
        }

        foreach ($plugins as $plugin) {
            if (($plugin['source'] ?? 'portal') !== 'portal') {
                continue;
            }

            $pluginFile = $plugin['file'] ?? null;
            if (empty($pluginFile)) {
                continue;
            }

            add_action(
                "in_plugin_update_message-{$pluginFile}",
                [$this, 'renderLicenseActivationNotice'],
                10,
                2
            );
        }
    }

    /**
     * Render "activate your license" message in the plugin update row.
     *
     * @param  array  $pluginData  Plugin metadata from the update transient
     * @param  object  $response  Update response object
     */
    public function renderLicenseActivationNotice(array $pluginData, object $response): void
    {
        $licensePageUrl = admin_url('admin.php?page=favethemes-license');

        printf(
            ' '.esc_html__('To update directly, %1$sactivate your license%2$s.', 'favethemes-license-manager'),
            '<a href="'.esc_url($licensePageUrl).'">',
            '</a>'
        );
    }

    /**
     * Get plugin information for the WordPress plugins API
     *
     * This is called when user clicks "View details" link for a plugin.
     *
     * @param  mixed  $result  Default result
     * @param  string  $action  API action
     * @param  object  $args  Request arguments
     * @return mixed Plugin info or default result
     */
    public function getPluginInfo($result, string $action, object $args)
    {
        if ($action !== 'plugin_information') {
            return $result;
        }

        // Check if this is one of our plugins
        if (! isset($args->slug) || ! $this->isBundledPluginSlug($args->slug)) {
            return $result;
        }

        if (! $this->options->isActivated()) {
            return $result;
        }

        $activation = $this->options->getActivation();
        $response = $this->apiClient->getPluginInfo($activation['token'], $args->slug);

        if (! $response['success'] || empty($response['data']['info'])) {
            return $result;
        }

        $info = $response['data']['info'];

        return (object) [
            'name' => $info['name'] ?? '',
            'slug' => $info['slug'] ?? $args->slug,
            'version' => $info['version'] ?? '',
            'author' => $info['author'] ?? '<a href="https://favethemes.com">Favethemes</a>',
            'author_profile' => $info['author_profile'] ?? 'https://favethemes.com',
            'requires' => $info['requires'] ?? '6.0',
            'requires_php' => $info['requires_php'] ?? '8.0',
            'tested' => $info['tested'] ?? '',
            'last_updated' => $info['last_updated'] ?? '',
            'download_link' => $info['download_link'] ?? '',
            'sections' => [
                'description' => $info['sections']['description'] ?? '',
                'changelog' => $info['sections']['changelog'] ?? '',
            ],
            'homepage' => $info['homepage'] ?? 'https://favethemes.com',
        ];
    }

    /**
     * Cleanup after successful plugin update
     *
     * @param  object  $upgrader  WordPress upgrader instance
     * @param  array  $options  Upgrade options
     */
    public function afterUpdate(object $upgrader, array $options): void
    {
        if ($options['type'] !== 'plugin') {
            return;
        }

        if (! isset($options['plugins']) || ! is_array($options['plugins'])) {
            return;
        }

        // Check if any of our plugins were updated
        $bundledPlugins = $this->getBundledPluginSlugs();
        $updated = false;

        foreach ($options['plugins'] as $plugin) {
            $slug = dirname($plugin);
            if (in_array($slug, $bundledPlugins, true)) {
                $updated = true;
                break;
            }
        }

        if ($updated) {
            // Clear update caches after successful update
            delete_transient(self::CACHE_KEY);
            delete_transient(self::PUBLIC_CACHE_KEY);

            // Send heartbeat with updated plugin versions
            if ($this->options->isActivated()) {
                $activation = $this->options->getActivation();
                $this->apiClient->sendHeartbeat($activation['token']);
            }
        }
    }

    /**
     * Get list of installed bundled plugins with their versions
     *
     * @return array List of plugins as ['file' => 'slug/file.php', 'version' => '1.0.0']
     */
    private function getInstalledBundledPlugins(): array
    {
        if (! function_exists('get_plugins')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        $allPlugins = get_plugins();
        $bundledPlugins = [];
        $bundledSlugs = $this->getBundledPluginSlugs();

        foreach ($allPlugins as $pluginFile => $pluginData) {
            $slug = dirname($pluginFile);

            if (in_array($slug, $bundledSlugs, true)) {
                $bundledPlugins[] = [
                    'file' => $pluginFile,
                    'version' => mb_substr($pluginData['Version'] ?? '0', 0, 100),
                ];
            }
        }

        return $bundledPlugins;
    }

    /**
     * Get list of known bundled plugin slugs
     *
     * Fetches from API with caching, falls back to defaults.
     *
     * @return array List of plugin slugs
     */
    private function getBundledPluginSlugs(): array
    {
        // Try to get cached slugs from API
        $cached = get_transient('flm_plugin_slugs');

        if ($cached !== false && is_array($cached)) {
            return apply_filters('flm_bundled_plugin_slugs', $cached);
        }

        // Fetch from API
        $response = $this->apiClient->getPluginSlugs();

        if ($response['success'] && ! empty($response['data']['plugins'])) {
            $slugs = array_column($response['data']['plugins'], 'slug');
            set_transient('flm_plugin_slugs', $slugs, DAY_IN_SECONDS);

            return apply_filters('flm_bundled_plugin_slugs', $slugs);
        }

        // Fallback to defaults if API fails
        $defaults = [
            'houzez-login-register',
            'houzez-theme-functionality',
            'favethemes-developer-tools',
        ];

        return apply_filters('flm_bundled_plugin_slugs', $defaults);
    }

    /**
     * Check if a slug belongs to a bundled plugin
     *
     * @param  string  $slug  Plugin slug
     * @return bool True if this is a bundled plugin
     */
    private function isBundledPluginSlug(string $slug): bool
    {
        return in_array($slug, $this->getBundledPluginSlugs(), true);
    }
}
