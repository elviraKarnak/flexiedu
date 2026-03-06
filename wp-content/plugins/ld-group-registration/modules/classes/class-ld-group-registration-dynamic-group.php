<?php
/**
 * Dynamic courses Module
 *
 * @since 4.3.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/modules/classes
 */

namespace LdGroupRegistration\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ld_Group_Registration_Dynamic_Group' ) ) {
	/**
	 * Class LD Group Registration Dynamic Group
	 */
	class Ld_Group_Registration_Dynamic_Group {
		/**
		 * Class Instance
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 4.3.3
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Save metabox options for dynamic group settings
		 *
		 * @param int $post_id  id of post.
		 *
		 * @since 4.3.0
		 */
		public function save_dynamic_group_settings( $post_id ) {
			if ( array_key_exists( 'ldgr_enable_dynamic_group', $_POST ) && 'on' === $_POST['ldgr_enable_dynamic_group'] ) {
				update_post_meta( $post_id, 'ldgr_enable_dynamic_group', $_POST['ldgr_enable_dynamic_group'] );
			} else {
				delete_post_meta( $post_id, 'ldgr_enable_dynamic_group' );
			}

			if ( array_key_exists( 'ldgr_dynamic_courses', $_POST ) && ! empty( $_POST['ldgr_dynamic_courses'] ) && array_key_exists( 'ldgr_enable_dynamic_group', $_POST ) && 'on' === $_POST['ldgr_enable_dynamic_group'] ) {
				update_post_meta( $post_id, 'ldgr_dynamic_courses', $_POST['ldgr_dynamic_courses'] );
			} else {
				delete_post_meta( $post_id, 'ldgr_dynamic_courses' );
			}

			if ( array_key_exists( 'ldgr_dynamic_unlimited_price', $_POST ) && ! empty( $_POST['ldgr_dynamic_unlimited_price'] ) && array_key_exists( 'ldgr_enable_dynamic_group', $_POST ) && 'on' === $_POST['ldgr_enable_dynamic_group'] ) {
				update_post_meta( $post_id, 'ldgr_dynamic_unlimited_price', $_POST['ldgr_dynamic_unlimited_price'] );
			} else {
				delete_post_meta( $post_id, 'ldgr_dynamic_unlimited_price' );
			}

			if ( array_key_exists( 'ldgr_unlimited_members_dynamic_price', $_POST ) && ! empty( $_POST['ldgr_unlimited_members_dynamic_price'] ) && array_key_exists( 'ldgr_enable_dynamic_group', $_POST ) && 'on' === $_POST['ldgr_enable_dynamic_group'] ) {
				update_post_meta( $post_id, 'ldgr_unlimited_members_dynamic_price', $_POST['ldgr_unlimited_members_dynamic_price'] );
			} else {
				delete_post_meta( $post_id, 'ldgr_unlimited_members_dynamic_price' );
			}
		}

		/**
		 * This function is used to add dropdown on front end for dynamic products options.
		 *
		 * @return void
		 *
		 * @since 4.3.0
		 */
		public function woo_add_dynamic_products_dropdown() {
			global $post, $woocommerce;

			// Check if post or woo cart empty.
			if ( empty( $post ) || empty( $woocommerce ) ) {
				return;
			}

			$product_id                = $post->ID;
			$is_group_purchase_enabled = get_post_meta( $product_id, '_is_group_purchase_active', true );
			// Check if group purchase enabled.
			if ( 'on' !== $is_group_purchase_enabled ) {
				return;
			}

			$current_user = wp_get_current_user();
			$user_id      = get_current_user_id();
			$email        = $current_user->user_email;

			// Check if first time purchase or whether the product is a subscription.
			if ( ! \wc_customer_bought_product( $email, $user_id, $product_id ) || 'subscription' === ldgr_get_woo_product_type( $product_id ) ) {
				return;
			}
			$value_show = get_post_meta( $product_id, '_is_checkbox_show_front_end', true );

			if ( 'on' !== $value_show ) {
				$default_option = 'group';
			} else {
				$default_option = get_post_meta( $product_id, '_ldgr_front_default_option', true );
			}
			$user_id = get_current_user_id();

			$user_group_id             = get_user_meta( $user_id, 'ldgr_group_product_' . $product_id, true );
			$is_enabled_dynamic_course = get_post_meta( $product_id, 'ldgr_enable_dynamic_group', true );
			$group_label               = 'Group';
			$lower_group_label         = 'Group';
			$course_label              = 'Course';

			if ( class_exists( 'LearnDash_Custom_Label' ) ) {
				$course_label      = learndash_get_custom_label( 'course' );
				$group_label       = learndash_get_custom_label( 'group' );
				$lower_group_label = \LearnDash_Custom_Label::label_to_lower( 'group' );
			}

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/dynamic-group/ldgr-dynamic-group-dropdown.template.php',
				[
					'default_option'            => $default_option,
					'group_ids'                 => $user_group_id,
					'is_enabled_dynamic_course' => $is_enabled_dynamic_course,
					'group_label'               => $group_label,
					'course_label'              => $course_label,
					'lower_group_label'         => $lower_group_label,
					'ldgr_nonce'                => wp_create_nonce( 'ldgr_variation_nonce' ),
				]
			);
		}

		/**
		 * This function is used to update price in cart and checkout
		 *
		 * @param object $cart_obj  woocommerce cart object.
		 * @return void
		 *
		 * @since 4.3.0
		 */
		public function update_dynamic_price( $cart_obj ) {
			// Iterate through each cart item.
			foreach ( $cart_obj->get_cart() as $key => $value ) {
				if ( isset( $value['ldgr_new_price'] ) ) {
					$price = $value['ldgr_new_price'];
					$value['data']->set_price( ( $price ) );
				}
			}
		}

		/**
		 * This function is used to add course title below product title on cart page.
		 *
		 * @param string $product_title     Product title for cart page.
		 * @param array  $cart_item         Cart item details.
		 * @param [type] $cart_item_key     Cart item key.
		 * @return string
		 *
		 * @since 4.3.0
		 */
		public function add_courses_to_cart_item_name( $product_title, $cart_item, $cart_item_key ) {
			// Add courses to show on cart page.
			if ( array_key_exists( 'ldgr_courses', $cart_item ) && ! empty( $cart_item['ldgr_courses'] ) ) {
				$courses_data       = '';
				$courses_data_label = '<br><b>' . sprintf(
				// translators: Courses.
					__( 'Additional %s : ', 'wdm_ld_group' ),
					\LearnDash_Custom_Label::get_label( 'courses' )
				) . '</b>';
				$courses_data .= '<ul>';
				foreach ( $cart_item['ldgr_courses'] as $key => $value ) {
					$courses_data = $courses_data . '<li>' . get_the_title( $key ) . '</li>';
				}
				$courses_data .= '</ul>';

				/**
				 * Filter to modify Product title on cart page.
				 *
				 * @since 4.3.0
				 *
				 * @param string $product_title
				 * @param array $cart_item
				 * @param [type] $cart_item_key
				 */
				$ldgr_product_title = $product_title . $courses_data_label . $courses_data;
				return apply_filters( 'ldgr_product_title_add_courses', $ldgr_product_title, $cart_item, $cart_item_key );
			}

			return $product_title;
		}

		/**
		 * This function is used to add additional seats title below product title on cart page.
		 *
		 * @param string $product_title     Product title for cart page.
		 * @param array  $cart_item         Cart item details.
		 * @param [type] $cart_item_key     Cart item key.
		 * @return string
		 *
		 * @since 4.3.0
		 */
		public function increase_seats_item_name( $product_title, $cart_item, $cart_item_key ) {
			// Add increase seats title to show on cart page.
			if ( array_key_exists( 'ldgr_dynamic_option', $cart_item ) && 'increase_seats' === $cart_item['ldgr_dynamic_option'] ) {
				$courses_data       = '';
				$courses_data_label = '<br><b>' . __( 'Additional Seats Purchase', 'wdm_ld_group' ) . '</b>';

				/**
				 * Filter to modify Product title on cart page
				 *
				 * @since 4.3.0
				 *
				 * @param string $product_title
					 * @param array $cart_item
				 * @param [type] $cart_item_key
				 */
				$ldgr_product_title = $product_title . $courses_data_label;
				return apply_filters( 'ldgr_product_title_increase_seats', $ldgr_product_title, $cart_item, $cart_item_key );
			}

			return $product_title;
		}

		/**
		 * This function is used to add dynamic selected courses in product courses
		 * before creating group.
		 *
		 * @param array  $courses       courses to be added in product.
		 * @param object $item          order item data.
		 * @return array
		 *
		 * @since 4.3.0
		 */
		public function add_dynamic_courses_in_order( $courses, $item ) {
			$dynamic_courses = wc_get_order_item_meta( $item->get_id(), 'ldgr_courses', true );
			if ( empty( $dynamic_courses ) ) {
				return $courses;
			}
			foreach ( $dynamic_courses as $key => $value ) {
				$courses[] = $key;
			}
			$courses = array_unique( $courses );

			/**
			 * Filter to modify courses array
			 *
			 * @since 4.3.0
			 *
			 * @param array $courses
			 * @param object $item
			 */
			$courses = apply_filters( 'ldgr_dynamic_courses_order', $courses, $item );
			return $courses;
		}

		/**
		 * This function is used to add dynamic course data in cart meta
		 * following data is added
		 * - courses selected on products page
		 * - Total price of product
		 * - Action client is taking
		 * - Group id in case action is other than create new group
		 *
		 * @param array   $cart_item_meta   cart item meta.
		 * @param integer $product_id       id of product.
		 * @return array
		 *
		 * @since 4.3.0
		 */
		public function add_courses_to_cart_item_meta( $cart_item_meta, $product_id ) {
			$post_course_data = array_filter(
				$_POST,
				function ( $key ) {
					return strpos( $key, 'course_' ) === 0;
				},
				ARRAY_FILTER_USE_KEY
			);

			if ( ! empty( $post_course_data ) ) {
				foreach ( $post_course_data as $key => $value ) {
					$cart_item_meta['ldgr_courses'][ ltrim( $key, 'course_' ) ] = $value;
				}
			}

			if ( isset( $_POST['ldgr_new_price'] ) ) {
				$cart_item_meta['ldgr_new_price'] = $_POST['ldgr_new_price'];
			}

			if ( isset( $_POST['ldgr_dynamic_option'] ) ) {
				$cart_item_meta['ldgr_dynamic_option'] = $_POST['ldgr_dynamic_option'];
			}

			if ( isset( $_POST['ldgr_dynamic_value'] ) ) {
				$cart_item_meta['ldgr_dynamic_value'] = $_POST['ldgr_dynamic_value'];
			}

			/**
			 * Filter to modify cart item meta array
			 *
			 * @since 4.3.0
			 *
			 * @param array $cart_item_meta
			 * @param object $product_id
			 */
			$cart_item_meta = apply_filters( 'ldgr_dynamic_courses_order', $cart_item_meta, $product_id );
			return $cart_item_meta;
		}

		/**
		 * Add dynamic course meta in order meta
		 *
		 * @param integer $item_id  id of order item.
		 * @param array   $values   values of order item.
		 * @return void
		 *
		 * @since 4.3.0
		 */
		public function update_order_item_meta( $item_id, $values ) {
			// Save data in order meta.

			// Save dynamic options.
			if ( isset( $values['ldgr_dynamic_option'] ) ) {
				wc_add_order_item_meta( $item_id, 'ldgr_dynamic_option', $values['ldgr_dynamic_option'] );

				switch ( $values['ldgr_dynamic_option'] ) {
					case 'create_new':
						$dynamic_option = __( 'New Group', 'wdm_ld_group' );
						break;

					case 'add_courses':
						$dynamic_option = __( 'Added Courses', 'wdm_ld_group' );
						break;

					case 'increase_seats':
						$dynamic_option = __( 'Increased Seats', 'wdm_ld_group' );
						break;
				}

				wc_add_order_item_meta( $item_id, __( 'Option Selected', 'wdm_ld_group' ), $dynamic_option );
			}

			// Save dynamic courses.
			if ( isset( $values['ldgr_courses'] ) ) {
				wc_add_order_item_meta( $item_id, 'ldgr_courses', $values['ldgr_courses'] );

				$courses_list = '<ul>';

				foreach ( $values['ldgr_courses'] as $key => $value ) {
					$courses_list .= '<li>' . get_the_title( $key ) . '</li><br>';
				}

				$courses_list .= '</ul>';

				wc_add_order_item_meta( $item_id, 'Courses', $courses_list );
			}

			// Save dynamic group id's.
			if ( isset( $values['ldgr_dynamic_value'] ) ) {
				wc_add_order_item_meta( $item_id, 'ldgr_dynamic_value', $values['ldgr_dynamic_value'] );
			}
		}

		/**
		 * This function is used add dynamic options before group creation
		 *
		 * @param array  $group_data     array to group data.
		 * @param object $item          Object of order item.
		 * @return array $group_data
		 *
		 * @since 4.3.0
		 */
		public function add_dynamic_data_before_group_creation( $group_data, $item ) {
			$option = wc_get_order_item_meta( $item->get_id(), 'ldgr_dynamic_option', true );
			$value  = wc_get_order_item_meta( $item->get_id(), 'ldgr_dynamic_value', true );

			if ( isset( $option ) ) {
				$group_data['ldgr_dynamic_option'] = $option;
			}

			if ( isset( $value ) ) {
				$group_data['ldgr_dynamic_value'] = $value;
			}

			/**
			 * Filter to modify group data
			 *
			 * @since 4.3.0
			 *
			 * @param array $group_data
			 * @param object $item
			 */
			$group_data = apply_filters( 'ldgr_dynamic_courses_order', $group_data, $item );
			return $group_data;
		}

		/**
		 * This function is used to show dynamic courses on single product page
		 *
		 * @return void
		 *
		 * @since 4.3.0
		 */
		public function woo_add_dynamic_courses() {
			global $post, $woocommerce;

			// Check if post or woo cart empty.
			if ( empty( $post ) || empty( $woocommerce ) ) {
				return;
			}

			$product_id = $post->ID;

			$is_group_purchase_enabled = get_post_meta( $product_id, '_is_group_purchase_active', true );

			// Check if group purchase enabled.
			if ( 'on' !== $is_group_purchase_enabled ) {
				return;
			}

			$product = wc_get_product( $product_id );

			$value_show = get_post_meta( $product_id, '_is_checkbox_show_front_end', true );
			if ( 'on' !== $value_show ) {
				$default_option = 'group';
			} else {
				$default_option = get_post_meta( $product_id, '_ldgr_front_default_option', true );
			}
			$courses = get_post_meta( $product_id, 'ldgr_dynamic_courses', [] );

			$courses = array_pop( $courses );

			// If no dynamic courses, return.
			if ( null === $courses || empty( $courses ) ) {
				return;
			}

			$def_course_image = get_option( 'ldgr_default_course_image' );
			if ( $image = wp_get_attachment_image_src( $def_course_image ) ) {
				$def_img = esc_url( $image[0] );
			} else {
				$def_img = esc_url( plugins_url( 'assets/images/no_image.png', WDM_LDGR_PLUGIN_FILE ) );
			}

			// $group_label       = 'group';
			// $lower_group_label = 'group';
			$courses_label = 'Courses';

			if ( class_exists( 'LearnDash_Custom_Label' ) ) {
				$courses_label = learndash_get_custom_label( 'courses' );
			}

			ldgr_get_template(
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/dynamic-group/ldgr-dynamic-group-courses.template.php',
				[
					'default_option'   => $default_option,
					'courses'          => $courses,
					'def_course_image' => $def_img,
					'price'            => $product->get_price(),
					'courses_label'    => $courses_label,
				]
			);
		}

		/**
		 * This function is used to fetch all courses from database
		 *
		 * @return array
		 *
		 * @since 4.3.0
		 */
		public static function ldgr_get_all_courses() {
			if ( ! defined( 'LEARNDASH_VERSION' ) ) {
				return;
			}
			global $post;
			$post_id = $post->ID;
			query_posts(
				[
					'post_type'        => 'sfwd-courses',
					'posts_per_page'   => - 1,
					'suppress_filters' => true,
				]
			);
			$courses         = [];
			$related_courses = (array) get_post_meta( $post_id, '_related_course', true );
			while ( have_posts() ) {
				the_post();
				if ( ! in_array( get_the_ID(), $related_courses ) && 'subscribe' !== learndash_get_setting( get_the_ID(), 'course_price_type' ) ) {
					$courses[ get_the_ID() ] = get_the_title();
				}
			}
			wp_reset_query();
			$post = get_post( $post_id );

			/**
			 * Filter to modify courses
			 *
			 * @since 4.3.0
			 *
			 * @param array $post_id
			 */
			$courses = apply_filters( 'ldgr_get_all_courses', $courses, $post_id );
			return $courses;
		}

		/**
		 * Fetch selected dynamic courses of product
		 *
		 * @param string $post_id   id of current post/product.
		 * @return array
		 *
		 * @since 4.3.0
		 */
		public static function selected_courses( $post_id = '' ) {
			if ( empty( $post_id ) ) {
				global $post;
				$post_id = $post->ID;
			}

			$courses = (array) get_post_meta( $post_id, 'ldgr_dynamic_courses', true );

			if ( ! $courses ) {
				$courses = [];
			}

			/**
			 * Filter to modify selected courses
			 *
			 * @since 4.3.0
			 *
			 * @param array $post_id
			 */
			$courses = apply_filters( 'ldgr_selected_courses', $courses, $post_id );
			return $courses;
		}

		/**
		 * Handle woocommerce add to cart validation.
		 *
		 * @param bool $passed       Validation status.
		 * @param int  $product_id   ID of the product.
		 * @param int  $quantity     Quantity for the product.
		 *
		 * @return bool              Updated validation.
		 */
		public function handle_woo_add_to_cart_validation( $passed, $product_id, $quantity ) {
			if ( isset( $_GET['resubscribe'] ) || isset( $_GET['subscription_renewal'] ) ) {
				return true;
			}

			if ( isset( $_GET['pay_for_order'] ) && $_GET['pay_for_order'] ) {
				return true;
			}

			$value = get_post_meta( $product_id, '_is_group_purchase_active', true );
			if ( 'on' === $value ) {
				$value_show     = get_post_meta( $product_id, 'ldgr_enable_dynamic_group', true );
				$enable_package = ldgr_check_package_enabled( $product_id );
				if ( 'on' === $value_show && ! $enable_package ) {
					global $woocommerce;
					$items = $woocommerce->cart->get_cart();
					foreach ( $items as $key => $item ) {
						if ( $item['product_id'] === $product_id ) {
							wc_add_notice( __( 'Can\'t add more than 1 product in cart.', 'wdm_ld_group' ), 'error' );
							return false;
						}
					}
				}
			}
			return $passed;
		}

		/**
		 * Remove the quantity field for add courses option
		 *
		 * @param int    $product_quantity      Quantity of product added in cart.
		 * @param int    $cart_item_key         Cart item unique key.
		 * @param object $cart_item             Cart item object.
		 *
		 * @return int
		 *
		 * @since 4.1.0
		 */
		public function remove_quantity_for_add_courses( $product_quantity, $cart_item_key, $cart_item ) {
			// Check if group registration enabled on the product in cart.
			if ( ! array_key_exists( 'wdm_ld_group_active', $cart_item ) || 'on' !== $cart_item['wdm_ld_group_active'] ) {
				return $product_quantity;
			}

			if ( isset( $cart_item['ldgr_dynamic_option'] ) && 'add_courses' === $cart_item['ldgr_dynamic_option'] ) {
				$product_quantity = sprintf( '%2$s <input type="hidden" name="cart[%1$s][qty]" value="%2$s" />', $cart_item_key, $cart_item['quantity'] );
			}
			return $product_quantity;
		}

		/**
		 * Hide group registration order item meta on admin side
		 *
		 * @param array $hidden_meta    Meta keys to hide.
		 * @return array
		 */
		public function hide_admin_group_reg_order_meta( $hidden_meta ) {
			if ( ! in_array( 'ldgr_dynamic_option', $hidden_meta, true ) ) {
				$hidden_meta[] = 'ldgr_dynamic_option';
			}

			if ( ! in_array( 'ldgr_dynamic_value', $hidden_meta, true ) ) {
				$hidden_meta[] = 'ldgr_dynamic_value';
			}

			return $hidden_meta;
		}

		/**
		 * Hide group registration order item meta on order complete page
		 *
		 * @param array $hidden_meta    Meta keys to hide.
		 * @return array
		 */
		public function hide_public_group_reg_order_meta( $formatted_meta, $object ) {
			$hidden_meta = [ 'ldgr_dynamic_option', 'ldgr_dynamic_value' ];

			foreach ( $formatted_meta as $key => $value ) {
				if ( in_array( $value->key, $hidden_meta, true ) ) {
					unset( $formatted_meta[ $key ] );
				}
			}

			return $formatted_meta;
		}

		/**
		 * Get user groups list related to the passed variation ID.
		 *
		 * @since 4.3.2
		 */
		public function ajax_get_variation_groups() {
			if ( wp_verify_nonce( filter_input( INPUT_POST, 'ldgr_nonce' ), 'ldgr_variation_nonce' ) ) {
				$variation_id = filter_input( INPUT_POST, 'variation_id', FILTER_SANITIZE_NUMBER_INT );
				$user_id      = get_current_user_id();

				$variation_groups = get_user_meta( $user_id, 'ldgr_group_product_' . $variation_id, true );
				if ( ! empty( $variation_groups ) ) {
					$options = sprintf(
						"<option value=''>%s</option>",
						sprintf(
							/* translators: Group label */
							esc_html__( 'Select %s Name ', 'wdm_ld_group' ),
							\LearnDash_Custom_Label::get_label( 'group' )
						)
					);

					foreach ( $variation_groups as $group_id ) {
						$group_courses = learndash_group_enrolled_courses( $group_id );
						$group_limit   = get_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, true );
						$options      .= stripslashes(
							sprintf(
								'<option data-courses="%1$s" value="%2$d" data-users="%3$d">%4$s</option>',
								wp_json_encode( $group_courses ),
								$group_id,
								$group_limit,
								get_the_title( $group_id )
							)
						);
					}

					echo wp_json_encode(
						[
							'status' => 'success',
							'data'   => $options,
						]
					);
					wp_die();
				}
			}
			echo wp_json_encode(
				[
					'status' => 'fail',
					'data'   => sprintf(
						"<option value=''>%s</option>",
						sprintf(
							/* translators: Group label */
							esc_html__( 'Select %s Name ', 'wdm_ld_group' ),
							\LearnDash_Custom_Label::get_label( 'group' )
						)
					),
				]
			);
			wp_die();
		}

		/**
		 * Get course list related to the passed variation ID.
		 *
		 * @since 4.3.3
		 */
		public function ajax_update_variation_course_list() {
			if ( wp_verify_nonce( filter_input( INPUT_POST, 'ldgr_nonce' ), 'ldgr_variation_nonce' ) ) {
				$variation_id      = filter_input( INPUT_POST, 'variation_id', FILTER_SANITIZE_NUMBER_INT );
				$variation_courses = get_post_meta( $variation_id, '_related_course', 1 );

				if ( ! empty( $variation_courses ) ) {
					$group_label   = 'Group';
					$courses_label = 'Courses';

					if ( class_exists( 'LearnDash_Custom_Label' ) ) {
						$courses_label = learndash_get_custom_label( 'courses' );
						$group_label   = learndash_get_custom_label( 'group' );
					}

					$def_course_image = get_option( 'ldgr_default_course_image' );
					if ( $image = wp_get_attachment_image_src( $def_course_image ) ) {
						$def_img = esc_url( $image[0] );
					} else {
						$def_img = esc_url( plugins_url( 'assets/images/no_image.png', WDM_LDGR_PLUGIN_FILE ) );
					}

					$course_list_html = ldgr_get_template(
						WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-product-related-courses.template.php',
						[
							'group_label'             => $group_label,
							'courses_label'           => $courses_label,
							'product_courses'         => $variation_courses,
							'def_course_image'        => $def_img,
							'display_product_courses' => get_option( 'ldgr_display_product_courses' ),
						],
						true
					);

					echo wp_json_encode(
						[
							'status' => 'success',
							'data'   => $course_list_html,
						]
					);
					wp_die();
				}
			}
			echo wp_json_encode(
				[
					'status' => 'fail',
					'data'   => false,
				]
			);
			wp_die();
		}
	}
}
