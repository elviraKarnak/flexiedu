<?php
/**
 * Trigger class for LearnDash Notifications
 *
 * @since 4.3.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/core/integrations/learndash-notifications
 */

defined( 'ABSPATH' ) || die();

use LearnDash_Notification\Notification;
use LearnDash_Notification\Trigger;

/**
 * Class LD_GB_Notifications_Manual_Grade_Added
 *
 * Trigger class for LearnDash Notifications
 *
 * @since 4.3.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/core/integrations/learndash-notifications
 */
final class LD_GB_Notifications_Manual_Grade_Added extends Trigger {
	/**
	 * The trigger slug.
	 *
	 * @var string
	 */
	protected $trigger = 'ld_gb_manual_grade_added';

	/**
	 * Constructor for LD_GB_Notifications_Manual_Grade_Added
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function __construct() {
		add_filter( 'learndash_notifications_are_triggering_objects_valid', [ $this, 'learndash_notifications_are_triggering_objects_valid' ], 10, 4 ); }

	/**
	 * The callback method for our Trigger
	 *
	 * @param array $args  Args sent by the fired action.
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function monitor( $args ) {
		$user_id      = $args['user_id'] ?? null;
		$gradebook_id = $args['gradebook'] ?? null;

		$models = $this->get_notifications( $this->trigger );
		if ( empty( $models ) ) {
			return;
		}

		$this->log( '==========Job start========' );
		$this->log( sprintf( 'Process %d notifications', count( $models ) ) );

		foreach ( $models as $model ) {
			if ( ! $this->is_valid( $model, $args ) ) {
				continue;
			}

			$emails = $model->gather_emails( $user_id );

			if ( absint( $model->delay ) ) {
				$this->queue_use_db( $emails, $model, $args );
			} else {
				$this->send( $emails, $model, $args );
				$model->mark_sent( $user_id, $this->trigger, $model->post->ID, $gradebook_id );
				$this->log( 'Done, moving next if any' );
			}
		}   }

	/**
	 * A base point for monitoring the events
	 *
	 * @access public
	 * @since 4.3.0
	 * @return void
	 */
	public function listen() {
		add_action( 'ld_gb_manual_grade_added', [ &$this, 'monitor' ] );
		add_action( 'leanrdash_notifications_send_delayed_email', [ &$this, 'send_db_delayed_email' ] ); // cspell:disable-line -- leanrdash is intentionally misspelled.
	}

	/**
	 * Preform some last minute checks before sending out a delayed email
	 *
	 * @param Notification $model The Notification model.
	 * @param array        $args  Args sent by the fired action.
	 *
	 * @access protected
	 * @since 4.3.0
	 * @return bool                Allowed/Disallowed
	 */
	protected function can_send_delayed_email( Notification $model, $args ) {
		$user_id      = $args['user_id'] ?? null;
		$gradebook_id = $args['gradebook'] ?? null;

		if ( ! $this->is_valid(
			$model,
			[
				'user_id'   => $user_id,
				'gradebook' => $gradebook_id,
			]
		) ) {
			return false;
		}

		return true;    }

	/**
	 * Check if triggering object of a notification is valid.
	 *
	 * @param bool         $valid         Valid/Invalid.
	 * @param string       $trigger       The trigger being ran.
	 * @param Notification $notification  Notification Object.
	 * @param array        $args          Args sent by the fired action.
	 *
	 * @access protected
	 * @since 4.3.0
	 * @return bool                        Valid/Invalid.
	 */
	public function learndash_notifications_are_triggering_objects_valid( bool $valid, string $trigger, Notification $notification, array $args ) {
		$notification_gradebook_id = get_post_meta(
			$notification->post->ID,
			'_ld_notifications_gradebook_id',
			true
		);

		// Waterfall.
		if ( ! empty( $args['gradebook'] ) && ! empty( $notification_gradebook_id ) ) {
			$valid = $this->is_value_valid( $args['gradebook'], $notification_gradebook_id );
		}

		return $valid;
	}
}
