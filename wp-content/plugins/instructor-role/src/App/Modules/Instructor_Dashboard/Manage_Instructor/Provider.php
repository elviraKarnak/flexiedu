<?php
/**
 * Instructor Management provider class file.
 *
 * @since 5.9.3
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Modules\Instructor_Dashboard\Manage_Instructor;

use StellarWP\Learndash\lucatume\DI52\ServiceProvider;

/**
 * Instructor Management service provider class.
 *
 * @since 5.9.3
 */
class Provider extends ServiceProvider {
	/**
	 * Register service providers.
	 *
	 * @since 5.9.3
	 *
	 * @return void
	 */
	public function register(): void {
		$this->hooks();
	}

	/**
	 * Hooks wrapper.
	 *
	 * @since 5.9.3
	 *
	 * @return void
	 */
	protected function hooks() {
		add_filter(
			'rest_user_query',
			$this->container->callback(
				Add_Instructor::class,
				'remove_admins_from_existing_users_list'
			),
			10,
			2
		);
	}
}
