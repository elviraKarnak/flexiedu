<?php
/**
 * Frontend Dashboard Template.
 *
 * @since 5.0.0
 * @version 5.9.3
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

ir_get_template(
	INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/header.php'
);

/**
 * Fires before the main body content of the frontend dashboard.
 *
 * @since 5.0.0
 */
do_action( 'ir_action_before_wisdm_frontend_dashboard_body' );
?>

<div class="ir-frontend-dashboard-template-body">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_content();
		}
	}
	?>
</div>

<?php
/**
 * Fires after the main body content of the frontend dashboard.
 *
 * @since 5.0.0
 */
do_action( 'ir_action_after_wisdm_frontend_dashboard_body' );

ir_get_template(
	INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/footer.php'
);
