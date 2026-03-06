<?php
/**
 * Instructor Frontend Course Creation template
 *
 * @since 4.4.0
 *
 * @package LearnDash\Instructor_Role
 */

use InstructorRole\Modules\Classes\Instructor_Role_Frontend_Dashboard;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
global $wp_query;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> >
	<?php if ( ! ( current_user_can( 'manage_options' ) || ( function_exists( 'wdm_is_instructor' ) && wdm_is_instructor() ) ) ) : ?>
		<div class="ir-no-builder-access">
			<p><?php esc_html_e( 'You need to be logged in as an administrator or instructor to view this page.', 'wdm_instructor_role' ); ?></p>
			<p>
				<?php
				echo wp_kses(
					sprintf(
						'Please <a href="%s">login</a> to proceed',
						wp_login_url( get_the_permalink() . get_query_var( 'ir_course', false ) )
					),
					[ 'a' => [ 'href' => [] ] ]
				);
				?>
			</p>
		</div>
	<?php else : ?>
		<div class="ir-course-builder-container"
		data-course-id="<?php echo esc_attr( get_query_var( 'ir_course', false ) ); ?>"
		data-label="<?php echo esc_attr( get_the_title( get_query_var( 'ir_course', false ) ) ); ?>"
		data-all-courses-link="<?php echo esc_attr( Instructor_Role_Frontend_Dashboard::get_back_to_dashboard_link( 'course' ) ); ?>">
			<?php
			echo do_blocks(
				'<!-- wp:instructor-role/course-builder -->
					<div class="wp-block-instructor-role-course-builder">
						<div class="wisdm-mui-poc"></div>
					</div>
				<!-- /wp:instructor-role/course-builder -->'
			);
			?>
		</div>
		<div class="ir-mobile-message" style="padding: 20px;">
			<?php esc_html_e( 'Note: To get the best experience use the Course builder on a desktop/laptop. Currently, the course builder is not optimized for Tablet and Mobile view.', 'wdm_instructor_role' ); ?>
		</div>
		<?php wp_nonce_field( 'wp_rest', 'ir_fcb_nonce' ); ?>
	<?php endif; ?>
	<?php get_footer(); ?>
</body>
</html>
