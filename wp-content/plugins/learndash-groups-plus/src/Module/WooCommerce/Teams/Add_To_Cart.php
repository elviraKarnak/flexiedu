<?php
/**
 * WooCommerce Team Add To Cart functionality.
 *
 * @since 2.0.0
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Module\WooCommerce\Teams;

use WC_Cart;
use WC_Product;
use WC_Subscriptions_Product;
use WC_Product_Variation;

use LearnDash\Groups_Plus\Module\Base as Module_Base;
use LearnDash\Groups_Plus\Module\Module_Interface;

use LearnDash\Groups_Plus\lucatume\DI52\App;

use LearnDash\Core\Utilities\Cast;

use LearnDash\Groups_Plus\Utility\SharedFunctions;
use WC_Product_Subscription_Variation;
use WC_Product_Variable;

/**
 * Class Teams Add To Cart.
 *
 * @since 2.0.0
 */
class Add_To_Cart extends Module_Base implements Module_Interface {
	/**
	 * Method to contain function call related to action hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function hook_actions(): void {
		if (
			! SharedFunctions::is_woocommerce_active()
			|| 'no' === get_option( 'enable_wc', 'no' )
		) {
			return;
		}

		add_action( 'woocommerce_before_calculate_totals', [ $this, 'change_cart_item_subtotal' ], 9, 1 );

		add_action( 'woocommerce_cart_calculate_fees', [ $this, 'add_sign_up_fee_as_a_real_fee' ], 10, 1 );
	}

	/**
	 * Method to contain function call related to filter hooks.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	protected function hook_filters(): void {
		if (
			! SharedFunctions::is_woocommerce_active()
			|| 'no' === get_option( 'enable_wc', 'no' )
		) {
			return;
		}

		add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'validate_add_to_cart' ], 10, 2 );

		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'update_cart_item_data' ], 10, 3 );

		add_filter( 'woocommerce_get_item_data', [ $this, 'populate_item_data' ], 10, 2 );

		add_filter( 'woocommerce_subscriptions_product_price_string_inclusions', [ $this, 'force_sign_up_fee_subtotal_string' ], 10, 2 );

		add_filter( 'woocommerce_subscriptions_cart_get_price', [ $this, 'remove_sign_up_fee_from_product_price_calculations' ], 10, 2 );
	}

	/**
	 * Validates whether we should allow this item to be added to the cart.
	 *
	 * @since 2.0.0
	 *
	 * @param bool $passed     Success/failure.
	 * @param int  $product_id Product ID.
	 *
	 * @return bool
	 */
	public function validate_add_to_cart( $passed, $product_id ) {
		$error_notices = [];
		$post_data     = wp_unslash( $_POST );

		if (
			! SharedFunctions::filter_has_var( 'groups_plus_teams_product_nonce', INPUT_POST )
			|| ! wp_verify_nonce(
				Cast::to_string(
					SharedFunctions::filter_input(
						'groups_plus_teams_product_nonce',
						INPUT_POST
					)
				),
				'learndash_groups_plus_add_teams_product'
			)
		) {
			return $passed;
		}

		if (
			empty( $post_data['team_name'] )
			&& ! isset( $post_data['choose_team'] )
		) {
			$error_notices[] = sprintf(
				// translators: Team label.
				__( '"%s name" is a required field', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			);
		}

		if (
			empty( $post_data['choose_team'] )
			&& ! isset( $post_data['team_name'] )
		) {
			$error_notices[] = sprintf(
				// translators: Team label.
				__( 'A %s must be chosen', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			);
		}

		$product = wc_get_product( $product_id );

		// If the previous checks have passed and it is a Variable Product, then return early.
		if (
			empty( $error_notices )
			&& $product instanceof WC_Product_Variable
		) {
			return $passed;
		}

		if (
			empty( $post_data['my_team_courses'] )
			|| ! is_array( $post_data['my_team_courses'] )
		) {
			$error_notices[] = sprintf(
				// translators: team course.
				__( 'Choose at least one %1$s %2$s', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' ),
				learndash_get_custom_label_lower( 'course' ),
			);
		}

		// Display errors notices.
		if ( ! empty( $error_notices ) ) {
			wc_add_notice( implode( '<br>', $error_notices ), 'error' );
			return false;
		}

		return $passed;
	}

	/**
	 * Add Cart Item Data while adding to the Cart.
	 *
	 * @since 2.0.0
	 *
	 * @param array<mixed> $cart_item_data Cart Item Data.
	 * @param int          $product_id     Product ID.
	 * @param int          $variation_id   Variation ID.
	 *
	 * @return array<mixed>
	 */
	public function update_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		if (
			! SharedFunctions::filter_has_var( 'groups_plus_teams_product_nonce', INPUT_POST )
			|| ! wp_verify_nonce(
				Cast::to_string(
					SharedFunctions::filter_input(
						'groups_plus_teams_product_nonce',
						INPUT_POST
					)
				),
				'learndash_groups_plus_add_teams_product'
			)
		) {
			return $cart_item_data;
		}

		if ( ! self::product_can_be_used_for_teams( $product_id ) ) {
			return $cart_item_data;
		}

		$post_data = wp_unslash( $_POST );

		// Filter down $post_data to only include the data we want to directly interface with.
		$add_to_cart_data = $this->filter_valid_cart_data( $post_data );

		foreach ( $add_to_cart_data as $key => $value ) {
			switch ( $key ) {
				case 'my_team_courses':
					if ( is_array( $value ) ) {
						$value = $this->sanitize_team_courses( $value, $product_id );
					}

					break;
				case 'choose_team':
					$value = get_the_title( Cast::to_int( $value ) );

					break;
				default:
					$value = sanitize_text_field( Cast::to_string( $value ) );
					break;
			}

			$cart_item_data[ $key ] = $value;
		}

		return $cart_item_data;
	}

	/**
	 * Populate Item Data for the Cart Item, which will cause WooCommerce to generate the necessary DOM below the Cart Item Name.
	 *
	 * @since 2.1.0
	 *
	 * @param array<int,array{key: string, value: string, hidden?: bool}> $item_data      Item Data array.
	 * @param array<string,mixed>                                         $cart_item_data Cart Item Data, which is separate from the Item Data which is assigned to the Cart Item later in the process.
	 *
	 * @return array<int,array{key: string, value: string, hidden?: bool}>
	 */
	public function populate_item_data( $item_data, $cart_item_data ) {
		$cart_item_data = wp_parse_args(
			$cart_item_data,
			[
				'product_id' => 0,
			]
		);

		if (
			! self::product_can_be_used_for_teams(
				Cast::to_int( $cart_item_data['product_id'] )
			)
		) {
			return $item_data;
		}

		$valid_cart_data = $this->filter_valid_cart_data( $cart_item_data );

		foreach ( $valid_cart_data as $key => $item_data_value ) {
			$item_data_name = '';
			switch ( $key ) {
				case 'my_team_courses':
					$item_data_name = sprintf(
						// translators: placeholder: Course label.
						esc_html__( '%s names', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'course' )
					);

					if ( is_array( $item_data_value ) ) {
						$item_data_value = $this->format_item_data_list( $item_data_value );
					}

					break;
				default:
					$item_data_name = sprintf(
						// translators: placeholder: Team label.
						esc_html__( '%s name', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'team' )
					);

					break;
			}

			$item_data_value = Cast::to_string( $item_data_value );

			/**
			 * Filters the Item Data Label.
			 *
			 * @since 2.1.0
			 *
			 * @param string                                                      $item_data_name Item Data Label.
			 * @param array<int,array{key: string, value: string, hidden?: bool}> $item_data      Item Data array.
			 * @param array<string,mixed>                                         $cart_item_data Cart Item Data, which is separate from the Item Data which is assigned to the Cart Item later in the process.
			 */
			$item_data_name = apply_filters(
				'learndash_groups_plus_cart_item_data_name',
				$item_data_name,
				$item_data,
				$cart_item_data
			);

			/**
			 * Filters the Item Data Value.
			 *
			 * @since 2.1.0
			 *
			 * @param string                                                      $item_data_value Item Data Value.
			 * @param array<int,array{key: string, value: string, hidden?: bool}> $item_data       Item Data array.
			 * @param array<string,mixed>                                         $cart_item_data  Cart Item Data, which is separate from the Item Data which is assigned to the Cart Item later in the process.
			 */
			$item_data_value = apply_filters(
				'learndash_groups_plus_cart_item_data_value',
				$item_data_value,
				$item_data,
				$cart_item_data
			);

			/**
			 * Filters the new Item Data.
			 *
			 * @since 2.1.0
			 *
			 * @param array{key: string, value: string, hidden?: bool}            $new_item_data      Item Data.
			 * @param array<int,array{key: string, value: string, hidden?: bool}> $item_data      Item Data array.
			 * @param array<string,mixed>                                         $cart_item_data Cart Item Data, which is separate from the Item Data which is assigned to the Cart Item later in the process.
			 */
			$item_data[] = apply_filters(
				'learndash_groups_plus_cart_item_data',
				[
					'key'   => $item_data_name,
					'value' => $item_data_value,
				],
				$item_data,
				$cart_item_data
			);
		}

		return $item_data;
	}

	/**
	 * Formats a list of Item Data for output.
	 *
	 * @since 2.1.0
	 *
	 * @param array<int|string,string> $array   Item Data to format. Gutenberg blocks will output it separated by pipe characters, Shortcodes will output an unordered list.
	 * @param int                      $post_id Post ID where we're displaying the Item Data. Defaults to the current Post ID.
	 *
	 * @return string
	 */
	protected function format_item_data_list( array $array, int $post_id = 0 ): string {
		if ( $post_id <= 0 ) {
			$post_id = Cast::to_int( get_the_ID() );
		}

		if (
			function_exists( 'has_block' )
			&& (
				has_block( 'woocommerce/cart', $post_id )
				|| has_block( 'woocommerce/checkout', $post_id )
			)
		) {
			// The Item Schema used by the API call in the Gutenberg Block doesn't allow any HTML.
			return implode( ' | ', $array );
		}

		// The way the DOM and styling in WooCommerce is done makes it simplest to include an empty paragraph before our list to ensure it doesn't overlap the label.
		return '<p>&nbsp;</p><ul>' .
			'<li>' . implode( '</li><li>', $array ) . '</li>' .
		'</ul>';
	}

	/**
	 * Filters down valid Cart Item Data to be used for our calculations.
	 *
	 * @since 2.1.0
	 *
	 * @param array<string,mixed> $data Data to filter down based on the valid keys.
	 *
	 * @return array<string,mixed>
	 */
	protected function filter_valid_cart_data( array $data ): array {
		/**
		 * Filters the valid Cart Item Data Keys for Teams Products.
		 *
		 * @since 2.1.0
		 *
		 * @param array<string> $keys Valid Cart Item Data Keys.
		 */
		$valid_keys = apply_filters(
			'learndash_groups_plus_teams_valid_cart_item_data_keys',
			[
				'team_name',
				'choose_team',
				'my_team_courses',
			]
		);

		return array_filter(
			$data,
			function( $key ) use ( $valid_keys ) {
				return in_array( $key, $valid_keys, true );
			},
			ARRAY_FILTER_USE_KEY
		);
	}

	/**
	 * Sanitize the Courses passed to be added to the Cart Item
	 *
	 * @since 2.0.0
	 * @since 2.1.0 Returned Array now includes the Course Title as the value.
	 *
	 * @param array<int, string> $value      Courses value. Key is the Course ID, value is the Course Price.
	 * @param int                $product_id Product ID.
	 *
	 * @return array<int, string> Course ID => Course Title.
	 */
	protected function sanitize_team_courses( array $value, int $product_id ): array {
		if ( ! is_array( $value ) ) {
			return [];
		}

		$course_ids = array_keys( $value );

		$product_team_courses = get_post_meta( $product_id, '_groups_plus_team_courses', true );
		if (
			empty( $product_team_courses )
			|| ! is_array( $product_team_courses )
		) {
			$product_team_courses = [];
		}

		// If the Course ID is not assigned to the Product, remove the Course.
		foreach ( $course_ids as $course_id ) {
			if (
				! in_array(
					Cast::to_string( $course_id ),
					$product_team_courses,
					true
				)
			) {
				unset( $value[ $course_id ] );
			}

			$value[ $course_id ] = get_the_title( $course_id );
		}

		return $value;
	}

	/**
	 * Calculate the correct subtotals for cart items.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Cart $cart Cart object.
	 *
	 * @return void
	 */
	public function change_cart_item_subtotal( $cart ) {
		$this->process_cart_items(
			$cart,
			function( $cart, $cart_item_data, $product ) {
				$cart_item_data = wp_parse_args(
					$cart_item_data,
					[
						'my_team_courses' => [],
					]
				);

				$price = self::calculate_subtotal_with_courses_added(
					$cart_item_data['my_team_courses'],
					$product->get_id(),
					! empty( $cart_item_data['team_name'] ),
					$cart_item_data
				);

				// Ensure the Subtotal is calculated correctly.
				$price = $price / $cart_item_data['quantity'];
				$product->set_price(
					Cast::to_string( $price )
				);
			}
		);
	}

	/**
	 * Ensure the Subtotal Column on the Cart and Checkout Page for each Line Item shows the correct Subtotal String.
	 * Example: $4.00 / month and a $5.00 sign-up fee.
	 *
	 * The Sign Up Fee is multiplied by the Quantity otherwise, which we do not want.
	 *
	 * @since 2.1.1
	 *
	 * @param array<string, mixed> $include What we want to include in the Subtotal String generated by WooCommerce Subscriptions.
	 * @param WC_Product           $product The current Product object.
	 *
	 * @return array<string, mixed>
	 */
	public function force_sign_up_fee_subtotal_string( $include, $product ) {
		if ( ! self::product_can_be_used_for_teams( $product->get_id() ) ) {
			return $include;
		}

		$include['sign_up_fee'] = true;

		return $include;
	}

	/**
	 * Removes the Sign Up Fee from $product->get_price() calculations.
	 * This is important to ensure that the Cart Subtotal and Total do not attempt to include the Sign Up Fee, as we are going to add it separately as a true WooCommerce Fee in order to ensure it is only counted once per-Line Item.
	 *
	 * @since 2.1.1
	 *
	 * @param float      $price The Product price.
	 * @param WC_Product $product The Product object.
	 *
	 * @return float
	 */
	public function remove_sign_up_fee_from_product_price_calculations( $price, $product ) {
		// Ensure recurring subtotals/totals include the Sign Up Fee.
		if ( doing_filter( 'woocommerce_calculated_total' ) ) {
			return $price;
		}

		if ( ! self::product_can_be_used_for_teams( $product->get_id() ) ) {
			return $price;
		}

		$sign_up_fee = Cast::to_float(
			WC_Subscriptions_Product::get_sign_up_fee( $product )
		);

		if ( empty( $sign_up_fee ) ) {
			return $price;
		}

		$price = max(
			0,
			Cast::to_float( $price ) - $sign_up_fee
		);

		return $price;
	}

	/**
	 * For each Teams Subscription Product in the Cart that has a Sign Up Fee, add that Sign Up Fee as a proper WooCommerce Fee.
	 *
	 * @since 2.1.1
	 *
	 * @param WC_Cart $cart WooCommerce Cart object.
	 *
	 * @return void
	 */
	public function add_sign_up_fee_as_a_real_fee( $cart ) {
		// Ensure recurring subtotals/totals don't include this fee.
		if ( doing_filter( 'woocommerce_calculated_total' ) ) {
			return;
		}

		if ( ! class_exists( 'WC_Subscriptions_Product' ) ) {
			return;
		}

		$this->process_cart_items(
			$cart,
			function( $cart, $cart_item_data, $product ) {
				if (
					! $product instanceof WC_Product_Subscription_Variation
					&& ! $product->is_type( 'subscription' )
					&& ! $product->is_type( 'variable-subscription' )
				) {
					return;
				}

				$sign_up_fee = WC_Subscriptions_Product::get_sign_up_fee( $product->get_id() );

				if ( empty( $sign_up_fee ) ) {
					return;
				}

				$team_name = '';
				if ( ! empty( $cart_item_data['team_name'] ) ) {
					$team_name = $cart_item_data['team_name'];
				} elseif ( ! empty( $cart_item_data['choose_team'] ) ) {
					$team_name = $cart_item_data['choose_team'];
				}

				if ( empty( $team_name ) ) {
					return;
				}

				$cart->add_fee(
					esc_html(
						sprintf(
							// translators: Sign up fees for team "Team Name".
							__( 'Sign up fees for %1$s "%2$s"', 'learndash-groups-plus' ),
							learndash_get_custom_label_lower( 'team' ),
							$team_name
						)
					),
					WC_Subscriptions_Product::get_sign_up_fee( $product->get_id() ),
					$product->is_taxable()
				);
			}
		);
	}

	/**
	 * A shorthand method to allow interacting with only Cart Items that are for selling Teams Products.
	 *
	 * @since 2.1.1
	 *
	 * @param WC_Cart  $cart     WooCommerce Cart object.
	 * @param callable $callback Callback callback function.
	 *
	 * @return void
	 */
	private function process_cart_items( WC_Cart $cart, callable $callback ): void {
		if ( count( $cart->cart_contents ) <= 0 ) {
			return;
		}

		foreach ( $cart->cart_contents as $cart_item_key => &$cart_item_data ) {
			if (
				! is_array( $cart_item_data )
				|| empty( $cart_item_data['data'] )
			) {
				continue;
			}

			$product = &$cart_item_data['data'];

			if ( ! $product instanceof WC_Product ) {
				continue;
			}

			if (
				empty( $cart_item_data['quantity'] )
				|| $cart_item_data['quantity'] <= 0
			) {
				continue;
			}

			// Not buying access for a Team.
			if ( ! self::product_can_be_used_for_teams( $product->get_id() ) ) {
				continue;
			}

			// Ensure the necessary Cart Item Data exists.
			if (
				// Variable Products don't pass through Courses.
				! $product instanceof WC_Product_Variation
				&& (
					empty( $cart_item_data['my_team_courses'] )
					|| ! is_array( $cart_item_data['my_team_courses'] )
				)
			) {
				continue;
			}

			$callback( $cart, $cart_item_data, $product );
		}
	}

	/**
	 * Gets the price to add to a line item based on the chosen Courses and the Quantity.
	 *
	 * @since 2.0.0
	 * @since 2.1.1 Added default value for $chosen_courses.
	 *
	 * @param array<int, string> $chosen_courses Chosen Courses. Key is Course ID, value is Course Price as a String. Defaults to an empty Array.
	 * @param int                $product_id     Product ID. Defaults to 0, which result in a subtotal of 0.0 being returned as there is no valid Product.
	 * @param bool               $is_new_team    Whether this is a New Team or if these Courses are being added to an existing Team. Defaults to false.
	 * @param array<mixed>       $cart_item      Cart Item Data. Defaults to an empty Array.
	 *
	 * @return float
	 */
	private function calculate_subtotal_with_courses_added( array $chosen_courses = [], int $product_id = 0, bool $is_new_team = false, array $cart_item = [] ): float {
		$total_price = 0.0;
		$product     = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product ) {
			return $total_price;
		}

		if ( $is_new_team ) {
			$total_price = Cast::to_float( $product->get_price( 'unfiltered' ) );
		}

		$course_ids = [];
		if ( ! empty( $chosen_courses ) ) {
			$course_ids = array_keys( $chosen_courses );
		} elseif ( $product instanceof WC_Product_Variation ) {
			$course_ids = get_post_meta(
				$product_id,
				SharedFunctions::$groups_plus_team_courses_meta_field,
				true
			);

			if (
				empty( $course_ids )
				|| ! is_array( $course_ids )
			) {
				$course_ids = [];
			}
		}

		foreach ( $course_ids as $course_id ) {
			// We cannot trust the client-side for Course Price, so we are re-grabbing it.
			$course_price = Cast::to_float(
				get_post_meta(
					$course_id,
					'_course_price',
					true
				)
			);

			if ( ! empty( $course_price ) ) {
				$total_price = $total_price + ( $course_price * $cart_item['quantity'] );
			}
		}

		return $total_price;
	}

	/**
	 * Check if the given Product is configured to be used to purchase Teams.
	 *
	 * @since 2.0.0
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool
	 */
	public static function product_can_be_used_for_teams( int $product_id ): bool {
		/**
		 * Product object.
		 *
		 * @var WC_Product
		 */
		$product = wc_get_product( $product_id );

		// These checks should all be against the parent product.
		if ( $product instanceof WC_Product_Variation ) {
			/**
			 * Variable Product object.
			 *
			 * @var WC_Product_Variable
			 */
			$product    = wc_get_product( $product->get_parent_id() );
			$product_id = $product->get_id();
		}

		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		if (
			$product->is_type(
				Cast::to_string(
					App::container()->getVar( 'Teams_WooCommerce_Product_Type' )
				)
			)
		) {
			return true;
		}

		$has_team_purchase_enable = get_post_meta( $product_id, SharedFunctions::$is_team_purchase_enable, true );

		// Product Types that aren't the Teams Product Type require this Post Meta to be set.
		if ( ! $has_team_purchase_enable ) {
			return false;
		}

		if ( $product->is_type( 'variable' ) ) {
			return true;
		}

		// The remaining Product Types require WooCommerce Subscriptions to be active.
		if ( ! SharedFunctions::is_woocommerce_subscription_active() ) {
			return false;
		}

		return $product->is_type( 'subscription' ) || $product->is_type( 'variable-subscription' );
	}
}
