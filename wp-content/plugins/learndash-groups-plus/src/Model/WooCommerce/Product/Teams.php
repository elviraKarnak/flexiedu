<?php
/**
 * WooCommerce Team Product Type.
 *
 * @since 2.0.0
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Model\WooCommerce\Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WC_Product;

/**
 * Class Teams
 *
 * @since 2.0.0
 */
class Teams extends WC_Product {
	/**
	 * Product type.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	protected $product_type;

	/**
	 * Class constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WC_Product|object $product Product to init.
	 *
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 */
	public function __construct( $product = 0 ) {
		$this->product_type = 'groups_plus_teams';

		parent::__construct( $product );

		$this->set_sold_individually( false );
	}

	/**
	 * Get product type.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 *
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 */
	public function get_type() {
		return $this->product_type;
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 *
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 */
	public function add_to_cart_url() {
		/**
		 * This filter is documented in the WooCommerce plugin under woocommerce/includes/abstracts/abstract-wc-product.php
		 *
		 * @var string
		 */
		return apply_filters( 'woocommerce_product_add_to_cart_url', $this->get_permalink(), $this ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	}

	/**
	 * Get add to cart text.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 *
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 */
	public function add_to_cart_text() {
		/**
		 * This filter is documented at https://woocommerce.com/document/change-add-to-cart-button-text/
		 *
		 * @var string
		 */
		return apply_filters( 'woocommerce_product_add_to_cart_text', __( 'Select options', 'learndash-groups-plus' ), $this ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	}
}
