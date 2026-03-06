<?php
/**
 * Hide show menu template
 *
 * @since 4.3.1
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<div>
	<form method="post" class="ir-show-hide-menu-settings">
		<div class="ir-menu-sorting">
			<div class="ir-menu-settings-container">
				<div class="ir-sidebar-menu-container">
					<ul class="ir-parent-menu" id="ir-parent-menu">
						<?php foreach ( $sidebar_menu as $parent_menu ) : ?>
							<?php if ( is_array( $parent_menu ) && ! array_key_exists( 'delete', $parent_menu ) ) : ?>

								<li class="<?php echo array_key_exists( 'class_name', $parent_menu ) ? $parent_menu['class_name'] : ''; ?> <?php echo array_key_exists( 'action_override', $parent_menu ) ? $parent_menu['action_override'] : ''; ?>" >
									<span class="ir-menu-name"><?php echo esc_html( $parent_menu['title'] ); ?></span>

									<!-- Draggable/Lock Menu icon -->
									<span style="float:left;margin-right:5px;" class="dashicons <?php echo array_key_exists( 'class_name', $parent_menu ) ? 'dashicons-lock' : 'dashicons-move'; ?>">
										<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-grip-vertical" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
									</span>

									<?php
										// get override data.
										$action_menu   = isset( $parent_menu['action_override'] ) ? array_search( 'action_menu', $parent_menu['action_override'] ) : '';
										$edit_override = isset( $parent_menu['action_override'] ) ? array_search( 'edit_action', $parent_menu['action_override'] ) : '';
										$add_override  = isset( $parent_menu['action_override'] ) ? array_search( 'add_action', $parent_menu['action_override'] ) : '';
									?>

									<!-- Action menu icon-->
									<?php if ( ! isset( $parent_menu['class_name'] ) && ! array_key_exists( 'class_name', $parent_menu ) || is_numeric( $action_menu ) ) : ?>
										<span class="dashicons dashicons-ellipsis parent-ellipsis ir-dashicons-spacing"></span>
									<?php endif; ?>
									<!-- Custom Menu delete functionality icon-->
									<?php if ( ! isset( $parent_menu['class_name'] ) && isset( $parent_menu['type'] ) ) : ?>
										<span name="ir_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][delete]" class="dashicons dashicons-trash ir-dashicons-spacing"></span>
									<?php endif; ?>
									<!-- Edit menu icon-->
									<?php if ( ! isset( $parent_menu['class_name'] ) && ! array_key_exists( 'class_name', $parent_menu ) || is_numeric( $edit_override ) ) : ?>
										<?php if ( array_key_exists( 'type', $parent_menu ) || is_numeric( $edit_override ) ) : ?>
											<span class="dashicons dashicons-edit parent-edit ir-dashicons-spacing"></span>
										<?php endif; ?>
									<?php endif; ?>
									<!-- Add action icon -->
									<?php if ( ! isset( $parent_menu['class_name'] ) && ! array_key_exists( 'class_name', $parent_menu ) || isset( $parent_menu['action_add'] ) ) : ?>
										<span class="dashicons dashicons-plus ir-dashicons-spacing"></span>
									<?php endif; ?>

									<?php if ( 'edit.php?post_type=product' == $parent_menu['slug'] && ( ! wdmCheckWooDependency() || ! class_exists( 'WooCommerce' ) ) ) : ?>
										<div style="float:right; margin-left: 8px; margin-right: 8px;" class="tooltip">
											<span style="color:red;float:right;padding-right:5px;" class="dashicons dashicons-hidden"></span>
											<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Learndash woocommerce plugin is inactive.', 'wdm_instructor_role' ); ?></a></span>
										</div>
									<?php elseif ( isset( $parent_menu['hide_restrict'] ) ) : ?>
										<div style="float:right; margin-left: 8px; margin-right: 8px;" class="tooltip">
											<span style="color:red;float:right;padding-right:5px;" class="dashicons dashicons-hidden"></span>
											<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This menu is hidden and restricted with its corresponding sub-menus.', 'wdm_instructor_role' ); ?></a></span>
										</div>
									<?php elseif ( isset( $parent_menu['hide'] ) ) : ?>
										<div style="float:right; margin-left: 8px; margin-right: 8px;" class="tooltip">
											<span style="color:red;float:right;padding-right:5px;" class="dashicons dashicons-hidden"></span>
											<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This menu is hidden with its corresponding sub-menus.', 'wdm_instructor_role' ); ?></a></span>
										</div>
									<?php endif; ?>
									<?php

										// Get Submenu data.
										$sub_menu_default          = isset( $parent_menu['submenu'] ) ? $parent_menu['submenu'] : []; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
										$submenu_sidebar           = isset( $sidebar_sub_menu[ $parent_menu['slug'] ] ) ? $sidebar_sub_menu[ $parent_menu['slug'] ] : $sub_menu_default;
										$submenu_item_count        = isset( $submenu_sidebar ) ? count( $submenu_sidebar ) : (int) '0';
										$submenu_item_delete_count = isset( $submenu_sidebar ) ? count( array_column( $submenu_sidebar, 'delete' ) ) : (int) '0';

									?>
									<?php if ( $submenu_item_count != $submenu_item_delete_count && isset( $sidebar_sub_menu[ $parent_menu['slug'] ] ) || array_key_exists( 'submenu', $parent_menu ) && ! empty( $parent_menu['submenu'] ) ) : ?>
										<span class="dashicons dashicons-arrow-down-alt2 ir-dashicons-spacing"></span>
									<?php endif; ?>

									<!-- Action Menu -->
									<?php if ( ! array_key_exists( 'class_name', $parent_menu ) || is_numeric( $action_menu ) ) : ?>
										<ul class="ir-action-menu">
											<table>
												<tr>
													<td><span><?php esc_html_e( 'Hide', 'wdm_instructor_role' ); ?></span></td>
													<td>
														<div>
															<label class="wdm-switch">
																<input type="checkbox" name="ir_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][hide]" <?php checked( isset( $parent_menu['hide'] ) ? $parent_menu['hide'] : '', 'on' ); ?>>
																<span class="wdm-slider round"></span>
															</label>
														</div>
													</td>
												</tr>
												<tr>
													<td><span><?php esc_html_e( 'Hide & Restrict', 'wdm_instructor_role' ); ?></span></td>
													<td>
														<div>
															<label class="wdm-switch">
																<input type="checkbox" name="ir_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][hide_restrict]" <?php checked( isset( $parent_menu['hide_restrict'] ) ? $parent_menu['hide_restrict'] : '', 'on' ); ?>>
																<span class="wdm-slider round"></span>
															</label>
														</div>
													</td>
												</tr>
											</table>
										</ul>
									<?php endif; ?>

									<!-- Edit Custom Menu -->
									<?php if ( ! array_key_exists( 'class_name', $parent_menu ) || is_numeric( $edit_override ) ) : ?>
										<?php if ( array_key_exists( 'type', $parent_menu ) || is_numeric( $edit_override ) ) : ?>
											<ul style="background:#f4f1f1;" class="ir-action-menu-edit">
												<li>
													<table>
														<tr>
														<span><h4><?php esc_html_e( 'Edit menu', 'wdm_instructor_role' ); ?></h4></span>
															<td><span><?php esc_html_e( 'Title', 'wdm_instructor_role' ); ?></span></td>
															<td>
																<div>
																	<input type="text" class="ir-validate-custom-menu"  name="ir_edit_custom_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][title]" placeholder="Custom menu" value="<?php echo esc_attr( $parent_menu['title'] ); ?>">
																</div>
															</td>
														</tr>
														<tr>
															<td><span><?php esc_html_e( 'URL', 'wdm_instructor_role' ); ?></span></td>
															<td>
																<div>
																	<input type="url" class="ir-validate-custom-menu"  name="ir_edit_custom_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][slug]" placeholder="http://example.com" value="<?php echo esc_attr( $parent_menu['slug'] ); ?>">
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<span>
																	<?php esc_html_e( 'Icon', 'wdm_instructor_role' ); ?>
																</span>
																<div class="tooltip">
																	<span class="dashicons dashicons-editor-help tooltip-trigger"></span>
																	<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'You can add dashicons to this input field.', 'wdm_instructor_role' ); ?></a></span>
																</div>
															</td>
															<td>
																<div>
																	<input type="text" name="ir_edit_custom_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][icon]" placeholder="dashicons-category" value="<?php echo esc_attr( $parent_menu['icon'] ); ?>">
																</div>
															</td>
														</tr>
														<tr>
															<td colspan=2>
															<p>
															<?php
															$link = "<a href='https://developer.wordpress.org/resource/dashicons/' target='_blank'>" . __( 'this link', 'wdm_instructor_role' ) . '</a>';
															printf(
																// translators:Dashicons notice.
																__( 'You can visit %1$s to view more dashicons.', 'wdm_instructor_role' ),
																$link
															);

															?>
															</td>
														</tr>
														<tr>
															<td colspan='2'>
																<span style="display: block ruby;">
																	<span>
																		<?php submit_button( __( 'Update', 'wdm_instructor_role' ), 'primary', 'ir-update-custom-menu', true ); ?>
																	</span>
																</span>
															</td>
														</tr>
													</table>
													<input type="hidden" name="ir_edit_custom_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][old_slug]" value="<?php echo esc_attr( $parent_menu['slug'] ); ?>">
													<input type="hidden" name="ir_edit_custom_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][old_title]" value="<?php echo esc_attr( $parent_menu['title'] ); ?>">
													<input type="hidden" name="ir_edit_custom_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][old_icon]" value="<?php echo esc_attr( $parent_menu['icon'] ); ?>">
												</li>
											</ul>
										<?php endif; ?>
									<?php endif; ?>

									<!-- Add Custom Menu -->
									<?php if ( ! array_key_exists( 'class_name', $parent_menu ) || isset( $parent_menu['action_add'] ) ) : ?>
										<ul class="ir-action-menu-add">
											<li>
												<table>
													<tr>
													<span><h4><?php esc_html_e( 'Add sub-menu', 'wdm_instructor_role' ); ?></h4></span>
														<td><span><?php esc_html_e( 'Sub-menu title', 'wdm_instructor_role' ); ?></span></td>
														<td>
															<div>
																	<input type="text"  class="ir-validate-custom-menu" placeholder="Custom Sub-Menu" name="custom_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][title]" />
															</div>
														</td>
													</tr>
													<tr>
														<td><span><?php esc_html_e( 'URL', 'wdm_instructor_role' ); ?></span></td>
														<td>
															<div>
																	<input type="url"  class="ir-validate-custom-menu" placeholder="http://example.com" name="custom_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][slug]" />
															</div>
														</td>
													</tr>
													<tr style="display:none;" >
														<td><span><?php esc_html_e( 'Icon', 'wdm_instructor_role' ); ?></span></td>
														<td>
															<div>
																	<input type="text"  placeholder="dashicons-category" name="custom_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][icon]" >
															</div>
														</td>
													</tr>
													<tr>
														<td colspan='2'>
															<span style="display: block ruby;">
																<span><?php submit_button( __( 'Save', 'wdm_instructor_role' ), 'primary', 'ir-save-custom-sub-menu', true ); ?></span>
															</span>
														</td>
													</tr>
												</table>
												<input type="hidden" name="custom_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][type]" value="custom" >

											</li>
										</ul>
									<?php endif; ?>

									<input type="hidden" name="ir_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][slug]" value="<?php echo esc_attr( $parent_menu['slug'] ); ?>">
									<input type="hidden" name="ir_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][title]" value="<?php echo esc_attr( $parent_menu['title'] ); ?>">
									<input type="hidden" name="ir_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][icon]" value="<?php echo esc_attr( $parent_menu['icon'] ); ?>">

									<?php if ( isset( $parent_menu['class_name'] ) && esc_attr( $parent_menu['class_name'] ) != '' ) : ?>
										<input type="hidden" name="ir_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][class_name]" value="<?php echo esc_attr( $parent_menu['class_name'] ); ?>">
									<?php endif; ?>
									<?php if ( isset( $parent_menu['action_override'] ) && esc_attr( $parent_menu['action_override'] ) != '' ) : ?>
										<input type="hidden" name="ir_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][action_override]" value="<?php echo esc_attr( $parent_menu['action_override'] ); ?>">
									<?php endif; ?>
									<?php if ( isset( $parent_menu['type'] ) && esc_attr( $parent_menu['type'] ) != '' ) : ?>
										<input type="hidden" name="ir_sidebar_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][type]" value="<?php echo esc_attr( $parent_menu['type'] ); ?>">
									<?php endif; ?>

									<?php if ( $submenu_item_count != $submenu_item_delete_count && isset( $sidebar_sub_menu[ $parent_menu['slug'] ] ) || array_key_exists( 'submenu', $parent_menu ) && ! empty( $parent_menu['submenu'] ) ) : ?>
										<ul class="ir-sub-menu">
											<?php foreach ( $submenu_sidebar as $sub_key => $sub_menu ) : ?>
												<?php
												// get override data.
												$action_sub_menu = isset( $sub_menu['action_menu'] );
												?>

												<?php if ( ! array_key_exists( 'delete', $sub_menu ) ) : ?>
													<li class="<?php echo isset( $sub_menu['class_name'] ) ? esc_attr( $sub_menu['class_name'] ) : ''; ?>">

														<?php
														if ( 'learndash-lms' == $parent_menu['slug'] && ! empty( learndash_get_custom_label( $sub_menu['title'] ) ) ) {
															echo learndash_get_custom_label( $sub_menu['title'] );
														} else {
															?>
															<span><?php echo esc_html( $sub_menu['title'] ); ?></span>
														<?php } ?>

														<!-- Draggable/Lock Sub-Menu icon -->
														<span style="float:left;" class="dashicons <?php echo array_key_exists( 'class_name', $sub_menu ) ? 'dashicons-lock' : 'dashicons-move'; ?>"></span>

														<?php if ( ! isset( $sub_menu['class_name'] ) || $action_sub_menu ) : ?>
															<span class="dashicons dashicons-ellipsis ir-dashicons-spacing"></span>
														<?php endif; ?>
														<!-- Custom Menu delete functionality -->
														<?php if ( isset( $sub_menu['type'] ) ) : ?>
															<span name="ir_sidebar_sub_menu[<?php echo esc_attr( $sub_menu['slug'] ); ?>][delete]" class="dashicons dashicons-trash ir-dashicons-spacing"></span>
														<?php endif; ?>
														<?php if ( array_key_exists( 'type', $sub_menu ) && ! empty( $sub_menu ) ) : ?>
															<span class="dashicons dashicons-edit ir-dashicons-spacing"></span>
														<?php endif; ?>
														<?php if ( isset( $sub_menu['hide_restrict'] ) ) : ?>
															<div style="float:right; margin-left: 8px; margin-right: 8px;" class="tooltip">
																<span style="color:red;float:right;padding-right:5px;" class="dashicons dashicons-hidden"></span>
																<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This sub-menu is hidden and restricted', 'wdm_instructor_role' ); ?></a></span>
															</div>
														<?php elseif ( isset( $sub_menu['hide'] ) ) : ?>
															<div style="float:right; margin-left: 8px; margin-right: 8px;" class="tooltip">
																<span style="color:red;float:right;padding-right:5px;" class="dashicons dashicons-hidden"></span>
																<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'This sub-menu is hidden', 'wdm_instructor_role' ); ?></a></span>
															</div>
														<?php endif; ?>

														<!-- Action Sub-Menu -->
														<?php if ( ! isset( $sub_menu['class_name'] ) || $action_sub_menu ) : ?>
															<ul class="ir-action-submenu">
																<table>
																	<tr>
																		<td><span><?php esc_html_e( 'Hide', 'wdm_instructor_role' ); ?></span></td>
																		<td>
																			<div>
																				<label class="wdm-switch">
																					<input type="checkbox" name="ir_sidebar_sub_menu[<?php echo esc_html( $parent_menu['slug'] ); ?>][<?php echo esc_html( $sub_menu['slug'] ); ?>][hide]" <?php checked( isset( $sub_menu['hide'] ) ? $sub_menu['hide'] : '', 'on' ); ?>>
																					<span class="wdm-slider round"></span>
																				</label>
																			</div>
																		</td>
																	</tr>
																	<tr>
																		<td><span><?php esc_html_e( 'Hide & Restrict', 'wdm_instructor_role' ); ?></span></td>
																		<td>
																			<div>
																				<label class="wdm-switch">
																					<input type="checkbox" name="ir_sidebar_sub_menu[<?php echo esc_html( $parent_menu['slug'] ); ?>][<?php echo esc_html( $sub_menu['slug'] ); ?>][hide_restrict]" <?php checked( isset( $sub_menu['hide_restrict'] ) ? $sub_menu['hide_restrict'] : '', 'on' ); ?>>
																					<span class="wdm-slider round"></span>
																				</label>
																			</div>
																		</td>
																	</tr>
																</table>
															</ul>
														<?php endif; ?>

														<!-- Edit Sub-Menu -->
														<?php if ( array_key_exists( 'type', $sub_menu ) && ! empty( $sub_menu ) ) : ?>
															<ul class="ir-action-submenu-edit">
																<li>
																	<div>
																		<table>
																			<tr>
																				<td>
																					<span>
																						<h4>
																							<?php esc_html_e( 'Edit sub-menu', 'wdm_instructor_role' ); ?>
																						</h4>
																					</span>
																				</td>
																			</tr>
																			<tr>
																				<td>
																					<span>
																						<?php esc_html_e( 'Title', 'wdm_instructor_role' ); ?>
																					</span>
																				</td>
																				<td>
																					<div>
																						<input type="text"  class="ir-validate-custom-menu" placeholder="Custom Sub Menu"  name="ir_edit_custom_sidebar_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][<?php echo esc_attr( $sub_menu['slug'] ); ?>][title]" value="<?php echo esc_attr( $sub_menu['title'] ); ?>">
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td><span><?php esc_html_e( 'URL', 'wdm_instructor_role' ); ?></span></td>
																				<td>
																					<div>
																						<input type="url"  class="ir-validate-custom-menu" placeholder=" http://example.com"  name="ir_edit_custom_sidebar_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][<?php echo esc_attr( $sub_menu['slug'] ); ?>][slug]" value="<?php echo esc_attr( $sub_menu['slug'] ); ?>">
																					</div>
																				</td>
																			</tr>
																			<tr>
																				<td colspan='2' >
																					<?php submit_button( __( 'Update', 'wdm_instructor_role' ), 'primary', 'ir-update-custom-sub-menu', true ); ?>
																				</td>
																			</tr>
																			<tr style="display:none;">
																				<td>
																					<span><?php esc_html_e( 'Icon', 'wdm_instructor_role' ); ?></span>
																					<div class="tooltip">
																						<span class="dashicons dashicons-editor-help tooltip-trigger"></span>
																						<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Visit https://developer.wordpress.org/resource/dashicons/ to view more dashicons and use the dashicons name here', 'wdm_instructor_role' ); ?></a></span>
																					</div>
																				</td>
																				<?php $custom_icon = isset( $sub_menu['icon'] ) ? $sub_menu['icon'] : ''; ?>
																					<td>
																						<div>
																							<input type="text" placeholder=" dashicons-category" name="ir_edit_custom_sidebar_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][<?php echo esc_attr( $sub_menu['slug'] ); ?>][icon]" value="<?php echo esc_attr( $custom_icon ); ?>">
																						</div>
																					</td>

																			</tr>
																		</table>
																	</div>
																</li>
																<input type="hidden" name="ir_edit_custom_sidebar_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][<?php echo esc_attr( $sub_menu['slug'] ); ?>][old_slug]" value="<?php echo esc_attr( $sub_menu['slug'] ); ?>">
																<input type="hidden" name="ir_edit_custom_sidebar_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][<?php echo esc_attr( $sub_menu['slug'] ); ?>][old_title]" value="<?php echo esc_attr( $sub_menu['title'] ); ?>">
																<?php if ( isset( $sub_menu['icon'] ) && ! empty( $sub_menu['icon'] ) ) : ?>
																	<input type="hidden" name="ir_edit_custom_sidebar_sub_menu[<?php echo esc_attr( $parent_menu['slug'] ); ?>][<?php echo esc_attr( $sub_menu['slug'] ); ?>][old_icon]" value="<?php echo esc_attr( $sub_menu['icon'] ); ?>">
																<?php endif; ?>
															</ul>
														<?php endif; ?>

														<input type="hidden" name="ir_sidebar_sub_menu[<?php echo esc_html( $parent_menu['slug'] ); ?>][<?php echo esc_html( $sub_menu['slug'] ); ?>][slug]" value="<?php echo esc_html( $sub_menu['slug'] ); ?>">
														<input type="hidden" name="ir_sidebar_sub_menu[<?php echo esc_html( $parent_menu['slug'] ); ?>][<?php echo esc_html( $sub_menu['slug'] ); ?>][title]" value="<?php echo esc_html( $sub_menu['title'] ); ?>">
														<?php if ( isset( $sub_menu['type'] ) && esc_attr( $sub_menu['type'] ) != '' ) : ?>
															<input type="hidden" name="ir_sidebar_sub_menu[<?php echo esc_html( $parent_menu['slug'] ); ?>][<?php echo esc_html( $sub_menu['slug'] ); ?>][type]" value="<?php echo esc_html( $sub_menu['type'] ); ?>">
														<?php endif; ?>
														<?php if ( isset( $sub_menu['action_menu'] ) && esc_attr( $sub_menu['action_menu'] ) != '' ) : ?>
															<input type="hidden" name="ir_sidebar_sub_menu[<?php echo esc_html( $parent_menu['slug'] ); ?>][<?php echo esc_html( $sub_menu['slug'] ); ?>][action_menu]" value="<?php echo esc_html( $sub_menu['action_menu'] ); ?>">
														<?php endif; ?>
														<?php if ( isset( $sub_menu['class_name'] ) && esc_attr( $sub_menu['class_name'] ) != '' ) : ?>
															<input type="hidden" name="ir_sidebar_sub_menu[<?php echo esc_html( $parent_menu['slug'] ); ?>][<?php echo esc_html( $sub_menu['slug'] ); ?>][class_name]" value="<?php echo esc_html( $sub_menu['class_name'] ); ?>">
														<?php endif; ?>

													</li>
												<?php endif; ?>
											<?php endforeach; ?>
										</ul>
										<!-- Adding new custom submenu items -->
										<div class="ir-sidebar-custom-submenu">
										</div>
									<?php endif; ?>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
					<!-- Adding new custom menu items-->
					<div class="ir-sidebar-custom-menu">
					</div>
				</div>
				<span style="display: block ruby; text-align:<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
				<span><?php submit_button( __( 'Save Settings', 'wdm_instructor_role' ), 'primary', 'ir-save-menu', false ); ?></span>
				<span><?php submit_button( __( 'Reset Settings', 'wdm_instructor_role' ), 'secondary', 'ir-menu-reset-settings', false ); ?></span>
				</span>
			</div>

			<ul class="ir-sub-menu ir-new-custom-menu" >
				<li>
					<div>
						<table>
							<thead>
								<tr>
									<td colspan=2>
										<h2 style="font-size: 16px;text-align: center;"><?php esc_html_e( 'Add new custom menu', 'wdm_instructor_role' ); ?></h2>
									</td>
								<tr>
							</thead>
							<tr>
								<td><?php esc_html_e( 'Menu title', 'wdm_instructor_role' ); ?></td>
								<td><input class="ir-validate-custom-menu" placeholder="Custom Menu" type="text" name=custom_menu[title] /></td>
							</tr>
							<tr>
								<td><?php esc_html_e( 'URL', 'wdm_instructor_role' ); ?></td>
								<td><input class="ir-validate-custom-menu" placeholder="http://example.com" type="url" name=custom_menu[slug] /></td>
							</tr>
							<tr>
								<td>
									<?php esc_html_e( 'Icon', 'wdm_instructor_role' ); ?>
									<div class="tooltip">
										<span class="dashicons dashicons-editor-help tooltip-trigger"></span>
										<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'You can add dashicons to this input field.', 'wdm_instructor_role' ); ?></a></span>
									</div>
								</td>
								<td>
									<input placeholder="dashicons-category" type="text" name=custom_menu[icon] />
								</td>
							</tr>
							<tr>
								<td colspan=2>
								<p>
								<?php
								$link = "<a href='https://developer.wordpress.org/resource/dashicons/' target='_blank'>" . __( 'this link', 'wdm_instructor_role' ) . '</a>';
								printf(
									// translators:Dashicons notice.
									__( 'You can visit %1$s to view more dashicons.', 'wdm_instructor_role' ),
									$link
								);

								?>
								</td>
							</tr>
							<input type="hidden"name=custom_menu[type] value="custom" />
							<tr>
								<td colspan=2>
									<span style="display: block ruby;">
										<span>
											<?php submit_button( __( 'Save', 'wdm_instructor_role' ), 'primary', 'ir-save-custom-menu', true ); ?>
										</span>
									</span>
								</td>
							</tr>
						</table>
					</div>
				</li>
			</ul>
		</div>
	</form>
</div>
