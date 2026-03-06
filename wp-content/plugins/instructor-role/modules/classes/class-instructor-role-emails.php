<?php
/**
 * Instructor Emails Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Emails' ) ) {
	/**
	 * Class Instructor Role Emails Module
	 */
	class Instructor_Role_Emails {
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
		 * Add Instructor emails tab
		 *
		 * @since 3.2.1
		 */
		public function ir_add_instructor_email_tab( $tabs ) {
			if ( function_exists( 'wdm_is_instructor' ) && wdm_is_instructor() ) {
				// Check if instructor emails setting enabled.
				$ir_settings = get_option( '_wdmir_admin_settings', [] );
				if ( ! array_key_exists( 'wdm_enable_instructor_course_mail', $ir_settings ) || 1 != $ir_settings['wdm_enable_instructor_course_mail'] ) {
					return $tabs;
				}
				$tabs['instructor-email'] = [
					'title'  => sprintf(
						/* translators: Course Label */
						_x( '%s Purchase Emails', 'used for instructor settings', 'wdm_instructor_role' ),
						\LearnDash_Custom_Label::get_label( 'course' )
					),
					'access' => [ 'instructor' ],
				];
			}
			return $tabs;
		}

		/**
		 * Add instructor email tab content
		 *
		 * @since 3.2.1
		 */
		public function ir_add_instructor_email_tab_content( $current_tab ) {
			if ( ! function_exists( 'wdm_is_instructor' ) && ! wdm_is_instructor() ) {
				return;
			}
			$user_id = get_current_user_id();
			if ( 'instructor-email' === $current_tab ) {
				if ( array_key_exists( 'instructor-email-save', $_POST ) && ! empty( $_POST['instructor-email-save'] ) ) {
					$this->ir_save_instructor_email_settings( $user_id );
				}
				$subject = get_user_meta( $user_id, 'ir-course-purchase-email-sub', 1 );
				$body    = get_user_meta( $user_id, 'ir-course-purchase-email-body', 1 );
				?>
				<div class="wl8qcn-email-form">
					<h2>
						<?php
						printf(
						/* translators: Course label */
							esc_html_x( '%s Purchase Email', 'placeholder: course', 'wdm_instructor_role' ),
							esc_html( \LearnDash_Custom_Label::get_label( 'course' ) )
						);
						?>
					</h2>
					<p class="irb-cpe-desc-wrap">
					<span>
						<i class="irb-icon-hand"></i>
					</span>
						<span class="irb-cpe-desc">
						<?php
						printf(
						/* translators: Course Label. */
							esc_html_x(
								'Now receive a email notification whenever your %s is purchased through a product. You can customize the contents of that email from here.',
								'placeholder: course',
								'wdm_instructor_role'
							),
							\LearnDash_Custom_Label::label_to_lower( 'course' )
						);
						?>
					</span>
					</p>
					<form method="post">
						<div class="irb-as-wrap">
							<span class="irb-as-label"><?php esc_html_e( 'Available shortcodes : ', 'wdm_instructor_role' ); ?></span>
							<div class="irb-as-values">
								<?php esc_html_e( '[site_name], [course_name], [instructor_name] and [customer_name]', 'wdm_instructor_role' ); ?>
							</div>
						</div>
						<table class="ir-course-purchase-email-body">
							<tbody>
							<tr scope="row">
								<th class="ir-email-settings-label">
									<?php esc_html_e( 'Email Subject', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<input type="text" name="ir-course-purchase-email-sub" value="<?php echo $subject; ?>">
								</td>
							</tr>
							<tr scope="row">
								<th class="ir-email-settings-label">
									<?php esc_html_e( 'Email Message', 'wdm_instructor_role' ); ?>
								</th>
								<td>
									<?php
									wp_editor(
										$body,
										'ir-course-purchase-email-body',
										[ 'media_buttons' => false ]
									);
									?>
								</td>
							</tr>
							</tbody>
						</table>
						<input type="submit" class="button-primary irb-btn" name="instructor-email-save" value="<?php esc_html_e( 'Save', 'wdm_instructor_role' ); ?>" />
					</form>
				</div>
				<?php
			}
		}

		/**
		 * Save instructor email settings
		 *
		 * @since 3.2.1
		 */
		public function ir_save_instructor_email_settings( $user_id ) {
			if ( empty( $user_id ) ) {
				$user_id = get_current_user_id();
			}

			$subject = sanitize_text_field( wp_unslash( $_POST['ir-course-purchase-email-sub'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Check it later.
			$body    = wpautop( $_POST['ir-course-purchase-email-body'] );

			update_user_meta( $user_id, 'ir-course-purchase-email-sub', $subject );
			update_user_meta( $user_id, 'ir-course-purchase-email-body', $body );
		}

		/**
		 * Send course purchase emails to instructors
		 *
		 * @since 3.2.1
		 */
		public function ir_send_course_purchase_email_to_instructor( $order_id ) {
			$enable_course_email = ir_get_settings( 'wdm_enable_instructor_course_mail' );
			if ( $enable_course_email ) {
				$order = new \WC_Order( $order_id );
				$items = $order->get_items();

				foreach ( $items as $item ) {
					$product_id     = $item['product_id'];
					$product_post   = get_post( $product_id );
					$author_id      = $product_post->post_author;
					$related_course = get_post_meta( $product_id, '_related_course', true );
					// If no course related then do not send emails.
					if ( empty( $related_course ) ) {
						continue;
					}
					$course_id       = $related_course[0];
					$assigned_course = get_post( $course_id );

					// Check if instructor is author of course if not product.
					if ( ! wdm_is_instructor( $author_id ) ) {
						if ( ! empty( $related_course ) ) {
							$author_id = $assigned_course->post_author;
						}
					}

					// Check if instructor specific email setting disabled.
					$is_instructor_emails_disabled = get_user_meta( $author_id, 'ir_course_emails_disabled', 1 );
					if ( ! empty( $is_instructor_emails_disabled ) ) {
						continue;
					}

					if ( wdm_is_instructor( $author_id ) ) {
						$subject               = get_user_meta( $author_id, 'ir-course-purchase-email-sub', 1 );
						$body                  = get_user_meta( $author_id, 'ir-course-purchase-email-body', 1 );
						$global_email_settings = get_option( '_wdmir_email_settings' );

						if ( empty( $subject ) ) {
							if ( is_array( $global_email_settings ) && array_key_exists( 'cp_subject', $global_email_settings ) && ! empty( $global_email_settings['cp_subject'] ) ) {
								$subject = $global_email_settings['cp_subject'];
							} else {
								// translators: Site name.
								$subject = sprintf( __( '[ %s ] : Course Purchase Email', 'wdm_is_instructor' ), get_bloginfo( 'name' ) );
							}
						}

						if ( empty( $body ) ) {
							if ( is_array( $global_email_settings ) && array_key_exists( 'cp_mail_content', $global_email_settings ) && ! empty( $global_email_settings['cp_mail_content'] ) ) {
								$body = $global_email_settings['cp_mail_content'];
							} else {
								$body  = '<p>' . sprintf( __( 'Hello %s , ', 'wdm_is_instructor' ), get_user_meta( $author_id, 'first_name', 1 ) . ' ' . get_user_meta( $author_id, 'last_name', 1 ), 1 ) . '</p>';
								$body .= '<p>' . sprintf( __( 'A new purchase has been made for your course <strong>%1$s</strong> by <strong>%2$s</strong>.', 'wdm_is_instructor' ), $assigned_course->post_title, $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) . '</p>';
								$body .= '<p>' . __( 'Thank You', 'wdm_is_instructor' ) . '</p>';
							}
						}

						$find    = [
							'[site_name]',
							'[course_name]',
							'[customer_name]',
							'[instructor_name]',
						];
						$replace = [
							get_bloginfo( 'name' ),
							$assigned_course->post_title,
							$order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
							get_user_meta( $author_id, 'first_name', 1 ) . ' ' . get_user_meta( $author_id, 'last_name', 1 ),
						];

						$author_data = get_userdata( $author_id );
						/**
						* Filter email ID for course purchase emails.
						*
						* @since 3.6.0
						*
						* @var string   $email_id   Email address.
						*/
						$email = apply_filters( 'ir_filter_course_purchase_email_id', $author_data->user_email );
						/**
						* Filter email subject for course purchase email
						*
						* @since 3.6.0
						*
						* @var string   $subject    Email subject.
						*/
						$subject = apply_filters( 'ir_filter_course_purchase_email_subject', str_replace( $find, $replace, $subject ) );
						/**
						* Filter email body for course purchase email.
						*
						* @since 3.6.0
						*
						* @var string   $body       Email body.
						*/
						$body = apply_filters( 'ir_filter_course_purchase_email_body', str_replace( $find, $replace, $body ) );
						/**
						* Filter email headers for course purchase email.
						*
						* @since 3.6.0
						*
						* @var array    $headers    Array of email headers.
						*/
						$headers = apply_filters( 'ir_filter_course_purchase_email_headers', [ 'Content-Type: text/html; charset=UTF-8' ] );

						if ( ! wp_mail( $email, $subject, $body, $headers ) ) {
							error_log( "IR DEBUG MESSAGE :: For Order : $order_id :: Instructor Email not sent successfully" );
						}
					}
				}
			}
		}
	}
}
