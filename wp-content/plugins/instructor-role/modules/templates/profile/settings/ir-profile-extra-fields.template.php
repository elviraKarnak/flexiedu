<?php
/**
 * Instructor Extra Fields Template
 *
 * @since 3.5.0
 *
 * @var mixed $social_links     List of all social links
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<h3>
	<?php esc_html_e( 'Instructor Profile Additional Data', 'wdm_instructor_role' ); ?>
</h3>

<table class="ir-profile-extra-fields-table form-table">
	<tr>
		<th>
			<label>
				<?php esc_html_e( 'FaceBook', 'wdm_instructor_role' ); ?>
			</label>
		</th>
		<td>
			<input type="text" name="ir_profile_social_links[facebook]" id="ir_profile_social_links_facebook" value="<?php echo esc_attr( $social_links['facebook'] ); ?>">
		</td>
	</tr>
	<tr>
		<th>
			<label>
				<?php esc_html_e( 'Twitter', 'wdm_instructor_role' ); ?>
			</label>
		</th>
		<td>
			<input type="text" name="ir_profile_social_links[twitter]" id="ir_profile_social_links_twitter" value="<?php echo esc_attr( $social_links['twitter'] ); ?>">
		</td>
	</tr>
	<tr>
		<th>
			<label>
				<?php esc_html_e( 'Youtube', 'wdm_instructor_role' ); ?>
			</label>
		</th>
		<td>
			<input type="text" name="ir_profile_social_links[youtube]" id="ir_profile_social_links_youtube" value="<?php echo esc_attr( $social_links['youtube'] ); ?>">
		</td>
		<?php wp_nonce_field( 'ir_profile_extra_fields_nonce', 'ir_social_fields_nonce' ); ?>
	</tr>
</table>
