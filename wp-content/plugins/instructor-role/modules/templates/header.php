<?php
/**
 * Outputs the header for Instructor Role frontend templates.
 *
 * @since 5.9.3
 * @version 5.9.3
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;

if ( wp_is_block_theme() ) {
	?>

	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		<div class="wp-site-blocks">
			<header class="wp-block-template-part">
				<?php block_header_area(); ?>
			</header>
	<?php
} else {
	get_header();
}
