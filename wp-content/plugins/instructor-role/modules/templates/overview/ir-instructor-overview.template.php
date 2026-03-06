<?php
/**
 * Template : Instructor Overview Template
 *
 * @param array $course_list        List of instructor course ids
 * @param string $ajax_loader       Ajax Loader path
 * @param object $instance          Instance of Instructor_Role_Overview class.
 * @since 3.1.0
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="irb-container">
	<div class="irb-overview-wrap">
		<div class="irb-overview">
			<h1><?php esc_html_e( 'Instructor Overview', 'wdm_instructor_role' ); ?></h1>

			<?php
			/**
			 * Fires before the statistics tiles are rendered.
			 *
			 * @since 5.0.0
			 */
			do_action( 'ir_action_before_stats_tiles' );
			?>

			<div class="irb-tiles-wrap">
				<?php if ( isset( $ir_overview_settings['course_block'] ) && 'on' == $ir_overview_settings['course_block'] ? $ir_overview_settings['course_block'] : '' == 'on' ) : ?>
					<div class="irb-tile">
						<div class="irb-tile-header">
							<span class="irb-header-icon">
								<i class="irb-icon-courses"></i>
							</span>
							<span class="irb-header-text"><?php echo esc_attr( $instance->courses_label ); ?></span>
						</div>
						<div class="irb-tile-content">
							<span class="irb-tile-value"><?php echo esc_attr( $instance->course_count ); ?></span>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( isset( $ir_overview_settings['student_block'] ) && 'on' == $ir_overview_settings['student_block'] ? $ir_overview_settings['student_block'] : '' == 'on' ) : ?>
					<div class="irb-tile">
						<div class="irb-tile-header">
							<span class="irb-header-icon">
								<i class="irb-icon-student"></i>
							</span>
							<span class="irb-header-text"><?php esc_html_e( 'Students', 'wdm_instructor_role' ); ?></span>
						</div>
						<div class="irb-tile-content">
							<span class="irb-tile-value"><?php echo esc_attr( $instance->student_count ); ?></span>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $instance->addon_info ) && array_key_exists( 'products', $instance->addon_info ) && isset( $ir_overview_settings['product_block'] ) && 'on' == $ir_overview_settings['product_block'] ? $ir_overview_settings['product_block'] : '' == 'on' ) : ?>
					<div class="irb-tile">
						<div class="irb-tile-header">
							<span class="irb-header-icon">
								<i class="irb-icon-product"></i>
							</span>
							<span class="irb-header-text"><?php esc_html_e( 'Products', 'wdm_instructor_role' ); ?></span>
						</div>
						<div class="irb-tile-content">
							<span class="irb-tile-value"><?php echo esc_attr( $instance->addon_info['products'] ); ?></span>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php
			/**
			 * Fires before the statistics tiles are rendered.
			 *
			 * @since 5.0.0
			 */
			do_action( 'ir_action_after_stats_tiles' );
			?>

			<?php
			/**
			 * Fires before the reporting tiles are rendered.
			 *
			 * @since 5.0.0
			 */
			do_action( 'ir_action_before_reporting_tiles' );
			?>

			<div class="irb-tiles-wrap irb-charts">
				<?php if ( 1 === intval( ir_get_settings( 'instructor_commission' ) ) && isset( $ir_overview_settings['earnings_block'] ) && 'on' == $ir_overview_settings['earnings_block'] ? $ir_overview_settings['earnings_block'] : '' == 'on' ) : ?>
					<div class="irb-tile irb-medium">
						<div class="irb-tile-header">
							<span class="irb-header-text irb-bold"><?php esc_html_e( 'Earnings', 'wdm_instructor_role' ); ?></span>
						</div>
						<div class="irb-tile-content">
							<div id="ir-earnings-pie-chart-div"></div>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( isset( $ir_overview_settings['reports_block'] ) && 'on' == $ir_overview_settings['reports_block'] ? $ir_overview_settings['reports_block'] : '' == 'on' ) : ?>
					<div class="irb-tile irb-medium">
						<div class="irb-tile-header">
							<span class="irb-header-text irb-bold"><?php esc_html_e( 'Course Reports', 'wdm_instructor_role' ); ?></span>
						</div>
						<div class="irb-tile-content">
							<div class="ir-ajax-overlay" style="display: none;">
								<img src="<?php echo esc_attr( $ajax_icon ); ?>" alt="Loading...">
							</div>
							<?php if ( ! empty( $course_list ) ) : ?>
								<div class="ir-instructor-course-select-wrap">
									<select name="sel-instructor-courses" id="instructor-courses-select">
										<?php foreach ( $course_list as $key => $course_id ) : ?>
											<option value="<?php echo esc_attr( $course_id ); ?>" <?php echo ! ( $key ) ? 'selected' : ''; ?>>
												<?php echo esc_html( get_the_title( $course_id ) ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
								<div id="ir-course-pie-chart-div"></div>
							<?php else : ?>
								<?php
								printf(
									/* translators: Courses label */
									esc_html__( 'There are no %s to show reports', 'wdm_instructor_role' ),
									\LearnDash_Custom_Label::get_label( 'courses' )
								);
								?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- <?php if ( 1 === intval( ir_get_settings( 'instructor_commission' ) ) ) : ?>
					<div class="ir-earnings ir-chart ir-theme-color">
						<div class="ir-chart-title"><?php esc_html_e( 'Earnings', 'wdm_instructor_role' ); ?></div>
						<div id="ir-earnings-pie-chart-div"></div>
					</div>
				<?php endif; ?>
				<div class="ir-course-reports ir-chart">
					<div class="ir-chart-title"><?php esc_html_e( 'Course Reports', 'wdm_instructor_role' ); ?></div>
					<div class="ir-ajax-overlay" style="display: none;">
						<img src="<?php echo esc_attr( $ajax_icon ); ?>" alt="Loading...">
					</div>
					<div class="ir-instructor-course-select-wrap">
						<select name="sel-instructor-courses" id="instructor-courses-select">
						<?php if ( ! empty( $course_list ) ) : ?>
							<?php foreach ( $course_list as $key => $course_id ) : ?>
								<option value="<?php echo esc_attr( $course_id ); ?>" <?php echo ! ( $key ) ? 'selected' : ''; ?>>
									<?php echo esc_html( get_the_title( $course_id ) ); ?>
								</option>
							<?php endforeach; ?>
						<?php else : ?>
							<?php
							printf(
								/* translators: Course label */
								esc_html__( 'No %s created', 'wdm_instructor_role' ),
								\LearnDash_Custom_Label::get_label( 'course' )
							);
							?>
						<?php endif; ?>
						</select>
					</div>
					<div id="ir-course-pie-chart-div"></div>
				</div> -->
			</div>

			<?php
			/**
			 * Fires after the reporting tiles are rendered.
			 *
			 * @since 5.0.0
			 */
			do_action( 'ir_action_after_reporting_tiles' );
			?>

			<?php
			/**
			 * Fires before the submission table.
			 *
			 * @since 5.0.0
			 */
			do_action( 'ir_action_before_submission_table' );
			?>

			<?php if ( isset( $ir_overview_settings['submission_block'] ) && 'on' == $ir_overview_settings['submission_block'] ? $ir_overview_settings['submission_block'] : '' == 'on' ) : ?>
				<div class="irb-tiles-wrap irb-sub">
					<div class="irb-tile irb-large">
						<div class="irb-tile-header">
							<span class="irb-header-text irb-bold"><?php esc_html_e( 'Submissions', 'wdm_instructor_role' ); ?></span>
						</div>
						<div class="irb-tile-content">
							<?php $instance->generateSubmissionReports(); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if ( ! in_array( 'on', array_values( $ir_overview_settings ) ) ) : ?>
			<div class="irbn-tiles-wrap irbn-sub">
				<div class="irbn-tile irbn-large">
					<div class="irbn-tile-header">
						<span class="irbn-header-text irbn-bold"></span>
					</div>
					<div class="irbn-tile-content">
						<?php if ( ! empty( $ir_overview_settings['no_blocks_prompt_message'] ) ) : ?>
							<p style="text-align:center;"><?php echo $ir_overview_settings['no_blocks_prompt_message']; ?></p>
						<?php endif; ?>
						<?php if ( empty( $ir_overview_settings['no_blocks_prompt_message'] ) ) : ?>
							<p style="text-align:center;"><?php esc_html_e( 'No content to display.', 'wdm_instructor_role' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php
			/**
			 * Fires before the submission table.
			 *
			 * @since 5.0.0
			 */
			do_action( 'ir_action_after_submission_table' );
			?>
		</div>
	</div>
</div>
