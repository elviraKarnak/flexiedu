<?php
/**
 * HTMl for settings pages.
 *
 * @since 1.2.0
 *
 * @var string $page Current settings page
 * @var array $active_section Currently active section and its args.
 */

defined( 'ABSPATH' ) || die();
?>

<form action="options.php" method="post">

	<?php settings_fields( $page ); ?>

	<section class="ld-gb-settings-content">
		<?php do_settings_sections( $page ); ?>

		<?php if ( $active_section['args']['display_submit'] ) : ?>
			<?php submit_button(); ?>
		<?php endif; ?>
	</section>

</form>