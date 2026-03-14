<?php

namespace FavethemesLicenseManager\Admin;

use FavethemesLicenseManager\Api\ApiClient;
use FavethemesLicenseManager\Core\Options;

/**
 * Portal Plugins Page - Fetches and manages plugins from Favethemes Portal
 */
class PluginsPage
{
    private Options $options;

    private ApiClient $apiClient;

    public function __construct(Options $options, ApiClient $apiClient)
    {
        $this->options = $options;
        $this->apiClient = $apiClient;

        add_action('admin_menu', [$this, 'registerMenu'], 11);
        add_action('admin_menu', [$this, 'addUpdateBadge'], 99);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);

        // Hide all other admin notices on this page
        add_action('in_admin_header', [$this, 'hideAdminNotices']);

        // AJAX handlers
        add_action('wp_ajax_flm_fetch_public_plugins', [$this, 'ajaxFetchPublicPlugins']);
        add_action('wp_ajax_flm_fetch_plugins', [$this, 'ajaxFetchPlugins']);
        add_action('wp_ajax_flm_install_plugin', [$this, 'ajaxInstallPlugin']);
        add_action('wp_ajax_flm_install_wporg_plugin', [$this, 'ajaxInstallWpOrgPlugin']);
        add_action('wp_ajax_flm_activate_plugin', [$this, 'ajaxActivatePlugin']);
        add_action('wp_ajax_flm_deactivate_plugin', [$this, 'ajaxDeactivatePlugin']);
        add_action('wp_ajax_flm_update_plugin', [$this, 'ajaxUpdatePlugin']);
        add_action('wp_ajax_flm_update_wporg_plugin', [$this, 'ajaxUpdateWpOrgPlugin']);
        add_action('wp_ajax_flm_bulk_install_required', [$this, 'ajaxBulkInstallRequired']);
    }

    /**
     * Hide all admin notices on the plugins page for a cleaner UI
     */
    public function hideAdminNotices(): void
    {
        $screen = get_current_screen();

        // Match any parent menu slug ending with our page slug
        if ($screen && str_ends_with($screen->id, '_page_favethemes-portal-plugins')) {
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
            remove_all_actions('network_admin_notices');
            remove_all_actions('user_admin_notices');
        }
    }

    /**
     * Register the plugins submenu page
     */
    public function registerMenu(): void
    {
        // In theme mode, the menu is registered by the theme's class-admin.php
        if (defined('FLM_THEME_MODE') && FLM_THEME_MODE) {
            return;
        }

        add_submenu_page(
            'favethemes-license',
            __('Plugins', 'favethemes-license-manager'),
            __('Plugins', 'favethemes-license-manager'),
            'install_plugins',
            'favethemes-portal-plugins',
            [$this, 'renderPage']
        );
    }

    /**
     * Enqueue CSS and JS assets
     */
    public function enqueueAssets(string $hook): void
    {
        // Check if we're on the portal plugins page by matching the page slug in the hook
        // This handles both 'favethemes-license_page_...' and 'houzez-license_page_...' parent slugs
        if (! str_ends_with($hook, '_page_favethemes-portal-plugins')) {
            return;
        }

        wp_enqueue_style(
            'flm-plugins-page',
            FLM_PLUGIN_URL.'assets/css/plugins-page.css',
            [],
            FLM_VERSION
        );

        wp_enqueue_script(
            'flm-plugins-page',
            FLM_PLUGIN_URL.'assets/js/plugins-page.js',
            ['jquery', 'wp-util'],
            FLM_VERSION,
            true
        );

        wp_localize_script('flm-plugins-page', 'flmPlugins', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('flm_plugins_nonce'),
            'isActivated' => $this->options->isActivated(),
            'strings' => [
                'installing' => __('Installing...', 'favethemes-license-manager'),
                'activating' => __('Activating...', 'favethemes-license-manager'),
                'updating' => __('Updating...', 'favethemes-license-manager'),
                'deactivating' => __('Deactivating...', 'favethemes-license-manager'),
                'installed' => __('Installed', 'favethemes-license-manager'),
                'activated' => __('Activated', 'favethemes-license-manager'),
                'updated' => __('Updated', 'favethemes-license-manager'),
                'deactivated' => __('Deactivated', 'favethemes-license-manager'),
                'error' => __('Error', 'favethemes-license-manager'),
                'install' => __('Install', 'favethemes-license-manager'),
                'activate' => __('Activate', 'favethemes-license-manager'),
                'update' => __('Update', 'favethemes-license-manager'),
                'deactivate' => __('Deactivate', 'favethemes-license-manager'),
                'active' => __('Active', 'favethemes-license-manager'),
                'inactive' => __('Inactive', 'favethemes-license-manager'),
                'notInstalled' => __('Not Installed', 'favethemes-license-manager'),
                'updateAvailable' => __('Update Available', 'favethemes-license-manager'),
                'confirmDeactivate' => __('Are you sure you want to deactivate this plugin?', 'favethemes-license-manager'),
                'fetchError' => __('Failed to fetch plugins. Please try again.', 'favethemes-license-manager'),
                'notActivated' => __('Please activate your license first.', 'favethemes-license-manager'),
                'required' => __('Required', 'favethemes-license-manager'),
                'recommended' => __('Recommended', 'favethemes-license-manager'),
                'optional' => __('Optional', 'favethemes-license-manager'),
                'bulkInstalling' => __('Installing %1$d of %2$d...', 'favethemes-license-manager'),
                'bulkInstallComplete' => __('All required plugins installed!', 'favethemes-license-manager'),
                'bulkInstallBtn' => __('Install All Required', 'favethemes-license-manager'),
                'activateAll' => __('Activate All Required', 'favethemes-license-manager'),
                'bulkActivating' => __('Activating %1$d of %2$d...', 'favethemes-license-manager'),
                'bulkActivateComplete' => __('All required plugins activated!', 'favethemes-license-manager'),
                'updateAll' => __('Update All', 'favethemes-license-manager'),
                'bulkUpdating' => __('Updating %1$d of %2$d...', 'favethemes-license-manager'),
                'bulkUpdateComplete' => __('All plugins updated!', 'favethemes-license-manager'),
                'wpOrg' => __('WordPress.org', 'favethemes-license-manager'),
            ],
        ]);
    }

    /**
     * Render the plugins page
     */
    public function renderPage(): void
    {
        $isActivated = $this->options->isActivated();
        $activation = $isActivated ? $this->options->getActivation() : null;

        include FLM_PLUGIN_DIR.'templates/plugins-page.php';
    }

    /**
     * AJAX: Fetch plugins from portal
     */
    public function ajaxFetchPlugins(): void
    {
        check_ajax_referer('flm_plugins_nonce', 'nonce');

        if (! current_user_can('install_plugins')) {
            wp_send_json_error(['message' => __('Permission denied.', 'favethemes-license-manager')]);
        }

        if (! $this->options->isActivated()) {
            wp_send_json_error(['message' => __('License not activated.', 'favethemes-license-manager')]);
        }

        $activation = $this->options->getActivation();

        // Get installed plugins info
        if (! function_exists('get_plugins')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        $installedPlugins = get_plugins();
        $installed = [];

        foreach ($installedPlugins as $file => $data) {
            $slug = dirname($file);
            if ($slug !== '.') {
                $installed[] = [
                    'slug' => $slug,
                    'version' => mb_substr($data['Version'] ?: '0', 0, 100),
                ];
            }
        }

        // Fetch from portal
        $response = $this->apiClient->getPortalPlugins($activation['token'], $installed);

        if (! $response['success']) {
            wp_send_json_error([
                'message' => $response['error'] ?? __('Failed to fetch plugins.', 'favethemes-license-manager'),
            ]);
        }

        // Enhance with local status
        $plugins = $response['data']['plugins'] ?? [];

        // Get WordPress's built-in update transient for wp.org plugin update detection
        $wpUpdateTransient = get_site_transient('update_plugins');

        foreach ($plugins as &$plugin) {
            $pluginFile = $plugin['plugin_file'] ?? '';
            $plugin['is_active_local'] = is_plugin_active($pluginFile);

            // For WordPress.org plugins, check WordPress's built-in update detection
            if (($plugin['source'] ?? 'portal') === 'wordpress.org' && $pluginFile) {
                if ($wpUpdateTransient && isset($wpUpdateTransient->response[$pluginFile])) {
                    $plugin['has_update'] = true;
                    $plugin['latest_version'] = $wpUpdateTransient->response[$pluginFile]->new_version;
                }
            }
        }

        wp_send_json_success([
            'plugins' => $plugins,
            'can_download' => $response['data']['can_download'] ?? true,
        ]);
    }

    /**
     * AJAX: Fetch public plugin data (no license required)
     *
     * Uses the /plugins/slugs endpoint which returns basic plugin info
     * without requiring an activation token. Downloads are blocked.
     */
    public function ajaxFetchPublicPlugins(): void
    {
        check_ajax_referer('flm_plugins_nonce', 'nonce');

        if (! current_user_can('install_plugins')) {
            wp_send_json_error(['message' => __('Permission denied.', 'favethemes-license-manager')]);
        }

        // Fetch public data from portal (no license needed)
        $response = $this->apiClient->getPluginSlugs();

        if (! $response['success']) {
            wp_send_json_error([
                'message' => $response['error'] ?? __('Failed to fetch plugins.', 'favethemes-license-manager'),
            ]);
        }

        $rawPlugins = $response['data']['plugins'] ?? [];

        // Get installed plugins info for local status
        if (! function_exists('get_plugins')) {
            require_once ABSPATH.'wp-admin/includes/plugin.php';
        }

        $installedPlugins = get_plugins();

        // Build lookup: plugin_file => plugin_data
        $installedByFile = [];
        foreach ($installedPlugins as $file => $data) {
            $installedByFile[$file] = $data;
        }

        // Enhance with local status to match the format ajaxFetchPlugins returns
        $plugins = [];
        foreach ($rawPlugins as $raw) {
            $pluginFile = $raw['file'] ?? '';
            $isInstalled = isset($installedByFile[$pluginFile]);
            $installedVersion = $isInstalled ? ($installedByFile[$pluginFile]['Version'] ?? null) : null;
            $latestVersion = $raw['latest_version'] ?? null;

            $hasUpdate = false;
            if ($isInstalled && $installedVersion && $latestVersion) {
                $hasUpdate = version_compare($latestVersion, $installedVersion, '>');
            }

            $plugins[] = [
                'slug' => $raw['slug'],
                'name' => $raw['name'],
                'description' => $raw['description'] ?? '',
                'plugin_file' => $pluginFile,
                'wp_slug' => $pluginFile,
                'thumbnail' => $raw['thumbnail'] ?? null,
                'source' => $raw['source'] ?? 'portal',
                'priority' => $raw['priority'] ?? 'recommended',
                'wp_org_slug' => $raw['wp_org_slug'] ?? null,
                'author_name' => $raw['author_name'] ?? null,
                'installed_version' => $installedVersion,
                'is_installed' => $isInstalled,
                'latest_version' => $latestVersion,
                'has_update' => $hasUpdate,
                'is_active_local' => $isInstalled && is_plugin_active($pluginFile),
                'download_url' => '',
            ];
        }

        wp_send_json_success([
            'plugins' => $plugins,
            'can_download' => false,
        ]);
    }

    /**
     * AJAX: Install a plugin
     */
    public function ajaxInstallPlugin(): void
    {
        check_ajax_referer('flm_plugins_nonce', 'nonce');

        if (! current_user_can('install_plugins')) {
            wp_send_json_error(['message' => __('Permission denied.', 'favethemes-license-manager')]);
        }

        $downloadUrl = sanitize_url($_POST['download_url'] ?? '');
        $pluginSlug = sanitize_text_field($_POST['plugin_slug'] ?? '');

        if (empty($pluginSlug)) {
            wp_send_json_error(['message' => __('Invalid plugin slug.', 'favethemes-license-manager')]);
        }

        if (empty($downloadUrl)) {
            wp_send_json_error(['message' => __('No download URL provided. Make sure the plugin has a release with a ZIP file uploaded.', 'favethemes-license-manager')]);
        }

        if (! $this->isAllowedDownloadUrl($downloadUrl)) {
            wp_send_json_error(['message' => __('Download URL is not from an allowed domain.', 'favethemes-license-manager')]);
        }

        require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH.'wp-admin/includes/plugin-install.php';
        require_once ABSPATH.'wp-admin/includes/file.php';

        // Allow local development URLs (.test, .local, .localhost, .dev)
        $localUrlFilter = $this->allowLocalUrls();

        // First, try to download and validate the file
        $tempFile = download_url($downloadUrl, 300);
        if (is_wp_error($tempFile)) {
            $errorMsg = $tempFile->get_error_message();
            // Common SSL issues with local development
            if (strpos($errorMsg, 'SSL') !== false || strpos($errorMsg, 'certificate') !== false) {
                $errorMsg .= ' (Tip: For local development, ensure your SSL certificate is trusted or use HTTP)';
            }
            wp_send_json_error(['message' => __('Failed to download plugin: ', 'favethemes-license-manager').$errorMsg]);
        }

        // Check if it's a valid ZIP file
        $fileSize = filesize($tempFile);
        if ($fileSize < 100) {
            // Read what was downloaded to see the error
            $content = file_get_contents($tempFile);
            @unlink($tempFile);

            // Check if it's an HTML error page
            if (strpos($content, '<html') !== false || strpos($content, '<!DOCTYPE') !== false) {
                wp_send_json_error(['message' => __('Download URL returned HTML instead of a ZIP file. The URL may have expired or the file is missing.', 'favethemes-license-manager')]);
            }

            wp_send_json_error(['message' => __('Downloaded file is too small or empty. Check if the release has a valid ZIP file uploaded.', 'favethemes-license-manager')]);
        }

        // Verify file integrity if hash is available
        $expectedHash = sanitize_text_field($_POST['file_hash'] ?? '');
        if (! empty($expectedHash)) {
            $actualHash = 'sha256:'.hash_file('sha256', $tempFile);
            if (! hash_equals($expectedHash, $actualHash)) {
                @unlink($tempFile);
                wp_send_json_error([
                    'message' => __('File integrity check failed. The downloaded file does not match the expected hash. Please try again or contact support.', 'favethemes-license-manager'),
                ]);
            }
        }

        // Clean up temp file - we'll let the upgrader download again
        @unlink($tempFile);

        $skin = new \WP_Ajax_Upgrader_Skin;
        $upgrader = new \Plugin_Upgrader($skin);

        $result = $upgrader->install($downloadUrl);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        if ($result === false) {
            $errors = $skin->get_errors();
            $message = is_wp_error($errors) ? $errors->get_error_message() : __('Installation failed.', 'favethemes-license-manager');
            wp_send_json_error(['message' => $message]);
        }

        // Verify the plugin was actually installed
        $pluginDir = WP_PLUGIN_DIR.'/'.$pluginSlug;
        if (! is_dir($pluginDir)) {
            // Check skin for any feedback messages
            $feedback = $skin->get_upgrade_messages();
            $feedbackStr = ! empty($feedback) ? ' Messages: '.implode(', ', $feedback) : '';

            // The ZIP might have extracted with a different folder name
            // Check what the upgrader actually installed
            $installedPlugin = $upgrader->plugin_info();
            if ($installedPlugin) {
                $installedSlug = dirname($installedPlugin);
                wp_send_json_error([
                    'message' => sprintf(
                        __('Plugin installed but folder name mismatch. Expected: %s, Got: %s. Update the plugin slug in the portal to match.', 'favethemes-license-manager'),
                        $pluginSlug,
                        $installedSlug
                    ),
                ]);
            }

            wp_send_json_error(['message' => __('Installation appeared to succeed but plugin folder not found.', 'favethemes-license-manager').$feedbackStr]);
        }

        // Clean up the local URL filter
        $this->disallowLocalUrls($localUrlFilter);

        wp_send_json_success(['message' => __('Plugin installed successfully.', 'favethemes-license-manager')]);
    }

    /**
     * AJAX: Activate a plugin
     */
    public function ajaxActivatePlugin(): void
    {
        check_ajax_referer('flm_plugins_nonce', 'nonce');

        if (! current_user_can('activate_plugins')) {
            wp_send_json_error(['message' => __('Permission denied.', 'favethemes-license-manager')]);
        }

        $pluginFile = sanitize_text_field($_POST['plugin_file'] ?? '');

        if (empty($pluginFile)) {
            wp_send_json_error(['message' => __('Invalid plugin.', 'favethemes-license-manager')]);
        }

        $result = activate_plugin($pluginFile);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success(['message' => __('Plugin activated successfully.', 'favethemes-license-manager')]);
    }

    /**
     * AJAX: Deactivate a plugin
     */
    public function ajaxDeactivatePlugin(): void
    {
        check_ajax_referer('flm_plugins_nonce', 'nonce');

        if (! current_user_can('activate_plugins')) {
            wp_send_json_error(['message' => __('Permission denied.', 'favethemes-license-manager')]);
        }

        $pluginFile = sanitize_text_field($_POST['plugin_file'] ?? '');

        if (empty($pluginFile)) {
            wp_send_json_error(['message' => __('Invalid plugin.', 'favethemes-license-manager')]);
        }

        deactivate_plugins($pluginFile);

        wp_send_json_success(['message' => __('Plugin deactivated successfully.', 'favethemes-license-manager')]);
    }

    /**
     * AJAX: Update a plugin
     */
    public function ajaxUpdatePlugin(): void
    {
        check_ajax_referer('flm_plugins_nonce', 'nonce');

        if (! current_user_can('update_plugins')) {
            wp_send_json_error(['message' => __('Permission denied.', 'favethemes-license-manager')]);
        }

        $downloadUrl = sanitize_url($_POST['download_url'] ?? '');
        $pluginFile = sanitize_text_field($_POST['plugin_file'] ?? '');

        if (empty($downloadUrl) || empty($pluginFile)) {
            wp_send_json_error(['message' => __('Invalid request.', 'favethemes-license-manager')]);
        }

        if (! $this->isAllowedDownloadUrl($downloadUrl)) {
            wp_send_json_error(['message' => __('Download URL is not from an allowed domain.', 'favethemes-license-manager')]);
        }

        // Check if plugin was active before update
        $wasActive = is_plugin_active($pluginFile);

        require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH.'wp-admin/includes/plugin-install.php';
        require_once ABSPATH.'wp-admin/includes/file.php';

        // Allow local development URLs (.test, .local, .localhost, .dev)
        $localUrlFilter = $this->allowLocalUrls();

        // Download new version
        $tempFile = download_url($downloadUrl);

        if (is_wp_error($tempFile)) {
            wp_send_json_error(['message' => $tempFile->get_error_message()]);
        }

        // Verify file integrity if hash is available
        $expectedHash = sanitize_text_field($_POST['file_hash'] ?? '');
        if (! empty($expectedHash)) {
            $actualHash = 'sha256:'.hash_file('sha256', $tempFile);
            if (! hash_equals($expectedHash, $actualHash)) {
                @unlink($tempFile);
                wp_send_json_error([
                    'message' => __('File integrity check failed. The downloaded file does not match the expected hash. Please try again or contact support.', 'favethemes-license-manager'),
                ]);
            }
        }

        // Get plugin directory
        $pluginDir = WP_PLUGIN_DIR.'/'.dirname($pluginFile);

        // Deactivate plugin first
        if ($wasActive) {
            deactivate_plugins($pluginFile);
        }

        // Remove old plugin directory
        global $wp_filesystem;
        WP_Filesystem();

        if ($wp_filesystem->exists($pluginDir)) {
            $wp_filesystem->delete($pluginDir, true);
        }

        // Extract new version
        $result = unzip_file($tempFile, WP_PLUGIN_DIR);

        // Clean up temp file
        @unlink($tempFile);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        // Reactivate if it was active
        if ($wasActive) {
            activate_plugin($pluginFile);
        }

        // Clean up the local URL filter
        $this->disallowLocalUrls($localUrlFilter);

        wp_send_json_success(['message' => __('Plugin updated successfully.', 'favethemes-license-manager')]);
    }

    /**
     * AJAX: Update a plugin from WordPress.org using native upgrader
     */
    public function ajaxUpdateWpOrgPlugin(): void
    {
        check_ajax_referer('flm_plugins_nonce', 'nonce');

        if (! current_user_can('update_plugins')) {
            wp_send_json_error(['message' => __('Permission denied.', 'favethemes-license-manager')]);
        }

        $pluginFile = sanitize_text_field($_POST['plugin_file'] ?? '');

        if (empty($pluginFile)) {
            wp_send_json_error(['message' => __('Invalid plugin.', 'favethemes-license-manager')]);
        }

        // Check if plugin was active before update
        $wasActive = is_plugin_active($pluginFile);

        require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH.'wp-admin/includes/plugin-install.php';

        // Ensure WordPress has current update data for this plugin
        $update_plugins = get_site_transient('update_plugins');
        if (! isset($update_plugins->response[$pluginFile])) {
            delete_site_transient('update_plugins');
            wp_update_plugins();
        }

        $skin = new \WP_Ajax_Upgrader_Skin;
        $upgrader = new \Plugin_Upgrader($skin);

        // Use WordPress's native upgrade method for wp.org plugins
        $result = $upgrader->upgrade($pluginFile);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        if ($result === false) {
            $errors = $skin->get_errors();
            $message = is_wp_error($errors) ? $errors->get_error_message() : __('Update failed.', 'favethemes-license-manager');
            wp_send_json_error(['message' => $message]);
        }

        // Reactivate if it was active before update
        if ($wasActive) {
            activate_plugin($pluginFile);
        }

        wp_send_json_success(['message' => __('Plugin updated successfully.', 'favethemes-license-manager')]);
    }

    /**
     * AJAX: Install a plugin from WordPress.org
     */
    public function ajaxInstallWpOrgPlugin(): void
    {
        check_ajax_referer('flm_plugins_nonce', 'nonce');

        if (! current_user_can('install_plugins')) {
            wp_send_json_error(['message' => __('Permission denied.', 'favethemes-license-manager')]);
        }

        $wpOrgSlug = sanitize_text_field($_POST['wp_org_slug'] ?? '');

        if (empty($wpOrgSlug)) {
            wp_send_json_error(['message' => __('Invalid plugin slug.', 'favethemes-license-manager')]);
        }

        require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH.'wp-admin/includes/plugin-install.php';

        // Get plugin info from WordPress.org
        $api = plugins_api('plugin_information', [
            'slug' => $wpOrgSlug,
            'fields' => [
                'short_description' => false,
                'sections' => false,
                'requires' => false,
                'rating' => false,
                'ratings' => false,
                'downloaded' => false,
                'last_updated' => false,
                'added' => false,
                'tags' => false,
                'compatibility' => false,
                'homepage' => false,
                'donate_link' => false,
            ],
        ]);

        if (is_wp_error($api)) {
            wp_send_json_error(['message' => $api->get_error_message()]);
        }

        $skin = new \WP_Ajax_Upgrader_Skin;
        $upgrader = new \Plugin_Upgrader($skin);

        $result = $upgrader->install($api->download_link);

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        if ($result === false) {
            $errors = $skin->get_errors();
            $message = is_wp_error($errors) ? $errors->get_error_message() : __('Installation failed.', 'favethemes-license-manager');
            wp_send_json_error(['message' => $message]);
        }

        wp_send_json_success(['message' => __('Plugin installed successfully.', 'favethemes-license-manager')]);
    }

    /**
     * AJAX: Bulk install all required plugins
     */
    public function ajaxBulkInstallRequired(): void
    {
        check_ajax_referer('flm_plugins_nonce', 'nonce');

        if (! current_user_can('install_plugins')) {
            wp_send_json_error(['message' => __('Permission denied.', 'favethemes-license-manager')]);
        }

        $plugins = isset($_POST['plugins']) ? json_decode(stripslashes($_POST['plugins']), true) : [];

        if (empty($plugins) || ! is_array($plugins)) {
            wp_send_json_error(['message' => __('No plugins to install.', 'favethemes-license-manager')]);
        }

        require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH.'wp-admin/includes/plugin-install.php';

        // Allow local development URLs (.test, .local, .localhost, .dev)
        $localUrlFilter = $this->allowLocalUrls();

        $results = [];
        $skin = new \WP_Ajax_Upgrader_Skin;

        foreach ($plugins as $plugin) {
            $slug = sanitize_text_field($plugin['slug'] ?? '');
            $source = sanitize_text_field($plugin['source'] ?? 'portal');
            $downloadUrl = isset($plugin['download_url']) ? sanitize_url($plugin['download_url']) : '';
            $wpOrgSlug = sanitize_text_field($plugin['wp_org_slug'] ?? $slug);

            if (empty($slug)) {
                continue;
            }

            $upgrader = new \Plugin_Upgrader($skin);

            if ($source === 'wordpress.org') {
                // Install from WordPress.org
                $api = plugins_api('plugin_information', [
                    'slug' => $wpOrgSlug,
                    'fields' => ['sections' => false],
                ]);

                if (is_wp_error($api)) {
                    $results[$slug] = ['success' => false, 'message' => $api->get_error_message()];

                    continue;
                }

                $result = $upgrader->install($api->download_link);
            } else {
                // Install from portal
                if (empty($downloadUrl)) {
                    $results[$slug] = ['success' => false, 'message' => __('No download URL.', 'favethemes-license-manager')];

                    continue;
                }

                if (! $this->isAllowedDownloadUrl($downloadUrl)) {
                    $results[$slug] = ['success' => false, 'message' => __('Download URL is not from an allowed domain.', 'favethemes-license-manager')];

                    continue;
                }

                $result = $upgrader->install($downloadUrl);
            }

            if (is_wp_error($result)) {
                $results[$slug] = ['success' => false, 'message' => $result->get_error_message()];
            } elseif ($result === false) {
                $errors = $skin->get_errors();
                $message = is_wp_error($errors) ? $errors->get_error_message() : __('Installation failed.', 'favethemes-license-manager');
                $results[$slug] = ['success' => false, 'message' => $message];
            } else {
                $results[$slug] = ['success' => true];
            }
        }

        // Clean up the local URL filter
        $this->disallowLocalUrls($localUrlFilter);

        $successCount = count(array_filter($results, fn ($r) => $r['success']));
        $totalCount = count($results);

        wp_send_json_success([
            'message' => sprintf(
                __('%d of %d plugins installed successfully.', 'favethemes-license-manager'),
                $successCount,
                $totalCount
            ),
            'results' => $results,
        ]);
    }

    /**
     * Append an update-count badge to the "Plugins" submenu item.
     *
     * Hooked at priority 99 so all menus are already registered.
     */
    public function addUpdateBadge(): void
    {
        $count = $this->getPluginUpdateCount();

        if ($count < 1) {
            return;
        }

        global $submenu;

        $parentSlug = $this->getParentMenuSlug();

        if (empty($submenu[$parentSlug])) {
            return;
        }

        foreach ($submenu[$parentSlug] as &$item) {
            if (($item[2] ?? '') === 'favethemes-portal-plugins') {
                $item[0] .= sprintf(
                    ' <span class="update-plugins count-%d"><span class="plugin-count">%d</span></span>',
                    $count,
                    $count
                );
                break;
            }
        }
    }

    /**
     * Return the parent menu slug for the current mode.
     */
    private function getParentMenuSlug(): string
    {
        if (defined('FLM_PARENT_MENU_SLUG')) {
            return FLM_PARENT_MENU_SLUG;
        }

        return 'favethemes-license';
    }

    /**
     * Count bundled-plugin updates from cached transients (no API calls).
     */
    private function getPluginUpdateCount(): int
    {
        $updatePlugins = get_site_transient('update_plugins');

        if (empty($updatePlugins->response)) {
            return 0;
        }

        $bundledSlugs = get_transient('flm_plugin_slugs');

        if (! is_array($bundledSlugs) || empty($bundledSlugs)) {
            return 0;
        }

        $count = 0;

        foreach ($updatePlugins->response as $pluginFile => $data) {
            $slug = dirname($pluginFile);
            if (in_array($slug, $bundledSlugs, true)) {
                $count++;
            }
        }

        return $count;
    }

    // ─────────────────────────────────────────────────
    // Download URL Domain Whitelist
    // ─────────────────────────────────────────────────

    /**
     * Get the list of domains allowed to serve plugin downloads.
     *
     * @return array<string>
     */
    private function getAllowedDownloadDomains(): array
    {
        $domains = ['app.favethemes.com', 'staging.favethemes.com'];

        // Add the configured API host (supports custom portal deployments)
        $apiHost = parse_url($this->apiClient->getApiUrl(), PHP_URL_HOST);
        if ($apiHost && ! in_array($apiHost, $domains, true)) {
            $domains[] = $apiHost;
        }

        // Allow custom domains via constant
        if (defined('FLM_ALLOWED_DOWNLOAD_DOMAINS') && is_array(FLM_ALLOWED_DOWNLOAD_DOMAINS)) {
            $domains = array_merge($domains, FLM_ALLOWED_DOWNLOAD_DOMAINS);
        }

        return array_unique($domains);
    }

    /**
     * Check if a download URL points to an allowed domain.
     */
    private function isAllowedDownloadUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            return false;
        }

        // Always allow local development hosts
        if ($this->isLocalDevHost($host)) {
            return true;
        }

        foreach ($this->getAllowedDownloadDomains() as $domain) {
            if (strcasecmp($host, $domain) === 0) {
                return true;
            }
        }

        return false;
    }

    // ─────────────────────────────────────────────────
    // Local Development URL Helpers
    // ─────────────────────────────────────────────────

    /**
     * Local development TLDs that should bypass URL validation and SSL verification.
     *
     * WARNING: These settings are for LOCAL DEVELOPMENT ONLY.
     * In production, URLs should use proper SSL certificates from trusted CAs.
     *
     * @var array<string>
     */
    private const LOCAL_DEV_TLDS = ['.test', '.local', '.localhost', '.dev'];

    /**
     * Check if a host is a local development domain
     */
    private function isLocalDevHost(string $host): bool
    {
        foreach (self::LOCAL_DEV_TLDS as $tld) {
            if (str_ends_with($host, $tld) || $host === 'localhost') {
                return true;
            }
        }

        return false;
    }

    /**
     * Add filters to allow local development URLs for HTTP requests.
     *
     * This enables two things for local dev domains (.test, .local, .localhost, .dev):
     * 1. Bypasses WordPress's host validation (http_request_host_is_external)
     * 2. Disables SSL certificate verification (for self-signed certs)
     *
     * WARNING: FOR LOCAL DEVELOPMENT ONLY. Do not use in production.
     *
     * @return array{host: callable, ssl: callable} The filter callbacks (needed for removal)
     */
    private function allowLocalUrls(): array
    {
        // Filter to allow local hosts
        $hostFilter = function ($allowed, $host, $url) {
            if ($this->isLocalDevHost($host)) {
                return true;
            }

            return $allowed;
        };

        // Filter to disable SSL verification for local hosts
        $sslFilter = function ($args, $url) {
            $host = wp_parse_url($url, PHP_URL_HOST);
            if ($host && $this->isLocalDevHost($host)) {
                $args['sslverify'] = false;
            }

            return $args;
        };

        add_filter('http_request_host_is_external', $hostFilter, 10, 3);
        add_filter('http_request_args', $sslFilter, 10, 2);

        return [
            'host' => $hostFilter,
            'ssl' => $sslFilter,
        ];
    }

    /**
     * Remove the local URL filters
     *
     * @param  array{host: callable, ssl: callable}  $filters  The filter callbacks returned by allowLocalUrls()
     */
    private function disallowLocalUrls(array $filters): void
    {
        remove_filter('http_request_host_is_external', $filters['host'], 10);
        remove_filter('http_request_args', $filters['ssl'], 10);
    }
}
