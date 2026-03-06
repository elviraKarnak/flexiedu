<?php
/**
 * Instructor Profile: Paragraph type data display template
 *
 * @since 3.5.0
 *
 * @var array   $section_details    Array of details for the section
 * @var string  $paragraph_data     Data for the paragraph type data
 * @var string  $image_class        Custom class for the section image
 * @var string  $image_style        Custom style for the section image
 * @var string  $icon_class         Custom class for the section icon
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="irp-achievements">
	<h2><?php echo esc_attr( $section_details['title'] ); ?></h2>
	<div class="<?php echo esc_attr( $image_class ); ?>" <?php echo esc_attr( $image_style ); ?> >
		<?php if ( ! empty( $paragraph_data ) ) : ?>
			<li>
				<i class="<?php echo esc_attr( $icon_class ); ?>"></i>
				<?php if ( is_array( $paragraph_data ) ) : ?>
						<span><?php echo esc_html( array_pop( array_reverse( $paragraph_data ) ) ); ?></span>
					<?php else : ?>
						<span><?php echo esc_html( $paragraph_data ); ?></span>
				<?php endif; ?>
			</li>
		<?php endif; ?>
	</div>
</div>
