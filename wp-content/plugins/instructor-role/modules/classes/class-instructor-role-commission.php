<?php
/**
 * Commission Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore instuctor tobe // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

use LearnDash\Core\Models\Transaction;
use LearnDash\Core\Utilities\Cast;
use LearnDash\Instructor_Role\StellarWP\DB\DB;
use LearnDash\Instructor_Role\Utilities\Number;

if ( ! class_exists( 'Instructor_Role_Commission' ) ) {
	/**
	 * Class Instructor Role Commission Module
	 */
	class Instructor_Role_Commission {
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
		 * Commission Logs Table Name
		 *
		 * @var string
		 *
		 * @since 4.2.0
		 */
		protected $commission_logs_table_name = '';

		/**
		 * Normalizes the commission percentage value.
		 *
		 * @since 5.9.4
		 *
		 * @param mixed $commission_percentage Commission percentage value.
		 * @param int   $instructor_id         Instructor ID.
		 *
		 * @return float
		 */
		public static function normalize_commission_percentage( $commission_percentage, int $instructor_id ): float {
			$commission_percentage = learndash_instructor_role_normalize_float_value( $commission_percentage, 3 );

			// Validate minimum and maximum values.

			// Minimum value.

			/**
			 * Filters the minimum commission percentage value.
			 *
			 * @since 5.9.4
			 *
			 * @param float $min_commission_percentage Minimum commission percentage value.
			 * @param int   $instructor_id             Instructor ID.
			 *
			 * @return float
			 */
			$min_commission_percentage = apply_filters(
				'learndash_instructor_role_commission_percentage_min_value',
				0.0,
				$instructor_id
			);

			if ( $commission_percentage < $min_commission_percentage ) {
				$commission_percentage = $min_commission_percentage;
			}

			// Maximum value.

			/**
			 * Filters the maximum commission percentage value.
			 *
			 * @since 5.9.4
			 *
			 * @param float $max_commission_percentage Maximum commission percentage value.
			 * @param int   $instructor_id             Instructor ID.
			 *
			 * @return float
			 */
			$max_commission_percentage = apply_filters(
				'learndash_instructor_role_commission_percentage_max_value',
				100.0,
				$instructor_id
			);

			if ( $commission_percentage > $max_commission_percentage ) {
				$commission_percentage = $max_commission_percentage;
			}

			return $commission_percentage;
		}

		/**
		 * Constructor
		 */
		public function __construct() {
			global $wpdb;

			$this->plugin_slug                = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->commission_logs_table_name = $wpdb->prefix . 'ir_commission_logs';
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
		 * Creating wdm_instructor_commission table.
		 *
		 * @since 2.4.0
		 */
		public function wdm_instructor_table_setup() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'wdm_instructor_commission';
			// if table doesn't exist then create a new table.
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				$sql = 'CREATE TABLE ' . $table_name . ' (
                id INT NOT NULL AUTO_INCREMENT,
                user_id int,
                order_id int,
                product_id int,
                actual_price float,
                commission_price float,
                transaction_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                product_type varchar(5) DEFAULT NULL,
                PRIMARY KEY  (id)
                        );';
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			} else { // if table already exist then check that product_type column exist or not.
				$fields = $wpdb->get_var( "SHOW fields FROM {$table_name} LIKE 'product_type'" );
				// if column 'product_type' isn't exist.
				if ( 'product_type' != $fields ) {
					$wpdb->query( 'ALTER TABLE ' . $table_name . ' ADD product_type VARCHAR(5) DEFAULT NULL' );
				}
				$this->wdmAddProductTypeToAlteredAttribute( $table_name );
			}
		}

		/**
		 * Update product_type field if it's set to NULL.
		 *
		 * @param string $table_name table name
		 *
		 * @since 2.4.0
		 */
		function wdmAddProductTypeToAlteredAttribute( $table_name ) {
			global $wpdb;
			$undef_product_type = $wpdb->get_results( "SELECT * FROM $table_name  WHERE product_type IS NULL", ARRAY_A );
			if ( ! empty( $undef_product_type ) ) {
				foreach ( $undef_product_type as $row ) {
					$to_add_product_type = '';
					$row_product_id      = $row['product_id'];
					$row_unique_id       = $row['id'];
					if ( get_post_type( $row_product_id ) == 'product' ) {
						$to_add_product_type = 'WC';
					} elseif ( get_post_type( $row_product_id ) == 'download' ) {
						$to_add_product_type = 'EDD';
					} elseif ( get_post_type( $row_product_id ) == 'sfwd-courses' ) {
						$to_add_product_type = 'LD';
					}
					if ( ! empty( $to_add_product_type ) ) {
						$wpdb->update( $table_name, [ 'product_type' => $to_add_product_type ], [ 'id' => $row_unique_id ], [ '%s' ], [ '%d' ] );
					}
				}
			}
		}

		/**
		 * Update user meta of instructor for amount paid.
		 *
		 * @return json_encode status of operation
		 *
		 * @since 2.4.0
		 */
		public function wdm_amount_paid_instructor() {
			if ( ! is_super_admin() ) {
				die();
			}
			$instructor_id = filter_input( INPUT_POST, 'instructor_id', FILTER_SANITIZE_NUMBER_INT );
			if ( ( '' == $instructor_id ) || ( ! wdm_is_instructor( $instructor_id ) ) ) {
				echo json_encode( [ 'error' => __( 'The user is not instructor.', 'wdm_instructor_role' ) ] );
				die();
			}

			// Sanitize float values - handle both comma and dot decimal separators.
			$total_paid       = Cast::to_float( Number::sanitize_decimal( filter_input( INPUT_POST, 'total_paid', FILTER_UNSAFE_RAW ) ) );
			$amount_tobe_paid = Cast::to_float( Number::sanitize_decimal( filter_input( INPUT_POST, 'amount_tobe_paid', FILTER_UNSAFE_RAW ) ) );
			$enter_amount     = Cast::to_float( Number::sanitize_decimal( filter_input( INPUT_POST, 'enter_amount', FILTER_UNSAFE_RAW ) ) );
			$payout_note      = filter_input( INPUT_POST, 'payout_note' );

			$usr_instructor_total = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );

			$usr_instructor_total = $this->getUsrInstructorTotal( $usr_instructor_total );
			if ( ( '' == $amount_tobe_paid || '' == $enter_amount ) || $total_paid != $usr_instructor_total
				|| $enter_amount > $amount_tobe_paid ) {
				echo json_encode( [ 'error' => __( 'Something is not correct with the amount', 'wdm_instructor_role' ) ] );
				die();
			}

			global $wpdb;
			$sql     = "SELECT commission_price FROM {$wpdb->prefix}wdm_instructor_commission WHERE user_id = $instructor_id";
			$results = $wpdb->get_col( $sql );
			if ( empty( $results ) ) {
				echo json_encode( [ 'error' => __( 'Could not find commission', 'wdm_instructor_role' ) ] );
				die();
			} else {
				$valid_amount_tobe_paid = 0;
				foreach ( $results as $value ) {
					$valid_amount_tobe_paid += $value;
				}
				$valid_amount_tobe_paid = round( ( $valid_amount_tobe_paid - $total_paid ), 2 );
				if ( $valid_amount_tobe_paid != $amount_tobe_paid ) {
					echo json_encode( [ 'error' => __( 'Amount to be paid is not correct', 'wdm_instructor_role' ) ] );
					die();
				}
			}

			$new_paid_amount = round( ( $total_paid + $enter_amount ), 2 );
			update_user_meta( $instructor_id, 'wdm_total_amount_paid', $new_paid_amount );

			// Record log entry in manual commission logs.
			$log_entry = $this->add_commission_log_entry( $instructor_id, $enter_amount, $total_paid, $amount_tobe_paid, $payout_note );

			/*
			* instructor_id is id of the instructor
			* enter_amount is amount entered by admin to pay
			* total_paid is the total amount paid by admin to instructor before current transaction
			* amount_tobe_paid is the amount required to be paid by admin
			* new_paid_amount is the total amount paid to instructor after current transaction
			*/
			do_action( 'wdm_commission_amount_paid', $instructor_id, $enter_amount, $total_paid, $amount_tobe_paid, $new_paid_amount );
			$new_amount_tobe_paid = round( ( $amount_tobe_paid - $enter_amount ), 2 );

			$data = [
				'amount_tobe_paid' => $new_amount_tobe_paid,
				'total_paid'       => $new_paid_amount,
				'row'              => $log_entry,
			];
			echo json_encode( [ 'success' => $data ] );
			die();
		}

		/**
		 * Function returns user instructor total.
		 *
		 * @param int $usr_instructor_total usr_instructor_total
		 *
		 * @return int usr_instructor_total
		 *
		 * @since 2.4.0
		 */
		public function getUsrInstructorTotal( $usr_instructor_total ) {
			if ( '' == $usr_instructor_total ) {
				return 0;
			}

			return $usr_instructor_total;
		}

		/**
		 * On woocommerce order complete, adding commission percentage in custom table.
		 *
		 * @param int $order_id order_id
		 *
		 * @since 2.4.0
		 */
		public function wdm_add_record_to_db( $order_id ) {
			$order = new \WC_Order( $order_id );
			global $wpdb;

			$items = $order->get_items();
			foreach ( $items as $item ) {
				$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
				$total      = $item['line_total'];

				$product_post = get_post( $product_id );
				$author_id    = $product_post->post_author;
				// Fix for FCC.
				// - If product not owned by instructor (like admin), then get instructor from the course owner instead of the product owner.
				if ( ! wdm_is_instructor( $author_id ) ) {
					$related_course = get_post_meta( $product_id, '_related_course', true );
					if ( ! empty( $related_course ) ) {
						$course_id       = $related_course[0];
						$assigned_course = get_post( $course_id );
						$author_id       = $assigned_course->post_author;
					}
				}

				$this->register_instructor_commission( Cast::to_int( $author_id ), $order_id, $product_id, $total, 'WC' );
			}
		}

		/**
		 * Process instructor's commissions for LearnDash purchase.
		 *
		 * @since 5.9.4
		 *
		 * @param int $transaction_id Transaction ID.
		 *
		 * @return void
		 */
		public function track_learndash_purchase( $transaction_id ): void {
			$transaction = Transaction::find( $transaction_id );

			if ( ! $transaction ) {
				return;
			}

			$product = $transaction->get_product();

			if ( ! $product ) {
				return;
			}

			$this->register_instructor_commission(
				Cast::to_int( $product->get_post()->post_author ),
				$transaction_id,
				$product->get_id(),
				$this->get_learndash_product_price( $transaction ),
				'LD'
			);
		}

		/**
		 * Adding transaction details after LD transaction.
		 *
		 * @since 2.4.0
		 * @deprecated 5.9.4 This function is no longer used.
		 *
		 * @param int    $meta_id    meta id.
		 * @param int    $object_id  object_id.
		 * @param string $meta_key   meta key.
		 * @param string $meta_value meta value.
		 *
		 * @return void
		 */
		public function wdm_instructor_updated_postmeta( $meta_id, $object_id, $meta_key, $meta_value ) {
			_deprecated_function( __METHOD__, '5.9.4' );

			global $wpdb;
			$post_type = get_post_type( $object_id );
			if ( 'sfwd-transactions' == $post_type && 'course_id' == $meta_key ) {
				$course_id   = $meta_value;
				$course_post = get_post( $course_id );
				$author_id   = $course_post->post_author;
				if ( wdm_is_instructor( $author_id ) ) {
					$commission_percent = get_user_meta( $author_id, 'wdm_commission_percentage', true );
					if ( '' == $commission_percent ) {
						$commission_percent = 0;
					}
					// @since 3.4.0 : Replaced 'payment_gross' with 'mc_gross' since the first is deprecated.
					$total = get_post_meta( $object_id, 'mc_gross', true );

					$payment_method = get_post_meta( $object_id, 'action', 1 );

					// @since - LD-Stripe 1.5.0 : Updated stripe payment method action to 'ld_stripe_init_checkout'
					if ( 'stripe' == $payment_method || 'ld_stripe_init_checkout' == $payment_method ) {
						$total    = floatval( get_post_meta( $object_id, 'stripe_price', true ) );
						$currency = get_post_meta( $object_id, 'stripe_currency', true );
						if ( 'usd' == $currency && defined( 'LEARNDASH_STRIPE_VERSION' ) && version_compare( LEARNDASH_STRIPE_VERSION, '1.8.0' ) < 0 ) {
							// Since stripe stores payments in cents.
							$total = $total / 100;
						}
					} else {
						$check_stripe = get_post_meta( $object_id, 'stripe_payment_intent', true );
						if ( $check_stripe ) {
							$course_payment_type_and_price = learndash_get_course_price( $course_id );
							$total                         = $course_payment_type_and_price['price'];
						}
					}

					if ( '' == $total ) {
						$total = 0;
					}

					$commission_price = ( $total * $commission_percent ) / 100;

					$data = [
						'user_id'          => $author_id,
						'order_id'         => $object_id,
						'product_id'       => $course_id,
						'actual_price'     => $total,
						'commission_price' => $commission_price,
					];
					$wpdb->insert( $wpdb->prefix . 'wdm_instructor_commission', $data );
				}
			}
		}

		/**
		 * To allow instructor to access dashboard.
		 *
		 * @param boolean $prevent_access A flag to prevent access to dashboard.
		 *
		 * @return boolean
		 */
		public function wdmAllowDashboardAccess( $prevent_access ) {
			if ( wdm_is_instructor() ) {
				return false;
			}
			return $prevent_access;
		}

		/**
		 * wdmAddWoocommercePostType adding woocommerce product post type.
		 *
		 * @param array $wdm_ar_post_types Contains list of post type which instructor can access.
		 */
		public function wdmAddWoocommercePostType( $wdm_ar_post_types ) {
			if ( wdmCheckWooDependency() && ! in_array( 'product', $wdm_ar_post_types ) ) {
				array_push( $wdm_ar_post_types, 'product' );
			}
			return $wdm_ar_post_types;
		}

		/**
		 * wdmAddWoocommerceMenu to add menu
		 *
		 * @param array $allowed_tabs List of menus to be shown on dashboard.
		 */
		public function wdmAddWoocommerceMenu( $allowed_tabs ) {
			if ( wdmCheckWooDependency() && ! in_array( 'edit.php?post_type=product', $allowed_tabs ) ) {
				array_push( $allowed_tabs, 'edit.php?post_type=product' );
			} elseif ( ! wdmCheckWooDependency() && in_array( 'edit.php?post_type=product', $allowed_tabs ) ) {
				unset( $allowed_tabs['edit.php?post_type=product'] );
			}
			return $allowed_tabs;
		}

		/**
		 * Conditionally provide access to the 'manage_woocommerce' capability to allow instructors to relate courses to products.
		 * Since LD-Woo added that check in version 1.6.0
		 *
		 * @since 3.2.0
		 */
		public function allowInstructorsToRelateCourses( $all_caps, $requested_caps, $args, $user ) {
			if ( ! defined( 'LEARNDASH_WOOCOMMERCE_VERSION' ) || 0 > version_compare( LEARNDASH_WOOCOMMERCE_VERSION, '1.6.0' ) ) {
				return $all_caps;
			}
			// Check if checking for woocommerce managing capability.
			if ( ! in_array( 'manage_woocommerce', $requested_caps ) ) {
				return $all_caps;
			}

			// Check if instructor.
			if ( ! wdm_is_instructor() ) {
				return $all_caps;
			}

			// Check if product edit page.
			global $post, $current_screen;

			$screen_id = empty( $current_screen ) ? '' : $current_screen->id;

			if ( empty( $post ) || 'product' !== $post->post_type || 'product' != $screen_id ) {
				return $all_caps;
			}

			$all_caps['manage_woocommerce'] = 1;

			return $all_caps;
		}

		/**
		 * Create commission logs table
		 *
		 * @since 4.2.0
		 */
		public function create_commission_logs_table() {
			global $wpdb;
			$table_name      = $this->commission_logs_table_name;
			$charset_collate = $wpdb->get_charset_collate();

			// Check if table exists.
			if ( true === ir_get_settings( 'table_commission_logs_created' ) ) {
				return;
			}

			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) === $table_name ) {
				return;
			}

			// No table found, so lets create it.
			$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id int NOT NULL,
			date_time datetime NOT NULL,
			notes varchar(255),
			amount float NOT NULL,
			total_paid float NOT NULL,
			remaining float NOT NULL,
			PRIMARY KEY  (id)
			) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( $sql );
			ir_set_settings( 'table_commission_logs_created', true );
		}

		/**
		 * Record manual commission payment log in the database
		 *
		 * @since 4.2.0
		 *
		 * @param int    $instructor_id      User ID of the instructor.
		 * @param float  $amount             Amount being paid.
		 * @param float  $total_paid         Total amount paid before current transaction.
		 * @param float  $total_unpaid       Amount required to be paid by the admin.
		 * @param string $payout_note        Note added for this manual payout transaction.
		 * @return $status             Array of the newly added row if success, false otherwise.
		 */
		public function add_commission_log_entry( $instructor_id, $amount, $total_paid, $total_unpaid, $payout_note ) {
			global $wpdb;

			$table_name = $this->commission_logs_table_name;

			$timestamp = time();
			$row_data  = [
				'user_id'    => $instructor_id,
				'date_time'  => gmdate( 'Y-m-d H:i:s', $timestamp ),
				'notes'      => $payout_note,
				'amount'     => round( $amount, 2 ),
				'total_paid' => round( floatval( $total_paid + $amount ), 2 ),
				'remaining'  => round( floatval( $total_unpaid - $amount ), 2 ),
			];

			$status = $wpdb->insert(
				$table_name,
				$row_data,
				[ '%d', '%s', '%s', '%f', '%f', '%f' ]
			);

			// Return the newly added row on success.
			if ( false !== $status ) {
				$row_data['log_id']        = $wpdb->insert_id;
				$row_data['nonce']         = esc_attr( wp_create_nonce( 'ir_commission_log_actions' ) );
				$row_data['date_time_gmt'] = gmdate( 'Y-m-d H:i:s T', $timestamp );
				return $row_data;
			}
			return $status;
		}

		/**
		 * Enqueue scripts for commission logs
		 *
		 * @since 4.2.0
		 */
		public function enqueue_commission_logs_scripts() {
			$screen = get_current_screen();
			if ( ( 'learndash-lms_page_instuctor' === $screen->id || 'learndash_page_instuctor' === $screen->id || 'admin_page_instuctor' === $screen->id ) && ( ( array_key_exists( 'tab', $_GET ) && 'commission_report' === $_GET['tab'] ) || wdm_is_instructor() ) ) {
				wp_enqueue_style(
					'ir-datatable-styles',
					plugins_url( 'css/datatables.min.css', __DIR__ ),
					[],
					gmdate( 'hi', time() )
				);

				wp_enqueue_script(
					'ir-datatables-script',
					plugins_url( 'js/datatables.min.js', __DIR__ ),
					[ 'jquery' ],
					gmdate( 'hi', time() ),
					true
				);

				wp_enqueue_style(
					'ir-datetime-picker-styles',
					plugins_url( 'css/jquery.datetimepicker.css', __DIR__ ),
					[],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/css/jquery.datetimepicker.css' )
				);

				wp_register_script(
					'ir-datetime-picker-script',
					plugins_url( 'js/jquery.datetimepicker.full.js', __DIR__ ),
					[ 'jquery' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/jquery.datetimepicker.full.js' ),
					true
				);

				wp_enqueue_script(
					'ir-commission-logs-script',
					plugins_url( 'js/commission/ir-manual-commission-logs.js', __DIR__ ),
					[ 'jquery', 'ir-paypal-payout-script', 'ir-datatables-script', 'ir-datetime-picker-script' ],
					filemtime( INSTRUCTOR_ROLE_ABSPATH . '/modules/js/commission/ir-manual-commission-logs.js' ),
					true
				);

				wp_localize_script(
					'ir-commission-logs-script',
					'ir_loc_data',
					[
						'ajax_url'                   => admin_url( 'admin-ajax.php' ),
						'modal_pay_button_html'      => '<input class="button-primary" type="button" value="' . esc_html__( 'Pay', 'wdm_instructor_role' ) . '" id="ir_pay_click" />',
						'main_pay_button_html'       => '<a href="#" class="button-primary" id="wdm_pay_amount">' . esc_html__( 'Pay', 'wdm_instructor_role' ) . '</a>',
						'invalid_date_message'       => __( 'Invalid date. Please enter a valid date', 'wdm_instructor_role' ),
						'invalid_amount_message'     => __( 'Invalid amount. Please enter a amount', 'wdm_instructor_role' ),
						'additional_amount_message'  => __( 'Invalid amount. You cannot pay more than what is owed', 'wdm_instructor_role' ),
						'confirm_delete_log_message' => __( 'Are you sure you want to delete this commission log ?', 'wdm_instructor_role' ),
						'note_limit'                 => 255,
						'invalid_note_length'        => __( 'The size of the note exceeds the maximum length of 255 characters', 'wdm_instructor_role' ),
						'is_admin'                   => current_user_can( 'manage_options' ) ? true : false,
					]
				);
			}
		}

		/**
		 * Add the manual commission logs table.
		 *
		 * @since 4.2.0
		 *
		 * @param int $instructor_id    User ID of the Instructor.
		 */
		public function add_commission_logs_table( $instructor_id ) {
			$commission_logs = $this->get_manual_commission_logs( $instructor_id );
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/commission/ir-manual-commission-logs-table.template.php',
				[
					'instructor_id'   => $instructor_id,
					'commission_logs' => $commission_logs,
				]
			);
		}

		/**
		 * Fetch manual commission logs of an instructor
		 *
		 * @since 4.2.0
		 *
		 * @param int $instructor_id    User ID of the instructor.
		 * @return array                Array of commission logs.
		 */
		public function get_manual_commission_logs( $instructor_id ) {
			global $wpdb;
			$commission_logs = [];

			if ( ! empty( $instructor_id ) ) {
				$table_name      = $this->commission_logs_table_name;
				$commission_logs = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT id, date_time, notes, amount, remaining FROM {$table_name} WHERE user_id = %d ORDER BY date_time DESC",
						$instructor_id
					),
					ARRAY_A
				);
			}
			/**
			 * Filter returned manual commission logs
			 *
			 * @since 4.2.0
			 *
			 * @param array $commission_logs    Array of commission logs for the instructor.
			 * @param int   $instructor_id      User ID of the instructor.
			 */
			return apply_filters( 'ir_filter_get_commission_logs', $commission_logs, $instructor_id );
		}

		/**
		 * Delete a manual commission log via ajax
		 *
		 * @since 4.2.0
		 */
		public function ajax_delete_manual_commission_log() {
			$response = [
				'message' => __( 'Some error occurred and the log was not deleted. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_commission_log_actions', 'nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$commission_log_id = filter_input( INPUT_POST, 'log_id', FILTER_SANITIZE_NUMBER_INT );

			// Check if admin or super admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				$response['message'] = __( 'Only administrator can delete commission logs.', 'wdm_instructor_role' );
				wp_send_json_error( $response );
			}

			global $wpdb;
			$table_name = $this->commission_logs_table_name;

			// Get the commission log.
			$commission_log = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $commission_log_id ),
				ARRAY_A
			);

			// Check if commission log exists.
			if ( null === $commission_log ) {
				$response['message'] = __( 'Commission log not found or no such log exists', 'wdm_instructor_role' );
				wp_send_json_error( $response );
			}

			// Update commission amount to reflect in instructor unpaid amount.
			$instructor_id = intval( $commission_log['user_id'] );
			$revert_amount = floatval( $commission_log['amount'] );

			$paid_amount = floatval( get_user_meta( $instructor_id, 'wdm_total_amount_paid', true ) );
			$total_paid  = $paid_amount - $revert_amount;
			update_user_meta( $instructor_id, 'wdm_total_amount_paid', $total_paid );

			// Delete commission log in DB.
			$results = $wpdb->delete(
				$table_name,
				[
					'id' => $commission_log_id,
				],
				[ '%d' ]
			);

			// Check if deleted successfully.
			if ( false === $results ) {
				// Revert changes if log not deleted.
				update_user_meta( $instructor_id, 'wdm_total_amount_paid', $paid_amount );
				wp_send_json_error( $response );
			} else {
				$response = [
					'message'       => __( 'Commission Log deleted successfully!!', 'wdm_instructor_role' ),
					'type'          => 'success',
					'paid_earnings' => $total_paid,
					'revert_amount' => $revert_amount,
				];
				wp_send_json_success( $response );
			}
		}

		/**
		 * Update a manual commission log via ajax
		 *
		 * @since 4.2.0
		 */
		public function ajax_update_manual_commission_log() {
			$response = [
				'message' => __( 'Some error occurred and the log was not updated. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_update_commission_log', 'nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Sanitize data.
			$log_id     = filter_input( INPUT_POST, 'log_id', FILTER_SANITIZE_NUMBER_INT );
			$log_date   = filter_input( INPUT_POST, 'date' );
			$log_amount = Cast::to_float( Number::sanitize_decimal( filter_input( INPUT_POST, 'amount', FILTER_UNSAFE_RAW ) ) );
			$log_note   = filter_input( INPUT_POST, 'note' );

			// Check if admin or super admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				$response['message'] = __( 'Only administrator can delete commission logs.', 'wdm_instructor_role' );
				wp_send_json_error( $response );
			}

			// Check if valid amount.
			if ( $log_amount < 0 ) {
				$response['message'] = __( 'Invalid amount. Please enter a valid amount.', 'wdm_instructor_role' );
				wp_send_json_error( $response );
			}

			global $wpdb;
			$table_name = $this->commission_logs_table_name;

			// Get the commission log.
			$commission_log = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $log_id ),
				ARRAY_A
			);

			// Check if commission log exists.
			if ( null === $commission_log ) {
				$response['message'] = __( 'Commission log not found or no such log exists', 'wdm_instructor_role' );
				wp_send_json_error( $response );
			}

			// Update commission amount to reflect in instructor unpaid amount.
			$instructor_id   = intval( $commission_log['user_id'] );
			$original_amount = floatval( $commission_log['amount'] );

			$paid_amount = floatval( get_user_meta( $instructor_id, 'wdm_total_amount_paid', true ) );
			$total_paid  = $paid_amount - $original_amount + $log_amount;
			update_user_meta( $instructor_id, 'wdm_total_amount_paid', $total_paid );

			// Update commission log in DB.
			$results = $wpdb->update(
				$table_name,
				[
					'date_time' => $log_date,
					'amount'    => $log_amount,
					'notes'     => $log_note,
				],
				[
					'id' => $log_id,
				],
				[ '%s', '%f', '%s' ],
				[ '%d' ]
			);

			// Check if deleted successfully.
			if ( false === $results ) {
				// Revert changes if log not deleted.
				update_user_meta( $instructor_id, 'wdm_total_amount_paid', $paid_amount );
				wp_send_json_error( $response );
			} else {
				$response = [
					'message'       => __( 'Commission Log updated successfully!!', 'wdm_instructor_role' ),
					'type'          => 'success',
					'paid_earnings' => $total_paid,
				];
				wp_send_json_success( $response );
			}       }

		/**
		 * Functionality for exporting instructor order details
		 */
		public function ajax_export_order_details() {
			$response = [
				'message' => __( 'Some error occurred and the export was not done. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir-export-order-details', 'nonce', false ) ) {
				wp_send_json_error( $response );
			}

			global $wpdb;
			$instructor_id                  = filter_input( INPUT_GET, 'wdm_instructor_id', FILTER_SANITIZE_NUMBER_INT );
			$start_date                     = filter_input( INPUT_GET, 'start_date' );
			$end_date                       = filter_input( INPUT_GET, 'end_date' );
			$search_conditions              = '';
			$order_details                  = [];
			$instructor_role_setting_object = Instructor_Role_Settings::get_instance();

			// Add additional conditions for start and end date.
			if ( ! empty( $start_date ) ) {
				$start_date         = date( 'Y-m-d', strtotime( $start_date ) );
				$search_conditions .= " AND transaction_time >= '$start_date 00:00:00'";
			}

			if ( ! empty( $end_date ) ) {
				$end_date           = date( 'Y-m-d', strtotime( $end_date ) );
				$search_conditions .= " AND transaction_time <= '$end_date 23:59:59'";
			}

			// Adding SQL conditions to retrieve the results.
			$sql = "SELECT *, {$wpdb->prefix}posts.post_title AS course_name
			FROM {$wpdb->prefix}wdm_instructor_commission
			LEFT JOIN {$wpdb->prefix}posts
			ON {$wpdb->prefix}wdm_instructor_commission.product_id = {$wpdb->prefix}posts.ID
			WHERE user_id = %d {$search_conditions}";

			$results = $wpdb->get_results( $wpdb->prepare( $sql, $instructor_id ) );

			if ( ! empty( $results ) ) {
				foreach ( $results as $value ) {
					$row             = [
						'order_id'         => $value->order_id,
						'date'             => $value->transaction_time,
						'course_title'     => $instructor_role_setting_object->wdmGetPostTitle( $value->product_id ),
						'actual_price'     => $value->actual_price,
						'commission_price' => $value->commission_price,
						'product_type'     => $value->product_type,
					];
					$order_details[] = $row;
				}
			} else {
				// add json error class.
				$response['message'] = __( 'Commission log not found or no such log exists', 'wdm_instructor_role' );
				wp_send_json_error( $response );
			}

			if ( file_exists( LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php' ) ) {
				require_once LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php';
				$csv = new \LmsParseCSV();

				// Try exporting the CSV file.
				$csv->file            = 'order_details.csv';
				$csv->output_filename = 'order_details.csv';
				$csv->output( 'order_details.csv', $order_details, array_keys( reset( $order_details ) ) );
				die();
			}

			// If we reach this point, an error occurred.
			wp_send_json_error( $response );
		}

		/**
		 * Functionality for exporting instructor manual commission log
		 */
		public function ajax_export_manual_commission_log() {
			$response = [
				'message' => __( 'Some error occurred and the export was not done. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir-export-manual-commission-log', 'nonce', false ) ) {
				wp_send_json_error( $response );
			}

			global $wpdb;
			$instructor_id = filter_input( INPUT_GET, 'wdm_instructor_id', FILTER_SANITIZE_NUMBER_INT );

			$sql                     = "SELECT * FROM {$wpdb->prefix}ir_commission_logs WHERE user_id = %d";
			$results                 = $wpdb->get_results( $wpdb->prepare( $sql, $instructor_id ) );
			$manual_transaction_data = [];

			if ( ! empty( $results ) ) {
				foreach ( $results as $value ) {
					$row                       = [
						'date'      => $value->date_time,
						'amount'    => $value->amount,
						'remaining' => $value->remaining,
						'note'      => $value->notes,
					];
					$manual_transaction_data[] = $row;
				}
			} else {
				$response['message'] = __( 'Commission log not found or no such log exists', 'wdm_instructor_role' );
				wp_send_json_error( $response );
			}

			if ( file_exists( LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php' ) ) {
				require_once LEARNDASH_LMS_LIBRARY_DIR . '/parsecsv.lib.php';
				$csv = new \LmsParseCSV();

				// Try exporting the CSV file.
				$csv->file            = 'manual_transaction.csv';
				$csv->output_filename = 'manual_transaction.csv';
				$csv->output( 'manual_transaction.csv', $manual_transaction_data, array_keys( reset( $manual_transaction_data ) ) );
				die();
			}

			// If we reach this point, an error occurred.
			wp_send_json_error( $response );
		}

		/**
		 * Functionality for bulk updating commission log for instructors
		 *
		 * @since 5.9.0
		 */
		public function ajax_bulk_update_commission_log() {
			$response = [
				'message' => __( 'Some error occurred and the update was not done. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir_bulk_update_commission_log', 'nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Check if user is admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
				);
			}

			$instructor_ids        = filter_input( INPUT_POST, 'instructor_ids', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
			$commission_percentage = filter_input( INPUT_POST, 'commission_percentage', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

			foreach ( $instructor_ids as $user_id ) {
				update_user_meta(
					$user_id,
					'wdm_commission_percentage',
					self::normalize_commission_percentage( $commission_percentage, Cast::to_int( $user_id ) )
				);
			}

			wp_send_json_success(
				[
					'message' => __( 'Successfully updated commissions', 'wdm_instructor_role' ),
					'type'    => 'success',
				]
			);
		}

		/**
		 * Functionality for disabling commission for specific instructor
		 *
		 * @since 5.9.0
		 */
		public function ajax_disable_commission() {
			$response = [
				'message' => __( 'Some error occurred and the update was not done. Please refresh the page and try again.', 'wdm_instructor_role' ),
				'type'    => 'error',
			];

			// Verify Nonce.
			if ( ! check_ajax_referer( 'ir-disable-commission', 'nonce', false ) ) {
				wp_send_json_error( $response );
			}

			// Check if user is admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error(
					[
						'message' => __( 'You do not have sufficient privileges to perform this action', 'wdm_instructor_role' ),
						'type'    => 'error',
					],
					403
				);
			}

			$instructor_id         = filter_input( INPUT_POST, 'instructor_id', FILTER_SANITIZE_NUMBER_INT );
			$commission_percentage = filter_input( INPUT_POST, 'commission_percentage', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
			$commission            = filter_input( INPUT_POST, 'commission', FILTER_SANITIZE_NUMBER_INT );

			if ( 1 == $commission && ! empty( $commission_percentage ) ) {
				update_user_meta(
					$instructor_id,
					'wdm_commission_percentage',
					self::normalize_commission_percentage( $commission_percentage, Cast::to_int( $instructor_id ) )
				);
				update_user_meta( $instructor_id, 'ir_commission_disabled', '' );
			} else {
				update_user_meta( $instructor_id, 'ir_commission_disabled', 1 );
			}

			wp_send_json_success(
				[
					'message' => __( 'Successfully updated commissions', 'wdm_instructor_role' ),
					'type'    => 'success',
				]
			);
		}

		/**
		 *
		 * Registers the commission for the instructor.
		 *
		 * @since 5.9.4
		 *
		 * @param int    $instructor_id         Instructor ID.
		 * @param int    $order_id              Order ID.
		 * @param int    $product_id            Product ID.
		 * @param float  $product_price         Product price.
		 * @param string $product_type          Product type.
		 *
		 * @return void
		 */
		private function register_instructor_commission(
			int $instructor_id,
			int $order_id,
			int $product_id,
			float $product_price,
			string $product_type
		): void {
			// Bail if the product price is invalid.
			if ( $product_price <= 0 ) {
				return;
			}

			// Bail if the user is not an instructor or the commission is disabled.

			if (
				! wdm_is_instructor( $instructor_id )
				|| Cast::to_bool( get_user_meta( $instructor_id, 'ir_commission_disabled', true ) )
			) {
				return;
			}

			$commission_percentage = Cast::to_float(
				get_user_meta( $instructor_id, 'wdm_commission_percentage', true )
			);

			// Bail if the commission percentage is invalid.

			if ( $commission_percentage <= 0 ) {
				return;
			}

			$commission_data = [
				'user_id'          => $instructor_id,
				'order_id'         => $order_id,
				'product_id'       => $product_id,
				'actual_price'     => $product_price,
				'commission_price' => $product_price * ( $commission_percentage / 100 ),
				'product_type'     => $product_type,
			];

			/**
			 * Filters the commission data before saving it to the database.
			 *
			 * @since 5.9.4
			 *
			 * @param array{user_id: int, order_id: int, product_id: int, actual_price: float, commission_price: float, product_type: string} $commission_data Commission data.
			 *
			 * @return array{user_id: int, order_id: int, product_id: int, actual_price: float, commission_price: float, product_type: string}
			 */
			$commission_data = apply_filters(
				'learndash_instructor_role_instructor_commission_data',
				$commission_data
			);

			DB::table( 'wdm_instructor_commission' )
			->upsert(
				$commission_data,
				[ 'user_id','order_id', 'product_id' ]
			);
		}

		/**
		 * Returns the price of the LearnDash product.
		 *
		 * @since 5.9.4
		 *
		 * @param Transaction $transaction Transaction object.
		 *
		 * @return float
		 */
		private function get_learndash_product_price( Transaction $transaction ): float {
			$pricing_dto = $transaction->get_pricing();

			if ( $transaction->is_free() ) {
				return 0.; // Free.
			}

			if ( $pricing_dto->trial_duration_value > 0 ) {
				return $pricing_dto->trial_price; // Subscription with a trial.
			}

			if ( $pricing_dto->discount > 0 ) {
				return $pricing_dto->discounted_price; // Discounted price (both for one-time and subscription).
			}

			return $pricing_dto->price; // Regular price (both for one-time and subscription).
		}
	}
}
