<?php
/**
 * Review Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore pendig wdmtv // ignoring misspelled words that we can't fix now.
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Review' ) ) {
	/**
	 * Class Instructor Role Review Module
	 */
	class Instructor_Role_Review {
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
		 * Meta key for pending review notices
		 *
		 * @var string  $pending_reviews_meta_key
		 *
		 * @since 3.5.0
		 */
		protected $pending_reviews_meta_key = '';

		public function __construct() {
			$this->plugin_slug              = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->pending_reviews_meta_key = 'ir_pending_review_notices';
		}

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 4.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * @since 2.1
		 * To validate user and to change default product status accordingly.
		 */
		public function approve_instructor_product_updates( $post_id, $post ) {
			// Check if post ready for review.
			if ( ! $this->is_post_updated_for_review( $post_id, $post ) ) {
				return;
			}

			// If Review Product setting is enabled. Then remove "publish product" capability of instructors.
			if ( WDMIR_REVIEW_PRODUCT && 'product' === $post->post_type ) {
				$status = 'draft';

				$product = [
					'ID'          => $post->ID,
					'post_status' => apply_filters( 'wdmtv_product_approval_status', $status ),
				];

				// If calling wp_update_post, unhook this function so it doesn't loop infinitely.
				remove_action( 'save_post', [ $this, 'approve_instructor_product_updates' ], 999, 2 );
				wp_update_post( $product );
				add_action( 'save_post', [ $this, 'approve_instructor_product_updates' ], 999, 2 );

				// ------- to send an email - starts --------- //.

				$send_mail = true;
				$send_mail = apply_filters( 'wdmir_pra_send_mail', $send_mail );

				// If you don't want to send en email on every update,you can apply your own logic and send an email whenever necessary.
				if ( $send_mail ) {
					$email_settings = get_option( '_wdmir_email_settings' );

					$pra_emails = isset( $email_settings['pra_emails'] ) ? explode( ',', $email_settings['pra_emails'] ) : '';

					$pra_emails = apply_filters( 'wdmir_pra_emails', $pra_emails );

					// If any E-Mail ID is not set, then send to admin email.
					if ( empty( $pra_emails ) ) {
						$pra_emails = get_option( 'admin_email' );
					}
					if ( is_array( $pra_emails ) ) {
						$pra_emails = array_filter( $pra_emails );
					}

					$subject = $this->conditionalOperator( $email_settings['pra_subject'] );
					$body    = $this->conditionalOperator( $email_settings['pra_mail_content'] );

					// replacing shortcodes.
					$subject = wdmir_post_shortcodes( $post_id, $subject, false );
					$subject = wdmir_user_shortcodes( get_current_user_id(), $subject );

					$body = wdmir_post_shortcodes( $post_id, $body, false );
					$body = wdmir_user_shortcodes( get_current_user_id(), $body );

					add_filter( 'wp_mail_content_type', 'wdmir_html_mail' );

					$subject = apply_filters( 'wdmir_pra_subject', $subject );
					$body    = apply_filters( 'wdmir_pra_body', $body );

					wdmir_wp_mail( $pra_emails, $subject, $body );

					remove_filter( 'wp_mail_content_type', 'wdmir_html_mail' );
				}
				$this->add_pending_review_notice( $post_id );
			}
		}

		/**
		 * Function to remove slashes from email settings.
		 */
		public function conditionalOperator( $email_settings ) {
			if ( isset( $email_settings ) ) {
				return wp_unslash( $email_settings );
			}

			return __( 'Product Updated by an Instructor', 'wdm_instructor_role' );
		}

		/**
		 * @since 2.1
		 * To Hide and show "Publish" button to instructor to products.
		 */
		public function wdmir_hide_publish_product() {
			global $post;

			if ( ! isset( $post->post_type ) || 'product' != $post->post_type ) {
				return;
			}

			if ( ! wdm_is_instructor( get_current_user_id() ) ) {
				return;
			}

			// If Review Product setting is enabled.
			if ( WDMIR_REVIEW_PRODUCT ) {
				if ( 'publish' == $post->post_status ) {
					echo '<script>';
					echo 'jQuery("#publishing-action #publish").attr("value","' . __( 'Save Draft', 'wdm_instructor_role' ) . '");';
					echo '</script>';
				} else {
					// To hide "Publish" button of publish meta box.
					echo '<style>';
					echo '#publishing-action {
                        display: none;
                    }';
					echo '</style>';
				}

				// To remove "Publish" option from dropdown of quick edit.
			}
		}

		/**
		 * @since 2.1
		 * To Send an email notification after a product has been published of an instructor.
		 */
		function wdmir_product_published_notification( $PID, $post ) {
			// check if we should send notification or not.
			if ( $this->wdmir_product_published_notification_cond( $post ) ) {
				return;
			}

			$pending_review_notices = maybe_unserialize( get_option( $this->pending_reviews_meta_key ) );
			if ( ! empty( $pending_review_notices ) && ! in_array( $post->ID, $pending_review_notices ) ) {
				return;
			}

			// Remove product from under review.
			$this->remove_pending_review_notice( $post->ID );

			// ------- to send an email - starts --------- //.

			$send_mail = true;
			$send_mail = apply_filters( 'wdmir_pri_send_mail', $send_mail );

			// If you don't want to send en email on every update,you can apply your own logic and send an email whenever necessary.
			if ( $send_mail ) {
				$post_id = $PID;

				$email_settings = get_option( '_wdmir_email_settings' );

				$pri_emails = get_the_author_meta( 'user_email', $post->post_author );

				$pri_emails = apply_filters( 'wdmir_pri_emails', $pri_emails );

				if ( ! empty( $pri_emails ) ) {
					if ( is_array( $pri_emails ) ) {
						$pri_emails = array_filter( $pri_emails );
					}

					$subject = isset( $email_settings['pri_subject'] ) ?
					wp_unslash( $email_settings['pri_subject'] ) : 'Product is Published by an Admin';
					$body    = isset( $email_settings['pri_mail_content'] ) ?
					wp_unslash( $email_settings['pri_mail_content'] ) : 'Product is Published by an Admin';

					// replacing shortcodes.
					$subject = wdmir_post_shortcodes( $post_id, $subject, false );
					$subject = wdmir_user_shortcodes( $post->post_author, $subject );

					$body = wdmir_post_shortcodes( $post_id, $body, false );
					$body = wdmir_user_shortcodes( $post->post_author, $body );

					add_filter( 'wp_mail_content_type', 'wdmir_html_mail' );

					$subject = apply_filters( 'wdmir_pri_subject', $subject );
					$body    = apply_filters( 'wdmir_pri_body', $body );

					wdmir_wp_mail( $pri_emails, $subject, $body );

					remove_filter( 'wp_mail_content_type', 'wdmir_html_mail' );
				}
			}

			// ------- to send an email - ends --------- //.
		}

		/**
		 * @since 2.3.1
		 * To check if we should send notification or not after a product has been published of an instructor.
		 */
		public function wdmir_product_published_notification_cond( $post ) {
			if ( empty( $post ) ) {
				return true;
			}
			// If current post is NOT product OR product author is not an instructor OR current user is an Instructor.
			if ( ( 'product' != $post->post_type ) || ( ! wdm_is_instructor( $post->post_author ) ) || ( wdm_is_instructor( get_current_user_id() ) ) ) {
				return true;
			}

			return false;
		}

		/**
		 * @since 2.1
		 * To show custom content if course content is in pending status.
		 * Filters:
		 * wdmir_show_course_page: true if you want to show main course page
		 * wdmir_show_course_content: true if you want to show all course content
		 */
		public function wdmir_approval_course_content( $content, $post ) {
			if ( WDMIR_REVIEW_COURSE && ! current_user_can( 'manage_options' ) && ! wdm_is_instructor() ) {
				// ---------------  If you want to show main course page. --------------- //.
				$show_course_page = false;
				$show_course_page = apply_filters( 'wdmir_show_course_page', $show_course_page );

				if ( 'sfwd-courses' == $show_course_page && $post->post_type ) {
					return $content;
				}

				// ---------------  If you want to show main course page. --------------- //.

				// ---------------  If you want to show all course content --------------- //.

				$show_course_content = false;
				$show_course_content = apply_filters( 'wdmir_show_course_content', $show_course_content );

				if ( $show_course_content ) {
					return $content;
				}

				// ---------------  If you want to show all course content --------------- //.

				$parent_course_id = wdmir_get_ld_parent( $post->ID );

				if ( empty( $parent_course_id ) ) {
					return $content;
				}

				if ( wdmir_is_parent_course_pending( $parent_course_id ) ) {
					$settings = get_option( '_wdmir_admin_settings', true );

					if ( isset( $settings['review_course_content'] ) && ! empty( $settings['review_course_content'] ) ) {
						$content = $settings['review_course_content'];
					} else {
						$content = sprintf(
							/* translators: Course Label */
							__( 'This %s is under review!!', 'wdm_instructor_role' ),
							\LearnDash_Custom_Label::get_label( 'course' )
						);
					}
				}
			}

			return $content;
		}

		/**
		 * @since 2.1
		 * To save that course content is edited by an instructor.
		 */
		public function approve_instructor_course_updates( $post_id, $post ) {
			if ( ! $this->is_post_updated_for_review( $post_id, $post ) ) {
				return;
			}

			// To avoid auto-draft posts.

			$ld_post_types = [
				'sfwd-certificates',
				'sfwd-courses',
				'sfwd-lessons',
				'sfwd-quiz',
				'sfwd-topic',
			];

			$ld_post_types = apply_filters( 'wdmtv_validate_ld_post_types', $ld_post_types );

			if ( ! in_array( $post->post_type, $ld_post_types ) ) {
				return;
			}

			// If Review Course setting is enabled.
			if ( WDMIR_REVIEW_COURSE ) {
				$parent_course_id = wdmir_get_ld_parent( $post_id );

				$this->add_course_for_review( $post_id, $parent_course_id );
			}
		}

		/**
		 * Function to remove slashes from email subject.
		 *
		 * @param string $content
		 * @param string $default_cont
		 */
		public function wdmir_unslash( $content, $default_cont = '' ) {
			if ( ! empty( $content ) ) {
				return wp_unslash( $content );
			}

			return $default_cont;
		}

		/**
		 * Function to returns string email settings into array format.
		 *
		 * @param array $email_settings
		 */
		public function wdmirCraEmails( $email_settings ) {
			if ( isset( $email_settings ) ) {
				return explode( ',', $email_settings );
			}

			return '';
		}

		/**
		 * @since 2.1
		 * To show approval pending meta box to the admin.
		 * Meta box: wdmir_approval_meta_box
		 * Callback function: wdmir_approval_meta_box_callback
		 */
		public function wdmir_approval_meta_box() {
			if ( WDMIR_REVIEW_COURSE && current_user_can( 'manage_options' ) ) {
				add_meta_box(
					'wdmir_approval_meta_box',
					__( 'Instructor Pending approvals', 'wdm_instructor_role' ),
					[ $this, 'wdmir_approval_meta_box_callback' ],
					'sfwd-courses',
					'side',
					'core'
				);
			}
		}

		/**
		 * Callback function of a meta box 'wdmir_approval_meta_box'.
		 * To show pending approval contents in the meta box.
		 *
		 * @since: 2.1
		 */
		public function wdmir_approval_meta_box_callback( $post ) {
			$current_post_id = $post->ID;

			$approval_data = wdmir_get_approval_meta( $current_post_id );

			if ( empty( $approval_data ) ) {
				echo __( 'No pending approvals', 'wdm_instructor_role' );
			} else {
				$pending_approvals = [];

				foreach ( $approval_data as $content_id => $content_meta ) {
					// If approval is pending.
					if ( 'pending' == $content_meta['status'] ) {
						// Check once again that parent did not change.
						if ( wdmir_get_ld_parent( $content_id ) == $current_post_id ) {
							$pending_approvals[ $content_id ] = $content_meta;
						}
					}
				}

				if ( empty( $pending_approvals ) ) {
					echo __( 'No pending approvals', 'wdm_instructor_role' );
				} else {
					echo "<ul class='wdmir-pendig-box'>";

					foreach ( $pending_approvals as $pending_id => $pending_meta ) {
						// Example: Sep 19, 2015 @ 10:48.
						$updated_date = date( 'M d, Y @ H:i', strtotime( $pending_meta['update_time'] ) );

						echo "<li>
                                <a href='" . get_edit_post_link( $pending_id ) . "'>" . get_the_title( $pending_id ) . '</a>
                                ( ' . $updated_date . ' )
                                </li>';
					}

					echo '</ul>';
				}
			}
		}

		/**
		 * @since 2.1
		 * To show checkbox in the publish meta box of a content, if content is having admin approval.
		 */
		public function wdmir_approve_field_publish() {
			if ( WDMIR_REVIEW_COURSE && current_user_can( 'manage_options' ) ) {
				global $post;

				$post_id = $post->ID;

				$pending_data = wdmir_am_i_pending_post( $post_id );

				if ( ! empty( $pending_data ) ) {
					echo '<div class="misc-pub-section misc-pub-section-last">
                        <span id="wdmir_pending">'
						. '<label><input type="checkbox" value="" name="wdmir_approve_field_publish" /> '
						. __( 'Approve Instructor Update', 'wdm_instructor_role' ) . ' </label>'
						. '</span></div>';
				}
			}
		}

		/**
		 * @since 3.0.9
		 * To approve LD content. If clicks the button then approve content and remove from approval data of a course.
		 */
		public function wdmir_approve_field_publish_test() {
			global $post;

			if ( empty( $post ) ) {
				return;
			}

			$post_id = $post->ID;
			if ( WDMIR_REVIEW_COURSE && current_user_can( 'manage_options' ) ) {
				$pending_data = wdmir_am_i_pending_post( $post_id );

				if ( ! empty( $pending_data ) ) {
					wp_enqueue_script(
						'test_js',
						plugins_url( 'js/approve_inst_update.js', __DIR__ ),
						[ 'wp-edit-post', 'wp-plugins', 'wp-i18n', 'wp-element', 'wp-compose' ],
						'0.1',
						true
					);
					$button_text = __( 'Approve Instructor Update', 'wdm_instructor_role' );

					$confirmation_message = ( 'sfwd-courses' === $post->post_type ) ?
					sprintf(
						/* translators: Course Label */
						__( 'Do you really want to approve instructor update for the whole %1$s. This would approve all the contents for this %1$s ?', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'course' )
					) : __( 'Do you really want to approve instructor update?', 'wdm_instructor_role' );

					$successfull_message = sprintf( // cspell:disable-line .
						/* translators: Course Label */
						__( 'The %s has been successfully updated', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'course' )
					);
					$request_approval_message = __( 'Please approve instructor updates to publish the post', 'wdm_instructor_role' );
					wp_localize_script(
						'test_js',
						'test_object',
						[
							'ajax_url'                 => admin_url( 'admin-ajax.php' ),
							'post_id'                  => $post_id,
							'button_text'              => $button_text,
							'confirmation_message'     => $confirmation_message,
							'successfull_message'      => $successfull_message, // cspell:disable-line .
							'request_approval_message' => $request_approval_message,
						]
					);
				}
			}
		}

		public function approveInstructorUpdateAjaxHandler() {
			if ( current_user_can( 'manage_options' ) && isset( $_POST['post_id'] ) && ! empty( $_POST['post_id'] ) ) {
				$post_id = $_POST['post_id'];
				if ( 'auto-draft' == get_post_type( $post_id ) ) {
					echo __( 'The current post status is auto-draft. Please change it to publish to approve instructor update', 'wdm_instructor_role' );
				} else {
					$parent_course_id = wdmir_get_ld_parent( $post_id );
					$approval_data    = wdmir_get_approval_meta( $parent_course_id );

					if ( isset( $approval_data[ $post_id ] ) || $parent_course_id === $post_id ) {
						if ( isset( $approval_data[ $post_id ] ) ) {
							unset( $approval_data[ $post_id ] );
						}

						// If course being approved, approve all contents as well.
						if ( $parent_course_id === $post_id ) {
							$approval_data = [];
						}

						wdmir_set_approval_meta( $parent_course_id, $approval_data );
						wdmir_update_approval_data( $parent_course_id );

						// empty means all pending contents are approved, so send an ack email to instructor.
						if ( empty( $approval_data ) ) {
							// ------- to send an email - starts --------- //.

							$send_mail = true;
							$send_mail = apply_filters( 'wdmir_cri_send_mail', $send_mail );

							// If you don't want to send en email on every update,you can apply your own logic and send an email whenever necessary.
							if ( $send_mail ) {
								$email_settings = get_option( '_wdmir_email_settings' );

								$post_author_id = get_post_field( 'post_author', $parent_course_id );

								$cri_emails = get_the_author_meta( 'user_email', $post_author_id );

								if ( ! empty( $cri_emails ) ) {
									$subject = isset( $email_settings['cri_subject'] ) ?
											wp_unslash( $email_settings['cri_subject'] ) : 'Course has been approved by an admin';
									$body    = isset( $email_settings['cri_mail_content'] ) ?
											wp_unslash( $email_settings['cri_mail_content'] ) : 'Course has been approved by an admin';

									// replacing shortcodes.
									$subject = wdmir_post_shortcodes( $post_id, $subject, true );
									$subject = wdmir_post_shortcodes( $parent_course_id, $subject, false );
									$subject = wdmir_user_shortcodes( get_current_user_id(), $subject );

									$body = wdmir_post_shortcodes( $post_id, $body, true );
									$body = wdmir_post_shortcodes( $parent_course_id, $body, false );
									$body = wdmir_user_shortcodes( get_current_user_id(), $body );

									add_filter( 'wp_mail_content_type', 'wdmir_html_mail' );

									$subject    = apply_filters( 'wdmir_cri_subject', $subject );
									$body       = apply_filters( 'wdmir_cri_body', $body );
									$cri_emails = apply_filters( 'wdmir_cri_emails', $cri_emails );

									wdmir_wp_mail( $cri_emails, $subject, $body );

									remove_filter( 'wp_mail_content_type', 'wdmir_html_mail' );
								}
							}
							echo 'approved';
							// ------- to send an email - ends --------- //.
						}
					}
				}
			}
			die();
		}

		/**
		 * @since 2.1
		 * To approve LD content. If checkbox is checked then approve content and remove from approval data of a course.
		 */
		public function wdmir_ld_approve_content( $post_id, $post ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// To avoid auto-draft posts.
			if ( 'auto-draft' == $post->post_status ) {
				return;
			}

			if ( WDMIR_REVIEW_COURSE && current_user_can( 'manage_options' ) && isset( $_POST['wdmir_approve_field_publish'] ) ) {
				$parent_course_id = wdmir_get_ld_parent( $post_id );

				$approval_data = wdmir_get_approval_meta( $parent_course_id );

				if ( isset( $approval_data[ $post_id ] ) ) {
					unset( $approval_data[ $post_id ] );

					wdmir_set_approval_meta( $parent_course_id, $approval_data );
					wdmir_update_approval_data( $parent_course_id );

					// empty means all pending contents are approved, so send an ack email to instructor.
					if ( empty( $approval_data ) ) {
						// ------- to send an email - starts --------- //.

						$send_mail = true;
						$send_mail = apply_filters( 'wdmir_cri_send_mail', $send_mail );

						// If you don't want to send en email on every update,you can apply your own logic and send an email whenever necessary.
						if ( $send_mail ) {
							$email_settings = get_option( '_wdmir_email_settings' );

							$post_author_id = get_post_field( 'post_author', $parent_course_id );

							$cri_emails = get_the_author_meta( 'user_email', $post_author_id );

							if ( ! empty( $cri_emails ) ) {
								$subject = isset( $email_settings['cri_subject'] ) ?
									wp_unslash( $email_settings['cri_subject'] ) : 'Course has been approved by an admin';
								$body    = isset( $email_settings['cri_mail_content'] ) ?
									wp_unslash( $email_settings['cri_mail_content'] ) : 'Course has been approved by an admin';

								// replacing shortcodes.
								$subject = wdmir_post_shortcodes( $post_id, $subject, true );
								$subject = wdmir_post_shortcodes( $parent_course_id, $subject, false );
								$subject = wdmir_user_shortcodes( get_current_user_id(), $subject );

								$body = wdmir_post_shortcodes( $post_id, $body, true );
								$body = wdmir_post_shortcodes( $parent_course_id, $body, false );
								$body = wdmir_user_shortcodes( get_current_user_id(), $body );

								add_filter( 'wp_mail_content_type', 'wdmir_html_mail' );

								$subject    = apply_filters( 'wdmir_cri_subject', $subject );
								$body       = apply_filters( 'wdmir_cri_body', $body );
								$cri_emails = apply_filters( 'wdmir_cri_emails', $cri_emails );

								wdmir_wp_mail( $cri_emails, $subject, $body );

								remove_filter( 'wp_mail_content_type', 'wdmir_html_mail' );
							}
						}

						// ------- to send an email - ends --------- //.
					}
				}
			}
		}

		/**
		 * @since 2.1
		 * To update course approval data on course update.
		 */
		public function wdmir_on_course_approval_update( $post_id, $post ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// To avoid auto-draft posts.
			if ( 'auto-draft' == $post->post_status ) {
				return;
			}

			if ( isset( $post->post_type ) && 'sfwd-courses' == $post->post_type ) {
				wdmir_update_approval_data( $post_id );
			}

			return;
		}

		/**
		 * @since 2.1
		 * Description: To add a "pending" column in the course listing page in a dashboard.
		 */
		public function wdmir_pending_column_head( $defaults ) {
			$show_column = true;
			$show_column = apply_filters( 'wdmir_show_pending_column', $show_column );

			if ( WDMIR_REVIEW_COURSE && $show_column ) {
				$defaults['wdmir_pending'] = __( 'Pending' );
			}

			return $defaults;
		}

		/**
		 * @since 2.1
		 * Description: To show status in a "pending" column in the course listing page in a dashboard.
		 */
		public function wdmir_pending_column_content( $column_name, $post_ID ) {
			$show_column = true;
			$show_column = apply_filters( 'wdmir_show_pending_column', $show_column );

			if ( WDMIR_REVIEW_COURSE && $show_column ) {
				if ( 'wdmir_pending' == $column_name ) {
					if ( wdmir_is_parent_course_pending( $post_ID ) ) {
						echo __( 'Yes', 'wdm_instructor_role' );
					} else {
						echo '-';
					}
				}
			}
		}

		/**
		 * Check for pending review notices
		 *
		 * @since 3.5.0
		 */
		public function display_review_notifications() {
			global $post;

			if ( empty( $post ) ) {
				return;
			}

			// Check if instructor.
			if ( ! wdm_is_instructor() || ! $this->is_post_review_notice_pending( $post->ID ) ) {
				return;
			}

			$notice_html = '';

			// Check if review settings enabled for product.
			if ( WDMIR_REVIEW_PRODUCT && 'product' === $post->post_type ) {
				$notice_html = IR_REVIEW_UPDATE_NOTICE;

				/**
				 * Filter review notice message type
				 *
				 * @since 3.5.0
				 *
				 * @param string $notice_type   Type of notice message defaults to 'success'
				 * @param object $post          WP_Post object
				 */
				$notice_type = apply_filters( 'ir_filter_review_notice_type', 'success', $post );

				/**
				 * Filter review notice message
				 *
				 * @since 3.5.0
				 *
				 * @param string $notice_message    Notice message to be displayed on review
				 * @param object $post              WP_Post object
				 */
				$notice_message = apply_filters(
					'ir_filter_review_notice_message',
					__( 'This product will be reviewed and published by the admin upon approval', 'wdm_instructor_role' ),
					$post
				);

				// Perform string replaces.
				$notice_html = str_replace(
					[ '{type}', '{message}' ],
					[ $notice_type, $notice_message ],
					$notice_html
				);
			}

			$ld_post_types = [
				'sfwd-courses',
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-quiz',
				'sfwd-certificates',
				'sfwd-assignment',
				'sfwd-essays',
				'ld-exam',
			];

			// Check if review settings enabled for course.
			if ( WDMIR_REVIEW_COURSE && in_array( $post->post_type, $ld_post_types ) ) {
				$notice_html = IR_REVIEW_UPDATE_NOTICE;

				/**
				 * Filter review notice message type
				 *
				 * @since 3.5.0
				 *
				 * @param string $notice_type   Type of notice message defaults to 'success'
				 * @param object $post          WP_Post object
				 */
				$notice_type = apply_filters( 'ir_filter_review_notice_type', 'success', $post );

				/**
				 * Filter review notice message
				 *
				 * @since 3.5.0
				 *
				 * @param string $notice_message    Notice message to be displayed on review
				 * @param object $post              WP_Post object
				 */
				$notice_message = apply_filters(
					'ir_filter_review_notice_message',
					sprintf(
						// translators:: Course.
						__( 'This %s will be reviewed and published by the admin upon approval', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'course' )
					),
					$post
				);

				// Perform string replaces.
				$notice_html = str_replace(
					[ '{type}', '{message}' ],
					[ $notice_type, $notice_message ],
					$notice_html
				);
			}

			if ( ! empty( $notice_html ) ) {
				// @codingStandardsIgnoreLine WordPress.Security.EscapeOutput.OutputNotEscaped
				printf( $notice_html );
			}
		}

		/**
		 * Check whether the post is updated for review or not. Only supports course and product post types
		 *
		 * @param int    $post_id      ID of the post.
		 * @param object $post      WP_Post object.
		 *
		 * @return bool             True if course to be reviewed, else false.
		 *
		 * @since 3.3.4
		 */
		public function is_post_updated_for_review( $post_id, $post ) {
			$is_updated = false;
			$user_id    = get_current_user_id();

			// Check.
			// 1. If current user is instructor and
			// 2. Post published
			// 3. Check and verify nonce.
			if ( wdm_is_instructor() && 'publish' === $post->post_status && array_key_exists( '_wpnonce', $_POST ) && wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post_id ) ) {
				$supported_post_types = [
					'sfwd-courses',
					'sfwd-lessons',
					'sfwd-topic',
					'sfwd-quiz',
					'sfwd-certificates',
					'product',
				];

				/**
				 * Filter list supported post types for review. Currently only supports courses and products.
				 *
				 * @since 3.5.0
				 *
				 * @param mixed     $supported_post_types   List of supported review post types
				 * @param int       $post_id                ID of the post to be reviewed
				 * @param object    $post                   WP_Post object of the post to be reviewed
				 * @param int       $user_id                ID of the current user
				 */
				$supported_post_types = apply_filters( 'ir_filter_review_post_types', $supported_post_types, $post_id, $post, $user_id );

				if ( in_array( $post->post_type, $supported_post_types ) ) {
					$is_updated = true;
				}

				// If draft or autosave then not ready for review.
				if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
					$is_updated = false;
				}
			}

			/**
			 * Filter is post updated for review
			 *
			 * @since 3.5.0
			 *
			 * @param bool      $is_updated     True if post ready for review, else false.
			 * @param int       $post_id        ID of the post to be reviewed
			 * @param object    $post           WP_Post object of the post to be reviewed
			 */
			return apply_filters( 'ir_filter_is_post_updated_for_review', $is_updated, $post_id, $post );
		}

		/**
		 * Enqueue block editor with review messages
		 *
		 * @since 3.5.0
		 */
		public function enqueue_block_editor_review_messages() {
			global $current_screen, $post;

			$review_screens = [
				'sfwd-courses',
				'sfwd-lessons',
				'sfwd-topic',
				'sfwd-quiz',
				'product',
			];

			// Check if instructor course edit screen and check if reviews are enabled.
			if ( WDMIR_REVIEW_COURSE && wdm_is_instructor() && ! empty( $current_screen ) && in_array( $current_screen->id, $review_screens ) ) {
				wp_enqueue_script(
					'ir-review-script',
					plugins_url( 'js/ir-review-script.js', __DIR__ ),
					[ 'wp-data', 'wp-core-data', 'wp-hooks' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . 'modules/js/ir-review-script.js' ),
					true
				);

				/**
				 * Filter the course review message displayed to instructors
				 *
				 * @since 3.5.0
				 *
				 * @param string $course_review_message     The review message displayed to the instructor when course is sent for review.
				 */
				$course_review_message = apply_filters(
					'ir_filter_review_message',
					sprintf(
						// translators:: Course.
						__( '%s is sent for review. It will be live once the admin reviews and publishes it', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'course' )
					)
				);

				/**
				 * Filter the review message notice type.
				 *
				 * @since 3.5.0
				 *
				 * @param string $review_message_type   Can be either one of 'success', 'warning', 'error' or 'info'
				 */
				$review_message_type = apply_filters( 'ir_filter_review_message_type', 'success' );

				wp_localize_script(
					'ir-review-script',
					'ir_review_data',
					[
						'review_notice'      => $course_review_message,
						'review_notice_type' => $review_message_type,
					]
				);
			}
		}

		/**
		 * Add pending review notice
		 *
		 * @param int $post_id  ID of the post.
		 */
		public function add_pending_review_notice( $post_id ) {
			if ( ! empty( $post_id ) ) {
				$pending_review_notices = maybe_unserialize( get_option( $this->pending_reviews_meta_key ) );
				if ( empty( $pending_review_notices ) ) {
					$pending_review_notices = [];
				}

				if ( ! in_array( $post_id, $pending_review_notices ) ) {
					$pending_review_notices[] = $post_id;
					update_option( $this->pending_reviews_meta_key, $pending_review_notices );
				}
			}
		}

		/**
		 * Remove pending review notice
		 *
		 * @param int $post_id  ID of the post.
		 */
		public function remove_pending_review_notice( $post_id ) {
			if ( ! empty( $post_id ) ) {
				$pending_review_notices = maybe_unserialize( get_option( $this->pending_reviews_meta_key ) );
				if ( empty( $pending_review_notices ) ) {
					return;
				}

				$search_key = array_search( $post_id, $pending_review_notices );
				if ( false !== $search_key ) {
					unset( $pending_review_notices[ $search_key ] );
					update_option( $this->pending_reviews_meta_key, $pending_review_notices );
				}
			}
		}

		/**
		 * Check whether post is pending review
		 *
		 * @param int $post_id  ID of the Post.
		 *
		 * @return bool         True if post pending review, false otherwise.
		 */
		public function is_post_review_notice_pending( $post_id ) {
			$is_review_pending = false;

			// Check if post ID empty.
			if ( empty( $post_id ) ) {
				return $is_review_pending;
			}

			// Fetch all pending review notices.
			$pending_review_notices = maybe_unserialize( get_option( $this->pending_reviews_meta_key ) );

			// Check if current post is pending review.
			if ( ! empty( $pending_review_notices ) && in_array( $post_id, $pending_review_notices ) ) {
				$is_review_pending = true;
			}
			return $is_review_pending;
		}

		/**
		 * Add course to be reviewed and send emails.
		 *
		 * @param int $post_id      ID of the Post initiating the review.
		 * @param int $course_id    ID of the course to be reviewed.
		 *
		 * @since 4.5.3
		 */
		public function add_course_for_review( $post_id, $course_id ) {
			if ( ! empty( $course_id ) ) {
				$approval_data = wdmir_get_approval_meta( $course_id );

				if ( empty( $approval_data ) ) {
					$approval_data = [];
				}

				$approval_data[ $post_id ]['status']      = 'pending';
				$approval_data[ $post_id ]['update_time'] = current_time( 'mysql' );

				// If course sent for review then draft course itself.
				wp_update_post(
					[
						'ID'          => $course_id,
						'post_status' => 'draft',
					]
				);

				$approval_data = apply_filters( 'wdmir_approval_post_meta', $approval_data );

				wdmir_set_approval_meta( $course_id, $approval_data );
				wdmir_update_approval_data( $course_id );

				// ------- to send an email - starts --------- //.

				$send_mail = true;
				$send_mail = apply_filters( 'wdmir_cra_send_mail', $send_mail, $post_id, $course_id );

				// If you don't want to send en email on every update,you can apply your own logic and send an email whenever necessary.

				if ( $send_mail ) {
					$email_settings = get_option( '_wdmir_email_settings' );

					$cra_emails = $this->wdmirCraEmails( $email_settings['cra_emails'] );

					$cra_emails = apply_filters( 'wdmir_cra_emails', $cra_emails );

					// If any E-Mail ID is not set, then send to admin email.
					if ( empty( $cra_emails ) ) {
						$cra_emails = get_option( 'admin_email' );
					} else {
						if ( is_array( $cra_emails ) ) {
							$cra_emails = array_filter( $cra_emails );
						}

						$subject = $this->wdmir_unslash(
							$email_settings['cra_subject'],
							sprintf(
								/* translators: Course label */
								__( '%s has been Updated by an Instructor', 'wdm_instructor_role' ),
								\LearnDash_Custom_Label::get_label( 'course' )
							)
						);
						$body = $this->wdmir_unslash(
							$email_settings['cra_mail_content'],
							sprintf(
								/* translators: Course label */
								__( '%s has been Updated by an Instructor', 'wdm_instructor_role' ),
								\LearnDash_Custom_Label::get_label( 'course' )
							)
						);
						// replacing shortcodes.
						$subject = wdmir_post_shortcodes( $post_id, $subject, true );
						$subject = wdmir_post_shortcodes( $course_id, $subject, false );
						$subject = wdmir_user_shortcodes( get_current_user_id(), $subject );

						$body = wdmir_post_shortcodes( $post_id, $body, true );
						$body = wdmir_post_shortcodes( $course_id, $body, false );
						$body = wdmir_user_shortcodes( get_current_user_id(), $body );

						add_filter( 'wp_mail_content_type', 'wdmir_html_mail' );

						$subject = apply_filters( 'wdmir_cra_subject', $subject );
						$body    = apply_filters( 'wdmir_cra_body', $body );

						wdmir_wp_mail( $cra_emails, $subject, $body );

						remove_filter( 'wp_mail_content_type', 'wdmir_html_mail' );
					}
				}
				$this->add_pending_review_notice( $post_id );
			}
		}

		/**
		 * Manage product reviews from frontend dashboard.
		 *
		 * @param bool   $is_updated  Whether to send product for review or not.
		 * @param int    $post_id     ID of the Post.
		 * @param object $post        WP Post Object.
		 *
		 * @return bool             True if product to be reviewed.
		 */
		public function manage_dashboard_product_reviews( $is_updated, $post_id, $post ) {
			// Check if product post and user is instructor.
			if ( 'product' === $post->post_type && wdm_is_instructor() ) {
				// Check if instructor is author of the product.
				if ( get_current_user_id() === intval( $post->post_author ) ) {
					$is_updated = true;
				}
			}
			return $is_updated;
		}
	}
}
