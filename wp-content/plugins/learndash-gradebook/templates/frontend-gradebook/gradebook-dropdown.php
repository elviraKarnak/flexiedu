<?php
/**
 * Template for the Gradebook Dropdown
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var array           $gradebook_ids
 * @var integer         $user_id
 * @var integer         $gradebook_id
 */

defined( 'ABSPATH' ) || die();
?>

<div class="ld-gb-frontend-gradebook-gradebook-dropdown-container">

	<?php $html_id = 'ld-gb-frontend-gradebook-gradebook-dropdown-' . wp_generate_uuid4(); ?>

	<label for="<?php echo esc_attr( $html_id ); ?>">
		<?php _ex( 'Gradebook:', 'Frontend Gradebook: Gradebook Dropdown Label', 'learndash-gradebook' ); ?>
	</label>

	<select id="<?php echo esc_attr( $html_id ); ?>" class="ld-gb-frontend-gradebook-gradebook-dropdown">
		<?php foreach ( $gradebook_ids as $index => $id ) : ?>
			<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $gradebook_id ); ?>>
				<?php echo get_the_title( $id ); ?>
			</option>
		<?php endforeach; ?>
	</select>

</div>
