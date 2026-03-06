<?php
/**
 * WooCommerce Additional Seats Module.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore USERID coures thepostid configration nopaging classname WPINC
 */

namespace LearnDash\Groups_Plus\Module\WooCommerce;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use InvalidArgumentException;
use LearnDash\Groups_Plus\Model\WooCommerce\Product\Seats as Seats_Product;
use LearnDash\Groups_Plus\Module\Group;
use LearnDash\Groups_Plus\Utility\Database;
use LearnDash\Groups_Plus\Utility\SharedFunctions;
use WC_Subscriptions_Product;
use LearnDash\Core\Utilities\Cast;

use WC_Order;
use WC_Product;
use WC_Cart;
use WP_Post;

/**
 * Class Seats
 */
class Seats {

	/**
	 * The instance of the class
	 *
	 * @since    2.0.0
	 *
	 * @access   private
	 *
	 * @var      Seats
	 */
	private static $instance = null;

	/**
	 * Organizations seat WooCommerce product type.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $product_type = 'groups_plus_seats';

	/**
	 * WC constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		if ( SharedFunctions::is_woocommerce_active() ) {
			add_action( 'init', array( $this, 'plugins_loaded' ), 20 );
			add_action( 'init', array( $this, 'load_new_product_type' ), 11 );
		}
	}

	/**
	 * Creates singleton instance of class.
	 *
	 * @since   2.0.0
	 *
	 * @return  Seats $instance The Seats Instance.
	 */
	public static function get_instance(): Seats {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 *
	 */
	public function plugins_loaded() {
		if ( 'yes' === get_option( 'enable_wc', 'no' ) ) {
			add_filter( 'woocommerce_product_class', array( $this, 'woocommerce_product_class' ), 20, 4 );
			add_filter( 'product_type_selector', array( $this, 'add_courses_product' ), 40 );
			add_filter( 'product_type_options', array( $this, 'add_virtual_and_downloadable_checks' ) );
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'custom_product_tabs' ), 40 );
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'wc_before_add_to_cart_btn' ) );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_dropdown_value_to_cart_item_data' ), 10, 4 );

			add_filter( 'woocommerce_get_item_data', [ $this, 'populate_item_data' ], 10, 2 );

			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'wc_checkout_create_order_line_item' ), 20, 4 );

			add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'change_order_item_meta_title' ), 20, 3 );
			add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'change_order_item_meta_value' ), 20, 3 );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'wc_checkout_update_order_meta' ), 10, 1 );
			add_action( 'woocommerce_store_api_checkout_update_order_meta', [ $this, 'wc_checkout_update_order_meta' ], 10, 1 );

			add_action( 'woocommerce_product_data_panels', array( $this, 'groups_plus_courses_options_product_tab_content' ) );
			add_action( 'woocommerce_process_product_meta_groups_plus_seats', array( $this, 'save_groups_plus_seat_configuration_option_field' ) );
			add_action( 'woocommerce_process_product_meta_subscription', array( $this, 'save_groups_plus_seat_configuration_option_field' ) );
			add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_groups_plus_seat_configuration_option_field' ) );
			add_action( 'woocommerce_process_product_meta_variable-subscription', array( $this, 'save_groups_plus_seat_configuration_option_field' ) );

			add_action( 'woocommerce_groups_plus_seats_add_to_cart', array( $this, 'woocommerce_simple_add_to_cart' ), 30 );
			add_filter( 'woocommerce_cart_item_name', array( $this, 'woocommerce_cart_item_name_func' ), 99, 3 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'change_product_price_display' ), 70, 2 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'change_product_cart_item_price' ), 99, 3 );

			add_action( 'woocommerce_order_status_completed', array( $this, 'process_groups_plus_seats_order_complete' ), 99 );

			add_action( 'admin_footer', array( $this, 'groups_plus_course_custom_js' ) );

			add_action( 'wp_loaded', array( $this, 'add_seats_to_groups_plus' ), 500 );

			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_cart' ), 100, 5 );

			// Add meta for sfwd-courses.
			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ), 10, 2 );
			add_action( 'save_post', array( $this, 'save_group_price' ), 20, 2 );
			add_action( 'woocommerce_before_calculate_totals', [ $this, 'apply_groups_plus_price_to_cart_item' ], 9, 4 );

			add_filter( 'woocommerce_subscriptions_product_price_string_inclusions', [ $this, 'force_sign_up_fee_subtotal_string' ], 10, 2 );

			add_filter( 'woocommerce_subscriptions_cart_get_price', [ $this, 'remove_sign_up_fee_from_product_price_calculations' ], 10, 2 );

			add_action( 'woocommerce_cart_calculate_fees', [ $this, 'add_sign_up_fee_as_a_real_fee' ], 10, 1 );

			add_action( 'wp_ajax_get_groups_plus_price', array( $this, 'get_groups_plus_price' ) );
			add_action( 'wp_ajax_nopriv_get_groups_plus_price', array( $this, 'get_groups_plus_price' ) );

			/********SUBSCRIPTION HOOKS*/
			add_action( 'woocommerce_subscription_status_expired', array( $this, 'remove_group_access' ), 99, 2 );
			add_action( 'woocommerce_subscription_status_on-hold', array( $this, 'remove_group_access' ), 99, 2 );
			add_action( 'woocommerce_subscription_status_cancelled', array( $this, 'remove_group_access' ), 99, 2 );
			add_action( 'woocommerce_subscription_status_pending-cancel', array( $this, 'remove_group_access' ), 99, 2 );
			add_action( 'woocommerce_subscription_status_active', array( $this, 'give_group_access' ), 99, 2 );
		}
	}

	/**
	 * Filter WooCommerce class name used for Organizations seat product.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function woocommerce_product_class( $classname, $product_type, $variation_type, $product_id ) {
		if ( $product_type === $this->product_type ) {
			$classname = Seats_Product::class;
		}

		return $classname;
	}

	/**
	 * @param $types
	 *
	 * @return mixed
	 */
	public function add_courses_product( $types ) {
		$types[ $this->product_type ] = __( 'LearnDash Seats', 'learndash-groups-plus' );

		return $types;

	}

	/**
	 * Add a custom product tab.
	 */
	function custom_product_tabs( $tabs ) {
		$tabs[ $this->product_type ] = array(
			'label'  => __( 'LearnDash Seats', 'learndash-groups-plus' ),
			'target' => 'groups_plus_seats_configration',
			'class'  => array( 'show_if_variable', 'show_if_subscription' ),
		);

		return $tabs;
	}

	/**
	 * Add form elements to the Product Single Template above the add to cart button.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function wc_before_add_to_cart_btn() {
		global $product;
		$product_id = $product->get_id();

		if ( $this->check_if_groups_plus_seats_product_in_items( $product_id ) ) {
			$product_id         = $product->get_id();
			$is_individual_seat = get_post_meta( $product_id, SharedFunctions::$individual_seat_meta_field, true );
			// if ($is_individual_seat === 'on'){
				$ld_groups = $this->get_ld_groups_hierarchy();

			wp_nonce_field( 'learndash_groups_plus_add_seat_product', 'groups_plus_seat_product_nonce' );

			echo '<table class="variations" cellspacing="0">';
			echo '			<tbody>';
			echo '				<tr> ';
			echo '					<td class="label"><label for="secondary_group"> ' . sprintf(
				esc_html__( 'Choose %s', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			) . ' </label></td>';
			echo '					<td class="value">';
			echo '						<select name="secondary_group" id="secondary_group" data-product-id="' . $product_id . '" required>';
			echo '<option value="">' . sprintf(
				esc_html__( 'Select %s', 'learndash-groups-plus' ),
				learndash_get_custom_label_lower( 'team' )
			) . '</option>';

			foreach ( $ld_groups as $ld_group ) {
				echo '	<optgroup label="' . esc_attr( $ld_group['name'] ) . '">';
				foreach ( $ld_group['child_groups'] as $child_group ) {
					echo '<option value="' . esc_attr( $child_group['id'] ) . '" class="attached enabled">' . __( $child_group['name'], 'learndash-groups-plus' ) . '</option>';
				}
				echo '	</optgroup>';
			}
			echo '						</select>';
			echo '					</td>';
			echo '				</tr>';
			echo '		</tbody> ';
			echo '</table>';
			// }
		}

	}

	/**
	 * Add Cart Item Data while adding to the Cart.
	 *
	 * @since 1.0.0
	 *
	 * @param array<mixed> $cart_item_data Cart Item Data.
	 * @param int          $product_id     Product ID.
	 * @param int          $variation_id   Variation ID.
	 *
	 * @return array<mixed>
	 */
	public function add_dropdown_value_to_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
		if (
			(
				! SharedFunctions::filter_has_var( 'nonce', INPUT_GET )
				&& ! SharedFunctions::filter_has_var( 'groups_plus_seat_product_nonce', INPUT_POST )
			)
			|| (
				// Groups Plus Dashboard.
				! wp_verify_nonce(
					Cast::to_string(
						SharedFunctions::filter_input(
							'nonce',
							INPUT_GET
						)
					),
					'learndash_groups_plus_add_seat_product'
				)
				// Seats Product page.
				&& ! wp_verify_nonce(
					Cast::to_string(
						SharedFunctions::filter_input(
							'groups_plus_seat_product_nonce',
							INPUT_POST
						)
					),
					'learndash_groups_plus_add_seat_product'
				)
			)
		) {
			return $cart_item_data;
		}

		$get_data  = wp_unslash( $_GET );
		$post_data = wp_unslash( $_POST );

		if (
			empty( $get_data['secondary_group'] )
			&& empty( $post_data['secondary_group'] )
		) {
			return $cart_item_data;
		}

		// Add the dropdown value as custom cart item data.
		if ( ! empty( $get_data['secondary_group'] ) ) {
			$cart_item_data['secondary_group'] = sanitize_text_field(
				$get_data['secondary_group']
			);
		} else {
			$cart_item_data['secondary_group'] = sanitize_text_field(
				$post_data['secondary_group']
			);
		}

		$product = wc_get_product( $product_id );
		if ( ! $product instanceof WC_Product ) {
			return $cart_item_data;
		}

		$is_individual_seat_purchase_enable = get_post_meta( $product_id, SharedFunctions::$is_individual_seat_purchase_enable, true );
		if (
			$product instanceof \WC_Product
			&& $product->is_type( $this->product_type )
		) {
			$groups_plus_price = get_post_meta( $cart_item_data['secondary_group'], '_groups_plus_price', true );
		} elseif (
			$product instanceof \WC_Product
			&& SharedFunctions::is_woocommerce_subscription_active()
			&& $product->is_type( 'subscription' )
			&& $is_individual_seat_purchase_enable
		) {
			$groups_plus_price = get_post_meta( $cart_item_data['secondary_group'], '_groups_plus_sub_seat_price', true );
		} else {
			$groups_plus_price = get_post_meta( $cart_item_data['secondary_group'], '_groups_plus_price', true );
		}

		if ( ! empty( $groups_plus_price ) ) {
			$cart_item_data['groups_plus_price'] = $groups_plus_price;
		} else {
			// Fallback to the actual Product Price.
			$cart_item_data['groups_plus_price'] = $product->get_price();
		}

		return $cart_item_data;
	}

	/**
	 * Populate Item Data for the Cart Item, which will cause WooCommerce to generate the necessary DOM below the Cart Item Name.
	 *
	 * @since 2.1.0
	 *
	 * @param array<int,array{key: string, value: string}> $item_data      Item Data array.
	 * @param array<string,mixed>                          $cart_item_data Cart Item Data, which is separate from the Item Data which is assigned to the Cart Item later in the process.
	 *
	 * @return array<int,array{key: string, value: string}>
	 */
	public function populate_item_data( $item_data, $cart_item_data ) {
		$cart_item_data = wp_parse_args(
			$cart_item_data,
			[
				'product_id' => 0,
			]
		);

		if (
			! $this->check_if_groups_plus_seats_product_in_items(
				Cast::to_int( $cart_item_data['product_id'] )
			)
		) {
			return $item_data;
		}

		$valid_cart_data = array_filter( $this->filter_valid_cart_data( $cart_item_data ) );

		// Take the passed in secondary_group and ensure that we have the necessary data to cleanly show both Organization and Team name where necessary.
		if ( ! empty( $valid_cart_data['secondary_group'] ) ) {
			$secondary_group = Cast::to_int( $valid_cart_data['secondary_group'] );
			$organization_id = Group::get_parent_group_id( $secondary_group );

			if (
				$organization_id
				&& $organization_id !== $secondary_group
			) {
				// Place Organization above Team in the Array.
				$valid_cart_data = array_merge(
					[
						'primary_group' => $organization_id,
					],
					$valid_cart_data
				);
			} else {
				if (
					! get_post_meta(
						$secondary_group,
						SharedFunctions::$is_non_organization_team,
						true
					)
				) {
					// These Seats were purchased for an Organization directly, so remove secondary_group from the Array.
					$valid_cart_data['primary_group'] = $secondary_group;
					unset( $valid_cart_data['secondary_group'] );
				}
			}
		}

		foreach ( $valid_cart_data as $key => $item_data_value ) {
			$item_data_name = '';

			switch ( $key ) {
				case 'primary_group':
				case 'secondary_group':
					if ( $key === 'primary_group' ) {
						$item_data_name = sprintf(
							// translators: placeholder: Organization label.
							esc_html__( '%s name', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'organization' )
						);
					} else {
						$item_data_name = sprintf(
							// translators: placeholder: Team label.
							esc_html__( '%s name', 'learndash-groups-plus' ),
							learndash_get_custom_label( 'team' )
						);
					}

					$item_data_value = get_the_title( Cast::to_int( $item_data_value ) );
					break;
				default:
					break;
			}

			$item_data_value = Cast::to_string( $item_data_value );

			/** This filter is documented in src/Module/WooCommerce/Teams/Add_To_Cart.php */
			$item_data_name = apply_filters(
				'learndash_groups_plus_cart_item_data_name',
				$item_data_name,
				$item_data,
				$cart_item_data,
			);

			/** This filter is documented in src/Module/WooCommerce/Teams/Add_To_Cart.php */
			$item_data_value = apply_filters(
				'learndash_groups_plus_cart_item_data_value',
				$item_data_value,
				$item_data,
				$cart_item_data,
			);

			/** This filter is documented in src/Module/WooCommerce/Teams/Add_To_Cart.php */
			$item_data[] = apply_filters(
				'learndash_groups_plus_cart_item_data',
				[
					'key'    => $item_data_name,
					'value'  => $item_data_value,
				],
				$item_data,
				$cart_item_data
			);
		}

		return $item_data;
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
			'learndash_groups_plus_seats_valid_cart_item_data_keys',
			[
				'secondary_group',
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

	// Cart page: Display dropdown value after the cart item name
	function display_dropdown_value_after_cart_item_name( $name, $cart_item, $cart_item_key ) {
		if ( is_cart() && isset( $cart_item['secondary_group'] ) ) {
			$group            = get_post( $cart_item['secondary_group'] );
			$organization_obj = get_post_parent( $cart_item['secondary_group'] );
			if ( ! empty( $organization_obj ) ) {
				$name .= '<p><strong>' . sprintf(
					__( '%s name:', 'learndash-groups-plus' ),
					learndash_get_custom_label( 'organization' )
				) . '</strong> ' . esc_html( $organization_obj->post_title ) . '</p>';
			}
			$name .= '<p><strong>' . sprintf(
				esc_html__( '%s name', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			) . ':</strong> ' . esc_html( $group->post_title ) . '</p>';
		}
		return $name;
	}

	function wc_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['secondary_group'] ) ) {
			$organization_obj = get_post_parent( $values['secondary_group'] );
			if ( ! empty( $organization_obj ) ) {
				$item->update_meta_data( 'primary_organization', $organization_obj->ID );
			}
			$item->update_meta_data( 'secondary_group', $values['secondary_group'] );
		}
	}

	/**
	 * Changing a meta title
	 *
	 * @param  string        $key  The meta key
	 * @param  WC_Meta_Data  $meta The meta object
	 * @param  WC_Order_Item $item The order item object
	 * @return string        The title
	 */
	function change_order_item_meta_title( $key, $meta, $item ) {

		// By using $meta-key we are sure we have the correct one.
		if ( 'primary_organization' === $meta->key ) {
			$key = '<strong>' . sprintf(
				__( '%s name:', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'organization' )
			) . '</strong>'; }

		if ( 'secondary_group' === $meta->key ) {
			$key = '<strong>' . sprintf(
				esc_html__( '%s name', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			) . '</strong>'; }

		return $key;
	}

	/**
	 * Changing a meta value
	 *
	 * @param  string        $value  The meta value
	 * @param  WC_Meta_Data  $meta   The meta object
	 * @param  WC_Order_Item $item   The order item object
	 * @return string        The title
	 */
	function change_order_item_meta_value( $value, $meta, $item ) {

		// By using $meta-key we are sure we have the correct one.
		if ( 'secondary_group' === $meta->key ) {
			$group = get_post( $value );
			$value = esc_html( $group->post_title );
		}

		if ( 'primary_organization' === $meta->key ) {
			$group = get_post( $value );
			$value = esc_html( $group->post_title );
		}

		return $value;
	}

	/**
	 * Creates additional Meta Data for the Order and otherwise prepare the Order for Completion by setting up the necessary transient.
	 *
	 * @since 1.0.0
	 * @since 2.1.0 Change parameter to accept int and WC_Order.
	 *
	 * @param int|WC_Order $order Order ID when created via [woocommerce_checkout], Order Object when created via the woocommerce/checkout block.
	 *
	 * @return void
	 */
	public function wc_checkout_update_order_meta( $order ) {
		$order = wc_get_order( $order );
		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		$order_id     = $order->get_id();
		$user         = $order->get_user();
		$order_status = $order->get_status();
		$user_id      = isset( $user->ID ) ? $user->ID : 0;

		$product_id = 0;
		$group_id   = 0;
		$_quantity  = 0;
		$line_items = $order->get_items( 'line_item' );
		if ( ! $line_items ) {
			return;
		}

		$continue              = false;
		$ld_primary_group_id   = $last_seats_count = 0;
		$ld_secondary_group_id = 0;

		foreach ( $line_items as $item_id => $item ) {
			$_quantity = $item->get_quantity();
			if (
				$this->check_if_groups_plus_seats_product_in_items(
					Cast::to_int( $item['product_id'] )
				)
				&& isset( $item['secondary_group'] )
			) {
				$product_id            = $item['product_id'];
				$ld_secondary_group_id = Cast::to_int( $item['secondary_group'] );
				$ld_primary_group_id   = Group::get_parent_group_id( $ld_secondary_group_id );
				$last_seats_count      = (int) get_post_meta( $ld_primary_group_id, 'number_of_licenses', true );
				$continue              = true;
				break;
			}
		}
		if ( false === $continue ) {
			return;
		}

		// Set a flag to process the order completion in groups plus.
		$order->update_meta_data( SharedFunctions::$process_order_meta_field, '1' );
		$order->save_meta_data();

		$attr = array(
			'user_id'               => $user_id,
			'order_id'              => $order_id,
			'order_status'          => $order_status,
			'ld_primary_group_id'   => $ld_primary_group_id,
			'ld_secondary_group_id' => $ld_secondary_group_id,
			'last_seats_count'      => $last_seats_count,
		);
		Database::add_seat_purchase( $attr );
	}

	private function get_ld_groups_hierarchy() {
		$group_orderby = ! empty( get_site_option( 'group_orderby' ) ) ? get_site_option( 'group_orderby' ) : 'title';
		$group_order   = ! empty( get_site_option( 'group_order' ) ) ? get_site_option( 'group_order' ) : 'ASC';

		$primary_groups_query_args = array(
			'post_type'   => 'groups',
			'nopaging'    => true,
			'post_parent' => 0,
			'post_status' => array( 'publish' ),
			'orderby'     => $group_orderby,
			'order'       => $group_order,
			's'           => '-[FAMILY]',
			'meta_query'  => array(
				array(
					'key'     => '_is_group_wc_enabled',
					'value'   => '1',
					'compare' => '!=',
				),
				array(
					'key'     => '_is_group_wc_enabled',
					'compare' => 'NOT EXISTS',
				),
				'relation' => 'OR',
			),
		);

		$primary_groups_query = new \WP_Query( $primary_groups_query_args );
		$primary_groups       = $primary_groups_query->posts;
		$ld_groups            = array();
		foreach ( $primary_groups as $primary_group ) {
			$child_groups = array();

			$secondary_groups_query_args = array(
				'post_type'   => 'groups',
				'nopaging'    => true,
				'post_parent' => $primary_group->ID,
				'post_status' => array( 'publish' ),
				'orderby'     => $group_orderby,
				'order'       => $group_order,
				's'           => '-[FAMILY]',
			);

			$secondary_groups_query = new \WP_Query( $secondary_groups_query_args );
			$secondary_groups       = $secondary_groups_query->posts;
			foreach ( $secondary_groups as $secondary_group ) {
				$child_groups[] = array(
					'id'   => $secondary_group->ID,
					'name' => $secondary_group->post_title,
				);
			}
			$exclude_from_individual_seat_purchase = (int) get_post_meta( $primary_group->ID, 'exclude_from_individual_seat_purchase', true );
			if ( empty( $exclude_from_individual_seat_purchase ) ) {
				$ld_groups[] = array(
					'id'           => $primary_group->ID,
					'name'         => $primary_group->post_title,
					'child_groups' => $child_groups,
				);
			}
		}
		return $ld_groups;
	}

	/**
	 * Contents of the courses options product tab.
	 */
	function groups_plus_courses_options_product_tab_content() {
		global $post, $woocommerce;
		$course_products = $this->list_course_products();
		?>
<div id='groups_plus_seats_configration' class='panel woocommerce_options_panel'>	<div class='options_group show_if_variable show_if_subscription'>

		<?php
			$this->woocommerce_wp_checkbox_individual(
				array(
					'id'          => SharedFunctions::$is_individual_seat_purchase_enable,
					'name'        => SharedFunctions::$is_individual_seat_purchase_enable,
					'label'       => __( 'Enable seat purchase', 'learndash-groups-plus' ),
					'description' => __( 'check to sell individual seat purchase', 'learndash-groups-plus' ),
				)
			);
		?>
	</div>

</div>
		<?php
	}

	/**
	 * @param $field
	 */
	public function woocommerce_wp_checkbox_individual( $field ) {
		global $thepostid, $post, $woocommerce;
		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['value']         = isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : 'off' );
		$field['checked']       = isset( $field['value'] ) && $field['value'] === 'on' ? "checked='checked'" : '';

		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
			<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>
			<input type="checkbox" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="on"
				' . esc_attr( $field['checked'] ) . '';
		echo '</p>';
	}



	/**
	 * @return array
	 */
	public function list_course_products() {
		$query_args = array(
			'post_type'      => 'product',
			'posts_per_page' => 999,
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'groups_plus_organizations',
				),
			),
		);
		$posts      = get_posts( $query_args );
		$license    = array();
		if ( $posts ) {
			foreach ( $posts as $post ) {
				$license[ $post->ID ] = get_the_title( $post->ID );
			}
		}

		return $license;
	}

	/**
	 * @param $post_id
	 */
	public function save_groups_plus_seat_configuration_option_field( $post_id ) {
		if ( isset( $_POST[ SharedFunctions::$is_individual_seat_purchase_enable ] ) ) {
			update_post_meta( $post_id, SharedFunctions::$is_individual_seat_purchase_enable, $_POST[ SharedFunctions::$is_individual_seat_purchase_enable ] );
		} else {
			delete_post_meta( $post_id, SharedFunctions::$is_individual_seat_purchase_enable );
		}
	}

	/**
	 *
	 */
	public function load_new_product_type() {
		new Seats_Product( $this->product_type );
	}

	/**
	 *
	 */
	public function woocommerce_simple_add_to_cart() {
		wc_get_template( 'single-product/add-to-cart/simple.php' );
	}

	/**
	 * @param $options
	 *
	 * @return mixed
	 */
	public function add_virtual_and_downloadable_checks( $options ) {

		if ( isset( $options['virtual'] ) ) {
			$options['virtual']['wrapper_class'] = $options['virtual']['wrapper_class'] . ' show_if_groups_plus_seats';
		}

		if ( isset( $options['downloadable'] ) ) {
			$options['downloadable']['wrapper_class'] = $options['downloadable']['wrapper_class'] . ' show_if_groups_plus_seats';
		}

		return $options;
	}

	/**
	 * @param $permalink
	 * @param $cart_item
	 * @param $cart_item_key
	 *
	 * @return bool
	 */
	public function woocommerce_cart_item_name_func( $title, $cart_item, $cart_item_key ) {
		if ( ! empty( $cart_item ) ) {
			$product = $cart_item['data'];
			if ( $product instanceof \WC_Product && $product->is_type( 'license' ) ) {
				$courses      = '';
				$course_names = array();
				$courses      = get_post_meta( $cart_item['product_id'], SharedFunctions::$seats_course_meta_field, true );
				$courses_new  = get_post_meta( $cart_item['product_id'], SharedFunctions::$seats_course_meta_field . '_new', true );
				if ( ! empty( $courses_new ) ) {
					foreach ( $courses_new as $new ) {
						$course_names[] = get_the_title( $new );
					}
				} else {
					if ( ! empty( $courses ) ) {
						foreach ( $courses as $c ) {
							$course_names[] = get_the_title( $c );
						}
					}
				}
				if ( ! empty( $course_names ) ) {
					$learn_dash_labels = new \LearnDash_Custom_Label();
					$course_label      = $learn_dash_labels::get_label( 'courses' );
					$courses           = '<p class="coures-assigned-heading">' . $course_label . ':</p><ul class="courses-assigned">';
					foreach ( $course_names as $n ) {
						$courses .= '<li>' . $n . '</li>';
					}
					$courses .= '</ul>';

				}

				return $title . $courses;
			}
		}

		return $title;
	}


	/**
	 * @param $price
	 * @param $product
	 *
	 * @return string
	 */
	public function change_product_price_display( $price, $product ) {
		$product_id                         = $product->get_id();
		$is_individual_seat_purchase_enable = get_post_meta( $product_id, SharedFunctions::$is_individual_seat_purchase_enable, true );

		// if ( $product instanceof \WC_Product && $product->is_type( $this->product_type ) || ( SharedFunctions::is_woocommerce_subscription_active() &&  $product->is_type( 'subscription' ) && $is_individual_seat_purchase_enable) ) {
		if ( $this->check_if_groups_plus_seats_product_in_items( $product_id ) ) {
			$price = $this->add_per_seat_text( $price, $product );
		}

		return $price;
	}

	/**
	 * @param $price
	 * @param \WC_Product $product
	 *
	 * @return mixed|void
	 */
	public function add_per_seat_text( $price, \WC_Product $product ) {

		// Value can be saved on settings page
		$per_seat_text = __( 'Seat', 'learndash-groups-plus' );
		$price         = preg_replace( '/\<\/bdi\>\<\/span\>/', "</bdi> &#47; $per_seat_text</span>", $price );

		return $price;
	}

	/**
	 * @param $price
	 * @param $cart_item
	 * @param $cart_item_key
	 *
	 * @return string
	 */
	public function change_product_cart_item_price( $price, $cart_item, $cart_item_key ) {
		if ( ! empty( $cart_item ) ) {
			if ( is_array( $cart_item ) ) {
				$cart_item = $cart_item['data'];
			}

			if ( $cart_item instanceof \WC_Product && $cart_item->is_type( $this->product_type ) ) {
				$price = $this->add_per_seat_text( $price, $cart_item );
			}
		}

		return $price;
	}

	/**
	 * @return array
	 */
	private function check_if_groups_plus_seats_product_in_cart() {
		$items  = \WC()->cart->get_cart();
		$return = array( 'status' => false );
		if ( $items ) {
			foreach ( $items as $item ) {
				$product = $item['data'];
				// $product = wc_get_product( $pid );
				if ( $product instanceof \WC_Product && $product->is_type( $this->product_type ) ) {
					$return = array(
						'status'     => true,
						'product_id' => $product->get_id(),
					);
					break;
				}
			}
		}

		return $return;
	}

	/**
	 * On Order Completion, add Seats to the Organization and Team.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function process_groups_plus_seats_order_complete( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order ) {
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

		$user             = $order->get_user();
		$user_id          = $user->ID;
		$user_meta        = get_userdata( $user->ID );
		$order_user_roles = (array) $user_meta->roles;

		// We only need to process the order once. That's why we check if the flag is set.
		$process_order = Cast::to_bool( $order->get_meta( SharedFunctions::$process_order_meta_field ) );

		if ( ! $process_order ) {
			return;
		}

		$product_id = 0;
		$group_id   = 0;
		$_quantity  = 0;
		$line_items = $order->get_items( 'line_item' );
		if ( ! $line_items ) {
			return;
		}

		$continue = false;
		foreach ( $line_items as $item_id => $item ) {
			$_quantity = $item->get_quantity();
			if (
				$this->check_if_groups_plus_seats_product_in_items(
					Cast::to_int( $item['product_id'] )
				)
				&& isset( $item['secondary_group'] )
			) {
				$product_id = $item['product_id'];
				$group_id   = $item['secondary_group'];
				$continue   = true;
				break;
			}
		}
		if ( false === $continue ) {
			return;
		}

		$primary_group_id = wp_get_post_parent_id( $group_id );

		if ( $primary_group_id ) {
			$number_of_licenses = Cast::to_int(
				get_post_meta(
					$primary_group_id,
					'number_of_licenses',
					true
				)
			);

			$number_of_licenses += $_quantity;

			update_post_meta(
				$primary_group_id,
				'number_of_licenses',
				$number_of_licenses
			);
		}

		$secondary_number_of_licenses = Cast::to_int(
			get_post_meta(
				$group_id,
				'number_of_licenses',
				true
			)
		);

		$secondary_number_of_licenses += $_quantity;

		update_post_meta(
			$group_id,
			'number_of_licenses',
			$secondary_number_of_licenses
		);

		// Add user to team if they are not group_leader or administrator
		if (
			! in_array(
				'group_leader',
				$order_user_roles,
				true
			)
			&& ! in_array(
				'administrator',
				$order_user_roles,
				true
			)
		) {
			ld_update_group_access( $user_id, $group_id );
		}

		// We remove the flag so that it won't be processed again in the future after status updates.
		$order->delete_meta_data( SharedFunctions::$process_order_meta_field );
		$order->save_meta_data();
	}

	/**
	 * Check if a given Product is an Seats Product.
	 *
	 * @since 1.0.0
	 * @since 2.1.0 No longer check for WooCommerce Subscriptions for Variation Products.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool
	 */
	private function check_if_groups_plus_seats_product_in_items( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		if ( $product->is_type( 'groups_plus_seats' ) ) {
			return true;
		}

		$has_seat_purchase_enable = get_post_meta( $product_id, SharedFunctions::$is_individual_seat_purchase_enable, true );

		// Product Types that aren't the Seats Product Type require this Post Meta to be set.
		if ( ! $has_seat_purchase_enable ) {
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

	/**
	 * @param $order_id
	 *
	 * @return array
	 */
	private function check_if_license_product_in_order( $order_id ) {
		$order  = wc_get_order( $order_id );
		$items  = $order->get_items();
		$return = array( 'status' => false );
		if ( $items ) {
			/** @var \WC_Order_Item_Product $item */
			foreach ( $items as $item ) {
				$product = $item->get_product();
				// $product = wc_get_product( $pid );
				if ( $product instanceof \WC_Product && $product->is_type( $this->product_type ) ) {
					$return = array(
						'status'     => true,
						'product_id' => $product->get_id(),
					);
					break;
				}
			}
		}

		return $return;
	}

	/**
	 * Show pricing fields for team courses product.
	 */
	function groups_plus_course_custom_js() {
		global $post, $product_object;

		if ( ! $post ) {
			return; }

		if ( 'product' != $post->post_type ) :
			return;
		endif;

		$is_groups_plus_seats = $product_object && $this->product_type === $product_object->get_type() ? true : false;

		?>
<script type='text/javascript'>
jQuery(document).ready(function() {
	var selectedProductType = ''
	var $group_pricing = '#general_product_data, .options_group.pricing';

	jQuery('.options_group.pricing').addClass(
		'show_if_groups_plus_seats');
	jQuery('.form-field._tax_status_field').parent().addClass(
		'show_if_groups_plus_seats')

	jQuery("#product-type").change((function() {

		selectedProductType = jQuery(this).val();
		jQuery("#_virtual").prop("checked", false);


		if ('groups_plus_organizations' === selectedProductType) {
			jQuery('#_virtual').prop('checked', true).show();
			jQuery('li.general_options').show();
			jQuery('li.inventory_options').show(); //Show inventory tab
			jQuery('#advanced_product_data').hide()
			jQuery('.advanced_options').removeClass('active')
			jQuery($group_pricing).removeClass('active').show()
		}

		if ('groups_plus_organizations_groups' === selectedProductType) {
			jQuery('#_virtual').prop('checked', true).show();
			jQuery('li.general_options').show();
			jQuery('li.inventory_options').show(); //Show inventory tab
		}

		if ( '<?php $this->product_type; ?>' === selectedProductType) {
			jQuery('#_virtual').prop('checked', true);
			jQuery('li.general_options').show()
			jQuery('#advanced_product_data').hide()
			jQuery('.advanced_options').removeClass('active')
			jQuery('#general_product_data').hide()
			jQuery($group_pricing).removeClass('hidden').show()
		}

	}));
		<?php
		if ( $is_groups_plus_seats ) {
			?>
			 jQuery('#general_product_data .pricing')
		.show();
	<?php } ?>
});
</script>
		<?php
	}

	/**
	 * @since 2.10.1.0
	 *
	 * Add a seats to team when group leader or admin can do from team page.
	 */
	public function add_seats_to_groups_plus() {
		if ( ! SharedFunctions::filter_has_var( 'add-learndash-groups-plus-group-seat' ) ) {
			return;
		}

		if ( true != SharedFunctions::filter_has_var( 'add-learndash-groups-plus-group-seat' ) ) {
			return;
		}
		$group_id       = SharedFunctions::filter_input( 'group-id' );
		$qty            = intval(
			Cast::to_string(
				SharedFunctions::filter_input( 'qty' )
			)
		);
		$add_seats_type = SharedFunctions::filter_input( 'add-seats-type' );
		$nonce          = SharedFunctions::filter_input( 'nonce' );

		if ( SharedFunctions::is_woocommerce_active() && 'yes' === get_option( 'enable_wc', 'no' ) && '1' === get_option( 'enable_add_seats', '0' ) ) {
			$subscription_product_query_args = array(
				'post_type'   => 'product',
				'post_status' => 'publish',
				'tax_query'   => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => array( 'subscription', 'variable-subscription' ),
					),
				),
				'meta_query'  => array(
					array(
						'key'   => SharedFunctions::$is_individual_seat_purchase_enable,
						'value' => 'on',
					),
				),
			);

			// $subscription_products = new WP_Query( $query_args );

			$no_subscription_product_query_args = array(
				'post_type' => 'product',
				'tax_query' => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => $this->product_type,
					),
				),
			);
			// $no_subscription_products = wc_get_products( $query_args );

			if ( isset( $add_seats_type ) && in_array( $add_seats_type, array( 'add-non-recurring-cost-seat', 'add-recurring-cost-seat' ) ) ) {
				// print_r ( $_REQUEST ); die;
				if ( $add_seats_type === 'add-recurring-cost-seat' ) {
					$products = new \WP_Query( $subscription_product_query_args );
				} else {
					$products = new \WP_Query( $no_subscription_product_query_args );
				}
			} else {

				$subscription_products    = new \WP_Query( $subscription_product_query_args );
				$no_subscription_products = new \WP_Query( $no_subscription_product_query_args );

				if ( count( $subscription_products->posts ) ) {
					$products = $subscription_products;
				} else {
					$products = $no_subscription_products;
				}
			}
		}

		if ( count( $products->posts ) ) {
			wp_safe_redirect(
				add_query_arg(
					[
						'add-to-cart'     => $products->posts[0]->ID,
						'quantity'        => $qty,
						'secondary_group' => $group_id,
						'nonce'           => $nonce,
					],
					wc_get_cart_url()
				)
			);
			exit;
		}
	}

	/**
	 * Validate seats product before being added to cart.
	 *
	 * @since 1.1.0
	 *
	 * @param bool  $valid
	 * @param int   $product_id
	 * @param int   $quantity
	 * @param int   $variation_id
	 * @param array $variations
	 * @return bool True if valid|false otherwise.
	 */
	public function validate_cart( $valid, $product_id, $quantity, $variation_id = null, $variations = null ): bool {
		$product      = wc_get_product( $product_id );
		$product_type = $product->get_type();

		if (
			$product_type === $this->product_type
			&& empty( $_POST['secondary_group'] )
			&& empty( $_GET['add-to-cart'] )
			&& empty( $_GET['secondary_group'] )
		) {
			wc_add_notice(
				sprintf(
					esc_html__( '%1$s is required. Please choose a %2$s first.', 'learndash-groups-plus' ),
					learndash_get_custom_label( 'team' ),
					learndash_get_custom_label_lower( 'team' ),
				),
				'error'
			);

			$valid = false;
		}

		return $valid;
	}

	/**
	 * Add metabox for team course price.
	 */

	public function add_metabox( $post_type, $post ) {
		add_meta_box(
			'learndash-groups-plus-seats-meta-box',
			__( 'LearnDash Groups Plus WooCommerce', 'learndash-groups-plus' ),
			array( $this, 'render_metabox' ),
			'groups',
			'side',
			'default'
		);
	}

	/**
	 * Renders the meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post The Post being edited.
	 *
	 * @return void
	 */
	public function render_metabox( $post ) {

		// Add nonce for security and authentication.
		wp_nonce_field( 'wc_seats_metabox_nonce_action', 'wc_seats_metabox_nonce' );

		$groups_plus_price          = get_post_meta( $post->ID, '_groups_plus_price', true );
		$groups_plus_sub_seat_price = get_post_meta( $post->ID, '_groups_plus_sub_seat_price', true );

		// Echo out the field
		ob_start();
		?>
		<p>
			<label for="groups_plus_price">
			<?php
			printf(
				esc_html__( '%s seat price', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			);
			?>
			</label>
			<br />
			<input type="text" name="_groups_plus_price" id="groups_plus_price"
				value="<?php echo esc_attr( $groups_plus_price ); ?>" size="30" />
		</p>
		<?php if ( SharedFunctions::is_woocommerce_subscription_active() ) : ?>
		<p>
			<label for="groups_plus_sub_seat_price"><?php _e( 'Public subscription seat price', 'learndash-groups-plus' ); ?></label>
			<br />
			<input type="text" name="_groups_plus_sub_seat_price" id="groups_plus_sub_seat_price"
				value="<?php echo esc_attr( $groups_plus_sub_seat_price ); ?>" size="30" />
		</p>
		<?php endif; ?>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Handles saving the meta box.
	 */
	public function save_group_price( $post_id, $post ) {

		// Add nonce for security and authentication.
		$nonce_name   = isset( $_POST['wc_seats_metabox_nonce'] ) ? $_POST['wc_seats_metabox_nonce'] : '';
		$nonce_action = 'wc_seats_metabox_nonce_action';

		// Check if nonce is set.
		if ( ! isset( $nonce_name ) ) {
			return;
		}

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( isset( $_POST['_groups_plus_price'] ) ) {
			$groups_plus_price = floatval( $_POST['_groups_plus_price'] );

			update_post_meta( $post_id, '_groups_plus_price', $groups_plus_price );
		}

		if ( isset( $_POST['_groups_plus_sub_seat_price'] ) ) {
			$groups_plus_sub_seat_price = floatval( $_POST['_groups_plus_sub_seat_price'] );

			update_post_meta( $post_id, '_groups_plus_sub_seat_price', $groups_plus_sub_seat_price );
		}

	}

	/**
	 * Override the Cart Item price for Seats based on the Team where applicable. This gets added to the Cart Item Data when the "Add Seats" button is clicked from the Frontend Dashboard.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Cart $cart WooCommerce Cart object.
	 *
	 * @return void
	 */
	public function apply_groups_plus_price_to_cart_item( $cart ) {
		if ( WC()->session->__isset( 'reload_checkout' ) ) {
			return;
		}

		$this->process_cart_items(
			$cart,
			function( $cart, $cart_item_data, $product ) {
				if ( empty( $cart_item_data['groups_plus_price'] ) ) {
					return;
				}

				$product->set_price( $cart_item_data['groups_plus_price'] );
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
		if ( ! self::product_can_be_used_for_seats( $product->get_id() ) ) {
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

		if ( ! self::product_can_be_used_for_seats( $product->get_id() ) ) {
			return $price;
		}

		$sign_up_fee = Cast::to_float(
			WC_Subscriptions_Product::get_sign_up_fee( $product )
		);

		if ( empty( $sign_up_fee ) ) {
			return $price;
		}

		$price = max( 0, $price - $sign_up_fee );

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
					! $product->is_type( 'subscription' )
					&& ! $product->is_type( 'variable-subscription' )
				) {
					return;
				}

				$sign_up_fee = WC_Subscriptions_Product::get_sign_up_fee( $product->get_id() );

				if ( empty( $sign_up_fee ) ) {
					return;
				}

				$group_id = 0;
				if ( ! empty( $cart_item_data['secondary_group'] ) ) {
					$group_id = $cart_item_data['secondary_group'];
				} elseif ( ! empty( $cart_item_data['primary_group'] ) ) {
					$group_id = $cart_item_data['primary_group'];
				}

				if ( empty( $group_id ) ) {
					return;
				}

				$cart->add_fee(
					esc_html(
						sprintf(
							// translators: Sign up fees for added seats in "Team Name".
							__( 'Sign up fees for added seats in "%1$s"', 'learndash-groups-plus' ),
							get_the_title( $group_id )
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

		foreach ( $cart->cart_contents as $cart_item_key => $cart_item_data ) {
			if (
				! is_array( $cart_item_data )
				|| empty( $cart_item_data['data'] )
			) {
				continue;
			}

			$product = $cart_item_data['data'];

			if ( ! $product instanceof WC_Product ) {
				continue;
			}

			if (
				empty( $cart_item_data['quantity'] )
				|| $cart_item_data['quantity'] <= 0
			) {
				continue;
			}

			// Not Buying Seats.
			if (
				(
					empty( $cart_item_data['secondary_group'] )
					&& empty( $cart_item_data['primary_group'] )
				)
				|| ! self::product_can_be_used_for_seats( $product->get_id() )
			) {
				continue;
			}

			$callback( $cart, $cart_item_data, $product );
		}
	}

	/**
	 * Check if the given Product is configured to be used to purchase Seats.
	 *
	 * @since 2.1.1
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool
	 */
	public static function product_can_be_used_for_seats( int $product_id ): bool {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		if ( $product->is_type( 'groups_plus_seats' ) ) {
			return true;
		}

		$has_seats_purchase_enable = get_post_meta( $product_id, SharedFunctions::$is_individual_seat_purchase_enable, true );

		// Product Types that aren't the Seats Product Type require this Post Meta to be set.
		if ( ! $has_seats_purchase_enable ) {
			return false;
		}

		if ( $product->is_type( 'variable' ) ) {
			return true;
		}

		// The remaining Product Types require WooCommerce Subscriptions to be active.
		if ( ! SharedFunctions::is_woocommerce_subscription_active() ) {
			return false;
		}

		return $product->is_type( 'subscription' )
			|| $product->is_type( 'variable-subscription' );
	}

	/**
	 * Returns an updated Price based on the chosen Team to display on Product Single.
	 *
	 * @since 1.0.0
	 *
	 * @throws InvalidArgumentException On an invalid Product.
	 *
	 * @return void
	 */
	public function get_groups_plus_price() {
		$error   = null;
		$success = false;

		$post_data = wp_unslash( $_POST );

		if (
			empty( $_POST['data'] )
			|| empty( $_POST['data']['nonce'] )
		) {
			echo wp_json_encode(
				[
					'success' => $success,
					'errors'  => __( 'Invalid nonce.', 'learndash-groups-plus' ),
					'html'    => '',
				]
			);
			die;
		}

		if (
			! wp_verify_nonce(
				Cast::to_string(
					sanitize_text_field(
						wp_unslash( $_POST['data']['nonce'] )
					)
				),
				'learndash_groups_plus_add_seat_product'
			)
		) {
			echo wp_json_encode(
				[
					'success' => $success,
					'errors'  => __( 'Invalid nonce.', 'learndash-groups-plus' ),
					'html'    => '',
				]
			);
			die;
		}

		try {
			$product_id = $post_data['data']['product_id'];
			$product    = wc_get_product( $product_id );

			if ( ! $product instanceof WC_Product ) {
				throw new InvalidArgumentException( esc_html( __( 'A valid product_id must be provided.', 'learndash-groups-plus' ) ) );
			}

			$is_individual_seat_purchase_enable = get_post_meta( $product_id, SharedFunctions::$is_individual_seat_purchase_enable, true );
			if ( $product instanceof \WC_Product && $product->is_type( $this->product_type ) ) {
				$groups_plus_price = get_post_meta( $post_data['data']['groups_plus_id'], '_groups_plus_price', true );
				if ( empty( $groups_plus_price ) ) {
					$groups_plus_price = $product->get_price();
				}
			} elseif ( $product instanceof \WC_Product && SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'subscription' ) && $is_individual_seat_purchase_enable ) {
				$groups_plus_price = get_post_meta( $post_data['data']['groups_plus_id'], '_groups_plus_sub_seat_price', true );
				if ( empty( $groups_plus_price ) ) {
					$groups_plus_price = $product->get_price();
				}
			} else {
				$groups_plus_price = get_post_meta( $post_data['data']['groups_plus_id'], '_groups_plus_price', true );
				if ( empty( $groups_plus_price ) ) {
					$groups_plus_price = $product->get_price();
				}
			}

			$price_string = wc_price( wc_get_price_to_display( $product, array( 'price' => $groups_plus_price ) ) );

			$per_seat_text = __( 'Seat', 'learndash-groups-plus' );
			$price_string  = preg_replace( '/\<\/bdi\>\<\/span\>/', "</bdi> &#47; $per_seat_text</span>", $price_string );

			if ( class_exists( WC_Subscriptions_Product::class ) ) {
				// We intentionally remove the set price as we're already outputting it normally. We just want the subscription data.
				$subscription_price_string = preg_replace(
					'/^' . preg_quote( Cast::to_string( $groups_plus_price ), '/' ) . '/',
					'',
					WC_Subscriptions_Product::get_price_string(
						$product,
						[
							'price' => $groups_plus_price,
						]
					)
				);

				$price_string .= trim( $subscription_price_string );
			}

			$success = true;
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
				'html'    => $price_string,
			)
		);
		die;
	}

	/**
	 * Removes Group Access for the Seat Purchaser in a Org/Team on Subscription Expiration. This does not apply if the Seat Purchaser is a Group Leader or an Administrator.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Subscription $subscription Subscription object.
	 *
	 * @return void
	 */
	public function remove_group_access( \WC_Subscription $subscription ) {
		$order_id = $subscription->get_last_order( 'ids', array( 'parent' ) );

		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		$group_id         = null;
		$user             = $order->get_user();
		$order_status     = $order->get_status();
		$user_id          = isset( $user->ID ) ? $user->ID : 0;
		$user_meta        = get_userdata( $user_id );
		$order_user_roles = (array) $user_meta->roles;

		$line_items = $order->get_items( 'line_item' );
		if ( ! $line_items ) {
			return;
		}

		$continue = false;
		foreach ( $line_items as $item_id => $item ) {
			$_quantity = $item->get_quantity();
			if (
				$this->check_if_groups_plus_seats_product_in_items(
					Cast::to_int( $item['product_id'] )
				)
				&& isset( $item['secondary_group'] )
			) {
				$product_id = $item['product_id'];
				$group_id   = $item['secondary_group'];
				$continue   = true;
				break;
			}
		}
		if ( false === $continue ) {
			return;
		}

		$primary_group_id = wp_get_post_parent_id( $group_id );
		if ( $primary_group_id ) {
			// Remove user from Team when roles of their is not group_leader or administrator.
			if ( ! in_array( 'group_leader', $order_user_roles ) && ! in_array( 'administrator', $order_user_roles ) ) {

				// Remove user from Team.
				ld_update_group_access( $user_id, $group_id, true );

			}
		}

	}

	/**
	 * Adds Group Access to the Seat Purchaser for a Org/Team on Subscription Activation. This does not apply if the Seat Purchaser is a Group Leader or an Administrator.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Subscription $subscription Subscription object.
	 *
	 * @return void
	 */
	public function give_group_access( \WC_Subscription $subscription ) {
		$order_id = $subscription->get_last_order( 'ids', array( 'parent' ) );

		$order = wc_get_order( $order_id );
		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		$group_id         = null;
		$user             = $order->get_user();
		$order_status     = $order->get_status();
		$user_id          = isset( $user->ID ) ? $user->ID : 0;
		$user_meta        = get_userdata( $user_id );
		$order_user_roles = (array) $user_meta->roles;

		$line_items = $order->get_items( 'line_item' );
		if ( ! $line_items ) {
			return;
		}

		$continue = false;
		foreach ( $line_items as $item_id => $item ) {
			$_quantity = $item->get_quantity();
			if (
				$this->check_if_groups_plus_seats_product_in_items(
					Cast::to_int( $item['product_id'] )
				)
				&& isset( $item['secondary_group'] )
			) {
				$product_id = $item['product_id'];
				$group_id   = $item['secondary_group'];
				$continue   = true;
				break;
			}
		}
		if ( false === $continue ) {
			return;
		}

		$primary_group_id = wp_get_post_parent_id( $group_id );
		if ( $primary_group_id ) {
			// Add user to team when roles of their is not group_leader or administrator
			if ( ! in_array( 'group_leader', $order_user_roles ) && ! in_array( 'administrator', $order_user_roles ) ) {

				// Add user to team group
				ld_update_group_access( $user_id, $group_id );
			}
		}
	}
}
