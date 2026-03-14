<?php

namespace FavethemesLicenseManager\Core;

/**
 * Multisite compatibility handler
 */
class Multisite
{
    private Options $options;

    public function __construct(Options $options)
    {
        $this->options = $options;

        if (is_multisite()) {
            $this->initHooks();
        }
    }

    private function initHooks(): void
    {
        // Add network admin notice for unlicensed sites
        add_action('network_admin_notices', [$this, 'networkAdminNotice']);

        // Add per-site admin notice
        add_action('admin_notices', [$this, 'siteAdminNotice']);
    }

    /**
     * Show network admin notice about unlicensed sites
     */
    public function networkAdminNotice(): void
    {
        if (! current_user_can('manage_network')) {
            return;
        }

        $unlicensedSites = $this->getUnlicensedSites();
        $count = count($unlicensedSites);

        if ($count === 0) {
            return;
        }

        $productName = ucfirst(FLM_THEME_SLUG);
        $message = sprintf(
            /* translators: %1$d: number of unlicensed sites, %2$s: product name */
            _n(
                '%1$d site in your network does not have an active %2$s license.',
                '%1$d sites in your network do not have active %2$s licenses.',
                $count,
                'favethemes-license-manager'
            ),
            $count,
            $productName
        );

        printf(
            '<div class="notice notice-warning"><p><strong>%s</strong> %s</p></div>',
            esc_html(sprintf(__('%s License:', 'favethemes-license-manager'), $productName)),
            esc_html($message)
        );
    }

    /**
     * Show site-specific admin notice if not activated
     */
    public function siteAdminNotice(): void
    {
        // Only show on dashboard and our plugin page
        $screen = get_current_screen();
        if (! $screen || ! in_array($screen->id, ['dashboard', 'toplevel_page_favethemes-license'])) {
            return;
        }

        if (! current_user_can('manage_options')) {
            return;
        }

        if ($this->options->isActivated()) {
            return;
        }

        // Check if Favethemes theme is active
        $theme = wp_get_theme();
        $themeSlug = $theme->get_template();
        if ($themeSlug !== FLM_THEME_SLUG && (! $theme->parent() || $theme->parent()->get_template() !== FLM_THEME_SLUG)) {
            return;
        }

        $productName = ucfirst(FLM_THEME_SLUG);
        $activateUrl = admin_url('admin.php?page=favethemes-license');

        printf(
            '<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
            esc_html(sprintf(__('Your %s theme is not licensed. Activate your license to receive automatic updates.', 'favethemes-license-manager'), $productName)),
            esc_url($activateUrl),
            esc_html__('Activate License', 'favethemes-license-manager')
        );
    }

    /**
     * Get list of sites without active licenses
     */
    private function getUnlicensedSites(): array
    {
        $sites = get_sites(['fields' => 'ids']);
        $unlicensed = [];

        foreach ($sites as $siteId) {
            switch_to_blog($siteId);

            // Check if Favethemes theme is active on this site
            $theme = wp_get_theme();
            $themeSlug = $theme->get_template();
            $isFavetheme = ($themeSlug === FLM_THEME_SLUG) ||
                        ($theme->parent() && $theme->parent()->get_template() === FLM_THEME_SLUG);

            if ($isFavetheme && ! $this->options->isActivated()) {
                $unlicensed[] = [
                    'id' => $siteId,
                    'name' => get_bloginfo('name'),
                    'url' => get_site_url(),
                ];
            }

            restore_current_blog();
        }

        return $unlicensed;
    }

    /**
     * Check if current site is network activated
     */
    public static function isNetworkActivated(): bool
    {
        if (! is_multisite()) {
            return false;
        }

        $plugins = get_site_option('active_sitewide_plugins', []);

        return isset($plugins[FLM_PLUGIN_BASENAME]);
    }
}
