<?php
/**
 * Settings Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore instuctor istrue slashs wdmcheck wdmid // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Dashboard_Block;
use LearnDash\Core\Utilities\Cast;
use LearnDash\Instructor_Role\Utilities\Translation;
use WP_User;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Settings' ) ) {
	/**
	 * Class Instructor Role Settings Module
	 */
	class Instructor_Role_Settings extends Instructor_Role_Dashboard_Block {
		/**
		 * Singleton instance of this class
		 *
		 * @var self $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 3.3.0
		 */
		protected $plugin_slug = '';

		public function __construct() {
			$this->plugin_slug = INSTRUCTOR_ROLE_TXT_DOMAIN;
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @since 3.5.0
		 *
		 * @return self
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Adding Instructor commission menu inside learndash-lms menu.
		 *
		 * @since 2.4.0
		 */
		public function instuctor_menu() {
			if ( $this->wdmCheckInstructorCap() ) {
				add_submenu_page(
					'learndash-lms',
					__( 'Instructor', 'wdm_instructor_role' ),
					__( 'Instructor', 'wdm_instructor_role' ),
					'instructor_page',
					'instuctor',
					[ $this, 'instuctor_page_callback' ]
				);
			}
		}

		/**
		 * Showing menus to instructor and to hind according to setting for new disable commission system feature.
		 *
		 * @return boolean to show menu or not
		 *
		 * @since 2.4.0
		 */
		public function wdmCheckInstructorCap() {
			$wdmid_admin_setting = get_option( '_wdmir_admin_settings', [] );
			$wl8_show_email_tab  = false;
			$wl8_show_com_n_ex   = true;
			if ( ! is_super_admin() && array_key_exists( 'instructor_mail', $wdmid_admin_setting ) && 1 == $wdmid_admin_setting['instructor_mail'] ) {
				$wl8_show_email_tab = true;
			}
			if ( ! is_super_admin() && array_key_exists( 'wdm_enable_instructor_course_mail', $wdmid_admin_setting ) && 1 == $wdmid_admin_setting['wdm_enable_instructor_course_mail'] ) {
				$wl8_show_email_tab = true;
			}
			if ( ! is_super_admin() && empty( $wdmid_admin_setting['instructor_commission'] ) ) {
				$wl8_show_com_n_ex = false;
			}
			$wdm_instructor = get_role( 'wdm_instructor' );
			if ( null !== $wdm_instructor ) {
				if ( ! $wl8_show_email_tab && ! $wl8_show_com_n_ex ) {
					$wdm_instructor->remove_cap( 'instructor_page' );
					return false;
				} else {
					$wdm_instructor->add_cap( 'instructor_page' );
					return true;
				}
			}
		}

		/**
		 * Adding tabs inside instructor commission page.
		 *
		 * @since 2.4.0
		 */
		public function instuctor_page_callback() {
			// check whether email tab should exist for instructor or not.
			$ir_admin_settings        = get_option( '_wdmir_admin_settings', [] );
			$ir_enable_email_settings = false;
			$ir_enable_commission     = true; // for showing commission and export tabs.
			$user_id                  = get_current_user_id();
			$is_commission_disabled   = get_user_meta( $user_id, 'ir_commission_disabled', true );
			$onboarding_step          = ir_filter_input( 'onboarding', INPUT_GET, 'string' );

			// If admin select instructor mail option then we need to display only three tabs.
			if ( array_key_exists( 'instructor_mail', $ir_admin_settings ) && 1 == $ir_admin_settings['instructor_mail'] ) {
				$ir_enable_email_settings = true;
			}

			if ( ( ! is_super_admin() && empty( $ir_admin_settings['instructor_commission'] ) ) || 1 == $is_commission_disabled ) {
				$ir_enable_commission = false;
			}

			$current_tab = $this->wdmSetCurrentTab( $ir_admin_settings, $ir_enable_commission, $ir_enable_email_settings );
			?>
			<div class="ir-mobile-header">
				<div class="ir-mobile-header-content">
					<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-text-wrap-disabled" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
						<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
						<path d="M4 6l10 0" />
						<path d="M4 18l10 0" />
						<path d="M4 12h17l-3 -3m0 6l3 -3" />
					</svg>
					<span>Instructor Role</span>
				</div>
			</div>
			<div class="admin-settings <?php echo ( false !== $onboarding_step ) ? 'onboarding-screen' : ''; ?>" style="background: #fff;">
				<div class="nav-tab-wrapper">
				<div style="font-size:18px; font-weight:600; color:#666666;padding-left:16px;"><?php esc_html_e( 'Instructor Dashboard', 'wdm_instructor_role' ); ?></div>
				<hr style="margin-top:17px; margin-bottom:22px; width:90%;">
				<?php $this->wl8ShowTabs( $current_tab, $ir_enable_email_settings, $ir_enable_commission ); ?>
				</div>
				<?php
					/**
					 * Hook to add instructor setting tab headers.
					 *
					 * Used to add additional instructor setting tab header for adding new settings tabs.
					 *
					 * @since 3.4.0
					 *
					 * @param string $current_tab Current selected instructor settings tab tab
					 */
					do_action( 'instuctor_tab_add', $current_tab );
				?>
			</h2>
			<?php
			$this->wl8ShowCurrentTab( $current_tab );
		}

		/**
		 * Checking current tab if not set then setting it to default tab
		 *
		 * @param array $wdmid_admin_setting        List of settings tabs
		 * @param bool  $ir_enable_commission        Whether instructor commission settings are enabled.
		 * @param bool  $ir_enable_email_settings    Whether instructor email settings are enabled.
		 *
		 * @return string $current_tab              Current active tab
		 */
		public function wdmSetCurrentTab( $wdmid_admin_setting, $ir_enable_commission, $ir_enable_email_settings ) {
			$instructor_allowed_tabs = [
				'commission_report',
				'export',
				'email',
				'instructor-email',
			];

			/**
			 * Filter list of tabs accessible to instructors.
			 *
			 * @param array $instructor_allowed_tabs    List of instructor setting tab instructor has access to.
			 *
			 * @since 3.5.5
			 */
			$instructor_allowed_tabs = apply_filters( 'ir_filter_instructor_allowed_tabs', $instructor_allowed_tabs );

			if ( current_user_can( 'manage_options' ) ) {
				$current_tab = 'setup';
			} elseif ( ! is_super_admin() && array_key_exists( 'instructor_mail', $wdmid_admin_setting ) && 1 == $wdmid_admin_setting['instructor_mail'] ) {
				$current_tab = 'email';
			} else {
				$current_tab = 'instructor-email';
			}

			// If instructor and tab set.
			if ( wdm_is_instructor() && isset( $_GET['tab'] ) ) {
				// Check if tab access allowed.
				if ( in_array( $_GET['tab'], $instructor_allowed_tabs ) ) {
					$current_tab = $_GET['tab'];
				}
			}

			// If admin allow all access.
			if ( is_super_admin() && isset( $_GET['tab'] ) ) {
				$current_tab = $_GET['tab'];
				if ( empty( $current_tab ) ) {
					$current_tab = 'instructor';
				}
			}

			return $current_tab;
		}

		/**
		 * Functions shows all tabs depending on conditions.
		 *
		 * @param string $current_tab              Currently selected instructor settings tab.
		 * @param bool   $ir_enable_email_settings Whether email settings are enabled or not.
		 * @param bool   $ir_enable_commission     Whether commission settings are enabled or not.
		 *
		 * @since 2.4.0
		 */
		public function wl8ShowTabs( $current_tab, $ir_enable_email_settings, $ir_enable_commission ) {
			$settings_tabs = [
				'setup'              => [
					'title'  => __( 'Setup', 'wdm_instructor_role' ),
					'access' => [ 'admin' ],
					'svg'    => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-zoom-reset" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M21 21l-6 -6" /><path d="M3.268 12.043a7.017 7.017 0 0 0 6.634 4.957a7.012 7.012 0 0 0 7.043 -6.131a7 7 0 0 0 -5.314 -7.672a7.021 7.021 0 0 0 -8.241 4.403" /><path d="M3 4v4h4" /></svg>',
				],
				'instructor'         => [
					'title'  => __( 'Manage instructor', 'wdm_instructor_role' ),
					'access' => [ 'admin' ],
					'svg'    => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-school" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" /><path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" /></svg>',
				],
				'dashboard_settings' => [
					'title'  => __( 'Dashboard Settings', 'wdm_instructor_role' ),
					'access' => [ 'admin' ],
					'svg'    => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z" /><path d="M4 16h6v4h-6z" /><path d="M14 12h6v8h-6z" /><path d="M14 4h6v4h-6z" /></svg>',
				],
				'commission_report'  => [
					'title'  => __( 'Commissions', 'wdm_instructor_role' ),
					'access' => [ 'admin', 'instructor' ],
					'svg'    => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt-tax" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l6 -6" /><circle cx="9.5" cy="8.5" r=".5" fill="currentColor" /><circle cx="14.5" cy="13.5" r=".5" fill="currentColor" /><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" /></svg>',
				],
				'email'              => [
					'title'  => __( 'Email Notification', 'wdm_instructor_role' ),
					'access' => [ 'admin', 'instructor' ],
					'svg'    => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail-share" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M13 19h-8a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v6" /><path d="M3 7l9 6l9 -6" /><path d="M16 22l5 -5" /><path d="M21 21.5v-4.5h-4.5" /></svg>',
				],
			];

			/**
			 * Filter the instructor settings tabs to be displayed
			*
			* @param array $settings_tabs    List of setting tabs to be displayed.
			* @param string $current_tab      Slug of the currently selected tab.
			*
			* @since 3.4.0
			*/
			$settings_tabs = apply_filters( 'ir_filter_instructor_setting_tabs', $settings_tabs, $current_tab );

			$settings_tabs['docs_and_videos'] = [
				'title'  => __( 'Docs and Videos', 'wdm_instructor_role' ),
				'access' => [ 'admin' ],
				'svg'    => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-info" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M11 14h1v4h1" /><path d="M12 11h.01" /></svg>',
			];

			foreach ( $settings_tabs as $key => $tab ) {
				// Check if admin tab.
				if ( current_user_can( 'manage_options' ) && ! in_array( 'admin', $tab['access'] ) ) {
					continue;
				}

				// Check if instructor tab.
				if ( wdm_is_instructor() && ! in_array( 'instructor', $tab['access'] ) ) {
					continue;
				}

				// If commission and export tab but setting disabled then don't show.
				if ( ( 'commission_report' == $key || 'export' == $key ) && ! $ir_enable_commission ) {
					continue;
				}

				// If email tab but setting disabled then don't show.
				if ( wdm_is_instructor() && 'email' == $key && ! $ir_enable_email_settings ) {
					continue;
				}
				?>
			<a class="nav-tab ir-flex align-center <?php echo ( $current_tab == $key ) ? 'nav-tab-active' : ''; ?>" href="?page=instuctor&tab=<?php echo esc_attr( $key ); ?>">
				<?php echo $tab['svg']; ?> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
				<?php echo esc_html( $tab['title'] ); ?>
			</a>
				<?php
			}
		}

		/**
		 * Function shows commission and export content.
		 *
		 * @param string $current_tab       Current tab
		 * @param bool   $wl8_show_com_n_ex Whether commission settings are enabled or not.
		 *
		 * @return html to show tabs
		 *
		 * @since 2.4.0
		 */
		public function wl8ShowCommissionAndExportContent( $current_tab, $wl8_show_com_n_ex ) {
			if ( ! $wl8_show_com_n_ex ) {
				return;
			}

			$tabs = [
				'commission_report' => __( 'Commission Report', 'wdm_instructor_role' ),
				'export'            => __( 'Export', 'wdm_instructor_role' ),
			];

			/**
			 * Filter the commission and export tabs display
			 *
			 * @param array $tabs              The tabs to currently be displayed
			 * @param bool   $wl8_show_com_n_ex Whether commission settings are enabled or not.
			 *
			 * @return array $tabs    Updated list of tabs to be displayed
			 */
			$tabs = apply_filters( 'ir_filter_commission_and_export_tabs', $tabs, $wl8_show_com_n_ex );

			foreach ( $tabs as $key => $tab ) {
				?>
				<a class="nav-tab <?php echo ( $current_tab == $key ) ? 'nav-tab-active' : ''; ?>" href="?page=instuctor&tab=<?php echo esc_attr( $key ); ?>">
					<?php echo esc_html( $tab ); ?>
				</a>
				<?php
			}
		}

		/**
		 * Function shows mail tab.
		 *
		 * @param string  $current_tab          Current_tab.
		 * @param boolean $wl8_show_email_tab   Whether email tab to be displayed or not.
		 * @param bool    $wl8_show_com_n_ex     Whether commission settings are enabled or not.
		 *
		 * @return html to show tab
		 */
		public function wl8ShowMailTab( $current_tab, $wl8_show_email_tab, $wl8_show_com_n_ex ) {
			if ( is_super_admin() || $wl8_show_email_tab ) {
				?>
				<a class="nav-tab <?php echo ( 'email' === $current_tab ) ? 'nav-tab-active' : ''; ?>" href="?page=instuctor&tab=email">
					<?php esc_html_e( 'Email', 'wdm_instructor_role' ); ?>
				</a>
				<?php
			}
		}

		/**
		 * Function shows current tab.
		 *
		 * @param string $current_tab Current tab.
		 *
		 * @since 2.4.0
		 */
		public function wl8ShowCurrentTab( $current_tab ) {
			switch ( $current_tab ) {
				case 'setup':
					$this->wdm_instructor_setup_tab();
					break;
				case 'instructor':
					$this->wdm_instructor_first_tab();
					break;
				case 'dashboard_settings':
					$this->wdm_instructor_dashboard_settings();
					break;
				case 'commission_report':
					$this->wdm_instructor_second_tab();
					break;
				case 'export':
					$this->wdm_instructor_third_tab();
					break;
				case 'email':
					if ( is_super_admin() ) {
						$this->wdmir_instructor_email_settings();
					} else {
						$this->wdmir_individual_instructor_email_setting();
					}
					break;
				case 'settings':
					$this->wdmir_instructor_settings();
					break;
				case 'docs_and_videos':
					$this->docs_and_videos();
					break;
				case 'menu_settings':
					$this->show_hide_menu_settings();
					break;
				case 'overview_settings':
					$this->show_hide_overview_settings();
					break;

				case 'backend-dashboard':
					$this->show_backend_dashboard_settings();
					break;
			}

			/**
			 * Display instructor content based on currently selected tab
			 *
			 * @param string $current_tab  Current selected instructor settings tab.
			 *
			 * @since 2.4.0
			 */
			do_action( 'instuctor_tab_checking', $current_tab );
		}

		/**
		 * Creates Menu Settings Tab
		 *
		 * @since 4.3.1
		 */
		public function show_hide_menu_settings() {
			global $menu, $submenu;
			// Required JS scripts.
			wp_enqueue_script(
				'ir-sortable-deps',
				plugins_url( '/js/settings/ir-sortable-menu-deps.js', __DIR__ ),
				[ 'jquery', 'jquery-ui-sortable', 'jquery-ui-core', 'jquery-ui-accordion' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/js/settings/ir-sortable-menu-deps.js' ),
				1
			);

			wp_localize_script(
				'ir-sortable-deps',
				'ir_sortable_deps_object',
				[
					'delete_confirm'             => __( 'Do you really want to reset overview settings to defaults ?', 'wdm_instructor_role' ),
					'empty_string'               => __( 'Input cannot be empty', 'wdm_instructor_role' ),
					'custom_menu_delete_confirm' => __( 'Do you really want to delete this custom menu ?', 'wdm_instructor_role' ),
				]
			);

			// Save logic.
			if ( ! empty( $_POST ) && array_key_exists( 'ir_sidebar_menu', $_POST ) && array_key_exists( 'ir_sidebar_sub_menu', $_POST ) && array_key_exists( 'ir-save-menu', $_POST ) ) {
				$save_menu     = $_POST['ir_sidebar_menu'];
				$save_sub_menu = $_POST['ir_sidebar_sub_menu'];
				ir_set_settings( 'ir_sidebar_menu', $save_menu );
				ir_set_settings( 'ir_sidebar_sub_menu', $save_sub_menu );
			}
			// Edit custom menu logic.
			if ( ! empty( $_POST ) && array_key_exists( 'ir_edit_custom_sidebar_menu', $_POST ) && array_key_exists( 'ir-update-custom-menu', $_POST ) ) {
				// get data to manipulate.
				$update_custom_menu = $_POST['ir_edit_custom_sidebar_menu'];
				$sidebar_menu       = ir_get_settings( 'ir_sidebar_menu' );

				foreach ( $update_custom_menu as $menu_key => $custom_menu_item ) {
					// Empty validation.
					if ( empty( $custom_menu_item['title'] ) || empty( $custom_menu_item['slug'] ) ) {
						continue;
					}
					// check if any data mismatch.
					if ( $custom_menu_item['slug'] != $custom_menu_item['old_slug'] || $custom_menu_item['title'] != $custom_menu_item['old_title'] || $custom_menu_item['icon'] != $custom_menu_item['old_icon'] ) {
						// get key of the old menu.
						$mismatch_key = $custom_menu_item['old_slug'];
						$menus_column = array_column( $sidebar_menu, 'slug' );
						// get old position of the custom menu.
						$key_position = array_search( $mismatch_key, $menus_column );
						// updated data.
						$updated_menu = [
							$custom_menu_item['slug'] => [
								'slug'  => $custom_menu_item['slug'],
								'title' => $custom_menu_item['title'],
								'icon'  => $custom_menu_item['icon'],
								'type'  => 'custom',
							],
						];
						// remove old element.
						unset( $sidebar_menu[ $mismatch_key ] );
						// splice array with associative key.
						$sidebar_menu = array_slice( $sidebar_menu, 0, $key_position, true ) + $updated_menu + array_slice( $sidebar_menu, $key_position, null, true );
						// cascade submenu update.
						$new_index_key = $custom_menu_item['slug'];
						$submenu_data  = ir_get_settings( 'ir_sidebar_sub_menu' );
						$old_index_key = $custom_menu_item['old_slug'];
						if ( $new_index_key != $old_index_key ) {
							$key_pos              = array_column( $submenu_data, 'slug' );
							$submenu_key_position = array_search( $old_index_key, $key_pos );
							$target_submenu_data  = [
								$new_index_key => $submenu_data[ $old_index_key ],
							];
							// remove old element.
							unset( $submenu_data[ $old_index_key ] );
							// push new associative element.
							$submenu_data = array_slice( $submenu_data, 0, $submenu_key_position, true ) + $target_submenu_data + array_slice( $submenu_data, $submenu_key_position, null, true );
							ir_set_settings( 'ir_sidebar_sub_menu', $submenu_data );
						}
						ir_set_settings( 'ir_sidebar_menu', $sidebar_menu );
					}
				}
			}

			// Edit custom sub menu logic.
			if ( ! empty( $_POST ) && array_key_exists( 'ir_edit_custom_sidebar_sub_menu', $_POST ) && array_key_exists( 'ir-update-custom-sub-menu', $_POST ) ) {
				// get data to manipulate.
				$update_custom_sub_menu = $_POST['ir_edit_custom_sidebar_sub_menu'];
				$sidebar_sub_menu       = ir_get_settings( 'ir_sidebar_sub_menu' );
				foreach ( $update_custom_sub_menu as $key => $custom_menu_sub_set_item ) {
					foreach ( $custom_menu_sub_set_item as $subkey => $custom_menu_sub_item ) { // cspell:disable-line .
						// Empty validation.
						if ( empty( $custom_menu_sub_item['title'] ) || empty( $custom_menu_sub_item['slug'] ) ) {
							continue;
						}

						// check if any data mismatch.
						if ( ! filter_var( $custom_menu_sub_item['slug'], FILTER_VALIDATE_URL ) === false && ( $custom_menu_sub_item['slug'] != $custom_menu_sub_item['old_slug'] || $custom_menu_sub_item['title'] != $custom_menu_sub_item['old_title'] || ( isset( $custom_menu_sub_item['old_icon'] ) && $custom_menu_sub_item['icon'] != $custom_menu_sub_item['old_icon'] ) ) ) {
							// get key of the old menu.
							$mismatch_key = $custom_menu_sub_item['old_slug'];
							$menus_column = array_column( $sidebar_sub_menu[ $key ], 'slug' );
							// get old position of the custom menu.
							$key_position = array_search( $mismatch_key, $menus_column );
							// updated data.
							$updated_sub_menu = [
								$custom_menu_sub_item['slug'] => [
									'slug'  => $custom_menu_sub_item['slug'],
									'title' => $custom_menu_sub_item['title'],
									'icon'  => isset( $custom_menu_sub_item['icon'] ) ? $custom_menu_sub_item['icon'] : '',
									'type'  => 'custom',
								],
							];
							// remove old element.
							unset( $sidebar_sub_menu[ $key ][ $mismatch_key ] );
							// splice array with associative key.
							$sidebar_sub_menu[ $key ] = array_slice( $sidebar_sub_menu[ $key ], 0, $key_position, true ) + $updated_sub_menu + array_slice( $sidebar_sub_menu[ $key ], $key_position, null, true );

							ir_set_settings( 'ir_sidebar_sub_menu', $sidebar_sub_menu );
						}
					}
				}
			}

			// Save custom menu.
			if ( ! empty( $_POST ) && array_key_exists( 'custom_menu', $_POST ) && array_key_exists( 'ir-save-custom-menu', $_POST ) ) {
				$save_menu     = $_POST['ir_sidebar_menu'];
				$save_sub_menu = $_POST['ir_sidebar_sub_menu'];
				ir_set_settings( 'ir_sidebar_menu', $save_menu );
				ir_set_settings( 'ir_sidebar_sub_menu', $save_sub_menu );

				$custom_menu = $_POST['custom_menu'];
				if ( '' != $custom_menu['slug'] && '' != $custom_menu['title'] ) {
					$custom_item_array = [
						$custom_menu['slug'] => $custom_menu,
					];
					$url               = $custom_menu['slug'];
					if ( ! filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
						// get actual array.
						$sidebar_menu = ir_get_settings( 'ir_sidebar_menu' );
						$key_position = count( $sidebar_menu ) - 1;
						// Splice array to save data at second last position.
						$sidebar_menu = array_slice( $sidebar_menu, 0, $key_position, true ) + $custom_item_array + array_slice( $sidebar_menu, $key_position, null, true );
						ir_set_settings( 'ir_sidebar_menu', $sidebar_menu );
					} else {
						echo '<h2>Please enter a valid URL</h2>';
					}
				}
			}

			// Save custom sub menu.
			if ( ! empty( $_POST ) && array_key_exists( 'custom_menu', $_POST ) && array_key_exists( 'ir-save-custom-sub-menu', $_POST ) ) {
				$save_menu     = $_POST['ir_sidebar_menu'];
				$save_sub_menu = $_POST['ir_sidebar_sub_menu'];
				ir_set_settings( 'ir_sidebar_menu', $save_menu );
				ir_set_settings( 'ir_sidebar_sub_menu', $save_sub_menu );

				$custom_sub_menu = $_POST['custom_sub_menu'];
				foreach ( $custom_sub_menu as $key => $custom_sub_menu_item ) {
					if ( ! filter_var( $custom_sub_menu_item['slug'], FILTER_VALIDATE_URL ) === false && '' != $custom_sub_menu_item['slug'] && '' != $custom_sub_menu_item['title'] ) {
						$sidebar_sub_menu = ir_get_settings( 'ir_sidebar_sub_menu' );
						$sidebar_sub_menu[ $key ][ $custom_sub_menu_item['slug'] ] = $custom_sub_menu_item;
						ir_set_settings( 'ir_sidebar_sub_menu', $sidebar_sub_menu );
					}
				}
			}

			// Reset data.
			if ( isset( $_POST['ir-menu-reset-settings'] ) ) {
				ir_set_settings( 'ir_sidebar_menu', '' );
				ir_set_settings( 'ir_sidebar_sub_menu', '' );
			}

			$sidebar_menu     = ir_get_settings( 'ir_sidebar_menu' );
			$sidebar_sub_menu = ir_get_settings( 'ir_sidebar_sub_menu' );

			$sidebar_menu = $this->get_dashboard_sidebar_menu( $sidebar_menu );

			// Template render.
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/settings/ir-show-hide-instructor-menu.template.php',
				[
					'sidebar_menu'     => $sidebar_menu,
					'sidebar_sub_menu' => $sidebar_sub_menu,
				]
			);
		}

		/**
		 * Save Helper function For Overview Settings page
		 *
		 * @since 4.3.1
		 */
		public function save_overview_settings() {
			// defaults.
			$ir_overview_settings = [];
			if ( isset( $_POST['ir_overview_settings'] ) ) {
				$ir_overview_settings = $_POST['ir_overview_settings'];
			} else {
				$ir_overview_settings = ir_get_settings( 'ir_overview_settings' );
				if ( is_string( $ir_overview_settings ) || ! $ir_overview_settings ) {
					$ir_overview_settings = [
						'course_block'     => '',
						'student_block'    => '',
						'product_block'    => '',
						'earnings_block'   => '',
						'reports_block'    => '',
						'submission_block' => '',
					];
				}
			}

			if ( ! isset( $ir_overview_settings['course_block'] ) || 'off' != $ir_overview_settings['course_block'] ) {
				$ir_overview_settings['course_block'] = 'on';
			}
			if ( isset( $_POST['ir-save-overview-settings'] ) && isset( $ir_overview_settings['course_block'] ) && ! isset( $_POST['ir_overview_settings']['course_block'] ) ) {
				$ir_overview_settings['course_block'] = 'off';
			}

			if ( ! isset( $ir_overview_settings['student_block'] ) || 'off' != $ir_overview_settings['student_block'] ) {
				$ir_overview_settings['student_block'] = 'on';
			}
			if ( isset( $_POST['ir-save-overview-settings'] ) && isset( $ir_overview_settings['student_block'] ) && ! isset( $_POST['ir_overview_settings']['student_block'] ) ) {
				$ir_overview_settings['student_block'] = 'off';
			}

			if ( ! isset( $ir_overview_settings['product_block'] ) || 'off' != $ir_overview_settings['product_block'] ) {
				$ir_overview_settings['product_block'] = 'on';
			}
			if ( isset( $_POST['ir-save-overview-settings'] ) && isset( $ir_overview_settings['product_block'] ) && ! isset( $_POST['ir_overview_settings']['product_block'] ) ) {
				$ir_overview_settings['product_block'] = 'off';
			}

			if ( ! isset( $ir_overview_settings['earnings_block'] ) || 'off' != $ir_overview_settings['earnings_block'] ) {
				$ir_overview_settings['earnings_block'] = 'on';
			}
			if ( isset( $_POST['ir-save-overview-settings'] ) && isset( $ir_overview_settings['earnings_block'] ) && ! isset( $_POST['ir_overview_settings']['earnings_block'] ) ) {
				$ir_overview_settings['earnings_block'] = 'off';
			}

			if ( ! isset( $ir_overview_settings['reports_block'] ) || 'off' != $ir_overview_settings['reports_block'] ) {
				$ir_overview_settings['reports_block'] = 'on';
			}
			if ( isset( $_POST['ir-save-overview-settings'] ) && isset( $ir_overview_settings['reports_block'] ) && ! isset( $_POST['ir_overview_settings']['reports_block'] ) ) {
				$ir_overview_settings['reports_block'] = 'off';
			}

			if ( ! isset( $ir_overview_settings['submission_block'] ) || 'off' != $ir_overview_settings['submission_block'] ) {
				$ir_overview_settings['submission_block'] = 'on';
			}
			if ( isset( $_POST['ir-save-overview-settings'] ) && isset( $ir_overview_settings['submission_block'] ) && ! isset( $_POST['ir_overview_settings']['submission_block'] ) ) {
				$ir_overview_settings['submission_block'] = 'off';
			}

			if ( ! isset( $ir_overview_settings['no_blocks_prompt_message'] ) ) {
				$ir_overview_settings['no_blocks_prompt_message'] = '';
			}

			if ( isset( $_POST['ir-save-overview-settings'] ) && isset( $ir_overview_settings['no_blocks_prompt_message'] ) && isset( $_POST['ir_overview_settings']['no_blocks_prompt_message'] ) ) {
				$ir_overview_settings['no_blocks_prompt_message'] = $_POST['ir_overview_settings']['no_blocks_prompt_message'];
			}

			ir_set_settings( 'ir_overview_settings', $ir_overview_settings );
		}

		/**
		 * Created Overview Settings Tab
		 *
		 * @since 4.3.1
		 */
		public function show_hide_overview_settings() {
			// Required JS scripts.
			wp_enqueue_script(
				'ir-overview-settings-deps',
				plugins_url( '/js/settings/ir-overview-settings-deps.js', __DIR__ ),
				[ 'jquery', 'jquery-ui-sortable', 'jquery-ui-core', 'jquery-ui-accordion' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/js/settings/ir-overview-settings-deps.js' ),
				1
			);
			wp_localize_script(
				'ir-overview-settings-deps',
				'ir_overview_settings_deps_object',
				[
					'delete_confirm' => __( 'Do you really want to reset overview settings to defaults ?', 'wdm_instructor_role' ),
				]
			);

			if ( isset( $_POST['ir-save-overview-settings'] ) ) {
				ir_set_settings( 'ir_overview_settings', '' );
			}
			$this->save_overview_settings();
			// Reset data.
			if ( isset( $_POST['ir-reset-overview-settings'] ) ) {
				$default_settings = [
					'course_block'     => 'on',
					'student_block'    => 'on',
					'product_block'    => 'on',
					'earnings_block'   => 'on',
					'reports_block'    => 'on',
					'submission_block' => 'on',
				];
				ir_set_settings( 'ir_overview_settings', $default_settings );
			}
			$overview_settings = ir_get_settings( 'ir_overview_settings' );
			// Template render.
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/settings/ir-show-hide-instructor-overview.template.php',
				[
					'overview_settings' => $overview_settings,
				]
			);
		}

		/**
		 * Outputs the content of the "Other Extensions" tab.
		 *
		 * @since 3.6.2
		 *
		 * @deprecated 5.9.1
		 *
		 * @return void
		 */
		public function wdmir_promotion() {
			_deprecated_function( __METHOD__, '5.9.1' );
		}

		/**
		 * Showing docs and videos.
		 */
		public function docs_and_videos() {
			$help_articles = [
				[
					'title' => __( 'Installation, Activation, and Prerequisites', 'wdm_instructor_role' ),
					'link'  => 'https://go.learndash.com/iroverview',
				],
				[
					'title' => __( 'Frontend Dashboard: Installation Guide', 'wdm_instructor_role' ),
					'link'  => 'https://learndash.com/support/docs/add-ons/frontend-dashboard-installation-guide/',
				],
				[
					'title' => __( 'How to Customize the Frontend Dashboard (Gutenberg Editor and Global settings)', 'wdm_instructor_role' ),
					'link'  => 'https://learndash.com/support/docs/add-ons/how-to-customize-the-frontend-dashboard-gutenberg-editor-and-global-settings/',
				],
				[
					'title' => __( 'How to disable the Backend dashboard(WP) for Instructors', 'wdm_instructor_role' ),
					'link'  => 'https://learndash.com/support/docs/add-ons/how-to-disable-the-backend-dashboard-for-instructors/',
				],
				[
					'title' => __( 'Elementor Compatibility Details', 'wdm_instructor_role' ),
					'link'  => 'https://learndash.com/support/docs/add-ons/elementor-compatibility/',
				],
				[
					'title' => __( 'Frontend Dashboard for Instructors: Gutenberg Blocks List', 'wdm_instructor_role' ),
					'link'  => 'https://learndash.com/support/docs/add-ons/frontend-dashboard-for-instructors-gutenberg-blocks-list/',
				],
				[
					'title' => __( 'The Frontend Course Creator', 'wdm_instructor_role' ),
					'link'  => 'https://learndash.com/support/docs/add-ons/the-frontend-course-creator/',
				],
				[
					'title' => __( 'The Frontend Quiz Creator', 'wdm_instructor_role' ),
					'link'  => 'https://learndash.com/support/docs/add-ons/the-frontend-quiz-creator/',
				],
				[
					'title' => __( 'Multiple Instructors Shortcode', 'wdm_instructor_role' ),
					'link'  => 'https://learndash.com/support/docs/add-ons/multiple-instructors-shortcode/',
				],
			];
			$video_links   = [
				[
					'title' => __( 'How to onboard instructors?', 'wdm_instructor_role' ),
					'link'  => 'https://www.youtube.com/watch?v=GUe4rHAMH9Q',
				],
				[
					'title' => __( 'How to set an instructor Commission?', 'wdm_instructor_role' ),
					'link'  => 'https://www.youtube.com/watch?v=NfqoMcsWGTw&t=1s',
				],
				[
					'title' => __( 'Frontend Course creator Settings', 'wdm_instructor_role' ),
					'link'  => 'https://www.youtube.com/watch?v=Bi7Tu2M-KEo&t=1s',
				],
				[
					'title' => __( 'Instructor List and Managing Instructors', 'wdm_instructor_role' ),
					'link'  => 'https://www.youtube.com/watch?v=6MYhMjgb7sk',
				],
				[
					'title' => __( 'Multiple Instructors', 'wdm_instructor_role' ),
					'link'  => 'https://www.youtube.com/watch?v=S7cWb5hR74s',
				],
				[
					'title' => __( 'How to sell Courses through Woocommerce?', 'wdm_instructor_role' ),
					'link'  => 'https://www.youtube.com/watch?v=pgOmswyh41g',
				],
				[
					'title' => __( 'Backend dashboard and its configuration', 'wdm_instructor_role' ),
					'link'  => 'https://www.youtube.com/watch?v=WaBbHlx8eVI',
				],
				[
					'title' => __( 'Student Teacher Communication.', 'wdm_instructor_role' ),
					'link'  => 'https://www.youtube.com/watch?v=8qcoGAOlUzc&t=1s',
				],
				[
					'title' => __( 'Full Plugin Walkthrough', 'wdm_instructor_role' ),
					'link'  => 'https://www.youtube.com/watch?v=0atrVP6-XcI',
				],
			];
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/settings/ir-settings-docs-and-videos-settings.template.php',
				[
					'help_articles' => $help_articles,
					'video_links'   => $video_links,
				]
			);
		}

		/*
		 *
		 * Setup Tab
		 *
		 */
		public function wdm_instructor_setup_tab() {
			// To get user Ids of instructors.
			$args                           = [
				'fields' => [ 'ID', 'display_name', 'user_email' ],
				'role'   => 'wdm_instructor',
			];
			$instructors                    = get_users( $args );
			$create_dashboard_onboarding    = ( ir_get_settings( 'create_dashboard_onboarding' ) === 'step_3' ) ? 1 : 0;
			$add_instructor_onboarding      = ( ir_get_settings( 'add_instructor_onboarding' ) === 'step_3' ) ? 1 : 0;
			$instructor_settings_onboarding = ( ir_get_settings( 'instructor_settings_onboarding' ) === 'step_1' ) ? 1 : 0;
			$course_creation_onboarding     = ( ir_get_settings( 'course_creation_onboarding' ) === 'step_1' ) ? 1 : 0;
			$commission_onboarding          = ( ir_get_settings( 'commission_onboarding' ) === 'step_1' ) ? 1 : 0;

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/settings/ir-instructor-setup-settings.template.php',
				[
					'instructors'                => $instructors,
					'create_dashboard_status'    => $create_dashboard_onboarding,
					'add_instructor_status'      => $add_instructor_onboarding,
					'instructor_settings_status' => $instructor_settings_onboarding,
					'course_creation_status'     => $course_creation_onboarding,
					'commission_status'          => $commission_onboarding,
				]
			);
		}

		/*
		 *
		 * Dashboard Settings Tab
		 *
		 */
		public function wdm_instructor_dashboard_settings() {
			if ( current_user_can( 'manage_options' ) ) {
				$is_admin = true;
			} else {
				$is_admin = false;
			}

			$is_learndash_certificate_builder_active = false;

			if ( class_exists( 'LearnDash_Certificate_Builder\Bootstrap' ) ) {
				$is_learndash_certificate_builder_active = true;
			}

			$user_id           = get_current_user_id();
			$nonce             = wp_create_nonce( 'ir-update-pass-' . $user_id );
			$localization_data = [
				'ajax_url'                                => admin_url( 'admin-ajax.php' ),
				'logout_sessions_nonce'                   => wp_create_nonce( 'update-user_' . $user_id ),
				'user_id'                                 => $user_id,
				'update_pass_nonce'                       => $nonce,
				'create_new_course_nonce'                 => wp_create_nonce( 'ir-create-new-course' ),
				'create_new_quiz_nonce'                   => wp_create_nonce( 'ir-create-new-quiz' ),
				'export_order_details_nonce'              => wp_create_nonce( 'ir-export-order-details' ),
				'export_manual_commission_log_nonce'      => wp_create_nonce( 'ir-export-manual-commission-log' ),
				'update_commission_log_nonce'             => wp_create_nonce( 'ir_update_commission_log' ),
				'delete_manual_commission_log_nonce'      => wp_create_nonce( 'ir_commission_log_actions' ),
				'ir_commission_paypal_payout_nonce'       => wp_create_nonce( 'ir_commission_paypal_payout_payment' ),
				'replyto-comment'                         => wp_create_nonce( 'replyto-comment' ),
				'unfiltered-html-comment'                 => wp_create_nonce( 'unfiltered-html-comment' ),
				'course_label'                            => \LearnDash_Custom_Label::get_label( 'course' ),
				'group_label'                             => \LearnDash_Custom_Label::get_label( 'group' ),
				'groups_label'                            => \LearnDash_Custom_Label::get_label( 'groups' ),
				'lesson_label'                            => \LearnDash_Custom_Label::get_label( 'lesson' ),
				'topic_label'                             => \LearnDash_Custom_Label::get_label( 'topic' ),
				'quiz_label'                              => \LearnDash_Custom_Label::get_label( 'quiz' ),
				'question_label'                          => \LearnDash_Custom_Label::get_label( 'question' ),
				'courses_label'                           => \LearnDash_Custom_Label::get_label( 'courses' ),
				'lessons_label'                           => \LearnDash_Custom_Label::get_label( 'lessons' ),
				'topics_label'                            => \LearnDash_Custom_Label::get_label( 'topics' ),
				'quizzes_label'                           => \LearnDash_Custom_Label::get_label( 'quizzes' ),
				'questions_label'                         => \LearnDash_Custom_Label::get_label( 'questions' ),
				'lower_course_label'                      => \LearnDash_Custom_Label::label_to_lower( 'course' ),
				'lower_courses_label'                     => \LearnDash_Custom_Label::label_to_lower( 'courses' ),
				'lower_lesson_label'                      => \LearnDash_Custom_Label::label_to_lower( 'lesson' ),
				'lower_topic_label'                       => \LearnDash_Custom_Label::label_to_lower( 'topic' ),
				'lower_quiz_label'                        => \LearnDash_Custom_Label::label_to_lower( 'quiz' ),
				'lower_quizzes_label'                     => \LearnDash_Custom_Label::label_to_lower( 'quizzes' ),
				'lower_group_label'                       => \LearnDash_Custom_Label::label_to_lower( 'group' ),
				'lower_questions_label'                   => \LearnDash_Custom_Label::label_to_lower( 'questions' ),
				'create_new_course_url'                   => add_query_arg(
					[
						'action' => 'ir_fcb_new_course',
					],
					admin_url( 'admin-ajax.php' ),
				),
				'is_fcc_enabled'                          => ir_get_settings( 'ir_enable_frontend_dashboard' ),
				'empty_overview_msg'                      => ir_get_settings( 'ir_frontend_overview_empty_message' ),
				'is_admin'                                => $is_admin,
				'product_review_enabled'                  => defined( 'WDMIR_REVIEW_PRODUCT' ) ? WDMIR_REVIEW_PRODUCT : false,
				'is_shared_steps'                         => learndash_is_course_shared_steps_enabled(),
				'ld_currency'                             => learndash_get_currency_symbol(),
				'woo_currency'                            => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
				'currency_symbol'                         => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : learndash_get_currency_symbol(),
				'woo_activated'                           => ( class_exists( 'WooCommerce' ) && class_exists( 'Learndash_WooCommerce' ) ) ? true : false,
				'is_shared_steps_questions'               => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Builder', 'shared_questions' ) : '',
				'assignments_comments_enabled'            => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'comment_status' ) : false,
				'assignments_comments_queryable'          => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'publicly_queryable' ) : false,
				'threadComments'                          => get_option( 'thread_comments_depth' ),
				'course_reports_email_nonce'              => wp_create_nonce( 'ir_send_course_email_notifications' ),
				'use_certificate_builder'                 => wp_create_nonce( 'use_certificate_builder' ),
				'is_learndash_certificate_builder_active' => $is_learndash_certificate_builder_active,
				'add_instructor_nonce'                    => wp_create_nonce( 'add_instructor_nonce' ),
				'ir_bulk_update_commission_log'           => wp_create_nonce( 'ir_bulk_update_commission_log' ),
				'apex_charts_locale'                      => Translation::get_apex_charts_locale(),
			];
			wp_enqueue_script(
				'ir-dashboard-settings-script',
				plugins_url( '/js/settings/ir-dashboard-settings.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/js/settings/ir-dashboard-settings.js' ),
				1
			);

			wp_localize_script(
				'ir-dashboard-settings-script',
				'ir_fd_loc',
				$localization_data
			);
			$ir_admin_settings       = get_option( '_wdmir_admin_settings', [] );
			$onboarding_step         = ir_filter_input( 'onboarding', INPUT_GET, 'string' );
			$current_step            = ir_get_settings( 'create_dashboard_onboarding' );
			$wdm_id_ir_dash_pri_menu = isset( $ir_admin_settings['wdm_id_ir_dash_pri_menu'] ) ? $ir_admin_settings['wdm_id_ir_dash_pri_menu'] : '';

			ir_set_settings( 'create_dashboard_onboarding_dismissed', 0 );

			if ( false !== $onboarding_step && false === $current_step ) {
				ir_set_settings( 'create_dashboard_onboarding', 'step_1' );
			} elseif ( 'step_3' === $onboarding_step ) {
				ir_set_settings( 'create_dashboard_onboarding', 'step_3' );
			}

			// To get user Ids of instructors.
			$args        = [
				'fields' => [ 'ID', 'display_name', 'user_email' ],
				'role'   => 'wdm_instructor',
			];
			$instructors = get_users( $args );

			wp_enqueue_script(
				'ir-backend-dashboard-settings-tabs-script',
				plugins_url( '/js/settings/ir-backend-dashboard-settings.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/js/settings/ir-backend-dashboard-settings.js' ),
				1
			);

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/settings/ir-dashboard-settings.template.php',
				[
					'instructors'                          => $instructors,
					'ir_enable_frontend_instructor_dashboard' => 'on',
					'wdm_id_ir_dash_pri_menu'              => $wdm_id_ir_dash_pri_menu,
					'wdm_login_redirect'                   => ir_get_settings( 'wdm_login_redirect' ),
					// Dashboard Page.
					'dashboard_page_id'                    => get_option( 'ir_frontend_dashboard_page', false ),
					'ir_enable_frontend_dashboard'         => ir_get_settings( 'ir_enable_frontend_dashboard' ),
					'ir_disable_backend_dashboard'         => ir_get_settings( 'ir_disable_backend_dashboard' ),
					'create_frontend_dashboard_link'       => add_query_arg(
						[
							'action' => 'ir_create_new_dashboard_page',
							'nonce'  => wp_create_nonce( 'ir-create-dashboard-page' ),
						],
						admin_url( 'admin-ajax.php' )
					),

					'ir_frontend_overview_block'           => ir_get_settings( 'ir_frontend_overview_block' ),
					'ir_frontend_courses_block'            => ir_get_settings( 'ir_frontend_courses_block' ),
					'ir_frontend_quizzes_block'            => ir_get_settings( 'ir_frontend_quizzes_block' ),
					'ir_frontend_settings_block'           => ir_get_settings( 'ir_frontend_settings_block' ),
					'ir_frontend_products_block'           => ir_get_settings( 'ir_frontend_products_block' ),
					'ir_frontend_commissions_block'        => ir_get_settings( 'ir_frontend_commissions_block' ),
					'ir_frontend_assignments_block'        => ir_get_settings( 'ir_frontend_assignments_block' ),
					'ir_frontend_essays_block'             => ir_get_settings( 'ir_frontend_essays_block' ),
					'ir_frontend_quiz_attempts_block'      => ir_get_settings( 'ir_frontend_quiz_attempts_block' ),
					'ir_frontend_comments_block'           => ir_get_settings( 'ir_frontend_comments_block' ),
					'ir_frontend_course_reports_block'     => ir_get_settings( 'ir_frontend_course_reports_block' ),
					'ir_frontend_groups_block'             => ir_get_settings( 'ir_frontend_groups_block' ),
					// Overview Settings.
					'ir_frontend_overview_course_tile_block' => ir_get_settings( 'ir_frontend_overview_course_tile_block' ),
					'ir_frontend_overview_student_tile_block' => ir_get_settings( 'ir_frontend_overview_student_tile_block' ),
					'ir_frontend_overview_submissions_tile_block' => ir_get_settings( 'ir_frontend_overview_submissions_tile_block' ),
					'ir_frontend_overview_quiz_attempts_tile_block' => ir_get_settings( 'ir_frontend_overview_quiz_attempts_tile_block' ),
					'ir_frontend_overview_course_progress_block' => ir_get_settings( 'ir_frontend_overview_course_progress_block' ),
					'ir_frontend_overview_top_courses_block' => ir_get_settings( 'ir_frontend_overview_top_courses_block' ),
					'ir_frontend_overview_earnings_block'  => ir_get_settings( 'ir_frontend_overview_earnings_block' ),
					'ir_frontend_overview_submissions_block' => ir_get_settings( 'ir_frontend_overview_submissions_block' ),
					'ir_frontend_overview_empty_message'   => ir_get_settings( 'ir_frontend_overview_empty_message' ),
					'ir_is_gutenberg_enabled'              => ir_is_gutenberg_enabled(),
					// Appearance Settings ( Gutenberg Disabled ).
					'ir_frontend_appearance_color_scheme'  => ir_get_settings( 'ir_frontend_appearance_color_scheme' ),
					'ir_frontend_appearance_custom_primary' => ir_get_settings( 'ir_frontend_appearance_custom_primary' ),
					'ir_frontend_appearance_custom_accent' => ir_get_settings( 'ir_frontend_appearance_custom_accent' ),
					'ir_frontend_appearance_custom_background' => ir_get_settings( 'ir_frontend_appearance_custom_background' ),
					'ir_frontend_appearance_custom_headings' => ir_get_settings( 'ir_frontend_appearance_custom_headings' ),
					'ir_frontend_appearance_custom_text'   => ir_get_settings( 'ir_frontend_appearance_custom_text' ),
					'ir_frontend_appearance_custom_border' => ir_get_settings( 'ir_frontend_appearance_custom_border' ),
					'ir_frontend_appearance_custom_side_bg' => ir_get_settings( 'ir_frontend_appearance_custom_side_bg' ),
					'ir_frontend_appearance_custom_side_mt' => ir_get_settings( 'ir_frontend_appearance_custom_side_mt' ),
					'ir_frontend_appearance_custom_text_light' => ir_get_settings( 'ir_frontend_appearance_custom_text_light' ),
					'ir_frontend_appearance_custom_text_ex_light' => ir_get_settings( 'ir_frontend_appearance_custom_text_ex_light' ),
					'ir_frontend_appearance_custom_text_primary_btn' => ir_get_settings( 'ir_frontend_appearance_custom_text_primary_btn' ),
					'fonts'                                => [
						''           => 'Theme (Default)',
						'Open Sans'  => 'Open Sans',
						'Roboto'     => 'Roboto',
						'Montserrat' => 'Montserrat',
						'Lato'       => 'Lato',
						'Poppins'    => 'Poppins',
						'Inter'      => 'Inter',
					],
					'ir_frontend_appearance_font_family'   => ir_get_settings( 'ir_frontend_appearance_font_family' ),
					'ir_frontend_appearance_font_size'     => ir_get_settings( 'ir_frontend_appearance_font_size' ),
					// Onboarding step.
					'create_dashboard_status'              => ir_get_settings( 'create_dashboard_onboarding' ),
					'onboarding'                           => $onboarding_step,
					'ir_frontend_dashboard_page'           => get_option( 'ir_frontend_dashboard_page' ),
					'ir_frontend_dashboard_launched'       => get_option( 'ir_frontend_dashboard_launched' ),
					// Backend Dashboard Settings.
					'instance'                             => $this,
					'banner_img'                           => plugins_url( '/images/frontend-db-intro.png', __DIR__ ),
				]
			);
		}

		/**
		 * [Displaying table for allocating instructor commission percentage].
		 *
		 * @return [html] [footable table for updating commission]
		 *
		 * @since 2.4.0
		 */
		public function wdm_instructor_first_tab() {
			$onboarding_step = ir_filter_input( 'onboarding', INPUT_GET, 'string' );
			if ( current_user_can( 'manage_options' ) ) {
				$is_admin = true;
			} else {
				$is_admin = false;
			}

			$is_learndash_certificate_builder_active = false;

			if ( class_exists( 'LearnDash_Certificate_Builder\Bootstrap' ) ) {
				$is_learndash_certificate_builder_active = true;
			}

			$user_id           = get_current_user_id();
			$nonce             = wp_create_nonce( 'ir-update-pass-' . $user_id );
			$localization_data = [
				'ajax_url'                                => admin_url( 'admin-ajax.php' ),
				'logout_sessions_nonce'                   => wp_create_nonce( 'update-user_' . $user_id ),
				'user_id'                                 => $user_id,
				'update_pass_nonce'                       => $nonce,
				'create_new_course_nonce'                 => wp_create_nonce( 'ir-create-new-course' ),
				'create_new_quiz_nonce'                   => wp_create_nonce( 'ir-create-new-quiz' ),
				'export_order_details_nonce'              => wp_create_nonce( 'ir-export-order-details' ),
				'export_manual_commission_log_nonce'      => wp_create_nonce( 'ir-export-manual-commission-log' ),
				'update_commission_log_nonce'             => wp_create_nonce( 'ir_update_commission_log' ),
				'delete_manual_commission_log_nonce'      => wp_create_nonce( 'ir_commission_log_actions' ),
				'ir_commission_paypal_payout_nonce'       => wp_create_nonce( 'ir_commission_paypal_payout_payment' ),
				'replyto-comment'                         => wp_create_nonce( 'replyto-comment' ),
				'unfiltered-html-comment'                 => wp_create_nonce( 'unfiltered-html-comment' ),
				'course_label'                            => \LearnDash_Custom_Label::get_label( 'course' ),
				'group_label'                             => \LearnDash_Custom_Label::get_label( 'group' ),
				'groups_label'                            => \LearnDash_Custom_Label::get_label( 'groups' ),
				'lesson_label'                            => \LearnDash_Custom_Label::get_label( 'lesson' ),
				'topic_label'                             => \LearnDash_Custom_Label::get_label( 'topic' ),
				'quiz_label'                              => \LearnDash_Custom_Label::get_label( 'quiz' ),
				'question_label'                          => \LearnDash_Custom_Label::get_label( 'question' ),
				'courses_label'                           => \LearnDash_Custom_Label::get_label( 'courses' ),
				'lessons_label'                           => \LearnDash_Custom_Label::get_label( 'lessons' ),
				'topics_label'                            => \LearnDash_Custom_Label::get_label( 'topics' ),
				'quizzes_label'                           => \LearnDash_Custom_Label::get_label( 'quizzes' ),
				'questions_label'                         => \LearnDash_Custom_Label::get_label( 'questions' ),
				'lower_course_label'                      => \LearnDash_Custom_Label::label_to_lower( 'course' ),
				'lower_courses_label'                     => \LearnDash_Custom_Label::label_to_lower( 'courses' ),
				'lower_lesson_label'                      => \LearnDash_Custom_Label::label_to_lower( 'lesson' ),
				'lower_topic_label'                       => \LearnDash_Custom_Label::label_to_lower( 'topic' ),
				'lower_quiz_label'                        => \LearnDash_Custom_Label::label_to_lower( 'quiz' ),
				'lower_quizzes_label'                     => \LearnDash_Custom_Label::label_to_lower( 'quizzes' ),
				'lower_group_label'                       => \LearnDash_Custom_Label::label_to_lower( 'group' ),
				'lower_questions_label'                   => \LearnDash_Custom_Label::label_to_lower( 'questions' ),
				'create_new_course_url'                   => add_query_arg(
					[
						'action' => 'ir_fcb_new_course',
					],
					admin_url( 'admin-ajax.php' ),
				),
				'is_fcc_enabled'                          => ir_get_settings( 'ir_enable_frontend_dashboard' ),
				'empty_overview_msg'                      => ir_get_settings( 'ir_frontend_overview_empty_message' ),
				'is_admin'                                => $is_admin,
				'product_review_enabled'                  => defined( 'WDMIR_REVIEW_PRODUCT' ) ? WDMIR_REVIEW_PRODUCT : false,
				'is_shared_steps'                         => learndash_is_course_shared_steps_enabled(),
				'ld_currency'                             => learndash_get_currency_symbol(),
				'woo_currency'                            => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
				'currency_symbol'                         => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : learndash_get_currency_symbol(),
				'woo_activated'                           => ( class_exists( 'WooCommerce' ) && class_exists( 'Learndash_WooCommerce' ) ) ? true : false,
				'is_shared_steps_questions'               => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Builder', 'shared_questions' ) : '',
				'assignments_comments_enabled'            => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'comment_status' ) : false,
				'assignments_comments_queryable'          => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'publicly_queryable' ) : false,
				'threadComments'                          => get_option( 'thread_comments_depth' ),
				'course_reports_email_nonce'              => wp_create_nonce( 'ir_send_course_email_notifications' ),
				'use_certificate_builder'                 => wp_create_nonce( 'use_certificate_builder' ),
				'is_learndash_certificate_builder_active' => $is_learndash_certificate_builder_active,
				'add_instructor_nonce'                    => wp_create_nonce( 'add_instructor_nonce' ),
				'ir_bulk_update_commission_log'           => wp_create_nonce( 'ir_bulk_update_commission_log' ),
				'is_commissions_enabled'                  => ir_get_settings( 'instructor_commission' ) ? true : false,
				'apex_charts_locale'                      => Translation::get_apex_charts_locale(),
			];
			wp_localize_script(
				'instructor-role-wisdm-manage-instructor-view-script',
				'ir_fd_loc',
				$localization_data
			);
			wp_set_script_translations( 'instructor-role-wisdm-manage-instructor-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_enqueue_script(
				'wdm_instructor_report_js',
				plugins_url( 'js/commission_report.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/commission_report.js' ),
				true
			);

			wp_enqueue_script(
				'wdm_commission_js',
				plugins_url( 'js/commission.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/commission.js' ),
				true
			);
			$data = [
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'invalid_percentage' => __( 'Invalid percentage', 'wdm_instructor_role' ),
				'csv_button_text'    => __( 'Create CSV', 'wdm_instructor_role' ),
			];
			wp_localize_script( 'wdm_commission_js', 'wdm_commission_data', $data );

			wp_enqueue_style(
				'ir-datatable-styles',
				plugins_url( 'css/datatables.min.css', __DIR__ ),
				[],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/datatables.min.css' ),
			);

			wp_enqueue_script(
				'ir-datatables-script',
				plugins_url( 'js/datatables.min.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/datatables.min.js' ),
				true
			);

			// To get user Ids of instructors.
			$args        = [
				'fields' => [ 'ID', 'display_name', 'user_email' ],
				'role'   => 'wdm_instructor',
			];
			$instructors = get_users( $args );

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/settings/ir-instructor-commission-settings.template.php',
				[
					'instructors' => $instructors,
					'onboarding'  => $onboarding_step,
				]
			);
		}

		/**
		 * [Commission report page].
		 *
		 * @return [html] [to show select tag of instructor]
		 *
		 * @since 2.4.0
		 */
		public function wdm_instructor_second_tab() {
			$onboarding_step = ir_filter_input( 'onboarding', INPUT_GET, 'string' );

			if ( 'step_1' === $onboarding_step ) {
				ir_set_settings( 'commission_onboarding', 'step_1' );
			}

			if ( current_user_can( 'manage_options' ) ) {
				$is_admin = true;
			} else {
				$is_admin = false;
			}

			$is_learndash_certificate_builder_active = false;

			if ( class_exists( 'LearnDash_Certificate_Builder\Bootstrap' ) ) {
				$is_learndash_certificate_builder_active = true;
			}

			$user_id           = get_current_user_id();
			$nonce             = wp_create_nonce( 'ir-update-pass-' . $user_id );
			$localization_data = [
				'ajax_url'                                => admin_url( 'admin-ajax.php' ),
				'logout_sessions_nonce'                   => wp_create_nonce( 'update-user_' . $user_id ),
				'user_id'                                 => $user_id,
				'update_pass_nonce'                       => $nonce,
				'create_new_course_nonce'                 => wp_create_nonce( 'ir-create-new-course' ),
				'create_new_quiz_nonce'                   => wp_create_nonce( 'ir-create-new-quiz' ),
				'export_order_details_nonce'              => wp_create_nonce( 'ir-export-order-details' ),
				'export_manual_commission_log_nonce'      => wp_create_nonce( 'ir-export-manual-commission-log' ),
				'update_commission_log_nonce'             => wp_create_nonce( 'ir_update_commission_log' ),
				'delete_manual_commission_log_nonce'      => wp_create_nonce( 'ir_commission_log_actions' ),
				'ir_commission_paypal_payout_nonce'       => wp_create_nonce( 'ir_commission_paypal_payout_payment' ),
				'replyto-comment'                         => wp_create_nonce( 'replyto-comment' ),
				'unfiltered-html-comment'                 => wp_create_nonce( 'unfiltered-html-comment' ),
				'course_label'                            => \LearnDash_Custom_Label::get_label( 'course' ),
				'group_label'                             => \LearnDash_Custom_Label::get_label( 'group' ),
				'groups_label'                            => \LearnDash_Custom_Label::get_label( 'groups' ),
				'lesson_label'                            => \LearnDash_Custom_Label::get_label( 'lesson' ),
				'topic_label'                             => \LearnDash_Custom_Label::get_label( 'topic' ),
				'quiz_label'                              => \LearnDash_Custom_Label::get_label( 'quiz' ),
				'question_label'                          => \LearnDash_Custom_Label::get_label( 'question' ),
				'courses_label'                           => \LearnDash_Custom_Label::get_label( 'courses' ),
				'lessons_label'                           => \LearnDash_Custom_Label::get_label( 'lessons' ),
				'topics_label'                            => \LearnDash_Custom_Label::get_label( 'topics' ),
				'quizzes_label'                           => \LearnDash_Custom_Label::get_label( 'quizzes' ),
				'questions_label'                         => \LearnDash_Custom_Label::get_label( 'questions' ),
				'lower_course_label'                      => \LearnDash_Custom_Label::label_to_lower( 'course' ),
				'lower_courses_label'                     => \LearnDash_Custom_Label::label_to_lower( 'courses' ),
				'lower_lesson_label'                      => \LearnDash_Custom_Label::label_to_lower( 'lesson' ),
				'lower_topic_label'                       => \LearnDash_Custom_Label::label_to_lower( 'topic' ),
				'lower_quiz_label'                        => \LearnDash_Custom_Label::label_to_lower( 'quiz' ),
				'lower_quizzes_label'                     => \LearnDash_Custom_Label::label_to_lower( 'quizzes' ),
				'lower_group_label'                       => \LearnDash_Custom_Label::label_to_lower( 'group' ),
				'lower_questions_label'                   => \LearnDash_Custom_Label::label_to_lower( 'questions' ),
				'create_new_course_url'                   => add_query_arg(
					[
						'action' => 'ir_fcb_new_course',
					],
					admin_url( 'admin-ajax.php' ),
				),
				'is_fcc_enabled'                          => ir_get_settings( 'ir_enable_frontend_dashboard' ),
				'empty_overview_msg'                      => ir_get_settings( 'ir_frontend_overview_empty_message' ),
				'is_admin'                                => $is_admin,
				'product_review_enabled'                  => defined( 'WDMIR_REVIEW_PRODUCT' ) ? WDMIR_REVIEW_PRODUCT : false,
				'is_shared_steps'                         => learndash_is_course_shared_steps_enabled(),
				'ld_currency'                             => learndash_get_currency_symbol(),
				'woo_currency'                            => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
				'currency_symbol'                         => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : learndash_get_currency_symbol(),
				'woo_activated'                           => ( class_exists( 'WooCommerce' ) && class_exists( 'Learndash_WooCommerce' ) ) ? true : false,
				'is_shared_steps_questions'               => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Builder', 'shared_questions' ) : '',
				'assignments_comments_enabled'            => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'comment_status' ) : false,
				'assignments_comments_queryable'          => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'publicly_queryable' ) : false,
				'threadComments'                          => get_option( 'thread_comments_depth' ),
				'course_reports_email_nonce'              => wp_create_nonce( 'ir_send_course_email_notifications' ),
				'use_certificate_builder'                 => wp_create_nonce( 'use_certificate_builder' ),
				'is_learndash_certificate_builder_active' => $is_learndash_certificate_builder_active,
				'ir-bulk-update-commission-log'           => wp_create_nonce( 'ir-bulk-update-commission-log' ),
				'apex_charts_locale'                      => Translation::get_apex_charts_locale(),
			];
			wp_localize_script(
				'instructor-role-wisdm-instructor-commissions-view-script',
				'ir_fd_loc',
				$localization_data
			);
			wp_set_script_translations( 'instructor-role-wisdm-instructor-commissions-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			if ( ! is_super_admin() ) {
				$instructor_id = get_current_user_id();
			} else {
				$args          = [
					'fields' => [ 'ID', 'display_name' ],
					'role'   => 'wdm_instructor',
				];
				$instructors   = get_users( $args );
				$instructor_id = '';
				if ( isset( $_REQUEST['wdm_instructor_id'] ) ) {
					$instructor_id = $_REQUEST['wdm_instructor_id'];
				}
				if ( empty( $instructors ) ) {
					echo '<div class="ir-instructor-settings-tab-content"><h1 class="ir-tel">' . __( 'No instructor found', 'wdm_instructor_role' ) . '</h1></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later.

					return;
				}
				?>
				<div class="ir-instructor-settings-tab-content">
					<?php if ( false !== $onboarding_step ) : ?>
						<div class="ir-onboarding-container">
							<h3><?php esc_html_e( 'Commissions', 'wdm_instructor_role' ); ?></h3>
							<span><?php esc_html_e( 'Enter a uniform commission rate for all, or select Edit Commission to assign varying commission percentages', 'wdm_instructor_role' ); ?></span>
							<a>
							</a>
						</div>
					<?php endif; ?>
					<div class="ir-flex justify-apart align-center">
						<div class="ir-heading-wrap">
							<div class="ir-tab-heading"><?php echo __( 'Commissions', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
						</div>
					</div>
					<div class="ir-heading-desc"></div>
					<div class="ir-commissions-reports">
						<div class="ir-flex justify-apart align-center">
							<div class="ir-heading-wrap">
								<div class="ir-tab-subheading"><?php echo __( 'Allow Commission', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
							</div>
							<label for="instructor_commission" class="ir-switch ir-ajax">
								<input type="checkbox" name="instructor_commission" id="instructor_commission" <?php checked( ir_get_settings( 'instructor_commission' ) ); ?>/>
								<span class="ir-slider round"></span>
							</label>
						</div>
						<div class="ir-subheading-desc"><?php echo __( 'Enabling commissions allows you to assign different commission rates to various instructors.', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
						<div class="allow-commission-section <?php echo ( ! ir_get_settings( 'instructor_commission' ) ) ? 'ir-hide' : ''; ?>">
							<div class="ir-flex align-center">
								<span class="allow-comm-label"><?php echo __( 'Change all instructors commission to', 'wdm_instructor_role' ); ?></span> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
								<div class="ind-commission">
									<input id="ir-bulk-commission" class="add-commission" type="add-commission" min="0" max="100" type="number" placeholder="<?php echo __( 'Enter commission', 'wdm_instructor_role' ); ?>"> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
									<span>%</span>
								</div>
							</div>
							<div class="ir-or">
								<span><?php echo __( 'OR', 'wdm_instructor_role' ); ?></span> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
							</div>
							<div class="ir-flex align-center">
								<div>
									<span class="allow-comm-label"><?php echo __( 'Change instructors commission manually', 'wdm_instructor_role' ); ?></span> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
									<div class="ir-help-txt">
										<?php echo __( 'It will redirect you to manage instructor page where you can edit commission of any instructor', 'wdm_instructor_role' ); ?> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
									</div>
								</div>
								<div class="edit-commission">
								<a href="<?php echo ( false !== $onboarding_step ) ? admin_url( 'admin.php?page=instuctor&tab=instructor&onboarding=commission_step' ) : admin_url( 'admin.php?page=instuctor&tab=instructor' ); ?>">
									<button class="ir-btn-outline">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit-circle" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2067FA" fill="none" stroke-linecap="round" stroke-linejoin="round">
											<path stroke="none" d="M0 0h24v24H0z" fill="none"/>
											<path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3z" />
											<path d="M16 5l3 3" />
											<path d="M9 7.07a7 7 0 0 0 1 13.93a7 7 0 0 0 6.929 -6" />
										</svg>
										<?php echo __( 'Edit commission', 'wdm_instructor_role' ); ?> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
									</button>
								</a>
								</div>
							</div>
						</div>
						<p style="text-align: <?php echo ( is_rtl() ) ? 'left' : 'right'; ?>" class="allow-commission-save <?php echo ( ! ir_get_settings( 'instructor_commission' ) ) ? 'ir-hide' : ''; ?>">
							<button class="ir-primary-btn ir-bulk-commission">
									Save Settings
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-loader-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3a9 9 0 1 0 9 9" /></svg>
									<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
								</button>
							</p>
					</div>
					<div class="ir-commissions-reports">
						<?php
							echo do_blocks(
								'<!-- wp:instructor-role/wisdm-instructor-commissions -->
								<div class="wp-block-instructor-role-wisdm-instructor-commissions"><div class="wisdm-instructor-commissions"></div></div>
								<!-- /wp:instructor-role/wisdm-instructor-commissions -->'
							);
						?>
					</div>
				</div>
				<?php
			}
			if ( '' != $instructor_id ) {
				$this->wdm_commission_report( $instructor_id );
			}
		}

		/**
		 * [Export tab for instructor and admin].
		 *
		 * @return [html] [instructor_third_tab]
		 *
		 * @since 2.4.0
		 */
		public function wdm_instructor_third_tab() {
			if ( ! is_super_admin() ) {
				$instructor_id = get_current_user_id();
			} else {
				$args        = [
					'fields' => [ 'ID', 'display_name' ],
					'role'   => 'wdm_instructor',
				];
				$instructors = get_users( $args );

				$instructor_id = '';
				if ( isset( $_REQUEST['wdm_instructor_id'] ) ) {
					if ( '-1' == $_REQUEST['wdm_instructor_id'] ) {
						$instructor_id = '-1';
					} else {
						$instructor_id = $_REQUEST['wdm_instructor_id'];
					}
				}
				if ( empty( $instructors ) ) {
					echo __( 'No instructor found', 'wdm_instructor_role' );

					return;
				}
			}
			wp_enqueue_script( 'wdm_instructor_report_js', plugins_url( 'js/commission_report.js', __DIR__ ), [ 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ] );
			$url = plugins_url( 'css/jquery-ui.css', __DIR__ );
			wp_enqueue_style( 'wdm-date-css', $url );
			wp_enqueue_script( 'wdm-datepicker-js', plugins_url( 'js/wdm_datepicker.js', __DIR__ ), [ 'jquery' ] );
			$start_date = isset( $_POST['wdm_start_date'] ) ? $_POST['wdm_start_date'] : '';
			$end_date   = isset( $_POST['wdm_end_date'] ) ? $_POST['wdm_end_date'] : '';
			?>
				<div class="wrap wdmir-email-wrap">
					<h2><?php esc_html_e( 'Export Reports', 'wdm_instructor_role' ); ?></h2>
				</div>
				<form method="post" action="?page=instuctor&tab=export">
					<table>
						<tbody class="ir-flex ir-export">
						<?php if ( is_super_admin() ) : ?>
							<tr>
								<th style="float:left;">
									<?php esc_html_e( 'Select Instructor:', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<select name="wdm_instructor_id">
										<option value="-1"><?php esc_html_e( 'All', 'wdm_instructor_role' ); ?></option>
										<?php foreach ( $instructors as $instructor ) : ?>
											<option
												value="<?php echo $instructor->ID; ?>"
												<?php echo ( $instructor_id == $instructor->ID ) ? 'selected' : ''; ?>
											>
												<?php echo $instructor->display_name; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						<?php endif; ?>
						<tr class="ir-flex">
							<th class="ir-label" style="float:left;"><?php esc_html_e( 'Start Date:', 'wdm_instructor_role' ); ?></th>
							<td class="irb-calender">
								<input
									type="text"
									name="wdm_start_date"
									id="wdm_start_date" value="<?php echo esc_attr( $start_date ); ?>"
									placeholder="<?php esc_html_e( 'select a date', 'wdm_instructor_role' ); ?>" readonly
								/>
							</td>
						</tr>
						<tr class="ir-flex ir-end-date">
							<th class="ir-label" style="float:left;"><?php esc_html_e( 'End Date:', 'wdm_instructor_role' ); ?></th>
							<td class="irb-calender">
								<input
									type="text"
									name="wdm_end_date"
									id="wdm_end_date" value="<?php echo esc_attr( $end_date ); ?>" placeholder="<?php esc_html_e( 'select a date', 'wdm_instructor_role' ); ?>"
									readonly
								/>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input
									type="submit"
									class="button-primary irb-btn"
									value="<?php esc_html_e( 'Submit', 'wdm_instructor_role' ); ?>"
									id="wdm_submit"
								/>
							</td>
						</tr>
						</tbody>
					</table>
				</form>
				<div class="ir-export-reports-container">
					<?php
					if ( '' != $instructor_id ) {
						$this->wdm_export_csv_report( $instructor_id, $start_date, $end_date );
					}
					?>
				</div>
			<?php
		}

		/**
		 * [Report filtered by instructor, start and end date].
		 *
		 * @param [int]    $instructor_id [description]
		 * @param [string] $start_date    [start_date]
		 * @param [string] $end_date      [end_date]
		 *
		 * @return [html] [report in table format]
		 *
		 * @since 2.4.0
		 */
		public function wdm_export_csv_report( $instructor_id, $start_date, $end_date ) {
			global $wpdb;

			if ( file_exists( LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php' ) ) {
				$url = admin_url( 'admin.php?page=instuctor&tab=export&wdm_export_report=wdm_export_report&wdm_instructor_id=' . $instructor_id . '&start_date=' . $start_date . '&end_date=' . $end_date );
				?>
				<a href="
				<?php
				echo $url;
				?>
			" class="button-primary irb-btn" style="float:right">
				<?php
				echo __( 'Export CSV', 'wdm_instructor_role' );
				?>
			</a>
				<?php
			}

			?>
					<!--Table shows Name, Email, etc-->
					<br><br>
					<table class="DataTable" data-filter="#filter" data-page-navigation=".pagination" id="wdm_report_tbl" data-page-size="5" >
						<thead>
							<tr>
								<th data-sort-initial="descending" data-class="expand">
									<?php
									echo __( 'Order ID', 'wdm_instructor_role' );
									?>
								</th>
								<th data-sort-initial="descending" data-class="expand">
									<?php

									echo $this->showOwnerOrPurchaser();
									?>
								</th>
								<th data-sort-initial="descending" data-class="expand">
									<?php
									echo __( 'Product / Course Name', 'wdm_instructor_role' );
									?>
								</th>
								<th>
									<?php
									echo __( 'Actual Price', 'wdm_instructor_role' );
									?>
								</th>
								<th>
				<?php
				echo __( 'Commission Price', 'wdm_instructor_role' );
				?>
								</th>

								<th>
									<?php
									echo __( 'Product Type', 'wdm_instructor_role' );
									?>
								</th>

							</tr>
							<?php
							do_action( 'wdm_commission_report_table_header', $instructor_id );
							?>
						</thead>
						<tbody>
							<?php
							$sql = "SELECT * FROM {$wpdb->prefix}wdm_instructor_commission WHERE 1=1 ";

							$this->wdmCreateSQLQuery( $instructor_id, $start_date, $end_date, $sql );

							$results = $wpdb->get_results( $sql );
							$hasData = false;
							if ( ! empty( $results ) ) {
								foreach ( $results as $value ) {
									if ( ! $this->userIdExists( $value->user_id ) ) {
										continue;
									}
									$hasData      = true;
									$user_details = get_user_by( 'id', $value->user_id );

									?>
									<tr>
										<td>
											<?php
											if ( is_super_admin() ) {
												?>
												<a href="
												<?php
												echo $this->wdmGetPostPermalink( $value->order_id, $value->product_type );
												?>
				" target="
												<?php
												echo $this->needToOpenNewDocument();
												?>
				">
												<?php
												echo $value->order_id;
												?>
				</a>

												<?php
											} else {
												echo $value->order_id;
											}
											?>
									</td>
									<td>
									<?php
									echo $this->wdmShowUserName( $value->order_id, $user_details->display_name, $value->product_type );
									?>
								</td>
									<td><a target="_new_blank"
									<?php
									echo $this->wdmGetPostEditLink( $value->product_id );
									?>
								>
									<?php
									echo $this->wdmGetPostTitle( $value->product_id );
									?>
					</a></td>
									<td>
									<?php
									echo $value->actual_price;
									?>
								</td>
									<td>
									<?php
									echo $value->commission_price;
									?>
								</td>
									<td>
									<?php
									echo $value->product_type;
									?>
								</td>

								</tr>
									<?php
								}
							} else {
								$hasData = true;
								?>
							<tr>
								<td colspan="6" class="ir-no-data-found">
								<?php
								echo __( 'No record found!', 'wdm_instructor_role' );
								?>
										</td>
										</tr>
												<?php
							}
							if ( ! $hasData ) {
								?>
							<tr>
								<td colspan="6" class="ir-no-data-found">
								<?php
								echo __( 'No record found!', 'wdm_instructor_role' );
								?>
										</td>
										</tr>
												<?php
							}
							do_action( 'wdm_commission_report_table', $instructor_id );
							?>
					</tbody>
					<tfoot >

						<tr>
							<td colspan="6" style="border-radius: 0 0 6px 6px;">
								<div class="pagination pagination-centered hide-if-no-paging"></div>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>
			<br>
			<?php
			if ( file_exists( LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php' ) ) {
				$url = admin_url( 'admin.php?page=instuctor&tab=export&wdm_export_report=wdm_export_report&wdm_instructor_id=' . $instructor_id . '&start_date=' . $start_date . '&end_date=' . $end_date );
				?>
				<a href="
				<?php
				echo $url;
				?>
				" class="button-primary irb-btn" style="float:right">
				<?php
				echo __( 'Export CSV', 'wdm_instructor_role' );
				?>
				</a>
				<?php
			}
		}

		public function wdmCreateSQLQuery( $instructor_id, $start_date, $end_date, &$sql ) {
			if ( '-1' != $instructor_id ) {
				$sql .= "AND user_id = $instructor_id ";
			}
			if ( '' != $start_date ) {
				$start_date = Date( 'Y-m-d', strtotime( $start_date ) );
				$sql       .= "AND transaction_time >='$start_date 00:00:00'";
			}
			if ( '' != $end_date ) {
				$end_date = Date( 'Y-m-d', strtotime( $end_date ) );
				$sql     .= " AND transaction_time <='$end_date 23:59:59'";
			}
		}

		/**
		 * [wdmShowUserName displaying owner/purchaser name according to product].
		 *
		 * @param [int]    $order_id     [order_id]
		 * @param [string] $display_name [display_name]
		 *
		 * @return [string] [owner/purchaser name]
		 */
		public function wdmShowUserName( $order_id, $display_name, $product_type ) {
			$product_type_array = [
				'WC' => '_customer_user',
				'LD' => 'LD', // v2.4.0
			];
			$product_type_array = apply_filters( 'wdm_product_type_array', $product_type_array );

			if ( is_super_admin() ) {
				return $display_name;
			}
			if ( ! isset( $product_type_array[ $product_type ] ) ) {
				$product_type_array['LD'] = 'LD';
			}

			if ( 'LD' == $product_type_array[ $product_type ] ) {
				$ownerID = get_post_field( 'post_author', $order_id );
			} else {
				$ownerID = get_post_meta( $order_id, $product_type_array[ $product_type ], true );
			}

			if ( empty( $ownerID ) ) {
				if ( false === get_post_status( $order_id ) ) {
					return __( 'Order has been deleted!', 'wdm_instructor_role' );
				}
			}
			$user_info = get_userdata( $ownerID );
			if ( $user_info ) {
				return $user_info->first_name . ' ' . $user_info->last_name;
			}
			return __( 'User not found!', 'wdm_instructor_role' );
		}

		/**
		 * [Export data filter wise].
		 *
		 * @return [file] [csv file]
		 *
		 * @since 2.4.0
		 */
		public function wdm_export_csv_date_filter() {
			if ( isset( $_GET['wdm_export_report'] ) && 'wdm_export_report' == $_GET['wdm_export_report'] ) {
				global $wpdb;
				$instructor_id = $_REQUEST['wdm_instructor_id'];
				$start_date    = $_GET['start_date'];
				$end_date      = $_GET['end_date'];
				$sql           = "SELECT * FROM {$wpdb->prefix}wdm_instructor_commission WHERE 1=1";
				if ( '' != $instructor_id && '-1' != $instructor_id ) {
					if ( $this->userIdExists( $instructor_id ) ) {
						$sql .= ' AND user_id=' . $instructor_id;
					}
				}
				if ( '' != $start_date ) {
					$start_date = Date( 'Y-m-d', strtotime( $start_date ) );
					$sql       .= " AND transaction_time >='$start_date 00:00:00'";
				}
				if ( '' != $end_date ) {
					$end_date = Date( 'Y-m-d', strtotime( $end_date ) );
					$sql     .= " AND transaction_time <='$end_date 23:59:59'";
				}

				$results = $wpdb->get_results( $sql );

				$course_progress_data = [];

				if ( empty( $results ) ) {
					$row = [ 'No data' => __( 'No data found', 'wdm_instructor_role' ) ];
				} else {
					foreach ( $results as $value ) {
						if ( ! $this->userIdExists( $value->user_id ) ) {
							continue;
						}
						$user_data = get_user_by( 'id', $value->user_id );
						$row       = [
							'Order id'         => $value->order_id,
							'' . $this->showOwnerOrPurchaserTR() => $this->wdmShowUserName( $value->order_id, $user_data->display_name, $value->product_type ),
							'Actual price'     => $value->actual_price,
							'Commission price' => $value->commission_price,
							'Product name'     => $this->wdmGetPostTitle( $value->product_id ),
							'Transaction time' => $value->transaction_time,
							'Product type'     => $value->product_type,
						];

						$course_progress_data[] = $row;
					}
				}

				if ( file_exists( LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php' ) ) {
					/**
					 * Include parseCSV to write csv file.
					 */
					require_once LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php';

					$csv                  = new \lmsParseCSV();
					$csv->file            = 'commission_report.csv';
					$csv->output_filename = 'commission_report.csv';
					/**
					 * Filters csv object.
					 *
					 * @since 4.0
					 *
					 * @param \lmsParseCSV $csv CSV object.
					 * @param string       $context The context of the csv object.
					 */
					$csv = apply_filters( 'ir_filter_csv_object', $csv, 'ir_commission_reports' );

					/**
					 * Filters the content will print onto the exported CSV
					 *
					 * @since 4.0
					 *
					 * @param void|array|mixed $content CSV content.
					 */
					$course_progress_data = apply_filters( 'ir_filter_course_export_data', $course_progress_data );

					if ( empty( $course_progress_data ) ) {
						$row   = [];
						$row[] = [ '' => __( 'No data found', 'wdm_instructor_role' ) ];
						$csv->output( 'commission_report.csv', $row, array_keys( reset( $row ) ) );
					} else {
						$csv->output( 'commission_report.csv', $course_progress_data, array_keys( reset( $course_progress_data ) ) );
					}
					die();
				}
			}
		}

		/**
		 * Function to check post is set or not.
		 */
		public function wdmCheckIsSet( $post ) {
			if ( isset( $post ) ) {
				return $post;
			}

			return '';
		}

		/**
		 * Function to return site url to edit post, if current user is super admin.
		 *
		 * @param [string] $value [checking for admin]
		 *
		 * @return [string] [url]
		 *
		 * @since 2.4.0
		 */
		public function wdmGetPostPermalink( $value, $type = null ) {
			if ( is_super_admin() && 'EDD' == $type ) {
				return site_url( 'wp-admin/edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $value );
			} elseif ( is_super_admin() ) {
				return site_url( 'wp-admin/post.php?post=' . $value . '&action=edit' );
			}

			return '#';
		}

		/**
		 * Function returns string '_new_blank', if user is super admin.
		 *
		 * @return [string] [open in new blank tab if user is super admin.]
		 *
		 * @since 2.4.0
		 */
		public function needToOpenNewDocument() {
			if ( is_super_admin() ) {
				return '_new_blank';
			}

			return '';
		}

		/**
		 * [showOwnerOrPurchaser showing heading if admin then username of owner if instructor then purchaser].
		 *
		 * @return [string] [heading]
		 */
		public function showOwnerOrPurchaser() {
			if ( is_super_admin() ) {
				return __( 'Instructor name', 'wdm_instructor_role' );
			}

			return __( 'Purchaser name', 'wdm_instructor_role' );
		}

		public function showOwnerOrPurchaserTR() {
			if ( is_super_admin() ) {
				return 'Instructor name';
			}

			return 'Purchaser name';
		}

		/**
		 * [Export functionality for admin as well as instructor].
		 *
		 * @return [nothing]
		 *
		 * @since 2.4.0
		 */
		public function wdm_export_commission_report() {
			if ( isset( $_GET['wdm_commission_report'] ) && 'wdm_commission_report' == $_GET['wdm_commission_report'] ) {
				global $wpdb;
				$instructor_id = $_REQUEST['wdm_instructor_id'];
				$user_data     = get_user_by( 'id', $instructor_id );

				$sql     = "SELECT * FROM {$wpdb->prefix}wdm_instructor_commission WHERE user_id=$instructor_id";
				$results = $wpdb->get_results( $sql );

				$course_progress_data = [];
				$amount_paid          = 0;
				if ( empty( $results ) ) {
					$row = [ 'instructor name' => $user_data->display_name ];
				} else {
					foreach ( $results as $value ) {
						$row                    = [
							'order id'         => $value->order_id,
							'instructor name'  => $user_data->display_name,
							'actual price'     => $value->actual_price,
							'commission price' => $value->commission_price,
							'product name'     => $this->wdmGetPostTitle( $value->product_id ),
							'transaction time' => $value->transaction_time,
						];
						$amount_paid            = $amount_paid + $value->commission_price;
						$course_progress_data[] = $row;
					}
					$paid_total = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );
					if ( '' == $paid_total ) {
						$paid_total = 0;
					}
					$amount_paid            = round( ( $amount_paid - $paid_total ), 2 );
					$amount_paid            = max( $amount_paid, 0 );
					$row                    = [
						'order id'         => __( 'Paid Earnings', 'wdm_instructor_role' ),
						'instructor name'  => $paid_total,
						'actual price'     => '',
						'commission price' => '',
						'product name'     => '',
						'transaction time' => '',
					];
					$course_progress_data[] = $row;
					$row                    = [
						'order id'         => __( 'Unpaid Earnings', 'wdm_instructor_role' ),
						'instructor name'  => $amount_paid,
						'actual price'     => '',
						'commission price' => '',
						'product name'     => '',
						'transaction time' => '',
					];
					$course_progress_data[] = $row;
				}

				if ( file_exists( LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php' ) ) {
					require_once LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php';
					$csv = new \lmsParseCSV();

					$csv->output( true, 'commission_report.csv', $course_progress_data, array_keys( reset( $course_progress_data ) ) );

					die();
				}
			}
		}

		/**
		 * [Commission Report page].
		 *
		 * @param [int] $instructor_id [instructor_id]
		 *
		 * @return [html] [to show all the commission report]
		 *
		 * @since 2.4.0
		 */
		public function wdm_commission_report( $instructor_id ) {
			global $wpdb;
			wp_enqueue_script(
				'wdm_footable_pagination',
				plugins_url( 'js/footable.paginate.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/footable.paginate.js' ),
				true
			);
			wp_enqueue_script(
				'wdm_instructor_report_js',
				plugins_url( 'js/commission_report.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/commission_report.js' ),
				true
			);
			$data = [
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'enter_amount'           => __( 'Please Enter amount', 'wdm_instructor_role' ),
				'enter_amount_less_than' => __( 'Please enter amount less than amount to be paid', 'wdm_instructor_role' ),
				'added_successfully'     => __( 'Record added successfully', 'wdm_instructor_role' ),
				'csv_button_text'        => __( 'Create CSV', 'wdm_instructor_role' ),
			];
			wp_localize_script( 'wdm_instructor_report_js', 'wdm_commission_data', $data );

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/commission/ir-commission-logs.template.php',
				[
					'instructor_id' => $instructor_id,
					'course_label'  => class_exists( 'LearnDash_Custom_Label' ) ? \LearnDash_Custom_Label::get_label( 'course' ) : 'Course',
					'instance'      => $this,
				]
			);
		}

		public function wdmcheckProductType( $value ) {
			if ( is_super_admin() ) {
				if ( 'EDD' == $value->product_type ) {
					?>
						<a
							href="<?php echo is_super_admin() ? esc_attr( site_url( 'wp-admin/edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $value->order_id ) ) : '#'; ?>"
							target="<?php echo is_super_admin() ? '_new_blank' : ''; ?>">
							<?php echo esc_html( $value->order_id ); ?>
						</a>
					<?php
				} else {
					?>
					<a
						href="<?php echo is_super_admin() ? esc_attr( site_url( 'wp-admin/post.php?post=' . $value->order_id . '&action=edit' ) ) : '#'; ?>"
						target="<?php echo is_super_admin() ? '_new_blank' : ''; ?>">
							<?php echo esc_html( $value->order_id ); ?>
						</a>
					<?php
				}
			} else {
				echo esc_html( $value->order_id );
			}
		}

		/**
		 * [Updating instructor commission using ajax].
		 *
		 * @return [string] [status]
		 *
		 * @since 2.4.0
		 */
		public function wdm_update_commission() {
			$percentage    = $_POST['commission'];
			$instructor_id = $_POST['instructor_id'];
			if ( wdm_is_instructor( $instructor_id ) ) {
				update_user_meta(
					$instructor_id,
					'wdm_commission_percentage',
					Instructor_Role_Commission::normalize_commission_percentage( $percentage, $instructor_id )
				);
				echo __( 'Updated successfully', 'wdm_instructor_role' );
			} else {
				echo __( 'Oops something went wrong', 'wdm_instructor_role' );
			}
			die();
		}

		public function userIdExists( $user_id ) {
			global $wpdb;
			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users WHERE ID = %d", $user_id ) );

			return empty( $count ) || 1 > $count ? false : true;
		}

		public function wdmGetPostTitle( $postID ) {
			$title = get_the_title( $postID );
			if ( empty( $title ) ) {
				return sprintf(
					/* translators: Course label */
					__( 'Product/ %s has been deleted !', 'wdm_instructor_role' ),
					\LearnDash_Custom_Label::get_label( 'Course' )
				);
			}
			return $title;
		}

		/**
		 * Gets the edit link for a post.
		 *
		 * @since 3.5.0
		 *
		 * @param int $post_id The ID of the post.
		 *
		 * @return string The edit link.
		 */
		public function wdmGetPostEditLink( $post_id = 0 ) {
			if ( get_post_status( $post_id ) === false ) {
				return 'style="pointer-event:none;"';
			}

			return 'href="' . esc_url( site_url( 'wp-admin/post.php?post=' . $post_id . '&action=edit' ) ) . '"';
		}

		/**
		 * Display of HTML content on Instructor Email Settings page.
		 * This function is called from file "commission.php" in function instuctor_page_callback()
		 *
		 * Shortcuts used in naming variables and elements
		 * cra_ = course review admin
		 * cri_ = course review instructor
		 * pra_ = product review admin
		 * pri_ = product review instructor
		 * cp_  = course purchase to instructor
		 * qc_  = quiz completion to instructor
		 *
		 * @since: version 2.1
		 */
		public function wdmir_instructor_email_settings() {
			if ( current_user_can( 'manage_options' ) ) {
				$is_admin = true;
			} else {
				$is_admin = false;
			}

			$is_learndash_certificate_builder_active = false;

			if ( class_exists( 'LearnDash_Certificate_Builder\Bootstrap' ) ) {
				$is_learndash_certificate_builder_active = true;
			}

			$user_id           = get_current_user_id();
			$nonce             = wp_create_nonce( 'ir-update-pass-' . $user_id );
			$localization_data = [
				'ajax_url'                                => admin_url( 'admin-ajax.php' ),
				'logout_sessions_nonce'                   => wp_create_nonce( 'update-user_' . $user_id ),
				'user_id'                                 => $user_id,
				'update_pass_nonce'                       => $nonce,
				'create_new_course_nonce'                 => wp_create_nonce( 'ir-create-new-course' ),
				'create_new_quiz_nonce'                   => wp_create_nonce( 'ir-create-new-quiz' ),
				'export_order_details_nonce'              => wp_create_nonce( 'ir-export-order-details' ),
				'export_manual_commission_log_nonce'      => wp_create_nonce( 'ir-export-manual-commission-log' ),
				'update_commission_log_nonce'             => wp_create_nonce( 'ir_update_commission_log' ),
				'delete_manual_commission_log_nonce'      => wp_create_nonce( 'ir_commission_log_actions' ),
				'ir_commission_paypal_payout_nonce'       => wp_create_nonce( 'ir_commission_paypal_payout_payment' ),
				'replyto-comment'                         => wp_create_nonce( 'replyto-comment' ),
				'unfiltered-html-comment'                 => wp_create_nonce( 'unfiltered-html-comment' ),
				'course_label'                            => \LearnDash_Custom_Label::get_label( 'course' ),
				'group_label'                             => \LearnDash_Custom_Label::get_label( 'group' ),
				'groups_label'                            => \LearnDash_Custom_Label::get_label( 'groups' ),
				'lesson_label'                            => \LearnDash_Custom_Label::get_label( 'lesson' ),
				'topic_label'                             => \LearnDash_Custom_Label::get_label( 'topic' ),
				'quiz_label'                              => \LearnDash_Custom_Label::get_label( 'quiz' ),
				'question_label'                          => \LearnDash_Custom_Label::get_label( 'question' ),
				'courses_label'                           => \LearnDash_Custom_Label::get_label( 'courses' ),
				'lessons_label'                           => \LearnDash_Custom_Label::get_label( 'lessons' ),
				'topics_label'                            => \LearnDash_Custom_Label::get_label( 'topics' ),
				'quizzes_label'                           => \LearnDash_Custom_Label::get_label( 'quizzes' ),
				'questions_label'                         => \LearnDash_Custom_Label::get_label( 'questions' ),
				'lower_course_label'                      => \LearnDash_Custom_Label::label_to_lower( 'course' ),
				'lower_courses_label'                     => \LearnDash_Custom_Label::label_to_lower( 'courses' ),
				'lower_lesson_label'                      => \LearnDash_Custom_Label::label_to_lower( 'lesson' ),
				'lower_topic_label'                       => \LearnDash_Custom_Label::label_to_lower( 'topic' ),
				'lower_quiz_label'                        => \LearnDash_Custom_Label::label_to_lower( 'quiz' ),
				'lower_quizzes_label'                     => \LearnDash_Custom_Label::label_to_lower( 'quizzes' ),
				'lower_group_label'                       => \LearnDash_Custom_Label::label_to_lower( 'group' ),
				'lower_questions_label'                   => \LearnDash_Custom_Label::label_to_lower( 'questions' ),
				'create_new_course_url'                   => add_query_arg(
					[
						'action' => 'ir_fcb_new_course',
					],
					admin_url( 'admin-ajax.php' ),
				),
				'is_fcc_enabled'                          => ir_get_settings( 'ir_enable_frontend_dashboard' ),
				'empty_overview_msg'                      => ir_get_settings( 'ir_frontend_overview_empty_message' ),
				'is_admin'                                => $is_admin,
				'product_review_enabled'                  => defined( 'WDMIR_REVIEW_PRODUCT' ) ? WDMIR_REVIEW_PRODUCT : false,
				'is_shared_steps'                         => learndash_is_course_shared_steps_enabled(),
				'ld_currency'                             => learndash_get_currency_symbol(),
				'woo_currency'                            => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '',
				'currency_symbol'                         => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : learndash_get_currency_symbol(),
				'woo_activated'                           => ( class_exists( 'WooCommerce' ) && class_exists( 'Learndash_WooCommerce' ) ) ? true : false,
				'is_shared_steps_questions'               => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Quizzes_Builder', 'shared_questions' ) : '',
				'assignments_comments_enabled'            => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'comment_status' ) : false,
				'assignments_comments_queryable'          => class_exists( 'LearnDash_Settings_Section' ) ? \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Assignments_CPT', 'publicly_queryable' ) : false,
				'threadComments'                          => get_option( 'thread_comments_depth' ),
				'course_reports_email_nonce'              => wp_create_nonce( 'ir_send_course_email_notifications' ),
				'use_certificate_builder'                 => wp_create_nonce( 'use_certificate_builder' ),
				'is_learndash_certificate_builder_active' => $is_learndash_certificate_builder_active,
				'apex_charts_locale'                      => Translation::get_apex_charts_locale(),
			];
			wp_localize_script(
				'instructor-role-dashboard-settings-view-script',
				'ir_fd_loc',
				$localization_data
			);
			wp_set_script_translations( 'instructor-role-dashboard-settings-view-script', 'wdm_instructor_role', INSTRUCTOR_ROLE_ABSPATH . '/languages' );
			wp_enqueue_media();
			wp_enqueue_editor();
			$email_settings   = get_option( '_wdmir_email_settings' );
			$default_settings = [
				'cra_emails'       => '',
				'cra_subject'      => '',
				'cra_mail_content' => '',
				'cri_subject'      => '',
				'cri_mail_content' => '',
				'pra_emails'       => '',
				'pra_subject'      => '',
				'pra_mail_content' => '',
				'pri_subject'      => '',
				'pri_mail_content' => '',
				'dra_emails'       => '',
				'dra_subject'      => '',
				'dra_mail_content' => '',
				'dri_subject'      => '',
				'dri_mail_content' => '',
				'cp_subject'       => '',
				'cp_mail_content'  => '',
				'qc_subject'       => '',
				'qc_mail_content'  => '',
			];

			$email_settings = shortcode_atts( $default_settings, $email_settings );
			?>
			<div class="wrap wdmir-email-wrap ir-instructor-settings-tab-content">
					<div class="ir-flex justify-apart align-center">
						<div class="ir-heading-wrap">
							<div class="ir-tab-heading"><?php echo __( 'E-mail Settings', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
						</div>
					</div>
					<div class="ir-heading-desc"></div>
					<?php
						echo do_blocks(
							'<!-- wp:instructor-role/dashboard-settings -->
							<div class="wp-block-instructor-role-dashboard-settings"><div class="dashboard-settings" data-paypal="true"></div></div>
							<!-- /wp:instructor-role/dashboard-settings --></div>'
						);

					?>
			</div>
			<?php
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_style( 'wdmir_tabs_css', '//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css' );
			wp_enqueue_script( 'wdm_email_form', plugin_dir_url( __DIR__ ) . 'js/wdm_email_form.js', [ 'jquery' ], '0.0.1' );
		}

		public function wdmRemoveSlashs( $email_content ) {
			if ( ! empty( $email_content ) ) {
				return wp_unslash( $email_content );
			}

			return '';
		}

		/*
		*   @since version 2.1
		*   Saving HTML form content of Instructor Email Settings page.
		*
		*/
		public function wdmir_email_settings_save() {
			if ( isset( $_POST['ins_email_setting_nonce'] ) &&
									wp_verify_nonce( $_POST['ins_email_setting_nonce'], 'ins_email_setting_nonce_action' ) &&
									is_admin() ) {
				$email_settings = [];
				do_action( 'wdmir_email_settings_save_before' );

				// Course Review To Admin - starts.
				$email_settings['cra_emails'] = '';
				$email_settings['cra_emails'] = $this->checkIsSets( $_POST['cra_emails'] );

				$email_settings['cra_subject'] = $this->checkIsSets( $_POST['cra_subject'] );

				$email_settings['cra_mail_content'] = $this->checkIsSets( $_POST['cra_mail_content'], 1 );

				// Course Review To Instructor - starts.
				$email_settings['cri_subject'] = $this->checkIsSets( $_POST['cri_subject'] );

				$email_settings['cri_mail_content'] = '';
				if ( isset( $_POST['cri_mail_content'] ) ) {
					$email_settings['cri_mail_content'] = $this->checkIsSets( $_POST['cri_mail_content'], 1 );
				}

				// Course Review To Instructor - ends.

				// Product Review To Admin - starts.
				$email_settings['pra_emails'] = '';
				if ( isset( $_POST['pra_emails'] ) ) {
					$email_settings['pra_emails'] = $_POST['pra_emails'];
				}

				$email_settings['pra_subject'] = '';
				if ( isset( $_POST['pra_subject'] ) ) {
					$email_settings['pra_subject'] = $this->checkIsSets( $_POST['pra_subject'] );
				}

				$email_settings['pra_mail_content'] = '';
				if ( isset( $_POST['pra_mail_content'] ) ) {
					$email_settings['pra_mail_content'] = $this->checkIsSets( $_POST['pra_mail_content'], 1 );
				}
				// Product Review To Admin - ends.

				// Product Review To Instructor - starts.
				$email_settings['pri_subject'] = '';
				if ( isset( $_POST['pri_subject'] ) ) {
					$email_settings['pri_subject'] = $_POST['pri_subject'];
				}

				$email_settings['pri_mail_content'] = '';
				if ( isset( $_POST['pri_mail_content'] ) ) {
					$email_settings['pri_mail_content'] = $this->checkIsSets( $_POST['pri_mail_content'], 1 );
				}
				// Product Review To Instructor - ends.

				// Download Review To Admin - starts v3.0.0.
				$email_settings['dra_emails'] = '';
				if ( isset( $_POST['dra_emails'] ) ) {
					$email_settings['dra_emails'] = $_POST['dra_emails'];
				}

				$email_settings['dra_subject'] = '';
				if ( isset( $_POST['dra_subject'] ) ) {
					$email_settings['dra_subject'] = $_POST['dra_subject'];
				}

				$email_settings['dra_mail_content'] = '';
				if ( isset( $_POST['dra_mail_content'] ) ) {
					$email_settings['dra_mail_content'] = $this->checkIsSets( $_POST['dra_mail_content'], 1 );
				}
				// Download Review To Admin - ends.

				// Download Review To Instructor - starts.
				$email_settings['dri_subject'] = '';
				if ( isset( $_POST['dri_subject'] ) ) {
					$email_settings['dri_subject'] = $_POST['dri_subject'];
				}

				$email_settings['dri_mail_content'] = '';
				if ( isset( $_POST['dri_mail_content'] ) ) {
					$email_settings['dri_mail_content'] = $this->checkIsSets( $_POST['dri_mail_content'], 1 );
				}
				// Download Review To Instructor - ends.

				// Course Purchase Emails To Instructor - starts.
				$email_settings['cp_subject'] = '';
				if ( isset( $_POST['cp_subject'] ) ) {
					$email_settings['cp_subject'] = filter_input( INPUT_POST, 'cp_subject' );
				}

				$email_settings['cp_mail_content'] = '';
				if ( isset( $_POST['cp_mail_content'] ) ) {
					$email_settings['cp_mail_content'] = $this->checkIsSets( filter_input( INPUT_POST, 'cp_mail_content' ), 1 );
				}
				// Course Purchase Emails To Instructor - ends.

				// Quiz Completion Emails To Instructor - starts.
				$email_settings['qc_subject'] = '';
				if ( isset( $_POST['qc_subject'] ) ) {
					$email_settings['qc_subject'] = filter_input( INPUT_POST, 'qc_subject' );
				}

				$email_settings['qc_mail_content'] = '';
				if ( isset( $_POST['qc_mail_content'] ) ) {
					$email_settings['qc_mail_content'] = $this->checkIsSets( filter_input( INPUT_POST, 'qc_mail_content' ), 1 );
				}
				// Quiz Completion Emails To Instructor - ends.

				// Saving email settings option.
				update_option( '_wdmir_email_settings', $email_settings );

				do_action( 'wdmir_email_settings_save_after' );

				wp_redirect( $_POST['_wp_http_referer'] );
			}
		}

		public function checkIsSets( $value, $autop = false ) {
			if ( $autop ) {
				$value = wpautop( $value );
			}
			if ( isset( $value ) ) {
				return $value;
			}

			return '';
		}

		public function save_instructor_mail_template_data() {
			if ( ! is_user_logged_in() ) {
				return false;
			}

			$current_user_id = get_current_user_id();
			if ( isset( $_POST['instructor_email_update'] ) ) {
				$email_template_data = [];
				if ( isset( $_POST['instructor_email_sub'] ) ) {
					$email_template_data['mail_sub'] = $_POST['instructor_email_sub'];
				}
				if ( isset( $_POST['instructor_email_message'] ) ) {
					$email_template_data['mail_content'] = $_POST['instructor_email_message'];
				}

				update_user_meta( $current_user_id, 'instructor_email_template', $email_template_data );
			}
		}

		/**
		 * Send email to instructors on quiz completion
		 *
		 * @param array  $data
		 * @param object $user
		 */
		public function send_email_to_instructor( $data, $user ) {
			// Return if course id not set.
			if ( empty( $data ) || ! array_key_exists( 'quiz', $data ) || empty( $data['quiz'] ) ) {
				return;
			}

			// Check if global instructor email setting enabled, return if not set.
			$send_instructor_email = ir_get_settings( 'instructor_mail' );
			if ( ! $send_instructor_email ) {
				return;
			}

			$course_id           = learndash_get_course_id( intval( $data['quiz'] ) );
			$send_to_instructors = [];

			// Check if course author is instructor.
			$course = get_post( $course_id );
			if ( wdm_is_instructor( $course->post_author ) ) {
				array_push( $send_to_instructors, $course->post_author );
			}

			// Get shared instructors for the course if any.
			$shared_instructors_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
			$shared_instructors      = explode( ',', $shared_instructors_list );
			$send_to_instructors     = array_merge( $send_to_instructors, $shared_instructors );

			// Since not an instructor course or shared instructors related to the course, return.
			if ( empty( $send_to_instructors ) ) {
				return;
			}

			foreach ( $send_to_instructors as $instructor ) {
				// Get instructor details.
				$instructor_info = get_userdata( $instructor );

				// If user does not exist or email not set, next.
				if ( empty( $instructor_info ) || empty( $instructor_info->user_email ) ) {
					continue;
				}

				// Check if instructor specific email setting disabled.
				$is_instructor_emails_disabled = get_user_meta( $instructor, 'ir_quiz_emails_disabled', 1 );
				if ( ! empty( $is_instructor_emails_disabled ) ) {
					continue;
				}

				$quiz = get_post( $data['quiz'] );
				if ( empty( $quiz ) ) {
					return;
				}
				$email_template_data  = get_user_meta( $instructor, 'instructor_email_template', true );
				$global_template_data = get_option( '_wdmir_email_settings' );

				$mail_sub     = '';
				$mail_content = '';

				// First check if instructor specific email settings configured and if not then whether global email settings are configured.
				if ( ! empty( $email_template_data ) && array_key_exists( 'mail_sub', $email_template_data ) && ! empty( $email_template_data['mail_sub'] ) ) {
					$mail_sub     = $email_template_data['mail_sub'];
					$mail_content = $email_template_data['mail_content'];
				} elseif ( is_array( $global_template_data ) && array_key_exists( 'qc_subject', $global_template_data ) && ! empty( $global_template_data['qc_subject'] ) ) {
					$mail_sub     = $global_template_data['qc_subject'];
					$mail_content = $global_template_data['qc_mail_content'];
				}

				if ( empty( $mail_sub ) ) {
					$quiz_label = class_exists( 'LearnDash_Custom_Label' ) ? \LearnDash_Custom_Label::get_label( 'quiz' ) : __( 'Quiz' );
					$mail_sub   = sprintf(
					/* translators: 1: Site Title 2: Quiz label */
						__( '%1$s : User %2$s Completed', 'wdm_instructor_role' ),
						get_bloginfo( 'name' ),
						$quiz_label
					);
				} else {
					$mail_sub = str_replace( '$userid', $user->ID, $mail_sub );
					$mail_sub = str_replace( '$username', $user->user_login, $mail_sub );
					$mail_sub = str_replace( '$useremail', $user->user_email, $mail_sub );
					$mail_sub = str_replace( '$quizname', $quiz->post_title, $mail_sub );
					$mail_sub = str_replace( '$result', $data['percentage'], $mail_sub );
					$mail_sub = str_replace( '$points', $data['points'], $mail_sub );
				}
				// wl8 changes ends here.

				if ( empty( $mail_content ) ) {
					$mail_content  = __( 'User has attempted following quiz -<br/>', 'wdm_instructor_role' );
					$mail_content .= sprintf(
						/* translators: username */
						__( 'UserName: %s <br/>', 'wdm_instructor_role' ),
						$user->user_login
					);
					$mail_content .= sprintf(
						/* translators: user email */
						__( 'Email: %s <br/>', 'wdm_instructor_role' ),
						$user->user_email
					);
					$mail_content .= sprintf(
						/* translators: quiz title */
						__( 'Quiz title: %s <br/>', 'wdm_instructor_role' ),
						$quiz->post_title
					);
					if ( $data['pass'] ) {
						$mail_sub .= 'Result: Passed ';
					} else {
						$mail_sub .= 'Result: Failed';
					}
				} else {
					$mail_content = str_replace( '$userid', $user->ID, $mail_content );
					$mail_content = str_replace( '$username', $user->user_login, $mail_content );
					$mail_content = str_replace( '$useremail', $user->user_email, $mail_content );
					$mail_content = str_replace( '$quizname', $quiz->post_title, $mail_content );
					$mail_content = str_replace( '$result', $data['percentage'], $mail_content );
					$mail_content = str_replace( '$points', $data['points'], $mail_content );
				}

				/**
				 * Filter quiz completion email subject.
				 *
				 * @since 4.1.0
				 *
				 * @param string $mail_sub  Subject of the Email.
				 * @param int $instructor   Instructor ID.
				 * @param object $user      User who completed the quiz.
				 * @param array $data       Quiz Data.
				 */
				$mail_sub = apply_filters( 'ir_filter_quiz_completion_email_subject', $mail_sub, $instructor, $user, $data );

				/**
				 * Filter quiz completion email body.
				 *
				 * @since 4.1.0
				 *
				 * @param string $mail_sub  Subject of the Email.
				 * @param int $instructor   Instructor ID.
				 * @param object $user      User who completed the quiz.
				 * @param array $data       Quiz Data.
				 */
				$mail_content = apply_filters( 'ir_filter_quiz_completion_email_content', $mail_content, $instructor, $user, $data );

				add_filter( 'wp_mail_content_type', [ $this, 'wdm_ir_set_html_content_type' ] );
				wp_mail( $instructor_info->user_email, $mail_sub, $mail_content );
				remove_filter( 'wp_mail_content_type', [ $this, 'wdm_ir_set_html_content_type' ] );
			}
		}

		/**
		 * Set mail content type to html.
		 *
		 * @param string $content_type  Content type.
		 *
		 * @return string
		 */
		public function wdm_ir_set_html_content_type( $content_type ) {
			unset( $content_type );
			return 'text/html';
		}

		public function wdmir_individual_instructor_email_setting() {
			$current_user_id  = get_current_user_id();
			$prev_stored_data = get_user_meta( $current_user_id, 'instructor_email_template', true );
			?>
			<div class="wl8qcn-email-form">
			<form method="post" action="">
				<div class="wl8qcn-email-heading">
					<h2>
					<?php
					echo esc_html(
						sprintf(
						/* translators: Quiz Label */
							_x( '%s Completion Emails', 'user for instructor settings', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( 'quiz' )
						)
					);
					?>
						</h2>
					<p class="irb-cpe-desc-wrap">
						<span>
							<i class="irb-icon-hand"></i>
						</span>
						<span class="wl8qcn-email-desc">
							<?php
							printf(
								/* translators: 1: Quizzes label 2: Courses label */
								esc_html__( 'Email to be sent to instructor when a student completes one of the %1$s from your %2$s', 'wdm_instructor_role' ),
								\LearnDash_Custom_Label::get_label( 'quizzes' ),
								\LearnDash_Custom_Label::get_label( 'courses' )
							);
							?>
						</span>
					</p>
				</div>
				<div class="wl8qcn-email-sub">
				<label for="email">
					<?php echo esc_html__( 'Email Subject:', 'wdm_instructor_role' ); ?>
				</label>
					<input id="instructor_email_sub" rows="5" class="instructor_email_sub" name="instructor_email_sub" value="<?php echo ! empty( $prev_stored_data ) ? esc_attr( $prev_stored_data['mail_sub'] ) : ''; ?>">
				</div>

				<div class="wl8qcn-email-content">
				<label for="text">
					<?php echo esc_html__( 'Email Message:', 'wdm_instructor_role' ); ?>
				</label>
				<?php
				$content = '';
				if ( ! empty( $prev_stored_data ) ) {
					$content = $prev_stored_data['mail_content'];
				}
				$editor_id = 'instructor_email_message';
				wp_editor( $content, $editor_id );
				?>
				</div>
				<div id="instructor_email_template_variable">
				<h4>
					<?php echo __( 'ALLOWED VARIABLES', 'wdm_instructor_role' ); ?>
				</h4>
				<table>
				<?php
				$allowed_vars = $this->wl8GetAllowedVars();
				foreach ( $allowed_vars as $desc => $var ) {
					echo "<tr class='irb-av'><td><code>$var</code></td><td>$desc</td></tr>";
				}
				?>
				</table>
				</div>
				<br/>
				<input id="instructor_email_update" name="instructor_email_update" class="irb-btn button button-primary" type="submit" value="<?php esc_attr_e( 'Save', 'wdm_instructor_role' ); ?>"/>
			</form>
			</div>
			<?php
		}

		/**
		 * Function returns allowed variable list.
		 */
		public function wl8GetAllowedVars() {
			// allowed variables...
			$vars = [
				'Userid'            => '$userid',
				'Username'          => '$username',
				'User\'s email'     => '$useremail',
				'Quiz name'         => '$quizname',
				'Result in percent' => '$result',
				'Reached points'    => '$points',
			];

			return $vars;
		}

		/**
		 * Display of HTML content on Instructor Settings page.
		 * This function is called from file "commission.php" in function instuctor_page_callback()
		 *
		 * @since version 2.1
		 * @since 5.0.0         Updated general settings layout.
		 */
		public function wdmir_instructor_settings() {
			$ir_admin_settings = get_option( '_wdmir_admin_settings', [] );

			// Student Teacher Communication.
			$ir_student_communication_check = '';
			$this->wdmSetSettingVariable( $ir_admin_settings, 'ir_student_communication_check', $ir_student_communication_check );

			// Category Check Customizer.
			$ir_ld_category_check = '';
			$this->wdmSetSettingVariable( $ir_admin_settings, 'ir_ld_category_check', $ir_ld_category_check );

			// Product Review.
			$review_product = '';
			$this->wdmSetSettingVariable( $ir_admin_settings, 'review_product', $review_product );

			// Course Review.
			$review_course = '';
			$this->wdmSetSettingVariable( $ir_admin_settings, 'review_course', $review_course );

			// Download Review.
			$review_download = '';
			$this->wdmSetSettingVariable( $ir_admin_settings, 'review_download', $review_download );

			// instructor quiz completion email.
			$wl8_en_inst_mail = '';
			$this->wdmSetSettingVariable( $ir_admin_settings, 'instructor_mail', $wl8_en_inst_mail );

			// instructor course purchase email.
			$wdm_enable_instructor_course_mail = '';
			$this->wdmSetSettingVariable( $ir_admin_settings, 'wdm_enable_instructor_course_mail', $wdm_enable_instructor_course_mail );

			// added in 2.4.0 v instructor commission.
			$wl8_en_inst_commi = ''; // cspell:disable-line .
			$this->wdmSetSettingVariable( $ir_admin_settings, 'instructor_commission', $wl8_en_inst_commi ); // cspell:disable-line .

			// Disable instructor backend dashboard ( WP ).
			$ir_disable_backend_dashboard = '';
			$this->wdmSetSettingVariable( $ir_admin_settings, 'ir_disable_backend_dashboard', $ir_disable_backend_dashboard );

			// Enable wisdm tabs access to all logged in users.
			$enable_tabs_access = '';
			$this->wdmSetSettingVariable( $ir_admin_settings, 'enable_tabs_access', $enable_tabs_access );

			$is_ld_category           = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' );
			$is_wp_category           = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'wp_post_category' );
			$active_theme             = wp_get_theme()->template;
			$enable_ld_category       = isset( $ir_admin_settings['enable_ld_category'] ) ? $ir_admin_settings['enable_ld_category'] : '';
			$enable_wp_category       = isset( $ir_admin_settings['enable_wp_category'] ) ? $ir_admin_settings['enable_wp_category'] : '';
			$enable_permalinks        = isset( $ir_admin_settings['enable_permalinks'] ) ? $ir_admin_settings['enable_permalinks'] : '';
			$enable_elu_header        = isset( $ir_admin_settings['enable_elu_header'] ) ? $ir_admin_settings['enable_elu_header'] : '';
			$enable_elu_layout        = isset( $ir_admin_settings['enable_elu_layout'] ) ? $ir_admin_settings['enable_elu_layout'] : '';
			$enable_elu_cover         = isset( $ir_admin_settings['enable_elu_cover'] ) ? $ir_admin_settings['enable_elu_cover'] : '';
			$enable_bb_cover          = isset( $ir_admin_settings['enable_bb_cover'] ) ? $ir_admin_settings['enable_bb_cover'] : '';
			$enable_open_pricing      = isset( $ir_admin_settings['enable_open_pricing'] ) ? $ir_admin_settings['enable_open_pricing'] : '';
			$enable_free_pricing      = isset( $ir_admin_settings['enable_free_pricing'] ) ? $ir_admin_settings['enable_free_pricing'] : '';
			$enable_buy_pricing       = isset( $ir_admin_settings['enable_buy_pricing'] ) ? $ir_admin_settings['enable_buy_pricing'] : '';
			$enable_recurring_pricing = isset( $ir_admin_settings['enable_recurring_pricing'] ) ? $ir_admin_settings['enable_recurring_pricing'] : '';
			$enable_closed_pricing    = isset( $ir_admin_settings['enable_closed_pricing'] ) ? $ir_admin_settings['enable_closed_pricing'] : '';
			$wdm_login_redirect       = isset( $ir_admin_settings['wdm_login_redirect'] ) ? $ir_admin_settings['wdm_login_redirect'] : '';
			$wdm_login_redirect_page  = isset( $ir_admin_settings['wdm_login_redirect_page'] ) ? $ir_admin_settings['wdm_login_redirect_page'] : '';
			$wdm_id_ir_dash_pri_menu  = isset( $ir_admin_settings['wdm_id_ir_dash_pri_menu'] ) ? $ir_admin_settings['wdm_id_ir_dash_pri_menu'] : '';

			$login_redirect_tooltip = esc_html__(
				'This setting can be used to redirect the Instructors on your site to any desired page on successful logins.',
				'wdm_instructor_role'
			);

			$args = [
				'name'             => 'wdm_login_redirect_page',
				'id'               => 'wdm_login_redirect_page',
				'sort_column'      => 'menu_order',
				'sort_order'       => 'ASC',
				'show_option_none' => __( 'Select a Page', 'wdm_instructor_role' ),
				'class'            => 'wdm_login_redirect_page',
				'echo'             => 0,
				'selected'         => $wdm_login_redirect_page,
			];

			// Template render.
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/settings/ir-general-settings.template.php',
				[
					'ir_disable_backend_dashboard'      => $ir_disable_backend_dashboard,
					'review_product'                    => $review_product,
					'review_course'                     => $review_course,
					'review_download'                   => $review_download,
					'wl8_en_inst_mail'                  => $wl8_en_inst_mail,
					'wdm_enable_instructor_course_mail' => $wdm_enable_instructor_course_mail,
					'wl8_en_inst_commi'                 => $wl8_en_inst_commi, // cspell:disable-line .
					'ir_student_communication_check'    => $ir_student_communication_check,
					'ir_ld_category_check'              => $ir_ld_category_check,
					'wdm_login_redirect'                => $wdm_login_redirect,
					'page_args'                         => $args,
					'login_redirect_tooltip'            => $login_redirect_tooltip,
					'wdm_id_ir_dash_pri_menu'           => $wdm_id_ir_dash_pri_menu,
					'enable_ld_category'                => $enable_ld_category,
					'enable_wp_category'                => $enable_wp_category,
					'enable_permalinks'                 => $enable_permalinks,
					'enable_elu_header'                 => $enable_elu_header,
					'enable_elu_layout'                 => $enable_elu_layout,
					'enable_elu_cover'                  => $enable_elu_cover,
					'enable_bb_cover'                   => $enable_bb_cover,
					'enable_open_pricing'               => $enable_open_pricing,
					'enable_free_pricing'               => $enable_free_pricing,
					'enable_buy_pricing'                => $enable_buy_pricing,
					'enable_recurring_pricing'          => $enable_recurring_pricing,
					'enable_closed_pricing'             => $enable_closed_pricing,
					'enable_tabs_access'                => $enable_tabs_access,
					'is_ld_category'                    => $is_ld_category,
					'is_wp_category'                    => $is_wp_category,
					'active_theme'                      => $active_theme,
				]
			);
		}

		public function wdmSetSettingVariable( $wdmir_admin_settings, $key, &$value ) {
			if ( isset( $wdmir_admin_settings[ $key ] ) && '1' == $wdmir_admin_settings[ $key ] ) {
				$value = 'checked';
			}
		}

		/*
		*   @since version 2.1
		*   Saving HTML form content of Instructor Settings page.
		*
		*/
		function wdmir_settings_save() {
			if ( isset( $_POST['instructor_setting_nonce'] ) && wp_verify_nonce( $_POST['instructor_setting_nonce'], 'instructor_setting_nonce_action' ) && is_admin() ) {
				$wdmir_admin_settings = [];
				$ir_admin_settings    = get_option( '_wdmir_admin_settings', [] );

				do_action( 'wdmir_settings_save_before' );

				// Product Review.
				$wdmir_admin_settings['review_product'] = '';
				if ( isset( $_POST['wdmir_review_product'] ) ) {
					$wdmir_admin_settings['review_product'] = 1;
				}

				// Course Review.
				$wdmir_admin_settings['review_course'] = '';
				if ( isset( $_POST['wdmir_review_course'] ) ) {
					$wdmir_admin_settings['review_course'] = 1;
				}
				// Download Review.
				$wdmir_admin_settings['review_download'] = '';
				if ( isset( $_POST['wdmir_review_download'] ) ) {
					$wdmir_admin_settings['review_download'] = 1;
				}

				// Enable instructor quiz completion email.
				$wdmir_admin_settings['instructor_mail'] = '';
				if ( isset( $_POST['wdm_enable_instructor_mail'] ) ) {
					$wdmir_admin_settings['instructor_mail'] = 1;
				}

				// Enable instructor course completion emails.
				$wdmir_admin_settings['wdm_enable_instructor_course_mail'] = '';
				if ( isset( $_POST['wdm_enable_instructor_course_mail'] ) ) {
					$wdmir_admin_settings['wdm_enable_instructor_course_mail'] = 1;
				}

				// Course Review.
				$wdmir_admin_settings['review_course_content'] = '';
				if ( isset( $_POST['wdmir_review_course_content'] ) ) {
					$wdmir_admin_settings['review_course_content'] = $_POST['wdmir_review_course_content'];
				}

				// instructor commission.
				$wdmir_admin_settings['instructor_commission'] = '';
				if ( isset( $_POST['wdm_enable_instructor_commission'] ) ) {
					$wdmir_admin_settings['instructor_commission'] = 1;
				}

				// LD Category Access.
				$wdmir_admin_settings['ir_ld_category_check'] = '';
				if ( isset( $_POST['ir_ld_category_check'] ) ) {
					$wdmir_admin_settings['ir_ld_category_check'] = 1;
				}

				// Enable student teacher communication module.
				$wdmir_admin_settings['ir_student_communication_check'] = '';
				if ( isset( $_POST['ir_student_communication_check'] ) ) {
					$wdmir_admin_settings['ir_student_communication_check'] = 1;
				}

				// Toggle Backend WP Dashboard for Instructors.
				$wdmir_admin_settings['ir_disable_backend_dashboard'] = '';
				if ( isset( $_POST['ir_disable_backend_dashboard'] ) ) {
					$wdmir_admin_settings['ir_disable_backend_dashboard'] = 1;
				}

				// Enable wisdm tabs block access for all logged in users.
				$wdmir_admin_settings['enable_tabs_access'] = '';
				if ( isset( $_POST['enable_tabs_access'] ) ) {
					$wdmir_admin_settings['enable_tabs_access'] = 1;
				}

				$ir_admin_settings = array_merge( $ir_admin_settings, $wdmir_admin_settings );

				// Saving instructor settings option.
				update_option( '_wdmir_admin_settings', $ir_admin_settings );

				/**
				 * Action after saving instructor general settings.
				 *
				 * @since 2.1.0
				 */
				do_action( 'wdmir_settings_save_after' );

				wp_redirect( $_POST['_wp_http_referer'] );
			}
		}

		/**
		 * Hide the new category creation links for instructors
		 *
		 * @since 3.5.0
		 */
		public function hide_category_links() {
			if ( ! wdm_is_instructor() || ! ir_admin_settings_check( 'ir_ld_category_check' ) ) {
				return;
			}
			global $current_screen;

			$target_screens = [
				'sfwd-courses',  // Courses.
				'sfwd-lessons',  // Lessons.
				'sfwd-topic',     // Topic.
			];

			// Check if course or lesson edit screen.
			if ( ! empty( $current_screen ) && in_array( $current_screen->id, $target_screens ) ) {
				?>
				<style>
				/* Hide instructor category adding link */
				.components-button.editor-post-taxonomies__hierarchical-terms-add.is-link, #category-adder {
					display: none;
				}
				</style>
				<?php
			}

			$target_screens = [
				'edit-sfwd-courses',  // Courses.
				'edit-sfwd-lessons',  // Lessons.
				'edit-sfwd-topic',     // Topic.
			];

			// Check if course or lesson listing screen.
			if ( ! empty( $current_screen ) && in_array( $current_screen->id, $target_screens ) ) {
				?>
				<style>
				/* Hide instructor category links */
				.edit-post-header__settings {
					display: none;
				}
				</style>
				<?php
			}
		}

		/**
		 * Toggle other module activation/deactivation based on configured settings.
		 *
		 * @since 3.6.0
		 */
		public function toggle_module_activation() {
			$active_modules = ir_get_active_core_modules();

			if ( empty( $active_modules ) ) {
				return;
			}

			// Handle Student Teacher Communication module.
			$is_student_communication_enabled = ir_get_settings( 'ir_student_communication_check' );

			global $bp;

			// If enabled, activate student communication module.
			if ( $is_student_communication_enabled && function_exists( 'bp_is_active' ) && bp_is_active( 'messages' ) ) {
				if ( ! in_array( 'student_communication', $active_modules ) ) {
					ir_enable_core_modules( [ 'student_communication' ] );
				}
			} elseif ( in_array( 'student_communication', $active_modules ) ) {
					ir_disable_core_modules( [ 'student_communication' ] );
			}

			// Handle Frontend Course Creator module.
			$is_frontend_course_creation_enabled = ir_get_settings( 'ir_enable_frontend_dashboard' );

			if ( 'on' === $is_frontend_course_creation_enabled ) {
				if ( ! in_array( 'frontend_dashboard', $active_modules, 1 ) ) {
					ir_enable_core_modules( [ 'frontend_dashboard' ] );
				}
			} elseif ( in_array( 'frontend_dashboard', $active_modules, 1 ) ) {
					ir_disable_core_modules( [ 'frontend_dashboard' ] );
			}

			// Enable Frontend Dashboard by default.
			if ( ! in_array( 'dashboard_block', $active_modules, 1 ) ) {
				ir_enable_core_modules( [ 'dashboard_block' ] );
			}
		}

		/**
		 * This function Builds sidebar array for GUI template in relation to database
		 *
		 * @return array $sidebar_menu
		 *
		 * @since 4.3.1
		 */
		public function get_dashboard_sidebar_menu( $sidebar = [] ) {
			$sidebar_menu         = [];
			$default_sidebar_menu = [
				[
					'title'      => 'LearnDash LMS',
					'submenu'    => [
						'overview'    => [
							'title'       => __( 'Overview', 'wdm_instructor_role' ),
							'slug'        => 'admin.php?page=ir_instructor_overview',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'course'      => [
							'title'       => __( 'Courses', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=sfwd-courses',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'lesson'      => [
							'title'       => __( 'Lessons', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=sfwd-lessons',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'topic'       => [
							'title'       => __( 'Topics', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=sfwd-topic',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'quiz'        => [
							'title'       => __( 'Quizzes', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=sfwd-quiz',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'question'    => [
							'title'       => __( 'Questions', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=sfwd-question',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'certificate' => [
							'title'       => __( 'Certificates', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=sfwd-certificates',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'group'       => [
							'title'       => __( 'Groups', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=groups',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'exam'        => [
							'title'       => __( 'Challenge Exams', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=ld-exam',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'assignment'  => [
							'title'       => __( 'Assignments', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=sfwd-assignment',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'essays'      => [
							'title'       => __( 'Essays', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=sfwd-essays',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'group_admin' => [
							'title'       => __( 'Group Administration', 'wdm_instructor_role' ),
							'slug'        => 'admin.php?page=group_admin_page',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'reports'     => [
							'title'       => __( 'Course Reports', 'wdm_instructor_role' ),
							'slug'        => 'instructor_lms_reports',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'instructor'  => [
							'title'       => __( 'Instructor', 'wdm_instructor_role' ),
							'slug'        => 'instructor_page',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
					],
					'slug'       => 'learndash-lms',
					'class_name' => 'ir-menu-item-sort-disabled',
					'action_add' => 'istrue',
					'icon'       => 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIGlkPSJMYXllcl8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIKCQkJCQkJCQkJIHZpZXdCb3g9IjAgMCA1OCA0Ni42IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA1OCA0Ni42OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CgkJCQkJCQkJPHBhdGggZmlsbD0iI2ZmZiIgc3R5bGU9Im9wYWNpdHk6LjQ1IiBkPSJNNTEsOS43VjIuNkM1MSwxLjIsNDkuOCwwLDQ4LjQsMEgyLjZDMS4yLDAsMCwxLjIsMCwyLjZ2Ny4xTDUxLDkuN3ogTTEyLjgsNC42YzAuNC0wLjQsMS4yLTAuNCwxLjYsMAoJCQkJCQkJCQljMC4yLDAuMiwwLjQsMC41LDAuNCwwLjhTMTQuNyw2LDE0LjUsNi4yYy0wLjIsMC4yLTAuNSwwLjQtMC44LDAuNHMtMC42LTAuMS0wLjgtMC40Yy0wLjItMC4yLTAuNC0wLjUtMC40LTAuOAoJCQkJCQkJCQlDMTIuNSw1LjEsMTIuNyw0LjgsMTIuOCw0LjZ6IE04LjYsNC42YzAuNC0wLjQsMS4yLTAuNCwxLjYsMGMwLjIsMC4yLDAuNCwwLjUsMC40LDAuOFMxMC40LDYsMTAuMiw2LjJDMTAsNi40LDkuNyw2LjUsOS40LDYuNQoJCQkJCQkJCQljLTAuMywwLTAuNi0wLjEtMC44LTAuNEM4LjMsNiw4LjIsNS43LDguMiw1LjRTOC4zLDQuOCw4LjYsNC42eiBNNC4zLDQuNmMwLjQtMC40LDEuMi0wLjQsMS42LDBjMC4yLDAuMiwwLjQsMC41LDAuNCwwLjgKCQkJCQkJCQkJUzYuMSw2LDUuOSw2LjJDNS43LDYuNCw1LjQsNi41LDUuMSw2LjVTNC41LDYuNCw0LjMsNi4yQzQsNiwzLjksNS43LDMuOSw1LjRDMy45LDUuMSw0LDQuOCw0LjMsNC42eiIvPgoJCQkJCQkJCTxwYXRoIGZpbGw9IiNmZmYiIHN0eWxlPSJvcGFjaXR5Oi40NSIgZD0iTTMwLDM0YzAtOC42LDctMTUuNSwxNS41LTE1LjVjMS45LDAsMy43LDAuNCw1LjQsMXYtNi43SDB2MjguOWMwLDEuNSwxLjIsMi42LDIuNiwyLjZIMzRDMzEuNSw0MS43LDMwLDM4LDMwLDM0eiIvPgoJCQkJCQkJCTxwYXRoIGZpbGw9IiNmZmYiIHN0eWxlPSJvcGFjaXR5Oi40NSIgZD0iTTQ1LjUsMjEuNUMzOC42LDIxLjUsMzMsMjcuMSwzMywzNHM1LjYsMTIuNSwxMi41LDEyLjVDNTIuNCw0Ni42LDU4LDQxLDU4LDM0UzUyLjQsMjEuNSw0NS41LDIxLjV6IE01Mi4zLDMwLjdsLTcuMiw4LjhoMAoJCQkJCQkJCQljLTAuMywwLjQtMC44LDAuNi0xLjMsMC42Yy0wLjUsMC0wLjktMC4yLTEuMi0wLjVsMCwwbC0zLjktNC4ybDAsMGMtMC4zLTAuMy0wLjQtMC43LTAuNC0xLjFjMC0wLjksMC43LTEuNywxLjctMS43CgkJCQkJCQkJCWMwLjUsMCwwLjksMC4yLDEuMiwwLjVsMCwwbDIuNiwyLjhsNi03LjNsMCwwQzUwLDI4LjIsNTAuNSwyOCw1MSwyOGMwLjksMCwxLjcsMC43LDEuNywxLjdDNTIuNywzMCw1Mi41LDMwLjQsNTIuMywzMC43TDUyLjMsMzAuN3oKCQkJCQkJCQkJIi8+CgkJCQkJCQkJPC9zdmc+',
				],
				[
					'title' => __( 'Comments', 'wdm_instructor_role' ),
					'slug'  => 'edit-comments.php',
					'icon'  => 'dashicons-admin-comments',
				],
				[
					'title'   => __( 'Products', 'wdm_instructor_role' ),
					'submenu' => [
						'all_products' => [
							'title'       => __( 'All Products', 'wdm_instructor_role' ),
							'slug'        => 'edit.php?post_type=product',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
						'new_product'  => [
							'title'       => __( 'New Product', 'wdm_instructor_role' ),
							'slug'        => 'post-new.php?post_type=product',
							'class_name'  => 'ir-submenu-item-sort-disabled',
							'action_menu' => 'istrue',
						],
					],
					'slug'    => 'edit.php?post_type=product',
					'icon'    => 'dashicons-archive',
				],
				[
					'title' => __( 'Profile', 'wdm_instructor_role' ),
					'slug'  => get_edit_profile_url(),
					'icon'  => 'dashicons-admin-users',
				],
				[
					'title'      => __( 'Logout', 'wdm_instructor_role' ),
					'slug'       => home_url() . '/wp-login.php?action=logout',
					'icon'       => 'dashicons-migrate',
					'class_name' => 'ir-menu-item-sort-disabled',
				],
			];

			if ( empty( $sidebar ) ) {
				$sidebar_menu = $default_sidebar_menu;
			} else {
				$updated_menu  = [];
				$default_slugs = array_column( $default_sidebar_menu, 'slug' );
				// extract slug.
				$data = array_keys( $sidebar );
				foreach ( $default_slugs as $menu_key => $menu_item ) {
					$key                  = array_search( $menu_item, $data );
					$updated_menu[ $key ] = $default_sidebar_menu[ $menu_key ];
				}
				// action keys data.
				foreach ( $default_slugs as $menu_key => $menu_item ) {
					$key = array_search( $menu_item, $data );
					foreach ( $sidebar as $menu ) {
						if ( $menu['slug'] == $menu_item ) {
							if ( isset( $menu['hide'] ) ) {
								$updated_menu[ $key ]['hide'] = $menu['hide'];
							}
							if ( isset( $menu['hide_restrict'] ) ) {
								$updated_menu[ $key ]['hide_restrict'] = $menu['hide_restrict'];
							}
							if ( isset( $menu['delete'] ) ) {
								$updated_menu[ $key ]['delete'] = $menu['delete'];
							}
						}
					}
				}
				ksort( $updated_menu );
				// Adding custom menus.
				$updated_menu = $this->ir_update_custom_menu_position( $sidebar, $updated_menu );
				$sidebar_menu = $updated_menu;
			}
			return $sidebar_menu;
		}

		/**
		 * Helper function for 'get_dashboard_sidebar_menu' function to append custom items for GUI sidebar array
		 *
		 * @return array $custom_menu / $updated_menu
		 *
		 * @since 4.3.1
		 */
		public function ir_update_custom_menu_position( $sidebar, $updated_menu ) {
			// get custom data menu.
			$menus      = array_column( $sidebar, 'slug' );
			$menus_type = array_column( $sidebar, 'type' );
			if ( isset( $menus_type ) ) {
				// Update reference array for custom data.
				foreach ( $menus as $key => $url ) {
					if ( ! in_array( $url, array_column( $updated_menu, 'slug' ) ) ) {
						array_splice( $updated_menu, $key, 0, [ $sidebar[ $url ] ] );
					}
				}
				// create new custom array based on reference array data.
				foreach ( $menus as $key => $url ) {
					$itemkey       = array_search( $url, array_column( $updated_menu, 'slug' ) ); // cspell:disable-line .
					$custom_menu[] = $updated_menu[ $itemkey ]; // cspell:disable-line .
				}
				return $custom_menu;
			} else {
				return $updated_menu;
			}
		}


		/**
		 * Menu Initialize action function for building menus and its primary operations
		 *
		 * @since 4.3.1
		 */
		public function initialize_instructor_admin_menu() {
			if ( wp_doing_ajax() ) {
				return;
			}

			if ( ir_get_settings( 'ir_sidebar_menu' ) && ir_get_settings( 'ir_sidebar_sub_menu' ) ) {
				// Clean menu.
				add_action( 'admin_menu', [ $this, 'clear_instructor_admin_menu' ], 9999 );
				add_action( 'admin_menu', [ $this, 'build_instructor_admin_menu' ], 99999 );
				add_action( 'admin_menu', [ $this, 'instructor_admin_menu_action' ], 99999 );
				add_action( 'init', [ $this, 'instructor_admin_menu_action' ] );
				add_action( 'init', [ $this, 'ir_delete_custom_menu_submenu_action', 999999 ] );
			}
		}

		/**
		 * Helper function to clear all menu and submenu items as instructor if custom menu array is present
		 *
		 * @since 4.3.1
		 */
		public function clear_instructor_admin_menu() {
			// check if instructor.
			if ( ! wdm_is_instructor() ) {
				return;
			}
			// Clean Menu.
			global $menu;
			foreach ( $menu as $menuitem ) {
				remove_menu_page( $menuitem[2] );
			}
			// Clean Submenu.
			global $submenu;
			$profile = get_edit_profile_url();
			$logout  = home_url() . '/wp-login.php?action=logout';

			$clean_array = [
				'learndash-lms',
				'edit-comments.php',
				'edit.php?post_type=product',
				$profile,
				$logout,

			];
			foreach ( $submenu as $key => $submenuitem ) {
				if ( in_array( $key, $clean_array ) ) {
					foreach ( $submenuitem as $submenuitemdata ) {
						remove_submenu_page( $key, $submenuitemdata[2] );
					}
				}
			}
		}

		/**
		 * Builds the $menu and $submenu globals for instructor from scratch referring the custom menu array if present
		 *
		 * @since 4.3.1
		 */
		public function build_instructor_admin_menu() {
			$sidebar_menu     = ir_get_settings( 'ir_sidebar_menu' );
			$sidebar_sub_menu = ir_get_settings( 'ir_sidebar_sub_menu' );

			// check if instructor.
			if ( ! wdm_is_instructor() ) {
				return;
			}

			wp_enqueue_script(
				'ir-datatables-script',
				plugins_url( 'js/datatables.min.js', __DIR__ ),
				[ 'jquery' ],
				gmdate( 'hi', time() ),
				true
			);

			// Build Menu.
			foreach ( $sidebar_menu as $menuitem ) {
				$slug = $menuitem['slug'];
				// Products check.
				if ( 'edit.php?post_type=product' == $menuitem['slug'] && ( ! wdmCheckWooDependency() || ! class_exists( 'WooCommerce' ) ) ) {
					continue;
				}

				// Icon fallback.
				if ( empty( $menuitem['icon'] ) ) {
					$menuicon = 'dashicons-arrow-right-alt2';
				} else {
					$menuicon = $menuitem['icon'];
				}

				add_menu_page(
					$menuitem['title'],
					$menuitem['title'],
					'instructor_reports',
					$slug,
					'',
					$menuicon
				);
			}

			// Check if class exists.
			if ( ! class_exists( 'Instructor_Role_Reports' ) ) {
				require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-reports.php';
			}
			// Get class instance.
			$course_report_instance = Instructor_Role_Reports::get_instance();

			// Build Submenu.
			foreach ( $sidebar_sub_menu as $key => $submenuitem ) {
				foreach ( $submenuitem as $submenuitemdata ) {
					// Default values.
					$parent_slug      = $key;
					$page_title       = $submenuitemdata['title'];
					$menu_title       = $submenuitemdata['title'];
					$capability       = 'instructor_reports';
					$menu_slug        = $submenuitemdata['slug'];
					$submenu_callback = '';

					// Updated values for specific sub menus.
					$submenu_callback = '';
					if ( 'instructor_reports' == $submenuitemdata['slug'] ) {
						$submenu_callback = [ $course_report_instance, 'show_reports_page' ];
					} elseif ( 'instructor_page' == $submenuitemdata['slug'] ) {
						$parent_slug      = 'learndash-lms';
						$page_title       = __( 'Instructor', 'wdm_instructor_role' );
						$menu_title       = __( 'Instructor', 'wdm_instructor_role' );
						$capability       = 'instructor_page';
						$menu_slug        = 'instuctor';
						$submenu_callback = [ $this, 'instuctor_page_callback' ];
					} elseif ( 'admin.php?page=group_admin_page' == $submenuitemdata['slug'] ) {
						$capability = 'wdm_instructor';
						$menu_slug  = 'admin.php?page=group_admin_page';
					} elseif ( 'edit.php?post_type=groups' == $submenuitemdata['slug'] ) {
						$capability = 'edit_groups';
					}

					if ( isset( $submenuitemdata['hide'] ) || isset( $submenuitemdata['hide_restrict'] ) ) {
						$parent_slug = null;
					}

					if ( 'learndash-lms' == $key && ! empty( learndash_get_custom_label( $submenuitemdata['title'] ) ) ) {
						$menu_title = learndash_get_custom_label( $submenuitemdata['title'] );
					} else {
						$menu_title = esc_html( $submenuitemdata['title'] );
					}

					add_submenu_page(
						$parent_slug,
						$page_title,
						$menu_title,
						$capability,
						$menu_slug,
						$submenu_callback
					);
				}
			}

			// Custom label building.
			// Check if class exists.
			if ( ! class_exists( 'Instructor_Role_Dashboard' ) ) {
				require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-dashboard.php';
			}
			// Get class instance.
			$update_label = Instructor_Role_Dashboard::get_instance();
			$update_label->update_instructor_lms_label();
		}

		/**
		 * Helper function for menu and submenu actions like Hide, Hide and Restrict
		 *
		 * @since 4.3.1
		 */
		public function instructor_admin_menu_action() {
			$sidebar_menu     = ir_get_settings( 'ir_sidebar_menu' );
			$sidebar_sub_menu = ir_get_settings( 'ir_sidebar_sub_menu' );

			// Instructor check.
			if ( ! wdm_is_instructor() ) {
				return;
			}
			if ( isset( $sidebar_menu ) && $sidebar_menu ) {
				// MENU Action.
				foreach ( $sidebar_menu as $menu ) {
					// check if instructor.
						// Hide menu action.
					if ( isset( $menu['hide'] ) ) {
						remove_menu_page( $menu['slug'] );
					}

					// Hide & Restrict menu action.
					if ( isset( $menu['hide_restrict'] ) ) {
						// Hide.
						remove_menu_page( $menu['slug'] );

						// menu slug cleaning.
						$removeChar   = 'https://';
						$http_referer = str_replace( $removeChar, '', $menu['slug'] );
						$removeChar   = 'http://';
						$http_referer = str_replace( $removeChar, '', $http_referer );

						// Restrict
						// If ends with $http_referer.
						if ( substr( home_url( $_SERVER['REQUEST_URI'] ), - strlen( $http_referer ) ) === $http_referer ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Check it later.
							wp_die( 'Restricted page' );
						}
					}
				}
			}

			if ( isset( $sidebar_sub_menu ) && $sidebar_sub_menu ) {
				// SUB-MENU Action.
				foreach ( $sidebar_sub_menu as $subkey => $submenu ) { // cspell:disable-line .
					foreach ( $submenu as $submenuitem ) {
						// Hide & Restrict menu action.
						if ( isset( $submenuitem['hide_restrict'] ) || isset( $sidebar_menu[ $subkey ]['hide_restrict'] ) ) { // cspell:disable-line .
							// Hide.
							remove_submenu_page( $subkey, $submenuitem['slug'] ); // cspell:disable-line .

							$url = $submenuitem['slug'];

							if ( 'instructor_page' == $url ) {
								$url = 'admin.php?page=instuctor';
							}

							// Menu slug cleaning.
							$removeChar   = 'https://';
							$http_referer = str_replace( $removeChar, '', $url );
							$removeChar   = 'http://';
							$http_referer = str_replace( $removeChar, '', $http_referer );

							// Restrict Page
							// If ends with $http_referer.
							if ( substr( home_url( $_SERVER['REQUEST_URI'] ), - strlen( $http_referer ) ) === $http_referer ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Check it later.
								wp_die( 'Restricted page' );
							}
						}
					}
				}
			}
		}

		/**
		 * Delete action helper function for custom menu and submenu
		 *
		 * @since 4.3.1
		 */
		public function ir_delete_custom_menu_submenu_action() {
			$sidebar_menu     = ir_get_settings( 'ir_sidebar_menu' );
			$sidebar_sub_menu = ir_get_settings( 'ir_sidebar_sub_menu' );

			// Menu Action.
			foreach ( $sidebar_menu as $menu ) {
				// Hide menu action.
				if ( isset( $menu['delete'] ) ) {
					unset( $sidebar_menu[ $menu['slug'] ] );
					ir_set_settings( 'ir_sidebar_menu', $sidebar_menu );
				}
			}

			// Submenu Action.
			foreach ( $sidebar_sub_menu as $subkey => $submenu ) { // cspell:disable-line .
				foreach ( $submenu as $submenuitemkey => $submenuitem ) {  // cspell:disable-line .
					// Hide submenu action.
					if ( isset( $submenuitem['delete'] ) ) {
						unset( $sidebar_sub_menu[ $subkey ][ $submenuitemkey ] ); // cspell:disable-line .
						// clean empty submenus.
						if ( empty( $sidebar_sub_menu[ $subkey ] ) ) { // cspell:disable-line .
							unset( $sidebar_sub_menu[ $subkey ] ); // cspell:disable-line .
						}
						ir_set_settings( 'ir_sidebar_sub_menu', $sidebar_sub_menu );
					}
				}
			}
		}

		/**
		 * Restrict instructor access to WP Dashboard.
		 *
		 * @since 5.0.0
		 */
		public function toggle_instructor_backend_access() {
			if ( ! wdm_is_instructor() ) {
				return;
			}

			// Get backend access setting.
			$restrict_access = ir_get_settings( 'ir_disable_backend_dashboard' );

			if ( ! $restrict_access ) {
				return;
			}

			// Redirect instructors to home.
			$url = home_url();

			/**
			 * Filter the redirect URI when instructors are restricted.
			 *
			 * @since 5.0.0
			 *
			 * @param string $url   Redirect URL, defaults to site home url.
			 */
			$url = apply_filters( 'ir_filter_dashboard_restrict_redirect', $url );
			if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) && ! $this->check_if_elementor_page() ) {
				wp_redirect( $url );
				exit;
			}
		}

		/**
		 * Show backend dashboard settings page
		 *
		 * @since 5.0.0
		 */
		public function show_backend_dashboard_settings() {
			wp_enqueue_script(
				'ir-backend-dashboard-settings-script',
				plugins_url( '/js/settings/ir-backend-dashboard-settings.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/js/settings/ir-backend-dashboard-settings.js' ),
				1
			);

			// Template render.
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/settings/ir-backend-dashboard-settings.template.php',
				[
					'instance'   => $this,
					'banner_img' => plugins_url( '/images/frontend-db-intro.png', __DIR__ ),
				]
			);
		}

		/**
		 * Check if this page is elementor or not.
		 *
		 * @since 5.3.0
		 */
		public function check_if_elementor_page() {
			$post_id = filter_input( INPUT_GET, 'post' );
			$action  = filter_input( INPUT_GET, 'action' );
			if ( ! empty( $post_id ) && 'elementor' == $action ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Ajax for updating admin settings.
		 *
		 * @since 5.9.0
		 */
		public function ajax_update_admin_settings() {
			$response = [
				'message' => __( 'Some error occurred and the update was not done. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Check if user is admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
				);
			}

			$ir_admin_key                         = ir_filter_input( 'key', INPUT_POST, 'string' );
			$ir_admin_value                       = ir_filter_input( 'value', INPUT_POST, 'string' );
			$success_flag                         = 0;
			$ir_admin_toggle_settings             = [
				'ir_enable_frontend_dashboard',
				'ir_disable_ld_links',
				'ir_enable_sync',
				'enable_wp_category',
				'enable_ld_category',
				'enable_open_pricing',
				'enable_free_pricing',
				'enable_buy_pricing',
				'enable_recurring_pricing',
				'enable_closed_pricing',
				'wdm_id_ir_dash_pri_menu',
				'wdm_login_redirect',
				'enable_permalinks',
			];
			$ir_admin_numeric_settings            = [
				'review_course',
				'review_product',
				'instructor_mail',
				'wdm_enable_instructor_course_mail',
				'enable_tabs_access',
				'ir_student_communication_check',
				'instructor_commission',
				'ir_ld_category_check',
			];
			$ir_admin_frontend_dashboard_settings = [
				'ir_frontend_overview_block',
				'ir_frontend_courses_block',
				'ir_frontend_quizzes_block',
				'ir_frontend_settings_block',
				'ir_frontend_products_block',
				'ir_frontend_commissions_block',
				'ir_frontend_assignments_block',
				'ir_frontend_essays_block',
				'ir_frontend_quiz_attempts_block',
				'ir_frontend_comments_block',
				'ir_frontend_course_reports_block',
				'ir_frontend_groups_block',
				'ir_frontend_certificates_block',
				'ir_frontend_overview_course_tile_block',
				'ir_frontend_overview_student_tile_block',
				'ir_frontend_overview_submissions_tile_block',
				'ir_frontend_overview_quiz_attempts_tile_block',
				'ir_frontend_overview_course_progress_block',
				'ir_frontend_overview_top_courses_block',
				'ir_frontend_overview_earnings_block',
				'ir_frontend_overview_submissions_block',
			];

			if ( in_array( $ir_admin_key, $ir_admin_toggle_settings ) ) {
				if ( $ir_admin_value === '1' ) {
					ir_set_settings( $ir_admin_key, 'on' );
				} else {
					ir_set_settings( $ir_admin_key, 'off' );
				}
				$success_flag = 1;
			}

			if ( in_array( $ir_admin_key, $ir_admin_numeric_settings ) ) {
				if ( $ir_admin_value === '1' ) {
					ir_set_settings( $ir_admin_key, 1 );
				} else {
					ir_set_settings( $ir_admin_key, '' );
				}
				$success_flag = 1;
			}

			if ( in_array( $ir_admin_key, $ir_admin_frontend_dashboard_settings ) ) {
				if ( $ir_admin_value === '1' ) {
					ir_set_settings( $ir_admin_key, 'on' );
				} else {
					ir_set_settings( $ir_admin_key, '' );
				}
				$this->update_dashboard_page_content();
				$success_flag = 1;
			}

			if ( $ir_admin_key === 'ir_disable_backend_dashboard' && 1 !== $success_flag ) {
				if ( $ir_admin_value === '1' ) {
					ir_set_settings( $ir_admin_key, '' );
				} else {
					ir_set_settings( $ir_admin_key, 1 );
				}
				$success_flag = 1;
			}

			if ( $ir_admin_key === 'wdmir_review_course' && 1 !== $success_flag ) {
				if ( $ir_admin_value === '1' ) {
					ir_set_settings( 'review_course', 1 );
				} else {
					ir_set_settings( 'review_course', '' );
				}
				$success_flag = 1;
			}

			if ( $ir_admin_key === 'ir_ld_category_check' && 1 !== $success_flag ) {
				if ( $ir_admin_value === '1' ) {
					ir_set_settings( 'ir_ld_category_check', 1 );
				} else {
					ir_set_settings( 'ir_ld_category_check', '' );
				}
				$success_flag = 1;
			}

			if ( $ir_admin_key === 'ir_enable_profile_links' || $ir_admin_key === 'ir_frontend_dashboard_page' && 1 !== $success_flag ) {
				update_option( $ir_admin_key, $ir_admin_value );
				$success_flag = 1;
			}

			if ( $ir_admin_key === 'stu_com_editor_set_button' || $ir_admin_key === 'stu_com_editor_set_popup' && 1 !== $success_flag ) {
				ir_set_settings( $ir_admin_key, $ir_admin_value );
				$success_flag = 1;
			}

			if (
				$ir_admin_key === 'wdm_bulk_commission'
				|| $ir_admin_key === 'instructor_commission'
			) {
				// Update all instructors meta.

				$all_instructors = ir_get_instructors();
				if ( ! empty( $all_instructors ) ) {
					foreach ( $all_instructors as $instructor ) {
						update_user_meta(
							$instructor->ID,
							$ir_admin_key === 'wdm_bulk_commission'
								? 'wdm_commission_percentage'
								: 'ir_commission_disabled',
							$ir_admin_key === 'wdm_bulk_commission'
								? Instructor_Role_Commission::normalize_commission_percentage( $ir_admin_value, $instructor->ID )
								: ! Cast::to_bool( $ir_admin_value )
						);
					}
				}

				$success_flag = 1;
			}

			if ( $ir_admin_key === 'wdm_login_redirect' ) {
				if ( $ir_admin_value === '1' ) {
					ir_set_settings( 'wdm_login_redirect_page', get_option( 'ir_frontend_dashboard_page', '' ) );
				} else {
					ir_set_settings( 'wdm_login_redirect_page', '' );
				}
				$success_flag = 1;
			}

			if ( 1 === $success_flag ) {
				// Return success response.
				wp_send_json_success(
					[
						'message' => __( 'Settings updated successfully.', 'wdm_instructor_role' ),
						'type'    => 'success',
					]
				);
			} else {
				// Return error response.
				wp_send_json_error( $response );
			}
		}

		/**
		 * Ajax for dismissing onboarding steps.
		 *
		 * @since 5.9.0
		 */
		public function ajax_dismiss_onboarding_steps() {
			$response = [
				'message' => __( 'Some error occurred and the update was not done. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

				// Check if user is admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
				);
			}

				$current_tab = ir_filter_input( 'tab', INPUT_POST, 'string' );

			switch ( $current_tab ) {
				case 'dashboard_settings':
					ir_set_settings( 'create_dashboard_onboarding_dismissed', 1 );
					break;
				case 'instructor':
					ir_set_settings( 'add_instructor_onboarding_dismissed', 1 );
					break;
				case 'instructor_settings':
					ir_set_settings( 'instructor_settings_onboarding_dismissed', 1 );
					break;
				case 'course_creation':
					ir_set_settings( 'course_creation_onboarding_dismissed', 1 );
					break;
			}

				// Return success response.
				wp_send_json_success(
					[
						'message' => __( 'Notice dismissed.', 'wdm_instructor_role' ),
						'type'    => 'success',
					]
				);
		}

		/**
		 * Ajax for updating and creating a new instructor.
		 *
		 * @since 5.9.0
		 */
		public function ajax_add_instructor() {
			$response = [
				'message' => __( 'Some error occurred and the update was not done. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Check if user is admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
				);
			}

			// Verify Nonce.
			if ( ! check_ajax_referer( 'add_instructor_nonce', 'nonce', false ) ) {
				wp_send_json_error( $response );
			}

			$instructor_id         = filter_input( INPUT_POST, 'instructor_id', FILTER_SANITIZE_NUMBER_INT );
			$commission_percentage = filter_input( INPUT_POST, 'commission_percentage', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			$commission            = filter_input( INPUT_POST, 'commission', FILTER_SANITIZE_NUMBER_INT );

			if ( ! empty( $instructor_id ) ) {
				$user = new WP_User( $instructor_id );

				if (
					in_array(
						'administrator',
						$user->roles,
						true
					)
				) {
					wp_send_json_error(
						[
							'message' => __( 'An administrator cannot be converted into an Instructor.', 'wdm_instructor_role' ),
							'type'    => 'error',
						],
						422
					);
				}

				$user->set_role( 'wdm_instructor' );
			} else {
				$username = filter_input( INPUT_POST, 'username' );
				$fname    = filter_input( INPUT_POST, 'fname', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				$lname    = filter_input( INPUT_POST, 'lname', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
				$email    = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
				$password = filter_input( INPUT_POST, 'password' );

				if ( empty( $username ) || empty( $fname ) || empty( $lname ) || empty( $email ) || empty( $password ) && empty( $instructor_id ) ) {
					wp_send_json_error(
						[
							'message' => __( 'Please enter valid input', 'wdm_instructor_role' ),
							'type'    => 'error',
						],
						403
					);
				}

				$args = [
					'user_login' => $username,
					'first_name' => $fname,
					'last_name'  => $lname,
					'user_email' => $email,
					'user_pass'  => $password,
					'role'       => 'wdm_instructor',
				];

				$user = wp_insert_user( $args );

				if ( 1 == $commission && ! empty( $commission_percentage ) ) {
					update_user_meta(
						$user,
						'wdm_commission_percentage',
						Instructor_Role_Commission::normalize_commission_percentage( $commission_percentage, Cast::to_int( $user ) )
					);
					update_user_meta( $user, 'ir_commission_disabled', '' );
				} else {
					update_user_meta( $user, 'ir_commission_disabled', 1 );
				}
				ir_set_settings( 'add_instructor_onboarding', 'step_3' );
				// Return success response.
				wp_send_json_success( $user );
			}

			if ( 1 == $commission && ! empty( $commission_percentage ) ) {
				update_user_meta(
					$instructor_id,
					'wdm_commission_percentage',
					Instructor_Role_Commission::normalize_commission_percentage( $commission_percentage, Cast::to_int( $instructor_id ) )
				);
				update_user_meta( $instructor_id, 'ir_commission_disabled', '' );
			} else {
				update_user_meta( $instructor_id, 'ir_commission_disabled', 1 );
			}

			ir_set_settings( 'add_instructor_onboarding', 'step_3' );

			// Return success response.
			wp_send_json_success(
				[
					'message' => __( 'Instructor is updated', 'wdm_instructor_role' ),
					'type'    => 'success',
				]
			);
		}

		/**
		 * Add dashboard onboarding step on frontend.
		 *
		 * @since 5.9.0
		 */
		public function add_dashboard_onboarding_step() {
			global $post;

			// Return if not admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Check if dashboard launch page.
			if ( ! empty( $post ) && has_block( 'instructor-role/wisdm-tabs', $post ) && 'ir_launch_setup' === ir_filter_input( 'action', INPUT_GET ) ) {
				$redirect_url = add_query_arg(
					[
						'page'       => 'instuctor',
						'tab'        => 'setup',
						'onboarding' => 'step_3',
					],
					admin_url( 'admin.php' )
				);
				?>
				<div class="ir-onboarding-container ir-onboarding-popup-modal" style="display: flex;">
				<span><strong>Note :</strong> <?php esc_html_e( 'Click on edit page to customize the dashboard with Gutenberg or go back to resume to the setup', 'wdm_instructor_role' ); ?></span>
					<button
				class="primary-bg modal-button modal-button-resume-setup ir-primary-btn" onclick="window.location.href='<?php echo admin_url( 'admin.php?page=instuctor&tab=setup' ); ?>'"><?php esc_html_e( 'Resume Setup', 'wdm_instructor_role' ); ?>
				<i class="fa fa-chevron-right" aria-hidden="true"></i></button>
					<button class="edit_frontend_dashboard_page setup-button" id="edit_frontend_dashboard_page">
						<?php esc_html_e( 'Edit Page', 'wdm_instructor_role' ); ?>
					</button>
				</span></div>
				<?php
			}
		}

		/**
		 * Handle edit page onboarding.
		 *
		 * @since 5.9.0
		 */
		public function edit_page_onboarding() {
			$response = [
				'status'  => 'error',
				'message' => __( 'Some error occurred. Please refresh and try again', 'wdm_instructor_role' ),
			];

			if ( ! wp_verify_nonce( ir_filter_input( 'ir_nonce' ), 'ir_complete_dashboard_launch' ) ) {
				echo wp_json_encode( $response );
				wp_die();
			}

			update_option( 'ir_frontend_dashboard_launched', 1 );
			ir_set_settings( 'create_dashboard_onboarding', 'step_3' );

			wp_send_json_success(
				[
					'message' => __( 'Onboarding is done', 'wdm_instructor_role' ),
					'type'    => 'success',
				]
			);
		}
	}
}
