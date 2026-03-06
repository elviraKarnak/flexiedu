<?php
/**
 * Instructor Dashboard Settings Page Template
 *
 * @since 4.0
 *
 * @var string   $ir_color_preset
 * @var string   $ir_color_preset_2
 * @var string   $ir_primary_color_1
 * @var string   $ir_primary_color_2
 * @var string   $ir_primary_color_3
 * @var string   $ir_primary_color_4
 * @var string   $ir_primary_color_5
 * @var string   $ir_accent_primary_color
 * @var string   $ir_layout_2_primary_color
 * @var string   $ir_layout_2_secondary_color
 * @var string   $ir_layout_2_tertiary_color
 * @var string   $ir_layout_2_accent_color
 * @var string   $ir_layout_2_text_color_1
 * @var string   $ir_layout_2_text_color_2
 * @var string   $ir_layout_2_background_color
 * @var string   $ir_dashboard_logo
 * @var string   $ir_dashboard_logo_2
 * @var string   $ir_dashboard_header
 * @var string   $ir_dashboard_image_background_color
 * @var string   $ir_dashboard_image_background_color_2
 * @var string   $ir_dashboard_title_label
 * @var string   $ir_dashboard_font_family
 * @var string   $ir_lms_label
 * @var int      $ir_dashboard_body_font_size
 * @var int      $ir_dashboard_header_font_size
 * @var int      $ir_dashboard_sidebar_font_size
 * @var string   $ir_dashboard_text_title
 * @var string   $ir_dashboard_text_background_color
 * @var string   $ir_dashboard_sub_title_font_color
 * @var string   $ir_dashboard_title_font_color
 * @var string   $ir_dashboard_text_sub_title
 * @var array    $google_fonts
 * @var array    $default_fonts
 *
 * @package LearnDash\Instructor_Role
 *
 * cspell:ignore pallete // ignoring misspelled words that we can't change now.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div>
	<div class="ir-inline-flex align-center ir-primary-color-setting ir-back-settings ir-hide-appearance">
		<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-narrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l4 4" /><path d="M5 12l4 -4" /></svg>
		<span class="">Back</span>
	</div>
	<h1><?php esc_html_e( 'Appearance', 'wdm_instructor_role' ); ?></h1>
	<form class="ir-dashboard-settings-form" method="post">
		<table>
			<tbody>
				<tr>
					<th colspan="100%">
						<h2 class="no-border">
							<?php esc_html_e( 'General', 'wdm_instructor_role' ); ?>
						</h2>
					</th>
				</tr>
				<tr class="ir-title">
					<th style="width: 150px;">
						<?php esc_html_e( 'LMS Label', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input type="text" name="ir_lms_label" id="ir_lms_label" placeholder="<?php esc_html_e( 'LearnDash LMS', 'wdm_instructor_role' ); ?>" value="<?php echo esc_html( $ir_lms_label ); ?>"/>
						<p>
							<span class="dashicons dashicons-info"></span>
							<em><?php esc_html_e( 'This will change the default LearnDash LMS Label.', 'wdm_instructor_role' ); ?></em>
						</p>
					</td>
				</tr>
				<tr>
					<th colspan="100%">
						<h2 class="no-border">
							<?php esc_html_e( 'Layout', 'wdm_instructor_role' ); ?>
						</h2>
					</th>
				</tr>
				<tr class="ir-title">
					<th>
						<?php esc_html_e( 'Select Layout', 'wdm_instructor_role' ); ?>
					</th>
					<td>

						<input id="ir_dashboard_layout-new" type="radio" name="ir_dashboard_layout" class="ir_dashboard_layout" value="layout-2" <?php checked( 'layout-2', $ir_dashboard_layout ); ?>>
						<label for="ir_dashboard_layout-new" class="ir-radio">
							<?php esc_html_e( 'New', 'wdm_instructor_role' ); ?>
						</label>

						<input id="ir_dashboard_layout-old" type="radio" name="ir_dashboard_layout" class="ir_dashboard_layout" value="layout-1" <?php checked( 'layout-1', $ir_dashboard_layout ); ?>>
						<label for="ir_dashboard_layout-old" class="ir-radio">
							<?php esc_html_e( 'Old', 'wdm_instructor_role' ); ?>
						</label>
						<p>
						</p>
					</td>
				</tr>
				<tr class="ir-title ir-logo-settings ir-dashboard-text-field ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th><?php esc_html_e( 'Dashboard Title', 'wdm_instructor_role' ); ?></th>
					<td>
						<input type="text" name="ir_dashboard_title_label" id="ir_dashboard_title_label" value="<?php echo esc_attr( $ir_dashboard_title_label ); ?>" placeholder="<?php esc_html_e( 'Instructor Dashboard', 'wdm_instructor_role' ); ?>"/>
					</td>
				</tr>
				<tr class="ir-title ir-bold">
					<th	style="vertical-align: top;" colspan="100%">
						<h2 class="no-border"><?php esc_html_e( 'Color Schemes', 'wdm_instructor_role' ); ?></h2>
					</th>
				</tr>
				<tr class="ir-title ir-preset-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Presets', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<div class="ir-color-scheme">
							<div class="preset <?php echo ( 'default' === $ir_color_preset ) ? 'preset-selected' : ''; ?>">
								<input type="radio" name="ir_color_preset" value="default" class="preset-radio" <?php checked( $ir_color_preset, 'default' ); ?>/>
								<span class="pallete-color" style="background-color: #d0d0d1;"></span>
								<span class="pallete-color" style="background-color: #ffffff;"></span>
								<span class="pallete-color" style="background-color: #ebebec;"></span>
								<span class="preset-name"><?php esc_html_e( 'Default', 'wdm_instructor_role' ); ?></span>
							</div>
							<div class="preset <?php echo ( 'preset_1' === $ir_color_preset ) ? 'preset-selected' : ''; ?>">
								<input type="radio" name="ir_color_preset" value="preset_1" class="preset-radio" <?php checked( $ir_color_preset, 'preset_1' ); ?>/>
								<span class="pallete-color" style="background-color: #272847;"></span>
								<span class="pallete-color" style="background-color: #1a1b35;"></span>
								<span class="pallete-color" style="background-color: #363868;"></span>
								<span class="preset-name"><?php esc_html_e( 'Preset 1', 'wdm_instructor_role' ); ?></span>
							</div>
							<div class="preset <?php echo ( 'preset_2' === $ir_color_preset ) ? 'preset-selected' : ''; ?>">
								<input type="radio" name="ir_color_preset" value="preset_2" class="preset-radio" <?php checked( $ir_color_preset, 'preset_2' ); ?>/>
								<span class="pallete-color" style="background-color: #1b4332;"></span>
								<span class="pallete-color" style="background-color: #2d6a4f;"></span>
								<span class="pallete-color" style="background-color: #40916c;"></span>
								<span class="preset-name"><?php esc_html_e( 'Preset 2', 'wdm_instructor_role' ); ?></span>
							</div>
							<div class="preset <?php echo ( 'preset_3' === $ir_color_preset ) ? 'preset-selected' : ''; ?>">
								<input type="radio" name="ir_color_preset" value="preset_3" class="preset-radio" <?php checked( $ir_color_preset, 'preset_3' ); ?>/>
								<span class="pallete-color" style="background-color: #caf0f8;"></span>
								<span class="pallete-color" style="background-color: #ade8f4;"></span>
								<span class="pallete-color" style="background-color: #90e0ef;"></span>
								<span class="preset-name"><?php esc_html_e( 'Preset 3', 'wdm_instructor_role' ); ?></span>
							</div>
						</div>
					</td>
				</tr>
				<tr class="ir-title ir-preset-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th></th>
					<td>
						<div class="ir-color-scheme">
							<div class="preset <?php echo ( 'custom' === $ir_color_preset ) ? 'preset-selected' : ''; ?>">
								<input type="radio" name="ir_color_preset" value="custom" class="preset-radio" <?php checked( $ir_color_preset, 'custom' ); ?> id="ir_custom_preset"/>
								<span class="pallete-color" style="background-color: #ef476f;"></span>
								<span class="pallete-color" style="background-color: #ffd166;"></span>
								<span class="pallete-color" style="background-color: #073b4c;"></span>
								<span class="preset-name"><?php esc_html_e( 'Custom', 'wdm_instructor_role' ); ?></span>
							</div>
						</div>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-1 <?php echo ( 'custom' !== $ir_color_preset || 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Primary Color', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_primary_color_1" id="ir_primary_color_1" value="<?php echo esc_attr( $ir_primary_color_1 ); ?>" data-default-color="#272847"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-1 <?php echo ( 'custom' !== $ir_color_preset || 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Secondary Color', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_primary_color_2" id="ir_primary_color_2" value="<?php echo esc_attr( $ir_primary_color_2 ); ?>" data-default-color="#1a1b35"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-1 <?php echo ( 'custom' !== $ir_color_preset || 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Accent Color', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_accent_primary_color" id="ir_accent_primary_color" value="<?php echo esc_attr( $ir_accent_primary_color ); ?>" data-default-color="#4553e6"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-1 <?php echo ( 'custom' !== $ir_color_preset || 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Active Menu', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_primary_color_3" id="ir_primary_color_3" value="<?php echo esc_attr( $ir_primary_color_3 ); ?>" data-default-color="#363868"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-1 <?php echo ( 'custom' !== $ir_color_preset || 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Menu Text Color', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_primary_color_4" id="ir_primary_color_4" value="<?php echo esc_attr( $ir_primary_color_4 ); ?>" data-default-color="#fff"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-1 <?php echo ( 'custom' !== $ir_color_preset || 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Menu Hover Color', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_primary_color_5" id="ir_primary_color_5" value="<?php echo esc_attr( $ir_primary_color_5 ); ?>" data-default-color="#333"/>
					</td>
				</tr>

				<tr class="ir-title ir-preset-settings ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Presets', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<div class="ir-color-scheme">
							<div class="preset <?php echo ( 'default' === $ir_color_preset_2 ) ? 'preset-selected' : ''; ?>" data-font="<?php echo esc_attr( $recommended_fonts['default'] ); ?>">
								<div>
									<input type="radio" name="ir_color_preset_2" value="default" class="preset-radio" <?php checked( $ir_color_preset_2, 'default' ); ?>/>
									<span class="pallete-color" style="background-color: #021768;"></span>
									<span class="pallete-color" style="background-color: #E7F1FF;"></span>
									<span class="pallete-color" style="background-color: #F26440;"></span>
								</div>
								<div>
									<span class="preset-name"><?php esc_html_e( 'Calm Ocean', 'wdm_instructor_role' ); ?></span>
								</div>
							</div>
							<div class="preset <?php echo ( 'preset_1' === $ir_color_preset_2 ) ? 'preset-selected' : ''; ?>" data-font="<?php echo esc_attr( $recommended_fonts['preset_1'] ); ?>">
								<div>
									<input type="radio" name="ir_color_preset_2" value="preset_1" class="preset-radio" <?php checked( $ir_color_preset_2, 'preset_1' ); ?>/>
									<span class="pallete-color" style="background-color: #0051F9;"></span>
									<span class="pallete-color" style="background-color: #0F1D3A;"></span>
									<span class="pallete-color" style="background-color: #03102C;"></span>
								</div>
								<div>
									<span class="preset-name"><?php esc_html_e( 'Serious Blue', 'wdm_instructor_role' ); ?></span>
								</div>
							</div>
							<div class="preset <?php echo ( 'preset_2' === $ir_color_preset_2 ) ? 'preset-selected' : ''; ?>" data-font="<?php echo esc_attr( $recommended_fonts['preset_2'] ); ?>">
								<div>
									<input type="radio" name="ir_color_preset_2" value="preset_2" class="preset-radio" <?php checked( $ir_color_preset_2, 'preset_2' ); ?>/>
									<span class="pallete-color" style="background-color: #FD9C0F;"></span>
									<span class="pallete-color" style="background-color: #2B1E43;"></span>
									<span class="pallete-color" style="background-color: #201239;"></span>
								</div>
								<div>
									<span class="preset-name"><?php esc_html_e( 'Friendly Mustard', 'wdm_instructor_role' ); ?></span>
								</div>
							</div>
							<div class="preset <?php echo ( 'preset_3' === $ir_color_preset_2 ) ? 'preset-selected' : ''; ?>" data-font="<?php echo esc_attr( $recommended_fonts['preset_3'] ); ?>">
								<div>
									<input type="radio" name="ir_color_preset_2" value="preset_3" class="preset-radio" <?php checked( $ir_color_preset_2, 'preset_3' ); ?>/>
									<span class="pallete-color" style="background-color: #E339D8;"></span>
									<span class="pallete-color" style="background-color: #FFDFEE;"></span>
									<span class="pallete-color" style="background-color: #FFFFFF;"></span>
								</div>
								<div>
									<span class="preset-name"><?php esc_html_e( 'Wise Pink', 'wdm_instructor_role' ); ?></span>
								</div>
							</div>
							<div class="preset <?php echo ( 'preset_4' === $ir_color_preset_2 ) ? 'preset-selected' : ''; ?>" data-font="<?php echo esc_attr( $recommended_fonts['preset_4'] ); ?>">
								<div>
									<input type="radio" name="ir_color_preset_2" value="preset_4" class="preset-radio" <?php checked( $ir_color_preset_2, 'preset_4' ); ?>/>
									<span class="pallete-color" style="background-color: #F45E55;"></span>
									<span class="pallete-color" style="background-color: #F4F2EB;"></span>
									<span class="pallete-color" style="background-color: #C89D2A;"></span>
								</div>
								<div>
									<span class="preset-name"><?php esc_html_e( 'Sweet Rose', 'wdm_instructor_role' ); ?></span>
								</div>
							</div>
							<div class="preset <?php echo ( 'custom' === $ir_color_preset_2 ) ? 'preset-selected' : ''; ?>">
								<div>
									<input type="radio" name="ir_color_preset_2" value="custom" class="preset-radio" <?php checked( $ir_color_preset_2, 'custom' ); ?>/>
									<span class="pallete-color" style="background-color: #ef476f;"></span>
									<span class="pallete-color" style="background-color: #ffd166;"></span>
									<span class="pallete-color" style="background-color: #073b4c;"></span>
								</div>
								<div>
									<span class="preset-name"><?php esc_html_e( 'Custom', 'wdm_instructor_role' ); ?></span>
								</div>
							</div>
						</div>
					</td>
				</tr>
				<!-- <tr class="ir-preset-settings ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th></th>
					<td>
						<div class="ir-color-scheme">
						</div>
					</td>
				</tr> -->
				<tr class="ir-preset-advanced ir-layout-2 <?php echo ( 'custom' !== $ir_color_preset_2 || 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Primary Color', 'wdm_instructor_role' ); ?>
						<div class="tooltip">
							<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
							<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Summary card icons, Graph, Table title background', 'wdm_instructor_role' ); ?></span>
						</div>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_layout_2_primary_color" id="ir_layout_2_primary_color" value="<?php echo esc_attr( $ir_layout_2_primary_color ); ?>" data-default-color="#272847"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-2 <?php echo ( 'custom' !== $ir_color_preset_2 || 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Secondary Color', 'wdm_instructor_role' ); ?>
						<div class="tooltip">
							<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
							<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Sidebar expand background', 'wdm_instructor_role' ); ?></span>
						</div>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_layout_2_secondary_color" id="ir_layout_2_secondary_color" value="<?php echo esc_attr( $ir_layout_2_secondary_color ); ?>" data-default-color="#1a1b35"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-2 <?php echo ( 'custom' !== $ir_color_preset_2 || 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Tertiary Color', 'wdm_instructor_role' ); ?>
						<div class="tooltip">
							<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
							<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Sidebar background, Sidebar active menu background', 'wdm_instructor_role' ); ?></span>
						</div>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_layout_2_tertiary_color" id="ir_layout_2_tertiary_color" value="<?php echo esc_attr( $ir_layout_2_tertiary_color ); ?>" data-default-color="#4553e6"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-2 <?php echo ( 'custom' !== $ir_color_preset_2 || 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Accent Color', 'wdm_instructor_role' ); ?>
						<div class="tooltip">
							<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
							<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Pagination,Sidebar main menu hover color', 'wdm_instructor_role' ); ?></span>
						</div>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_layout_2_accent_color" id="ir_layout_2_accent_color" value="<?php echo esc_attr( $ir_layout_2_accent_color ); ?>" data-default-color="#363868"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-2 <?php echo ( 'custom' !== $ir_color_preset_2 || 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Text Color 1', 'wdm_instructor_role' ); ?>
						<div class="tooltip">
							<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
							<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Summary card text, Graph title, Table title', 'wdm_instructor_role' ); ?></span>
						</div>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_layout_2_text_color_1" id="ir_layout_2_text_color_1" value="<?php echo esc_attr( $ir_layout_2_text_color_1 ); ?>" data-default-color="#fff"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-2 <?php echo ( 'custom' !== $ir_color_preset_2 || 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Text Color 2', 'wdm_instructor_role' ); ?>
						<div class="tooltip">
							<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
							<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Sidebar menu text color', 'wdm_instructor_role' ); ?></span>
						</div>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_layout_2_text_color_2" id="ir_layout_2_text_color_2" value="<?php echo esc_attr( $ir_layout_2_text_color_2 ); ?>" data-default-color="#333"/>
					</td>
				</tr>
				<tr class="ir-preset-advanced ir-layout-2 <?php echo ( 'custom' !== $ir_color_preset_2 || 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Background Color', 'wdm_instructor_role' ); ?>
						<div class="tooltip">
							<span class="dashicons dashicons-editor-help tooltip-trigger" ></span>
							<span class="tooltip-drop tooltip-top"><?php esc_html_e( 'Body Background', 'wdm_instructor_role' ); ?></span>
						</div>
					</th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_layout_2_background_color" id="ir_layout_2_background_color" value="<?php echo esc_attr( $ir_layout_2_background_color ); ?>" data-default-color="#333"/>
					</td>
				</tr>
				<tr class="ir-title ir-bold ir-logo-settings ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th colspan="100%">
						<h2 class="no-border"><?php esc_html_e( 'Font', 'wdm_instructor_role' ); ?></h2>
					</th>
				</tr>
				<tr class="ir-title ir-logo-settings ir-dashboard-text-field ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th><?php esc_html_e( 'Font Family', 'wdm_instructor_role' ); ?></th>
					<td>
						<select name="ir_dashboard_font_family" id="ir_dashboard_font_family">
							<option value="-1" disabled style="font-weight: 600;text-align:center;font-size:16px;color:#000;"><?php esc_html_e( '-- System Fonts --', 'wdm_instructor_role' ); ?></option>
							<option value="arial, sans-serif" <?php selected( $ir_dashboard_font_family, '' ); ?>><?php esc_html_e( 'Arial', 'wdm_instructor_role' ); ?></option>
							<?php foreach ( $default_fonts as $key => $value ) : ?>
								<option value='<?php echo esc_attr( $value ); ?>' <?php selected( $ir_dashboard_font_family, $value ); ?>><?php echo esc_attr( $key ); ?></option>
							<?php endforeach; ?>
							<option value="-1" disabled style="font-weight: 600;text-align:center;font-size:16px;color:#000;"><?php esc_html_e( '-- Google Fonts --', 'wdm_instructor_role' ); ?></option>
							<?php foreach ( $google_fonts as $key => $value ) : ?>
								<option value='<?php echo esc_attr( $value ); ?>' <?php selected( $ir_dashboard_font_family, $value ); ?>><?php echo esc_attr( $value ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php if ( 'layout-2' === $ir_dashboard_layout && ! empty( $ir_color_preset_2 ) ) : ?>
							<p class="ir-help-text">
								<?php esc_html_e( 'Recommended Font:', 'wdm_instructor_role' ); ?>
								<span data-preset-font="<?php echo ( 'custom' !== $ir_color_preset_2 ) ? esc_attr( $recommended_fonts[ $ir_color_preset_2 ] ) : esc_attr__( 'None', 'wdm_instructor_role' ); ?>"><?php echo ( 'custom' !== $ir_color_preset_2 ) ? esc_attr( $recommended_fonts[ $ir_color_preset_2 ] ) : esc_attr__( 'None', 'wdm_instructor_role' ); ?></span>
							</p>
						<?php endif; ?>
					</td>
				</tr>
				<tr class="ir-title ir-logo-settings ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th>
						<?php esc_html_e( 'Font Size', 'wdm_instructor_role' ); ?>
					</th>
					<td>
						<select id="ir_dashboard_font_size" name="ir_dashboard_font_size">
							<option value="normal" <?php selected( 'normal', $ir_dashboard_font_size ); ?>><?php esc_html_e( 'Normal', 'wdm_instructor_role' ); ?></option>
							<option value="large" <?php selected( 'large', $ir_dashboard_font_size ); ?>><?php esc_html_e( 'Large', 'wdm_instructor_role' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="ir-title ir-bold ir-logo-settings ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th colspan="100%">
						<h2 class="no-border"><?php esc_html_e( 'Logo', 'wdm_instructor_role' ); ?></h2>
					</th>
				</tr>
				<tr class="ir-title ir-logo-settings ir-dashboard-image-field ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th><?php esc_html_e( 'Image Upload', 'wdm_instructor_role' ); ?></th>
					<td>
						<div id="ir-dashboard-image-container-layout-2">
							<?php if ( $dashboard_logo_2 = wp_get_attachment_image_src( $ir_dashboard_logo_2, 'full' ) ) : ?>
								<button class="ir_upload_image button">
									<img
										class="ir-dashboard-logo-img"
										src="<?php echo esc_url( $dashboard_logo_2[0] ); ?>"
										alt="<?php esc_html_e( 'Instructor Dashboard Logo 2', 'wdm_instructor_role' ); ?>"
									/>
								</button>
								<button class="ir_remove_image button">
									<?php esc_html_e( 'Remove', 'wdm_instructor_role' ); ?>
								</button>
							<?php else : ?>
								<button class="ir_upload_image button">
									<?php esc_html_e( 'Upload', 'wdm_instructor_role' ); ?>
								</button>
								<button class="ir_remove_image button ir-hide">
									<?php esc_html_e( 'Remove', 'wdm_instructor_role' ); ?>
								</button>
							<?php endif; ?>
							<input type="hidden" name="ir_dashboard_logo_2" id="ir_dashboard_logo_2" value="<?php echo esc_attr( $ir_dashboard_logo_2 ); ?>"/>
							<div class="ir-example-logo">
								<img src="<?php echo esc_attr( $ir_example_logo_2 ); ?>" alt="<?php esc_html_e( 'Default Logo', 'wdm_instructor_role' ); ?>">
								<span><?php esc_html_e( 'Example Logo', 'wdm_instructor_role' ); ?></span>
							</div>
						</div>
						<span class="dashicons dashicons-info"></span>
						<em><?php esc_html_e( 'We recommended a image of size 242x55px', 'wdm_instructor_role' ); ?></em>
					</td>
				</tr>
				<tr class="ir-title ir-logo-settings ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th><?php esc_html_e( 'Alignment', 'wdm_instructor_role' ); ?></th>
					<td>
						<select id="ir_logo_alignment_2" name="ir_logo_alignment_2">
							<option value="center" <?php selected( 'center', $ir_logo_alignment_2 ); ?>><?php esc_html_e( 'Center', 'wdm_instructor_role' ); ?></option>
							<option value="left" <?php selected( 'left', $ir_logo_alignment_2 ); ?>><?php esc_html_e( 'Left', 'wdm_instructor_role' ); ?></option>
							<option value="right" <?php selected( 'right', $ir_logo_alignment_2 ); ?>><?php esc_html_e( 'Right', 'wdm_instructor_role' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="ir-title ir-logo-settings ir-layout-2 <?php echo ( 'layout-2' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th><?php esc_html_e( 'Background Color', 'wdm_instructor_role' ); ?></th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_dashboard_image_background_color_2" id="ir_dashboard_image_background_color_2" value="<?php echo esc_attr( $ir_dashboard_image_background_color_2 ); ?>"/>
					</td>
				</tr>

				<!-- Old Settings -->
				<tr class="ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th colspan="100%">
						<h2><?php esc_html_e( 'Header Settings', 'wdm_instructor_role' ); ?></h2>
					</th>
				</tr>
				<tr class="ir-title ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>">
					<th><?php esc_html_e( 'Type', 'wdm_instructor_role' ); ?></th>
					<td>
						<select name="ir_dashboard_header" id="ir_dashboard_header">
							<option value="-1" <?php selected( '-1', $ir_dashboard_header ); ?>><?php esc_html_e( 'None', 'wdm_instructor_role' ); ?></option>
							<option value="image" <?php selected( 'image', $ir_dashboard_header ); ?>><?php esc_html_e( 'Image', 'wdm_instructor_role' ); ?></option>
							<option value="text" <?php selected( 'text', $ir_dashboard_header ); ?>><?php esc_html_e( 'Text', 'wdm_instructor_role' ); ?></option>
						</select>
					</td>
				</tr>
				<tr class="ir-logo-settings ir-layout-1 ir-dashboard-image-field <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>" <?php echo ( 'image' !== $ir_dashboard_header ) ? "style='display: none;'" : ''; ?> >
					<th><?php esc_html_e( 'Image Upload', 'wdm_instructor_role' ); ?></th>
					<td>
						<div id="ir-dashboard-image-container">
							<?php if ( $dashboard_logo = wp_get_attachment_image_src( $ir_dashboard_logo ) ) : ?>
								<button class="ir_upload_image button">
									<img
										class="ir-dashboard-logo-img"
										src="<?php echo esc_url( $dashboard_logo[0] ); ?>"
										alt="<?php esc_html_e( 'Instructor Dashboard Logo', 'wdm_instructor_role' ); ?>"
									/>
								</button>
								<button class="ir_remove_image button">
									<?php esc_html_e( 'Remove', 'wdm_instructor_role' ); ?>
								</button>
							<?php else : ?>
								<button class="ir_upload_image button">
									<?php esc_html_e( 'Upload', 'wdm_instructor_role' ); ?>
								</button>
								<button class="ir_remove_image button ir-hide">
									<?php esc_html_e( 'Remove', 'wdm_instructor_role' ); ?>
								</button>
							<?php endif; ?>
							<input type="hidden" name="ir_dashboard_logo" id="ir_dashboard_logo" value="<?php echo esc_attr( $ir_dashboard_logo ); ?>"/>
						</div>
						<span class="dashicons dashicons-info"></span>
						<em><?php esc_html_e( 'We recommended a square image of size 110px', 'wdm_instructor_role' ); ?></em>
					</td>
				</tr>
				<tr class="ir-dashboard-image-field ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>" <?php echo ( 'image' !== $ir_dashboard_header ) ? "style='display: none;'" : ''; ?> >
					<th><?php esc_html_e( 'Background Color', 'wdm_instructor_role' ); ?></th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_dashboard_image_background_color" id="ir_dashboard_image_background_color" value="<?php echo esc_attr( $ir_dashboard_image_background_color ); ?>"/>
					</td>
				</tr>
				<tr class="ir-dashboard-text-field ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>" <?php echo ( 'text' !== $ir_dashboard_header ) ? "style='display: none;'" : ''; ?> >
					<th><?php esc_html_e( 'Title', 'wdm_instructor_role' ); ?></th>
					<td>
						<input type="text" name="ir_dashboard_text_title" id="ir_dashboard_text_title" value="<?php echo esc_attr( $ir_dashboard_text_title ); ?>" />
					</td>
				</tr>
				<tr class="ir-dashboard-text-field ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>" <?php echo ( 'text' !== $ir_dashboard_header ) ? "style='display: none;'" : ''; ?> >
					<th><?php esc_html_e( 'Sub Title', 'wdm_instructor_role' ); ?></th>
					<td>
						<input type="text" name="ir_dashboard_text_sub_title" id="ir_dashboard_text_sub_title" value="<?php echo esc_attr( $ir_dashboard_text_sub_title ); ?>"/>
					</td>
				</tr>
				<tr class="ir-dashboard-text-field ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>" <?php echo ( 'text' !== $ir_dashboard_header ) ? "style='display: none;'" : ''; ?> >
					<th><h3><?php esc_html_e( 'Title Font Settings', 'wdm_instructor_role' ); ?></h3></th>
				</tr>
				<tr class="ir-dashboard-text-field ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>" <?php echo ( 'text' !== $ir_dashboard_header ) ? "style='display: none;'" : ''; ?> >
					<th><?php esc_html_e( 'Font Color', 'wdm_instructor_role' ); ?></th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_dashboard_title_font_color" id="ir_dashboard_title_font_color" value="<?php echo esc_attr( $ir_dashboard_title_font_color ); ?>"/>
					</td>
				</tr>
				<tr class="ir-dashboard-text-field ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>" <?php echo ( 'text' !== $ir_dashboard_header ) ? "style='display: none;'" : ''; ?> >
					<th><h3><?php esc_html_e( 'Sub Title Font Settings', 'wdm_instructor_role' ); ?></h3></th>
				</tr>
				<tr class="ir-dashboard-text-field ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>" <?php echo ( 'text' !== $ir_dashboard_header ) ? "style='display: none;'" : ''; ?> >
					<th><?php esc_html_e( 'Font Color', 'wdm_instructor_role' ); ?></th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_dashboard_sub_title_font_color" id="ir_dashboard_sub_title_font_color" value="<?php echo esc_attr( $ir_dashboard_sub_title_font_color ); ?>"/>
					</td>
				</tr>
				<tr class="ir-dashboard-text-field ir-logo-settings ir-layout-1 <?php echo ( 'layout-1' !== $ir_dashboard_layout ) ? 'ir-hide' : ''; ?>" <?php echo ( 'text' !== $ir_dashboard_header ) ? "style='display: none;'" : ''; ?> >
					<th><?php esc_html_e( 'Background Color', 'wdm_instructor_role' ); ?></th>
					<td>
						<input class="ir-color-picker" type="text" name="ir_dashboard_text_background_color" id="ir_dashboard_text_background_color" value="<?php echo esc_attr( $ir_dashboard_text_background_color ); ?>" data-default-color="#00ACD3" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php wp_nonce_field( 'ir_dashboard_settings', 'ir_dashboard_nonce' ); ?>
		<?php submit_button( __( 'Save Settings', 'wdm_instructor_role' ) ); ?>
	</form>
</div>
