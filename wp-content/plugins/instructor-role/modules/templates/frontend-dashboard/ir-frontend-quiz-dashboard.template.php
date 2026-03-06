<?php
/**
 * Instructor Frontend Quiz Creation template
 *
 * @since 4.4.0
 *
 * @package LearnDash\Instructor_Role
 */

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
							wp_login_url( site_url() . '/quiz-builder/' . get_query_var( 'ir_quiz', false ) )
						),
						[ 'a' => [ 'href' => [] ] ]
					);
				?>
			</p>
		</div>
	<?php else : ?>
		<div class="ir-quiz-builder-container">
			<?php if ( function_exists( 'learndash_get_course_id' ) ) : ?>
				<input type="hidden" id="ir_course_id" value="<?php echo esc_attr( learndash_get_course_id( get_query_var( 'ir_quiz', false ) ) ); ?>">
			<?php endif; ?>
			<?php
			echo do_blocks(
				'<!-- wp:instructor-role/quiz-builder -->
					<div class="wp-block-instructor-role-quiz-builder">
						<div class="wisdm-quiz-builder"></div>
					</div>
				<!-- /wp:instructor-role/quiz-builder -->'
			);
			?>
		</div>
	<?php endif; ?>
	<?php get_footer(); ?>
</body>
</html>
