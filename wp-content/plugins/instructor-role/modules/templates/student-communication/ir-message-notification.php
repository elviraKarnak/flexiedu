<?php
/**
 * Message notification modal window.
 *
 * @since 3.6.0
 *
 * @var array   $doubt_threads      Array of message threads for the post.
 * @var int     $instructor_id      ID of the course instructor.
 * @var int     $user_id            User ID.
 * @var bool    $buddyboss_active   True if active theme is buddyboss, false otherwise.
 * @var bool    $is_header          True if template used in header, false otherwise.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="ir-message-notification">
	<div class="ir-msg-notification-inner">
		<span class="ir-close-note"><i class="irc-icon-Close"></i></span>
		<div class="ir-msg-items">

			<?php if ( ! empty( $doubt_threads ) && array_key_exists( 'threads', $doubt_threads ) && null !== $doubt_threads['threads'] ) : ?>

				<?php foreach ( $doubt_threads['threads'] as $thread ) : ?>

					<?php $last_message = ( $buddyboss_active ) ? current( $thread->messages ) : end( $thread->messages ); ?>

					<div class="ir-msg-item <?php echo ( ! empty( $thread->unread_count ) ) ? 'ir-unread-thread' : ''; ?>" data-doubt-id="<?php echo esc_attr( $thread->thread_id ); ?>">
						<?php if ( $is_header ) : ?>
							<a href="<?php echo bp_loggedin_user_domain() . bp_get_messages_slug(); ?>" target="_blank">
						<?php endif; ?>
							<div class="ir-inst-dp"><?php echo get_avatar( $last_message->sender_id ); ?></div>
								<div class="ir-msg-content">
									<p>
									<?php if ( $last_message->sender_id == $user_id ) : ?>

										<?php if ( 1 == count( $thread->messages ) ) : ?>

											<?php
												printf(
													/* translators: Message subject */
													esc_html__( 'You sent a doubt: %s', 'wdm_instructor_role' ),
													$thread->last_message_subject
												);
											?>

										<?php else : ?>

											<?php
												printf(
													/* translators: 1: User name 2: Message title */
													esc_html__( 'You replied to %1$s: %2$s', 'wdm_instructor_role' ),
													get_the_author_meta( 'display_name', $last_message->sender_id ),
													$thread->last_message_subject
												);
											?>

										<?php endif; ?>

									<?php else : ?>

										<?php
											printf(
												/* translators: 1: User name 2: Message subject */
												esc_html__( '%1$s replied to your doubt: %2$s', 'wdm_instructor_role' ),
												get_the_author_meta( 'display_name', $last_message->sender_id ),
												$thread->last_message_subject
											);
										?>

									<?php endif; ?>

									</p>
									<span class="ir-msg-time"><?php echo esc_html( bp_core_time_since( strtotime( $last_message->date_sent ) ) ); ?></span>
								</div>
							<?php wp_nonce_field( 'ir_get_doubt_messages', 'ir_doubt_thread_nonce_' . $thread->thread_id ); ?>
						<?php if ( $is_header ) : ?>
							</a>
						<?php endif; ?>
					</div>

				<?php endforeach; ?>

			<?php endif; ?>

		</div>
		<div class="ir-message-doubts">
			<a class="ir-color" href="<?php echo bp_loggedin_user_domain() . bp_get_messages_slug(); ?>" target="_blank"><?php esc_html_e( 'View All', 'wdm_instructor_role' ); ?></a>
			<button class="ir-button ir-bg-color"><?php esc_html_e( 'Message New doubts', 'wdm_instructor_role' ); ?></button>
		</div>
	</div>
</div>
