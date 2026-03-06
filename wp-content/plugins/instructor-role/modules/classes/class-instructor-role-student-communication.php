<?php
/**
 * Student Communication Module
 *
 * @since 3.6.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Student_Communication' ) ) {
	/**
	 * Class Instructor Role Student Communication Module
	 */
	class Instructor_Role_Student_Communication {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 3.3.0
		 */
		protected static $instance = null;

		/**
		 * Plugin Slug
		 *
		 * @var string  $plugin_slug
		 *
		 * @since 3.3.0
		 */
		protected $plugin_slug = '';

		/**
		 * Whether active theme is buddyboss
		 *
		 * @var bool  $buddyboss_active
		 *
		 * @since 3.6.0
		 */
		protected $buddyboss_active = false;

		public function __construct() {
			$this->plugin_slug = INSTRUCTOR_ROLE_TXT_DOMAIN;
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 3.5.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Add student communication message modal window.
		 *
		 * @since 3.6.0
		 *
		 * @param int $course_id    Course ID.
		 * @param int $user_id      User ID.
		 */
		public function add_message_modal_window( $course_id, $user_id ) {
			global $post;

			// Check if logged in user and lesson, topic and assignment page.
			if ( ! is_user_logged_in() || ! is_singular( [ 'sfwd-lessons', 'sfwd-topic' ] ) ) {
				return;
			}

			// Data for message editor.
			$message_editor_data = $this->get_message_editor_data();

			// Get course lessons list.
			$course_lessons  = array_column( learndash_course_get_lessons( $course_id ), 'ID' );
			$lesson_topics   = [];
			$selected_lesson = 0;
			$selected_topic  = 0;

			$course = get_post( $course_id );
			// Get lesson topics list.
			if ( learndash_get_post_type_slug( 'lesson' ) === $post->post_type ) {
				$selected_lesson = $post->ID;
				$lesson_topics   = array_column( learndash_course_get_topics( $course_id, $selected_lesson ), 'ID' );
			} elseif ( learndash_get_post_type_slug( 'topic' ) === $post->post_type ) {
				$selected_topic  = $post->ID;
				$selected_lesson = learndash_get_lesson_id( $post->ID, $course_id );
				$lesson_topics   = array_column( learndash_course_get_topics( $course_id, $selected_lesson ), 'ID' );
			}

			// Get doubts for current lesson/topic.
			$doubt_threads       = $this->get_user_doubt_threads( $post, $user_id );
			$unread_thread_count = $this->get_user_post_unread_doubts_count( $post, $user_id, $doubt_threads );

			// Check if buddyboss theme active.
			$buddyboss_active = false;
			$active_theme     = wp_get_theme();
			if ( ! empty( $active_theme ) && 'buddyboss-theme' === $active_theme->template ) {
				$buddyboss_active = true;
			}

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/student-communication/ir-message-modal-window.template.php',
				[
					'course_id'           => $course_id,
					'user_id'             => $user_id,
					'message_editor_data' => $message_editor_data,
					'course_lessons'      => $course_lessons,
					'lesson_topics'       => $lesson_topics,
					'current_post'        => $post,
					'selected_lesson'     => $selected_lesson,
					'selected_topic'      => $selected_topic,
					'instructor_id'       => absint( $course->post_author ),
					'doubt_threads'       => $doubt_threads,
					'unread_thread_count' => $unread_thread_count,
					'buddyboss_active'    => $buddyboss_active,
				]
			);
		}

		/**
		 * Add student communication message modal window for buddyboss theme.
		 *
		 * @since 3.6.0
		 *
		 * @param int $course_id    Course ID.
		 * @param int $user_id      User ID.
		 */
		public function buddyboss_add_message_modal_window( $post_id, $course_id, $user_id ) {
			// Check if buddyboss is active theme.
			$active_theme = wp_get_theme();
			if ( ! empty( $active_theme ) && 'buddyboss-theme' === $active_theme->template ) {
				$this->add_message_modal_window( $course_id, $user_id );
			}
		}

		/**
		 * Add student communication message menu.
		 *
		 * @since 3.6.0
		 *
		 * @param int $course_id    Course ID.
		 * @param int $user_id      User ID.
		 */
		public function add_message_menu( $course_id, $user_id ) {
			global $post;

			if ( is_user_logged_in() && is_singular( [ 'sfwd-lessons', 'sfwd-topic' ] ) ) {
				// Get doubts for current lesson/topic.
				$doubt_threads       = $this->get_user_doubt_threads(
					$post,
					get_current_user_id(),
					3,
					[
						'meta_query' => [
							[
								'key' => 'ir_related_ld_post',
							],
						],
					]
				);
				$unread_thread_count = $this->get_user_post_unread_doubts_count( $post, $user_id, $doubt_threads );

				$course = get_post( $course_id );

				// Check if buddyboss theme active.
				$buddyboss_active = false;
				$active_theme     = wp_get_theme();
				if ( ! empty( $active_theme ) && 'buddyboss-theme' === $active_theme->template ) {
					$buddyboss_active = true;
				}

				ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/student-communication/ir-message-menu.template.php',
					[
						'msg_icon'            => plugins_url( 'media/message_list.svg', __DIR__ ),
						'instructor_id'       => absint( $course->post_author ),
						'doubt_threads'       => $doubt_threads,
						'user_id'             => $user_id,
						'buddyboss_active'    => $buddyboss_active,
						'unread_thread_count' => $unread_thread_count,
					]
				);
			}
		}

		/**
		 * Enqueue student communication message styles and scripts.
		 *
		 * @since 3.6.0
		 */
		public function enqueue_message_scripts() {
			// Enqueue styles and scripts for lesson, topic and assignment page.
			if ( is_user_logged_in() && is_singular( [ 'sfwd-lessons', 'sfwd-topic' ] ) ) {
				global $post;

				wp_enqueue_style(
					'ir-student-communication-message-styles',
					plugins_url( 'css/ir-student-communication-message.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/ir-student-communication-message.css' )
				);

				wp_enqueue_script(
					'ir-student-communication-message-script',
					plugins_url( 'js/dist/ir-student-communication-message.js', __DIR__ ),
					[ 'jquery' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/dist/ir-student-communication-message.js' )
				);

				wp_enqueue_script(
					'ir-student-communication-script',
					plugins_url( 'js/communication/ir-student-communication-script.js', __DIR__ ),
					[ 'jquery' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/communication/ir-student-communication-script.js' )
				);

				$current_user_id = get_current_user_id();
				$doubt_threads   = $this->get_user_doubt_threads( $post, $current_user_id );

				wp_localize_script(
					'ir-student-communication-message-script',
					'ir_communication_loc',
					[
						'ajax_url'      => admin_url( 'admin-ajax.php' ),
						'post_id'       => $post->ID,
						'course_id'     => learndash_get_course_id( $post->ID ),
						'has_doubts'    => ( empty( $doubt_threads['threads'] ) ) ? false : true,
						'doubt_threads' => $doubt_threads,
						'nonce'         => wp_create_nonce( 'ir_heartbeat_nonce_' . $post->ID ),
						'sending_text'  => __( 'Sending...', 'wdm_instructor_role' ),
						'send_text'     => __( 'Send', 'wdm_instructor_role' ),
					]
				);

				// Fetch color settings.
				$accent_color = ir_get_settings( 'stu_com_editor_accent_color' );
				if ( ! empty( $accent_color ) ) {
					$custom_css = "
                    .ir-button-alt, .ir-color{
                        color: {$accent_color} !important;
                    }

                    .ir-bg-color, #wp-ir_communication-wrap .mce-flow-layout-item .ir-doubt-send-btn.ir-valid button {
                        background-color: {$accent_color} !important;
                    }

                    .ir-ask-doubts, .ir-ask-doubts .ir-close-note, .ir-message-notification .ir-close-note{
                        border: 1px solid {$accent_color};
                    }
                    ";

					wp_add_inline_style( 'ir-student-communication-message-styles', $custom_css );
				}
			}
		}

		/**
		 * Send doubts to instructor
		 *
		 * @since 3.6.0
		 */
		public function ajax_ir_send_doubt_to_instructor() {
			$response = [
				'message' => __( 'The doubt could not be sent. Please contact the Admin or Instructor.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_send_doubt_to_instructor', 'ir_communication_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$lesson_id     = filter_input( INPUT_POST, 'lesson_id', FILTER_SANITIZE_NUMBER_INT );
			$topic_id      = filter_input( INPUT_POST, 'topic_id', FILTER_SANITIZE_NUMBER_INT );
			$subject       = sanitize_text_field( wp_unslash( ( $_POST['subject'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Check it later.
			$instructor_id = filter_input( INPUT_POST, 'instructor_id', FILTER_SANITIZE_NUMBER_INT );
			$thread_id     = filter_input( INPUT_POST, 'thread_id', FILTER_SANITIZE_NUMBER_INT );
			$message       = filter_input( INPUT_POST, 'message', FILTER_DEFAULT );

			// Validate subject only for new threads and message content both.
			if ( ( empty( $subject ) && empty( $thread_id ) ) || empty( $message ) ) {
				if ( empty( $subject ) ) {
					$response['message'] = __( 'Enter a Subject regarding your doubt in the chat to raise a doubt.', 'wdm_instructor_role' );
				} else {
					$response['message'] = __( 'Cannot send an empty message to the Instructor.', 'wdm_instructor_role' );
				}

				wp_send_json_error( $response );
			}

			// Validate if a lesson is selected.
			if ( empty( $lesson_id ) ) {
				$response['message'] = sprintf(
					/* translators: Lesson label */
					__( 'Select %s name in the chat to raise a doubt.', 'wdm_instructor_role' ),
					\LearnDash_Custom_Label::get_label( 'lesson' )
				);
				wp_send_json_error( $response );
			}

			// Validate recipient.
			$instructor = get_userdata( $instructor_id );
			if ( empty( $instructor ) ) {
				wp_send_json_error( $response );
			}

			// Check whether to create new thread or update existing.
			if ( empty( $thread_id ) ) {
				// Attempt to create new message thread.
				$updated_thread_id = messages_new_message(
					[
						'recipients'    => $instructor_id,
						'subject'       => $subject,
						'content'       => $message,
						'error_type'    => 'wp_error',
						'append_thread' => false,
					]
				);

				$response = [
					'message' => __( 'Your doubt has been sent to the Instructor successfully.', 'wdm_instructor_role' ),
					'type'    => 'success',
				];
			} else {
				// Attempt to send the message.
				$updated_thread_id = messages_new_message(
					[
						'thread_id'  => $thread_id,
						'recipients' => $instructor_id,
						'content'    => $message,
						'error_type' => 'wp_error',
					]
				);
				$response          = [
					'message' => __( 'Your reply/doubt on this conversation has been sent successfully.', 'wdm_instructor_role' ),
					'type'    => 'success',
				];
			}

			// Send the message.
			if ( intval( $updated_thread_id ) > 0 ) {
				// Get thread messages.
				$thread_messages = \BP_Messages_Thread::get_messages( $updated_thread_id );

				// Get the last updated message of the thread.
				$last_message = is_array( $thread_messages ) ? array_pop( $thread_messages ) : $thread_messages[0];
				$message_id   = $last_message->id;

				$related_ld_post_id = empty( $topic_id ) ? $lesson_id : $topic_id;
				// Save course, lesson and topic details to thread.
				bp_messages_add_meta(
					$message_id,
					'ir_related_ld_post',
					$related_ld_post_id
				);

				wp_send_json_success( $response );
				// Message could not be sent.
			} else {
				$response['message'] = $updated_thread_id->get_error_message();
				$response['type']    = 'error';
				wp_send_json_error( $response );
			}
		}

		/**
		 * Get list of topics for a lesson.
		 *
		 * @since 3.6.0
		 */
		public function ajax_ir_get_lesson_topics() {
			$response = [
				'message' => __( 'Some error occurred. Please reload the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_get_lesson_topic_nonce', 'ir_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$lesson_id = filter_input( INPUT_POST, 'lesson_id', FILTER_SANITIZE_NUMBER_INT );
			$course_id = filter_input( INPUT_POST, 'course_id', FILTER_SANITIZE_NUMBER_INT );

			// Validate if a lesson is selected.
			if ( empty( $lesson_id ) ) {
				$response['message'] = __( 'Some error occurred. Please reload the page and try again.', 'wdm_instructor_role' );
				wp_send_json_error( $response );
			}

			// Validate Course ID.
			if ( empty( $course_id ) ) {
				$response['message'] = sprintf(
					/* translators: Course label */
					__( 'Some error occurred. %s either trashed or not found. Please contact site administrator.', 'wdm_instructor_role' ),
					\LearnDash_Custom_Label::get_label( 'course' )
				);
				wp_send_json_error( $response );
			}

			$topic_list = learndash_get_topic_list( $lesson_id, $course_id );

			$topic_dropdown = ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/student-communication/ir-message-topic-list.template.php',
				[
					'topic_list' => $topic_list,
				],
				1
			);

			wp_send_json_success(
				[
					'topic_dropdown' => $topic_dropdown,
					'type'           => 'success',
				]
			);
		}

		/**
		 * Get all buddypress message threads classified as doubts for current user.
		 *
		 * @since 3.6.0
		 *
		 * @param WP_Post $post     Current WP post.
		 * @param int     $user_id      ID of the user.
		 * @param int     $max          Max number of threads to fetch. Defaults to 3.
		 * @param string  $args      Array of arguments.
		 *
		 * @return mixed            Array of message threads on success. Boolean false on failure.
		 */
		public function get_user_doubt_threads( $post, $user_id, $max = 3, $atts = [] ) {
			$user_threads = [];

			// Check if buddypress active.
			if ( ! function_exists( 'buddypress' ) ) {
				return $user_threads;
			}

			$args = shortcode_atts(
				[
					'user_id'    => $user_id,
					'box'        => 'sentbox', // cspell:disable-line .
					'type'       => 'all',
					'meta_query' => [
						[
							'key'   => 'ir_related_ld_post',
							'value' => $post->ID,
						],
					],
				],
				$atts
			);

			// Get message threads for post.
			$user_threads = \BP_Messages_Thread::get_current_threads_for_user( $args );

			// Check if buddyboss theme active.
			$active_theme = wp_get_theme();
			if ( ! empty( $active_theme ) && 'buddyboss-theme' === $active_theme->template ) {
				$this->buddyboss_active = true;
			}

			$message_threads = $user_threads['threads'];
			if ( ! empty( $message_threads ) ) {
				// Sort based on last message date.
				usort( $message_threads, [ $this, 'sort_threads_on_last_message_date' ] );

				// Return only limited number of threads.
				$message_threads = array_slice( $message_threads, 0, $max );
			}

			$user_threads['threads'] = $message_threads;

			/**
			 * Filter list of doubts threads.
			 *
			 * @since 3.6.0
			 *
			 * @param array $user_threads       Array of doubt threads related to the post.
			 * @param WP_Post $post             Post related to the doubts.
			 * @param int $user_id              ID of the user.
			 */
			return apply_filters( 'ir_filter_user_doubt_threads', $user_threads, $post, $user_id );
		}

		/**
		 * Sort threads based on last sent message date
		 *
		 * @since 3.6.0
		 *
		 * @param object $thread_1  Buddypress message thread being compared.
		 * @param object $thread_2  Buddypress message thread being compared against.
		 */
		public function sort_threads_on_last_message_date( $thread_1, $thread_2 ) {
			$last_message_1 = ( $this->buddyboss_active ) ? current( $thread_1->messages ) : end( $thread_1->messages );
			$last_message_2 = ( $this->buddyboss_active ) ? current( $thread_2->messages ) : end( $thread_2->messages );
			$date_1         = strtotime( $last_message_1->date_sent );
			$date_2         = strtotime( $last_message_2->date_sent );

			$diff = $date_1 - $date_2;
			if ( 0 === $diff ) {
				return 0;
			}
			return ( 0 > $diff ) ? 1 : -1;
		}

		/**
		 * Send doubts to instructor
		 *
		 * @since 3.6.0
		 */
		public function ajax_ir_get_doubt_messages() {
			$response = [
				'message' => __( 'Thread messages not found. Please try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_get_doubt_messages', 'ir_get_doubt_messages_nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$thread_id     = filter_input( INPUT_POST, 'thread_id', FILTER_SANITIZE_NUMBER_INT );
			$post_id       = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
			$instructor_id = filter_input( INPUT_POST, 'instructor_id', FILTER_SANITIZE_NUMBER_INT );

			// Validate thread id.
			if ( empty( $thread_id ) ) {
				wp_send_json_error( $response );
			}

			// Get thread messages.
			$thread_messages = \BP_Messages_Thread::get_messages( $thread_id );
			$thread_obj      = new \BP_Messages_Thread( $thread_id );

			// Mark thread as read.
			$read_messages = messages_mark_thread_read( $thread_id );

			if ( false != $read_messages ) {
				$read_messages = $thread_obj->unread_count;
			}

			// If buddyboss theme, sort messages.
			$active_theme = wp_get_theme();
			if ( ! empty( $active_theme ) && 'buddyboss-theme' === $active_theme->template ) {
				sort( $thread_messages );
			}

			// Get subject from the first message.
			$subject = '';
			if ( ! empty( $thread_messages ) ) {
				$subject = $thread_messages[0]->subject;
			}

			$thread_history_html = ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/student-communication/ir-dyanamic-message-window.template.php', // cspell:disable-line .
				[
					'thread_data' => $thread_messages,
					'instance'    => $this,
				],
				1
			);

			$new_message_button_html = ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/student-communication/ir-new-message-button-box.template.php',
				[],
				1
			);

			$response = [
				'type'                    => 'success',
				'thread_history_html'     => $thread_history_html,
				'new_message_button_html' => $new_message_button_html,
				'doubt_subject'           => $subject,
				'read_messages'           => $read_messages,
			];

			wp_send_json_success( $response );
		}

		/**
		 * Get message editor data for configuring the editor.
		 *
		 * @since 3.6.0
		 *
		 * @param string $editor_id     ID of the editor. Defaults to 'ir_communication'.
		 */
		public function get_message_editor_data( $editor_id = 'ir_communication' ) {
			$message_editor_data = [
				'editor_id'       => 'ir_communication',
				'editor_settings' => [
					'wpautop'       => false,
					'media_buttons' => true,
					'textarea_name' => $editor_id,
					'textarea_rows' => get_option( 'default_post_edit_rows', 10 ),
					'tabindex'      => '',
					'editor_css'    => '',
					'editor_class'  => '',
					'teeny'         => false,
					'dfw'           => true,
					'tinymce'       => [
						'toolbar1' => 'bold,italic,bullist,numlist,wp_add_media,ir_doubt_send_button', // cspell:disable-line .
						'toolbar2' => '',
						'toolbar3' => '',
					],
					'quicktags'     => false, // cspell:disable-line .
					'status_bar'    => false,
				],
			];

			/**
			 * Filter message editor box data
			 *
			 * @since 3.6.0
			 *
			 * @return array    Array of message editor configuration data.
			 */
			return apply_filters( 'ir_filter_message_editor_data', $message_editor_data );
		}

		/**
		 * Add send button on editor.
		 *
		 * @since 3.6.0
		 *
		 * @param array  $buttons        First-row list of buttons.
		 * @param string $editor_id     Unique editor identifier.
		 */
		public function add_send_button_to_editor( $buttons, $editor_id ) {
			if ( 'ir_communication' === $editor_id ) {
				if ( ! in_array( 'ir_doubt_send_button', $buttons ) ) {
					array_push( $buttons, '|', 'ir_doubt_send_button' );
				}
			}
			return $buttons;
		}

		/**
		 * Add new tinymce plugin for adding send button to message editor.
		 *
		 * @since 3.6.0
		 *
		 * @param array  $external_plugins   An array of external TinyMCE plugins.
		 * @param string $editor_id         Unique editor identifier.
		 */
		public function add_message_editor_plugin( $external_plugins, $editor_id ) {
			if ( 'ir_communication' === $editor_id ) {
				if ( ! in_array( 'ir_doubt_send_button', $external_plugins ) ) {
					$external_plugins['ir_doubt_send_button'] = plugins_url( 'js/communication/tinymce/ir-doubt-send.js', __DIR__ );
					$external_plugins['placeholder']          = plugins_url( 'js/communication/tinymce/ir-message-placeholder.js', __DIR__ );
				}
			}
			return $external_plugins;
		}

		/**
		 * Set placeholder text for the message editor
		 *
		 * @since 3.6.0
		 *
		 * @param string $textarea_html     Text area HTML for the wp editor.
		 */
		public function set_message_editor_placeholder( $textarea_html ) {
			// Check if logged in user and lesson, topic and assignment page.
			if ( ! is_user_logged_in() || ! is_singular( [ 'sfwd-lessons', 'sfwd-topic' ] ) ) {
				return $textarea_html;
			}

			/**
			 * Set placeholder text for the message editor.
			 *
			 * @since 3.6.0
			 *
			 * @param string $placeholder   Placeholder text for the message editor.
			 */
			$placeholder = apply_filters( 'ir_filter_message_editor_placeholder_text', __( 'Start typing your doubts here...', 'wdm_instructor_role' ) );

			$textarea_html = preg_replace( '/<textarea/', "<textarea placeholder=\"{$placeholder}\"", $textarea_html );

			return $textarea_html;
		}

		/**
		 * Get count of doubts for a specific post for a user.
		 *
		 * @since 3.6.0
		 *
		 * @param WP_Post $post         Post object.
		 * @param int     $user_id          User ID.
		 * @param array   $user_threads   User threads. Defaults to empty array.
		 *
		 * @return int                  Count of unread doubts related to the post for the user.
		 */
		public function get_user_post_unread_doubts_count( $post, $user_id, $user_threads = [] ) {
			$unread_doubts_count = 0;

			// If empty, fetch threads.
			if ( empty( $user_threads ) ) {
				$user_threads = $this->get_user_doubt_threads( $post, $user_id );
			}

			// Check if post has threads.
			if ( ! empty( $user_threads ) && array_key_exists( 'threads', $user_threads ) && null !== $user_threads['threads'] ) {
				// Calculate number of unread doubt threads.
				foreach ( $user_threads['threads'] as $thread ) {
					if ( $thread->unread_count ) {
						$unread_doubts_count += $thread->unread_count;
					}
				}
			}

			/**
			 * Filter count of unread doubts related to a post for a user.
			 *
			 * @since 3.6.0
			 *
			 * @param int $unread_doubts_count  Number of unread doubts.
			 * @param WP_Post $post             Post object.
			 * @param int $user_id              User ID.
			 */
			return apply_filters( 'ir_filter_user_post_unread_doubts_count', $unread_doubts_count, $post, $user_id );
		}

		/**
		 * Add Student Communication settings tab in Instructor Settings
		 *
		 * @since 3.6.0
		 *
		 * @param array  $tabs          Array of tabs.
		 * @param string $current_tab   Current selected instructor tab.
		 */
		public function add_student_communication_settings_tab( $tabs, $current_tab ) {
			// Check if admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return $tabs;
			}

			// Check if profile tab already exists.
			if ( ! array_key_exists( 'ir-student-communication', $tabs ) ) {
				$tabs['ir-student-communication'] = [
					'title'  => __( 'Student Communication', 'wdm_instructor_role' ),
					'access' => [ 'admin' ],
				];
			}
			return $tabs;
		}

		/**
		 * Display Student Communication settings for configuring profile settings.
		 *
		 * @since 3.6.0
		 *
		 * @param string $current_tab   Slug of the selected tab in instructor settings.
		 */
		public function add_student_communication_settings_tab_contents( $current_tab ) {
			// Check if admin and student-communication tab.
			if ( ! current_user_can( 'manage_options' ) || 'ir-student-communication' != $current_tab ) {
				return;
			}

			// Get settings data.
			$ir_st_comm_editor_accent_color = ir_get_settings( 'stu_com_editor_accent_color' );
			$ir_st_comm_editor_set_button   = ir_get_settings( 'stu_com_editor_set_button' );
			$ir_st_comm_editor_set_popup    = ir_get_settings( 'stu_com_editor_set_popup' );

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/student-communication/settings/ir-student-communication-settings.template.php',
				[
					'ir_st_comm_editor_accent_color' => $ir_st_comm_editor_accent_color,
					'ir_st_comm_editor_set_button'   => $ir_st_comm_editor_set_button,
					'ir_st_comm_editor_set_popup'    => $ir_st_comm_editor_set_popup,
				]
			);
		}

		/**
		 * Enqueue student communication settings styles and scripts.
		 *
		 * @since 3.6.0
		 */
		public function enqueue_student_communication_settings_assets() {
			global $current_screen;

			// Instructor settings scripts.
			$page_slug = sanitize_title( __( 'LearnDash LMS', 'learndash' ) ) . '_page_instuctor';
			if ( $page_slug === $current_screen->id && ! empty( $_GET ) && array_key_exists( 'page', $_GET ) && 'instuctor' === $_GET['page'] && array_key_exists( 'tab', $_GET ) && 'ir-profile' === $_GET['tab'] ) {
				// Enqueue color picker.
				wp_enqueue_style( 'wp-color-picker' );

				if ( ! did_action( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}

				wp_enqueue_script(
					'ir-student-communication-settings-script',
					plugins_url( 'js/communication/ir-student-communication-settings-script.js', __DIR__ ),
					[ 'jquery', 'wp-color-picker' ],
					filemtime( plugin_dir_path( __DIR__ ) . '/js/communication/ir-student-communication-settings-script.js' ),
					// filemtime( plugins_dir( 'js/communication/ir-student-communication-settings-script.js', __DIR__ ) ),
					true
				);
			}
		}

		/**
		 * Get days since the timestamp.
		 *
		 * @param string $from_timestamp    Timestamp to calculate days since.
		 * @param string $to_timestamp      Timestamp to compare the previous timestamp with. Defaults to current timestamp.
		 */
		public function get_day_since( $from_timestamp, $to_timestamp = '' ) {
			$since_string = __( 'Someday', 'wdm_instructor_role' );

			if ( empty( $to_timestamp ) ) {
				$to_timestamp = time();
			}

			// If invalid, return default string.
			if ( $from_timestamp > $to_timestamp ) {
				return $since_string;
			}

			// Calculate day since for the from timestamp.
			if ( $from_timestamp >= strtotime( 'today' ) ) {
				$since_string = __( 'Today', 'wdm_instructor_role' );
			} elseif ( $from_timestamp >= strtotime( 'yesterday' ) ) {
				$since_string = __( 'Yesterday', 'wdm_instructor_role' );
			} else {
				$since_string = date( 'l jS F Y', $from_timestamp );
			}

			return $since_string;
		}

		/**
		 * Display course, lesson and/or topic details for buddypress messages.
		 *
		 * @since 3.6.0
		 *
		 * @param array $thread_subject    Subject of the current buddypress thread.
		 */
		public function display_bp_messages_ld_post_data( $thread_subject ) {
			global $wp_query;

			// Get the current message id.
			if ( ! empty( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'bp_nouveau_messages' ) && ! empty( $_POST['id'] ) ) {
				$thread_id = (int) $_POST['id'];
			}

			// For legacy template.
			if ( empty( $thread_id ) ) {
				$thread_id = get_query_var( 'page' );
			}

			if ( empty( $thread_id ) ) {
				$thread_id = bp_get_message_thread_id();
			}

			// Return if thread not found.
			if ( ! empty( $thread_id ) ) {
				$messages = \BP_Messages_Thread::get_messages( $thread_id );

				// If no messages found, return.
				if ( ! empty( $messages ) ) {
					// Get first message.
					$message = reset( $messages );

					$related_ld_post_id = (int) bp_messages_get_meta( $message->id, 'ir_related_ld_post', 1 );

					// Check if any message related to ld post type.
					if ( ! empty( $related_ld_post_id ) ) {
						$related_ld_post = get_post( $related_ld_post_id );
						$course_id       = learndash_get_course_id( $related_ld_post_id );
						$course          = get_post( $course_id );
						$post_type       = $related_ld_post->post_type;
						$lesson_label    = \LearnDash_Custom_Label::get_label( 'lesson' );

						if ( learndash_get_post_type_slug( 'topic' ) === $post_type ) {
							$lesson_label = \LearnDash_Custom_Label::get_label( 'topic' );
						}

						$thread_subject .= sprintf(
							/* translators: 1: Course label 2:Course title 3:Lesson label 4: Lesson title */
							__( ' :: %1$s : %2$s, %3$s: %4$s', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( 'course' ),
							$course->post_title,
							$lesson_label,
							$related_ld_post->post_title
						);
					}
				}
			}

			return $thread_subject;
		}

		/**
		 * Display related course, lesson and/or topic information on buddyboss theme messages in inbox.
		 *
		 * @since 3.6.0
		 *
		 * @param string $date_string
		 * @param string $calculated_time
		 * @param string $date
		 * @param string $date_format
		 */
		public function buddyboss_display_bp_messages_ld_post_data( $date_string, $calculated_time, $date, $date_format ) {
			// Check if buddyboss is active theme.
			$active_theme = wp_get_theme();
			if ( ! empty( $active_theme ) && 'buddyboss-theme' === $active_theme->template && ! empty( $_POST['action'] ) && 'messages_get_thread_messages' === $_POST['action'] ) {
				$date_string = $this->display_bp_messages_ld_post_data( $date_string );
			}
			return $date_string;
		}

		/**
		 * Display related course, lesson and/or topic information on buddypress messages in inbox.
		 *
		 * @since 3.6.0
		 *
		 * @param string $thread_subject    Subject of the buddypress thread.
		 */
		public function default_display_bp_messages_ld_post_data( $thread_subject ) {
			// Check if buddyboss is not active theme.
			$active_theme = wp_get_theme();
			if ( ! empty( $active_theme ) && 'buddyboss-theme' !== $active_theme->template ) {
				$thread_subject = $this->display_bp_messages_ld_post_data( $thread_subject );
			}
			return $thread_subject;
		}

		/**
		 * Add course, lesson and/or topic meta for the buddypress messages
		 *
		 * @since 3.6.0
		 *
		 * @param array $thread_subject    Subject of the current buddypress thread.
		 */
		public function add_bp_messages_ld_post_data( $thread_subject ) {
			if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'bp_nouveau_messages' ) || empty( $_POST['id'] ) ) {
				return $thread_subject;
			}

			// Get the current message id.
			$thread_id = (int) $_POST['id'];

			// Return if thread not found.
			if ( ! empty( $thread_id ) ) {
				$messages = \BP_Messages_Thread::get_messages( $thread_id );

				// If no messages found, return.
				if ( ! empty( $messages ) ) {
					// Get first message.
					$message = reset( $messages );

					$related_ld_post_id = (int) bp_messages_get_meta( $message->id, 'ir_related_ld_post', 1 );

					// Check if any message related to ld post type.
					if ( ! empty( $related_ld_post_id ) ) {
						$related_ld_post = get_post( $related_ld_post_id );
						$course_id       = learndash_get_course_id( $related_ld_post_id );
						$post_type       = $related_ld_post->post_type;
						$lesson_label    = \LearnDash_Custom_Label::get_label( 'lesson' );

						if ( learndash_get_post_type_slug( 'topic' ) === $post_type ) {
							$lesson_label = \LearnDash_Custom_Label::get_label( 'topic' );
						}

						$thread_subject .= ir_get_template(
							INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/student-communication/ir-bp-ld-post-data-display.template.php',
							[
								'post'         => $related_ld_post,
								'course_id'    => $course_id,
								'lesson_label' => $lesson_label,
							],
							1
						);
					}
				}
			}
			return $thread_subject;
		}

		/**
		 * Process student doubts on heartbeat.
		 *
		 * Note: Not in use currently, looking to implement in future versions based on approach.
		 *
		 * @since 3.6.0
		 *
		 * @param array $response
		 * @param array $data
		 */
		public function process_student_doubts( $response, $data ) {
			// Check if student communication data exists.
			if ( ! empty( $data['ir_student_communication_data'] ) ) {
				$ir_student_comm_data = $data['ir_student_communication_data'];
				$post_id              = intval( $ir_student_comm_data['post_id'] );

				// Verify Nonce.
				if ( ! wp_verify_nonce( $ir_student_comm_data['ir_nonce'], 'ir_heartbeat_nonce_' . $post_id ) ) {
					return $response;
				}

				$post    = get_post( $post_id );
				$user_id = get_current_user_id();

				// Get updated count.
				$unread_thread_count = $this->get_user_post_unread_doubts_count( $post, $user_id );

				$response['ir_unread_thread_count'] = $unread_thread_count;
			}
			return $response;
		}
	}
}
