<?php
/**
 * Additional instructor settings template
 *
 * @since 3.5.2
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<br>
<div id="ir_additional_settings_container" class="ir-accordion">
	<h2 class="ir-additional-settings-label"><?php esc_html_e( 'Additional Settings', 'wdm_instructor_role' ); ?></h2>
	<div class="ir-panel">
		<h4>
			<?php
				echo esc_html(
					sprintf(
						// translators: course, topic and/or quiz placeholders.
						__( 'Enable or disable the following sections in the %1$s, %2$s and %3$s settings on the Instructor Dashboard', 'wdm_instructor_role' ),
						$course_label,
						$lesson_label,
						$topic_label
					)
				);
				?>
		</h4>
		<table class="form-table ir-additional-settings-form-table">
			<tbody>
				<?php if ( 'yes' == $is_wp_category ) : ?>
					<tr class="ir-setting" scope="row">
						<td>
							<label for="enable_wp_category"><?php esc_html_e( 'WP Categories', 'wdm_instructor_role' ); ?></label>
						</td>
						<td>
							<input type="checkbox" id="enable_wp_category" name="enable_wp_category" <?php echo ( 'off' != $additional_settings['enable_wp_category'] ) ? esc_html( 'checked' ) : ''; ?>/>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ( 'yes' == $is_ld_category ) : ?>
					<tr class="ir-setting" scope="row">
						<td>
							<label for="enable_ld_category"><?php esc_html_e( 'LearnDash Categories', 'wdm_instructor_role' ); ?></label>
						</td>
						<td>
							<input type="checkbox" name="enable_ld_category" id="enable_ld_category" <?php echo ( 'off' != $additional_settings['enable_ld_category'] ) ? esc_html( 'checked' ) : ''; ?>/>
						</td>
					</tr>
				<?php endif; ?>
				<tr class="ir-setting" scope="row">
					<td>
						<label for="enable_permalinks"><?php esc_html_e( 'Permalinks', 'wdm_instructor_role' ); ?></label>
					</td>
					<td>
						<input type="checkbox" name="enable_permalinks" id="enable_permalinks" <?php echo ( 'off' != $additional_settings['enable_permalinks'] ) ? esc_html( 'checked' ) : ''; ?>/>
					</td>
				</tr>
				<?php if ( 'elumine' === $active_theme ) : ?>
					<tr class="ir-setting" scope="row">
						<td>
							<label for="enable_elu_header"><?php esc_html_e( 'Header', 'wdm_instructor_role' ); ?></label>
						</td>
						<td>
							<input type="checkbox" name="enable_elu_header" id="enable_elu_header" <?php echo ( 'off' != @$additional_settings['enable_elu_header'] ) ? esc_html( 'checked' ) : ''; ?>/>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ( 'elumine' === $active_theme ) : ?>
					<tr class="ir-setting" scope="row">
						<td>
							<label for="enable_elu_layout"><?php esc_html_e( 'Layout ', 'wdm_instructor_role' ); ?></label>
						</td>
						<td>
							<input type="checkbox" name="enable_elu_layout" id="enable_elu_layout" <?php echo ( 'off' != @$additional_settings['enable_elu_layout'] ) ? esc_html( 'checked' ) : ''; ?>/>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ( 'elumine' === $active_theme ) : ?>
					<tr class="ir-setting" scope="row">
						<td>
							<label for="enable_elu_cover"><?php esc_html_e( 'Cover Photo', 'wdm_instructor_role' ); ?></label>
						</td>
						<td>
							<input type="checkbox" name="enable_elu_cover" id="enable_elu_cover" <?php echo ( 'off' != @$additional_settings['enable_elu_cover'] ) ? esc_html( 'checked' ) : ''; ?>/>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ( 'buddyboss-theme' === $active_theme ) : ?>
					<tr class="ir-setting" scope="row">
						<td>
							<label for="enable_bb_cover"><?php esc_html_e( 'Cover Photo', 'wdm_instructor_role' ); ?></label>
						</td>
						<td>
							<input type="checkbox" name="enable_bb_cover" id="enable_bb_cover" <?php echo ( 'off' != @$additional_settings['enable_bb_cover'] ) ? esc_html( 'checked' ) : ''; ?>/>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<h4>
		<?php
			echo esc_html(
				sprintf(
					// translators: Course and Group placeholders.
					__( '%1$s and %2$s Pricing Options', 'wdm_instructor_role' ),
					$course_label,
					$group_label
				)
			);
			?>
		</h4>
		<table class="form-table ir-additional-settings-form-table">
			<tbody>
				<tr class="ir-setting" scope="row">
					<td>
						<label for="enable_open_pricing"><?php esc_html_e( 'Open', 'wdm_instructor_role' ); ?></label>
					</td>
					<td>
						<input type="checkbox" name="enable_open_pricing" id="enable_open_pricing" <?php echo ( 'off' != $additional_settings['enable_open_pricing'] ) ? esc_html( 'checked' ) : ''; ?>/>
					</td>
				</tr>
				<tr class="ir-setting" scope="row">
					<td>
						<label for="enable_free_pricing"><?php esc_html_e( 'Free', 'wdm_instructor_role' ); ?></label>
					</td>
					<td>
						<input type="checkbox" name="enable_free_pricing" id="enable_free_pricing" <?php echo ( 'off' != $additional_settings['enable_free_pricing'] ) ? esc_html( 'checked' ) : ''; ?>/>
					</td>
				</tr>
				<tr class="ir-setting" scope="row">
					<td>
						<label for="enable_buy_pricing"><?php esc_html_e( 'Buy Now', 'wdm_instructor_role' ); ?></label>
					</td>
					<td>
						<input type="checkbox" name="enable_buy_pricing" id="enable_buy_pricing" <?php echo ( 'off' != $additional_settings['enable_buy_pricing'] ) ? esc_html( 'checked' ) : ''; ?>/>
					</td>
				</tr>
				<tr class="ir-setting" scope="row">
					<td>
						<label for="enable_recurring_pricing"><?php esc_html_e( 'Recurring', 'wdm_instructor_role' ); ?></label>
					</td>
					<td>
						<input type="checkbox" name="enable_recurring_pricing" id="enable_recurring_pricing" <?php echo ( 'off' != $additional_settings['enable_recurring_pricing'] ) ? esc_html( 'checked' ) : ''; ?>/>
					</td>
				</tr>
				<tr class="ir-setting" scope="row">
					<td>
						<label for="enable_closed_pricing"><?php esc_html_e( 'Closed', 'wdm_instructor_role' ); ?></label>
					</td>
					<td>
						<input type="checkbox" name="enable_closed_pricing" id="enable_closed_pricing" <?php echo ( 'off' != $additional_settings['enable_closed_pricing'] ) ? esc_html( 'checked' ) : ''; ?>/>
					</td>
				</tr>
				<!-- <tr class="ir-setting" scope="row">
					<td>
						<h3> <?php esc_html_e( 'Navigation Settings', 'wdm_instructor_role' ); ?> </h3>
					</td>
				</tr>
				<tr class="ir-setting" scope="row">
					<td>
						<label for="wdm_login_redirect"><?php esc_html_e( 'Instructor Login Redirect', 'wdm_instructor_role' ); ?></label>
						<?php
						$meta_key_tooltip = esc_html__(
							'This setting can be used to redirect the Instructors on your site to any desired page on successful log ins.',
							'wdm_instructor_role'
						);
						?>
						<span class="dashicons dashicons-editor-help" title="<?php echo esc_attr( $meta_key_tooltip ); ?>"></span>
					</td>
					<td>
						<input onclick="show_redirect_pages()" type="checkbox" name="wdm_login_redirect" id="wdm_login_redirect" <?php echo ( 'off' != $additional_settings['wdm_login_redirect'] ) ? esc_html( 'checked' ) : ''; ?>/>
					</td>
				</tr>
				<tr class="ir-setting login-page-row" scope="row">
					<td>
						<label for="wdm_login_redirect"><?php esc_html_e( 'Select Login Redirect Page: ', 'wdm_instructor_role' ); ?></label>
					</td>
					<td>
						<?php
							// dropdown page selector.
							// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Should be checked later.
							$args  = [
								'name'             => 'wdm_login_redirect_page',
								'id'               => 'wdm_login_redirect_page',
								'sort_column'      => 'menu_order',
								'sort_order'       => 'ASC',
								'show_option_none' => __( 'Select a Page', 'wdm_instructor_role' ),
								'class'            => 'wdm_login_redirect_page',
								'echo'             => 0,
								'selected'         => $additional_settings['wdm_login_redirect_page'],
							];
							$pages = wp_dropdown_pages( $args );
							if ( ! empty( $pages ) ) {
								echo $pages;
							}

							?>

					</td>
				</tr>
				<tr class="ir-setting" scope="row">
					<td>
						<label for="wdm_id_ir_dash_pri_menu"><?php esc_html_e( 'Add the Instructor Dashboard Link to the Primary Header Menu for Instructors', 'wdm_instructor_role' ); ?></label>
					</td>
					<td>
						<input type="checkbox" name="wdm_id_ir_dash_pri_menu" id="wdm_id_ir_dash_pri_menu" <?php echo ( 'off' != $additional_settings['wdm_id_ir_dash_pri_menu'] ) ? esc_html( 'checked' ) : ''; ?>/>
					</td>
				</tr> -->
			</tbody>
		</table>
	</div>
</div>
