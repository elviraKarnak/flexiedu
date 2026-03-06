<?php
/**
 * Reports base module.
 *
 * @since 3.0.0
 *
 * @package LearnDash\Reports
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WRLD_REPORTS_FILE' ) ) {
	define( 'WRLD_REPORTS_FILE', __FILE__ );
}

if ( ! defined( 'WRLD_PLUGIN_VERSION' ) ) {
	/**
	 * The Free version of the plugin.
	 *
	 * This constant is no longer used.
	 *
	 * @deprecated 3.0.0. Use LDRP_PLUGIN_VERSION instead.
	 */
	define( 'WRLD_PLUGIN_VERSION', defined( 'LDRP_PLUGIN_VERSION' ) ? LDRP_PLUGIN_VERSION : '' );
}

// Constant for text domain.
if ( ! defined( 'WRLD_REPORTS_PATH' ) ) {
	define( 'WRLD_REPORTS_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'WRLD_REPORTS_SITE_URL' ) ) {
	/**
	 * The constant CSP_PLUGIN_SITE_URL contains the url path to the plugin directory
	 * eg. https://example.com/wp-content/plugins/block-sample/
	 */
	define( 'WRLD_REPORTS_SITE_URL', untrailingslashit( plugins_url( '/', WRLD_REPORTS_FILE ) ) );
}

if ( ! defined( 'WRLD_COURSE_TIME_FREQUENCY' ) ) {
	/**
	 * This constant defines the frequency at which the activity time is being saved in database.
	 */
	define( 'WRLD_COURSE_TIME_FREQUENCY', 30 ); // Define in seconds.
}

if ( ! defined( 'WRLD_COURSE_SESSION_TIMEOUT' ) ) {
	/**
	 * This constant defines the active session timeout for the current activity time-tracking.
	 */
	define( 'WRLD_COURSE_SESSION_TIMEOUT', 30 * MINUTE_IN_SECONDS ); // Define in seconds.
}

require_once __DIR__ . '/includes/functions.php';

if ( ! function_exists( 'generate_quiz_attempts' ) ) {
	function generate_quiz_attempts( $new_id, $old_id ) {
		global $wpdb;
		$user_time = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . "ld_time_entries WHERE user_id={$old_id}", ARRAY_A );
		if ( ! empty( $user_time ) ) {
			foreach ( $user_time as &$time ) {
				unset( $time['id'] );
				$time['user_id'] = $new_id;
				$new_user_time   = $wpdb->insert(
					$wpdb->prefix . 'ld_time_entries',
					$time
				);
			}
		}
		$usermeta = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->usermeta . " WHERE user_id={$old_id}", ARRAY_A );
		if ( ! empty( $usermeta ) ) {
			foreach ( $usermeta as &$meta ) {
				unset( $meta['umeta_id'] );
				$meta['user_id'] = $new_id;
				$new_usermeta    = $wpdb->insert(
					$wpdb->usermeta,
					$meta
				);
			}
		}
		$activities            = $wpdb->get_results( 'SELECT * FROM ' . \LDLMS_DB::get_table_name( 'user_activity' ) . " WHERE user_id={$old_id}", ARRAY_A );
		$new_activity_meta_ids = array();
		if ( ! empty( $activities ) ) {
			foreach ( $activities as &$activity ) {
				$old_activity_id = $activity['activity_id'];
				unset( $activity['activity_id'] );
				$activity['user_id'] = $new_id;
				$new_activity        = $wpdb->insert(
					\LDLMS_DB::get_table_name( 'user_activity' ),
					$activity
				);
				$new_activity_id     = $wpdb->insert_id;
				$activities_meta     = $wpdb->get_results( 'SELECT * FROM ' . \LDLMS_DB::get_table_name( 'user_activity_meta' ) . " WHERE activity_id={$old_activity_id}", ARRAY_A );
				if ( ! empty( $activities_meta ) ) {
					foreach ( $activities_meta as &$activity_meta ) {
						unset( $activity_meta['activity_meta_id'] );
						$activity_meta['activity_id'] = $new_activity_id;
						$new_activity_meta            = $wpdb->insert(
							\LDLMS_DB::get_table_name( 'user_activity_meta' ),
							$activity_meta
						);
						$new_activity_meta_ids[]      = $wpdb->insert_id;
					}
				}
			}
		}
		$statistic_refs = $wpdb->get_results( 'SELECT * FROM ' . \LDLMS_DB::get_table_name( 'quiz_statistic_ref' ) . " WHERE user_id={$old_id}", ARRAY_A );
		if ( ! empty( $statistic_refs ) ) {
			foreach ( $statistic_refs as $statistic_ref ) {
				$old_statistic_ref_id = $statistic_ref['statistic_ref_id'];
				unset( $statistic_ref['statistic_ref_id'] );
				$statistic_ref['user_id'] = $new_id;
				$new_statistic_ref_id     = $wpdb->insert(
					\LDLMS_DB::get_table_name( 'quiz_statistic_ref' ),
					$statistic_ref
				);
				$new_statistic_ref_id     = $wpdb->insert_id;
				$statistics               = $wpdb->get_results( 'SELECT * FROM ' . \LDLMS_DB::get_table_name( 'quiz_statistic' ) . " WHERE statistic_ref_id={$old_statistic_ref_id}", ARRAY_A );
				if ( ! empty( $statistics ) ) {
					foreach ( $statistics as &$statistic ) {
						$statistic['statistic_ref_id'] = $new_statistic_ref_id;
						$new_statistics                = $wpdb->insert(
							\LDLMS_DB::get_table_name( 'quiz_statistic' ),
							$statistic
						);
					}
				}
				$usermeta = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->usermeta . " WHERE user_id={$new_id} AND meta_key = '_sfwd-quizzes'", ARRAY_A );
				if ( ! empty( $usermeta ) ) {
					$meta_value = maybe_unserialize( $usermeta['meta_value'] );
					foreach ( $meta_value as &$value ) {
						if ( $value['statistic_ref_id'] == $old_statistic_ref_id ) {
							$value['statistic_ref_id'] = $new_statistic_ref_id;
						}
					}
					$usermeta['meta_value'] = serialize( $meta_value );
					$new_usermeta           = $wpdb->update(
						$wpdb->usermeta,
						$usermeta,
						array( 'umeta_id' => $usermeta['umeta_id'] )
					);
				}
				$activities_meta = $wpdb->get_results( 'SELECT * FROM ' . \LDLMS_DB::get_table_name( 'user_activity_meta' ) . ' WHERE activity_meta_id IN (' . implode( ',', $new_activity_meta_ids ) . ") AND activity_meta_key = 'statistic_ref_id' AND activity_meta_value={$old_statistic_ref_id}", ARRAY_A );
				if ( ! empty( $activities_meta ) ) {
					foreach ( $activities_meta as &$activity_meta ) {
						$activity_meta['activity_meta_value'] = $new_statistic_ref_id;
						$new_activity_meta                    = $wpdb->update(
							\LDLMS_DB::get_table_name( 'user_activity_meta' ),
							$activity_meta,
							array( 'activity_meta_id' => $activity_meta['activity_meta_id'] )
						);
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'generate_user' ) ) {
	function generate_user( $user_id ) {
		global $wpdb;
		$user = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}users WHERE ID={$user_id}", ARRAY_A );
		unset( $user['ID'] );
		// $last_id = $wpdb->get_var( "SELECT ID FROM {$wpdb->prefix}users DESC LIMIT 1;" );
		// $user['ID'] = $last_id + 1;
		$user['user_login'] .= bin2hex( random_bytes( 6 ) );
		$user['user_email'] .= bin2hex( random_bytes( 6 ) );
		$insert_id           = $wpdb->insert(
			$wpdb->users,
			$user
		);
		generate_quiz_attempts( $wpdb->insert_id, $user_id );
	}
}
