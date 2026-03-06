<?php
/**
 * WooCommerce Integration Module
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Woocommerce' ) ) {
	/**
	 * Class Instructor Role Woocommerce Module
	 */
	class Instructor_Role_Woocommerce {
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
		 * To restrict access to all other product types except 'course' to instructors.
		 *
		 * @return array
		 */
		public function restrict_product_types( $product_types ) {
			if ( wdm_is_instructor() ) {
				/**
				* added in version 1.3
				* filter name: wdmir_product_types
				* param: array of product types
				*/
				$product_types = apply_filters( 'wdmir_product_types', [ 'course' => 'Course' ] );
			}

			return $product_types;
		}

		/**
		 * Filter woocommerce products for cross-sell and upsell sections.
		 *
		 * @since 4.1.0
		 *
		 * @param array $products
		 *
		 * @return array    $products
		 */
		public function filter_woocommerce_products( $products ) {
			if ( function_exists( 'wdm_is_instructor' ) && ! wdm_is_instructor() ) {
				return $products;
			}
			global $current_user;

			$instructor_products = [];

			foreach ( $products as $key => $item ) {
				$post_obj    = get_post( $key );
				$post_author = $post_obj->post_author;
				// Filter out the product specific to current user.
				if ( $post_author == $current_user->ID ) {
					$instructor_products[ $key ] = $item;
				}
			}
			return $instructor_products;
		}

		/**
		 * Allow instructors rest API access to products endpoints.
		 *
		 * @since 5.0.1
		 *
		 * @param bool   $permission
		 * @param string $context
		 * @param int    $object_id
		 * @param string $post_type
		 * @return bool
		 */
		public function allow_instructors_rest_api_access( $permission, $context, $object_id, $post_type ) {
			if ( ! is_user_logged_in() && ! wdm_is_instructor() ) {
				return $permission;
			}

			if ( ! empty( $object_id ) ) {
				$current_user_id = get_current_user_id();
				$post_author     = get_post_field( 'post_author', $object_id );

				if ( intval( $post_author ) !== $current_user_id ) {
					return $permission;
				}
			}

			$allowed_post_types = [
				'product',
				'product_cat',
				'product_tag',
			];

			if ( ! in_array( $post_type, $allowed_post_types, 1 ) ) {
				return $permission;
			}

			return true;
		}
	}
}
