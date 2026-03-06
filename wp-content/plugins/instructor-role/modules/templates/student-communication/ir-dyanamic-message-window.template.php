<?php
/**
 * Dynamic message modal window.
 *
 * @var array   $thread_data    Thread data for the current doubt.
 * @var object  $instance       Instance of the student communication module class.
 *
 * @since 3.6.0
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<?php if ( ! empty( $thread_data ) ) : ?>
	<?php foreach ( $thread_data as $thread_message ) : ?>
		<?php
			$class = '';
		if ( get_current_user_ID() == $thread_message->sender_id ) {
			$class = 'irc-self';
		}
		?>
		<div class="ir-received-msg-item <?php echo $class; ?>">
			<div class="ir-received-msg-time"><b><?php echo esc_html( $instance->get_day_since( strtotime( $thread_message->date_sent ) ) ); ?></b></div>
			<div class="ir-received-msg">
				<div class="ir-msg-from">
					<div class="ir-inst-dp"><?php echo get_avatar( $thread_message->sender_id ); ?></div>
					<div class="ir-msg-name">
						<span><?php echo get_the_author_meta( 'display_name', $thread_message->sender_id ); ?></span>
						<span class="ir-msg-time"><?php echo esc_html( bp_core_time_since( strtotime( $thread_message->date_sent ) ) ); ?></span>
					</div>
				</div>
				<?php echo nl2br( $thread_message->message ); ?>
			</div>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
