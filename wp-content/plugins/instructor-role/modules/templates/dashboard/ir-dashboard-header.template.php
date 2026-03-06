<?php
/**
 * Instructor Dashboard Header Template.
 *
 * @since 4.3.0
 *
 * @param string $dashboard_logo     Logo URL.
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;

?>
<li id="ir-admin-logo-item">
	<?php if ( 'image' === $header_type && ! empty( $dashboard_logo ) ) : ?>
		<div class="ir-admin-logo ir-admin-image">
			<?php if ( '' !== $logo_url ) : ?>
				<a href="<?php echo esc_attr( $logo_url ); ?>">
			<?php endif; ?>
					<img src="<?php echo esc_attr( $dashboard_logo ); ?>" alt="<?php esc_attr_e( 'Dashboard Logo', 'wdm_instructor_role' ); ?>">
			<?php if ( '' !== $logo_url ) : ?>
				</a>
			<?php endif; ?>
		</div>
	<?php elseif ( 'text' === $header_type ) : ?>
		<div id='ir-admin-logo-text' class='ir-admin-logo'>
			<div class='ir-admin-menu-logo-title' style="<?php echo esc_attr( $title_font_styles ); ?>">
				<?php echo esc_html( $text_title ); ?>
			</div>
			<div class='ir-admin-menu-logo-subtitle' style="<?php echo esc_attr( $subtitle_font_styles ); ?>">
				<?php echo esc_html( $text_sub_title ); ?>
			</div>
		</div>
	<?php endif; ?>
</li>
