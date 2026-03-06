<?php
/**
 * Instructor Profile: List type data display template
 *
 * @since 3.5.0
 *
 * @var array   $section_details    Array of details for the section
 * @var array   $list_data          Array of data for the list type data
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
		<?php if ( is_array( $list_data ) && ! empty( $list_data ) ) : ?>
			<?php foreach ( $list_data as $data ) : ?>
				<li>
					<i class="<?php echo esc_attr( $icon_class ); ?>"></i>
					<span><?php echo esc_html( $data ); ?></span>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>
