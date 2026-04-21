<?php
/**
 * Quick start guide, via WP Pointers.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_QuickStart
 *
 * Quick start guide, via WP Pointers.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */
class LD_GB_QuickStart {
	/**
	 * Pointers to show.
	 *
	 * @since 1.0.0
	 *
	 * @var array|null
	 */
	public $pointers;

	/**
	 * Pointers that have been dismissed.
	 *
	 * @since 1.0.0
	 *
	 * @var array|false|null
	 */
	public $dismissed_pointers;

	/**
	 * LD_GB_QuickStart constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'ld_gb_admin_script_data', [ $this, 'translations' ] );
		add_action( 'ld_gb_load_pointers', [ $this, 'included_pointers' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_pointers' ], 100 );
		add_action( 'current_screen', [ $this, 'screen_actions' ] );
		add_filter( 'admin_body_class', [ $this, 'body_class' ] );

		// Restart quickstart
		if ( isset( $_GET['ld_gb_restart_quickstart'] ) ) {
			add_action( 'admin_init', [ $this, 'restart_quickstart' ] );
		}

		add_action( 'wp_ajax_ld_gb_disable_quickstart', [ $this, 'ajax_disable_quickstart' ] );
	}

	/**
	 * Does various mocking and modifications for the Quickstart.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param WP_Screen $current_screen Current screen.
	 */
	function screen_actions( $current_screen ) {
		global $post, $wp_object_cache;

		if ( ! self::quickstart_enabled() ) {
			return;
		}

		if ( ! self::inside_quickstart() ) {
			return;
		}

		// Mock fake post
		if ( in_array(
			$current_screen->id,
			[
				'gradebook',
				'admin_page_learndash-gradebook',
				'admin_page_learndash-gradebook-user-grades',
			]
		) ) {
			$dummy                = new stdClass();
			$dummy->ID            = 999999999999;
			$dummy->post_title    = _x( 'My Gradebook', 'Fake Gradebook name for the Quick Start Guide', 'learndash-gradebook' );
			$dummy->post_author   = get_current_user_id();
			$dummy->post_date     = get_gmt_from_date( time() );
			$dummy->post_date_gmt = get_gmt_from_date( time() );
			$dummy->post_type     = 'gradebook';

			$post = new WP_Post( $dummy );

			$wp_object_cache->add( $dummy->ID, $post, 'posts' );
		}

		switch ( $current_screen->id ) {
			case 'gradebook':
				// Remove submit div so they can't try to save it
				remove_meta_box( 'submitdiv', 'gradebook', 'side' );

				// Get rid of comments
				remove_meta_box( 'commentsdiv', 'gradebook', 'default' );
				remove_meta_box( 'commentstatusdiv', 'gradebook', 'default' );

				// Mock Post Meta
				add_filter( 'rbm_field_ld_gb_components_value', [ $this, 'mock_components' ] );
				add_filter( 'ld_gb_rbm_fh_get_meta_field_components', [ $this, 'mock_components' ] );
				add_filter(
					'rbm_field_ld_gb_gradebook_weighting_enable_value',
					[
						$this,
						'mock_enable_weighting',
					]
				);
				break;

			case 'admin_page_learndash-gradebook':
				add_filter( 'ld_gb_gradebook_data', [ $this, 'mock_gradebook_users' ] );
				add_filter( 'ld_gb_gradebook_columns', [ $this, 'mock_gradebook_table_columns' ] );
				add_filter(
					'ld_gb_gradebook_sortable_columns',
					[
						$this,
						'mock_gradebook_table_sortable_columns',
					]
				);

				break;

			case 'admin_page_learndash-gradebook-user-grades':
				// Mock user grades
				add_filter( 'ld_gb_user_grade_components', [ $this, 'mock_user_grades' ] );

				// Mock Post Meta
				add_filter( 'rbm_field_ld_gb_components_value', [ $this, 'mock_components' ] );
				add_filter( 'ld_gb_rbm_fh_get_meta_field_components', [ $this, 'mock_components' ] );
				break;
		}
	}

	/**
	 * Adds quickstart class to admin body.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @param string $class Current body class.
	 *
	 * @return string
	 */
	function body_class( $class ) {
		if ( ! self::quickstart_enabled() ) {
			return $class;
		}

		if ( ! self::inside_quickstart() ) {
			return $class;
		}

		return $class . ' ld-gb-quickstart';
	}

	/**
	 * Returns mock components.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @return array
	 */
	function mock_components() {
		return [
			[
				'id'          => 0,
				'name'        => _x( 'Quizzes', 'Quizzes Component for the Quick Start Guide', 'learndash-gradebook' ),
				'quizzes_all' => '1',
				'weight'      => '30',
			],
			[
				'id'              => 1,
				'name'            => _x( 'Assignments', 'Assignments Component for the Quick Start Guide', 'learndash-gradebook' ),
				'assignments_all' => '1',
				'weight'          => '70',
			],
		];
	}

	/**
	 * Returns mock enable weighting.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @return string
	 */
	function mock_enable_weighting() {
		return '1';
	}

	/**
	 * Adds mock information to the table.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $data Table data.
	 *
	 * @return array
	 */
	function mock_gradebook_users( $data ) {
		$mock_users = json_decode( file_get_contents( LEARNDASH_GRADEBOOK_DIR . 'admin/includes/mock-data/gradebook-users.json' ), true );

		return $mock_users;
	}

	/**
	 * Adds mock information to the table columns.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $columns Columns.
	 *
	 * @return array
	 */
	function mock_gradebook_table_columns( $columns ) {
		return [
			'display_name' => _x( 'User', 'User Column Title for the Gradebook in the Quick Start Guide', 'learndash-gradebook' ),
			'grade'        => _x( 'Overall Grade', 'Overall Grade Column Title for the Gradebook in the Quick Start Guide', 'learndash-gradebook' ),
			'component_1'  => _x( 'Quizzes', 'Quizzes Component Column Title for the Gradebook in the Quick Start Guide', 'learndash-gradebook' ),
			'component_2'  => _x( 'Test 1', 'Test 1 Column Title for the Gradebook in the Quick Start Guide', 'learndash-gradebook' ),
			'component_3'  => _x( 'Reading Assignments', 'Reading Assignments Column Title for the Gradebook in the Quick Start Guide', 'learndash-gradebook' ),
		];
	}

	/**
	 * Adds mock information to the table columns.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $columns Columns.
	 *
	 * @return array
	 */
	function mock_gradebook_table_sortable_columns( $columns ) {
		return [];
	}

	/**
	 * Adds mock data to the user grades.
	 *
	 * @since 1.2.0
	 * @access private
	 *
	 * @param array $components Components.
	 *
	 * @return array
	 */
	function mock_user_grades( $components ) {
		return [
			[
				'id'             => 0,
				'name'           => _x( 'Quizzes', 'User Grades Quizzes Component Title in the Quick Start Guide', 'learndash-gradebook' ),
				'averaged_score' => 80,
				'grades'         => [
					[
						'type'          => 'quiz',
						'score_display' => '90%',
						'name'          => _x( 'Quiz A', 'User Grades Quizzes A Grade Title in the Quick Start Guide', 'learndash-gradebook' ),
						'post_id'       => 0,
					],
					[
						'type'          => 'quiz',
						'score_display' => '70%',
						'name'          => _x( 'Quiz B', 'User Grades Quizzes B Grade Title in the Quick Start Guide', 'learndash-gradebook' ),
						'post_id'       => 0,
					],
				],
				'overridden'     => false,
			],
			[
				'id'             => 1,
				'name'           => _x( 'Assignments', 'User Grades Assignments Component Title in the Quick Start Guide', 'learndash-gradebook' ),
				'averaged_score' => 93,
				'grades'         => [
					[
						'type'          => 'assignment',
						'score_display' => '100%',
						'name'          => _x( 'Assignment A', 'User Grades Assignment A Grade Title in the Quick Start Guide', 'learndash-gradebook' ),
						'post_id'       => 0,
					],
					[
						'type'          => 'manual',
						'score_display' => '87%',
						// Translators: This is a Take Home Assignment. User Grades Take Home Assignment Grade Title in the Quick Start Guide.
						'name'          => _x( 'Take Home Assignment', 'User Grades Take Home Assignment Grade Title in the Quick Start Guide', 'learndash-gradebook' ),
						'post_id'       => 0,
					],
				],
				'overridden'     => false,
			],
		];
	}

	/**
	 * Provides translations for localization.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $data Data to be localized.
	 *
	 * @return array
	 */
	function translations( $data ) {
		$data['l10n']['quickstart_text']                  = _x( 'Next', 'Quickstart Guide Next link text', 'learndash-gradebook' );
		$data['l10n']['quickstart_back_text']             = _x( 'Previous', 'Quickstart Guide Previous link text', 'learndash-gradebook' );
		$data['l10n']['disable_quickstart_error_generic'] = _x( 'Could not disable Quickstart Guide.', 'Could not disable Quickstart Guide error message', 'learndash-gradebook' );
		$data['l10n']['disabled_for_quickstart']          = _x( 'This feature is disabled for this Quickstart Guide.', 'This feature has been disabled for the Quickstart Guide error message', 'learndash-gradebook' );
		$data['l10n']['dismiss']                          = _x( 'Dismiss', 'Quickstart Guide Dismiss/Quit link tex', 'learndash-gradebook' );

		return $data;
	}

	/**
	 * Adds included pointers.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function included_pointers() {
		$included = [
			[
				'pointer_id' => 'quickstart_intro',
				'target'     => '#toplevel_page_learndash-lms',
				'link'       => admin_url( 'index.php' ),
				'screen'     => '',
				'options'    => [
					'title'           => _x( 'Gradebook by LearnDash', 'Quickstart Guide Intro Title', 'learndash-gradebook' ),
					'content_pieces'  => [
						__( 'Congratulations on installing Gradebook by LearnDash!', 'learndash-gradebook' ),
						__( 'To begin the Quick Start guide, click the button below. Otherwise you may click "Dismiss" to exit the guide at any time.', 'learndash-gradebook' ),
					],
					'position'        => [
						'edge'  => ( ! is_rtl() ) ? 'left' : 'right',
						'align' => ( ! is_rtl() ) ? 'left' : 'right',
					],
					'quickstart_text' => _x( 'Begin Guide', 'Begin Quickstart Guide link text', 'learndash-gradebook' ),
				],
			],
			[
				'pointer_id' => 'manage_gradebook_intro',
				'target'     => '.nav-tab-gradebook, .ld-tab-buttons',
				'link'       => admin_url( 'post.php?post=999999999999&action=edit&post_type=gradebook&ld_gb_quickstart=true' ),
				'screen'     => 'gradebook',
				'options'    => [
					'title'          => _x( 'Create a Gradebook', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'The first thing to do is create a Gradebook. You can have as many as you like, even if only just one.', 'learndash-gradebook' ),
						'<strong>' . __( 'Note: All data on this page is just an example and will not be saved.', 'learndash-gradebook' ) . '</strong>',
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#manage-gradebooks" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => 'top',
						'align' => ( ! is_rtl() ) ? 'left' : 'right',
					],
				],
			],
			[
				'pointer_id' => 'manage_gradebook_title',
				'target'     => '#title',
				'link'       => admin_url( 'post.php?post=999999999999&action=edit&post_type=gradebook&ld_gb_quickstart=true' ),
				'screen'     => 'gradebook',
				'options'    => [
					'title'          => _x( 'Gradebook Title', 'Gradebook Title Quick Start Guide Title', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'First thing\'s first, give your Gradebook a title.', 'learndash-gradebook' ),
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#manage-gradebooks" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => 'top',
						'align' => ( ! is_rtl() ) ? 'left' : 'right',
					],
				],
			],
			[
				'pointer_id' => 'manage_gradebook_components',
				'target'     => '#gradebook-grading',
				'link'       => admin_url( 'post.php?post=999999999999&action=edit&post_type=gradebook&ld_gb_quickstart=true' ),
				'screen'     => 'gradebook',
				'options'    => [
					'title'          => _x( 'Grading', 'Gradebook Edit Screen Title Quick Start Guide Title', 'learndash-gradebook' ),
					'content_pieces' => [
						sprintf( _x( 'Next you need to setup what will count towards the students\' grade for this Gradebook. You can either select a %1$s or set it to "All %2$s".', 'First %s is Course and second %s is Courses', 'learndash-gradebook' ), LearnDash_Custom_Label::get_label( 'course' ), LearnDash_Custom_Label::get_label( 'courses' ) ),
						__( 'You will then need to add Components. Each Component is something that will have a grade factored and be visible to the students. EG: Biology, Tests, etc.', 'learndash-gradebook' ),
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#manage-gradebooks" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => 'bottom',
						'align' => ( ! is_rtl() ) ? 'left' : 'right',
					],
				],
			],
			[
				'pointer_id' => 'manage_gradebook_weighting',
				'target'     => '#gradebook-weighting',
				'link'       => admin_url( 'post.php?post=999999999999&action=edit&post_type=gradebook&ld_gb_quickstart=true' ),
				'screen'     => 'gradebook',
				'options'    => [
					'title'          => _x( 'Weighting', 'Gradebook Edit Screen Weighting Quick Start Guide Title', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'By default, all Components have an equal weight in final grade calculations. You may change this to a a weighted system, where each Component can have a custom weight.', 'learndash-gradebook' ),
						__( 'To do so, simply turn it on here and then enter the weight for each Component.', 'learndash-gradebook' ),
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#manage-gradebooks" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => ( ! is_rtl() ) ? 'right' : 'left',
						'align' => 'center',
					],
				],
			],
			[
				'pointer_id' => 'manage_gradebook_settings',
				'target'     => '#gradebook-settings',
				'link'       => admin_url( 'post.php?post=999999999999&action=edit&post_type=gradebook&ld_gb_quickstart=true' ),
				'screen'     => 'gradebook',
				'options'    => [
					'title'          => _x( 'Settings', 'Gradebook Edit Screen Settings Quick Start Guide Title', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'Here you can find some extra settings.', 'learndash-gradebook' ),
						sprintf(
							preg_replace(
								'/(\d+)%/',
								'$1%%',
								_x( 'Completion Grading applies to %1$s and %2$s. By default, they will not count towards the final grade until they are marked complete, at which point they count for a 100% grade. If you set this to "Fail until completion", then any %3$s and %4$s not yet marked complete will count for a 0% grade.', 'First %s is Lessons, second %s is Topics, third %s is Lessons, and fourth %s is Topics', 'learndash-gradebook' )
							),
							LearnDash_Custom_Label::get_label( 'lessons' ),
							LearnDash_Custom_Label::get_label( 'topics' ),
							LearnDash_Custom_Label::get_label( 'lessons' ),
							LearnDash_Custom_Label::get_label( 'topics' )
						),
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#manage-gradebooks" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => ( ! is_rtl() ) ? 'right' : 'left',
						'align' => 'center',
					],
				],
			],
			[
				'pointer_id' => 'manage_gradebook_report_card',
				'target'     => '#gradebook-shortcode',
				'link'       => admin_url( 'post.php?post=999999999999&action=edit&post_type=gradebook&ld_gb_quickstart=true' ),
				'screen'     => 'gradebook',
				'options'    => [
					'title'          => _x( 'Report Card', 'Gradebook Edit Screen Report Card Quick Start Guide Title', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'Copy and paste this shortcode and use it to insert a Report Card for this Gradebook on any page.', 'learndash-gradebook' ),
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#manage-gradebooks" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => ( ! is_rtl() ) ? 'right' : 'left',
						'align' => 'center',
					],
				],
			],
			[
				'pointer_id' => 'settings_intro',
				'target'     => '.nav-tab-admin_page_learndash-gradebook-settings, .ld-tab-buttons a[href$="?page=learndash-gradebook-settings"]',
				'link'       => admin_url( 'admin.php?page=learndash-gradebook-settings&section=general&ld_gb_quickstart=true' ),
				'screen'     => 'admin_page_learndash-gradebook-settings',
				'section'    => 'general',
				'options'    => [
					'title'          => _x( 'Gradebook Settings', 'Settings Page Quick Start Guide Title', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'This is where you manage the settings for this Gradebook plugin.', 'learndash-gradebook' ),
						__( 'You can make any changes desired and then click "Save Changes" at the bottom of this page.', 'learndash-gradebook' ),
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#settings" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => 'top',
						'align' => ( ! is_rtl() ) ? 'left' : 'right',
					],
				],
			],
			[
				'pointer_id' => 'gradebook',
				'target'     => '.wp-list-table',
				'link'       => admin_url( 'admin.php?page=learndash-gradebook&gradebook=999999999999&ld_gb_quickstart=true' ),
				'screen'     => 'admin_page_learndash-gradebook',
				'options'    => [
					'title'          => __( 'The Gradebook', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'This is where you view the Gradebook. The Gradebook is the report on all of your students and all of their respective grades.', 'learndash-gradebook' ),
						'<strong>' . __( 'Note: All above data is just an example and will not be saved.', 'learndash-gradebook' ) . '</strong>',
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#the-gradebook" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => 'top',
						'align' => ( ! is_rtl() ) ? 'left' : 'right',
					],
				],
			],
			[
				'pointer_id' => 'manage_gradebook_users',
				'target'     => '.wp-list-table > tbody > tr:first-of-type > td:first-of-type',
				'link'       => admin_url( 'admin.php?page=learndash-gradebook-user-grades&gradebook=999999999999&return=gradebook-edit&user=' . get_current_user_id() . '&ld_gb_quickstart=true' ),
				'screen'     => 'admin_page_learndash-gradebook',
				'options'    => [
					'title'          => _x( 'User Grades', 'Gradebook View/Edit Grades Quick Start Guide Title', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'By hovering over the first column for a user, you can click a link to view or edit their grades for this Gradebook.', 'learndash-gradebook' ),
						'<strong>' . __( 'Note: This link does not show during the Quickstart Guide.', 'learndash-gradebook' ) . '</strong>',
						__( 'The next Quickstart page will be the user Grades for your profile, which you would normally get to by clicking this link.', 'learndash-gradebook' ),
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#the-gradebook" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => 'top',
						'align' => ( ! is_rtl() ) ? 'left' : 'right',
					],
				],
			],
			[
				'pointer_id' => 'user_grades',
				'target'     => '#ld-gb-gradebook',
				'link'       => admin_url( 'admin.php?page=learndash-gradebook-user-grades&gradebook=999999999999&return=gradebook-edit&user=' . get_current_user_id() . '&ld_gb_quickstart=true' ),
				'screen'     => 'admin_page_learndash-gradebook-user-grades',
				'options'    => [
					'title'          => _x( 'User Grades', 'User Grades Page Quick Start Guide Title', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'This is the page you will use to edit any user\'s grades.', 'learndash-gradebook' ),
						__( 'Above you can see some example data containing a couple of Grading Components as well as some example grades.', 'learndash-gradebook' ),
						__( 'Normally when using this tool you would be able to add special statuses to each grade as well as add any number of Manual Grades. Manual Grades can be anything you want and contain any grade score. This could be a take home project, attendance, etc.', 'learndash-gradebook' ),
						'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on/#manual-grades" target="_blank">' . _x( 'Further instructions', 'Documentation Link Text', 'learndash-gradebook' ) . '</a>',
					],
					'position'       => [
						'edge'  => ( ! is_rtl() ) ? 'left' : 'right',
						'align' => ( ! is_rtl() ) ? 'left' : 'right',
					],
				],
			],
			[
				'pointer_id' => 'quickstart_end',
				'target'     => '#wpbody-content > .wrap:eq(0) > h2:eq(0)',
				'link'       => admin_url( 'admin.php?page=learndash-gradebook-user-grades&gradebook=999999999999&return=gradebook-edit&user=' . get_current_user_id() . '&ld_gb_quickstart=true' ),
				'screen'     => 'admin_page_learndash-gradebook-user-grades',
				'options'    => [
					'title'          => _x( 'Gradebook by LearnDash', 'Quick Start Guide Completed Title', 'learndash-gradebook' ),
					'content_pieces' => [
						__( 'Congratulations! You have completed the Gradebook by LearnDash Quickstart tutorial.', 'learndash-gradebook' ),
						sprintf(
							_x( 'If you need any more help, please visit the %1$splugin documentation page%2$s.', '%s are both HTML for a link to the documentation', 'learndash-gradebook' ), // cspell: disable-line -- That's why I don't like the practice to include HTML in translations.
							'<a href="https://www.learndash.com/support/docs/add-ons/gradebook-add-on">',
							'</a>'
						),
						__( 'Click "Dismiss" to exit the Quickstart guide.', 'learndash-gradebook' ),
					],
					'position'       => [
						'edge'  => 'top',
						'align' => ( ! is_rtl() ) ? 'left' : 'right',
					],
				],
			],
		];

		foreach ( $included as $pointer ) {
			$this->add_pointer( $pointer );
		}
	}

	/**
	 * Adds a pointer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Pointer args.
	 */
	public function add_pointer( $args ) {
		$args = wp_parse_args(
			$args,
			[
				'pointer_id' => '',
				'target'     => '',
				'options'    => [
					'content'  => '',
					'position' => [
						'edge'  => ( ! is_rtl() ) ? 'left' : 'right',
						'align' => 'top,',
					],
				],
			]
		);

		/**
		 * Adds a pointer.
		 *
		 * If you pass false here, it will short circuit and not add the pointer.
		 *
		 * @since 1.0.0
		 */
		$args = apply_filters( 'ld_gb_add_pointer', $args, $this->pointers );

		if ( $args === false ) {
			return false;
		}

		$this->pointers[] = $args;

		return $this->pointers;
	}

	/**
	 * Is the quickstart enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function quickstart_enabled() {
		static $enabled;

		// Cache result
		if ( $enabled !== null ) {
			return $enabled;
		}

		// If the Role has had edit_courses revoked then it is impossible for them to do this
		if ( ! current_user_can( 'edit_courses' ) ) {
			return false;
		}

		$quickstart_roles = ld_gb_get_quickstart_roles();

		// Is this user role allowed to use the Quickstart?
		if ( ! ld_gb_current_user_match_roles( $quickstart_roles ) ) {
			return false;
		}

		return $enabled = get_user_meta( get_current_user_id(), 'ld_gb_disable_quickstart', true ) !== '1';
	}

	/**
	 * Is the user viewing a Quickstart page?
	 * Make sure to add the Query Param "ld_gb_quickstart" to your requests in order to ensure we can know whether they're viewing a Quickstart page or not
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public static function inside_quickstart() {
		return ( isset( $_REQUEST['ld_gb_quickstart'] ) && $_REQUEST['ld_gb_quickstart'] ) ? true : false;  }

	/**
	 * Restart the quickstart guide for the user.
	 *
	 * @since 1.1.1
	 * @access private
	 */
	function restart_quickstart() {
		ld_gb_increment_quickstart_guide_reset_counter();

		$this->enable_quickstart();

		wp_redirect( remove_query_arg( 'ld_gb_restart_quickstart' ) );
		exit();
	}

	/**
	 * Enables the quickstart.
	 *
	 * @since 1.0.0
	 */
	public function enable_quickstart() {
		/**
		 * Short-circuits this method and prevents enabling.
		 *
		 * @since 1.0.0
		 */
		if ( ! apply_filters( 'ld_gb_enable_quickstart', true ) ) {
			return true;
		}

		return delete_user_meta( get_current_user_id(), 'ld_gb_disable_quickstart' );
	}

	/**
	 * Disables the quickstart.
	 *
	 * @since 1.0.0
	 */
	public function disable_quickstart() {
		/**
		 * Short-circuits this method and prevents enabling.
		 *
		 * @since 1.0.0
		 */
		if ( ! apply_filters( 'ld_gb_disable_quickstart', true ) ) {
			return true;
		}

		return update_user_meta( get_current_user_id(), 'ld_gb_disable_quickstart', 1 );
	}

	/**
	 * Loads all of the admin pointers for the quickstart.
	 *
	 * @since 1.0.0
	 */
	public function load_pointers() {
		if ( ! self::quickstart_enabled() ) {
			return;
		}

		/**
		 * Fires before loading pointers. Add pointers here.
		 *
		 * @since 1.0.0
		 *
		 * @hooked LD_GB_QuickStart->included_pointers() 10
		 */
		do_action( 'ld_gb_load_pointers' );

		if ( empty( $this->pointers ) ) {
			return;
		}

		// Load pointers for this screen
		$current_screen = get_current_screen();
		$section        = isset( $_GET['section'] ) ? $_GET['section'] : false;

		// If we're viewing a Gradebook page but we are not in the Quickstart, do not load the Pointers
		// Checking against the screen id in this way may not be 100% reliable
		if ( ! self::inside_quickstart() && strpos( $current_screen->id, 'gradebook' ) !== false ) {
			return;
		}

		// Setup some pointer defaults
		foreach ( $this->pointers as $i => &$pointer ) {
			// Setup link
			if ( ! isset( $pointer['options']['quickstart_link'] ) && isset( $this->pointers[ $i + 1 ] ) ) {
				$pointer['options']['quickstart_link'] = $this->pointers[ $i + 1 ]['link'];
			}

			// Setup back link
			if ( ! isset( $pointer['options']['quickstart_back_link'] ) && isset( $this->pointers[ $i - 1 ] ) ) {
				$pointer['options']['quickstart_back_link'] = $this->pointers[ $i - 1 ]['link'];
			}

			// Setup content
			if ( isset( $pointer['options']['content_pieces'] ) ) {
				$pointer['options']['content'] = '';

				foreach ( $pointer['options']['content_pieces'] as $content_piece ) {
					$pointer['options']['content'] .= "<p>$content_piece</p>";
				}
			}

			// Add title to content and button
			if ( isset( $pointer['options']['title'] ) ) {
				$pointer['options']['content'] = "<h3>{$pointer['options']['title']}</h3>" . $pointer['options']['content'];

				if ( ! isset( $pointer['options']['quickstart_text'] ) &&
					isset( $pointer['options']['quickstart_link'] ) &&
					isset( $this->pointers[ $i + 1 ] )
				) {
					$pointer['options']['quickstart_text'] = sprintf(
						'Next: %s',
						$this->pointers[ $i + 1 ]['options']['title']
					);
				}
			}
		}
		unset( $pointer );

		$load_pointers = [];
		foreach ( $this->pointers as $pointer ) {
			if ( $pointer['screen'] == $current_screen->id ) {
				if ( isset( $pointer['section'] ) && $pointer['section'] != $section ) {
					continue;
				}

				$load_pointers[] = $pointer;
			}
		}

		// Ending pointer
		if ( isset( $_GET['ld_gb_quickstart_end'] ) ) {
			$load_pointers[] = $this->pointers[ count( $this->pointers ) - 1 ];
		}

		// If no pointers, assume we're on the first one
		if ( ! $load_pointers ) {
			$load_pointers[] = $this->pointers[0];
		}

		/**
		 * Filter pointers to show.
		 *
		 * @since 1.0.0
		 */
		$load_pointers = apply_filters( 'ld_gb_quickstart_pointers', $load_pointers );

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		wp_localize_script( 'ld-gb-admin', 'LD_GB_Quickstart', $load_pointers );
	}

	/**
	 * AJAX callback for cancelling the quickstart.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function ajax_disable_quickstart() {
		if ( $this->disable_quickstart() ) {
			wp_send_json_success();
		} else {
			wp_send_json_error(
				[
					'error' => 'Could not disable quickstart',
				]
			);
		}
	}
}
