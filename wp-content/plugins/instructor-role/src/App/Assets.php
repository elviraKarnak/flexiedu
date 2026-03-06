<?php
/**
 * Class file to manage common assets.
 *
 * @since 5.9.5
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role;

use LearnDash\Instructor_Role\StellarWP\Assets\Asset;

/**
 * Assets class.
 *
 * @since 5.9.5
 */
class Assets {
	/**
	 * Registers the assets.
	 *
	 * @since 5.9.5
	 *
	 * @return void
	 */
	public function register(): void {
		Asset::add( 'learndash-instructor-role-translations', 'translations.js' )
			->register();
	}
}
