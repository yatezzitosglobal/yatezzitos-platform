<?php

namespace FavethemesLicenseManager\Admin;

use FavethemesLicenseManager\Core\Options;

/**
 * Branded admin notice for theme updates.
 *
 * Renders a persistent, modern card-style notification when a theme update
 * is available. The notice remains visible until the theme is updated.
 */
class UpdateNotice
{
    private Options $options;

    public function __construct(Options $options)
    {
        $this->options = $options;

        add_action('admin_notices', [$this, 'render']);
    }

    /**
     * Render the branded update notice card.
     */
    public function render(): void
    {
        if (! current_user_can('update_themes')) {
            return;
        }

        $updateData = $this->getUpdateData();
        if (! $updateData) {
            return;
        }

        $newVersion = $updateData['new_version'];
        $currentVersion = $this->getCurrentThemeVersion();
        $themeName = ucfirst(FLM_THEME_SLUG);

        if ($this->options->isActivated()) {
            $buttonUrl = admin_url('update-core.php');
            $buttonText = __('Update Now', 'favethemes-license-manager');
        } else {
            $buttonUrl = admin_url('admin.php?page=favethemes-license');
            $buttonText = __('Activate License', 'favethemes-license-manager');
        }

        $this->renderStyles();
        ?>
        <div class="flm-update-notice">
            <div class="flm-update-notice__content">
                <div class="flm-update-notice__icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15v-4H7l5-7v4h4l-5 7z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="flm-update-notice__text">
                    <strong>
                        <?php
                        printf(
                            /* translators: %1$s: theme name, %2$s: new version */
                            esc_html__('%1$s %2$s is available!', 'favethemes-license-manager'),
                            esc_html($themeName),
                            esc_html($newVersion)
                        );
        ?>
                    </strong>
                    <p>
                        <?php
        printf(
            /* translators: %1$s: current version, %2$s: new version */
            esc_html__('You are running version %1$s. Update to %2$s for the latest features and security fixes.', 'favethemes-license-manager'),
            esc_html($currentVersion),
            esc_html($newVersion)
        );
        ?>
                    </p>
                </div>
                <div class="flm-update-notice__action">
                    <a href="<?php echo esc_url($buttonUrl); ?>" class="flm-update-notice__button">
                        <?php echo esc_html($buttonText); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get update data from the update_themes transient.
     *
     * @return array{new_version: string}|null
     */
    private function getUpdateData(): ?array
    {
        $transient = get_site_transient('update_themes');

        if (! is_object($transient) || empty($transient->response[FLM_THEME_SLUG])) {
            return null;
        }

        $data = $transient->response[FLM_THEME_SLUG];

        if (empty($data['new_version'])) {
            return null;
        }

        return $data;
    }

    /**
     * Get the current installed theme version.
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

    /**
     * Inline CSS scoped to .flm-update-notice.
     */
    private function renderStyles(): void
    {
        ?>
        <style>
            .flm-update-notice {
                display: flex;
                align-items: center;
                margin: 15px 0 10px;
                padding: 0;
                background: var(--flm-bg, #ffffff);
                border: 1px solid var(--flm-border, #dcdcde);
                border-left: 4px solid var(--flm-primary, #1e3a5f);
                border-radius: var(--flm-radius, 4px);
                box-shadow: var(--flm-shadow-sm, 0 1px 2px rgba(0, 0, 0, 0.04));
            }
            .flm-update-notice__content {
                display: flex;
                align-items: center;
                gap: 16px;
                padding: 16px;
                width: 100%;
            }
            .flm-update-notice__icon {
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: color-mix(in srgb, var(--flm-primary, #1e3a5f) 10%, transparent);
                color: var(--flm-primary, #1e3a5f);
            }
            .flm-update-notice__text {
                flex: 1;
                min-width: 0;
            }
            .flm-update-notice__text strong {
                display: block;
                font-size: 14px;
                color: var(--flm-text, #1e1e1e);
                margin-bottom: 2px;
            }
            .flm-update-notice__text p {
                margin: 0;
                font-size: 13px;
                color: var(--flm-text-light, #50575e);
            }
            .flm-update-notice__action {
                flex-shrink: 0;
            }
            .flm-update-notice__button {
                display: inline-block;
                padding: 8px 20px;
                background: var(--flm-primary, #1e3a5f);
                color: #ffffff !important;
                font-size: 13px;
                font-weight: 600;
                line-height: 1.4;
                text-decoration: none;
                border-radius: var(--flm-radius, 4px);
                transition: var(--flm-transition, all 0.2s ease);
                white-space: nowrap;
            }
            .flm-update-notice__button:hover,
            .flm-update-notice__button:focus {
                background: var(--flm-primary-light, #2a4d7a);
                color: #ffffff !important;
                text-decoration: none;
            }
        </style>
        <?php
    }
}
