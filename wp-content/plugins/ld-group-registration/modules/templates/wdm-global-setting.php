<?php
/**
 * Template: LDGR Global Settings Template
 *
 * @since 3.0.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 *
 * cspell:ignore sbmt
 */

defined( 'ABSPATH' ) || exit();
?>

<div class="wdm-ldgr-setting-div">
	<div class="ldgr-setup-wizard-help-tip">
		<a href="<?php echo esc_url( $setup_wizard_link ); ?>" title="<?php esc_html_e( 'Launch setup wizard', 'wdm_ld_group' ); ?>">
			<img src="<?php echo esc_url( plugins_url( 'media/setup-wizard-gear.svg', __DIR__ ) ); ?>">
		</a>
	</div>
	<form name="frm_ld_gr_setting" method="POST">
	<table>
		<tr>
			<th>
				<label for="ldgr_admin_approval">
				<?php
				echo apply_filters( 'gr_admin_approve_label', __( 'Allow Group Leader to Remove Members from the Group (without Admin Approval) : ', 'wdm_ld_group' ) );
				?>
				</label>
				<div>
					<label class="wdm_help_text">
					<?php
					_e( 'It allows the Group Leader to remove a member from group without sending a request to the admin.', 'wdm_ld_group' );
					?>
					</label>
				</div>
			</th>
			<td>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_admin_approval"   <?php echo ( $ldgr_admin_approval == 'on' ) ? 'checked' : ''; ?>>
					<span class="wdm-slider round"></span>
				</label>
			</td>
		</tr>
		<tr>
			<th>
				<label for="ldgr_group_limit">
				<?php
				echo apply_filters( 'gr_group_limit_label', __( 'Fix Group Limit : ', 'wdm_ld_group' ) );
				?>
				</label>
				<div>
					<label class="wdm_help_text">
					<?php
					_e( 'Restrict users to be added to a group on removing currently added users.', 'wdm_ld_group' );
					?>
					</label>
				</div>
			</th>
			<td>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_group_limit" <?php echo ( $ldgr_group_limit == 'on' ) ? 'checked' : ''; ?>>
					<span class="wdm-slider round"></span>
				</label>
			</td>
		</tr>
		<tr>
			<th>
				<label for="ldgr_reinvite_user">
				<?php
				echo apply_filters( 'gr_reinvite_user_label', __( 'Allow Group Leader to ReInvite Group Users : ', 'wdm_ld_group' ) );
				?>
				</label>
				<div>
					<label class="wdm_help_text">
					<?php
					_e( 'Enable this option if you want to allow Group Leader to ReInvite Group Users via email. It allows user to reset password.', 'wdm_ld_group' );
					?>
					</label>
				</div>
			</th>
			<td>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_reinvite_user"
					<?php
					echo ( $ldgr_reinvite_user == 'on' ) ? 'checked' : '';
					?>
					>
					<span class="wdm-slider round"></span>
				</label>
			</td>
		</tr>

		<!-- ldgr_group_courses -->
		<tr>
			<th>
				<label for="ldgr_group_courses">
				<?php
				echo apply_filters( 'gr_group_courses_label', __( 'Display the Courses associated with Group : ', 'wdm_ld_group' ) );
				?>
				</label>
				<div>
					<label class="wdm_help_text">
					<?php
					_e( 'Enable this option if you want to display the Courses of a Group on Group Registration page.', 'wdm_ld_group' );
					?>
					</label>
				</div>
			</th>
			<td>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_group_courses"
					<?php
					echo ( $ldgr_group_courses == 'on' ) ? 'checked' : '';
					?>
					>
					<span class="wdm-slider round"></span>
				</label>
			</td>
		</tr>

		<tr>
			<th>
				<label for="ldgr_user_redirects">
					<?php
					echo apply_filters(
						'gr_user_redirects_label',
						__( 'Redirect users after successful login : ', 'wdm_ld_group' )
					);
					?>
				</label>
				<div>
					<label class="wdm_help_text">
						<?php
						_e(
							'Enable this option if you wish to redirect users to specific pages after login.',
							'wdm_ld_group'
						);
						?>
					</label>
				</div>
			</th>
			<td>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_user_redirects" <?php echo ( $ldgr_user_redirects == 'on' ) ? 'checked' : ''; ?> >
					<span class="wdm-slider round"></span>
				</label>
			</td>
		</tr>

		<tr class='ldgr-user-redirects-settings' <?php echo ( $ldgr_user_redirects == 'on' ) ? '' : 'style="display: none"'; ?>>
			<td>
				<div>
					<p>
						<label for="ldgr_redirect_group_leader">
							<?php _e( 'Redirect Group Leader', 'wdm_ld_group' ); ?>
						</label>
						<?php
							echo wp_dropdown_pages(
								[
									'name'              => 'ldgr_redirect_group_leader',
									'echo'              => 0,
									'show_option_none'  => __( '&mdash; Select &mdash;' ),
									'option_none_value' => '0',
									'selected'          => get_option( 'ldgr_redirect_group_leader' ),
								]
							);
							?>
					</p>
					<p>
						<label for="ldgr_redirect_group_user">
							<?php _e( 'Redirect Group User', 'wdm_ld_group' ); ?>
						</label>
						<?php
							echo wp_dropdown_pages(
								[
									'name'              => 'ldgr_redirect_group_user',
									'echo'              => 0,
									'show_option_none'  => __( '&mdash; Select &mdash;' ),
									'option_none_value' => '0',
									'selected'          => get_option( 'ldgr_redirect_group_user' ),
								]
							);
							?>
					</p>
				</div>
			</td>
		</tr>

		<tr>
			<th>
				<label for="ldgr_unlimited_members">
					<?php
					echo apply_filters(
						'ldgr_unlimited_members_label',
						__( 'Enter a label for Unlimited Members: ', 'wdm_ld_group' )
					);
					?>
				</label>
				<div>
					<label class="wdm_help_text">
						<?php
						_e(
							'This label will be used on the product, cart and checkout pages for referring the unlimited seats options',
							'wdm_ld_group'
						);
						?>
					</label>
				</div>
			</th>
			<td>
				<input type="text" name="ldgr_unlimited_members_label" value="<?php echo esc_attr( $ldgr_unlimited_members_label ); ?>" />
			</td>
		</tr>

		<tr>
			<th>
				<label for="ldgr_display_product_footer">
				<?php
				echo apply_filters( 'ldgr_display_product_footer_label', __( 'Display the group product footer bar on product page: ', 'wdm_ld_group' ) );
				?>
				</label>
				<div>
					<label class="wdm_help_text">
					<?php
					_e( 'Whether or not to display the sticky product footer bar on the product page for group products.', 'wdm_ld_group' );
					?>
					</label>
				</div>
			</th>
			<td>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_display_product_footer"
						<?php echo ( 'on' === $ldgr_display_product_footer ) ? 'checked' : ''; ?>>
					<span class="wdm-slider round"></span>
				</label>
			</td>
		</tr>

		<tr>
			<th>
				<label for="ldgr_display_product_courses">
				<?php
				echo apply_filters( 'ldgr_display_product_courses_label', __( 'Display courses added in the group on group creation on product page : ', 'wdm_ld_group' ) );
				?>
				</label>
				<div>
					<label class="wdm_help_text">
					<?php
					_e( 'Enable this to display the courses related to the group product on the woocommerce product page.', 'wdm_ld_group' );
					?>
					</label>
				</div>
			</th>
			<td>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_display_product_courses"
						<?php echo ( 'on' === $ldgr_display_product_courses ) ? 'checked' : ''; ?>>
					<span class="wdm-slider round"></span>
				</label>
			</td>
		</tr>
		<tr>
			<th>
				<label for="ldgr_autofill_group_name">
				<?php
				echo apply_filters( 'ldgr_autofill_group_name_label', __( 'Autofill Group Names : ', 'wdm_ld_group' ) );
				?>
				</label>
				<div>
					<label class="wdm_help_text">
					<?php
					_e( 'Enable this to autofill group names during woocommerce product group creation.', 'wdm_ld_group' );
					?>
					</label>
				</div>
			</th>
			<td>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_autofill_group_name"
						<?php echo ( 'on' === $ldgr_autofill_group_name ) ? 'checked' : ''; ?>>
					<span class="wdm-slider round"></span>
				</label>
			</td>
		</tr>
		<tr>
			<th>
				<label for="ldgr_enter_bulk_discount">
					<?php
					echo apply_filters(
						'ldgr_enter_bulk_discount',
						__( 'Bulk Discounts: ', 'wdm_ld_group' )
					);
					?>
				</label>
				<div>
					<label class="wdm_help_text">
						<?php
						printf(
							esc_html__(
								// translators: article link.
								'Enable this option if you want bulk purchase discount of group products. Learn more %s',
								'wdm_ld_group'
							),
							'<a target="blank" href="https://www.learndash.com/support/docs/add-ons/group-registration-for-learndash/">here</a>'
						);
						?>
					</label>
				</div>
			</th>
			<td>
				<label class="wdm-switch">
					<input type="checkbox" name="ldgr_bulk_discounts" <?php echo ( 'on' === $ldgr_bulk_discounts ) ? 'checked' : ''; ?> >
					<span class="wdm-slider round"></span>
				</label>
			</td>
		</tr>
		<tr class='ldgr_bulk_discount_setting_data' <?php echo ( 'on' === $ldgr_bulk_discounts ) ? '' : 'style="display: none"'; ?>>
			<td colspan="2">
				<div class="ldgr_duplicate_row_rule_error" style="margin-bottom: 10px; color: red;display: none;"><?php esc_html_e( 'Duplicate quantity rule not allowed', 'wdm_ld_group' ); ?></div>
				<div class="addel-container">
					<table class="ldgr_bulk_discount_table">
						<tr>
							<th>
								<?php
								esc_html_e(
									'Min Quantity',
									'wdm_ld_group'
								);
								?>
							</th>
							<th>
								<?php
								esc_html_e(
									'Percentage',
									'wdm_ld_group'
								);
								?>
							</th>
							<th></th>
						</tr>
						<?php
						if ( $ldgr_bulk_discount_global_values ) {
							foreach ( $ldgr_bulk_discount_global_values as $discount_global_values ) {
								?>
									<tr class="addel-target">
										<td>
											<input class="ldgr_bulk_discount_setting_input ldgr_bulk_discount_value_validate" min="1" step="1" type="number" value="<?php echo esc_html( $discount_global_values['quantity'] ); ?>" name="ldgr_bulk_discount_global_values[min_quantity][]">
										</td>
										<td>
											<input class="ldgr_bulk_discount_setting_input" type="number" min="1" max="100" step="0.01" value="<?php echo esc_html( $discount_global_values['value'] ); ?>" name="ldgr_bulk_discount_global_values[discount_value][]">
										</td>
										<td>
											<div class="addel-delete"><b><span class="dashicons dashicons-no"></span></b></div>
										</td>
									</tr>
								<?php
							}
						} else {
							?>
								<tr class="addel-target">
									<td>
										<input class="ldgr_bulk_discount_setting_input ldgr_bulk_discount_value_validate" min="1" step="1" type="number" name="ldgr_bulk_discount_global_values[min_quantity][]">
									</td>
									<td>
										<input class="ldgr_bulk_discount_setting_input" type="number" min="1" max="100" step="0.01" name="ldgr_bulk_discount_global_values[discount_value][]">
									</td>
									<td>
										<div class="addel-delete"><b><span class="dashicons dashicons-no"></span></b></div>
									</td>
								</tr>
							<?php
						}
						?>
					</table>
					<button class="addel-add"><?php esc_html_e( 'Add', 'wdm_ld_group' ); ?></button>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php submit_button( __( 'Save', 'wdm_ld_group' ) ); ?>
				<?php wp_nonce_field( 'ldgr_setting', 'sbmt_ldgr_setting' ); ?>
			</td>
		</tr>
	</table>
	</form>
</div>
