<?php
/**
 * Paypal Payouts Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 *
 * cspell:ignore instuctor // ignoring misspelled words that we can't change now.
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;


require INSTRUCTOR_ROLE_ABSPATH . 'libs/Payouts-PHP-SDK/vendor/autoload.php';

use Exception;
use LearnDash\Core\Utilities\Cast;
use LearnDash\Instructor_Role\Utilities\Number;
use PaypalPayoutsSDK\Core\PayPalHttpClient;
use PaypalPayoutsSDK\Core\SandboxEnvironment;
use PaypalPayoutsSDK\Core\ProductionEnvironment;
use PaypalPayoutsSDK\Payouts\PayoutsPostRequest;
use PaypalPayoutsSDK\Payouts\PayoutsGetRequest;
use PaypalPayoutsSDK\Payouts\PayoutsItemGetRequest;
use PaypalPayoutsSDK\Payouts\PayoutsItemCancelRequest;

if ( ! class_exists( 'Instructor_Role_Payouts' ) ) {
	/**
	 * Class Instructor Role Comments Module
	 */
	class Instructor_Role_Payouts {
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
		 * Instructor Payouts table name
		 *
		 * @var string
		 * @since 3.4.0
		 */
		protected $payouts_table_name = '';

		/**
		 * List of paypal payouts response status codes
		 *
		 * @var array
		 * @since 3.4.0
		 */
		protected $payout_status_codes = null;

		/**
		 * List of possible paypal payout transaction statuses
		 *
		 * @var array
		 * @since 3.4.0
		 */
		protected $payout_transaction_status = null;

		public function __construct() {
			global $wpdb;
			$this->plugin_slug               = INSTRUCTOR_ROLE_TXT_DOMAIN;
			$this->payouts_table_name        = $wpdb->prefix . 'ir_paypal_payouts_transactions';
			$this->payout_status_codes       = $this->set_payout_status_codes();
			$this->payout_transaction_status = $this->set_payout_transaction_status();
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
		 * Create payouts table to store transactions.
		 *
		 * @since 3.4.0
		 */
		public function create_payouts_table() {
			global $wpdb;
			$table_name      = $this->payouts_table_name;
			$charset_collate = $wpdb->get_charset_collate();

			// Create table if not exists.
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				$sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                user_id int NOT NULL,
                batch_id varchar(20) NOT NULL,
                status varchar(20) NOT NULL,
                transaction_status varchar(20) NOT NULL,
                amount float NOT NULL,
                type varchar(10) NOT NULL,
                PRIMARY KEY  (id)
                ) $charset_collate;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}

		/**
		 * Set paypal status codes with their description
		 *
		 * @return array    List of status payout codes along with their explanation.
		 * @since 3.4.0
		 */
		protected function set_payout_status_codes() {
			// List of available payout status codes along with their explanation.
			$status_codes = [
				'200' => __( 'OK	The request succeeded.', 'wdm_instructor_role' ),
				'201' => __( 'Created	A POST method successfully created a resource. If the resource was already created by a previous execution of the same method, for example, the server returns the HTTP 200 OK status code.', 'wdm_instructor_role' ),
				'202' => __( 'Accepted	The server accepted the request and will execute it later.', 'wdm_instructor_role' ),
				'204' => __( 'No Content	The server successfully executed the method but returns no response body.', 'wdm_instructor_role' ),
				'400' => __( 'Bad Request	INVALID_REQUEST. Request is not well-formed, syntactically incorrect, or violates schema.', 'wdm_instructor_role' ),
				'401' => __( 'Unauthorized	AUTHENTICATION_FAILURE. Authentication failed due to invalid authentication credentials.', 'wdm_instructor_role' ),
				'403' => __( 'Forbidden	NOT_AUTHORIZED. Authorization failed due to insufficient permissions.', 'wdm_instructor_role' ),
				'404' => __( 'Not Found	RESOURCE_NOT_FOUND. The specified resource does not exist.', 'wdm_instructor_role' ),
				'405' => __( 'Method Not Allowed	METHOD_NOT_SUPPORTED. The server does not implement the requested HTTP method.', 'wdm_instructor_role' ),
				'406' => __( 'Not Acceptable	MEDIA_TYPE_NOT_ACCEPTABLE. The server does not implement the media type that would be acceptable to the client.', 'wdm_instructor_role' ),
				'415' => __( 'Unsupported Media Type	UNSUPPORTED_MEDIA_TYPE. The server does not support the request payload’s media type.', 'wdm_instructor_role' ),
				'422' => __( 'Unprocessable Entity	UNPROCESSABLE_ENTITY. The API cannot complete the requested action, or the request action is semantically incorrect or fails business validation.', 'wdm_instructor_role' ),
				'429' => __( 'Unprocessable Entity	RATE_LIMIT_REACHED. Too many requests. Blocked due to rate limiting.', 'wdm_instructor_role' ),
				'500' => __( 'Internal Server Error	INTERNAL_SERVER_ERROR. An internal server error has occurred.', 'wdm_instructor_role' ),
				'503' => __( 'Service Unavailable	SERVICE_UNAVAILABLE. Service Unavailable.', 'wdm_instructor_role' ),
			];
			/**
			 * Filter payout status codes list
			 *
			 * Filter paypal payout batch status codes to add or update codes or messages
			 *
			 * @since 3.4.0
			 *
			 * @param array $status_codes   List of possible payout batch status codes.
			 */
			return apply_filters( 'ir_filter_paypal_payout_status_codes', $status_codes );
		}

		/**
		 * Set the default list of possible payout transaction status with details
		 *
		 * @return array    List of payout transaction status codes along with their explanation.
		 * @since 3.4.0
		 */
		protected function set_payout_transaction_status() {
			// List of possible transaction statuses on processing of a payout.
			$transaction_status = [
				'SUCCESS'   => __( 'Funds have been credited to the recipient’s account.', 'wdm_instructor_role' ),
				'FAILED'    => __( 'This payout request has failed, so funds were not deducted from the sender’s account.', 'wdm_instructor_role' ),
				'PENDING'   => __( 'Your payout request was received and will be processed.', 'wdm_instructor_role' ),
				'UNCLAIMED' => __( 'The recipient for this payout does not have a PayPal account. A link to sign up for a PayPal account was sent to the recipient. However, if the recipient does not claim this payout within 30 days, the funds are returned to your account.', 'wdm_instructor_role' ),
				'RETURNED'  => __( 'The recipient has not claimed this payout, so the funds have been returned to your account.', 'wdm_instructor_role' ),
				'ONHOLD'    => __( 'This payout request is being reviewed and is on hold.', 'wdm_instructor_role' ), // cspell:disable-line .
				'BLOCKED'   => __( 'This payout request has been blocked.', 'wdm_instructor_role' ),
				'REFUNDED'  => __( 'This payout request was refunded.', 'wdm_instructor_role' ),
				'REVERSED'  => __( 'This payout request was reversed.', 'wdm_instructor_role' ),
			];
		}

		/**
		 * Add payout specific fields to instructor profile
		 *
		 * @param object $user      Current WP_User object.
		 *
		 * @since 3.4.0
		 */
		public function add_instructor_payout_fields( $user ) {
			// If not instructor or admin then return.
			if ( ! ( wdm_is_instructor() || current_user_can( 'manage_options' ) ) ) {
				return;
			}
			// Get payout email.
			$payout_email = get_user_meta( $user->ID, 'ir_paypal_payouts_email', true );
			$payout_email = ( empty( $payout_email ) ) ? '' : $payout_email;

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/payouts/ir-paypal-payouts-settings.template.php',
				[
					'payout_email' => $payout_email,
				]
			);
		}

		/**
		 * Save instructor payout details
		 *
		 * @param int $user_id      ID of the User.
		 *
		 * @since 3.4.0
		 */
		public function save_instructor_payout_details( $user_id ) {
			// If not instructor or admin then return.
			if ( ! ( wdm_is_instructor() || current_user_can( 'manage_options' ) ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_payouts_nonce', $_POST ) || ! wp_verify_nonce( $_POST['ir_payouts_nonce'], 'ir_paypal_payout_settings_nonce' ) ) {
				return;
			}

			// Check if email set.
			if ( ! array_key_exists( 'ir_paypal_payouts_email', $_POST ) || empty( $_POST['ir_paypal_payouts_email'] ) ) {
				return;
			}

			// Update.
			update_user_meta(
				$user_id,
				'ir_paypal_payouts_email',
				trim( $_POST['ir_paypal_payouts_email'] )
			);
		}

		/**
		 * Add paypal payouts settings tab in Instructor Settings
		 *
		 * @param string $current_tab   Current selected instructor tab.
		 *
		 * @since 3.4.0
		 */
		public function add_payouts_admin_settings_tab( $tabs, $current_tab ) {
			// Check if admin.
			if ( ! current_user_can( 'manage_options' ) ) {
				return $tabs;
			}

			// Check if payouts tab already exists.
			if ( ! array_key_exists( 'ir-paypal-payouts', $tabs ) ) {
				$tabs['ir-paypal-payouts'] = [
					'title'  => __( 'Paypal Payouts', 'wdm_instructor_role' ),
					'access' => [ 'admin' ],
					'svg'    => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-credit-card-pay" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 19h-6a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v4.5" /><path d="M3 10h18" /><path d="M16 19h6" /><path d="M19 16l3 3l-3 3" /><path d="M7.005 15h.005" /><path d="M11 15h2" /></svg>',
				];
			}
			return $tabs;
		}

		/**
		 * Display paypal payout settings for configuring payouts.
		 *
		 * @since 3.4
		 */
		public function add_payouts_admin_settings_tab_content( $current_tab ) {
			// Check if admin and payouts tab.
			if ( ! current_user_can( 'manage_options' ) || 'ir-paypal-payouts' != $current_tab ) {
				return;
			}

			$payout_test_environment          = get_option( 'ir_payout_test_environment' );
			$payout_client_id                 = get_option( 'ir_payout_client_id' );
			$payout_client_secret_key         = get_option( 'ir_payout_client_secret_key' );
			$payout_sandbox_client_id         = get_option( 'ir_payout_sandbox_client_id' );
			$payout_sandbox_client_secret_key = get_option( 'ir_payout_sandbox_client_secret_key' );
			$payout_currency                  = get_option( 'ir_payout_currency' );
			$payout_pay_note                  = get_option( 'ir_payout_pay_note' );

			/**
			 * Filter list of paypal payout currencies used for configuring payouts
			 *
			 * @param array     Array of supported paypal payout currencies with code.
			 */
			$payout_currencies = apply_filters(
				'ir_filter_paypal_payout_currencies',
				[
					'USD' => 'United States dollar',
					'AUD' => 'Australian dollar',
					'BRL' => 'Brazilian real',
					'CAD' => 'Canadian dollar',
					'CZK' => 'Czech koruna',
					'DKK' => 'Danish krone',
					'EUR' => 'Euro',
					'HKD' => 'Hong Kong dollar',
					'HUF' => 'Hungarian forint',
					'INR' => 'Indian rupee',
					'ILS' => 'Israeli new shekel',
					'JPY' => 'Japanese yen',
					'MYR' => 'Malaysian ringgit',
					'MXN' => 'Mexican peso',
					'TWD' => 'New Taiwan dollar',
					'NZD' => 'New Zealand dollar',
					'NOK' => 'Norwegian krone',
					'PHP' => 'Philippine peso',
					'PLN' => 'Polish złoty', // cspell:disable-line .
					'GBP' => 'Pound sterling',
					'RUB' => 'Russian ruble',
					'SGD' => 'Singapore dollar',
					'SEK' => 'Swedish krona',
					'CHF' => 'Swiss franc',
					'THB' => 'Thai baht',
				]
			);

			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/payouts/ir-paypal-payouts-admin-settings.template.php',
				[
					'payout_test_environment'          => $payout_test_environment,
					'payout_client_id'                 => $payout_client_id,
					'payout_client_secret_key'         => $payout_client_secret_key,
					'payout_sandbox_client_id'         => $payout_sandbox_client_id,
					'payout_sandbox_client_secret_key' => $payout_sandbox_client_secret_key,
					'payout_currency'                  => $payout_currency,
					'payout_pay_note'                  => $payout_pay_note,
					'payout_currencies'                => $payout_currencies,
				]
			);
		}

		/**
		 * Save paypal payout admin configuration settings
		 *
		 * @since 3.4
		 */
		public function save_payouts_admin_settings() {
			// If not admin then return.
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_nonce', $_POST ) || ! wp_verify_nonce( $_POST['ir_nonce'], 'ir_paypal_payout_admin_settings_nonce' ) ) {
				return;
			}

			if ( array_key_exists( 'ir_payout_test_environment', $_POST ) && ! empty( $_POST['ir_payout_test_environment'] ) ) {
				update_option( 'ir_payout_test_environment', trim( $_POST['ir_payout_test_environment'] ) );
			} else {
				delete_option( 'ir_payout_test_environment' );
			}

			if ( array_key_exists( 'ir_payout_client_id', $_POST ) && ! empty( $_POST['ir_payout_client_id'] ) ) {
				update_option( 'ir_payout_client_id', trim( $_POST['ir_payout_client_id'] ) );
			} else {
				delete_option( 'ir_payout_client_id' );
			}

			if ( array_key_exists( 'ir_payout_client_secret_key', $_POST ) && ! empty( $_POST['ir_payout_client_secret_key'] ) ) {
				update_option( 'ir_payout_client_secret_key', trim( $_POST['ir_payout_client_secret_key'] ) );
			} else {
				delete_option( 'ir_payout_client_secret_key' );
			}

			if ( array_key_exists( 'ir_payout_sandbox_client_id', $_POST ) && ! empty( $_POST['ir_payout_sandbox_client_id'] ) ) {
				update_option( 'ir_payout_sandbox_client_id', trim( $_POST['ir_payout_sandbox_client_id'] ) );
			} else {
				delete_option( 'ir_payout_sandbox_client_id' );
			}

			if ( array_key_exists( 'ir_payout_sandbox_client_secret_key', $_POST ) && ! empty( $_POST['ir_payout_sandbox_client_secret_key'] ) ) {
				update_option( 'ir_payout_sandbox_client_secret_key', trim( $_POST['ir_payout_sandbox_client_secret_key'] ) );
			} else {
				delete_option( 'ir_payout_sandbox_client_secret_key' );
			}

			if ( array_key_exists( 'ir_payout_currency', $_POST ) && ! empty( $_POST['ir_payout_currency'] ) ) {
				update_option( 'ir_payout_currency', trim( $_POST['ir_payout_currency'] ) );
			}

			if ( array_key_exists( 'ir_payout_pay_note', $_POST ) && ! empty( $_POST['ir_payout_pay_note'] ) ) {
				update_option( 'ir_payout_pay_note', trim( $_POST['ir_payout_pay_note'] ) );
			} else {
				delete_option( 'ir_payout_pay_note' );
			}
		}

		/**
		 * Override commission payment template to include payouts.
		 *
		 * @param string $template_path     Template path
		 * @return string                   Updated template path
		 * @since 3.4
		 */
		public function add_payout_commissions_template( $template_path ) {
			if ( INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/payouts/ir-commission-payment.template.php' === $template_path ) {
				$template_path = INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/payouts/ir-commission-payout-payment.template.php';
			}
			return $template_path;
		}

		/**
		 * Enqueue scripts for paypal payouts
		 *
		 * @since 3.4.0
		 */
		public function enqueue_payout_scripts() {
			global $current_screen;
			// Check if commission reports screen.
			$page_slug = sanitize_title( __( 'LearnDash LMS', 'learndash' ) ) . '_page_instuctor';
			if ( ( $page_slug != $current_screen->id && 'learndash_page_instuctor' != $current_screen->id && 'admin_page_instuctor' != $current_screen->id ) || empty( $_GET ) || ! array_key_exists( 'page', $_GET ) || 'instuctor' != $_GET['page'] ) {
				return;
			}

			wp_enqueue_script(
				'ir-paypal-payout-script',
				plugins_url( 'js/ir-paypal-payout-script.js', __DIR__ ),
				[ 'jquery' ]
			);

			wp_localize_script(
				'ir-paypal-payout-script',
				'ir_commission_data',
				[
					'ajax_url'                      => admin_url( 'admin-ajax.php' ),
					'payment_method_empty'          => __( 'Please select a valid payment method', 'wdm_instructor_role' ),
					'manual_commission_log_actions' => '<span class="dashicons dashicons-edit"></span><span class="dashicons dashicons-no"></span>',
					'i18n'                          => [
						'decimal'        => '',
						'emptyTable'     => __( 'No data available in table', 'wdm_instructor_role' ),
						'info'           => __( 'Showing _START_ to _END_ of _TOTAL_ entries', 'wdm_instructor_role' ),
						'infoEmpty'      => __( 'Showing 0 to 0 of 0 entries', 'wdm_instructor_role' ),
						'infoFiltered'   => __( '(filtered from _MAX_ total entries)', 'wdm_instructor_role' ),
						'infoPostFix'    => '',
						'thousands'      => ',',
						'lengthMenu'     => __( 'Show _MENU_ entries', 'wdm_instructor_role' ),
						'loadingRecords' => __( 'Loading...', 'wdm_instructor_role' ),
						'processing'     => '',
						'search'         => __( 'Search :', 'wdm_instructor_role' ),
						'zeroRecords'    => __( 'No matching records found', 'wdm_instructor_role' ),
						'paginate'       => [
							'first'    => __( 'First', 'wdm_instructor_role' ),
							'last'     => __( 'Last', 'wdm_instructor_role' ),
							'next'     => __( 'Next', 'wdm_instructor_role' ),
							'previous' => __( 'Previous', 'wdm_instructor_role' ),
						],
						'aria'           => [
							'sortAscending'  => __( ': activate to sort column ascending', 'wdm_instructor_role' ),
							'sortDescending' => __( ': activate to sort column descending', 'wdm_instructor_role' ),
						],
					],
				]
			);
		}

		/**
		 * Ajax handle paypal payout transaction
		 *
		 * @since 3.4.0
		 */
		public function ajax_ir_payout_transaction() {
			if ( empty( $_POST ) || ! array_key_exists( 'action', $_POST ) || 'ir_payout_transaction' != $_POST['action'] ) {
				wp_die();
			}

			// Verify nonce.
			if ( ! array_key_exists( 'ir_nonce', $_POST ) || ! wp_verify_nonce( $_POST['ir_nonce'], 'ir_commission_paypal_payout_payment' ) ) {
				wp_die();
			}

			// Check if admin.
			if ( ! is_super_admin() ) {
				wp_die();
			}

			// Check if instructor.
			$instructor_id = filter_input( INPUT_POST, 'instructor_id', FILTER_SANITIZE_NUMBER_INT );
			if ( ( '' == $instructor_id ) || ( ! wdm_is_instructor( $instructor_id ) ) ) {
				wp_send_json( [ 'error' => __( 'Oops something went wrong', 'wdm_instructor_role' ) ] );
			}

			// Check if payout configuration set.
			if ( ! $this->is_paypal_payout_configured( $instructor_id ) ) {
				wp_send_json(
					[
						'error' => __(
							'Please configure paypal payout settings and/or instructor paypal email',
							'wdm_instructor_role'
						),
					]
				);
			}

			// Extract transaction details - handle both comma and dot decimal separators.
			$paid_earnings      = Cast::to_float( Number::sanitize_decimal( filter_input( INPUT_POST, 'total_paid', FILTER_UNSAFE_RAW ) ) );
			$unpaid_earnings    = Cast::to_float( Number::sanitize_decimal( filter_input( INPUT_POST, 'amount_tobe_paid', FILTER_UNSAFE_RAW ) ) ); // cspell:disable-line .
			$transaction_amount = Cast::to_float( Number::sanitize_decimal( filter_input( INPUT_POST, 'enter_amount', FILTER_UNSAFE_RAW ) ) );

			$instructor_earnings = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );
			$instructor_earnings = empty( $instructor_earnings ) ? 0 : $instructor_earnings;

			// Verify transaction details.
			if ( empty( $unpaid_earnings ) || empty( $transaction_amount ) || $paid_earnings != $instructor_earnings || $transaction_amount > $unpaid_earnings ) {
				wp_send_json( [ 'error' => __( 'Oops something went wrong', 'wdm_instructor_role' ) ] );
			}

			// Verify commission amount details.
			global $wpdb;
			$table = $wpdb->prefix . 'wdm_instructor_commission';
			$sql   = $wpdb->prepare( "SELECT commission_price FROM $table WHERE user_id = %d", $instructor_id );

			$results = $wpdb->get_col( $sql );
			if ( empty( $results ) ) {
				wp_send_json(
					[
						'error' => __( 'Oops something went wrong', 'wdm_instructor_role' ),
					]
				);
			} else {
				$total_earnings = 0;
				foreach ( $results as $value ) {
					$total_earnings += $value;
				}
				$calculated_unpaid_earnings = round( ( $total_earnings - $paid_earnings ), 2 );
				if ( $calculated_unpaid_earnings != $unpaid_earnings ) {
					wp_send_json( [ 'error' => __( 'Oops something went wrong', 'wdm_instructor_role' ) ] );
				}
			}

			/**
			 * Action before creating paypal payout transaction
			 *
			 * @var int     $instructor_id      User ID of the instructor.
			 * @var float   $transaction_amount Amount of the transaction.
			 * @since 3.4.0
			 */
			do_action( 'ir_action_before_create_payout_transaction', $instructor_id, $transaction_amount );

			$request_response = $this->create_payout_transaction( $instructor_id, $transaction_amount );
			// Check for errors.
			if ( $request_response->error ) {
				$details = isset( $request_response->details ) ? $request_response->details[0]->issue : '';
				wp_send_json(
					[
						'error' => sprintf(
							// translators: Error type and details.
							__( ' Error Type: %1$s - %2$s', 'wdm_instructor_role' ),
							$request_response->name,
							$details
						),
					]
				);
			}

			$batch_id = $request_response->result->batch_header->payout_batch_id;

			// Get complete payout request.
			$payout_transaction = $this->get_payout_request_details( $batch_id );

			/**
			 * Action after creating paypal payout transaction
			 *
			 * @var int     $instructor_id      User ID of the instructor.
			 * @var float   $transaction_amount Amount of the transaction.
			 * @var object  $payout_transaction Payout transaction response object.
			 * @since 3.4.0
			 */
			do_action( 'ir_action_after_create_payout_transaction', $instructor_id, $transaction_amount, $payout_transaction );

			$status_code        = $payout_transaction->statusCode;
			$payout_status      = $payout_transaction->result->batch_header->batch_status;
			$transaction_status = $payout_transaction->result->items[0]->transaction_status;

			// Add transaction to database.
			$this->add_payout_transaction_to_db( $instructor_id, $payout_status, $transaction_status, $batch_id, $transaction_amount );

			// Check if failed request.
			if ( ( $status_code % 200 ) > 100 || 'DENIED' == $payout_status || 'CANCELED' == $payout_status ) {
				wp_send_json(
					[
						'error' => __( $payout_transaction->result->items[0]->errors->message, 'wdm_instructor_role' ),
					]
				);
			}

			// Check if sandbox enabled.
			$payout_test_environment = get_option( 'ir_payout_test_environment' );
			$updated_paid_earnings   = round( ( $paid_earnings + $transaction_amount ), 2 );

			// Update commission details only for production.
			if ( 'on' != $payout_test_environment ) {
				$this->update_instructor_commissions( $instructor_id, $payout_transaction );
			}

			/**
			 * Action after commission amount paid to instructor
			 * - Fired for both live and sandbox transactions
			 *
			 * @var int $instructor_id              User ID of the instructor
			 * @var float $transaction_amount       Amount entered by admin to pay
			 * @var float $paid_earnings            The total amount paid by admin to instructor before
			 *                                      current transaction
			 * @var float $unpaid_earnings          The amount required to be paid by admin
			 * @var float $updated_paid_earnings    The total amount paid to instructor after current transaction
			 */
			do_action(
				'wdm_commission_amount_paid',
				$instructor_id,
				$transaction_amount,
				$paid_earnings,
				$unpaid_earnings,
				$updated_paid_earnings
			);

			$unpaid_earnings = round( ( $unpaid_earnings - $transaction_amount ), 2 );

			$data = [
				'unpaid_earnings' => $unpaid_earnings,
				'paid_earnings'   => $updated_paid_earnings,
			];

			wp_send_json(
				[
					'success' => $data,
				]
			);
		}

		/**
		 * Create a new paypal payout transaction
		 *
		 * @param int   $instructor_id        ID of the instructor
		 * @param float $transaction_amount   Amount of the transaction
		 *
		 * @return object
		 * @since 3.4.0
		 */
		protected function create_payout_transaction( $instructor_id, $transaction_amount ) {
			$payout_request       = new PayoutsPostRequest();
			$payout_request->body = $this->build_payout_request_body( $instructor_id, $transaction_amount );
			$payout_client        = $this->get_payout_client();
			try {
				$response = $payout_client->execute( $payout_request );
			} catch ( Exception $exception ) {
				$response        = json_decode( $exception->getMessage() );
				$response->error = 1;
			}
			return $response;
		}

		/**
		 * Build the payout request body
		 *
		 * @param int $instructor_id        User ID of the instructor
		 * @param int $transaction_amount   Amount of the payout transaction
		 *
		 * @return array                    Request body for the payout transaction
		 *
		 * @since 3.4.0
		 */
		protected function build_payout_request_body( $instructor_id, $transaction_amount ) {
			$instructor_paypal_email = get_user_meta( $instructor_id, 'ir_paypal_payouts_email', true );
			$payout_note             = get_option( 'ir_payout_pay_note' );

			// If payout note empty, set a default note.
			if ( empty( $payout_note ) ) {
				$payout_note = __( 'Your instructor commission payout', 'wdm_instructor_role' );
			}
			$payout_currency = get_option( 'ir_payout_currency' );
			$sender_item_id  = 'ir_' . $instructor_id . '_' . time();

			/**
			 * Filter instructor payout email subject
			 *
			 * @var string    $payout_email_subject   Subject of the instructor payout email.
			 * @var int       $instructor_id          ID of the instructor to send payout.
			 *
			 * @since 3.4
			 */
			$payout_email_subject = apply_filters(
				'ir_filter_payout_email_subject',
				// translators: Blog name.
				sprintf( __( '%s : Instructor Paypal Payout', 'wdm_instructor_role' ), get_bloginfo( 'name' ) ),
				$instructor_id
			);

			$request_body = [
				'sender_batch_header' => [
					'email_subject' => $payout_email_subject,
				],
				'items'               => [
					[
						'recipient_type' => 'EMAIL',
						'receiver'       => $instructor_paypal_email,
						'note'           => $payout_note,
						'sender_item_id' => $sender_item_id,
						'amount'         => [
							'currency' => $payout_currency,
							'value'    => $transaction_amount,
						],
					],
				],
			];

			/**
			 * Filter paypal payout request body before the transaction
			 *
			 * @var array $request_body     Payout request body.
			 * @var int   $instructor_id    ID of the instructor.
			 *
			 * @since 3.4
			 */
			$request_body = apply_filters(
				'ir_filter_payout_request_body',
				$request_body,
				$instructor_id
			);

			return $request_body;
		}

		/**
		 * Get the paypal client object with respect to the environment set( i.e. Sandbox or Production)
		 *
		 * @return object   Paypal Client object
		 *
		 * @since 3.4.0
		 */
		protected function get_payout_client() {
			// Get environment variable.
			$payout_test_environment = get_option( 'ir_payout_test_environment' );

			if ( 'on' == $payout_test_environment ) {
				// If sandbox set environment to sandbox.
				$client_id          = get_option( 'ir_payout_sandbox_client_id' );
				$client_secret      = get_option( 'ir_payout_sandbox_client_secret_key' );
				$payout_environment = new SandboxEnvironment( $client_id, $client_secret );
			} else {
				// If sandbox not set environment to production.
				$client_id          = get_option( 'ir_payout_client_id' );
				$client_secret      = get_option( 'ir_payout_client_secret_key' );
				$payout_environment = new ProductionEnvironment( $client_id, $client_secret );
			}

			return new PayPalHttpClient( $payout_environment );
		}

		/**
		 * Check if paypal payout and instructor paypal settings configured
		 *
		 * @param int $instructor_id    User ID of the instructor.
		 * @return bool                 True if configurations set, False otherwise.
		 *
		 * @since 3.4.0
		 */
		public function is_paypal_payout_configured( $instructor_id ) {
			$instructor_paypal_email = get_user_meta( $instructor_id, 'ir_paypal_payouts_email', true );

			// Check if instructor paypal email set.
			if ( empty( $instructor_paypal_email ) ) {
				return false;
			}

			// Check if currency set.
			$payout_currency = get_option( 'ir_payout_currency' );
			if ( empty( $payout_currency ) ) {
				return false;
			}

			// Check if paypal settings configured.
			$payout_test_environment = get_option( 'ir_payout_test_environment' );

			if ( 'on' == $payout_test_environment ) {
				// If sandbox check sandbox variables.
				$client_id     = get_option( 'ir_payout_sandbox_client_id' );
				$client_secret = get_option( 'ir_payout_sandbox_client_secret_key' );
			} else {
				// If sandbox not check production variables.
				$client_id     = get_option( 'ir_payout_client_id' );
				$client_secret = get_option( 'ir_payout_client_secret_key' );
			}

			// If either client id or secret not set then return.
			if ( empty( $client_id ) || empty( $client_secret ) ) {
				return false;
			}

			/**
			 * Filter paypal configuration check.
			 *
			 * @var bool        True if paypal configuration set, False otherwise.
			 *
			 * @since 3.4.0
			 */
			return apply_filters( 'ir_filter_check_paypal_configuration', true );
		}

		/**
		 * Record payouts transaction to database
		 *
		 * @param int    $instructor_id            User ID of the instructor
		 * @param string $payout_status         Payout status
		 * @param string $transaction_status    Payout item transaction status
		 * @param string $batch_id              Payout Batch ID
		 * @param float  $transaction_amount     Transaction Amount
		 *
		 * @return int                          The ID of the inserted row, or false on error.
		 *
		 * @since 3.4.0
		 */
		public function add_payout_transaction_to_db( $instructor_id, $payout_status, $transaction_status, $batch_id, $transaction_amount ) {
			global $wpdb;

			$payout_test_environment = get_option( 'ir_payout_test_environment' );
			if ( 'on' == $payout_test_environment ) {
				$transaction_type = 'SANDBOX';
			} else {
				$transaction_type = 'LIVE';
			}

			$table_name = $this->payouts_table_name;

			// Record payouts entry in database.
			$insert_status = $wpdb->insert(
				$table_name,
				[
					'user_id'            => $instructor_id,
					'batch_id'           => $batch_id,
					'status'             => $payout_status,
					'transaction_status' => $transaction_status,
					'amount'             => $transaction_amount,
					'type'               => $transaction_type,
				],
				[
					'%d',
					'%s',
					'%s',
					'%s',
					'%f',
					'%s',
				]
			);

			// If successful insert return inserted row id.
			if ( $insert_status ) {
				return $wpdb->insert_id;
			}

			return $insert_status;
		}

		/**
		 * Get paypal payout request details
		 *
		 * @param string $batch_id      Batch ID of the payout transaction.
		 * @return object               Payout request details.
		 *
		 * @since 3.4.0
		 */
		public function get_payout_request_details( $batch_id ) {
			$payout_get_request = new PayoutsGetRequest( $batch_id );
			$payout_client      = $this->get_payout_client();
			try {
				$response = $payout_client->execute( $payout_get_request );
			} catch ( Exception $exception ) {
				$response        = json_decode( $exception->getMessage() );
				$response->error = 1;
			}
			return $response;
		}

		/**
		 * Add paypal transactions report
		 *
		 * @param int $instructor_id
		 * @since 3.4.0
		 */
		public function add_paypal_transactions_report( $instructor_id ) {
			global $wpdb;
			$table_name = $this->payouts_table_name;

			$sql = $wpdb->prepare(
				"SELECT batch_id, status, amount, type FROM $table_name WHERE user_id = %d ORDER BY id DESC",
				$instructor_id
			);

			$payout_transactions = $wpdb->get_results( $sql, ARRAY_A );

			// Fetch the payouts transactions report for the instructor.
			ir_get_template(
				INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/payouts/ir-paypal-payouts-transactions.template.php',
				[
					'payout_transactions' => $payout_transactions,
				]
			);
		}

		/**
		 * Fetch payout transaction details via ajax
		 *
		 * @since 3.4.0
		 */
		public function ajax_fetch_payout_transaction_details() {
			if ( ! empty( $_POST ) && array_key_exists( 'action', $_POST ) && 'ir-get-payout-transaction-details' == $_POST['action'] ) {
				// Verify nonce.
				if ( ! array_key_exists( 'ir_nonce', $_POST ) || ! wp_verify_nonce( $_POST['ir_nonce'], 'ir_fetch_payout_transaction_details' ) ) {
					wp_send_json(
						[
							'type' => 'success',
							'html' => __( 'Oops, some error occurred, please try again after some time', 'wdm_instructor_role' ),
						]
					);
				}

				$batch_id       = sanitize_text_field( wp_unslash( $_POST['batch_id'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- Check it later.
				$payout_details = $this->get_payout_request_details( $batch_id );

				$status = $this->payout_status_codes[ $payout_details->statusCode ];

				$time_created       = ir_get_date_in_site_timezone(
					$payout_details->result->batch_header->time_created
				);
				$transaction_status = $payout_details->result->items[0]->transaction_status;
				$errors             = $payout_details->result->items[0]->errors;
				$amount             = [
					'value'    => $payout_details->result->batch_header->amount->value,
					'currency' => $payout_details->result->batch_header->amount->currency,
				];

				$html = ir_get_template(
					INSTRUCTOR_ROLE_ABSPATH . '/modules/templates/payouts/ir-paypal-payout-transaction-details.template.php',
					[
						'status'             => $status,
						'time_created'       => $time_created,
						'transaction_status' => $transaction_status,
						'errors'             => $errors,
						'amount'             => $amount,
					],
					1
				);

				wp_send_json(
					[
						'type' => 'success',
						'html' => $html,
					]
				);
			}
			wp_die();
		}

		/**
		 * Update instructor commissions after payout and setup cron
		 *
		 * @param int    $instructor_id         User ID of the instructor.
		 * @param object $payout_transaction    Payout transaction response object.
		 * @param string $previous_status       Previous transaction status, empty string if not set.
		 * @since 3.4.0
		 */
		public function update_instructor_commissions( $instructor_id, $payout_transaction, $previous_status = '' ) {
			$transaction_status = $payout_transaction->result->items[0]->transaction_status;

			switch ( $transaction_status ) {
				// If successful transaction.
				case 'SUCCESS':
					$instructor_earnings   = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );
					$transaction_amount    = $payout_transaction->result->items[0]->payout_item->amount->value;
					$updated_paid_earnings = round( ( $instructor_earnings + $transaction_amount ), 2 );
					$batch_id              = $payout_transaction->result->batch_header->payout_batch_id;

					// Remove any crons set for this payout.
					if ( wp_next_scheduled( 'ir_process_payout_' . $batch_id ) ) {
						wp_unschedule_event(
							wp_next_scheduled( 'ir_process_payout_' . $batch_id ),
							'ir_process_payout_' . $batch_id
						);
					}

					/**
					 * Fires after a successful commission payout
					 *
					 * @since 3.4.0
					 *
					 * @param int    $instructor_id     User ID of the instructor.
					 * @param string $batch_id          Unique batch ID of the payout transaction.
					 */
					do_action( 'ir_action_after_successful_payout', $instructor_id, $batch_id );

					// Update commissions meta if not already updated.
					if ( empty( $previous_status ) ) {
						update_user_meta( $instructor_id, 'wdm_total_amount_paid', $updated_paid_earnings );
					}
					break;

				// If pending or unclaimed payout.
				case 'PENDING':
				case 'UNCLAIMED':
					$batch_id              = $payout_transaction->result->batch_header->payout_batch_id;
					$instructor_earnings   = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );
					$transaction_amount    = $payout_transaction->result->items[0]->payout_item->amount->value;
					$updated_paid_earnings = round( ( $instructor_earnings + $transaction_amount ), 2 );

					// Setup daily cron for rechecking in future.
					if ( ! wp_next_scheduled( 'ir_process_payout_' . $batch_id ) ) {
						wp_schedule_event(
							strtotime( '+1 day' ),
							'daily',
							'ir_process_payout_' . $batch_id,
							[
								$instructor_id,
								$payout_transaction,
								$transaction_status,
							]
						);
					}

					/**
					 * Fires after a pending or unclaimed commission payout
					 *
					 * @param int    $instructor_id     User ID of the instructor.
					 * @param string $batch_id          Unique batch ID of the payout transaction.
					 */
					do_action( 'ir_action_after_pending_unclaimed_payout', $instructor_id, $payout_transaction->result->batch_header->payout_batch_id );

					// Update commissions meta if not updated already.
					if ( empty( $previous_status ) ) {
						update_user_meta( $instructor_id, 'wdm_total_amount_paid', $updated_paid_earnings );
					}
					break;

				// If transaction failed or blocked.
				case 'BLOCKED':
				case 'FAILED':
					$batch_id = $payout_transaction->result->batch_header->payout_batch_id;
					// Remove any crons set for this payout.
					if ( wp_next_scheduled( 'ir_process_payout_' . $batch_id ) ) {
						wp_unschedule_event(
							wp_next_scheduled( 'ir_process_payout_' . $batch_id ),
							'ir_process_payout_' . $batch_id
						);
					}
					/**
					 * Fires after a failed or blocked commission payout
					 *
					 * @param int    $instructor_id     User ID of the instructor.
					 * @param string $batch_id          Unique batch ID of the payout transaction.
					 */
					do_action( 'ir_action_after_failed_blocked_payout', $instructor_id, $batch_id );
					break;

				// If returned, refunded or reversed transaction.
				case 'RETURNED':
				case 'REFUNDED':
				case 'REVERSED':
					$batch_id = $payout_transaction->result->batch_header->payout_batch_id;
					// Remove any crons set for this payout.
					if ( wp_next_scheduled( 'ir_process_payout_' . $batch_id ) ) {
						wp_unschedule_event(
							wp_next_scheduled( 'ir_process_payout_' . $batch_id ),
							'ir_process_payout_' . $batch_id
						);
					}

					// Revert instructor commission updates.
					$instructor_earnings = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );
					$transaction_amount  = $payout_transaction->result->items[0]->payout_item->amount->value;

					$updated_paid_earnings = round( ( $instructor_earnings - $transaction_amount ), 2 );

					/**
					 * Fires after a successful reverting, refunding or reversing a commission payout
					 *
					 * @param int    $instructor_id     User ID of the instructor.
					 * @param string $batch_id          Unique batch ID of the payout transaction.
					 */
					do_action( 'ir_action_after_revert_refund_reverse_payout', $instructor_id, $payout_transaction->result->batch_header->payout_batch_id );

					// Update commissions meta.
					update_user_meta( $instructor_id, 'wdm_total_amount_paid', $updated_paid_earnings );
					break;

				default:
					/**
					 * Fires for any other status for a commission payout
					 *
					 * @param int    $instructor_id     User ID of the instructor.
					 * @param string $batch_id          Unique batch ID of the payout transaction.
					 */
					do_action( 'ir_action_after_other_payout', $instructor_id, $payout_transaction->result->batch_header->payout_batch_id );
					break;
			}
		}

		/**
		 * Process all the scheduled cron events for all payouts
		 *
		 * @since 3.4.0
		 */
		public function process_scheduled_payout_transactions() {
			global $wpdb;
			$payouts_table = $this->payouts_table_name;

			// Get all schedule payout batch ids.
			$sql = "SELECT batch_id FROM $payouts_table WHERE transaction_status IN ( 'PENDING', 'UNCLAIMED' )";

			$batch_ids = $wpdb->get_col( $sql );

			// If no scheduled batch ids found return.
			if ( empty( $batch_ids ) ) {
				return;
			}

			// Process the scheduled payouts and accordingly update instructor commissions.
			foreach ( $batch_ids as $batch_id ) {
				add_action(
					'ir_process_payout_' . $batch_id,
					[ $this, 'update_instructor_commissions', 10, 3 ]
				);
			}
		}
	}
}
