<?php
/**
 * LDGR Woocommerce Group Registration Metabox Template.
 *
 * @since 4.0.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;

?>
<?php do_action( 'ldgr_woo_product_metabox_start', $post ); ?>
<table>
	<tr>
		<td>
			<input
				type="checkbox"
				id="wdm_ld_group_registration"
				name="wdm_ld_group_registration"
				<?php echo ( $value != '' ) ? 'checked' : ''; ?>
			/>
		</td>
		<th style="text-align: left;">
			<?php echo apply_filters( 'wdm_group_registration_label', __( 'Enable Group Registration.', 'wdm_ld_group' ) ); ?>
			<?php echo wc_help_tip( __( 'This will allow you to sell this product as a LearnDash Group.', 'wdm_ld_group' ) ); ?>
		</th>
	</tr>
</table>

<table class="wdm_show_other_option" style="padding: 10px; width: -webkit-fill-available;">
	<tr>
		<td>
			<input
				type="checkbox"
				id="wdm_show_front_option"
				name="wdm_ld_group_registration_show_front_end"
				<?php echo ( $value_show != '' ) ? 'checked' : ''; ?>
			/>
		</td>
		<td>
			<?php
				echo apply_filters(
					'wdm_show_front_label',
					__( 'Give users the option to purchase an Individual OR a Group Product.', 'wdm_ld_group' )
				);
				?>
		</td>
	</tr>
	<tr class="wdm-default-front-option">
		<td></td>
		<td colspan="2">
			<p style="font-weight: bold;">
				<?php esc_html_e( 'What would be the default option?', 'wdm_ld_group' ); ?>
			</p>
			<p>
				<input
					type="radio"
					name="wdm_ld_group_active"
					value="individual"
					<?php echo ( $default_option == 'individual' ) ? 'checked' : ''; ?>
				/>
				<?php echo apply_filters( 'wdm_gr_single_label', __( 'Individual', 'wdm_ld_group' ) ); ?>
				<input
					type="radio"
					name="wdm_ld_group_active"
					value="group"
					<?php echo ( $default_option != 'individual' ) ? 'checked' : ''; ?>
				/>
				<?php echo apply_filters( 'wdm_gr_group_label', __( 'Group', 'wdm_ld_group' ) ); ?>
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<input
				type="checkbox"
				name="wdm_ldgr_paid_course"
				<?php echo ( $paid_course == 'on' ) ? 'checked' : ''; ?>
			/>
		</td>
		<td style="text-align: left;">
			<?php
				echo apply_filters(
					'wdm_local_pay_course_label',
					__( 'Ask the Group Leader to pay for access to Courses.', 'wdm_ld_group' )
				);
				echo wc_help_tip( __( 'Once enabled, the Group Leader will not have access to the Group Courses by default. Additionally, enabling this will ask the Group Leaders to pay for access to courses.', 'wdm_ld_group' ) );
				?>
		</td>
	</tr>
	<tr>
		<td>
			<input
				type="checkbox"
				name="ldgr_enable_unlimited_members"
				id="ldgr_enable_unlimited_members"
				<?php echo ( $is_unlimited == 'on' ) ? 'checked' : ''; ?>
			/>
		</td>
		<td style="text-align: left;">
			<?php
				echo apply_filters(
					'ldgr_enable_unlimited_members_label',
					__( 'Sell this Group with unlimited members.', 'wdm_ld_group' )
				);
				echo wc_help_tip( __( 'Once enabled, the user can purchase unlimited seats for this Product.', 'wdm_ld_group' ) );
				?>
		</td>
	</tr>
	<tr class="ldgr-unlimited-group-members-settings" <?php echo ( $is_unlimited !== 'on' ) ? 'style="display: none;"' : ''; ?>">
		<td>
		</td>
		<td>
			<!-- <p>
				<label for="ldgr_unlimited_members_option_label">
					<?php _e( 'Label for unlimited members', 'wdm_ld_group' ); ?> :
				</label>
				<input type="text" name="ldgr_unlimited_members_option_label" class="text" id="ldgr_unlimited_members_option_label" value="<?php echo $unlimited_label; ?>"/>
			</p> -->
			<p>
				<label for="ldgr_unlimited_members_option_price">
					<?php _e( 'Price for unlimited members', 'wdm_ld_group' ); ?> :
				</label>
				<input type="number" min='0' oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" name="ldgr_unlimited_members_option_price" class="text wc_input_price" id="ldgr_unlimited_members_option_price" value="<?php echo $unlimited_price; ?>" <?php echo ( 'on' === $is_unlimited ) ? 'required' : ''; ?>/>
			</p>
		</td>
	</tr>
	<tr>
		<td>
			<input
				type="checkbox"
				name="ldgr_enable_dynamic_group"
				id="ldgr_enable_dynamic_group"
				<?php echo ( $is_dynamic == 'on' ) ? 'checked' : ''; ?>
			/>
		</td>
		<td style="text-align: left;">
			<?php
				echo apply_filters(
					'ldgr_enable_dynamic_group_label',
					__( 'Additional Options for Groups', 'wdm_ld_group' )
				);
				?>
			<?php echo wc_help_tip( __( 'This setting will allow the Group Leader to: <br> - Add more Courses to the Purchase.<br> - Add more seats to an existing Group.<br> - Create a new group (even if the Group Leader has an existing Group)', 'wdm_ld_group' ) ); ?>
		</td>
	</tr>

	<tr class="ldgr-dynamic-group-settings" <?php echo ( 'on' !== $is_dynamic ) ? 'style="display: none;"' : ''; ?>">
		<td>
		</td>
		<td>
			<?php
			ldgr_wp_select_multiple(
				[
					'id'          => 'ldgr_dynamic_courses[]',
					'class'       => 'select2 regular-width select short ld_dynamic_courses',
					'label'       => sprintf( _x( 'Additional %1$s for %2$s Purchase.', 'LearnDash Courses', 'wdm_ld_group' ), $courses_label, $group_label ),
					'options'     => $courses,
					'desc_tip'    => true,
					'description' => __( 'The user can additionally add these courses to the purchase.', 'wdm_ld_group' ),
					'value'       => $selected_courses,
				]
			);
			?>
		</td>
	</tr>

	<tr class="ldgr-dynamic-group-settings ldgr-unlimited-price-type-setting" <?php echo ( 'on' !== $is_unlimited || 'on' !== $is_dynamic ) ? 'style="display: none;"' : ''; ?>">
		<td style="text-align: left;padding: 15px 0px;">
		</td>
		<td style="text-align: left;">
			<b>
				<?php
				echo apply_filters(
					'ldgr_dynamic_unlimited_price',
					__( '"Unlimited" Price Type - Additional Courses.', 'wdm_ld_group' )
				);
				?>
				<?php echo wc_help_tip( __( 'This setting will allow you to set the price for unlimited seats of the additional courses.', 'wdm_ld_group' ) ); ?>
			</b>
			<select name="ldgr_dynamic_unlimited_price">
				<option value="default" <?php echo ( 'default' === $is_dynamic_unlimited_default ) ? 'selected' : ''; ?>><?php esc_html_e( 'Select Type', 'wdm_ld_group' ); ?></option>
				<option value="fixed" <?php echo ( 'fixed' === $is_dynamic_unlimited_default ) ? 'selected' : ''; ?>><?php esc_html_e( 'Fixed Price', 'wdm_ld_group' ); ?></option>
				<option value="multiple" <?php echo ( 'multiple' === $is_dynamic_unlimited_default ) ? 'selected' : ''; ?>><?php esc_html_e( 'Multiple of price', 'wdm_ld_group' ); ?></option>
			</select>
		</td>
	</tr>

	<tr class="ldgr-dynamic-unlimited-member-value ldgr-unlimited-price-type-setting" <?php echo ( $is_dynamic_unlimited_default == 'default' || $is_unlimited !== 'on' ) ? 'style="display: none;"' : ''; ?>">
		<td></td>
		<td>
			<p>
				<label for="ldgr_unlimited_members_dynamic_price">
					<?php esc_html_e( 'Price Type Value.', 'wdm_ld_group' ); ?> :
				</label>
				<input type="number" min='0' oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" name="ldgr_unlimited_members_dynamic_price" class="text wc_input_price" id="ldgr_unlimited_members_dynamic_price" value="<?php echo $unlimited_dynamic_price; ?>" <?php echo ( 'default' === $is_dynamic_unlimited_default ) ? '' : 'required'; ?>/>

				<?php echo wc_help_tip( __( 'For example:<br> - If the Fixed Price Type is selected and the value you enter is 500, each additional course will be sold for 500.<br> - If the Multiple Price Type is selected and the value you enter in 5, each additional course will be sold for 5 *price for the course.', 'wdm_ld_group' ) ); ?>
			</p>
		</td>
	</tr>
</table>

<table class="wdm_show_other_option" style="padding: 10px; width: -webkit-fill-available;">
	<tr class="ldgr_enforce-qty">
		<td class="ldgr-checkbox-wrap">
			<input
				type="checkbox"
				name="ldgr_bulk_discount_min_qty_check"
				id="ldgr_bulk_discount_min_qty_check"
				<?php echo ( $ldgr_bulk_discount_min_qty_check == 'on' ) ? 'checked' : ''; ?>
			/>
		</td>
		<td>
			<?php echo esc_html( apply_filters( 'ldgr_filter_min_qty_text', __( 'Minimum number of seats to purchase.', 'wdm_ld_group' ) ) ); ?>
			<?php echo wc_help_tip( __( 'Select the minimum number of seats for your users to purchase.', 'wdm_ld_group' ) ); ?>
		</td>
	</tr>
	<tr class="ldgr_bulk_discount_min_qty_details" <?php echo ( 'on' === $ldgr_bulk_discount_min_qty_check ) ? 'style="padding-left: 15px;"' : 'style="padding-left: 15px; display: none !important"'; ?>>
		<td class="ldgr-label">
			<?php echo esc_html( apply_filters( 'ldgr_filter_min_qty_details_text', __( 'Minimum Quantity', 'wdm_ld_group' ) ) ); ?>
		</td>
		<td>
			<input type="number" name="ldgr_bulk_discount_min_qty_value" id="ldgr_bulk_discount_min_qty_value" value="<?php echo esc_attr( $ldgr_bulk_discount_min_qty_value ); ?>" <?php echo ( 'on' === $ldgr_bulk_discount_min_qty_check ) ? 'required' : ''; ?>>
		</td>
	</tr>
</table>

<table class="wdm_show_other_option" style="padding: 10px;">
	<tr>
		<td style="text-align: left;padding: 15px 0px;">
			<b>
				<?php
				echo apply_filters(
					'wdm_product_bulk_discount_label',
					__( 'Bulk Discount Setting', 'wdm_ld_group' )
				);
				?>
			</b>
		</td>
		<td style="text-align: left;">
			<select name="ldgr_type_bulk_discount_for_product_setting">
				<option value="Global" <?php echo ( 'Global' === $ldgr_type_bulk_discount_for_product_setting ) ? 'selected' : ''; ?>><?php esc_html_e( 'Global', 'wdm_ld_group' ); ?></option>
				<option value="Product" <?php echo ( 'Product' === $ldgr_type_bulk_discount_for_product_setting ) ? 'selected' : ''; ?>><?php esc_html_e( 'Product', 'wdm_ld_group' ); ?></option>
				<option value="Disable" <?php echo ( 'Disable' === $ldgr_type_bulk_discount_for_product_setting ) ? 'selected' : ''; ?>><?php esc_html_e( 'Disable', 'wdm_ld_group' ); ?></option>
			</select>
		</td>
	</tr>
</table>
<table class="wdm_show_other_option ldgr_bulk_discount_setting_data" <?php echo ( 'Product' === $ldgr_type_bulk_discount_for_product_setting ) ? '' : 'style="display: none !important"'; ?>>
	<tr>
		<td></td>
		<td>
			<div class="ldgr_duplicate_row_rule_error" style="margin-bottom: 10px; color: red;display: none;"><?php esc_html_e( 'Duplicate quantity rule not allowed', 'wdm_ld_group' ); ?></div>
		</td>
	</tr>
	<tr class="ldgr_bulk_discount_on_product_setting">
		<td style="text-align: left;">
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
								'Discount Type',
								'wdm_ld_group'
							);
							?>
						</th>
						<th>
						<?php
							esc_html_e(
								'Value',
								'wdm_ld_group'
							);
							?>
						</th>
						<th></th>
					</tr>
					<?php
					if ( $ldgr_enable_bulk_discount_for_product_setting ) {
						foreach ( $ldgr_enable_bulk_discount_for_product_setting as $discount_product_values ) {
							?>
								<tr class="addel-target">
									<td>
										<input class="ldgr_bulk_discount_setting_input ldgr_bulk_discount_value_validate" min="1" step="1" type="number" value="<?php echo esc_html( $discount_product_values['quantity'] ); ?>" name="ldgr_enable_bulk_discount_for_product_setting[min_quantity][]">
									</td>
									<td>
										<select class="ldgr_enable_bulk_discount_for_product_setting" name="ldgr_enable_bulk_discount_for_product_setting[discount_type][]">
											<option value="Percentage" <?php echo ( 'Percentage' === $discount_product_values['type'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Percentage', 'wdm_ld_group' ); ?></option>
											<option value="Fixed" <?php echo ( 'Fixed' === $discount_product_values['type'] ) ? 'selected' : ''; ?>><?php esc_html_e( 'Fixed', 'wdm_ld_group' ); ?></option>
										</select>
									</td>
									<td>
										<input class="ldgr_bulk_discount_setting_input" min="1" max="100" step="0.01" type="number" value="<?php echo esc_html( $discount_product_values['value'] ); ?>" name="ldgr_enable_bulk_discount_for_product_setting[discount_value][]">
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
									<input class="ldgr_bulk_discount_setting_input ldgr_bulk_discount_value_validate" min="1" step="1" type="number" name="ldgr_enable_bulk_discount_for_product_setting[min_quantity][]">
								</td>
								<td>
									<select class="ldgr_enable_bulk_discount_for_product_setting" name="ldgr_enable_bulk_discount_for_product_setting[discount_type][]">
										<option value="Percentage"><?php esc_html_e( 'Percentage', 'wdm_ld_group' ); ?></option>
										<option value="Fixed"><?php esc_html_e( 'Fixed', 'wdm_ld_group' ); ?></option>
									</select>
								</td>
								<td>
									<input class="ldgr_bulk_discount_setting_input" min="1" max="100" step="0.01" type="number" name="ldgr_enable_bulk_discount_for_product_setting[discount_value][]">
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
</table>
<?php do_action( 'ldgr_woo_product_metabox_end', $post ); ?>
