<?php
/**
 * Instructor Role User Switch Module
 *
 * @since 4.2.1
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Classes;

use WP_User;

defined( 'ABSPATH' ) || exit;

/**
 * User switching.
 *
 * @since 4.2.1
 */
class Instructor_Role_User_Switch {
	/**
	 * Private class constructor. Use `get_instance()` to get the instance.
	 */
	private function __construct() {
	}

	/**
	 * Function that handles the overall user switch action.
	 *
	 * @since 4.2.1
	 */
	public function action_init() {
		if ( ! isset( $_GET['action'] ) ) {
			return;
		}

		// Security measure.
		if ( ! current_user_can( 'manage_options' ) ) {
			add_action( 'wp_logout', [ $this, 'ir_destroy_switch_cookie' ] );
		}

		// Allow action only if the user is logged in.
		$current_user = ( is_user_logged_in() ) ? wp_get_current_user() : null;
		if ( ! $current_user ) {
			return;
		}

		// if user can manage_options.
		if ( current_user_can( 'manage_options' ) || wdm_is_instructor() ) {
			switch ( $_REQUEST['action'] ) {
				case 'wdm_ir_switch_user':
					// check if the user has admin privileges.
					if ( ! current_user_can( 'manage_options' ) ) {
						global $current_user;
						wp_get_current_user();
						$user         = "'<strong>" . $current_user->user_login . "'</strong> ( " . $current_user->user_email . ' )';
						$link         = "<a href='" . get_home_url() . "/wp-login.php?action=wdm_ir_switchback_user'>" . esc_html__( 'switch back to admin', 'wdm_instructor_role' ) . '</a>';
						$ir_dashboard = "<a href='" . get_home_url() . "/wp-admin/admin.php?page=ir_instructor_overview'>" . esc_html__( 'Instructor dashboard', 'wdm_instructor_role' ) . '</a>';
						wp_die(
							sprintf(
							/* translators: activate link */
								__( 'You are already logged in as instructor named %1$s <br><br>Do you want to %2$s or go to %3$s ?', 'wdm_instructor_role' ),
								$user,
								$link,
								$ir_dashboard // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later.
							)
						);
					}

					// check if the user is trying to switch to himself.
					if ( $_GET['user_id'] == $current_user->ID ) {
						wp_die( esc_html_e( 'You are already logged in as this user', 'wdm_instructor_role' ) );
					}

					// set the user variable.
					if ( isset( $_GET['user_id'] ) ) {
						$user_id = absint( $_GET['user_id'] );
					} else {
						$user_id = 0;
					}

					if ( $user_id > 0 && wdm_is_instructor( $user_id ) ) {
						$user = get_user_by( 'id', $user_id );
						if ( $user ) {
							/**
							 * Security logic needs to be updated we studied nonce and got to know that
							 * the nonce is stored in a session and as soon as user is logged out this is destroyed
							 * hence the logic we created on call did not work. I further studied few plugins like
							 * user-switch and found out they store old session and old nonce details and authenticate the user
							 * this logic will require more time to invest.
							 */
							// set nonce cookie.
							$nonce         = $this->create_switch_user_nonce( get_current_user_id() );
							$cookie_path   = home_url();
							$cookie_domain = $_SERVER['HTTP_HOST'];
							setcookie( 'wdm_ir_switch_user_key', $nonce, time() + DAY_IN_SECONDS, $cookie_path, $cookie_domain, false );
							// set user cookie.
							setcookie( 'wdm_ir_old_user', get_current_user_id(), time() + DAY_IN_SECONDS, $cookie_path, $cookie_domain, false );

							// Check if redirect to frontend dashboard.
							$redirect_to_frontend_dashboard = false;
							$frontend_page                  = false;

							if ( array_key_exists( 'frontend', $_GET ) && $_GET['frontend'] ) {
								$frontend_page                  = get_option( 'ir_frontend_dashboard_page', false );
								$redirect_to_frontend_dashboard = true;
							}

							// Switch Logic.
							if ( $redirect_to_frontend_dashboard && $frontend_page ) {
								$this->ir_switch_user( $user, (string) get_permalink( $frontend_page ) );
							} else {
								$this->ir_switch_user( $user, admin_url() . 'admin.php?page=ir_instructor_overview' );
							}
						}
					}
					exit;
				case 'wdm_ir_switchback_user':
					if ( ! wdm_is_instructor() ) {
						wp_redirect( admin_url() );
						exit;
					}

					// check if cookie is set.
					if ( ! isset( $_COOKIE['wdm_ir_old_user'] ) ) {
						wp_die( esc_html_e( 'Sorry you are not logged in as an instructor', 'wdm_instructor_role' ) );
					} else {
						// read data from cookie.
						$user_id = $_COOKIE['wdm_ir_old_user'];
					}

					/**
					 * Security logic needs to be updated
					 */
					// check if cookie is set.
					if ( ! isset( $_COOKIE['wdm_ir_switch_user_key'] ) ) {
						wp_die( 'Sorry you are not logged in as an instructor' );
					} else {
						// verify nonce.
						$nonce = $_COOKIE['wdm_ir_switch_user_key'];
						if ( ! $this->verify_switch_user_nonce( $nonce, $user_id ) ) {
							wp_die( 'Sorry you are not logged in as an instructor' );
						}
					}
					/* This is a security check to make sure that the user id is a positive integer. */
					if ( $user_id > 0 ) {
						$user = get_user_by( 'id', $user_id );
						if ( $user ) {
							$this->ir_destroy_switch_cookie();
							$this->ir_switch_user( $user, admin_url() . 'admin.php?page=instuctor&tab=instructor' );
						}
					}
					exit;
			}
		} else {
			return;
		}
	}

	/**
	 * Function that handles the user switch action.
	 *
	 * @since 4.2.1
	 *
	 * @param WP_User $user         User object.
	 * @param string  $redirect_url Redirect URL.
	 *
	 * @return void
	 */
	public function ir_switch_user( $user, $redirect_url = '' ) {
		// logout user.
		wp_destroy_current_session();
		wp_clear_auth_cookie();

		// Switch user.
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID );
		do_action( 'wp_login', $user->user_login, $user );

		if ( empty( $redirect_url ) ) {
			$redirect_url = home_url();
		}
		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * Destroy switching user cookie.
	 *
	 * @since 4.2.1
	 */
	public function ir_destroy_switch_cookie() {
		// delete cookie.
		$cookie_path   = home_url();
		$cookie_domain = $_SERVER['HTTP_HOST'];
		setcookie( 'wdm_ir_old_user', '', time() - DAY_IN_SECONDS, $cookie_path, $cookie_domain, false );
		setcookie( 'wdm_ir_switch_user_key', '', time() - DAY_IN_SECONDS, $cookie_path, $cookie_domain, false );    }

	/**
	 * This function handles generation of profile links on profile page.
	 *
	 * @since 4.2.1
	 */
	public function ir_show_profile_links() {
		if ( ! isset( $_GET['user_id'] ) ) {
			$user_id = get_current_user_id();
		} else {
			$user_id = $_GET['user_id'];
		}

		ir_get_template(
			INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/profile/settings/ir-profile-links.template.php',
			[
				'user_id' => $user_id,
			]
		);
	}

	/**
	 * Singleton instance.
	 *
	 * @return Instructor_Role_User_Switch User Switching instance.
	 */
	public static function get_instance() {
		static $instance;

		if ( ! isset( $instance ) ) {
			$instance = new Instructor_Role_User_Switch();
		}

		return $instance;
	}

	/**
	 * Create a cryptographic token tied to the switching user, action and window of time.
	 *
	 * @since 4.2.1
	 *
	 * @param int $uid  ID of the switching user.
	 * @return string   The security token.
	 */
	public function create_switch_user_nonce( $uid ) {
		$i = wp_nonce_tick();

		/**
		 * Filter the nonce action used for switch user
		 *
		 * @since 4.2.1
		 *
		 * @param string $nonce_action      Nonce action used for verification.
		 */
		$action = apply_filters( 'ir_filter_switch_user_nonce_action', 'ir_switch_user_' . $uid );

		return substr( wp_hash( 'ir|' . $i . '|' . $action . '|sec', 'nonce' ), -12, 10 );
	}

	/**
	 * Verify that the user switching back to is the correct one by validating the token against the window of time.
	 *
	 * @since 4.2.1
	 *
	 * @param string $nonce Nonce value used for verification.
	 * @param int    $uid      ID of the user switching back to.
	 * @return int|false        1 if the nonce is valid and generated between 0-12 hours ago,
	 *                          2 if the nonce is valid and generated between 12-24 hours ago.
	 *                          False if the nonce is invalid.
	 */
	public function verify_switch_user_nonce( $nonce, $uid ) {
		$i = wp_nonce_tick();

		/**
		 * Filter the nonce action used for switch user
		 *
		 * @since 4.2.1
		 *
		 * @param string $nonce_action      Nonce action used for verification.
		 */
		$action = apply_filters( 'ir_filter_switch_user_nonce_action', 'ir_switch_user_' . $uid );

		// Nonce generated 0-12 hours ago.
		$expected = substr( wp_hash( 'ir|' . $i . '|' . $action . '|sec', 'nonce' ), -12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			return 1;
		}

		// Nonce generated 12-24 hours ago.
		$expected = substr( wp_hash( 'ir|' . ( $i - 1 ) . '|' . $action . '|sec', 'nonce' ), -12, 10 );
		if ( hash_equals( $expected, $nonce ) ) {
			return 2;
		}

		return false;
	}
}
