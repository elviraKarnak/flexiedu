<?php
/**
 * Share Course Metabox Template
 *
 * @since 3.2.0
 *
 * @var object  $course
 * @var array   $all_instructors
 * @var array   $shared_instructor_ids
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;

?>
<?php if ( get_current_user_id() == $course->post_author || current_user_can( 'manage_options' ) ) : ?>
	<div class="ir-share-course-metabox-div">
		<p>
		<?php
		printf(
			/* translators: Course label */
			esc_html__( 'Select the list of instructors you wish to share this %s with', 'wdm_instructor_role' ),
			\LearnDash_Custom_Label::label_to_lower( 'course' )
		);
		?>
			</p>
		<select name="shared_instructors[]" id="ir-shared-instructors" style="width: 100%;" multiple>
			<?php foreach ( $all_instructors as $instructor ) : ?>
				<option
					value="<?php echo $instructor->ID; ?>"
					data-avatar="<?php echo get_avatar_url( $instructor->ID, [ 'size' => 32 ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>"
					<?php echo in_array( $instructor->ID, $shared_instructor_ids ) ? 'selected' : ''; ?>>
					<?php echo $instructor->display_name; ?>
				</option>
			<?php endforeach ?>
		</select>
		<?php wp_nonce_field( 'ir_shared_instructors_nonce', 'ir_nonce' ); ?>
	</div>
<?php else : ?>
	<div class="ir-course-shared-message">
		<?php
		echo apply_filters(
			'ir_filter_share_course_restriction_message',
			sprintf(
				wp_kses(
					/* translators: 1: Course Label 2: Author Name */
					__( '<p>Sorry, but you cannot share this %1$s with anyone.</p><p>Contact <b>%2$s</b>, the author of this %1$s.</p>', 'wdm_instructor_role' ),
					[
						'p' => [
							'b' => [],
						],
					]
				),
				\LearnDash_Custom_Label::label_to_lower( 'course' ),
				get_the_author_meta( 'display_name', $course->post_author )
			),
			$course
		);
		?>
	</div>
<?php endif; ?>
