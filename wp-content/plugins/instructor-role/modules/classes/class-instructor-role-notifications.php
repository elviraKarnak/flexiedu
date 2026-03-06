<?php
/**
 * Notifications Module
 *
 * @since 3.3.0
 *
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Notifications' ) ) {
	/**
	 * Class Instructor Role Notifications Module
	 */
	class Instructor_Role_Notifications {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

		/**
		 * Plugin slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 3.3.0
		 */
		protected $plugin_slug = '';

		/**
		 * Constructor
		 *
		 * @since 3.3.0
		 */
		public function __construct() {
			$this->plugin_slug = INSTRUCTOR_ROLE_TXT_DOMAIN;
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 3.3.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Allow access to notifications to instructors
		 *
		 * @param array $allowed_data   List of allowed data types to instructors.
		 * @return array                Updated list of allowed data types.
		 *
		 * @since 3.3.0
		 */
		public function allow_notifications_access( $allowed_data ) {
			$allowed_data[] = 'ld-notification';
			return $allowed_data;
		}

		/**
		 * Enable access to notifications screen.
		 *
		 * @since 3.3.0
		 *
		 * @param array $sub_menu
		 */
		public function enable_notifications_screen_access( $sub_menu ) {
			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $sub_menu;
			}

			// Check if instructor has access.
			if ( ! array_key_exists( 'ld-notifications', $sub_menu ) ) {
				$sub_menu['ld-notifications'] = [
					'name' => __( 'Notifications', 'wdm_instructor_role' ),
					'cap'  => 'instructor_page',
					'link' => 'edit.php?post_type=ld-notification',
				];
			}

			return $sub_menu;
		}

		/**
		 * Override admin privileges to fetch notification details for instructors.
		 *
		 * @param array  $all_caps       All capabilities for the user.
		 * @param array  $requested_caps Requested capabilities to perform the action.
		 * @param array  $args           Additional arguments.
		 * @param object $user           Current WP_User object instance.
		 *
		 * @return array   $all_caps       Updated list of all capabilities.
		 */
		public function override_notification_privileges_for_instructors( $all_caps, $requested_caps, $args, $user ) {
			if ( empty( $_POST ) ) {
				return $all_caps;
			}

			// Check nonce.
			if ( ! array_key_exists( 'nonce', $_POST ) || ! wp_verify_nonce( $_POST['nonce'], 'ld_notifications_nonce' ) ) {
				return $all_caps;
			}

			// Check the action.
			if ( ! array_key_exists( 'action', $_POST ) || 'ld_notifications_get_children_list' != $_POST['action'] ) {
				return $all_caps;
			}

			// Check if instructor.
			if ( ! wdm_is_instructor( $user->ID ) ) {
				return $all_caps;
			}

			// Override instructor privileges for momentary access.
			if ( in_array( 'manage_options', $requested_caps ) ) {
				$all_caps['manage_options'] = 1;
			}

			return $all_caps;
		}

		/**
		 * Add instructor in the list of recipients.
		 *
		 * @param array $recipients     List of recipients.
		 *
		 * @return array                   Updated list of recipients.
		 *
		 * @since 3.3.0
		 */
		public function add_instructor_in_recipients_list( $recipients ) {
			// Add instructor as a recipient.
			if ( ! in_array( 'ir_instructor', $recipients ) ) {
				$recipients['ir_instructor'] = __( 'Instructor', 'wdm_instructor_role' );
			}
			return $recipients;
		}

		/**
		 * Add instructor email in recipients emails
		 *
		 * @param array $emails     Returned email addresses
		 * @param array $recipients Recipients type of a notification
		 * @param int   $user_id    User ID which trigger a notification
		 * @param int   $course_id  Course ID which trigger a notification
		 *
		 * @return array $emails    Updated email addresses.
		 *
		 * @since 3.3.0
		 */
		public function add_instructor_email_in_recipients_emails( $emails, $recipients, $user_id, $course_id ) {
			foreach ( $recipients as $recipient ) {
				switch ( $recipient ) {
					case 'ir_instructor':
						$course = get_post( $course_id );

						// Is empty course.
						if ( empty( $course ) ) {
							break;
						}

						$course_author = $course->post_author;

						// If instructor, then add email.
						if ( wdm_is_instructor( $course_author ) ) {
							$user_data = get_userdata( $course_author );
							$emails[]  = $user_data->user_email;
						}

						// If shared course, then add co-instructor emails.
						$co_instructors_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
						if ( ! empty( $co_instructors_list ) ) {
							$co_instructors = explode( ',', $co_instructors_list );
							foreach ( $co_instructors as $instructor_id ) {
								$instructor_details = get_userdata( $instructor_id );
								$emails[]           = $instructor_details->user_email;
							}
						}
						break;
				}
			}
			return $emails;
		}

		/**
		 * Update instructor notification settings to include only instructor courses and remove 'all' option from settings.
		 *
		 * @param array $notification_settings      Array of LD notification settings.
		 * @return array                            Updated array of LD notification settings.
		 *
		 * @since 3.5.0
		 */
		public function update_instructor_notification_settings( $notification_settings ) {
			if ( ! wdm_is_instructor() ) {
				return $notification_settings;
			}

			// First lets remove the 'all' option from the settings options.
			$notification_option_keys = [
				'group_id',
				'course_id',
				'lesson_id',
				'topic_id',
				'quiz_id',
			];
			foreach ( $notification_option_keys as $key ) {
				unset( $notification_settings[ $key ]['value']['all'] );
			}

			// Get current instructor courses.
			$instructor_course_ids = ir_get_instructor_complete_course_list();

			// Update notification settings for list of course ids.
			$instructor_course_values = [
				'' => __( '-- Select %s --', 'wdm_instructor_role' ),
			];

			foreach ( $instructor_course_ids as $course_id ) {
				$instructor_course_values[ $course_id ] = get_the_title( $course_id );
			}
			$notification_settings['course_id']['value'] = $instructor_course_values;

			return $notification_settings;
		}

		/**
		 * Allow access to the learndash notifications post types for fetching different notifications before sending them for instructors
		 *
		 * @param array $excluded_post_types    List of excluded post types.
		 */
		public function allow_notification_post_type_access( $excluded_post_types ) {
			global $current_screen;

			// Check if not notification listing screen.
			if ( empty( $current_screen ) || 'edit-ld-notification' != $current_screen->id ) {
				$excluded_post_types[] = 'ld-notification';
			}
			return $excluded_post_types;
		}
	}
}
