<?php
/**
 * Instructor Profile Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Profile' ) ) {
	/**
	 * Class Instructor Role Comments Module
	 */
	class Instructor_Role_Profile {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
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

		/**
		 * Instructor Profile Query variable
		 *
		 * @var string  $profile_var
		 *
		 * @since 3.5.0
		 */
		protected $profile_var = '';

		/**
		 * Profile introduction section meta key
		 *
		 * @var string  $introduction_settings_meta_key
		 * @since 3.5.0
		 */
		private $introduction_settings_meta_key = '';

		/**
		 * Profile links enable meta key
		 *
		 * @var string  $profile_links_meta_key
		 * @since 3.5.0
		 */
		private $profile_links_meta_key = '';

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->plugin_slug                    = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->profile_var                    = 'ir_instructor_profile';
			$this->introduction_settings_meta_key = 'ir_profile_introduction_data';
			$this->profile_links_meta_key         = 'ir_enable_profile_links';
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 3.5.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Add rewrite rule for instructor profile
		 *
		 * @since 3.5.0
		 */
		public function add_profile_rewrite_rule() {
			add_rewrite_rule(
				'^instructor/([^/]+)/?$',
				'index.php?author_name=$matches[1]&' . "{$this->profile_var}=true",
				'top'
			);
		}

		/**
		 * Add query variables for instructor profile template
		 *
		 * @since 3.5.0
		 *
		 * @param array $vars   Array of query variables.
		 *
		 * @return array        Updated array of query variables.
		 */
		public function add_profile_query_var( $vars ) {
			$vars[] = $this->profile_var;
			return $vars;
		}

		/**
		 * Add instructor profile template
		 *
		 * @since 3.5.0
		 *
		 * @param string $template  Template path to be fetched for the current query.
		 *
		 * @return string           Updated template path for the instructor profile template.
		 */
		public function add_instructor_profile_template( $template ) {
			global $wp_query, $ld_course_grid_assets_needed;

			// Check if instructor profile template.
			if ( $wp_query->is_author() && get_query_var( $this->profile_var ) ) {
				// Get current user details.
				$cur_auth = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );

				// Check if current user is instructor.
				if ( in_array( 'wdm_instructor', $cur_auth->roles ) ) {
					$ld_course_grid_assets_needed = true;
					/**
					 * Filter the instructor profile template
					 *
					 * @param string $template Template file path for the instructor profile.
					 * @param object $cur_auth Instructor user object.
					 *
					 * @since 3.5.6
					 */
					$template = apply_filters( 'ir_filter_instructor_profile_template', INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/profile/ir-instructor-profile.template.php', $cur_auth );
				}
			}
			return $template;
		}

		/**
		 * Enqueue necessary styles and scripts for the profile template.
		 *
		 * @since 3.5.0
		 */
		public function enqueue_profile_assets() {
			global $wp_query;
			// Check if instructor profile template.
			if ( $wp_query->is_author() && get_query_var( $this->profile_var ) ) {
				wp_enqueue_script(
					'ir-script',
					plugins_url( 'js/dist/ir-script.js', __DIR__ ),
					[ 'jquery' ],
					INSTRUCTOR_ROLE_PLUGIN_VERSION,
					true
				);
				wp_enqueue_style(
					'ir-style',
					plugins_url( 'css/ir-script.css', __DIR__ ),
					[],
					INSTRUCTOR_ROLE_PLUGIN_VERSION
				);
				if ( defined( 'WDM_LD_COURSE_VERSION' ) ) {
					if ( ! is_rtl() ) {
						wp_enqueue_style(
							'reviews-css',
							plugins_url( 'public/css/reviews-shortcode.css', RRF_PLUGIN_FILE ),
							[],
							WDM_LD_COURSE_VERSION
						);
					} else {
						wp_enqueue_style(
							'reviews-css',
							plugins_url( 'public/css/rtl/reviews-shortcode.css', RRF_PLUGIN_FILE ),
							[],
							WDM_LD_COURSE_VERSION
						);
					}
				}
			}
		}

		/**
		 * Enqueue necessary styles and scripts for the profile settings.
		 *
		 * @since 3.5.0
		 */
		public function enqueue_profile_settings_assets() {
			global $current_screen;

			// Instructor settings scripts.
			$page_slug = sanitize_title( __( 'LearnDash LMS', 'learndash' ) ) . '_page_instuctor';
			if ( $page_slug === $current_screen->id || empty( $_GET ) || ! array_key_exists( 'page', $_GET ) || 'instuctor' != $_GET['page'] ) {
				wp_enqueue_media();
				wp_enqueue_script( 'ir-profile-admin-script', plugins_url( 'js/ir-profile-admin-script.js', __DIR__ ), [ 'jquery', 'jquery-ui-sortable' ], INSTRUCTOR_ROLE_TXT_DOMAIN, true );
				wp_localize_script(
					'ir-profile-admin-script',
					'ir_fcb_loc',
					[
						'image_url'       => plugins_url( '/images/frontend-course-creator-settings.png', __DIR__ ),
						'admin_image_url' => plugins_url( '/images/frontend-course-creator-admin-settings.png', __DIR__ ),
					]
				);
				wp_enqueue_style( 'ir-profile-admin-styles', plugins_url( 'css/ir-profile-admin-styles.css', __DIR__ ), [], INSTRUCTOR_ROLE_TXT_DOMAIN );

				/**
				 * Filter Introduction settings warning message
				 *
				 * @since 3.5.0
				 *
				 * @param string $invalid_data_warning_msg  Warning message for invalid introduction sections error.
				 */
				$invalid_data_warning_msg = apply_filters(
					'ir_filter_intro_section_warning_msg',
					__( 'One or more of the introduction section setting fields have been left empty, please fix them before saving the changes', 'wdm_instructor_role' )
				);
				wp_localize_script(
					'ir-profile-admin-script',
					'ir_loc',
					[
						'ajax_url'                 => admin_url( 'admin-ajax.php' ),
						'add_section_html'         => $this->get_add_section_html(),
						'warning_span'             => $this->get_warning_span_html(),
						'invalid_data_warning_msg' => $invalid_data_warning_msg,
						'frontend_dashboard_view_settings_nonce' => wp_create_nonce( 'frontend_dashboard_view_settings_nonce' ),
						'is_gutenberg_enabled'     => ir_is_gutenberg_enabled(),
					]
				);
			}

			// Profile/User edit screen scripts.
			if ( 'profile' === $current_screen->id || 'user-edit' === $current_screen->id ) {
				wp_enqueue_script(
					'ir-profile-user-script',
					plugins_url( 'js/ir-profile-user-script.js', __DIR__ ),
					[ 'jquery' ],
					INSTRUCTOR_ROLE_TXT_DOMAIN,
					true
				);
				wp_enqueue_style(
					'ir-profile-user-styles',
					plugins_url( 'css/ir-profile-user-style.css', __DIR__ ),
					[],
					INSTRUCTOR_ROLE_TXT_DOMAIN
				);
			}
		}

		/**
		 * Add Profile settings tab in Instructor Settings
		 *
		 * @since 3.5.0
		 *
		 * @param array  $tabs          Array of tabs.
		 * @param string $current_tab   Current selected instructor tab.
		 */
		public function add_profile_settings_tab( $tabs, $current_tab ) {
			// Check if admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return $tabs;
			}

			// Check if profile tab already exists.
			if ( ! array_key_exists( 'ir-profile', $tabs ) ) {
				$tabs['ir-profile'] = [
					'title'  => __( 'Instructor Settings', 'wdm_instructor_role' ),
					'access' => [ 'admin' ],
					'svg'    => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>',
				];
			}
			return $tabs;
		}

		/**
		 * Display Profile settings for configuring profile settings.
		 *
		 * @since 3.5.0
		 *
		 * @param string $current_tab   Slug of the selected tab in instructor settings.
		 */
		public function add_profile_settings_tab_contents( $current_tab ) {
			// Check if admin and profile tab.
			if ( ! current_user_can( 'manage_options' ) || 'ir-profile' != $current_tab ) {
				return;
			}

			$onboarding_step = ir_filter_input( 'onboarding', INPUT_GET, 'string' );
			ir_set_settings( 'instructor_settings_onboarding_dismissed', 0 );

			if ( false !== $onboarding_step ) {
				ir_set_settings( 'instructor_settings_onboarding', 'step_1' );
			}

			$this->enqueue_student_communication_settings_assets();

			$course_label            = \LearnDash_Custom_Label::get_label( 'course' );
			$ir_enable_profile_links = get_option( $this->profile_links_meta_key, false );

			// Introduction Settings Data.
			$introduction_settings_data = $this->fetch_introduction_settings_data();

			/**
			 * Filter the default introduction settings for the instructor profile
			 *
			 * @since 3.5.0
			 */
			$default_intro_settings_options = apply_filters(
				'ir_filter_default_intro_setting_options',
				[
					'image'     => [
						'-1'                    => __( '-- Select Section Image --', 'wdm_instructor_role' ),
						'irp-education-list'    => __( 'Education', 'wdm_instructor_role' ),
						'irp-achievements-list' => __( 'Achievements', 'wdm_instructor_role' ),
						'irp-custom'            => __( 'Custom Image', 'wdm_instructor_role' ),
					],
					'data_type' => [
						'-1'        => __( '-- Select Data Type --', 'wdm_instructor_role' ),
						'paragraph' => __( 'Paragraph', 'wdm_instructor_role' ),
						'list'      => __( 'List', 'wdm_instructor_role' ),
					],
					'icon'      => [
						'none'             => __( '-- Select Icon --', 'wdm_instructor_role' ),
						'ir-icon-Students' => __( 'Students', 'wdm_instructor_role' ),
						'ir-icon-Trophy'   => __( 'Trophy', 'wdm_instructor_role' ),
						'dashicon'         => __( 'Dashicon', 'wdm_instructor_role' ),
					],
				]
			);

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/profile/settings/ir-profile-settings.template.php',
				[
					'introduction_settings_data'        => $introduction_settings_data,
					'default_intro_settings_options'    => $default_intro_settings_options,
					'course_label'                      => $course_label,
					'enable_profile_links'              => $ir_enable_profile_links,
					'permalink_settings_link'           => home_url() . '/wp-admin/options-permalink.php',
					'wl8_en_inst_mail'                  => ir_get_settings( 'wl8_en_inst_mail' ),
					'wdm_enable_instructor_course_mail' => ir_get_settings( 'wdm_enable_instructor_course_mail' ),
					'wl8_en_inst_commi'                 => ir_get_settings( 'wl8_en_inst_commi' ), // cspell:disable-line .
					'ir_student_communication_check'    => ir_get_settings( 'ir_student_communication_check' ),
					'ir_ld_category_check'              => ir_get_settings( 'ir_ld_category_check' ),
					'enable_tabs_access'                => ir_get_settings( 'enable_tabs_access' ),
					'onboarding_step'                   => $onboarding_step,
					'ir_st_comm_editor_accent_color'    => ir_get_settings( 'stu_com_editor_accent_color' ),
					'ir_st_comm_editor_set_button'      => ir_get_settings( 'stu_com_editor_set_button' ),
					'ir_st_comm_editor_set_popup'       => ir_get_settings( 'stu_com_editor_set_popup' ),
				]
			);
		}

		/**
		 * Fetch introduction settings data
		 *
		 * @return array
		 * @since 3.5.0
		 */
		public function fetch_introduction_settings_data() {
			$introduction_data = get_option( $this->introduction_settings_meta_key, -1 );

			// If meta key not set, set default data.
			if ( -1 === $introduction_data ) {
				$introduction_data = [
					[
						'title'     => __( 'Education', 'wdm_instructor_role' ),
						'image'     => 'irp-education-list',
						'meta_key'  => 'ir_profile_education_list',
						'data_type' => 'list',
						'icon'      => 'ir-icon-Students',
					],
					[
						'title'     => __( 'Achievements', 'wdm_instructor_role' ),
						'image'     => 'irp-achievements-list',
						'meta_key'  => 'ir_profile_achievement_list',
						'data_type' => 'list',
						'icon'      => 'ir-icon-Trophy',
					],
				];

				return $introduction_data;
			}

			/**
			 * Filter fetch introduction settings data
			 *
			 * @since 3.5.0
			 *
			 * @var array       Array of introduction settings data.
			 */
			return apply_filters( 'ir_filter_fetch_introduction_settings_data', maybe_unserialize( $introduction_data ) );
		}

		/**
		 * Get demo HTML to add new section row
		 *
		 * @return string   HTML data to be used to add new section
		 *
		 * @since 3.5.0
		 */
		public function get_add_section_html() {
			$section_html = <<<SECTION
<tr class="ir-profile-settings-row">
    <td>
    <span class="dashicons dashicons-sort">
	<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-grip-vertical" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
	</span>
    </td>
    <td>
        <span id="ir-profile-section-title-{data_id}">{title}</span>
    </td>
    <td id="ir-profile-section-actions-{data_id}" class="ir-right-align">
        <a title="Edit">
            <span data-id="{data_id}" class="dashicons dashicons-admin-tools ir-profile-setting-edit">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
			</span>
        </a>
        <input id="ir-profile-section-data-{data_id}" type="hidden" name="ir_profile_section[{data_id}]" value='{section_data}'>
        <a class="ir-profile-delete-section" title="Delete">
            <span class="dashicons dashicons-trash">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
			</span>
        </a>
    </td>
</tr>
SECTION;
			/**
			 * Filter the add section demo HTML
			 *
			 * @since 3.5.0
			 */
			return apply_filters( 'ir_filter_add_section_html', $section_html );
		}

		/**
		 * Get demo HTML to add warning span
		 *
		 * @return string   HTML data to be used to add warning section
		 *
		 * @since 3.5.0
		 */
		public function get_warning_span_html() {
			$section_html = '<span class="dashicons dashicons-warning" title="' . __( 'Warning', 'wdm_instructor_role' ) . '"></span>';

			/**
			 * Filter the add section demo HTML
			 *
			 * @since 3.5.0
			 */
			return apply_filters( 'ir_filter_warning_section_html', $section_html );
		}

		/**
		 * Save profile settings
		 *
		 * @since 3.5.0
		 */
		public function save_profile_settings() {
			// If not admin then return.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_nonce', $_POST ) || ! wp_verify_nonce( $_POST['ir_nonce'], 'ir_profile_settings_nonce' ) ) {
				return;
			}

			// Validate and get final section data.
			$profile_section_data = $_POST['ir_profile_section'];
			$wdmir_admin_settings = [];
			$ir_admin_settings    = get_option( '_wdmir_admin_settings', [] );

			$wdmir_admin_settings['ir_enable_profile_links'] = '';
			if ( isset( $_POST['ir_enable_profile_links'] ) ) {
				$wdmir_admin_settings['ir_enable_profile_links'] = 1;
			}

			$final_section_data = [];
			foreach ( $profile_section_data as $section_data ) {
				$details = json_decode( stripslashes( $section_data ) );
				// Validate section data.
				if ( ! $this->is_valid_section_data( $details, $final_section_data ) ) {
					continue;
				}

				// Add to final section data list.
				$final_section_data[] = (array) $details;
			}

			$ir_admin_settings = array_merge( $ir_admin_settings, $wdmir_admin_settings );

			// Saving instructor settings option.
			update_option( '_wdmir_admin_settings', $ir_admin_settings );

			// Save data in options.
			update_option( $this->introduction_settings_meta_key, maybe_serialize( $final_section_data ) );
		}

		/**
		 * Validate if section data is valid or invalid
		 *
		 * @since 3.5.0
		 *
		 * @param object $section_data      Section data to be validated.
		 * @param array  $all_section_data  Array of all section data
		 *
		 * @return bool                     False if invalid, else true.
		 */
		public function is_valid_section_data( $section_data, $all_section_data ) {
			// Check if empty title or meta_key.
			if ( empty( $section_data->title ) || empty( $section_data->meta_key ) ) {
				return false;
			}

			// Check if unique meta_key.
			if ( ! empty( $all_section_data ) ) {
				$meta_keys = array_column( $all_section_data, 'meta_key' );
				if ( in_array( $section_data->meta_key, $meta_keys ) ) {
					return false;
				}
			}

			// Check if valid image.
			$valid_image_options = [
				'irp-education-list',
				'irp-achievements-list',
				'irp-custom',
			];

			if ( ! in_array( $section_data->image, $valid_image_options ) || ( 'irp-custom' === $section_data->image && empty( $section_data->custom_image_url ) ) ) {
				return false;
			}

			// Validate data type.
			$valid_data_types = [
				'list',
				'paragraph',
			];

			if ( ! in_array( $section_data->data_type, $valid_data_types ) ) {
				return false;
			}

			// Check if valid icon/dashicon.
			$valid_icon_options = [
				'ir-icon-Students',
				'ir-icon-Trophy',
				'dashicon',
			];

			if ( ! in_array( $section_data->icon, $valid_icon_options ) || ( 'dashicon' === $section_data->icon && empty( $section_data->custom_dashicon ) ) ) {
				return false;
			}

			/**
			 * Filter section data validation
			 *
			 * @since 3.5.0
			 *
			 * @var bool        False if section data is invalid, else true
			 * @var object      Section data object.
			 * @var array       Array of all section data.
			 */
			return apply_filters( 'ir_filter_is_valid_section_data', true, $section_data, $all_section_data );
		}

		/**
		 * Add additional instructor profile fields
		 *
		 * @param object $user      WP_User object of the current user.
		 *
		 * @since 3.5.0
		 */
		public function add_extra_instructor_profile_fields( $user ) {
			// If not instructor or admin then return.
			if ( ! ( wdm_is_instructor() || current_user_can( 'manage_options' ) ) ) {
				return;
			}

			// Get social links data.
			$social_links = get_user_meta( $user->ID, 'ir_profile_social_links', true );
			$social_links = shortcode_atts(
				[
					'facebook' => '',
					'twitter'  => '',
					'youtube'  => '',
				],
				$social_links
			);

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/profile/settings/ir-profile-extra-fields.template.php',
				[
					'social_links' => $social_links,
				]
			);
		}

		/**
		 * Save additional instructor profile fields.
		 *
		 * @param int $user_id      ID of the User.
		 *
		 * @since 3.5.0
		 */
		public function save_extra_instructor_profile_fields( $user_id ) {
			// If not instructor or admin then return.
			if ( ! ( wdm_is_instructor() || current_user_can( 'manage_options' ) ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_social_fields_nonce', $_POST ) || ! wp_verify_nonce( $_POST['ir_social_fields_nonce'], 'ir_profile_extra_fields_nonce' ) ) {
				return;
			}

			// Check if social links data set.
			if ( array_key_exists( 'ir_profile_social_links', $_POST ) && ! empty( $_POST['ir_profile_social_links'] ) ) {
				$social_links = [
					'facebook' => empty( $_POST['ir_profile_social_links']['facebook'] ) ? '' : $_POST['ir_profile_social_links']['facebook'],
					'twitter'  => empty( $_POST['ir_profile_social_links']['twitter'] ) ? '' : $_POST['ir_profile_social_links']['twitter'], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Check it later.
					'youtube'  => empty( $_POST['ir_profile_social_links']['youtube'] ) ? '' : $_POST['ir_profile_social_links']['youtube'], // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Check it later.
				];
			}

			// Update.
			update_user_meta(
				$user_id,
				'ir_profile_social_links',
				$social_links
			);
		}

		/**
		 * Add dynamic introduction section fields
		 *
		 * @param object $user      WP_User object of the current user.
		 *
		 * @since 3.5.0
		 */
		public function add_instructor_introduction_sections( $user ) {
			// If not instructor or admin then return.
			if ( ! ( wdm_is_instructor() || current_user_can( 'manage_options' ) ) ) {
				return;
			}

			// Get introduction sections data.
			$introduction_settings_data = $this->fetch_introduction_settings_data();

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/profile/introduction-sections/ir-profile-introduction-section.template.php',
				[
					'introduction_settings_data' => $introduction_settings_data,
					'instance'                   => $this,
					'userdata'                   => $user,
				]
			);
		}

		/**
		 * Get instructor courses statistics to be displayed on profile page
		 *
		 * @param int $user_id      User ID of the instructor
		 *
		 * @return array            Array of course statistics for the instructor
		 *
		 * @since 3.5.0
		 */
		public static function get_instructor_course_statistics( $user_id ) {
			$course_statistics = [
				'completed_course_per' => 0,
				'courses_offered'      => 0,
				'students_count'       => 0,
			];

			if ( empty( $user_id ) || ! wdm_is_instructor( $user_id ) ) {
				return $course_statistics;
			}

			// Final instructor course list.
			$course_list = ir_get_instructor_complete_course_list( $user_id );

			// No courses yet...
			if ( ! empty( $course_list ) && array_sum( $course_list ) > 0 ) {
				// Get courses offered count.
				$course_statistics['courses_offered'] = count( $course_list );

				// Fetch the list of students in the courses.
				$all_students = [];
				foreach ( $course_list as $course_id ) {
					// Check if trashed course.
					if ( 'trash' === get_post_status( $course_id ) ) {
						--$course_statistics['courses_offered'];
					}

					$students_list = ir_get_users_with_course_access( $course_id, [ 'direct', 'group' ] );

					if ( empty( $students_list ) ) {
						continue;
					}
					$all_students = array_merge( $all_students, $students_list );
				}

				$unique_students_list = array_unique( $all_students );
				// Get enrolled students count.
				$course_statistics['students_count'] = count( $unique_students_list );

				// Get students completed courses percentage.
				$students_completed_courses_count = 0;
				$students_started_courses_count   = 0;
				foreach ( $unique_students_list as $user_id ) {
					$usermeta        = get_user_meta( $user_id, '_sfwd-course_progress', true );
					$course_progress = empty( $usermeta ) ? [] : $usermeta;
					foreach ( $course_progress as $c_id => $progress ) {
						// If not an instructor course, continue.
						if ( ! in_array( $c_id, $course_list ) ) {
							continue;
						}

						// Increment course start count.
						++$students_started_courses_count;

						// Check if course completed.
						if ( learndash_get_course_steps_count( $c_id ) == $progress['completed'] ) {
							++$students_completed_courses_count;
						}
					}
				}

				if ( $students_started_courses_count ) {
					$course_statistics['completed_course_per'] = round( $students_completed_courses_count / ( $students_started_courses_count / 100 ), 2 );
				}
			}

			if ( defined( 'WDM_LD_COURSE_VERSION' ) ) {
				$total_reviews = 0;
				$total_rating  = 0;
				foreach ( $course_list as $instructor_course ) {
					// Rating and Review Details.
					$rating_details = rrf_get_course_rating_details( $instructor_course );
					$total_reviews += $rating_details['total_count'];
					$total_rating  += $rating_details['total_rating'];
				}
				$course_statistics['avg_instructor_rating']    = empty( $total_rating ) ? 0 : round( $total_rating / $total_reviews, 2 );
				$course_statistics['instructor_reviews_count'] = $total_reviews;
			}

			/**
			 * Filter the instructor profile course statistics
			 *
			 * @since 3.5.0
			 *
			 * @param array $course_statistics      Array of instructor course statistics.
			 * @param int   $user_id                User ID of the instructor.
			 */
			return apply_filters( 'ir_filter_profile_course_statistics', $course_statistics, $user_id );
		}

		/**
		 * Display section input on instructor profile page
		 *
		 * @since 3.5.0
		 *
		 * @param int    $user_id       ID of the User.
		 * @param string $data_type     Type of data, default types are 'list' or 'paragraph'
		 * @param string $meta_key      Unique meta key used for storing data.
		 *
		 * @return string               HTML data for displaying the section input.
		 */
		public function display_section_input( $user_id, $data_type, $meta_key ) {
			$input_html = '';

			switch ( $data_type ) {
				case 'list':
					$list_data = maybe_unserialize( get_user_meta( $user_id, $meta_key, 1 ) );
					if ( ! is_array( $list_data ) ) {
						$list_data = [ $list_data ];
					}

					if ( empty( $list_data ) ) {
						$list_data = [ '' ];
					}

					$input_html = ir_get_template(
						INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/profile/introduction-sections/ir-profile-list-input.template.php',
						[
							'list_data' => $list_data,
							'meta_key'  => $meta_key,
						],
						1
					);
					break;

				case 'paragraph':
					$paragraph_data = get_user_meta( $user_id, $meta_key, 1 );
					if ( is_array( $paragraph_data ) ) {
						$paragraph_data = $paragraph_data[0];
					}

					if ( empty( $paragraph_data ) ) {
						$paragraph_data = '';
					}

					$input_html = '<div class="ir-profile-paragraph-container">
                        <div class="ir-profile-input">
                            <textarea name="' . $meta_key . '" class="ir-profile-input-paragraph">' . $paragraph_data . '</textarea>
                        </div>
                    </div>';
					break;
			}

			/**
			 * Filter the display section input box html
			 *
			 * @since 3.5.0
			 *
			 * @param string $input_html        HTML of the input box
			 * @param string $data_type         Data type of the display section. Either list or paragraph by default.
			 * @param string $meta_key          Meta key for the input saved in the database and used as input name.
			 */
			return apply_filters( 'ir_filter_display_section_input', $input_html, $data_type, $meta_key );
		}

		/**
		 * Save dynamic introduction section fields
		 *
		 * @param int $user_id      ID of the User.
		 *
		 * @since 3.5.0
		 */
		public function save_instructor_introduction_sections( $user_id ) {
			// If not instructor or admin then return.
			if ( ! ( wdm_is_instructor() || current_user_can( 'manage_options' ) ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_profile_introduction_section_nonce', $_POST ) || ! wp_verify_nonce( $_POST['ir_profile_introduction_section_nonce'], 'ir_profile_introduction_section_nonce' ) ) {
				return;
			}

			// Get introduction sections data.
			$introduction_settings_data = $this->fetch_introduction_settings_data();

			foreach ( $introduction_settings_data as $section_details ) {
				// Check if section data set.
				if ( array_key_exists( $section_details['meta_key'], $_POST ) && ! empty( $_POST[ $section_details['meta_key'] ] ) ) {
					$section_value = empty( $_POST[ $section_details['meta_key'] ] ) ? '' : $_POST[ $section_details['meta_key'] ];

					/**
					 * Filter introduction section value before it is saved in user meta
					 *
					 * @since 3.5.0
					 *
					 * @param mixed $section_value      Section value to be saved.
					 * @param int   $user_id            User ID of the instructor.
					 * @param array $section_details    Details of the section.
					 */
					$section_value = apply_filters( 'ir_filter_save_introduction_section_value', $section_value, $user_id, $section_details );

					// Update.
					update_user_meta(
						$user_id,
						$section_details['meta_key'],
						$section_value
					);
				}
			}
		}

		/**
		 * Display instructor introduction sections details
		 *
		 * @since 3.5.0
		 *
		 * @param int $user_id      User ID of the instructor.
		 */
		public static function display_instructor_sections( $user_id ) {
			// Get introduction sections settings.
			$introduction_settings_data = maybe_unserialize( get_option( 'ir_profile_introduction_data' ) );

			if ( ! is_array( $introduction_settings_data ) ) {
				$introduction_settings_data = [];
			}

			foreach ( $introduction_settings_data as $section_settings ) {
				$section_data = maybe_unserialize( get_user_meta( $user_id, $section_settings['meta_key'], 1 ) );
				$section_data = empty( $section_data ) ? '' : $section_data;
				$image_style  = '';
				$image_class  = $section_settings['image'];
				if ( 'irp-custom' === $image_class ) {
					$image_class .= ' irp-block-item';
					self::update_profile_section_image_url( $section_settings['custom_image_url'] );
					$image_style = 'style=background-image:url(' . $section_settings['custom_image_url'] . ');';
				}
				$icon_class = $section_settings['icon'];
				if ( 'dashicon' === $icon_class ) {
					$icon_class = 'dashicons ' . $section_settings['custom_dashicon'];
				}
				switch ( $section_settings['data_type'] ) {
					case 'list':
						ir_get_template(
							INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/profile/introduction-sections/ir-profile-list-display.template.php',
							[
								'section_details' => $section_settings,
								'list_data'       => $section_data,
								'image_class'     => $image_class,
								'image_style'     => $image_style,
								'icon_class'      => $icon_class,
							]
						);
						break;
					case 'paragraph':
						ir_get_template(
							INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/profile/introduction-sections/ir-profile-paragraph-display.template.php',
							[
								'section_details' => $section_settings,
								'paragraph_data'  => $section_data,
								'image_class'     => $image_class,
								'image_style'     => $image_style,
								'icon_class'      => $icon_class,
							]
						);
						break;

					default:
						/**
						 * Allow 3rd party plugins to display sections for custom data types.
						 *
						 * @since 3.5.0
						 *
						 * @param int   $user_id            ID of the user.
						 * @param array $section_settings   Settings of the section being rendered.
						 */
						do_action( 'ir_action_display_instructor_sections', $user_id, $section_settings );
						break;
				}
			}
		}

		/**
		 * Filter the ld_course_list shortcode on the profile page to display instructor courses
		 *
		 * @since 3.5.0
		 *
		 * @param array $atts   Array of attributes for the WP_Query.
		 * @param array $attr   Array of attributes for the ld_course_list shortcode.
		 *
		 * @return array        Updated array of attributes for the WP_Query.
		 */
		public function filter_ld_course_list_for_instructors( $atts, $attr ) {
			// Check if current user is instructor and instructor parameter set in ld_course_list attributes.
			if ( is_array( $attr ) && array_key_exists( 'instructor', $attr ) ) {
				$instructor_id = intval( $attr['instructor'] );
				if ( wdm_is_instructor( $instructor_id ) ) {
					$instructor_shared_courses = ir_get_instructor_complete_course_list( $instructor_id );
					if ( empty( $instructor_shared_courses ) ) {
						$instructor_shared_courses = [ 0 ];
					}
					$atts['post__in'] = $instructor_shared_courses;
				}
			}

			return $atts;
		}

		/**
		 * Display instructor ratings and reviews graph
		 *
		 * @since 3.5.0
		 *
		 * @param int $user_id      User ID of the instructor.
		 */
		public static function display_instructor_ratings_graph_section( $user_id ) {
			// Check if instructor and RRF active, if not return.
			if ( ! defined( 'WDM_LD_COURSE_VERSION' ) || ! wdm_is_instructor( $user_id ) ) {
				return;
			}

			// Get instructor course list.
			$instructor_courses = ir_get_instructor_complete_course_list( $user_id );

			// Get ratings and review details for all courses.
			$combined_ratings          = [
				'average_rating' => 0.00,
				'total_count'    => 0,
				'max_stars'      => 5,
				'total_rating'   => 0,
				'rating'         => [
					1 => 0,
					2 => 0,
					3 => 0,
					4 => 0,
					5 => 0,
				],
			];
			$instructor_course_reviews = [];

			foreach ( $instructor_courses as $course_id ) {
				$course_ratings                    = rrf_get_course_rating_details( $course_id );
				$combined_ratings['total_count']  += $course_ratings['total_count'];
				$combined_ratings['total_rating'] += $course_ratings['total_rating'];
				foreach ( $combined_ratings['rating'] as $index => $value ) {
					$combined_ratings['rating'][ $index ] += $course_ratings['rating'][ $index ];
				}
				$instructor_course_reviews[ $course_id ] = rrf_get_all_course_reviews( $course_id );
			}
			$combined_ratings['average_rating'] = empty( $combined_ratings['total_rating'] ) ? 0.00 : floatval( $combined_ratings['total_rating'] ) / floatval( $combined_ratings['total_count'] );
			$combined_ratings['average_rating'] = number_format( $combined_ratings['average_rating'], 2, '.', '' );
			$review_split                       = rrf_get_bar_values( $combined_ratings );

			$rating_args                = [
				'size'         => 'xs',
				'show-clear'   => false,
				'show-caption' => false,
				'readonly'     => true,
			];
			$is_review_comments_enabled = get_option( 'wdm_course_review_setting', 1 );
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/profile/ratings-and-reviews/ir-profile-ratings-and-reviews-section.template.php',
				[
					'combined_ratings'           => $combined_ratings,
					'review_split'               => $review_split,
					'instructor_course_reviews'  => $instructor_course_reviews,
					'course_label'               => \LearnDash_Custom_Label::get_label( 'course' ),
					'rating_args'                => $rating_args,
					'is_review_comments_enabled' => $is_review_comments_enabled,
				]
			);
		}

		/**
		 * Update profile section Image URL
		 *
		 * @param string $original_image_url    Image URL.
		 */
		public static function update_profile_section_image_url( $original_image_url ) {
			$image = wp_get_image_editor( $original_image_url );
			if ( ! is_wp_error( $image ) ) {
				$image_size = $image->get_size();

				if ( $image_size['width'] > 200 || $image_size['height'] > 200 ) {
					$image->resize( 200, 200, false );
					$image->save( $original_image_url );
				}
			}
		}

		/**
		 * Add Instructor profile metabox in the nav menu
		 *
		 * @since 3.5.0
		 */
		public function add_profile_nav_menu_meta_box() {
			add_meta_box(
				'add-instructor-profile-nav-menu',
				__( 'Instructor', 'wdm_instructor_role' ),
				[ $this, 'display_instructor_profile_nav_menu_meta_box' ],
				'nav-menus',
				'side'
			);
		}

		public function display_instructor_profile_nav_menu_meta_box() {
			?>
			<div class="tabs-panel tabs-panel-active">
				<ul>
				</ul>
			</div>
			<?php
		}

		/**
		 * Update buddypress course author links for instructors
		 *
		 * @param string $domain        The domain url to be returned.
		 * @param int    $user_id       ID of the user.
		 * @param string $user_nicename User nicename.
		 * @param string $user_login    User login.
		 *
		 * @return string   $domain     The updated domain url for instructor profile.
		 */
		public function update_bp_course_author_links( $domain, $user_id, $user_nicename, $user_login ) {
			// Check if instructor and if profile links enabled.
			if ( ! wdm_is_instructor( $user_id ) || ! get_option( $this->profile_links_meta_key, false ) ) {
				return $domain;
			}

			// Check if course archive or single.
			if ( ! $this->post_has_courses() ) {
				global $post;
				return $domain;
			}

			$structure = get_option( 'permalink_structure' );
			if ( empty( $structure ) ) {
				$domain = add_query_arg(
					[
						'author'           => $user_id,
						$this->profile_var => 1,
					],
					get_site_url()
				);
			} else {
				$username = get_the_author_meta( 'nicename', $user_id );
				$domain   = get_site_url() . '/instructor/' . rawurlencode( $username );
			}

			return $domain;
		}

		/**
		 * Update elumine theme course author links for instructors
		 *
		 * @param array $author_meta    The author meta.
		 *
		 * @return string $author_meta  The author meta updated with url for instructor profile.
		 */
		public function update_elumine_course_author_links( $author_meta ) {
			$author_id = get_the_author_meta( 'ID' );

			// Check if instructor and if profile links enabled.
			if ( ! wdm_is_instructor( $author_id ) || ! get_option( $this->profile_links_meta_key, false ) ) {
				return $author_meta;
			}

			// Check if course archive or single.
			if ( ! $this->post_has_courses() ) {
				return $author_meta;
			}

			$structure = get_option( 'permalink_structure' );
			if ( empty( $structure ) ) {
				$author_meta['link'] = add_query_arg(
					[
						'author'           => $author_id,
						$this->profile_var => 1,
					],
					get_site_url()
				);
			} else {
				$username            = get_the_author_meta( 'nicename', $author_id );
				$author_meta['link'] = get_site_url() . '/instructor/' . rawurlencode( $username );
			}

			return $author_meta;
		}

		/**
		 * Update buddyboss theme course author links for instructors
		 *
		 * @param array $author_link        The author link.
		 * @param array $author_id          The author ID.
		 * @param array $author_nicename    The author nicename.
		 *
		 * @return string $author_link      The author link updated with url for instructor profile.
		 */
		public function update_buddyboss_course_author_links( $author_link, $author_id, $author_nicename ) {
			// Check if instructor and if profile links enabled.
			if ( ! wdm_is_instructor( $author_id ) || ! get_option( $this->profile_links_meta_key, false ) ) {
				return $author_link;
			}

			// Check if course archive or single.
			if ( ! $this->post_has_courses() ) {
				return $author_link;
			}

			$structure = get_option( 'permalink_structure' );
			if ( empty( $structure ) ) {
				$author_link = add_query_arg(
					[
						'author'           => $author_id,
						$this->profile_var => 1,
					],
					get_site_url()
				);
			} else {
				$author_link = get_site_url() . '/instructor/' . rawurlencode( $author_nicename );
			}

			return $author_link;
		}

		/**
		 * Check whether the current post has any courses or not to show instructor author links.
		 *
		 * @since 3.5.7
		 */
		public function post_has_courses() {
			$has_courses = false;

			// Check if course archive.
			if ( is_post_type_archive( learndash_get_post_type_slug( 'course' ) ) ) {
				$has_courses = true;
			}

			// Check if single course page.
			if ( is_singular( learndash_get_post_type_slug( 'course' ) ) ) {
				$has_courses = true;
			}

			global $post;

			if ( is_a( $post, 'WP_Post' ) && ( preg_match( '/(\[ld_\w+_list)/', $post->post_content ) || preg_match( '/wp:learndash\/ld-course-list/', $post->post_content ) ) ) {
				$has_courses = true;
			}

			if ( is_a( $post, 'WP_Post' ) && learndash_get_post_type_slug( 'course' ) === $post->post_type ) {
				$has_courses = true;
			}

			/**
			 * Check if post has courses to override instructor author links.
			 *
			 * @since 3.5.7
			 *
			 * @param bool $has_courses     True if post has courses, false otherwise.
			 */
			return apply_filters( 'ir_filter_post_has_courses', $has_courses );
		}

		/**
		 * Enqueue student communication settings styles and scripts.
		 *
		 * @since 3.6.0
		 */
		public function enqueue_student_communication_settings_assets() {
			global $current_screen;

			// Instructor settings scripts.
			$page_slug = sanitize_title( __( 'LearnDash LMS', 'learndash' ) ) . '_page_instuctor';
			if ( $page_slug === $current_screen->id && ! empty( $_GET ) && array_key_exists( 'page', $_GET ) && 'instuctor' === $_GET['page'] && array_key_exists( 'tab', $_GET ) && 'ir-profile' === $_GET['tab'] ) {
				// Enqueue color picker.
				wp_enqueue_style( 'wp-color-picker' );

				if ( ! did_action( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}

				wp_enqueue_script(
					'ir-student-communication-settings-script',
					plugins_url( 'js/communication/ir-student-communication-settings-script.js', __DIR__ ),
					[ 'jquery', 'wp-color-picker' ],
					filemtime( plugin_dir_path( __DIR__ ) . '/js/communication/ir-student-communication-settings-script.js' ),
					// filemtime( plugins_dir( 'js/communication/ir-student-communication-settings-script.js', __DIR__ ) ),
					true
				);
			}
		}

		/**
		 * Save student communication settings
		 *
		 * @since 3.6.0
		 */
		public function save_student_communication_settings() {
			// If not admin then return.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_nonce', $_POST ) || ! wp_verify_nonce( ir_filter_input( 'ir_nonce' ), 'ir_stu_com_settings_nonce' ) ) {
				return;
			}

			$wdmir_admin_settings = [];
			$ir_admin_settings    = get_option( '_wdmir_admin_settings', [] );

			$wdmir_admin_settings['enable_tabs_access'] = '';
			if ( isset( $_POST['enable_tabs_access'] ) ) {
				$wdmir_admin_settings['enable_tabs_access'] = 1;
			}

			$wdmir_admin_settings['ir_student_communication_check'] = '';
			if ( isset( $_POST['ir_student_communication_check'] ) ) {
				$wdmir_admin_settings['ir_student_communication_check'] = 1;
			}

			// Save editor accent color.
			$wdmir_admin_settings['stu_com_editor_accent_color'] = ir_filter_input( 'ir_st_comm_editor_accent_color' );
			$wdmir_admin_settings['stu_com_editor_set_button']   = ir_filter_input( 'ir_st_comm_editor_set_button' );
			$wdmir_admin_settings['stu_com_editor_set_popup']    = ir_filter_input( 'ir_st_comm_editor_set_popup' );

			$ir_admin_settings = array_merge( $ir_admin_settings, $wdmir_admin_settings );

			// Saving instructor settings option.
			update_option( '_wdmir_admin_settings', $ir_admin_settings );
		}

		/**
		 * Save instructor features settings
		 *
		 * @since 5.9.0
		 */
		public function save_instructor_features_settings() {
			// If not admin then return.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_nonce', $_POST ) || ! wp_verify_nonce( ir_filter_input( 'ir_nonce' ), 'ir_instructor_features_nonce' ) ) {
				return;
			}

			$wdmir_admin_settings = [];
			$ir_admin_settings    = get_option( '_wdmir_admin_settings', [] );

			$wdmir_admin_settings['review_product'] = '';
			if ( isset( $_POST['review_product'] ) ) {
				$wdmir_admin_settings['review_product'] = 1;
			}

			$wdmir_admin_settings['instructor_mail'] = '';
			if ( isset( $_POST['instructor_mail'] ) ) {
				$wdmir_admin_settings['instructor_mail'] = 1;
			}

			$wdmir_admin_settings['wdm_enable_instructor_course_mail'] = '';
			if ( isset( $_POST['wdm_enable_instructor_course_mail'] ) ) {
				$wdmir_admin_settings['wdm_enable_instructor_course_mail'] = 1;
			}

			$ir_admin_settings = array_merge( $ir_admin_settings, $wdmir_admin_settings );

			// Saving instructor settings option.
			update_option( '_wdmir_admin_settings', $ir_admin_settings );
		}
	}
}

