<?php
/**
 * Woocommerce Rest API Handler Module
 *
 * @since 5.1.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Api;

use WP_Rest_Server;
use WP_Error;
use WP_REST_Posts_Controller;
use WP_Post, WP_Query;
use WP_User;
use WP_User_Query;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Woocommerce_Api_Handler' ) ) {
	/**
	 * Class Instructor Role Woocommerce Api Handler
	 */
	class Instructor_Role_Woocommerce_Api_Handler extends Instructor_Role_Dashboard_Block_Api_Handler {
		/**
		 * Singleton instance of this class
		 *
		 * @var object  $instance
		 *
		 * @since 5.1.0
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 5.1.0
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
		 * @since 5.1.0
		 */
		public function register_custom_endpoints() {
			// List Products.
			register_rest_route(
				$this->namespace,
				'/products/',
				[
					[
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => [ $this, 'get_products' ],
						'permission_callback' => [ $this, 'get_products_permissions_check' ],
					],
				]
			);

			// Trash products.
			register_rest_route(
				$this->namespace,
				'/products/trash',
				[
					[
						'methods'             => WP_REST_Server::DELETABLE,
						'callback'            => [ $this, 'trash_products' ],
						'permission_callback' => [ $this, 'trash_products_permissions_check' ],
					],
				]
			);

			// Trash products.
			register_rest_route(
				$this->namespace,
				'/products/restore',
				[
					[
						'methods'             => WP_REST_Server::EDITABLE,
						'callback'            => [ $this, 'restore_products' ],
						'permission_callback' => [ $this, 'restore_products_permissions_check' ],
					],
				]
			);
		}

		/**
		 * Get products permissions check
		 *
		 * @since 5.1.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_products_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Get instructor products list page data
		 *
		 * @since 5.1.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function get_products( $request ) {
			$data            = [];
			$found_products  = [];
			$users           = [];
			$current_user_id = get_current_user_id();

			$parameters = shortcode_atts(
				[
					'search'     => '',
					'page'       => 1,
					'per_page'   => 9,
					'status'     => 'any',
					'month'      => '',
					'price_type' => '',
					'categories' => [],
					'tags'       => [],
				],
				$request->get_params()
			);

			// Default query parameters.
			$args = [
				'post_type'      => 'product',
				'posts_per_page' => $parameters['per_page'],
				'post_status'    => 'any',
				'paged'          => $parameters['page'],
				'order_by'       => 'ID',
			];

			// For instructor user.
			if ( wdm_is_instructor( $current_user_id ) ) {
				$args['author'] = $current_user_id;
			}

			// Search products.
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
				}
			}

			// Filter by Product Categories.
			if ( ! empty( $parameters['categories'] ) ) {
				$categories = explode( ',', $parameters['categories'] );

				if ( ! is_array( $categories ) ) {
					$categories = [ $categories ];
				}

				$args['tax_query'] = [
					[
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => $categories,
					],
				];
			}

			// Filter by Product Tags.
			if ( ! empty( $parameters['tags'] ) ) {
				$tags = explode( ',', $parameters['tags'] );
				if ( ! is_array( $tags ) ) {
					$tags = [ trim( $tags ) ];
				}

				$args['tax_query'] = [
					[
						'taxonomy' => 'product_tag',
						'field'    => 'term_id',
						'terms'    => $tags,
					],
				];
			}

			// Find requested products.
			$products_query = new WP_Query( $args );

			foreach ( $products_query->posts as $product ) {
				if ( ! array_key_exists( $product->post_author, $users ) ) {
					$users[ $product->post_author ] = get_userdata( $product->post_author );
				}

				$found_products[] = $this->get_list_single( 'product', $product, $users );
			}

			// Final data.
			$data = [
				'posts'        => $found_products,
				'posts_count'  => $products_query->post_count,
				'total_posts'  => $products_query->found_posts,
				'max_page_num' => $products_query->max_num_pages,
			];

			// Create the response object.
			$response = rest_ensure_response( $data );

			// Add a custom status code.
			$response->set_status( 200 );

			return $response;
		}

		/**
		 * Add additional product details to the product on the listing page.
		 *
		 * @since 5.1.0
		 *
		 * @param array   $data   Array of returned post data.
		 * @param WP_Post $post   Post object.
		 * @param string  $type   Type of the post.
		 * @param array   $users  Array of user details.
		 */
		public function add_additional_product_details( $data, $post, $type, $users ) {
			if ( 'product' === $type && function_exists( 'wc_get_product' ) ) {
				$product             = wc_get_product( $post->ID );
				$data['is_featured'] = $product->is_featured();
				$data['price']       = $product->get_price();
				$data['price_html']  = $product->get_price_html();
				$data['currency']    = get_woocommerce_currency_symbol();

				if ( array_key_exists( 'edit_url', $data ) ) {
					unset( $data['edit_url'] );
				}

				if ( array_key_exists( 'clone_url', $data ) ) {
					unset( $data['clone_url'] );
				}

				$pending_review_products = maybe_unserialize( get_option( 'ir_pending_review_notices', [] ) );

				$data['review_pending'] = false;
				if ( WDMIR_REVIEW_PRODUCT && in_array( $post->ID, $pending_review_products, true ) ) {
					$data['review_pending'] = true;
				}
			}
			return $data;
		}

		/**
		 * Trash products permissions check
		 *
		 * @since 5.1.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function trash_products_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Trash products
		 *
		 * @since 5.1.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function trash_products( $request ) {
			$data             = [];
			$user_id          = get_current_user_id();
			$delete           = false;
			$query_parameters = $request->get_params();

			// Get the product(s) to be trashed.
			$trash_ids = [];

			if ( isset( $query_parameters['products'] ) ) {
				$trash_ids = explode( ',', $query_parameters['products'] );
			}

			// Check whether to trash or permanently delete.
			if ( isset( $query_parameters['action'] ) && 'delete' === $query_parameters['action'] ) {
				$delete = true;
			}

			foreach ( $trash_ids as $product_id ) {
				$product = get_post( $product_id );

				// Check if valid product.
				if ( empty( $product ) || ! $product instanceof WP_Post || 'product' !== $product->post_type ) {
					continue;
				}

				// Verify if user is product author or admin.
				if ( current_user_can( 'manage_options' ) || ( intval( $product->post_author ) === $user_id ) ) {
					// Trash or delete product.
					if ( ! $delete ) {
						$trashed_product   = wp_trash_post( $product_id );
						$data['trashed'][] = $trashed_product;
					} else {
						$deleted_product   = wp_delete_post( $product_id, $delete );
						$data['deleted'][] = $deleted_product;
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
		 * Restore products permissions check
		 *
		 * @since 5.1.0
		 *
		 * @param WP_REST_Request $request WP_REST_Request instance.
		 */
		public function restore_products_permissions_check( $request ) {
			return $this->instructor_request_permission_check( $request );
		}

		/**
		 * Restore trashed products.
		 *
		 * @since 5.1.0
		 *
		 * @param WP_REST_Request $request  WP_REST_Request instance.
		 */
		public function restore_products( $request ) {
			$data    = [];
			$user_id = get_current_user_id();

			$parameters = $request->get_body_params();

			// If empty get all params.
			if ( empty( $parameters ) ) {
				$parameters = $request->get_params();
			}

			// Get the product(s) to be restored.
			$restore_ids = [];

			if ( isset( $parameters['products'] ) ) {
				$restore_ids = explode( ',', $parameters['products'] );
			}

			foreach ( $restore_ids as $product_id ) {
				$product = get_post( $product_id );

				// Check if valid trashed product.
				if ( empty( $product ) || ! $product instanceof WP_Post || 'product' !== $product->post_type || 'trash' !== $product->post_status ) {
					continue;
				}

				// Verify if user is product author or admin.
				if ( current_user_can( 'manage_options' ) || ( intval( $product->post_author ) === $user_id ) ) {
					// Restore product.
					$restored_product = wp_untrash_post( $product_id );
					if ( ! empty( $restored_product ) ) {
						$data['restored'][] = $restored_product;
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
		 * Add featured image check to products list response.
		 *
		 * @since 5.1.0
		 *
		 * @param WP_REST_Response $response The response object.
		 * @param WC_Data          $object   Object data.
		 * @param WP_REST_Request  $request  Request object.
		 *
		 * @return WP_REST_Response          Updated response object
		 */
		public function add_featured_image_check( $response, $object, $request ) {
			// Check whether Course Product type.
			if ( $object instanceof \WC_Product_Course && 'course' === $object->get_type() ) {
				// Add featured image check to response.
				$product_data                       = $response->get_data();
				$product_data['has_featured_image'] = has_post_thumbnail( $object->get_id() );

				// Add product review check.
				$product_data['review_pending'] = false;
				if ( WDMIR_REVIEW_PRODUCT ) {
					$pending_review_products = maybe_unserialize( get_option( 'ir_pending_review_notices', [] ) );
					if ( in_array( $object->get_id(), $pending_review_products, true ) ) {
						$product_data['review_pending'] = true;
					}
				}
				$response->set_data( $product_data );
			}
			return $response;
		}

		/**
		 * Handle empty featured image save from the frontend dashboard.
		 *
		 * @param WC_Data         $product  Product object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating If is creating a new object.
		 *
		 * @return WC_Data                  Updated Product Object.
		 */
		public function handle_empty_featured_image_save( $product, $request, $creating ) {
			$product_data = $request->get_params();
			if ( ! empty( $product_data ) && array_key_exists( 'has_featured_image', $product_data ) ) {
				$has_featured_image = filter_var( $product_data['has_featured_image'], FILTER_VALIDATE_BOOLEAN );
				// If featured image not set.
				if ( ! $has_featured_image ) {
					// Remove featured image and update gallery.
					$featured_image_id = $product->get_image_id();
					$gallery_images    = $product->get_gallery_image_ids();

					if ( ! empty( $featured_image_id ) ) {
						array_unshift( $gallery_images, $featured_image_id );
						$product->set_image_id( '' );
						$product->set_gallery_image_ids( $gallery_images );
					}
				}
			}

			return $product;
		}

		/**
		 * Handle product review save from the frontend dashboard.
		 *
		 * @param WC_Data         $product  Product object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating If is creating a new object.
		 *
		 * @since 5.1.0
		 *
		 * @return WC_Data                  Updated Product Object.
		 */
		public function handle_product_review_save( $product, $request, $creating ) {
			// Set product review pending if enabled.
			if ( defined( 'WDMIR_REVIEW_PRODUCT' ) && WDMIR_REVIEW_PRODUCT ) {
				$current_user_id = get_current_user_id();

				// Check if user is instructor and is publishing/updating product.
				if ( wdm_is_instructor( $current_user_id ) && 'publish' === $product->get_status() ) {
					// Add product to review.
					$pending_review_products = maybe_unserialize( get_option( 'ir_pending_review_notices', [] ) );

					if ( ! in_array( $product->get_id(), $pending_review_products, true ) ) {
						$pending_review_products[] = $product->get_id();
						update_option( 'ir_pending_review_notices', $pending_review_products );
					}

					// Save status as draft.
					$product->set_status( 'draft' );
				}
			}

			return $product;
		}

		/**
		 * Handle new product review save from the frontend dashboard.
		 *
		 * @param WC_Data         $product  Product object.
		 * @param WP_REST_Request $request  Request object.
		 * @param bool            $creating If is creating a new object.
		 *
		 * @since 5.1.0
		 */
		public function handle_new_product_review_save( $product, $request, $creating ) {
			// Set product review pending if enabled.
			if ( defined( 'WDMIR_REVIEW_PRODUCT' ) && WDMIR_REVIEW_PRODUCT ) {
				$current_user_id = get_current_user_id();

				// Check if user is instructor.
				if ( wdm_is_instructor( $current_user_id ) ) {
					// Add product to review.
					$pending_review_products = maybe_unserialize( get_option( 'ir_pending_review_notices', [] ) );

					if ( ! in_array( $product->get_id(), $pending_review_products, true ) ) {
						$pending_review_products[] = $product->get_id();
						update_option( 'ir_pending_review_notices', $pending_review_products );
					}

					// Save status as draft.
					$product->set_status( 'draft' );
				}
			}
		}
	}
}
