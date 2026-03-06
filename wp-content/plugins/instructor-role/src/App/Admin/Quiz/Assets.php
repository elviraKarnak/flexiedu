<?php
/**
 * Quiz Admin Assets Loader.
 *
 * @since 5.9.7
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Admin\Quiz;

use LDLMS_Post_Types;
use LearnDash\Instructor_Role\StellarWP\Assets\Asset;
use LearnDash\Instructor_Role\StellarWP\Assets\Assets as Base_Assets;

/**
 * Quiz Admin Assets Loader.
 *
 * @since 5.9.7
 */
class Assets {
	/**
	 * Asset Group to register our Assets to and enqueue from.
	 *
	 * @since 5.9.7
	 *
	 * @var string
	 */
	public static string $group = 'learndash-instructor-role-quiz-admin';

	/**
	 * Registers assets to the asset group.
	 *
	 * @since 5.9.7
	 *
	 * @return void
	 */
	public function register_assets(): void {
		$pointers_asset = Base_Assets::instance()->get( 'learndash-instructor-role-admin-pointers' );

		if ( $pointers_asset instanceof Asset ) {
			$pointers_asset->add_to_group( self::$group );
		}

		Asset::add( 'learndash-instructor-role-quiz-admin-edit', 'edit.js' )
			->add_to_group( self::$group )
			->set_path( 'dist/js/admin/quiz', false )
			->set_condition( fn() => $this->should_load_assets() )
			->set_dependencies( 'wp-pointer', 'jquery' )
			->enqueue_on( 'admin_enqueue_scripts', 10 ) // Must match the where it is hooked in the provider.
			->add_localize_script(
				'learndash.instructorRole.quiz.edit',
				[
					'frontendQuizEditorButtonMoved' => ir_get_settings( 'ir_enable_frontend_dashboard' )
						&& version_compare( constant( 'LEARNDASH_VERSION' ), '4.22.1', '>=' ) // @phpstan-ignore-line -- This constant can change.
							? sprintf(
								// translators: placeholder: Quiz label.
								__(
									'Edit via Frontend %s Creator has moved into the Actions menu.',
									'wdm_instructor_role'
								),
								learndash_get_custom_label( 'quiz' )
							)
							: '',
				]
			)
			->register();
	}

	/**
	 * Enqueues the assets.
	 *
	 * @since 5.9.7
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		Base_Assets::instance()->enqueue_group( self::$group );
	}

	/**
	 * Determines if the assets should be loaded.
	 *
	 * @since 5.9.7
	 *
	 * @return bool
	 */
	private function should_load_assets(): bool {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		return $screen->id === learndash_get_post_type_slug( LDLMS_Post_Types::QUIZ )
			&& $screen->action !== 'add';
	}
}
