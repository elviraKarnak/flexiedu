<?php
/**
 * Admin notices.
 *
 * @since 1.0.1
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_Notices
 *
 * Admin notices.
 *
 * @since 1.0.1
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */
class LD_GB_Notices {
	/**
	 * LD_GB_Notices constructor.
	 *
	 * @since 1.0.1
	 */
	public function __construct() {
		add_action( 'wp_ajax_ld_gb_dismiss_notice', [ $this, 'ajax_dismiss_notice' ] );

		if ( isset( $_GET['ld_gb_return_notice'] ) ) {
			add_action( 'admin_init', [ $this, 'return_notice_query_param' ] );
		}
	}

	/**
	 * Returns a dismissed notice via a query parameter ($_GET).
	 *
	 * @since 1.0.1
	 * @access private
	 */
	function return_notice_query_param() {
		$ID = $_GET['ld_gb_return_notice'];

		self::return_notice( $ID );
	}

	/**
	 * Dismisses a notice.
	 *
	 * @since 1.0.1
	 *
	 * @param string $ID The notice ID.
	 *
	 * @return bool True on success, false on failure.
	 */
	public static function dismiss_notice( $ID ) {
		if ( ! ( $user_ID = get_current_user_id() ) ) {
			return false;
		}

		$result = update_user_meta( $user_ID, "ld_gb_dismiss_notice_{$ID}", 1 );

		return $result || false;
	}

	/**
	 * Returns a dismissed notice.
	 *
	 * @since 1.0.1
	 *
	 * @param string $ID The notice ID.
	 *
	 * @return bool True on success, false on failure.
	 */
	public static function return_notice( $ID ) {
		if ( ! ( $user_ID = get_current_user_id() ) ) {
			return false;
		}

		$result = delete_user_meta( $user_ID, "ld_gb_dismiss_notice_{$ID}" );

		return $result || false;
	}

	/**
	 * AJAX callback for dismissing a notice.
	 *
	 * @since 1.0.1
	 * @access private
	 */
	function ajax_dismiss_notice() {
		$ID = isset( $_POST['id'] ) ? $_POST['id'] : false;

		if ( ! $ID ) {
			wp_send_json_error();
		}

		if ( self::dismiss_notice( $ID ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}
}
