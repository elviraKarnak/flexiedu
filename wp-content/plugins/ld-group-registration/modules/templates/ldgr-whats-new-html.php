<?php
/**
 * What's New HTML for LearnDash LMS - Group Registration
 *
 * @since 4.3.8
 * @version 4.3.15
 * @deprecated 4.3.14 This file is no longer in use.
 *
 * @package LearnDash\Seats_Plus
 */

_deprecated_file( __FILE__, '4.3.14', '', 'This file is no longer in use.' );

defined( 'ABSPATH' ) || exit;

// A link to customizer option of CPB.
$query['autofocus[panel]']   = 'cpb_panel';
$query['autofocus[section]'] = 'cpb_layout_section';
?>
<div class="main-content cpb-whats-new">
	<div class="cpb-header">
		<h1>LearnDash LMS - Group Registration v4.3.8</h1>
	</div>
	<div class="content">
		<div class="odd">
			<div class="column">
				<h1><strong>Total Seats</strong> Count</h1>
				<h3>A new <strong>Total Seats</strong> Count introduced on the “Group” Edit Page.</h3>
				<ul>
					<li>The newly introduced count enables the admin to easily add and remove seats from an existing Group on the Edit Page of a LearnDash Group.</li>
					<li>The admin can also use this new count to easily manage the “Seats Left” in a particular group.</li>
					<li>For more details, visit the <a href="https://www.learndash.com/support/docs/add-ons/group-registration-for-learndash/">Help guide</a>.</li>
				</ul>
			</div>
			<div class="column">
				<div class="youtube-container">
					<img width="560" height="" src="<?php echo esc_url( plugins_url( '/media/seat-count-fix-flow.png', __DIR__ ) ); ?>">
				</div>
			</div>
		</div>
		<div class="cpb-cta">
			<a href="https://www.learndash.com/help/" class="button" target="_blank">Support</a>
			<a href="https://www.learndash.com/support/docs/add-ons/group-registration-for-learndash/" class="button" target="_blank">Docs</a>
		</div>
	</div>
</div>
