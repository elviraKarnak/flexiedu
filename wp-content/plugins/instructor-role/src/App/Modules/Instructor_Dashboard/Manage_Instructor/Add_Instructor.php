<?php
/**
 * Add Instructor logic Class.
 *
 * @since 5.9.3
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Modules\Instructor_Dashboard\Manage_Instructor;

use WP_REST_Request;
use LearnDash\Core\Utilities\Cast;

/**
 * Add Instructor logic Class.
 *
 * @since 5.9.3
 */
class Add_Instructor {
	/**
	 * Query param used to determine if this request is for our plugin.
	 * This is particularly important when restricting results within /wp/v2/users,
	 * as we do not want to change the returned results globally.
	 *
	 * @since 5.9.3
	 *
	 * @var string
	 */
	private string $query_param = 'learndash_instructor_role_add_instructor';

	/**
	 * Removes Administrators from the dropdown used to turn existing users into Instructors.
	 *
	 * @since 5.9.3
	 *
	 * @param array<string, mixed>     $user_args WP_User_Query args to search using.
	 * @param WP_REST_Request<mixed[]> $request   Request object.
	 *
	 * @return array<string, mixed>
	 */
	public function remove_admins_from_existing_users_list( $user_args, $request ) {
		if ( ! $this->is_valid_rest_request( $request ) ) {
			return $user_args;
		}

		$user_args = wp_parse_args(
			$user_args,
			[
				'role__not_in' => [],
			]
		);

		// Ensure that we are dealing with an Array of User Roles.
		if ( ! is_array( $user_args['role__not_in'] ) ) {
			$user_args['role__not_in'] = [
				Cast::to_string( $user_args['role__not_in'] ),
			];
		}

		$user_args['role__not_in'] = array_filter( $user_args['role__not_in'] );

		$user_args['role__not_in'][] = 'administrator';

		return $user_args;
	}

	/**
	 * Checks whether this is a valid REST request for Adding Instructors.
	 *
	 * @since 5.9.3
	 *
	 * @param WP_REST_Request<mixed[]> $request Request object.
	 *
	 * @return bool
	 */
	private function is_valid_rest_request( $request ): bool {
		return $request->has_param( $this->query_param );
	}
}
