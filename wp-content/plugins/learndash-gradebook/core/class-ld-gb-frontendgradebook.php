<?php
/**
 * Frontend Gradebook
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_FrontendGradebook
 *
 * Contains the grade for a given user.
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */
class LD_GB_FrontendGradebook {
	/**
	 * Whether or not this has been output.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private $used = false;

	/**
	 * LD_GB_FrontendGradebook constructor.
	 *
	 * @since 3.0.0
	 */
	function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );

		// Template actions
		add_action( 'ld_gb_frontend_gradebook_gradebook_dropdown', [ $this, 'gradebook_dropdown' ], 10, 2 );
		add_action( 'ld_gb_frontend_gradebook_group_dropdown', [ $this, 'group_dropdown' ], 10, 2 );
		add_action( 'ld_gb_frontend_gradebook_results', [ $this, 'gradebook_results' ], 10, 3 );
		add_action( 'ld_gb_frontend_gradebook_table_list', [ $this, 'gradebook_table_list' ], 10, 3 );
		add_action( 'ld_gb_frontend_gradebook_table_head', [ $this, 'gradebook_table_head' ], 10, 4 );
		add_action( 'ld_gb_frontend_gradebook_table_row', [ $this, 'gradebook_table_row' ], 10, 7 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel', [ $this, 'edit_panel' ], 10, 4 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_back_to_gradebook', [ $this, 'back_to_gradebook' ], 10, 2 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_user_grade', [ $this, 'edit_panel_user_grade' ], 10, 4 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_components', [ $this, 'edit_panel_components' ], 10, 5 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_component', [ $this, 'edit_panel_component' ], 10, 6 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_component_grade', [ $this, 'edit_panel_component_grade' ], 10, 5 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_grade_row', [ $this, 'edit_panel_grade_row' ], 10, 7 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_no_grades', [ $this, 'edit_panel_no_grades' ], 10, 4 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_grade_add', [ $this, 'edit_panel_grade_add' ], 10, 6 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_grade_edit', [ $this, 'edit_panel_grade_edit' ], 10, 5 );
		add_action( 'ld_gb_frontend_gradebook_edit_panel_component_override', [ $this, 'edit_panel_component_override' ], 10, 5 );
		add_action( 'ld_gb_frontend_gradebook_export_buttons', [ $this, 'export_buttons' ], 10, 2 );
		add_action( 'ld_gb_frontend_gradebook_notice', [ $this, 'notice' ], 10, 2 );    }

	/**
	 * Loads frontend Gradebook assets.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function enqueue_assets() {
		global $post;

		if ( ! is_object( $post ) ) {
			return;
		}

		// This should not be necessary to filter in 99.999% of cases
		// If you have a Shortcode or Block that is being used as a wrapper for the Frontend Gradebook then Filter this to add your Shortcode/Block
		$shortcodes_or_blocks = apply_filters( 'ld_gb_frontend_gradebook_load_assets_check', [ 'ld_gradebook', 'realbigplugins/frontend-gradebook-block' ] );

		$found = false;

		$elementor_contents = learndash_gradebook_get_elementor_contents( $post->ID );

		foreach ( $shortcodes_or_blocks as $shortcode_or_block ) {
			if ( has_shortcode( $post->post_content, $shortcode_or_block ) ) {
				$found = true;
				break;
			}

			if ( has_block( $shortcode_or_block, $post ) ) {
				$found = true;
				break;
			}

			foreach ( $elementor_contents as $content ) {
				if ( has_shortcode( $content, $shortcode_or_block ) ) {
					$found = true;
					break 2;
				}

				if ( has_block( $shortcode_or_block, $content ) ) {
					$found = true;
					break 2;
				}
			}
		}

		/**
		 * Change loading of frontend gradebook styles.
		 *
		 * @since 3.0.2
		 */
		$found = apply_filters( 'ld_gb_load_frontend_gradebook_styles', $found );

		if ( ! $found ) {
			return;
		}

		// If you need to make your script run _before_ Gradebook's, filter the Script/Style dependencies
		wp_enqueue_style( 'ld-gb-frontend-gradebook' );
		wp_style_add_data( 'ld-gb-frontend-gradebook', 'rtl', 'replace' );

		wp_enqueue_script( 'ld-gb-frontend-gradebook' );

		// If you need to make your script run _after_ Gradebook's, use this hook and set LD GB's Frontend Script/Style as your dependency
		do_action( 'ld_gb_frontend_gradebook_assets_enqueued' );    }

	/**
	 * Unloads report card assets if this was not output.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function unload_assets() {
		if ( ! $this->used ) {
			wp_dequeue_script( 'ld-gb-frontend-gradebook' );
		}   }

	/**
	 * Renders the output.
	 *
	 * @since 3.0.0
	 *
	 * @param array  $atts
	 * @param string $content
	 *
	 * @return mixed
	 */
	public function render( $atts = [], $is_block = false ) {
		// Remove empty data
		$atts = array_filter( $atts );

		$atts = shortcode_atts(
			[
				'gradebook'    => false,
				'grade_format' => ld_gb_get_option_field( 'grade_display_mode', 'letter' ),
			],
			$atts,
			'ld_frontend_gradebook'
		);

		ob_start();

		// Permissions are checked in the template.

		// Allow the theme to load the template instead of the plugin
		ld_gb_locate_template(
			'frontend-gradebook/frontend-gradebook.php',
			[
				'gradebook_id' => $atts['gradebook'],
				'grade_format' => $atts['grade_format'],
				'is_block'     => $is_block,
			]
		);

		return ob_get_clean();
	}

	/**
	 * Outputs the Gradebook Dropdown
	 *
	 * @param array   $gradebook_ids  Array of Gradebook IDs
	 * @param integer $gradebook_id   Gradebook to select by default
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function gradebook_dropdown( $gradebook_ids, $gradebook_id ) {
		ld_gb_locate_template(
			'frontend-gradebook/gradebook-dropdown.php',
			[
				'gradebook_ids' => $gradebook_ids,
				'gradebook_id'  => $gradebook_id,
			]
		);  }

	/**
	 * Outputs the Group Dropdown
	 *
	 * @param array   $group_ids  Array of Group IDs
	 * @param integer $group_id   Group to select by default
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function group_dropdown( $group_ids, $group_id ) {
		if ( empty( $group_ids ) ) {
			ld_gb_locate_template(
				'frontend-gradebook/errors/no-groups.php',
				[]
			);
		} else {
			ld_gb_locate_template(
				'frontend-gradebook/group-dropdown.php',
				[
					'group_ids' => $group_ids,
					'group_id'  => $group_id,
				]
			);
		}   }

	/**
	 * Loads the Gradebook Results
	 * This is used for both the default loaded Gradebook/Group Combo as well as when refreshed via the API
	 *
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function gradebook_results( $gradebook_id, $group_id, $grade_format ) {
		ld_gb_locate_template(
			'frontend-gradebook/gradebook-results.php',
			[
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
				'grade_format' => $grade_format,
			]
		);  }

	/**
	 * Outputs the Gradebook Table
	 *
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 * @param integer $grade_format  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function gradebook_table_list( $gradebook_id, $group_id, $grade_format ) {
		ld_gb_locate_template(
			'frontend-gradebook/table/table-list.php',
			[
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
				'grade_format' => $grade_format,
			]
		);  }

	/**
	 * Outputs the Table Headers
	 *
	 * @param array   $user_columns  User-defined Columns
	 * @param array   $components    Array of Component Keys and Names
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function gradebook_table_head( $user_columns, $components, $gradebook_id, $group_id ) {
		ld_gb_locate_template(
			'frontend-gradebook/table/table-head.php',
			[
				'user_columns' => $user_columns,
				'components'   => $components,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
			]
		);  }

	/**
	 * Outputs a Table Row
	 *
	 * @param integer $user_id       WP_User ID
	 * @param array   $user_columns  User-defined Columns
	 * @param array   $user_grade    User Grade
	 * @param string  $grade_format  Grade Display Format
	 * @param array   $components    Array of Component Keys and Names
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function gradebook_table_row( $user_id, $user_columns, $user_grade, $grade_format, $components, $gradebook_id, $group_id ) {
		ld_gb_locate_template(
			'frontend-gradebook/table/table-row.php',
			[
				'user_id'      => $user_id,
				'user_columns' => $user_columns,
				'user_grade'   => $user_grade,
				'grade_format' => $grade_format,
				'components'   => $components,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
			]
		);  }

	/**
	 * Outputs the Edit Panel
	 *
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel( $user_id, $gradebook_id, $group_id, $grade_format ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/edit-panel.php',
			[
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
				'grade_format' => $grade_format,
			]
		);  }

	/**
	 * Outputs the Back to Gradebook button
	 *
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function back_to_gradebook( $user_id, $gradebook_id ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/edit-panel-back-to-gradebook.php',
			[
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
			]
		);  }

	/**
	 * Outputs the Edit Panel User Grade
	 *
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 * @param object  $user_grade    LD_GB_UserGrade Object
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel_user_grade( $user_id, $gradebook_id, $user_grade, $grade_format ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/edit-panel-user-grade.php',
			[
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
				'user_grade'   => $user_grade,
				'grade_format' => $grade_format,
			]
		);  }

	/**
	 * Outputs the Edit Panel Components List
	 *
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 * @param object  $user_grade    LD_GB_UserGrade Object
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel_components( $user_id, $gradebook_id, $group_id, $user_grade, $grade_format ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/components/edit-panel-components.php',
			[
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
				'user_grade'   => $user_grade,
				'grade_format' => $grade_format,
			]
		);  }

	/**
	 * Outputs the Edit Panel Component
	 *
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 * @param object  $user_grade    LD_GB_UserGrade Object
	 * @param array   $component     Component Array
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel_component( $user_id, $gradebook_id, $group_id, $user_grade, $component, $grade_format ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/components/edit-panel-component.php',
			[
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
				'user_grade'   => $user_grade,
				'component'    => $component,
				'grade_format' => $grade_format,
			]
		);  }

	/**
	 * Outputs the Edit Panel Component Grade
	 *
	 * @param array   $component     Component Array
	 * @param string  $grade_format  Grade Display Format
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel_component_grade( $component, $grade_format, $user_id, $gradebook_id, $group_id ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/components/edit-panel-component-grade.php',
			[
				'component'    => $component,
				'grade_format' => $grade_format,
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
			]
		);  }

	/**
	 * Outputs the Edit Panel Grade Row
	 *
	 * @param array   $grade         Grade Array
	 * @param object  $user_grade    LD_GB_UserGrade Object
	 * @param array   $component     Component Array
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 * @param string  $grade_format  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel_grade_row( $grade, $user_grade, $component, $user_id, $gradebook_id, $group_id, $grade_format ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/components/edit-panel-grade-row.php',
			[
				'grade'        => $grade,
				'user_grade'   => $user_grade,
				'component'    => $component,
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
				'grade_format' => $grade_format,
			]
		);  }

	/**
	 * Outputs the Edit Panel "No Grades" Row
	 *
	 * @param object  $user_grade    LD_GB_UserGrade Object
	 * @param array   $component     Component Array
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel_no_grades( $user_grade, $component, $user_id, $gradebook_id ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/components/edit-panel-no-grades.php',
			[
				'user_grade'   => $user_grade,
				'component'    => $component,
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
			]
		);  }

	/**
	 * Outputs the Edit Panel Grade Add Form
	 *
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 * @param integer $component     Component Array
	 * @param integer $user_grade    LD_GB_UserGrade Object
	 * @param integer $grade_format  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel_grade_add( $user_id, $gradebook_id, $group_id, $component, $user_grade, $grade_format ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/components/edit-panel-grade-add.php',
			[
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
				'component'    => $component,
				'user_grade'   => $user_grade,
				'grade_format' => $grade_format,
			]
		);  }

	/**
	 * Outputs the Edit Panel Grade Edit Form
	 *
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 * @param integer $component     Component Array
	 * @param integer $grade_format  Grade Display Format
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel_grade_edit( $user_id, $gradebook_id, $group_id, $component, $grade_format ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/components/edit-panel-grade-edit.php',
			[
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
				'component'    => $component,
				'grade_format' => $grade_format,
			]
		);  }

	/**
	 * Outputs the Edit Panel Component Override Form
	 *
	 * @param integer $component     Component Array
	 * @param integer $grade_format  Grade Display Format
	 * @param integer $user_id       User ID
	 * @param integer $gradebook_id  Gradebook ID
	 * @param integer $group_id      Group ID
	 *
	 * @access public
	 * @since 3.0.0
	 * @return void
	 */
	public function edit_panel_component_override( $component, $grade_format, $user_id, $gradebook_id, $group_id ) {
		ld_gb_locate_template(
			'frontend-gradebook/edit-panel/components/edit-panel-component-override.php',
			[
				'component'    => $component,
				'grade_format' => $grade_format,
				'user_id'      => $user_id,
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
			]
		);  }

	/**
	 * Outputs the Gradebook Export Buttons.
	 *
	 * @since 3.0.0
	 *
	 * @param int $gradebook_id Gradebook ID.
	 * @param int $group_id     Group ID.
	 *
	 * @return void
	 */
	public function export_buttons( $gradebook_id, $group_id ) {
		ld_gb_locate_template(
			'frontend-gradebook/export-buttons.php',
			[
				'gradebook_id' => $gradebook_id,
				'group_id'     => $group_id,
			]
		);
	}

	/**
	 * Outputs the Error Notice. Can be used for Success Messages too by changing the Type to "success".
	 *
	 * @since 3.0.0
	 *
	 * @param string $message Message Text.
	 * @param string $type    Notice Type. Default is "alert".
	 *
	 * @return void Echoes HTML.
	 */
	public function notice( $message, $type = 'alert' ) {
		ld_gb_locate_template(
			'frontend-gradebook/errors/dialog.php',
			[
				'message' => $message,
				'type'    => $type,
			]
		);
	}
}
