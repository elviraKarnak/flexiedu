<?php
/**
 * Instructor Dashboard Setup Notice.
 *
 * @since 5.0.0
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<div>
	<h2><?php esc_html_e( 'You are one step away from launching the Instructor Dashboard.', 'wdm_instructor_role' ); ?></h2>
	<div>
		<p class="ir-notice-info">
		<?php
		echo wp_kses(
			__( 'Customize the below pre-configured dashboard as per you need and <strong>click on the “Publish”</strong> to make the dashboard live or Click on <strong>"Save as draft"</strong> to edit later', 'wdm_instructor_role' ),
			[
				'strong' => [],
			]
		);
		?>
	</p>
		<ul>
			<li>
				<?php
				echo wp_kses(
					__( 'Select you desired page template from the right sidebar. By default Instructor Dashboard template is applied. You can choose you theme’s template or any other as per you need. <a href="https://learndash.com/support/docs/add-ons/introducing-frontend-dashboard/" target="_blank">Learn More</a>', 'wdm_instructor_role' ),
					[
						'a' => [
							'href'   => [],
							'target' => [],
						],
					]
				);
				?>
			</li>
			<li>
				<?php esc_html_e( 'Each Dashboard tab seen below is a Gutenberg block. click on the', 'wdm_instructor_Role' ); ?>
				<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" aria-hidden="true" focusable="false"><path d="M13.8 5.2H3v1.5h10.8V5.2zm-3.6 12v1.5H21v-1.5H10.2zm7.2-6H6.6v1.5h10.8v-1.5z"></path></svg>
				<?php
				esc_html_e(
					' icon in the header to view all the blocks on this page',
					'wdm_instructor_role'
				);
				?>
			</li>
			<li>
				<?php esc_html_e( 'You can also hide/show/reorder the tabs to customize the dashboard or created custom tabs and add other blocks/content useful for the instructors.', 'wdm_instructor_role' ); ?>
			</li>
			<li>
				<?php
				echo wp_kses(
					__( 'Once done with you customization <strong>click on publish to make the dashboard live</strong>', 'wdm_instructor_role' ),
					[
						'strong' => [],
					]
				);
				?>
			</li>
			<li>
				<?php
				echo wp_kses(
					__( 'Or <strong>Click on Save to draft</strong> to edit later', 'wdm_instructor_role' ),
					[
						'strong' => [],
					]
				);
				?>
			</li>
		</ul>
	</div>
</div>
