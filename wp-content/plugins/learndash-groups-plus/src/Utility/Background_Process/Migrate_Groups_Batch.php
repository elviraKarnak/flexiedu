<?php
/**
 * Upgrade LearnDash Groups to Teams background process.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Utility\Background_Process;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use LearnDash_Groups_Plus_WP_Background_Process;

/**
 * Class Migrate_Groups_Batch
 */
class Migrate_Groups_Batch extends LearnDash_Groups_Plus_WP_Background_Process {
	/**
	 * @var string
	 */
	protected $action = 'migrate_groups';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $primary_group ) {
		if ( $primary_group instanceof \WP_Post ) {
			$has_children = learndash_get_group_children( $primary_group->ID );
			if ( empty( $has_children ) ) {
				// Get the original post id
				$post_id = $primary_group->ID;

				// the original post data then
				$post = get_post( $post_id );

				$current_user    = wp_get_current_user();
				$new_post_author = $current_user->ID;

				// if post data exists (I am sure it is, but just in a case), create the post duplicate
				if ( $post ) {

					// new post data array
					$args = array(
						'comment_status' => $post->comment_status,
						'ping_status'    => $post->ping_status,
						'post_author'    => $new_post_author,
						'post_content'   => $post->post_content,
						'post_excerpt'   => $post->post_excerpt,
						'post_name'      => $post->post_name,
						'post_parent'    => $post_id,
						'post_password'  => $post->post_password,
						'post_status'    => 'publish',
						'post_title'     => $post->post_title,
						'post_type'      => $post->post_type,
						'to_ping'        => $post->to_ping,
						'menu_order'     => $post->menu_order,
					);

					$new_post_id = wp_insert_post( $args );

					// hook
					do_action( 'create_groups_plus', $new_post_id, $current_user->ID );

					/*
						* get all current post terms ad set them to the new post draft
						*/
					$taxonomies = get_object_taxonomies( get_post_type( $post ) ); // returns array of taxonomy names for post type, ex array("category", "post_tag");
					if ( $taxonomies ) {
						foreach ( $taxonomies as $taxonomy ) {
							$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
							wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
						}
					}

					// duplicate all post meta
					$post_meta = get_post_meta( $post_id );
					if ( $post_meta ) {

						foreach ( $post_meta as $meta_key => $meta_values ) {

							if ( '_wp_old_slug' == $meta_key ) { // do nothing for this meta key
								continue;
							}

							foreach ( $meta_values as $meta_value ) {
								add_post_meta( $new_post_id, $meta_key, $meta_value );
							}
						}
					}

					// clone course
					$group_courses_new = learndash_group_enrolled_courses( $post_id );
					learndash_set_group_enrolled_courses( $new_post_id, $group_courses_new );

					// clone users
					$group_users_new = learndash_get_groups_user_ids( $post_id );
					learndash_set_groups_users( $new_post_id, $group_users_new );

					// clone group leader
					$group_leaders_new = learndash_get_groups_administrator_ids( $post_id );
					learndash_set_groups_administrators( $new_post_id, $group_leaders_new );

				} else {
					// wp_die( 'Post creation failed, could not find original post.' );
				}
			}
		}
		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
		update_option( 'started-learndash-groups-plus-migrate-groups-process', true );
		delete_option( 'started-learndash-groups-plus-migrate-groups-process' );
	}
}
