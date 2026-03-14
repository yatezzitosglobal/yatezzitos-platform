<?php

namespace FavethemesLicenseManager\Admin;

use FavethemesLicenseManager\Api\ApiClient;
use FavethemesLicenseManager\Core\Options;

/**
 * Admin menu page handler
 */
class AdminPage
{
    private Options $options;

    private ApiClient $apiClient;

    public function __construct(Options $options, ApiClient $apiClient)
    {
        $this->options = $options;
        $this->apiClient = $apiClient;

        $this->initHooks();
    }

    private function initHooks(): void
    {
        add_action('admin_menu', [$this, 'addMenuPage'], 25);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);

        // Handle OAuth callback
        add_action('admin_init', [$this, 'handleOAuthCallback']);

        // Hide all other admin notices on the license page
        add_action('in_admin_header', [$this, 'hideAdminNotices']);
    }

    /**
     * Hide all admin notices on the license page for a cleaner UI
     */
    public function hideAdminNotices(): void
    {
        $screen = get_current_screen();

        if ($screen && (
            $screen->id === 'toplevel_page_favethemes-license'
            || str_ends_with($screen->id, '_page_favethemes-license')
        )) {
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
            remove_all_actions('network_admin_notices');
            remove_all_actions('user_admin_notices');
        }
    }

    /**
     * Add admin menu page
     */
    public function addMenuPage(): void
    {
        if (defined('FLM_PARENT_MENU_SLUG')) {
            // Theme integration: register as submenu under parent menu
            add_submenu_page(
                FLM_PARENT_MENU_SLUG,
                __('License', 'favethemes-license-manager'),
                __('License', 'favethemes-license-manager'),
                'manage_options',
                'favethemes-license',
                [$this, 'renderPage']
            );
        } else {
            // Standalone: top-level menu
            $productName = ucfirst(FLM_THEME_SLUG);

            add_menu_page(
                sprintf(__('%s License', 'favethemes-license-manager'), $productName),
                sprintf(__('%s License', 'favethemes-license-manager'), $productName),
                'manage_options',
                'favethemes-license',
                [$this, 'renderPage'],
                'dashicons-admin-network',
                59
            );
        }
    }

    /**
     * Enqueue admin assets
     */
    public function enqueueAssets(string $hook): void
    {
        if ($hook !== 'toplevel_page_favethemes-license'
            && ! str_ends_with($hook, '_page_favethemes-license')) {
            return;
        }

        // CSS
        wp_enqueue_style(
            'flm-admin',
            FLM_PLUGIN_URL.'assets/css/admin.css',
            [],
            FLM_VERSION
        );

        // JS
        wp_enqueue_script(
            'flm-admin',
            FLM_PLUGIN_URL.'assets/js/admin.js',
            ['jquery'],
            FLM_VERSION,
            true
        );

        // Localize script
        wp_localize_script('flm-admin', 'flmAdmin', [
            'nonce' => wp_create_nonce('flm_admin_nonce'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'i18n' => [
                'activating' => __('Activating...', 'favethemes-license-manager'),
                'deactivating' => __('Deactivating...', 'favethemes-license-manager'),
                'error' => __('An error occurred. Please try again.', 'favethemes-license-manager'),
                'success' => __('Success!', 'favethemes-license-manager'),
            ],
        ]);
    }

    /**
     * Render the admin page
     */
    public function renderPage(): void
    {
        $options = $this->options;
        include FLM_PLUGIN_DIR.'templates/admin-page.php';
    }

    /**
     * Handle OAuth callback from portal
     */
    public function handleOAuthCallback(): void
    {
        // Only on our admin page
        if (! isset($_GET['page']) || $_GET['page'] !== 'favethemes-license') {
            return;
        }

        // Check for callback token
        if (! isset($_GET['callback_token'])) {
            return;
        }

        $callbackToken = sanitize_text_field($_GET['callback_token']);

        // Finalize the activation
        $response = $this->apiClient->finalizeActivation($callbackToken);

        if ($response['success'] && ! empty($response['data'])) {
            $data = $response['data'];

            // Save user data
            $this->options->saveUser([
                'email' => $data['user']['email'] ?? '',
            ]);

            // Save license data
            $this->options->saveLicense([
                'source' => $data['license']['source'] ?? 'direct',
                'license_id' => $data['license']['id'] ?? '',
                'purchase_code' => $data['license']['purchase_code'] ?? '',
                'envato_username' => $data['license']['envato_username'] ?? '',
                'plan_code' => $data['license']['plan_code'] ?? '',
                'status' => $data['license']['status'] ?? 'active',
                'expires_at' => $data['license']['expires_at'] ?? '',
                'purchased_at' => $data['license']['purchased_at'] ?? '',
            ]);

            // Save activation data
            $this->options->saveActivation([
                'activation_id' => $data['activation']['id'] ?? '',
                'token' => $data['activation']['token'] ?? '',
                'domain' => $data['activation']['domain'] ?? $this->options->getCurrentDomain(),
                'environment' => $data['activation']['environment'] ?? 'live',
                'activated_at' => $data['activation']['activated_at'] ?? current_time('mysql'),
                'fingerprint' => $this->options->generateFingerprint(),
            ]);

            // Clear public version caches so authenticated checks take over
            delete_transient('flm_public_plugin_versions');
            delete_transient('flm_public_theme_version');
            delete_site_transient('update_plugins');
            delete_site_transient('update_themes');

            // Close this tab and reload the opener (original license page)
            $this->closeTabAndReloadOpener(admin_url('admin.php?page=favethemes-license&activated=1'));
        } else {
            // Close this tab and reload the opener with error
            $errorMessage = $response['error'] ?? __('Activation failed', 'favethemes-license-manager');
            $this->closeTabAndReloadOpener(admin_url('admin.php?page=favethemes-license&activation_error='.urlencode($errorMessage)));
        }
    }

    /**
     * Output a minimal HTML page that closes this tab and reloads the opener tab.
     * Falls back to a normal redirect if window.opener is unavailable.
     */
    private function closeTabAndReloadOpener(string $fallbackUrl): void
    {
        $safeUrl = esc_url($fallbackUrl);

        echo '<!DOCTYPE html><html><head><title>'.esc_html__('Redirecting...', 'favethemes-license-manager').'</title></head><body>';
        echo '<script>';
        echo 'if(window.opener&&!window.opener.closed){';
        echo 'window.opener.location.reload();';
        echo 'window.close();';
        echo '}else{';
        echo 'window.location.href="'.esc_js($safeUrl).'";';
        echo '}';
        echo '</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url='.$safeUrl.'"></noscript>';
        echo '</body></html>';
        exit;
    }
}
