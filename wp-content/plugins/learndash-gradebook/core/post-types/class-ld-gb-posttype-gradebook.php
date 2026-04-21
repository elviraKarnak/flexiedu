<?php
/**
 * Creates the Gradebook post type.
 *
 * @since 1.2.0
 *
 * @package LearnDash_Gradebook
 *
 * cspell:ignore heirarchy
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_PostType_Gradebook
 *
 * Creates the Gradebook post type.
 *
 * @since 1.2.0
 */
class LD_GB_PostType_Gradebook {
	/**
	 * LD_GB_PostType_Gradebook constructor.
	 *
	 * @since 1.2.0
	 */
	function __construct() {
		add_action( 'current_screen', [ $this, 'screen_actions' ] );
		add_filter( 'ld_gb_admin_script_data', [ $this, 'add_script_data' ] );
		add_action( 'init', [ $this, 'register_post_type' ] );
		add_filter( 'post_updated_messages', [ $this, 'post_messages' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_filter(
			'ld_gb_fieldhelpers_gradebook-settings_save_field_components',
			[
				$this,
				'validate_components_save',
			],
			10,
			2
		);

		add_action( 'wp_ajax_ld_gb_get_component_options', [ $this, 'ajax_get_component_options' ] );
		add_action( 'wp_ajax_ld_gb_get_new_component_id', [ $this, 'ajax_get_new_component_id' ] );

		add_filter( 'wp_dropdown_users_args', [ $this, 'wp_dropdown_users_args' ], 10, 2 ); }

	/**
	 * Loads actions specific to the current screen.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @param WP_Screen $wp_screen
	 */
	function screen_actions( $wp_screen ) {
		switch ( $wp_screen->id ) {
			case 'gradebook':
				add_filter( 'rbm_fieldhelpers_load_select2', '__return_true' );
				add_filter( 'rbm_fieldhelpers_load_datetimepicker', '__return_true' );
				add_action( 'admin_enqueue_scripts', [ $this, 'gradebook_post_edit_scripts' ] );
				break;
		}
	}

	/**
	 * Enqueues scripts only on the Gradebook Post Edit page.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function gradebook_post_edit_scripts() {
		wp_enqueue_script( 'tiny_mce' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_editor();
		wp_enqueue_media();
	}

	/**
	 * Adds some data to be localized.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	function add_script_data( $data ) {
		$data['component_weights'] = ld_gb_get_field( 'component_weights' );
		$data['components']        = ld_gb_get_field( 'components' );
		$data['gradebook_id']      = get_the_ID();

		return $data;
	}

	/**
	 * Registers the post type.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function register_post_type() {
		$labels = [
			'name'               => _x( 'Gradebooks', 'post type general name', 'learndash-gradebook' ),
			'singular_name'      => _x( 'Gradebook', 'post type singular name', 'learndash-gradebook' ),
			'menu_name'          => _x( 'Gradebooks', 'admin menu', 'learndash-gradebook' ),
			'name_admin_bar'     => _x( 'Gradebook', 'add new on admin bar', 'learndash-gradebook' ),
			'add_new'            => _x( 'Add New', 'gradebook', 'learndash-gradebook' ),
			'add_new_item'       => __( 'Add New Gradebook', 'learndash-gradebook' ),
			'new_item'           => __( 'New Gradebook', 'learndash-gradebook' ),
			'edit_item'          => __( 'Edit Gradebook', 'learndash-gradebook' ),
			'view_item'          => __( 'View Gradebook', 'learndash-gradebook' ),
			'all_items'          => __( 'All Gradebooks', 'learndash-gradebook' ),
			'search_items'       => __( 'Search Gradebooks', 'learndash-gradebook' ),
			'parent_item_colon'  => __( 'Parent Gradebooks:', 'learndash-gradebook' ),
			'not_found'          => __( 'No Gradebooks found.', 'learndash-gradebook' ),
			'not_found_in_trash' => __( 'No Gradebooks found in Trash.', 'learndash-gradebook' ),
		];

		/**
		 * Post type labels for the Gradebook post type.
		 *
		 * @since 1.2.0
		 */
		$labels = apply_filters( 'ld_gb_posttype_gradebook_labels', $labels ); // cspell:disable-line.

		$args = [
			'labels'             => $labels,
			'description'        => __( 'Gradebook by LearnDash', 'learndash-gradebook' ),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'gradebook',
			'capabilities'       => [
				'edit_post'          => 'edit_gradebook',
				'edit_posts'         => 'edit_gradebooks',
				'edit_others_posts'  => 'edit_others_gradebooks',
				'publish_posts'      => 'publish_gradebooks',
				'read_post'          => 'read_gradebook',
				'read_private_posts' => 'read_private_gradebooks',
				'delete_post'        => 'delete_gradebook',
			],
			'map_meta_cap'       => true,
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => [ 'title', 'author' ],
		];

		/**
		 * Post type arguments for the Gradebook post type.
		 *
		 * @since 1.2.0
		 */
		$args = apply_filters( 'ld_gb_posttype_gradebook_args', $args ); // cspell:disable-line.

		register_post_type( 'gradebook', $args );
	}

	/**
	 * Adds Gradebook post updated messages.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @param array $messages Post messages.
	 *
	 * @return array
	 */
	function post_messages( $messages ) {
		global $post;

		$scheduled_date = date_i18n( __( 'M j, Y @ H:i' ), strtotime( $post->post_date ) );

		$messages['gradebook'] = [
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Gradebook updated.' ),
			2  => __( 'Custom field updated.' ),
			3  => __( 'Custom field deleted.' ),
			4  => __( 'Gradebook updated.' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Gradebook restored to revision from %s.' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Gradebook published.' ),
			7  => __( 'Gradebook saved.' ),
			8  => __( 'Gradebook submitted.' ),
			9  => sprintf( __( 'Gradebook scheduled for: %s.' ), '<strong>' . $scheduled_date . '</strong>' ),
			10 => __( 'Gradebook draft updated.' ),
		];

		return $messages;
	}

	/**
	 * Adds meta boxes to the post type.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function add_meta_boxes() {
		$current_screen = get_current_screen();

		add_meta_box(
			'gradebook-grading',
			_x( 'Grading', 'Gradebook Edit Screen: Grading Meta Box Title', 'learndash-gradebook' ),
			[ $this, 'mb_grading' ],
			'gradebook'
		);

		add_meta_box(
			'gradebook-shortcode',
			_x( 'Report Card', 'Gradebook Edit Screen: Report Card Meta Box Title', 'learndash-gradebook' ),
			[ $this, 'mb_shortcode' ],
			'gradebook',
			'side',
			'high'
		);

		add_meta_box(
			'gradebook-weighting',
			_x( 'Weighting', 'Gradebook Edit Screen: Weighting Meta Box Title', 'learndash-gradebook' ),
			[ $this, 'mb_weighting' ],
			'gradebook',
			'side'
		);

		add_meta_box(
			'gradebook-settings',
			_x( 'Settings', 'Gradebook Edit Screen: Settings Meta Box Title', 'learndash-gradebook' ),
			[ $this, 'mb_settings' ],
			'gradebook',
			'side'
		);
	}

	/**
	 * Outputs the shortcode metabox.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function mb_shortcode() {
		?>
		<p>
			<?php _e( 'In order to show a report card for this Gradebook on your site, copy and paste the following shortcode wherever you like.', 'learndash-gradebook' ); ?>
		</p>

		<p>
			<code>
				[ld_report_card gradebook="<?php the_ID(); ?>"]
			</code>
		</p>
		<?php
	}

	/**
	 * Outputs the grading metabox.
	 *
	 * @since 1.2.0
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function mb_grading( $post ) {
		// Fix specific to the Instructor Roles plugin by WisdmLabs
		if ( function_exists( 'wdm_set_author' ) ) {
			remove_filter( 'pre_get_posts', 'wdm_set_author' );
		} elseif ( class_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin' ) && method_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' ) ) {
			learndash_gradebook_remove_class_action( 'pre_get_posts', 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' );
		}

		?>
		<div class="notice error inline ld-gb-component-error-message" style="display: none;">
			<p>
				<?php _e( 'At least one Component must exist.', 'learndash-gradebook' ); ?>
			</p>
		</div>
		<?php

		$courses = get_posts(
			[
				'post_type'   => 'sfwd-courses',
				'numberposts' => - 1,
				'post_status' => 'any',
			]
		);

		$course_ids = ld_gb_get_field( 'course', get_the_ID(), false );

		$course_ids = ( ! is_array( $course_ids ) ) ? [ $course_ids ] : $course_ids;
		$course_ids = array_filter( $course_ids );

		ld_gb_do_field_select(
			[
				'name'                  => 'course',
				'group'                 => 'gradebook-settings',
				'label'                 => LearnDash_Custom_Label::get_label( 'courses' ),
				/* translators: First %s is Courses, second %s is Courses, third is Courses, forth is Lessons, fifth is Topics, and sixth is Courses */
				'description'           => sprintf( __( 'Select %1$s to use for this Gradebook. If "All %2$s" is selected, all active %3$s will count towards the Gradebook grade.', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'courses' ), LearnDash_Custom_Label::get_label( 'courses' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
				'description_placement' => 'after_label',
				/* translators: First %s is Courses */
				'options'               => [ 'all' => sprintf( __( 'All %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'courses' ) ) ] + wp_list_pluck( $courses, 'post_title', 'ID' ),
				'input_class'           => 'regular-text',
				'placeholder'           => __( 'Make a Selection', 'learndash-gradebook' ),
				/* translators: First %s is Courses */
				'option_none'           => sprintf( __( 'All %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
				'select2_options'       => [
					'allowClear' => true,
				],
				'l10n'                  => [
					/* translators: First %s is Courses */
					'no_options' => sprintf( __( 'All %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
					/* translators: First %s is Courses */
					'no_results' => sprintf( __( 'No %s Available', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
				],
				'input_atts'            => [
					'required' => true,
				],
				'default'               => ( $post->post_status == 'auto-draft' && ! $course_ids ) ? false : ( ( ! $course_ids ) ? [ 'all' ] : false ),
				'multiple'              => true,
			]
		);

		if ( $post->post_status == 'auto-draft' && ! $course_ids ) {
			// If it is a new post, we do not need to worry about a legacy fallback to 'all' for an empty Course ID
			$options = [
				'lessons'            => [],
				'topics'             => [],
				'quizzes'            => [],
				'assignment_lessons' => [],
				'assignment_topics'  => [],
			];
		} else {
			$options = self::get_component_options( $course_ids );
		}

		ld_gb_do_field_repeater(
			[
				'name'                => 'components',
				'group'               => 'gradebook-settings',
				'label'               => __( 'Components', 'learndash-gradebook' ),
				'add_item_text'       => __( 'Add Component', 'learndash-gradebook' ),
				'delete_item_text'    => __( 'Delete Component', 'learndash-gradebook' ),
				'confirm_delete_text' => __( 'Are you sure you want to delete this Component? This cannot be undone', 'learndash-gradebook' ),
				'fields'              => [
					'id'                          => [
						'type' => 'hidden',
					],
					'name'                        => [
						'type' => 'text',
						'args' => [
							'label'       => __( 'Name', 'learndash-gradebook' ),
							'input_class' => 'regular-text',
						],
					],
					'lessons_section_divider'     => [
						'type' => 'html',
						'args' => [
							/* translators: First %s is Lesson */
							'html' => '<div class="ld-gb-clearfix"></div><hr/>' . sprintf( __( '%s Grading', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lesson' ) ),
						],
					],
					'lessons'                     => [
						'type' => 'select',
						'args' => [
							'label'                 => LearnDash_Custom_Label::get_label( 'lessons' ),
							/* translators: First %s is Lessons */
							'description'           => sprintf( __( '%s will be graded based on completion.', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
							'description_placement' => 'after_label',
							'options'               => wp_list_pluck( $options['lessons'], 'text', 'value' ),
							'wrapper_class'         => 'fieldhelpers-col-2',
							'input_class'           => 'widefat ld-gb-component-items-select',
							'input_atts'            => [
								'data-type'             => 'lessons',
								'data-disable-from-all' => 'lessons',
							],
							'show_empty_select'     => true,
							'multiple'              => true,
							/* translators: First %s is Lessons */
							'placeholder'           => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
							'l10n'                  => [
								/* translators: First %s is Lessons */
								'no_options' => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
								/* translators: First %s is Lessons */
								'no_results' => sprintf( __( 'No %s Available', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
							],
						],
					],
					'lessons_all'                 => [
						'type' => 'toggle',
						'args' => [
							/* translators: First %s is Lessons */
							'label'                 => sprintf( __( 'All %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
							'wrapper_class'         => 'fieldhelpers-col-2',
							'input_atts'            => [ 'data-disable-group' => 'lessons' ],
							/* translators: First %s is Lessons and second %s is Lessons */
							'description'           => sprintf( __( 'Enabling this will cause all %1$s to be factored into the Component grade. This will override any %2$s you have selected and instead include everything.', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lessons' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
							'description_placement' => 'after_label',
						],
					],
					'topics_section_divider'      => [
						'type' => 'html',
						'args' => [
							/* translators: First %s is Topic */
							'html' => '<div class="ld-gb-clearfix"></div><hr/>' . sprintf( __( '%s Grading', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topic' ) ),
						],
					],
					'topics'                      => [
						'type' => 'select',
						'args' => [
							/* translators: First %s is Topics */
							'label'                 => sprintf( __( '%s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
							/* translators: First %s is Topics */
							'description'           => sprintf( __( '%s will be graded based on completion.', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
							'description_placement' => 'after_label',
							'options'               => wp_list_pluck( $options['topics'], 'text', 'value' ),
							'wrapper_class'         => 'fieldhelpers-col-2',
							'input_class'           => 'widefat ld-gb-component-items-select',
							'input_atts'            => [
								'data-type'             => 'topics',
								'data-disable-from-all' => 'topics',
							],
							'show_empty_select'     => true,
							'multiple'              => true,
							/* translators: First %s is Topics */
							'placeholder'           => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
							'l10n'                  => [
								/* translators: First %s is Topics */
								'no_options' => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
								/* translators: First %s is Topics */
								'no_results' => sprintf( __( 'No %s Available', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
							],
						],
					],
					'topics_all'                  => [
						'type' => 'toggle',
						'args' => [
							/* translators: First %s is Topics */
							'label'                 => sprintf( __( 'All %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
							'wrapper_class'         => 'fieldhelpers-col-2',
							'input_atts'            => [ 'data-disable-group' => 'topics' ],
							/* translators: First %s is Topics and second %s is Topics */
							'description'           => sprintf( __( 'Enabling this will cause all %1$s to be factored into the Component grade. This will override any %2$s you have selected and instead include everything.', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
							'description_placement' => 'after_label',
						],
					],
					'quizzes_section_divider'     => [
						'type' => 'html',
						'args' => [
							/* translators: First %s is Quiz */
							'html' => '<div class="ld-gb-clearfix"></div><hr/>' . sprintf( __( '%s Grading', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'quiz' ) ),
						],
					],
					'quizzes'                     => [
						'type' => 'select',
						'args' => [
							'label'             => LearnDash_Custom_Label::get_label( 'quizzes' ),
							'options'           => wp_list_pluck( $options['quizzes'], 'text', 'value' ),
							'wrapper_class'     => 'fieldhelpers-col-2',
							'input_class'       => 'widefat ld-gb-component-items-select',
							'input_atts'        => [
								'data-type'             => 'quizzes',
								'data-disable-from-all' => 'quizzes',
							],
							'show_empty_select' => true,
							'multiple'          => true,
							/* translators: First %s is Quizzes */
							'placeholder'       => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ),
							'l10n'              => [
								/* translators: First %s is Quizzes */
								'no_options' => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ),
								/* translators: First %s is Quizzes */
								'no_results' => sprintf( __( 'No %s Available', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ),
							],
						],
					],
					'quizzes_all'                 => [
						'type' => 'toggle',
						'args' => [
							/* translators: First %s is Quizzes */
							'label'                 => sprintf( __( 'All %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ),
							'wrapper_class'         => 'fieldhelpers-col-2',
							'input_atts'            => [ 'data-disable-group' => 'quizzes' ],
							/* translators: First %s is Quizzes and second %s is Quizzes */
							'description'           => sprintf( __( 'Enabling this will cause all %1$s to be factored into the Component grade. This will override any %2$s you have selected and instead include everything.', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'quizzes' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ),
							'description_placement' => 'after_label',
						],
					],
					'assignments_section_divider' => [
						'type' => 'html',
						'args' => [
							/* translators: First %s is Assignment */
							'html' => '<div class="ld-gb-clearfix"></div><hr/>' . sprintf( __( '%s Grading', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'assignment' ) ),
						],
					],
					'assignments_all'             => [
						'type' => 'toggle',
						'args' => [
							/* translators: First %s is Assignments */
							'label'                 => sprintf( __( 'All %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'assignments' ) ),
							'wrapper_class'         => 'fieldhelpers-col-2',
							'input_atts'            => [ 'data-disable-group' => 'assignments' ],
							/* translators: First %s is Assignments, second %s is Assignments, third is Lessons, and forth is Topics */
							'description'           => sprintf( __( 'Enabling this will cause all %1$s to be factored into the Component grade. This will override any settings for %2$s from %3$s and %4$s from %5$s that you have selected, and instead include everything.', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'assignments' ), LearnDash_Custom_Label::get_label( 'assignments' ), LearnDash_Custom_Label::get_label( 'lessons' ), LearnDash_Custom_Label::get_label( 'assignments' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
							'description_placement' => 'after_label',
						],
					],
					'assignments_clearfix'        => [
						'type' => 'html',
						'args' => [
							'html' => '<div class="ld-gb-clearfix"></div>',
						],
					],
					'assignment_lessons'          => [
						'type' => 'select',
						'args' => [
							/* translators: First %s is Assignments and second %s is Lessons */
							'label'                 => sprintf( __( '%1$s from %2$s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'assignments' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
							'options'               => wp_list_pluck( $options['assignment_lessons'], 'text', 'value' ),
							'wrapper_class'         => 'fieldhelpers-col-2',
							'input_class'           => 'widefat ld-gb-component-items-select',
							'input_atts'            => [
								'data-type'             => 'assignment_lessons',
								'data-disable-from-all' => 'assignments',
							],
							'show_empty_select'     => true,
							'multiple'              => true,
							/* translators: Lessons. */
							'placeholder'           => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
							'description'           => sprintf(
								/* translators: %1$s Assignments, %2$s Lessons. */
								__(
									'Only %1$s belonging to these %2$s will be graded. This will override any selected %1$s. Also, if "All %1$s" is selected, this will be overridden.',
									'learndash-gradebook'
								),
								LearnDash_Custom_Label::get_label( 'assignments' ),
								LearnDash_Custom_Label::get_label( 'lessons' )
							),
							'description_placement' => 'after_label',
							'l10n'                  => [
								/* translators: Lessons. */
								'no_options' => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lessons' ) ),
								/* translators: First %s is Lessons and second %s is Assignment */
								'no_results' => sprintf( __( 'No %1$s with %2$s Uploads Available', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'lessons' ), LearnDash_Custom_Label::get_label( 'assignment' ) ),
							],
						],
					],
					'assignment_topics'           => [
						'type' => 'select',
						'args' => [
							/* translators: First %s is Assignments and second %s is Topics */
							'label'                 => sprintf( __( '%1$s from %2$s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'assignments' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
							'options'               => wp_list_pluck( $options['assignment_topics'], 'text', 'value' ),
							'wrapper_class'         => 'fieldhelpers-col-2',
							'input_class'           => 'widefat ld-gb-component-items-select',
							'input_atts'            => [
								'data-type'             => 'assignment_topics',
								'data-disable-from-all' => 'assignments',
							],
							'show_empty_select'     => true,
							'multiple'              => true,
							/* translators: First %s is Topics */
							'placeholder'           => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
							/* translators: First %s is Assignments, second %s is Topics, third is Assignments, and forth is Assignments */
							'description'           => sprintf( __( 'Only %1$s belonging to these %2$s will be graded. This will override any selected %3$s. Also, if "All %4$s" is selected, this will be overridden.', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'assignments' ), LearnDash_Custom_Label::get_label( 'topics' ), LearnDash_Custom_Label::get_label( 'assignments' ), LearnDash_Custom_Label::get_label( 'assignments' ) ),
							'description_placement' => 'after_label',
							'l10n'                  => [
								/* translators: First %s is Topics */
								'no_options' => sprintf( __( 'No %s', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ) ),
								/* translators: First %s is Topics and second %s is Assignment */
								'no_results' => sprintf( __( 'No %1$s with %2$s Uploads Available', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'topics' ), LearnDash_Custom_Label::get_label( 'assignment' ) ),
							],
						],
					],
				],
			]
		);

		if ( function_exists( 'wdm_set_author' ) ) {
			add_filter( 'pre_get_posts', 'wdm_set_author' );
		} elseif ( class_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin' ) && method_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' ) ) {
			add_action( 'pre_get_posts', [ InstructorRole\Modules\Classes\Instructor_Role_Admin::get_instance(), 'wdm_set_author' ] );
		}

		ld_gb_fieldhelpers()->fields->save->initialize_fields( 'gradebook-settings' );  }

	/**
	 * Outputs the weighting metabox.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function mb_weighting() {
		$components = ld_gb_get_field( 'components' );

		include_once LEARNDASH_GRADEBOOK_DIR . 'core/post-types/views/metabox-gradebook-weighting.php';

		ld_gb_init_field_group( 'gradebook-weighting' );
	}

	/**
	 * Outputs the settings metabox.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function mb_settings() {
		$fields = $this->get_settings_metabox_fields();

		foreach ( $fields as $field ) {
			$field = wp_parse_args(
				$field,
				[
					'callback' => '',
				]
			);

			if ( empty( $field['callback'] ) ) {
				continue;
			}

			$field['group'] = 'gradebook-settings';

			call_user_func( $field['callback'], $field );
		}

		ld_gb_init_field_group( 'gradebook-settings' );
	}

	/**
	 * Returns the Fields used for the Settings meta box
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function get_settings_metabox_fields() {
		$fields = [
			'completion_grading_mode' => [
				'name'     => 'completion_grading_mode',
				'callback' => 'ld_gb_do_field_radio',
				'label'    => __( 'Completion Grading', 'learndash-gradebook' ),
				'default'  => 'completion',
				'options'  => [
					'completion' => __( 'Only count on completion', 'learndash-gradebook' ),
					'pass_fail'  => __( 'Fail until completion', 'learndash-gradebook' ),
					'incomplete' => __( 'Mark as Incomplete until completion', 'learndash-gradebook' ),
				],
			],
			'include_all_users'       => [
				'name'                      => 'include_all_users',
				'callback'                  => 'ld_gb_do_field_toggle',
				'label'                     => __( 'Include All Users In Gradebook', 'learndash-gradebook' ),
				/* translators: First %s is Course, second %s is Course, and third is Course */
				'description'               => sprintf( __( 'If this Gradebook has a %1$s set, only students enrolled in that %2$s will show in the Gradebook. If you would to show all users in this Gradebook, despite a %3$s selected, enable this.', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'course' ), LearnDash_Custom_Label::get_label( 'course' ), LearnDash_Custom_Label::get_label( 'course' ) ),
				'description_placement'     => 'after_label',
				'description_tip_alignment' => 'right',
			],
			'component_orderby'       => [
				'name'     => 'component_orderby',
				'callback' => 'ld_gb_do_field_select',
				'label'    => __( 'Order Component Resources By', 'learndash-gradebook' ),
				'options'  => [
					[
						'text'  => _x( 'Name', 'Component Orderby Option', 'learndash-gradebook' ),
						'value' => 'title',
					],
					[
						'text'  => _x( 'Grade', 'Component Orderby Option', 'learndash-gradebook' ),
						'value' => 'grade',
					],
					[
						'text'  => _x( 'Date Created', 'Component Orderby Option', 'learndash-gradebook' ),
						'value' => 'date',
					],
					[
						'text'  => _x( 'Date Modified', 'Component Orderby Option', 'learndash-gradebook' ),
						'value' => 'modified',
					],
				],
			],
			'component_order'         => [
				'name'     => 'component_order',
				'callback' => 'ld_gb_do_field_select',
				'label'    => __( 'Component Resource Order', 'learndash-gradebook' ),
				'options'  => [
					[
						'text'  => _x( 'Ascending', 'Component Order Option', 'learndash-gradebook' ),
						'value' => 'asc',
					],
					[
						'text'  => _x( 'Descending', 'Component Order Option', 'learndash-gradebook' ),
						'value' => 'desc',
					],
				],
			],
		];

		return $fields; }

	/**
	 * Makes sure components are valid when saving.
	 *
	 * @since 1.3.6
	 * @access private
	 *
	 * @param array $components
	 * @param int   $gradebook_ID
	 */
	function validate_components_save( $components, $gradebook_ID ) {
		$components = $this->validate_components_ids( $components, $gradebook_ID );

		return $components;
	}

	/**
	 * If components are missing ID's, generate new ones.
	 *
	 * @since 1.3.6
	 * @access private
	 *
	 * @param array $components
	 * @param int   $gradebook_ID
	 *
	 * @return array
	 */
	private function validate_components_ids( $components, $gradebook_ID ) {
		foreach ( $components as &$component ) {
			if ( ! $component['id'] || (int) $component['id'] < 0 ) {
				$component['id'] = $this->get_latest_component_id( $gradebook_ID );
			}
		}

		return $components;
	}

	/**
	 * Get available options for specific Courses.
	 *
	 * @since 1.2.6
	 * @updated 4.0.0
	 *
	 * @param array $course_ids Course IDs to filter by.
	 *
	 * @return array Options grouped by type.
	 */
	public static function get_course_component_options( $course_ids ) {
		$options = [
			'lessons'            => [],
			'topics'             => [],
			'quizzes'            => [],
			'assignment_lessons' => [],
			'assignment_topics'  => [],
		];

		$option_type_map = [
			'sfwd-courses' => 'courses',
		];

		foreach ( $course_ids as $course_id ) {
			foreach ( $option_type_map as $post_type => $option_type ) {
				$steps_object     = LDLMS_Factory_Post::course_steps( $course_id );
				$course_hierarchy = $steps_object->get_steps( 'h' );

				$formatted_data = [
					'lessons' => [],
					'topics'  => [],
					'quizzes' => [],
				];

				if ( count( $course_ids ) > 1 ) {
					self::get_formatted_course_heirarchy( $course_hierarchy, $formatted_data, [ $course_id ] ); // cspell:disable-line.
				} else {
					self::get_formatted_course_heirarchy( $course_hierarchy, $formatted_data ); // cspell:disable-line.
				}

				// Add everything but Assignments
				foreach ( $formatted_data as $data_type => $data ) {
					foreach ( $data as $id => $formatted_title ) {
						// We want to also include this data for our Assignment Lesson/Topic dropdowns
						if ( ( $data_type == 'lessons' || $data_type == 'topics' ) &&
							learndash_lesson_hasassignments( get_post( $id ) ) ) { // cspell:disable-line.

							$options[ "assignment_$data_type" ][] = [
								'value' => $id,
								'text'  => $formatted_title,
							];
						}

						$options[ $data_type ][] = [
							'value' => $id,
							'text'  => $formatted_title,
						];
					}
				}
			}
		}

		return $options;
	}

	/**
	 * Get ALL available options (Lesson, Topic, Quiz, Assignment).
	 *
	 * @since 1.2.6
	 *
	 * @return array Options grouped by type.
	 */
	public static function get_all_component_options() {
		$options = [
			'lessons'            => [],
			'topics'             => [],
			'quizzes'            => [],
			'assignment_lessons' => [],
			'assignment_topics'  => [],
		];

		$posts = get_posts(
			[
				'post_type'   => [ 'sfwd-courses' ],
				'numberposts' => - 1,
			]
		);

		if ( ! $posts || is_wp_error( $posts ) ) {
			return $options;
		}

		// Holds all our Non-Assignment Data to add to $options later
		// We have to manually traverse each Course's Step Hierarchy, so it is easiest to process them and then add as a batch at the end
		$formatted_data = [
			'lessons' => [],
			'topics'  => [],
			'quizzes' => [],
		];

		foreach ( $posts as $post ) {
			$steps_object     = LDLMS_Factory_Post::course_steps( $post->ID );
			$course_hierarchy = $steps_object->get_steps( 'h' );

			self::get_formatted_course_heirarchy( $course_hierarchy, $formatted_data, [ $post->ID ] ); // cspell:disable-line.
		}

		// Add everything but Assignments
		foreach ( $formatted_data as $data_type => $data ) {
			foreach ( $data as $id => $formatted_title ) {
				// We want to also include this data for our Assignment Lesson/Topic dropdowns
				if ( ( $data_type == 'lessons' || $data_type == 'topics' ) &&
					learndash_lesson_hasassignments( get_post( $id ) ) ) { // cspell:disable-line.

					$options[ "assignment_$data_type" ][] = [
						'value' => $id,
						'text'  => $formatted_title,
					];
				}

				$options[ $data_type ][] = [
					'value' => $id,
					'text'  => $formatted_title,
				];
			}
		}

		return $options;
	}

	/**
	 * Gets component options.
	 *
	 * @since 1.2.6
	 * @updated 4.0.0
	 *
	 * @param array $course_ids Send course post IDs to filter by Courses. Otherwise all component options are returned.
	 *
	 * @return array Component options grouped by type.
	 */
	public static function get_component_options( $course_ids = [ 'all' ] ) {
		if ( ! $course_ids || $course_ids == [ 'all' ] ) {
			return self::get_all_component_options();
		} else {
			return self::get_course_component_options( $course_ids );
		}
	}

	/**
	 * Constructs a Component Title based on an Array of Post IDs
	 *
	 * @param array  IDs, in order of LearnDash Course Hierarchy
	 *
	 * @access public
	 * @since 1.3.7
	 * @return string Formatted Hierarchy string
	 */
	public static function get_component_title( $ids = [] ) {
		// Remove duplicates and empties
		$ids = array_unique( array_filter( $ids ) );

		$titles = [];
		foreach ( $ids as $id ) {
			$titles[] = get_the_title( $id );
		}

		return implode( ' -> ', $titles );  }

	/**
	 * Builds out a Course Hierarchy in a format we can use.
	 *
	 * @param array LDLMS_Factory_Post::course_steps(                    $course_id )->get_steps( 'h' )
	 * @param array Holds our results. Passed by Reference
	 * @param array Holds the Component Post IDs used to build the Title
	 *
	 * @access public
	 * @since 1.3.7
	 * @return void
	 */
	public static function get_formatted_course_heirarchy( $course_hierarchy_steps, &$formatted_data = [
		'lessons' => [],
		'topics'  => [],
		'quizzes' => [],
	], $components_array = [] ) {
		$option_type_map = [
			'sfwd-lessons' => 'lessons',
			'sfwd-topic'   => 'topics',
			'sfwd-quiz'    => 'quizzes',
		];

		foreach ( $course_hierarchy_steps as $key => $value ) {
			if ( is_array( $value ) ) {
				if ( is_numeric( $key ) ) { // Only check against Post IDs

					$type = $option_type_map[ get_post_type( $key ) ];

					if ( ! isset( $formatted_data[ $type ][ $key ] ) ) {
						$formatted_data[ $type ][ $key ] = self::get_component_title( array_merge( $components_array, [ $key ] ) );
					} else { // Shared Component

						$formatted_data[ $type ][ $key ] .= ', ' . self::get_component_title( array_merge( $components_array, [ $key ] ) );
					}

					if ( ! empty( $value ) ) {
						self::get_formatted_course_heirarchy( $value, $formatted_data, array_merge( $components_array, [ $key ] ) ); // cspell:disable-line.
					}
				} else { // If not a Post ID, we need to go a level Deeper

					self::get_formatted_course_heirarchy( $value, $formatted_data, $components_array ); // cspell:disable-line.
				}
			}
		}   }

	/**
	 * Retrieves the latest component ID AND saves it to the DB.
	 *
	 * Be warned: Even calling this function will increment the last component ID.
	 *
	 * @since 1.3.6
	 * @access private
	 *
	 * @param int|string $gradebook_ID
	 *
	 * @return int
	 */
	private function get_latest_component_id( $gradebook_ID ) {
		$last = (int) get_post_meta( $gradebook_ID, 'last_component_id', true );
		update_post_meta( $gradebook_ID, 'last_component_id', $last + 1 );

		return $last;
	}

	/**
	 * Retrieves all options for a course (lessons, topics, quizzes, assignments).
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function ajax_get_component_options() {
		// Fix specific to the Instructor Roles plugin by WisdmLabs
		if ( function_exists( 'wdm_set_author' ) ) {
			remove_filter( 'pre_get_posts', 'wdm_set_author' );
		} elseif ( class_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin' ) && method_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' ) ) {
			learndash_gradebook_remove_class_action( 'pre_get_posts', 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' );
		}

		$courses = $_POST['courses'];
		$courses = ( $courses && ! is_array( $courses ) ) ? [ $courses ] : $courses;

		if ( $_POST['postStatus'] == 'auto-draft' && ! $courses ) {
			// If it is a new post, we do not need to worry about a legacy fallback to 'all' for an empty Course ID
			$options = [
				'lessons'            => [],
				'topics'             => [],
				'quizzes'            => [],
				'assignment_lessons' => [],
				'assignment_topics'  => [],
			];
		} else {
			$options = self::get_component_options( $courses );
		}

		if ( function_exists( 'wdm_set_author' ) ) {
			add_filter( 'pre_get_posts', 'wdm_set_author' );
		} elseif ( class_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin' ) && method_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' ) ) {
			add_action( 'pre_get_posts', [ InstructorRole\Modules\Classes\Instructor_Role_Admin::get_instance(), 'wdm_set_author' ] );
		}

		wp_send_json_success( $options );
	}

	/**
	 * Retrieves a new component ID.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function ajax_get_new_component_id() {
		$gradebook_ID = $_POST['gradebook_id'];

		$last = $this->get_latest_component_id( $gradebook_ID );

		wp_send_json_success( [ 'id' => $last + 1 ] );
	}

	/**
	 * Allows the Author of a Gradebook to be reassigned to any other User, provided they can Edit Gradebooks
	 *
	 * @param array $query_args    Args for WP_User_Query/get_users()
	 * @param array $function_args Args provided to wp_dropdown_users()
	 *
	 * @access public
	 * @since 1.4.1
	 * @return array Args for WP_User_Query/get_users()
	 */
	public function wp_dropdown_users_args( $query_args, $function_args ) {
		if ( get_post_type() !== 'gradebook' ) {
			return $query_args;
		}

		// Do not restrict to only Authors/Administrators
		$query_args['who'] = 'all';

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		// All Roles
		$role_names = $wp_roles->get_names();

		$can_edit_gradebooks = [];

		foreach ( $role_names as $role_key => $role_name ) {
			$role = get_role( $role_key );

			if ( isset( $role->capabilities['edit_gradebook'] ) && $role->capabilities['edit_gradebook'] == 1 ) {
				$can_edit_gradebooks[] = $role_key;
			}
		}

		$query_args['role__in'] = $can_edit_gradebooks;

		return $query_args; }

	public function fix_instructors_not_seeing_course_components( $post_types ) {
		return $post_types[] = 'gradebook'; }
}
