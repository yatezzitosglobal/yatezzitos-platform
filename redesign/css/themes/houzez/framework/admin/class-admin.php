<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Houzez_Admin {

    public static $instance;
    private $template_path = HOUZEZ_FRAMEWORK . 'admin/';

    public function __construct() {

        add_action( 'admin_menu', array( $this, 'houzez_register_admin_pages' ) );
        add_action( 'admin_menu', array( $this, 'houzez_remove_parent_menu' ) );
        // Legacy plugin AJAX handlers removed — now handled by FLM PluginsPage
        // Legacy purchase code AJAX handlers removed — now handled by FLM AdminPage
        add_action('wp_ajax_houzez_feedback', array( $this, 'houzez_feedback'));

        // https://github.com/elementor/elementor/issues/6022
		add_action( 'admin_init', function() {
			if ( did_action( 'elementor/loaded' ) ) {
				remove_action( 'admin_init', [ \Elementor\Plugin::$instance->admin, 'maybe_redirect_to_getting_started' ] );
			}
		}, 1 );

        // One-time redirect after theme activation or update (license page or plugins page)
        add_action( 'admin_init', array( $this, 'houzez_maybe_redirect_to_plugins' ) );
        add_action( 'after_switch_theme', array( $this, 'houzez_reset_redirect_flag' ) );
        add_action( 'upgrader_process_complete', array( $this, 'houzez_maybe_reset_on_theme_update' ), 10, 2 );

        // Add modern header to Theme Builder page
        add_action( 'admin_head', array( $this, 'houzez_add_theme_builder_header' ) );

        // Hide admin notices on all Houzez admin pages
        add_action( 'in_admin_header', array( $this, 'houzez_hide_admin_notices' ) );
        add_action( 'admin_head', array( $this, 'houzez_hide_admin_notices_css' ) );
    }

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function houzez_reset_redirect_flag() {
        delete_option( 'houzez_admin_redirect_done' );
    }

    /**
     * Reset the redirect flag when Houzez is updated (upload replace, one-click, or bulk).
     *
     * WordPress fires `upgrader_process_complete` after any upgrade/install.
     * We check all possible shapes of $hook_extra to detect a Houzez theme update.
     */
    public function houzez_maybe_reset_on_theme_update( $upgrader, $hook_extra ) {
        if ( ! isset( $hook_extra['type'] ) || $hook_extra['type'] !== 'theme' ) {
            return;
        }

        $updated_houzez = false;

        // Single theme update (one-click from Themes page)
        if ( isset( $hook_extra['theme'] ) && $hook_extra['theme'] === 'houzez' ) {
            $updated_houzez = true;
        }

        // Bulk theme updates
        if ( isset( $hook_extra['themes'] ) && in_array( 'houzez', $hook_extra['themes'], true ) ) {
            $updated_houzez = true;
        }

        // Theme upload (install/overwrite via zip) — check the destination folder name
        if ( ! $updated_houzez && isset( $upgrader->result['destination_name'] ) && $upgrader->result['destination_name'] === 'houzez' ) {
            $updated_houzez = true;
        }

        if ( $updated_houzez ) {
            delete_option( 'houzez_admin_redirect_done' );
        }
    }

    public function houzez_maybe_redirect_to_plugins() {
        if ( get_option( 'houzez_admin_redirect_done' ) ) {
            return;
        }

        if ( wp_doing_ajax() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
            return;
        }

        if ( isset( $_GET['activate-multi'] ) || is_network_admin() ) {
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        update_option( 'houzez_admin_redirect_done', 1, false );

        if ( function_exists( 'houzez_is_license_activated' ) && houzez_is_license_activated() ) {
            wp_safe_redirect( admin_url( 'admin.php?page=favethemes-portal-plugins' ) );
        } else {
            wp_safe_redirect( admin_url( 'admin.php?page=favethemes-license' ) );
        }
        exit;
    }

	public function houzez_register_admin_pages() {
    	$sub_menus = array();

    	$houzez = houzez_theme_branding();

        add_menu_page(
            $houzez,
            $houzez,
            'manage_options',
            'houzez_dashboard',
            '',
            HOUZEZ_IMAGE.'houzez-icon.svg',
            '5'
        );

        $sub_menus['plugins'] = array(
            'houzez_dashboard',
            esc_html__( 'Plugins', 'houzez' ),
            esc_html__( 'Plugins', 'houzez' ),
            'manage_options',
            'favethemes-portal-plugins',
            array( $this, 'houzez_plugins' ),
        );

        if( class_exists('\HouzezStudio\Houzez_Studio') ) {
        	$sub_menus['houzez_studio'] = array( 
	            'houzez_dashboard', 
	            esc_html__( 'Theme Builder', 'houzez' ),
	            esc_html__( 'Theme Builder', 'houzez' ),
	            'edit_pages', 
	            'edit.php?post_type=fts_builder',
	        );
        }

        if( class_exists('Houzez') ) {
	        $sub_menus['houzez_fbuilder'] = array( 
	            'houzez_dashboard', 
	            esc_html__( 'Fields builder', 'houzez' ),
	            esc_html__( 'Fields builder', 'houzez' ),
	            'manage_options', 
	            'houzez_fbuilder', 
	            array( 'Houzez_Fields_Builder', 'render' )
	        );

			// $sub_menus['houzez-template-library'] = array(
			// 	'houzez_dashboard',
			// 	esc_html__( 'Template Library', 'houzez' ),
			// 	esc_html__( 'Template Library', 'houzez' ),
			// 	'manage_options',
			// 	'houzez-template-library',
			// 	array( 'Houzez_Library', 'admin_page' ),
			// );

			$sub_menus['houzez_image_sizes'] = array(
				'houzez_dashboard',
				esc_html__( 'Media Manager', 'houzez' ),
				esc_html__( 'Media Manager', 'houzez' ),
				'manage_options',
				'houzez_image_sizes',
				array( 'Houzez_Image_Sizes', 'render_page' ),
	        );

	        $sub_menus['houzez_currencies'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Currencies', 'houzez' ),
	            esc_html__( 'Currencies', 'houzez' ),
	            'manage_options',
	            'houzez_currencies',
	            array( 'Houzez_Currencies', 'render' )
	        );

	        $sub_menus['fcc_api_settings'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Currency Switcher', 'houzez' ),
	            esc_html__( 'Currency Switcher', 'houzez' ),
	            'manage_options',
	            'fcc_api_settings',
	            array( 'FCC_API_Settings', 'render' )
	        );

	        $sub_menus['houzez_post_types'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Post Types', 'houzez' ),
	            esc_html__( 'Post Types', 'houzez' ),
	            'manage_options',
	            'houzez_post_types',
	            array( 'Houzez_Post_Type', 'render' )
	        );

	        $sub_menus['houzez_taxonomies'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Taxonomies', 'houzez' ),
	            esc_html__( 'Taxonomies', 'houzez' ),
	            'manage_options',
	            'houzez_taxonomies',
	            array( 'Houzez_Taxonomies', 'render' )
	        );

	        $sub_menus['houzez_permalinks'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Permalinks', 'houzez' ),
	            esc_html__( 'Permalinks', 'houzez' ),
	            'manage_options',
	            'houzez_permalinks',
	            array( 'Houzez_Permalinks', 'render' )
	        );

	        $sub_menus['houzez_import_locations'] = array(
	            'houzez_dashboard',
	            esc_html__( 'Import Locations', 'houzez' ),
	            esc_html__( 'Import Locations', 'houzez' ),
	            'manage_options',
	            'import_locations',
	            array( 'Houzez_Import_Locations', 'render' )
	        );
	    }

        // $sub_menus['mobile_app'] = array(
        //     'houzez_dashboard',
        //     esc_html__( 'Mobile App', 'houzez' ),
        //     esc_html__( 'Mobile App', 'houzez' ),
        //     'manage_options',
        //     'houzez_mobile_app',
        //     array( $this, 'houzez_mobile_app' ),
        // );

	    // Add filter for third party uses
        $sub_menus = apply_filters( 'houzez_admin_sub_menus', $sub_menus, 20 );

        $sub_menus['documentation'] = array(
            'houzez_dashboard',
            esc_html__( 'Documentation', 'houzez' ),
            esc_html__( 'Documentation', 'houzez' ),
            'manage_options',
            'houzez_help',
            array( $this, 'houzez_documentation' ),
        );

        $sub_menus['feedback'] = array(
            'houzez_dashboard',
            esc_html__( 'Feedback', 'houzez' ),
            esc_html__( 'Feedback', 'houzez' ),
            'manage_options',
            'houzez_feedback',
            array( $this, 'houzez_feedback_page' ),
        );

        // Purchase Code menu removed — now handled by FLM AdminPage (License)

		if ( is_plugin_active( 'one-click-demo-import/one-click-demo-import.php' ) ) {
			$sub_menus['demo_import'] = array(
				'houzez_dashboard',
				esc_html__( 'Demo Import', 'houzez' ),
				esc_html__( 'Demo Import', 'houzez' ),
				'manage_options',
				'admin.php?page=houzez-one-click-demo-import',
			);
		} else {
			$sub_menus['demo_import'] = array(
				'houzez_dashboard',
				esc_html__( 'Demo Import', 'houzez' ),
				esc_html__( 'Demo Import', 'houzez' ),
				'manage_options',
				'houzez_demo_import',
				array( $this, 'houzez_demo_import_fallback' ),
			);
		}

		/*$sub_menus['houzez_new_html'] = array(
	            'houzez_dashboard',
	            esc_html__( 'New HTML', 'houzez' ),
	            esc_html__( 'New HTML', 'houzez' ),
	            'manage_options',
	            'houzez_new_html',
	            array( 'Houzez_HTML', 'render' )
	        );*/

        if ( $sub_menus ) {
            foreach ( $sub_menus as $sub_menu ) {
                call_user_func_array( 'add_submenu_page', $sub_menu );
            }
        }
	}

	public function houzez_feedback() {

		$headers   = array();
		$current_user = wp_get_current_user();

		$target_email   = is_email("houzez@favethemes.com");
		$website        = get_bloginfo( 'name' );
		$site_url       = network_site_url( '/' );
		$sender_name    = $current_user->display_name;
		$sender_email   = sanitize_email( $_POST['email'] );
		$sender_email   = is_email( $sender_email ); 
		$sender_subject = sanitize_text_field( $_POST['subject'] );
		$message        = stripslashes( $_POST['message'] );

		$nonce = $_POST['feedback_nonce'];
        if (!wp_verify_nonce( $nonce, 'houzez_feedback_security') ) {
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Invalid Nonce!', 'houzez')
            ));
            wp_die();
        }

		if (!$sender_email) {
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Email address is Invalid!', 'houzez')
            ));
            wp_die();
        }

        if ( empty($message) ) {
            echo json_encode(array(
                'success' => false,
                'msg' => esc_html__('Your message is empty!', 'houzez')
            ));
            wp_die();
        }

        $subject = sprintf( esc_html__('New Feedback by %s from %s', 'houzez'), $sender_name, $website );

        $body = esc_html__("You have received new message from: ", 'houzez') . $sender_name . " <br/>";

        if ( ! empty( $website ) ) {
            $body .= esc_html__( "Website : ", 'houzez' ) . '<a href="' . esc_url( $site_url ) . '" target="_blank">' . $website . "</a><br/><br/>";
        }

        if ( ! empty( $sender_subject ) ) {
            $body .= esc_html__( "Subject : ", 'houzez' ) .$sender_subject. "<br/>";
        }

        $body .= "<br/>" . esc_html__("Message:", 'houzez') . " <br/>";
        $body .= wpautop( $message ) . " <br/>";
        $body .= sprintf( esc_html__( 'You can contact %s via email %s', 'houzez'), $sender_name, $sender_email );

		$headers[] = "Reply-To: $sender_name <$sender_email>";
		$headers[] = "Content-Type: text/html; charset=UTF-8";
		$headers   = apply_filters( "houzez_feedback_mail_header", $headers ); 

		if ( wp_mail( $target_email, $subject, $body, $headers ) ) {
            echo json_encode( array(
                'success' => true,
                'msg' => esc_html__("Thank you for your feedback!", 'houzez')
            ));
        } else {
            echo json_encode(array(
                    'success' => false,
                    'msg' => esc_html__("Server Error: Make sure Email function working on your server!", 'houzez')
                )
            );
        }
        wp_die();
	}

	public function houzez_documentation() {
		require_once $this->template_path . 'documentation.php';
	}

	public function houzez_plugins() {
		$flm = \FavethemesLicenseManager\Core\Plugin::getInstance();
		$options = $flm->getOptions();
		$isActivated = $options->isActivated();
		$activation = $isActivated ? $options->getActivation() : null;
		include FLM_PLUGIN_DIR . 'templates/plugins-page.php';
	}

	public function houzez_feedback_page() {
		require_once $this->template_path . 'feedback.php';
	}

	public function houzez_demo_import_fallback() {
		require_once $this->template_path . 'demo-import-fallback.php';
	}

	public function houzez_mobile_app() {
		require_once $this->template_path . 'mobile-app.php';
	}

	public function houzez_hide_admin_notices() {
		global $parent_file;
		if ( $parent_file === 'houzez_dashboard' ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
			remove_all_actions( 'network_admin_notices' );
			remove_all_actions( 'user_admin_notices' );
		}
	}

	public function houzez_hide_admin_notices_css() {
		global $parent_file;
		if ( $parent_file === 'houzez_dashboard' ) {
			echo '<style>.notice, .update-nag, .updated, .error, .is-dismissible { display: none !important; }</style>';
		}
	}

	public function houzez_remove_parent_menu() {
		global $submenu;
		unset( $submenu['houzez_dashboard'][0] );
	}

    public function houzez_add_theme_builder_header() {
        // Only add header on the fts_builder post type edit page
        $screen = get_current_screen();
        if ( ! $screen || $screen->post_type !== 'fts_builder' || $screen->base !== 'edit' ) {
            return;
        }
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Find the wrap div and add our header
			var $wrap = $('.wrap');
			if ($wrap.length) {
				// Remove the default h1 title
				$wrap.find('h1.wp-heading-inline').hide();
				
				// Add our modern header
				var headerHtml = `
					<div class="houzez-header" style="margin: -10px -20px 15px -22px;">
						<div class="houzez-header-content">
							<div class="houzez-logo">
								<h1><?php esc_html_e('Theme Builder', 'houzez'); ?></h1>
							</div>
							<div class="houzez-header-actions">
								<a href="<?php echo esc_url(admin_url('post-new.php?post_type=fts_builder')); ?>" class="houzez-btn houzez-btn-primary">
									<i class="dashicons dashicons-plus"></i>
									<?php esc_html_e('Add New Layout', 'houzez'); ?>
								</a>
							</div>
						</div>
					</div>
				`;
				
				// Insert the header at the beginning of the wrap
				$wrap.prepend(headerHtml);
				
				
			}
        });
        </script>
        
        <style>
        .post-type-fts_builder .page-title-action, #screen-options-link-wrap {
            display: none !important;
        }
        </style>
        <?php
	}

}

return Houzez_Admin::instance();