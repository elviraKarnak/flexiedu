<?php
/**
 * This file contains the common functions.
 *
 * @package LearnDash\Reports
 *
 * cspell:ignore dashboad nonlogged recomendation
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wrld_add_admin_menus' ) ) {
	/**
	 * Plugin menus.
	 */
	function wrld_add_admin_menus() {
		include_once WRLD_REPORTS_PATH . '/includes/admin/dashboard/class-dashboard.php';
		new \WRLDAdmin\Dashboard();
	}
}


if ( ! function_exists( 'wrld_register_blocks' ) ) {
	/**
	 * Registers all the supported blocks in the plugin
	 */
	function wrld_register_blocks() {
		if ( defined( 'LEARNDASH_VERSION' ) ) {
			add_filter( 'block_categories_all', 'wrld_add_custom_block_category', 10, 2 );
			wp_register_script( 'wrld-common-script', WRLD_REPORTS_SITE_URL . '/includes/blocks/src/commons/common-functions.js', array(), LDRP_PLUGIN_VERSION, false );
			wp_localize_script( 'wrld-common-script', 'wisdm_ld_reports_common_script_data', wrld_get_common_script_localized_data() );

			// Backwards compatibility alias for the old variable name. Added in version 3.0.5. Can be deleted later.
			wp_add_inline_script(
				'wrld-common-script',
				'Object.defineProperty(window, "wisdm_learndash_reports_front_end_script_report_filters", {
					get: function() {
						console.warn("wisdm_learndash_reports_front_end_script_report_filters is deprecated. Use wisdm_ld_reports_common_script_data instead.");
						return window.wisdm_ld_reports_common_script_data;
					},
					configurable: true
				});'
			);

			include_once WRLD_REPORTS_PATH . '/includes/blocks/registry/class-wrld-register-block-types.php';
			new WisdmReportsLearndashBlockRegistry\WRLD_Register_Block_Types();
		}
	}
}

if ( ! function_exists( 'wrld_add_custom_block_category' ) ) {
	/**
	 * Creates the custom block category in the block registry, this will later be useful to categorize
	 * the blocks added by our plugin
	 *
	 * @param array  $block_categories existing block categories.
	 * @param string $editor_context context.
	 */
	function wrld_add_custom_block_category( $block_categories, $editor_context ) {
		if ( ! empty( $editor_context->post ) ) {
			array_push(
				$block_categories,
				array(
					'slug'  => 'wisdm-learndash-reports',
					'title' => __( 'ProPanel', 'learndash-reports-pro' ),
					'icon'  => null,
				)
			);
		}
		return $block_categories;
	}
}

if ( ! function_exists( 'wrld_register_patterns' ) ) {
	/**
	 * Registers the default pattern of blocks for the reports plugin
	 */
	function wrld_register_patterns() {
		global $wrld_pattern, $wrld_student_dashboard_pattern;

		$reports_page_pattern_blocks = [
			[
				'blockName'    => 'core/columns',
				'attrs'        => [
					'className' => 'wrld-mw-1400',
				],
				'innerBlocks'  => [
					[
						'blockName'    => 'core/column',
						'attrs'        => [
							'width'     => '',
							'className' => 'lr-top-tiles',
						],
						'innerBlocks'  => [
							[
								'blockName'    => 'wisdm-learndash-reports/date-filters',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-date-filters"><div class="wisdm-learndash-reports-chart-block"><div class="wisdm-learndash-reports-date-filters front"></div></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-date-filters"><div class="wisdm-learndash-reports-chart-block"><div class="wisdm-learndash-reports-date-filters front"></div></div></div>',
								],
							],
							[
								'blockName'    => 'core/columns',
								'attrs'        => [
									'className' => 'lr-tiles-container',
								],
								'innerBlocks'  => [
									[
										'blockName'    => 'core/column',
										'attrs'        => [
											'className' => 'lr-tre',
										],
										'innerBlocks'  => [
											[
												'blockName' => 'wisdm-learndash-reports/total-revenue-earned',
												'attrs' => [
													'lock' => [
														'remove' => true,
														'move'   => false,
													],
												],
												'innerBlocks' => [],
												'innerHTML' => '<div class="wp-block-wisdm-learndash-reports-total-revenue-earned"><div class="wisdm-learndash-reports-total-revenue-earned front"></div></div>',
												'innerContent' => [
													'<div class="wp-block-wisdm-learndash-reports-total-revenue-earned"><div class="wisdm-learndash-reports-total-revenue-earned front"></div></div>',
												],
											],
										],
										'innerHTML'    => '<div class="wp-block-column lr-tre"></div>',
										'innerContent' => [
											'<div class="wp-block-column lr-tre">',
											null,
											'</div>',
										],
									],
									[
										'blockName'    => 'core/column',
										'attrs'        => [],
										'innerBlocks'  => [
											[
												'blockName' => 'wisdm-learndash-reports/total-courses',
												'attrs' => [
													'lock' => [
														'remove' => true,
														'move'   => false,
													],
												],
												'innerBlocks' => [],
												'innerHTML' => '<div class="wp-block-wisdm-learndash-reports-total-courses"><div class="wisdm-learndash-reports-total-courses front"></div></div>',
												'innerContent' => [
													'<div class="wp-block-wisdm-learndash-reports-total-courses"><div class="wisdm-learndash-reports-total-courses front"></div></div>',
												],
											],
										],
										'innerHTML'    => '<div class="wp-block-column"></div>',
										'innerContent' => [
											'<div class="wp-block-column">',
											null,
											'</div>',
										],
									],
									[
										'blockName'    => 'core/column',
										'attrs'        => [],
										'innerBlocks'  => [
											[
												'blockName' => 'wisdm-learndash-reports/total-learners',
												'attrs' => [
													'lock' => [
														'remove' => true,
														'move'   => false,
													],
												],
												'innerBlocks' => [],
												'innerHTML' => '<div class="wp-block-wisdm-learndash-reports-total-learners"><div class="wisdm-learndash-reports-total-learners front"></div></div>',
												'innerContent' => [
													'<div class="wp-block-wisdm-learndash-reports-total-learners"><div class="wisdm-learndash-reports-total-learners front"></div></div>',
												],
											],
										],
										'innerHTML'    => '<div class="wp-block-column"></div>',
										'innerContent' => [
											'<div class="wp-block-column">',
											null,
											'</div>',
										],
									],
									[
										'blockName'    => 'core/column',
										'attrs'        => [],
										'innerBlocks'  => [
											[
												'blockName' => 'wisdm-learndash-reports/pending-assignments',
												'attrs' => [
													'lock' => [
														'remove' => true,
														'move'   => false,
													],
												],
												'innerBlocks' => [],
												'innerHTML' => '<div class="wp-block-wisdm-learndash-reports-pending-assignments"><div class="wisdm-learndash-reports-pending-assignments front"></div></div>',
												'innerContent' => [
													'<div class="wp-block-wisdm-learndash-reports-pending-assignments"><div class="wisdm-learndash-reports-pending-assignments front"></div></div>',
												],
											],
										],
										'innerHTML'    => '<div class="wp-block-column"></div>',
										'innerContent' => [
											'<div class="wp-block-column">',
											null,
											'</div>',
										],
									],
								],
								'innerHTML'    => '<div class="wp-block-columns lr-tiles-container"></div>',
								'innerContent' => [
									'<div class="wp-block-columns lr-tiles-container">',
									null,
									'',
									null,
									'',
									null,
									'',
									null,
									'</div>',
								],
							],
						],
						'innerHTML'    => '<div class="wp-block-column lr-top-tiles">
					</div>',
						'innerContent' => [
							'<div class="wp-block-column lr-top-tiles">',
							null,
							'',
							null,
							'</div>',
						],
					],
				],
				'innerHTML'    => '<div class="wp-block-columns wrld-mw-1400"></div>',
				'innerContent' => [
					'<div class="wp-block-columns wrld-mw-1400">',
					null,
					'</div>',
				],
			],
			[
				'blockName'    => 'core/columns',
				'attrs'        => [
					'className' => 'wrld-mw-1400',
				],
				'innerBlocks'  => [
					[
						'blockName'    => 'core/column',
						'attrs'        => [
							'className' => 'wisdm-reports',
						],
						'innerBlocks'  => [
							[
								'blockName'    => 'wisdm-learndash-reports/revenue-from-courses',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-revenue-from-courses"><div class="wisdm-learndash-reports-revenue-from-courses front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-revenue-from-courses"><div class="wisdm-learndash-reports-revenue-from-courses front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/daily-enrollments',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-daily-enrollments"><div class="wisdm-learndash-reports-daily-enrollments front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-daily-enrollments"><div class="wisdm-learndash-reports-daily-enrollments front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/report-filters',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-report-filters"><div class="wisdm-learndash-reports-report-filters front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-report-filters"><div class="wisdm-learndash-reports-report-filters front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/time-spent-on-a-course',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-time-spent-on-a-course"><div class="wisdm-learndash-reports-time-spent-on-a-course"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-time-spent-on-a-course"><div class="wisdm-learndash-reports-time-spent-on-a-course"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/course-completion-rate',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-course-completion-rate"><div class="wisdm-learndash-reports-course-completion-rate front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-course-completion-rate"><div class="wisdm-learndash-reports-course-completion-rate front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/course-progress-rate',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-course-progress-rate"><div class="wisdm-learndash-reports-course-progress-rate front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-course-progress-rate"><div class="wisdm-learndash-reports-course-progress-rate front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/quiz-completion-rate-per-course',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-quiz-completion-rate-per-course"><div class="wisdm-learndash-reports-quiz-completion-rate-per-course front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-quiz-completion-rate-per-course"><div class="wisdm-learndash-reports-quiz-completion-rate-per-course front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/quiz-completion-time-per-course',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-quiz-completion-time-per-course"><div class="wisdm-learndash-reports-quiz-completion-time-per-course front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-quiz-completion-time-per-course"><div class="wisdm-learndash-reports-quiz-completion-time-per-course front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/learner-pass-fail-rate-per-course',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-learner-pass-fail-rate-per-course"><div class="wisdm-learndash-reports-learner-pass-fail-rate-per-course front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-learner-pass-fail-rate-per-course"><div class="wisdm-learndash-reports-learner-pass-fail-rate-per-course front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/average-quiz-attempts',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-average-quiz-attempts"><div class="wisdm-learndash-reports-average-quiz-attempts front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-average-quiz-attempts"><div class="wisdm-learndash-reports-average-quiz-attempts front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/inactive-users',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-inactive-users"><div class="wisdm-learndash-reports-inactive-users front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-inactive-users"><div class="wisdm-learndash-reports-inactive-users front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/learner-activity-log',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-learner-activity-log"><div class="wisdm-learndash-reports-learner-activity-log front"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-learner-activity-log"><div class="wisdm-learndash-reports-learner-activity-log front"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/course-list',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-course-list"><div class="wisdm-learndash-reports-course-list"></div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-course-list"><div class="wisdm-learndash-reports-course-list"></div></div>',
								],
							],
							[
								'blockName'    => 'wisdm-learndash-reports/quiz-reports',
								'attrs'        => [
									'lock' => [
										'remove' => true,
										'move'   => false,
									],
								],
								'innerBlocks'  => [],
								'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-quiz-reports"><div id="wisdm-learndash-reports-quiz-report-view" class="wisdm-learndash-reports-quiz-reports">[ldrp_quiz_reports]</div></div>',
								'innerContent' => [
									'<div class="wp-block-wisdm-learndash-reports-quiz-reports"><div id="wisdm-learndash-reports-quiz-report-view" class="wisdm-learndash-reports-quiz-reports">[ldrp_quiz_reports]</div></div>',
								],
							],
						],
						'innerHTML'    => '<div class="wp-block-column wisdm-reports"></div>',
						'innerContent' => [
							'<div class="wp-block-column wisdm-reports">',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'',
							null,
							'</div>',
						],
					],
				],
				'innerHTML'    => '<div class="wp-block-columns wrld-mw-1400"></div>',
				'innerContent' => [
					'<div class="wp-block-columns wrld-mw-1400">',
					null,
					'</div>',
				],
			],
		];

		/**
		 * Filters the Pattern Blocks.
		 *
		 * @since 3.0.0
		 *
		 * @param array{blockName: string, attrs: array<string, mixed>, innerBlocks: array<string, mixed>[], innerHTML: string, innerContent: mixed[] }[] $blocks       Blocks to use for the Reports Page Pattern.
		 * @param string                                                                                                                                      $pattern_name Pattern Name.
		 *
		 * @return array{blockName: string, attrs: array<string, mixed>, innerBlocks: array<string, mixed>[], innerHTML: string, innerContent: mixed[] }[] Blocks to use for the Reports Page Pattern.
		 */
		$reports_page_pattern_blocks = apply_filters(
			'learndash_propanel_pattern_blocks',
			$reports_page_pattern_blocks,
			'wisdm-learndash-reports/default-report-pattern'
		);

		/**
		 * Filters the Pattern Category slugs to show the Pattern within.
		 *
		 * @since 3.0.0
		 *
		 * @param string[] $pattern_categories Pattern Category slugs.
		 * @param string   $pattern_name       Pattern name.
		 *
		 * @return string[]
		 */
		$reports_page_pattern_categories = apply_filters(
			'learndash_propanel_pattern_categories',
			[
				'wisdm-ld-reports',
			],
			'wisdm-learndash-reports/default-report-pattern'
		);

		$pattern = array(
			'title'       => __( 'ProPanel Dashboard', 'learndash-reports-pro' ),
			'description' => __( 'This pattern can be used to quickly create the reporting dashboard on any page.', 'learndash-reports-pro' ),
			'categories'  => $reports_page_pattern_categories,
			'content'     => serialize_blocks( $reports_page_pattern_blocks ),
		);

		$wrld_pattern = $pattern['content'];
		register_block_pattern( 'wisdm-learndash-reports/default-report-pattern', $pattern );

		// student dashboard pattern

		$student_page_pattern_blocks = [
			[
				'blockName'    => 'wisdm-learndash-reports/student-profile',
				'attrs'        => [
					'lock' => [
						'remove' => true,
						'move'   => false,
					],
				],
				'innerBlocks'  => [],
				'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-student-dashboard wp-block-wisdm-learndash-reports-student-profile"><div class="wisdm-learndash-reports-student-profile front"></div></div>',
				'innerContent' => [
					'<div class="wp-block-wisdm-learndash-reports-student-dashboard wp-block-wisdm-learndash-reports-student-profile"><div class="wisdm-learndash-reports-student-profile front"></div></div>',
				],
			],
			[
				'blockName'    => 'wisdm-learndash-reports/student-table',
				'attrs'        => [
					'lock' => [
						'remove' => true,
						'move'   => false,
					],
				],
				'innerBlocks'  => [],
				'innerHTML'    => '<div class="wp-block-wisdm-learndash-reports-student-table"><div class="wisdm-learndash-reports-student-table front"></div></div>',
				'innerContent' => [
					'<div class="wp-block-wisdm-learndash-reports-student-table"><div class="wisdm-learndash-reports-student-table front"></div></div>',
				],
			],
		];

		/** This filter is documented in includes/reports-base/includes/functions.php */
		$student_page_pattern_blocks = apply_filters(
			'learndash_propanel_pattern_blocks',
			$student_page_pattern_blocks,
			'wisdm-learndash-reports/student-dashboard-pattern'
		);

		/** This filter is documented in includes/reports-base/includes/functions.php */
		$student_page_pattern_categories = apply_filters(
			'learndash_propanel_pattern_categories',
			[
				'wisdm-ld-reports',
			],
			'wisdm-learndash-reports/student-dashboard-pattern'
		);

		$student_dashboard_pattern = array(
			'title'       => sprintf(
				// translators: placeholder: Custom quiz label.
				__( 'Student %s Results', 'learndash-reports-pro' ),
				learndash_get_custom_label( 'quiz' )
			),
			'description' => sprintf(
				// translators: placeholder: Custom quiz label.
				__( 'This pattern can be used to quickly create show student %s results on any page.', 'learndash-reports-pro' ),
				learndash_get_custom_label_lower( 'quiz' )
			),
			'categories'  => $student_page_pattern_categories,
			'content'     => serialize_blocks( $student_page_pattern_blocks ),
		);

		$wrld_student_dashboard_pattern = $student_dashboard_pattern['content'];
		register_block_pattern(
			'wisdm-learndash-reports/student-dashboard-pattern',
			$student_dashboard_pattern
		);
	}
}

if ( ! function_exists( 'wrld_create_patterns_page' ) ) {
	/**
	 * On the first activation of the plugin this function creates a new page with the
	 * reports pattern if not exists.
	 *
	 * @param bool $force_create To create reporting page forcefully.
	 */
	function wrld_create_patterns_page( $force_create = false ) {
		if ( ! get_option( 'ldrp_reporting_page', false ) || $force_create ) {
			global $wrld_pattern;
			$page = wp_insert_post(
				array(
					'post_title'   => 'ProPanel Dashboard',
					'post_name'    => 'reporting-dashboard',
					'post_content' => $wrld_pattern,
					'post_status'  => 'draft',
					'post_type'    => 'page',
				)
			);
			if ( ! is_wp_error( $page ) ) {
				update_option( 'ldrp_reporting_page', $page );
				$edit_link = get_edit_post_link( $page );
				if ( ! empty( $edit_link ) && $force_create ) {
					wp_safe_redirect( htmlspecialchars_decode( $edit_link ) );
					exit;
				}
			}
		}
	}
}

if ( ! function_exists( 'wrld_create_student_patterns_page' ) ) {
	/**
	 * On the first activation of the plugin this function creates a new page with the
	 * reports pattern if not exists.
	 *
	 * @param bool $force_create To create reporting page forcefully.
	 */
	function wrld_create_student_patterns_page( $force_create = false ) {
		if ( ( ! get_option( 'ldrp_student_page', false ) && defined( 'LDRP_PLUGIN_VERSION' ) ) || $force_create ) {
			global $wrld_student_dashboard_pattern;
			$page = wp_insert_post(
				array(
					'post_title'   => 'Student Quiz Results',
					'post_name'    => 'student-dashboard',
					'post_content' => $wrld_student_dashboard_pattern,
					'post_status'  => 'draft',
					'post_type'    => 'page',
				)
			);
			if ( ! is_wp_error( $page ) ) {
				update_option( 'ldrp_student_page', $page );
				$edit_link = get_edit_post_link( $page );
				if ( ! empty( $edit_link ) && $force_create ) {
					wp_safe_redirect( htmlspecialchars_decode( $edit_link ) );
					exit;
				}
			}
		}
	}
}

if ( ! function_exists( 'wrld_register_pattern_category' ) ) {
	/**
	 * Registers the new custom category to categorize the newly added block patterns by the plugin
	 */
	function wrld_register_pattern_category() {
		register_block_pattern_category(
			'wisdm-ld-reports',
			array( 'label' => __( 'ProPanel', 'learndash-reports-pro' ) )
		);
	}
}

if ( ! function_exists( 'wrld_register_apis' ) ) {
	/**
	 * The function registers all the API endpoints written to fetch the data from the server.
	 */
	function wrld_register_apis() {
		if ( defined( 'LEARNDASH_VERSION' ) ) {
			include_once WRLD_REPORTS_PATH . '/includes/apis/class-wrld-learndash-endpoints.php';
			$endpoint_entry = WRLD_LearnDash_Endpoints::get_instance();
		}
	}
}
if ( ! function_exists( 'wrld_load_admin_functions' ) ) {
	/**
	 * The function includes the admin related functions.
	 */
	function wrld_load_admin_functions() {
		if ( defined( 'LEARNDASH_VERSION' ) ) {
			include_once WRLD_REPORTS_PATH . '/includes/admin/class-admin-functions.php';
			include_once WRLD_REPORTS_PATH . '/includes/admin/class-time-spent-onboarding.php';

			include_once WRLD_REPORTS_PATH . '/includes/admin/class-reports-setup-wizard.php';
		}
	}
}

if ( ! function_exists( 'wrld_db_install' ) ) {
	/**
	 * The functions is called on the install to create the databases required.
	 *
	 * TODO: Remove if no longer required. @JD
	 */
	function wrld_db_install() {
		global $wpdb;
		global $wrld_db_version;

		$table_name = $wpdb->prefix . 'ld_time_entries';

		$charset_collate = $wpdb->get_charset_collate();
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) === $table_name ) {
			return;
		}
		$wrld_db_version = LDRP_PLUGIN_VERSION;
		$sql             = 'CREATE TABLE ' . $table_name . " (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				course_id bigint(20) unsigned NOT NULL DEFAULT '0',
				post_id bigint(20) unsigned NOT NULL DEFAULT '0',
				user_id bigint(20) unsigned NOT NULL DEFAULT '0',
				activity_updated int(11) unsigned DEFAULT NULL,
				time_spent bigint(20) unsigned DEFAULT NULL,
				ip_address VARCHAR(100) NULL DEFAULT '',
			  	PRIMARY KEY  (id),
			  	KEY user_id (user_id),
			  	KEY post_id (post_id),
				KEY course_id (course_id),
			  	KEY activity_updated (activity_updated)
				) " . $charset_collate . ';';
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		dbDelta( $sql );

		add_option( 'wrld_db_version', $wrld_db_version, false );
	}
}

if ( ! function_exists( 'wrld_quiz_activity_table' ) ) {
	/**
	 * The functions is called on the install to create the databases required.
	 *
	 * TODO: Remove if no longer required. @JD
	 */
	function wrld_quiz_activity_table() {
		global $wpdb;
		global $wrld_db_version;

		$table_name = $wpdb->prefix . 'ld_quiz_entries';

		$charset_collate = $wpdb->get_charset_collate();
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) === $table_name ) {
			return;
		}
		$wrld_db_version = LDRP_PLUGIN_VERSION;
		$sql             = 'CREATE TABLE ' . $table_name . " (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				course_id bigint(20) unsigned NOT NULL DEFAULT '0',
				post_id bigint(20) unsigned NOT NULL DEFAULT '0',
				user_id bigint(20) unsigned NOT NULL DEFAULT '0',
				activity_updated int(11) unsigned DEFAULT NULL,
				time_spent bigint(20) unsigned DEFAULT NULL,
				ip_address VARCHAR(100) NULL DEFAULT '',
			  	PRIMARY KEY  (id),
			  	KEY user_id (user_id),
			  	KEY post_id (post_id),
				KEY course_id (course_id),
			  	KEY activity_updated (activity_updated)
				) " . $charset_collate . ';';
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		dbDelta( $sql );

		update_option( 'wrld_db_version', $wrld_db_version, false );
	}
}

if ( ! function_exists( 'wrld_course_time_spent_table' ) ) {
	/**
	 * The functions is called on the install to create the databases required.
	 *
	 * TODO: Remove if no longer required. @JD
	 */
	function wrld_course_time_spent_table() {
		global $wpdb;
		global $wrld_db_version;
		$table_name = $wpdb->prefix . 'ld_course_time_spent';

		$charset_collate = $wpdb->get_charset_collate();
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) === $table_name ) {
			return;
		}
		$wrld_db_version = LDRP_PLUGIN_VERSION;
		$sql             = 'CREATE TABLE ' . $table_name . ' (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				course_id bigint(255) NOT NULL,
				post_id VARCHAR(255) NOT NULL,
				group_id VARCHAR(255) NULL,
				category_id VARCHAR(255) NULL,
				user_id VARCHAR(255) NOT NULL,
				completion_time bigint(20) unsigned DEFAULT NULL,
				total_time_spent bigint(20) unsigned DEFAULT NULL,
				enrollment_date VARCHAR(255) NULL,
				completion_date VARCHAR(255) NULL,
				PRIMARY KEY  (id),
				INDEX idx_user_id (user_id),
				INDEX idx_course_id (course_id)
				) ' . $charset_collate . ';';
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		dbDelta( $sql );

		update_option( 'wrld_db_version', $wrld_db_version, false );
	}
}

if ( ! function_exists( 'wrld_cached_data_table' ) ) {
	/**
	 * The functions is called on the install to create the databases required.
	 *
	 * TODO: Remove if no longer required. @JD
	 */
	function wrld_cached_data_table() {
		global $wpdb;
		global $wrld_db_version;

		$table_name      = $wpdb->prefix . 'wrld_cached_entries';
		$charset_collate = $wpdb->get_charset_collate();
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) === $table_name ) {
			return;
		}
		$wrld_db_version = LDRP_PLUGIN_VERSION;
		$sql             = 'CREATE TABLE ' . $table_name . " (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				option_name varchar(191) NOT NULL DEFAULT '',
				option_value longtext NULL DEFAULT '',
				object_id bigint(20) unsigned NOT NULL DEFAULT '0',
				object_type varchar(20) NULL DEFAULT '',
				created_on bigint(10) NOT NULL DEFAULT 0,
				expires_on bigint(10) NOT NULL DEFAULT 0,
			  	PRIMARY KEY  (id),
			  	KEY option_name (option_name),
			  	KEY object_id (object_id),
				KEY object_type (object_type)
				) " . $charset_collate . ';';

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}
		dbDelta( $sql );
		update_option( 'wrld_db_version', $wrld_db_version, false );
	}
}

if ( ! function_exists( 'wrld_enqueue_global_styles' ) ) {
	/**
	 * Enqueues the commonly required stylesheet and scripts by the plugin.
	 */
	function wrld_enqueue_global_styles() {
		if ( ! is_rtl() ) {
			wp_enqueue_style( 'wrld_global_styles', WRLD_REPORTS_SITE_URL . '/assets/css/style.css', [ 'wp-components' ], LDRP_PLUGIN_VERSION );
		} else {
			wp_enqueue_style( 'wrld_global_styles', WRLD_REPORTS_SITE_URL . '/assets/css/style.rtl.css', [ 'wp-components' ], LDRP_PLUGIN_VERSION );
		}

		// Enqueue the common script to ensure localized data is available for all blocks.
		wp_enqueue_script( 'wrld-common-script' );
	}
}

if ( ! function_exists( 'wrld_learndash_dependency_check' ) ) {
	/**
	 * Checks if all the available dependencies are present.
	 *
	 * @deprecated 3.0.0 This check is done in the Dependency_Checker class.
	 *
	 * @return void
	 */
	function wrld_learndash_dependency_check() {
		_deprecated_function( __FUNCTION__, '3.0.0' );

		// check if learndash is active.
		if ( ! defined( 'LEARNDASH_VERSION' ) ) {
			unset( $_GET['activate'] );
			add_action( 'admin_notices', 'wrld_activation_notices' );
		}
	}
}

if ( ! function_exists( 'wrld_activation_notices' ) ) {
	/**
	 * Displays the notice about the LearnDash plugin not being active.
	 *
	 * @deprecated 3.0.0 This check is done in the Dependency_Checker class.
	 *
	 * @return void
	 */
	function wrld_activation_notices() {
		_deprecated_function( __FUNCTION__, '3.0.0' );

		echo "<div class='error'>
			<p>LearnDash LMS plugin is not active. In order to make <strong>ProPanel</strong> plugin work, you need to install and activate LearnDash LMS first.</p>
		</div>";
	}
}

if ( ! function_exists( 'wrld_nonlogged_in_user_block' ) ) {
	/**
	 * This function blocks the non-logged in user from accessing the reports page.
	 *
	 * @param string $content wp-post content.
	 */
	function wrld_nonlogged_in_user_block( $content ) {
		$fallback_template = WRLD_REPORTS_PATH . '/includes/templates/guest-message.php';
		$template          = apply_filters( 'wrld-get-guest-template-path', $fallback_template );

		if ( file_exists( $template ) ) {
			include_once $template;
		} else {
			include_once $fallback_template;
		}

		$content = wrld_get_guest_message_on_reports_page( $content );

		return $content;
	}
}

if ( ! function_exists( 'wrld_load_textdomain' ) ) {
	/**
	 * Load plugin textdomain.
	 *
	 * @deprecated 3.0.0 This is done in the main plugin file.
	 *
	 * @return void
	 */
	function wrld_load_textdomain() {
		_deprecated_function( __FUNCTION__, '3.0.0' );

		load_plugin_textdomain( 'learndash-reports-pro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
}

if ( ! function_exists( 'wrld_welcome_modal' ) ) {
	/**
	 * This function is used to show the welcome message on first visit to the ProPanel dashboard page.
	 *
	 * @param string $content This is content.
	 */
	function wrld_welcome_modal( $content ) {
		global $post;
		$auto_generated_page         = get_option( 'ldrp_reporting_page', 0 );
		$auto_generated_student_page = get_option( 'ldrp_student_page', 0 );
		$visited_dashboard           = get_option( 'wrld_visited_dashboard', false );
		$other_dashboard             = '';

		if ( is_admin() || $post->ID != $auto_generated_page || defined( 'REST_REQUEST' ) || ! current_user_can( 'manage_options' ) ) {
			return $content;
		}

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			if ( false === $visited_dashboard || 'pro' !== $visited_dashboard ) {
				if ( is_rtl() ) {
					wp_enqueue_style( 'wrld_welcome_modal_style', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-welcome-modal.rtl.css', array(), LDRP_PLUGIN_VERSION );
				} else {
					wp_enqueue_style( 'wrld_welcome_modal_style', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-welcome-modal.css', array(), LDRP_PLUGIN_VERSION );
				}
				wp_enqueue_script( 'wrld_welcome_modal_script', WRLD_REPORTS_SITE_URL . '/assets/js/wrld-welcome-modal.js', array( 'jquery' ), LDRP_PLUGIN_VERSION, true );
				$local_script_data = array(
					'wp_ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( 'wrld-welcome-modal' ),
				);
				wp_localize_script( 'wrld_welcome_modal_script', 'wrld_welcome_modal_script_data', $local_script_data );
				if ( ( empty( $auto_generated_student_page ) || 'publish' !== get_post_status( $auto_generated_student_page ) ) && defined( 'LDRP_PLUGIN_VERSION' ) ) {
					$other_dashboard = '<div class="secondary-cta-onboarding"><span>' . esc_html__( 'OR', 'learndash-reports-pro' ) . '</span><div>' . esc_html__( 'Go back and configure the Student Quiz Reports page.', 'learndash-reports-pro' ) . '</div><a href="' . esc_url( add_query_arg( array( 'page' => 'wrld-dashboard-page' ), admin_url() ) ) . '"><button class="modal-button2 modal-button-reports2 secondary">' . esc_html__( 'Configure Student Quiz Reports page', 'learndash-reports-pro' ) . '<i class="fa fa-chevron-right" aria-hidden="true"></i></button></a></div>';
				}
				include_once WRLD_REPORTS_PATH . '/includes/templates/welcome-modal.php';
				if ( ! defined( 'LDRP_PLUGIN_VERSION' ) && 'free' !== $visited_dashboard ) {
					$content = $content . wrld_free_get_popup_modal_content( $other_dashboard );
					update_option( 'wrld_visited_dashboard', 'free' );
				} elseif ( defined( 'LDRP_PLUGIN_VERSION' ) && ( false === $visited_dashboard || 'free' === $visited_dashboard ) ) {
					$content = $content . wrld_pro_get_popup_modal_content( $other_dashboard );
					update_option( 'wrld_visited_dashboard', 'pro' );
				}
			}
			return $content;
		}
	}
}

if ( ! function_exists( 'wrld_student_welcome_modal' ) ) {
	/**
	 * This function is used to show the welcome message on first visit to the ProPanel dashboard page.
	 *
	 * @param string $content This is content.
	 */
	function wrld_student_welcome_modal( $content ) {
		global $post;
		$auto_generated_dashboard_page = get_option( 'ldrp_reporting_page', 0 );
		$auto_generated_page           = get_option( 'ldrp_student_page', 0 );
		$visited_dashboard             = get_option( 'wrld_visited_student_dashboard', false );
		$other_dashboard               = '';
		if ( is_admin() || $post->ID != $auto_generated_page || defined( 'REST_REQUEST' ) || ! current_user_can( 'manage_options' ) ) {
			return $content;
		}

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			if ( false === $visited_dashboard ) {
				if ( is_rtl() ) {
					wp_enqueue_style( 'wrld_welcome_modal_style', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-welcome-modal.rtl.css', array(), LDRP_PLUGIN_VERSION );
				} else {
					wp_enqueue_style( 'wrld_welcome_modal_style', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-welcome-modal.css', array(), LDRP_PLUGIN_VERSION );
				}
				wp_enqueue_script( 'wrld_welcome_modal_script', WRLD_REPORTS_SITE_URL . '/assets/js/wrld-welcome-modal.js', array( 'jquery' ), LDRP_PLUGIN_VERSION, true );
				$local_script_data = array(
					'wp_ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'       => wp_create_nonce( 'wrld-welcome-modal' ),
				);
				wp_localize_script( 'wrld_welcome_modal_script', 'wrld_welcome_modal_script_data', $local_script_data );
				if ( empty( $auto_generated_dashboard_page ) || 'publish' !== get_post_status( $auto_generated_dashboard_page ) ) {
					$other_dashboard = '<div class="secondary-cta-onboarding"><span>' . esc_html__( 'OR', 'learndash-reports-pro' ) . '</span><div>' . esc_html__( 'Go back and configure the ProPanel Dashboard.', 'learndash-reports-pro' ) . '</div><a href="' . esc_url( add_query_arg( array( 'page' => 'wrld-dashboard-page' ), admin_url() ) ) . '"><button class="modal-button2 modal-button-reports2">' . esc_html__( 'Configure ProPanel Dashboard', 'learndash-reports-pro' ) . '<i class="fa fa-chevron-right" aria-hidden="true"></i></button></a></div>';
				}
				include_once WRLD_REPORTS_PATH . '/includes/templates/welcome-modal.php';
				$content = $content . wrld_student_get_popup_modal_content( $other_dashboard );
				update_option( 'wrld_visited_student_dashboard', true );
			}
			return $content;
		}
	}
}

if ( ! function_exists( 'wrld_free_onboarding_modal' ) ) {
	/**
	 * Shows the onboarding modal for the plugin.
	 *
	 * @return void
	 */
	function wrld_free_onboarding_modal() {
		$screen = get_current_screen();

		if ( ! empty( $screen ) && 'plugins' !== $screen->base && 'update' !== $screen->base ) {
			return; // not on admin plugins page.
		}

		$visited_settings_page = get_option( 'wrld_settings_page_visited', false );
		if ( false !== $visited_settings_page ) {
			return; // user knows about setting page.
		}

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_modal_style', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-modal.rtl.css', [], LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_modal_style', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-modal.css', [], LDRP_PLUGIN_VERSION );
			}
			wp_enqueue_script( 'wrld_modal_script', WRLD_REPORTS_SITE_URL . '/assets/js/wrld-modal.js', [ 'jquery' ], LDRP_PLUGIN_VERSION, false );
			wp_localize_script( 'wrld_modal_script', 'wrld_modal_script_object', [ 'wp_ajax_url' => admin_url( 'admin-ajax.php' ) ] );
			$modal_head              = __( 'Welcome to ProPanel!', 'learndash-reports-pro' );
			$modal_description       = __( 'Plugin Activation Successful. You are just a few steps away from launching your ProPanel Dashboard.', 'learndash-reports-pro' );
			$info_url                = 'admin.php?page=wrld-settings&subtab=data-upgrade';
			$modal_action_text       = __( 'Let\'s get started!', 'learndash-reports-pro' );
			$action_close            = '';
			$plugin_first_activation = get_option( 'wrld_free_plugin_first_activated', false );

			if ( $plugin_first_activation && ! is_array( $plugin_first_activation ) ) {
				// plugin updated to v1.2.0.
				$modal_description = __( 'The plugin has been updated successfully.', 'learndash-reports-pro' );
				$modal_action_text = __( 'Go Ahead!', 'learndash-reports-pro' );
			}

			// cspell:disable-next-line .
			$wp_nonce = wp_create_nonce( 'reports-firrst-install-modal' );
			include_once WRLD_REPORTS_PATH . '/includes/templates/admin-modal.php';
		}
	}
}

if ( ! function_exists( 'wrld_free_upgrade_to_pro_modal' ) ) {
	/**
	 * Shows a modal to update the reports pro version.
	 *
	 * This function is no longer used.
	 *
	 * @deprecated 3.0.0
	 *
	 * @return void
	 */
	function wrld_free_upgrade_to_pro_modal() {
		_deprecated_function( __FUNCTION__, '3.0.0' );
	}
}

if ( ! function_exists( 'wrld_add_review_notice' ) ) {
	/**
	 * To display the notice to participate in the survey.
	 *
	 * This function is no longer used.
	 *
	 * @deprecated 3.0.0
	 *
	 * @return void
	 */
	function wrld_add_review_notice() {
		_deprecated_function( __FUNCTION__, '3.0.0' );
	}
}

if ( ! function_exists( 'wrld_add_upgrade_notice' ) ) {
	/**
	 * Adds notice to upgrade to the pro version of the plugin.
	 *
	 * This function is no longer used.
	 *
	 * @deprecated 3.0.0
	 *
	 * @return void
	 */
	function wrld_add_upgrade_notice() {
		_deprecated_function( __FUNCTION__, '3.0.0' );
	}
}

if ( ! function_exists( 'wrld_add_recomendation_notice' ) ) {
	/**
	 * Adds notice to update the reports pro plugin.
	 *
	 * This function is no longer used.
	 *
	 * @deprecated 3.0.0
	 *
	 * @return void
	 */
	function wrld_add_recomendation_notice() {
		_deprecated_function( __FUNCTION__, '3.0.0' );
	}
}

if ( ! function_exists( 'wrld_show_review_notice' ) ) {
	/**
	 * This function returns the html code for the Review Notice
	 *
	 * @since 1.0.1
	 * @deprecated 3.0.0
	 *
	 * @param string $message_head Notification heading.
	 * @param string $message Notification message body.
	 * @param string $button_text Text to be displayed on the action button in the notification.
	 * @param string $link Link for the action button in the notification default to learndash.com.
	 *
	 * @return void
	 */
	function wrld_show_review_notice( $message_head, $message, $button_text, $link = 'https://wordpress.org/support/plugin/wisdm-reports-for-learndash/reviews/#new-post' ) {
		_deprecated_function( __FUNCTION__, '3.0.0' );
	}
}

if ( ! function_exists( 'wrld_get_common_script_localized_data' ) ) {
	/**
	 * This function is used to generate the commonly used localized data for the common script used by all the blocks.
	 */
	function wrld_get_common_script_localized_data() {
		$report_type = 'default-ld-reports';
		if ( isset( $_GET['ld_report_type'] ) && 'quiz-reports' == $_GET['ld_report_type'] ) {
			$report_type = 'quiz-reports';
		}
		if ( ( isset( $_GET['screen'] ) && 'quiz' == $_GET['screen'] ) || isset( $_GET['pageno'] ) ) {
			$report_type = 'quiz-reports';
		}
		$currency = function_exists( 'learndash_get_currency_symbol' ) ? learndash_get_currency_symbol() : '';
		$currency = empty( $currency ) && function_exists( 'learndash_30_get_currency_symbol' ) ? @learndash_30_get_currency_symbol() : $currency;
		$currency = empty( $currency ) ? '$' : $currency;

		$auto_generated_page               = get_option( 'ldrp_reporting_page', false );
		$auto_student_generated_page       = get_option( 'ldrp_student_page', false );
		$visited_dashboard                 = get_option( 'wrld_visited_dashboard', false );
		$visited_student_dashboard         = get_option( 'wrld_visited_student_dashboard', false );
		$page_configuration_status         = false;
		$page_student_configuration_status = false;
		if ( $auto_generated_page && $auto_generated_page > 0 && ( isset( $_GET['post'] ) && $auto_generated_page == $_GET['post'] ) ) {
			$page_configuration_status = 'publish' !== get_post_status( $auto_generated_page );
		}
		if ( $auto_student_generated_page && $auto_student_generated_page > 0 && ( isset( $_GET['post'] ) && $auto_student_generated_page == $_GET['post'] ) ) {
			$page_student_configuration_status = 'publish' !== get_post_status( $auto_student_generated_page );
		}
		// Build report_preferences from user meta.
		$filter_options = maybe_unserialize( get_user_meta( get_current_user_id(), 'qre_custom_reports_saved_query', true ) );
		$filter_defaults = array(
			'course_filter' => -1,
			'group_filter'  => -1,
			'quiz_filter'   => -1,
		);
		if ( empty( $filter_options ) ) {
			$filter_options = $filter_defaults;
		} else {
			foreach ( $filter_defaults as $key => $default ) {
				if ( ! array_key_exists( $key, $filter_options ) ) {
					$filter_options[ $key ] = $default;
				}
			}
		}

		$data = array(
			'is_rtl'                            => is_rtl(),
			'report_preferences'                => array(
				'settings'              => $filter_options,
				'selected_course_title' => -1 !== intval( $filter_options['course_filter'] ) ? get_the_title( $filter_options['course_filter'] ) : __( 'All', 'learndash-reports-pro' ),
				'selected_group_title'  => -1 !== intval( $filter_options['group_filter'] ) ? get_the_title( $filter_options['group_filter'] ) : __( 'All', 'learndash-reports-pro' ),
				'selected_quiz_title'   => -1 !== intval( $filter_options['quiz_filter'] ) ? get_the_title( $filter_options['quiz_filter'] ) : __( 'All', 'learndash-reports-pro' ),
			),
			'plugin_asset_url'                  => WRLD_REPORTS_SITE_URL . '/assets',
			'is_pro_version_active'             => apply_filters( 'wisdm_ld_reports_pro_version', false ),
			'upgrade_link'                      => 'https://go.learndash.com/ppaddon',
			'is_admin_user'                     => current_user_can( 'manage_options' ),
			'currency_in_use'                   => apply_filters( 'wrld_currency_in_use', $currency ),
			'report_type'                       => $report_type,
			'ajaxurl'                           => admin_url( 'admin-ajax.php' ),
			'report_nonce'                      => wp_create_nonce( 'wisdm_ld_reports_page' ),
			'start_date'            => apply_filters( 'wrld_filter_start_date', date( 'j M Y H:i:s', strtotime( gmdate( 'j M Y' ) . '-30 days' ) ) ),// phpcs:ignore.
			'end_date'              => apply_filters( 'wrld_filter_end_date', date( 'j M Y H:i:s', current_time( 'timestamp' ) ) ),// phpcs:ignore.
			'ld_custom_labels'                  => wrld_get_custom_ld_labels(),
			'ld_custom_labels_lower'            => wrld_get_custom_ld_labels( 'lower' ),
			'is_demo'                           => apply_filters( 'wrld_is_demo_enabled', false ),
			'dashboard_page_id'                 => $auto_generated_page,
			'student_page_id'                   => $auto_student_generated_page,
			'page_configuration_status'         => $page_configuration_status,
			'page_student_configuration_status' => $page_student_configuration_status,
			'visited_dashboard'                 => $visited_dashboard,
			'visited_student_dashboard'         => $visited_student_dashboard,
			'notice_content'                    => array(
												'header' => __( 'You are one step away from launching your ProPanel Dashboard.', 'learndash-reports-pro' ),
												'li_1'   => __( 'Each Reporting component seen below is a Gutenberg block. They can be found by clicking on the "+" icon (block inserter)', 'learndash-reports-pro' ),
												'li_2'   => __( 'The dashboard below is pre-configured. You can also hide/show/reorder the blocks and reuse the same pattern below.', 'learndash-reports-pro' ),
												'li_3'   => __( 'Once launched, only the admin can access this page. To provide access to others, navigate to the WordPress dashboard > ProPanel > Settings tab.', 'learndash-reports-pro' ),
											),
			'notice_student_content'            => array(
												'header' => __( 'Your Student Quiz Reports page is configured and ready to publish. Click on the ”Publish” button to make it live!', 'learndash-reports-pro' ),
												'li_1'   => __( 'The dashboard below is pre-configured. You can also hide/show/reorder the blocks and reuse the same pattern below.', 'learndash-reports-pro' ),
												'li_2'   => __( 'Each Reporting component seen below is a Gutenberg block. They can be found by clicking on the "+" icon (block inserter)', 'learndash-reports-pro' ),
											),
			'user_roles'                        => wrld_get_roles_of_current_user(),
			'wpml_lang'                         => apply_filters( 'wpml_current_language', false ),
			'ld_api_settings'                   => get_option( 'learndash_settings_rest_api', array() ),
			'exclude_courses'                   => get_option( 'exclude_courses', array() ),
			'course_groups'                     => array(),
			'courses'                           => array(),
			'course_categories'                 => array(),
			'quizes'                            => array(), // cspell:disable-line.
			'is_idle_tracking_enabled'          => get_option( 'wrld_time_tracking_status', false ),
			'idle_tracking_active_from'         => get_option( 'wrld_time_tracking_last_update', false ) == '' ? 'Not Configured' : date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), get_option( 'wrld_time_tracking_last_update', false ) ),
			'time_tacking_setting_url'          => admin_url( 'admin.php?page=wrld-settings#wlrd-dashboard-time-settings' ), // cspell:disable-line.
			'current_user'                      => wp_get_current_user(),
			'avatar_url'                        => get_avatar_url( wp_get_current_user()->ID ),
			'courses_enrolled'                  => function_exists( 'learndash_user_get_enrolled_courses' ) ? learndash_user_get_enrolled_courses( get_current_user_id() ) : array(),
			'qre_filters'                       => array(),
			'qre_request_params'                => array(),
		);
		return $data;
	}
}

function wrld_get_roles_of_current_user() {
	$user_roles = array();
	$user       = wp_get_current_user();
	if ( ! empty( $user ) ) {
		$user_roles = (array) $user->roles;
	}
	return $user_roles;
}

if ( ! function_exists( 'wrld_get_custom_ld_labels' ) ) {
	/**
	 * This function when called checks if the custom labels are defined in the learndash settings and returns the
	 * array of custom labels with label keys.
	 *
	 * @param string $case_version This is the case version, can be 'normal' or 'lower'.
	 *
	 * @return array $result array of custom labels with label keys.
	 */
	function wrld_get_custom_ld_labels( string $case_version = 'normal' ) {
		$labels = array( 'course', 'courses', 'quiz', 'quizzes', 'lesson', 'lessons', 'topic', 'topics', 'question', 'questions', 'group', 'groups' );
		$result = array();
		foreach ( $labels as $label ) {
			if ( $case_version === 'lower' ) {
				$result[ $label ] = \LearnDash_Custom_Label::label_to_lower( $label );
			} else {
				$result[ $label ] = \LearnDash_Custom_Label::get_label( $label );
			}
		}
		return $result;
	}
}

if ( ! function_exists( 'wrld_register_admin_ajax_callbacks' ) ) {
	/** Ajax callbacks */
	function wrld_register_admin_ajax_callbacks() {
		$auto_generated_page         = get_option( 'ldrp_reporting_page', false );
		$visited_auto_generated_page = (bool) get_option( 'wrld_reporting_page_visited', true );
		include_once WRLD_REPORTS_PATH . '/includes/admin/class-admin-functions.php';
		add_action( 'wp_ajax_wrld_page_visit', '\WisdmReportsLearndash\Admin_Functions::update_reporting_page_visit' );
		add_action( 'wp_ajax_wrld_gutenberg_block_visit', '\WisdmReportsLearndash\Admin_Functions::wp_ajax_wrld_gutenberg_block_visit' );
		// add_action( 'wp_ajax_wrld_notice_action', '\WisdmReportsLearndash\Admin_Functions::wrld_notice_action' );.

		$wrld_pages = array( 'wrld-dashboard-page', 'wrld-license-activation', 'wrld-other-plugins', 'wrld-help', 'wrld-settings' );
		if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ( ! isset( $_GET['page'] ) || ! in_array( sanitize_text_field( $_GET['page'] ), $wrld_pages ) ) ) {
			$settings_data = get_option( 'wrld_settings', array() );
			unset( $settings_data['skip-license-activation'] );
			update_option( 'wrld_settings', $settings_data );
			// var_dump($settings_data);exit();.
		}
	}
}

if ( ! function_exists( 'wrld_add_page_to_primary_menu' ) ) {
	/**
	 * This function adds the link to the autogenerated reports page to the primary menu
	 * only when the page is published & configuration set to show the menu automatically
	 * in the primary menu.
	 *
	 * @param string $items Items.
	 * @param string $args Args.
	 */
	function wrld_add_page_to_primary_menu( $items, $args ) {
		$settings_data     = get_option( 'wrld_settings', false );
		$wrld_page         = get_option( 'ldrp_reporting_page', false );
		$wrld_student_page = get_option( 'ldrp_student_page', false );
		if ( $settings_data && isset( $settings_data['wrld-menu-config-setting'] ) && $settings_data['wrld-menu-config-setting'] && $wrld_page && $wrld_page > 0 ) {
			if ( 'publish' === get_post_status( $wrld_page ) ) {// phpcs:ignore
				if ( 'primary' == $args->theme_location ) {// phpcs:ignore
					$items .= '<li id="menu-item-' . $wrld_page . '" class="menu-item menu-item-type-custom" ><a class="menu-link" href="' . get_post_permalink( $wrld_page ) . '">' . __( 'ProPanel Dashboard', 'learndash-reports-pro' ) . '</a></li>';
				}
			}
		}
		if ( ! is_user_logged_in() ) {
			return $items;
		}
		if ( $settings_data && isset( $settings_data['wrld-menu-student-setting'] ) && $settings_data['wrld-menu-student-setting'] && $wrld_student_page && $wrld_student_page > 0 ) {
			if ( 'publish' === get_post_status( $wrld_student_page ) ) {// phpcs:ignore
				if ( 'primary' == $args->theme_location ) {// phpcs:ignore
					$items .= '<li id="menu-item-' . $wrld_student_page . '" class="menu-item menu-item-type-custom" ><a class="menu-link" href="' . get_post_permalink( $wrld_student_page ) . '">' . apply_filters( 'wrld_student_dashboard_menu_title', __( 'My Quiz Results', 'learndash-reports-pro' ) ) . '</a></li>';
				}
			}
		}
		return $items;
	}
}

if ( ! function_exists( 'wrld_dashboad_link' ) ) {
	/**
	 * Report dashboard link.
	 */
	function wrld_dashboad_link() {
		ob_start();
		$wrld_page = get_option( 'ldrp_reporting_page', false );
		if ( false != $wrld_page && $wrld_page > 0 ) {// phpcs:ignore
			$link = get_post_permalink( $wrld_page );
			?>
			<a class='wrld-dashboard-link' href=<?php echo esc_attr( $link ); ?>>
				<button class='button wrld-dashboard-link-btn'><?php esc_html_e( 'ProPanel Dashboard', 'learndash-reports-pro' ); ?></button>
			</a>
			<?php
		}
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

// shortcode for time spent on a course.

if ( ! function_exists( 'total_time_spent_on_a_course_shortcode' ) ) {
	/** Shortcode for total time spent
	 *
	 * @param string $atts shortcode attribute.
	 */
	function total_time_spent_on_a_course_shortcode( $atts ) {
		ob_start();
		$arguments = shortcode_atts(
			array(
				'course_id' => 0,
			),
			$atts
		);
		$course_id = $arguments['course_id'];
		if ( empty( $course_id ) ) {
			global $post;
			if ( in_array( $post->post_type, array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ) ) ) {
				$course_id = learndash_get_course_id( $post->ID );
			}
		}

		$user_id = get_current_user_id();
		global $wpdb;

		$user_time_spent = 0;

		$output = $wpdb->get_results( $wpdb->prepare( 'SELECT time_spent , activity_updated FROM ' . $wpdb->prefix . 'ld_time_entries WHERE course_id = %d AND user_id = %d', $course_id, $user_id ), ARRAY_A ); // phpcs:ignore

		$user_time_spent = array_sum( array_column( $output, 'time_spent' ) );
		$last_activity   = count( array_column( $output, 'activity_updated' ) ) > 0 ? max( array_column( $output, 'activity_updated' ) ) : 0;
		$date            = 0 === $last_activity ? '-' : wp_date( 'h:i a', $last_activity ) . ', ' . date_i18n( 'd', $last_activity ) . 'th ' . date_i18n( 'M Y', $last_activity );
		?>
	<div class="wrld-total-time-spent">
		<div class="wrld-ts-figure">
			<span class="wrld-ts-label"><?php esc_html_e( 'Total time spent:', 'learndash-reports-pro' ); ?></span>
			<span class="wrld-ts-val"><?php echo esc_html( gmdate( 'H:i:s', $user_time_spent ) ); ?> </span>
		</div>
		<div class="wrld-last-updated">
			<span class="wrld-ts-val"><?php echo esc_html( __( 'Last updated:', 'learndash-reports-pro' ) . ' ' . $date ); ?> </span>
		</div>
	</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

if ( ! function_exists( 'wisdm_reports_free_multi_installation_check' ) ) {
	/**
	 * This function will check if the multiple instances of the plugin LearnDash LMS - Reports are active on the site.
	 * In case of multiple instances it will deactivate the plugin which was installed from wisdmlabs.
	 *
	 * @deprecated 1.8.2 This function is no longer used because we don't host the plugin in multiple locations anymore.
	 */
	function wisdm_reports_free_multi_installation_check() {
		_deprecated_function( __FUNCTION__, '1.8.2' );
	}
}


/***Old time data migration script*/
if ( ! function_exists( 'old_time_data_migration_script' ) ) {
	/**
	 * Data migration script.
	 */
	function old_time_data_migration_script() {
			global $wpdb;
			$can_migrate = get_option( 'is_old_time_spent_migrated', false );
		if ( ! $can_migrate ) {
			/**  Update_option( 'time_tracking_data_migration_start', current_time( 'timestamp' ) );. */
			$table_name     = $wpdb->prefix . 'learndash_user_activity';
			$new_table_name = $wpdb->prefix . 'ld_time_entries';
			$old_data        = $wpdb->get_results( "SELECT * FROM $table_name where activity_type = 'course' or activity_type = 'lesson' or activity_type = 'topic' or activity_type = 'quiz'" );// phpcs:ignore

			if ( ! empty( $old_data ) ) {
				foreach ( $old_data as $attempt ) {
					$user_id          = $attempt->user_id;
					$post_id          = $attempt->post_id;
					$course_id        = $attempt->course_id;
					$activity_updated = current_time( 'timestamp' ); // phpcs:ignore.
					$total_time_spent = 0;
					if ( ! empty( $attempt->activity_completed ) ) {
						// If the Course is complete then we take the time as the completed - started times.
						if ( empty( $attempt->activity_completed ) || empty( $attempt->activity_started ) ) {
							continue;
						}
						if ( $attempt->activity_completed - $attempt->activity_started < 0 ) {
							continue;
						}

						$total_time_spent = ( $attempt->activity_completed - $attempt->activity_started );
						// saving meta for average time.
						if ( 'course' === $attempt->activity_type ) {
							update_user_meta( $user_id, 'course_time_' . $course_id, $total_time_spent );
						} elseif ( 'lesson' === $attempt->activity_type ) {
							update_user_meta( $user_id, 'lesson_time_' . $post_id, $total_time_spent );
						} elseif ( 'topic' === $attempt->activity_type ) {
							update_user_meta( $user_id, 'topic_time_' . $post_id, $total_time_spent );
						} elseif ( 'quiz' === $attempt->activity_type ) {
							update_user_meta( $user_id, 'quiz_time_' . $post_id, $total_time_spent );
						}
						$activity_updated = $attempt->activity_completed;
					} else {
						if ( empty( $attempt->activity_updated ) || empty( $attempt->activity_started ) ) {
							continue;
						}
						if ( $attempt->activity_updated - $attempt->activity_started < 0 ) {
							continue;
						}
						// But if the Course is not complete we calculate the time based on the updated timestamp.
						// This is updated on the course for each lesson, topic, quiz.
						$total_time_spent = ( $attempt->activity_updated - $attempt->activity_started );
						$activity_updated = $attempt->activity_updated;
					}

					if ( $post_id !== $course_id ) {
						continue;
					}

					// adding data into new database.
						$insert_id = $wpdb->insert(
							$new_table_name,
							array(
								'course_id'        => $course_id,
								'post_id'          => $post_id,
								'user_id'          => $user_id,
								'activity_updated' => $activity_updated,
								'time_spent'       => $total_time_spent,
								'ip_address'       => '',
							),
							array(
								'%d',
								'%d',
								'%d',
								'%d',
								'%d',
								'%s',
							)
						);

					$migrated = get_option( 'time_tracking_data_migration_ids', false );
					if ( empty( $migrated ) ) {
						$migrated = array();
					}
					$migrated[] = $insert_id;
					update_option( 'time_tracking_data_migration_ids', $migrated );
				}
				update_option( 'is_old_time_spent_migrated', true );
			}
		}
	}
}

if ( ! function_exists( 'course_group_data_migration_script' ) ) {
	function course_group_data_migration_script() {
		if ( class_exists( 'WRLD_Quiz_Export_Db' ) ) {
			$instance = \WRLD_Quiz_Export_Db::instance();
		}
	}
}

if ( ! function_exists( 'wrld_rest_prepare_filter' ) ) {
	function wrld_rest_prepare_filter( $response, $post, $request ) {
		if ( array_key_exists( 'content', $response->data ) && isset( $_GET['preload_progress'] ) ) {
			$response->data['content']['raw']      .= '
				<!-- wp:columns {"className":"wrld-mw-1400"} -->
					<div class="wp-block-columns wrld-mw-1400">
						<!-- wp:column {"className":"wisdm-reports"} -->
							<div class="wp-block-column wisdm-reports">
								<!-- wp:wisdm-learndash-reports/course-progress-rate -->
								<div class="wp-block-wisdm-learndash-reports-course-progress-rate"><div class="wisdm-learndash-reports-course-progress-rate front"></div></div>
								<!-- /wp:wisdm-learndash-reports/course-progress-rate -->
							</div>
						<!-- /wp:column -->
					</div>
				<!-- /wp:columns -->';
			$response->data['content']['rendered'] .= '
				<!-- wp:columns {"className":"wrld-mw-1400"} -->
					<div class="wp-block-columns wrld-mw-1400">
						<!-- wp:column {"className":"wisdm-reports"} -->
							<div class="wp-block-column wisdm-reports">
								<!-- wp:wisdm-learndash-reports/course-progress-rate -->
								<div class="wp-block-wisdm-learndash-reports-course-progress-rate"><div class="wisdm-learndash-reports-course-progress-rate front"></div></div>
								<!-- /wp:wisdm-learndash-reports/course-progress-rate -->
							</div>
						<!-- /wp:column -->
					</div>
				<!-- /wp:columns -->';
		}
		if ( array_key_exists( 'content', $response->data ) && isset( $_GET['preload_activity'] ) ) {
			$response->data['content']['raw']      .= '
				<!-- wp:columns {"className":"wrld-mw-1400"} -->
					<div class="wp-block-columns wrld-mw-1400">
						<!-- wp:column {"className":"wisdm-reports"} -->
							<div class="wp-block-column wisdm-reports">
								<!-- wp:wisdm-learndash-reports/inactive-users -->
								<div class="wp-block-wisdm-learndash-reports-inactive-users"><div class="wisdm-learndash-reports-inactive-users front"></div></div>
								<!-- /wp:wisdm-learndash-reports/inactive-users -->

								<!-- wp:wisdm-learndash-reports/learner-activity-log -->
								<div class="wp-block-wisdm-learndash-reports-learner-activity-log"><div class="wisdm-learndash-reports-learner-activity-log front"></div></div>
								<!-- /wp:wisdm-learndash-reports/learner-activity-log -->
							</div>
						<!-- /wp:column -->
					</div>
				<!-- /wp:columns -->';
			$response->data['content']['rendered'] .= '
				<!-- wp:columns {"className":"wrld-mw-1400"} -->
					<div class="wp-block-columns wrld-mw-1400">
						<!-- wp:column {"className":"wisdm-reports"} -->
							<div class="wp-block-column wisdm-reports">
								<!-- wp:wisdm-learndash-reports/inactive-users -->
								<div class="wp-block-wisdm-learndash-reports-inactive-users"><div class="wisdm-learndash-reports-inactive-users front"></div></div>
								<!-- /wp:wisdm-learndash-reports/inactive-users -->

								<!-- wp:wisdm-learndash-reports/learner-activity-log -->
								<div class="wp-block-wisdm-learndash-reports-learner-activity-log"><div class="wisdm-learndash-reports-learner-activity-log front"></div></div>
								<!-- /wp:wisdm-learndash-reports/learner-activity-log -->
							</div>
						<!-- /wp:column -->
					</div>
				<!-- /wp:columns -->';
		}
		return $response;
	}
}

if ( ! function_exists( 'wrld_redirect_to_data_upgrade' ) ) {
	function wrld_redirect_to_data_upgrade() {
		if ( ! get_option( 'wrld_visited_dashboard', false ) ) {
			return;
		}
		if ( ! get_option( 'wrld_existing_data_upgrade_redirect' ) ) {
			update_option( 'wrld_existing_data_upgrade_redirect', time(), false );
			$config_link = admin_url( 'admin.php?page=wrld-settings&subtab=data-upgrade' );
			wp_redirect( $config_link );
			exit();
		}
	}
}

if ( ! function_exists( 'learndash_propanel_get_the_title' ) ) {
	/**
	 * Get the post title in decoded format.
	 *
	 * @since 3.0.0
	 *
	 * @param int|WP_Post $post Post ID or object.
	 *
	 * @return string
	 */
	function learndash_propanel_get_the_title( $post ): string {
		$post_title = get_the_title( $post );

		return html_entity_decode( $post_title, ENT_QUOTES, 'UTF-8' );
	}
}

// Dynamically add New blocks on manual page edit click.
add_filter( 'rest_prepare_page', 'wrld_rest_prepare_filter', 10, 3 );
add_action( 'init', 'wrld_add_admin_menus', 96 );
add_action( 'init', 'wrld_register_blocks', 97 );
add_action( 'init', 'wrld_register_pattern_category', 98 );
add_action( 'init', 'wrld_register_patterns', 99 );
add_action( 'init', 'wrld_create_patterns_page', 100 );
add_action( 'init', 'wrld_create_student_patterns_page', 100 );
/** Doc add_action( 'admin_notices', 'wrld_notify_first_report_page_creation', 99 );.*/
add_action( 'admin_notices', 'wrld_free_onboarding_modal', 99 );
add_action( 'admin_init', 'wrld_register_admin_ajax_callbacks' );
add_action( 'plugins_loaded', 'wrld_register_apis', 60 ); // Run after the plugin is loaded.
add_action( 'plugins_loaded', 'wrld_load_admin_functions', 60 ); // Run after the plugin is loaded.
add_action( 'wp_enqueue_scripts', 'wrld_enqueue_global_styles' );
add_action( 'admin_enqueue_scripts', 'wrld_enqueue_global_styles' );
add_filter( 'the_content', 'wrld_nonlogged_in_user_block', 10, 1 );
add_filter( 'the_content', 'wrld_welcome_modal', 11, 1 );
add_filter( 'the_content', 'wrld_student_welcome_modal', 11, 1 );
add_filter( 'wp_loaded', 'wrld_db_install' );
add_filter( 'wp_loaded', 'wrld_course_time_spent_table' );
add_filter( 'wp_loaded', 'wrld_quiz_activity_table' );
add_filter( 'wp_loaded', 'wrld_cached_data_table' );
add_filter( 'wp_nav_menu_items', 'wrld_add_page_to_primary_menu', 10, 2 );
// for old data migration.
// add_action( 'wp_loaded', 'old_time_data_migration_script' );
add_action( 'plugins_loaded', 'course_group_data_migration_script', 60 ); // Run after the plugin is loaded.
add_action( 'init', 'wrld_redirect_to_data_upgrade' );
add_shortcode( 'wrld_dashboard_link', 'wrld_dashboad_link' );
add_shortcode( 'wrld_course_time', 'total_time_spent_on_a_course_shortcode' );

