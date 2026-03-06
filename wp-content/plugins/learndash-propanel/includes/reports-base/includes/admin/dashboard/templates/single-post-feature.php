<?php
/**
 * Single Post Feature Template.
 *
 * @package LearnDash\Reports
 *
 * $linkImg  Post Image Link;
 * $title  Title,
 * $is_new = true;
 * $is_pro = true;
 * $doc_config_link Documentation link
 * $is_latest Latest version check
 * $post_description Post Description;
 *
 * @since 3.0.0
 * @version 3.0.0
 *
 * cspell:ignore slideindex showmodal
 */

defined( 'ABSPATH' ) || exit;

$is_pro_user = apply_filters( 'wisdm_ld_reports_pro_version', false );
// $img_array   = count( $img_array ) < 1 ? array( ) : $img_array;
?>

<div class="wrld-feature-post">
	<div class="wrld-feature-post-header">
		<h2><?php echo $title ?? ''; ?></h2>
	</div>
	<div class="wrld-feature-post-content">
		<div class="wrld-post-info">
			<p class="wrld-post-desc"><?php echo $post_description ?? ''; ?></p>
			<p class="wrld-post-info-bottom"><p class="wrld-post-notice" style="margin:0;padding:0;">
			<?php
			if ( isset( $version_data ) && count( $version_data ) > 0 ) { // Latest post
				if ( $is_pro_user ) { // Pro user.
					if ( ! $is_old_version ) {
						esc_html_e( 'You are on older version please update the plugin to get the best experience.', 'learndash-reports-pro' );
						?>
					<div class="wrld-feature-post-footer">
						<div class="wrld-feature-post-footer-button">
							<?php echo '<a href="https://go.learndash.com/ppaddon" target="_blank">' . esc_html__( 'Update', 'learndash-reports-pro' ) . '</a>'; ?>
						</div>
					</div>
						<?php
					}
				}
			}
			?>
			</p>
			</p>
		</div>
		<?php if ( ! empty( $img_array ) ) { ?>
		<div class="wrld-post-images wrld-slider-container">
			<?php foreach ( $img_array as $img ) { ?>
			<img class="mySlides" onclick="wrldShowmodal(this,this.parentElement);" src="<?php echo $img; ?>" alt="" style="width:100%">
			<?php } ?>
			<button class="wrld-slider-btn-color wrld-slider-btn-left <?php echo count( $img_array ) === 1 ? 'wrld-single-hide' : ''; ?>" data-slideindex="1" onclick="plusDivs(this,-1)">&#10094;</button>
			<button class="wrld-slider-btn-color wrld-slider-btn-right wrld-slider-btn-nxt <?php echo count( $img_array ) === 1 ? 'wrld-single-hide' : ''; ?>" data-slideindex="1" onclick="plusDivs(this,1)">&#10095;</button>
		</div>
		<?php } ?>
	</div>
	<?php if ( '' !== $doc_config_link ) { ?>
	<div class="wrld-feature-post-footer">
		<div class="wrld-feature-post-footer-text">
			<a class="wrld-config-doc-link <?php echo '' == $doc_config_link ? 'wrld-single-hide' : ''; ?>" href="<?php echo $doc_config_link; ?>"
				target="_blank"><?php esc_html_e( 'Configuration settings DOC', 'learndash-reports-pro' ); ?>
				<span class="dashicons dashicons-external"></span></a>
		</div>
	</div>
	<?php } ?>
</div>
