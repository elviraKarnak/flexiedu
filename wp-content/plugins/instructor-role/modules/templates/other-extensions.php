<?php
/**
 * Partial: Page - Extensions.
 *
 * @var object
 *
 * @package LearnDash\Instructor_Role
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div id="ir-other-extensions" class="ir-instructor-settings-tab-content">
	<?php
	if ( $extensions ) {
		?>
		<div class="ir-flex justify-apart align-center">
			<div class="ir-heading-wrap">
				<div class="ir-tab-heading"><?php echo __( 'Other Plugins', 'wdm_instructor_role' ); ?></div> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
			</div>
			<a class="ir-primary-btn" href="https://www.learndash.com/add-ons/" target="_blank" class="browse-all">
				<?php _e( 'Browse all our extensions', 'wdm_instructor_role' ); ?>
			</a>
		</div>
		<div class="ir-heading-desc"></div>
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

			echo '<li class="product ir-extension" title="' . __( 'Click here to know more', 'wdm_instructor_role' ) . '">';
			echo '<div>';
			echo '<h3 class="ir-extension-name">' . $extension->title . '</h3>';
			if ( ! empty( $extension->image ) ) {
				echo '<img src="' . $extension->image . '"/>';
			} else {
			}
			echo '<p class="ir-extension-desc">' . $extension->excerpt . '</p>';
			echo '</div>';
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later.
			echo '<a class="ir-exlore-btn" href="' . $extension->link . '" target="_blank">' . __( 'Explore', 'wdm_instructor_role' ) . '</a>'; // cspell:disable-line .
			echo '</li>';
			++$i;
		}
		?>
		</ul>
		<?php
		// If all the extensions have been installed on the site.
		if ( 0 == $i ) {
			?>
		<h1 class="thank-you"><?php _e( 'You have all of our extensions. Thank you for your support!', 'wdm_instructor_role' ); ?></h1>
			<?php
		}
	}
	?>

</div>
