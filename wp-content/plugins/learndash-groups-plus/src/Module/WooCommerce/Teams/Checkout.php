<?php
/**
 * WooCommerce Team Checkout functionality.
 *
 * @since 2.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore USERID
 */

namespace LearnDash\Groups_Plus\Module\WooCommerce\Teams;

use WC_Order;
use WC_Product;
use WC_Meta_Data;
use WC_Order_Item;
use WC_Order_Item_Product;

use WP_Post;
use WP_User;

use LearnDash\Groups_Plus\Module\Base as Module_Base;
use LearnDash\Groups_Plus\Module\Module_Interface;

use LearnDash\Core\Utilities\Cast;

use LearnDash\Groups_Plus\Utility\Database;
use LearnDash\Groups_Plus\Utility\SharedFunctions;

/**
 * Class Teams Checkout.
 *
 * @since 2.0.0
 */
class Checkout extends Module_Base implements Module_Interface {
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

		add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'create_order_item_meta' ], 20, 4 );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'create_order_meta' ], 10, 1 );
		add_action( 'woocommerce_store_api_checkout_update_order_meta', [ $this, 'create_order_meta' ], 10, 1 );

		add_action( 'woocommerce_order_status_completed', [ $this, 'order_completed' ], 99 );
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

		add_filter( 'woocommerce_order_item_display_meta_key', [ $this, 'change_order_item_meta_key_display' ], 20, 3 );
		add_filter( 'woocommerce_order_item_display_meta_value', [ $this, 'change_order_item_meta_value_display' ], 20, 3 );
	}

	/**
	 * Change the displayed titles for our Order Meta.
	 *
	 * @since 2.0.0
	 *
	 * @param string                $meta_key The meta key.
	 * @param WC_Meta_Data          $meta     The meta object.
	 * @param WC_Order_Item_Product $item     The order item object.
	 *
	 * @return string
	 */
	public function change_order_item_meta_key_display( $meta_key, $meta, $item ) {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			return $meta_key;
		}

		$product = $item->get_product();

		if ( ! $product instanceof WC_Product ) {
			return $meta_key;
		}

		if ( ! Add_To_Cart::product_can_be_used_for_teams( $product->get_id() ) ) {
			return $meta_key;
		}

		if ( 'team_name' === $meta_key ) {
			$meta_key = '<b>' . sprintf(
				// translators: Team.
				__( '%s name', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			) . '</b>';
		} elseif ( 'choose_team' === $meta_key ) {
			$meta_key = '<b>' . sprintf(
				// translators: Team.
				__( '%s name', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			) . '</b>';
		} elseif ( 'exclude_from_individual_seat_purchase' === $meta_key ) {
			$meta_key = '<b>' . esc_html(
				/**
				 * Filter to enable changing the label for the "Exclude from publicly sold seats" checkbox.
				 *
				 * @since 1.0.0
				 *
				 * @var string
				 */
				apply_filters(
					'change_label_of_exclude_from_publicly_sold_seats', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
					__( 'Exclude from publicly sold seats:', 'learndash-groups-plus' )
				)
			) . '</b>';
		} elseif ( 'my_team_courses' === $meta_key ) {
			$meta_key = '<b>' . learndash_get_custom_label( 'courses' ) . '</b>';
		}

		return $meta_key;
	}

	/**
	 * Change the displayed values for our Order Meta.
	 *
	 * @since 2.0.0
	 *
	 * @param string                $meta_value Displayed meta value.
	 * @param WC_Meta_Data          $meta       Meta object.
	 * @param WC_Order_Item_Product $item       Order item object.
	 *
	 * @return string
	 */
	public function change_order_item_meta_value_display( $meta_value, $meta, $item ) {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			return $meta_value;
		}

		$product = $item->get_product();

		if ( ! $product instanceof WC_Product ) {
			return $meta_value;
		}

		if ( ! Add_To_Cart::product_can_be_used_for_teams( $product->get_id() ) ) {
			return $meta_value;
		}

		// $meta->key does work due to its magic method, but phpstan doesn't like it.
		$meta_array = $meta->get_data();
		if ( empty( $meta_array['key'] ) ) {
			return $meta_value;
		}

		$meta_key = $meta_array['key'];

		if ( 'choose_team' === $meta_key ) {
			$choose_team_post = get_post( Cast::to_int( $meta_value ) );

			if ( ! $choose_team_post instanceof WP_Post ) {
				return $meta_value;
			}

			$meta_value = $choose_team_post->post_title;
		} elseif ( 'my_team_courses' === $meta_key ) {
			$my_team_courses = json_decode( $meta_value, true );

			if ( ! is_array( $my_team_courses ) ) {
				return $meta_value;
			}

			$course_ids = array_keys( $my_team_courses );
			ob_start();
			?>
			<br />
			<ul class="wc-item-meta">
				<?php
				foreach ( $course_ids as $course_id ) :
					$course_post = get_post( $course_id );

					if ( ! $course_post instanceof WP_Post ) {
						continue;
					}

					$course_price = wc_price(
						Cast::to_float(
							get_post_meta(
								$course_id,
								'_course_price',
								true
							)
						)
					);
					?>
					<li>
						<?php
						echo wp_kses_post(
							sprintf(
								// translators: Course Title (Course Price/seat).
								__( '%1$s (%2$s/seat)', 'learndash-groups-plus' ),
								$course_post->post_title,
								$course_price
							)
						);
						?>
					</li>

				<?php endforeach; ?>
			</ul>
			<?php
			$meta_value = Cast::to_string( ob_get_clean() );
		}

		return $meta_value;
	}

	/**
	 * Add our Cart Item Meta to the corresponding Order Items.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Order_Item_Product $item          The Order Line Item.
	 * @param string                $cart_item_key The Cart Item Key for this Order Line Item.
	 * @param array<mixed>          $values        The Cart Item Data for this Order Line Item.
	 * @param WC_Order              $order         The Order object.
	 *
	 * @return void
	 */
	public function create_order_item_meta( $item, $cart_item_key, $values, $order ) {
		if ( ! Add_To_Cart::product_can_be_used_for_teams( $item->get_product_id() ) ) {
			return;
		}

		$valid_keys = [
			'team_name',
			'choose_team',
			'exclude_from_individual_seat_purchase',
			'my_team_courses',
		];

		// Filter down $values to only include the data we want to directly interface with.
		$item_data = array_filter(
			$values,
			function( $key ) use ( $valid_keys ) {
				return in_array( $key, $valid_keys, true );
			},
			ARRAY_FILTER_USE_KEY
		);

		$json_encoded_fields = [
			'my_team_courses',
		];

		foreach ( $item_data as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			if ( in_array( $key, $json_encoded_fields, true ) ) {
				$value = wp_json_encode( $value );
			}

			$item->update_meta_data(
				$key,
				sanitize_text_field(
					wp_unslash(
						Cast::to_string(
							$value
						)
					)
				)
			);
		}
	}

	/**
	 * Creates additional Meta Data for the Order and otherwise prepare the Order for Completion by setting up the necessary transient.
	 *
	 * @since 2.0.0
	 * @since 2.1.0 Change parameter to accept int and WC_Order.
	 *
	 * @param int|WC_Order $order Order ID when created via [woocommerce_checkout], Order Object when created via the woocommerce/checkout block.
	 *
	 * @return void
	 */
	public function create_order_meta( $order ) {
		$order = wc_get_order( $order );
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$order_id = $order->get_id();
		$user     = $order->get_user();

		if ( ! $user instanceof WP_User ) {
			return;
		}

		// We only care about Order Items with a Team in them.
		$line_items = array_filter(
			$order->get_items( 'line_item' ),
			function( $order_item ) {
				return $this->is_valid_order_item( $order_item );
			}
		);

		if ( empty( $line_items ) ) {
			return;
		}

		$processed_items = 0;

		foreach ( $line_items as $item ) {
			/**
			 * Data to send to the Organization Purchases table.
			 *
			 * Teams are effectively Organizations without the ability to create Teams underneath them.
			 *
			 * @var array<string, mixed>
			 */
			$data = [
				'user_id'                               => ( $user->ID > 0 ) ? $user->ID : 0,
				'order_id'                              => $order_id,
				'order_status'                          => $order->get_status(),
				'organization_name'                     => '',
				'exclude_from_individual_seat_purchase' => 0,
				'organization_id'                       => 0,
				'my_organization_courses'               => wp_json_encode( [] ),
			];

			/**
			 * Which Keys should be extracted from the Order Item Meta to be stored in the Organization Purchases table.
			 *
			 * Key: Order Item Meta Key.
			 * Value: Organization Purchases table column.
			 *
			 * @var array<string, string>
			 */
			$key_map = [
				'team_name'                             => 'organization_name',
				'choose_team'                           => 'organization_id',
				'my_team_courses'                       => 'my_organization_courses',
				'exclude_from_individual_seat_purchase' => 'exclude_from_individual_seat_purchase',
			];

			foreach ( $key_map as $meta_key => $data_column ) {
				if ( empty( $item->get_meta( $meta_key ) ) ) {
					continue;
				}

				$data[ $data_column ] = sanitize_text_field(
					wp_unslash(
						Cast::to_string(
							$item->get_meta( $meta_key )
						)
					)
				);
			}

			Database::add_organization_purchase( $data );
			$processed_items++;
		}

		// Set a flag to process the order completion in groups plus.
		$order->update_meta_data( SharedFunctions::$process_order_meta_field, '1' );
		$order->save_meta_data();
	}

	/**
	 * On Order Completion, create a Team.
	 *
	 * @since 2.0.0
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function order_completed( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		if ( ! $order->has_status( 'completed' ) ) {
			return;
		}

		if (
			(
				function_exists( 'wcs_order_contains_renewal' )
				&& wcs_order_contains_renewal( $order )
			)
			|| (
				function_exists( 'wcs_get_subscriptions_for_resubscribe_order' )
				&& ! empty(
					wcs_get_subscriptions_for_resubscribe_order( $order )
				)
			)
		) {
			return;
		}

		$user = $order->get_user();

		if ( ! $user instanceof WP_User ) {
			return;
		}

		// We only need to process the order once. That's why we check if the flag is set.
		$process_order = Cast::to_bool( $order->get_meta( SharedFunctions::$process_order_meta_field ) );

		if ( ! $process_order ) {
			return;
		}

		// We only care about Order Items with a Team in them.
		$line_items = array_filter(
			$order->get_items( 'line_item' ),
			function( $order_item ) {
				return $this->is_valid_order_item( $order_item );
			}
		);

		if ( empty( $line_items ) ) {
			return;
		}

		$processed_items = 0;
		$team_ids        = [];

		foreach ( $line_items as $item ) {
			$team_id = $this->process_team( $item, $user );

			if ( empty( $team_id ) ) {
				continue;
			}

			$number_of_licenses = $this->calculate_seat_count( $item, $team_id );
			$team_courses       = $this->get_course_ids( $item );

			// Process the team for this line item.
			if (
				! in_array( 'group_leader', $user->roles, true )
				&& ! in_array( 'administrator', $user->roles, true )
			) {
				// Add Group Leader role if needed.
				$user->add_role( 'group_leader' );
			}

			// Store the team ID for this line item.
			$team_ids[] = $team_id;

			// Set a flag to identify this as a Non-Organization Team.
			update_post_meta( $team_id, SharedFunctions::$is_non_organization_team, true );

			update_post_meta( $team_id, 'number_of_licenses', $number_of_licenses );

			// Add user to team group as leader.
			ld_update_leader_group_access( $user->ID, $team_id );

			// enrolled courses to primary group.
			foreach ( $team_courses as $course_id ) {
				ld_update_course_group_access(
					Cast::to_int( $course_id ),
					$team_id
				);
			}

			$processed_items++;
		}

		// If we processed any items, store the team IDs in order meta and clean up.
		if ( $processed_items > 0 ) {
			// Store all team IDs in order meta (comma-separated if multiple).
			update_post_meta( $order_id, SharedFunctions::$linked_group_id_meta, implode( ',', $team_ids ) );

			// We remove the flag so that it won't be processed again in the future after status updates.
			$order->delete_meta_data( SharedFunctions::$process_order_meta_field );
			$order->save_meta_data();
		}
	}

	/**
	 * Processes a Order Item on Order Completion to create/update a Team.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Order_Item $item Order Item object for creating/updating the Team.
	 * @param WP_User       $user       User object to use as the Team Author.
	 *
	 * @return int Team ID on success, 0 on failure.
	 */
	protected function process_team( WC_Order_Item $item, $user ): int {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			return 0;
		}

		$product = wc_get_product( $item->get_product_id() );

		if ( ! $product instanceof WC_Product ) {
			return 0;
		}

		$team_id = 0;

		if ( ! empty( $item->get_meta( 'team_name' ) ) ) {
			$team_name                             = sanitize_text_field(
				wp_unslash(
					Cast::to_string(
						$item->get_meta( 'team_name' )
					)
				)
			);
			$exclude_from_individual_seat_purchase = sanitize_text_field(
				wp_unslash(
					Cast::to_string(
						$item->get_meta( 'exclude_from_individual_seat_purchase' )
					)
				)
			);

			$team_id = $this->create_team( $team_name, $user->ID );

			if ( $team_id <= 0 ) {
				return 0;
			}

			update_post_meta( $team_id, 'has_group_created_thru_wc', true );
			update_post_meta( $team_id, 'exclude_from_individual_seat_purchase', $exclude_from_individual_seat_purchase );
			update_post_meta( $team_id, 'has_group_created_for_courses_sell', true );
		} elseif ( ! empty( $item->get_meta( 'choose_team' ) ) ) {
			$team_id = Cast::to_int( $item->get_meta( 'choose_team' ) );
		}

		if ( $team_id <= 0 ) {
			return 0;
		}

		return $team_id;
	}

	/**
	 * Create a Team with a given name.
	 *
	 * @since 2.0.0
	 *
	 * @param string $team_name Team name.
	 * @param int    $user_id   User ID to use as the Author.
	 *
	 * @return int Team ID or 0 if a Team could not be created.
	 */
	protected function create_team( string $team_name, int $user_id = 0 ): int {
		if ( $user_id <= 0 ) {
			$user_id = get_current_user_id();
		}

		// Don't allow a Team to be created without a valid author.
		if ( ! $user_id ) {
			return 0;
		}

		/**
		 * This filter is documented in src/Module/WooCommerce/Organizations.php
		 */
		$user_id = apply_filters(
			'custom_group_post_author', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- This is an existing filter that is used in other areas.
			$user_id,
			get_current_user_id(),
			'groups-plus-teams-purchase'
		);

		$team_id = wp_insert_post(
			[
				'post_type'    => 'groups',
				'post_status'  => 'publish',
				'post_title'   => $team_name,
				'post_content' => '',
				'post_author'  => $user_id,
			]
		);

		/**
		 * Fires on Team creation.
		 *
		 * @since 2.0.0
		 *
		 * @param int $team_id Team Post ID, or 0 on failure.
		 * @param int $user_id Author User ID.
		 *
		 * @return void
		 */
		do_action( 'learndash_groups_plus_team_created', $team_id, $user_id );

		return $team_id;
	}

	/**
	 * Checks if a given Order Item is valid for a Team purchase.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Order_Item $item Order Item object.
	 *
	 * @return bool
	 */
	protected function is_valid_order_item( WC_Order_Item $item ): bool {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			return false;
		}

		if ( ! Add_To_Cart::product_can_be_used_for_teams( $item->get_product_id() ) ) {
			return false;
		}

		if (
			empty( $item->get_meta( 'team_name' ) )
			&& empty( $item->get_meta( 'choose_team' ) )
		) {
			return false;
		}

		// If this is a variable product, the rest of the checks do not apply.
		if ( ! empty( $item->get_variation_id() ) ) {
			return true;
		}

		if (
			empty( $item->get_meta( 'my_team_courses' ) )
			|| ! is_array(
				json_decode(
					Cast::to_string( $item->get_meta( 'my_team_courses' ) ),
					true
				)
			)
		) {
			return false;
		}

		return true;
	}

	/**
	 * Calculates the Seats for a given Team after adding newly purchased Seats from the Order Item.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Order_Item $item    Order Item object.
	 * @param int           $team_id Team ID.
	 *
	 * @return int
	 */
	protected function calculate_seat_count( WC_Order_Item $item, int $team_id ): int {
		// If a Team was chosen during the purchase, Seats could already exist for the Team.
		$old_seats = Cast::to_int(
			get_post_meta(
				$team_id,
				'number_of_licenses',
				true
			)
		);

		if ( ! $item instanceof WC_Order_Item_Product ) {
			return $old_seats;
		}

		$product = wc_get_product( $item->get_product_id() );

		if ( ! $product instanceof WC_Product ) {
			return $old_seats;
		}

		if (
			! $product->is_type( 'variable' )
			&& (
				! SharedFunctions::is_woocommerce_subscription_active()
				|| ! $product->is_type( 'variable-subscription' )
			)
		) {
			// Non-Variable Products get their Seats via Quantity.
			return $old_seats + $item->get_quantity();
		}

		// Seats are set in the Variation rather as Post Meta.
		$variation_seats = Cast::to_int(
			get_post_meta(
				$item->get_variation_id(),
				SharedFunctions::$variable_product_allow_seats,
				true
			)
		);

		return $old_seats + $variation_seats;
	}

	/**
	 * Get the Courses chosen for a given Order Item.
	 *
	 * @since 2.0.0
	 *
	 * @param WC_Order_Item $item Order Item object.
	 *
	 * @return array<int, int|string>
	 */
	protected function get_course_ids( WC_Order_Item $item ): array {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			return [];
		}

		$product = wc_get_product( $item->get_product_id() );

		if ( ! $product instanceof WC_Product ) {
			return [];
		}

		if (
			! $product->is_type( 'variable' )
			&& (
				! SharedFunctions::is_woocommerce_subscription_active()
				|| ! $product->is_type( 'variable-subscription' )
			)
		) {
			$course_data = json_decode(
				Cast::to_string( $item->get_meta( 'my_team_courses' ) ),
				true
			);

			if (
				empty( $course_data )
				|| ! is_array( $course_data )
			) {
				return [];
			}

			return array_keys(
				$course_data
			);
		}

		$course_ids = get_post_meta(
			$item->get_variation_id(),
			SharedFunctions::$groups_plus_team_courses_meta_field,
			true
		);

		if (
			empty( $course_ids )
			|| ! is_array( $course_ids )
		) {
			return [];
		}

		return $course_ids;
	}
}
