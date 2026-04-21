<?php
/**
 * Report Card
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_ReportCard
 *
 * Contains the grade for a given user.
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */
class LD_GB_ReportCard {
	/**
	 * Whether or not this has been output.
	 *
	 * @since 3.0.0
	 *
	 * @var bool
	 */
	private $used = false;

	/**
	 * LD_GB_ReportCard constructor.
	 *
	 * @since 3.0.0
	 */
	function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'wp_print_footer_scripts', [ $this, 'unload_assets' ], 1 );

		// Template actions
		add_action( 'report-card-expand-collapse', [ $this, 'template_expand_collapse' ], 10, 2 );
		add_action( 'report-card-title', [ $this, 'template_title' ], 10, 2 );
		add_action( 'report-card-overall-grade', [ $this, 'template_overall_grade' ], 10, 3 );
		add_action( 'report-card-component', [ $this, 'template_component' ], 10, 4 );
		add_action( 'report-card-component-toggle', [ $this, 'template_component_toggle' ], 10, 4 );
		add_action( 'report-card-component-info', [ $this, 'template_component_title' ], 10, 5 );
		add_action( 'report-card-component-info', [ $this, 'template_component_grade' ], 20, 5 );
		add_action( 'report-card-grades-header', [ $this, 'template_grades_header' ], 10, 3 );
		add_action( 'report-card-grade', [ $this, 'template_grade' ], 10, 5 );
		add_action( 'report-card-grade-content', [ $this, 'template_grade_type' ], 10, 5 );
		add_action( 'report-card-grade-content', [ $this, 'template_grade_name' ], 20, 5 );
		add_action( 'report-card-grade-content', [ $this, 'template_grade_score' ], 30, 5 );
	}

	/**
	 * Loads report card assets.
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
		// If you have a Shortcode or Block that is being used as a wrapper for the Report Card then Filter this to add your Shortcode/Block
		$shortcodes_or_blocks = apply_filters( 'ld_gb_report_card_load_assets_check', [ 'ld_report_card', 'realbigplugins/report-card-block' ] );

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
		 * Change loading of report card styles.
		 *
		 * @since 1.0.0
		 */
		$load_report_card_styles = apply_filters( 'ld_gb_report_card_styles', $found );

		if ( $load_report_card_styles ) {
			wp_enqueue_style( 'ld-gb-report-card' );
			wp_style_add_data( 'ld-gb-report-card', 'rtl', 'replace' );

			// LearnDash v3.x changed how their CSS was loaded, so we need to load in the images we were using from them before like this

			if ( defined( 'LEARNDASH_VERSION' ) ) {
				// Pad in a Patch version if necessary
				$ld_version = ( substr_count( LEARNDASH_VERSION, '.' ) == 1 ) ? LEARNDASH_VERSION . '.0' : LEARNDASH_VERSION;

				// @phpstan-ignore-next-line -- This constant can be changed.
				if ( version_compare( $ld_version, '3.0.0', '>=' ) ) : ?>
					<style type="text/css">
						.ld-gb-report-card-component-expand.list_arrow.collapse {
							background: url("<?php echo LEARNDASH_LMS_PLUGIN_URL; ?>assets/images/gray_arrow_collapse.png") no-repeat scroll 0 50% transparent;
							padding: 5px;
						}
						.ld-gb-report-card-component-expand.list_arrow.expand {
							background: url("<?php echo LEARNDASH_LMS_PLUGIN_URL; ?>assets/images/gray_arrow_expand.png") no-repeat scroll 0 50% transparent;
							padding: 5px;
						}
					</style>
					<?php
				endif;
			}
		}
	}

	/**
	 * Unloads report card assets if this was not output.
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function unload_assets() {
		if ( ! $this->used ) {
			wp_dequeue_script( 'ld-gb-report-card' );
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
				'user'               => get_current_user_id(),
				'gradebook'          => false,
				'logged_out_message' => __( 'Please log in to view your report card.', 'learndash-gradebook' ),
				'grade_format'       => ld_gb_get_option_field( 'grade_display_mode', 'letter' ),
			],
			$atts,
			'ld_report_card'
		);

		// Tell LearnDash to load template assets
		global $learndash_shortcode_used;
		$learndash_shortcode_used = true;

		// Tell LD GB to load the report card assets
		$this->used = true;

		if ( ! is_user_logged_in() ) {
			return '<div class="ld-gb-report-card-container">' . $atts['logged_out_message'] . '</div>';
		}

		// Get user
		if ( ! ( $user = get_user_by( 'id', $atts['user'] ) ) ) {
			return '<div class="ld-gb-report-card-container">' . _x( 'Cannot get user.', 'Report Card Shortcode/Block missing a User ID error', 'learndash-gradebook' ) . '</div>';
		}

		$current_user_id = get_current_user_id();

		$current_user_has_access = ld_gb_is_super_admin()
			|| (int) $atts['user'] === $current_user_id
			|| (
				learndash_is_group_leader_user( $current_user_id )
				&& (
					learndash_get_group_leader_manage_users() === 'advanced'
					|| learndash_is_group_leader_of_user( $current_user_id, $atts['user'] )
				)
			);

		if ( ! $current_user_has_access ) {
			return '<div class="ld-gb-report-card-container">' . esc_html__( 'You do not have access to view this report card.', 'learndash-gradebook' ) . '</div>';
		}

		$gradebooks = [];

		// Load all Gradebooks that the Student is in specifically
		if ( ! $atts['gradebook'] ) {
			$user_progress = SFWD_LMS::get_course_info(
				$atts['user'],
				[
					'user_id' => $atts['user'],
					'return'  => true,
					'type'    => 'registered',
				]
			);

			if ( isset( $user_progress['courses_registered'] ) &&
				! empty( $user_progress['courses_registered'] ) ) {
				foreach ( $user_progress['courses_registered'] as $course_id ) {
					// Get all Gradebooks that have a specific Course set that this Student is enrolled in and have begun (This includes Open courses)
					$gradebook_query = new WP_Query(
						[
							'post_type'      => 'gradebook',
							'posts_per_page' => -1,
							'fields'         => 'ids',
							'post_status'    => 'publish',
							'meta_query'     => [
								'relation' => 'OR',
								[
									'key'   => 'ld_gb_course',
									// Legacy
									'value' => $course_id,
								],
								[
									'key'     => 'ld_gb_course',
									'compare' => 'LIKE',
									'value'   => '"' . $course_id . '"',
								],
							],
						]
					);

					if ( $gradebook_query->have_posts() ) {
						foreach ( $gradebook_query->posts as $gradebook_id ) {
							$gradebooks[] = $gradebook_id;
						}
					}
				}
			}

			// If the Gradebook has "All Courses" set, then we should show that too
			$gradebook_query = new WP_Query(
				[
					'post_type'      => 'gradebook',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'post_status'    => 'publish',
					'meta_query'     => [
						'relation' => 'OR',
						[
							// Legacy
							'key'   => 'ld_gb_course',
							'value' => '',
						],
						[
							'key'   => 'ld_gb_course',
							'value' => 'a:1:{i:0;s:0:"";}',
						],
						[
							// Legacy
							'key'   => 'ld_gb_course',
							'value' => 'all',
						],
						[
							'key'     => 'ld_gb_course',
							'compare' => 'LIKE',
							'value'   => '"all"',
						],
						[
							'key'   => 'ld_gb_include_all_users',
							'value' => '1',
						],
					],
				]
			);

			if ( $gradebook_query->have_posts() ) {
				foreach ( $gradebook_query->posts as $gradebook_id ) {
					$gradebooks[] = $gradebook_id;
				}
			}

			$gradebooks = array_unique( $gradebooks );
		} else {
			$gradebooks = [ $atts['gradebook'] ];
		}

		ob_start();

		if ( empty( $gradebooks ) ) {
			if ( $is_block && defined( 'REST_REQUEST' ) ) {
				?>

				<div class="admin-notice error ld-gb-report-card-no-gradebooks">

				<?php

			}

			ld_gb_locate_template( 'report-card/report-card-error.php' );

			if ( $is_block && defined( 'REST_REQUEST' ) ) {
				?>

				</div>

				<?php

			}
		} else {
			foreach ( $gradebooks as $gradebook_id ) {
				if ( ! get_post( $gradebook_id ) ) {
					ld_gb_locate_template( 'report-card/report-card-error.php' );
				}

				$user_grade = new LD_GB_UserGrade( $user, $gradebook_id );

				// Legacy template override
				if ( $template_file = locate_template( [ '/learndash/report-card.php' ] ) ) {
					include $template_file;
				} else {
					// Allow the theme to load the template instead of the plugin
					ld_gb_locate_template(
						'report-card/report-card.php',
						[
							'user_grade'   => $user_grade,
							'gradebook_id' => $gradebook_id,
							'grade_format' => $atts['grade_format'],
							'is_block'     => $is_block,
						]
					);
				}
			}
		}

		return ob_get_clean();
	}

	/**
	 * Outputs the report card expand/collapse.
	 *
	 * @since 3.0.0
	 *
	 * @param LD_GB_UserGrade $user_grade   User Grade object.
	 * @param int             $gradebook_id Gradebook ID.
	 *
	 * @return void
	 */
	public function template_expand_collapse( $user_grade, $gradebook_id ) {
		ld_gb_locate_template(
			'report-card/expand-collapse.php',
			[
				'user_grade'   => $user_grade,
				'gradebook_id' => $gradebook_id,
			]
		);
	}

	/**
	 * Outputs the report card title.
	 *
	 * @since 3.0.0
	 *
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 */
	public function template_title( $user_grade, $gradebook_id ) {
		ld_gb_locate_template(
			'report-card/title.php',
			[
				'user_grade'   => $user_grade,
				'gradebook_id' => $gradebook_id,
			]
		);
	}

	/**
	 * Outputs the report card overall grade.
	 *
	 * @since 3.0.0
	 *
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 * @param string          $grade_format
	 */
	public function template_overall_grade( $user_grade, $gradebook_id, $grade_format = 'letter' ) {
		ld_gb_locate_template(
			'report-card/overall-grade.php',
			[
				'user_grade'   => $user_grade,
				'gradebook_id' => $gradebook_id,
				'grade_format' => $grade_format,
			]
		);
	}

	/**
	 * Outputs the report card component.
	 *
	 * @since 3.0.0
	 *
	 * @param array           $component
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 * @param string          $grade_format
	 */
	public function template_component( $component, $user_grade, $gradebook_id, $grade_format = 'letter' ) {
		ld_gb_locate_template(
			'report-card/component/component.php',
			[
				'user_grade'   => $user_grade,
				'component'    => $component,
				'gradebook_id' => $gradebook_id,
				'grade_format' => $grade_format,
			]
		);
	}

	/**
	 * Outputs the report card toggle.
	 *
	 * @since 3.0.0
	 *
	 * @param array           $component
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 * @param string          $component_handle
	 */
	public function template_component_toggle( $component, $user_grade, $gradebook_id, $component_handle ) {
		ld_gb_locate_template(
			'report-card/component/toggle.php',
			[
				'user_grade'       => $user_grade,
				'component'        => $component,
				'gradebook_id'     => $gradebook_id,
				'component_handle' => $component_handle,
			]
		);
	}

	/**
	 * Outputs the report card title.
	 *
	 * @since 3.0.0
	 *
	 * @param array           $component
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 * @param string          $component_handle
	 * @param string          $grade_format
	 */
	public function template_component_title( $component, $user_grade, $gradebook_id, $component_handle, $grade_format = 'letter' ) {
		ld_gb_locate_template(
			'report-card/component/title.php',
			[
				'user_grade'       => $user_grade,
				'component'        => $component,
				'gradebook_id'     => $gradebook_id,
				'component_handle' => $component_handle,
				'grade_format'     => $grade_format,
			]
		);
	}

	/**
	 * Outputs the report card grade.
	 *
	 * @since 3.0.0
	 *
	 * @param array           $component
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 * @param string          $component_handle
	 * @param string          $grade_format
	 */
	public function template_component_grade( $component, $user_grade, $gradebook_id, $component_handle, $grade_format = 'letter' ) {
		ld_gb_locate_template(
			'report-card/component/grade.php',
			[
				'user_grade'       => $user_grade,
				'component'        => $component,
				'gradebook_id'     => $gradebook_id,
				'component_handle' => $component_handle,
				'grade_format'     => $grade_format,
			]
		);
	}

	/**
	 * Outputs the report card grades header.
	 *
	 * @since 3.0.0
	 *
	 * @param array           $component
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 */
	public function template_grades_header( $component, $user_grade, $gradebook_id ) {
		ld_gb_locate_template(
			'report-card/component/grades-header.php',
			[
				'user_grade'   => $user_grade,
				'component'    => $component,
				'gradebook_id' => $gradebook_id,
			]
		);
	}

	/**
	 * Outputs the report card grade.
	 *
	 * @since 3.0.0
	 *
	 * @param string          $grade
	 * @param int             $grade_index
	 * @param array           $component
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 */
	public function template_grade( $grade, $grade_index, $component, $user_grade, $gradebook_id ) {
		ld_gb_locate_template(
			'report-card/grade/grade.php',
			[
				'grade'        => $grade,
				'grade_index'  => $grade_index,
				'user_grade'   => $user_grade,
				'component'    => $component,
				'gradebook_id' => $gradebook_id,
			]
		);
	}

	/**
	 * Outputs the report card grade type.
	 *
	 * @since 3.0.0
	 *
	 * @param string          $grade
	 * @param int             $grade_index
	 * @param array           $component
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 */
	public function template_grade_type( $grade, $grade_index, $component, $user_grade, $gradebook_id ) {
		ld_gb_locate_template(
			'report-card/grade/type.php',
			[
				'grade'        => $grade,
				'grade_index'  => $grade_index,
				'user_grade'   => $user_grade,
				'component'    => $component,
				'gradebook_id' => $gradebook_id,
			]
		);
	}

	/**
	 * Outputs the report card grade name.
	 *
	 * @since 3.0.0
	 *
	 * @param string          $grade
	 * @param int             $grade_index
	 * @param array           $component
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 */
	public function template_grade_name( $grade, $grade_index, $component, $user_grade, $gradebook_id ) {
		ld_gb_locate_template(
			'report-card/grade/name.php',
			[
				'grade'        => $grade,
				'grade_index'  => $grade_index,
				'user_grade'   => $user_grade,
				'component'    => $component,
				'gradebook_id' => $gradebook_id,
			]
		);
	}

	/**
	 * Outputs the report card grade core.
	 *
	 * @since 3.0.0
	 *
	 * @param string          $grade
	 * @param int             $grade_index
	 * @param array           $component
	 * @param LD_GB_UserGrade $user_grade
	 * @param int             $gradebook_id
	 */
	public function template_grade_score( $grade, $grade_index, $component, $user_grade, $gradebook_id ) {
		ld_gb_locate_template(
			'report-card/grade/score.php',
			[
				'grade'        => $grade,
				'grade_index'  => $grade_index,
				'user_grade'   => $user_grade,
				'component'    => $component,
				'gradebook_id' => $gradebook_id,
			]
		);
	}
}
