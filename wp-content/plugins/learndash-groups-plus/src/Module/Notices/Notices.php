<?php
/**
 * Admin Notices module file.
 *
 * @since 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Module\Notices;

use LearnDash\Groups_Plus\StellarWP\AdminNotices\AdminNotices;
use LearnDash\Groups_Plus\StellarWP\SuperGlobals\SuperGlobals;

/**
 * Admin Notices module class.
 *
 * @since 2.1.2
 */
class Notices {
	/**
	 * Returns admin notice query param.
	 *
	 * @since 2.1.2
	 *
	 * @var string
	 */
	public static $admin_notice_key = 'ld_groups_plus_message';

	/**
	 * Returns admin notice query param value for autologin set password.
	 *
	 * @since 2.1.2
	 *
	 * @var string
	 */
	public static $admin_notice_autologin_set_password = 'autologin_set_password';

	/**
	 * Registers admin notices.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	public function register_admin_notices(): void {
		if ( SuperGlobals::get_get_var( self::$admin_notice_key ) === self::$admin_notice_autologin_set_password ) {
			AdminNotices::show(
				'learndash_groups_plus_autologin_set_password',
				esc_html__( 'Please set your password. Autologin link is valid for one-time use only.', 'learndash-groups-plus' )
			)
				->on( 'profile.php' )
				->asWarning()
				->autoParagraph();
		}
	}
}
