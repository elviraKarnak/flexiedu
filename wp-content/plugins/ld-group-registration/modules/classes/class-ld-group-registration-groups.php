<?php
/**
 * Groups Module
 *
 * @since 4.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/modules/classes
 *
 * cspell:ignore rmvl
 */

namespace LdGroupRegistration\Modules\Classes;

use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Sub_Groups;
use LearnDash\Core\Utilities\Cast;
use WP_User;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ld_Group_Registration_Groups' ) ) {
	/**
	 * Class LD Group Registration Groups
	 */
	class Ld_Group_Registration_Groups {
		/**
		 * Class Instance
		 *
		 * @since 4.3.3
		 *
		 * @var Ld_Group_Registration_Groups|null
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @since 4.3.3
		 *
		 * @return Ld_Group_Registration_Groups
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Trigger group enrollment emails.
		 *
		 * @since 4.3.15
		 *
		 * @param int                       $user_id        The user ID.
		 * @param int                       $group_id       The group ID.
		 * @param array<int|string, string> $final_csv_data The final CSV data.
		 *
		 * @return void
		 */
		public function trigger_group_enrollment_emails( $user_id, $group_id, $final_csv_data = [] ) {
			// Fetch enable/disable email setting.
			$email_enabled_option = get_option( 'wdm_u_add_gr_enable' );

			/**
			 * Filter to determine if the group enrollment email should be sent.
			 *
			 * @param bool $should_send_email Whether to send the email.
			 * @param int  $group_id          The group ID.
			 *
			 * @return bool
			 */
			$should_send_email = apply_filters( 'wdm_group_enrollment_email_status', true, $group_id );

			// If email is disabled, return.
			if (
				! $should_send_email
				|| 'off' === $email_enabled_option
			) {
				return;
			}

			// Populate the email variables.
			$member_user     = new WP_User( $user_id );
			$courses         = learndash_group_enrolled_courses( $group_id );
			$lead_user       = new WP_User( get_current_user_id() );
			$enrolled_course = [];
			$emails_list     = [];
			$url             = ''; // Initialize $url variable to prevent "might not be defined" errors.

			if ( ! empty( $courses ) ) {
				foreach ( $courses as $key => $value ) {
					$enrolled_course[] = get_the_title( $value );
					$url               = get_permalink( $value );
					unset( $key );
				}
			}

			// Fetch the subject.
			$t_sub = Cast::to_string( get_option( 'wdm-u-add-gr-sub' ) );
			if ( empty( $t_sub ) ) {
				$t_sub = defined( 'WDM_U_ADD_GR_SUB' ) ? WDM_U_ADD_GR_SUB : 'User added to group {group_title}';
			}
			$subject = stripslashes( $t_sub );
			$subject = str_replace( '{group_title}', get_the_title( $group_id ), $subject );
			$subject = str_replace( '{group_leader_name}', ucfirst( strtolower( $lead_user->first_name ) ) . ' ' . ucfirst( strtolower( $lead_user->last_name ) ), $subject );
			$subject = str_replace( '{user_first_name}', ucfirst( strtolower( $member_user->first_name ) ), $subject );
			$subject = str_replace( '{user_last_name}', ucfirst( strtolower( $member_user->last_name ) ), $subject );
			$subject = str_replace( '{login_url}', wp_login_url( (string) $url ), $subject );

			// Fetch the body.
			$tbody = Cast::to_string( get_option( 'wdm-u-add-gr-body' ) );
			if ( empty( $tbody ) ) {
				$tbody = defined( 'WDM_U_ADD_GR_BODY' ) ? WDM_U_ADD_GR_BODY : 'Hello {user_first_name}, you have been added to {group_title} by {group_leader_name}. Courses: {course_list}';
			}
			$body = stripslashes( $tbody );
			$body = str_replace( '{group_title}', get_the_title( $group_id ), $body );
			$body = str_replace( '{course_list}', $this->get_course_list_html( $enrolled_course, $group_id, $member_user->ID ), $body );
			$body = str_replace( '{group_leader_name}', ucfirst( strtolower( $lead_user->first_name ) ) . ' ' . ucfirst( strtolower( $lead_user->last_name ) ), $body );
			$body = str_replace( '{user_first_name}', ucfirst( strtolower( $member_user->first_name ) ), $body );
			$body = str_replace( '{user_last_name}', ucfirst( strtolower( $member_user->last_name ) ), $body );
			$body = str_replace( '{login_url}', wp_login_url( (string) $url ), $body );

			// Send user enrollment email.
			$emails_list[ $user_id ] = [
				'email'   => trim( $member_user->user_email ),
				'subject' => $subject,
				'body'    => $body,
				'new'     => false,
			];
			$this->send_bulk_upload_emails( $emails_list, $group_id, $final_csv_data );
		}

		/**
		 * Enqueue Data tables scripts and styles
		 */
		public static function enqueue_data_table() {
			wp_register_script(
				'wdm_datatable_js',
				plugins_url( 'js/datatable.js', __DIR__ ),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);

			$dt_data = [
				'previous'                             => __( 'Previous', 'wdm_ld_group' ),
				'first'                                => __( 'First', 'wdm_ld_group' ),
				'last'                                 => __( 'Last', 'wdm_ld_group' ),
				'next'                                 => __( 'Next', 'wdm_ld_group' ),
				'no_data_available_in_table'           => __( 'No data available in table', 'wdm_ld_group' ),
				'no_matching_records_found'            => __( 'No matching records found', 'wdm_ld_group' ),
				'search_colon'                         => __( 'Search:', 'wdm_ld_group' ),
				'processing_dot_dot_dot'               => __( 'Processing...', 'wdm_ld_group' ),
				'loading_dot_dot_dot'                  => __( 'Loading...', 'wdm_ld_group' ),
				'show__menu__entries'                  => sprintf(
					// translators: For Showing entries in menu.
					__( 'Show %s entries', 'wdm_ld_group' ),
					'_MENU_'
				),
				'showing_zero_to_zero_of_zero_entries' => __( 'Showing 0 to 0 of 0 entries', 'wdm_ld_group' ),
				'filtered_from__max__tot_entries'      => sprintf(
					/* translators: For Showing maximum number of entries. */
					__( '(filtered from %s total entries)', 'wdm_ld_group' ),
					'_MAX_'
				),
				'showing__start__to__end__of__total__entries' => sprintf(
					/* translators: For Showing from - to number of entries in pagination. */
					__( 'Showing %1$s to %2$s of %3$s entries', 'wdm_ld_group' ),
					'_START_',
					' _END_',
					'_TOTAL_'
				),
				's_sort_descending'                    => __( ': activate to sort column descending', 'wdm_ld_group' ),
				's_sort_ascending'                     => __( ': activate to sort column ascending', 'wdm_ld_group' ),
			];
			wp_localize_script( 'wdm_datatable_js', 'wdm_datatable', $dt_data );

			wp_enqueue_script( 'wdm_datatable_js' );
		}

		/**
		 * Group removal request reject ajax
		 */
		public function handle_reject_request() {
			$user_id  = filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT );
			$group_id = filter_input( INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT );

			echo wp_json_encode( $this->ldgr_group_request_process( 'reject', $user_id, $group_id ) );
			die();
		}

		/**
		 * Group removal request processing
		 *
		 * @param int $user_id   ID of the user.
		 * @param int $group_id  ID of the group.
		 */
		public function ldgr_group_request_process_check( $user_id, $group_id ) {
			if ( ! is_user_logged_in() ) {
				echo json_encode( [ 'error' => __( 'Please login to perform action', 'wdm_ld_group' ) ] );
				die();
			}
			if ( ! is_super_admin() ) {
				echo json_encode( [ 'error' => __( 'You are not the authorized user to perform this action', 'wdm_ld_group' ) ] );
				die();
			}
			if ( '' == $user_id || '' == $group_id ) {
				echo json_encode( [ 'error' => __( 'Oops, something went wrong', 'wdm_ld_group' ) ] );
				die();
			}
		}

		/**
		 * Process Group Request
		 *
		 * @param string $action       Action to perform on the request.
		 * @param int    $user_id      ID of the user.
		 * @param int    $group_id     ID of the group.
		 *
		 * @return array            Request response.
		 */
		public function ldgr_group_request_process( $action, $user_id, $group_id ) {
			$this->ldgr_group_request_process_check( $user_id, $group_id );

			$removal_request = maybe_unserialize( get_post_meta( $group_id, 'removal_request', true ) );
			if ( empty( $removal_request ) ) {
				return [ 'error' => __( 'No request found', 'wdm_ld_group' ) ];
			}
			if ( ( $key = array_search( $user_id, $removal_request ) ) !== false ) {
				unset( $removal_request[ $key ] );
			}
			if ( empty( $removal_request ) ) {
				delete_post_meta( $group_id, 'removal_request', null );
			} else {
				update_post_meta( $group_id, 'removal_request', $removal_request );
			}
			if ( 'accept' === $action ) {
				$remove_user = $this->ldgr_remove_user_from_group( $user_id, $group_id );
				$group_limit = get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true );
				$total_limit = get_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, true );

				if ( $remove_user ) {
					return [
						'success'     => __( 'Request accepted successfully', 'wdm_ld_group' ),
						'group_limit' => $group_limit,
						'total_limit' => $total_limit,
					];
				}
			} elseif ( 'reject' == $action ) {
				$admin_group_ids       = learndash_get_groups_administrator_ids( $group_id );
				$wdm_gr_gl_acpt_enable = get_option( 'wdm_gr_gl_acpt_enable' );
				if ( ! empty( $admin_group_ids ) && 'off' != $wdm_gr_gl_acpt_enable ) {
					$user_data = get_user_by( 'id', $user_id );
					foreach ( $admin_group_ids as $key => $value ) {
						if ( apply_filters( 'wdm_removal_request_reject_email_status', true, $group_id ) ) {
							$leader_data = get_user_by( 'id', $value );
							$subject     = __( 'User Removal request from group', 'wdm_ld_group' );

							$subject = get_option( 'wdm-gr-gl-acpt-sub' );
							if ( empty( $subject ) ) {
								$subject = WDM_GR_GL_ACPT_SUB;
							}
							$subject = stripslashes( $subject );
							$subject = str_replace( '{group_title}', get_the_title( $group_id ), $subject );
							$subject = str_replace( '{user_email}', $user_data->user_email, $subject );
							$subject = str_replace( '{group_leader_name}', ucfirst( strtolower( $leader_data->first_name ) ) . ' ' . ucfirst( strtolower( $leader_data->last_name ) ), $subject );
							$subject = apply_filters( 'wdm_removal_request_reject_subject', $subject, $group_id, $user_id, $value );

							$body = get_option( 'wdm-gr-gl-acpt-body' );
							if ( empty( $body ) ) {
								$body = WDM_GR_GL_ACPT_BODY;
							}
							$body = stripslashes( $body );

							$body = str_replace( '{group_title}', get_the_title( $group_id ), $body );
							$body = str_replace( '{user_email}', $user_data->user_email, $body );
							$body = str_replace( '{group_leader_name}', ucfirst( strtolower( $leader_data->first_name ) ) . ' ' . ucfirst( strtolower( $leader_data->last_name ) ), $body );
							$body = apply_filters( 'wdm_removal_request_reject_body', $body, $group_id, $user_id, $value );

							ldgr_send_group_mails(
								$leader_data->user_email,
								$subject,
								$body,
								[],
								[],
								[
									'email_type' => 'WDM_GR_GL_ACPT_BODY',
									'group_id'   => $group_id,
								]
							);
						}
					}
				}
				return [ 'success' => __( 'Request rejected successfully', 'wdm_ld_group' ) ];
			}
		}

		/**
		 * Group removal request accept ajax
		 */
		public function handle_accept_request() {
			$user_id  = filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT );
			$group_id = filter_input( INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT );
			echo wp_json_encode( $this->ldgr_group_request_process( 'accept', $user_id, $group_id ) );
			die();
		}

		/**
		 * Bulk group removal request accept ajax
		 */
		public function handle_bulk_accept_request() {
			$user_ids = filter_input( INPUT_POST, 'user_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$group_id = filter_input( INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT );

			$response = [];

			foreach ( $user_ids as $key => $user_id ) {
				$response[ $user_id ] = $this->ldgr_group_request_process( 'accept', $user_id, $group_id );
			}

			echo wp_json_encode( $response );
			die();
		}

		/**
		 * Bulk group removal request reject ajax
		 */
		public function handle_bulk_reject_request() {
			$user_ids = filter_input( INPUT_POST, 'user_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$group_id = filter_input( INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT );

			foreach ( $user_ids as $key => $user_id ) {
				$response[ $user_id ] = $this->ldgr_group_request_process( 'reject', $user_id, $group_id );
			}

			echo wp_json_encode( $response );
			die();
		}

		/**
		 * Save post type group
		 * - Updating registration left count for each leader
		 *
		 * @param int    $post_id   ID of the post.
		 * @param object $post      The WP Post object.
		 * @param bool   $update    Whether it is a new post or existing post.
		 */
		public function handle_registrations_left_save( $post_id, $post, $update ) {
			if ( ! isset( $_POST['wdm_ld_group'] ) || ! wp_verify_nonce( filter_input( INPUT_POST, 'wdm_ld_group' ), 'wdm_ld_group_value' ) ) {
				return;
			}

			// Group users list.
			$group_users = learndash_get_groups_user_ids( $post_id );

			// Get original data.
			$original_seats_left  = get_post_meta( $post_id, 'wdm_group_users_limit_' . $post_id, true );
			$original_total_seats = get_post_meta( $post_id, 'wdm_group_total_users_limit_' . $post_id, true );

			// Get updated data.
			$seats_left  = (int) filter_input( INPUT_POST, 'ldgr_seats_left', FILTER_SANITIZE_NUMBER_INT );
			$total_seats = (int) filter_input( INPUT_POST, 'ldgr_total_seats', FILTER_SANITIZE_NUMBER_INT );

			// Set seats left to total seats for newly created groups.
			if ( ! $update || ! metadata_exists( 'post', $post_id, 'wdm_group_users_limit_' . $post_id ) ) {
				$seats_left           = $total_seats;
				$original_seats_left  = 0;
				$original_total_seats = 0;
			}

			$enrolled_users_count = count( $group_users );

			// Calculate seats left using enrolled users in group.
			$seats_left = $total_seats - $enrolled_users_count;

			// If group users more than total, update total and set seats left to 0.
			if ( $enrolled_users_count > $total_seats ) {
				$total_seats = $enrolled_users_count;
				$seats_left  = 0;
			}

			$difference = 0;

			// 1. Increased seats.
			if ( $total_seats > $original_total_seats ) {
				$difference = $total_seats - $original_total_seats;
				// Add difference to seats left.
				$seats_left += $difference;
			}

			// 2. Decreased seats.
			if ( $total_seats < $original_total_seats ) {
				$difference = $original_total_seats - $total_seats;
				if ( $difference > $original_seats_left ) {
					// If not valid seats left, return.
					// @todo if possible show errors.
					return;
				}
				// Add difference to seats left.
				$seats_left -= $difference;
			}

			// Check for unlimited seats group.
			$is_unlimited = get_post_meta( $post_id, 'ldgr_unlimited_seats', 1 );

			// Update seats left.
			if ( ! $is_unlimited ) {
				update_post_meta( $post_id, 'wdm_group_users_limit_' . $post_id, $seats_left );
				update_post_meta( $post_id, 'wdm_group_total_users_limit_' . $post_id, $total_seats );
				ldgr_recalculate_group_seats( $post_id );
			}
		}

		/**
		 * Adding group registration left count meta box
		 */
		public function add_groups_metaboxes() {
			$screens = [ 'groups' ];

			foreach ( $screens as $screen ) {
				add_meta_box(
					'wdm_ld_group',
					__( 'Group Registrations left', 'wdm_ld_group' ),
					[ $this, 'ldgr_registrations_left_callback' ],
					$screen
				);
			}
		}

		/**
		 * Display group registrations left metabox
		 *
		 * @param obj $post  Object of type Post.
		 */
		public function ldgr_registrations_left_callback( $post ) {
			wp_nonce_field( 'wdm_ld_group_value', 'wdm_ld_group' );
			$group_id = $post->ID;

			// Recalculate group seats.
			ldgr_recalculate_group_seats( $group_id );

			$group_limit    = intval( get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true ) );
			$total_limit    = intval( get_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, true ) );
			$is_unlimited   = get_post_meta( $group_id, 'ldgr_unlimited_seats', 1 );
			$is_fixed_group = get_option( 'ldgr_group_limit' );

			self::enqueue_data_table();

			wp_enqueue_script(
				'wdm_admin_js',
				plugins_url(
					'js/wdm_admin.js',
					__DIR__
				),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_enqueue_script(
				'ldgr_snackbar',
				plugins_url(
					'js/snackbar.js',
					__DIR__
				),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);

			/**
			 * Filter admin localized data.
			 *
			 * @since 4.2.3
			 *
			 * @param array $data   Localized data to be passed in js.
			 */
			$data = apply_filters(
				'ldgr_filter_admin_localized_data',
				[
					'ajax_url'                => admin_url( 'admin-ajax.php' ),
					'ajax_loader'             => plugins_url( 'media/ajax-loader.gif', __DIR__ ),
					'no_user_selected'        => __( 'No user selected', 'wdm_ld_group' ),
					'datatable'               => [
						'length_menu' => [ [ 10, 25, 50, -1 ], [ 10, 25, 50, 'All' ] ],
						'column_defs' => [
							'orderable' => false,
							'targets'   => 0,
						],
					],
					'invalid_seat_update_msg' => __( ' Total seats should always be greater than ( or equal to ) the number of enrolled users.', 'wdm_ld_group' ),
					'seats_left_text'         => __( '{seat_count} seats', 'wdm_ld_group' ),
					'original_seats_left'     => $group_limit,
					'original_total_seats'    => $total_limit,
					'no_change_reset_msg'     => __( 'No changes to reset', 'wdm_ld_group' ),
					'update_reset_msg'        => __( 'Seat count reset successfully', 'wdm_ld_group' ),
					'is_fix_group_limit'      => $is_fixed_group,
					'ldgr_nonce'              => wp_create_nonce( 'ldgr_recalculate_user_seats' ),
				]
			);

			wp_localize_script( 'wdm_admin_js', 'wdm_ajax', $data );

			wp_enqueue_style(
				'wdm_datatable_css',
				plugins_url(
					'css/datatables.min.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_enqueue_style(
				'wdm_style_css',
				plugins_url(
					'css/style.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_enqueue_style(
				'wdm_snackbar_css',
				plugins_url(
					'css/wdm-snackbar.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);

			$removal_request = maybe_unserialize( get_post_meta( $group_id, 'removal_request', true ) );

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-group-registrations-left-metabox.template.php',
				[
					'is_unlimited'       => $is_unlimited,
					'group_limit'        => $group_limit,
					'total_limit'        => $total_limit,
					'removal_request'    => $removal_request,
					'group_id'           => $group_id,
					'is_fixed_group'     => $is_fixed_group,
					'settings_page_link' => admin_url( 'admin.php?page=wdm-ld-gr-setting' ),
				]
			);
		}

		/**
		 * Ajax for handling bulk removal request of a group leader
		 */
		public function handle_bulk_remove_group_users() {
			$return    = [];
			$user_ids  = filter_input( INPUT_POST, 'user_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			$group_ids = filter_input( INPUT_POST, 'group_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

			if ( ! is_array( $user_ids ) || empty( $user_ids ) ) {
				echo json_encode( [ 'error' => __( 'Oops Something went wrong', 'wdm_ld_group' ) ] );
				die();
			}

			foreach ( $user_ids as $key => $user_id ) {
				$return[ $user_id ] = $this->remove_group_user( $user_id, $group_ids[ $key ] );
			}

			echo wp_json_encode( $return );

			die();
		}


		/**
		 * Ajax for new user enrollment form submission check.
		 */
		public function enroll_form_validation_for_sub_groups() {
			$group_id           = filter_input( INPUT_POST, 'group_id', FILTER_DEFAULT );
			$email_of_the_users = filter_input( INPUT_POST, 'email', FILTER_VALIDATE_EMAIL, FILTER_REQUIRE_ARRAY );
			$sub_groups         = learndash_get_group_children( $group_id );
			if ( empty( $sub_groups ) ) {
				foreach ( $email_of_the_users as $email_of_the_users_mail ) {
					$user = get_user_by( 'email', $email_of_the_users_mail );
					if ( ! empty( $user ) ) {
						$user_check_in_group = learndash_get_users_group_ids( $user->ID, $group_id );
						if ( in_array( $group_id, $user_check_in_group ) ) {
							$user_already_in_parent_group [] = $email_of_the_users_mail;
						}
					}
				}
			}

			if ( isset( $user_already_in_parent_group ) && ! empty( $user_already_in_parent_group ) ) {
				$error_data_for_enroll_users = [
					'status' => 'failed',
					'users'  => $user_already_in_parent_group,
					'msg'    => sprintf(
						// translators: group, sub-group.
						esc_html__( 'This user is already part of the main %1$s and cannot be added to this %2$s.', 'wdm_ld_group' ),
						\LearnDash_Custom_Label::label_to_lower( 'group' ),
						\LearnDash_Custom_Label::label_to_lower( 'subgroup' )
					),
				];
				echo wp_json_encode( $error_data_for_enroll_users );
			} else {
				echo wp_json_encode( [ 'status' => 'success' ] );
			}
			die();
		}



		/**
		 * Remove user from group
		 *
		 * @param int $user_id      ID of the user.
		 * @param int $group_id     ID of the group.
		 *
		 * @return array            Success or error message in key => value format.
		 */
		public function remove_group_user( $user_id, $group_id ) {
			if ( is_user_logged_in() ) {
				if ( learndash_is_group_leader_user( get_current_user_id() ) || learndash_is_group_leader_user( get_current_user_id() ) || current_user_can( 'manage_options' ) ) {
					$admin_group_ids = learndash_get_administrators_group_ids( get_current_user_id() );

					if ( ! in_array( $group_id, $admin_group_ids ) ) {
						return [ 'error' => __( 'You are not the owner of this group', 'wdm_ld_group' ) ];
					}

					if ( '' != $user_id && '' != $group_id ) {
						$ldgr_admin_approval = get_option( 'ldgr_admin_approval' );

						if ( 'on' == $ldgr_admin_approval ) {
							$response = $this->ldgr_remove_user_from_group( $user_id, $group_id );
							if ( $response ) {
								return [ 'success' => __( 'User removed from the Group Successfully', 'wdm_ld_group' ) ];
							} else {
								return [ 'error' => __( 'Oops Something went wrong', 'wdm_ld_group' ) ];
							}
							// die();.
						} else {
							// When Admin needs to approve the remove request.
							$removal_request = maybe_unserialize( get_post_meta( $group_id, 'removal_request', true ) );
							if ( empty( $removal_request ) ) {
								$removal_request = [];
							}

							$removal_request[]  = $user_id;
							$removal_req_unique = array_unique( $removal_request );
							update_post_meta( $group_id, 'removal_request', $removal_req_unique );

							// Fetch email enable/disable setting
							$wdm_a_rq_rmvl_enable = get_option( 'wdm_a_rq_rmvl_enable' );

							if ( apply_filters( 'wdm_removal_request_admin_email_status', true, $group_id ) && 'off' != $wdm_a_rq_rmvl_enable ) {
								$user_data   = get_user_by( 'id', $user_id );
								$group_title = get_the_title( $group_id );
								$subject     = __( 'User Removal request from group', 'wdm_ld_group' );
								$leader_data = get_user_by( 'id', get_current_user_id() );

								$subject = get_option( 'wdm-a-rq-rmvl-sub' );
								if ( empty( $subject ) ) {
									$subject = WDM_A_RQ_RMVL_SUB;
								}
								$subject = stripslashes( $subject );
								$subject = str_replace( '{group_title}', $group_title, $subject );
								$subject = str_replace( '{user_email}', $user_data->user_email, $subject );
								$subject = str_replace( '{group_edit_link}', admin_url( 'post.php?post=' . $group_id . '&action=edit' ), $subject );
								$subject = str_replace( '{group_leader_name}', ucfirst( strtolower( $leader_data->first_name ) ) . ' ' . ucfirst( strtolower( $leader_data->last_name ) ), $subject );
								$subject = apply_filters( 'wdm_removal_subject', $subject, $group_id, get_current_user_id(), $user_id );

								$tbody = get_option( 'wdm-a-rq-rmvl-body' );
								if ( empty( $tbody ) ) {
									$tbody = WDM_A_RQ_RMVL_BODY;
								}
								$body = stripslashes( $tbody );

								$body = str_replace( '{group_title}', $group_title, $body );
								$body = str_replace( '{group_leader_name}', ucfirst( strtolower( $leader_data->first_name ) ) . ' ' . ucfirst( strtolower( $leader_data->last_name ) ), $body );
								$body = str_replace( '{user_email}', $user_data->user_email, $body );
								$body = str_replace( '{group_edit_link}', admin_url( 'post.php?post=' . $group_id . '&action=edit' ), $body );
								$body = apply_filters( 'wdm_removal_request_body', $body, $group_id, get_current_user_id(), $user_id );

								// Admin emails
								$admin_email = ! empty( get_option( 'wdm-gr-admin-email' ) ) ? get_option( 'wdm-gr-admin-email' ) : get_option( 'admin_email' );

								ldgr_send_group_mails(
									apply_filters( 'wdm_removal_request_email_to', $admin_email ),
									$subject,
									$body,
									[],
									[],
									[
										'email_type' => 'WDM_A_RQ_RMVL_BODY',
										'group_id'   => $group_id,
									]
								);
							}

							return [ 'success' => __( 'Removal request sent Successfully', 'wdm_ld_group' ) ];
						}
					} else {
						return [ 'error' => __( 'Oops Something went wrong', 'wdm_ld_group' ) ];
						// die();
					}
				} else {
					return [ 'error' => __( "You don't have privilege to do this action", 'wdm_ld_group' ) ];
				}
			} else {
				return [ 'error' => __( "You don't have privilege to do this action", 'wdm_ld_group' ) ];
			}
			return [];
		}

		/**
		 *  Ajax for handling removal request from group leader
		 */
		public function handle_group_unenrollment() {
			check_ajax_referer( 'ldgr_nonce_remove_user', 'nonce' );

			$user_id  = filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT );
			$group_id = filter_input( INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT );

			echo wp_json_encode( $this->remove_group_user( $user_id, $group_id ) );
			die();
		}

		/**
		 * After trashing group, deleting associated user details
		 *
		 * @param int $post_id  ID of the post
		 */
		public function handle_group_deletion( $post_id ) {
			// We check if the global post type isn't ours and just return
			global $post_type;
			if ( $post_type != 'groups' ) {
				return;
			}
			$group_leaders = learndash_get_groups_administrator_ids( $post_id );
			if ( ! empty( $group_leaders ) ) {
				foreach ( $group_leaders as $k => $v ) {
					delete_user_meta( $v, 'wdm_group_users_limit_' . $post_id, null );
					delete_user_meta( $v, 'wdm_group_total_users_limit_' . $post_id, null );
					delete_user_meta( $v, 'wdm_group_product_' . $post_id, null );
					delete_user_meta( $v, 'ldgr_unlimited_seats', null );
					unset( $k );
				}
			}
		}

		/**
		 * Process the CSV file upload and enroll users.
		 *
		 * @param int   $step           Step offset value for enrolling users in batch.
		 * @param float $percentage     Overall percentage of users uploaded.
		 *
		 * @return array $response      List of enrolled users if success, else error messages.
		 */
		public function ldgr_upload_csv( &$step, &$percentage ) {
			if ( isset( $_POST['wdm_upload_check'] ) &&
			( isset( $_POST['wdm_ldgr_csv_upload_enroll_field'] ) && wp_verify_nonce( $_POST['wdm_ldgr_csv_upload_enroll_field'], 'wdm_ldgr_csv_upload_enroll' ) )
			) {
				$response    = [];
				$csv_invalid = $this->check_if_valid_csv_file( $_FILES );

				// Return if file type not CSV.
				if ( ! empty( $csv_invalid ) ) {
					$response = [
						'type'    => 'error',
						'message' => $csv_invalid,
					];
					return $response;
				}

				$group_id = filter_input( INPUT_POST, 'wdm_group_id', FILTER_SANITIZE_NUMBER_INT );

				if ( $step ) {
					/**
					 * Filter the batch length for CSV uploads.
					 *
					 * @param int $batch_length     Batch length for CSV uploads.
					 */
					$batch_length = apply_filters( 'ldgr_filter_csv_upload_batch_length', 10 );
				}

				$csv_data_list = $this->get_csv_data_list( $_FILES, $group_id, $step, $batch_length );

				if ( ! empty( $csv_data_list['error'] ) ) {
					$response = [
						'type'    => 'error',
						'message' => $csv_data_list['error'],
					];
					return $response;
				}

				// Batch Process data.
				if ( $step ) {
					$csv_length = count( $csv_data_list['emails'] );
					if ( $csv_length > $batch_length ) {
						$start                        = ( $step - 1 ) * $batch_length;
						$csv_data_list['emails']      = array_slice( $csv_data_list['emails'], $start, $batch_length );
						$csv_data_list['first_names'] = array_slice( $csv_data_list['first_names'], $start, $batch_length );
						$csv_data_list['last_names']  = array_slice( $csv_data_list['last_names'], $start, $batch_length );

						++$step;
						$processed_count = intval( $start + $batch_length );

						if ( $csv_length <= $processed_count ) {
							$step = 'done';
						}
					} else {
						$step            = 'done';
						$processed_count = $csv_length;
					}
				}

				$percentage = intval( ( $processed_count / $csv_length ) * 100 );

				$data = [];
				$data = $this->ldgr_enroll_users( $csv_data_list, $group_id );

				if ( array_key_exists( 'type', $data ) ) {
					$response = $data;
				}

				if ( array_key_exists( 'users', $data ) ) {
					$response['data'] = $data['users'];
				}

				return $response;
			}
		}

		/**
		 * Enroll users from CSV
		 *
		 * @param array $csv_data_list  CSV data details array.
		 * @param int   $group_id       ID of the group.
		 *
		 * @return array                List of newly added users on success, else error message.
		 */
		public function ldgr_enroll_users( $csv_data_list, $group_id ) {
			global $error_data;
			global $success_data;
			$response                    = [];
			$ldgr_dynamic_fields_setting = get_option( 'ldgr_dynamic_fields', [] );

			$final_csv_data = [
				'fname'    => $csv_data_list['first_names'],
				'lname'    => $csv_data_list['last_names'],
				'email'    => $csv_data_list['emails'],
				'group_id' => $group_id,
			];

			/**
			 * Later CSV data after upload.
			 *
			 * @param array $final_csv_data     Filtered CSV data to be used while enrolling users to group.
			 * @param int   $group_id           ID of the group.
			 * @param array $csv_data_list      Original CSV file data.
			 */
			$final_csv_data = apply_filters( 'wdm_ld_gr_alter_upload_data', $final_csv_data, $group_id, $csv_data_list );

			$fname    = $final_csv_data['fname'];
			$lname    = $final_csv_data['lname'];
			$email    = $final_csv_data['email'];
			$group_id = $final_csv_data['group_id'];

			$newly_added_user = [];
			$lead_user        = new WP_User( get_current_user_id() );
			$courses          = learndash_group_enrolled_courses( $group_id );
			$group_limit      = get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true );
			$is_unlimited     = get_post_meta( $group_id, 'ldgr_unlimited_seats', 1 );
			$group_leader_ids = learndash_get_groups_administrator_ids( $group_id );
			$all_emails_list  = [];
			$url              = '';

			foreach ( $email as $k => $val ) {
				if ( $group_limit > 0 || $is_unlimited ) {
					$user_exits = email_exists( $val );
					if ( '' != $user_exits ) {
						$member_user = new WP_User( $user_exits );

						$already_enroll = apply_filters(
							'ldgr_filter_enroll_user_in_group',
							learndash_is_user_in_group(
								$user_exits,
								$group_id
							),
							$user_exits,
							$group_id
						);

						// Retrieves the all group leader ids.
						$group_leader_ids = learndash_get_groups_administrator_ids( $group_id );

						// adds user if user is not group member or leader.
						// if (!$already_enroll && !in_array($member_user->ID, $group_leader_ids)) { .
						if ( ! $already_enroll ) {
							if ( apply_filters( 'is_ldgr_default_user_add_action', true ) ) {
								ld_update_group_access( $user_exits, $group_id );
								delete_user_meta( $member_user->ID, '_total_groups_an_user_removed_from' );
							}

							do_action( 'ldgr_action_existing_user_enroll', $user_exits, $group_id, $final_csv_data );

							$t_sub = get_option( 'wdm-u-add-gr-sub' );
							if ( empty( $t_sub ) ) {
								$t_sub = WDM_U_ADD_GR_SUB;
							}
							$subject         = stripslashes( $t_sub );
							$enrolled_course = [];

							// check empty array $courses
							if ( ! empty( $courses ) ) {
								foreach ( $courses as $key => $value ) {
									$enrolled_course[] = get_the_title( $value );
									$url               = get_permalink( $value );
									unset( $key );
								}
							}
							$subject = str_replace( '{group_title}', get_the_title( $group_id ), $subject );
							// $subject = str_replace("{course_list}", '' , $subject);
							$subject = str_replace( '{group_leader_name}', ucfirst( strtolower( $lead_user->first_name ) ) . ' ' . ucfirst( strtolower( $lead_user->last_name ) ), $subject );
							$subject = str_replace( '{user_first_name}', ucfirst( strtolower( $member_user->first_name ) ), $subject );
							$subject = str_replace( '{user_last_name}', ucfirst( strtolower( $member_user->last_name ) ), $subject );
							$subject = str_replace( '{login_url}', wp_login_url( $url ), $subject );

							$tbody = get_option( 'wdm-u-add-gr-body' );
							if ( empty( $tbody ) ) {
								$tbody = WDM_U_ADD_GR_BODY;
							}
							$body = stripslashes( $tbody );

							$body = str_replace( '{group_title}', get_the_title( $group_id ), $body );
							$body = str_replace( '{course_list}', $this->get_course_list_html( $enrolled_course, $group_id, $member_user->ID ), $body );
							$body = str_replace( '{group_leader_name}', ucfirst( strtolower( $lead_user->first_name ) ) . ' ' . ucfirst( strtolower( $lead_user->last_name ) ), $body );
							$body = str_replace( '{user_first_name}', ucfirst( strtolower( $member_user->first_name ) ), $body );
							$body = str_replace( '{user_last_name}', ucfirst( strtolower( $member_user->last_name ) ), $body );
							$body = str_replace( '{login_url}', wp_login_url( $url ), $body );

							// Fetch enable/disable email setting.
							$email_enabled_option = get_option( 'wdm_u_add_gr_enable' );

							if ( apply_filters( 'wdm_group_enrollment_email_status', true, $group_id ) && 'off' != $email_enabled_option ) {
								$all_emails_list[ $member_user->ID ] = [
									'email'   => $val,
									'subject' => $subject,
									'body'    => $body,
									'new'     => false,
								];
							}
							$success_data .= apply_filters( 'wdm_group_enrollment_success_message', sprintf( /* translators: Enrolled User Name. */__( '%s has been enrolled', 'wdm_ld_group' ), $val ), $group_id, $val );
							--$group_limit;
							$newly_added_user[] = $member_user->ID;

							// added code for multisite user check.
							if ( is_multisite() ) {
								$blog_id = get_current_blog_id();
								if ( false == is_user_member_of_blog( $member_user->ID, $blog_id ) ) {
									add_user_to_blog( $blog_id, $member_user->ID, 'subscriber' );
								}
							}
							// end code for multisite user check.

							// Adding Dynamic meta on enroll  to group
							foreach ( $ldgr_dynamic_fields_setting as $key => $value ) {
								if ( 'yes' === $value['override'] && ! empty( get_user_meta( $member_user->ID, $value['key'], true ) ) ) {
									continue;
								} else {
									if ( ! isset( $csv_data_list[ $value['key'] ][ $k ] ) ) {
										$csv_data_list[ $value['key'] ][ $k ] = '';
									}
									update_user_meta( $member_user->ID, $value['key'], $csv_data_list[ $value['key'] ][ $k ] );
								}
							}
						} else {
							/**
							 * Filter to change user already enrolled error message.
							 *
							 * @param string $error_data    Error message.
							 */
							$error_data .= apply_filters( 'wdm_group_enrollment_error_message', sprintf( /* translators: Enrolled User Name. */__( '%s is already enrolled to group', 'wdm_ld_group' ), $member_user->user_email ), $group_id, $val );
						}
					} else {
						// If E-mail is invalid then show error for that user only.
						if ( ! filter_var( $val, FILTER_VALIDATE_EMAIL ) ) {
							/**
							 * Filter to change invalid email error message.
							 *
							 * @param string $error_data    Error message.
							 */
							$error_data .= apply_filters( 'wdm_group_leader_enrollment_error_message', sprintf( /* translators: The Email address. */__( 'Invalid E-mail address : %s ', 'wdm_ld_group' ), $val ), $group_id, $val );
						} else {
							$password = wp_generate_password( 8 );
							$userdata = [
								'user_login' => $val,
								'user_email' => $val,
								'first_name' => $fname[ $k ],
								'last_name'  => $lname[ $k ],
								'user_pass'  => $password, // When creating a user, `user_pass` is expected.
							];
							/**
							 * Filter userdata before creating a new user and enrolling to group.
							 *
							 * @param array $userdata       Userdata used in user creation and enrollment.
							 * @param array $final_csv_data Processed CSV data used for user enrollment.
							 */
							$userdata = apply_filters( 'ldgr_filter_new_user_details', $userdata, $final_csv_data );

							$member_user_id = wp_insert_user( $userdata );
							// Adding Dynamic meta.
							foreach ( $ldgr_dynamic_fields_setting as $key => $value ) {
								if ( 'yes' === $value['override'] && ! empty( get_user_meta( $member_user_id, $value['key'], true ) ) ) {
									continue;
								} else {
									if ( ! isset( $csv_data_list[ $value['key'] ][ $k ] ) ) {
										$csv_data_list[ $value['key'] ][ $k ] = '';
									}
									update_user_meta( $member_user_id, $value['key'], $csv_data_list[ $value['key'] ][ $k ] );
								}
							}
							$f_name = $fname[ $k ];
							$l_name = $lname[ $k ];

							$all_emails_list[ $member_user_id ] = [
								'email'     => $val,
								'new'       => true,
								'group_id'  => $group_id,
								'user_data' => $userdata,
								'courses'   => $courses,
								'lead_user' => $lead_user,
							];

							// On success.
							--$group_limit;
							$newly_added_user[] = $member_user_id;
						}
					}
				}
			}

			$this->send_bulk_upload_emails( $all_emails_list, $group_id, $final_csv_data );

			$error_data = str_replace( '<br>', '<br>ERROR: ', $error_data );
			$error_data = ldgr_str_lreplace( 'ERROR: ', '', $error_data );
			if ( '' != $error_data ) {
				$response = [
					'type'    => 'error',
					'message' => $error_data,
				];
			}
			$success_data = str_replace( '<br>', '<br>SUCCESS: ', $success_data );
			$success_data = ldgr_str_lreplace( 'SUCCESS: ', '', $success_data );
			if ( '' != $success_data ) {
				$response = [
					'type'    => 'success',
					'message' => $success_data,
				];
			}

			// Update Group User Limit.
			update_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, $group_limit );
			ldgr_recalculate_group_seats( $group_id );

			if ( $group_limit <= 0 && ! $is_unlimited ) {
				do_action( 'wdm_group_limit_is_zero', $group_id );
			}

			if ( ! empty( $newly_added_user ) ) {
				do_action( 'ld_group_postdata_updated', $group_id, $group_leader_ids, $newly_added_user, $courses );
			}

			$response['users'] = $newly_added_user;
			return $response;
		}

		/**
		 * Get course list HTML
		 *
		 * @param array $course_list    List of courses to display.
		 *
		 * @return string               HTML list of courses.
		 */
		public function get_course_list_html( $course_list, $group_id = 0, $user_id = 0 ) {
			$return = '';
			if ( ! empty( $course_list ) ) {
				$return = '<ul>';
				foreach ( $course_list as $course ) {
					$return .= '<li>' . $course . '</li>';
				}
				$return .= '</ul>';
			}
			return apply_filters( 'ldgr_course_list_html', $return, $course_list, $group_id, $user_id );
		}

		/**
		 * Creating user, associating user with group
		 */
		public function handle_group_enrollment_form() {
			global $error_data;
			if ( array_key_exists( 'ldgr_enroll_users_nonce', $_POST ) && wp_verify_nonce( filter_input( INPUT_POST, 'ldgr_enroll_users_nonce' ), 'ldgr_enroll_users' ) && isset( $_POST['wdm_add_user_check'] ) ) {
				$group_id       = filter_input( INPUT_POST, 'wdm_group_id', FILTER_SANITIZE_NUMBER_INT );
				$email          = filter_input( INPUT_POST, 'wdm_members_email', FILTER_SANITIZE_EMAIL, FILTER_REQUIRE_ARRAY );
				$fname          = filter_input( INPUT_POST, 'wdm_members_fname', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );
				$lname          = filter_input( INPUT_POST, 'wdm_members_lname', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );
				$dynamic_fields = filter_input( INPUT_POST, 'wdm_dynamic', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

				if ( is_group_leader_restricted_to_perform_actions( get_current_user_id(), $group_id ) ) {
					$error_data = __( "You don't have permission to perform this action.", 'wdm_ld_group' );
				} else {
					$data = [
						'emails'      => $email,
						'first_names' => $fname,
						'last_names'  => $lname,
					];

					if ( ! empty( $dynamic_fields ) && is_array( $dynamic_fields ) ) {
						$data = array_merge( $data, $dynamic_fields );
					}

					$this->ldgr_enroll_users(
						$data,
						$group_id
					);
				}
			}
		}

		/**
		 * Register new user and enroll in group
		 *
		 * @param int    $member_user_id   ID of the user to register and enroll.
		 * @param string $f_name           First name of the user.
		 * @param string $l_name           Last name of the user.
		 * @param string $val              Email of the user.
		 * @param string $password         Password of the new user.
		 * @param array  $courses          List of courses to enroll in.
		 * @param obj    $lead_user        Group leader.
		 * @param int    $group_id         ID of the group.
		 *
		 * @return string                  Status of the newly enrolled user.
		 */
		public function new_user_registration( $member_user_id, $f_name, $l_name, $val, $password, $courses, $lead_user, $group_id ) {
			global $success_data;

			if ( ! is_wp_error( $member_user_id ) ) {
				$user_data           = get_user_by( 'id', $member_user_id );
				$key                 = get_password_reset_key( $user_data );
				$reset_arg           = [
					'action' => 'rp',
					'key'    => $key,
					'login'  => rawurlencode( $user_data->user_login ),
				];
				$reset_password_link = add_query_arg( $reset_arg, network_site_url( 'wp-login.php', 'login' ) );

				$subject = get_option( 'wdm-u-ac-crt-sub' );
				if ( empty( $subject ) ) {
					$subject = WDM_U_AC_CRT_SUB;
				}
				$enrolled_course = [];
				foreach ( $courses as $key => $value ) {
					$enrolled_course[] = get_the_title( $value );
					$url               = get_permalink( $value );
					unset( $key );
				}
				$subject = stripslashes( $subject );
				$subject = str_replace( '{group_title}', get_the_title( $group_id ), $subject );
				$subject = str_replace( '{site_name}', get_bloginfo(), $subject );
				$subject = str_replace( '{user_first_name}', ucfirst( $f_name ), $subject );
				$subject = str_replace( '{user_last_name}', ucfirst( $l_name ), $subject );
				$subject = str_replace( '{user_email}', $val, $subject );
				$subject = str_replace( '{user_password}', $password, $subject );
				$subject = str_replace( '{course_list}', $this->get_course_list_html( $enrolled_course, $group_id, $member_user_id ), $subject );
				$subject = str_replace( '{group_leader_name}', ucfirst( strtolower( $lead_user->first_name ) ) . ' ' . ucfirst( strtolower( $lead_user->last_name ) ), $subject );
				$subject = str_replace( '{login_url}', wp_login_url(), $subject );
				$subject = str_replace( '{reset_password}', $reset_password_link, $subject );

				$tbody = get_option( 'wdm-u-ac-crt-body' );
				if ( empty( $tbody ) ) {
					$tbody = WDM_U_AC_CRT_BODY;
				}
				$body = stripslashes( $tbody );

				$body = str_replace( '{group_title}', get_the_title( $group_id ), $body );
				$body = str_replace( '{site_name}', get_bloginfo(), $body );
				$body = str_replace( '{user_first_name}', ucfirst( $f_name ), $body );
				$body = str_replace( '{user_last_name}', ucfirst( $l_name ), $body );
				$body = str_replace( '{user_email}', $val, $body );
				$body = str_replace( '{user_password}', $password, $body );
				$body = str_replace( '{course_list}', $this->get_course_list_html( $enrolled_course, $group_id, $member_user_id ), $body );
				$body = str_replace( '{group_leader_name}', ucfirst( strtolower( $lead_user->first_name ) ) . ' ' . ucfirst( strtolower( $lead_user->last_name ) ), $body );
				$body = str_replace( '{login_url}', wp_login_url(), $body );
				$body = str_replace( '{reset_password}', $reset_password_link, $body );

				// Fetch enable/disable email setting
				$wdm_u_ac_crt_enable = get_option( 'wdm_u_ac_crt_enable' );
				if ( apply_filters( 'wdm_group_enrollment_email_status', true, $group_id ) && 'off' != $wdm_u_ac_crt_enable ) {
					ldgr_send_group_mails(
						$val,
						apply_filters( 'wdm_group_email_subject', $subject, $group_id, $member_user_id ),
						apply_filters( 'wdm_group_email_body', $body, $group_id, $member_user_id ),
						[],
						[],
						[
							'email_type' => 'WDM_U_AC_CRT_BODY',
							'group_id'   => $group_id,
						]
					);
				}
				$success_data .= apply_filters( 'wdm_group_enrollment_success_message', sprintf( __( '%s has been enrolled', 'wdm_ld_group' ), $val ) );
				ld_update_group_access( $member_user_id, $group_id );
				$member_user_data = new WP_User( $member_user_id );

				$blogname          = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				$member_login_data = stripslashes( $member_user_data->user_login );
				$member_email_data = stripslashes( $member_user_data->user_email );

				$tbody = get_option( 'wdm-a-u-ac-crt-body' );
				if ( empty( $tbody ) ) {
					$tbody = WDM_A_U_AC_CRT_BODY;
				}
				$message = stripslashes( $tbody );

				$message = str_replace( '{group_title}', get_the_title( $group_id ), $message );
				$message = str_replace( '{site_name}', $blogname, $message );
				$message = str_replace( '{user_email}', $member_email_data, $message );
				$message = str_replace( '{user_login}', $member_login_data, $message );

				$title = get_option( 'wdm-a-u-ac-crt-sub' );
				if ( empty( $title ) ) {
					$title = WDM_A_U_AC_CRT_SUB;
				}

				$title = str_replace( '{group_title}', get_the_title( $group_id ), $title );
				$title = str_replace( '{site_name}', $blogname, $title );
				$title = str_replace( '{user_email}', $member_email_data, $title );
				$title = str_replace( '{user_login}', $member_login_data, $title );

				// admin emails
				$admin_email = ! empty( get_option( 'wdm-gr-admin-email' ) ) ? get_option( 'wdm-gr-admin-email' ) : get_option( 'admin_email' );
				$send_to     = apply_filters( 'new_user_admin_notification_mail_to', $admin_email );

				// Fetch enable/disable email setting
				$wdm_a_u_ac_crt_enable = get_option( 'wdm_a_u_ac_crt_enable' );

				if ( apply_filters( 'wdm_new_user_creation_email_status', true, $group_id ) && 'off' != $wdm_a_u_ac_crt_enable ) {
					$title   = apply_filters( 'wdm_new_user_admin_notification_subject', $title, $group_id );
					$message = apply_filters( 'wdm_new_user_admin_notification_body', $message, $group_id, $member_user_data );
					ldgr_send_group_mails(
						$send_to,
						$title,
						$message,
						[],
						[],
						[
							'email_type' => 'WDM_A_U_AC_CRT_BODY',
							'group_id'   => $group_id,
						]
					);
				}

				return $success_data;
			}
		}

		/**
		 * Get selected group value.
		 */
		public function get_selected_group_value( $group_id, $val ) {
			if ( $group_id == '' ) {
				$group_id = $val;
			}
			return $group_id;
		}

		/**
		 * Add groups shortcodes
		 */
		public function add_groups_shortcodes() {
			add_shortcode(
				'wdm_group_users',
				[ $this, 'handle_group_registration_shortcode_display' ]
			);
		}

		/**
		 * Display group registration shortcode page - [wdm_group_users]
		 */
		public function handle_group_registration_shortcode_display() {
			if ( ! defined( 'LEARNDASH_VERSION' ) ) {
				ob_start();
				echo '<h2>' . esc_html__( 'Some issue viewing this page, please contact site administrator.', 'wdm_ld_group' ) . '</h2>';
				/**
				 * After learndash not activated.
				 *
				 * @since 4.3.0
				 */
				do_action( 'ldgr_action_after_ld_not_active_restriction' );
				return ob_get_clean();
			}

			if ( ! is_user_logged_in() ) {
				ob_start();
				echo '<h2>' . esc_html__( 'Please Login to view this page', 'wdm_ld_group' ) . '</h2>';
				/**
				 * After login restriction.
				 *
				 * @since 4.2.0
				 */
				do_action( 'ldgr_action_after_login_restriction' );
				return ob_get_clean();
			}

			$user_id   = get_current_user_id();
			$group_ids = ldgr_get_leader_group_ids( $user_id );

			if ( empty( $group_ids ) ) {
				ob_start();
				echo '<h2>' . esc_html__( 'You are not the leader of any group', 'wdm_ld_group' ) . '</h2>';
				/**
				 * After no groups found.
				 *
				 * @since 4.2.0
				 */
				do_action( 'ldgr_action_no_groups' );
				return ob_get_clean();
			}

			$group_selected = false;
			$group_id       = filter_input( INPUT_POST, 'wdm_group_id', FILTER_SANITIZE_NUMBER_INT );

			if ( empty( $group_id ) ) {
				$group_id = current( $group_ids );
				// If only one group ( without any subgroups ), do not show listing page.
				if ( 1 === count( $group_ids ) && empty( ldgr_get_sub_group_ids( $group_id ) ) ) {
					$group_selected = true;
					// Set Group ID in POST.
					$_POST['wdm_group_id'] = $group_id;
				}
			} else {
				$group_selected = true;
			}
			$user_data = get_user_by( 'id', $user_id );
			if ( ! $this->check_if_group_leader( $user_id ) ) {
				ob_start();
				echo '<h2>' . esc_html__( 'You do not have privilege to view this page.', 'wdm_ld_group' ) . '</h2>';
				/**
				 * After no group privileges.
				 *
				 * @since 4.2.0
				 */
				do_action( 'ldgr_action_no_group_privileges' );
				return ob_get_clean();
			}

			$need_to_restrict   = false;
			$sub_current_status = '';
			$group_limit        = 0;
			$grp_limit_count    = '';
			$subscription_id    = '';
			$user_sub_det       = '';
			$sub_current_status = '';

			$this->enqueue_group_users_display_shortcode_scripts( $group_id );
			if ( $group_selected ) {
				// Due to transient is set it doesn't refreshes the users so set it to zero.
				update_option( '_transient_timeout_learndash_group_users_' . $group_id, 0 );

				$group_limit        = intval( get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true ) );
				$grp_limit_count    = ( $group_limit < 0 ) ? 0 : $group_limit;
				$subscription_id    = get_post_meta( $group_id, 'wdm_group_subscription_' . $group_id, true );
				$user_sub_det       = $this->get_subscription_status( $user_id, $subscription_id );
				$need_to_restrict   = $user_sub_det['need_to_restrict'];
				$sub_current_status = $user_sub_det['sub_current_status'];
			}

			$sub_group_instance = Ld_Group_Registration_Sub_Groups::get_instance();
			global $error_data, $success_data;
			return ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-group-users/ldgr-group-users.template.php',
				[
					'group_limit'        => $group_limit,
					'grp_limit_count'    => $grp_limit_count,
					'subscription_id'    => $subscription_id,
					'user_sub_det'       => $user_sub_det,
					'need_to_restrict'   => $need_to_restrict,
					'sub_current_status' => $sub_current_status,
					'group_id'           => $group_id,
					'group_ids'          => $group_ids,
					'group_selected'     => $group_selected,
					'instance'           => $this,
					'sub_group_instance' => $sub_group_instance,
					'user_id'            => $user_id,
					'error_data'         => $error_data,
					'success_data'       => $success_data,
				],
				1
			);
			// return ob_get_clean();
		}

		/**
		 * Get subscription status
		 *
		 * @param int $user_id          ID of the user.
		 * @param int $subscription_id  ID of the subscription.
		 *
		 * @return array $details       Details about the subscription.
		 */
		public function get_subscription_status( $user_id, $subscription_id ) {
			$details = [
				'sub_current_status' => '',
				'need_to_restrict'   => false,
			];
			if ( ! empty( $subscription_id ) ) {
				$not_active_sub = get_user_meta( $user_id, '_wdm_total_hold_subscriptions', true );
				if ( ! empty( $not_active_sub ) && ( in_array( $subscription_id, $not_active_sub ) ) ) {
					$details['need_to_restrict'] = true;
					$wdm_subscription            = \wcs_get_subscription( $subscription_id );
					// $sub_current_status = '';
					if ( $wdm_subscription instanceof \WC_Subscription ) {
						$details['sub_current_status'] = $wdm_subscription->get_status();
					}
				}
			}
			return $details;
		}

		/**
		 * Display subscription errors
		 *
		 * @param bool  $need_to_restrict    Whether to restrict the content or not.
		 * @param int   $subscription_id     ID of the subscription.
		 * @param array $sub_current_status Details about the subscription status.
		 */
		public function show_subscription_errors( $need_to_restrict, $subscription_id, $sub_current_status ) {
			$wdm_link = '';
			if ( $need_to_restrict ) {
				$wdm_link .= "<a href='" . get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . "subscriptions/'>#{$subscription_id}</a>";

				$allowed_html = [
					'a' => [
						'href' => [],
					],
					'p' => [],
				];

				if ( 'on-hold' === $sub_current_status ) {
					echo wp_kses( '<p>' . sprintf( /* translators: Subscription Link. */__( 'Your %s subscription put on the hold. Please contact admin.', 'wdm_ld_group' ), $wdm_link ) . '</p>', $allowed_html );
				} elseif ( 'cancelled' === $sub_current_status ) {
					echo wp_kses( '<p>' . sprintf( /* translators: Subscription Link. */__( 'Your %s subscription has been cancelled. Please contact admin.', 'wdm_ld_group' ), $wdm_link ) . '</p>', $allowed_html );
				} elseif ( 'switched' === $sub_current_status || 'expired' === $sub_current_status ) {
					echo wp_kses( '<p>' . sprintf( /* translators: Subscription Link. */__( 'Your %s subscription has been expired. Please contact admin.', 'wdm_ld_group' ), $wdm_link ) . '</p>', $allowed_html );
				} elseif ( 'pending' === $sub_current_status ) {
					echo wp_kses( '<p>' . sprintf( /* translators: Subscription Link. */__( 'Your %s subscription status is pending. Please contact admin.', 'wdm_ld_group' ), $wdm_link ) . '</p>', $allowed_html );
				} else {
					echo wp_kses( '<p>' . sprintf( /* translators: Subscription Link. */__( 'Your %s subscription put on the hold. Please contact admin.', 'wdm_ld_group' ), $wdm_link ) . '</p>', $allowed_html );
				}
			}
		}

		/**
		 * Add product link to add new users to the group.
		 *
		 * @param int $group_id     ID of the group
		 *
		 * @return string           Link to the product associated with the group.
		 */
		public function add_new_users_link( $group_id ) {
			$is_unlimited = get_post_meta( $group_id, 'ldgr_unlimited_seats', 1 );
			if ( get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true ) == 0 && ! $is_unlimited ) {
				?>
				<a
					class="ldgr-add-new-users"
					href="<?php echo get_permalink( get_user_meta( get_current_user_id(), 'wdm_group_product_' . $group_id, true ) ); ?>"
				>
					<?php echo apply_filters( 'wdm_add_new_users_label', __( 'Add New Users', 'wdm_ld_group' ) ); ?>
				</a>
				<?php
			}
		}

		/**
		 * Adding group leader column in groups post type listing
		 *
		 * @param array $array containing column names.
		 */
		public function add_column_heading( $array ) {
			$res = array_slice( $array, 0, 2, true ) + [ 'group_leader' => __( 'Group Leader', 'wdm_ld_group' ) ] + array_slice( $array, 2, count( $array ) - 1, true );
			return $res;
		}

		/**
		 * Fetching group leader associated with each group.
		 *
		 * @param string $column_key    Key of the column.
		 * @param int    $group_id      ID of the group.
		 */
		public function add_column_data( $column_key, $group_id ) {
			// exit early if this is not the column we want.
			if ( 'group_leader' != $column_key ) {
				return;
			}
			$group_leader = learndash_get_groups_administrator_ids( $group_id );
			if ( ! empty( $group_leader ) ) {
				$group_temp = [];
				foreach ( $group_leader as $k => $v ) {
					$group_user   = get_user_by( 'id', $v );
					$group_temp[] = $group_user->user_email;
					unset( $k );
				}
				echo esc_html( implode( ', ', $group_temp ) );
			}
		}

		/**
		 * Check if group leader
		 *
		 * @param int $user_id  ID of the user.
		 * @return bool         True if user is group leader, false otherwise.
		 */
		public function check_if_group_leader( $user_id ) {
			if ( current_user_can( 'manage_options' ) ) {
				return true;
			}
			if ( function_exists( 'learndash_is_group_leader_user' ) ) {
				if ( learndash_is_group_leader_user( $user_id ) ) {
					return true;
				}
				return false;
			} else {
				if ( learndash_is_group_leader_user( $user_id ) ) {
					return true;
				}
				return false;
			}
		}

		/**
		 * Update group product details in usermeta if group limit empty.
		 *
		 * @param int $group_id     ID of the group.
		 */
		public function handle_group_limit_empty( $group_id ) {
			if ( \metadata_exists( 'post', $group_id, 'wdm_group_users_limit_' . $group_id ) ) {
				$not_updated_user = [];
				$product_id       = '';
				$user_id          = '';
				$admin_ids        = \learndash_get_groups_administrator_ids( $group_id );
				if ( ! empty( $admin_ids ) ) {
					foreach ( $admin_ids as $id ) {
						if ( \metadata_exists( 'user', $id, 'wdm_group_product_' . $group_id ) ) {
							$product_id = get_user_meta( $id, 'wdm_group_product_' . $group_id, true );
							$user_id    = $id;
							break;
						}
					}
				}
				if ( ! empty( $product_id ) ) {
					foreach ( $admin_ids as $id ) {
						if ( $id != $user_id ) {
							update_user_meta( $id, 'wdm_group_product_' . $group_id, $product_id );
						}
					}
				}
			}
		}

		public function ldgr_remove_user_from_group( $user_id, $group_id ) {
			$group_limit       = get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true );
			$total_group_limit = get_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, true );

			// Check if If total group limit set.
			if ( empty( $total_group_limit ) || '' === $total_group_limit ) {
				$total_group_limit = -1;
			}

			if ( '' == $group_limit ) {
				$group_limit = 0;
			}

			// If the restrict group limit setting is not enabled, then increase group limit on user removal.
			$ldgr_group_limit = get_option( 'ldgr_group_limit' );
			if ( 'on' !== $ldgr_group_limit ) {
				// Check if group limit does not exceed total group limit.
				if ( $total_group_limit < 0 || $group_limit < $total_group_limit ) {
					$group_limit = ++$group_limit;
					update_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, $group_limit );
				}
			} else {
				// If fixed group limit is enabled, reduce 1 from total seats.
				--$total_group_limit;
				update_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, $total_group_limit );
			}

			$ldgr_admin_approval   = get_option( 'ldgr_admin_approval' );
			$wdm_gr_gl_rmvl_enable = get_option( 'wdm_gr_gl_rmvl_enable' );

			if ( $ldgr_admin_approval != 'on' && 'off' != $wdm_gr_gl_rmvl_enable ) {
				$admin_group_ids = learndash_get_groups_administrator_ids( $group_id );
				if ( ! empty( $admin_group_ids ) ) {
					$user_data = get_user_by( 'id', $user_id );
					foreach ( $admin_group_ids as $key => $value ) {
						if ( apply_filters( 'wdm_removal_request_accept_email_status', true, $group_id ) ) {
							$leader_data = get_user_by( 'id', $value );

							$gl_rmvl_sub = get_option( 'wdm-gr-gl-rmvl-sub' );
							if ( empty( $gl_rmvl_sub ) ) {
								$gl_rmvl_sub = WDM_GR_GL_RMVL_SUB;
							}
							$gl_rmvl_sub = str_replace( '{group_title}', get_the_title( $group_id ), $gl_rmvl_sub );
							$gl_rmvl_sub = str_replace( '{user_email}', $user_data->user_email, $gl_rmvl_sub );
							$gl_rmvl_sub = str_replace( '{group_leader_name}', ucfirst( strtolower( $leader_data->first_name ) ) . ' ' . ucfirst( strtolower( $leader_data->last_name ) ), $gl_rmvl_sub );
							$subject     = apply_filters( 'wdm_removal_request_accept_subject', $gl_rmvl_sub, $group_id, $user_id, $value );

							$gl_rmvl_body = get_option( 'wdm-gr-gl-rmvl-body' );
							if ( empty( $gl_rmvl_body ) ) {
								$gl_rmvl_body = WDM_GR_GL_RMVL_BODY;
							}
							$gl_rmvl_body = str_replace( '{group_title}', get_the_title( $group_id ), $gl_rmvl_body );
							$gl_rmvl_body = str_replace( '{user_email}', $user_data->user_email, $gl_rmvl_body );
							$gl_rmvl_body = str_replace( '{group_leader_name}', ucfirst( strtolower( $leader_data->first_name ) ) . ' ' . ucfirst( strtolower( $leader_data->last_name ) ), $gl_rmvl_body );
							$body         = stripslashes( $gl_rmvl_body );
							$body         = apply_filters( 'wdm_removal_request_accept_body', $body, $group_id, $user_id, $value );

							ldgr_send_group_mails(
								$leader_data->user_email,
								$subject,
								$body,
								[],
								[],
								[
									'email_type' => 'WDM_GR_GL_RMVL_BODY',
									'group_id'   => $group_id,
								]
							);
						}
					}
				}
			}

			ld_update_group_access( $user_id, $group_id, true );
			do_action( 'wdm_removal_request_accepted_successfully', $group_id, $user_id );

			return true;
		}

		public function send_reinvite_mail_callback() {
			if ( is_user_logged_in() ) {
				if ( learndash_is_group_leader_user( get_current_user_id() ) || learndash_is_group_leader_user( get_current_user_id() ) || current_user_can( 'manage_options' ) ) {
					$admin_group_ids = learndash_get_administrators_group_ids( get_current_user_id() );
					$user_id         = filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT );
					$group_id        = filter_input( INPUT_POST, 'group_id', FILTER_SANITIZE_NUMBER_INT );

					if ( ! in_array( $group_id, $admin_group_ids ) ) {
						echo json_encode( [ 'error' => __( 'You are not the owner of this group', 'wdm_ld_group' ) ] );
						die();
					}
					if ( '' != $user_id && '' != $group_id ) {
						// Fetch enable/disable email setting
						$wdm_gr_reinvite_enable = get_option( 'wdm_gr_reinvite_enable' );
						if ( apply_filters( 'wdm_send_reinvite_email_status', true, $group_id ) && 'off' != $wdm_gr_reinvite_enable ) {
							$user_data   = get_user_by( 'id', $user_id );
							$group_title = get_the_title( $group_id );
							$leader_data = get_user_by( 'id', get_current_user_id() );

							$user_login = $user_data->user_login;

							// Calculation for Reset Password link.
							global $wpdb;
							$key       = get_password_reset_key( $user_data );
							$reset_arg = [
								'action' => 'rp',
								'key'    => $key,
								'login'  => rawurlencode( $user_login ),
							];

							$reset_password_link = add_query_arg( $reset_arg, network_site_url( 'wp-login.php', 'login' ) );

							// fetch enrolled courses.
							$courses         = learndash_group_enrolled_courses( $group_id, true );
							$enrolled_course = [];
							foreach ( $courses as $key => $value ) {
								$enrolled_course[] = get_the_title( $value );
								$url               = get_permalink( $value );
								unset( $key );
							}

							$t_sub = get_option( 'wdm-reinvite-sub' );
							if ( empty( $t_sub ) ) {
								$t_sub = WDM_REINVITE_SUB;
							}
							$subject = stripslashes( $t_sub );
							$subject = str_replace( '{group_title}', get_the_title( $group_id ), $subject );
							$subject = str_replace( '{site_name}', get_bloginfo(), $subject );
							$subject = str_replace( '{user_first_name}', ucfirst( $user_data->first_name ), $subject );
							$subject = str_replace( '{user_last_name}', ucfirst( $user_data->last_name ), $subject );
							$subject = str_replace( '{user_email}', $user_data->user_email, $subject );
							$subject = str_replace( '{reset_password}', $reset_password_link, $subject );
							$subject = str_replace( '{course_list}', $this->get_course_list_html( $enrolled_course, $group_id, $user_id ), $subject );
							$subject = str_replace( '{group_leader_name}', ucfirst( strtolower( $leader_data->first_name ) ) . ' ' . ucfirst( strtolower( $leader_data->last_name ) ), $subject );
							$subject = str_replace( '{login_url}', wp_login_url(), $subject );
							$subject = apply_filters( 'wdm_reinvite_email_subject', $subject, $group_id, get_current_user_id(), $user_id );

							$tbody = get_option( 'wdm-reinvite-body' );
							if ( empty( $tbody ) ) {
								$tbody = WDM_REINVITE_BODY;
							}

							$body = stripslashes( $tbody );
							// $body = $reset_password_link;
							$body = str_replace( '{group_title}', get_the_title( $group_id ), $body );
							$body = str_replace( '{site_name}', get_bloginfo(), $body );
							$body = str_replace( '{user_first_name}', ucfirst( $user_data->first_name ), $body );
							$body = str_replace( '{user_last_name}', ucfirst( $user_data->last_name ), $body );
							$body = str_replace( '{user_email}', $user_data->user_email, $body );
							$body = str_replace( '{reset_password}', $reset_password_link, $body );
							$body = str_replace( '{course_list}', $this->get_course_list_html( $enrolled_course, $group_id, $user_id ), $body );
							$body = str_replace( '{group_leader_name}', ucfirst( strtolower( $leader_data->first_name ) ) . ' ' . ucfirst( strtolower( $leader_data->last_name ) ), $body );
							$body = str_replace( '{login_url}', wp_login_url(), $body );

							$body = apply_filters( 'wdm_reinvite_email_body', $body, $group_id, get_current_user_id(), $user_id );

							ldgr_send_group_mails(
								$user_data->user_email,
								$subject,
								$body,
								[],
								[],
								[
									'email_type' => 'WDM_REINVITE_BODY',
									'group_id'   => $group_id,
								]
							);

							echo json_encode(
								[
									'success' => __( 'Re Invitation mail has been sent successfully.', 'wdm_ld_group' ),
								]
							);
						}
						die();
					} else {
						echo json_encode( [ 'error' => __( 'Oops Something went wrong', 'wdm_ld_group' ) ] );
						die();
					}
				} else {
					echo json_encode( [ 'error' => __( "You don't have privilege to do this action", 'wdm_ld_group' ) ] );
				}
			} else {
				echo json_encode( [ 'error' => __( "You don't have privilege to do this action", 'wdm_ld_group' ) ] );
			}
			die();
		}

		/**
		 * Upload users from CSV via ajax
		 */
		public function ajax_upload_users_from_csv() {
			if ( ! is_user_logged_in() ) {
				echo wp_json_encode(
					[
						'error' => __( "You don't have privilege to do this action", 'wdm_ld_group' ),
					]
				);
				die();
			}

			$user_id = get_current_user_id();
			if ( ! $this->check_if_group_leader( $user_id ) ) {
				echo wp_json_encode(
					[
						'error' => __( "You don't have privilege to do this action", 'wdm_ld_group' ),
					]
				);
				die();
			}

			$step       = filter_input( INPUT_POST, 'step', FILTER_SANITIZE_NUMBER_INT );
			$percentage = 0;

			$response       = $this->ldgr_upload_csv( $step, $percentage );
			$enrolled_users = [];
			$results        = [];

			if ( 'success' === $response['type'] ) {
				$enrolled_users      = $response['data'];
				$group_id            = filter_input( INPUT_POST, 'wdm_group_id', FILTER_SANITIZE_NUMBER_INT );
				$enrolled_users_list = $this->get_enrolled_users_list( $enrolled_users, $group_id );
				$results['users']    = $enrolled_users_list;
			}

			if ( array_key_exists( 'type', $response ) ) {
				if ( 'error' === $response['type'] ) {
					$results['error'] = $response['message'];
				} else {
					$results['update'] = $response['message'];
				}
			}

			if ( array_key_exists( 'error', $results ) && ! empty( $results['error'] ) ) {
				$results['step'] = 'done';
			} else {
				$results['step'] = $step;
			}

			$results['percentage'] = $percentage;

			echo wp_json_encode( $results );
			die();
		}

		/**
		 * Get List of Enrolled users
		 *
		 * @param array $enrolled_users         List of users to be enrolled.
		 * @param int   $group_id               ID of the group to enroll the users in.
		 *
		 * @return array    $enrolled_users_list    List of enrolled users
		 */
		public function get_enrolled_users_list( $enrolled_users, $group_id ) {
			$enrolled_users_list = [];
			if ( empty( $enrolled_users ) || empty( $group_id ) ) {
				return $enrolled_users_list;
			}

			$default                            = [ 'removal_request' => [] ];
			$removal_request['removal_request'] = maybe_unserialize( get_post_meta( $group_id, 'removal_request', true ) );
			$removal_request                    = array_filter( $removal_request );

			$removal_request = wp_parse_args( $default, $removal_request );
			$removal_request = $removal_request['removal_request'];

			$ldgr_reinvite_user  = get_option( 'ldgr_reinvite_user' );
			$reinvite_class_data = 'wdm-reinvite';
			$reinvite_text_data  = apply_filters( 'wdm_change_reinvite_label', __( 'Re-Invite', 'wdm_ld_group' ) );

			foreach ( $enrolled_users as $user_id ) {
				$user_data = get_user_by( 'id', $user_id );

				$user_name  = get_user_meta( $user_id, 'first_name', true ) . ' ' . get_user_meta( $user_id, 'last_name', true );
				$user_email = $user_data->user_email;

				if ( ! in_array( $user_id, $removal_request ) ) {
					$class_data = 'wdm_remove';
					$text_data  = __( 'Remove', 'wdm_ld_group' );
				} else {
					$class_data = 'request_sent';
					$text_data  = __( 'Request sent', 'wdm_ld_group' );
				}

				$action = '';
				if ( $ldgr_reinvite_user == 'on' ) {
					$action = "<a
					href='#'
					data-user_id ='$user_id'
					data-group_id='$group_id'
					class='$reinvite_class_data button'>$reinvite_text_data</a>&nbsp;";
				}

				if ( apply_filters( 'wdm_ldgr_remove_user_button', true, $user_id, $group_id ) ) {
					$action .= "<a
					href='#'
					data-user_id ='$user_id'
					data-group_id='$group_id'
					data-nonce='" . esc_attr( wp_create_nonce( 'ldgr_nonce_remove_user' ) ) . "'
					class='$class_data button'>$text_data</a>";
				}
				// Add ajax loader dashicons.
				$action .= '<span class="dashicons dashicons-update spin hide"></span>';

				$checkbox = "<input type='checkbox' name='bulk_select' data-user_id ='$user_id' data-group_id='$group_id'>";

				$enrolled_users_list[] = apply_filters(
					'ldgr_ajax_upload_user_each',
					[
						$checkbox,
						$user_name,
						$user_email,
						$action,
					],
					$user_id,
					$group_id
				);

				$user_name = $user_email = $action = '';
			}

			return apply_filters( 'ldgr_ajax_upload_user_list', $enrolled_users_list, $group_id );
		}

		/**
		 * Enqueue scripts for the group registration page shortcode
		 *
		 * @param int $group_id  ID of the group.
		 */
		public function enqueue_group_users_display_shortcode_scripts( $group_id ) {
			self::enqueue_data_table();

			wp_enqueue_style(
				'wdm_datatable_css',
				plugins_url(
					'css/datatables.min.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_enqueue_style(
				'wdm_style_css',
				plugins_url(
					'css/style.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_enqueue_style(
				'wdm_snackbar_css',
				plugins_url(
					'css/wdm-snackbar.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);

			wp_register_script(
				'wdm_remove_js',
				plugins_url( 'js/wdm_remove.js', __DIR__ ),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);

			wp_register_script(
				'snackbar_js',
				plugins_url( 'js/snackbar.js', __DIR__ ),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);

			/**
			 * Filter groups dashboard localized data.
			 *
			 * @since 4.2.3
			 *
			 * @param array $data   Localized data.
			 */
			$data = apply_filters(
				'ldgr_filter_groups_dashboard_data',
				[
					'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
					'group_id'                 => $group_id,
					'group_limit'              => get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true ),
					'is_unlimited'             => get_post_meta( $group_id, 'ldgr_unlimited_seats', 1 ),
					'admin_approve'            => get_option( 'ldgr_admin_approval' ),
					'ajax_loader'              => plugins_url( 'media/ajax-loader.gif', __DIR__ ),
					'request_sent'             => __( 'Request sent', 'wdm_ld_group' ),
					'remove_html'              => '<a href="#" class="wdm_remove_add_user" title=' . __( 'Delete', 'wdm_ld_group' ) . '><span class="dashicons dashicons-no"></span></a>',
					'user_limit'               => __( 'User limit exceeded', 'wdm_ld_group' ),
					'student_singular'         => __( 'the student', 'wdm_ld_group' ),
					'student_plural'           => __( 'the students', 'wdm_ld_group' ),
					'no_user_selected'         => __( 'No user selected', 'wdm_ld_group' ),
					'are_you_sure'             => __( "Are you sure you want to remove the following user from the group? \n\n {user}", 'wdm_ld_group' ),
					'are_you_sure_plural'      => __( 'Are you sure you want to remove the selected users from the group?', 'wdm_ld_group' ),
					'only_csv_file_allowed'    => __( 'Only CSV file allowed!', 'wdm_ld_group' ),
					'no_matching_record_found' => __( 'No matching records found', 'wdm_ld_group' ),
					'search'                   => __( 'Search', 'wdm_ld_group' ),
					'processing'               => __( 'Processing...', 'wdm_ld_group' ),
					'loading'                  => __( 'Loading...', 'wdm_ld_group' ),
					'no_user_is_enrolled'      => __( 'No user is enrolled', 'wdm_ld_group' ),
					'users_uploaded_msg'       => __( 'Users uploaded successfully!!', 'wdm_ld_group' ),
					// translators: For menu.
					'length_menu_msg'          => sprintf( __( 'Show %s Users', 'wdm_ld_group' ), '_MENU_' ),
					'of'                       => __( 'of', 'wdm_ld_group' ),
					// translators: For max total entries.
					'info_filtered'            => sprintf( __( '(filtered from %s total entries)', 'wdm_ld_group' ), '_MAX_' ),
					'empty_msg'                => __( 'Please do not leave this field empty', 'wdm_ld_group' ),
					'error_msg'                => __( 'Some error occurred, kindly refresh the page and try again. If the problem still persists, please contact the site administrator.', 'wdm_ld_group' ),
					'search_placeholder'       => __( 'Search user by name or email', 'wdm_ld_group' ),
					'invalid_email'            => __( 'Please enter a valid email address', 'wdm_ld_group' ),
					'invalid_number'           => __( 'Please enter digits', 'wdm_ld_group' ),
					'required_checkbox'        => __( 'Please check the checkbox', 'wdm_ld_group' ),
					'required_textarea'        => __( 'Please enter the text', 'wdm_ld_group' ),
					'length_menu'              => [ [ 10, 25, 50, -1 ], [ 10, 25, 50, 'All' ] ],
				]
			);

			wp_enqueue_media();
			wp_localize_script( 'wdm_remove_js', 'wdm_data', $data );

			wp_enqueue_script( 'wdm_remove_js' );
			wp_enqueue_script( 'snackbar_js' );

			wp_enqueue_style(
				'ldgr-select2-style',
				'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css',
				[],
				LD_GROUP_REGISTRATION_VERSION,
				false
			);

			// Enqueue select2 script.
			wp_enqueue_script(
				'ldgr-select2-script',
				'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js',
				[],
				LD_GROUP_REGISTRATION_VERSION,
				true
			);

			// Enqueue Re-Invite javascript.
			wp_enqueue_script(
				'wdm_reinvite_js',
				plugins_url(
					'js/reinvite.js',
					__DIR__
				),
				[ 'jquery', 'wdm_remove_js' ],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_enqueue_script(
				'ldgr_group_settings',
				plugins_url(
					'js/ldgr-group-settings.js',
					__DIR__
				),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_localize_script(
				'ldgr_group_settings',
				'ldgr_loc',
				[
					'ajax_url'                  => admin_url( 'admin-ajax.php' ),
					'invalid_group_name'        => sprintf( /* translators: Group Label. */ __( 'Please enter a valid %s name', 'wdm_ld_group' ), \LearnDash_Custom_Label::label_to_lower( 'group' ) ),
					'invalid_sub_group_name'    => sprintf( /* translators: Sub Group Label. */ __( 'Please enter a valid %s name less than 100 characters', 'wdm_ld_group' ), \LearnDash_Custom_Label::label_to_lower( 'subgroup' ) ),
					'invalid_group_id'          => sprintf( /* translators: Group Label. */ __( 'Some error occurred, %s id not found. Please refresh the page and try again', 'wdm_ld_group' ), \LearnDash_Custom_Label::label_to_lower( 'group' ) ),
					'invalid_sub_group_seats'   => sprintf( /* translators: Sub Group Label. */ __( 'Please enter valid number of seats for the %s', 'wdm_ld_group' ), \LearnDash_Custom_Label::label_to_lower( 'subgroup' ) ),
					'common_error'              => __( 'Some error occurred', 'wdm_ld_group' ),
					'group_limit'               => sprintf( /* translators: Group Labels. */ __( '%1$s seat limit exceeded from parent %2$s', 'wdm_ld_group' ), \LearnDash_Custom_Label::get_label( 'group' ), \LearnDash_Custom_Label::label_to_lower( 'group' ) ),
					'invalid_sub_group_courses' => sprintf( /* translators: Sub Group Label. */ __( 'Please select courses for your %s', 'wdm_instructor_role' ), \LearnDash_Custom_Label::label_to_lower( 'subgroup' ) ),
					'empty_group_limit'         => __( 'Please enter a valid number of seats', 'wdm_ld_group' ),
				]
			);
			wp_enqueue_script(
				'ldgr-dashboard-script',
				plugins_url(
					'../assets/js/dist/ldgr-dashboard.js',
					__DIR__
				),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);

			$dynamic_fields = get_option( 'ldgr_dynamic_fields', [] );

			$add_user_html = ldgr_get_template(
				plugin_dir_path( __DIR__ ) . 'templates/ldgr-group-users/ldgr-group-add-new-user-single.template.php',
				[
					'dynamic_fields' => $dynamic_fields,
				],
				1
			);

			wp_localize_script(
				'ldgr-dashboard-script',
				'ldgr_dashboard_loc',
				[
					'row_html' => $add_user_html,
				]
			);

			wp_enqueue_style(
				'ldgr-dashboard-style',
				plugins_url(
					'../assets/css/ldgr-dashboard.css',
					__DIR__
				),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);

			// Fetch color settings.
			$accent_color = get_option( 'ldgr_dashboard_accent_color' );
			if ( ! empty( $accent_color ) ) {
				$custom_css = "
				.ldgr-color, #wdm_groups_tab .tabs .current a{
					color : {$accent_color} !important;
				}
				#wdm_groups_tab .tabs .current a{
					border-bottom-color: {$accent_color} !important;
				}
				.ldgr-bg-color, .empty-bg .filled-bg{
					background-color : {$accent_color} !important;
				}
				";

				wp_add_inline_style( 'ldgr-dashboard-style', $custom_css );
			}
		}

		/**
		 * Update group details via ajax
		 *
		 * @since 3.2.0
		 */
		public function ajax_update_group_details() {
			if ( array_key_exists( 'action', $_POST ) && 'ldgr_update_group_details' == $_POST['action'] ) {
				$group_id   = intval( $_POST['group_id'] );
				$group_name = $_POST['group_name'];
				if ( empty( $group_id ) || empty( $group_name ) ) {
					echo json_encode(
						[
							'status'  => 'error',
							'message' => __( 'Group name or Group ID not found', 'wdm_ld_group' ),
						]
					);
					die();
				}

				$update = wp_update_post(
					[
						'ID'         => $group_id,
						'post_title' => $group_name,
					]
				);

				if ( array_key_exists( 'group_image_id', $_POST ) && ! empty( $_POST['group_image_id'] ) ) {
					set_post_thumbnail( $group_id, intval( $_POST['group_image_id'] ) );
				}

				if ( empty( $update ) ) {
					echo wp_json_encode(
						[
							'status'  => 'error',
							'message' => __( 'Group details could not be updated. Please try again later or contact admin', 'wdm_ld_group' ),
						]
					);
					die();
				}

				echo wp_json_encode(
					[
						'status'  => 'success',
						'message' => __( 'Group details updated successfully!!', 'wdm_ld_group' ),
					]
				);
			}
			die();
		}

		/**
		 * Remove group image via ajax
		 *
		 * @since 4.2.0
		 */
		public function ajax_ldgr_remove_group_image() {
			if ( array_key_exists( 'action', $_POST ) && 'ldgr_remove_group_image' == $_POST['action'] ) {
				$group_id = intval( $_POST['group_id'] );

				if ( empty( $group_id ) ) {
					echo json_encode(
						[
							'status'  => 'error',
							'message' => __( 'Some error occurred. Group ID not found', 'wdm_ld_group' ),
						]
					);
					die();
				}

				$update = delete_post_meta( $group_id, '_thumbnail_id' );

				if ( empty( $update ) ) {
					echo json_encode(
						[
							'status'  => 'error',
							'message' => __( 'Group image could not be updated. Please try again later or contact admin', 'wdm_ld_group' ),
						]
					);
					die();
				}

				echo json_encode(
					[
						'status'  => 'success',
						'message' => __( 'Group details updated successfully!!', 'wdm_ld_group' ),
					]
				);
			}
			die();
		}
		/**
		 * Display notification messages.
		 */
		public function show_notification_messages( $error_data, $success_data ) {
			if ( ! empty( $success_data ) ) {
				?>
				<div class = 'wdm-update-message'>
					<?php echo esc_html( $success_data ); ?>
				</div>
				<?php
			}

			if ( ! empty( $error_data ) ) {
				?>
				<div class = 'wdm-error-message'>
					<?php echo esc_html( $error_data ); ?>
				</div>
				<?php
			}
		}

		/**
		 * Show group registration page select wrapper
		 *
		 * @param int   $user_id         Current user id.
		 * @param array $group_ids     Array of group ids belongs to current logged in user.
		 */
		public function show_group_select_wrapper( $user_id, $group_ids, $sub_group_instance ) {
			// $group_ids = ldgr_get_leader_group_ids( $user_id );
			$args = [
				'user_id'                          => $user_id,
				'group_ids'                        => $group_ids,
				'Ld_Group_Registration_Groups'     => $this,
				'Ld_Group_Registration_Sub_Groups' => $sub_group_instance,
				// cspell:disable-next-line .
				'is_heirarchical'                  => learndash_is_groups_hierarchical_enabled(),
			];
			ldgr_get_template(
				plugin_dir_path( __DIR__ ) . 'templates/ldgr-group-users/ldgr-group-users-select-wrapper.template.php',
				$args,
				false
			);
		}

		/**
		 * Display group select box
		 *
		 * @param int   $group_id     ID of the group.
		 * @param array $group_ids  List of all groups.
		 * @param obj   $user_data    User data object of group leader.
		 */
		public function display_group_select_list_html( $group_id, $group_ids, $user_data ) {
			foreach ( $group_ids as $value ) {
				$demo_title  = get_post( $value );
				$group_title = $demo_title->post_title;
				$username    = $user_data->user_login;
				$title       = str_replace( $username . ' - ', '', $group_title );
				$group_id    = $this->get_selected_group_value( $group_id, $value );
				$title       = apply_filters( 'wdm_modify_ldgr_group_title', $title, $value );
				?>
					<option value="<?php echo esc_html( $value ); ?>" <?php selected( $value, $group_id ); ?>>
						<?php echo esc_html( $title ); ?>
					</option>
				<?php
			}
		}

		/**
		 * Show group registration tabs.
		 *
		 * @param int  $group_id             ID of the group.
		 * @param bool $need_to_restrict     Whether there is a need to restrict any content.
		 */
		public function show_group_registrations_tabs( $group_id, $need_to_restrict, $group_ids ) {
			ldgr_recalculate_group_seats( $group_id );
			$group_limit     = intval( get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true ) );
			$grp_limit_count = ( $group_limit < 0 ) ? 0 : $group_limit;
			$is_unlimited    = get_post_meta( $group_id, 'ldgr_unlimited_seats', 1 );

			// If unlimited seats but empty group limit then set group limit for pre-requisites.
			if ( $is_unlimited && $group_limit <= 0 ) {
				$group_limit = 1;
			}

			$ldgr_group_courses = get_option( 'ldgr_group_courses' );
			$group_courses      = [];

			if ( 'on' == $ldgr_group_courses ) {
				$group_courses = learndash_group_enrolled_courses( $group_id );
			}

			$sub_group_courses = learndash_group_enrolled_courses( $group_id );

			/**
			 * Filter the list of courses in the group on groups dashboard.
			 *
			 * @param array $group_courses  List of courses in the group.
			 * @param int $group_id         ID of the group.
			 *
			 * @since 4.1.5
			 */
			$group_courses = apply_filters( 'ldgr_filter_group_course_list', $group_courses, $group_id );

			/**
			 * Filter the list of users to be displayed on any of the groups dashboard tabs.
			 *
			 * @since 4.1.4
			 *
			 * @param array $users      List of group users to be displayed for the group.
			 * @param int   $group_id   ID of the current LD group.
			 */
			$users = apply_filters( 'ldgr_filter_tab_user_list', learndash_get_groups_user_ids( $group_id ), $group_id );

			$tab_headers = [
				[
					'title' => __( 'Enrolled Users', 'wdm_ld_group' ),
					'slug'  => 'wdm_enrolled_users_label',
					'icon'  => plugin_dir_url( __DIR__ ) . 'media/enrolled-users.png',
					'id'    => 1,
				],
				[
					'title' => __( 'Report', 'wdm_ld_group' ),
					'slug'  => 'wdm_ldgr_view_report_label',
					'icon'  => plugin_dir_url( __DIR__ ) . 'media/report.svg',
					'id'    => 2,
				],
			];

			$tab_contents = [
				[
					'id'       => 1,
					'active'   => true,
					'template' => plugin_dir_path(
						__DIR__
					) . 'templates/ldgr-group-users/tabs/enrolled-users-tab.template.php',
				],
				[
					'id'       => 2,
					'active'   => false,
					'template' => plugin_dir_path(
						__DIR__
					) . 'templates/ldgr-group-users/tabs/reports-tab.template.php',
				],
			];

			$sub_group_instance = Ld_Group_Registration_Sub_Groups::get_instance();
			$sub_groups         = $sub_group_instance->get_all_sub_group_ids( $group_id );
			/**
			 * Filter tab headers on the groups dashboard.
			 *
			 * @since 4.1.0
			 *
			 * @param array $tab_headers    Array of tab headers.
			 * @param int   $group_id       ID of the group.
			 */
			$tab_headers = apply_filters( 'ldgr_filter_group_registration_tab_headers', $tab_headers, $group_id );
			/**
			 * Filter tab contents on the groups dashboard.
			 *
			 * @since 4.1.0
			 *
			 * @param array $tab_contents   Array of tab contents.
			 * @param int   $group_id       ID of the group.
			 */
			$tab_contents    = apply_filters( 'ldgr_filter_group_registration_tab_contents', $tab_contents, $group_id );
			$is_sub_group    = $sub_group_instance->is_group_sub_group( $group_id );
			$parent_group_id = '';
			if ( $is_sub_group ) {
				$parent_group_id = $sub_group_instance->get_parent_group_sub_group( $group_id );
			}

			$clear_icon = plugin_dir_url( __DIR__ ) . 'media/clear.png';

			$total_group_limit = get_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, true );

			// Fetch color settings.
			$colors = [
				'banner' => get_option( 'ldgr_dashboard_banner_color' ),
				'accent' => get_option( 'ldgr_dashboard_accent_color' ),
			];

			$dynamic_fields = get_option( 'ldgr_dynamic_fields', [] );

			if ( 1 === count( $group_ids ) && empty( ldgr_get_sub_group_ids( $group_id ) ) ) {
				$single_group = true;
			} else {
				$single_group = false;
			}

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-group-users/ldgr-group-users-tabs.template.php',
				[
					'group_courses'      => $group_courses,
					'sub_group_courses'  => $sub_group_courses,
					'group_limit'        => $group_limit,
					'grp_limit_count'    => $grp_limit_count,
					'is_unlimited'       => $is_unlimited,
					'users'              => $users,
					'tab_headers'        => $tab_headers,
					'tab_contents'       => $tab_contents,
					'clear_icon'         => $clear_icon,
					'group_id'           => $group_id,
					'single_group'       => $single_group,
					'need_to_restrict'   => $need_to_restrict,
					'sub_groups'         => $sub_groups,
					'instance'           => $this,
					'is_sub_group'       => $is_sub_group,
					'parent_group_id'    => $parent_group_id,
					'colors'             => $colors,
					'total_group_limit'  => $total_group_limit,
					'dynamic_fields'     => $dynamic_fields,
					'is_fix_group_limit' => get_option( 'ldgr_group_limit' ),
				]
			);
		}

		/**
		 * Check if a tab has any pre-requisites or not on group registration page.
		 *
		 * @param array $tab_header     Details about the tab to check.
		 *
		 * @return boolean              False if not pre-req or tab required, true otherwise.
		 */
		public function not_required_tab( $tab_header ) {
			if ( ! array_key_exists( 'pre_req', $tab_header ) ) {
				return false;
			}

			$pre_req = true;

			$condition = $tab_header['pre_req'];

			switch ( $condition['check'] ) {
				case 'greater':
					if ( $condition['key'] > $condition['value'] ) {
						$pre_req = false;
					}
					break;

				case 'lesser':
					if ( $condition['key'] < $condition['value'] ) {
						$pre_req = false;
					}
					break;

				// cspell:disable-next-line .
				case 'equall':
					if ( $condition['key'] == $condition['value'] ) {
						$pre_req = false;
					}
					break;
			}

			return apply_filters( 'ldgr_filter_pre_requisite_tab_check', $pre_req, $tab_header );
		}

		/**
		 * Get selected group name
		 *
		 * @param int $group_id     ID of the group.
		 * @param obj $user_data    User details.
		 *
		 * @return string           Selected group name title.
		 */
		public function get_selected_group_name( $group_id, $user_data ) {
			if ( empty( $group_id ) ) {
				return '';
			}

			$group_title = filter_var( get_the_title( $group_id ), FILTER_SANITIZE_SPECIAL_CHARS );

			if ( empty( $user_data ) ) {
				return $group_title;
			}

			$group_title = str_replace( [ '&#38;#8211;', $user_data->user_login ], '', $group_title );

			return trim( $group_title );
		}

		/**
		 * Validate CSV file
		 *
		 * @param array $csv_file  CSV file details.
		 *
		 * @return boolean         True if valid CSV file, else false.
		 */
		public function check_if_valid_csv_file( $csv_file ) {
			$msg = '';

			$file_name = $csv_file['uploadcsv']['tmp_name'];

			$ext = pathinfo( $csv_file['uploadcsv']['name'], PATHINFO_EXTENSION );

			if ( '' == $file_name || null == $file_name ) {
				$msg = __( 'No files chosen to upload!', 'wdm_ld_group' );
			}
			if ( 'csv' != $ext ) {
				$msg = __( 'Only CSV file is allowed!', 'wdm_ld_group' );
			}

			return apply_filters( 'ldgr_filter_csv_file_validation', $msg, $csv_file );
		}

		/**
		 * Get CSV data
		 *
		 * @param array $csv_file       CSV file all details.
		 * @param int   $group_id       ID of the group.
		 * @param int   $step           Current batch processing step (only used in patch processing)
		 * @param int   $batch_length   Length of batch in batch processing (only used in patch processing)
		 *
		 * @return array             Extracted CSV file details.
		 */
		public function get_csv_data_list( $csv_file, $group_id, $step = 1, $batch_length = 0 ) {
			$file_name = $csv_file['uploadcsv']['tmp_name'];

			/**
			 * Added a filter to allow the user to change the element mapping in the CSV file.
			 *
			 * @since 4.3.4
			 */
			$csv_data_list = apply_filters(
				'ldgr_filter_csv_data_list_map',
				[
					'emails'      => [],
					'first_names' => [],
					'last_names'  => [],
				]
			);
			$column_count  = count( $csv_data_list );

			$group_limit  = get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true );
			$is_unlimited = get_post_meta( $group_id, 'ldgr_unlimited_seats', 1 );

			$allowed_columns  = apply_filters( 'ldgr_filter_allowed_csv_columns', $column_count, $file_name, $group_id );
			$required_columns = apply_filters( 'ldgr_filter_required_csv_columns', $column_count, $file_name, $group_id );
			$file             = fopen( $file_name, 'r' );
			$count            = 0;
			while ( ( $data = fgetcsv( $file, 1000, ',' ) ) !== false ) {
				if ( 0 == $count ) {
					++$count;
				} else {
					// @todo: Check if we can secure incoming CSV in any way here.
					// $data = array_map( 'utf8_encode', $data );
					++$count;

					// Handle dynamic required/not required data

					if ( count( $data ) != $required_columns && count( $data ) != $allowed_columns ) {
						if ( $allowed_columns > 3 ) {
							$msg = __( 'Value is not in a proper format, check required dynamic fields!', 'wdm_ld_group' );
						} else {
							$msg = __( 'Value is not in a proper format, check sample file for format!', 'wdm_ld_group' );
						}
						return [ 'error' => $msg ];
					}

					if ( ! $this->valid_csv_data( $data, $allowed_columns, $required_columns ) ) {
						if ( $allowed_columns > 3 ) {
							$msg = __( 'One of the required dynamic field value or its field type value is missing!', 'wdm_ld_group' );
						} else {
							$msg = __( 'One of the required value is missing!', 'wdm_ld_group' );
						}
						return [ 'error' => $msg ];
					}

					$csv_data_list['first_names'][] = trim( $data[0] );
					$csv_data_list['last_names'][]  = trim( $data[1] );
					$csv_data_list['emails'][]      = trim( $data[2] );

					$csv_data_list = apply_filters( 'ldgr_filter_csv_data_list', $csv_data_list, $data, $group_id );
				}
			}
			fclose( $file );

			$csv_length = count( $csv_data_list['emails'] );

			if ( $step > 1 ) {
				$enrolled_users_count = ( $step - 1 ) * $batch_length;
				$csv_length           = intval( $csv_length - $enrolled_users_count );
			}

			if ( $csv_length > $group_limit && ! $is_unlimited ) {
				$msg = sprintf(
					/* translators: 1. Group label 2. Group Limit */
					__( 'Warning: %1$s limit reached. You can only enroll %2$d more users!!', 'wdm_ld_group' ),
					\LearnDash_Custom_Label::get_label( 'group' ),
					$group_limit
				);
				return [ 'error' => $msg ];
			}

			return $csv_data_list;
		}

		/**
		 * Validate CSV file data
		 *
		 * @param array $csv_data       CSV data to be validated.
		 * @param int   $allowed_columns  Allowed columns in the CSV to be read.
		 *
		 * @return boolean              True if CSV data valid, false otherwise.
		 */
		public function valid_csv_data( $csv_data, $allowed_columns, $required_columns ) {
			$column   = 0;
			$is_valid = true;

			while ( $column < $allowed_columns ) {
				if ( ! isset( $csv_data[ $column ] ) || empty( $csv_data[ $column ] ) ) {
					$is_valid = false;
					break;
				}
				++$column;
			}

			$is_valid = apply_filters( 'ldgr_filter_valid_csv_data', $is_valid, $csv_data, $allowed_columns, $required_columns );
			return $is_valid;
		}

		/**
		 * Send bulk upload emails
		 *
		 * @param array<string, mixed>[] $all_emails_list     List of all emails to send emails to.
		 * @param int                    $group_id            ID of the group.
		 * @param array<mixed,string>    $final_csv_data      Final CSV data.
		 */
		public function send_bulk_upload_emails( $all_emails_list, $group_id, $final_csv_data ) {
			global $success_data;

			if ( empty( $all_emails_list ) ) {
				return;
			}

			$all_emails_list = apply_filters( 'ldgr_filter_enroll_user_emails', $all_emails_list, $group_id, $final_csv_data );

			foreach ( $all_emails_list as $user_id => $details ) {
				if ( $details['new'] ) {
					if ( apply_filters( 'is_ldgr_default_user_add_action', true ) ) {
						$success_data = $this->new_user_registration(
							$user_id,
							$details['user_data']['first_name'],
							$details['user_data']['last_name'],
							$details['user_data']['user_email'],
							$details['user_data']['user_pass'],
							$details['courses'],
							$details['lead_user'],
							$details['group_id']
						);
					}
				} else {
					ldgr_send_group_mails(
						$details['email'],
						$details['subject'],
						$details['body'],
						[],
						[],
						[
							'email_type' => 'WDM_U_ADD_GR_BODY',
							'group_id'   => $group_id,
						]
					);
				}
				do_action( 'ldgr_action_new_user_enroll', $user_id, $details );
			}
		}

		/**
		 * Display group featured image on groups dashboard.
		 *
		 * @param int $group_id     ID of the group
		 *
		 * @since 4.2.0
		 */
		public function display_group_image( $group_id, $width ) {
			if ( empty( $group_id ) ) {
				return;
			}

			// Fetch image url.
			if ( has_post_thumbnail( $group_id ) ) {
				$group_image = esc_url( get_the_post_thumbnail_url( $group_id ) );
			} else {
				$def_group_image = get_option( 'ldgr_default_group_image' );
				if ( $image = wp_get_attachment_image_src( $def_group_image ) ) {
					$group_image = esc_url( $image[0] );
				} else {
					$group_image = esc_url( plugins_url( 'assets/images/no_image.png', dirname( __DIR__ ) ) );
				}
			}

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-group-image.template.php',
				[
					'src'      => $group_image,
					'width'    => $width,
					'group_id' => $group_id,
				]
			);
		}

		/**
		 * Restrict group leader upload privileges to only view user uploaded media.
		 *
		 * @since 4.2.2
		 *
		 * @param array $query  An array of query variables.
		 *
		 * @return array        Updated array of query variables.
		 */
		public function restrict_group_leader_upload_privileges( $query ) {
			$user_id = get_current_user_id();

			if ( function_exists( 'learndash_is_group_leader_user' ) && learndash_is_group_leader_user( $user_id ) ) {
				$query['author'] = $user_id;
			}

			/**
			 * Filter query for restricting group leader privileges for image uploads.
			 *
			 * @since 4.2.2
			 *
			 * @param object $query     WP_Query object to be filtered.
			 */
			return apply_filters( 'restrict_group_leader_upload_privileges', $query );
		}
	}
}
