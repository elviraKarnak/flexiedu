<?php
/**
 * Instructor Author Profile Template
 *
 * @since 3.5.0
 * @version 5.9.3
 *
 * @package LearnDash\Instructor_Role
 */

use InstructorRole\Modules\Classes\Instructor_Role_Profile;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$author_data                  = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
$instructor_course_statistics = Instructor_Role_Profile::get_instructor_course_statistics( $author_data->ID );
$instructor_social_links      = get_user_meta( $author_data->ID, 'ir_profile_social_links', true );

ir_get_template(
	INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/header.php'
);
?>

<?php
/**
 * Instructor Profile Start
 *
 * @since 3.5.2
 *
 * @param array $author_data    Instructor user data.
 */
do_action( 'ir_action_profile_start', $author_data );
?>

<div class="ir-profile">

	<?php
	/**
	 * Instructor Profile Header Start
	 *
	 * @since 3.5.2
	 *
	 * @param array $author_data    Instructor user data.
	 */
	do_action( 'ir_action_profile_header_start', $author_data );
	?>

	<div class="irp-top">
		<div class="irp-container">
			<div class="irp-image">
				<?php
				echo get_avatar(
					$author_data->ID,
					220,
					'',
					'',
					[
						'height' => 220,
						'width'  => 220,
					]
				);
				?>
			</div>
		</div>
	</div>

	<?php
	/**
	 * Instructor Profile Header End
	 *
	 * @since 3.5.2
	 *
	 * @param array $author_data    Instructor user data.
	 */
	do_action( 'ir_action_profile_header_end', $author_data );
	?>

	<?php
	/**
	 * Instructor Profile Content Start
	 *
	 * @since 3.5.2
	 *
	 * @param array $author_data    Instructor user data.
	 */
	do_action( 'ir_action_profile_content_start', $author_data );
	?>

	<div class="irp-content">
		<div class="irp-container irp-flex">
			<div class="irp-left">
				<div class="irp-info">
					<h1>
						<?php the_author_meta( 'display_name', $author_data->ID ); ?>
					</h1>
					<span class="irp-designation">
						<?php echo esc_html( ir_get_profile_designation( $author_data ) ); ?>
					</span>
					<?php if ( defined( 'WDM_LD_COURSE_VERSION' ) && ! empty( $instructor_course_statistics['instructor_reviews_count'] ) ) : ?>
						<div class="irp-rating">
							<div class="">
								<span class="irp-avg-rating">
									<i class="ir-icon-Star"></i>
									<span><?php echo esc_attr( $instructor_course_statistics['avg_instructor_rating'] ); ?></span>
								</span>
								<span class="irp-total-rating">
									(<?php echo esc_attr( $instructor_course_statistics['instructor_reviews_count'] ); ?>)
								</span>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( ! empty( $instructor_course_statistics ) ) : ?>
					<div class="irp-courses-info">
						<?php if ( ! empty( $instructor_course_statistics['courses_offered'] ) ) : ?>
							<div class="irp-courses-offered">
								<i class="ir-icon-Courses"></i>
								<label>
									<span><?php echo esc_html( $instructor_course_statistics['courses_offered'] ); ?></span>
									<?php
										printf(
										/* translators: Courses label */
											esc_html__( '%s offered', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::get_label( 'courses' )
										);
									?>
								</label>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $instructor_course_statistics['students_count'] ) ) : ?>
							<div class="irp-enrolled-courses">
								<i class="ir-icon-Students"></i>
								<label>
									<span><?php echo esc_html( $instructor_course_statistics['students_count'] ); ?></span> <?php esc_html_e( 'Enrolled Students', 'wdm_instructor_role' ); ?>
								</label>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $instructor_course_statistics['completed_course_per'] ) ) : ?>
							<div class="irp-completed-courses">
								<i class="ir-icon-Percentage"></i>
								<label><span><?php echo esc_html( $instructor_course_statistics['completed_course_per'] ) . '%'; ?></span>
									<?php
										printf(
										/* translators: Courses label */
											esc_html__( 'Students completed %s', 'wdm_instructor_role' ),
											\LearnDash_Custom_Label::get_label( 'courses' )
										);
									?>
								</label>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $instructor_social_links ) ) : ?>
					<div class="irp-social">
						<h2>
							<?php esc_html_e( 'CONNECT WITH ME', 'wdm_instructor_role' ); ?>
						</h2>
						<?php if ( ! empty( $instructor_social_links['facebook'] ) ) : ?>
							<div class="irp-social-type irp-fb">
								<i class="ir-icon-Facebook"></i>
								<a href="<?php printf( '//%s', esc_attr( $instructor_social_links['facebook'] ) ); ?>"><?php echo esc_attr( $instructor_social_links['facebook'] ); ?></a>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $instructor_social_links['twitter'] ) ) : ?>
							<div class="irp-social-type irp-twitter">
								<i class="ir-icon-Twitter"></i>
								<a href="<?php printf( '//%s', esc_attr( $instructor_social_links['twitter'] ) ); ?>"><?php echo esc_attr( $instructor_social_links['twitter'] ); ?></a>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $instructor_social_links['youtube'] ) ) : ?>
							<div class="irp-social-type irp-youtube">
								<i class="ir-icon-YouTube"></i>
								<a href="<?php printf( '//%s', esc_attr( $instructor_social_links['youtube'] ) ); ?>"><?php echo esc_attr( $instructor_social_links['youtube'] ); ?></a>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="irp-right">
				<div class="irp-right-content">
					<div class="irp-tabs">
						<span class="irp-active" data-id="irp-intro">
							<?php esc_html_e( 'INTRODUCTION', 'wdm_instructor_role' ); ?>
						</span>
						<span data-id="irp-courses">
							<?php
							echo esc_html_x(
								strtoupper( \LearnDash_Custom_Label::get_label( 'courses' ) ),
								'placeholder: Courses',
								'wdm_instructor_role'
							);
							?>
							</span>
						<?php if ( defined( 'WDM_LD_COURSE_VERSION' ) ) : ?>
							<span data-id="irp-rrf">
								<?php esc_html_e( 'RATINGS & REVIEWS', 'wdm_instructor_role' ); ?>
							</span>
						<?php endif; ?>
					</div>
					<div class="irp-tabs-content">
						<div class="irp-tab-content" data-id="irp-intro" style="display: block;">
							<h3 class="irp-hidden-lg irp-title"><?php esc_html_e( 'INTRODUCTION', 'wdm_instructor_role' ); ?></h3>
							<p>
								<?php the_author_meta( 'description', $author_data->ID ); ?>
							</p>

							<?php Instructor_Role_Profile::display_instructor_sections( $author_data->ID ); ?>

						</div>

						<div class="irp-tab-content" data-id="irp-courses">
							<h3 class="irp-hidden-lg irp-title">
							<?php
							echo esc_html_x(
								strtoupper( \LearnDash_Custom_Label::get_label( 'courses' ) ),
								'placeholder: Courses',
								'wdm_instructor_role'
							);
							?>
							</h3>
							<?php echo do_shortcode( "[ld_course_list col=2 instructor={$author_data->ID}]" ); ?>
						</div>
						<?php if ( defined( 'WDM_LD_COURSE_VERSION' ) ) : ?>
							<div class="irp-tab-content" data-id="irp-rrf">
								<h3 class="irp-hidden-lg irp-title"><?php esc_html_e( 'RATINGS & REVIEWS', 'wdm_instructor_role' ); ?></h3>
								<?php Instructor_Role_Profile::display_instructor_ratings_graph_section( $author_data->ID ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	/**
	 * Instructor Profile Content End
	 *
	 * @since 3.5.2
	 *
	 * @param array $author_data    Instructor user data.
	 */
	do_action( 'ir_action_profile_content_end', $author_data );
	?>

</div>

<?php
/**
 * Instructor Profile End
 *
 * @since 3.5.2
 *
 * @param array $author_data    Instructor user data.
 */
do_action( 'ir_action_profile_end', $author_data );
?>

<?php
ir_get_template(
	INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/footer.php'
);
