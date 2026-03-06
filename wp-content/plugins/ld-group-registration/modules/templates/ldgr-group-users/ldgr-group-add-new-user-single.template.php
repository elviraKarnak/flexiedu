<?php
/**
 * LDGR Group Users add single user row template.
 *
 * @since 4.2.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div class="ldgr-add-user">
	<div class="ldgr-field">
		<label><?php esc_html_e( 'first name', 'wdm_ld_group' ); ?></label>
		<input type="text" class="ldgr-textbox" name='wdm_members_fname[]' />
		<span class="ldgr-field-error"></span>
	</div>
	<div class="ldgr-field">
		<label><?php esc_html_e( 'last name', 'wdm_ld_group' ); ?></label>
		<input type="text" class="ldgr-textbox" name='wdm_members_lname[]' />
		<span class="ldgr-field-error"></span>
	</div>
	<div class="ldgr-field">
		<label><?php esc_html_e( 'email', 'wdm_ld_group' ); ?></label>
		<input type="text" class="ldgr-textbox" name='wdm_members_email[]' />
		<span class="ldgr-field-error"></span>
	</div>
	<?php
	if ( ! empty( $dynamic_fields ) && is_array( $dynamic_fields ) ) {
		$dynamic_field_class = new \LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Dynamic_Fields();
		foreach ( $dynamic_fields as $key => $value ) {
			$html = $dynamic_field_class->create_dynamic_field( $value );
			echo $html;
		}
	}
	?>
	<div class="ldgr-field remove-user">
		<i class="ldgr-icon-Trash"></i>
		<span class="ldgr-rm-usr"><?php esc_html_e( 'Remove User', 'wdm_ld_group' ); ?></span>
	</div>
</div>
