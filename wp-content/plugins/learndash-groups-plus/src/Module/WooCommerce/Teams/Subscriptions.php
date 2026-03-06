<?php
/**
 * WooCommerce Team Subscription functionality.
 *
 * @since 2.0.0
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Module\WooCommerce\Teams;

use LearnDash\Groups_Plus\Module\Base as Module_Base;
use LearnDash\Groups_Plus\Module\Module_Interface;

use LearnDash\Core\Utilities\Cast;

use LearnDash\Groups_Plus\Utility\SharedFunctions;

use WC_Subscription;

/**
 * Class Teams Subscriptions.
 *
 * @since 2.0.0
 */
class Subscriptions extends Module_Base implements Module_Interface {
	/**
	 * Method to contain function call related to action hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function hook_actions(): void {
		if (
			! SharedFunctions::is_woocommerce_active()
			|| 'no' === get_option( 'enable_wc', 'no' )
		) {
			return;
		}

		add_action(
			'woocommerce_subscription_status_changed',
			[ $this, 'alter_group' ],
			99,
			4
		);
	}

	/**
	 * When a Subscription is no longer Active, mark the Team as a Draft and remove the Users and Courses from it.
	 *
	 * When a Subscription is marked Active, re-add any removed Users and Courses and set it to Published.
	 *
	 * @since 2.0.0
	 *
	 * @param int             $subscription_id Subscription ID.
	 * @param string          $new_status      New status.
	 * @param string          $old_status      Old status.
	 * @param WC_Subscription $subscription    Subscription object.
	 *
	 * @return void
	 */
	public function alter_group( $subscription_id, $new_status, $old_status, $subscription ) {
		$order_id                           = $subscription->get_last_order( 'ids', [ 'parent' ] );
		$primary_group_ids                  = Cast::to_string( get_post_meta( $order_id, SharedFunctions::$linked_group_id_meta, true ) );
		$has_group_created_for_courses_sell = Cast::to_bool( get_post_meta( $order_id, 'has_group_created_for_courses_sell', true ) );

		if (
			empty( $primary_group_ids )
			|| $has_group_created_for_courses_sell !== true
		) {
			return;
		}

		// Handle both single group ID and multiple group IDs (comma-separated).
		$group_ids = array_map(
			static function ( $group_id ) {
				return Cast::to_int( trim( $group_id ) );
			},
			explode( ',', $primary_group_ids ),
		);

		foreach ( $group_ids as $primary_group_id ) {
			$primary_group = get_post( $primary_group_id );

			if (
				empty( $primary_group )
				|| ! is_a( $primary_group, 'WP_Post' )
			) {
				continue;
			}

			if ( $new_status === 'active' ) {
				/**
				 * Assign courses back to group
				 */
				$group_course_ids = get_post_meta( $primary_group_id, 'group_courses', true );
				if (
					! empty( $group_course_ids )
					&& is_array( $group_course_ids )
				) {
					foreach ( $group_course_ids as $course_id ) {
						$course_id = Cast::to_int( $course_id );

						ld_update_course_group_access( $course_id, $primary_group_id, false );
						delete_transient( "learndash_course_groups_{$course_id}" );
					}
				}

				delete_post_meta( $primary_group_id, 'group_courses' );

				/**
				 * Assign users back to group
				 */
				$user_ids = get_post_meta( $primary_group_id, 'group_users', true );
				if (
					! empty( $user_ids )
					&& is_array( $user_ids )
				) {
					foreach ( $user_ids as $user_id ) {
						$user_id = Cast::to_int( $user_id );

						ld_update_group_access( $user_id, $primary_group_id, false );
						delete_transient( "learndash_user_groups_{$user_id}" );
					}
				}

				delete_post_meta( $primary_group_id, 'group_users' );

				$primary_group->post_status = 'publish';
			} else {
				/**
				 * Remove courses from Group
				 */
				$group_course_ids = learndash_group_enrolled_courses( $primary_group_id );
				if (
					! empty( $group_course_ids )
					&& is_array( $group_course_ids )
				) {
					foreach ( $group_course_ids as $course_id ) {
						ld_update_course_group_access( $course_id, $primary_group_id, true );
						delete_transient( "learndash_course_groups_{$course_id}" );
					}
				}

				update_post_meta( $primary_group_id, 'group_courses', $group_course_ids );

				/**
				 * Remove users access from Group
				 */
				$users = learndash_get_groups_user_ids( $primary_group_id );
				if ( ! empty( $users )
					&& is_array( $users )
				) {
					foreach ( $users as $user_id ) {
						ld_update_group_access( $user_id, $primary_group_id, true );
						delete_transient( "learndash_user_groups_{$user_id}" );
					}
				}

				update_post_meta( $primary_group_id, 'group_users', $users );

				$primary_group->post_status = 'draft';
			}

			wp_update_post(
				[
					'ID'          => $primary_group->ID,
					'post_status' => $primary_group->post_status,
				]
			);
		}
	}
}
