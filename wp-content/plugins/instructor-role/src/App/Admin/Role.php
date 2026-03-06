<?php
/**
 * Role class file.
 *
 * @since 5.9.4
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Admin;

use WP_Role;

/**
 * Role class.
 *
 * @since 5.9.4
 */
class Role {
	/**
	 * Instructor role key.
	 *
	 * @since 5.9.4
	 *
	 * @var string
	 */
	private const ROLE_INSTRUCTOR = 'wdm_instructor';

	/**
	 * Registers instructor role for LearnDash notes capabilities.
	 *
	 * @since 5.9.4
	 *
	 * @param WP_Role[] $roles Existing roles.
	 *
	 * @return WP_Role[] Returned roles.
	 */
	public function add_notes_capabilities( $roles ) {
		$instructor_role = get_role( self::ROLE_INSTRUCTOR );

		if ( ! $instructor_role instanceof WP_Role ) {
			return $roles;
		}

		$roles[] = $instructor_role;

		return $roles;
	}
}
