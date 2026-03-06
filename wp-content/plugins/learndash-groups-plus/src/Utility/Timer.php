<?php
/**
 * Timer Utility.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore WPINC usermeta umeta atts
 */

namespace LearnDash\Groups_Plus\Utility;

use WP_Post;
use WP_Post_Type;
use WP_User;
use LDLMS_Post_Types;
use LearnDash\Core\Utilities\Cast;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Timer
 */
class Timer {
	/**
	 * Array of post types that support time tracking.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string>
	 */
	public static $timed_post_types = [ 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ];

	/**
	 * Class constructor
	 */
	public function __construct() {
		self::run_frontend_hooks();
	}

	/**
	 * Initialize frontend actions and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function run_frontend_hooks() {
		add_action( 'wp_ajax_timer_entry', [ __CLASS__, 'timer_entry' ] );

		// Store timer when course completion get triggered.
		add_action( 'learndash_course_completed', [ __CLASS__, 'course_completed_store_timer' ] );

		// Return time spent on post( course, lesson, topic).
		add_shortcode( 'time', [ __CLASS__, 'shortcode_time' ] );

		// Return total time spent on all the courses enrolled.
		add_shortcode( 'total_time', [ __CLASS__, 'shortcode_total_time' ] );
	}

	/**
	 * Timer entry AJAX callback.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function timer_entry() {
		global $wpdb;
		$return_object = [];

		if ( ! is_user_logged_in() ) {
			$return_object['success']             = false;
			$return_object['message']             = 'You must be logged in.';
			$return_object['fields']['course_id'] = $_GET['course_id'];
			$return_object['fields']['post_id']   = $_GET['post_id'];

			echo json_encode( $return_object );
			exit;
		}

		$course_ID = absint( $_POST['course_id'] );
		$post_ID   = absint( $_POST['post_id'] );
		$user_id   = get_current_user_id();

		if ( 0 === $post_ID ) {
			$return_object['success']             = false;
			$return_object['message']             = 'One or more the the fields did not validate as a absolute integer.';
			$return_object['fields']['course_id'] = $_GET['course_id'];
			$return_object['fields']['post_id']   = $_GET['post_id'];

			echo json_encode( $return_object );
			exit;
		}

		$meta_key = 'timer_' . $course_ID . '_' . $post_ID;

		$q = "SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = '{$meta_key}' AND user_id = {$user_id}";

		// get var instead of get user meta
		$timer = $wpdb->get_var( $q );

		// $return_object['$timer'] = $timer;

		// Set timer default
		if ( empty( $timer ) ) {
			$timer = 0;
			// create user meta since it doesn't exist and we need a unique to update
			$wpdb->insert(
				$wpdb->usermeta,
				[
					'user_id'    => $user_id,
					'meta_key'   => $meta_key,
					'meta_value' => 0,
				],
				[
					'%d',
					'%s',
					'%d',
				]
			);
		}

		$q              = "SELECT umeta_id FROM $wpdb->usermeta WHERE meta_key = '{$meta_key}' AND user_id = {$user_id}";
		$timer_umeta_id = $wpdb->get_var( $q );

		// $return_object['$timer_umeta_id'] = $timer_umeta_id;

		// Amount of time added every call // JS interval must match
		$timer_interval = 10;

		// Set timer default, minimum is 10 seconds
		if ( '' === $timer_interval || (int) $timer_interval < 5 ) {
			$timer_interval = 15;
		}

		$timer += $timer_interval;

		$q = "UPDATE {$wpdb->usermeta} SET meta_value = {$timer} WHERE umeta_id = {$timer_umeta_id}";
		$wpdb->query( $q );

		$return_object['success'] = true;
		$return_object['time']    = $timer;

		echo json_encode( $return_object );
		exit;
	}

	private static function convert_second_to_time( $second ) {
		if ( ! $second ) {
			return '00:00:00';
		}

		$hours   = floor( $second / 3600 );
		$second -= $hours * 3600;

		$minutes = floor( $second / 60 );
		$second -= $minutes * 60;

		if ( $hours < 10 ) {
			$hours = '0' . $hours;
		}
		if ( $minutes < 10 ) {
			$minutes = '0' . $minutes;
		}
		if ( $second < 10 ) {
			$second = '0' . $second;
		}

		$total_time = ( $hours ) ? $hours . ':' : '';
		$total_time = ( $minutes ) ? $total_time . $minutes . ':' : $total_time . '00:';
		$total_time = ( $second ) ? $total_time . $second : $total_time . '00';

		return $total_time;
	}

	/**
	 * Gets the total time spent on a post.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post|null $post_object Post object or null.
	 * @param int          $user_id     User ID.
	 *
	 * @return string Total time in HH:MM:SS format.
	 */
	public static function get_time_spent( $post_object, $user_id ) {
		$user_id = Cast::to_int( $user_id );

		if (
			! $user_id
			|| ! $post_object instanceof WP_Post
		) {
			return self::convert_second_to_time( 0 );
		}

		// Course: aggregate all content times.
		if ( $post_object->post_type === learndash_get_post_type_slug( LDLMS_Post_Types::COURSE ) ) {
			return self::convert_second_to_time( self::get_course_time_in_seconds( $post_object->ID, $user_id ) );
		}

		// Handle steps aggregation.
		if ( in_array( $post_object->post_type, self::$timed_post_types, true ) ) {
			return self::convert_second_to_time( self::get_step_time_in_seconds( $post_object->ID, $user_id ) );
		}

		return '';
	}

	/**
	 * Gets the total time spent on a course.
	 *
	 * @since 1.0.0
	 *
	 * @param int $course_id Course ID.
	 * @param int $user_ID   User ID.
	 *
	 * @return int Total time in seconds.
	 */
	private static function get_course_time_in_seconds( $course_id, $user_ID ) {
		global $wpdb;

		$completed_on_meta_key = 'timer_' . learndash_get_course_id( $course_id ) . '_%';

		$query_results = $wpdb->prepare( "SELECT SUM( meta_value ) FROM $wpdb->usermeta WHERE meta_key LIKE %s AND user_id = %d", $completed_on_meta_key, $user_ID );

		$timer_results = $wpdb->get_var( $query_results );

		return $timer_results;
	}

	/**
	 * Gets the total time spent on a step including all its child steps recursively.
	 *
	 * This function works for all LearnDash step types:
	 * - Lessons: includes topics + topic quizzes + lesson quizzes
	 * - Topics: includes topic quizzes
	 * - Quizzes: returns only quiz time (no children)
	 *
	 * @since 2.1.5
	 *
	 * @param int $step_id Step ID.
	 * @param int $user_ID User ID.
	 *
	 * @return int Total time in seconds.
	 */
	private static function get_step_time_in_seconds( int $step_id, int $user_ID ): int {
		global $wpdb;

		$course_id = learndash_get_course_id( $step_id );

		if ( ! is_int( $course_id ) ) {
			return 0; // Step is not associated with a course.
		}

		// Get time spent on the step itself.
		$step_meta_key = 'timer_' . $course_id . '_' . $step_id;
		$step_time     = Cast::to_int( get_user_meta( $user_ID, $step_meta_key, true ) );

		// Get all child steps recursively.
		$child_step_ids = learndash_course_get_children_of_step( $course_id, $step_id, '', 'ids', true );

		$children_time = 0;

		if (
			! empty( $child_step_ids )
			&& is_array( $child_step_ids )
		) {
			// Build the meta keys for all child steps.
			$child_meta_keys = array_map(
				fn( $child_id ) => 'timer_' . $course_id . '_' . $child_id,
				$child_step_ids
			);

			// Get sum of time for all child steps using a single query.
			$placeholders = implode( ', ', array_fill( 0, count( $child_meta_keys ), '%s' ) );
			$query        = $wpdb->prepare(
				"SELECT SUM( meta_value ) FROM $wpdb->usermeta WHERE meta_key IN ($placeholders) AND user_id = %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- We are using a prepared query.
				array_merge( $child_meta_keys, [ $user_ID ] )
			);

			$children_time = Cast::to_int( $wpdb->get_var( $query ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- We are using a prepared query.
		}

		return $step_time + $children_time;
	}

	/**
	 * Stores the time spent on a course when the course is completed.
	 *
	 * @since 1.0.0
	 *
	 * @param array{user: bool|WP_User, course: WP_Post} $data Data.
	 *
	 * @return void
	 */
	public static function course_completed_store_timer( $data ) {
		if ( ! $data['user'] instanceof WP_User ) {
			return; // User is not defined.
		}

		$user_ID    = $data['user']->ID;
		$course     = $data['course'];
		$meta_key   = 'course_timer_completed_' . $course->ID;
		$meta_value = self::get_time_spent( $course, $user_ID );

		update_user_meta( $user_ID, $meta_key, $meta_value );
	}

	/**
	 * Renders the time spent on a course. It's the [time] shortcode.
	 *
	 * @param array<string, int|string> $attributes Shortcode attributes.
	 *
	 * @return string
	 */
	public static function shortcode_time( $attributes ) {
		$request = shortcode_atts(
			[
				'user-id'   => '',
				'course-id' => '',
			],
			$attributes
		);

		if ( '' === $request['user-id'] ) {
			$request['user-id'] = get_current_user_id();
		}

		$user_ID = absint( $request['user-id'] );
		$post_ID = absint( $request['course-id'] );

		$user_ID = self::protect_user( $user_ID );

		if ( 0 === $user_ID ) {
			return '';
		}

		if ( '' == $request['course-id'] ) {
			global $post;

			if ( ( ! is_404() && ! is_archive() && is_a( $post, 'WP_Post' ) ) ) {
				$course_post = get_post( learndash_get_course_id( $post->ID ) );

				if (
					$course_post instanceof WP_Post
					&& ! self::can_current_user_access_post( $course_post->ID )
				) {
					return '';
				}

				return self::get_time_spent( $course_post, $user_ID );
			}
		} elseif ( $post_ID ) {
			if ( ! self::can_current_user_access_post( $post_ID ) ) {
				return '';
			}

			$requested_post = get_post( $post_ID );

			return self::get_time_spent( $requested_post, $user_ID );
		}

		return '';
	}

	/**
	 * Renders the total time spent on all the courses enrolled by the user.
	 * It's the [total_time] shortcode.
	 *
	 * @param array<string, int|string> $attributes Shortcode attributes.
	 *
	 * @return string
	 */
	public static function shortcode_total_time( $attributes ) {
		$request = shortcode_atts(
			[
				'user-id' => '',
			],
			$attributes
		);

		if ( '' === $request['user-id'] ) {
			$request['user-id'] = get_current_user_id();
		}

		$request['user-id'] = self::protect_user(
			intval( $request['user-id'] )
		);

		if ( 0 === $request['user-id'] ) {
			return '';
		}

		$enrolled_courses = learndash_user_get_enrolled_courses( $request['user-id'] );
		$total_time       = 0;
		foreach ( $enrolled_courses as $enrolled_course ) {
			$course_cumulative_time = self::get_course_time_in_seconds( $enrolled_course, $request['user-id'] );
			if ( ! empty( $course_cumulative_time ) ) {
				$total_time = $total_time + $course_cumulative_time;
			}
		}

		return self::convert_second_to_time( $total_time );
	}

	/**
	 * Checks if the current user can access the post.
	 *
	 * If the post ID is not set, the current user can access the post. It allows the shortcode's optional parameters to be skipped.
	 * If the post is password protected, then only admins can access the post.
	 * If the current user is a guest, the user can only access published posts.
	 * If the current user is logged in, they can only access posts they have access to.
	 *
	 * @since 2.1.1
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return bool
	 */
	private static function can_current_user_access_post( int $post_id ): bool {
		// We have to duplicate the learndash_shortcode_can_current_user_access_post function content,
		// because it's available since LearnDash Core version 4.11.0 only.
		if ( function_exists( 'learndash_shortcode_can_current_user_access_post' ) ) {
			return learndash_shortcode_can_current_user_access_post( $post_id );
		}

		/**
		 * If post ID is not set, assume the user can access it. It allows shortcode's optional parameters to be skipped.
		 *
		 * Some shortcodes have optional post IDs parameters, such as course_id, group_id, step_id, etc.
		 * This check allows us to pass the post IDs without checking if they're set.
		 *
		 * See includes/shortcodes/ld_course_content.php for example. Users can pass course_id, group_id, and post_id, but they're optional.
		 */

		if ( $post_id <= 0 ) {
			return true;
		}

		$current_user_id = get_current_user_id();

		// Admins can access any post.

		if ( learndash_is_admin_user( $current_user_id ) ) {
			return true;
		}

		// Only admins can access password protected posts.

		if ( post_password_required( $post_id ) ) {
			return false;
		}

		// If guest user, check if the post is published.

		if ( $current_user_id <= 0 ) {
			return get_post_status( $post_id ) === 'publish';
		}

		// If logged in user, check if the user has access to the post.

		$post_type_object = get_post_type_object(
			(string) get_post_type( $post_id )
		);

		return (
			$post_type_object instanceof WP_Post_Type
			&& user_can( $current_user_id, $post_type_object->cap->read_post, $post_id )
		);
	}

	/**
	 * Protects a user ID from being accessed by other users.
	 *
	 * If the current user is not logged in, then the user ID is set to 0.
	 * If the current user is an admin, then they can access any user.
	 * If the current user is a group leader, then they can only access users in their group.
	 * If the current user is not an admin or group leader, then they can only access themselves.
	 * Otherwise, the user ID is set to 0.
	 *
	 * @since 2.1.1
	 *
	 * @param int $user_id User ID.
	 *
	 * @return int The user ID.
	 */
	private static function protect_user( int $user_id ): int {
		// We have to duplicate the learndash_shortcode_protect_user function content,
		// because it's available since LearnDash Core version 4.11.0 only.
		if ( function_exists( 'learndash_shortcode_protect_user' ) ) {
			return learndash_shortcode_protect_user( $user_id );
		}

		if ( ! is_user_logged_in() ) {
			return 0;
		}

		$current_user_id = get_current_user_id();

		// If the current user is an admin, then they can access any user.
		if ( learndash_is_admin_user( $current_user_id ) ) {
			return $user_id;
		}

		// If the current user is a group leader, then they can only access users in their group
		// or everyone if the advanced setting is enabled.
		if (
			learndash_is_group_leader_user( $current_user_id )
			&& (
				learndash_get_group_leader_manage_users() === 'advanced'
				|| learndash_is_group_leader_of_user( $current_user_id, $user_id )
			)
		) {
			return $user_id;
		}

		return $current_user_id === $user_id
			? $user_id
			: 0;
	}
}
