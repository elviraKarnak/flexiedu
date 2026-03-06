<?php
/**
 * Quiz Admin Edit functionality.
 *
 * @since 5.9.7
 *
 * @package LearnDash\Instructor_Role
 */

namespace LearnDash\Instructor_Role\Admin\Quiz;

use LDLMS_Post_Types;

/**
 * Quiz Admin Edit functionality.
 *
 * @since 5.9.7
 */
class Edit {
	/**
	 * Add frontend edit button to the actions dropdown.
	 * This only applies for the Quiz Edit page on LearnDash 4.22.1 and above.
	 *
	 * @param array{title: string, link: string, isExternal: bool}[] $action_menu      An array of header action menu items.
	 * @param string                                                 $menu_tab_key     Menu tab key.
	 * @param string                                                 $screen_post_type Screen post type slug.
	 *
	 * @return array{title: string, link: string, isExternal: bool}[]
	 */
	public function add_frontend_edit_button_to_actions_dropdown( $action_menu, string $menu_tab_key, string $screen_post_type ) {
		$post_id = get_the_ID();

		if (
			! $post_id // /quiz-builder/ without a Post ID is not a valid URL.
			|| ! ir_get_settings( 'ir_enable_frontend_dashboard' )
			|| $screen_post_type !== learndash_get_post_type_slug( LDLMS_Post_Types::QUIZ )
			|| version_compare( constant( 'LEARNDASH_VERSION' ), '4.22.1', '<' ) // @phpstan-ignore-line -- This constant can change.
		) {
			return $action_menu;
		}

		$url = trailingslashit( get_site_url() ) . "quiz-builder/{$post_id}";

		array_unshift(
			$action_menu,
			[
				'title'      => sprintf(
					/* translators: placeholder: Quiz Label */
					esc_html__( 'Edit via Frontend %s Creator', 'wdm_instructor_role' ),
					learndash_get_custom_label( 'quiz' )
				),
				'link'       => $url,
				'isExternal' => false,
			]
		);

		return $action_menu;
	}
}
