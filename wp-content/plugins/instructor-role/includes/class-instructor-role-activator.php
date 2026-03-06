<?php
/**
 * Fired during plugin activation
 *
 * @link https://learndash.com
 * @since 1.0.0
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Includes;

defined( 'ABSPATH' ) || exit;

use InstructorRole\Modules\Classes\Instructor_Role_Dashboard_Block;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since 1.0.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */
class Instructor_Role_Activator {
	/**
	 * Activation Sequence
	 *
	 * Performs necessary actions such as adding instructor role and capabilities to admin.
	 *
	 * @since 3.5.0
	 *
	 * @param bool $network_wide    Whether to enable the plugin for all sites in the network or just the current site.
	 *                              Multisite only. Default false.
	 */
	public function activate( $network_wide ) {
		require_once INSTRUCTOR_ROLE_ABSPATH . 'includes/instructor-role-functions.php';

		$this->identify_customer_type();
		$this->add_instructor_role();
		if ( is_multisite() && $network_wide ) {
			global $wpdb;
			foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
				switch_to_blog( $blog_id );
				$admin_role = get_role( 'administrator' );
				if ( null !== $admin_role ) {
					$admin_role->add_cap( 'instructor_reports' );
					$admin_role->add_cap( 'instructor_page' );
				}
				restore_current_blog();
			}
		} else {
			$admin_role = get_role( 'administrator' );
			if ( null !== $admin_role ) {
				$admin_role->add_cap( 'instructor_reports' );
				$admin_role->add_cap( 'instructor_page' );
			}
		}

		/**
		 * Fires once after the instructor role plugin is activated
		 *
		 * @since 3.6.1
		 *
		 * @param bool $network_wide    Whether to enable the plugin for all sites in the network or just the current site.
		 *                              Multisite only. Default false.
		 */
		do_action( 'ir_action_plugin_activated', $network_wide );
	}

	/**
	 * Admin Activation Sequence.
	 * Check for plugin dependencies on plugin activation.
	 *
	 * @since 3.5.0
	 * @deprecated 5.9.2 This is handled via the Dependency_Checker class now.
	 *
	 * @return void
	 */
	public function admin_activate() {
		_deprecated_function( __METHOD__, '5.9.2' );
	}

	/**
	 * Handle admin notices.
	 *
	 * @since 3.6.2
	 * @deprecated 5.9.2 This is handled via the Dependency_Checker class now.
	 *
	 * @return void
	 */
	public function handle_admin_notices() {
		_deprecated_function( __METHOD__, '5.9.2' );
	}

	/**
	 * Add the instructor role
	 *
	 * @since 1.0
	 */
	public function add_instructor_role() {
		$instructor_caps = [
			'wpProQuiz_show'               => true, // true allows this capability.
			'wpProQuiz_add_quiz'           => true,
			'wpProQuiz_edit_quiz'          => true, // Use false to explicitly deny.
			'wpProQuiz_delete_quiz'        => true,
			'wpProQuiz_show_statistics'    => true,
			'wpProQuiz_import'             => true,
			'wpProQuiz_export'             => true,
			'read_course'                  => true,
			'publish_courses'              => true,
			'edit_courses'                 => true,
			'delete_courses'               => true,
			'edit_course'                  => true,
			'delete_course'                => true,
			'edit_published_courses'       => true,
			'delete_published_courses'     => true,
			'edit_assignment'              => true,
			'edit_assignments'             => true,
			'publish_assignments'          => true,
			'read_assignment'              => true,
			'delete_assignment'            => true,
			'edit_published_assignments'   => true,
			'delete_published_assignments' => true,
			'read'                         => true,
			'edit_others_assignments'      => true,
			'instructor_reports'           => true, // very important, custom for course report submenu page.
			'instructor_page'              => true, // very important, for showing instructor submenu page. added in 2.4.0.
			'manage_categories'            => true,
			'wpProQuiz_toplist_edit'       => true, // to show leaderboard of quiz.
			'upload_files'                 => true, // to upload files.
			'delete_essays'                => true,  // added v 2.4.0 for essay
			'delete_others_essays'         => true,
			'delete_private_essays'        => true,
			'delete_published_essays'      => true,
			'edit_essays'                  => true,
			'edit_others_essays'           => true,
			'edit_private_essays'          => true,
			'edit_published_essays'        => true,
			'publish_essays'               => true,
			'read_essays'                  => true,
			'read_private_essays'          => true,
			'edit_posts'                   => true,
			'publish_posts'                => true,
			'edit_published_posts'         => true,
			'delete_posts'                 => true,
			'delete_published_posts'       => true,
			'view_h5p_contents'            => true,
			'edit_h5p_contents'            => true,
			'unfiltered_html'              => true,
			'delete_product'               => true,
			'delete_products'              => true,
			'delete_published_products'    => true,
			'edit_product'                 => true,
			'edit_products'                => true,
			'edit_published_products'      => true,
			'publish_products'             => true,
			'read_product'                 => true,
			'assign_product_terms'         => true,
		];

		// Add instructor caps in options.
		update_option( 'ir_instructor_caps', $instructor_caps );

		add_role(
			'wdm_instructor',
			__( 'Instructor', 'wdm_instructor_role' ),
			$instructor_caps
		);
	}


	/**
	 * Handle upgrade notices if any.
	 *
	 * @since 3.6.2
	 * @deprecated 5.9.1 This is no longer used.
	 *
	 * @param array  $data     Data.
	 * @param object $response Response.
	 *
	 * @return void
	 */
	public function handle_update_notices( $data, $response ) {
		_deprecated_function( __METHOD__, '5.9.1' );

		/**
		 * Plugin update message.
		 *
		 * @since 3.6.2
		 *
		 * @deprecated 5.9.1
		 *
		 * @param string $upgrade_notice Upgrade notice.
		 */
		echo wp_kses_post( _deprecated_hook( 'ir_in_plugin_update_message', '' ) );
	}

	/**
	 * Get the upgrade notice.
	 *
	 * @since 3.6.2
	 *
	 * @deprecated 5.9.1 This is no longer used.
	 *
	 * @param string $version Plugin version.
	 *
	 * @return string Upgrade notice section.
	 */
	protected function get_upgrade_notice( $version ) {
		_deprecated_function( __METHOD__, '5.9.1' );

		return '';
	}

	/**
	 * Parse update notice from readme file. Code Adopted from WooCommerce.
	 *
	 * @deprecated 5.9.1 This is no longer used.
	 *
	 * @since 3.6.2
	 *
	 * @param string $content Instructor Role readme file content.
	 * @param string $new_version Plugin new version.
	 *
	 * @return string
	 */
	private function parse_update_notice( $content, $new_version ) {
		_deprecated_function( __METHOD__, '5.9.1' );

		return '';
	}

	/**
	 * Enqueues styles needed to display plugin update section.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function enqueue_plugin_update_css() {
		$current_screen = get_current_screen();

		if ( 'plugins' === $current_screen->id ) {
			wp_enqueue_style(
				'ir-upgrade-styles',
				plugins_url( 'modules/css/ir-upgrade-notice.css', __DIR__ ),
				[],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/ir-upgrade-notice.css' )
			);
		}
	}

	/**
	 * Identify if the customer is an existing or new customer.
	 *
	 * @since 4.3.0
	 */
	public function identify_customer_type() {
		// Check if admin.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check if existing customer.
		$new_customer_flag      = ir_get_settings( 'ir_new_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );
		$existing_customer_flag = ir_get_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );

		// Checks if identification process already completed.
		if ( $new_customer_flag || $existing_customer_flag ) {
			return;
		}

		// * Check if wdm_instructor_role user role already exists.
		if ( function_exists( 'wp_roles' ) ) {
			$all_roles = wp_roles()->roles;
			if ( in_array( 'wdm_instructor', $all_roles ) ) {
				ir_set_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );
				return;
			}
		}

		// Check if administrator has any instructor capabilities.
		if ( current_user_can( 'instructor_reports' ) || current_user_can( 'instructor_page' ) ) {
			ir_set_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );
			return;
		}

		// Check if any instructor table already exists.
		if ( $this->plugin_tables_exist() ) {
			ir_set_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );
			return;
		}

		// Check if any of the plugin option already exists.
		if ( $this->plugin_options_exist() ) {
			ir_set_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );
			return;
		}

		// Set new default appearance layouts.
		ir_set_settings( 'ir_new_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );
		ir_set_settings( 'ir_color_preset_2', 'default' );

		/**
		 * Action to perform actions only for new customers for current update.
		 *
		 * @since 5.0.0
		 */
		do_action( 'ir_action_new_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION );

		/**
		 * Action to perform actions only for new customers.
		 *
		 * @since 5.0.0
		 */
		do_action( 'ir_action_new_customer' );
	}

	/**
	 * Display modal popup for new features.
	 *
	 * @since 4.3.0
	 */
	public function display_new_features_pop_up() {
		// Check if admin.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check if existing customer.
		$existing_customer = intval( ir_get_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION ) );

		// Check if popup is dismissed or not.
		$is_popup_update_dismissed = ir_get_settings( 'is_popup_update_dismissed_' . INSTRUCTOR_ROLE_PLUGIN_VERSION );

		// Check if the new frontend dashboard introduced.
		$is_fdb_introduced = intval( ir_get_settings( 'fdb_introduced' ) );

		// Show modal to admins if not already introduced.
		if ( ! $is_fdb_introduced && ! $is_popup_update_dismissed ) {
			wp_enqueue_style(
				'ir_new_features_popup_styles',
				plugins_url( 'modules/css/ir-new-features-popup.css', __DIR__ ),
				[],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/css/ir-new-features-popup.css' )
			);
			wp_enqueue_script(
				'ir_new_features_popup_script',
				plugins_url( 'modules/js/ir-new-features-popup.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/js/ir-new-features-popup.js' ),
				1
			);
			wp_localize_script(
				'ir_new_features_popup_script',
				'ir_new_loc',
				[
					'ajax_url'             => admin_url( 'admin-ajax.php' ),
					'nonce'                => wp_create_nonce( 'ir_popup_actions' ),
					'is_existing_customer' => ir_get_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION ),
					'view_settings_link'   => add_query_arg(
						[
							'page'          => 'instuctor',
							'tab'           => 'ir-frontend-dashboard',
							'fdb_suggested' => 1,
						],
						admin_url( 'admin.php' )
					),
				]
			);
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/ir-new-features-popup.template.php',
				[
					'image_url'            => plugins_url( 'modules/media/new-dashboard-overview.png', __DIR__ ),
					'instructor_popup'     => plugins_url( 'modules/css/images/instructor_modal.png', __DIR__ ),
					'is_existing_customer' => ir_get_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION ),
				]
			);
		}

		// v5.8.0 update modals.
		// @phpstan-ignore-next-line It won't be shown anyway, but we don't want to delete it for now.
		if ( '5.8.0' === INSTRUCTOR_ROLE_PLUGIN_VERSION ) {
			// Check if frontend dashboard pattern already updated with commissions and products.
			$is_pattern_updated  = intval( ir_get_settings( 'is_pattern_updated_' . INSTRUCTOR_ROLE_PLUGIN_VERSION ) );
			$is_notice_dismissed = intval( ir_get_settings( 'is_pattern_update_dismissed_' . INSTRUCTOR_ROLE_PLUGIN_VERSION ) );
			$fd_page_id          = get_option( 'ir_frontend_dashboard_page', false );

			// No need to show popup if already dismissed.
			if ( $is_notice_dismissed ) {
				return;
			}

			// Show modal to admins if pattern not updated and frontend dashboard is already introduced.
			if ( ! $is_pattern_updated && $is_fdb_introduced && ! empty( $fd_page_id ) ) {
				wp_enqueue_style(
					'ir_pattern_update_popup_styles',
					plugins_url( 'modules/css/ir-pattern-update-popup.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/css/ir-pattern-update-popup.css' )
				);
				wp_enqueue_script(
					'ir_new_features_popup_script',
					plugins_url( 'modules/js/ir-new-features-popup.js', __DIR__ ),
					[ 'jquery' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/js/ir-new-features-popup.js' ),
					1
				);
				wp_localize_script(
					'ir_new_features_popup_script',
					'ir_new_loc',
					[
						'ajax_url'             => admin_url( 'admin-ajax.php' ),
						'nonce'                => wp_create_nonce( 'ir_popup_actions' ),
						'is_existing_customer' => ir_get_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION ),
						'fd_page_link'         => get_edit_post_link( $fd_page_id ),
					]
				);
				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/ir-pattern-update-popup.template.php',
					[
						'image_url'            => plugins_url( 'modules/media/new-dashboard-overview.png', __DIR__ ),
						'is_existing_customer' => ir_get_settings( 'ir_existing_customer_' . INSTRUCTOR_ROLE_PLUGIN_VERSION ),
						'manual_edit_link'     => add_query_arg(
							[
								'action' => 'fdb_update_pattern',
								'mode'   => 'manual',
								'nonce'  => wp_create_nonce( 'ir_popup_actions' ),
							],
							admin_url( 'admin-ajax.php' )
						),
						'auto_edit_link'       => add_query_arg(
							[
								'action' => 'fdb_update_pattern',
								'mode'   => 'auto',
								'nonce'  => wp_create_nonce( 'ir_popup_actions' ),
							],
							admin_url( 'admin-ajax.php' )
						),
					]
				);
			}
		}
	}

	/**
	 * Display admin notice for the newly added layouts.
	 *
	 * @since 4.3.0
	 */
	public function display_new_features_notice() {
		// Check if admin.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// v5.8.0 update notices.
		// @phpstan-ignore-next-line It won't be shown anyway, but we don't want to delete it for now.
		if ( '5.8.0' === INSTRUCTOR_ROLE_PLUGIN_VERSION ) {
			// Check if pattern update notice dismissed.
			$is_pattern_updated  = intval( ir_get_settings( 'is_pattern_updated_' . INSTRUCTOR_ROLE_PLUGIN_VERSION ) );
			$is_notice_dismissed = intval( ir_get_settings( 'is_pattern_update_dismissed_' . INSTRUCTOR_ROLE_PLUGIN_VERSION ) );

			if ( ! $is_pattern_updated && $is_notice_dismissed ) {
				wp_enqueue_style(
					'ir_new_features_notices_styles',
					plugins_url( 'modules/css/ir-new-features-notices.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/css/ir-new-features-notices.css' )
				);
				wp_enqueue_script(
					'ir_new_features_notices_scripts',
					plugins_url( 'modules/js/ir-new-features-notices.js', __DIR__ ),
					[ 'jquery' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/ir-new-features-notices.js' ),
					1
				);
				wp_localize_script(
					'ir_new_features_notices_scripts',
					'ir_new_loc',
					[
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'ir_popup_actions' ),
					]
				);
				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/ir-new-features-notices.template.php',
					[
						'image_url'        => plugins_url( 'modules/media/learndash_logo.png', __DIR__ ),
						'manual_edit_link' => add_query_arg(
							[
								'action' => 'fdb_update_pattern',
								'mode'   => 'manual',
								'nonce'  => wp_create_nonce( 'ir_popup_actions' ),
							],
							admin_url( 'admin-ajax.php' )
						),
						'auto_edit_link'   => add_query_arg(
							[
								'action' => 'fdb_update_pattern',
								'mode'   => 'auto',
								'nonce'  => wp_create_nonce( 'ir_popup_actions' ),
							],
							admin_url( 'admin-ajax.php' )
						),
					]
				);
			}
		}   }

	/**
	 * Check if any existing instructor role plugin table exists in the database.
	 *
	 * @since 4.3.0
	 */
	public function plugin_tables_exist() {
		global $wpdb;

		$table_exists = false;

		$ir_tables = [
			$wpdb->prefix . 'wdm_instructor_commission',
			$wpdb->prefix . 'ir_paypal_payouts_transactions',
			$wpdb->prefix . 'ir_commission_logs',
		];

		foreach ( $ir_tables as $table_name ) {
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$table_exists = true;
				break;
			}
		}
		return $table_exists;
	}

	/**
	 * Check if any Instructor Role options already exist in the database.
	 *
	 * @since 4.3.0
	 */
	public function plugin_options_exist() {
		$option_exists       = false;
		$instructor_caps     = get_option( 'ir_instructor_caps' );
		$plugin_options_list = [
			'ir_instructor_caps',
			'_wdmir_admin_settings',
			'ir_active_modules',
		];

		foreach ( $plugin_options_list as $plugin_option ) {
			if ( false !== $instructor_caps ) {
				$option_exists = true;
				break;
			}
		}

		return $option_exists;
	}

	/**
	 * Ajax for Frontend Dashboard Introduced
	 *
	 * @since 4.4.0
	 */
	public function ajax_fdb_introduced() {
		$response = [
			'status'  => 'error',
			'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
		];

		// Verify nonce.
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'ir_popup_actions' ) ) {
			echo wp_json_encode( $response );
			wp_die();
		}

		// Set meta for new layouts suggested and no further reminders needed.
		ir_set_settings( 'fdb_introduced', 1 );

		// Also set meta for pattern updated for release 2 for new installs.
		ir_set_settings( 'is_pattern_updated_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );

		// Set default dashboard global settings.
		Instructor_Role_Dashboard_Block::configure_vanilla_frontend_dashboard_settings();

		echo wp_json_encode(
			[
				'status'   => 'success',
				'redirect' => add_query_arg(
					[
						'page'          => 'instuctor',
						'tab'           => 'setup',
						'fdb_suggested' => 1,
					],
					admin_url( 'admin.php' )
				),
			]
		);
		wp_die();
	}

	/**
	 * Ajax for Frontend Dashboard Pattern Update
	 *
	 * @since 5.1.0
	 */
	public function ajax_fdb_update_pattern() {
		$response = [
			'status'  => 'error',
			'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
		];

		// Verify nonce.
		if ( ! wp_verify_nonce( ir_filter_input( 'nonce', INPUT_GET ), 'ir_popup_actions' ) ) {
			echo wp_json_encode( $response );
			wp_die();
			exit();
		}

		// Set meta for pattern updated for release 2 and no further reminders needed.
		ir_set_settings( 'is_pattern_updated_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );

		$mode = ir_filter_input( 'mode', INPUT_GET );

		// Whether to automatically update pattern.
		$fd_page_id = get_option( 'ir_frontend_dashboard_page', false );

		if ( 'auto' === $mode && ! empty( $fd_page_id ) ) {
			wp_update_post(
				[
					'ID'           => $fd_page_id,
					'post_content' => ir_get_template(
						INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-static-dashboard-block.template.php',
						[],
						true
					),
				]
			);
		}

		$page_url = add_query_arg(
			[
				'post'   => $fd_page_id,
				'action' => 'edit',
			],
			admin_url( 'post.php' )
		);

		// If gutenberg disabled, redirect to different location.
		if ( ! ir_is_gutenberg_enabled() ) {
			if ( 'manual' === $mode ) {
				$page_url = add_query_arg(
					[
						'page'            => 'instuctor',
						'tab'             => 'ir-dashboard-settings',
						'fdb_manual_edit' => 1,
					],
					admin_url( 'admin.php' )
				);
			} else {
				Instructor_Role_Dashboard_Block::configure_vanilla_frontend_dashboard_settings();
				$page_url = get_permalink( $fd_page_id );
			}
		}

		wp_safe_redirect( $page_url );
		exit();
	}

	/**
	 * Ajax for adding pattern update notice
	 *
	 * @since 5.1.0
	 */
	public function ajax_add_pattern_update_notice() {
		$response = [
			'status'  => 'error',
			'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
		];

		// Verify nonce.
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'ir_popup_actions' ) ) {
			echo wp_json_encode( $response );
			wp_die();
		}

		// Set meta for customer attempt to dismiss pattern update popup.
		ir_set_settings( 'is_pattern_update_dismissed_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );

		echo wp_json_encode(
			[
				'status' => 'success',
			]
		);
		wp_die();
	}

	/**
	 * Ajax for dismissing new features popup.
	 *
	 * @since 5.9.0
	 */
	public function ajax_new_features_popup_dismissed() {
		$response = [
			'status'  => 'error',
			'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
		];

		// Verify nonce.
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'ir_popup_actions' ) ) {
			echo wp_json_encode( $response );
			wp_die();
		}

		// Set meta for customer attempt to dismiss pattern update popup.
		ir_set_settings( 'is_popup_update_dismissed_' . INSTRUCTOR_ROLE_PLUGIN_VERSION, 1 );

		echo wp_json_encode(
			[
				'status' => 'success',
			]
		);
		wp_die();
	}
}
