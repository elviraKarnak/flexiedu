<?php
/**
 * Adds the Settings page.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_AdminPage_Settings
 *
 * Adds the Settings page.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */
class LD_GB_AdminPage_Settings {
	/**
	 * LD_GB_AdminPage_Settings constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_init', [ $this, 'page_actions' ] );
		add_filter( 'ld_gb_admin_page_learndash-gradebook-settings_sections', [ $this, 'page_sections' ] );
		add_action( 'admin_notices', [ $this, 'notify_invalid_scales' ] );

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' ) {
			add_action( 'admin_init', [ $this, 'save_custom_options' ] );
		}

		// Fix specific to the Instructor Roles plugin by WisdmLabs
		add_filter( 'wdmir_set_post_types', [ $this, 'fix_granting_edit_gradebooks_to_instructors' ] ); // cspell: disable-line.
	}

	/**
	 * Registers the plugin's settings.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function register_settings() {
		// "Options not found" page will show up if there are no settings registered, even though nothing is actually
		// saved on the Licensing/Support page
		register_setting( 'learndash_gradebook-licensing', 'ld_gb_dummy' );

		$this->add_settings_fields();
	}

	/**
	 * Adds all settings fields.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	private function add_settings_fields() {
		$settings = $this->get_settings( true );

		foreach ( $settings as $page => $page_settings ) {
			add_settings_section(
				'main',
				null,
				null,
				$page
			);

			foreach ( $page_settings['fields'] as $setting ) {
				$setting['args'] = array_merge(
					[
						'option_field' => true,
						'name'         => $setting['id'],
					],
					$setting['args']
				);

				add_settings_field(
					$setting['id'],
					$setting['label'],
					$setting['callback'],
					$page,
					$setting['section'],
					$setting['args']
				);

				if ( isset( $setting['args']['option_field'] ) && $setting['args']['option_field'] ) {
					// Ensure our options are not autoloaded unnecessarily.
					add_option( ( ( ! isset( $setting['args']['no_init'] ) || ! $setting['args']['no_init'] ) ? 'ld_gb_' : '' ) . $setting['id'], '', '', false );

					register_setting( $page, ( ( ! isset( $setting['args']['no_init'] ) || ! $setting['args']['no_init'] ) ? 'ld_gb_' : '' ) . $setting['id'] );
				}
			}
		}
	}

	/**
	 * Gets an array of all the Settings for the plugin, organized by section
	 *
	 * @param boolean $load_notice  Whether or not to load Admin Notices
	 *
	 * @access public
	 * @since 3.0.0
	 * @return array    Plugin settings
	 */
	public function get_settings( $load_notice = false ) {
		$settings = [];

		// General
		$quickstart_roles = ld_gb_get_quickstart_roles();

		ob_start();
		?>
		<p>
			<?php if ( ld_gb_current_user_match_roles( $quickstart_roles ) ) : ?>
				<a href="<?php echo admin_url( 'index.php?ld_gb_restart_quickstart' ); ?>" class="button">
					<?php _e( 'Click here if you would like to restart the Quickstart guide.', 'learndash-gradebook' ); ?>
				</a>
			<?php endif; ?>
		</p>
		<p>
			<code><?php echo admin_url( 'index.php?ld_gb_restart_quickstart' ); ?></code><br/>
			<span class="description">
				<?php _e( 'Copy this link and give it to any users (who have access) that you would like to have restart the Quickstart guide.', 'learndash-gradebook' ); ?>
			</span>
		</p>
		<?php
		$restart_quickstart_html = ob_get_clean();

		$settings['learndash_gradebook-general'] = [
			'id'        => 'general',
			'tab_label' => _x( 'General', 'General Settings Tab Label', 'learndash-gradebook' ),
			'label'     => _x( 'General Settings', 'General Settings Title', 'learndash-gradebook' ),
			'callback'  => [ __CLASS__, 'section_output' ],
			'fields'    => [
				[
					'id'       => 'quiz_score_type',
					'label'    => sprintf( _x( '%s grades used for calculations', 'Quiz grades used for calculations setting label. %s is Quiz', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'quiz' ) ) .
								ld_gb_get_field_tip( sprintf( _x( 'How the %1$s scores will be determined for students who have re-taken any %2$s. Either the best of all the scores or the most recently taken score.', 'Quiz grades used for calculations settings help text. First %s is Quiz and second %s is Quizzes', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'quiz' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ) ),
					'callback' => 'ld_gb_do_field_radio',
					'section'  => 'main',
					'args'     => [
						'default' => 'best',
						'options' => [
							'best'   => _x( 'Best', '"Best" setting for Quiz grades used for calculations', 'learndash-gradebook' ),
							'recent' => _x( 'Most Recent', '"Most Recent" setting for Quiz grades used for calculations', 'learndash-gradebook' ),
						],
					],
				],

				[
					'id'       => 'grade_display_mode',
					'label'    => _x( 'Grade Display Mode', 'Grade Display Mode setting label', 'learndash-gradebook' ),
					'callback' => 'ld_gb_do_field_radio',
					'section'  => 'main',
					'args'     => [
						'default' => 'letter',
						'options' => [
							'letter'                => _x( 'Letter', '"Letter" setting for Grade Display Mode', 'learndash-gradebook' ),
							'percentage'            => _x( 'Percentage', '"Percentage" setting for Grade Display Mode', 'learndash-gradebook' ),
							'letter_and_percentage' => _x( 'Letter and Percentage', '"Letter and Percentage" setting for Grade Display Mode', 'learndash-gradebook' ),
						],
					],
				],

				[
					'id'       => 'grade_precision',
					'label'    => _x( 'Grade Rounding Precision', 'Grade Rounding Precision setting label', 'learndash-gradebook' ) .
								ld_gb_get_field_tip( _x( 'The number of decimal places a grade will round to. Setting this to -1 will allow you to round to the tens place.', 'Grand Rounding Precision help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_number',
					'section'  => 'main',
					'args'     => [
						'default' => 0,
						'min'     => '-1',
						'max'     => '10',
					],
				],

				[
					'id'       => 'grade_round_mode',
					'label'    => _x( 'Grade Rounding Mode', 'Grade Rounding Mode setting label', 'learndash-gradebook' ),
					'callback' => 'ld_gb_do_field_radio',
					'section'  => 'main',
					'args'     => [
						'default' => 'ceil',
						'options' => [
							'ceil'  => _x( 'Round Up', '"Round Up" setting for Grade Rounding Mode', 'learndash-gradebook' ),
							'floor' => _x( 'Round Down', '"Round Down" setting for Grade Rounding Mode', 'learndash-gradebook' ),
							'round' => _x( 'Closest', '"Closest" setting for Grade Rounding Mode', 'learndash-gradebook' ),
						],
					],
				],
				[
					'id'       => 'max_percentage_grade',
					'label'    => _x( 'Max Percentage Grade: 100%', 'Max Percentage Grade: 100% setting label', 'learndash-gradebook' ) .
						ld_gb_get_field_tip( _x( 'Use a percentage-based grading scale where the maximum possible score is 100%, even when actual grade is above 100%.', 'Max Percentage Grade: 100% help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_toggle',
					'section'  => 'main',
					'args'     => [
						'checked_value' => 'yes',
					],
				],
				[
					'id'       => 'assignment_grade_lesson_name',
					'label'    => sprintf( _x( 'Use the containing %1$s/%2$s name for %3$s in Gradebooks', 'Use the containing Topic/Lesson name for Assignments in Gradebooks setting label. First %s is Topic, second %s is Lesson, and third %s is Assignments', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topic' ), LearnDash_Custom_Label::get_label( 'lesson' ), LearnDash_Custom_Label::get_label( 'assignments' ) ) .
								ld_gb_get_field_tip( _x( 'This will not apply when editing a Gradebook.', 'Use Containing Topic/Lesson name for Assignments in Gradebooks help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_toggle',
					'section'  => 'main',
					'args'     => [
						'checked_value' => 'yes',
					],
				],

				[
					'id'       => 'user_columns',
					'label'    => _x( 'Which columns should be shown for the Gradebook?', 'Which columns should be shown for the Gradebook? setting label', 'learndash-gradebook' ) . ld_gb_get_field_tip( _x( 'The Overall Grade and Component columns will always be shown', 'Which columns should be shown for the Gradebook? help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_repeater',
					'section'  => 'main',
					'args'     => [
						'sortable'               => true,
						'first_item_undeletable' => true,
						'default'                => [
							[
								'column' => 'display_name',
							],
						],
						'fields'                 => [
							'column' => [
								'type' => 'select',
								'args' => [
									'label'           => _x( 'Column', 'Column setting label for Which columns should be shown for the Gradebook?', 'learndash-gradebook' ),
									'select2_disable' => true,
									'options'         => [
										'display_name' => _x( 'Display Name', '"Display Name" selection for a column with the Which Columns should be shown for the Gradebook? setting', 'learndash-gradebook' ),
										'first_name'   => _x( 'First Name', '"First Name" selection for a column with the Which Columns should be shown for the Gradebook? setting', 'learndash-gradebook' ),
										'last_name'    => _x( 'Last Name', '"Last Name" selection for a column with the Which Columns should be shown for the Gradebook? setting', 'learndash-gradebook' ),
										'user_email'   => _x( 'Email Address', '"Email Address" selection for a column with the Which Columns should be shown for the Gradebook? setting', 'learndash-gradebook' ),
										'user_login'   => _x( 'Username', '"Username" selection for a column with the Which Columns should be shown for the Gradebook? setting', 'learndash-gradebook' ),
									],
								],
							],
						],
					],
				],

				[
					'id'       => 'disable_manual_grades',
					'label'    => _x( 'Disable Manual Grades', 'Disable Manual Grades setting label', 'learndash-gradebook' ) .
								ld_gb_get_field_tip( _x( 'If you do not want to allow Manual Grades to be entered, turn this option on.', 'Disable Manual Grades help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_toggle',
					'section'  => 'main',
					'args'     => [
						'checked_value' => 'yes',
					],
				],

				[
					'id'       => 'disable_component_override',
					'label'    => _x( 'Disable Component Grade Override', 'Disable Component Grade Override setting label', 'learndash-gradebook' ) .
								ld_gb_get_field_tip( _x( 'If you do not want to allow overriding the Grade of a Component, turn this option on.', 'Disable Component Grade Override help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_toggle',
					'section'  => 'main',
					'args'     => [
						'checked_value' => 'yes',
					],
				],

				[
					'id'       => 'gradebook_disable_sorting_by_grades_backend',
					'label'    => _x( 'Disable Sorting by Grades for the Backend Gradebook', 'Disable Sorting by Grades for the Backend Gradebook setting label', 'learndash-gradebook' ) .
								ld_gb_get_field_tip( _x( 'If you are having issues loading the Gradebook, try enabling this. This was previously named "Gradebook Safe Mode".', 'Disable Sorting by Grades for the Backend Gradebook help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_toggle',
					'section'  => 'main',
					'args'     => [
						'checked_value' => 'yes',
					],
				],

				[
					'id'       => 'gradebook_non_group_leaders_show_only_group_users',
					'label'    => sprintf( _x( 'Non-Group Leaders only have their own %s Users in their Gradebook', 'Non-Group Leaders only have their own Group Users in their Gradebook. The %s is Group', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'group' ) ) .
								ld_gb_get_field_tip( sprintf( _x( 'If you have a lot of Users on your website you may want to enable this. Otherwise, Admins may be unable to access the backend. By default, non-Group Leaders will be calculating grade data for all Students across every %1$s at once. This setting will not take effect if no %2$s exist on this site.', 'Non-Group Leaders only have their own Group Users in their Gradebook help text. First %s is Group, second %s is Groups', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'group' ), LearnDash_Custom_Label::get_label( 'groups' ) ) ),
					'callback' => 'ld_gb_do_field_toggle',
					'section'  => 'main',
					'args'     => [
						'checked_value' => 'yes',
					],
				],

				[
					'id'       => 'gradebook_restart_quickstart',
					'label'    => _x( 'Restart Quickstart Guide', 'Restart Quickstart Guide setting label', 'learndash-gradebook' ),
					'callback' => 'ld_gb_do_field_html',
					'section'  => 'main',
					'args'     => [
						'html'         => $restart_quickstart_html,
						'option_field' => false,
					],
				],
			],
		];

		// Roles
		$quickstart_roles = ld_gb_get_quickstart_roles();

		$roles = get_editable_roles();
		$roles = array_filter(
			$roles,
			function ( $role, $id ) {
				return $id !== 'subscriber';
			},
			ARRAY_FILTER_USE_BOTH
		);

		$all_roles            = [];
		$gradebook_roles      = [];
		$edit_gradebook_roles = [];
		$ld_capable_roles     = [];

		foreach ( $roles as $role_ID => $role ) {
			$all_roles[ $role_ID ] = $role['name'];

			if ( isset( $role['capabilities']['view_gradebook'] ) ) {
				$gradebook_roles[] = $role_ID;
			}

			if ( isset( $role['capabilities']['edit_gradebooks'] ) ) {
				$edit_gradebook_roles[] = $role_ID;
			}

			// Edit Courses is used as the user must be able to view the proper pages during the Quickstart Guide
			if ( isset( $role['capabilities']['edit_courses'] ) ) {
				// These have to stay hard-coded as we're working around a LearnDash Core Capability
				if ( in_array( $role_ID, [ 'administrator', 'group_leader' ] ) ) {
					$ld_capable_roles[ $role_ID ] = sprintf(
						'%s (cannot disable)',
						$role['name']
					);

					continue;
				}

				$ld_capable_roles[ $role_ID ] = $role['name'];
			}
		}

		// Holds an Array with the Role as the Key and the default granted Caps as the Values
		$default_ld_gb_caps = ld_gb_get_capabilities();

		$roles_with_view_gradebook  = [];
		$roles_with_edit_gradebooks = [];

		// Determine which Roles should be excluded from being shown on the Settings Screen
		foreach ( $default_ld_gb_caps as $role => $caps ) {
			if ( in_array( 'view_gradebook', $caps ) ) {
				$roles_with_view_gradebook[] = $role;
			}

			if ( in_array( 'edit_gradebooks', $caps ) ) {
				$roles_with_edit_gradebooks[] = $role;
			}
		}

		$ld_non_capable_roles      = array_diff_key( $all_roles, array_flip( $roles_with_view_gradebook ) );
		$ld_non_capable_roles_edit = array_diff_key( $all_roles, array_flip( $roles_with_edit_gradebooks ) );

		$settings['learndash_gradebook-roles'] = [
			'id'       => 'roles',
			'label'    => _x( 'Roles', 'Roles Settings Title', 'learndash-gradebook' ),
			'callback' => [ __CLASS__, 'section_output' ],
			'fields'   => [
				[
					'id'       => 'gradebook_roles',
					'label'    => _x( 'View the Gradebook', 'View the Gradebook capability label', 'learndash-gradebook' ) .
								ld_gb_get_field_tip( _x( 'Allow extra roles to view the Gradebook. Administrators and Group Leaders are always able to view the Gradebook.', 'View the Gradebook help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_checkbox',
					'section'  => 'main',
					'args'     => [
						'options'       => $ld_non_capable_roles,
						'value'         => $gradebook_roles,
						'multiple'      => true,
						'role_selector' => true,
					],
				],

				[
					'id'       => 'edit_gradebook_roles',
					'label'    => _x( 'Create and Edit their own Gradebooks', 'Create and Edit their own Gradebooks capability label', 'learndash-gradebook' ) .
								ld_gb_get_field_tip( _x( 'Allow extra roles to edit and create their own Gradebooks. Administrators are able to edit any Gradebook.', 'Create and Edit their own Gradebooks help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_checkbox',
					'section'  => 'main',
					'args'     => [
						'options'       => $ld_non_capable_roles_edit,
						'value'         => $edit_gradebook_roles,
						'multiple'      => true,
						'role_selector' => true,
					],
				],

				[
					'id'       => 'quickstart_roles',
					'label'    => _x( 'Quickstart Guide Visibility', 'Quickstart Guide Visibility capability label', 'learndash-gradebook' ) .
								ld_gb_get_field_tip( _x( 'Who can see the Quickstart Guide. NOTE: Some roles are not listed because they do not have enough capability to view LearnDash pages.', 'Quickstart Guide Visibility help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_checkbox',
					'section'  => 'main',
					'args'     => [
						'options'       => $ld_capable_roles,
						'value'         => $quickstart_roles,
						'multiple'      => true,
						'role_selector' => true,
					],
				],
			],
		];

		// Styles
		$letter_grade_scale = ld_gb_get_option_field( 'letter_grade_scale', ld_gb_get_default_letter_grade_scale() );

		if ( ! is_array( $letter_grade_scale ) ) {
			$letter_grade_scale = ld_gb_get_default_letter_grade_scale();
		}

		$letter_grade_scale = learndash_gradebook_sanitize_grade_style_settings( $letter_grade_scale );

		$grade_color_scale = ld_gb_get_option_field( 'grade_color_scale', ld_gb_get_default_grade_color_scale() );

		if ( ! is_array( $grade_color_scale ) ) {
			$grade_color_scale = ld_gb_get_default_grade_color_scale();
		}

		$grade_color_scale = learndash_gradebook_sanitize_grade_style_settings( $grade_color_scale );

		$settings['learndash_gradebook-styles'] = [
			'id'       => 'styles',
			'label'    => _x( 'Styles', 'Settings Title', 'learndash-gradebook' ),
			'callback' => [ __CLASS__, 'section_output' ],
			'fields'   => [
				[
					'id'       => 'letter_grade_scale',
					'label'    => _x( 'Letter Grade Scale', 'Letter Grade Scale setting label', 'learndash-gradebook' ) .
								ld_gb_get_field_tip( _x( 'On save, this will automatically be sorted by grade.', 'Letter Grade Scale help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_repeater',
					'section'  => 'main',
					'args'     => [
						'value'    => $letter_grade_scale,
						'default'  => ld_gb_get_default_letter_grade_scale(),
						'sortable' => false,
						'fields'   => [
							'grade'  => [
								'type' => 'number',
								'args' => [
									'min'     => 0,
									'postfix' => '%',
								],
							],
							'letter' => [
								'type' => 'text',
							],
						],
					],
				],

				[
					'id'       => 'grade_color_scale',
					'label'    => _x( 'Grade Color Scale', 'Grade Color Scale setting label', 'learndash-gradebook' ) .
								ld_gb_get_field_tip( _x( 'On save, this will automatically be sorted by grade.', 'Grade Color Scale help text', 'learndash-gradebook' ) ),
					'callback' => 'ld_gb_do_field_repeater',
					'section'  => 'main',
					'args'     => [
						'value'    => $grade_color_scale,
						'default'  => ld_gb_get_default_grade_color_scale(),
						'sortable' => false,
						'fields'   => [
							'grade' => [
								'type' => 'number',
								'args' => [
									'min'     => 0,
									'postfix' => '%',
								],
							],
							'color' => [
								'type' => 'colorpicker',
							],
						],
					],
				],
			],
		];

		/**
		 * All plugin settings fields displayed on the setting page.
		 *
		 * @since 1.2.0
		 */
		$settings = apply_filters( 'ld_gb_settings_fields', $settings );

		return $settings;   }

	/**
	 * Loads on the Gradebook Settings page only.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function page_actions() {
		if ( ! isset( $_GET['page'] ) || $_GET['page'] != 'learndash-gradebook-settings' ) {
			return;
		}

		add_filter( 'rbm_fieldhelpers_load_select2', '__return_true' );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'settings_page_scripts' ] );
	}

	/**
	 * This page's sections.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @return array
	 */
	function page_sections() {
		$sections = $this->get_settings();

		/**
		 * Sections for the settings page.
		 *
		 * @since 1.2.0
		 */
		$sections = apply_filters( 'ld_gb_settings_page_sections', $sections );

		return $sections;
	}

	/**
	 * Loads scripts on this page.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	static function settings_page_scripts() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' ); }

	/**
	 * Saves any custom options on form submit.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function save_custom_options() {
		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] == 'learndash_gradebook-styles' ) {
			check_admin_referer( 'learndash_gradebook-styles-options' );

			$this->save_letter_grade_scale();
			$this->save_grade_scale_colors();
		}

		if ( isset( $_POST['option_page'] ) && $_POST['option_page'] == 'learndash_gradebook-roles' ) {
			check_admin_referer( 'learndash_gradebook-roles-options' );

			$this->save_gradebook_roles();
		}
	}

	/**
	 * Removes options for WP to save so that I can save them with custom methods.
	 *
	 * @since 1.1.0
	 * @deprecated 4.3.2
	 *
	 * @param array $options Options to be saved.
	 *
	 * @return array
	 */
	static function remove_style_settings( $options ) {
		_deprecated_function( __METHOD__, '4.3.2' );

		if ( ( $key = array_search( 'ld_gb_letter_grade_scale', $options['learndash_gradebook-styles'] ) ) !== false ) {
			unset( $options['learndash_gradebook-styles'][ $key ] );
		}

		if ( ( $key = array_search( 'ld_gb_grade_color_scale', $options['learndash_gradebook-styles'] ) ) !== false ) {
			unset( $options['learndash_gradebook-styles'][ $key ] );
		}

		return $options;
	}

	/**
	 * Saves the letter grade scale.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function save_letter_grade_scale() {
		if ( ! isset( $_POST['ld_gb_letter_grade_scale'] ) ) {
			delete_option( 'ld_gb_letter_grade_scale' );

			return;
		}

		// Sort
		usort(
			$_POST['ld_gb_letter_grade_scale'],
			function ( $a, $b ) {
				return $a['grade'] < $b['grade'];
			}
		);

		update_option( 'ld_gb_letter_grade_scale', $_POST['ld_gb_letter_grade_scale'] );
	}

	/**
	 * Saves the grade scale styles.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function save_grade_scale_colors() {
		if ( ! isset( $_POST['ld_gb_grade_color_scale'] ) ) {
			delete_option( 'ld_gb_grade_color_scale' );

			return;
		}

		// Sort
		usort(
			$_POST['ld_gb_grade_color_scale'],
			function ( $a, $b ) {
				return $a['grade'] < $b['grade'];
			}
		);

		update_option( 'ld_gb_grade_color_scale', $_POST['ld_gb_grade_color_scale'] );
	}

	/**
	 * Updates roles/capabilities for viewing the Gradebook.
	 *
	 * @since 1.1.0
	 * @access private
	 */
	function save_gradebook_roles() {
		$roles                = get_editable_roles();
		$gradebook_roles      = isset( $_POST['ld_gb_gradebook_roles'] ) ? $_POST['ld_gb_gradebook_roles'] : [];
		$edit_gradebook_roles = isset( $_POST['ld_gb_edit_gradebook_roles'] ) ? $_POST['ld_gb_edit_gradebook_roles'] : [];

		// Holds the Caps necessary to Edit Gradebooks
		$edit_gradebook_caps = $this->get_edit_gradebook_capabilities();

		// Holds any default LD GB Caps, important for preserving the Group Leader's normal abilities after revoking Edit Gradebooks
		$default_ld_gb_caps = ld_gb_get_capabilities();

		// This is used in the loop to effectively whitelist any special Caps that a Role may have by default
		$temp_edit_gradebook_caps = [];

		foreach ( $roles as $role_ID => $role ) {
			if ( $role_ID == 'administrator' ) {
				continue;
			}

			$role = get_role( $role_ID );

			if ( ! isset( $default_ld_gb_caps[ $role_ID ] ) ||
				( isset( $default_ld_gb_caps[ $role_ID ] ) && ! in_array( 'view_gradebook', $default_ld_gb_caps[ $role_ID ] ) ) ) { // Ensure Group Leader's ability to View Gradebooks doesn't get wiped

				if ( in_array( $role_ID, $gradebook_roles ) ) {
					if ( ! $role->has_cap( 'view_gradebook' ) ) {
						$role->add_cap( 'view_gradebook' );
					}
				} elseif ( $role->has_cap( 'view_gradebook' ) ) {
						$role->remove_cap( 'view_gradebook' );
				}
			}

			if ( array_key_exists( $role_ID, $default_ld_gb_caps ) ) {
				// Only check against caps that they were not granted by default
				$temp_edit_gradebook_caps = array_diff( $edit_gradebook_caps, $default_ld_gb_caps[ $role_ID ] );
			} else {
				// Check against all Edit Gradebook caps
				$temp_edit_gradebook_caps = $edit_gradebook_caps;
			}

			if ( in_array( $role_ID, $edit_gradebook_roles ) ) {
				foreach ( $temp_edit_gradebook_caps as $cap ) {
					if ( ! $role->has_cap( $cap ) ) {
						$role->add_cap( $cap );
					}
				}
			} else {
				foreach ( $temp_edit_gradebook_caps as $cap ) {
					if ( $role->has_cap( $cap ) ) {
						$role->remove_cap( $cap );
					}
				}
			}
		}
	}

	/**
	 * Grab the Edit Gradebook Capabilities.
	 * By default, this includes everything but the ability to Edit/Delete the Gradebooks of Other Users
	 *
	 * @access private
	 * @since 1.4.0
	 * @return array Edit Gradebook Capabilities
	 */
	private function get_edit_gradebook_capabilities() {
		return apply_filters(
			'get_edit_gradebook_capabilities',
			[
				'read_gradebook',
				'read_private_gradebooks',
				'publish_gradebooks',
				'edit_gradebook',
				'edit_gradebooks',
				'edit_private_gradebooks',
				'edit_published_gradebooks',
				'delete_gradebooks',
				'delete_private_gradebooks',
				'delete_published_gradebooks',
				'delete_gradebook',
			]
		);  }

	/**
	 * Addresses the fact that the Instructor Roles plugin doesn't purely use Capabilities when checking whether or not a user can view a specific page
	 *
	 * @param array $learndash_post_types Array of LearnDash Post Types
	 *
	 * @access public
	 * @since 1.4.0
	 * @return array Array of LearnDash Post Types
	 */
	public function fix_granting_edit_gradebooks_to_instructors( $learndash_post_types ) {
		$learndash_post_types[] = 'gradebook';

		return $learndash_post_types;   }

	/**
	 * Settings page section General output.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @param array $active_section Currently active section and its args.
	 */
	static function section_output( $active_section ) {
		$page = isset( $_GET['section'] ) ? "learndash_gradebook-{$_GET['section']}" : 'learndash_gradebook-general';

		include LEARNDASH_GRADEBOOK_DIR . 'admin/views/html-settings-page.php';
	}

	/**
	 * Notifies the user if scales are invalid.
	 *
	 * @since 1.1.0
	 * @access private
	 */
	function notify_invalid_scales() {
		$letter_grade_scale = ld_gb_get_option_field( 'letter_grade_scale', ld_gb_get_default_letter_grade_scale() );

		$letter_grade_scale = learndash_gradebook_sanitize_grade_style_settings( $letter_grade_scale );

		$grade_color_scale = ld_gb_get_option_field( 'grade_color_scale', ld_gb_get_default_grade_color_scale() );

		$grade_color_scale = learndash_gradebook_sanitize_grade_style_settings( $grade_color_scale );

		$grade_scale_search = array_filter(
			$letter_grade_scale,
			function ( $row ) {
				return intval( $row['grade'] ) === 0;
			}
		);

		if ( empty( $grade_scale_search ) ) {
			add_settings_error(
				'ld-gb-invalid-scales',
				'',
				_x( 'Letter Grade Scale requires a 0% grade to exist. It has been added manually and will show on the next page refresh.', 'Invalid Letter Grade Scale provided error message', 'learndash-gradebook' ),
				'error'
			);

			$letter_grade_scale[] = [
				'grade'  => 0,
				'letter' => 'F',
			];

			update_option( 'ld_gb_letter_grade_scale', $letter_grade_scale );
		}

		$color_scale_search = array_filter(
			$grade_color_scale,
			function ( $row ) {
				return intval( $row['grade'] ) === 0;
			}
		);

		if ( empty( $color_scale_search ) ) {
			add_settings_error(
				'ld-gb-invalid-scales',
				'',
				_x( 'Grade Color Scale requires a 0% grade to exist. It has been added manually and will show on the next page refresh.', 'Invalid Grade Color Scale provided error message', 'learndash-gradebook' ),
				'error'
			);

			$grade_color_scale[] = [
				'grade' => 0,
				'color' => '#f00',
			];

			update_option( 'ld_gb_grade_color_scale', $grade_color_scale );
		}
	}
}
