<?php
/**
 * Group Users Tab Student Access Confirmation Template.
 *
 * @since 4.1.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="ldgr-st-access-confirmation">
	<img src="<?php echo esc_url( plugins_url( '../assets/images/completed.png', dirname( __DIR__ ) ) ); ?>" class="ldgr-completed">
	<h2 class="ldgr-enrolled-txt">You’ve been enrolled to the courses listed below</h2>
	<div class="ldgr-access-note">
		<span class="ldgr-note-label"><i class="ldgr-icon-Note"></i>Note</span>
		<span class="ldgr-access-txt">You can also access these courses and view progress from  <b>‘My Courses’ section</b></span>
	</div>
	<div class="ldgr-access-group-courses">
		<div class="ldgr-access-group-courses-items">
			<div class="ldgr-access-group-courses-item">
				<img src="<?php echo esc_url( plugins_url( '../assets/images/course1.png', dirname( __DIR__ ) ) ); ?>">
				<div class="ldgr-access-course-title">
					German 101
				</div>
			</div>
			<div class="ldgr-access-group-courses-item">
				<img src="<?php echo esc_url( plugins_url( '../assets/images/course2.png', dirname( __DIR__ ) ) ); ?>">
				<div class="ldgr-access-course-title">
					French 101
				</div>
			</div>
		</div>
	</div>
</div>
