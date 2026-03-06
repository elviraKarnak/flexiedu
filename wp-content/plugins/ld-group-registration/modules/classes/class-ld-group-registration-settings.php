<?php
/**
 * Settings Module
 *
 * @since 4.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/modules/classes
 *
 * cspell:ignore rmvl sbmt apperance
 */

namespace LdGroupRegistration\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ld_Group_Registration_Settings' ) ) {
	/**
	 * LD Group Registration Settings
	 */
	class Ld_Group_Registration_Settings {
		/**
		 * Class Instance
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 4.3.3
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Construct.
		 */
		public function __construct() {
			$this->set_notified( 'ldgr_whats_new_notified', 'whats_new' );
		}

		/**
		 * Add settings menu
		 */
		public function add_settings_menu() {
			add_action( 'admin_enqueue_scripts', [ $this, 'ldgr_load_admin_styles_to_display_dot' ] );
			// cspell:disable-next-line .
			$option_name = $this->can_show_notification( 'ldgr_whats_new_notified' ) ? 'ldgr_whats_new_notified_alredy' : 'ldgr_whats_new_notified';
			add_submenu_page(
				'learndash-lms',
				__( 'Group Registration Settings', 'wdm_ld_group' ),
				__( 'Group Registration Settings', 'wdm_ld_group' ) . $this->get_notification_html( $option_name ),
				'manage_options',
				'wdm-ld-gr-setting',
				[ $this, 'display_settings_menu' ]
			);
		}


		/**
		 * Enqueue the required styles for admin side of LDGR to display dot notification.
		 */
		public function ldgr_load_admin_styles_to_display_dot() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_style(
				'ldgr-notification-dot-styles',
				plugins_url(
					'css/ldgr-notification-dot' . $suffix . '.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);
		}

		/**
		 * Function to set  notified option.
		 *
		 * @since 4.1.0
		 */
		public function set_notified( $option_name, $tab ) {
			if ( $this->can_show_notification( $option_name ) && isset( $_GET['page'] ) && 'wdm-ld-gr-setting' == $_GET['page'] && $this->get_current_tab() == $tab ) {
				update_option( $option_name, LD_GROUP_REGISTRATION_VERSION );
			}
		}


		/**
		 * Returns the current tab.
		 *
		 * @return string $current_tab current tab.
		 */
		public function get_current_tab() {
			global $pagenow;
			static $current_tab = null;

			if ( null !== $current_tab ) {
				return $current_tab;
			}

			if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'wdm-ld-gr-setting' == $_GET['page'] ) {
				if ( isset( $_GET['tab'] ) ) {
					$current_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
					return $current_tab;
				}

				$current_tab = 'general';
				return $current_tab;
			}

			$current_tab = false;
			return $current_tab;
		}

		/**
		 * Display settings menu for group registration
		 */
		public function display_settings_menu() {
			add_action( 'admin_init', [ 'LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Settings', 'add_beacon_helpscout_script' ] );
			wp_enqueue_script(
				'wdm-ldgr-setting-js',
				plugins_url(
					'js/wdm-setting.js',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);

			wp_localize_script(
				'wdm-ldgr-setting-js',
				'ldgr_loc',
				[
					'invalid_discount_setting_msg' => __( 'Please enter valid min quantity and percentage values for bulk discounts', 'wdm_ld_group' ),
				]
			);

			$current_tab = 'general';
			if ( isset( $_GET['tab'] ) ) {
				$current_tab = $_GET['tab'];
			}

			$tab_headers = [
				'general'    => __( 'General Settings', 'wdm_ld_group' ),
				'email'      => __( 'Email Settings', 'wdm_ld_group' ),
				'appearance' => __( 'Appearance', 'wdm_ld_group' ),
			];

			$tab_headers = apply_filters( 'ldgr_setting_tab_headers', $tab_headers );
			?>
			<style>
			#beacon-container .BeaconContainer {
				z-index: 999999;
			}
			</style>
			<h1 class="nav-tab-wrapper">
				<?php foreach ( $tab_headers as $key => $value ) : ?>
					<a class="nav-tab <?php echo( ( $current_tab == $key ) ? 'nav-tab-active' : '' ); ?> " href="?page=wdm-ld-gr-setting&tab=<?php echo $key; ?>">
						<?php echo $value; ?>
					</a>
				<?php endforeach; ?>
			</h1>
			<?php

			switch ( $current_tab ) {
				case 'general':
					$this->display_global_settings();
					break;
				case 'email':
					$this->display_email_settings();
					break;
				case 'appearance':
					$this->display_appearance_settings();
					break;
			}

			do_action( 'ldgr_settings_tab_content_end', $current_tab );
		}

		/**
		 * Function to check if we can show notification of red dot.
		 *
		 * @since 4.2.0
		 */
		public function can_show_notification( $option_name ) {
			return version_compare( get_option( $option_name ), LD_GROUP_REGISTRATION_VERSION, '<' );
		}

		/**
		 * Function to get the notification html for whats new tab red dot.
		 *
		 * @since 4.2.0
		 */
		public function get_notification_html( $option_name ) {
			// cspell:disable-next-line .
			return $this->can_show_notification( $option_name ) ? '<span class="ldgr-update-notifcation"></span>' : '';
		}

		/**
		 * Display email settings
		 */
		public function display_email_settings() {
			wp_enqueue_style(
				'wdm-admin_css',
				plugins_url(
					'css/wdm-admin.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);
			if ( isset( $_POST['sbmt_wdm_gr_email_setting'] ) && wp_verify_nonce( $_POST['sbmt_wdm_gr_email_setting'], 'wdm_gr_email_setting' ) ) {
				$admin_email = $_POST['wdm-gr-admin-email'];
				update_option( 'wdm-gr-admin-email', trim( $admin_email ) );

				$gl_rmvl_sub = $_POST['wdm-gr-gl-rmvl-sub'];
				update_option( 'wdm-gr-gl-rmvl-sub', trim( $gl_rmvl_sub ) );
				$gl_rmvl_body = $_POST['wdm-gr-gl-rmvl-body'];
				update_option( 'wdm-gr-gl-rmvl-body', trim( $gl_rmvl_body ) );
				$gl_acpt_sub = $_POST['wdm-gr-gl-acpt-sub'];
				update_option( 'wdm-gr-gl-acpt-sub', trim( $gl_acpt_sub ) );
				$gl_acpt_body = $_POST['wdm-gr-gl-acpt-body'];
				update_option( 'wdm-gr-gl-acpt-body', trim( $gl_acpt_body ) );

				$a_rq_rmvl_sub = $_POST['wdm-a-rq-rmvl-sub'];
				update_option( 'wdm-a-rq-rmvl-sub', trim( $a_rq_rmvl_sub ) );
				$a_rq_rmvl_body = $_POST['wdm-a-rq-rmvl-body'];
				update_option( 'wdm-a-rq-rmvl-body', trim( $a_rq_rmvl_body ) );

				$u_add_gr_sub = $_POST['wdm-u-add-gr-sub'];
				update_option( 'wdm-u-add-gr-sub', trim( $u_add_gr_sub ) );
				$u_add_gr_body = $_POST['wdm-u-add-gr-body'];
				update_option( 'wdm-u-add-gr-body', trim( $u_add_gr_body ) );
				$u_ac_crt_sub = $_POST['wdm-u-ac-crt-sub'];
				update_option( 'wdm-u-ac-crt-sub', trim( $u_ac_crt_sub ) );
				$u_ac_crt_body = $_POST['wdm-u-ac-crt-body'];
				update_option( 'wdm-u-ac-crt-body', trim( $u_ac_crt_body ) );

				$a_u_ac_crt_sub = $_POST['wdm-a-u-ac-crt-sub'];
				update_option( 'wdm-a-u-ac-crt-sub', trim( $a_u_ac_crt_sub ) );
				$a_u_ac_crt_body = $_POST['wdm-a-u-ac-crt-body'];
				update_option( 'wdm-a-u-ac-crt-body', trim( $a_u_ac_crt_body ) );

				// Save ReInvite Email setting.
				$wdm_reinvite_sub  = stripslashes( $_POST['wdm-gr-reinvite-sub'] );
				$wdm_reinvite_body = $_POST['wdm-gr-reinvite-body'];

				update_option( 'wdm-reinvite-sub', trim( $wdm_reinvite_sub ) );
				update_option( 'wdm-reinvite-body', trim( $wdm_reinvite_body ) );

				// Save email enable/disable settings
				$wdm_gr_gl_rmvl_enable = 'on';
				if ( ! array_key_exists( 'wdm-gr-gl-rmvl-enable', $_POST ) ) {
					$wdm_gr_gl_rmvl_enable = 'off';
				}

				update_option( 'wdm_gr_gl_rmvl_enable', $wdm_gr_gl_rmvl_enable );

				$wdm_gr_gl_acpt_enable = 'on';
				if ( ! array_key_exists( 'wdm-gr-gl-acpt-enable', $_POST ) ) {
					$wdm_gr_gl_acpt_enable = 'off';
				}

				update_option( 'wdm_gr_gl_acpt_enable', $wdm_gr_gl_acpt_enable );

				$wdm_a_rq_rmvl_enable = 'on';
				if ( ! array_key_exists( 'wdm-a-rq-rmvl-enable', $_POST ) ) {
					$wdm_a_rq_rmvl_enable = 'off';
				}

				update_option( 'wdm_a_rq_rmvl_enable', $wdm_a_rq_rmvl_enable );

				$wdm_u_add_gr_enable = 'on';
				if ( ! array_key_exists( 'wdm-u-add-gr-enable', $_POST ) ) {
					$wdm_u_add_gr_enable = 'off';
				}

				update_option( 'wdm_u_add_gr_enable', $wdm_u_add_gr_enable );

				$wdm_u_ac_crt_enable = 'on';
				if ( ! array_key_exists( 'wdm-u-ac-crt-enable', $_POST ) ) {
					$wdm_u_ac_crt_enable = 'off';
				}

				update_option( 'wdm_u_ac_crt_enable', $wdm_u_ac_crt_enable );

				$wdm_a_u_ac_crt_enable = 'on';
				if ( ! array_key_exists( 'wdm-a-u-ac-crt-enable', $_POST ) ) {
					$wdm_a_u_ac_crt_enable = 'off';
				}

				update_option( 'wdm_a_u_ac_crt_enable', $wdm_a_u_ac_crt_enable );

				$wdm_gr_reinvite_enable = 'on';
				if ( ! array_key_exists( 'wdm-gr-reinvite-enable', $_POST ) ) {
					$wdm_gr_reinvite_enable = 'off';
				}

				update_option( 'wdm_gr_reinvite_enable', $wdm_gr_reinvite_enable );

				?>
				<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
					<p><strong><?php esc_html_e( 'Settings saved.', 'wdm_ld_group' ); ?></strong></p>
				</div>
				<div class="notice notice-info is-dismissible">
					<p>
						<strong>
							<?php esc_html_e( 'Note: Any empty fields will be replaced by defaults', 'wdm_ld_group' ); ?>
						</strong>
					</p>
				</div>
				<?php
			}

			$admin_email = get_option( 'wdm-gr-admin-email' );
			if ( empty( $admin_email ) ) {
				$admin_email = get_bloginfo( 'admin_email' );
				update_option( 'wdm-gr-admin-email', trim( $admin_email ) );
			}

			$gl_rmvl_sub  = get_option( 'wdm-gr-gl-rmvl-sub' );
			$gl_rmvl_sub  = empty( $gl_rmvl_sub ) ? WDM_GR_GL_RMVL_SUB : $gl_rmvl_sub;
			$gl_rmvl_body = get_option( 'wdm-gr-gl-rmvl-body' );
			$gl_rmvl_body = empty( $gl_rmvl_body ) ? WDM_GR_GL_RMVL_BODY : $gl_rmvl_body;

			$gl_acpt_sub  = get_option( 'wdm-gr-gl-acpt-sub' );
			$gl_acpt_sub  = empty( $gl_acpt_sub ) ? WDM_GR_GL_ACPT_SUB : $gl_acpt_sub;
			$gl_acpt_body = get_option( 'wdm-gr-gl-acpt-body' );
			$gl_acpt_body = empty( $gl_acpt_body ) ? WDM_GR_GL_ACPT_BODY : $gl_acpt_body;

			$a_rq_rmvl_sub  = get_option( 'wdm-a-rq-rmvl-sub' );
			$a_rq_rmvl_sub  = empty( $a_rq_rmvl_sub ) ? WDM_A_RQ_RMVL_SUB : $a_rq_rmvl_sub;
			$a_rq_rmvl_body = get_option( 'wdm-a-rq-rmvl-body' );
			$a_rq_rmvl_body = empty( $a_rq_rmvl_body ) ? WDM_A_RQ_RMVL_BODY : $a_rq_rmvl_body;

			$u_add_gr_sub  = get_option( 'wdm-u-add-gr-sub' );
			$u_add_gr_sub  = empty( $u_add_gr_sub ) ? WDM_U_ADD_GR_SUB : $u_add_gr_sub;
			$u_add_gr_body = get_option( 'wdm-u-add-gr-body' );
			$u_add_gr_body = empty( $u_add_gr_body ) ? WDM_U_ADD_GR_BODY : $u_add_gr_body;

			$u_ac_crt_sub  = get_option( 'wdm-u-ac-crt-sub' );
			$u_ac_crt_sub  = empty( $u_ac_crt_sub ) ? WDM_U_AC_CRT_SUB : $u_ac_crt_sub;
			$u_ac_crt_body = get_option( 'wdm-u-ac-crt-body' );
			$u_ac_crt_body = empty( $u_ac_crt_body ) ? WDM_U_AC_CRT_BODY : $u_ac_crt_body;

			$a_u_ac_crt_sub  = get_option( 'wdm-a-u-ac-crt-sub' );
			$a_u_ac_crt_sub  = empty( $a_u_ac_crt_sub ) ? WDM_A_U_AC_CRT_SUB : $a_u_ac_crt_sub;
			$a_u_ac_crt_body = get_option( 'wdm-a-u-ac-crt-body' );
			$a_u_ac_crt_body = empty( $a_u_ac_crt_body ) ? WDM_A_U_AC_CRT_BODY : $a_u_ac_crt_body;

			// ReInvite Email Setting.
			$wdm_reinvite_sub  = get_option( 'wdm-reinvite-sub' );
			$wdm_reinvite_sub  = empty( $wdm_reinvite_sub ) ? WDM_REINVITE_SUB : $wdm_reinvite_sub;
			$wdm_reinvite_body = get_option( 'wdm-reinvite-body' );
			$wdm_reinvite_body = empty( $wdm_reinvite_body ) ? WDM_REINVITE_BODY : $wdm_reinvite_body;

			// Fetch email enable/disable settings
			$wdm_gr_gl_rmvl_enable  = get_option( 'wdm_gr_gl_rmvl_enable' );
			$wdm_gr_gl_acpt_enable  = get_option( 'wdm_gr_gl_acpt_enable' );
			$wdm_a_rq_rmvl_enable   = get_option( 'wdm_a_rq_rmvl_enable' );
			$wdm_u_add_gr_enable    = get_option( 'wdm_u_add_gr_enable' );
			$wdm_u_ac_crt_enable    = get_option( 'wdm_u_ac_crt_enable' );
			$wdm_a_u_ac_crt_enable  = get_option( 'wdm_a_u_ac_crt_enable' );
			$wdm_gr_reinvite_enable = get_option( 'wdm_gr_reinvite_enable' );

			wp_enqueue_style(
				'wdm-email_css',
				plugins_url(
					'css/wdm-email.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_enqueue_script(
				'wdm-email_js',
				plugins_url(
					'js/wdm-email.js',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);

			include_once plugin_dir_path( __DIR__ ) . 'templates/wdm-email-setting.php';
		}

		/**
		 * Display promotions page
		 *
		 * @deprecated 4.3.14 This function is no longer in use.
		 */
		public function display_promotions_page() {
			_deprecated_function( __METHOD__, '4.3.14' );
		}

		/**
		 * Display global settings
		 */
		public function display_global_settings() {
			wp_enqueue_style(
				'wdm-admin_css',
				plugins_url(
					'css/wdm-admin.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);

			wp_enqueue_script(
				'wdm-ldgr-addel-js',
				plugins_url(
					'js/addel.jquery.min.js',
					__DIR__
				),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);

			wp_enqueue_script(
				'ldgr_group_settings',
				plugins_url(
					'js/ldgr-group-settings.js',
					__DIR__
				),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);

			$ldgr_admin_approval              = get_option( 'ldgr_admin_approval' );
			$ldgr_group_limit                 = get_option( 'ldgr_group_limit' );
			$ldgr_reinvite_user               = get_option( 'ldgr_reinvite_user' );
			$ldgr_group_courses               = get_option( 'ldgr_group_courses' );
			$ldgr_user_redirects              = get_option( 'ldgr_user_redirects' );
			$ldgr_redirect_group_leader       = get_option( 'ldgr_redirect_group_leader' );
			$ldgr_redirect_group_user         = get_option( 'ldgr_redirect_group_user' );
			$ldgr_unlimited_members_label     = get_option( 'ldgr_unlimited_members_label' );
			$ldgr_bulk_discounts              = get_option( 'ldgr_bulk_discounts' );
			$ldgr_bulk_discount_global_values = unserialize( get_option( 'ldgr_bulk_discount_global_values' ) );
			$ldgr_display_product_courses     = get_option( 'ldgr_display_product_courses' );
			$ldgr_display_product_courses     = empty( $ldgr_display_product_courses ) ? 'on' : $ldgr_display_product_courses;
			$ldgr_autofill_group_name         = get_option( 'ldgr_autofill_group_name' );
			$ldgr_autofill_group_name         = empty( $ldgr_autofill_group_name ) ? 'off' : $ldgr_autofill_group_name;
			$ldgr_display_product_footer      = get_option( 'ldgr_display_product_footer' );
			$ldgr_display_product_footer      = empty( $ldgr_display_product_footer ) ? 'on' : $ldgr_display_product_footer;
			// $ldgr_logo_enabled           = get_option( 'ldgr_logo_enabled' );
			// $ldgr_logo_url               = get_option( 'ldgr_logo_url' );
			// $ldgr_paid_course_for_leader = get_option("ldgr_global_gl_paid_course");

			if ( isset( $_POST['sbmt_ldgr_setting'] ) && wp_verify_nonce( $_POST['sbmt_ldgr_setting'], 'ldgr_setting' )
			) {
				$ldgr_admin_approval = '';
				if ( isset( $_POST['ldgr_admin_approval'] ) ) {
					$ldgr_admin_approval = $_POST['ldgr_admin_approval'];
				}

				update_option( 'ldgr_admin_approval', $ldgr_admin_approval );
				// $ldgr_admin_approval = $_POST['ldgr_admin_approval'];

				$ldgr_group_limit = '';
				if ( isset( $_POST['ldgr_group_limit'] ) ) {
					$ldgr_group_limit = $_POST['ldgr_group_limit'];
				}

				update_option( 'ldgr_group_limit', $ldgr_group_limit );
				// $ldgr_group_limit = $_POST['ldgr_group_limit'];

				$ldgr_reinvite_user = '';
				if ( isset( $_POST['ldgr_reinvite_user'] ) ) {
					$ldgr_reinvite_user = $_POST['ldgr_reinvite_user'];
				}

				update_option( 'ldgr_reinvite_user', $ldgr_reinvite_user );

				$ldgr_group_courses = '';
				if ( isset( $_POST['ldgr_group_courses'] ) ) {
					$ldgr_group_courses = $_POST['ldgr_group_courses'];
				}

				update_option( 'ldgr_group_courses', $ldgr_group_courses );

				// Check if redirects enabled
				$ldgr_user_redirects = '';
				if ( isset( $_POST['ldgr_user_redirects'] ) ) {
					$ldgr_user_redirects = $_POST['ldgr_user_redirects'];
				}
				update_option( 'ldgr_user_redirects', $ldgr_user_redirects );

				if ( 'on' == $ldgr_user_redirects ) {
					// Save redirect settings.
					$group_leader_redirect = intval( $_POST['ldgr_redirect_group_leader'] );
					$group_user_redirect   = intval( $_POST['ldgr_redirect_group_user'] );

					if ( ! empty( $group_leader_redirect ) ) {
						update_option( 'ldgr_redirect_group_leader', $group_leader_redirect );
					}

					if ( ! empty( $group_user_redirect ) ) {
						update_option( 'ldgr_redirect_group_user', $group_user_redirect );
					}
				}

				// Check if group logo enabled
				// $ldgr_logo_enabled = '';
				// if ( isset( $_POST['ldgr_logo_enabled'] ) ) {
				// $ldgr_logo_enabled = $_POST['ldgr_logo_enabled'];
				// }
				// update_option( 'ldgr_logo_enabled', $ldgr_logo_enabled );

				// if ( 'on' == $ldgr_logo_enabled ) {
				// $ldgr_logo_url = '';
				// if ( isset( $_POST['ldgr_logo_url'] ) ) {
				// $ldgr_logo_url = $_POST['ldgr_logo_url'];
				// }
				// update_option( 'ldgr_logo_url', $ldgr_logo_url );
				// }

				// Check if any label set for unlimited member options.
				$ldgr_unlimited_members_label = '';
				if ( isset( $_POST['ldgr_unlimited_members_label'] ) ) {
					$ldgr_unlimited_members_label = $_POST['ldgr_unlimited_members_label'];
				}
				update_option( 'ldgr_unlimited_members_label', $ldgr_unlimited_members_label );

				$ldgr_bulk_discounts = '';
				if ( isset( $_POST['ldgr_bulk_discounts'] ) ) {
					$ldgr_bulk_discounts = $_POST['ldgr_bulk_discounts'];
				}
				update_option( 'ldgr_bulk_discounts', $ldgr_bulk_discounts );

				$ldgr_bulk_discount_global_values = null;
				if ( isset( $_POST['ldgr_bulk_discount_global_values'] ) ) {
					$temp_check_values = $_POST['ldgr_bulk_discount_global_values'];
					if ( 1 === count( $temp_check_values['min_quantity'] ) && '' === $temp_check_values['min_quantity'][0] ) {
						$ldgr_bulk_discount_global_values = null;
						delete_option( 'ldgr_bulk_discount_global_values' );
					} else {
						$ldgr_bulk_discount_global_values = $_POST['ldgr_bulk_discount_global_values'];
						$all_min_quantity                 = array_filter( $ldgr_bulk_discount_global_values['min_quantity'] );
						$all_values                       = array_filter( $ldgr_bulk_discount_global_values['discount_value'] );
						$final_array                      = [];
						$quantity_check_array             = [];
						foreach ( $all_min_quantity as $index => $quantity ) {
							if ( ! in_array( $quantity, $quantity_check_array ) && ! empty( $quantity ) && ! empty( $all_values[ $index ] ) ) {
								array_push(
									$final_array,
									[
										'quantity' => $quantity,
										'type'     => 'Percentage',
										'value'    => $all_values[ $index ],
									]
								);
								array_push( $quantity_check_array, $quantity );
							}
						}
						$ldgr_bulk_discount_global_values = $final_array;
						update_option( 'ldgr_bulk_discount_global_values', serialize( $final_array ) );
					}
				}

				$ldgr_display_product_courses = 'on';
				if ( 'on' === ldgr_filter_input( 'ldgr_display_product_courses' ) ) {
					$ldgr_display_product_courses = 'on';
				} else {
					$ldgr_display_product_courses = 'off';
				}
				update_option( 'ldgr_display_product_courses', $ldgr_display_product_courses );

				$ldgr_autofill_group_name = 'off';
				if ( 'on' === ldgr_filter_input( 'ldgr_autofill_group_name' ) ) {
					$ldgr_autofill_group_name = 'on';
				} else {
					$ldgr_autofill_group_name = 'off';
				}
				update_option( 'ldgr_autofill_group_name', $ldgr_autofill_group_name );

				$ldgr_display_product_footer = 'on';
				if ( 'on' === ldgr_filter_input( 'ldgr_display_product_footer' ) ) {
					$ldgr_display_product_footer = 'on';
				} else {
					$ldgr_display_product_footer = 'off';
				}
				update_option( 'ldgr_display_product_footer', $ldgr_display_product_footer );
			}
			// Get setup wizard link.
			$wizard_handler    = \Wisdm_Wizard_Handler::get_instance();
			$setup_wizard_link = $wizard_handler->get_wizard_first_step_link( 'ld-group-registration' );
			include_once plugin_dir_path( __DIR__ ) . 'templates/wdm-global-setting.php';
		}

		/**
		 * Add settings page link on dashboard
		 *
		 * @param array $links  Array of all the links.
		 */
		public function add_settings_page_link( $links ) {
			$settings_link = [
				'<a href="' . admin_url( 'admin.php?page=wdm-ld-gr-setting' ) . '">' . __( 'Settings', 'wdm_ld_group' ) . '</a>',
			];
			return array_merge( $links, $settings_link );
		}

		/**
		 * Handle redirects for woocommerce logins
		 *
		 * @param string $redirect_to   URL to be redirected to.
		 * @param obj    $user             User details object.
		 *
		 * @return string               Updated redirect URL.
		 */
		public function handle_woo_login_redirect( $redirect_to, $user ) {
			return $this->handle_wp_login_redirect( $redirect_to, '', $user );
		}

		/**
		 * Handle all login redirects
		 *
		 * @param string $redirect_to   URL to redirect to.
		 * @param string $requested     Requested URL.
		 * @param obj    $user             User object.
		 *
		 * @return string               Updated URL to be redirected to.
		 */
		public function handle_wp_login_redirect( $redirect_to, $requested, $user ) {
			// If unsuccessful login or administrator, return.
			if ( is_wp_error( $user ) || empty( $user ) || user_can( $user, 'manage_options' ) ) {
				return $redirect_to;
			}

			// Check if user redirects enabled.
			$user_redirects = get_option( 'ldgr_user_redirects' );

			// If disabled then return.
			if ( 'on' != $user_redirects ) {
				return $redirect_to;
			}

			// Get settings for group leader redirection.
			$redirect_group_leader = get_option( 'ldgr_redirect_group_leader' );

			if ( ! empty( $redirect_group_leader ) ) {
				// Check if is a group leader.
				if ( learndash_is_group_leader_user( $user->ID ) ) {
					return get_page_link( $redirect_group_leader );
				}
			}

			// Get settings for group user redirection.
			$redirect_group_user = get_option( 'ldgr_redirect_group_user' );

			if ( ! empty( $redirect_group_user ) ) {
				// Check if group user (i.e. Subscriber) and only one role.
				if ( in_array( 'subscriber', $user->roles ) && 1 == count( $user->roles ) ) {
					return get_page_link( $redirect_group_user );
				}
			}

			return $redirect_to;
		}

		/**
		 * Add Feedback Setting Tab
		 *
		 * @param array $setting_tabs
		 *
		 * @return array
		 * @since 4.1.0
		 *
		 * @deprecated 4.3.14 This function is no longer in use.
		 */
		public function add_feedback_and_other_setting_tab_header( $setting_tabs ) {
			_deprecated_function( __METHOD__, '4.3.14' );
		}

		/**
		 * Display the feedback tab
		 *
		 * @param string $current_tab   Currently active tab.
		 *
		 * @deprecated 4.3.14 This function is no longer in use.
		 */
		public function display_feedback_tab_contents( $current_tab ) {
			_deprecated_function( __METHOD__, '4.3.14' );
		}

		/**
		 * Display appearance settings options.
		 *
		 * @since 4.2.2
		 */
		public function display_appearance_settings() {
			// Enqueue styles
			wp_enqueue_style(
				'wdm-admin_css',
				plugins_url(
					'css/wdm-admin.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);

			wp_enqueue_style( 'wp-color-picker' );

			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}

			$group_label  = 'group';
			$course_label = 'course';

			if ( function_exists( 'LearnDash_Custom_Label' ) ) {
				$group_label  = \LearnDash_Custom_Label::label_to_lower( 'group' );
				$course_label = \LearnDash_Custom_Label::label_to_lower( 'course' );
			}

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-appearance-settings.template.php',
				[
					'ldgr_dashboard_banner_color' => get_option( 'ldgr_dashboard_banner_color' ),
					'ldgr_dashboard_accent_color' => get_option( 'ldgr_dashboard_accent_color' ),
					'ldgr_dashboard_footer_color' => get_option( 'ldgr_dashboard_footer_color' ),
					'ldgr_default_group_image'    => get_option( 'ldgr_default_group_image' ),
					'ldgr_default_course_image'   => get_option( 'ldgr_default_course_image' ),
					'group_label'                 => $group_label,
					'course_label'                => $course_label,
				]
			);
		}

		/**
		 * Save appearance settings options
		 *
		 * @since 4.2.2
		 */
		public function save_apperance_settings() {
			if ( array_key_exists( 'ldgr_nonce', $_POST ) && wp_verify_nonce( $_POST['ldgr_nonce'], 'ldgr_save_appearance_settings' ) ) {
				// Save dashboard banner color.
				$ldgr_dashboard_banner_color = array_key_exists( 'ldgr_dashboard_banner_color', $_POST ) ? trim( $_POST['ldgr_dashboard_banner_color'] ) : '';
				update_option( 'ldgr_dashboard_banner_color', $ldgr_dashboard_banner_color );

				// Save dashboard accent color.
				$ldgr_dashboard_accent_color = array_key_exists( 'ldgr_dashboard_accent_color', $_POST ) ? trim( $_POST['ldgr_dashboard_accent_color'] ) : '';
				update_option( 'ldgr_dashboard_accent_color', $ldgr_dashboard_accent_color );

				// Save dashboard footer color.
				$ldgr_dashboard_footer_color = array_key_exists( 'ldgr_dashboard_footer_color', $_POST ) ? trim( $_POST['ldgr_dashboard_footer_color'] ) : '';
				update_option( 'ldgr_dashboard_footer_color', $ldgr_dashboard_footer_color );

				// Save default group image.
				$ldgr_default_group_image = array_key_exists( 'ldgr_default_group_image', $_POST ) ? trim( $_POST['ldgr_default_group_image'] ) : '';
				update_option( 'ldgr_default_group_image', $ldgr_default_group_image );

				// Save default course image.
				$ldgr_default_course_image = array_key_exists( 'ldgr_default_course_image', $_POST ) ? trim( $_POST['ldgr_default_course_image'] ) : '';
				update_option( 'ldgr_default_course_image', $ldgr_default_course_image );
			}
		}

		/**
		 * Recalculate total group seats to include existing seats and enrolled users.
		 *
		 * @todo To be removed in future versions.
		 *
		 * @since 4.3.8
		 */
		public function migrate_and_recalculate_group_seats() {
			$recalculate_total_seats = get_option( 'ldgr_recalculate_total_seats', false );
			if ( false === $recalculate_total_seats ) {
				error_log( '------------ Migrating and recalculating group seats-----------' );

				$groups_query_args = [
					'post_type'   => 'groups',
					'nopaging'    => true,
					'post_status' => [ 'publish', 'pending', 'draft', 'future', 'private' ],
					'fields'      => 'ids',
				];

				$groups_query = new \WP_Query( $groups_query_args );

				foreach ( $groups_query->posts as $group_id ) {
					$seats_left     = (int) get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true );
					$old_total      = (int) get_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, true );
					$enrolled_users = (int) count( learndash_get_groups_user_ids( $group_id ) );

					$limit = $seats_left + $enrolled_users;
					error_log( '----------------------' );
					error_log( 'For Group ID: ' . print_r( $group_id, 1 ) );
					error_log( 'Old Total : ' . print_r( $old_total, 1 ) );
					error_log( '-- Seats Left : ' . print_r( $seats_left, 1 ) );
					error_log( '-- Enrolled Users : ' . print_r( $enrolled_users, 1 ) );
					error_log( 'New Total : ' . print_r( $limit, 1 ) );
					error_log( '----------------------' );
					update_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, $limit );
				}
				update_option( 'ldgr_recalculate_total_seats', true );
				error_log( '------------ End of migrating and recalculating total group seats-----------' );
			}
		}

		/**
		 * Add the Helpscout Beacon script on the LDGR Settings backend pages.
		 * Callback to action hook 'ldgr_helpscout_beacon'.
		 *
		 * @since 4.9.10
		 * @deprecated 4.3.14 This function is no longer in use.
		 */
		public function ldgr_add_beacon_helpscout_script() {
			_deprecated_function( __METHOD__, '4.3.14' );
			?>
				<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script><script type="text/javascript">window.Beacon('init', '3e5c5ccb-2341-4aae-beb0-3fad2b47f7b9')</script>
			<?php
		}

		/**
		 * Calling function to the helpscout beacon.
		 * Callback to action hook 'ldgr_helpscout_beacon'.
		 *
		 * @since 4.9.10
		 * @deprecated 4.3.14 This function is no longer in use.
		 */
		public function ldgr_load_beacon_helpscout() {
			_deprecated_function( __METHOD__, '4.3.14' );

			global $pagenow, $typenow;

			// If CPT 'form' and edit.php page.
			if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'wdm-ld-gr-setting' === $_GET['page'] ) {
				/**
				 * Use the action to execute some code on the PEP backend page.
				 *
				 * @hooked add_beacon_helpscout_script - 10
				 */
				do_action( 'ldgr_helpscout_beacon' );
			}
		}
	}
}
