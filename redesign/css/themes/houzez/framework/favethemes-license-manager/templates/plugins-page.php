<?php
/**
 * Portal Plugins Page Template
 *
 * @var bool $isActivated
 * @var array|null $activation
 */
if (! defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap flm-plugins-wrap">
    <div class="flm-plugins-header">
        <div class="flm-plugins-header-content">
            <div class="flm-plugins-header-icon">
                <span class="dashicons dashicons-admin-plugins"></span>
            </div>
            <div class="flm-plugins-header-text">
                <h1><?php esc_html_e('Manage Plugins', 'favethemes-license-manager'); ?></h1>
                <p class="flm-plugins-subtitle">
                    <?php esc_html_e('Install and manage plugins for the site.', 'favethemes-license-manager'); ?>
                </p>
            </div>
        </div>
        <div class="flm-plugins-header-actions">
            <button type="button" class="button flm-refresh-btn" id="flm-refresh-plugins">
                <span class="dashicons dashicons-update"></span>
                <?php esc_html_e('Refresh', 'favethemes-license-manager'); ?>
            </button>
        </div>
    </div>

    <?php if (! $isActivated) { ?>
        <!-- License Required Banner -->
        <div class="flm-license-banner">
            <span class="dashicons dashicons-lock"></span>
            <span class="flm-license-banner-text">
                <?php esc_html_e('Activate your license to download and update plugins.', 'favethemes-license-manager'); ?>
            </span>
            <a href="<?php echo esc_url(admin_url('admin.php?page=favethemes-license')); ?>" class="button button-primary flm-license-banner-btn">
                <span class="dashicons dashicons-admin-network"></span>
                <?php esc_html_e('Activate License', 'favethemes-license-manager'); ?>
            </a>
        </div>
    <?php } ?>

    <!-- Stats Bar -->
    <div class="flm-plugins-stats" id="flm-plugins-stats">
        <div class="flm-stat-item">
            <span class="flm-stat-number" id="stat-total">-</span>
            <span class="flm-stat-label"><?php esc_html_e('Total', 'favethemes-license-manager'); ?></span>
        </div>
        <div class="flm-stat-item flm-stat-active">
            <span class="flm-stat-number" id="stat-active">-</span>
            <span class="flm-stat-label"><?php esc_html_e('Active', 'favethemes-license-manager'); ?></span>
        </div>
        <div class="flm-stat-item flm-stat-inactive">
            <span class="flm-stat-number" id="stat-inactive">-</span>
            <span class="flm-stat-label"><?php esc_html_e('Inactive', 'favethemes-license-manager'); ?></span>
        </div>
        <div class="flm-stat-item flm-stat-updates">
            <span class="flm-stat-number" id="stat-updates">-</span>
            <span class="flm-stat-label"><?php esc_html_e('Updates', 'favethemes-license-manager'); ?></span>
        </div>
        <div class="flm-stat-item flm-stat-required">
            <span class="flm-stat-number" id="stat-required">-</span>
            <span class="flm-stat-label"><?php esc_html_e('Required', 'favethemes-license-manager'); ?></span>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="flm-bulk-actions" id="flm-bulk-actions" style="display: none;">
        <button type="button" class="button button-primary" id="flm-bulk-install-btn" style="display: none;">
            <span class="dashicons dashicons-download"></span>
            <span class="flm-bulk-text"><?php esc_html_e('Install All Required', 'favethemes-license-manager'); ?></span>
            <span class="flm-bulk-count" id="bulk-required-count">0</span>
        </button>
        <button type="button" class="button button-primary" id="flm-activate-all-btn" style="display: none;">
            <span class="dashicons dashicons-controls-play"></span>
            <span class="flm-bulk-text"><?php esc_html_e('Activate All Required', 'favethemes-license-manager'); ?></span>
            <span class="flm-bulk-count" id="bulk-activate-count">0</span>
        </button>
        <button type="button" class="button button-primary" id="flm-update-all-btn" style="display: none;">
            <span class="dashicons dashicons-update"></span>
            <span class="flm-bulk-text"><?php esc_html_e('Update All', 'favethemes-license-manager'); ?></span>
            <span class="flm-bulk-count" id="bulk-update-count">0</span>
        </button>
    </div>

    <!-- Filter Tabs -->
    <div class="flm-plugins-filters">
        <button type="button" class="flm-filter-btn active" data-filter="all">
            <?php esc_html_e('All', 'favethemes-license-manager'); ?>
            <span class="flm-filter-count" id="filter-count-all">0</span>
        </button>
        <button type="button" class="flm-filter-btn" data-filter="active">
            <?php esc_html_e('Active', 'favethemes-license-manager'); ?>
            <span class="flm-filter-count" id="filter-count-active">0</span>
        </button>
        <button type="button" class="flm-filter-btn" data-filter="inactive">
            <?php esc_html_e('Inactive', 'favethemes-license-manager'); ?>
            <span class="flm-filter-count" id="filter-count-inactive">0</span>
        </button>
        <button type="button" class="flm-filter-btn" data-filter="updates">
            <?php esc_html_e('Updates Available', 'favethemes-license-manager'); ?>
            <span class="flm-filter-count" id="filter-count-updates">0</span>
        </button>
        <button type="button" class="flm-filter-btn" data-filter="not-installed">
            <?php esc_html_e('Not Installed', 'favethemes-license-manager'); ?>
            <span class="flm-filter-count" id="filter-count-not-installed">0</span>
        </button>
    </div>

    <!-- Loading State -->
    <div class="flm-plugins-loading" id="flm-plugins-loading">
        <div class="flm-spinner"></div>
        <p><?php esc_html_e('Loading plugins from portal...', 'favethemes-license-manager'); ?></p>
    </div>

    <!-- Error State -->
    <div class="flm-plugins-error" id="flm-plugins-error" style="display: none;">
        <span class="dashicons dashicons-warning"></span>
        <p id="flm-error-message"></p>
        <button type="button" class="button" id="flm-retry-btn">
            <?php esc_html_e('Try Again', 'favethemes-license-manager'); ?>
        </button>
    </div>

    <!-- Plugins Grid -->
    <div class="flm-plugins-grid" id="flm-plugins-grid" style="display: none;"></div>

    <!-- Empty State -->
    <div class="flm-plugins-empty" id="flm-plugins-empty" style="display: none;">
        <span class="dashicons dashicons-plugins-checked"></span>
        <p><?php esc_html_e('No plugins found matching your filter.', 'favethemes-license-manager'); ?></p>
    </div>
</div>

<!-- Plugin Card Template -->
<script type="text/html" id="tmpl-flm-plugin-card">
    <div class="flm-plugin-card flm-priority-{{ data.priority || 'recommended' }}" data-slug="{{ data.slug }}" data-status="{{ data.status }}" data-priority="{{ data.priority || 'recommended' }}">
        <div class="flm-plugin-card-header">
            <# if (data.thumbnail) { #>
                <div class="flm-plugin-thumbnail">
                    <img src="{{ data.thumbnail }}" alt="{{ data.name }}">
                </div>
            <# } else { #>
                <div class="flm-plugin-icon">
                    <span class="dashicons dashicons-admin-plugins"></span>
                </div>
            <# } #>
            <div class="flm-plugin-badges">
                <# if (data.priority === 'required') { #>
                    <span class="flm-badge flm-badge-required"><?php esc_html_e('Required', 'favethemes-license-manager'); ?></span>
                <# } else if (data.priority === 'recommended') { #>
                    <span class="flm-badge flm-badge-recommended"><?php esc_html_e('Recommended', 'favethemes-license-manager'); ?></span>
                <# } else if (data.priority === 'optional') { #>
                    <span class="flm-badge flm-badge-optional"><?php esc_html_e('Optional', 'favethemes-license-manager'); ?></span>
                <# } #>
                <# if (data.has_update) { #>
                    <span class="flm-badge flm-badge-update"><?php esc_html_e('Update', 'favethemes-license-manager'); ?></span>
                <# } #>
                <# if (data.is_active_local) { #>
                    <span class="flm-badge flm-badge-active"><span class="flm-status-dot"></span><?php esc_html_e('Active', 'favethemes-license-manager'); ?></span>
                <# } else if (data.is_installed) { #>
                    <span class="flm-badge flm-badge-inactive"><span class="flm-status-dot"></span><?php esc_html_e('Inactive', 'favethemes-license-manager'); ?></span>
                <# } #>
            </div>
        </div>

        <div class="flm-plugin-card-body">
            <h3 class="flm-plugin-name">{{ data.name }}</h3>
            <p class="flm-plugin-description">{{ data.description || '<?php esc_html_e('No description available.', 'favethemes-license-manager'); ?>' }}</p>

            <div class="flm-plugin-meta">
                <span class="flm-plugin-author">
                    <span class="dashicons dashicons-admin-users"></span>
                    {{ data.author_name || 'Favethemes' }}
                </span>
                <# if (data.source !== 'wordpress.org' && data.latest_version) { #>
                    <span class="flm-plugin-version">
                        <span class="dashicons dashicons-tag"></span>
                        <# if (data.is_installed && data.installed_version) { #>
                            v{{ data.installed_version }}
                            <# if (data.has_update) { #>
                                <span class="flm-version-arrow">&rarr;</span> v{{ data.latest_version }}
                            <# } #>
                        <# } else { #>
                            v{{ data.latest_version }}
                        <# } #>
                    </span>
                <# } else if (data.source === 'wordpress.org' && data.has_update && data.latest_version) { #>
                    <span class="flm-plugin-version">
                        <span class="dashicons dashicons-tag"></span>
                        <# if (data.installed_version) { #>
                            v{{ data.installed_version }} <span class="flm-version-arrow">&rarr;</span> v{{ data.latest_version }}
                        <# } else { #>
                            <?php esc_html_e('Update to', 'favethemes-license-manager'); ?> v{{ data.latest_version }}
                        <# } #>
                    </span>
                <# } #>
            </div>
        </div>

        <div class="flm-plugin-card-footer">
            <# if (!flmPlugins.isActivated) { #>
                <# if (!data.is_installed || data.has_update) { #>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=favethemes-license')); ?>" class="button flm-license-link">
                        <span class="dashicons dashicons-admin-network"></span>
                        <?php esc_html_e('Activate License', 'favethemes-license-manager'); ?>
                    </a>
                <# } #>
                <# if (data.is_installed && data.is_active_local) { #>
                    <button type="button" class="button flm-action-btn flm-deactivate-btn" data-action="deactivate" data-slug="{{ data.slug }}" data-plugin-file="{{ data.plugin_file }}">
                        <?php esc_html_e('Deactivate', 'favethemes-license-manager'); ?>
                    </button>
                <# } else if (data.is_installed && !data.is_active_local) { #>
                    <button type="button" class="button button-primary flm-action-btn" data-action="activate" data-slug="{{ data.slug }}" data-plugin-file="{{ data.plugin_file }}">
                        <span class="dashicons dashicons-controls-play"></span>
                        <?php esc_html_e('Activate', 'favethemes-license-manager'); ?>
                    </button>
                <# } #>
            <# } else { #>
                <# if (!data.is_installed) { #>
                    <button type="button" class="button button-primary flm-action-btn" data-action="install" data-slug="{{ data.slug }}" data-plugin-file="{{ data.plugin_file }}" data-download-url="{{ data.download_url }}" data-source="{{ data.source }}" data-wp-org-slug="{{ data.wp_org_slug || data.slug }}">
                        <span class="dashicons dashicons-download"></span>
                        <?php esc_html_e('Install', 'favethemes-license-manager'); ?>
                    </button>
                <# } else if (data.has_update) { #>
                    <button type="button" class="button button-primary flm-action-btn" data-action="update" data-slug="{{ data.slug }}" data-plugin-file="{{ data.plugin_file }}" data-download-url="{{ data.download_url }}" data-source="{{ data.source }}">
                        <span class="dashicons dashicons-update"></span>
                        <?php esc_html_e('Update', 'favethemes-license-manager'); ?>
                    </button>
                    <# if (data.is_active_local) { #>
                        <button type="button" class="button flm-action-btn flm-deactivate-btn" data-action="deactivate" data-slug="{{ data.slug }}" data-plugin-file="{{ data.plugin_file }}">
                            <?php esc_html_e('Deactivate', 'favethemes-license-manager'); ?>
                        </button>
                    <# } #>
                <# } else if (data.is_active_local) { #>
                    <button type="button" class="button flm-action-btn flm-deactivate-btn" data-action="deactivate" data-slug="{{ data.slug }}" data-plugin-file="{{ data.plugin_file }}">
                        <?php esc_html_e('Deactivate', 'favethemes-license-manager'); ?>
                    </button>
                <# } else { #>
                    <button type="button" class="button button-primary flm-action-btn" data-action="activate" data-slug="{{ data.slug }}" data-plugin-file="{{ data.plugin_file }}">
                        <span class="dashicons dashicons-controls-play"></span>
                        <?php esc_html_e('Activate', 'favethemes-license-manager'); ?>
                    </button>
                <# } #>
            <# } #>
        </div>

        <div class="flm-plugin-card-overlay" style="display: none;">
            <div class="flm-spinner"></div>
            <span class="flm-overlay-text"></span>
        </div>
    </div>
</script>
