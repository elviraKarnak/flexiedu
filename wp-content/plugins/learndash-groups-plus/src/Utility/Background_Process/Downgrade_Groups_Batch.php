<?php
/**
 * Downgrade Teams to LearnDash Groups background process.
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
 * Class Downgrade_Groups_Batch
 */
class Downgrade_Groups_Batch extends LearnDash_Groups_Plus_WP_Background_Process {
	/**
	 * @var string
	 */
	protected $action = 'downgrade_groups';

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
			$children = learndash_get_group_children( $primary_group->ID );
			if ( ! empty( $children ) && is_array( $children ) ) {
				foreach ( $children as $child_id ) {
					wp_delete_post( $child_id );
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
		update_option( 'started-learndash-groups-plus-downgrade-groups-process', true );
		delete_option( 'started-learndash-groups-plus-downgrade-groups-process' );
	}
}
