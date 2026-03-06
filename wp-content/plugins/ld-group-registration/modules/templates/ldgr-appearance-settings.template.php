<?php
/**
 * Appearance Settings Template.
 *
 * @since 4.2.2
 * @version 4.3.15
 *
 * @param string $ldgr_dashboard_banner_color Dashboard banner color value.
 * @param string $ldgr_dashboard_accent_color Dashboard accent color value.
 * @param string $ldgr_default_group_image    Default group image value.
 * @param string $ldgr_default_course_image   Default course image value.
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>
<h2><?php esc_html_e( 'Appearance Settings', 'wdm_ld_group' ); ?></h2>

<div class="ldgr-settings">
	<form method="post" id="ldgr-appearance-settings-form">

		<div class="ldgr-dashboard-banner-color">
			<label for="ldgr_dashboard_banner_color">
				<?php esc_html_e( 'Banner Color', 'wdm_ld_group' ); ?>
			</label>
			<input type="text" name="ldgr_dashboard_banner_color" id="ldgr_dashboard_banner_color" value="<?php echo esc_attr( $ldgr_dashboard_banner_color ); ?>" data-default-color="#F6FBFF" />
		</div>

		<div class="ldgr-dashboard-accent-color">
			<label for="ldgr_dashboard_accent_color">
				<?php esc_html_e( 'Accent Color', 'wdm_ld_group' ); ?>
			</label>
			<input type="text" name="ldgr_dashboard_accent_color" id="ldgr_dashboard_accent_color" value="<?php echo esc_attr( $ldgr_dashboard_accent_color ); ?>" data-default-color="#0D7EE7" />
		</div>

	<div class="ldgr-dashboard-footer-color">
			<label for="ldgr_dashboard_footer_color">
				<?php esc_html_e( 'Footer Text Color', 'wdm_ld_group' ); ?>
			</label>
			<input type="text" name="ldgr_dashboard_footer_color" id="ldgr_dashboard_footer_color" value="<?php echo esc_attr( $ldgr_dashboard_footer_color ); ?>" data-default-color="#ee9823" />
		</div>

		<div class="ldgr-default-group-image">
			<label for="ldgr_default_group_image">
				<?php
				printf(
					// translators: Group.
					esc_html__( 'Default %s image', 'wdm_ld_group' ),
					$group_label
				);
				?>
			</label>
			<?php if ( $group_image = wp_get_attachment_image_src( $ldgr_default_group_image ) ) : ?>
				<button class="ldgr_upload_image button">
					<img
						class="ldgr-def-group-img"
						src="<?php echo esc_url( $group_image[0] ); ?>"
						alt="
						<?php
						printf(
							// translators: Group.
							esc_html__( '%s Image', 'wdm_ld_group' ),
							\LearnDash_Custom_Label::get_label( 'group' )
						);
						?>
						"
					/>
				</button>
				<button class="ldgr_remove_image button">
					<?php esc_html_e( 'Remove', 'wdm_ld_group' ); ?>
				</button>
			<?php else : ?>
				<button class="ldgr_upload_image button">
					<?php esc_html_e( 'Upload', 'wdm_ld_group' ); ?>
				</button>
				<button class="ldgr_remove_image button ldgr-hide">
					<?php esc_html_e( 'Remove', 'wdm_ld_group' ); ?>
				</button>
			<?php endif; ?>
			<input type="hidden" name="ldgr_default_group_image" id="ldgr_default_group_image" value="<?php echo esc_attr( $ldgr_default_group_image ); ?>"/>
		</div>

		<div class="ldgr-default-course-image">
			<label for="ldgr_default_course_image">
				<?php
				printf(
					// translators: Course.
					esc_html__( 'Default %s image', 'wdm_ld_group' ),
					$course_label
				);
				?>
			</label>
			<?php if ( $course_image = wp_get_attachment_image_src( $ldgr_default_course_image ) ) : ?>
				<button class="ldgr_upload_image button">
					<img
						class="ldgr-def-course-img"
						src="<?php echo esc_url( $course_image[0] ); ?>"
						alt="
						<?php
						printf(
							// translators: Course.
							esc_html__( '%s Image', 'wdm_ld_group' ),
							\LearnDash_Custom_Label::get_label( 'course' )
						);
						?>
						"
					/>
				</button>
				<button class="ldgr_remove_image button">
					<?php esc_html_e( 'Remove', 'wdm_ld_group' ); ?>
				</button>
			<?php else : ?>
				<button class="ldgr_upload_image button">
					<?php esc_html_e( 'Upload', 'wdm_ld_group' ); ?>
				</button>
				<button class="ldgr_remove_image button ldgr-hide">
					<?php esc_html_e( 'Remove', 'wdm_ld_group' ); ?>
				</button>
			<?php endif; ?>
			<input type="hidden" name="ldgr_default_course_image" id="ldgr_default_course_image" value="<?php echo esc_attr( $ldgr_default_course_image ); ?>"/>
		</div>

		<?php wp_nonce_field( 'ldgr_save_appearance_settings', 'ldgr_nonce' ); ?>

		<?php submit_button( __( 'Save', 'wdm_ld_group' ) ); ?>

	</form>
</div>
