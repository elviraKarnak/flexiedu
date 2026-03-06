<?php
/**
 * Backend Dashboard Settings Template
 *
 * @since 5.0.0
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter&family=Open+Sans&display=swap" rel="stylesheet">
<div class="ir-backend-dashboard-settings">

	<ul class="ir-tabs-nav">
		<li class="active"><a href="#menu_settings"><?php esc_html_e( 'Menu Settings', 'wdm_instructor_role' ); ?></a></li>
		<li><a href="#overview_page"><?php esc_html_e( 'Overview Page', 'wdm_instructor_role' ); ?></a></li>
		<li><a href="#appearance_settings"><?php esc_html_e( 'Appearance', 'wdm_instructor_role' ); ?></a></li>
	</ul>
	<section class="ir-tabs-content">
		<div id="menu_settings" class="ir-tab">
			<?php
			/**
			 * Menu Settings Tab Content
			 *
			 * @since 5.0.0
			 */
			do_action( 'ir_action_menu_settings_content' );
			?>
			<?php $instance->show_hide_menu_settings(); ?>
		</div>
		<div id="overview_page" class="ir-tab">
			<?php
			/**
			 * Overview Page Settings Tab Content
			 *
			 * @since 5.0.0
			 */
			do_action( 'ir_action_overview_page_settings_content' );
			?>
			<?php $instance->show_hide_overview_settings(); ?>

		</div>
		<div id="appearance_settings" class="ir-tab">
			<?php
			/**
			 * Appearance Settings Tab Content
			 *
			 * @since 5.0.0
			 */
			do_action( 'ir_action_appearance_settings_content' );
			?>
		</div>
	</section>
</div>
