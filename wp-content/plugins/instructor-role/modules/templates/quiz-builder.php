<?php
/**
 * Instructor Frontend Dashboard template
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
<body class="cleanpage"> <?php // cspell:disable-line . ?>
	<?php if ( ! ( current_user_can( 'manage_options' ) || ( function_exists( 'wdm_is_instructor' ) && wdm_is_instructor() ) ) ) : ?>
		<div class="ir-no-builder-access">
			<p><?php esc_html_e( 'You need to be logged in as an administrator or instructor to view this page.', 'wdm_instructor_role' ); ?></p>
			<p>
				<?php
				echo wp_kses(
					sprintf(
						'Please <a href="%s">login</a> to proceed',
						wp_login_url(
							home_url( filter_input( INPUT_SERVER, 'REQUEST_URI' ) )
						)
					),
					[ 'a' => [ 'href' => [] ] ]
				);
				?>
			</p>
		</div>
	<?php else : ?>
		<div class="ir-quiz-builder-container">

		</div>

	<?php endif; ?>
	<?php get_footer(); ?>
</body>
</html>
