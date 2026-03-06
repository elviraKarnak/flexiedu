<?php
/**
 * LearnDash Certificates API Handler Module
 *
 * @since 5.8.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Api;

use InstructorRole\Modules\Classes\Instructor_Role_Shortcodes_TinyMCE;
use WP_Rest_Server;
use WP_Error;
use WP_REST_Posts_Controller;
use WP_Post, WP_Query;
use WP_User;
use WP_User_Query;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Certificates_Api_Handler' ) ) {
	/**
	 * Class Instructor Role Certificates Api Handler
	 */
	class Instructor_Role_Certificates_Api_Handler extends Instructor_Role_Dashboard_Block_Api_Handler {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 5.8.0
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 5.8.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Register custom endpoints
		 *
		 * @since 5.8.0
		 */
		public function register_custom_endpoints() {
			// List and Create Certificates.
			register_rest_route(
				$this->namespace,
				'/certificates/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_certificates' ],
						'permission_callback' => [ $this, 'get_certificates_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'create_certificate' ],
						'permission_callback' => [ $this, 'get_certificates_permissions_check' ],
					],
				]
			);

			// Trash certificates.
			register_rest_route(
				$this->namespace,
				'/certificates/trash',
				[
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => [ $this, 'trash_certificates' ],
						'permission_callback' => [ $this, 'trash_certificates_permissions_check' ],
					],
				]
			);

			// Restore certificates.
			register_rest_route(
				$this->namespace,
				'/certificates/restore',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'restore_certificates' ],
						'permission_callback' => [ $this, 'restore_certificates_permissions_check' ],
					],
				]
			);

			// View and Edit Certificates.
			register_rest_route(
				$this->namespace,
				'/certificates/(?P<id>[\d]+)',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_certificate' ],
						'permission_callback' => [ $this, 'get_certificates_permissions_check' ],
					],
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'update_certificate' ],
						'permission_callback' => [ $this, 'get_certificates_permissions_check' ],
					],
				]
			);

			// Get Certificates filters.
			register_rest_route(
				$this->namespace,
				'/certificates/filters',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_certificate_filters' ],
						'permission_callback' => [ $this, 'get_certificate_filters_permissions_check' ],
					],
				]
			);
		}

		/**
		 * Get certificates permissions check
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_certificates_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get instructor certificates list page data
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_certificates( $request ) {
			$data               = [];
			$found_certificates = [];
			$users              = [];
			$current_user_id    = get_current_user_id();

			$parameters = shortcode_atts(
				[
					'search'   => '',
					'page'     => 1,
					'per_page' => 9,
					'status'   => 'any',
					'month'    => '',
				],
				$request->get_params()
			);

			// Default query parameters.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'certificate' ),
				'posts_per_page' => $parameters['per_page'],
				'post_status'    => $parameters['status'],
				'paged'          => $parameters['page'],
				'order_by'       => 'date',
				'order'          => 'DESC',
			];

			// For instructor user.
			if ( wdm_is_instructor( $current_user_id ) ) {
				$args['author'] = $current_user_id;
			}

			// Search certificates.
			if ( isset( $parameters['search'] ) && ! empty( $parameters['search'] ) ) {
				$args['s'] = trim( $parameters['search'] );
			}

			// Filter by status.
			if ( 'any' !== $parameters['status'] ) {
				switch ( $parameters['status'] ) {
					case 'publish':
					case 'draft':
					case 'trash':
						$args['post_status'] = $parameters['status'];
						break;
					default:
						$args['post_status'] = 'any';
						break;
				}
			}

			// Filter by month.
			if ( ! empty( $parameters['month'] ) ) {
				$args['m'] = trim( $parameters['month'] );
			}

			// Find requested certificates.
			$certificates_query = new WP_Query( $args );

			foreach ( $certificates_query->posts as $certificate ) {
				if ( ! array_key_exists( $certificate->post_author, $users ) ) {
					$users[ $certificate->post_author ] = get_userdata( $certificate->post_author );
				}

				$found_certificates[] = $this->get_list_single( 'certificate', $certificate, $users );
			}

			// Final data.
			$data = [
				'posts'        => $found_certificates,
				'posts_count'  => $certificates_query->post_count,
				'total_posts'  => $certificates_query->found_posts,
				'max_page_num' => $certificates_query->max_num_pages,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Trash certificates permissions check
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function trash_certificates_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Trash certificates
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function trash_certificates( $request ) {
			$data             = [];
			$user_id          = get_current_user_id();
			$delete           = false;
			$query_parameters = $request->get_params();

			// Get the certificate(s) to be trashed.
			$trash_ids = [];

			if ( isset( $query_parameters['certificates'] ) ) {
				$trash_ids = explode( ',', $query_parameters['certificates'] );
			}

			// Check whether to trash or permanently delete.
			if ( isset( $query_parameters['action'] ) && 'delete' === $query_parameters['action'] ) {
				$delete = true;
			}

			foreach ( $trash_ids as $certificate_id ) {
				$certificate = get_post( $certificate_id );

				// Check if valid certificate.
				if ( empty( $certificate ) || ! $certificate instanceof WP_Post || learndash_get_post_type_slug( 'certificate' ) !== $certificate->post_type ) {
					continue;
				}

				// Verify if user is certificate author or admin.
				if ( current_user_can( 'manage_options' ) || ( intval( $certificate->post_author ) === $user_id ) ) {
					// Trash or delete certificate.
					if ( ! $delete ) {
						$trashed_certificate = wp_trash_post( $certificate_id );
						$data['trashed'][]   = $trashed_certificate;
					} else {
						$deleted_certificate = wp_delete_post( $certificate_id, $delete );
						$data['deleted'][]   = $deleted_certificate;
					}
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Restore certificates permissions check
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function restore_certificates_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Restore trashed certificates.
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function restore_certificates( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			$parameters = $request->get_body_params();

			// If empty get all params.
			if ( empty( $parameters ) ) {
				$parameters = $request->get_params();
			}

			// Get the certificate(s) to be restored.
			$restore_ids = [];

			if ( isset( $parameters['certificates'] ) ) {
				$restore_ids = explode( ',', $parameters['certificates'] );
			}

			foreach ( $restore_ids as $certificate_id ) {
				$certificate = get_post( $certificate_id );

				// Check if valid trashed certificate.
				if ( empty( $certificate ) || ! $certificate instanceof WP_Post || learndash_get_post_type_slug( 'certificate' ) !== $certificate->post_type || 'trash' !== $certificate->post_status ) {
					continue;
				}

				// Verify if user is certificate author or admin.
				if ( current_user_can( 'manage_options' ) || ( intval( $certificate->post_author ) === $user_id ) ) {
					// Restore certificate.
					$restored_certificate = wp_untrash_post( $certificate_id );
					if ( ! empty( $restored_certificate ) ) {
						$data['restored'][] = $restored_certificate;
					}
				}
			}

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Create Certificate
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function create_certificate( $request ) {
			$thumbnail_id                  = filter_var( $request['featured_media'], FILTER_VALIDATE_INT );
			$post_title                    = sanitize_text_field( $request['title'] );
			$post_content                  = sanitize_text_field( $request['content'] );
			$post_status                   = sanitize_text_field( $request['post_status'] );
			$post_date                     = sanitize_text_field( $request['date'] );
			$post_password                 = sanitize_text_field( $request['password'] );
			$learndash_certificate_options = [];
			$data                          = [];

			if ( empty( $post_title ) ) {
				return new WP_Error( 'ir_rest_certificate_error', esc_html__( 'Please provide certificate title.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			if ( 'publish' === $post_status && ! empty( $post_status ) ) {
				$post_status = 'publish';
			} elseif ( 'private' === $post_status && ! empty( $post_status ) ) {
				$post_status = 'private';
			} else {
				$post_status = 'draft';
			}

			if ( empty( $post_date ) || 'Immediately' === $post_date ) {
				$post_date = '';
			}

			if ( empty( $post_password ) ) {
				$post_password = '';
			}

			$new_post = [
				'post_title'    => $post_title,
				'post_content'  => $post_content,
				'post_status'   => $post_status,
				'post_author'   => get_current_user_id(),
				'post_type'     => learndash_get_post_type_slug( 'certificate' ),
				'post_date'     => $post_date,
				'post_password' => $post_password,
			];

			$post_id = wp_insert_post( $new_post );

			if ( false !== $post_id ) {
				if ( isset( $request['pdf_page_format'] ) && ! empty( $request['pdf_page_format'] ) ) {
					$learndash_certificate_options['pdf_page_format'] = esc_attr( $request['pdf_page_format'] );
				} else {
					$learndash_certificate_options['pdf_page_format'] = 'LETTER';
				}

				if ( isset( $request['pdf_page_orientation'] ) && ! empty( $request['pdf_page_orientation'] ) ) {
					$learndash_certificate_options['pdf_page_orientation'] = esc_attr( $request['pdf_page_orientation'] );
				} else {
					$learndash_certificate_options['pdf_page_orientation'] = 'L';
				}

				if ( isset( $thumbnail_id ) ) {
					set_post_thumbnail( $post_id, $thumbnail_id );
				}

				update_post_meta( $post_id, 'learndash_certificate_options', $learndash_certificate_options );
				$post                      = get_post( $post_id );
				$ld_certificate_builder_on = ( '1' === get_post_meta( filter_var( $request['id'], FILTER_VALIDATE_INT ), 'ld_certificate_builder_on', true ) ) ? true : false;
				$author_details            = get_userdata( $post->post_author );

				$data = [
					'id'                        => $post->ID,
					'title'                     => html_entity_decode( $post->post_title ),
					'author_url'                => get_avatar_url( $post->post_author ),
					'author_name'               => $author_details->display_name,
					'author_email'              => $author_details->user_email,
					'content'                   => $post->post_content,
					'date'                      => $post->post_date,
					'status'                    => $post->post_status,
					'view_url'                  => get_the_permalink( $post ),
					'slug'                      => $post->post_name,
					'featured_image'            => get_post_thumbnail_id( $post->ID ),
					'featured_image_url'        => wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ),
					'pdf_page_format'           => $learndash_certificate_options['pdf_page_format'],
					'pdf_page_orientation'      => $learndash_certificate_options['pdf_page_orientation'],
					'ld_certificate_builder_on' => $ld_certificate_builder_on,
					'is_password_protected'     => post_password_required( $post->ID ),
					'password'                  => $post->post_password,
				];

				$response = rest_ensure_response( $data );
				$response->set_status( 200 );
				return $response;
			} else {
				// Error Response.
				return new WP_Error( 'ir_rest_certificate_error', esc_html__( 'Certificate post creation failed.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}
		}

		/**
		 * Get a Certificate
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function get_certificate( $request ) {
			$data = [];

			$post = get_post( $request['id'] );

			// Check if valid WP_Post object.
			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			$current_user_id = get_current_user_id();

			// If instructor user, check for access.
			if ( wdm_is_instructor( $current_user_id ) ) {
				if ( intval( $post->post_author ) !== $current_user_id ) {
					return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}

			if ( class_exists( 'LearnDash_Certificate_Builder\Bootstrap' ) ) {
				$ld_certificate_builder_on = ( '1' === get_post_meta( filter_var( $request['id'], FILTER_VALIDATE_INT ), 'ld_certificate_builder_on', true ) ) ? true : false;
			} else {
				$ld_certificate_builder_on = false;
			}

			$author_details                = get_userdata( $post->post_author );
			$learndash_certificate_options = get_post_meta( $post->ID, 'learndash_certificate_options', true );

			$data = [
				'id'                        => $post->ID,
				'title'                     => html_entity_decode( $post->post_title ),
				'author_url'                => get_avatar_url( $post->post_author ),
				'author_name'               => $author_details->display_name,
				'author_email'              => $author_details->user_email,
				'content'                   => $post->post_content,
				'date'                      => $post->post_date,
				'status'                    => $post->post_status,
				'view_url'                  => get_the_permalink( $post ),
				'edit_link'                 => get_edit_post_link( $post, 'edit' ),
				'slug'                      => $post->post_name,
				'featured_image'            => get_post_thumbnail_id( $post->ID ),
				'featured_image_url'        => wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ),
				'pdf_page_format'           => empty( $learndash_certificate_options['pdf_page_format'] ) ? '' : $learndash_certificate_options['pdf_page_format'],
				'pdf_page_orientation'      => empty( $learndash_certificate_options['pdf_page_orientation'] ) ? '' : $learndash_certificate_options['pdf_page_orientation'],
				'ld_certificate_builder_on' => $ld_certificate_builder_on,
				'is_password_protected'     => post_password_required( $post->ID ),
				'password'                  => $post->post_password,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Update a Certificate
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function update_certificate( $request ) {
			$post                          = get_post( $request['id'] );
			$thumbnail_id                  = filter_var( $request['featured_media'], FILTER_VALIDATE_INT );
			$post_title                    = sanitize_text_field( $request['title'] );
			$post_content                  = sanitize_text_field( $request['content'] );
			$post_status                   = sanitize_text_field( $request['post_status'] );
			$post_date                     = sanitize_text_field( $request['date'] );
			$post_password                 = sanitize_text_field( $request['password'] );
			$learndash_certificate_options = [];
			$current_user_id               = get_current_user_id();
			// Check if valid WP_Post object.
			if ( empty( $post ) || ! $post instanceof WP_Post ) {
				return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Invalid post ID.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
			}

			// If instructor user, check for access.
			if ( wdm_is_instructor( $current_user_id ) ) {
				if ( intval( $post->post_author ) !== $current_user_id ) {
					return new WP_Error( 'ir_rest_invalid_post_id', esc_html__( 'Sorry but you do not have access to this resource.', 'wdm_instructor_role' ), [ 'status' => 401 ] );
				}
			}

			if ( 'publish' === $post_status && ! empty( $post_status ) ) {
				$post_status = 'publish';
			} elseif ( 'private' === $post_status && ! empty( $post_status ) ) {
				$post_status = 'private';
			} else {
				$post_status = 'draft';
			}

			if ( empty( $post_date ) ) {
				$post_date = '';
			}

			if ( empty( $post_password ) ) {
				$post_password = '';
			}

			if ( isset( $request['pdf_page_format'] ) && ! empty( $request['pdf_page_format'] ) ) {
				$learndash_certificate_options['pdf_page_format'] = esc_attr( $request['pdf_page_format'] );
			} else {
				$learndash_certificate_options['pdf_page_format'] = 'LETTER';
			}

			if ( isset( $request['pdf_page_orientation'] ) && ! empty( $request['pdf_page_orientation'] ) ) {
				$learndash_certificate_options['pdf_page_orientation'] = esc_attr( $request['pdf_page_orientation'] );
			} else {
				$learndash_certificate_options['pdf_page_orientation'] = 'L';
			}

			wp_update_post(
				[
					'ID'            => $post->ID,
					'post_status'   => $post_status,
					'post_title'    => $post_title,
					'post_content'  => $post_content,
					'post_date'     => $post_date,
					'post_password' => $post_password,
				]
			);

			if ( isset( $thumbnail_id ) ) {
				set_post_thumbnail( $post->ID, $thumbnail_id );
			}

			if ( class_exists( 'LearnDash_Certificate_Builder\Bootstrap' ) ) {
				$ld_certificate_builder_on = ( '1' === get_post_meta( filter_var( $request['id'], FILTER_VALIDATE_INT ), 'ld_certificate_builder_on', true ) ) ? true : false;
			} else {
				$ld_certificate_builder_on = false;
			}

			update_post_meta( $post->ID, 'learndash_certificate_options', $learndash_certificate_options );
			$updated_post   = get_post( $request['id'] );
			$author_details = get_userdata( $updated_post->post_author );

			$data = [
				'id'                        => $updated_post->ID,
				'title'                     => html_entity_decode( $updated_post->post_title ),
				'author_url'                => get_avatar_url( $updated_post->post_author ),
				'author_name'               => $author_details->display_name,
				'author_email'              => $author_details->user_email,
				'content'                   => $updated_post->post_content,
				'date'                      => $updated_post->post_date,
				'status'                    => $updated_post->post_status,
				'view_url'                  => get_the_permalink( $updated_post ),
				'slug'                      => $updated_post->post_name,
				'featured_image'            => get_post_thumbnail_id( $updated_post->ID ),
				'featured_image_url'        => wp_get_attachment_url( get_post_thumbnail_id( $updated_post->ID ) ),
				'pdf_page_format'           => $learndash_certificate_options['pdf_page_format'],
				'pdf_page_orientation'      => $learndash_certificate_options['pdf_page_orientation'],
				'ld_certificate_builder_on' => $ld_certificate_builder_on,
				'is_password_protected'     => post_password_required( $updated_post->ID ),
				'password'                  => $updated_post->post_password,
			];

			$response = rest_ensure_response( $data );
			$response->set_status( 200 );
			return $response;
		}

		/**
		 * Get Certificate Filters
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function get_certificate_filters( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			// Get date filters data.
			$args = [
				'post_type'      => learndash_get_post_type_slug( 'certificate' ),
				'posts_per_page' => -1,
				'post_status'    => 'any',
			];

			if ( ! current_user_can( 'manage_options' ) ) {
				$args['author'] = $user_id;
			}

			$certificate_list = new WP_Query( $args );

			$date_filter   = [];
			$date_keys     = [];
			$date_filter[] = [
				'value' => '',
				'label' => __( 'All dates', 'wdm_instructor_role' ),
			];

			foreach ( $certificate_list->posts as $single_certificate ) {
				$certificate_date = strtotime( $single_certificate->post_date );
				$key              = gmdate( 'Ym', $certificate_date );
				if ( ! in_array( $key, $date_keys ) ) {
					$date_filter[] = [
						'value' => gmdate( 'Ym', $certificate_date ),
						'label' => gmdate( 'F Y', $certificate_date ),
					];
					$date_keys[]   = gmdate( 'Ym', $certificate_date );
				}
			}

			$data = [
				'date_filter' => $date_filter,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;       }

		/**
		 * Certificates filter permissions check
		 *
		 * @since 5.8.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_certificate_filters_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Add additional certificate details to the post on the listing page.
		 *
		 * @since 5.8.0
		 *
		 * @param array   $data   Array of returned post data.
		 * @param WP_Post $post   Post object.
		 * @param string  $type   Type of the post.
		 * @param array   $users  Array of user details.
		 */
		public function add_additional_certificate_details( $data, $post, $type, $users ) {
			if ( function_exists( 'learndash_get_post_type_slug' ) && 'certificate' === $type ) {
				$courses = learndash_certificate_get_used_by( $post->ID, learndash_get_post_type_slug( 'course' ) );
				$groups  = learndash_certificate_get_used_by( $post->ID, learndash_get_post_type_slug( 'group' ) );
				$quizzes = learndash_certificate_get_used_by( $post->ID, learndash_get_post_type_slug( 'quiz' ) );

				if ( class_exists( 'LearnDash_Certificate_Builder\Bootstrap' ) && ( '1' === get_post_meta( $post->ID, 'ld_certificate_builder_on', true ) ) ) {
					$data['preview'] = get_preview_post_link( $post );
				} else {
					$data['preview'] = false;
				}

				$data['courses']      = $courses;
				$data['course_count'] = count( $courses );
				$data['groups']       = $groups;
				$data['groups_count'] = count( $groups );
				$data['quizzes']      = $quizzes;
				$data['quiz_count']   = count( $quizzes );
			}
			return $data;
		}

		/**
		 * Initialize TinyMCE Editor.
		 *
		 * @since 5.8.0
		 */
		public function initialize_editor() {
			// Return if LD not active.
			if ( ! class_exists( 'LearnDash_Shortcodes_TinyMCE' ) ) {
				return;
			}

			if ( ! class_exists( 'Instructor_Role_Shortcodes_TinyMCE' ) ) {
				/**
				 * The class responsible for defining all actions to control the tinymce shortcode related functionalities.
				 */
				require_once INSTRUCTOR_ROLE_ABSPATH . 'modules/classes/class-instructor-role-shortcodes-tinymce.php';
			}

			$shortcodes_tinymce_handler = Instructor_Role_Shortcodes_TinyMCE::get_instance();
			add_action( 'wp_enqueue_scripts', [ $shortcodes_tinymce_handler, 'load_admin_scripts' ] );
			add_action( 'wp_print_footer_scripts', [ $shortcodes_tinymce_handler, 'qt_button_script' ] );
		}
	}
}
