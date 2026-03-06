<?php
/**
 * WooCommerce Pre-Built Organizations Product Type.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Model\WooCommerce\Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WC_Product;

/**
 * Class Organizations_Groups
 */
class Organizations_Groups extends WC_Product {
	/**
	 * Product type.
	 *
	 * @var string
	 */
	protected $product_type;

	/**
	 * Class constructor.
	 *
	 * @param string $product Product type.
	 */
	public function __construct( $product ) {
		$this->product_type = 'groups_plus_organizations_groups';
		parent::__construct( $product );
	}

	/**
	 * Get product type.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->product_type;
	}

	/**
	 * Get the add to url used mainly in loops.
	 * 
	 * @since 1.0
	 *
	 * @return string
	 */
	public function add_to_cart_url() {
		return apply_filters( 'woocommerce_product_add_to_cart_url', $this->get_permalink(), $this );
	}

	/**
	 * Get add to cart text.
	 *
	 * @since 1.0
	 * 
	 * @return string
	 */
	public function add_to_cart_text() {
		return apply_filters( 'woocommerce_product_add_to_cart_text', __( 'Select options', 'learndash-groups-plus' ), $this );
	}
}
