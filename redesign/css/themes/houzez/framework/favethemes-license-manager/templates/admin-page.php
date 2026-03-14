<?php
/**
 * Admin page template for Favethemes License Manager
 */
defined('ABSPATH') || exit;

$is_activated = $options->isActivated();
$license = $options->getLicense();
$activation = $options->getActivation();
$user = $options->getUser();
?>

<div class="wrap flm-admin-wrap <?php echo $is_activated ? 'flm-activated' : 'flm-not-activated'; ?>">
    <!-- Notice Container (hidden for non-activated via CSS) -->
    <div class="flm-notice-container">
        <div class="flm-notice flm-notice-error" style="display: none;" data-notice="error">
            <div class="flm-notice-icon">
                <span class="dashicons dashicons-warning"></span>
            </div>
            <div class="flm-notice-content">
                <p class="flm-notice-message"></p>
            </div>
            <button type="button" class="flm-notice-dismiss">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>

        <div class="flm-notice flm-notice-success" style="display: none;" data-notice="success">
            <div class="flm-notice-icon">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="flm-notice-content">
                <p class="flm-notice-message"></p>
            </div>
            <button type="button" class="flm-notice-dismiss">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
    </div>

    <?php if ($is_activated) { ?>
        <?php
        $env = $activation['environment'] ?? 'live';
        $env_label = $env === 'staging' ? __('Staging', 'favethemes-license-manager') : __('Live', 'favethemes-license-manager');
        $activated_date = $activation['activated_at'] ?? '';
        $user_email = $user['email'] ?? '';
        $is_envato = ($license['source'] ?? '') === 'envato';
        $expires_at = $license['expires_at'] ?? '';
        ?>
        <!-- Activated State - Horizontal Layout -->
        <div class="flm-activated-card flm-activated-horizontal">
            <!-- Compact accent line -->
            <div class="flm-activated-accent"></div>

            <div class="flm-activated-content">
                <!-- Header Row: Status + Actions -->
                <div class="flm-activated-header-row">
                    <div class="flm-activated-status">
                        <span class="flm-status-check">
                            <span class="dashicons dashicons-yes-alt"></span>
                        </span>
                        <div class="flm-status-text">
                            <h2 class="flm-activated-title">
                                <?php esc_html_e('License Activated', 'favethemes-license-manager'); ?>
                            </h2>
                            <?php if (! empty($user_email)) { ?>
                            <p class="flm-connected-as">
                                <?php
                                printf(
                                    /* translators: %s: user email address */
                                    esc_html__('Connected as %s', 'favethemes-license-manager'),
                                    '<strong>'.esc_html($user_email).'</strong>'
                                );
                                ?>
                            </p>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="flm-activated-actions">
                        <a href="<?php echo esc_url(FLM_PORTAL_URL.'/dashboard'); ?>"
                           class="button button-primary flm-portal-button"
                           target="_blank"
                           rel="noopener noreferrer">
                            <span class="dashicons dashicons-external"></span>
                            <?php esc_html_e('Manage', 'favethemes-license-manager'); ?>
                        </a>

                        <button type="button"
                                class="flm-check-status-link"
                                data-action="check-status">
                            <span class="flm-button-text"><?php esc_html_e('Check Status', 'favethemes-license-manager'); ?></span>
                            <span class="flm-button-loader" style="display: none;">
                                <span class="spinner is-active"></span>
                            </span>
                        </button>

                        <button type="button"
                                class="flm-deactivate-link"
                                data-action="deactivate">
                            <?php esc_html_e('Deactivate', 'favethemes-license-manager'); ?>
                        </button>

                        <button type="button"
                                class="flm-force-deactivate-link"
                                id="flm-force-clear-btn"
                                data-action="force-deactivate"
                                style="display: none;"
                                title="<?php esc_attr_e('Clear local license data without contacting the server', 'favethemes-license-manager'); ?>">
                            <?php esc_html_e('Force Clear', 'favethemes-license-manager'); ?>
                        </button>
                    </div>
                </div>

                <!-- Info Row -->
                <div class="flm-license-info-row">
                    <div class="flm-info-cell">
                        <span class="flm-info-label"><?php esc_html_e('Domain', 'favethemes-license-manager'); ?></span>
                        <span class="flm-info-value"><?php echo esc_html($activation['domain'] ?? $options->getCurrentDomain()); ?></span>
                    </div>

                    <div class="flm-info-cell">
                        <span class="flm-info-label"><?php esc_html_e('Environment', 'favethemes-license-manager'); ?></span>
                        <span class="flm-info-value">
                            <span class="flm-env-badge flm-env-<?php echo esc_attr($env); ?>">
                                <?php echo esc_html($env_label); ?>
                            </span>
                        </span>
                    </div>

                    <div class="flm-info-cell">
                        <span class="flm-info-label">
                            <?php echo $is_envato ? esc_html__('Purchased', 'favethemes-license-manager') : esc_html__('Activated', 'favethemes-license-manager'); ?>
                        </span>
                        <span class="flm-info-value">
                            <?php
                            $date_value = $is_envato ? ($license['purchased_at'] ?? '') : $activated_date;
        if ($date_value) {
            echo esc_html(date_i18n(get_option('date_format'), strtotime($date_value)));
        } else {
            echo esc_html__('N/A', 'favethemes-license-manager');
        }
        ?>
                        </span>
                    </div>

                    <?php if ($expires_at) { ?>
                    <div class="flm-info-cell">
                        <span class="flm-info-label"><?php esc_html_e('Support Until', 'favethemes-license-manager'); ?></span>
                        <span class="flm-info-value">
                            <?php
        $expires_ts = strtotime($expires_at);
                        $is_expired = $expires_ts && $expires_ts < time();
                        ?>
                            <span class="<?php echo $is_expired ? 'flm-date-expired' : ''; ?>">
                                <?php echo esc_html(date_i18n(get_option('date_format'), $expires_ts)); ?>
                            </span>
                        </span>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    <?php } else { ?>
        <!-- Not Activated State - Full Welcome Card -->
        <div class="flm-welcome-card">
            <!-- Decorative Header Gradient -->
            <div class="flm-welcome-gradient"></div>

            <div class="flm-welcome-content">
                <!-- Floating Brand Icon -->
                <div class="flm-brand-icon">
                    <span class="dashicons dashicons-shield-alt"></span>
                </div>

                <!-- Header -->
                <div class="flm-welcome-header">
                    <h2 class="flm-welcome-title">
                        <?php esc_html_e('Activate Your License', 'favethemes-license-manager'); ?>
                    </h2>
                    <p class="flm-welcome-subtitle">
                        <?php esc_html_e('Get the most out of your theme with automatic updates, security patches, and the Houzez Studio template library.', 'favethemes-license-manager'); ?>
                    </p>
                </div>

                <!-- Benefits List -->
                <ul class="flm-benefits-list">
                    <li class="flm-benefit-item">
                        <span class="flm-benefit-icon">
                            <span class="dashicons dashicons-update"></span>
                        </span>
                        <div class="flm-benefit-content">
                            <span class="flm-benefit-title"><?php esc_html_e('Automatic Updates', 'favethemes-license-manager'); ?></span>
                            <span class="flm-benefit-description"><?php esc_html_e('Receive theme and plugin updates with one click, directly from your dashboard.', 'favethemes-license-manager'); ?></span>
                        </div>
                    </li>
                    <li class="flm-benefit-item">
                        <span class="flm-benefit-icon">
                            <span class="dashicons dashicons-shield"></span>
                        </span>
                        <div class="flm-benefit-content">
                            <span class="flm-benefit-title"><?php esc_html_e('Security & Bug Fixes', 'favethemes-license-manager'); ?></span>
                            <span class="flm-benefit-description"><?php esc_html_e('Critical security patches and bug fixes delivered automatically to keep your site safe.', 'favethemes-license-manager'); ?></span>
                        </div>
                    </li>
                    <li class="flm-benefit-item">
                        <span class="flm-benefit-icon">
                            <span class="dashicons dashicons-layout"></span>
                        </span>
                        <div class="flm-benefit-content">
                            <span class="flm-benefit-title"><?php esc_html_e('Studio Library Access', 'favethemes-license-manager'); ?></span>
                            <span class="flm-benefit-description"><?php esc_html_e('Import professionally designed templates, blocks, and page layouts from the Houzez Studio library directly in Elementor.', 'favethemes-license-manager'); ?></span>
                        </div>
                    </li>
                    <li class="flm-benefit-item">
                        <span class="flm-benefit-icon">
                            <span class="dashicons dashicons-star-filled"></span>
                        </span>
                        <div class="flm-benefit-content">
                            <span class="flm-benefit-title"><?php esc_html_e('New Features', 'favethemes-license-manager'); ?></span>
                            <span class="flm-benefit-description"><?php esc_html_e('Be the first to access new features and improvements as they are released.', 'favethemes-license-manager'); ?></span>
                        </div>
                    </li>
                </ul>

                <!-- Connect Button -->
                <div class="flm-connect-action">
                    <button type="button"
                            class="button button-primary flm-connect-button"
                            id="flm-connect-activate"
                            data-action="connect-activate">
                        <span class="flm-button-text">
                            <span class="dashicons dashicons-admin-links"></span>
                            <?php esc_html_e('Connect & Activate License', 'favethemes-license-manager'); ?>
                        </span>
                        <span class="flm-button-loader" style="display: none;">
                            <span class="spinner is-active"></span>
                            <?php esc_html_e('Connecting...', 'favethemes-license-manager'); ?>
                        </span>
                    </button>
                </div>

                <!-- Helper Text -->
                <p class="flm-helper-text">
                    <span class="dashicons dashicons-lock"></span>
                    <?php esc_html_e("You'll be securely redirected to complete activation.", 'favethemes-license-manager'); ?>
                </p>
            </div>
        </div>

        <!-- Without Activation Note -->
        <div class="flm-manual-note">
            <div class="flm-manual-note-icon">
                <span class="dashicons dashicons-info-outline"></span>
            </div>
            <div class="flm-manual-note-content">
                <p class="flm-manual-note-title"><?php esc_html_e('Without activation', 'favethemes-license-manager'); ?></p>
                <p class="flm-manual-note-text"><?php esc_html_e('Theme and plugin updates must be downloaded manually from ThemeForest and uploaded via the WordPress dashboard. Bundled plugins can be found in the "plugins" folder inside the downloaded zip file, and installed through Plugins > Add New > Upload Plugin.', 'favethemes-license-manager'); ?></p>
            </div>
        </div>
    <?php } ?>

    <!-- Deactivation Confirmation Modal -->
    <div class="flm-modal" id="flm-deactivate-modal" style="display: none;">
        <div class="flm-modal-overlay"></div>
        <div class="flm-modal-content flm-modal-content-deactivate">
            <div class="flm-modal-header flm-modal-header-deactivate">
                <div class="flm-modal-header-left">
                    <div class="flm-modal-header-icon">
                        <span class="dashicons dashicons-shield"></span>
                    </div>
                    <h2><?php esc_html_e('Deactivate License', 'favethemes-license-manager'); ?></h2>
                </div>
                <button type="button" class="flm-modal-close">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
            </div>
            <div class="flm-modal-body flm-modal-body-deactivate">
                <div class="flm-modal-icon flm-modal-icon-warning">
                    <span class="dashicons dashicons-warning"></span>
                </div>
                <p class="flm-modal-body-heading"><?php esc_html_e('Are you sure you want to deactivate?', 'favethemes-license-manager'); ?></p>
                <p class="flm-modal-body-subtext"><?php esc_html_e('Deactivating will disconnect this site from your license. The following will stop working immediately:', 'favethemes-license-manager'); ?></p>
                <ul class="flm-modal-consequences">
                    <li>
                        <span class="dashicons dashicons-update"></span>
                        <?php esc_html_e('Automatic theme and plugin updates', 'favethemes-license-manager'); ?>
                    </li>
                    <li>
                        <span class="dashicons dashicons-admin-plugins"></span>
                        <?php esc_html_e('One-click bundled plugin installation', 'favethemes-license-manager'); ?>
                    </li>
                    <li>
                        <span class="dashicons dashicons-layout"></span>
                        <?php esc_html_e('Studio library and demo import access', 'favethemes-license-manager'); ?>
                    </li>
                </ul>
                <div class="flm-modal-reassurance">
                    <span class="dashicons dashicons-info-outline"></span>
                    <span><?php esc_html_e('Your license will not be revoked. You can reactivate it anytime from this page.', 'favethemes-license-manager'); ?></span>
                </div>
            </div>
            <div class="flm-modal-footer flm-modal-footer-deactivate">
                <button type="button" class="button button-secondary flm-modal-cancel">
                    <?php esc_html_e('Keep Active', 'favethemes-license-manager'); ?>
                </button>
                <button type="button" class="button flm-modal-confirm flm-button-deactivate" data-action="confirm-deactivate">
                    <span class="flm-button-text">
                        <span class="dashicons dashicons-dismiss"></span>
                        <?php esc_html_e('Deactivate License', 'favethemes-license-manager'); ?>
                    </span>
                    <span class="flm-button-loader" style="display: none;">
                        <span class="spinner is-active"></span>
                        <?php esc_html_e('Deactivating...', 'favethemes-license-manager'); ?>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Force Deactivation Confirmation Modal -->
    <div class="flm-modal" id="flm-force-deactivate-modal" style="display: none;">
        <div class="flm-modal-overlay"></div>
        <div class="flm-modal-content">
            <div class="flm-modal-header">
                <h2><?php esc_html_e('Force Clear License Data', 'favethemes-license-manager'); ?></h2>
                <button type="button" class="flm-modal-close">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
            </div>
            <div class="flm-modal-body">
                <div class="flm-modal-icon flm-modal-icon-warning">
                    <span class="dashicons dashicons-warning"></span>
                </div>
                <p><?php esc_html_e('This will clear your local license data without contacting the license server.', 'favethemes-license-manager'); ?></p>
                <p class="flm-modal-note"><?php esc_html_e('Use this only if normal deactivation fails (e.g., server unreachable or license already removed from portal). The activation slot may remain occupied on the server until cleared from the portal.', 'favethemes-license-manager'); ?></p>
            </div>
            <div class="flm-modal-footer">
                <button type="button" class="button button-secondary flm-modal-cancel">
                    <?php esc_html_e('Cancel', 'favethemes-license-manager'); ?>
                </button>
                <button type="button" class="button flm-button-danger flm-modal-confirm" data-action="confirm-force-deactivate">
                    <span class="flm-button-text">
                        <?php esc_html_e('Force Clear', 'favethemes-license-manager'); ?>
                    </span>
                    <span class="flm-button-loader" style="display: none;">
                        <span class="spinner is-active"></span>
                        <?php esc_html_e('Clearing...', 'favethemes-license-manager'); ?>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
