<?php
/**
 * Message window topics list template
 *
 * @since 3.6.0
 *
 * @var array   $topic_list      Array of learndash topics.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<option value="0">
	<?php
	printf(
		/* translators: Topic Label */
		esc_html__( 'Select %s', 'wdm_instructor_role' ),
		\LearnDash_Custom_Label::get_label( 'topic' )
	);
	?>
</option>
<?php foreach ( $topic_list as $topic ) : ?>
	<option value="<?php echo esc_attr( $topic->ID ); ?>">
		<?php
		echo wp_trim_words(
			sprintf(
				/* translators: 1: Topic label 2: Topic title */
				esc_html__(
					'%1$s: %2$s',
					'wdm_instructor_role'
				),
				\LearnDash_Custom_Label::get_label( 'topic' ),
				$topic->post_title
			)
		);
		?>
	</option>
<?php endforeach; ?>
