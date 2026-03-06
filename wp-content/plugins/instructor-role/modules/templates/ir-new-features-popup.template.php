<?php
/**
 * New Features PopUp Message Template
 *
 * @since 4.3.0
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter&family=Open+Sans&display=swap" rel="stylesheet">
<script type="text/javascript">
	function ir_remove_modal() {
		document.getElementById("wrld-custom-modal").remove();
	}
</script>
<div id="wrld-custom-modal" class="wrld-custom-popup-modal ir-popup-lg">
	<div class="wrld-modal-content">
		<div class="wrld-modal-content-container">
			<div class="wrld-modal-info-section">
				<span id="ir_close_modal" class="dashicons dashicons-no"></span>
				<div class="ir-modal-head">
							<span>
								<p class="ir-modal-head-text">
									<?php esc_html_e( 'Instructor Role Setup ', 'wdm_instructor_role' ); ?>
								</p>
							</span>
				</div>
				<div>
					<span>
							<p class="ir-modal-text">
								<b class="ir-heading-color" style="font-size: 20px">
								<?php
								echo esc_html_e( 'This plugin has new user role named Instructor', 'wdm_instructor_role' );
								?>
								</b>
							</p>
							<p class="ir-modal-text">
							<b class="ir-heading-color">
							<?php
							echo esc_html_e( 'Who is an instructor', 'wdm_instructor_role' );
							?>
							</b>
							</p>
							<p class="ir-modal-text">
								<?php
								echo esc_html_e( 'An Instructor can be a teacher, junior course creator, subject matter expert, or anyone wishing to sell courses on your platform. You can create new instructors or assign existing users as instructors', 'wdm_instructor_role' );
								?>
							</p>
						</span>
					<div class="ir-flex ir-capabilitites"> <?php // cspell:disable-line . ?>
						<div>
							<p class="ir-modal-text">
								<b class="ir-heading-color">
									<?php
									echo esc_html_e( 'What are Instructor Capabilities:', 'wdm_instructor_role' );
									?>
								</b>
							</p>
							<ul class="ir-modal-list">
								<li>
									<b class="ir-heading-color"><?php echo esc_html_e( 'Manage Courses — ', 'wdm_instructor_role' ); ?></b>
									<?php
									echo esc_html_e( 'Instructor can manage courses in the course page', 'wdm_instructor_role' );
									?>
								</li>
								<li>
									<b class="ir-heading-color"><?php echo esc_html_e( 'Create Courses — ', 'wdm_instructor_role' ); ?></b>
									<?php
									echo esc_html_e( 'Instructors can use the front-end builder to easily create their own courses.', 'wdm_instructor_role' );
									?>

								</li>
								<li>
									<b class="ir-heading-color"><?php echo esc_html_e( 'Earn Commissions — ', 'wdm_instructor_role' ); ?></b>
									<?php
									echo esc_html_e( 'Reward your instructors with commissions.', 'wdm_instructor_role' );
									?>
								</li>
							</ul>
						</div>
						<div style="margin-top: auto; margin-bottom: 20px;">
							<img src=<?php echo esc_url_raw( $instructor_popup ); ?> alt="">
						</div>
					</div>
					<div class="wrld-modal-actions" style="justify-content:center;">
						<div class="wrld-modal-action-item">
							<button class="modal-button modal-button-configure wrld-modal-button">
								<?php esc_html_e( 'Get Started', 'wdm_instructor_role' ); ?>
								<i class="fa fa-chevron-right" aria-hidden="true"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
