<?php
/**
 * Partial: Page - Extensions.
 *
 * @since 3.5.1
 * @version 4.3.15
 * @deprecated 4.3.14 This file is no longer in use.
 *
 * @var object
 *
 * @package LearnDash\Seats_Plus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

_deprecated_file( __FILE__, '4.3.14', '', 'This file is no longer in use.' );

?>
<div id="ldgr-other-extensions">
	<?php
	if ( $extensions ) {
		echo '<h2>' . __( 'Check Out Our Other Extensions', 'wdm_ld_group' ) . '</h2>';
		?>
		<!-- <div> -->
		<ul class="extensions">
		<?php
			$extensions = $extensions->ld_extension;
			$i          = 0;
		foreach ( $extensions as $extension ) {
			if ( $i > 7 ) {
				break;
			}

			// If plugin is already installed, don't list this plugin.
			if ( file_exists( WP_PLUGIN_DIR . '/' . $extension->dir . '/' . $extension->plug_file ) ) {
				continue;
			}

			echo '<li class="product" title="' . __( 'Click here to know more', 'wdm_ld_group' ) . '">';
			echo '<a href="' . $extension->link . '" target="_blank">';
			echo '<h3>' . $extension->title . '</h3>';
			if ( ! empty( $extension->image ) ) {
				echo '<img src="' . $extension->image . '"/>';
			} else {
				// echo '<h3>'.$extension->title.'</h3>';
			}
			// echo '<span class="price">' . $extension->price . '</span>';
			echo '<p>' . $extension->excerpt . '</p>';
			echo '</a>';
			echo '</li>';
			++$i;
		}
		?>
		</ul>
	<!-- </div> -->
		<?php
		// If all the extensions have been installed on the site.
		if ( 0 == $i ) {
			?>
		<h1 class="thank-you"><?php _e( 'You have all of our extensions. Thank you for your support!', 'wdm_ld_group' ); ?></h1>
			<?php
		}
	}
	?>
	<p>
		<a href="" target="_blank" class="browse-all">
		<?php _e( 'Browse all our extensions', 'wdm_ld_group' ); ?>
		</a>
	</p>
</div>
