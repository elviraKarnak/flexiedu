<?php
/**
 * WooCommerce Organization Module.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore USERID nopaging numberposts thepostid classname
 */

namespace LearnDash\Groups_Plus\Module\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use LearnDash\Core\Utilities\Cast;

use LearnDash\Groups_Plus\Model\WooCommerce\Product\Organizations as Organizations_Product;
use LearnDash\Groups_Plus\Module\Group;
use LearnDash\Groups_Plus\Utility\Database;
use LearnDash\Groups_Plus\Utility\SharedFunctions;

use WC_Order;
use WC_Product;
use WC_Cart;
use WC_Product_Variation;
use WC_Subscription;
use WP_User;

/**
 * Class Organizations.
 */
class Organizations {

	/**
	 * The instance of the class.
	 *
	 * @since    1.0.0
	 *
	 * @access   private
	 *
	 * @var      Organizations
	 */
	private static $instance = null;

	/**
	 * Organizations WooCommerce product type.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $product_type = 'groups_plus_organizations';

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

			add_action( 'admin_footer', array( $this, 'groups_plus_organizations_custom_js' ) );
		}
	}

	/**
	 * Creates singleton instance of class
	 *
	 * @since 2.8.0
	 *
	 * @return Organizations $instance The InitializePlugin Class.
	 */
	public static function get_instance() {
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
			add_filter( 'product_type_selector', array( $this, 'add_organizations_product' ), 20 );
			add_filter( 'product_type_options', array( $this, 'add_virtual_and_downloadable_checks' ) );
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'custom_product_tabs' ), 20 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'groups_plus_organization_courses_options_product_tab_content' ) );
			add_action( 'woocommerce_process_product_meta_groups_plus_organizations', array( $this, 'save_groups_plus_organization_courses_option_field' ) );
			add_action( 'woocommerce_process_product_meta_subscription', array( $this, 'save_groups_plus_organization_courses_option_field' ) );
			add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_groups_plus_organization_courses_option_field' ) );
			add_action( 'woocommerce_process_product_meta_variable-subscription', array( $this, 'save_groups_plus_organization_courses_option_field' ) );

			// Product variation hooks
			add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'render_variation_group_selector' ), 10, 3 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'store_variation_related_courses' ), 10, 2 );

			// add_filter( 'woocommerce_product_data_tabs', array( $this, 'hide_attributes_data_panel' ) );
			add_filter( 'woocommerce_variable_sale_price_html', array( $this, 'wc_remove_prices' ), 10, 2 );
			add_filter( 'woocommerce_variable_price_html', array( $this, 'wc_remove_prices' ), 10, 2 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'wc_remove_prices' ), 10, 2 );
			add_filter( 'woocommerce_is_sold_individually', array( $this, 'wc_remove_all_quantity_fields' ), 10, 2 );
			add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'change_order_item_meta_title' ), 20, 3 );
			add_filter( 'woocommerce_order_item_display_meta_value', array( $this, 'change_order_item_meta_value' ), 20, 3 );

			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'wc_before_add_to_cart_btn' ) );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'wc_add_to_cart_validation' ), 10, 5 );

			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_dropdown_value_to_cart_item_data' ), 10, 4 );

			add_filter( 'woocommerce_get_item_data', [ $this, 'populate_item_data' ], 10, 2 );

			add_filter( 'woocommerce_cart_item_price', array( $this, 'change_product_cart_item_price' ), 99, 3 );
			add_action( 'woocommerce_before_calculate_totals', [ $this, 'wc_before_calculate_totals' ], 9, 1 );

			// add_action( 'woocommerce_new_order_item', array( $this, 'add_values_to_order_item_meta') ,10,3);
			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'wc_checkout_create_order_line_item' ), 20, 4 );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'wc_checkout_update_order_meta' ), 10, 1 );
			add_action( 'woocommerce_store_api_checkout_update_order_meta', [ $this, 'wc_checkout_update_order_meta' ], 10, 1 );

			add_action( 'woocommerce_order_status_completed', array( $this, 'process_groups_plus_organizations_order_complete' ), 99 );

			// add meta for sfwd-courses
			add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
			add_action( 'save_post', array( $this, 'save_course_price' ), 20, 2 );

			add_action( 'woocommerce_groups_plus_organizations_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );

			/********SUBSCRIPTION HOOKS*/
			add_action( 'woocommerce_subscription_status_expired', array( $this, 'change_group_to_draft' ), 99, 2 );
			add_action( 'woocommerce_subscription_status_on-hold', array( $this, 'change_group_to_draft' ), 99, 2 );
			add_action( 'woocommerce_subscription_status_cancelled', array( $this, 'change_group_to_draft' ), 99, 2 );
			add_action( 'woocommerce_subscription_status_pending-cancel', array( $this, 'change_group_to_draft' ), 99, 2 );
			add_action( 'woocommerce_subscription_status_active', array( $this, 'change_group_to_published' ), 99, 2 );

			// Ajax hooks
			add_action( 'wp_ajax_get_courses_of_variation_product', array( $this, 'get_courses_of_variation_product' ) );
			add_action( 'wp_ajax_nopriv_get_courses_of_variation_product', array( $this, 'get_courses_of_variation_product' ) );
		}
	}

	/**
	 * Filter WooCommerce team name used for Organizations product.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function woocommerce_product_class( $classname, $product_type, $variation_type, $product_id ) {
		if ( $product_type === $this->product_type ) {
			$classname = Organizations_Product::class;
		}

		return $classname;
	}

	/**
	 * @param $types
	 *
	 * @return mixed
	 */
	public function add_organizations_product( $types ) {
		$types[ $this->product_type ] = sprintf(
			__( 'LearnDash %s', 'learndash-groups-plus' ),
			learndash_get_custom_label( 'organization' )
		);

		return $types;
	}

	/**
	 * @param $options
	 *
	 * @return mixed
	 */
	public function add_virtual_and_downloadable_checks( $options ) {

		if ( isset( $options['virtual'] ) ) {
			$options['virtual']['wrapper_class'] = $options['virtual']['wrapper_class'] . ' show_if_groups_plus_organizations';
		}

		if ( isset( $options['downloadable'] ) ) {
			$options['downloadable']['wrapper_class'] = $options['downloadable']['wrapper_class'] . ' show_if_groups_plus_organizations';
		}

		return $options;
	}

	/**
	 * Add a custom product tab.
	 */
	function custom_product_tabs( $tabs ) {
		$tabs[ $this->product_type ] = array(
			'label'  => sprintf(
				'LearnDash %1$s %2$s',
				learndash_get_custom_label( 'organization' ),
				learndash_get_custom_label_lower( 'courses' )
			),
			'target' => 'groups_plus_organization_courses_options',
			'class'  => array( 'show_if_groups_plus_organizations', 'show_if_variable', 'show_if_subscription' ),
		);

		return $tabs;
	}

	/**
	 * Contents of the courses options product tab.
	 */
	function groups_plus_organization_courses_options_product_tab_content() {
		global $post, $woocommerce;
		$courses = $this->list_courses();

		$values = get_post_meta( $post->ID, SharedFunctions::$groups_plus_organization_courses_meta_field, true );
		if ( ! $values ) {
			$values = array( 0 );
		}

		?>
<div id='groups_plus_organization_courses_options' class='panel woocommerce_options_panel'>
	<div class='options_group show_if_subscription show_if_variable'>
		<?php
			$this->woocommerce_wp_checkbox_enable_organization_purchase(
				array(
					'id'          => SharedFunctions::$is_organization_purchase_enable,
					'name'        => SharedFunctions::$is_organization_purchase_enable,
					'label'       => sprintf(
						__( 'Enable %s purchase', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'organization' )
					),
					'description' => sprintf(
						__( 'check to enable %s purchase', 'learndash-groups-plus' ),
						learndash_get_custom_label_lower( 'organization' )
					),
				)
			);
		?>
	</div>
	<div class='options_group show_if_groups_plus_organizations show_if_subscription'>
		<?php
			self::woocommerce_wp_select_multiple(
				array(
					'id'          => SharedFunctions::$groups_plus_organization_courses_meta_field,
					'name'        => '_groups_plus_organization_courses[]',
					'label'       => sprintf( esc_html( '%s' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
					'description' => sprintf( __( 'Select LearnDash %s.', 'learndash-groups-plus' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
					'options'     => $courses,
					'value'       => $values,
				)
			);
		?>
	</div>
</div>
		<?php
	}

	/**
	 * @return array
	 */
	function list_courses() {
		$posts   = get_posts(
			array(
				'post_type'      => 'sfwd-courses',
				'posts_per_page' => 9999,
				'orderby'        => 'title',
				'order'          => 'ASC',
			)
		);
		$courses = array();
		// $courses[0] = sprintf( __( 'Select %s', 'learndash-groups-plus' ), \LearnDash_Custom_Label::get_label( 'course' ) );
		foreach ( $posts as $course_post ) {
			$courses[ $course_post->ID ] = get_the_title( $course_post->ID );
		}

		return $courses;
	}

	/**
	 * @param $field
	 */
	public function woocommerce_wp_checkbox_enable_organization_purchase( $field ) {
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
				' . esc_attr( $field['checked'] ) . ' />';
		echo '</p>';
	}

	/**
	 * @param $field
	 */
	public function woocommerce_wp_input( $field ) {
		global $thepostid, $post, $woocommerce;
		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
		$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
		$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
		$field['value']         = isset( $field['value'] ) ? $field['value'] : '';

		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">';
			echo '<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';
			echo '<input type="' . $field['type'] . '" id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $field['value'] ) . '" class="' . esc_attr( $field['class'] ) . '" />';

		if ( ! empty( $field['description'] ) ) {

			if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
			} else {
				echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			}
		}
		echo '</p>';
	}


	/**
	 * Outputs a Multiple Select field
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $field Field arguments.
	 *
	 * @return void
	 */
	public function woocommerce_wp_select_multiple( $field ) {
		global $thepostid, $post, $woocommerce;
		$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
		$field['id']            = Cast::to_string( $field['id'] );
		$field['description']   = Cast::to_string( $field['description'] );
		$field['label']         = Cast::to_string( $field['label'] );
		$field['class']         = isset( $field['class'] ) ? Cast::to_string( $field['class'] ) : 'select short';
		$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? Cast::to_string( $field['wrapper_class'] ) : '';
		$field['name']          = isset( $field['name'] ) ? Cast::to_string( $field['name'] ) : $field['id'];
		$field['value']         = isset( $field['value'] ) ? $field['value'] : ( get_post_meta( $thepostid, $field['id'], true ) ? get_post_meta( $thepostid, $field['id'], true ) : [] );

		if (
			empty( $field['value'] )
			|| ! is_array( $field['value'] )
		) {
			$field['value'] = [];
		}

		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" multiple="multiple">';

		if (
			empty( $field['options'] )
			|| ! is_array( $field['options'] )
		) {
			$field['options'] = [];
		}

		foreach ( $field['options'] as $key => $value ) {
			$course_price = wc_price( get_post_meta( $key, '_course_price', true ) );
			echo '<option value="' . esc_attr( $key ) . '" ' . ( in_array( $key, $field['value'] ) ? 'selected="selected"' : '' ) . '>' . esc_html( '#' . $key . ' - ' . $value ) . ' (' . $course_price . ')' . '</option>';

		}

		echo '</select> ';

		if ( ! empty( $field['description'] ) ) {

			if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
				echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
			} else {
				echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
			}
		}
		echo '</p>';
	}

	public function render_variation_group_selector( $loop, $data, $variation ) {
		// $courses_options = [ 0 => __( 'No Related courses', 'uncanny-learndash-groups' ) ];

		$is_organization_purchase_enable = get_post_meta( $variation->post_parent, SharedFunctions::$is_organization_purchase_enable, true );

		if ( $is_organization_purchase_enable === 'on' ) {

			$courses = self::list_courses();

			echo '<div class="form-row form-row-full">';

			wp_nonce_field( 'save_post', 'wc_nonce' );

			$seat_value = (int) get_post_meta( $variation->ID, SharedFunctions::$variable_product_allow_seats, true );
			self::woocommerce_wp_input(
				array(
					'type'        => 'number',
					'id'          => SharedFunctions::$variable_product_allow_seats,
					'name'        => '_variable_product_allow_seats[' . $loop . ']',
					'label'       => __( 'Allow seats', 'learndash-groups-plus' ),
					'description' => __( 'How many seats you wanted to allow.', 'learndash-groups-plus' ),
					'value'       => $seat_value,
				)
			);

			$values = get_post_meta( $variation->ID, SharedFunctions::$groups_plus_organization_courses_meta_field, true );
			if ( ! $values ) {
				// $values = [ 0 ];
			}

			self::woocommerce_wp_select_multiple(
				array(
					'id'          => SharedFunctions::$groups_plus_organization_courses_meta_field,
					'name'        => '_groups_plus_organization_courses[' . $loop . '][]',
					'label'       => sprintf( esc_html( '%s' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
					'description' => sprintf( __( 'Select LearnDash %s.', 'learndash-groups-plus' ), \LearnDash_Custom_Label::get_label( 'course' ) ),
					'options'     => $courses,
					'value'       => $values,
				)
			);

			echo '</div>';
		}
	}

	public function store_variation_related_courses( $variation_id, $loop ) {
		if (
			! SharedFunctions::filter_has_var( 'wc_nonce', INPUT_POST )
			|| ! wp_verify_nonce(
				Cast::to_string(
					SharedFunctions::filter_input(
						'wc_nonce',
						INPUT_POST
					)
				),
				'save_post'
			)
		) {
			return;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( SharedFunctions::filter_has_var( SharedFunctions::$groups_plus_organization_courses_meta_field, INPUT_POST ) && ! empty( SharedFunctions::filter_input_array( SharedFunctions::$groups_plus_organization_courses_meta_field, INPUT_POST ) ) ) {
			update_post_meta( $variation_id, SharedFunctions::$groups_plus_organization_courses_meta_field, SharedFunctions::filter_input_array( SharedFunctions::$groups_plus_organization_courses_meta_field, INPUT_POST )[ $loop ] );
		} else {
			delete_post_meta( $variation_id, SharedFunctions::$groups_plus_organization_courses_meta_field );
		}
		// echo  filter_input(INPUT_POST,'attribute_seats[0]');
		// echo SharedFunctions::filter_has_var( SharedFunctions::$variable_product_allow_seats, INPUT_POST );
		// print_r($_POST['_variable_product_allow_seats']);
		// echo $search_html = filter_input(INPUT_POST, SharedFunctions::$variable_product_allow_seats);
		// echo SharedFunctions::filter_input( SharedFunctions::$variable_product_allow_seats, INPUT_POST )[$loop];
		if ( SharedFunctions::filter_has_var( SharedFunctions::$variable_product_allow_seats, INPUT_POST ) ) {
			update_post_meta( $variation_id, SharedFunctions::$variable_product_allow_seats, $_POST['_variable_product_allow_seats'][ $loop ] );
		} else {
			delete_post_meta( $variation_id, SharedFunctions::$variable_product_allow_seats );
		}
	}

	/**
	 * Save the custom fields.
	 */
	function save_groups_plus_organization_courses_option_field( $post_id ) {
		if ( isset( $_POST[ SharedFunctions::$is_organization_purchase_enable ] ) ) {
			update_post_meta( $post_id, SharedFunctions::$is_organization_purchase_enable, $_POST[ SharedFunctions::$is_organization_purchase_enable ] );

			if ( isset( $_POST[ SharedFunctions::$is_organization_group_purchase_enable ] ) ) {
				delete_post_meta( $post_id, SharedFunctions::$is_organization_group_purchase_enable );
			}
		} else {
			delete_post_meta( $post_id, SharedFunctions::$is_organization_purchase_enable );
		}

		if ( isset( $_POST[ SharedFunctions::$groups_plus_organization_courses_meta_field ] ) ) {
			update_post_meta( $post_id, SharedFunctions::$groups_plus_organization_courses_meta_field, $_POST[ SharedFunctions::$groups_plus_organization_courses_meta_field ] );
		} else {
			delete_post_meta( $post_id, SharedFunctions::$groups_plus_organization_courses_meta_field );
		}

	}

	/**
	 * Hide Attributes data panel.
	 */
	function hide_attributes_data_panel( $tabs ) {
		// Other default values for 'attribute' are; general, inventory, shipping, linked_product, variations, advanced
		// $tabs['attribute']['class'][]      = 'hide_if_groups_plus_organizations hide_if_variable';
		$tabs['linked_product']['class'][] = 'hide_if_groups_plus_organizations hide_if_variable';
		$tabs['variations']['class'][]     = 'hide_if_groups_plus_organizations hide_if_variable';
		$tabs['shipping']['class'][]       = 'hide_if_groups_plus_organizations hide_if_variable';

		return $tabs;

	}

	public function wc_remove_prices( $price, $product ) {
		if ( $product instanceof \WC_Product && $product->is_type( $this->product_type ) ) {
			// $price = '';
		} elseif ( $product instanceof \WC_Product && SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'subscription' ) ) {
			// $price = '';
		}
		return $price;
	}

	public function wc_remove_all_quantity_fields( $return, $product ) {
		$has_organization_purchase_enable = get_post_meta( $product->get_id(), SharedFunctions::$is_organization_purchase_enable, true );
		if ( $product instanceof \WC_Product && $product->is_type( $this->product_type ) ) {
			return true;
		} elseif ( $product instanceof \WC_Product && SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'subscription' ) && $has_organization_purchase_enable ) {
			return true;
		}

		return $return;
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
		if ( $item instanceof \WC_Order_Item_Product ) {
			$product = $item->get_product();
			// $product_id = $item->get_product_id();
			$product_id = $product->get_id();
			if ( $this->check_if_groups_plus_organizations_product_in_items( $product_id ) ) {
				// By using $meta-key we are sure we have the correct one.
				if ( 'organization_name' === $meta->key ) {
					$key = '<b>' . sprintf( __( '%s name', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ) . '</b>'; }

				if ( 'choose_organization' === $meta->key ) {
					$key = '<b>' . sprintf( __( '%s name', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ) . '</b>'; }

				if ( 'exclude_from_individual_seat_purchase' === $meta->key ) {
					$key = '<b>' . __( 'Exclude from publicly sold seats', 'learndash-groups-plus' ) . '</b>'; }

				if ( 'my_organization_courses' === $meta->key ) {
					$key = '<b>' . __( 'Course Names', 'learndash-groups-plus' ) . '</b>'; }
			}
		}
		return $key;
	}

	/**
	 * Changing a meta value
	 */
	function change_order_item_meta_value( $meta_value, $meta, $item ) {
		$new_meta_value = $meta_value;
		if ( $item instanceof \WC_Order_Item_Product ) {
			$product = $item->get_product();
			// $product_id = $item->get_product_id();
			$product_id = $product->get_id();
			if ( $this->check_if_groups_plus_organizations_product_in_items( $product_id ) ) {
				if ( 'choose_organization' === $meta->key ) {
					$choose_organization_post = get_post( $meta_value );
					$new_meta_value           = $choose_organization_post->post_title;
				}
				if ( 'my_organization_courses' === $meta->key ) {
					$my_organization_courses = json_decode( $meta_value );
					$new_meta_value          = '<br>&nbsp;';
					$new_meta_value         .= '<ul style="font-weight: 400;" >';
					foreach ( $my_organization_courses as $my_organization_course => $qty ) {
						$course_post     = get_post( $my_organization_course );
						$course_price    = wc_price( get_post_meta( $my_organization_course, '_course_price', true ) );
						$new_meta_value .= '<li>' . esc_html( $course_post->post_title ) . ' (' . $course_price . ') ' . __( 'Qty:', 'learndash-groups-plus' ) . $qty . '</li>';
					}
					$new_meta_value .= '</ul>';
				}
			}
		}
		return $new_meta_value;
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

		if ( $product instanceof \WC_Product
			&&
			(
				$product->is_type( $this->product_type ) ||
				( SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'variable' ) ) ||
				( SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'subscription' ) ) ||
				( SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'variable-subscription' ) )
			)
		) {
			$product_id    = $product->get_id();
			$product_price = $product->get_price();

			if ( SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'variable' ) ||
				SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'subscription' ) ||
				SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'variable-subscription' )
			) {
				$has_organization_purchase_enable = get_post_meta( $product_id, SharedFunctions::$is_organization_purchase_enable, true );

				if ( ! $has_organization_purchase_enable ) {
					return;
				}
			}

			$groups_plus_organization_courses = get_post_meta( $product_id, SharedFunctions::$groups_plus_organization_courses_meta_field, true );
			if ( ! is_array( $groups_plus_organization_courses ) ) {
				$groups_plus_organization_courses = array();
			}
			$groups_plus_organization_courses_arr = array();
			foreach ( $groups_plus_organization_courses as $groups_plus_organization_course ) {
				$post = get_post( $groups_plus_organization_course );
				if ( ! empty( $post ) ) {
					$course_name  = get_the_title( $groups_plus_organization_course );
					$course_price = get_post_meta( $groups_plus_organization_course, '_course_price', true );
					if ( empty( $course_price ) ) {
						$course_price = 0;
					}
					$groups_plus_organization_courses_arr[] = array(
						'course_id'    => $groups_plus_organization_course,
						'course_name'  => $course_name,
						'course_price' => $course_price,
					);
				}
			}
			usort(
				$groups_plus_organization_courses_arr,
				function( $a, $b ) {
					return $a['course_name'] <=> $b['course_name'];
				}
			);
			$show_textbox_organization_name = true;
			$show_hidden_organization       = false;
			$hidden_organization_id         = 0;
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				// \print_r($user->roles);
				// show organization name textbox if not group leader user.
				if ( ! in_array( 'group_leader', (array) $user->roles ) ) {
					$show_textbox_organization_name = true;
				} elseif ( in_array( 'group_leader', (array) $user->roles ) ) {
					// Get parent group
					$administrators_group_ids = learndash_get_administrators_group_ids( $user->ID );

					if ( empty( $administrators_group_ids ) ) {
						$show_textbox_organization_name = true;
					} else {
						$group_orderby = ! empty( get_site_option( 'group_orderby' ) ) ? get_site_option( 'group_orderby' ) : 'title';
						$group_order   = ! empty( get_site_option( 'group_order' ) ) ? get_site_option( 'group_order' ) : 'ASC';
						$args          = array(
							'numberposts' => -1,
							'post_type'   => 'groups',
							'post__in'    => $administrators_group_ids,
							'orderby'     => $group_orderby,
							'order'       => $group_order,
							'post_parent' => 0,
							'nopaging'    => true,
							's'           => '-[FAMILY]',
						);
						$parent_groups = get_posts( $args );

						if ( count( $parent_groups ) <= 1 ) {
							$show_textbox_organization_name = false;
							$show_hidden_organization       = true;
							$hidden_organization_id         = $parent_groups[0]->ID ?? 0;
						} elseif ( count( $parent_groups ) >= 2 ) {
							$show_textbox_organization_name = false;
						}
					}
				}
			}

			wp_nonce_field( 'learndash_groups_plus_add_organization_product', 'groups_plus_organization_product_nonce' );

			echo '<table class="variations learndash-groups-plus-organization-product-variations" cellspacing="0">';
			echo '			<tbody>';
			if ( $show_textbox_organization_name ) {
				echo '				<tr>';
				echo '					<td class="label"><label for="organization_name"> ' . sprintf( __( 'Enter %s name', 'learndash-groups-plus' ), learndash_get_custom_label_lower( 'organization' ) ) . '</label></td>';
				echo '					<td class="value">';
				echo '                       <input type="text" name="organization_name" id="organization_name" required/>';
				echo '					</td>';
				echo '				</tr>';
				$hide_exclude_from_publicly_sold_seats_checkbox = get_site_option( 'hide_exclude_from_publicly_sold_seats_checkbox' );
				echo '				<tr style="' . ( $hide_exclude_from_publicly_sold_seats_checkbox === 'yes' ? 'display:none' : 'display:block' ) . '">';
				echo '					<td class="label"><label for="exclude_from_individual_seat_purchase"> ' .
					esc_html(
						/**
						 * Filter the "Exclude from publicly sold seats" checkbox label.
						 *
						 * @since 1.0.0
						 *
						 * @param string $label Label for the checkbox.
						 *
						 * @return string
						 */
						apply_filters(
							'change_label_of_exclude_from_publicly_sold_seats', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- This was an existing filter.
							__( 'Exclude from publicly sold seats:', 'learndash-groups-plus' )
						)
					) . '</label>';
				echo '                       <input type="checkbox" class="" name="exclude_from_individual_seat_purchase" value="1" checked/>';
				echo '					</td>';
				echo '				</tr>';
			} elseif ( $show_textbox_organization_name === false && $show_hidden_organization === true ) {
				echo '				<tr>';
				echo '					<td> <input type="hidden" name="choose_organization" value="' . $hidden_organization_id . '" /> </td>';
				echo '				</tr>';
			} else {
				echo '				<tr>';
				echo '					<td class="label"><label for="choose_organization"> ' . sprintf( __( 'Choose %s name:', 'learndash-groups-plus' ), learndash_get_custom_label_lower( 'organization' ) ) . '</label></td>';
				echo '					<td class="value">';
				echo '                       <select name="choose_organization" id="choose_organization" required>';
				foreach ( $parent_groups as $parent_group ) {
					echo '<option value="' . $parent_group->ID . '" class="attached enabled">' . $parent_group->post_title . '</option>';
				}
				echo '                       </select>   ';
				echo '					</td>';
				echo '				</tr>';
			}
			if ( $product->is_type( 'variable' ) || $product->is_type( 'variable-subscription' ) ) {
				echo '				<tr> ';
				echo '					<td class="label"><label for=""> ' . sprintf(
					__( '%1$s %2$s package:', 'learndash-groups-plus' ),
					learndash_get_custom_label( 'organization' ),
					learndash_get_custom_label_lower( 'courses' ),
				) . ' </label></td>';
				echo '					<td class="value">';
				echo '                       <ul id="organization_courses_list" class="variable_product">';
				// echo '                            <li></li>';
				echo '                       </ul>';
				echo '					</td>';
				echo '				</tr> ';
			} else {
				echo '				<tr> ';
				echo '					<td class="label"><label for=""> ' . sprintf(
					__( 'Select %1$s %2$s:', 'learndash-groups-plus' ),
					learndash_get_custom_label_lower( 'organization' ),
					learndash_get_custom_label_lower( 'courses' ),
				) . ' </label></td>';
				echo '					<td class="value">';
				echo '					<input type="text" id="courses_search_box" placeholder="' . __( 'Search for names...', 'learndash-groups-plus' ) . '" title="Type in a name">';
				echo '                       <ul id="organization_courses_list">';
				foreach ( $groups_plus_organization_courses_arr as $groups_plus_organization_course ) {
					$course_name                   = $groups_plus_organization_course['course_name'];
					$course_price                  = $groups_plus_organization_course['course_price'];
					$course_price_without_currency = wc_trim_zeros( $course_price );
					$course_price_with_currency    = wc_price( $course_price );
					echo '                        <li><input type="number" name="my_organization_courses[' . $groups_plus_organization_course['course_id'] . ']" step="1" min="0"  class="input-text qty text learndash-groups-plus-organization-product-qty" data-price=' . esc_attr( $course_price_without_currency ) . ' placeholder="' . __( 'Quantity', 'learndash-groups-plus' ) . '"/> &nbsp;&nbsp;<label for="groups_plus_organization_course_' . $groups_plus_organization_course['course_id'] . '">' . $course_name . ' (' . $course_price_with_currency . ')</label> </li>';
				}
				echo '                       </ul>';
				echo '					</td>';
				echo '				</tr>';
			}
			echo '		</tbody> ';
			echo '</table>';

			if ( $product->is_type( $this->product_type ) ) {
				echo '<div class="btn-learndash-groups-plus-organization-price">' .
					esc_html__( 'Total price so far:', 'learndash-groups-plus' ) . ' ' . wp_kses_post( $product->get_price_html() ) .
				'</div>';
			} elseif (
				SharedFunctions::is_woocommerce_subscription_active() &&
				$product->is_type( 'subscription' )
			) {
				echo '<div class="btn-learndash-groups-plus-organization-price">' .
					esc_html__( 'Total price so far:', 'learndash-groups-plus' ) . ' ' . wp_kses_post( $product->get_price_html() ) .
				'</div>';
			}

			?>
<script type="text/javascript">
jQuery(document).ready(function() {
	var currency = '<?php echo get_woocommerce_currency_symbol(); ?>';
	var product_price = <?php echo $product_price; ?>;
	var is_new_organization = '<?php echo $show_textbox_organization_name; ?>';
	jQuery(document).on('change', 'input.learndash-groups-plus-organization-product-qty', function() {
		var totalPrice = 0;
		if ( is_new_organization ){
			totalPrice = product_price;
		}
		jQuery("input.learndash-groups-plus-organization-product-qty").each(function(index, element) {

			var single_course_price = parseFloat(jQuery(element).data('price'));
			var single_course_qty = jQuery(this).val();
			if (single_course_qty === "" || isNaN(single_course_qty)) {
				single_corse_qty = 0;
			}


			var single_course_total = single_course_price * single_course_qty;
			totalPrice = totalPrice + single_course_total;
		});
		totalPrice = currency + totalPrice.toFixed(2);
		jQuery( '.btn-learndash-groups-plus-organization-price > span.woocommerce-Price-amount' ).html( totalPrice );

	});
});
</script>
			<?php
		}

	}

	public function wc_add_to_cart_validation( $passed ) {
		$error_notice = array(); // Initializing

		if ( isset( $_POST['organization_name'] ) && empty( $_POST['organization_name'] ) ) {
			$passed         = false;
			$error_notice[] = sprintf(
				__( '"%s name" is a required field', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'organization' )
			);
		}

		if ( isset( $_POST['my_organization_courses'] ) && empty( array_filter( $_POST['my_organization_courses'] ) ) ) {
			$passed         = false;
			$error_notice[] = sprintf(
				// translators: organization course qty.
				__( 'Choose at least one %1$s %2$s qty', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'organization' ),
				learndash_get_custom_label_lower( 'course' ),
			);
		}

		// Display errors notices
		if ( ! empty( $error_notice ) ) {
			wc_add_notice( implode( '<br>', $error_notice ), 'error' );
		}

		return $passed;
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
			! SharedFunctions::filter_has_var( 'groups_plus_organization_product_nonce', INPUT_POST )
			|| ! wp_verify_nonce(
				Cast::to_string(
					SharedFunctions::filter_input(
						'groups_plus_organization_product_nonce',
						INPUT_POST
					)
				),
				'learndash_groups_plus_add_organization_product'
			)
		) {
			return $cart_item_data;
		}

		if ( ! $this->check_if_groups_plus_organizations_product_in_items( $product_id ) ) {
			return $cart_item_data;
		}

		$post_data = wp_unslash( $_POST );

		if (
			isset( $post_data['organization_name'] )
			&& ! empty( $post_data['organization_name'] )
		) {
			$organization_name = sanitize_text_field(
				$post_data['organization_name']
			);

			// Add the dropdown value as custom cart item data.
			$cart_item_data['organization_name'] = $organization_name;
		}

		if (
			isset( $post_data['exclude_from_individual_seat_purchase'] )
			&& ! empty( $post_data['exclude_from_individual_seat_purchase'] )
		) {
			$exclude_from_individual_seat_purchase = sanitize_text_field( $post_data['exclude_from_individual_seat_purchase'] );

			// Add the dropdown value as custom cart item data.
			$cart_item_data['exclude_from_individual_seat_purchase'] = $exclude_from_individual_seat_purchase;
		}

		if (
			isset( $post_data['choose_organization'] )
			&& ! empty( $post_data['choose_organization'] )
		) {
			$choose_organization = sanitize_text_field( $post_data['choose_organization'] );

			// Add the dropdown value as custom cart item data.
			$cart_item_data['choose_organization'] = $choose_organization;
		}

		if ( isset( $post_data['my_organization_courses'] ) ) {
			$my_organization_courses = $post_data['my_organization_courses'];

			$groups_plus_organization_courses = get_post_meta( $product_id, '_groups_plus_organization_courses', true );

			// checking selected courses from the organization course, if it's not, then will remove those courses to add into cart item.
			foreach ( $my_organization_courses as $my_organization_course => $qty ) {
				if (
					! in_array(
						Cast::to_string( $my_organization_course ),
						$groups_plus_organization_courses,
						true
					)
					|| empty( $qty )
				) {
					unset( $my_organization_courses[ $my_organization_course ] );
				}
			}

			// Add the dropdown value as custom cart item data.
			$cart_item_data['my_organization_courses'] = $my_organization_courses;
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
			! $this->check_if_groups_plus_organizations_product_in_items(
				Cast::to_int( $cart_item_data['product_id'] )
			)
		) {
			return $item_data;
		}

		$valid_cart_data = array_filter( $this->filter_valid_cart_data( $cart_item_data ) );

		foreach ( $valid_cart_data as $key => $item_data_value ) {
			$item_data_name   = '';
			$item_data_hidden = false;

			switch ( $key ) {
				case 'my_organization_courses':
					$item_data_name = sprintf(
						// translators: placeholder: Course label.
						esc_html__( '%s names', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'course' )
					);

					if ( is_array( $item_data_value ) ) {
						$item_data_value = array_map(
							function( $quantity, $course_id ) {
								$course_price = wc_price(
									get_post_meta(
										$course_id,
										'_course_price',
										true
									)
								);

								return get_the_title( $course_id ) . ' (' . $course_price . ') ' . __( 'Qty:', 'learndash-groups-plus' ) . $quantity;
							},
							$item_data_value,
							array_keys( $item_data_value )
						);

						$item_data_value = $this->format_item_data_list( $item_data_value );
					}

					break;
				case 'exclude_from_individual_seat_purchase':
					$item_data_hidden = true;
					break;
				default:
					$item_data_name = sprintf(
						// translators: placeholder: Organization label.
						esc_html__( '%s name', 'learndash-groups-plus' ),
						learndash_get_custom_label( 'organization' )
					);

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
					'hidden' => $item_data_hidden,
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
			'learndash_groups_plus_organizations_valid_cart_item_data_keys',
			[
				'organization_name',
				'choose_organization',
				'my_organization_courses',
				'exclude_from_individual_seat_purchase',
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
	 * Change the Cart Item Price to match the customer's choices.
	 *
	 * @since 1.0.0
	 *
	 * @param string              $price         Cart Item Price.
	 * @param array<string,mixed> $cart_item     Cart Item Data.
	 * @param string              $cart_item_key Cart Item Key.
	 *
	 * @return string
	 */
	public function change_product_cart_item_price( $price, $cart_item, $cart_item_key ) {
		if ( empty( $cart_item ) ) {
			return $price;
		}

		if (
			! is_array( $cart_item )
			|| empty( $cart_item['data'] )
		) {
			return $price;
		}

		$cart_item_data = $cart_item['data'];

		if ( ! $cart_item_data instanceof WC_Product ) {
			return $price;
		}

		if ( ! self::product_can_be_used_for_organizations( $cart_item_data->get_id() ) ) {
			return $price;
		}

		$product_id = $cart_item_data->get_id();
		$cart_item  = wp_parse_args(
			$cart_item,
			[
				'my_organization_courses' => [],
			]
		);

		$price = self::get_sum_price_of_selected_courses(
			$cart_item['my_organization_courses'],
			$product_id,
			! empty( $cart_item['organization_name'] )
		);
		$price = wc_price( $price );

		return $price;
	}

	/**
	 * Gets the product row subtotal in the cart.
	 *
	 * @since 1.0.0
	 * @deprecated 2.1.1 This filter caused issues when attempting to show the correct subtotal for Subscriptions with a Sign Up Fee. Simply removing it fixed the issue with no drawbacks.
	 *
	 * @param string     $product_subtotal Subtotal as a string.
	 * @param WC_Product $product          Product object.
	 * @param int        $quantity         Product row quantity.
	 * @param WC_Cart    $cart             WooCommerce Cart object.
	 *
	 * @return string
	 */
	public function wc_cart_product_subtotal( $product_subtotal, $product, $quantity, $cart ) {
		_deprecated_function( __METHOD__, '2.1.1' );

		return $product_subtotal;
	}

	/**
	 * Calculate the correct subtotals for cart items.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Cart $cart Cart object.
	 *
	 * @return void
	 */
	public function wc_before_calculate_totals( WC_Cart $cart ) {
		if ( count( $cart->cart_contents ) <= 0 ) {
			return;
		}

		foreach ( $cart->cart_contents as $cart_item_key => $values ) {
			$_product = $values['data'];
			if ( ! self::product_can_be_used_for_organizations( $_product->get_id() ) ) {
				continue;
			}

			$product_id = $_product->get_id();
			$values     = wp_parse_args(
				$values,
				[
					'my_organization_courses' => [],
				]
			);

			$price = self::get_sum_price_of_selected_courses(
				$values['my_organization_courses'],
				$product_id,
				! empty( $values['organization_name'] )
			);
			$_product->set_price( $price );
		}
	}

	/**
	 * Check if the given Product is configured to be used to purchase Organizations.
	 *
	 * @since 2.1.1
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool
	 */
	public static function product_can_be_used_for_organizations( int $product_id ): bool {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		// These checks should all be against the parent product.
		if ( $product instanceof WC_Product_Variation ) {
			$product = wc_get_product( $product->get_parent_id() );

			if ( ! $product instanceof WC_Product ) {
				return false;
			}

			$product_id = $product->get_id();
		}

		if (
			$product->is_type(
				'groups_plus_organizations'
			)
		) {
			return true;
		}

		$has_organization_purchase_enable = get_post_meta( $product_id, SharedFunctions::$is_organization_purchase_enable, true );

		// Product Types that aren't the Organizations Product Type require this Post Meta to be set.
		if ( ! $has_organization_purchase_enable ) {
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
	 * Add organization_name and organization courses in order meta
	 */
	/*
	 function add_values_to_order_item_meta($item_id, $cart_item,  $order_id )
	{
		if ( $this->check_if_groups_plus_organizations_product_in_items( $cart_item->legacy_values['product_id'] ) ){
				if(!empty($cart_item->legacy_values['organization_name']))
				{
					wc_add_order_item_meta($item_id,'organization_name',$cart_item->legacy_values['organization_name']);
				}

				if(!empty($cart_item->legacy_values['exclude_from_individual_seat_purchase']))
				{
					wc_add_order_item_meta($item_id,'exclude_from_individual_seat_purchase',$cart_item->legacy_values['exclude_from_individual_seat_purchase']);
				}

				if(!empty($cart_item->legacy_values['choose_organization']))
				{
					wc_add_order_item_meta($item_id,'choose_organization', $cart_item->legacy_values['choose_organization'] );
				}

				if(!empty($cart_item->legacy_values['my_organization_courses']))
				{
					$my_organization_courses = $cart_item->legacy_values['my_organization_courses'];
					$my_organization_courses = json_encode($my_organization_courses);

					wc_add_order_item_meta($item_id,'my_organization_courses',$my_organization_courses);

				}

		}

		// print_r($cart_item); die;
	} */

	/**
	 * Add organization_name and organization courses in order meta
	 */
	function wc_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
		if ( $this->check_if_groups_plus_organizations_product_in_items( $item->get_product_id() ) ) {
			if ( isset( $values['organization_name'] ) ) {
				$item->update_meta_data( 'organization_name', $values['organization_name'] );
			}

			if ( isset( $values['exclude_from_individual_seat_purchase'] ) ) {
				$item->update_meta_data( 'exclude_from_individual_seat_purchase', $values['exclude_from_individual_seat_purchase'] );
			}

			if ( isset( $values['choose_organization'] ) ) {
				$item->update_meta_data( 'choose_organization', $values['choose_organization'] );
			}

			if ( isset( $values['my_organization_courses'] ) ) {
				$my_organization_courses = $values['my_organization_courses'];
				$my_organization_courses = json_encode( $my_organization_courses );

				$item->update_meta_data( 'my_organization_courses', $my_organization_courses );
			}
		}
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

		$continue                              = false;
		$ld_primary_group_id                   = $last_seats_count = 0;
		$organization_name                     = '';
		$organization_id                       = 0;
		$exclude_from_individual_seat_purchase = 0;
		$my_organization_courses               = json_encode( array() );
		foreach ( $line_items as $item_id => $item ) {
			$_quantity = $item->get_quantity();
			if (
				$this->check_if_groups_plus_organizations_product_in_items(
					Cast::to_int( $item['product_id'] )
				)
				&& (
					isset( $item['organization_name'] )
					|| isset( $item['choose_organization'] )
				)
			) {
				$product_id = $item['product_id'];
				if ( isset( $item['organization_name'] ) ) {
					$organization_name = $item['organization_name'];
				} elseif ( isset( $item['choose_organization'] ) ) {
					$organization_id = $item['choose_organization'];
				}

				$exclude_from_individual_seat_purchase = $item['exclude_from_individual_seat_purchase'];

				$my_organization_courses = $item['my_organization_courses'];
				$continue                = true;
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
			'user_id'                               => $user_id,
			'order_id'                              => $order_id,
			'order_status'                          => $order_status,
			'organization_name'                     => $organization_name,
			'exclude_from_individual_seat_purchase' => $exclude_from_individual_seat_purchase,
			'organization_id'                       => $organization_id,
			'my_organization_courses'               => $my_organization_courses,
		);
		Database::add_organization_purchase( $attr );
	}

	/**
	 * Check if a given Product is an Organizations Product.
	 *
	 * @since 1.0.0
	 * @since 2.1.0 No longer check for WooCommerce Subscriptions for Variation Products.
	 *
	 * @param int $product_id Product ID.
	 *
	 * @return bool
	 */
	private function check_if_groups_plus_organizations_product_in_items( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		if ( $product->is_type( 'groups_plus_organizations' ) ) {
			return true;
		}

		$has_organization_purchase_enable = get_post_meta( $product_id, SharedFunctions::$is_organization_purchase_enable, true );

		// Product Types that aren't the Organizations Product Type require this Post Meta to be set.
		if ( ! $has_organization_purchase_enable ) {
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
	 * On Order Completion, create an Organization.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return void
	 */
	public function process_groups_plus_organizations_order_complete( $order_id ) {
		global $wpdb;
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

		$user    = $order->get_user();
		$user_id = $user->ID;

		// We only need to process the order once. That's why we check if the flag is set.
		$process_order = Cast::to_bool( $order->get_meta( SharedFunctions::$process_order_meta_field ) );

		if ( ! $process_order ) {
			return;
		}

		$line_items = $order->get_items( 'line_item' );

		if ( ! $line_items ) {
			return;
		}

		$processed_items   = 0;
		$primary_group_ids = [];

		foreach ( $line_items as $item_id => $item ) {
			if (
				$this->check_if_groups_plus_organizations_product_in_items(
					Cast::to_int( $item['product_id'] )
				)
				&& (
					isset( $item['organization_name'] )
					|| isset( $item['choose_organization'] )
				)
			) {
				$quantity   = $item->get_quantity();
				$product_id = $item['product_id'];
				$product    = wc_get_product( $product_id );
				$primary_group_id   = 0;
				$number_of_licenses = 0;
				$organization_name  = '';
				$my_organization_courses = [];
				$my_groups_plus_courses  = [];

				if ( isset( $item['organization_name'] ) ) {
					$organization_name                     = $item['organization_name'];
					$exclude_from_individual_seat_purchase = $item['exclude_from_individual_seat_purchase'];
					$ld_group_args                         = array(
						'post_type'    => 'groups',
						'post_status'  => 'publish',
						'post_title'   => sanitize_text_field(
							Cast::to_string(
								wp_unslash( $organization_name )
							)
						),
						'post_content' => '',
						/**
						 * Allows changing the Post Author of the created Team.
						 *
						 * @since 1.0.0
						 *
						 * @param int    $author_id Author ID.
						 * @param int    $current_user_id  Current User ID.
						 * @param string $context          Context for the Filter.
						 *
						 * @return int
						 */
						'post_author'  => apply_filters(
							'custom_group_post_author', // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- This was an existing filter.
							$user->ID,
							get_current_user_id(),
							'groups-plus-organizations-purchase'
						),
					);

					$primary_group_id = wp_insert_post( $ld_group_args );
					if ( $primary_group_id ) {
						update_post_meta( $primary_group_id, 'has_group_created_thru_wc', true );
						update_post_meta( $primary_group_id, 'exclude_from_individual_seat_purchase', $exclude_from_individual_seat_purchase );
						update_post_meta( $primary_group_id, 'has_group_created_for_courses_sell', true );

						// hook
						do_action( 'create_organization', $primary_group_id, $user_id );
					}
				}

				if ( isset( $item['choose_organization'] ) ) {
					$primary_group_id = $item['choose_organization'];
				}

				$my_organization_courses = (array) json_decode( $item['my_organization_courses'] );
				$my_groups_plus_courses  = array();
				if ( $primary_group_id ) {
					$number_of_licenses = (int) get_post_meta( $primary_group_id, 'number_of_licenses', true );

					if ( $product instanceof \WC_Product && SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'variable' ) ||
						$product instanceof \WC_Product && SharedFunctions::is_woocommerce_subscription_active() && $product->is_type( 'variable-subscription' )
					) {

						// do fetch courses selection from the variation selected
						$seats = (int) get_post_meta( $item['variation_id'], SharedFunctions::$variable_product_allow_seats, true );

						$number_of_licenses = $number_of_licenses + $seats;

						$groups_plus_organization_courses = get_post_meta( $item['variation_id'], SharedFunctions::$groups_plus_organization_courses_meta_field, true );
						if ( is_array( $groups_plus_organization_courses ) && ! empty( $groups_plus_organization_courses ) ) {
							$my_groups_plus_courses = $groups_plus_organization_courses;
						}
					} else {
						foreach ( $my_organization_courses as $my_organization_course => $qty ) {
							$post_course = get_post( $my_organization_course );
							if ( ! empty( $post_course ) ) {
								$groups_plus_name            = $post_course->post_title;
								$sql                         = "SELECT p.ID FROM {$wpdb->prefix}posts AS p
											INNER JOIN {$wpdb->prefix}postmeta AS pm
											ON ( p.ID = pm.post_id )
											WHERE
											p.post_type = 'groups'
											AND p.post_status = 'publish'
											AND pm.meta_key = %s AND pm.meta_value = %d LIMIT 1";
								$existing_secondary_group_id = $wpdb->get_var( $wpdb->prepare( $sql, 'group_created_for_course_id_' . $my_organization_course, $primary_group_id ) );
								if ( ! $existing_secondary_group_id ) {
									$ld_group_args      = array(
										'post_type'    => 'groups',
										'post_status'  => 'publish',
										'post_title'   => $groups_plus_name,
										'post_content' => '',
										'post_parent'  => $primary_group_id,
										'post_author'  => apply_filters( 'custom_group_post_author', $user->ID, get_current_user_id(), 'groups-plus-organizations-purchase' ),
									);
									$secondary_group_id = wp_insert_post( $ld_group_args );

									// hook
									do_action( 'create_groups_plus', $secondary_group_id, $user_id );

									$number_of_licenses = $number_of_licenses + intval( $qty );

									update_post_meta( $secondary_group_id, 'number_of_licenses', intval( $qty ) );

									// Created post meta that save the course id to group metadata
									update_post_meta( $secondary_group_id, 'group_created_for_course_id_' . $my_organization_course, $primary_group_id );

									// update_post_meta( $secondary_group_id, 'group_created_for_course_id_of_primary_group_' . $primary_group_id , $my_organization_course );

									// enrolled course to secondary group
									learndash_set_group_enrolled_courses( $secondary_group_id, array( $my_organization_course ) );
								} else {
									$nmb_of_licenses = (int) get_post_meta( $existing_secondary_group_id, 'number_of_licenses', true );

									$nmb_of_licenses = $nmb_of_licenses + intval( $qty );

									$number_of_licenses = $number_of_licenses + intval( $qty );

									update_post_meta( $existing_secondary_group_id, 'number_of_licenses', $nmb_of_licenses );
								}
							}
						}
						$my_groups_plus_courses = array_keys( $my_organization_courses );
					}
				}

				// Processes the organization for this line item.
				if ( $primary_group_id ) {
					$user_meta = get_userdata( $user_id );

					if (
						! in_array( 'group_leader', $user_meta->roles )
						&& ! in_array( 'administrator', $user_meta->roles )
					) {
						$u = new WP_User( $user_id );
						// Adds role.
						$u->add_role( 'group_leader' );
					}

					// Stores the group ID for this line item.
					$primary_group_ids[] = $primary_group_id;

					update_post_meta( $primary_group_id, 'number_of_licenses', $number_of_licenses );

					// Adds user to team group as leader.
					ld_update_leader_group_access( $user_id, $primary_group_id );

					// Sets enrolled courses to the current group.
					$primary_grp_enrolled_courses = learndash_group_enrolled_courses( $primary_group_id );
					$primary_grp_enrolled_courses = array_merge( $primary_grp_enrolled_courses, $my_groups_plus_courses );
					learndash_set_group_enrolled_courses( $primary_group_id, $primary_grp_enrolled_courses );
				}

				$processed_items++;
			}
		}

		// If we processed any items, store the primary group IDs in order meta and clean up.
		if ( $processed_items > 0 ) {
			// Store all primary group IDs in order meta (comma-separated if multiple).
			update_post_meta( $order_id, SharedFunctions::$linked_group_id_meta, implode( ',', $primary_group_ids ) );

			// We remove the flag so that it won't be processed again in the future after status updates.
			$order->delete_meta_data( SharedFunctions::$process_order_meta_field );
			$order->save_meta_data();
		}
	}


	/**
	 * Changes the group status to draft when a subscription status is updated to other than active.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Subscription $subscription WooCommerce subscription object.
	 *
	 * @return void
	 */
	public function change_group_to_draft( WC_Subscription $subscription ) {
		$order_id                           = $subscription->get_last_order( 'ids', array( 'parent' ) );
		$primary_group_ids                  = Cast::to_string( get_post_meta( $order_id, SharedFunctions::$linked_group_id_meta, true ) );
		$has_group_created_for_courses_sell = Cast::to_bool( get_post_meta( $order_id, 'has_group_created_for_courses_sell', true ) );

		if (
			empty( $primary_group_ids )
			|| $has_group_created_for_courses_sell !== true
		) {
			return;
		}

		// Handle both single group ID and multiple group IDs (comma-separated).
		$group_ids = array_map(
			static function ( $group_id ) {
				return Cast::to_int( trim( $group_id ) );
			},
			explode( ',', $primary_group_ids ),
		);

		foreach ( $group_ids as $primary_group_id ) {
			$child_groups = Group::get_child_groups( $primary_group_id );

			foreach ( $child_groups as $child_group ) {

				// $groups_plus_course_id = get_post_meta( $child_group->ID, 'group_created_for_course_id_of_primary_group_' . $primary_group_id, true );

				/**
				 * Remove courses from Group
				 */
				$group_course_ids = learndash_group_enrolled_courses( $child_group->ID );
				if ( $group_course_ids ) {
					foreach ( $group_course_ids as $course_id ) {
						ld_update_course_group_access( $course_id, $child_group->ID, true );
						$transient_key = "learndash_course_groups_{$course_id}";
						delete_transient( $transient_key );
					}
				}

				/**
				 * Remove users access from Group
				 */
				$users = learndash_get_groups_user_ids( $child_group->ID );
				if ( $users ) {
					foreach ( $users as $user_id ) {
						ld_update_group_access( $user_id, $child_group->ID, true );
						$transient_key = "learndash_user_groups_{$user_id}";
						delete_transient( $transient_key );
					}
				}

				// if (($key = array_search( $groups_plus_course_id , $secondary_grp_enrolled_courses)) !== false) {
				// unset($secondary_grp_enrolled_courses[$key]);
				// }

				update_post_meta( $child_group->ID, 'group_courses', $group_course_ids );
				update_post_meta( $child_group->ID, 'group_users', $users );

				$post              = get_post( $child_group->ID );
				$post->post_status = 'draft';
				wp_update_post( $post );
			}
		}
	}

	/**
	 * Changes the group status to published when a subscription status is updated to active.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Subscription $subscription WooCommerce subscription object.
	 *
	 * @return void
	 */
	public function change_group_to_published( WC_Subscription $subscription ) {
		$order_id = $subscription->get_last_order( 'ids', array( 'parent' ) );

		$primary_group_ids                  = Cast::to_string( get_post_meta( $order_id, SharedFunctions::$linked_group_id_meta, true ) );
		$has_group_created_for_courses_sell = Cast::to_bool( get_post_meta( $order_id, 'has_group_created_for_courses_sell', true ) );

		if (
			empty( $primary_group_ids )
			|| $has_group_created_for_courses_sell !== true
		) {
			return;
		}

		// Handle both single group ID and multiple group IDs (comma-separated).
		$group_ids = array_map(
			static function ( $group_id ) {
				return Cast::to_int( trim( $group_id ) );
			},
			explode( ',', $primary_group_ids ),
		);

		foreach ( $group_ids as $primary_group_id ) {
			$child_groups = Group::get_child_groups( $primary_group_id );

			foreach ( $child_groups as $child_group ) {

				/*
				$groups_plus_course_id = get_post_meta( $child_group->ID, 'group_created_for_course_id_of_primary_group_' . $primary_group_id, true );

				$secondary_grp_enrolled_courses = learndash_group_enrolled_courses($child_group->ID);
				array_push($secondary_grp_enrolled_courses, $groups_plus_course_id );
				$secondary_grp_enrolled_courses = array_values(array_unique($secondary_grp_enrolled_courses));
				learndash_set_group_enrolled_courses($child_group->ID,  $secondary_grp_enrolled_courses); */

				$post              = get_post( $child_group->ID );
				$post->post_status = 'publish';
				wp_update_post( $post );

				/**
				 * Assign courses back to group
				 */
				$group_course_ids = get_post_meta( $child_group->ID, 'group_courses', true );
				if ( $group_course_ids ) {
					foreach ( $group_course_ids as $course_id ) {
						ld_update_course_group_access( $course_id, $child_group->ID, false );
						$transient_key = "learndash_course_groups_{$course_id}";
						delete_transient( $transient_key );
					}
				}
				delete_post_meta( $child_group->ID, 'group_courses' );

				/**
				 * Assign users back to group
				 */
				$user_ids = get_post_meta( $child_group->ID, 'group_users', true );
				if ( $user_ids ) {
					foreach ( $user_ids as $user_id ) {
						ld_update_group_access( $user_id, $child_group->ID, false );
						$transient_key = "learndash_user_groups_{$user_id}";
						delete_transient( $transient_key );
					}
				}
				delete_post_meta( $child_group->ID, 'group_users' );

			}
		}

	}

	/**
	 * Gets the price to add to a line item based on the chosen Courses and the Quantity.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int, int> $chosen_courses      Chosen Courses. Key is Course ID, value is the number of Seats to purchase.
	 * @param int             $product_id          Product ID.
	 * @param bool            $is_new_organization Whether this is a New Organization or if these Courses are being added to an existing Organization.
	 *
	 * @return float
	 */
	private function get_sum_price_of_selected_courses( $chosen_courses, $product_id = 0, $is_new_organization = false ) {
		$total_price = 0.0;
		$product     = wc_get_product( $product_id );

		if ( $is_new_organization ) {
			$total_price = Cast::to_float( $product->get_price( 'unfiltered' ) );
		}

		// Ensure we grab prices for Variable Products correctly.
		if (
			empty( $chosen_courses )
			&& $product instanceof WC_Product_Variation
		) {
			// Sanity check for type.
			$chosen_courses = [];

			$course_ids = get_post_meta(
				$product_id,
				SharedFunctions::$groups_plus_organization_courses_meta_field,
				true
			);

			if (
				empty( $course_ids )
				|| ! is_array( $course_ids )
			) {
				$course_ids = [];
			}

			// Variations control Seat Count via the Variation Meta.
			$seats = Cast::to_int(
				get_post_meta(
					$product_id,
					SharedFunctions::$variable_product_allow_seats,
					true
				)
			);

			foreach ( $course_ids as $course_id ) {
				$chosen_courses[ $course_id ] = $seats;
			}
		}

		if ( ! is_array( $chosen_courses ) ) {
			return $total_price;
		}

		foreach ( $chosen_courses as $chosen_course => $qty ) {
			$course_price = Cast::to_float(
				get_post_meta(
					Cast::to_int( $chosen_course ),
					'_course_price',
					true
				)
			);
			if ( ! empty( $course_price ) ) {
				$total_price = $total_price + ( $course_price * $qty );
			}
		}

		return $total_price;
	}

	/**
	 *
	 */
	public function load_new_product_type() {
		new Organizations_Product( $this->product_type );
	}

	/**
	 *
	 */
	public function woocommerce_simple_add_to_cart() {
		wc_get_template( 'single-product/add-to-cart/simple.php' );
	}


	/**
	 * Add metabox for team course price.
	 */

	public function add_metabox() {
		add_meta_box(
			'learndash-groups-plus-organizations-meta-box',
			__( 'LearnDash Groups Plus WooCommerce', 'learndash-groups-plus' ),
			array( $this, 'render_metabox' ),
			'sfwd-courses',
			'side',
			'default'
		);
	}

	/**
	 * Renders the meta box.
	 */
	public function render_metabox( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
		$course_price = get_post_meta( $post->ID, '_course_price', true );
		// Echo out the field
		ob_start();
		?>
<p>
	<label for="smashing-post-team"><?php _e( 'Course Price', 'learndash-groups-plus' ); ?></label>
	<br />
	<input type="text" name="_course_price" id="course_price"
		value="<?php echo esc_attr( $course_price ); ?>" size="30" />
</p>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Handles saving the meta box.
	 */
	public function save_course_price( $post_id, $post ) {
		// Add nonce for security and authentication.
		$nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
		$nonce_action = 'custom_nonce_action';

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

		$course_price = sanitize_text_field( $_POST['_course_price'] );

		update_post_meta( $post_id, '_course_price', $course_price );
	}

	function get_courses_of_variation_product() {
		$error   = null;
		$success = false;
		try {

			$html_string  = '';
			$variation_id = $_POST['data']['variation_id'];

			$product           = wc_get_product( $variation_id );
			$parent_product_id = $product->get_parent_id();

			$is_organization_purchase_enable       = get_post_meta( $parent_product_id, SharedFunctions::$is_organization_purchase_enable, true );
			$is_organization_group_purchase_enable = get_post_meta( $parent_product_id, SharedFunctions::$is_organization_group_purchase_enable, true );

			// for the organization purchase enable, return courses
			if ( $is_organization_purchase_enable === 'on' ) {
				$groups_plus_organization_courses = get_post_meta( $variation_id, SharedFunctions::$groups_plus_organization_courses_meta_field, true );

				$html_string = '';
				foreach ( $groups_plus_organization_courses as $groups_plus_organization_course ) {
					$post = get_post( $groups_plus_organization_course );
					if ( ! empty( $post ) ) {
						$html_string .= '<li>' . get_the_title( $groups_plus_organization_course ) . '</li>';
					}
				}
			}
			// for the organization group purchase enable, return groups
			elseif ( $is_organization_group_purchase_enable === 'on' ) {
				$groups_plus_organization_groups = get_post_meta( $variation_id, SharedFunctions::$groups_plus_organization_groups_meta_field, true );

				if ( ! is_array( $groups_plus_organization_groups ) ) {
					$groups_plus_organization_groups = (array) $groups_plus_organization_groups;
				}

				$html_string = '';
				foreach ( $groups_plus_organization_groups as $groups_plus_organization_group ) {
					$html_string .= '<li>' . get_the_title( $groups_plus_organization_group ) . '</li>';
				}
			}
			$success = true;
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}

		echo json_encode(
			array(
				'success' => $success,
				'errors'  => $error,
				'html'    => $html_string,
			)
		);
		die;
	}


	/**
	 * Show pricing fields for team courses product.
	 */
	function groups_plus_organizations_custom_js() {
		global $post, $product_object;

		if ( ! $post ) {
			return; }

		if ( 'product' != $post->post_type ) :
			return;
		endif;

		$is_groups_plus_organizations = $product_object && $this->product_type === $product_object->get_type() ? true : false;

		?>
<script type='text/javascript'>
jQuery(document).ready(function() {
	//for Price tab
	// jQuery('#general_product_data .pricing').addClass('show_if_groups_plus_organizations');
	var selectedProductType = ''
	var $group_pricing = '#general_product_data, .options_group.pricing';
	//jQuery($group_pricing).addClass('show_if_courses').addClass('show_if_license');
	jQuery('.options_group.pricing').addClass('show_if_groups_plus_organizations');
	jQuery('.form-field._tax_status_field').parent().addClass('show_if_groups_plus_organizations');

	var product_type_selected = jQuery('#product-type').val();
	if ( '<?php echo $this->product_type; ?>' === product_type_selected || 'groups_plus_seats' === product_type_selected) {
		jQuery('li.general_options').show(); //Show general tab
		jQuery('li.general_options a').trigger('click'); //Imitate general tab clicked
		jQuery('.options_group.pricing').removeClass('hidden').show(); //Show pricing fields
		jQuery('.form-field._tax_status_field').parent().show(); //Show tax fields
	}

	if ( '<?php echo $this->product_type; ?>' === product_type_selected ){
		jQuery('li.inventory_options').show(); //Show inventory tab
	}

	if ('groups_plus_organizations_groups' === product_type_selected ){
		jQuery('li.inventory_options').show(); //Show inventory tab
	}

		<?php
		if ( $is_groups_plus_organizations ) {
			?>
			 jQuery('#general_product_data .pricing')
		.show();
	<?php } ?>
});
</script>
		<?php
	}

}
?>
