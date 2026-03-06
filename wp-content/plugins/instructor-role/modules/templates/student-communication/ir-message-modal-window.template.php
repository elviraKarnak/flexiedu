<?php
/**
 * Message modal window.
 *
 * @since 3.6.0
 *
 * @var int     $course_id              ID of the course.
 * @var int     $user_id                ID of the User.
 * @var array   $message_editor_data    Message editor configuration data.
 * @var array   $course_lessons         Array of lessons in the course.
 * @var array   $lesson_topics          Array of lessons in the course.
 * @var WP_Post $current_post           WP Post object.
 * @var int     $selected_lesson        Lesson related to current post.
 * @var int     $selected_topic         Topic related to current post.
 * @var int     $instructor_id          ID of the course instructor.
 * @var array   $doubt_threads          Array of message threads for the post.
 * @var int     $unread_thread_count    Unread thread count.
 * @var bool    $buddyboss_active       True if active theme is buddyboss, false otherwise.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>
<div class="ir-ask-doubts">
	<div class="ir-msg-inner">
		<span class="ir-close-note"><i class="irc-icon-Close"></i></span>
		<div class="ir-inst-dp">
			<?php echo get_avatar( $instructor_id ); ?>
		</div>
		<div class="ir-inst-msg">
			<p><b><?php esc_html_e( 'Hello! ', 'wdm_instructor_role' ); ?></b>
				<?php
					printf(
						/* translators: Instructor Name */
						esc_html__( 'I am %s, your instructor.', 'wdm_instructor_role' ),
						get_the_author_meta( 'display_name', $instructor_id )
					);
					?>
			</p>
			<p>
			<?php if ( ir_get_settings( 'stu_com_editor_set_popup' ) ) : ?>
				<?php esc_html_e( ir_get_settings( 'stu_com_editor_set_popup' ) ); ?>
			<?php else : ?>
				<?php esc_html_e( 'If you have any doubts, feel free to message me.', 'wdm_instructor_role' ); ?>
			<?php endif; ?>
			</p>
			<div class="ir-send-doubts">
				<button class="ir-button ir-bg-color">
				<?php if ( ir_get_settings( 'stu_com_editor_set_button' ) ) : ?>
					<?php esc_html_e( ir_get_settings( 'stu_com_editor_set_button' ) ); ?>
				<?php else : ?>
					<?php esc_html_e( 'send your doubts', 'wdm_instructor_role' ); ?>
				<?php endif; ?>
				</button>
			</div>
		</div>
	</div>
</div>

<?php
	// Get the message notification list template.
	ir_get_template(
		INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/student-communication/ir-message-notification.php',
		[
			'doubt_threads'    => $doubt_threads,
			'instructor_id'    => $instructor_id,
			'user_id'          => $user_id,
			'buddyboss_active' => $buddyboss_active,
			'is_header'        => false,
		]
	);
	?>

<div class="ir-question-mark" title="<?php esc_html_e( 'send your doubts', 'wdm_instructor_role' ); ?>">
	<span class="ir-question-bg ir-bg-color">
		<?php if ( intval( $unread_thread_count ) > 0 ) : ?>
			<span class="ir-msg-count" data-count="<?php echo esc_attr( $unread_thread_count ); ?>"><?php echo esc_html( $unread_thread_count ); ?></span>
		<?php endif; ?>
		<i class="irc-icon-Question-fill"></i>
	</span>
</div>

<div class="ir-msg-toast">
	<div class="ir-msg-sent">
		<i class="irc-icon-Correct-fill ir-type"></i>
		<span>
		</span>
		<i class="irc-icon-Close"></i>
	</div>
</div>

<div class="ir-msg-box">
	<div class="ir-msg-box-inner">
		<div class="ir-ch-wrap">
			<div class="ir-ch-name">
				<h3>
					<b>
					<?php
						printf(
							/* translators: Course label */
							esc_html__( '%s :', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( 'course' )
						);
						?>
					</b><?php echo wp_trim_words( get_the_title( $course_id ) ); ?></h3>
			</div>
			<div class="ir-ch-actions">
				<?php // cspell:disable-next-line . ?>
				<span class="ir-minimize" title="Minimize"><i class="irc-icon-Minimise"></i></span>
				<span class="ir-maximize" title="Maximize"><i class="irc-icon-Expand"></i><i class="irc-icon-Collapse"></i></span>
				<span class="ir-close" title="Close"><i class="irc-icon-Close"></i></span>
			</div>
		</div>
		<div class="ir-lh-wrap">
			<span class="ir-dropdown"><i class="irc-icon-Arrow"></i></span>
			<select class="ir-lh" data-nonce="<?php echo wp_create_nonce( 'ir_get_lesson_topic_nonce' ); ?>">
				<option value="0">
					<?php
					printf(
						/* translators: Lesson label */
						esc_html_x( 'Select %s', 'Lesson', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'lesson' )
					);
					?>
				</option>
				<?php foreach ( $course_lessons as $lesson ) : ?>
					<option value="<?php echo esc_attr( $lesson ); ?>" <?php selected( $selected_lesson, $lesson ); ?>>
						<?php
						echo wp_trim_words(
							sprintf(
								/* translators: 1: Lesson label 2: Lesson title */
								esc_html_x( '%1$s: %2$s', 'Lessons', 'wdm_instructor_role' ),
								\LearnDash_Custom_Label::get_label( 'lesson' ),
								get_the_title( $lesson )
							)
						);
						?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="ir-th-wrap">
			<span class="ir-dropdown"><i class="irc-icon-Arrow"></i></span>
			<select class="ir-th">
				<option value="0">
					<?php
					printf(
						/* translators: Topic label */
						esc_html_x( 'Select %s', 'Topic', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'topic' )
					);
					?>
				</option>
				<?php foreach ( $lesson_topics as $topic ) : ?>
					<option value="<?php echo esc_attr( $topic ); ?>" <?php selected( $selected_topic, $topic ); ?>>
						<?php
						echo wp_trim_words(
							sprintf(
								/* translators: 1: Topic label 2: Topic title */
								esc_html_x(
									'%1$s: %2$s',
									'Topic',
									'wdm_instructor_role'
								),
								\LearnDash_Custom_Label::get_label( 'topic' ),
								get_the_title( $topic )
							)
						);
						?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="ir-sh-wrap">
			<input type="text" class="ir-sub" placeholder="<?php esc_attr_e( 'Type your subject', 'wdm_instructor_role' ); ?>">
		</div>
		<div class="ir-thread-loader">
			<img src="<?php echo plugins_url( '../images/loader.gif', __DIR__ ); ?>">
		</div>
		<div class="ir-thread <?php echo esc_attr( empty( $thread_data ) ? '' : 'type-thread' ); ?>" data-thread-id="0">

			<div class="ir-received-msg-wrap"></div>
			<?php wp_editor( '', $message_editor_data['editor_id'], $message_editor_data['editor_settings'] ); ?>
		</div>
		<?php wp_nonce_field( 'ir_send_doubt_to_instructor', 'ir_communication_nonce' ); ?>
		<input type="hidden" id="ir_instructor_id" name="ir_instructor_id" value="<?php echo absint( esc_attr( $instructor_id ) ); ?>">
	</div>
</div>
