<?php
/**
 * Instructor Profile: List type data input template
 *
 * @since 3.5.0
 *
 * @var array   $list_data      Array of data for the list type input.
 * @var string  $meta_key       Meta key name of the data.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="ir-profile-list-container">
	<?php foreach ( $list_data as $single ) : ?>
		<div class="ir-profile-list">
			<div class="ir-profile-input">
				<input name="<?php echo esc_attr( $meta_key ); ?>[]" class="ir-profile-input-list" size="50" value="<?php echo esc_attr( $single ); ?>"/>
				<span class="ir-profile-remove-input dashicons dashicons-no"></span>
			</div>
		</div>
	<?php endforeach; ?>
	<span class="ir-profile-add-input dashicons dashicons-plus-alt"></span>
</div>
