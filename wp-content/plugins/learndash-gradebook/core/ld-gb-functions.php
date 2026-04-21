<?php
/**
 * Basic, global functions.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */

defined( 'ABSPATH' ) || die();

use LearnDash\Core\Utilities\Cast;

/**
 * Returns the get_user() args for getting users for a Gradebook.
 *
 * @since 1.2.2
 * @updated 4.0.0
 *
 * @param int   $gradebook_ID
 * @param bool  $group Optional user group to retrieve from.
 * @param array $args Optional extra get_users() arguments.
 *
 * @return array Get user arguments.
 */
function ld_gb_get_gradebook_get_users_args( $gradebook_ID, $group = false, $args = [] ) {
	$override = apply_filters( 'ld_gb_get_gradebook_get_users_args_override', null, $gradebook_ID, $group, $args );

	if ( $override ) {
		return $override;
	}

	$user_args = wp_parse_args(
		$args,
		[
			'include' => [],
		]
	);

	if ( $group != false ) {
		$group_users_IDs = learndash_get_groups_user_ids( $group );

		$user_args['include'] = $group_users_IDs;

		// Cancel users if none in group
		if ( empty( $user_args['include'] ) ) {
			$user_args['include'] = [ 0 ];
		}
	} else {
		$only_actually_owned_groups = ld_gb_get_option_field( 'gradebook_non_group_leaders_show_only_group_users', false );

		$has_groups = get_posts(
			[
				'post_type'      => 'groups',
				'posts_per_page' => 1,
				'post_status'    => [ 'publish', 'pending', 'draft', 'future', 'private' ],
				'fields'         => 'ids',
			]
		);

		// If they are a Group Leader with no Groups, do not show them any Users
		// This will only apply if at least one Group exists
		if ( learndash_is_group_leader_user() && $has_groups ) {
			if ( empty( learndash_get_groups( true ) ) ) {
				$user_args['include'] = [ 0 ];
			}
		} elseif ( $has_groups && $only_actually_owned_groups && ! learndash_is_group_leader_user() ) {
			// The second parameter ensures it will only include Groups that this user is explicitly a Group Leader of
			$group_ids = learndash_get_administrators_group_ids( get_current_user_id(), true );

			$group_users_IDs = [];

			foreach ( $group_ids as $group_id ) {
				$group_users_IDs = array_unique( array_merge( $group_users_IDs, learndash_get_groups_user_ids( $group_id ) ) );
			}

			$user_args['include'] = $group_users_IDs;

			// Cancel users if no users in any of their Groups
			if ( empty( $user_args['include'] ) ) {
				$user_args['include'] = [ 0 ];
			}
		}
	}

	if ( $user_args['include'] == [ 0 ] ) {
		// We have already determined _not_ to show any Users. Don't proceed with the next calculations, which could override this value
		return $user_args;
	}

	$course_ids = ld_gb_get_field( 'course', $gradebook_ID );
	$course_ids = ( ! is_array( $course_ids ) ) ? [ $course_ids ] : $course_ids;
	$course_ids = array_filter( $course_ids );

	$all_users = ld_gb_get_field( 'include_all_users', $gradebook_ID );

	if ( $course_ids && $course_ids !== [ 'all' ] && $all_users !== '1' ) {
		// Best method to retrieve users who have the course as IN_PROGRESS or COMPLETED
		$activity_result = learndash_reports_get_activity(
			[
				'per_page'          => 0,
				'activity_types'    => '',
				'post_types'        => learndash_get_post_types(),
				'course_ids'        => $course_ids,
				'course_ids_action' => 'IN',
				'activity_status'   => [ 'IN_PROGRESS', 'COMPLETED' ],
			]
		);

		if ( $activity_result && ! empty( $activity_result['results'] ) ) {
			if ( $group ) {
				$user_args['include'] = array_values(
					array_intersect(
						wp_list_pluck( $activity_result['results'], 'user_id' ),
						$user_args['include']
					)
				);
			} else {
				$user_args['include'] = array_merge(
					$user_args['include'],
					wp_list_pluck( $activity_result['results'], 'user_id' )
				);
			}

			if ( empty( $user_args['include'] ) ) {
				// If showing All Users is not enabled and no Users have started any of the Course Content, exclude all Users from being shown
				// This is important to have here especially when viewing a Group, since if no members of the Group have started that Course Content, then it would otherwise default to showing All Users if a User outside of the Group had started the Course
				$user_args['include'] = [ 0 ];
			}
		} else {
			// If showing All Users is not enabled and no Users have started any of the Course Content, exclude all Users from being shown
			$user_args['include'] = [ 0 ];
		}

		$user_args['include'] = array_unique( $user_args['include'] );
	}

	return $user_args;
}

/**
 * Sanitizes a grade to be an int 0-100.
 *
 * @since 1.0.0
 *
 * @param $grade
 *
 * @return int|string
 */
function ld_gb_sanitize_grade( $grade ) {
	// Allow "%"
	$grade = str_replace( '%', '', $grade );

	// Don't allow non-ints
	if ( preg_match( '/\D/', $grade ) ) {
		return '';
	}

	// Keep it above 0
	// (in theory, preg_match would remove the negative sign, but this is just covering all bases)
	if ( (int) $grade < 0 ) {
		$grade = 0;
	}

	return $grade;
}

/**
 * Returns the component's name.
 *
 * @since 1.0.0
 *
 * @param string $component Component ID.
 *
 * @return string
 */
function ld_gb_get_grade_type_name( $component ) {
	switch ( $component ) {
		case 'quiz':
			$name = LearnDash_Custom_Label::get_label( 'quiz' );
			break;

		case 'assignment':
			$name = LearnDash_Custom_Label::get_label( 'assignment' );
			break;

		case 'lesson':
			$name = LearnDash_Custom_Label::get_label( 'lesson' );
			break;

		case 'topic':
			$name = LearnDash_Custom_Label::get_label( 'topic' );
			break;

		case 'manual':
			$name = _x( 'Manual Grade', 'Manual Grade Grade Type Label', 'learndash-gradebook' );
			break;

		default:
			$name = _x( 'Error: Invalid Grade Type', 'Invalid Grade Type error message', 'learndash-gradebook' );
			break;
	}

	/**
	 * Filter the returned component name.
	 *
	 * @since 1.0.0
	 */
	$name = apply_filters( 'ld_gb_component_name', $name, $component );

	return esc_attr( $name );
}

/**
 * Gets the default letter grade scale.
 *
 * @since 1.0.0
 *
 * @return array
 */
function ld_gb_get_default_letter_grade_scale() {
	return [
		[
			'grade'  => 97,
			'letter' => 'A+',
		],
		[
			'grade'  => 93,
			'letter' => 'A',
		],
		[
			'grade'  => 90,
			'letter' => 'A-',
		],
		[
			'grade'  => 87,
			'letter' => 'B+',
		],
		[
			'grade'  => 83,
			'letter' => 'B',
		],
		[
			'grade'  => 80,
			'letter' => 'B-',
		],
		[
			'grade'  => 77,
			'letter' => 'C+',
		],
		[
			'grade'  => 73,
			'letter' => 'C',
		],
		[
			'grade'  => 70,
			'letter' => 'C-',
		],
		[
			'grade'  => 67,
			'letter' => 'D+',
		],
		[
			'grade'  => 63,
			'letter' => 'D',
		],
		[
			'grade'  => 60,
			'letter' => 'D-',
		],
		[
			'grade'  => 0,
			'letter' => 'F',
		],
	];
}

/**
 * Gets the default grade scale styles.
 *
 * @since 1.0.0
 *
 * @return array
 */
function ld_gb_get_default_grade_color_scale() {
	return [
		[
			'grade' => 90,
			'color' => '#369e2e',
		],
		[
			'grade' => 80,
			'color' => '#809e1f',
		],
		[
			'grade' => 70,
			'color' => '#9e961c',
		],
		[
			'grade' => 60,
			'color' => '#9e5321',
		],
		[
			'grade' => 0,
			'color' => '#862a26',
		],
	];
}

/**
 * Determines if the two array of roles (one being the current users) has any intersections.
 *
 * @since 1.0.0
 *
 * @param array $roles Roles to check against.
 *
 * @return bool
 */
function ld_gb_current_user_match_roles( $roles ) {
	if ( ! ( $current_user = wp_get_current_user() ) ) {
		return false;
	}

	return array_intersect( $roles, $current_user->roles ) || false;
}

/**
 * Gets the grade statuses.
 *
 * @since 1.0.1
 *
 * @return mixed|void
 */
function ld_gb_get_grade_statuses() {
	/**
	 * Statuses a grade can have.
	 *
	 * @since 1.0.1
	 */
	$grade_statuses = apply_filters(
		'ld_gb_grade_statuses',
		[
			'incomplete' => [
				'label' => _x( 'Incomplete', 'Incomplete Grade Status label', 'learndash-gradebook' ),
				'score' => false,
			],
			'pending'    => [
				'label' => _x( 'Pending Approval', 'Pending Approval Grade Status label', 'learndash-gradebook' ),
				'score' => false,
			],
		]
	);

	return $grade_statuses;
}

/**
 * Returns a list of all Gradebook by LearnDash role capabilities.
 *
 * @since 1.1.0
 */
function ld_gb_get_capabilities() {
	$capabilities = [
		'administrator' => [
			'view_gradebook',
			// Gradebook Post Type
			'read_gradebook',
			'read_private_gradebooks',
			'publish_gradebooks',
			'edit_gradebook',
			'edit_gradebooks',
			'edit_private_gradebooks',
			'edit_published_gradebooks',
			'edit_other_gradebooks',
			'edit_others_gradebooks',
			'delete_gradebooks',
			'delete_private_gradebooks',
			'delete_published_gradebooks',
			'delete_others_gradebooks',
			'delete_gradebook',
		],
		'group_leader'  => [
			'view_gradebook',
			// Gradebook Post Type
			'read_gradebook',
			'read_private_gradebooks',
		],
	];

	/**
	 * Gradebook by LearnDash role capabilities.
	 *
	 * @since 1.2.0
	 */
	$capabilities = apply_filters( 'ld_gb_role_capabilities', $capabilities );

	return $capabilities;
}

/**
 * Grab the Roles with the Quickstart Guide enabled
 *
 * @since 1.4.0
 * @return array Roles with the Quickstart Guide enabled
 */
function ld_gb_get_quickstart_roles() {
	if ( ! ( $quickstart_roles = ld_gb_get_option_field( 'quickstart_roles' ) ) ) {
		$quickstart_roles = [];
	}

	// Merge roles that cannot have the quickstart guide be disabled to ones in the stored value.
	return array_unique( array_merge( $quickstart_roles, [ 'administrator', 'group_leader' ] ) );}

/**
 * Loads a template file from the theme if it exists, otherwise from the plugin.
 *
 * @since 1.1.0
 *
 * @param string $template_name Template file to load.
 * @param array  $args Arguments to extract for the template.
 */
function ld_gb_locate_template( $template_name, $args = [] ) {
	/**
	 * Filter the template to be located.
	 *
	 * @since 1.1.0
	 */
	$template_name = apply_filters( 'ld_gb_locate_template_name', $template_name, $args );

	/**
	 * Filter the args to use in the template.
	 *
	 * @since 1.1.0
	 */
	$args = apply_filters( 'ld_gb_locate_template_args', $args, $template_name );

	if ( $theme_file = locate_template( [ "/learndash/{$template_name}" ] ) ) {
		$template_file = $theme_file;
	} else {
		$template_file = LEARNDASH_GRADEBOOK_DIR . "templates/{$template_name}";
	}

	/**
	 * Filter the template file to be located.
	 *
	 * @since 2.0.0
	 */
	$template_file = apply_filters( 'ld_gb_locate_template', $template_file, $template_name, $args );

	extract( $args );

	include $template_file;}

/**
 * Returns a float ceil at specified precision.
 *
 * @since 1.1.0
 *
 * @param int $number Number to round.
 * @param int $precision Precision to round to.
 *
 * @return float
 */
function ld_gb_ceil_precision( $number, $precision ) {
	$coefficient = pow( 10, $precision );

	return ceil( $number * $coefficient ) / $coefficient;
}

/**
 * Returns a float floor at specified precision.
 *
 * @since 1.1.0
 *
 * @param int $number Number to round.
 * @param int $precision Precision to round to.
 *
 * @return float
 */
function ld_gb_floor_precision( $number, $precision ) {
	$coefficient = pow( 10, $precision );

	return floor( $number * $coefficient ) / $coefficient;
}

/**
 * Rounds a grade based on args.
 *
 * @since 1.1.0
 *
 * @param int    $grade Grade to round.
 * @param int    $precision Decimal points out to round to.
 * @param string $round Type of rounding.
 */
function ld_gb_round_grade( $grade, $precision = 0, $round = 'ceil' ) {
	switch ( $round ) {
		case 'ceil':
			$grade = ld_gb_ceil_precision( $grade, $precision );
			break;

		case 'floor':
			$grade = ld_gb_floor_precision( $grade, $precision );
			break;

		case 'round':
			$grade = round( $grade, $precision );
			break;
	}

	return $grade;
}

/**
 * Returns grade information for one user.
 *
 * @since 1.1.5
 *
 * @param int $user_ID ID of user to get grade for.
 * @param int $gradebook Gradebook to get grade for.
 *
 * @return array|bool Grade on success, false if no user.
 */
function ld_gb_get_user_grade( $user_ID, $gradebook ) {
	$user = new WP_User( $user_ID );

	if ( ! $user ) {
		return false;
	}

	// Allows you to override the returned value before anything is processed
	// Since the Gradebook is an expensive query, it is better to not do this twice if possible
	$override = apply_filters( 'ld_gb_get_user_grade', false, $user, $gradebook );

	if ( $override ) {
		return $override;
	}

	$user_grade = new LD_GB_UserGrade( $user->ID, $gradebook );

	$data = [
		'display_name' => $user->display_name ? $user->display_name : false,
		'first_name'   => ( $user->first_name ) ? $user->first_name : false,
		'last_name'    => ( $user->last_name ) ? $user->last_name : false,
		'user_email'   => ( $user->user_email ) ? $user->user_email : false,
		'user_login'   => ( $user->user_login ) ? $user->user_login : false,
		'grade'        => $user_grade->get_user_grade(),
		'ID'           => $user->ID,
	];

	if ( $user_grade->get_components() ) {
		foreach ( $user_grade->get_components() as $component ) {
			$data[ "component_{$component['id']}" ] = $component['averaged_score'];
		}
	}

	return $data;
}

/**
 * Gets users grades in batch.
 *
 * @since 1.1.0
 *
 * @param array $_user_args Optional arguments to use in getting users.
 * @param int   $gradebook Gradebook to use.
 * @param bool  $user_query If true, use global wp_query
 *
 * @return array User grade data.
 */
function ld_gb_get_users_grades( $_user_args, $gradebook, $user_query = false ) {
	$data = [];

	$user_args = wp_parse_args(
		$_user_args,
		[
			'orderby' => 'display_name',
		]
	);

	if ( $user_query ) {
		$query = new WP_User_Query( $user_args );
		$users = $query->get_results();
	} else {
		$users = get_users( $user_args );
	}

	if ( $users ) {
		/** @var WP_User $user */
		foreach ( (array) $users as $user ) {
			$data[ $user->ID ] = ld_gb_get_user_grade( $user->ID, $gradebook );
		}
	}

	/**
	 * Filters the Gradebook data.
	 *
	 * @since 1.1.0
	 */
	$data = apply_filters( 'ld_gb_gradebook_data', $data, $gradebook );

	if ( isset( $query ) ) {
		return [
			'query'  => $query,
			'grades' => $data,
		];
	} else {
		return $data;
	}
}

/**
 * Remove Class Filter Without Access to Class Object
 *
 * In order to use the core WordPress remove_filter() on a filter added with the callback
 * to a class, you either have to have access to that class object, or it has to be a call
 * to a static method.  This method allows you to remove filters with a callback to a class
 * you don't have access to.
 *
 * https://gist.github.com/tripflex/c6518efc1753cf2392559866b4bd1a53
 *
 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
 * Updated 2-27-2017 to use internal WordPress removal for 4.7+ (to prevent PHP warnings output)
 *
 * @param string $tag         Filter to remove
 * @param string $class_name  Class name for the filter's callback
 * @param string $method_name Method name for the filter's callback
 * @param int    $priority    Priority of the filter (default 10)
 *
 * @since 1.4.3
 * @return bool Whether the function is removed.
 */
function learndash_gradebook_remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
	global $wp_filter;

	$removed = false;

	// Check that filter actually exists first
	if ( ! isset( $wp_filter[ $tag ] ) ) {
		return $removed;
	}

	/**
	 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
	 * a simple array, rather it is an object that implements the ArrayAccess interface.
	 *
	 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
	 *
	 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
	 */
	if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
		// Create $fob object from filter tag, to use below
		$fob       = $wp_filter[ $tag ];
		$callbacks = &$wp_filter[ $tag ]->callbacks;
	} else {
		$callbacks = &$wp_filter[ $tag ];
	}

	// Exit if there aren't any callbacks for specified priority
	if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
		return $removed;
	}

	// Loop through each filter for the specified priority, looking for our class & method
	foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {
		// Filter should always be an array - array( $this, 'method' ), if not goto next
		if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
			continue;
		}

		// If first value in array is not an object, it can't be a class
		if ( ! is_object( $filter['function'][0] ) ) {
			continue;
		}

		// Method doesn't match the one we're looking for, goto next
		if ( $filter['function'][1] !== $method_name ) {
			continue;
		}

		// Method matched, now let's check the Class
		if ( get_class( $filter['function'][0] ) === $class_name ) {
			// WordPress 4.7+ use core remove_filter() since we found the class object
			if ( isset( $fob ) ) {
				// Handles removing filter, resetting callback priority keys mid-iteration, etc.
				$fob->remove_filter( $tag, $filter['function'], $priority );
			} else {
				// Use legacy removal process (pre 4.7)
				unset( $callbacks[ $priority ][ $filter_id ] );
				// and if it was the only filter in that priority, unset that priority
				if ( empty( $callbacks[ $priority ] ) ) {
					unset( $callbacks[ $priority ] );
				}
				// and if the only filter for that tag, set the tag to an empty array
				if ( empty( $callbacks ) ) {
					$callbacks = [];
				}
				// Remove this filter from merged_filters, which specifies if filters have been sorted
				unset( $GLOBALS['merged_filters'][ $tag ] );
			}

			$removed = true;
		}
	}

	return $removed;
}

/**
 * Remove Class Action Without Access to Class Object
 *
 * In order to use the core WordPress remove_action() on an action added with the callback
 * to a class, you either have to have access to that class object, or it has to be a call
 * to a static method.  This method allows you to remove actions with a callback to a class
 * you don't have access to.
 *
 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
 *
 * @param string $tag         Action to remove
 * @param string $class_name  Class name for the action's callback
 * @param string $method_name Method name for the action's callback
 * @param int    $priority    Priority of the action (default 10)
 *
 * @since 1.4.3
 * @return bool               Whether the function is removed.
 */
function learndash_gradebook_remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
	learndash_gradebook_remove_class_filter( $tag, $class_name, $method_name, $priority );
}

/**
 * Globally scopes a function to modify Manual Grades.
 *
 * @since 1.6.0
 *
 * @param array $grade     An array of Grade Data. You _must_ provide a Component Index, Gradebook Post ID, and User ID.
 * @param bool  $overwrite Whether you want to overwrite/update a Manual Grade with a matching Name in the same Component. Defaults to true. In most cases, you probably want to ignore this.
 *
 * @return array Returns the updated User Grade data or an error.
 */
function learndash_gradebook_update_manual_grade( $grade, $overwrite = true ) {
	$grade = wp_parse_args(
		$grade,
		[
			'score'     => 0,
			'name'      => '',
			'status'    => '',
			'component' => false,
			'gradebook' => false,
			'user_id'   => false,
			'completed' => time(),
		]
	);

	if ( $grade['user_id'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'A user_id must be provided.', 'Manual Grade Update: User ID not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( $grade['component'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'A component index must be provided.', 'Manual Grade Update: Component ID not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( $grade['gradebook'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'A Gradebook ID must be provided.', 'Manual Grade Update: Gradebook ID not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( ld_gb_get_option_field( 'max_percentage_grade', false ) ) {
		$grade['score'] = min( $grade['score'], 100 );
	}

	// Rounding
	$grade['score'] = ld_gb_round_grade(
		(float) $grade['score'],
		ld_gb_get_option_field( 'grade_precision', 0 ),
		ld_gb_get_option_field( 'grade_round_mode', 'ceil' )
	);

	if ( $grade['score'] < 0 ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'Grade must be a valid number greater than 0', 'Manual Grade Update: Invalid grade provided error', 'learndash-gradebook' ),
			],
		];
	}

	$original_grades = false; // This is only used if $overwrite is true
	$manual_grades   = [];

	if ( ! $overwrite ) {
		// We're only adding a new Manual Grade. If there is a match, bail

		if ( $manual_grades = get_user_meta( $grade['user_id'], "ld_gb_manual_grades_{$grade['gradebook']}_{$grade['component']}", true ) ) {
			// If already set, don't add it
			foreach ( $manual_grades as $i => $manual_grade ) {
				if ( $manual_grade['component'] == $grade['component'] && $manual_grade['name'] == $grade['name'] ) {
					return [
						'type' => 'error',
						'data' => [
							'error' => _x( 'A Manual Grade of the same name already exists in this Component.', 'Manual Grade Update: A matching Manual Grade already exists error', 'learndash-gradebook' ),
						],
					];
				}
			}
		}
	} else {
		// If we're allowing an update, then overwrite a previous match if found
		// Otherwise, add the Manual Grade

		if ( $original_grades = $manual_grades = get_user_meta( $grade['user_id'], "ld_gb_manual_grades_{$grade['gradebook']}_{$grade['component']}", true ) ) {
			// Find and delete it. We'll be adding it back in
			foreach ( $manual_grades as $i => $manual_grade ) {
				if ( $manual_grade['component'] == $grade['component'] && $manual_grade['name'] == $grade['previous_name'] ) {
					unset( $manual_grades[ $i ] );
					$manual_grades = array_values( $manual_grades );
					break;
				}
			}
		}
	}

	// If no Manual Grades were previously saved, ensure we are initialized correctly
	if ( ! $manual_grades ) {
		$manual_grades = [];
	}

	$updated_manual_grade = apply_filters(
		'learndash_update_manual_grade',
		[
			'score'     => $grade['score'],
			'name'      => $grade['name'],
			'component' => $grade['component'],
			'status'    => $grade['status'],
			'completed' => $grade['completed'],
		],
		$grade
	);

	$manual_grades[] = $updated_manual_grade;

	if ( $original_grades !== false ) {
		if ( $manual_grades == $original_grades ) {
			return [
				'type' => 'error',
				'data' => [
					'error' => _x( 'No change detected.', 'Manual Grade Update: No new Manual Grade entered error', 'learndash-gradebook' ),
				],
			];
		}
	}

	$result = update_user_meta( $grade['user_id'], "ld_gb_manual_grades_{$grade['gradebook']}_{$grade['component']}", $manual_grades );

	if ( $result === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'Could not save manual grade', 'Manual Grade Update: Generic error while saving the Manual Grade', 'learndash-gradebook' ),
			],
		];
	}

	// Get final score
	$user_grade = new LD_GB_UserGrade( $grade['user_id'], $grade['gradebook'] );

	// Get component grade
	$component                = $user_grade->get_component( $grade['component'] );
	$component_grade['score'] = LD_GB_UserGrade::get_display_grade( $component['averaged_score'], 'letter' );
	$component_grade['color'] = LD_GB_UserGrade::get_display_grade_color( $component['averaged_score'] );

	$grade = LD_GB_UserGrade::modify_grade_by_status( $grade );

	if ( $original_grades !== false ) {
		/**
		 * Fires on successfully editing a manual grade.
		 *
		 * @since 1.2.0
		 * @since 4.0.0 Updated.
		 *
		 * @param array $grade                Edited Manual Grade.
		 * @param array $updated_manual_grade The saved Manual Grade data.
		 */
		do_action( 'ld_gb_manual_grade_edited', $grade, $updated_manual_grade );

		return [
			'type' => 'success',
			'data' => [
				'success'           => _x( 'Successfully changed manual grade', 'Manual Grade Update: Successfully updated message', 'learndash-gradebook' ),
				'user_grade'        => [
					'score' => LD_GB_UserGrade::get_display_grade( $user_grade->get_user_grade(), 'letter' ),
					'color' => LD_GB_UserGrade::get_display_grade_color( $user_grade->get_user_grade() ),
				],
				'component_grade'   => $component_grade,
				'score'             => $grade['original_score'],
				'score_display'     => $grade['score_display'],
				'name'              => $grade['name'],
				'completed'         => $grade['completed'],
				'completed_display' => ld_gb_get_datetimestamp( $grade['completed'] ),
				'grade'             => $updated_manual_grade,
			],
		];
	} else {
		/**
		 * Fires on successfully adding a manual grade
		 *
		 * @since 1.2.0
		 * @since 4.0.0 Updated.
		 *
		 * @param array $grade                Manually Added Grade.
		 * @param array $updated_manual_grade The saved Manual Grade data.
		 */
		do_action( 'ld_gb_manual_grade_added', $grade, $updated_manual_grade );

		return [
			'type' => 'success',
			'data' => [
				'success'           => _x( 'Successfully added manual grade', 'Manual Grade Update: Successfully added message', 'learndash-gradebook' ),
				'user_grade'        => [
					'score' => LD_GB_UserGrade::get_display_grade( $user_grade->get_user_grade(), 'letter' ),
					'color' => LD_GB_UserGrade::get_display_grade_color( $user_grade->get_user_grade() ),
				],
				'component_grade'   => $component_grade,
				'score'             => $grade['original_score'],
				'score_display'     => $grade['score_display'],
				'name'              => $grade['name'],
				'completed'         => $grade['completed'],
				'completed_display' => ld_gb_get_datetimestamp( $grade['completed'] ),
				'grade'             => $updated_manual_grade,
			],
		];
	}}

/**
 * Globally scopes a function for deleting a Manual Grade
 *
 * @param array $grade  An array of Grade Data. You _must_ provide a Component Index, Gradebook Post ID, and User ID
 *
 * @since 1.6.0
 * @return array          Returns the updated User Grade data or an error
 */
function learndash_gradebook_delete_manual_grade( $grade ) {
	$grade = wp_parse_args(
		$grade,
		[
			'name'      => false,
			'component' => false,
			'gradebook' => false,
			'user_id'   => false,
		]
	);

	if ( $grade['user_id'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'A user_id must be provided.', 'Manual Grade Delete: User ID not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( $grade['component'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'A component index must be provided.', 'Manual Grade Delete: Component ID not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( $grade['gradebook'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'A Gradebook ID must be provided.', 'Manual Grade Delete: Gradebook ID not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( $grade['name'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'The name of the Manual Grade you want to delete must be defined.', 'Manual Grade Delete: Manual Grade Name not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( $manual_grades = get_user_meta( $grade['user_id'], "ld_gb_manual_grades_{$grade['gradebook']}_{$grade['component']}", true ) ) {
		// If already set, delete it
		foreach ( $manual_grades as $i => $manual_grade ) {
			if ( $manual_grade['component'] == $grade['component'] && $manual_grade['name'] == $grade['name'] ) {
				unset( $manual_grades[ $i ] );
				$manual_grades = array_values( $manual_grades );
			}
		}
	} else {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'Could not delete Manual Grade. No Manual Grades found.', 'Manual Grade Delete: No Manual Grades found error', 'learndash-gradebook' ),
			],
		];
	}

	if ( empty( $manual_grades ) ) {
		$result = delete_user_meta( $grade['user_id'], "ld_gb_manual_grades_{$grade['gradebook']}_{$grade['component']}" );
	} else {
		$result = update_user_meta( $grade['user_id'], "ld_gb_manual_grades_{$grade['gradebook']}_{$grade['component']}", $manual_grades );
	}

	if ( $result === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'Could not delete Manual Grade', 'Manual Grade Delete: Could not delete Manual Grade error', 'learndash-gradebook' ),
			],
		];
	}

	// Get final score
	$user_grade = new LD_GB_UserGrade( $grade['user_id'], $grade['gradebook'] );

	// Get component grade
	$component                = $user_grade->get_component( $grade['component'] );
	$component_grade['score'] = LD_GB_UserGrade::get_display_grade( $component['averaged_score'], 'letter' );
	$component_grade['color'] = LD_GB_UserGrade::get_display_grade_color( $component['averaged_score'] );

	/**
	 * Fires on successfully deleting a manual grade.
	 *
	 * @since 1.2.0
	 *
	 * @param array $grade Deleted Manual Grade.
	 */
	do_action( 'ld_gb_manual_grade_deleted', $grade );

	return [
		'type' => 'success',
		'data' => [
			'success'         => _x( 'Successfully deleted manual grade', 'Manual Grade Delete: Successfully deleted message', 'learndash-gradebook' ),
			'user_grade'      => [
				'score' => LD_GB_UserGrade::get_display_grade( $user_grade->get_user_grade(), 'letter' ),
				'color' => LD_GB_UserGrade::get_display_grade_color( $user_grade->get_user_grade() ),
			],
			'component_grade' => $component_grade,
		],
	];}

/**
 * Update a Post-driven Grade (Quizzes, Lessons, Topics, and Assignments)
 *
 * @param array $grade  Grade Array
 *
 * @since 2.0.0
 * @return array          Returns the updated User Grade data or an error
 */
function learndash_gradebook_edit_post_driven_grade( $grade ) {
	if ( $grade['user_id'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'A user_id must be provided.', 'Grade Edit: User ID not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( $grade['component'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'A component index must be provided.', 'Grade Edit: Component ID not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( $grade['gradebook'] === false ) {
		return [
			'type' => 'error',
			'data' => [
				'error' => _x( 'A Gradebook ID must be provided.', 'Grade Edit: Gradebook ID not provided error', 'learndash-gradebook' ),
			],
		];
	}

	if ( isset( $grade['status'] ) ) {
		if ( $grade['status'] ) {
			update_user_meta( $grade['user_id'], "ld_gb_grade_status_{$grade['gradebook']}_{$grade['post_id']}", $grade['status'] );
		} else {
			delete_user_meta( $grade['user_id'], "ld_gb_grade_status_{$grade['gradebook']}_{$grade['post_id']}" );
		}
	}

	// Get final score
	$user_grade = new LD_GB_UserGrade( $grade['user_id'], $grade['gradebook'] );

	// Get component grade.
	$component                = $user_grade->get_component( $grade['component'] );
	$component_grade['score'] = LD_GB_UserGrade::get_display_grade( $component['averaged_score'], 'letter' );
	$component_grade['color'] = LD_GB_UserGrade::get_display_grade_color( $component['averaged_score'] );

	$grade = LD_GB_UserGrade::modify_grade_by_status( $grade );

	return [
		'type' => 'success',
		'data' => [
			'score_display'   => $grade['score_display'],
			'component_grade' => $component_grade,
			'user_grade'      => [
				'score' => LD_GB_UserGrade::get_display_grade( $user_grade->get_user_grade(), 'letter' ),
				'color' => LD_GB_UserGrade::get_display_grade_color( $user_grade->get_user_grade() ),
			],
		],
	];}

/**
 * Updates/Adds a Component Grade Override for a User within a Gradebook
 *
 * @param integer $user_id       WP_User ID
 * @param integer $gradebook_id  WP_Post ID
 * @param integer $component_id  Index of the Component
 * @param float   $new_grade     Grade that the Component Grade Override is being set to
 *
 * @since 2.0.0
 * @return array                   Array of Component Grade Overrides for the User
 */
function learndash_gradebook_update_component_grade_override( $user_id, $gradebook_id, $component_id, $new_grade ) {
	$component_grades = get_user_meta( $user_id, "ld_gb_component_grades_{$gradebook_id}", true );

	if ( ! $component_grades ) {
		$component_grades = [];
	}

	$new_grade = max( (float) $new_grade, 0 );

	$component_grades[ $component_id ] = $new_grade;

	update_user_meta( $user_id, "ld_gb_component_grades_{$gradebook_id}", $component_grades );

	return $component_grades;}

/**
 * Deletes a Component Grade Override for a User within a Gradebook
 *
 * @param integer $user_id       WP_User ID
 * @param integer $gradebook_id  WP_Post ID
 * @param integer $component_id  Index of the Component
 *
 * @return boolean|array|Object          True if all Components Grade Overrides were deleted, Array of remaining Component Grade Overrides otherwise. WP_Error on failure.
 */
function learndash_gradebook_delete_component_grade_override( $user_id, $gradebook_id, $component_id ) {
	$component_grades = get_user_meta( $user_id, "ld_gb_component_grades_{$gradebook_id}", true );

	if ( ! $component_grades ) {
		return new WP_Error( 500, _x( 'No Component Grade Overrides found for this User', 'Component Override Delete: No Component Override found for this User error', 'learndash-gradebook' ) );
	}

	if ( isset( $component_grades[ $component_id ] ) ) {
		unset( $component_grades[ $component_id ] );
	} else {
		return new WP_Error( 500, _x( 'No Grade Override found for this Component', 'Component Override Delete: No Component Override found for this Component error', 'learndash-gradebook' ) );
	}

	if ( ! $component_grades ) {
		// All Component Grade Overrides were removed, so delete the User meta
		delete_user_meta( $user_id, "ld_gb_component_grades_{$gradebook_id}" );

		return true;
	} else {
		update_user_meta( $user_id, "ld_gb_component_grades_{$gradebook_id}", $component_grades );

		return $component_grades;
	}}

/**
 * There's oddly not a Group Leader counterpart to learndash_is_user_in_group()
 *
 * @param integer $group_id  Group ID
 * @param integer $user_id   User ID
 *
 * @since 2.0.0
 * @return boolean             True/False
 */
function learndash_gradebook_is_user_group_leader_of_group( $group_id, $user_id = null ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	return get_user_meta( $user_id, 'learndash_group_leaders_' . $group_id, true );}

/**
 * Gets the Gradebook Data
 *
 * @param integer $gradebook_id  Gradebook ID
 * @param integer $group_id      Group ID. False-y value for All Users
 * @param array   $params        Args for WP_User_Query
 *
 * @since 2.0.0
 * @return array                   Gradebook Data
 */
function learndash_gradebook_get_gradebook_data( $gradebook_id, $group_id, $params ) {
	$gradebook_id = (int) $gradebook_id;
	$group_id     = (int) $group_id;

	$user_columns = learndash_gradebook_get_user_columns( $gradebook_id, $group_id );

	$primary_column = 'display_name';
	foreach ( $user_columns as $key => $name ) {
		$primary_column = $key;
		break;
	}

	$params = wp_parse_args(
		$params,
		[
			's'        => false,
			'per_page' => 30,
			'paged'    => 1,
			'order'    => 'asc',
			'orderby'  => $primary_column,
			'meta_key' => false,
		]
	);

	if ( in_array( $params['orderby'], [ 'first_name', 'last_name' ] ) ) {
		$params['meta_key'] = $params['orderby'];
		$params['orderby']  = 'meta_value';
	}

	$search = ! empty( $params['s'] ) ? urldecode( trim( $params['s'] ) ) : false;

	$saved_components = ld_gb_get_field( 'components', $gradebook_id );

	$user_args = ld_gb_get_gradebook_get_users_args(
		$gradebook_id,
		$group_id,
		[
			'number'   => $params['per_page'],
			'offset'   => $params['per_page'] * ( absint( $params['paged'] ) - 1 ),
			'order'    => $params['order'],
			'orderby'  => $params['orderby'],
			'search'   => $search ? "*$search*" : '',
			'meta_key' => $params['meta_key'],
		]
	);

	$data = ld_gb_get_users_grades( $user_args, $gradebook_id, true );

	if ( $data['grades'] ) {
		if ( $saved_components ) {
			$data['components'] = [];

			$components = [];
			foreach ( $saved_components as $component ) {
				$components[] = "component_{$component['id']}";

				$data['components'][ "component_{$component['id']}" ] = $component['name'];
			}
		}

		if (
			isset( $params['orderby'] ) && ( $params['orderby'] == 'grade' || strpos( $params['orderby'], 'component' ) !== false )
		) {
			usort(
				$data['grades'],
				function ( $a, $b ) use ( $params ) {
					return learndash_gradebook_sort_by_keys_value( $a, $b, $params['orderby'], $params['order'] );
				}
			);
		}
	}

	return $data;}

/**
 * Updates the largest shown number of users count for any Gradebook in the database.
 *
 * This is only used for diagnostics and telemetry.
 *
 * @param integer $new_total_users  Provided total users count from the query constructing the Gradebook
 *
 * @since 4.3.0
 * @return void
 */
function ld_gb_update_largest_total_users( $new_total_users ) {
	$old_total_users = ld_gb_get_largest_total_users();

	if ( (int) $new_total_users <= $old_total_users ) {
		return;
	}

	update_option( 'ld_gb_largest_total_users', (int) $new_total_users );}

/**
 * Retrieves the largest shown number of users count for any Gradebook from the database.
 *
 * This is only used for diagnostics and telemetry.
 *
 * @since 4.3.0
 * @return integer  Largest total users count from the database
 */
function ld_gb_get_largest_total_users() {
	return (int) get_option( 'ld_gb_largest_total_users', 0 );}

/**
 * Increments the Quickstart Guide Reset Counter
 *
 * This is only used for diagnostics and telemetry.
 *
 * @since 4.3.0
 * @return void
 */
function ld_gb_increment_quickstart_guide_reset_counter() {
	$count = (int) ld_gb_get_quickstart_guide_reset_counter();

	++$count;

	update_option( 'ld_gb_quickstart_guide_reset_counter', (int) $count );}

/**
 * Retrieves the Quickstart Guide Reset Counter
 *
 * This is only used for diagnostics and telemetry.
 *
 * @since 4.3.0
 * @return integer  Quickstart Guide Reset Counter.
 */
function ld_gb_get_quickstart_guide_reset_counter() {
	return (int) get_option( 'ld_gb_quickstart_guide_reset_counter', 0 );}

/**
 * A custom usort function for sorting by a Value at a given Key
 * This will only work for numeric and string values!
 *
 * @param array  $a        Array A
 * @param array  $b        Array B
 * @param string $orderby  Key
 * @param string $order    Order direction
 *
 * @since 2.0.0
 * @return integer            Whether to move the Array up or down the stack
 */
function learndash_gradebook_sort_by_keys_value( $a, $b, $orderby, $order = 'asc' ) {
	if ( strtolower( $order ) == 'asc' ) {
		return $b[ $orderby ] - $a[ $orderby ];
	} else {
		return $a[ $orderby ] - $b[ $orderby ];
	}}

/**
 * Outputs a formatted Grade Display, including any styling
 *
 * @param integer $score   Grade as a Score out of 100
 * @param string  $format  Grade Format
 *
 * @since 2.0.0
 * @return string            HTML
 */
function learndash_gradebook_get_grade_display( $score, $format = false ) {
	if ( $score === false ) {
		return '';
	}

	if ( ! $format ) {
		$format = Cast::to_string( ld_gb_get_option_field( 'grade_display_mode', 'letter' ) );
	}

	$background_color = LD_GB_UserGrade::get_display_grade_color( $score );

	$html      = '<span class="ld-gb-grade ' . esc_attr( $format ) . '" style="background: ' . $background_color . '; color: ' . ( ( learndash_gradebook_is_light_color( $background_color ) ) ? 'inherit' : '#fff' ) . '">';
		$html .= LD_GB_UserGrade::get_display_grade( $score, $format );
	$html     .= '</span>';

	$html = apply_filters( 'learndash_gradebook_get_grade_display', $html, $score, $format );

	return $html;}

/**
 * Get the user-defined Columns set for both Frontend and Backend Gradebooks
 *
 * @param integer $gradebook_id  Gradebook ID
 * @param integer $group_id      Group ID
 *
 * @since 2.0.0
 * @return array                   User-defined Columns
 */
function learndash_gradebook_get_user_columns( $gradebook_id = 0, $group_id = 0 ) {
	$chosen_columns = ld_gb_get_option_field(
		'user_columns',
		[
			[
				'column' => 'display_name',
			],
		]
	);

	if ( ! $chosen_columns ) {
		$chosen_columns = [
			[
				'column' => 'display_name',
			],
		];
	}

	$column_labels = [
		'display_name' => _x( 'Display Name', 'Gradebook User Column: Display Name', 'learndash-gradebook' ),
		'first_name'   => _x( 'First Name', 'Gradebook User Column: First Name', 'learndash-gradebook' ),
		'last_name'    => _x( 'Last Name', 'Gradebook User Column: Last Name', 'learndash-gradebook' ),
		'user_email'   => _x( 'Email Address', 'Gradebook User Column: Email Address', 'learndash-gradebook' ),
		'user_login'   => _x( 'Username', 'Gradebook User Column: Username', 'learndash-gradebook' ),
	];

	$columns = [];

	foreach ( $chosen_columns as $row ) {
		if ( isset( $column_labels[ $row['column'] ] ) ) {
			$columns[ $row['column'] ] = $column_labels[ $row['column'] ];
		}
	}

	return apply_filters( 'learndash_gradebook_get_user_columns', $columns, $gradebook_id, $group_id );}

/**
 * Returns a Dashicon for a given Grade Type
 *
 * @param string $type  Grade Type
 *
 * @since 1.2.0
 * @return string         Dashicon
 */
function ld_gb_grade_icon( $type ) {
	switch ( $type ) {
		case 'quiz':
			$icon = 'dashicons-editor-help';
			break;

		case 'assignment':
			$icon = 'dashicons-media-text';
			break;

		case 'lesson':
			$icon = 'dashicons-welcome-write-blog';
			break;

		case 'topic':
			$icon = 'dashicons-portfolio';
			break;

		case 'manual':
			$icon = 'dashicons-edit';
			break;

		default:
			$icon = '';
	}

	return $icon;}

/**
 * Returns the offset in seconds the site's timezone is from UTC.
 *
 * @since 3.1.0
 *
 * @return int Offset in seconds.
 */
function ld_gb_get_timezone_offset() {
	$timezone_offset = wp_timezone()->getOffset( new DateTime( 'now', null ) );

	return apply_filters( 'ld_gb_get_timezone_offset', $timezone_offset );
}

/**
 * Returns a datetimestamp format to use that includes the site's timezone
 *
 * @since 3.1.0
 * @return string  PHP Date format
 */
function ld_gb_get_datetimestamp_format() {
	$datetimestamp_format = get_option( 'date_format', 'F j, Y' ) . ' @ ' . get_option( 'time_format', 'g:i a' );

	// Append timezone offset
	if ( strpos( $datetimestamp_format, 'T' ) === false ) {
		$datetimestamp_format .= ' T';
	}

	return apply_filters( 'ld_gb_get_datetimestamp_format', $datetimestamp_format );}

/**
 * Output a human readable timestamp converted for output in the site's timezone
 *
 * @param integer         $timestamp             Unix Timestamp
 * @param string          $datetimestamp_format  PHP Date Format
 * @param integer|boolean $timezone_offset       Timezone offset from UTC in seconds
 *
 * @since 3.1.0
 * @return string                                  Date string
 */
function ld_gb_get_datetimestamp( $timestamp, $datetimestamp_format = '', $timezone_offset = false ) {
	if ( empty( $datetimestamp_format ) ) {
		$datetimestamp_format = ld_gb_get_datetimestamp_format();
	}

	if ( $timezone_offset === false ) {
		$timezone_offset = ld_gb_get_timezone_offset();
	}

	return date_i18n( $datetimestamp_format, $timestamp + $timezone_offset );}

/**
 * Gets the Elementor contents from a page without rendering them.
 * This allows us to check for Shortcodes and Gutenberg Blocks
 *
 * @param integer $post_id  Post ID to check
 *
 * @since 4.0.0
 * @return array              Array of Elementor contents
 */
function learndash_gradebook_get_elementor_contents( $post_id ) {
	if ( ! class_exists( 'Elementor\Plugin' ) ) {
		return [];
	}

	$elementor_document = Elementor\Plugin::$instance->documents->get_doc_for_frontend( $post_id );

	if ( ! $elementor_document ) {
		return [];
	}

	$elementor_data = $elementor_document->get_elements_data();

	// Retrieve only the content of any given Elementor Element
	$flatten_elementor_data = function ( $elementor_data, $result = [] ) use ( &$flatten_elementor_data ) {
		foreach ( $elementor_data as $data ) {
			if ( ! empty( $data['settings'] ) ) {
				if ( isset( $data['settings']['shortcode'] ) && $data['settings']['shortcode'] ) {
					$result[] = $data['settings']['shortcode'];
				}

				if ( isset( $data['settings']['editor'] ) && $data['settings']['editor'] ) {
					$result[] = $data['settings']['editor'];
				}
			}

			if ( empty( $data['elements'] ) ) {
				continue;
			}

			$result = $result + $flatten_elementor_data( $data['elements'], $result );
		}

		return $result;
	};

	$flattened = $flatten_elementor_data( $elementor_data );

	return $flattened;}

/**
 * Filterable wrapper function for is_super_admin()
 *
 * @param integer $user_id  User ID to check. Defaults to the currently logged in user
 *
 * @since 4.1.0
 * @return boolean            The resulting value of is_super_admin()
 */
function ld_gb_is_super_admin( $user_id = false ) {
	return apply_filters( 'ld_gb_is_super_admin', is_super_admin( $user_id ), $user_id );}

use function LearnDash\Gradebook\SSNepenthe\ColorUtils\{
	rgb, scale_color, is_light
};

/**
 * Determine brightness of Hex color similar to SASS lightness()
 *
 * @param string  $hex         Hex Color
 * @param integer $percentage  Percentage of lightness to check against
 *
 * @since 2.0.0
 * @return boolean               Light or Dark color
 */
function learndash_gradebook_is_light_color( $hex, $percentage = 65 ) {
	return is_light( $hex, $percentage );}

/**
 * Replicates SASS scale-color()
 *
 * @param string $hex   Hex Color
 * @param array  $args  Associative Array of Arguments, such as 'lightness'
 *
 * @since 2.0.0
 * @return string         Scaled Hex Color
 */
function learndash_gradebook_scale_color( $hex, $args ) {
	return scale_color( $hex, $args );
}

/**
 * Sanitizes saved grade style settings to help prevent errors.
 *
 * @since 4.3.2
 *
 * @param array{grade: string, letter?: string, color?: string}[]|mixed $settings Grade style settings. Should be an array of arrays with a grade key at minimum. Mixed is to account for data that needs to be sanitized.
 *
 * @return array{grade: string, letter?: string, color?: string}[]
 */
function learndash_gradebook_sanitize_grade_style_settings( $settings ): array {
	if ( ! is_array( $settings ) ) {
		return [];
	}

	return array_filter(
		$settings,
		function ( $row ) {
			if (
				! is_array( $row )
				|| ! isset( $row['grade'] )
				|| ! is_scalar( $row['grade'] )
			) {
				return false;
			}

			return true;
		}
	);
}
