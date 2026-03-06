<?php
/**
 * Instructor Dashboard Module
 *
 * @since 4.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore instuctor persistant // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Dashboard' ) ) {
	/**
	 * Class Instructor Role Dashboard Module
	 */
	class Instructor_Role_Dashboard {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 4.0
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 4.0
		 */
		protected $plugin_slug = '';

		/**
		 * Block Namespace
		 *
		 * @var string  $block_namespace
		 *
		 * @since 5.4.0
		 */
		protected $block_namespace = '';

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->plugin_slug     = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->block_namespace = 'instructor-role';
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 4.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Enqueue dashboard scripts
		 *
		 * @since 4.0
		 */
		public function enqueue_dashboard_scripts() {
			global $post;

			if ( ! wdm_is_instructor() ) {
				return;
			}
			$layout = ir_get_settings( 'ir_dashboard_layout' );

			if ( 'layout-2' === $layout ) {
				wp_enqueue_style(
					'irb-styles',
					plugins_url( 'css/irb-new-dashboard.css', __DIR__ ),
					[],
					INSTRUCTOR_ROLE_PLUGIN_VERSION
				);
				wp_enqueue_script(
					'irb-new-dashboard-script',
					plugins_url( 'js/dist/irb-new-dashboard.js', __DIR__ ),
					[ 'ir-lib-apex-charts' ]
				);
			} else {
				wp_enqueue_style(
					'irb-styles',
					plugins_url( 'css/irb-admin.css', __DIR__ ),
					[],
					INSTRUCTOR_ROLE_PLUGIN_VERSION
				);
			}

			$screen = get_current_screen();

			if ( learndash_get_post_type_slug( 'course' ) === $screen->id ) {
				$this->add_course_page_scripts();
			}
		}

		/**
		 * Add course page specific scripts
		 *
		 * @since 4.0
		 */
		public function add_course_page_scripts() {
			wp_enqueue_script(
				'ir-dashboard-course-script',
				plugins_url( 'js/dashboard/ir-dashboard-course-script.js', __DIR__ ),
				[ 'jquery' ],
				filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/js/dashboard/ir-dashboard-course-script.js' ),
				1
			);

			$settings = [
				'course_page',
				'learndash_course_builder',
				'sfwd-courses-settings',
			];

			if ( learndash_get_total_post_count( learndash_get_post_type_slug( 'group' ) ) !== 0 ) {
				$settings[] = 'learndash_course_groups';
			}

			/**
			 * Filter the list of settings for progress bar.
			 *
			 * @since 4.0
			 *
			 * @param array $settings   Settings for progress bar.
			 */
			$settings      = apply_filters( 'ir_filter_progress_bar_settings', $settings );
			$settings_text = sprintf(
				// translators: Total settings count.
				__( '_count_ out of %d settings', 'wdm_instructor_role' ),
				count( $settings )
			);

			wp_localize_script(
				'ir-dashboard-course-script',
				'ir_dashboard_loc',
				[
					'settings'      => $settings,
					'settings_text' => $settings_text,
					'step_width'    => floatval( 100 / ( count( $settings ) - 1 ) ),
				]
			);
		}

		/**
		 * Add save and continue section on course page
		 *
		 * @since 4.0
		 */
		public function add_save_and_continue_section() {
			if ( ! wdm_is_instructor() ) {
				return;
			}

			$layout = ir_get_settings( 'ir_dashboard_layout' );

			// Save and continue is part of layout 1, if not set then lets return.
			if ( 'layout-1' !== $layout ) {
				return;
			}

			$screen = get_current_screen();

			$settings = [
				'course_page',
				'learndash_course_builder',
				'sfwd-courses-settings',
			];

			if ( learndash_get_total_post_count( learndash_get_post_type_slug( 'group' ) ) !== 0 ) {
				$settings[] = 'learndash_course_groups';
			}

			/**
			 * Filter the list of settings for progress bar.
			 *
			 * @since 4.0
			 *
			 * @param array $settings   Settings for progress bar.
			 */
			$settings = apply_filters( 'ir_filter_progress_bar_settings', $settings );

			$current_setting = 'course_page';
			if ( ! empty( $_GET ) && isset( $_GET['currentTab'] ) ) {
				$current_setting = $_GET['currentTab'];
			}

			/**
			 * Filter the current setting for progress bar.
			 *
			 * @since 4.0
			 *
			 * @param string $current_setting   Currently active setting.
			 */
			$current_setting = apply_filters( 'ir_filter_progress_bar_current_setting', $current_setting );

			$current_setting_count = 0;
			if ( in_array( $current_setting, $settings ) ) {
				$current_setting_count = array_search( $current_setting, $settings );
			}
			$current_setting_count = intval( $current_setting_count ) + 1;

			if ( learndash_get_post_type_slug( 'course' ) === $screen->id ) {
				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/dashboard/ir-course-footer.template.php',
					[
						'settings_count'        => count( $settings ),
						'current_setting_count' => $current_setting_count,
						'step_width'            => floatval( 100 / ( count( $settings ) - 1 ) ),
					]
				);
			}
		}

		/**
		 * Add appearance settings tab in Instructor Settings
		 *
		 * @since 4.3.0
		 *
		 * @param array  $tabs          Array of tabs.
		 * @param string $current_tab   Current selected instructor tab.
		 */
		public function add_appearance_settings_tab( $tabs, $current_tab ) {
			// Check if admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return $tabs;
			}

			// Check if appearance tab already exists.
			if ( ! array_key_exists( 'ir-appearance', $tabs ) ) {
				$tabs['ir-appearance'] = [
					'title'  => __( 'Appearance', 'wdm_instructor_role' ),
					'access' => [ 'admin' ],
				];
			}
			return $tabs;
		}

		/**
		 * Display Appearance settings for configuring appearance settings.
		 *
		 * @since 4.3.0
		 * @since 5.0.0     Updated hook on which this method is called.
		 */
		public function add_appearance_settings_tab_contents() {
			// Check if admin and appearance tab.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$default_fonts    = self::get_default_fonts();
			$google_fonts     = self::get_google_fonts();
			$dashboard_layout = ir_get_settings( 'ir_dashboard_layout' );

			if ( empty( $dashboard_layout ) ) {
				$dashboard_layout = 'layout-1';
			}

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/dashboard/ir-dashboard-settings-page.template.php',
				[
					'ir_dashboard_layout'                 => $dashboard_layout,
					'ir_color_preset'                     => ir_get_settings( 'ir_color_preset' ),
					'ir_color_preset_2'                   => ir_get_settings( 'ir_color_preset_2' ),
					'ir_primary_color_1'                  => ir_get_settings( 'ir_primary_color_1' ),
					'ir_primary_color_2'                  => ir_get_settings( 'ir_primary_color_2' ),
					'ir_primary_color_3'                  => ir_get_settings( 'ir_primary_color_3' ),
					'ir_primary_color_4'                  => ir_get_settings( 'ir_primary_color_4' ),
					'ir_primary_color_5'                  => ir_get_settings( 'ir_primary_color_5' ),
					'ir_accent_primary_color'             => ir_get_settings( 'ir_accent_primary_color' ),
					'ir_layout_2_primary_color'           => ir_get_settings( 'ir_layout_2_primary_color' ),
					'ir_layout_2_secondary_color'         => ir_get_settings( 'ir_layout_2_secondary_color' ),
					'ir_layout_2_tertiary_color'          => ir_get_settings( 'ir_layout_2_tertiary_color' ),
					'ir_layout_2_accent_color'            => ir_get_settings( 'ir_layout_2_accent_color' ),
					'ir_layout_2_text_color_1'            => ir_get_settings( 'ir_layout_2_text_color_1' ),
					'ir_layout_2_text_color_2'            => ir_get_settings( 'ir_layout_2_text_color_2' ),
					'ir_layout_2_background_color'        => ir_get_settings( 'ir_layout_2_background_color' ),
					'ir_dashboard_header'                 => ir_get_settings( 'ir_dashboard_header' ),
					'ir_dashboard_logo'                   => ir_get_settings( 'ir_dashboard_logo' ),
					'ir_dashboard_logo_2'                 => ir_get_settings( 'ir_dashboard_logo_2' ),
					'ir_example_logo_2'                   => plugins_url( '/modules/media/ir-example-logo.png', INSTRUCTOR_ROLE_BASE ),
					'ir_logo_alignment_2'                 => ir_get_settings( 'ir_logo_alignment_2' ),
					'ir_dashboard_image_background_color' => ir_get_settings( 'ir_dashboard_image_background_color' ),
					'ir_dashboard_image_background_color_2' => ir_get_settings( 'ir_dashboard_image_background_color_2' ),
					'ir_dashboard_text_sub_title'         => ir_get_settings( 'ir_dashboard_text_sub_title' ),
					'ir_dashboard_text_title'             => ir_get_settings( 'ir_dashboard_text_title' ),
					'ir_dashboard_title_label'            => ir_get_settings( 'ir_dashboard_title_label' ),
					'ir_dashboard_font_family'            => ir_get_settings( 'ir_dashboard_font_family' ),
					'ir_dashboard_title_font_color'       => ir_get_settings( 'ir_dashboard_title_font_color' ),
					'ir_dashboard_sub_title_font_color'   => ir_get_settings( 'ir_dashboard_sub_title_font_color' ),
					'ir_dashboard_text_background_color'  => ir_get_settings( 'ir_dashboard_text_background_color' ),
					'ir_lms_label'                        => ir_get_settings( 'ir_lms_label' ),
					'ir_dashboard_font_size'              => ir_get_settings( 'ir_dashboard_font_size' ),
					'google_fonts'                        => $google_fonts,
					'default_fonts'                       => $default_fonts,
					'recommended_fonts'                   => [
						'default'  => 'Open Sans',
						'preset_1' => 'Poppins',
						'preset_2' => 'Nunito',
						'preset_3' => 'Solway', // cspell:disable-line .
						'preset_4' => 'Open Sans',
					],
				]
			);
		}

		/**
		 * Save appearance settings
		 *
		 * @since 4.3.0
		 */
		public function save_appearance_settings() {
			// If not admin then return.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_dashboard_nonce', $_POST ) || ! wp_verify_nonce( filter_input( INPUT_POST, 'ir_dashboard_nonce' ), 'ir_dashboard_settings' ) ) {
				return;
			}

			// Save dashboard settings.
			// Layout 1.
			$ir_color_preset                       = ir_filter_input( 'ir_color_preset' );
			$ir_primary_color_1                    = ir_filter_input( 'ir_primary_color_1' );
			$ir_primary_color_2                    = ir_filter_input( 'ir_primary_color_2' );
			$ir_primary_color_3                    = ir_filter_input( 'ir_primary_color_3' );
			$ir_primary_color_4                    = ir_filter_input( 'ir_primary_color_4' );
			$ir_primary_color_5                    = ir_filter_input( 'ir_primary_color_5' );
			$ir_accent_primary_color               = ir_filter_input( 'ir_accent_primary_color' );
			$ir_dashboard_header                   = ir_filter_input( 'ir_dashboard_header' );
			$ir_dashboard_logo                     = ir_filter_input( 'ir_dashboard_logo' );
			$ir_dashboard_image_background_color   = ir_filter_input( 'ir_dashboard_image_background_color' );
			$ir_dashboard_image_background_color_2 = ir_filter_input( 'ir_dashboard_image_background_color_2' );
			$ir_dashboard_text_title               = ir_filter_input( 'ir_dashboard_text_title' );
			$ir_dashboard_text_sub_title           = ir_filter_input( 'ir_dashboard_text_sub_title' );
			$ir_dashboard_title_font_color         = ir_filter_input( 'ir_dashboard_title_font_color' );
			$ir_dashboard_sub_title_font_color     = ir_filter_input( 'ir_dashboard_sub_title_font_color' );
			$ir_dashboard_text_background_color    = ir_filter_input( 'ir_dashboard_text_background_color' );

			// Layout 2.
			$ir_color_preset_2            = ir_filter_input( 'ir_color_preset_2' );
			$ir_layout_2_primary_color    = ir_filter_input( 'ir_layout_2_primary_color' );
			$ir_layout_2_secondary_color  = ir_filter_input( 'ir_layout_2_secondary_color' );
			$ir_layout_2_tertiary_color   = ir_filter_input( 'ir_layout_2_tertiary_color' );
			$ir_layout_2_accent_color     = ir_filter_input( 'ir_layout_2_accent_color' );
			$ir_layout_2_text_color_1     = ir_filter_input( 'ir_layout_2_text_color_1' );
			$ir_layout_2_text_color_2     = ir_filter_input( 'ir_layout_2_text_color_2' );
			$ir_layout_2_background_color = ir_filter_input( 'ir_layout_2_background_color' );
			$ir_dashboard_logo_2          = ir_filter_input( 'ir_dashboard_logo_2' );
			$ir_logo_alignment_2          = ir_filter_input( 'ir_logo_alignment_2' );
			$ir_dashboard_title_label     = ir_filter_input( 'ir_dashboard_title_label' );
			$ir_dashboard_font_size       = ir_filter_input( 'ir_dashboard_font_size' );

			// Common.
			$ir_dashboard_layout      = ir_filter_input( 'ir_dashboard_layout' );
			$ir_dashboard_font_family = ir_filter_input( 'ir_dashboard_font_family' );
			$ir_lms_label             = ir_filter_input( 'ir_lms_label' );

			ir_set_settings( 'ir_color_preset', $ir_color_preset );
			ir_set_settings( 'ir_color_preset_2', $ir_color_preset_2 );
			ir_set_settings( 'ir_dashboard_layout', $ir_dashboard_layout );
			ir_set_settings( 'ir_primary_color_1', $ir_primary_color_1 );
			ir_set_settings( 'ir_primary_color_2', $ir_primary_color_2 );
			ir_set_settings( 'ir_primary_color_3', $ir_primary_color_3 );
			ir_set_settings( 'ir_primary_color_4', $ir_primary_color_4 );
			ir_set_settings( 'ir_primary_color_5', $ir_primary_color_5 );
			ir_set_settings( 'ir_accent_primary_color', $ir_accent_primary_color );
			ir_set_settings( 'ir_layout_2_primary_color', $ir_layout_2_primary_color );
			ir_set_settings( 'ir_layout_2_secondary_color', $ir_layout_2_secondary_color );
			ir_set_settings( 'ir_layout_2_tertiary_color', $ir_layout_2_tertiary_color );
			ir_set_settings( 'ir_layout_2_accent_color', $ir_layout_2_accent_color );
			ir_set_settings( 'ir_layout_2_text_color_1', $ir_layout_2_text_color_1 );
			ir_set_settings( 'ir_layout_2_text_color_2', $ir_layout_2_text_color_2 );
			ir_set_settings( 'ir_layout_2_background_color', $ir_layout_2_background_color );
			ir_set_settings( 'ir_dashboard_header', $ir_dashboard_header );
			ir_set_settings( 'ir_dashboard_logo', $ir_dashboard_logo );
			ir_set_settings( 'ir_dashboard_logo_2', $ir_dashboard_logo_2 );
			ir_set_settings( 'ir_logo_alignment_2', $ir_logo_alignment_2 );
			ir_set_settings( 'ir_dashboard_image_background_color', $ir_dashboard_image_background_color );
			ir_set_settings( 'ir_dashboard_image_background_color_2', $ir_dashboard_image_background_color_2 );
			ir_set_settings( 'ir_dashboard_text_title', $ir_dashboard_text_title );
			ir_set_settings( 'ir_dashboard_title_label', $ir_dashboard_title_label );
			ir_set_settings( 'ir_dashboard_font_family', $ir_dashboard_font_family );
			ir_set_settings( 'ir_lms_label', $ir_lms_label );
			ir_set_settings( 'ir_dashboard_font_size', $ir_dashboard_font_size );
			ir_set_settings( 'ir_dashboard_text_sub_title', $ir_dashboard_text_sub_title );
			ir_set_settings( 'ir_dashboard_title_font_color', $ir_dashboard_title_font_color );
			ir_set_settings( 'ir_dashboard_sub_title_font_color', $ir_dashboard_sub_title_font_color );
			ir_set_settings( 'ir_dashboard_text_background_color', $ir_dashboard_text_background_color );
		}

		/**
		 * Enqueue scripts for instructor dashboard settings
		 *
		 * @since 4.0
		 */
		public function enqueue_scripts() {
			$screen = get_current_screen();

			// Get current page and tab from URL.
			if ( empty( $_GET ) || ! array_key_exists( 'page', $_GET ) || ! array_key_exists( 'tab', $_GET ) ) {
				return;
			}

			$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$tab  = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

			// Check if instructor settings page and appearance tab.
			if ( 'instuctor' !== $page || 'dashboard_settings' !== $tab ) {
				return;
			}

			if ( ! empty( $screen ) && 'learndash-lms_page_instuctor' === $screen->id ) {
				wp_enqueue_style(
					'ir-backend-dashboard-settings-styles',
					plugins_url( 'css/dashboard/ir-dashboard-settings-styles.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/dashboard/ir-dashboard-settings-styles.css' )
				);

				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script(
					'ir-backend-dashboard-settings-script',
					plugins_url( 'js/dashboard/ir-dashboard-settings-script.js', __DIR__ ),
					[ 'jquery', 'wp-color-picker' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/dashboard/ir-dashboard-settings-script.js' ),
					1
				);
				wp_localize_script(
					'ir-backend-dashboard-settings-script',
					'ir_loc_data',
					[
						'media_title'  => __( 'Select Image', 'wdm_instructor_role' ),
						'media_button' => __( 'Use this image', 'wdm_instructor_role' ),
					]
				);
			}
		}

		/**
		 * Get google fonts in an array
		 *
		 * @since 4.0
		 */
		public static function get_google_fonts() {
			$google_fonts = get_option( 'ir_google_fonts' );
			if ( false === $google_fonts ) {
				$google_fonts = [];
				$font_json    = file_get_contents( INSTRUCTOR_ROLE_ABSPATH . '/modules/media/google-fonts.json' );
				$font_decoded = json_decode( $font_json );
				$fonts        = $font_decoded->items;

				foreach ( $fonts as $key => $value ) {
					array_push( $google_fonts, $value->family );
				}
				update_option( 'ir_google_fonts', $google_fonts );
			}

			return apply_filters( 'ir_filter_google_fonts', $google_fonts );
		}

		/**
		 * Get default web-safe font list
		 *
		 * @since 4.3.0
		 *
		 * @return array    Array of web-safe font list.
		 */
		public static function get_default_fonts() {
			$default_fonts = [
				'Helvetica'           => 'helvetica, sans-serif',
				'Verdana'             => 'verdana, sans-serif',
				'Tahoma'              => 'Tahoma, sans-serif',
				'Trebuchet'           => 'trebuchet ms, sans-serif',
				'Gill Sans'           => 'gill sans, sans-serif',
				'Times New Roman'     => 'times new roman, serif',
				'Georgia'             => 'georgia, serif',
				'Palatino'            => 'palatino, serif',
				'Baskerville'         => 'baskerville, serif',
				'Courier'             => 'courier, monospace',
				'Monaco'              => 'monaco, monospace',
				'Bradley Hand'        => 'bradley hand, cursive',
				'Luminari'            => 'luminari, fantasy', // cspell:disable-line .
				'Comic Sans MS'       => 'comic sans ms, cursive',
				'Optima'              => 'optima, sans-serif',
				'Didot'               => 'didot, serif',
				'American Typewriter' => 'american typewriter, serif',
			];

			return apply_filters( 'ir_filter_get_default_fonts', $default_fonts );
		}

		/**
		 * Add instructor logo on dashboard.
		 *
		 * @since 4.0
		 */
		public function add_instructor_logo() {
			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return;
			}
			// Get active layout.
			$layout                     = ir_get_settings( 'ir_dashboard_layout' );
			$dashboard_background_color = '';

			// Get logo settings.
			if ( 'layout-2' === $layout ) {
				$logo_image_url             = $this->get_new_dashboard_logo( ir_get_settings( 'ir_dashboard_logo_2' ) );
				$ir_logo_alignment          = ir_get_settings( 'ir_logo_alignment_2' );
				$dashboard_background_color = ir_get_settings( 'ir_dashboard_image_background_color_2' );

				/**
				 * A url to logo on the instructor dashboard.
				 *
				 * @since 3.5.6
				 *
				 * @param string A URL to the instructor dashboard logo.
				 */
				$logo_url = apply_filters( 'ir_instructor_dashboard_logo_url', '' );

				$args = [
					'dashboard_logo' => $logo_image_url,
					'logo_url'       => $logo_url,
				];
			} else {
				$header_type = ir_get_settings( 'ir_dashboard_header' );
				if ( 'image' === $header_type ) {
					$ir_dashboard_logo          = ir_get_settings( 'ir_dashboard_logo' );
					$dashboard_background_color = ir_get_settings( 'ir_dashboard_image_background_color' );
					$logo_image_url             = ! empty( $ir_dashboard_logo ) ? esc_attr( wp_get_attachment_image_src( $ir_dashboard_logo )[0] ) : '';
					/**
					 * A url to logo on the instructor dashboard.
					 *
					 * @since 3.5.6
					 *
					 * @param string A URL to the instructor dashboard logo.
					 */
					$logo_url = apply_filters( 'ir_instructor_dashboard_logo_url', '' );

					$args = [
						'dashboard_logo' => $logo_image_url,
						'logo_url'       => $logo_url,
					];
				} elseif ( 'text' === $header_type ) {
					$ir_dashboard_text_title       = ir_get_settings( 'ir_dashboard_text_title' );
					$ir_dashboard_text_sub_title   = ir_get_settings( 'ir_dashboard_text_sub_title' );
					$ir_dashboard_title_font_color = ir_get_settings( 'ir_dashboard_title_font_color' );

					/**
					 * Customize instructor logo title styles.
					 *
					 * @param string $styles    Styles applied to the logo title.
					 */
					$title_font_styles                 = apply_filters(
						'ir_filter_logo_title_styles',
						"font-family: 'Poppins', sans-serif;
						font-size: 26px;
						text-align: center;
						padding: 15px;
						color: {$ir_dashboard_title_font_color};"
					);
					$ir_dashboard_sub_title_font_color = ir_get_settings( 'ir_dashboard_sub_title_font_color' );
					/**
					 * Customize instructor logo subtitle styles.
					 *
					 * @param string $styles    Styles applied to the logo subtitle.
					 */
					$subtitle_font_styles = apply_filters(
						'ir_filter_logo_title_styles',
						"font-family: 'Poppins', sans-serif;
						font-size: 12px;
						text-align: center;
						color: {$ir_dashboard_sub_title_font_color};"
					);

					$dashboard_background_color = ir_get_settings( 'ir_dashboard_text_background_color' );

					$args = [
						'text_title'           => $ir_dashboard_text_title,
						'text_sub_title'       => $ir_dashboard_text_sub_title,
						'title_font_styles'    => $title_font_styles,
						'subtitle_font_styles' => $subtitle_font_styles,
					];
				}
			}

			if ( 'layout-2' === $layout ) {
				$template_path           = INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/dashboard/ir-dashboard-header-layout-2.template.php';
				$dashboard_title         = ir_get_settings( 'ir_dashboard_title_label' );
				$args['dashboard_title'] = empty( $dashboard_title ) ? __( 'Instructor Dashboard', 'wdm_instructor_role' ) : $dashboard_title;
				$args['logo_alignment']  = empty( $ir_logo_alignment ) ? 'center' : $ir_logo_alignment;
			} else {
				$args['header_type'] = $header_type;
				$template_path       = INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/dashboard/ir-dashboard-header.template.php';
			}

			$dashboard_header = ir_get_template(
				$template_path,
				$args,
				true
			);

			// Apply changes through JS.
			?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					$('#adminmenu').prepend(`<?php echo $dashboard_header; ?>`);
					<?php if ( ! empty( $dashboard_background_color ) ) : ?>
						$('.ir-admin-logo').css('background', "<?php echo esc_attr( $dashboard_background_color ); ?>");
					<?php endif; ?>
				});
			</script>
			<?php
		}

		/**
		 * Add accent color styles to instructor dashboard.
		 *
		 * @since 4.0
		 */
		public function add_accent_color_styles() {
			// Check if instructor.
			if ( is_user_logged_in() && wdm_is_instructor() ) {
				$layout        = ir_get_settings( 'ir_dashboard_layout' );
				$preset_colors = [];

				// Check which layout is selected.
				if (
					empty( $layout ) // New customers may not have this setting.
					|| 'layout-2' === $layout
				) {
					$ir_color_preset = ir_get_settings( 'ir_color_preset_2' );

					// If default preset, then no need for custom colors.
					if ( 'default' === $ir_color_preset ) {
						return;
					}

					// Get preset colors.
					$preset_colors = self::get_preset_colors( $ir_color_preset, 2 );

					$template_path = INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/dashboard/ir-accent-color-layout-2-styles.template.php';
				} else {
					$ir_color_preset = ir_get_settings( 'ir_color_preset' );

					// If default preset, then no need for custom colors.
					if ( 'default' === $ir_color_preset ) {
						return;
					}

					// Get preset colors.
					$preset_colors = self::get_preset_colors( $ir_color_preset );

					$template_path = INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/dashboard/ir-accent-color-styles.template.php';
				}

				$custom_styling = ir_get_template(
					$template_path,
					$preset_colors,
					1
				);
				wp_add_inline_style( 'irb-styles', $custom_styling );
			}
		}

		/**
		 * Exclude menu items from the list of post types to restrict access to.
		 *
		 * @since 4.0
		 *
		 * @param array $post_types     Array of post types to be excluded.
		 *
		 * @return array                Updated array of excluded post types.
		 */
		public function exclude_menu_items( $post_types ) {
			if ( wdm_is_instructor() ) {
				$post_types[] = 'nav_menu_item';
			}
			return $post_types;
		}

		/**
		 * Remove default admin bar for instructors
		 *
		 * @since 4.0
		 */
		public function remove_admin_bar() {
			if ( is_user_logged_in() && wdm_is_instructor() ) {
				remove_action( 'in_admin_header', 'wp_admin_bar_render', 0 );
				$layout = ir_get_settings( 'ir_dashboard_layout' );
				if ( 'layout-2' !== $layout ) {
					?>
					<script type="text/javascript">
						jQuery(document).ready(function ($) {
							$('#adminmenu').prepend(`<?php echo $this->get_toggle_menu_button(); ?>`);
						});
					</script>
					<?php
				}
			}
		}

		/**
		 * Add dashboard menu for instructors
		 *
		 * @since 4.0
		 */
		public function add_instructor_menu() {
			if ( wdm_is_instructor() ) {
				$primary_menu_slug = $this->ir_set_primary_menu_handle();

				if ( has_nav_menu( $primary_menu_slug ) ) {
					echo wp_nav_menu(
						[
							'theme_location'  => $primary_menu_slug,
							'menu_id'         => 'ir-primary-menu',
							'container_id'    => 'ir-primary-navigation',
							'container_class' => '',
							'echo'            => false,
						]
					);
				} else {
					/**
					 * Allow third party plugins to update default instructor menu.
					 *
					 * @since 3.3.0
					 */
					$template = apply_filters(
						'ir_filter_default_instructor_menu_path',
						INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/settings/ir-default-instructor-menu.template.php'
					);
					include $template;
				}
			}
		}
		/**
		 * Function gets the name of the theme
		 *
		 * @return string $theme_name
		 * @since 4.2.1
		 */
		public function ir_get_theme_name() {
			$theme_data = wp_get_theme();
			return $theme_data->Name;
		}

		/**
		 * Function to set primary menu handle
		 * Description : If the user has created a menu and assigned it to the "Instructor Role Menu" location, then use
		 * that menu. Otherwise, use the default menu for the theme
		 *
		 * @return string $primary_menu_handle
		 * @since 4.2.1
		 */
		public function ir_set_primary_menu_handle() {
			$theme_name        = $this->ir_get_theme_name();
			$compatible_themes = [
				'Kadence'           => 'primary',
				'Astra'             => 'primary',
				'BuddyBoss Theme'   => 'header-menu',
				'eLumine'           => 'primary',
				'Twenty Twenty'     => 'primary',
				'Twenty Twenty-One' => 'primary',
			];

			/**
			 * Allow 3rd party plugins to alter $compatible_themes variable
			 *
			 * @since 4.2.1
			 */
			$compatible_themes = apply_filters( 'ir_filter_compatible_themes', $compatible_themes );

			// get all nav menus.
			$nav_menus = get_nav_menu_locations();
			if ( isset( $nav_menus['ir-instructor-menu'] ) && 0 !== $nav_menus['ir-instructor-menu'] && ! empty( $nav_menus['ir-instructor-menu'] ) ) {
				$primary_menu_handle = 'ir-instructor-menu';
			} else {
				// check if element exist in the array.
				if ( array_key_exists( $theme_name, $compatible_themes ) ) {
					$primary_menu_handle = $compatible_themes[ $theme_name ];
				} else {
					$primary_menu_handle = '';
				}
			}
			return $primary_menu_handle;
		}

		/**
		 * Render fall back menu if no menu is set
		 *
		 * @since 4.2.1
		 */
		public function ir_display_fallback_menu( array $args = [] ) {
			$latest        = new \WP_Query(
				[
					'post_type'      => 'page',
					'orderby'        => 'menu_order title',
					'order'          => 'ASC',
					'posts_per_page' => 5,
				]
			);
			$page_ids      = wp_list_pluck( $latest->posts, 'ID' );
			$page_ids      = implode( ',', $page_ids );
			$fallback_args = [
				'depth'       => -1,
				'include'     => $page_ids,
				'show_home'   => false,
				'before'      => '',
				'after'       => '',
				'menu_id'     => 'ir-primary-menu',
				'menu_class'  => 'ir-primary-menu',
				'container'   => 'ul',
				'fallback_cb' => 'wp_page_menu',
			];
			?>
			<div id="ir-primary-navigation" class="menu-test-container ir-default-menu">
				<?php
				wp_page_menu( $fallback_args );
				?>
			</div>
			<?php
		}
		/**
		 * Render persistent menu items into the dashboard menu.
		 *
		 * @since 4.2.1
		 */
		public function render_persistant_menu_items( $items ) {
			if ( is_admin() ) {
				$home_url = home_url();
				if ( isset( $_COOKIE['wdm_ir_old_user'] ) ) {
					$switch_back = '<li class="switch-back menu-item menu-item-type-custom menu-item-object-custom"><a href=' . $home_url . '/wp-login.php?action=wdm_ir_switchback_user >' . esc_html__( 'Switch to Admin', 'wdm_instructor_role' ) . '</a></li>';
				} else {
					$switch_back = '';
				}

				$items .= $switch_back . '<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="' . $home_url . '">' . esc_html__( 'Exit Dashboard', 'wdm_instructor_role' ) . '</a></li>';
			} elseif ( wdm_is_instructor() && ir_get_settings( 'wdm_id_ir_dash_pri_menu' ) == 'on' ) {
				// Check if Backend Dashboard Disabled.
				$is_backend_disabled = ir_get_settings( 'ir_disable_backend_dashboard' );

				if ( $is_backend_disabled ) {
					$dashboard_url = get_permalink( get_option( 'ir_frontend_dashboard_page' ) );

					$items .= '<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="' . $dashboard_url . '" class="menu-link">' . esc_html__( 'Instructor Dashboard', 'wdm_instructor_role' ) . '</a></li>';
				} else {
					// Add Instructor Dashboard link.
					$admin_url = admin_url();

					$items .= '<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="' . $admin_url . 'admin.php?page=ir_instructor_overview" class="menu-link">' . esc_html__( 'Instructor Dashboard', 'wdm_instructor_role' ) . '</a></li>';
				}
			}

			return $items;
		}

		/**
		 * Get toggle menu button.
		 *
		 * @since 4.0
		 */
		public function get_toggle_menu_button() {
			$toggle_menu_button = '
			<li id="collapse-menu" class="hide-if-no-js">
				<button type="button" id="collapse-button" aria-label="' . esc_attr__( 'Collapse Main menu' ) . '" aria-expanded="true">
					<span class="irb-icon-side-bar-expand"></span>
				</button>
			</li>';

			/**
			 * Filter the toggle menu button HTML.
			 *
			 * @since 4.0
			 */
			$toggle_menu_button = apply_filters( 'ir_filter_toggle_menu_html', $toggle_menu_button );

			return $toggle_menu_button;
		}

		/**
		 * Instructor Dashboard footer text.
		 *
		 * @since 4.0
		 */
		public function dashboard_footer_text() {
			return __return_empty_string();
		}

		/**
		 * Get preset colors
		 *
		 * @param string $ir_color_preset    The preset key.
		 * @param int    $layout             The layout key. Default set to 1.
		 *
		 * @return array                     List of preset colors.
		 */
		public static function get_preset_colors( $ir_color_preset, $layout = 1 ) {
			$preset_colors = [];

			if ( 2 === $layout ) {
				switch ( $ir_color_preset ) {
					case 'preset_1':
						$preset_colors['primary']      = '#0051F9';
						$preset_colors['secondary']    = '#0F1D3A';
						$preset_colors['tertiary']     = '#03102C';
						$preset_colors['accent']       = '#F26440';
						$preset_colors['text_color_1'] = '#0F2B65';
						$preset_colors['text_color_2'] = '#C4C4C4';
						$preset_colors['background']   = '#E8EBF3';
						break;

					case 'preset_2':
						$preset_colors['primary']      = '#FD9C0F';
						$preset_colors['secondary']    = '#2B1E43';
						$preset_colors['tertiary']     = '#201239';
						$preset_colors['accent']       = '#D757A7';
						$preset_colors['text_color_1'] = '#2C1D7B';
						$preset_colors['text_color_2'] = '#E5E5E5';
						$preset_colors['background']   = '#F7F7F7';
						break;

					case 'preset_3':
						$preset_colors['primary']      = '#E339D8';
						$preset_colors['secondary']    = '#FFDFEE';
						$preset_colors['tertiary']     = '#FFFFFF';
						$preset_colors['accent']       = '#FC9618';
						$preset_colors['text_color_1'] = '#220B6D';
						$preset_colors['text_color_2'] = '#220B6D';
						$preset_colors['background']   = '#F7F7F7';
						break;

					case 'preset_4':
						$preset_colors['primary']      = '#F45E55';
						$preset_colors['secondary']    = '#F4F2EB';
						$preset_colors['tertiary']     = '#FFFFFF';
						$preset_colors['accent']       = '#C89D2A';
						$preset_colors['text_color_1'] = '#22302A';
						$preset_colors['text_color_2'] = '#003722';
						$preset_colors['background']   = '#FFFEF9';
						break;

					case 'custom':
						$preset_colors['primary']      = ir_get_settings( 'ir_layout_2_primary_color' );
						$preset_colors['secondary']    = ir_get_settings( 'ir_layout_2_secondary_color' );
						$preset_colors['tertiary']     = ir_get_settings( 'ir_layout_2_tertiary_color' );
						$preset_colors['accent']       = ir_get_settings( 'ir_layout_2_accent_color' );
						$preset_colors['text_color_1'] = ir_get_settings( 'ir_layout_2_text_color_1' );
						$preset_colors['text_color_2'] = ir_get_settings( 'ir_layout_2_text_color_2' );
						$preset_colors['background']   = ir_get_settings( 'ir_layout_2_background_color' );
						break;

					default:
						$preset_colors['primary']      = '#2067FA';
						$preset_colors['secondary']    = '#FFF1DC';
						$preset_colors['tertiary']     = '#FFFFFF';
						$preset_colors['accent']       = '#F26440';
						$preset_colors['text_color_1'] = '#021768';
						$preset_colors['text_color_2'] = '#021768';
						$preset_colors['background']   = '#F5F3EC';
						break;
				}
			} else {
				switch ( $ir_color_preset ) {
					case 'preset_1':
						$preset_colors['primary']      = '#272847';
						$preset_colors['secondary']    = '#1a1b35';
						$preset_colors['tertiary']     = '#363868';
						$preset_colors['accent']       = '#4553e6';
						$preset_colors['text_color_1'] = '#fff';
						$preset_colors['text_color_2'] = '#ddd';
						break;

					case 'preset_2':
						$preset_colors['primary']      = '#1b4332';
						$preset_colors['secondary']    = '#2d6a4f';
						$preset_colors['tertiary']     = '#40916c';
						$preset_colors['accent']       = '#95d5b2';
						$preset_colors['text_color_1'] = '#fff';
						$preset_colors['text_color_2'] = '#74c69d';
						break;

					case 'preset_3':
						$preset_colors['primary']      = '#caf0f8';
						$preset_colors['secondary']    = '#ade8f4';
						$preset_colors['tertiary']     = '#90e0ef';
						$preset_colors['accent']       = '#0096c7';
						$preset_colors['text_color_1'] = '#023e8a';
						$preset_colors['text_color_2'] = '#03045e';
						break;

					case 'custom':
						$preset_colors['primary']      = ir_get_settings( 'ir_primary_color_1' );
						$preset_colors['secondary']    = ir_get_settings( 'ir_primary_color_2' );
						$preset_colors['tertiary']     = ir_get_settings( 'ir_primary_color_3' );
						$preset_colors['accent']       = ir_get_settings( 'ir_accent_primary_color' );
						$preset_colors['text_color_1'] = ir_get_settings( 'ir_primary_color_4' );
						$preset_colors['text_color_2'] = ir_get_settings( 'ir_primary_color_5' );
						break;

					default:
						$preset_colors['primary']      = '#d0d0d1';
						$preset_colors['secondary']    = '#ffffff';
						$preset_colors['tertiary']     = '#ebebec';
						$preset_colors['accent']       = '#4553e6';
						$preset_colors['text_color_1'] = '#3d404e';
						$preset_colors['text_color_2'] = '#4553e6';
						break;
				}
			}

			/**
			 * Filter preset color list
			 *
			 * @since 4.1.1
			 *
			 * @param array $preset_colors      List of preset colors.
			 * @param string $ir_color_preset   Configured preset option value.
			 * @param int $layout               The layout being used.
			 */
			return apply_filters( 'ir_filter_preset_colors', $preset_colors, $ir_color_preset, $layout );
		}

		/**
		 * Add font settings for instructor dashboard
		 *
		 * @since 4.3.0
		 */
		public function add_font_settings() {
			// Check if instructor.
			if ( is_user_logged_in() && wdm_is_instructor() ) {
				$layout = ir_get_settings( 'ir_dashboard_layout' );

				// Check if layout 2.
				if ( 'layout-2' !== $layout ) {
					return;
				}

				$font_family = ir_get_settings( 'ir_dashboard_font_family' );
				$font_size   = ir_get_settings( 'ir_dashboard_font_size' );

				$custom_styles = '';

				$default_fonts = array_values( self::get_default_fonts() );

				// Check and apply font family.
				if ( ! empty( $font_family ) ) {
					$custom_styles .= "
						body ,body select,body .button, button,.ld__builder--app, .ld__builder-sidebar-widget, .ld-global-header h1, optgroup, #ir-primary-menu *, tbody , table th,table td,table tr, .wdm-button, input, textarea, .apexcharts-canvas *, label *, .components-snackbar-list *{
							font-family: {$font_family} !important;
						}
					";
					if ( ! in_array( $font_family, $default_fonts, true ) && -1 !== $font_family ) {
						wp_enqueue_style(
							'ir-google-fonts',
							'https://fonts.googleapis.com/css?family=' . rawurlencode( $font_family ),
							[],
							strtotime( 'today' )
						);
					}
				}

				// Check and apply Body font size.
				if ( ! empty( $font_size ) && 'large' === $font_size ) {
					$custom_styles .= '
					.wp-menu-name, .wp-menu-image{
						font-size: 18px;
					}
					#adminmenu .wp-submenu a, #adminmenu .wp-submenu li.current a{
						font-size: 16px;
					}
					.irbn-overview-wrap .irbn-overview > h1{
						font-size: 30px;
					}
					.irbn-overview-wrap .irbn-overview .irbn-tiles-wrap .irbn-tile .irbn-tile-right .irbn-tile-value{
						font-size: 42px;
					}
					.irbn-overview-wrap .irbn-overview .irbn-tiles-wrap .irbn-tile .irbn-tile-right .irbn-text{
						font-size: 22px;
					}
					.irbn-overview-wrap .irbn-overview .irbn-charts .irbn-tile .irbn-tile-header, .irbn-overview-wrap .irbn-overview .irbn-sub .irbn-tile .irbn-tile-header{
						font-size: 22px;
					}
					.irbn-overview-wrap select#instructor-courses-select{
						font-size: 16px;
					}
					.irbn-overview-wrap .irbn-overview .irbn-sub .ir-assignment-table-header th, .irbn-overview-wrap .irbn-overview .irbn-sub .ir-assignment-table-body td, .irbn-overview-wrap .irbn-overview .irbn-sub .ir-assignment-table-body td a{
						font-size: 16px;
					}
					.dataTables_wrapper{
						font-size: 16px;
					}
					.no-reports-message{
						font-size: 16px;
					}
					';
				}

				// Add styles.
				if ( ! empty( $custom_styles ) ) {
					wp_add_inline_style( 'irb-styles', $custom_styles );
				}
			}
		}

		/**
		 * Hide LD tab buttons on listing pages.
		 *
		 * @since 4.3.0
		 */
		public function hide_sections_on_dashboard() {
			$screen = get_current_screen();

			if ( ! function_exists( 'learndash_get_post_type_slug' ) ) {
				return;
			}

			$screen_list = [
				'edit-' . learndash_get_post_type_slug( 'course' ),
				'edit-' . learndash_get_post_type_slug( 'lesson' ),
				'edit-' . learndash_get_post_type_slug( 'topic' ),
				'edit-' . learndash_get_post_type_slug( 'quiz' ),
				'edit-' . learndash_get_post_type_slug( 'question' ),
				'edit-' . learndash_get_post_type_slug( 'certificate' ),
				'edit-' . learndash_get_post_type_slug( 'group' ),
				'groups_page_group_admin_page',
				'edit-' . learndash_get_post_type_slug( 'exam' ),
				'edit-' . learndash_get_post_type_slug( 'assignment' ),
				'edit-' . learndash_get_post_type_slug( 'essay' ),
			];

			if ( ! in_array( $screen->id, $screen_list ) ) {
				return;
			}
			$custom_styles = '
				#sfwd-header div.ld-tab-buttons {
					display: none;
				}
				#sfwd-header {
					margin-bottom: 30px;
				}
			';

			wp_add_inline_style( 'irb-styles', $custom_styles );
		}

		/**
		 * Update LMS Label on instructor dashboard.
		 *
		 * @since 4.3.0
		 */
		public function update_instructor_lms_label() {
			$ir_lms_label = ir_get_settings( 'ir_lms_label' );

			// Check if label set and is instructor.
			if ( empty( $ir_lms_label ) || ! wdm_is_instructor() ) {
				return;
			}
			global $menu;

			foreach ( $menu as $key => $menu_item ) {
				// Compare menu item slug with learndash slug.
				if ( 'learndash-lms' === $menu_item[2] ) {
					// Update label.
					$menu[ $key ][0] = esc_html( $ir_lms_label );
				}
			}
		}

		/**
		 * Add mobile icons for instructor dashboard
		 *
		 * @since 4.3.0
		 *
		 * @param string $nav_menu  The HTML content for the navigation menu.
		 * @param object $args      Nav menu arguments.
		 *
		 * @return array            Updated HTML for navigation menu.
		 */
		public function add_mobile_icons_to_dashboard( $nav_menu, $args ) {
			// Check if admin side.
			if ( ! is_admin() ) {
				return $nav_menu;
			}
			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $nav_menu;
			}

			// Check if instructor dashboard menu.
			if ( ! is_object( $args ) || 'ir-primary-navigation' !== $args->container_id ) {
				return $nav_menu;
			}

			$menu_icons_html = '<div class="wdm-mob-menu wdm-admin-menu-show wdm-hidden">
				<span class="dashicons dashicons-menu-alt"></span>
			</div>
			<div class="ir-mob-dashboard-menu">
				<span class="dashicons dashicons-ellipsis"></span>
			</div>';

			// Check if menu object exists.
			if ( ! property_exists( $args, 'menu' ) || ! $args->menu instanceof \WP_Term ) {
				return $nav_menu;
			}

			/**
			 * Filter the menu icons added to the dashboard navigation HTML.
			 *
			 * @since 4.3.0
			 *
			 * @param string $menu_icons_html   The HTML content for the menu icons to be added.
			 * @param string $nav_menu          The HTML content for the navigation menu.
			 * @param object $args              Nav menu arguments.
			 */
			$menu_icons_html = apply_filters( 'ir_filter_mobile_icons_nav_menu_html', $menu_icons_html, $nav_menu, $args );
			$search_string   = '<div id="ir-primary-navigation" class="menu-' . $args->menu->slug . '-container">';
			$nav_menu        = str_replace( $search_string, $search_string . $menu_icons_html, $nav_menu );

			return $nav_menu;
		}

		/**
		 * Add layout classes to the body tag
		 *
		 * @since 4.3.0
		 *
		 * @param string $classes    Space-separated list of CSS classes.
		 * @return string            Updated space-separated list of CSS classes.
		 */
		public function add_layout_classes_to_body( $classes ) {
			if ( ! wdm_is_instructor() ) {
				return $classes;
			}
			$layout = ir_get_settings( 'ir_dashboard_layout' );
			if ( 'layout-2' === $layout && false === strpos( $classes, 'dashboard-layout-2' ) ) {
				$classes .= ' dashboard-layout-2 ';
			} else {
				$classes .= ' dashboard-layout-1 ';
			}
			return $classes;
		}

		/**
		 * Get dashboard logo for new layouts
		 *
		 * @since 4.3.0
		 *
		 * @param string $new_dashboard_logo    Saved setting for new layout dashboard logo.
		 * @return string
		 */
		public function get_new_dashboard_logo( $new_dashboard_logo ) {
			$dashboard_logo_url = '';

			// Check if logo set.
			if ( empty( $new_dashboard_logo ) ) {
				// Set example logo if not set yet.
				$unset_logo = get_option( 'ir_unset_example_logo' );
				if ( false === $unset_logo ) {
					$dashboard_logo_url = plugins_url( '/modules/media/ir-example-logo.png', INSTRUCTOR_ROLE_BASE );
				}
			} else {
				$dashboard_logo_url = esc_attr( wp_get_attachment_image_src( $new_dashboard_logo, 'full' )[0] );
				update_option( 'ir_unset_example_logo', 1 );
			}
			return $dashboard_logo_url;
		}

		/**
		 * Set the layouts to the new layout for new customers.
		 *
		 * @since 4.3.0
		 */
		public function set_new_layouts() {
			$layout = ir_get_settings( 'ir_dashboard_layout' );

			if ( 'layout-2' !== $layout ) {
				ir_set_settings( 'ir_dashboard_layout', 'layout-2' );
			}
		}

		/**
		 * Add frontend dashboard settings tab in Instructor Settings
		 *
		 * @since 4.4.0
		 *
		 * @param array  $tabs          Array of tabs.
		 * @param string $current_tab   Current selected instructor tab.
		 */
		public function add_frontend_dashboard_settings_tab( $tabs, $current_tab ) {
			// Check if admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return $tabs;
			}

			// Check if frontend_dashboard tab already exists.
			if ( ! array_key_exists( 'ir-frontend-dashboard', $tabs ) ) {
				$tabs['ir-frontend-dashboard'] = [
					'title'  => sprintf( /* translators: Course Label */ __( '%s Creation', 'wdm_instructor_role' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
					'access' => [ 'admin' ],
					'svg'    => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-book" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6l0 13" /><path d="M12 6l0 13" /><path d="M21 6l0 13" /></svg>',
				];
			}
			return $tabs;
		}

		/**
		 * Display settings for configuring frontend dashboard settings.
		 *
		 * @since 4.3.0
		 *
		 * @param string $current_tab   Slug of the selected tab in instructor settings.
		 */
		public function add_frontend_dashboard_settings_tab_contents( $current_tab ) {
			// Check if admin and frontend dashboard tab.
			if ( ! current_user_can( 'manage_options' ) || 'ir-frontend-dashboard' !== $current_tab ) {
				return;
			}

			$onboarding_step = ir_filter_input( 'onboarding', INPUT_GET, 'string' );
			ir_set_settings( 'course_creation_onboarding_dismissed', 0 );

			if ( false !== $onboarding_step ) {
				ir_set_settings( 'course_creation_onboarding', 'step_1' );
			}

			$fonts = [
				''           => 'Theme (Default)',
				'Open Sans'  => 'Open Sans',
				'Roboto'     => 'Roboto',
				'Montserrat' => 'Montserrat',
				'Lato'       => 'Lato',
				'Poppins'    => 'Poppins',
				'Inter'      => 'Inter',
			];

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-frontend-dashboard-settings-page.template.php',
				[
					'ir_enable_frontend_dashboard'         => ir_get_settings( 'ir_enable_frontend_dashboard' ),
					'ir_disable_ld_links'                  => ir_get_settings( 'ir_disable_ld_links' ),
					'ir_enable_sync'                       => ir_get_settings( 'ir_enable_sync' ),
					'image_url'                            => plugins_url( '/images/frontend-course-creator-settings.png', __DIR__ ),
					'admin_image_url'                      => plugins_url( '/images/frontend-course-creator-admin-settings.png', __DIR__ ),
					// Appearance settings.
					'ir_frontend_course_creator_color_scheme' => ir_get_settings( 'ir_frontend_course_creator_color_scheme' ),
					'ir_frontend_course_creator_custom_primary' => ir_get_settings( 'ir_frontend_course_creator_custom_primary' ),
					'ir_frontend_course_creator_custom_accent' => ir_get_settings( 'ir_frontend_course_creator_custom_accent' ),
					'ir_frontend_course_creator_custom_background' => ir_get_settings( 'ir_frontend_course_creator_custom_background' ),
					'ir_frontend_course_creator_custom_headings' => ir_get_settings( 'ir_frontend_course_creator_custom_headings' ),
					'ir_frontend_course_creator_custom_text' => ir_get_settings( 'ir_frontend_course_creator_custom_text' ),
					'ir_frontend_course_creator_custom_border' => ir_get_settings( 'ir_frontend_course_creator_custom_border' ),
					'ir_frontend_course_creator_custom_text_light' => ir_get_settings( 'ir_frontend_course_creator_custom_text_light' ),
					'ir_frontend_course_creator_custom_text_ex_light' => ir_get_settings( 'ir_frontend_course_creator_custom_text_ex_light' ),
					'ir_frontend_course_creator_custom_text_primary_btn' => ir_get_settings( 'ir_frontend_course_creator_custom_text_primary_btn' ),
					'fonts'                                => $fonts,
					'ir_frontend_course_creator_font_family' => ir_get_settings( 'ir_frontend_course_creator_font_family' ),
					'ir_frontend_course_creator_font_size' => ir_get_settings( 'ir_frontend_course_creator_font_size' ),
					'enable_ld_category'                   => ir_get_settings( 'enable_ld_category' ),
					'enable_wp_category'                   => ir_get_settings( 'enable_wp_category' ),
					'enable_permalinks'                    => ir_get_settings( 'enable_permalinks' ),
					'enable_elu_header'                    => ir_get_settings( 'enable_elu_header' ),
					'enable_elu_layout'                    => ir_get_settings( 'enable_elu_layout' ),
					'enable_elu_cover'                     => ir_get_settings( 'enable_elu_cover' ),
					'enable_bb_cover'                      => ir_get_settings( 'enable_bb_cover' ),
					'enable_open_pricing'                  => ir_get_settings( 'enable_open_pricing' ),
					'enable_free_pricing'                  => ir_get_settings( 'enable_free_pricing' ),
					'enable_buy_pricing'                   => ir_get_settings( 'enable_buy_pricing' ),
					'enable_recurring_pricing'             => ir_get_settings( 'enable_recurring_pricing' ),
					'enable_closed_pricing'                => ir_get_settings( 'enable_closed_pricing' ),
					'enable_tabs_access'                   => ir_get_settings( 'enable_tabs_access' ),
					'is_ld_category'                       => \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'ld_course_category' ),
					'is_wp_category'                       => \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Courses_Taxonomies', 'wp_post_category' ),
					'active_theme'                         => wp_get_theme()->template,
					'review_course'                        => ir_get_settings( 'review_course' ),
					'onboarding_step'                      => $onboarding_step,
				]
			);
		}

		/**
		 * Save frontend dashboard settings
		 *
		 * @since 4.4.0
		 */
		public function save_frontend_dashboard_settings() {
			// If not admin then return.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_nonce', $_POST ) || ! wp_verify_nonce( filter_input( INPUT_POST, 'ir_nonce' ), 'ir_frontend_dashboard_settings' ) ) {
				return;
			}

			global $wp_rewrite;

			// Save dashboard settings.
			$ir_enable_frontend_dashboard = ir_filter_input( 'ir_enable_frontend_dashboard' );
			$ir_disable_ld_links          = empty( ir_filter_input( 'ir_disable_ld_links' ) ) ? 'off' : 'on';
			$ir_enable_sync               = empty( ir_filter_input( 'ir_enable_sync' ) ) ? 'off' : 'on';

			ir_set_settings( 'ir_enable_sync', $ir_enable_sync );

			// Appearance settings.
			ir_set_settings( 'ir_frontend_course_creator_color_scheme', ir_filter_input( 'ir_frontend_course_creator_color_scheme' ) );
			ir_set_settings( 'ir_frontend_course_creator_custom_primary', ir_filter_input( 'ir_frontend_course_creator_custom_primary' ) );
			ir_set_settings( 'ir_frontend_course_creator_custom_accent', ir_filter_input( 'ir_frontend_course_creator_custom_accent' ) );
			ir_set_settings( 'ir_frontend_course_creator_custom_background', ir_filter_input( 'ir_frontend_course_creator_custom_background' ) );
			ir_set_settings( 'ir_frontend_course_creator_custom_headings', ir_filter_input( 'ir_frontend_course_creator_custom_headings' ) );
			ir_set_settings( 'ir_frontend_course_creator_custom_text', ir_filter_input( 'ir_frontend_course_creator_custom_text' ) );
			ir_set_settings( 'ir_frontend_course_creator_custom_border', ir_filter_input( 'ir_frontend_course_creator_custom_border' ) );
			ir_set_settings( 'ir_frontend_course_creator_custom_text_light', ir_filter_input( 'ir_frontend_course_creator_custom_text_light' ) );
			ir_set_settings( 'ir_frontend_course_creator_custom_text_ex_light', ir_filter_input( 'ir_frontend_course_creator_custom_text_ex_light' ) );
			ir_set_settings( 'ir_frontend_course_creator_custom_text_primary_btn', ir_filter_input( 'ir_frontend_course_creator_custom_text_primary_btn' ) );
			ir_set_settings( 'ir_frontend_course_creator_font_family', ir_filter_input( 'ir_frontend_course_creator_font_family' ) );
			ir_set_settings( 'ir_frontend_course_creator_font_size', ir_filter_input( 'ir_frontend_course_creator_font_size' ) );

			// Syncing with frontend dashboard.
			$ir_enable_sync_check = ir_get_settings( 'ir_enable_sync' );
			if ( 'on' === $ir_enable_sync_check ) {
				$this->sync_with_frontend_dashboard();
			}

			// flush_rewrite_rules.
			if ( ir_get_settings( 'ir_enable_frontend_dashboard' ) && get_option( 'permalink_structure' ) === '/%postname%/' ) {
				$wp_rewrite->set_permalink_structure( '/%postname%/' );

				add_rewrite_rule(
					'^course-builder/([\d]+)/?$',
					'index.php?course_builder=true&ir_course=$matches[1]',
					'top'
				);

				add_rewrite_tag( '%course_builder%', '([^&]+)' );

				add_rewrite_rule(
					'^quiz-builder/([\d]+)/?$',
					'index.php?quiz_builder=true&ir_quiz=$matches[1]',
					'top'
				);

				add_rewrite_tag( '%quiz_builder%', '([^&]+)' );

				add_rewrite_rule(
					'^instructor/([^/]+)/?$',
					'index.php?author_name=$matches[1]&{ir_instructor_profile}=true',
					'top'
				);

				flush_rewrite_rules();
			}
		}

		/**
		 * This method is used to filter the content displayed for Tabs block based on the block setting.
		 *
		 * @param string $block_content Content for the block.
		 * @param array  $block         Parsed Block.
		 * @return string $block_content Conditionally Modified Content.
		 */
		public function load_dashboard_block_content( $block_content, $block ) {
			global $ir_dashboard_module_setting, $post;

			if ( 'instructor-role/wisdm-tab-item' !== $block['blockName'] ) {
				return $block_content;
			}

			$inner_blocks = $block['innerBlocks'];

			// If not instructor or admin, restrict access to unauthorized wisdm blocks.
			if ( ! current_user_can( 'manage_options' ) && ! wdm_is_instructor() ) {
				foreach ( $inner_blocks as $key => $inner_block ) {
					// @todo Uncomment below code to allow groups tab access to group leaders.
					// phpcs:disable
					// If groups block, allow access to group leaders.
					// if ( 'instructor-role/wisdm-groups' === $inner_block['blockName'] && learndash_is_group_leader_user() ) {
					// 	continue;
					// }
					// phpcs:enable

					if ( $this->is_dashboard_block( $inner_block['blockName'] ) ) {
						$block_content = str_replace(
							$inner_block['innerHTML'],
							ir_get_template(
								INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/ir-frontend-dashboard-no-access.template.php',
								[],
								1
							),
							$block_content
						);
					}
				}
			}

			// @todo Improve handling for classic editor flow.
			if ( ( isset( $_GET['tab'] ) ? $_GET['tab'] : 0 ) == $block['attrs']['blockIndex'] ) {
				return $block_content;
			} else {
				return '';
			}
		}

		/**
		 * Enqueue settings pages scripts and styles.
		 *
		 * @since 5.2.0
		 */
		public function enqueue_settings_scripts() {
			global $current_screen;
			// Instructor settings scripts.
			$page_slug = sanitize_title( __( 'LearnDash LMS', 'learndash' ) ) . '_page_instuctor';
			if ( $page_slug === $current_screen->id && ! empty( $_GET ) && array_key_exists( 'page', $_GET ) && 'instuctor' === $_GET['page'] && array_key_exists( 'tab', $_GET ) && 'ir-frontend-dashboard' === $_GET['tab'] ) {
				wp_enqueue_script(
					'ir-frontend-dashboard-settings-script',
					plugins_url( 'js/settings/ir-frontend-course-creator-settings.js', __DIR__ ),
					[ 'jquery' ],
					filemtime( plugin_dir_path( __DIR__ ) . '/js/settings/ir-frontend-dashboard-settings.js' ),
					true
				);
				wp_enqueue_style(
					'ir-frontend-dashboard-settings-styles',
					plugins_url( 'css/dashboard/ir-dashboard-settings-styles.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/dashboard/ir-dashboard-settings-styles.css' )
				);
				wp_localize_script(
					'ir-frontend-dashboard-settings-script',
					'ir_fd_loc',
					[
						'dashboard_colors' => $this->get_dashboard_colors(),
					]
				);
			}
		}

		/**
		 * Get dashboard colors
		 *
		 * @since 5.2.0
		 *
		 * @return object   List of Dashboard colors.
		 */
		public function get_dashboard_colors() {
			$dashboard_default_colors = json_decode(
				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/frontend-dashboard/dashboard-colors.json',
					[],
					1
				)
			);

			$custom_colors = [
				'name'             => 'Custom',
				'primary'          => ir_get_settings( 'ir_frontend_course_creator_custom_primary' ),
				'accent'           => ir_get_settings( 'ir_frontend_course_creator_custom_accent' ),
				'background'       => ir_get_settings( 'ir_frontend_course_creator_custom_background' ),
				'headings'         => ir_get_settings( 'ir_frontend_course_creator_custom_headings' ),
				'text'             => ir_get_settings( 'ir_frontend_course_creator_custom_text' ),
				'border'           => ir_get_settings( 'ir_frontend_course_creator_custom_border' ),
				'text_light'       => ir_get_settings( 'ir_frontend_course_creator_custom_text_light' ),
				'text_ex_light'    => ir_get_settings( 'ir_frontend_course_creator_custom_text_ex_light' ),
				'text_primary_btn' => ir_get_settings( 'ir_frontend_course_creator_custom_text_primary_btn' ),
			];

			$dashboard_default_colors->custom = json_decode( json_encode( $custom_colors ), false );

			return $dashboard_default_colors;
		}

		/**
		 * Function to check if to sync on frontend dashboard page save.
		 *
		 * @since 5.3.0
		 */
		public function check_dashboard_sync( $post_ID, $post, $update ) {
			$post_id              = $post_ID;
			$ir_enable_sync_check = ir_get_settings( 'ir_enable_sync' );
			$dashboard_page_id    = get_option( 'ir_frontend_dashboard_page' );
			if ( $post_id == $dashboard_page_id && 'on' === $ir_enable_sync_check ) {
				$this->sync_with_frontend_dashboard();
			}
		}

		/**
		 * Sync the frontend dashboard with FCC Settings.
		 *
		 * @since 5.3.0
		 */
		public function sync_with_frontend_dashboard() {
			$dashboard_page_id = get_option( 'ir_frontend_dashboard_page' );

			if ( empty( $dashboard_page_id ) ) {
				return;
			}

			$post_content = get_the_content( null, false, $dashboard_page_id );
			$blocks       = parse_blocks( $post_content );
			$attributes   = $blocks[0]['attrs'];

			$color_scheme = array_key_exists( 'colorScheme', $attributes ) ? $attributes['colorScheme'] : '';
			$font_family  = array_key_exists( 'fontFamily', $attributes ) ? $attributes['fontFamily'] : '';
			$font_size    = array_key_exists( 'fontSize', $attributes ) ? $attributes['fontSize'] : '';

			if ( empty( $font_family ) ) {
				$font_family = 'Theme (Default)';
			}
			if ( empty( $font_size ) ) {
				$font_size = '16px';
			}

			switch ( $color_scheme ) {
				case 'wise_pink':
					ir_set_settings( 'ir_frontend_course_creator_color_scheme', 'wise_pink' );
					ir_set_settings( 'ir_frontend_course_creator_custom_primary', '#E339D8' );
					ir_set_settings( 'ir_frontend_course_creator_custom_accent', '#FFEAFE' );
					ir_set_settings( 'ir_frontend_course_creator_custom_background', '#F5EDF5' );
					ir_set_settings( 'ir_frontend_course_creator_custom_headings', '#3C2E3B' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text', '#696769' );
					ir_set_settings( 'ir_frontend_course_creator_custom_border', '#E5D5E4' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_light', '#938392' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_ex_light', '#BCADBB' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_primary_btn', '#ffffff' );
					ir_set_settings( 'ir_frontend_course_creator_font_family', $font_family );
					ir_set_settings( 'ir_frontend_course_creator_font_size', $font_size );
					break;

				case 'royal_purple':
					ir_set_settings( 'ir_frontend_course_creator_color_scheme', 'royal_purple' );
					ir_set_settings( 'ir_frontend_course_creator_custom_primary', '#954FB6' );
					ir_set_settings( 'ir_frontend_course_creator_custom_accent', '#FBF1FF' );
					ir_set_settings( 'ir_frontend_course_creator_custom_background', '#EFE6F3' );
					ir_set_settings( 'ir_frontend_course_creator_custom_headings', '#3F3444' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text', '#636364' );
					ir_set_settings( 'ir_frontend_course_creator_custom_border', '#E8DEED' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_light', '#988D9D' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_ex_light', '#BFB4C5' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_primary_btn', '#ffffff' );
					ir_set_settings( 'ir_frontend_course_creator_font_family', $font_family );
					ir_set_settings( 'ir_frontend_course_creator_font_size', $font_size );
					break;

				case 'friendly_mustang':
					ir_set_settings( 'ir_frontend_course_creator_color_scheme', 'friendly_mustang' );
					ir_set_settings( 'ir_frontend_course_creator_custom_primary', '#FC9618' );
					ir_set_settings( 'ir_frontend_course_creator_custom_accent', '#FFF5EA' );
					ir_set_settings( 'ir_frontend_course_creator_custom_background', '#F4EFE8' );
					ir_set_settings( 'ir_frontend_course_creator_custom_headings', '#3C352E' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text', '#6B6A69' );
					ir_set_settings( 'ir_frontend_course_creator_custom_border', '#E4DDD3' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_light', '#948D84' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_ex_light', '#BDB6AD' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_primary_btn', '#ffffff' );
					ir_set_settings( 'ir_frontend_course_creator_font_family', $font_family );
					ir_set_settings( 'ir_frontend_course_creator_font_size', $font_size );
					break;

				case 'natural_green':
					ir_set_settings( 'ir_frontend_course_creator_color_scheme', 'natural_green' );
					ir_set_settings( 'ir_frontend_course_creator_custom_primary', '#21CF3D' );
					ir_set_settings( 'ir_frontend_course_creator_custom_accent', '#F1FFF3' );
					ir_set_settings( 'ir_frontend_course_creator_custom_background', '#EAF4EC' );
					ir_set_settings( 'ir_frontend_course_creator_custom_headings', '#354538' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text', '#646564' );
					ir_set_settings( 'ir_frontend_course_creator_custom_border', '#D3E9D7' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_light', '#879789' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_ex_light', '#ACBBAF' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_primary_btn', '#ffffff' );
					ir_set_settings( 'ir_frontend_course_creator_font_family', $font_family );
					ir_set_settings( 'ir_frontend_course_creator_font_size', $font_size );
					break;

				case 'custom':
					ir_set_settings( 'ir_frontend_course_creator_color_scheme', 'custom' );
					ir_set_settings( 'ir_frontend_course_creator_custom_primary', $attributes['primary'] );
					ir_set_settings( 'ir_frontend_course_creator_custom_accent', $attributes['sidebar_active_bg'] );
					ir_set_settings( 'ir_frontend_course_creator_custom_background', $attributes['page_bg'] );
					ir_set_settings( 'ir_frontend_course_creator_custom_headings', $attributes['headings'] );
					ir_set_settings( 'ir_frontend_course_creator_custom_text', $attributes['text'] );
					ir_set_settings( 'ir_frontend_course_creator_custom_border', $attributes['border'] );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_light', $attributes['text_light'] );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_ex_light', $attributes['text_extra_light'] );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_primary_btn', $attributes['primary_btn_text'] );
					ir_set_settings( 'ir_frontend_course_creator_font_family', $font_family );
					ir_set_settings( 'ir_frontend_course_creator_font_size', $font_size );
					break;

				default:
					ir_set_settings( 'ir_frontend_course_creator_color_scheme', 'calm_ocean' );
					ir_set_settings( 'ir_frontend_course_creator_custom_primary', '#2067FA' );
					ir_set_settings( 'ir_frontend_course_creator_custom_accent', '#F3F9FB' );
					ir_set_settings( 'ir_frontend_course_creator_custom_background', '#EAEEF4' );
					ir_set_settings( 'ir_frontend_course_creator_custom_headings', '#2E353C' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text', '#666666' );
					ir_set_settings( 'ir_frontend_course_creator_custom_border', '#D6D8E7' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_light', '#868E96' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_ex_light', '#ADB5BD' );
					ir_set_settings( 'ir_frontend_course_creator_custom_text_primary_btn', '#ffffff' );
					ir_set_settings( 'ir_frontend_course_creator_font_family', $font_family );
					ir_set_settings( 'ir_frontend_course_creator_font_size', $font_size );
					break;
			}
		}

		/**
		 * Check whether the block is a wisdm frontend dashboard block.
		 *
		 * @since 5.4.0
		 *
		 * @param string $block_name    Name of the block.
		 * @return boolean              True if is a dashboard block, false otherwise.
		 */
		public function is_dashboard_block( $block_name ) {
			if ( empty( $block_name ) ) {
				return false;
			}

			// Compare block namespace.
			$block_namespace = explode( '/', $block_name );
			if ( $this->block_namespace === $block_namespace[0] ) {
				return true;
			}

			return false;
		}

		/**
		 * Save general course creation settings
		 *
		 * @since 5.9.0
		 */
		public function save_general_course_creation_settings() {
			// If not admin then return.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_nonce', $_POST ) || ! wp_verify_nonce( ir_filter_input( 'ir_nonce' ), 'ir_general_course_creation_nonce' ) ) {
				return;
			}

			$wdmir_admin_settings = [];
			$ir_admin_settings    = get_option( '_wdmir_admin_settings', [] );

			$wdmir_admin_settings['ir_enable_frontend_dashboard'] = 'off';
			if ( isset( $_POST['ir_enable_frontend_dashboard'] ) ) {
				$wdmir_admin_settings['ir_enable_frontend_dashboard'] = 'on';
			}

			$wdmir_admin_settings['ir_enable_sync'] = 'off';
			if ( isset( $_POST['ir_enable_sync'] ) ) {
				$wdmir_admin_settings['ir_enable_sync'] = 'on';
			}

			$wdmir_admin_settings['ir_disable_ld_links'] = 'off';
			if ( isset( $_POST['ir_disable_ld_links'] ) ) {
				$wdmir_admin_settings['ir_disable_ld_links'] = 'on';
			}

			$wdmir_admin_settings['review_course'] = '';
			if ( isset( $_POST['wdmir_review_course'] ) ) {
				$wdmir_admin_settings['review_course'] = 1;
			}

			$wdmir_admin_settings['enable_ld_category'] = 'off';
			if ( isset( $_POST['enable_ld_category'] ) ) {
				$wdmir_admin_settings['enable_ld_category'] = 'on';
			}

			$wdmir_admin_settings['enable_permalinks'] = 'off';
			if ( isset( $_POST['enable_permalinks'] ) ) {
				$wdmir_admin_settings['enable_permalinks'] = 'on';
			}

			$wdmir_admin_settings['ir_ld_category_check'] = '';
			if ( isset( $_POST['ir_ld_category_check'] ) ) {
				$wdmir_admin_settings['ir_ld_category_check'] = 1;
			}

			$wdmir_admin_settings['enable_open_pricing'] = 'off';
			if ( isset( $_POST['enable_open_pricing'] ) ) {
				$wdmir_admin_settings['enable_open_pricing'] = 'on';
			}

			$wdmir_admin_settings['enable_free_pricing'] = 'off';
			if ( isset( $_POST['enable_free_pricing'] ) ) {
				$wdmir_admin_settings['enable_free_pricing'] = 'on';
			}

			$wdmir_admin_settings['enable_buy_pricing'] = 'off';
			if ( isset( $_POST['enable_buy_pricing'] ) ) {
				$wdmir_admin_settings['enable_buy_pricing'] = 'on';
			}

			$wdmir_admin_settings['enable_recurring_pricing'] = 'off';
			if ( isset( $_POST['enable_recurring_pricing'] ) ) {
				$wdmir_admin_settings['enable_recurring_pricing'] = 'on';
			}

			$wdmir_admin_settings['enable_closed_pricing'] = 'off';
			if ( isset( $_POST['enable_closed_pricing'] ) ) {
				$wdmir_admin_settings['enable_closed_pricing'] = 'on';
			}

			$ir_admin_settings = array_merge( $ir_admin_settings, $wdmir_admin_settings );
			// Saving instructor settings option.
			update_option( '_wdmir_admin_settings', $ir_admin_settings );
		}
	}
}
