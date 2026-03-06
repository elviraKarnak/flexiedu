<?php
/**
 * Template : Instructor Overview Template Layout 2
 *
 * @since 4.3.0
 * @version 5.9.4
 *
 * @var array<int> $course_list            List of instructor course ids.
 * @var string $ajax_loader                Ajax Loader path.
 * @var Instructor_Role_Overview $instance Instance of Instructor_Role_Overview class.
 * @var bool $ir_commission_disabled       Whether commission is disabled or not.
 *
 * @package LearnDash\Instructor_Role
 */

use InstructorRole\Modules\Classes\Instructor_Role_Overview;
use LearnDash\Core\Utilities\Cast;

defined( 'ABSPATH' ) || exit;
?>
<div class="irbn-container">
	<div class="irbn-overview-wrap">
		<div class="irbn-overview">
			<h1><?php esc_html_e( 'Instructor Overview', 'wdm_instructor_role' ); ?></h1>
			<div class="irbn-tiles-wrap">
				<?php if ( isset( $ir_overview_settings['course_block'] ) && 'on' == $ir_overview_settings['course_block'] ? $ir_overview_settings['course_block'] : '' == 'on' ) : ?>
					<div class="irbn-tile">
						<div class="irbn-tile-left">
							<span class="irbn-icon">
								<i class="ird-icon-Courses"></i>
							</span>
						</div>
						<div class="irbn-tile-right">
							<span class="irbn-tile-value"><?php echo esc_attr( Cast::to_string( $instance->course_count ) ); ?></span>
							<span class="irbn-text"><?php echo esc_attr( $instance->courses_label ); ?></span>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( isset( $ir_overview_settings['student_block'] ) && 'on' == $ir_overview_settings['student_block'] ? $ir_overview_settings['student_block'] : '' == 'on' ) : ?>

					<div class="irbn-tile">
						<div class="irbn-tile-left">
							<span class="irbn-icon">
								<i class="ird-icon-Student"></i>
							</span>
						</div>
						<div class="irbn-tile-right">
							<span class="irbn-tile-value"><?php echo esc_attr( Cast::to_string( $instance->student_count ) ); ?></span>
							<span class="irbn-text"><?php esc_html_e( 'Students', 'wdm_instructor_role' ); ?></span>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $instance->addon_info ) && array_key_exists( 'products', $instance->addon_info ) && isset( $ir_overview_settings['product_block'] ) && 'on' == $ir_overview_settings['product_block'] ? $ir_overview_settings['product_block'] : '' == 'on' ) : ?>
					<div class="irbn-tile">
						<div class="irbn-tile-left">
							<span class="irbn-icon">
								<i class="ird-icon-Products"></i>
							</span>
						</div>
						<div class="irbn-tile-right">
							<span class="irbn-tile-value"><?php echo esc_attr( $instance->addon_info['products'] ); ?></span>
							<span class="irbn-text"><?php esc_html_e( 'Products', 'wdm_instructor_role' ); ?></span>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<div class="irbn-tiles-wrap irbn-charts">
			<?php if ( 1 === intval( ir_get_settings( 'instructor_commission' ) ) && ! $ir_commission_disabled && isset( $ir_overview_settings['earnings_block'] ) && 'on' == $ir_overview_settings['earnings_block'] ? $ir_overview_settings['earnings_block'] : '' == 'on' ) : ?>
					<div class="irbn-tile irbn-medium">
						<div class="irbn-tile-header">
							<span class="irbn-header-text irbn-bold"><?php esc_html_e( 'Earnings', 'wdm_instructor_role' ); ?></span>
						</div>
						<div class="irbn-tile-content">
							<div id="ir-earnings-pie-chart-div"></div>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( isset( $ir_overview_settings['reports_block'] ) && 'on' == $ir_overview_settings['reports_block'] ? $ir_overview_settings['reports_block'] : '' == 'on' ) : ?>

					<div class="irbn-tile irbn-medium">
						<div class="irbn-tile-header">
							<span class="irbn-header-text irbn-bold"><?php esc_html_e( 'Course Reports', 'wdm_instructor_role' ); ?></span>
							<?php if ( ! empty( $course_list ) ) : ?>
								<div class="ir-instructor-course-select-wrap">
									<select name="sel-instructor-courses" id="instructor-courses-select">
										<?php foreach ( $course_list as $key => $course_id ) : ?>
											<option value="<?php echo esc_attr( Cast::to_string( $course_id ) ); ?>" <?php echo ! ( $key ) ? 'selected' : ''; ?>>
												<?php echo esc_html( get_the_title( $course_id ) ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
							<?php endif; ?>
						</div>
						<div class="irbn-tile-content">
							<div class="ir-ajax-overlay" style="display: none;">
								<img src="<?php echo esc_attr( $ajax_icon ); ?>" alt="Loading...">
							</div>
							<?php if ( ! empty( $course_list ) ) : ?>
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

				<!-- <?php if ( 1 === ir_get_settings( ( 'instructor_commission' ) ) ) : ?>
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
								<option value="<?php echo esc_attr( Cast::to_string( $course_id ) ); ?>" <?php echo ! ( $key ) ? 'selected' : ''; ?>>
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
			<?php if ( isset( $ir_overview_settings['submission_block'] ) && 'on' == $ir_overview_settings['submission_block'] ? $ir_overview_settings['submission_block'] : '' == 'on' ) : ?>

				<div class="irbn-tiles-wrap irbn-sub">
					<div class="irbn-tile irbn-large">
						<div class="irbn-tile-header">
							<span class="irbn-header-text irbn-bold"><?php esc_html_e( 'Submissions', 'wdm_instructor_role' ); ?></span>
						</div>
						<div class="irbn-tile-content">
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
		</div>
	</div>
</div>
