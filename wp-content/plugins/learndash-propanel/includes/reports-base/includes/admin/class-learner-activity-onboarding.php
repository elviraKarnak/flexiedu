<?php
/**
 * This file contains the class learner activity onboarding functions.
 *
 * @deprecated 1.8.2 The file is no longer used because the promotion ended.
 *
 * @package LearnDash\Reports
 */

namespace WisdmReportsLearndash;

defined( 'ABSPATH' ) || exit;

/**
 * Learner Activity tracking onboarding login
 *
 * @since 1.4.1
 * @deprecated 1.8.2 The class is no longer used because the promotion ended.
 */
class Learner_Activity_Onboarding {
	/**
	 * Description message
	 *
	 * @var $instance comment about this variable.
	 *
	 * This is single instance of a class.
	 */
	protected static $instance = null;
	/**
	 * This function has been used to create the static instance of the admin functions class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * This is the constructor for the class that will instantiate all processes related to the class.
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * This method is used to add all the admin related actions and filter hooks.
	 */
	public function init_hooks() {
		// add_action( 'init', array( $this, 'wrld_user_activity_onboarding_dismiss_logic' ) );
		// add_action( 'admin_notices', array( $this, 'wrld_user_activity_onboarding_logic' ) );
		// add_action( 'init', array( $this, 'wrld_course_progress_onboarding_dismiss_logic' ) );
		// add_action( 'admin_notices', array( $this, 'wrld_course_progress_onboarding_logic' ) );
	}

	/**
	 * Returns html of banners.
	 *
	 * @since 1.4.1
	 */
	public function wrld_user_activity_onboarding_logic() {
		if ( ! defined( 'LDRP_PLUGIN_VERSION' ) ) {
			return; // pro version is not active.
		}

		$plugin_first_activation = get_option( 'wrld_free_plugin_first_activated', false );

		if ( ! $plugin_first_activation ) {
			return;
		}
		$wrld_page = get_option( 'ldrp_reporting_page', false );

		if ( empty( $wrld_page ) || 'publish' !== get_post_status( $wrld_page ) ) {
			return;
		}

		$page_link = get_edit_post_link( $wrld_page );
		if ( false === $wrld_page || null === $page_link ) {
			$action_text = __( 'Create ProPanel Dashboard', 'learndash-reports-pro' );
			$page_link   = add_query_arg( array( 'create_wisdm_reports' => true ) );
		}
		$page_link = $page_link . '&dla_om=true';
		if ( ! get_option( 'wrld_learner_activity_notice_dismiss' ) ) {
			$this->show_onboarding_notice( $page_link );
		}
		if ( ! get_option( 'wrld_learner_activity_modal_dismiss' ) ) {
			$this->show_onboarding_modal( $page_link );
		}
	}

	/**
	 * Returns html of banners.
	 *
	 * @since 1.4.1
	 */
	public function wrld_course_progress_onboarding_logic() {
		if ( ! defined( 'LDRP_PLUGIN_VERSION' ) ) {
			return; // pro version is not active.
		}

		$plugin_first_activation = get_option( 'wrld_free_plugin_first_activated', false );

		if ( ! $plugin_first_activation ) {
			return;
		}
		$wrld_page = get_option( 'ldrp_reporting_page', false );

		if ( empty( $wrld_page ) || 'publish' !== get_post_status( $wrld_page ) ) {
			return;
		}

		$page_link = get_edit_post_link( $wrld_page );
		if ( false === $wrld_page || null === $page_link ) {
			$action_text = __( 'Create ProPanel Dashboard', 'learndash-reports-pro' );
			$page_link   = add_query_arg( array( 'create_wisdm_reports' => true ) );
		}
		$page_link = $page_link . '&dla_op=true';
		if ( ! get_option( 'wrld_course_progress_notice_dismiss' ) ) {
			$this->show_course_progress_onboarding_notice( $page_link );
		}
		if ( ! get_option( 'wrld_course_progress_modal_dismiss' ) ) {
			$this->show_course_progress_onboarding_modal( $page_link );
		}
	}

	/**
	 * Returns html of banners.
	 *
	 * @since 1.4.1
	 * @deprecated 3.0.0
	 *
	 * @return void
	 */
	public function wrld_marketing_december_campaign() {
		_deprecated_function( __METHOD__, '3.0.0' );
	}

	/**
	 * Returns html of banners.
	 *
	 * @since 1.4.1
	 */
	public function wrld_user_activity_onboarding_dismiss_logic() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			$get_data = filter_var_array( $_GET, FILTER_UNSAFE_RAW ); // phpcs:ignore WordPress.Security.NonceVerification
			if ( isset( $get_data['dla_om'] ) ) {
				if ( $get_data['dla_om'] ) {
					update_option( 'wrld_learner_activity_modal_dismiss', true );
				}
			}

			if ( isset( $get_data['dla_on'] ) ) {
				if ( $get_data['dla_on'] ) {
					update_option( 'wrld_learner_activity_notice_dismiss', true );
				}
			}
		}
	}

	public function wrld_course_progress_onboarding_dismiss_logic() {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			$get_data = filter_var_array( $_GET, FILTER_UNSAFE_RAW ); // phpcs:ignore WordPress.Security.NonceVerification
			if ( isset( $get_data['dla_oq'] ) ) {
				if ( $get_data['dla_oq'] ) {
					update_option( 'wrld_course_progress_modal_dismiss', true );
				}
			}

			if ( isset( $get_data['dla_op'] ) ) {
				if ( $get_data['dla_op'] ) {
					update_option( 'wrld_course_progress_notice_dismiss', true );
				}
			}
		}
	}

	/**
	 * Handles the dismiss logic for the marketing campaign.
	 *
	 * @deprecated 3.0.0
	 *
	 * @return void
	 */
	public function wrld_marketing_december_dismiss_logic() {
		_deprecated_function( __METHOD__, '3.0.0' );
	}

	/**
	 * Helper function to show onboarding notice.
	 *
	 * @since 1.4.1
	 * @deprecated 3.0.0
	 *
	 * @return void
	 */
	public function show_marketing_notice() {
		_deprecated_function( __METHOD__, '3.0.0' );
	}

	/**
	 * Helper function to show onboarding modal.
	 *
	 * @since 1.4.1
	 * @deprecated 3.0.0
	 *
	 * @return void
	 */
	public function show_marketing_modal() {
		_deprecated_function( __METHOD__, '3.0.0' );
	}

	/**
	 * Helper function to shoe onboarding notice.
	 *
	 * @param string $page_link This is the start date of the pre BFCM Sale.
	 *
	 * @since 1.4.1
	 */
	public function show_onboarding_notice( $page_link ) {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			$page_link = $page_link . '&dla_on=true';

			global $wp;
			$current_url = get_permalink( add_query_arg( array(), $wp->request ) );
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_learner_activity_onboarding_style', WRLD_REPORTS_SITE_URL . '/assets/css/learner-activity-onboarding.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_learner_activity_onboarding_style', WRLD_REPORTS_SITE_URL . '/assets/css/learner-activity-onboarding.css', array(), LDRP_PLUGIN_VERSION );
			}
			$banner_head          = __( 'Introducing Learner Activity Reports ', 'learndash-reports-pro' );
			$banner_message       = __( 'We have Introduced two new Gutenberg blocks : Inactive user list and Learner Activity Log  so that you can understand and take actions to improve the learners engagement in the course', 'learndash-reports-pro' );
			$banner_message_addon = __(
				'Update your ProPanel dashboard now!
			',
				'learndash-reports-pro'
			);
			$button_text          = __( 'UPGRADE NOW', 'learndash-reports-pro' );
			$link                 = 'https://www.learndash.com/reports-by-learndash';
			$wisdm_logo           = WRLD_REPORTS_SITE_URL . '/assets/images/learndash.png';
			$background           = WRLD_REPORTS_SITE_URL . '/assets/images/bfcm-right-during.png';
			$dismiss_attribute    = add_query_arg( array( 'wdm_banner_type' => 'upgrade' ), $current_url );
			include WRLD_REPORTS_PATH . '/includes/templates/learner-activity-onboarding.php';
		}
	}

	/**
	 * Helper function to shoe onboarding notice.
	 *
	 * @param string $page_link This is the start date of the pre BFCM Sale.
	 *
	 * @since 1.4.1
	 */
	public function show_course_progress_onboarding_notice( $page_link ) {
		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			$page_link = $page_link . '&dla_op=true';

			global $wp;
			$current_url = get_permalink( add_query_arg( array(), $wp->request ) );
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_learner_activity_onboarding_style', WRLD_REPORTS_SITE_URL . '/assets/css/learner-activity-onboarding.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_learner_activity_onboarding_style', WRLD_REPORTS_SITE_URL . '/assets/css/learner-activity-onboarding.css', array(), LDRP_PLUGIN_VERSION );
			}
			$banner_head          = __( 'Introducing Course Progress Reports ', 'learndash-reports-pro' );
			$banner_message       = __( 'We have introduced a new Gutenberg graph : Course Progress so that you can understand and take actions to improve the learners progress in the course', 'learndash-reports-pro' );
			$banner_message_addon = __(
				'Update your ProPanel dashboard now!
			',
				'learndash-reports-pro'
			);
			$button_text          = __( 'UPGRADE NOW', 'learndash-reports-pro' );
			$link                 = 'https://www.learndash.com/reports-by-learndash';
			$wisdm_logo           = WRLD_REPORTS_SITE_URL . '/assets/images/learndash.png';
			$background           = WRLD_REPORTS_SITE_URL . '/assets/images/bfcm-right-during.png';
			$dismiss_attribute    = add_query_arg( array( 'wdm_banner_type' => 'upgrade' ), $current_url );
			include WRLD_REPORTS_PATH . '/includes/templates/course-progress-onboarding.php';
		}
	}

	/**
	 * Helper function to shoe onboarding modal.
	 *
	 * @param string $page_link This is the start date of the pre BFCM Sale.
	 *
	 * @since 1.4.1
	 */
	public function show_onboarding_modal( $page_link ) {
		$screen = get_current_screen();

		if ( ! empty( $screen ) && 'plugins' !== $screen->base && 'update' !== $screen->base ) {
			return; // not on admin plugins page.
		}

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			// show modal asking for update.
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_learner_activity_modal', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-modal-learner-activity.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_learner_activity_modal', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-modal-learner-activity.css', array(), LDRP_PLUGIN_VERSION );
			}
			wp_enqueue_script( 'wrld_modal_la_script', WRLD_REPORTS_SITE_URL . '/assets/js/wrld-modal-learner-activity.js', array( 'jquery' ), LDRP_PLUGIN_VERSION );
			$modal_head = __(
				'Introducing Learner Activity Reports
			',
				'learndash-reports-pro'
			);

			$info_url          = '#';
			$modal_action_text = __( 'Got It!', 'learndash-reports-pro' );
			$action_close      = 'update-pro';
			// cspell:disable-next-line .
			$wp_nonce     = wp_create_nonce( 'reports-firrst-install-modal' );
			$dismiss_link = add_query_arg( array( 'dla_om' => true ) );

			include_once WRLD_REPORTS_PATH . '/includes/templates/admin-modal-learner-activity.php';
		}
	}

	/**
	 * Helper function to shoe onboarding modal.
	 *
	 * @param string $page_link This is the start date of the pre BFCM Sale.
	 *
	 * @since 1.4.1
	 */
	public function show_course_progress_onboarding_modal( $page_link ) {
		$screen = get_current_screen();

		if ( ! empty( $screen ) && 'plugins' !== $screen->base && 'update' !== $screen->base ) {
			return; // not on admin plugins page.
		}

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			// show modal asking for update.
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_learner_activity_modal', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-modal-learner-activity.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_learner_activity_modal', WRLD_REPORTS_SITE_URL . '/assets/css/wrld-modal-learner-activity.css', array(), LDRP_PLUGIN_VERSION );
			}
			wp_enqueue_script( 'wrld_modal_la_script', WRLD_REPORTS_SITE_URL . '/assets/js/wrld-modal-learner-activity.js', array( 'jquery' ), LDRP_PLUGIN_VERSION );
			$modal_head = __(
				'Introducing Course Progress Reports
			',
				'learndash-reports-pro'
			);

			$info_url          = '#';
			$modal_action_text = __( 'Got It!', 'learndash-reports-pro' );
			$action_close      = 'update-pro';
			// cspell:disable-next-line .
			$wp_nonce     = wp_create_nonce( 'reports-firrst-install-modal' );
			$dismiss_link = add_query_arg( array( 'dla_oq' => true ) );

			include_once WRLD_REPORTS_PATH . '/includes/templates/admin-modal-course-progress.php';
		}
	}
}

\WisdmReportsLearndash\Learner_Activity_Onboarding::instance();
