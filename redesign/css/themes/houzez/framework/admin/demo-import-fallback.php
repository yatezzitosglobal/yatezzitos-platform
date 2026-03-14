<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$plugin_slug = 'one-click-demo-import';
$plugin_file = 'one-click-demo-import/one-click-demo-import.php';
$all_plugins = get_plugins();
$is_installed = isset( $all_plugins[ $plugin_file ] );
$plugins_url  = admin_url( 'admin.php?page=favethemes-portal-plugins' );

if ( $is_installed ) {
    $status_label = __( 'Installed — Inactive', 'houzez' );
    $status_class = 'houzez-status-warning';
    $action_label = __( 'Activate Plugin', 'houzez' );
    $action_icon  = 'dashicons-admin-plugins';
    $message      = __( 'The One Click Demo Import plugin is installed but not activated. Go to the Plugins page to activate it.', 'houzez' );
} else {
    $status_label = __( 'Not Installed', 'houzez' );
    $status_class = 'houzez-status-error';
    $action_label = __( 'Install Plugin', 'houzez' );
    $action_icon  = 'dashicons-download';
    $message      = __( 'The One Click Demo Import plugin is required to import demo content. Go to the Plugins page to install it.', 'houzez' );
}
?>

<div class="wrap houzez-template-library">
    <div class="houzez-header">
        <div class="houzez-header-content">
            <div class="houzez-logo">
                <h1><?php esc_html_e( 'Demo Import', 'houzez' ); ?></h1>
            </div>
            <div class="houzez-header-actions">
                <a href="<?php echo esc_url( $plugins_url ); ?>" class="houzez-btn houzez-btn-secondary">
                    <i class="dashicons dashicons-admin-plugins"></i>
                    <?php esc_html_e( 'Manage Plugins', 'houzez' ); ?>
                </a>
            </div>
        </div>
    </div>

    <div class="houzez-dashboard">
        <div class="houzez-main-card">
            <div class="houzez-card-header">
                <h2>
                    <i class="dashicons dashicons-download"></i>
                    <?php esc_html_e( 'One Click Demo Import', 'houzez' ); ?>
                </h2>
                <div class="houzez-status-badge <?php echo esc_attr( $status_class ); ?>">
                    <?php echo esc_html( $status_label ); ?>
                </div>
            </div>
            <div class="houzez-card-body">
                <div class="houzez-actions">
                    <div class="houzez-action">
                        <div class="houzez-action-icon">
                            <i class="dashicons <?php echo esc_attr( $action_icon ); ?>"></i>
                        </div>
                        <div class="houzez-action-content">
                            <h4><?php echo esc_html( $action_label ); ?></h4>
                            <p><?php echo esc_html( $message ); ?></p>
                            <a href="<?php echo esc_url( $plugins_url ); ?>" class="houzez-btn houzez-btn-primary">
                                <i class="dashicons <?php echo esc_attr( $action_icon ); ?>"></i>
                                <?php echo esc_html( $action_label ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
