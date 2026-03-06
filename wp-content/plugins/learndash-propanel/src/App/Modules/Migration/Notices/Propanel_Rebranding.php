<?php
/**
 * Supports the rebranding to ProPanel.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Reports
 */

namespace LearnDash\Reports\Modules\Migration\Notices;

/**
 * Supports the rebranding to ProPanel.
 *
 * @since 3.0.0
 */
class Propanel_Rebranding {
	/**
	 * Option key to store whether the rebranding notice has been displayed.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private const ADMIN_NOTICE_REBRANDING_OPTION_KEY = 'ld_reports_propanel_rebranding_notice_displayed';

	/**
	 * Option key that indicates that the plugin has been updated.
	 *
	 * It's an old meta that only exists in the database if the plugin was updated from a version before the rebranding.
	 * We use it to hide the rebranding notice for fresh installs.
	 *
	 * @since 3.0.0
	 *
	 * @var string
	 */
	private const IS_UPDATE_OPTION_KEY = 'wrld_whats_new_tab_visited';


	/**
	 * Adds an admin notice to inform the user that the plugin is now ProPanel.
	 *
	 * @since 3.0.0
	 *
	 * @return void
	 */
	public function add_admin_notice() {
		// Only show this notice to admin users and on LD admin pages.
		if (
			! current_user_can( 'manage_options' )
			|| ! learndash_should_load_admin_assets()
		) {
			return;
		}

		if (
			empty( get_option( self::IS_UPDATE_OPTION_KEY ) )
			|| ! empty( get_option( self::ADMIN_NOTICE_REBRANDING_OPTION_KEY ) )
		) {
			return;
		}

		update_option( self::ADMIN_NOTICE_REBRANDING_OPTION_KEY, LDRP_PLUGIN_VERSION );

		?>
		<div class="notice notice-info is-dismissible">
			<p>
				<?php
				printf(
					// translators: %1$s is the opening anchor tag with the target link, %2$s is the closing anchor tag.
					esc_html__(
						'LearnDash Reports Pro is now ProPanel! New name, same great plugin. %1$sLearn more%2$s.',
						'learndash-reports-pro'
					),
					'<a href="https://go.learndash.com/reportsmerge" target="_blank">',
					'</a>'
				);
				?>
			</p>
		</div>
		<?php
	}
}
