<?php
/**
 * Setup Wizard: General Settings View Template.
 *
 * @since 4.2.0
 * @version 4.3.15
 *
 * @var object  $wizard_handler         Setup Wizard class object.
 * @var mixed   $ldgr_admin_approval    Admin approval setting value.
 * @var mixed   $ldgr_user_redirects    User redirect setting value.
 * @var mixed   $group_leader_redirect  Group leader redirects setting value
 * @var mixed   $group_user_redirect    Group user redirects setting value.
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<form method="post" action="<?php echo esc_url( $wizard_handler->get_next_step_link() ); ?>">
	<table class="form-table">
		<tbody>
			<!-- <tr>
				<th scope="row"><label for="inp_textbox">Textbox</label></th>
				<td>
					<input type="text" id="inp_textbox" name="inp_textbox" class="location-input" value="">
					<p class="description">The textbox input field</p>
				</td>
			</tr> -->
			<!-- <tr>
				<th scope="row"><label for="male">Radio Buttons</label></th>
				<td>
					<input type="radio" id="male" name="gender" value="male">
					<label for="male">Male</label><br>
					<input type="radio" id="female" name="gender" value="female">
					<label for="female">Female</label><br>
					<input type="radio" id="other" name="gender" value="other">
					<label for="other">Other</label>
					<p class="description">The Radio Button input field</p>
				</td>
			</tr> -->
			<tr>
				<th scope="row">
					<label for="ldgr_admin_approval">
						<?php esc_html_e( 'Remove Users', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_admin_approval" id="ldgr_admin_approval" class="switch-input" <?php checked( $ldgr_admin_approval, 'on' ); ?>>
					<label for="ldgr_admin_approval" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description"><?php esc_html_e( 'Allow group leader to remove members from the group (without Administrator approval).', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="ldgr_user_redirects">
						<?php esc_html_e( 'User Redirects', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<input type="checkbox" name="ldgr_user_redirects" id="ldgr_user_redirects" class="switch-input" <?php checked( $ldgr_user_redirects, 'on' ); ?>>
					<label for="ldgr_user_redirects" class="switch-label">
						<span class="toggle--on"><?php esc_html_e( 'On', 'wdm_ld_group' ); ?></span>
						<span class="toggle--off"><?php esc_html_e( 'Off', 'wdm_ld_group' ); ?></span>
					</label>
					<span class="description"><?php esc_html_e( 'Redirect users to specific pages after successful login.', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<tr class="ldgr-user-redirects-settings <?php echo ( 'on' !== $ldgr_user_redirects ) ? 'hide' : ''; ?>">
				<th scope="row">
					<label for="ldgr_redirect_group_leader">
						<?php esc_html_e( 'Group Leader', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<?php
					echo wp_dropdown_pages(
						[
							'name'              => 'ldgr_redirect_group_leader',
							'echo'              => 0,
							'show_option_none'  => __( '&mdash; Select &mdash;' ),
							'option_none_value' => '0',
							'selected'          => $group_leader_redirect,
						]
					);
					?>
					<span class="description"><?php esc_html_e( 'Redirect group leaders to specific pages after successful login.', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<tr class="ldgr-user-redirects-settings <?php echo ( 'on' !== $ldgr_user_redirects ) ? 'hide' : ''; ?>">
				<th scope="row">
					<label for="ldgr_redirect_group_user">
						<?php esc_html_e( 'Group Member', 'wdm_ld_group' ); ?>
					</label>
				</th>
				<td>
					<?php
					echo wp_dropdown_pages(
						[
							'name'              => 'ldgr_redirect_group_user',
							'echo'              => 0,
							'show_option_none'  => __( '&mdash; Select &mdash;' ),
							'option_none_value' => '0',
							'selected'          => $group_user_redirect,
						]
					);
					?>
					<span class="description"><?php esc_html_e( 'Redirect group members to specific pages after successful login.', 'wdm_ld_group' ); ?></span>
				</td>
			</tr>
			<!-- <tr>
				<th scope="row"><label for="inp_color">Color</label></th>
				<td>
					<input type="color" id="inp_color" name="inp_color">
					<span class="description">Color input type. Probably it will behave differently in different browsers.</span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="inp_date">Date</label></th>
				<td>
					<input type="date" id="inp_date" name="inp_date">
					<span class="description">Date input type. Probably it will behave differently in different browsers.</span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="inp_email">Email</label></th>
				<td>
					<input type="email" id="inp_email" name="inp_email">
					<span class="description">Email input type.</span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="inp_file">File</label></th>
				<td>
					<input type="file" id="inp_file" name="inp_file">
					<span class="description">File input type.</span>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="inp_number">Number</label></th>
				<td>
					<input type="number" id="inp_number" name="inp_number" min="1" max="5">
					<span class="description">Number input type.</span>
				</td>
			</tr> -->
		</tbody>
	</table>
	<p class="wc-setup-actions step">
		<input type="submit" class="button-primary button button-large button-next" value="<?php esc_html_e( 'Continue', 'wdm_ld_group' ); ?>" name="save_step">
		<input type="hidden" name="wisdm_setup_step" value="<?php echo esc_attr( $wizard_handler->get_current_step_slug() ); ?>" />
		<a href="<?php echo esc_url( $wizard_handler->get_next_step_link() ); ?>" class="button button-large button-next"><?php esc_html_e( 'Skip this step', 'wdm_ld_group' ); ?></a>
	<?php wp_nonce_field( 'setup_general_settings', 'wisdm_setup_nonce' ); ?>
	</p>
</form>
