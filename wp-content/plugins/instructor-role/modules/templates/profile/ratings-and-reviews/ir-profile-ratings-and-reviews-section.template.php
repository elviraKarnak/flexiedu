<?php
/**
 * Instructor Ratings and Reviews Section Template
 *
 * @since 3.5.0
 *
 * @var array   $combined_ratings               Combined ratings for all course reviews.
 * @var array   $review_split                   Course reviews split data for the bar graph display.
 * @var array   $instructor_course_reviews      List of all course reviews for all of the current instructor courses.
 * @var string  $course_label                   Label for LearnDash Course
 * @var mixed   $rating_args                    RRF ratings arguments
 * @var mixed   $is_review_comments_enabled     RRF check if review comments are enabled
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.

defined( 'ABSPATH' ) || exit;
?>
<div class="review-top-section">
	<div class="review-top-col">
		<div class="review-top-star-wrap">
			<div class="irp-rating">
				<div class="">
					<span class="irp-avg-rating">
						<i class="ir-icon-Star"></i>
						<span><?php echo esc_attr( $combined_ratings['average_rating'] ); ?></span>
					</span>
					<span class="irp-total-rating">
						(<?php echo esc_attr( $combined_ratings['total_count'] ); ?>)
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="review-top-col">
		<?php for ( $i = count( $review_split ); $i > 0; $i-- ) : ?>
			<div class="review-split-wrap">
				<div class="review-split-title"><?php echo esc_html( $i ); ?></div>
				<div class="review-split-percent">
						<div class="review-split-percent-inner review-split-percent-inner-1"></div>
						<div class="review-split-percent-inner review-split-percent-inner-2" style="width:<?php echo esc_attr( $review_split[ $i ]['percentage'] ); ?>%;"></div>
				</div><!-- .review-split-percent closing -->
				<div class="review-split-count"><?php echo esc_html( $review_split[ $i ]['value'] ); ?></div>
			</div>
		<?php endfor; ?>
	</div>
</div>
<div class="review-bottom-section">
	<div class="reviews-listing-wrap" id="reviews-listing-wrap">
		<div class="wdm_course_rating_reviews">
			<div class="review_listing">
				<?php if ( ! empty( $instructor_course_reviews ) ) : ?>
					<?php foreach ( $instructor_course_reviews as $course_id => $course_reviews ) : ?>
						<div>
							<span><?php echo esc_attr( $course_label ); ?>: </span>
							<a href="<?php echo esc_attr( get_permalink( $course_id ) ); ?>">
								<?php echo esc_attr( get_the_title( $course_id ) ); ?>
							</a>
						</div>
						<?php foreach ( $course_reviews as $review ) : ?>
							<?php include \ns_wdm_ld_course_review\Review_Submission::get_template( 'single-review.php' ); ?>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div><!-- .review_listing closing -->
		</div>
	</div>
</div>
