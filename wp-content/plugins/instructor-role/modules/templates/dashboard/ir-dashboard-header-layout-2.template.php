<?php
/**
 * Instructor Dashboard Header Template for Layout 2.
 *
 * @since 4.3.0
 *
 * @param string $dashboard_logo     Logo URL.
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<?php if ( ! empty( $dashboard_logo ) ) : ?>
	<li id="ir-admin-logo-item">
		<div class="ir-admin-logo ir-admin-image" style="justify-content: <?php echo esc_attr( $logo_alignment ); ?>;">
			<?php if ( '' !== $logo_url ) : ?>
				<a href="<?php echo esc_attr( $logo_url ); ?>">
			<?php endif; ?>
					<img src="<?php echo esc_attr( $dashboard_logo ); ?>" alt="<?php esc_attr_e( 'Dashboard Logo', 'wdm_instructor_role' ); ?>">
			<?php if ( '' !== $logo_url ) : ?>
				</a>
			<?php endif; ?>
		</div>
	</li>
<?php endif; ?>
<li id="ir-collapse-menu-item">
	<div style="min-height: <?php echo ( empty( $dashboard_logo ) ) ? '80px' : ''; ?>">
		<span>
			<?php echo esc_html( $dashboard_title ); ?>
		</span>
		<button type="button" id="collapse-button" aria-label="<?php esc_attr_e( 'Collapse Main menu', 'wdm_instructor_role' ); ?>" aria-expanded="true">
			<span class="dashicons dashicons-arrow-left-alt2"></span>
		</button>
		<button type="button" id="ir-collapse-button-mobile" aria-label="<?php esc_attr_e( 'Collapse Main menu', 'wdm_instructor_role' ); ?>" aria-expanded="true">
			<span class="dashicons dashicons-arrow-left-alt2"></span>
		</button>
	</div>
</li>
