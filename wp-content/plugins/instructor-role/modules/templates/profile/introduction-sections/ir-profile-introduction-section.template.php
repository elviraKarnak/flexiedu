<?php
/**
 * Instructor Introduction Sections Template
 *
 * @since 3.5.0
 *
 * @var mixed   $introduction_settings_data     Array of all introduction section fields
 * @var object  $instance                       Instance of class irProfile.
 * @var object  $userdata                       WP_User object for the user.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<h3>
	<?php esc_html_e( 'Introduction Section Details', 'wdm_instructor_role' ); ?>
</h3>

<table class="ir-profile-introduction-sections-table form-table">
	<?php foreach ( $introduction_settings_data as $section_settings ) : ?>
		<tr>
			<th>
				<label>
					<?php echo esc_attr( $section_settings['title'] ); ?>
				</label>
			</th>
			<td>
				<?php echo $instance->display_section_input( $userdata->ID, $section_settings['data_type'], $section_settings['meta_key'] ); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	<?php wp_nonce_field( 'ir_profile_introduction_section_nonce', 'ir_profile_introduction_section_nonce' ); ?>
</table>
