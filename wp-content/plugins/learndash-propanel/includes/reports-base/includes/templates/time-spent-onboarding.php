<?php
/**
 * Template to display the Learner activity onboarding notice.
 *
 * @package LearnDash\Reports
 *
 * @since 3.0.0
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="notice notice-error is-dismissible wrld-ts-main-container" style="padding:0px !important;">

	<div class="wrld-ts-logo">
		<img class="wrld-ts-logo-img" src='<?php echo esc_html( $wisdm_logo ); ?>'>
	</div>
	<div class="wrld-ts-center">
		<div class="wrld-ts-head-text">
			<?php echo esc_html( $banner_head ); ?>
		</div>
		<div class="wrld-ts-sub-text">
			<?php echo esc_html( $banner_message ); ?>
		</div>

		<div class="wrld-ts-btn-container">
			<div class="right-box wrld-ts-right-box">
			<b class="add-weight first-rh-row"><?php esc_html_e( 'Mandatory Data Upgrade required', 'learndash-reports-pro' ); ?></b>
				<div class="first-r-row">
					<?php esc_html_e( "We've also enhanced the performance the above report by optimizing how we store time spent data in our course time entries table. To ", 'learndash-reports-pro' ); ?>
					<b class="add-weight"><?php esc_html_e( "see the learner's earlier time spent information on this enhanced report,", 'learndash-reports-pro' ); ?></b>
					<?php esc_html_e( ' click below to go to data upgrade settings.', 'learndash-reports-pro' ); ?>
				</div>
				<a href="<?php echo esc_html( $page_link ); ?>"><button  class="wrld-ts-auto-button">
						<div class="wrld-btn-txt">
							<?php esc_html_e( ' Upgrade data now ', 'learndash-reports-pro' ); ?></div>
						<span class="right_arrow_icon">></span>
					</button></a>
			</div>
		</div>

	</div>
</div>
