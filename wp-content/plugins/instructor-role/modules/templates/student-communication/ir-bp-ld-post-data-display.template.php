<?php
/**
 * Buddypress Message LD Post data display template.
 *
 * @since 3.6.0
 *
 * @var WP_Post $post           Post object.
 * @var int $course_id          Course ID.
 * @var string  $lesson_label   Lesson/Topic label.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<div class="ir-ld-post-info">
	<?php
	printf(
		// Translators: Course label, Course title, Lesson label, Lesson title.
		esc_html__( '%1$s : %2$s, %3$s: %4$s', 'wdm_instructor_role' ),
		\LearnDash_Custom_Label::get_label( 'course' ),
		get_the_title( $course_id ),
		$lesson_label,
		$post->post_title
	);
	?>
</div>
