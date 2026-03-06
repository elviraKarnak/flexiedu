<?php
/**
 * Default Instructor Menu Template
 *
 * @since 3.1.0
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="ir-primary-navigation" class="menu-test-container ir-default-menu">
	<div class="wdm-mob-menu wdm-admin-menu-show wdm-hidden">
		<span class="dashicons dashicons-menu-alt"></span>
	</div>
	<div class="ir-mob-dashboard-menu">
		<span class="dashicons dashicons-ellipsis"></span>
	</div>
	<ul id="ir-primary-menu" class="menu">
		<?php
		if ( isset( $_COOKIE['wdm_ir_old_user'] ) ) {
			?>
			<li class="switch-back menu-item menu-item-type-custom menu-item-object-custom"><a href="<?php echo home_url() . '/wp-login.php?action=wdm_ir_switchback_user'; ?>"><?php esc_html_e( 'Switch to Admin', 'wdm_instructor_role' ); ?></a></li>
			<?php
		}
		?>
		<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="<?php echo home_url(); ?>"><? esc_html_e( 'Exit Dashboard', 'wdm_instructor_role' ) ?></a></li>
		<?php if ( defined( 'WDM_LD_REPORTS_FILE' ) && get_option( 'ldrp_reporting_page' ) ) : ?>
			<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="<?php echo get_permalink( get_option( 'ldrp_reporting_page' ) ); ?>"><?php _e( 'Advanced Reports', 'instructor-role' ); ?></a></li>
		<?php endif; ?>
		<li class="menu-item menu-item-type-custom menu-item-object-custom"><a href="<?php echo wp_logout_url(); ?>"><?php _e( 'Logout', 'instructor-role' ); ?></a></li>
	</ul>
</div>
