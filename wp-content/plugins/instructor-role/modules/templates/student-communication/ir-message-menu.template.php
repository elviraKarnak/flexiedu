<?php
/**
 * Message top menu.
 *
 * @since 3.6.0
 *
 * @var string  $msg_icon               Path to the message icon.
 * @var int     $instructor_id          ID of the course instructor.
 * @var array   $doubt_threads          Array of message threads for the post.
 * @var int     $user_id                User ID.
 * @var bool    $buddyboss_active       True if active theme is buddyboss, false otherwise.
 * @var int     $unread_thread_count    Count of unread threads.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<div class="ir-message-menu">
	<img src="<?php echo esc_attr( $msg_icon ); ?>" alt="<?php echo esc_attr__( 'Message List', 'wdm_instructor_role' ); ?>">
	<?php if ( intval( $unread_thread_count ) > 0 ) : ?>
		<span class="ir-msg-count" data-count="<?php echo esc_attr( $unread_thread_count ); ?>"><?php echo esc_html( $unread_thread_count ); ?></span>
	<?php endif; ?>
	<div class="ir-pointer-wrap"><span class="ir-pointer"></span></div>
	<?php if ( ! empty( $doubt_threads['threads'] ) ) : ?>
		<?php
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/student-communication/ir-message-notification.php',
				[
					'doubt_threads'    => $doubt_threads,
					'instructor_id'    => $instructor_id,
					'user_id'          => $user_id,
					'buddyboss_active' => $buddyboss_active,
					'is_header'        => true,
				]
			);
		?>
	<?php else : ?>
		<div class="ir-message-notification">
			<span class="no-messages">
				<?php esc_html_e( 'No doubts found for this post', 'wdm_instructor_role' ); ?>
			</span>
		</div>
	<?php endif; ?>
</div>
