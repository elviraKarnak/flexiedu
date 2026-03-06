<?php
/**
 * Group Registration Setup Wizard
 *
 * @since 4.2.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/wisdm-setup
 *
 * cspell:ignore rmvl
 */

namespace LdGroupRegistration\WisdmSetup;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ld_Group_Registration_Setup_Wizard' ) ) {
	class Ld_Group_Registration_Setup_Wizard {
		public function __construct() {
			add_filter( 'wisdm_setup_wizards', [ $this, 'ldgr_setup_wizard' ] );
			add_action( 'admin_notices', [ $this, 'ldgr_add_setup_wizard_link' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'ldgr_setup_wizard_scripts' ] );
			add_action( 'ld-group-registration_setup_wizard_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
			add_action( 'wp_ajax_ldgr-setup-wizard-dismiss', [ $this, 'ajax_ldgr_setup_wizard_dismiss' ] );
		}

		/**
		 * Enqueue necessary styles and scripts for the setup wizard.
		 *
		 * @since 4.2.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_style(
				'ldgr-setup-styles',
				plugin_dir_url( __FILE__ ) . 'assets/css/ldgr-setup-styles.css',
				[],
				filemtime( plugin_dir_path( __FILE__ ) . 'assets/css/ldgr-setup-styles.css' )
			);

			wp_enqueue_script(
				'ldgr-setup-scripts',
				plugin_dir_url( __FILE__ ) . 'assets/js/ldgr-setup-scripts.js',
				[],
				filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/ldgr-setup-scripts.js' ),
				false
			);
		}
		/**
		 * Injects the wizard, steps and other data to Wisdm setup wizard.
		 *
		 * @param array $wizards
		 * @return array
		 */
		public function ldgr_setup_wizard( $wizards ) {
			$ldgr_wizard = [
				'ld-group-registration' => [ // Unique wizard slug.
					'title'      => 'LearnDash LMS - Group Registration', // Product Name
					'capability' => 'manage_options', // The user must have this capability to load the wizard.
					'steps'      => [ // Sequential steps.
						'introduction'         => [ // step slug, every step slug must be unique.
							'step_title'    => 'Introduction', // This will display at the top as a step title.
							'view_callback' => [ $this, 'intro_view' ], // A callback function to display content of this step.
						],
						'general-settings'     => [
							'step_title'    => __( 'General Settings', 'wdm_ld_group' ),
							'view_callback' => [ $this, 'general_settings_view' ],
							'save_callback' => [ $this, 'general_settings_save' ], // A callback function to save the data of this step. Optional.
						],
						'email-configurations' => [
							'step_title'    => __( 'Email Settings', 'wdm_ld_group' ),
							'view_callback' => [ $this, 'email_configurations_view' ],
							'save_callback' => [ $this, 'email_configurations_save' ],
						],
						'group-code'           => [
							'step_title'    => __( 'Group Code Settings', 'wdm_ld_group' ),
							'view_callback' => [ $this, 'group_code_view' ],
							'save_callback' => [ $this, 'group_code_save' ],
						],
						'sample-product'       => [
							'step_title'    => __( 'Done', 'wdm_ld_group' ),
							'view_callback' => [ $this, 'sample_product_view' ],
						],
					],
				],
			];

			return array_merge( $wizards + $ldgr_wizard );
		}

		/**
		 * Intro view setup.
		 *
		 * @since 4.2.0
		 */
		public function intro_view() {
			$wizard_handler = \Wisdm_Wizard_Handler::get_instance();
			// Update to dismiss wizard notice.
			update_option( 'ldgr_setup_dismiss', 1 );

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/wisdm-setup/templates/intro.template.php',
				[
					'wizard_handler' => $wizard_handler,
				]
			);
		}

		/**
		 * Setup the view for configuring general group registration settings.
		 *
		 * @since 4.2.0
		 */
		public function general_settings_view() {
			$wizard_handler        = \Wisdm_Wizard_Handler::get_instance();
			$ldgr_admin_approval   = get_option( 'ldgr_admin_approval' );
			$ldgr_user_redirects   = get_option( 'ldgr_user_redirects' );
			$group_leader_redirect = get_option( 'ldgr_redirect_group_leader' );
			$group_user_redirect   = get_option( 'ldgr_redirect_group_user' );

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/wisdm-setup/templates/general-settings.template.php',
				[
					'wizard_handler'        => $wizard_handler,
					'ldgr_admin_approval'   => $ldgr_admin_approval,
					'ldgr_user_redirects'   => $ldgr_user_redirects,
					'group_leader_redirect' => $group_leader_redirect,
					'group_user_redirect'   => $group_user_redirect,
				]
			);
		}

		/**
		 * Setup the view for configuring group registration email settings.
		 *
		 * @since 4.2.0
		 */
		public function email_configurations_view() {
			$wizard_handler                       = \Wisdm_Wizard_Handler::get_instance();
			$ldgr_create_user_admin               = get_option( 'wdm_a_u_ac_crt_enable' );
			$ldgr_remove_user_admin               = get_option( 'wdm_a_rq_rmvl_enable' );
			$ldgr_remove_user_accept_group_leader = get_option( 'wdm_gr_gl_rmvl_enable' );
			$ldgr_remove_user_reject_group_leader = get_option( 'wdm_gr_gl_acpt_enable' );
			$ldgr_user_created_member             = get_option( 'wdm_u_ac_crt_enable' );
			$ldgr_user_added_member               = get_option( 'wdm_u_add_gr_enable' );
			$ldgr_reinvite_user_member            = get_option( 'wdm_gr_reinvite_enable' );

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/wisdm-setup/templates/email-configurations.template.php',
				[
					'wizard_handler'                       => $wizard_handler,
					'ldgr_create_user_admin'               => $ldgr_create_user_admin,
					'ldgr_remove_user_admin'               => $ldgr_remove_user_admin,
					'ldgr_remove_user_accept_group_leader' => $ldgr_remove_user_accept_group_leader,
					'ldgr_remove_user_reject_group_leader' => $ldgr_remove_user_reject_group_leader,
					'ldgr_user_created_member'             => $ldgr_user_created_member,
					'ldgr_user_added_member'               => $ldgr_user_added_member,
					'ldgr_reinvite_user_member'            => $ldgr_reinvite_user_member,
				]
			);
		}

		/**
		 * Setup the view for configuring group code settings.
		 *
		 * @since 4.2.0
		 */
		public function group_code_view() {
			$wizard_handler                  = \Wisdm_Wizard_Handler::get_instance();
			$ldgr_enable_group_code          = get_option( 'ldgr_enable_group_code' );
			$ldgr_group_code_enrollment_page = get_option( 'ldgr_group_code_enrollment_page' );
			$ldgr_enable_gdpr                = get_option( 'ldgr_enable_gdpr' );

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/wisdm-setup/templates/group-code.template.php',
				[
					'wizard_handler'                  => $wizard_handler,
					'ldgr_enable_group_code'          => $ldgr_enable_group_code,
					'ldgr_group_code_enrollment_page' => $ldgr_group_code_enrollment_page,
					'ldgr_enable_gdpr'                => $ldgr_enable_gdpr,
				]
			);
		}

		/**
		 * A final step to tell the user that all steps are completed. Now, you decide what to do.
		 */
		public function sample_product_view() {
			$setup_wizard        = \Wisdm_Setup_Wizard::get_instance();
			$group_product_link  = add_query_arg(
				[
					'post_type'    => 'product',
					'product_type' => 'group_product',
				],
				admin_url( 'post-new.php' )
			);
			$group_settings_link = add_query_arg( 'page', 'wdm-ld-gr-setting', admin_url( 'admin.php' ) );

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/wisdm-setup/templates/sample-product.template.php',
				[
					'setup_wizard'        => $setup_wizard,
					'group_product_link'  => $group_product_link,
					'group_settings_link' => $group_settings_link,
				]
			);
		}

		/**
		 * The callback function to save general settings step data.
		 *
		 * @since 4.2.0
		 */
		public function general_settings_save() {
			check_ajax_referer( 'setup_general_settings', 'wisdm_setup_nonce' );

			$ldgr_admin_approval = '';
			if ( array_key_exists( 'ldgr_admin_approval', $_POST ) ) {
				$ldgr_admin_approval = sanitize_text_field( wp_unslash( $_POST['ldgr_admin_approval'] ) );
			}
			update_option( 'ldgr_admin_approval', $ldgr_admin_approval );

			$ldgr_user_redirects = '';
			if ( isset( $_POST['ldgr_user_redirects'] ) ) {
				$ldgr_user_redirects = sanitize_text_field( wp_unslash( $_POST['ldgr_user_redirects'] ) );
			}
			update_option( 'ldgr_user_redirects', $ldgr_user_redirects );

			if ( 'on' === $ldgr_user_redirects ) {
				// Save redirect settings.
				$group_leader_redirect = intval( filter_input( INPUT_POST, 'ldgr_redirect_group_leader', FILTER_SANITIZE_NUMBER_INT ) );
				$group_user_redirect   = intval( filter_input( INPUT_POST, 'ldgr_redirect_group_user', FILTER_SANITIZE_NUMBER_INT ) );
				if ( ! empty( $group_leader_redirect ) ) {
					update_option( 'ldgr_redirect_group_leader', $group_leader_redirect );
				}

				if ( ! empty( $group_user_redirect ) ) {
					update_option( 'ldgr_redirect_group_user', $group_user_redirect );
				}
			}
		}

		/**
		 * The callback function to save email configurations step data.
		 *
		 * @since 4.2.0
		 */
		public function email_configurations_save() {
			check_ajax_referer( 'setup_email_configurations', 'wisdm_setup_nonce' );

			$ldgr_create_user_admin = 'on';
			if ( ! array_key_exists( 'ldgr_create_user_admin', $_POST ) ) {
				$ldgr_create_user_admin = 'off';
			}
			update_option( 'wdm_a_u_ac_crt_enable', $ldgr_create_user_admin );

			$ldgr_remove_user_admin = 'on';
			if ( ! array_key_exists( 'ldgr_remove_user_admin', $_POST ) ) {
				$ldgr_remove_user_admin = 'off';
			}
			update_option( 'wdm_a_rq_rmvl_enable', $ldgr_remove_user_admin );

			$ldgr_remove_user_accept_group_leader = 'on';
			if ( ! array_key_exists( 'ldgr_remove_user_accept_group_leader', $_POST ) ) {
				$ldgr_remove_user_accept_group_leader = 'off';
			}
			update_option( 'wdm_gr_gl_rmvl_enable', $ldgr_remove_user_accept_group_leader );

			$ldgr_remove_user_reject_group_leader = 'on';
			if ( ! array_key_exists( 'ldgr_remove_user_reject_group_leader', $_POST ) ) {
				$ldgr_remove_user_reject_group_leader = 'off';
			}
			update_option( 'wdm_gr_gl_acpt_enable', $ldgr_remove_user_reject_group_leader );

			$ldgr_user_created_member = 'on';
			if ( ! array_key_exists( 'ldgr_user_created_member', $_POST ) ) {
				$ldgr_user_created_member = 'off';
			}
			update_option( 'wdm_u_ac_crt_enable', $ldgr_user_created_member );

			$ldgr_user_added_member = 'on';
			if ( ! array_key_exists( 'ldgr_user_added_member', $_POST ) ) {
				$ldgr_user_added_member = 'off';
			}
			update_option( 'wdm_u_add_gr_enable', $ldgr_user_added_member );

			$ldgr_reinvite_user_member = 'on';
			if ( ! array_key_exists( 'ldgr_reinvite_user_member', $_POST ) ) {
				$ldgr_reinvite_user_member = 'off';
			}
			update_option( 'wdm_gr_reinvite_enable', $ldgr_reinvite_user_member );
		}

		/**
		 * The callback function to save group code settings step data.
		 *
		 * @since 4.2.0
		 */
		public function group_code_save() {
			check_ajax_referer( 'setup_group_code_settings', 'wisdm_setup_nonce' );

			$ldgr_enable_group_code = '';
			if ( array_key_exists( 'ldgr_enable_group_code', $_POST ) ) {
				$ldgr_enable_group_code = sanitize_text_field( wp_unslash( $_POST['ldgr_enable_group_code'] ) );
			}
			update_option( 'ldgr_enable_group_code', $ldgr_enable_group_code );

			$ldgr_group_code_enrollment_page = intval( filter_input( INPUT_POST, 'ldgr_group_code_enrollment_page', FILTER_SANITIZE_NUMBER_INT ) );
			if ( ! empty( $ldgr_group_code_enrollment_page ) ) {
				update_option( 'ldgr_group_code_enrollment_page', $ldgr_group_code_enrollment_page );
			}

			$ldgr_enable_gdpr = '';
			if ( array_key_exists( 'ldgr_enable_gdpr', $_POST ) ) {
				$ldgr_enable_gdpr = sanitize_text_field( wp_unslash( $_POST['ldgr_enable_gdpr'] ) );
			}
			update_option( 'ldgr_enable_gdpr', $ldgr_enable_gdpr );
		}

		/**
		 * Add setup wizard link on admin dashboard.
		 *
		 * @since 4.2.0
		 */
		public function ldgr_add_setup_wizard_link() {
			$wizard_handler     = \Wisdm_Wizard_Handler::get_instance();
			$ldgr_setup_dismiss = get_option( 'ldgr_setup_dismiss' );

			// If setup dismissed, then do not show link.
			if ( ! empty( $ldgr_setup_dismiss ) ) {
				return;
			}

			$link = $wizard_handler->get_wizard_first_step_link( 'ld-group-registration' );

			?>
			<div class="notice ldgr-setup-wizard-notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						// translators: anchor tag with the text "here".
						esc_html__( 'Configure all the Settings for the LearnDash LMS - Group Registration from %s and start selling Groups NOW!', 'wdm_ld_group' ),
						"<a href='" . esc_url( $link ) . "'>" . 'here</a>'
					);
					?>
					<?php wp_nonce_field( 'ldgr-setup-wizard-dismiss', 'ldgr_wizard_nonce' ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Enqueue setup wizard scripts to dismiss setup notices.
		 *
		 * @since 4.2.0
		 */
		public function ldgr_setup_wizard_scripts() {
			$ldgr_setup_dismiss = get_option( 'ldgr_setup_dismiss' );

			// If setup dismissed, then do not enqueue any scripts.
			if ( true === $ldgr_setup_dismiss ) {
				return;
			}

			wp_enqueue_script(
				'ldgr_setup_dismiss_script',
				plugin_dir_url( __FILE__ ) . 'assets/js/ldgr-setup-dismiss-script.js',
				[],
				filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/ldgr-setup-dismiss-script.js' ),
				false
			);

			wp_localize_script(
				'ldgr_setup_dismiss_script',
				'ldgr_setup_loc',
				[
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				]
			);
		}

		/**
		 * Dismiss setup wizard link via ajax.
		 *
		 * @since 4.2.0
		 */
		public function ajax_ldgr_setup_wizard_dismiss() {
			check_ajax_referer( 'ldgr-setup-wizard-dismiss', 'nonce' );
			echo wp_json_encode( [ 'wizard_dismiss' => $this->dismiss_setup_wizard_link() ] );
			die();
		}

		/**
		 * Dismiss setup wizard
		 *
		 * @since 4.2.0
		 */
		public function dismiss_setup_wizard_link() {
			return update_option( 'ldgr_setup_dismiss', 1 );
		}
	}
}
