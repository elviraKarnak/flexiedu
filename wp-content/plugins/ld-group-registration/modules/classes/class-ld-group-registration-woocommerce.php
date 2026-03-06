<?php
/**
 * Woocommerce Module
 *
 * @since 4.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/modules/classes
 */

namespace LdGroupRegistration\Modules\Classes;

defined( 'ABSPATH' ) || exit;

use LearnDash\Core\Utilities\Cast;
use WC_Product;

if ( ! class_exists( 'Ld_Group_Registration_Woocommerce' ) ) {
	/**
	 * LD Group Registration Woocommerce
	 */
	class Ld_Group_Registration_Woocommerce {
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
		 * Group Registration order item meta key for group order
		 *
		 * @var string
		 */
		protected $ldgr_order_item_key;

		/**
		 * Group Registration order item meta key for group name
		 *
		 * @var string
		 */
		protected $ldgr_group_name_item_key;

		public function __construct() {
			$this->ldgr_order_item_key      = '_ldgr_is_group_reg_order';
			$this->ldgr_group_name_item_key = '_ldgr_group_name';
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
			if ( $value == 'on' ) {
				$value_show     = get_post_meta( $product_id, '_is_checkbox_show_front_end', true );
				$enable_package = ldgr_check_package_enabled( $product_id );

				$default_option = get_post_meta( $product_id, '_ldgr_front_default_option', true );

				if ( isset( $_GET['add-to-cart'] ) && ( ( isset( $default_option ) && 'group' === $default_option ) || isset( $_GET['ldgr_group_name'] ) || isset( $_GET['ldgr_group_id'] ) ) ) {
					$_POST['wdm_ld_group_active'] = 'on';
				} elseif ( isset( $_GET['add-to-cart'] ) ) {
					$_POST['wdm_ld_group_active'] = '';
				}

				// New Price.
				if ( ! isset( $_POST['ldgr_new_price'] ) ) {
					$product                 = wc_get_product( $product_id );
					$price                   = $product->get_price();
					$_POST['ldgr_new_price'] = $price;
				}
				if ( ! isset( $_POST['ldgr_dynamic_value'] ) ) {
					$_POST['ldgr_dynamic_value'] = '';
				}
				// Set group product as active.
				if ( isset( $_POST['wdm_ld_group_active'] ) && 'on' === $_POST['wdm_ld_group_active'] ) {
					// Set dynamic fields.
					if ( ! isset( $_POST['ldgr_dynamic_option'] ) ) {
						$_POST['ldgr_dynamic_option'] = 'create_new';
					}
					// Create group; group name.
					if ( isset( $_GET['ldgr_group_name'] ) && ! empty( $_GET['ldgr_group_name'] ) ) {
						// Set group name from URL.
						$_POST['ldgr_group_name'] = sanitize_text_field( $_GET['ldgr_group_name'] );
					} elseif ( ! isset( $_POST['ldgr_group_name'] ) ) {
						// Set group name from autofill.
						$_POST['ldgr_group_name'] = esc_html( get_the_title( $product_id ) . ' | ' . date( 'm/d/Y' ) );
					}

					// Add more seats.
					if ( isset( $_GET['ldgr_group_id'] ) ) {
						$group_id = sanitize_text_field( wp_unslash( $_GET['ldgr_group_id'] ) );
						$group    = get_post( $group_id );

						if ( $group ) {
							if ( $product_id === $this->ldgr_check_related_group_product() ) {
								// The group exists.
								$_POST['ldgr_group_name']     = get_the_title( wp_unslash( $_GET['ldgr_group_id'] ) );
								$_POST['ldgr_dynamic_option'] = 'increase_seats';
								$_POST['ldgr_dynamic_value']  = $group_id;
							} else {
								wc_add_notice(
									sprintf(
										/* translators: Group label */
										__( 'Error : Incorrect %1$s selected for purchasing additional seats. The following item added to cart will result in a new %2$s product purchase and a new %3$s will be created with the selected number of seats.', 'wdm_ld_group' ),
										\LearnDash_Custom_Label::label_to_lower( 'group' ),
										\LearnDash_Custom_Label::label_to_lower( 'group' ),
										\LearnDash_Custom_Label::label_to_lower( 'group' ),
									),
									'error'
								);
							}
						} else {
							// The group doesn't exist.
							wc_add_notice(
								sprintf(
									/* translators: Group label */
									__(
										'
                                    Error : The selected %1$s doesn\'t exist for purchasing additional seats. The following item added to cart will result in a new %2$s product purchase and a new %3$s will be created with the selected number of seats.
                                    ',
										'wdm_ld_group'
									),
									\LearnDash_Custom_Label::label_to_lower( 'group' ),
									\LearnDash_Custom_Label::label_to_lower( 'group' ),
									\LearnDash_Custom_Label::label_to_lower( 'group' ),
								),
								'error'
							);
						}
					}
				}

				// Set minimum quantity.
				if ( 'on' === get_option( 'ldgr_autofill_group_name' ) && isset( $_POST['wdm_ld_group_active'] ) && 'on' === $_POST['wdm_ld_group_active'] ) {
					$min_qty = (int) get_post_meta(
						$product_id,
						'ldgr_bulk_discount_min_qty_value',
						1
					);
					// Set minimum quantity.
					if ( $quantity < $min_qty ) {
						$quantity = (int) $min_qty;
					}
				}

				// Check if variation.
				$product_type = ldgr_get_woo_product_type( $product_id );
				if ( 'variable' === $product_type ) {
					// Set Variation product.
					$variation_id = filter_input( INPUT_GET, 'variation_id', FILTER_SANITIZE_NUMBER_INT );

					if ( ! isset( $variation_id ) ) {
						$variation_id = filter_input( INPUT_POST, 'variation_id', FILTER_SANITIZE_NUMBER_INT );
					}

					$variation = wc_get_product( $variation_id );
					if ( $variation && $variation->is_type( 'variation' ) ) {
						// The variation ID is valid.
						if ( isset( $_GET['ldgr_group_name'] ) || ( empty( $_POST[ 'ldgr_group_name_' . $variation_id ] ) && 'on' === get_option( 'ldgr_autofill_group_name' ) ) ) {
							$_POST[ 'ldgr_group_name_' . $variation_id ] = sanitize_text_field( $_POST['ldgr_group_name'] );
						}
					} else {
						// The variation ID is not valid.
						wc_add_notice(
							sprintf(
								__( 'This is a variation product please provide valid variation ID', 'wdm_ld_group' ),
								$variation_id
							),
							'error'
						);
						return false;
					}
				}

				if ( $value_show == 'on' && ! $enable_package ) {
					if ( isset( $_POST['wdm_ld_group_active'] ) ) {
						if ( 'on' != $_POST['wdm_ld_group_active'] ) {
							global $woocommerce;
							$items = $woocommerce->cart->get_cart();
							foreach ( $items as $key => $item ) {
								if ( isset( $item['wdm_ld_group_active'] ) ) {
									continue;
								}
								if ( $item['product_id'] == $product_id ) {
									wc_add_notice( __( 'Product already exists in cart.', 'wdm_ld_group' ), 'error' );
									return false;
								}
							}
							if ( $quantity > 1 ) {
								wc_add_notice( __( 'Only 1 quantity allowed.', 'wdm_ld_group' ), 'error' );
								return false;
							}
						} else {
							// Check if variation.
							$product_type = ldgr_get_woo_product_type( $product_id );
							if ( 'variable' === $product_type ) {
								// URL Parameters computation.
								if ( null !== filter_input( INPUT_GET, 'variation_id', FILTER_SANITIZE_NUMBER_INT ) ) {
									$variation_id = filter_input( INPUT_GET, 'variation_id', FILTER_SANITIZE_NUMBER_INT );
								} else {
									$variation_id = filter_input( INPUT_POST, 'variation_id', FILTER_SANITIZE_NUMBER_INT );
								}

								// The variation ID is valid.
								if ( isset( $_GET['ldgr_group_name'] ) || ( empty( $_POST[ 'ldgr_group_name_' . $variation_id ] ) && 'on' === get_option( 'ldgr_autofill_group_name' ) ) ) {
									$_POST[ 'ldgr_group_name_' . $variation_id ] = sanitize_text_field( $_POST['ldgr_group_name'] );
								}

								if ( ! array_key_exists( 'ldgr_group_name_' . $variation_id, $_POST ) || empty( $_POST[ 'ldgr_group_name_' . $variation_id ] ) ) {
									wc_add_notice(
										sprintf(
										/* translators: Group label */
											__( 'Please select %s name..', 'wdm_ld_group' ),
											\LearnDash_Custom_Label::label_to_lower( 'group' )
										),
										'error'
									);
									return false;
								}
							} elseif ( ! isset( $_POST['ldgr_group_name'] ) || '' === $_POST['ldgr_group_name'] ) {
								wc_add_notice(
									sprintf(
									/* translators: Group label */
										__( 'Please select %s name', 'wdm_ld_group' ),
										\LearnDash_Custom_Label::label_to_lower( 'group' )
									),
									'error'
								);
								return false;
							}
						}
					} else {
						if ( ! isset( $_POST['wdm_ld_group_active'] ) || 'on' != $_POST['wdm_ld_group_active'] ) {
							wc_add_notice( __( 'Product already exists in cart.', 'wdm_ld_group' ), 'error' );
						} else {
							wc_add_notice( __( 'Select type of product.', 'wdm_ld_group' ), 'error' );
						}
						return false;
					}
				} else {
					// old code.

					// if ( ! isset( $_POST['ldgr_group_name'] ) || '' === $_POST['ldgr_group_name'] ) {
					// wc_add_notice(
					// sprintf(
					// * translators: Group label */
					// __( 'Please select %s name', 'wdm_ld_group' ),
					// \LearnDash_Custom_Label::label_to_lower( 'group' )
					// ),
					// 'error'
					// );
					// return false;
					// }

					// end old code.
					// Check if variation.
					$product_type = ldgr_get_woo_product_type( $product_id );
					if ( 'variable' === $product_type ) {
						// URL Parameters computation.
						if ( null !== filter_input( INPUT_GET, 'variation_id', FILTER_SANITIZE_NUMBER_INT ) ) {
							$variation_id = filter_input( INPUT_GET, 'variation_id', FILTER_SANITIZE_NUMBER_INT );
						} else {
							$variation_id = filter_input( INPUT_POST, 'variation_id', FILTER_SANITIZE_NUMBER_INT );
						}

						// The variation ID is valid.
						if ( isset( $_GET['ldgr_group_name'] ) || ( empty( $_POST[ 'ldgr_group_name_' . $variation_id ] ) && 'on' === get_option( 'ldgr_autofill_group_name' ) ) ) {
							$_POST[ 'ldgr_group_name_' . $variation_id ] = sanitize_text_field( $_POST['ldgr_group_name'] );
						}

						if ( ! array_key_exists( 'ldgr_group_name_' . $variation_id, $_POST ) || empty( $_POST[ 'ldgr_group_name_' . $variation_id ] ) ) {
							wc_add_notice(
								sprintf(
								/* translators: Group label */
									__( 'Please select %s name..', 'wdm_ld_group' ),
									\LearnDash_Custom_Label::label_to_lower( 'group' )
								),
								'error'
							);
							return false;
						}
					} elseif ( ! isset( $_POST['ldgr_group_name'] ) || '' === $_POST['ldgr_group_name'] ) {
						wc_add_notice(
							sprintf(
							/* translators: Group label */
								__( 'Please select %s name', 'wdm_ld_group' ),
								\LearnDash_Custom_Label::label_to_lower( 'group' )
							),
							'error'
						);
						return false;
					}

					// end new pasted code.
				}
			}
			return $passed;
		}

		/**
		 * Update woocommerce cart item quantity
		 *
		 * @param string $product_quantity  Cart item quantity HTML.
		 * @param string $cart_item_key     Cart item key.
		 * @param array  $cart_item          Cart item details.
		 *
		 * @return string                   Updated cart item quantity HTML.
		 */
		public function woo_update_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
			$product_id = $cart_item['product_id'];
			$value      = get_post_meta( $product_id, '_is_group_purchase_active', true );
			if ( $value == 'on' ) {
				$value_show = get_post_meta( $product_id, '_is_checkbox_show_front_end', true );
				if ( 'on' == $value_show && ! isset( $cart_item['wdm_ld_group_active'] ) ) {
					$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
					return $product_quantity;
				}

				if ( isset( $cart_item['ldgr_dynamic_option'] ) && 'increase_seats' === $cart_item['ldgr_dynamic_option'] ) {
					return $product_quantity;
				}

				$min_qty_check = get_post_meta( $product_id, 'ldgr_bulk_discount_min_qty_check', 1 );

				if ( 'on' === $min_qty_check ) {
					$min_qty = get_post_meta( $product_id, 'ldgr_bulk_discount_min_qty_value', 1 );

					if ( ! is_nan( floatval( $min_qty ) ) ) {
						return str_replace( 'min="0"', 'min="' . $min_qty . '"', $product_quantity );
					}
				}
			}

			return $product_quantity;
		}

		/**
		 * Update Cart item name on group product purchase
		 *
		 * @param string $product_title     Product title in cart.
		 * @param array  $cart_item          Cart item details.
		 * @param string $cart_item_key     Cart item key.
		 *
		 * @return string                   Updated product title in cart.
		 */
		public function woo_update_cart_item_name( $product_title, $cart_item, $cart_item_key ) {
			$product_id = $cart_item['product_id'];
			$value      = get_post_meta( $product_id, '_is_group_purchase_active', true );
			if ( 'on' == $value ) {
				$value_show     = get_post_meta( $product_id, '_is_checkbox_show_front_end', true );
				$enable_package = ldgr_check_package_enabled( $product_id );
				if ( 'on' != $value_show || isset( $cart_item['wdm_ld_group_active'] ) || $enable_package ) {
					$temp = $product_title . '<br>' . apply_filters( 'wdm_group_registration_label_below_product_name', __( 'Group Registration', 'wdm_ld_group' ), $product_title, $cart_item_key, $cart_item );
					if ( array_key_exists( 'ldgr_group_name', $cart_item ) && ! empty( $cart_item['ldgr_group_name'] ) ) {
						$temp = $product_title . '<br>' . sprintf(
						// translators: Group label, Courses Label.
							__( '%2$s %1$s Name %3$s : ', 'wdm_ld_group' ),
							\LearnDash_Custom_Label::get_label( 'group' ),
							'<b>',
							'</b>'
						)
						. stripslashes( $cart_item['ldgr_group_name'] );
					}

					// Check for variations.
					$variation_id = array_key_exists( 'variation_id', $cart_item ) ? intval( $cart_item['variation_id'] ) : '';
					if ( ! empty( $variation_id ) && array_key_exists( 'ldgr_group_name_' . $variation_id, $cart_item ) && ! empty( $cart_item[ 'ldgr_group_name_' . $variation_id ] ) ) {
						$temp = $product_title . '<br>' .
						sprintf(
										/* translators: Group label */
							__( '<strong> %s name </strong> : ', 'wdm_ld_group' ),
							\LearnDash_Custom_Label::label_to_lower( 'group' )
						)
						. stripslashes( $cart_item[ 'ldgr_group_name_' . $variation_id ] );
					}
					return $temp;
				}
			}

			return $product_title;
		}

		/**
		 * Adding 'Group Registration' item meta if group_registration enabled by user
		 *
		 * @param int                   $item_id ID of Item in the cart.
		 * @param ?array<string, mixed> $values  Item data.
		 *
		 * @return void
		 */
		public function update_woo_order_item_meta( $item_id, $values ) {
			if ( $values === null ) {
				return;
			}

			$product_id = Cast::to_int( $values['product_id'] );
			$value      = get_post_meta( $product_id, '_is_group_purchase_active', true );

			if ( 'on' == $value ) {
				$value_show     = get_post_meta( $product_id, '_is_checkbox_show_front_end', true );
				$enable_package = ldgr_check_package_enabled( $product_id );
				// $values = $item['legacy_values'];
				if ( 'on' != $value_show || isset( $values['wdm_ld_group_active'] ) || $enable_package ) {
					// wc_add_order_item_meta($item_id, 'Group Registration', $values[ 'wdm_ld_group_active' ]);.
					wc_add_order_item_meta(
						$item_id,
						__( 'Group Registration', 'wdm_ld_group' ),
						'<span class="dashicons dashicons-yes"></span>'
					);
					// Add hidden meta to be used for detecting group order
					wc_add_order_item_meta( $item_id, $this->ldgr_order_item_key, 1 );

					if ( array_key_exists( 'ldgr_group_name', $values ) && ! empty( $values['ldgr_group_name'] ) ) {
						wc_add_order_item_meta( $item_id, __( 'Group Name', 'wdm_ld_group' ), stripslashes( Cast::to_string( $values['ldgr_group_name'] ) ) );
						// Add hidden meta to be used for detecting group name
						wc_add_order_item_meta( $item_id, $this->ldgr_group_name_item_key, stripslashes( Cast::to_string( $values['ldgr_group_name'] ) ) );
					}

					// Check for variations.
					$variation_id = array_key_exists( 'variation_id', $values ) ? Cast::to_int( $values['variation_id'] ) : '';

					if ( ! empty( $variation_id ) && array_key_exists( 'ldgr_group_name_' . $variation_id, $values ) && ! empty( $values[ 'ldgr_group_name_' . $variation_id ] ) ) {
						wc_add_order_item_meta( $item_id, __( 'Group Name', 'wdm_ld_group' ), stripslashes( Cast::to_string( $values[ 'ldgr_group_name_' . $variation_id ] ) ) );
						// Add hidden meta to be used for detecting group name
						wc_add_order_item_meta( $item_id, $this->ldgr_group_name_item_key, stripslashes( Cast::to_string( $values[ 'ldgr_group_name_' . $variation_id ] ) ) );
					}
				}
			}
			if ( isset( $values['wdm_enroll_me'] ) ) {
				wc_add_order_item_meta( $item_id, '_add_group_leader', 'on' );
			}
		}

		/**
		 * Checking if group registration enabled by the user for product
		 *
		 * @param array  $item   Item details.
		 * @param array  $values Array of values.
		 * @param string $key    Item key.
		 *
		 * @return array        Updated item details.
		 */
		public function check_group_registration_status_for_product( $item, $values, $key ) {
			$product_id = $values['product_id'];
			$value      = get_post_meta( $product_id, '_is_group_purchase_active', true );
			if ( 'on' == $value ) {
				$value_show     = get_post_meta( $product_id, '_is_checkbox_show_front_end', true );
				$enable_package = ldgr_check_package_enabled( $product_id );
				if ( 'on' != $value_show || array_key_exists( 'wdm_ld_group_active', $values ) || $enable_package ) {
					// $item[ 'wdm_ld_group_active' ] = $values[ 'wdm_ld_group_active' ];
					$item['wdm_ld_group_active'] = 'on';
					if ( array_key_exists( 'ldgr_group_name', $values ) ) {
						$item['ldgr_group_name'] = stripslashes( $values['ldgr_group_name'] );
					}
					// Check for variations.
					$variation_id = array_key_exists( 'variation_id', $values ) ? intval( $values['variation_id'] ) : '';
					if ( ! empty( $variation_id ) && array_key_exists( 'ldgr_group_name_' . $variation_id, $values ) ) {
						$item[ 'ldgr_group_name_' . $variation_id ] = stripslashes( $values[ 'ldgr_group_name_' . $variation_id ] );
					}
				}
			}

			if ( array_key_exists( 'wdm_enroll_me', $values ) ) {
				$item['wdm_enroll_me'] = 'on';
			}

			return $item;
		}

		/**
		 * Setting cart item data for checking if group registration is checked by user
		 *
		 * @param array $cart_item_meta     Cart item metadata.
		 * @param int   $product_id         ID of the product.
		 *
		 * @return array                Updated cart item metadata.
		 */
		public function save_cart_item_data( $cart_item_meta, $product_id ) {
			$value = get_post_meta( $product_id, '_is_group_purchase_active', true );
			if ( 'on' == $value ) {
				$value_show     = get_post_meta( $product_id, '_is_checkbox_show_front_end', true );
				$enable_package = ldgr_check_package_enabled( $product_id );
				if ( 'on' != $value_show || ( isset( $_POST['wdm_ld_group_active'] ) && '' != $_POST['wdm_ld_group_active'] ) || $enable_package ) {
					// $cart_item_meta[ 'wdm_ld_group_active' ] = $_POST[ 'wdm_ld_group_active' ];
					$cart_item_meta['wdm_ld_group_active'] = 'on';
					if ( array_key_exists( 'ldgr_group_name', $_POST ) ) {
						$cart_item_meta['ldgr_group_name'] = sanitize_text_field( $_POST['ldgr_group_name'] );
					}

					// Check for variations.
					$variation_id = array_key_exists( 'variation_id', $_POST ) ? intval( $_POST['variation_id'] ) : '';
					if ( ! empty( $variation_id ) && array_key_exists( 'ldgr_group_name_' . $variation_id, $_POST ) ) {
						$cart_item_meta[ 'ldgr_group_name_' . $variation_id ] = sanitize_text_field( $_POST[ 'ldgr_group_name_' . $variation_id ] );
						$cart_item_meta['ldgr_group_name']                    = $cart_item_meta[ 'ldgr_group_name_' . $variation_id ];
					} elseif ( array_key_exists( 'variation_id', $_GET ) ) {
						$cart_item_meta[ 'ldgr_group_name_' . intval( $_GET['variation_id'] ) ] = sanitize_text_field( $_POST[ 'ldgr_group_name_' . intval( $_GET['variation_id'] ) ] );
					}
				}
			}
			if ( isset( $_POST['wdm_enroll_me'] ) ) {
				$cart_item_meta['wdm_enroll_me'] = 'on';
			}

			return $cart_item_meta;
		}

		/**
		 * Display group registration options on product single page.
		 */
		public function display_woo_group_registration_options() {
			global $post;

			$product_id = $post->ID;
			$value      = get_post_meta( $product_id, '_is_group_purchase_active', true );
			if ( $value == '' ) {
				return;
			}

			$product      = wc_get_product( $product_id );
			$product_type = ldgr_get_woo_product_type( $product_id );

			$variation_ids = [];
			if ( 'variable' === $product_type ) {
				$variation_ids = ldgr_get_product_variation_ids( $product_id );
			}

			$enable_package = ldgr_check_package_enabled( $product_id );

			$value_show = get_post_meta( $product_id, '_is_checkbox_show_front_end', true );
			if ( 'on' !== $value_show ) {
				$default_option = 'group';
			} else {
				$default_option = get_post_meta( $product_id, '_ldgr_front_default_option', true );
			}
			$ldgr_dynamic_unlimited_price         = get_post_meta( $product_id, 'ldgr_dynamic_unlimited_price', true );
			$ldgr_unlimited_members_dynamic_price = get_post_meta( $product_id, 'ldgr_unlimited_members_dynamic_price', true );
			wp_enqueue_script(
				'wdm_single_product_gr_js',
				plugins_url(
					'js/wdm_single_product_gr.js',
					__DIR__
				),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION,
				true
			);
			wp_enqueue_script(
				'wdm_single_product_functions_js',
				plugins_url(
					'js/wdm_single_product_functions.js',
					__DIR__
				),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION,
				true
			);
			wp_enqueue_style(
				'wdm_single_product_gr_css',
				plugins_url(
					'css/wdm_single_product_gr.css',
					__DIR__
				)
			);
			$default_script = '';

			if ( 'on' == $value_show && ! $enable_package ) {
				$default_script = 'front';
			} elseif ( $enable_package ) {
				$default_script = 'package';
			}

			$cal_enroll = false;
			if ( is_user_logged_in() ) {
				$cal_enroll = true;
			}

			$autofill = get_option( 'ldgr_autofill_group_name' );

			wp_localize_script(
				'wdm_single_product_gr_js',
				'wdm_gr_data',
				[
					'enroll_option_nonce'                  => wp_create_nonce( 'learndash_seats_plus_show_enroll_option' ),
					'default_script'                       => $default_script,
					'ajax_url'                             => admin_url( 'admin-ajax.php' ),
					'ajax_loader'                          => plugins_url( 'media/ajax-loader.gif', __DIR__ ),
					'cal_enroll'                           => $cal_enroll,
					'default_option'                       => $default_option,
					'price'                                => $product->get_price(),
					'autofill'                             => $autofill,
					'ldgr_dynamic_unlimited_price'         => $ldgr_dynamic_unlimited_price,
					'ldgr_unlimited_members_dynamic_price' => $ldgr_unlimited_members_dynamic_price,
					'ldgr_unlimited_text'                  => __( 'Unlimited', 'wdm_ld_group' ),
					'product_type'                         => $product_type,
					'variation_ids'                        => $variation_ids,
					'display_product_courses'              => get_option( 'ldgr_display_product_courses' ),
					'bulk_discount_data'                   => $this->get_bulk_discount_for_product(),
				]
			);

			wp_localize_script(
				'wdm_single_product_functions_js',
				'wdm_functions_data',
				[
					'decimal_separator'  => wc_get_price_decimal_separator(),
					'thousand_separator' => wc_get_price_thousand_separator(),
					'decimals'           => wc_get_price_decimals(),
					'price_format'       => get_woocommerce_price_format(),
					'currency_symbol'    => get_woocommerce_currency_symbol(),
					'price_on_load'      => $product->get_price(),
				]
			);

			if ( 'on' == $value_show && ! $enable_package ) {
				?>
				<div class="wdm_group_registration">
					<?php // cspell:disable-next-line . ?>
					<input type="radio" name="wdm_ld_group_active" value="" id="wdm_gr_signle" <?php echo ( 'individual' == $default_option ) ? 'checked' : ''; ?>>
					<?php // cspell:disable-next-line . ?>
					<label for="wdm_gr_signle"> <?php echo esc_html( apply_filters( 'wdm_gr_single_label', __( 'Individual', 'wdm_ld_group' ) ) ); ?></label>
					<input type="radio" name="wdm_ld_group_active" value="on" id="wdm_gr_group" <?php echo ( 'individual' != $default_option || $enable_package ) ? 'checked' : ''; ?>>
					<label for="wdm_gr_group"> <?php echo esc_html( apply_filters( 'wdm_gr_group_label', __( 'Group', 'wdm_ld_group' ) ) ); ?></label>
				</div>
				<?php
			}
			$show_enroll_me = false;
			$paid_course    = get_post_meta( $product_id, '_is_ldgr_paid_course', true );

			if ( empty( $paid_course ) || 'off' == $paid_course ) {
				$show_enroll_me = false;
			} else {
				$show_enroll_me = true;
			}

			if ( $show_enroll_me && ! ldgr_is_user_in_group( $product_id ) ) {
				?>
				<div class="wdm-enroll-me-div">
					<label>
						<input type="checkbox" name="wdm_enroll_me">
						<!-- <label for="wdm_enroll_me"> -->
						<?php echo esc_html( apply_filters( 'wdm_enroll_me_label', __( 'Enroll Me', 'wdm_ld_group' ) ) ); ?>
					</label>
					<img id="wdm_enroll_help_btn" src="<?php echo esc_url( plugins_url( 'media/help.png', __DIR__ ) ); ?>"><br>
					<span class="wdm_enroll_me_help_text" style="display: none;color: #808080;font-style: italic;font-size:small;font-weight:normal;">
						<?php echo esc_html( apply_filters( 'wdm_enroll_me_help_text', __( 'This will add Group Leader as Group Member & will charge for it.', 'wdm_ld_group' ) ) ); ?>
					</span>
				</div>
				<?php
			}
		}

		/**
		 * Check whether order is a renewal order
		 *
		 * @param int $order_id     ID of the order.
		 *
		 * @return bool             True if renewal, false otherwise.
		 */
		public function woo_is_renewal_order( $order_id ) {
			if ( function_exists( 'wcs_order_contains_renewal' ) ) {
				if ( \wcs_order_contains_renewal( $order_id ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Creating group on Woocommerce order completion
		 *
		 * @param int $order_id     ID of the completed order.
		 */
		public function handle_group_creation_on_order_completion( $order_id ) {
			if ( $this->woo_is_renewal_order( $order_id ) ) {
				return;
			}

			$order      = new \WC_Order( $order_id );
			$product_id = null;
			$group_data = [];
			$items      = $order->get_items();

			$group_creation_done = get_post_meta( $order_id, 'wdm_successful_group_creation', true );

			/**
			 * Toggle whether to create group for the given order on order completion.
			 *
			 * @since 4.2.3
			 *
			 * @param string $group_creation_done   The string 'done' if group not to be created for the given order.
			 * @param int    $order_id              ID of the order.
			 */
			$group_creation_done = apply_filters( 'ldgr_filter_toggle_group_creation_for_order', $group_creation_done, $order_id );

			if ( 'done' == $group_creation_done ) {
				return;
			}

			if ( WC_VERSION < '3.0.0' ) {
				foreach ( $items as $item ) {
					$product_id         = $item['product_id'];
					$quantity           = apply_filters( 'wdm_modify_total_number_of_registrations', $item['qty'], $product_id, $order_id );
					$product_type       = ldgr_get_woo_product_type( $product_id );
					$group_registration = isset( $item[ __( 'Group Registration', 'wdm_ld_group' ) ] ) ? $item[ __( 'Group Registration', 'wdm_ld_group' ) ] : '';

					if ( empty( $group_registration ) ) {
						// Get hidden meta to be used for detecting group order
						$group_registration = isset( $item[ $this->ldgr_order_item_key ] );
						$group_registration = empty( $group_registration ) ? '' : $group_registration;
					}

					// check whether group leader paid for course.
					$add_group_leader = isset( $item['_add_group_leader'] ) ? true : false;

					// $courses = maybe_unserialize(get_post_meta($product_id, '_related_course', true));
					$courses = '';
					$uid     = $order->get_user_id();
					if ( $product_type == 'subscription' || $product_type == 'variable-subscription' ) {
						if ( isset( $item['variation_id'] ) && $item['variation_id'] != '' && ! empty( $item['variation_id'] ) ) {
							$product_id = $item['variation_id'];
						}
						// check if the order is resubscription order.
						if ( wcs_order_contains_resubscribe( $order ) ) { // order is a subscribe order
							// get the subscription id.
							$subscription_ids = ldgr_get_order_subscription_ids( $order, $product_id, $order_id );
							// replace old subscription id with new subscription id
							$old_subscription_id = get_post_meta( $order_id, '_subscription_resubscribe', true );

							// update the entry in the post meta and user meta.
							$subscription_id = $subscription_ids[0]; // since resubscribe will only have single subscription always.
							global $wpdb;
							$sql      = 'SELECT post_id from ' . $wpdb->prefix . "postmeta WHERE meta_key LIKE 'wdm_group_subscription_%' and meta_value LIKE {$old_subscription_id}";
							$group_id = $wpdb->get_var( $sql );

							update_post_meta( $group_id, 'wdm_group_subscription_' . $group_id, $subscription_id );

							// get the group out of draft state
							$tot_hld_subscription = get_user_meta( $uid, '_wdm_total_hold_subscriptions' );
							if ( ( $key_to_pop = array_search( $old_subscription_id, $tot_hld_subscription ) ) !== false ) {
								unset( $tot_hld_subscription[ $key_to_pop ] );
							}
							update_user_meta( $uid, '_wdm_total_hold_subscriptions', $tot_hld_subscription );
							$post = [
								'ID'          => $group_id,
								'post_status' => 'publish',
							];
							\wp_update_post( $post );
							return;
						}
					}
					if ( $product_type == 'variable-subscription' || $product_type == 'variable' ) {
						$variation_id = $item['variation_id'];
						if ( ! empty( $variation_id ) ) {
							$courses = maybe_unserialize( get_post_meta( $variation_id, '_related_course', true ) );
						}
						$product_id = $variation_id;
					} else {
						$courses = maybe_unserialize( get_post_meta( $product_id, '_related_course', true ) );
					}

					$courses = empty( $courses ) ? [] : $courses;
					/**
					 * Filter to modify courses array.
					 *
					 * @since 4.3.0
					 *
					 * @param array $courses    Array of courses from product meta.
					 * @param array $item       Order item object
					 */
					$courses = apply_filters( 'ldgr_order_courses_data', $courses, $item );

					if ( array_sum( $courses ) && '' != $group_registration ) {
						$user1 = new \WP_User( $uid );
						if ( ! user_can( $uid, 'manage_options' ) ) {
							$user1->add_role( 'group_leader' );
							$user1->remove_role( 'customer' );
							$user1->remove_role( 'subscriber' );
						}
						$group_data['leader'] = $uid;
						$group_data['course'] = $courses;
						/**
						 * Filter to modify group data before processing.
						 *
						 * @since 4.3.0
						 *
						 * @param array $group_data The group data.
						 * @param array $item       Order item object
						 */
						$group_data = apply_filters( 'ldgr_order_group_data', $group_data, $item );
						$this->create_learndash_group( $group_data, $order, $item, $order_id, $quantity, $product_id, $product_type, $add_group_leader );
						update_post_meta( $order_id, 'wdm_successful_group_creation', 'done' );
					} elseif ( ! empty( $courses ) ) {
						foreach ( $courses as $c_id ) {
							ld_update_course_access( $uid, $c_id );
						}
					}
				}
			} else {
				foreach ( $items as $key_item_id => $item ) {
					$key_item_id = $key_item_id;

					$default_quantity = $item['qty'];
					$product_id       = $item['product_id'];
					$product_type     = ldgr_get_woo_product_type( $product_id );
					$courses          = '';
					$uid              = $order->get_user_id();
					if ( $product_type == 'subscription' || $product_type == 'variable-subscription' ) {
						if ( isset( $item['variation_id'] ) && $item['variation_id'] != '' && ! empty( $item['variation_id'] ) ) {
							$product_id = $item['variation_id'];
						}
						// check if the order is resubscription order
						if ( wcs_order_contains_resubscribe( $order ) ) { // order is a subscribe order
							// get the subscription id
							$subscription_ids = ldgr_get_order_subscription_ids( $order, $product_id, $order_id );
							// replace old subscription id with new subscription id
							$old_subscription_id = get_post_meta( $order_id, '_subscription_resubscribe', true );

							// update the entry in the post meta and user meta
							$subscription_id = $subscription_ids[0]; // since resubscribe will only have single subscription always
							global $wpdb;
							$sql      = 'SELECT post_id from ' . $wpdb->prefix . "postmeta WHERE meta_key LIKE 'wdm_group_subscription_%' and meta_value LIKE {$old_subscription_id}";
							$group_id = $wpdb->get_var( $sql );

							update_post_meta( $group_id, 'wdm_group_subscription_' . $group_id, $subscription_id );

							// get the group out of draft state
							$tot_hld_subscription = get_user_meta( $uid, '_wdm_total_hold_subscriptions' );
							if ( ( $key_to_pop = array_search( $old_subscription_id, $tot_hld_subscription ) ) !== false ) {
								unset( $tot_hld_subscription[ $key_to_pop ] );
							}
							update_user_meta( $uid, '_wdm_total_hold_subscriptions', $tot_hld_subscription );
							$post = [
								'ID'          => $group_id,
								'post_status' => 'publish',
							];
							\wp_update_post( $post );
							return;
						}
					}
					if ( $product_type == 'variable-subscription' || $product_type == 'variable' ) {
						$variation_id = $item['variation_id'];

						// check if enabled for package
						$enable_package = ldgr_check_package_enabled( $product_id );
						if ( $enable_package ) {
							$package_qty      = get_post_meta( $variation_id, 'wdm_gr_package_seat_' . $variation_id, true );
							$default_quantity = ! empty( $package_qty ) ? ( $package_qty * $default_quantity ) : $default_quantity;
						}

						if ( ! empty( $variation_id ) ) {
							$courses    = maybe_unserialize( get_post_meta( $variation_id, '_related_course', true ) );
							$product_id = $variation_id;
						}
					} else {
						$courses = maybe_unserialize( get_post_meta( $product_id, '_related_course', true ) );
					}

					$courses = empty( $courses ) ? [] : $courses;
					/**
					 * Filter to modify courses array.
					 *
					 * @since 4.3.0
					 *
					 * @param int $courses      Array of courses from product meta.
					 * @param array $item       Order item object
					 */
					$courses = apply_filters( 'ldgr_order_courses_data', $courses, $item );

					$quantity = apply_filters( 'wdm_modify_total_number_of_registrations', $default_quantity, $product_id, $order_id );

					$group_registration = isset( $item[ __( 'Group Registration', 'wdm_ld_group' ) ] ) ? $item[ __( 'Group Registration', 'wdm_ld_group' ) ] : '';

					if ( empty( $group_registration ) ) {
						// Get hidden meta to be used for detecting group order
						$group_registration = isset( $item[ $this->ldgr_order_item_key ] );
						$group_registration = empty( $group_registration ) ? '' : $group_registration;
					}

					// check whether group leader paid for course
					$add_group_leader = isset( $item['_add_group_leader'] ) ? true : false;

					if ( array_sum( $courses ) && $group_registration != '' ) {
						$user1 = new \WP_User( $uid );
						if ( ! user_can( $uid, 'manage_options' ) ) {
							$user1->add_role( 'group_leader' );
							$user1->remove_role( 'customer' );
							$user1->remove_role( 'subscriber' );
						}
						$group_data['leader'] = $uid;
						$group_data['course'] = $courses;

						/**
						 * Filter to modify group data before processing.
						 *
						 * @since 4.3.0
						 *
						 * @param array $group_data The group data.
						 * @param array $item       Order item object
						 */
						$group_data = apply_filters( 'ldgr_order_group_data', $group_data, $item );
						$this->create_learndash_group( $group_data, $order, $item, $order_id, $quantity, $product_id, $product_type, $add_group_leader );
						update_post_meta( $order_id, 'wdm_successful_group_creation', 'done' );
					} elseif ( ! empty( $courses ) ) {
						foreach ( $courses as $c_id ) {
							ld_update_course_access( $uid, $c_id );
						}
					}
				}
			}
		}

		/**
		 * Create learndash group process
		 *
		 * @param array  $data           Contains the leader and courses data.
		 * @param object $order          WC Order Object.
		 * @param int    $order_id       Order ID.
		 * @param int    $quantity       Quantity.
		 * @param int    $product_id     Product ID.
		 * @param string $product_type   Type of product.
		 * @param bool   $add_group_leader Whether to add the group leader or not.
		 * @param object $item           Order Item.
		 */
		public function create_learndash_group( $data, $order, $item, $order_id = 1, $quantity = 1, $product_id = 0, $product_type = 'simple', $add_group_leader = false ) {
			global $wpdb;
			$user_data       = get_user_by( 'id', $data['leader'] );
			$username        = $user_data->user_login;
			$subscription_id = '';
			$group_id        = '';

			$group_enroll_course = $data['course'];
			if ( is_numeric( $group_enroll_course ) ) {
				$group_enroll_course = [ $group_enroll_course ];
			}

			if ( 'subscription' == $product_type || 'variable-subscription' == $product_type ) {
				$subscription_ids = ldgr_get_order_subscription_ids( $order, $product_id, $order_id );
				$subscription_id  = $subscription_ids[ count( $subscription_ids ) - 1 ];
				// $sql = "SELECT meta_key FROM {$wpdb->prefix}usermeta WHERE meta_key LIKE 'wdm_group_product_%' AND meta_value LIKE '{$product_id}' AND user_id = ".$data[ 'leader' ];
				$group_id = '';
			} else {
				// Commenting old code
				/*
				$sql         = "SELECT SUBSTRING_INDEX( meta_key,  '_' , -1 ) AS group_id FROM {$wpdb->prefix}usermeta WHERE meta_key LIKE 'wdm_group_product_%' AND meta_value LIKE '{$product_id}' AND user_id = " . $data['leader'];
				$user_groups = $wpdb->get_col( $sql );

				foreach ( $user_groups as $g_id ) {
					if ( get_post_status( $g_id ) == 'publish' ) {
						$group_id = $g_id;
						break;
					}
				} */
				if ( 'create_new' == $data['ldgr_dynamic_option'] ) {
					$group_id = '';
				} elseif ( 'increase_seats' == $data['ldgr_dynamic_option'] ) {
					$group_id = $data['ldgr_dynamic_value'];
				} elseif ( 'add_courses' == $data['ldgr_dynamic_option'] ) {
					$group_id = $data['ldgr_dynamic_value'];
					foreach ( $group_enroll_course as $course_id ) {
						update_post_meta( $course_id, 'learndash_group_enrolled_' . $group_id, time() );
					}
					return;
				}
			}

			// decrease group limit by 1 if group leader is paid for itself.
			$original_quantity = $quantity;
			if ( $add_group_leader ) {
				$quantity = --$quantity;
			}

			// Filter to change the Quantity for the group when product is purchased.
			$quantity = apply_filters( 'wdm_change_group_quantity', $quantity, $order_id, $product_id, $item );

			// Check whether to restrict course access for group leader.
			$ldgr_gl_course_access = get_option( 'ldgr_gl_course_access' );

			if ( empty( $ldgr_gl_course_access ) ) {
				$ldgr_gl_course_access = 'on';
			}

			if ( 'on' !== $ldgr_gl_course_access ) {
				// error_log( 'Since GL course access disabled, removing course access for group leader' );
				foreach ( $group_enroll_course as $course_id ) {
					ld_update_course_access( $data['leader'], $course_id, true );
				}
			}

			if ( '' == $group_id ) {
				$author_id  = 1;
				$title_sql  = "SELECT post_title FROM {$wpdb->prefix}posts WHERE ID = $product_id";
				$temp_title = $wpdb->get_var( $title_sql );
				$title      = apply_filters( 'wdm_group_name', $username . ' - ' . $temp_title, $data['leader'], $product_id, $order_id, $item );
				// Set the post ID so that we know the post was created successfully.
				$post_id = wp_insert_post(
					[
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_author'    => $author_id,
						'post_title'     => $title,
						'post_status'    => 'publish',
						'post_type'      => 'groups',
					]
				);

				foreach ( $group_enroll_course as $course_id ) {
					update_post_meta( $course_id, 'learndash_group_enrolled_' . $post_id, time() );
				}
				learndash_set_groups_administrators( $post_id, [ $data['leader'] ] );
				$old_meta = get_user_meta( $data['leader'], 'ldgr_group_product_' . $product_id, true );
				if ( ! empty( $old_meta ) ) {
					$meta = array_push( $old_meta, $post_id );
					update_user_meta( $data['leader'], 'ldgr_group_product_' . $product_id, $old_meta );
				} else {
					update_user_meta( $data['leader'], 'ldgr_group_product_' . $product_id, [ $post_id ] );
				}
				// update_user_meta( $data['leader'], 'wdm_group_product_' . $post_id, $product_id );
				update_post_meta( $post_id, 'wdm_group_users_limit_' . $post_id, $quantity );
				update_post_meta( $post_id, 'wdm_group_total_users_limit_' . $post_id, $original_quantity );
				if ( ! empty( $subscription_id ) ) {
					update_post_meta( $post_id, 'wdm_group_subscription_' . $post_id, $subscription_id );
				}
				// check if group leader has paid for course.
				if ( $add_group_leader ) {
					ld_update_group_access( $data['leader'], $post_id );
				} else {
					// If group leader is not paid for course then remove course access
					// As we are replacing the product id with variation id.
					if ( 'variable-subscription' == $product_type || 'variable' == $product_type ) {
						$parent      = new \WC_Product_Variation( $product_id );
						$paid_course = get_post_meta( $parent->get_parent_id(), '_is_ldgr_paid_course', true );
					} else {
						$paid_course = get_post_meta( $product_id, '_is_ldgr_paid_course', true );
					}
					if ( empty( $paid_course ) || $paid_course == 'off' ) {
						foreach ( $group_enroll_course as $course_id ) {
							ld_update_course_access( $data['leader'], $course_id );
						}
					} else {
						foreach ( $group_enroll_course as $course_id ) {
							ld_update_course_access( $data['leader'], $course_id, true );
						}
					}
				}
				/**
				 * Fired after a new group is created
				 *
				 * @since 1.0.0
				 *
				 * @param int $post_id
				 * @param int $product_id
				 * @param int $order_id
				 * @param object $order
				 * @param object $item
				 */
				do_action( 'ldgr_action_after_create_group', $post_id, $product_id, $order_id, $order, $item );
				do_action( 'wdm_created_new_group_using_ldgr', $post_id, $product_id, $order_id, $order );
				ldgr_recalculate_group_seats( $post_id );
			} else {
				// Remove course access only if user is not added in group.
				$group_users = learndash_get_groups_user_ids( $group_id, true );

				if ( ! in_array( $data['leader'], $group_users ) ) {
					if ( $add_group_leader ) {
						ld_update_group_access( $data['leader'], $group_id );
					} else {
						// If group leader is not paid for course then remove course access.
						// As we are replacing the product id with variation id.
						if ( 'variable-subscription' == $product_type || 'variable' == $product_type ) {
							$parent      = new \WC_Product_Variation( $product_id );
							$paid_course = get_post_meta( $parent->get_parent_id(), '_is_ldgr_paid_course', true );
						} else {
							$paid_course = get_post_meta( $product_id, '_is_ldgr_paid_course', true );
						}
						if ( empty( $paid_course ) || 'off' == $paid_course ) {
							// $ldgr_paid_course_for_leader = get_option("ldgr_global_gl_paid_course");
							// if($ldgr_paid_course_for_leader=='on')
							// {
							// foreach ($group_enroll_course as $course_id) {
							// ld_update_course_access($data['leader'],$course_id,true);
							// }
							// }
						} else {
							foreach ( $group_enroll_course as $course_id ) {
								ld_update_course_access( $data['leader'], $course_id, true );
							}
						}
					}
				} elseif ( $add_group_leader ) {
					$quantity = $quantity + 1;
				}

				// Update if not unlimited group seat purchase
				$is_unlimited = wc_get_order_item_meta( $item->get_id(), '_ldgr_unlimited_seats', true );
				if ( isset( $is_unlimited ) && 'Yes' != $is_unlimited ) {
					$limit  = get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true );
					$limit += $quantity;
					update_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, $limit );

					// Update quantity in total users meta key
					$old_actual_limit  = get_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, true );
					$old_actual_limit += $quantity;
					update_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, $old_actual_limit );
				}
				do_action( 'ldgr_action_after_update_group', $group_id, $product_id, $order_id, $order, $item );
				ldgr_recalculate_group_seats( $group_id );
			}
			unset( $order );
		}

		/**
		 * Adding group purchase meta box in product post type.
		 */
		public function add_group_purchase_metabox() {
			$screens = [ 'product' ];

			foreach ( $screens as $screen ) {
				add_meta_box(
					'wdm_ld_woo',
					__( 'Group purchase', 'wdm_ld_group' ),
					[ $this, 'create_group_checkbox' ],
					$screen
				);
			}
		}

		/**
		 * Group purchase checkbox in product post type.
		 *
		 * @param obj $post     Post object.
		 */
		public function create_group_checkbox( $post ) {
			wp_enqueue_script(
				'jquery_addel_library',
				plugins_url( 'js/addel.jquery.min.js', __DIR__ ),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_enqueue_script(
				'wdm_related_courses_js',
				plugins_url( 'js/related_courses.js', __DIR__ ),
				[ 'jquery' ],
				LD_GROUP_REGISTRATION_VERSION
			);

			// Check for setup wizard sample product key.
			if ( array_key_exists( 'product_type', $_GET ) && 'group_product' === $_GET['product_type'] ) {
				$sample_product_data = [ 'enable_group_product' => 1 ];

				/**
				 * Filter sample product data to be localized.
				 *
				 * @since 4.2.0
				 *
				 * @param array $sample_product_data    Sample product data.
				 * @param int $product_id               ID of the product.
				 */
				$sample_product_data = apply_filters( 'ldgr_filter_sample_product_data', $sample_product_data, $post->ID );
				wp_localize_script( 'wdm_related_courses_js', 'ldgr_setup_wizard', $sample_product_data );
			}

			wp_enqueue_style(
				'ldgr_product_metabox_css',
				plugins_url( 'css/ldgr-product-metabox.css', __DIR__ ),
				[],
				LD_GROUP_REGISTRATION_VERSION
			);
			wp_nonce_field( 'wdm_ld_woo_value', 'wdm_ld_woo' );

			$value       = get_post_meta( $post->ID, '_is_group_purchase_active', true );
			$value_show  = get_post_meta( $post->ID, '_is_checkbox_show_front_end', true );
			$paid_course = get_post_meta( $post->ID, '_is_ldgr_paid_course', true );

			if ( 'on' !== $value_show ) {
				$default_option = 'group';
			} else {
				$default_option = get_post_meta( $post->ID, '_ldgr_front_default_option', true );
			}

			$group_label   = 'group';
			$courses_label = 'courses';

			if ( function_exists( 'LearnDash_Custom_Label' ) ) {
				$courses_label = learndash_get_custom_label( 'courses' );
				$group_label   = learndash_get_custom_label( 'group' );
			}
			$is_unlimited                 = get_post_meta( $post->ID, 'ldgr_enable_unlimited_members', 1 );
			$is_dynamic                   = get_post_meta( $post->ID, 'ldgr_enable_dynamic_group', 1 );
			$is_dynamic_unlimited_default = get_post_meta( $post->ID, 'ldgr_dynamic_unlimited_price', 1 );
			$dynamic_courses              = get_post_meta( $post->ID, 'ldgr_dynamic_courses', 1 );
			// $unlimited_label = get_post_meta($post->ID, 'ldgr_unlimited_members_option_label', 1);
			$unlimited_label                               = get_option( 'ldgr_unlimited_members_label' );
			$unlimited_price                               = get_post_meta( $post->ID, 'ldgr_unlimited_members_option_price', 1 );
			$unlimited_dynamic_price                       = get_post_meta( $post->ID, 'ldgr_unlimited_members_dynamic_price', 1 );
			$ldgr_type_bulk_discount_for_product_setting   = get_post_meta( $post->ID, 'ldgr_type_bulk_discount_for_product_setting', true );
			$ldgr_enable_bulk_discount_for_product_setting = unserialize( get_post_meta( $post->ID, 'ldgr_enable_bulk_discount_for_product_setting', true ) );
			$ldgr_bulk_discount_min_qty_check              = get_post_meta( $post->ID, 'ldgr_bulk_discount_min_qty_check', 1 );
			$ldgr_bulk_discount_min_qty_value              = get_post_meta( $post->ID, 'ldgr_bulk_discount_min_qty_value', 1 );
			$courses                                       = Ld_Group_Registration_Dynamic_Group::ldgr_get_all_courses();
			$selected_courses                              = Ld_Group_Registration_Dynamic_Group::selected_courses();

			$template = apply_filters( 'ldgr_product_metabox_path', WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-woocommerce-group-registration-metabox.template.php', $post );

			ldgr_get_template(
				$template,
				[
					'post'                             => $post,
					'value'                            => $value,
					'value_show'                       => $value_show,
					'default_option'                   => $default_option,
					'paid_course'                      => $paid_course,
					'is_unlimited'                     => $is_unlimited,
					'unlimited_label'                  => $unlimited_label,
					'unlimited_price'                  => $unlimited_price,
					'is_dynamic'                       => $is_dynamic,
					'courses_label'                    => $courses_label,
					'group_label'                      => $group_label,
					'courses'                          => $courses,
					'selected_courses'                 => $selected_courses,
					'is_dynamic_unlimited_default'     => $is_dynamic_unlimited_default,
					'unlimited_dynamic_price'          => $unlimited_dynamic_price,
					'ldgr_bulk_discount_min_qty_check' => $ldgr_bulk_discount_min_qty_check,
					'ldgr_bulk_discount_min_qty_value' => $ldgr_bulk_discount_min_qty_value,
					'ldgr_type_bulk_discount_for_product_setting' => $ldgr_type_bulk_discount_for_product_setting,
					'ldgr_enable_bulk_discount_for_product_setting' => $ldgr_enable_bulk_discount_for_product_setting,
				]
			);
		}

		/**
		 * Save group purchase settings on product publish
		 *
		 * @param int $post_id  ID of the post.
		 */
		public function save_group_purchase_options( $post_id ) {
			// Check if our nonce is set.
			if ( ! isset( $_POST['wdm_ld_woo'] ) ) {
				return;
			}
			if ( ! wp_verify_nonce( $_POST['wdm_ld_woo'], 'wdm_ld_woo_value' ) ) {
				return;
			}
			if ( ! isset( $_POST['wdm_ld_group_registration'] ) ) {
				delete_post_meta( $post_id, '_is_group_purchase_active', null );
				delete_post_meta( $post_id, '_is_checkbox_show_front_end', null );
				delete_post_meta( $post_id, '_is_ldgr_paid_course', null );
				delete_post_meta( $post_id, '_ldgr_front_default_option', null );
				delete_post_meta( $post_id, 'ldgr_type_bulk_discount_for_product_setting', null );
				delete_post_meta( $post_id, 'ldgr_enable_bulk_discount_for_product_setting', null );
				delete_post_meta( $post_id, 'ldgr_bulk_discount_min_qty_check', null );
				delete_post_meta( $post_id, 'ldgr_bulk_discount_min_qty_value', null );
			} else {
				if ( ! isset( $_POST['wdm_ld_group_registration_show_front_end'] ) ) {
					delete_post_meta( $post_id, '_is_checkbox_show_front_end', null );
				} else {
					update_post_meta(
						$post_id,
						'_is_checkbox_show_front_end',
						$_POST['wdm_ld_group_registration_show_front_end']
					);
				}
				if ( ! isset( $_POST['wdm_ldgr_paid_course'] ) || empty( $_POST['wdm_ldgr_paid_course'] ) ) {
					update_post_meta( $post_id, '_is_ldgr_paid_course', 'off' );
				} else {
					update_post_meta( $post_id, '_is_ldgr_paid_course', $_POST['wdm_ldgr_paid_course'] );
				}

				if ( ! isset( $_POST['wdm_ld_group_active'] ) ) {
					delete_post_meta( $post_id, '_ldgr_front_default_option', null );
				} else {
					update_post_meta( $post_id, '_ldgr_front_default_option', $_POST['wdm_ld_group_active'] );
				}

				update_post_meta( $post_id, '_is_group_purchase_active', $_POST['wdm_ld_group_registration'] );

				if ( ! isset( $_POST['ldgr_type_bulk_discount_for_product_setting'] ) ) {
					delete_post_meta( $post_id, 'ldgr_type_bulk_discount_for_product_setting', null );
				} else {
					update_post_meta( $post_id, 'ldgr_type_bulk_discount_for_product_setting', $_POST['ldgr_type_bulk_discount_for_product_setting'] );
				}

				if ( ! isset( $_POST['ldgr_enable_bulk_discount_for_product_setting'] ) ) {
					delete_post_meta( $post_id, 'ldgr_enable_bulk_discount_for_product_setting', null );
				} else {
					$temp_check_values = $_POST['ldgr_enable_bulk_discount_for_product_setting'];
					if ( 1 === count( $temp_check_values['min_quantity'] ) && '' === $temp_check_values['min_quantity'][0] ) {
						delete_post_meta( $post_id, 'ldgr_enable_bulk_discount_for_product_setting', null );
					} else {
						$ldgr_bulk_discount_product_values = $_POST['ldgr_enable_bulk_discount_for_product_setting'];
						$all_min_quantity                  = array_filter( $ldgr_bulk_discount_product_values['min_quantity'] );
						$all_types                         = array_filter( $ldgr_bulk_discount_product_values['discount_type'] );
						$all_values                        = array_filter( $ldgr_bulk_discount_product_values['discount_value'] );
						$final_array                       = [];
						$quantity_check_array              = [];
						foreach ( $all_min_quantity as $index => $quantity ) {
							if ( ! in_array( $quantity, $quantity_check_array ) && ! empty( $all_types[ $index ] ) && ! empty( $all_values[ $index ] ) ) {
								array_push(
									$final_array,
									[
										'quantity' => $quantity,
										'type'     => $all_types[ $index ],
										'value'    => $all_values[ $index ],
									]
								);
								array_push( $quantity_check_array, $quantity );
							}
						}
						update_post_meta( $post_id, 'ldgr_enable_bulk_discount_for_product_setting', serialize( $final_array ) );
					}
				}

				if ( ! isset( $_POST['ldgr_bulk_discount_min_qty_check'] ) ) {
					delete_post_meta( $post_id, 'ldgr_bulk_discount_min_qty_check', null );
					delete_post_meta( $post_id, 'ldgr_bulk_discount_min_qty_value', null );
				} else {
					update_post_meta( $post_id, 'ldgr_bulk_discount_min_qty_check', $_POST['ldgr_bulk_discount_min_qty_check'] );
					update_post_meta( $post_id, 'ldgr_bulk_discount_min_qty_value', $_POST['ldgr_bulk_discount_min_qty_value'] );
				}
			}       }

		/**
		 * Ajax check and show enroll option
		 */
		public function ajax_show_enroll_option_callback() {
			// Check referer and nonce.
			check_ajax_referer( 'learndash_seats_plus_show_enroll_option', 'enroll_option_nonce' );

			// Clean input.
			$cur_var = filter_input( INPUT_POST, 'cur_var', FILTER_SANITIZE_NUMBER_INT );
			$type    = sanitize_text_field( wp_unslash( $_POST['type'] ?? '' ) );
			if ( ! empty( $cur_var ) || 0 != $cur_var ) {
				$enrolled = ldgr_is_user_in_group( $cur_var, $type );
				echo esc_html( $enrolled );
				die();
			}
			echo false;
			die();
		}

		/**
		 * Add Group details on the product single page
		 *
		 * @since 3.8.2
		 */
		public function woo_add_group_details() {
			global $post, $woocommerce;

			// Check if post or woo cart empty.
			if ( empty( $post ) || empty( $woocommerce ) || empty( $woocommerce->cart ) ) {
				return;
			}

			$cart_items = $woocommerce->cart->get_cart_contents();
			$in_cart    = false;

			$product_id   = $post->ID;
			$product_type = ldgr_get_woo_product_type( $product_id );

			$value = get_post_meta( $product_id, '_is_group_purchase_active', true );

			// Check if group purchase active.
			if ( '' == $value ) {
				return;
			}

			// Get product group name.
			$group_name = '';

			// If variable product.
			$variation_group_names = [];
			if ( 'variable' == $product_type ) {
				$variation_ids = ldgr_get_product_variation_ids( $product_id );
				foreach ( $variation_ids as $variation_id ) {
					$in_cart                                = false;
					$variation_group_names[ $variation_id ] = [
						'value'   => $this->get_group_name( $variation_id, $cart_items, $product_type, $in_cart ),
						'in_cart' => $in_cart,
					];
				}
			} else {
				$group_name = $this->get_group_name( $product_id, $cart_items, $product_type, $in_cart );
			}

			// If product already in cart and without group name then do not allow group naming now.
			if ( $in_cart && empty( $group_name ) && empty( $variation_group_names ) ) {
				return;
			}

			$enable_package = ldgr_check_package_enabled( $product_id );

			$default_option = get_post_meta( $product_id, '_ldgr_front_default_option', true );

			// if ( ! $enable_package ) {
			if ( 'variable' == $product_type ) {
				$this->display_group_name_box( $product_id, $variation_group_names, $default_option, $product_type );
			} else {
				$this->display_group_name_box( $product_id, $group_name, $default_option, $product_type );
			}
			// }
		}

		/**
		 *  Update Group Title for groups created from WC Orders
		 *
		 * @param string $group_title   Group Title to be updated.
		 * @param int    $leader        Group leader user ID.
		 * @param int    $product_id    Product ID.
		 * @param int    $order_id      Order ID.
		 * @param object $item          Order Item.
		 *
		 * @return string group_title   Updated Group Title
		 * @since 3.8.2
		 */
		public function woo_update_group_title( $group_title, $leader, $product_id, $order_id, $item ) {
			// If order item not found or empty, return.
			if ( empty( $item ) ) {
				return $group_title;
			}

			// Check if woo order item.
			if ( ! is_a( $item, 'WC_Order_Item' ) ) {
				return $group_title;
			}

			// Fetch saved group name, if any.
			$group_name = wc_get_order_item_meta( $item->get_id(), __( 'Group Name', 'wdm_ld_group' ), true );

			// Check hidden meta for group name
			if ( empty( $group_name ) ) {
				$group_name = wc_get_order_item_meta( $item->get_id(), $this->ldgr_group_name_item_key, true );
			}
			// If found set the group name.
			if ( ! empty( $group_name ) ) {
				$group_title = $group_name;
			}

			return $group_title;
		}

		/**
		 * Get list of product ids for all the group products purchased by the user
		 *
		 * @param integer $user_id        User ID.
		 *
		 * @return array    $product_ids    List of group product ids purchased by the customer or false.
		 * @since 3.8.2
		 */
		public function get_customer_group_products( $user_id = 0 ) {
			$product_ids = false;
			if ( empty( $user_id ) ) {
				$current_user = wp_get_current_user();
				$user_id      = $current_user->ID;
			}

			if ( 0 == $user_id ) {
				return false;
			}

			// GET USER ORDERS (COMPLETED + PROCESSING).
			$customer_orders = get_posts(
				[
					'numberposts' => -1,
					'meta_key'    => '_customer_user',
					'meta_value'  => $user_id,
					'post_type'   => wc_get_order_types(),
					'post_status' => 'completed',
				]
			);

			// LOOP THROUGH ORDERS AND GET PRODUCT IDS.
			if ( ! $customer_orders ) {
				return false;
			}

			$product_ids = [];
			foreach ( $customer_orders as $customer_order ) {
				$order = wc_get_order( $customer_order->ID );
				$items = $order->get_items();
				foreach ( $items as $item ) {
					$group_registration = false;
					$group_registration = wc_get_order_item_meta( $item->get_id(), __( 'Group Registration', 'wdm_ld_group' ), true );

					// Check for additional meta key
					$group_registration = empty( $group_registration ) ? wc_get_order_item_meta( $item->get_id(), $this->ldgr_order_item_key, 1 ) : $group_registration;

					if ( empty( $group_registration ) ) {
						continue;
					}
					$product_id                           = $item->get_product_id();
					$product_ids[ $customer_order->ID ][] = $product_id;
				}
			}
			// $product_ids = array_unique($product_ids);

			return $product_ids;
		}

		/**
		 * Check if product bought by customer and get order item details for that order
		 *
		 * @param int   $product_id         ID of the product.
		 * @param array $customer_products  List of group products bought by the customer, grouped by order as key.
		 *
		 * @return array    $product_details    Details about product order and order item if found, else false for both values.
		 * @since 3.8.2
		 */
		public function get_existing_product_details( $product_id, $customer_products ) {
			$product_details = [
				'order'  => false,
				'status' => false,
			];

			if ( empty( $product_id ) || empty( $customer_products ) ) {
				return $product_details;
			}

			foreach ( $customer_products as $order_id => $order_products ) {
				if ( in_array( $product_id, $order_products ) ) {
					$item_id = $this->get_order_item_for_product( $order_id, $product_id );
					if ( false !== $item_id ) {
						$product_details = [
							'item'   => $item_id,
							'status' => true,
						];

						return $product_details;
					}
				}
			}

			return $product_details;
		}

		/**
		 * Get the order item ID for a product purchased by the customer
		 *
		 * @param int $order_id     ID of the order to check for order items.
		 * @param int $product_id   ID of the product to check against in each order item.
		 *
		 * @return mixed            ID of the order item if found, false otherwise.
		 * @since 3.8.2
		 */
		public function get_order_item_for_product( $order_id, $product_id ) {
			if ( empty( $order_id ) || empty( $product_id ) ) {
				return false;
			}

			$order = new \WC_Order( $order_id );
			$items = $order->get_items();
			foreach ( $items as $item ) {
				if ( $product_id == $item['product_id'] ) {
					return $item->get_id();
				}
			}

			return false;
		}

		/**
		 * Get the group name for a previously bought ldgr group product
		 *
		 * @param int   $product_id         ID of the woocommerce product.
		 * @param array $customer_products  List of group products bought by the customer, grouped by order as key.
		 *
		 * @return string   $group_name     Existing group name if found, empty otherwise.
		 * @since 3.8.2
		 */
		public function get_existing_group_name( $product_id, $customer_products ) {
			$group_name      = '';
			$product_details = $this->get_existing_product_details( $product_id, $customer_products );
			if ( $product_details['status'] ) {
				$item_id    = $product_details['item'];
				$group_name = wc_get_order_item_meta( $item_id, __( 'Group Name', 'wdm_ld_group' ), true );
				// Check hidden meta for group name.
				if ( empty( $group_name ) ) {
					$group_name = wc_get_order_item_meta( $item_id, $this->ldgr_group_name_item_key, true );
				}
			}

			return $group_name;
		}

		/**
		 * Get Updated Group name
		 *
		 * @param int $product_id     ID of the product to get the group name for.
		 *
		 * @return string                   The group name if found, empty otherwise
		 * @since 3.8.2
		 */
		public function get_updated_group_name( $product_id ) {
			$group_name = '';
			$user_id    = get_current_user_id();

			if ( empty( $user_id ) || empty( $product_id ) ) {
				return $group_name;
			}

			global $wpdb;

			$table = $wpdb->prefix . 'usermeta';

			$sql = "SELECT SUBSTRING_INDEX( meta_key,  '_' , -1 ) as group_id FROM $table WHERE meta_key LIKE 'wdm_group_product_%' AND user_id = $user_id AND meta_value = $product_id";

			$user_groups = $wpdb->get_col( $sql );

			$group_id = 0;
			foreach ( $user_groups as $g_id ) {
				if ( get_post_status( $g_id ) == 'publish' ) {
					$group_id = $g_id;
					break;
				}
			}

			if ( empty( $group_id ) ) {
				return $group_name;
			}

			$group_name = get_the_title( $group_id );

			return $group_name;
		}

		/**
		 * Display group name box
		 *
		 * @param int    $product_id        ID of the product.
		 * @param int    $group_name        ID of the group.
		 * @param string $default_option    Default option to be displayed.
		 * @param string $product_type      Type of the product.
		 */
		public function display_group_name_box( $product_id, $group_name, $default_option, $product_type ) {
			$group_section_classes = 'ldgr_group_name';
			$variation_ids         = [];
			$default_attributes    = [];
			if ( 'variable' === $product_type ) {
				$variation_ids          = ldgr_get_product_variation_ids( $product_id );
				$group_section_classes .= ' ldgr_variations';

				$product_variations = new \WC_Product_Variable( $product_id );
				$default_attributes = $product_variations->get_default_attributes();

				// Check for multiple attributes.
				if ( count( $default_attributes ) > 1 ) {
					// Since currently only one attribute supported, remove the others.
					$attr_count = count( $default_attributes );
					while ( ! empty( $default_attributes ) && 1 < $attr_count ) {
						array_pop( $default_attributes );
						$attr_count = count( $default_attributes );
					}
				}
			}
			$product_courses  = get_post_meta( $product_id, '_related_course', 1 );
			$def_course_image = get_option( 'ldgr_default_course_image' );
			if ( $image = wp_get_attachment_image_src( $def_course_image ) ) {
				$def_course_image = esc_url( $image[0] );
			} else {
				$def_course_image = esc_url( plugins_url( 'assets/images/no_image.png', WDM_LDGR_PLUGIN_FILE ) );
			}

			$product = wc_get_product( $product_id );

			if ( ! $product instanceof WC_Product ) {
				return;
			}

			$price   = $product->get_price();

			$group_label       = __( 'Group', 'wdm_ld_group' );
			$lower_group_label = __( 'Group', 'wdm_ld_group' );
			$courses_label     = __( 'Courses', 'wdm_ld_group' );

			if ( class_exists( 'LearnDash_Custom_Label' ) ) {
				$courses_label     = learndash_get_custom_label( 'courses' );
				$group_label       = learndash_get_custom_label( 'group' );
				$lower_group_label = \LearnDash_Custom_Label::label_to_lower( 'group' );
			}

			// Fetch color settings.
			$footer_color = get_option( 'ldgr_dashboard_footer_color' );
			if ( ! empty( $footer_color ) ) {
				$custom_css = "
				.ldgr-g-name .ldgr-g-val, .ldgr-g-courses .ldgr-g-val, .ldgr-seats .ldgr-g-val {
					color : {$footer_color};
				}

				.ldgr-cal .ldgr-value {
					color : {$footer_color};
				}

				.ldgr-cal .ldgr-discounted-value {
					color : {$footer_color};
				";

				wp_add_inline_style( 'wdm_single_product_gr_css', $custom_css );
			}

			$template = apply_filters(
				'ldgr_group_name_box_template_path',
				WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-group-name-box.template.php',
				$product_id,
				$group_name
			);

			ldgr_get_template(
				$template,
				[
					'price'                  => $price,
					'group_label'            => $group_label,
					'group_section_classes'  => $group_section_classes,
					'default_option'         => $default_option,
					'variation_ids'          => $variation_ids,
					'group_name'             => $group_name,
					'product_id'             => $product_id,
					'default_attributes'     => $default_attributes,
					'courses_label'          => $courses_label,
					'product_courses'        => $product_courses,
					'def_course_image'       => $def_course_image,
					'instance'               => $this,
					'display_product_footer' => get_option( 'ldgr_display_product_footer' ),
				]
			);
		}

		/**
		 * Get group name for the product.
		 *
		 * @param int    $product_id     ID of the product.
		 * @param array  $cart_items     List of items in the cart.
		 * @param string $product_type   Type of the product.
		 * @param bool   $in_cart        Whether the item is in the cart or not.
		 *
		 * @return string $group_name    Group name for the product.
		 */
		public function get_group_name( $product_id, $cart_items, $product_type, &$in_cart ) {
			$group_name = '';

			// No need to fetch existing/updated group name for subscriptions.
			if ( 'subscription' !== $product_type && 'variable-subscription' !== $product_type ) {
				// 1. Check for updated group name
				$group_name = $this->get_updated_group_name( $product_id );

				// 2. Check if group name set
				// Get all customer products
				$customer_products = $this->get_customer_group_products();

				if ( empty( $group_name ) && ! empty( $customer_products ) ) {
					$group_name = $this->get_existing_group_name( $product_id, $customer_products );
				}
			}

			// 3. Check if group name set in cart
			if ( empty( $group_name ) && ! empty( $cart_items ) ) {
				foreach ( $cart_items as $cart_item ) {
					if ( array_key_exists( 'variation_id', $cart_item ) ) {
						// Check if current variation product and the one in cart are same.
						if ( $product_id != $cart_item['variation_id'] ) {
							continue;
						}
					} elseif ( $product_id !== $cart_item['product_id'] ) {
						// Check if current product and the one in cart are same.
						continue;
					}

					// Check if group registration enabled on the product in cart.
					if ( ! array_key_exists( 'wdm_ld_group_active', $cart_item ) || 'on' !== $cart_item['wdm_ld_group_active'] ) {
						continue;
					}

					$in_cart = true;
					// Check if group name assigned to the product in cart.
					if ( array_key_exists( 'ldgr_group_name', $cart_item ) && ! empty( $cart_item['ldgr_group_name'] && empty( $group_name ) ) ) {
						$group_name = stripslashes( $cart_item['ldgr_group_name'] );
						break;
					}

					// Check if group name assigned to the variation in cart.
					if ( array_key_exists( 'variation_id', $cart_item ) ) {
						if ( array_key_exists( 'ldgr_group_name_' . $cart_item['variation_id'], $cart_item ) && ! empty( $cart_item[ 'ldgr_group_name_' . $cart_item['variation_id'] ] && empty( $group_name ) ) ) {
							$group_name = stripslashes( $cart_item[ 'ldgr_group_name_' . $cart_item['variation_id'] ] );
							break;
						}
					}
				}
			}
			return $group_name;
		}

		/**
		 * Check for default selected attribute for a variable product and add a class
		 *
		 * @param int   $variation_id         ID of the variation.
		 * @param array $default_attributes   Default attribute and its value for the product.
		 *
		 * @return string                     default variation class to be added if found, else empty string.
		 */
		public function check_for_default_variation_class( $variation_id, $default_attributes ) {
			$variation_classes = '';
			if ( empty( $variation_id ) ) {
				return $variation_classes;
			}

			$variation_details = new \WC_Product_Variation( $variation_id );

			$variation_attribute = $variation_details->get_variation_attributes();

			$default_key   = key( $default_attributes );
			$variation_key = key( $variation_attribute );

			if ( 'attribute_' . $default_key == $variation_key ) {
				$default_value   = array_shift( $default_attributes );
				$variation_value = array_shift( $variation_attribute );
				if ( $default_value == $variation_value ) {
					$variation_classes = 'ldgr_default_variation';
				}
			}

			return $variation_classes;
		}

		/**
		 * Hide group registration order item meta on admin side
		 *
		 * @param array $hidden_meta
		 * @return array
		 */
		public function hide_admin_group_reg_order_meta( $hidden_meta ) {
			if ( ! in_array( $this->ldgr_order_item_key, $hidden_meta ) ) {
				$hidden_meta[] = $this->ldgr_order_item_key;
			}

			if ( ! in_array( $this->ldgr_group_name_item_key, $hidden_meta ) ) {
				$hidden_meta[] = $this->ldgr_group_name_item_key;
			}

			return $hidden_meta;
		}

		/**
		 * Update Cart item price on group product purchase
		 *
		 * @param number $product_subtotal     Product subtotal in cart.
		 * @param object $cart_item           Cart item.
		 * @param number $quantity             Quantity of product.
		 */
		public function ldgr_woocommerce_cart_item_subtotal( $product_subtotal, $cart_item, $quantity ) {
			$ldgr_bulk_discounts = get_option( 'ldgr_bulk_discounts' );
			if ( 'on' === $ldgr_bulk_discounts ) {
				$product_id         = $cart_item['product_id'];
				$quantity           = $cart_item['quantity'];
				$line_subtotal      = $cart_item['line_subtotal'];
				$final_price        = $line_subtotal;
				$bulk_discount_type = get_post_meta( $product_id, 'ldgr_type_bulk_discount_for_product_setting', true );
				if ( 'Global' === $bulk_discount_type ) {
					$ldgr_bulk_discount_global_values = unserialize( get_option( 'ldgr_bulk_discount_global_values' ) );
					foreach ( $ldgr_bulk_discount_global_values as $global_values ) {
						if ( $quantity >= (int) $global_values['quantity'] ) {
							$discount_price = $line_subtotal * ( ( (float) $global_values['value'] ) / 100 );
							$final_price    = $line_subtotal - $discount_price;
						}
					}
				} elseif ( 'Product' === $bulk_discount_type ) {
					$ldgr_enable_bulk_discount_for_product_setting = unserialize( get_post_meta( $product_id, 'ldgr_enable_bulk_discount_for_product_setting', true ) );
					foreach ( $ldgr_enable_bulk_discount_for_product_setting as $product_values ) {
						if ( $quantity >= (int) $product_values['quantity'] ) {
							if ( 'Fixed' === $product_values['type'] ) {
								$discount_price = (float) $product_values['value'];
							} else {
								$discount_price = $line_subtotal * ( ( (float) $product_values['value'] ) / 100 );
							}
							$final_price = $line_subtotal - $discount_price;
						}
					}
				}
				$new_subtotal = wc_price( $final_price );
				return $new_subtotal;
			} else {
				return $product_subtotal;
			}
		}

		/**
		 * Add discount fees
		 *
		 * @param object $cart Cart Object.
		 */
		public function ldgr_woocommerce_cart_calculate_fees( $cart ) {
			$ldgr_bulk_discounts = get_option( 'ldgr_bulk_discounts' );
			if ( 'on' === $ldgr_bulk_discounts ) {
				$total_discount = 0;
				foreach ( $cart->get_cart() as $key => $value ) {
					$line_subtotal       = $value['line_subtotal'];
					$quantity            = $value['quantity'];
					$bulk_discount_type  = get_post_meta( $value['product_id'], 'ldgr_type_bulk_discount_for_product_setting', true );
					$item_total_discount = 0;
					if ( 'Global' === $bulk_discount_type ) {
						$ldgr_bulk_discount_global_values = unserialize( get_option( 'ldgr_bulk_discount_global_values' ) );
						if ( ! empty( $ldgr_bulk_discount_global_values ) ) {
							foreach ( $ldgr_bulk_discount_global_values as $global_values ) {
								if ( $quantity >= (int) $global_values['quantity'] ) {
									$item_total_discount = $line_subtotal * ( ( (float) $global_values['value'] ) / 100 );
								}
							}
						}
					} elseif ( 'Product' === $bulk_discount_type ) {
						$ldgr_enable_bulk_discount_for_product_setting = unserialize( get_post_meta( $value['product_id'], 'ldgr_enable_bulk_discount_for_product_setting', true ) );
						if ( ! empty( $ldgr_enable_bulk_discount_for_product_setting ) ) {
							foreach ( $ldgr_enable_bulk_discount_for_product_setting as $product_values ) {
								if ( $quantity >= (int) $product_values['quantity'] ) {
									if ( 'Fixed' === $product_values['type'] ) {
										$item_total_discount = (float) $product_values['value'];
									} else {
										$item_total_discount = $line_subtotal * ( ( (float) $product_values['value'] ) / 100 );
									}
								}
							}
						}
					}
					$total_discount = $total_discount + $item_total_discount;
				}
				if ( $total_discount > 0 ) {
					/**
					 * Filter group discount label.
					 *
					 * @since 4.2.0
					 *
					 * @param string $discount_label  Discount label.
					 * @param float  $total_discount  Total discount amount.
					 */
					$discount_label = apply_filters(
						'ldgr_filter_discount_label',
						__( 'Group Discount', 'wdm_ld_group' ),
						$total_discount
					);
					$cart->add_fee( $discount_label, -$total_discount );
				}
			}
		}

		/**
		 * Display the goto dashboard button on thankyou page.
		 *
		 * @since 4.2.0
		 *
		 * @param int $order_id     ID of the woocommerce order.
		 */
		public function display_goto_dashboard_button( $order_id ) {
			// Get order details.
			$order = new \WC_Order( $order_id );
			$items = $order->get_items();

			// Check if any order item is a group purchase.
			foreach ( $items as $item ) {
				$group_registration = false;
				// Check for additional meta key.
				$group_registration = wc_get_order_item_meta( $item->get_id(), $this->ldgr_order_item_key, 1 );

				if ( $group_registration ) {
					$groups_dashboard_page = ldgr_get_groups_dashboard_page();
					echo '<style>.ldgr-goto-dashboard-div {
						display: flex;
						justify-content: center;
						margin-top: 50px;
					}

					.ldgr-goto-dashboard-link{
						padding: 10px 20px;
						border-radius: 3px;
						background-color: #0d7ee7;
						color: #fff;
					}</style>';

					$allowed_html = [
						'div' => [
							'class' => [],
						],
						'a'   => [
							'href'  => [],
							'class' => [],
						],
					];
					echo wp_kses(
						'<div class="ldgr-goto-dashboard-div"><a href="' . esc_url( get_the_permalink( $groups_dashboard_page ) ) . '" class="ldgr-goto-dashboard-link">' . sprintf( /* translators: Group Label. */esc_html__( 'Proceed to %s', 'wdm_ld_group' ), \LearnDash_Custom_Label::get_label( 'group' ) ) . '</a></div>',
						$allowed_html
					);
					return;
				}
			}
		}

		/**
		 * Enforce minimum quantity for group products.
		 *
		 * @param int    $min          Minimum quantity
		 * @param object $product   WC Product object.
		 */
		public function enforce_min_quantity_for_products( $min, $product ) {
			// Check if group purchase active.
			$group_purchase_active = get_post_meta( $product->get_id(), '_is_group_purchase_active', 1 );
			$default_option        = get_post_meta( $product->get_id(), '_ldgr_front_default_option', true );
			if ( 'on' === $group_purchase_active && 'individual' !== $default_option ) {
				// Check if min qty enforced
				$min_qty_check = get_post_meta( $product->get_id(), 'ldgr_bulk_discount_min_qty_check', 1 );

				if ( 'on' === $min_qty_check ) {
					$min_qty = get_post_meta( $product->get_id(), 'ldgr_bulk_discount_min_qty_value', 1 );
					if ( ! is_nan( floatval( $min_qty ) ) ) {
						$min = $min_qty;
					}
				}
			}

			/**
			 * Filter minimum product quantity to be set for a group product.
			 *
			 * @since 4.2.2
			 *
			 * @param int $min          Minimum quantity for the product.
			 * @param object $product   WC Product object.
			 */
			return apply_filters( 'ldgr_filter_enforce_min_quantity', $min, $product );
		}

		/**
		 * Get localized data for checking bulk discount dynamically
		 *
		 * @since 4.3.8
		 */
		public function get_bulk_discount_for_product() {
			global $product;

			// Check if valid product.
			if ( empty( $product ) || ! $product instanceof \WC_Product ) {
				return false;
			}

			$discount_data        = [];
			$product_id           = $product->get_id();
			$bulk_discount_status = get_post_meta( $product_id, 'ldgr_type_bulk_discount_for_product_setting', true );
			if ( 'Disable' !== $bulk_discount_status ) {
				if ( 'Product' === $bulk_discount_status ) {
					$discount_data = unserialize( get_post_meta( $product_id, 'ldgr_enable_bulk_discount_for_product_setting', true ) );
				} else {
					$ldgr_bulk_discounts = get_option( 'ldgr_bulk_discounts' );
					if ( 'on' === $ldgr_bulk_discounts ) {
						$discount_data = unserialize( get_option( 'ldgr_bulk_discount_global_values' ) );
					}
				}
			}

			/**
			 * Filter the discounts data for the product.
			 *
			 * @since 4.3.8
			 *
			 * @param array $discount_data  Array of bulk discount data for the product.
			 * @param object $product       WC Product Object.
			 */
			return apply_filters( 'ldgr_filter_get_bulk_discount_for_product', $discount_data, $product );
		}

		/**
		 * Update product quantity when adding to cart.
		 *
		 * @param int $quantity   The quantity being added to cart.
		 * @param int $product_id The ID of the product being added to cart.
		 *
		 * @return int The updated quantity.
		 */
		public function update_product_quantity( $quantity, $product_id ) {
			$default_option = get_post_meta( $product_id, '_ldgr_front_default_option', true );

			if ( isset( $_GET['add-to-cart'] ) && ( ( isset( $default_option ) && 'group' === $default_option ) || isset( $_GET['ldgr_group_name'] ) || isset( $_GET['ldgr_group_id'] ) ) ) {
				$_POST['wdm_ld_group_active'] = 'on';
			}

			// Check if product is an LDGR product.
			if ( 'on' === get_post_meta( $product_id, '_is_group_purchase_active', true ) && isset( $_POST['wdm_ld_group_active'] ) && 'on' === $_POST['wdm_ld_group_active'] && ! isset( $_GET['ldgr_group_id'] ) ) {
				// Check if URL parameter is present and sanitize it.
				if ( isset( $_POST['wdm_ld_group_active'] ) && 'on' === $_POST['wdm_ld_group_active'] ) {
					// Get the minimum quantity and make sure it's an integer.
					$min_qty = (int) get_post_meta(
						$product_id,
						'ldgr_bulk_discount_min_qty_value',
						true
					);
					if ( $quantity < $min_qty ) {
						$quantity = $min_qty;
					}
				}
			}
			if ( ! isset( $_GET['ldgr_group_id'] ) ) {
				add_filter( 'woocommerce_add_to_cart_qty_html', [ $this, 'update_cart_quantity' ], 10, 2 );
			}
			return $quantity;
		}

		/**
		 * Update cart quantity.
		 *
		 * @param int $count      Current quantity of product in cart.
		 * @param int $product_id Product ID.
		 *
		 * @return string Updated quantity string.
		 */
		public function update_cart_quantity( $count, $product_id ) {
			// Get minimum quantity for bulk discount.
			$min_qty = (int) get_post_meta(
				$product_id,
				'ldgr_bulk_discount_min_qty_value',
				true
			);
			// Get default option from post meta.
			$default_option = get_post_meta( $product_id, '_ldgr_front_default_option', true );

			// Check if count is less than minimum quantity and certain conditions are met.
			if (
				$count < $min_qty &&
				(
					isset( $_GET['add-to-cart'] ) && (
						isset( $default_option ) && 'group' === $default_option ||
						isset( $_GET['ldgr_group_name'] ) ||
						isset( $_GET['variation_id'] )
					)
				)
			) {
				// Set count to minimum quantity.
				$count = $min_qty;
			}

			// Return updated quantity string.
			return ( $count > 1 ? absint( $count ) . ' &times; ' : '' );
		}

		/**
		 * Checks if a given group ID is related to a product.
		 *
		 * @return int|false The product ID if related, false otherwise.
		 */
		public function ldgr_check_related_group_product() {
			// Check if the group ID is set in the URL parameter.
			if ( ! isset( $_GET['ldgr_group_id'] ) ) {
				return false;
			}

			// Get the group ID from the URL parameter and sanitize it.
			$group_id = absint( $_GET['ldgr_group_id'] );

			// Check if the group ID is valid.
			if ( empty( $group_id ) ) {
				return false;
			}

			// Get the order IDs associated with the group ID.
			$order_ids = get_post_meta( $group_id, 'wdm_group_reg_order_id_' . $group_id );

			// Initialize the related product IDs array.
			$related_product_ids = [];

			// Loop through each order ID and get the related product IDs.
			foreach ( $order_ids as $order_id ) {
				$order = wc_get_order( $order_id );
				foreach ( $order->get_items() as $item_id => $item ) {
					// Get the group name and related product ID.
					$group_name            = wc_get_order_item_meta( $item_id, '_ldgr_group_name', true );
					$related_product_ids[] = wc_get_order_item_meta( $item_id, '_product_id', true );
				}
			}

			// Check if there are any related product IDs.
			if ( empty( $related_product_ids ) ) {
				return false;
			}

			global $wpdb;

			// Loop through each related product ID and check if it is associated with the group ID.
			foreach ( $related_product_ids as $related_product_id ) {
				$meta_key = $wpdb->prepare( 'ldgr_group_product_%d', $related_product_id );
				$results  = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT meta_value FROM $wpdb->usermeta WHERE meta_key = %s",
						$meta_key
					)
				);

				// Loop through each result and check if it contains the group ID.
				foreach ( $results as $result ) {
					$meta_value = maybe_unserialize( $result );
					if ( is_array( $meta_value ) && in_array( $group_id, $meta_value, true ) ) {
						return Cast::to_int( $related_product_id );
					}
				}
			}

			// If no related product is found with the group ID, return the first related product ID.
			return Cast::to_int( $related_product_ids[0] );
		}
	}
}
