<?php
/**
 * Hotjar Survey Template File
 *
 * @since 5.7.0
 *
 * @param string $image_url     Logo URL.
 * @param string $survey_url    Survey URL.
 * @param string $dismiss_link  Dismiss Link.
 *
 * cspell:ignore hotjar
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="notice is-dismissable" style="background-color: #fefece; font-weight: 500;"> <?php // cspell:disable-line . ?>
	<div style="display:inline-block;width:10%;text-align:left;">
		<img src="<?php echo esc_url_raw( $image_url ); ?>" style="object-fit: contain;max-height:55px;margin-left:10px; margin-top:10px;"/>
	</div>
	<div style="display:inline-block;vertical-align: top;margin: 12px 20px;">
		<div>
			<?php esc_html_e( 'We would love to hear from you what should be the next feature for the Instructor Role plugin.', 'wdm_instructor_role' ); ?>
		</div>
		<div><?php esc_html_e( 'This survey is just 3 questions long and your feedback will be very helpful to us', 'wdm_instructor_role' ); ?></div>
		<a href="<?php echo esc_url_raw( $survey_url ); ?>" target="_blank"><?php esc_html_e( 'Take the Survey', 'wdm_instructor_role' ); ?></a> | <a href="<?php echo esc_url_raw( $dismiss_link ); ?>"><?php esc_html_e( 'Dismiss', 'wdm_instructor_role' ); ?></a>
	</div>
</div>
