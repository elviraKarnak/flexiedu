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
	<div class='wrld-help-page-section'>
		<div class='wrld-section-head'>
			<div class='help-icon'></div>
			<div class='wrld-section-head-text'><span class='text'><?php esc_html_e( 'Need help?', 'wdm_instructor_role' ); ?></span></div>
		</div>
		<div class='wrld-section-subhead'>
			<div class='wrld-section-subhead-text'>
				<?php esc_html_e( 'Refer the following links from the documentation of the plugin:', 'wdm_instructor_role' ); ?>
			</div>
		</div>
		<ul class='wrld-help-link-wrapper'>
			<?php
			foreach ( $help_articles as $article ) {
				?>
				<li>
					<a class='wrld-help-page-links' target="__blank" href="<?php echo esc_attr( $article['link'] ); ?>">
						<span><?php echo esc_html( $article['title'] ); ?></span>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
		<div class='wrld-section-head'>
			<div class='help-icon'></div>
			<div class='wrld-section-head-text'><span class='text'><?php esc_html_e( 'Video Links', 'wdm_instructor_role' ); ?></span></div>
		</div>
		<div class='wrld-section-subhead'>
			<div class='wrld-section-subhead-text'>
				<?php esc_html_e( 'Refer the following links from the documentation of the plugin:', 'wdm_instructor_role' ); ?>
			</div>
		</div>
		<ul class='wrld-help-link-wrapper'>
			<?php
			foreach ( $video_links as $links ) {
				?>
				<li>
					<a class='wrld-help-page-links' target="__blank" href="<?php echo esc_attr( $links['link'] ); ?>">
						<span><?php echo esc_html( $links['title'] ); ?></span>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
	</div>
</div>
