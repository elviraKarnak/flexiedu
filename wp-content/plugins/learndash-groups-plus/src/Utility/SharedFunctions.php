<?php
/**
 * Shared Functions used throughout the plugin.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore USERID ldquiz
 */

namespace LearnDash\Groups_Plus\Utility;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use LearnDash\Core\Utilities\Cast;

/**
 * Class SharedFunctions
 */
class SharedFunctions {
	/**
	 * @var string
	 */
	static $group_name_field = '_group_name';
	/**
	 * @var string
	 */
	static $seats_course_meta_field = '_groups_plus_seats_courses';
	/**
	 * @var string
	 */
	static $linked_group_id_meta = '_linked_group_id';

	/**
	 * @var string
	 */
	static $individual_seat_meta_field = '_is_individual_seat';

	/**
	 * @var string
	 */
	static $groups_plus_organization_courses_meta_field = '_groups_plus_organization_courses';

	/**
	 * @var string
	 */
	static $groups_plus_organization_groups_meta_field = '_groups_plus_organization_groups';

	/**
	 * @var string
	 */
	static $is_organization_purchase_enable = '_is_organization_purchase_enable';

	/**
	 * Meta field Courses for a Team Product are stored in.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public static $groups_plus_team_courses_meta_field = '_groups_plus_team_courses';

	/**
	 * Meta field used by Subscription Products to flag it as selling Teams.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public static $is_team_purchase_enable = '_groups_plus_is_team_purchase_enable';

	/**
	 * Meta field used by Groups to flag them as a Non-Organization Team.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public static $is_non_organization_team = '_groups_plus_is_non_organization_team';

	/**
	 * @var string
	 */
	static $is_organization_group_purchase_enable = '_is_organization_group_purchase_enable';

	/**
	 * @var string
	 */
	static $is_individual_seat_purchase_enable = '_is_individual_seat_purchase_enable';

	/**
	 * @var string
	 */
	static $variable_product_allow_seats = '_variable_product_allow_seats';

	/**
	 * Meta field used to flag whether we need to process an order.
	 *
	 * @since 2.1.2
	 *
	 * @var string
	 */
	public static $process_order_meta_field = 'ld_groups_plus_process_order';

	/**
	 * Class constructor
	 */
	public function __construct() {

	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @return bool
	 * @since 2.8.0
	 */
	public static function is_woocommerce_active() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return true;
		} elseif ( is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
			return true;
		} elseif ( class_exists( 'WooCommerce' ) ) {
			return true;
		} elseif ( function_exists( 'WC' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if WooCommerce Subscription is active
	 *
	 * @return bool
	 * @since 2.8.0
	 */
	public static function is_woocommerce_subscription_active() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
			return true;
		} elseif ( is_plugin_active_for_network( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
			return true;
		} elseif ( class_exists( 'WC_Subscriptions' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sets a database cache for the given User.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 $user_id now defaults to 0.
	 *
	 * @param string  $key     Cache name. Should contain "USERID".
	 * @param mixed[] $data    Data to cache. Defaults to an empty array.
	 * @param int     $user_id User ID to store this for. Defaults to 0 to signify the currently logged in user.
	 *
	 * @return void
	 */
	public static function set_transient_cache( $key, $data = [], $user_id = 0 ) {
		$user_id = Cast::to_int( $user_id );

		if ( $user_id <= 0 ) {
			$user_id = wp_get_current_user()->ID;
		}

		$key = str_replace(
			'USERID',
			$user_id,
			Cast::to_string( $key )
		);

		if ( is_multisite() ) {
			add_site_option( $key, $data );
		} else {
			add_option( $key, $data );
		}
	}


	/**
	 * Retrieves a set database cache for the given User.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 $key now defaults to an empty string and $user_id to 0.
	 *
	 * @param string $key          Cache name. Should contain "USERID". Defaults to an empty string.
	 * @param int    $user_id      User ID for get User for. Defaults to 0 to signify the currently logged in user.
	 * @param bool   $is_transient Whether to pull from a Transient. Defaults to false, which causes it to pull from an Option. Unused.
	 *
	 * @return false|mixed|void
	 */
	public static function get_transient_cache( $key = '', $user_id = 0, $is_transient = false ) {
		$user_id = Cast::to_int( $user_id );

		if ( $user_id <= 0 ) {
			$user_id = wp_get_current_user()->ID;
		}

		$key = str_replace(
			'USERID',
			$user_id,
			Cast::to_string( $key )
		);

		/*
		 if ( false === $is_transient ) {
			if ( is_multisite() ) {
				return get_site_transient( $key );
			} else {
				return get_transient( $key );
			}
		} */

		if ( $is_transient ) {
			if ( is_multisite() ) {
				return get_site_transient( $key );
			} else {
				return get_transient( $key );
			}
		}

		if ( is_multisite() ) {
			return get_site_option( $key, false );
		} else {
			return get_option( $key, false );
		}
	}

	/**
	 * Remove a cache for a given User.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 $key now defaults to an empty string and $user_id to 0.
	 *
	 * @param string $all     Whether to delete All caches for the User. Defaults to "no".
	 * @param string $key     Cache name. Should contain "USERID". Defaults to an empty string.
	 * @param int    $user_id User ID for get User for. Defaults to 0 to signify the currently logged in user.
	 *
	 * @return void
	 */
	public static function remove_transient_cache( $all = 'no', $key = '', $user_id = 0 ) {
		$user_id = Cast::to_int( $user_id );

		if ( $user_id <= 0 ) {
			$user_id = wp_get_current_user()->ID;
		}

		if ( 'no' === $all ) {
			$key = str_replace(
				'USERID',
				$user_id,
				Cast::to_string( $key )
			);

			delete_option( $key );
			delete_transient( $key );
		}
	}


	/**
	 * @param      $product
	 * @param bool    $initial
	 *
	 * @return float|string
	 */
	public static function get_custom_product_price( $product, $initial = false ) {

		if ( $product instanceof \WC_Product && $product->is_type( 'groups_plus_courses' ) ) {
			if ( 'yes' === (string) get_option( 'woocommerce_calc_taxes' ) ) {
				$price = 'yes' === get_option( 'woocommerce_prices_include_tax' ) ? wc_get_price_including_tax( $product ) : wc_get_price_excluding_tax( $product );
			} else {
				$price = $product->get_price();
			}
			\WoocommerceLicense::$product_price[ $product->get_id() ] = $price;

			return apply_filters( 'get_course_price', $price, $product );
		} elseif ( $product instanceof \WC_Product && $product->is_type( 'groups_plus_seats' ) ) {
			return self::get_license_price( $product, $initial );
		} else {
			return $product->get_price();
		}
	}

	/**
	 * Check if BuddyBoss is active
	 *
	 * @return bool
	 * @since 2.8.0
	 */
	public static function is_buddyboss_active() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'buddyboss-platform/bp-loader.php' ) ) {
			return true;
		} elseif ( is_plugin_active_for_network( 'buddyboss-platform/bp-loader.php' ) ) {
			return true;
		} elseif ( class_exists( 'BuddyPress' ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function filter_has_var( $variable = null, $type = INPUT_GET ) {
		return filter_has_var( $type, $variable );
	}

	/**
	 * Wrapper method for PHP's filter_input() with some defaults set.
	 *
	 * Example:
	 *
	 * SharedFunctions::filter_input(
	 *     'email',
	 *     INPUT_GET,
	 *     FILTER_SANITIZE_EMAIL
	 * )
	 *
	 * @since 1.0.0
	 * @since 2.1.0 $variable default removed, as one should always be set when using this method.
	 *
	 * @param string $variable Name of a variable to get.
	 * @param int    $type     One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV.
	 * @param int    $flags    The ID of the filter to apply. Defaults to 513, which before PHP 8.1 was FILTER_SANITIZE_STRING.
	 *
	 * @return mixed The filtered input.
	 */
	public static function filter_input( $variable, $type = INPUT_GET, $flags = 513 ) {
		// Backwards compatibility.
		$variable = Cast::to_string( $variable );

		if ( $flags === 513 ) {
			// Emulate deprecated FILTER_SANITIZE_STRING flag.
			return htmlspecialchars(
				Cast::to_string(
					filter_input( $type, $variable )
				)
			);
		}

		return filter_input( $type, $variable, $flags );
	}

	public static function filter_input_array( $variable = null, $type = INPUT_GET, $flags = array() ) {
		if ( empty( $flags ) ) {
			$flags = array(
				'filter' => FILTER_VALIDATE_INT,
				'flags'  => FILTER_REQUIRE_ARRAY,
			);
		}
		/*
		 * View input types: https://www.php.net/manual/en/function.filter-input.php
		 * View flags at: https://www.php.net/manual/en/filter.filters.sanitize.php
		 */
		$args = array( $variable => $flags );
		$val  = filter_input_array( $type, $args );

		return isset( $val[ $variable ] ) ? $val[ $variable ] : array();
	}

	/**
	 * Retrieves the path to a template file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name Template file name.
	 *
	 * @return string Template file path.
	 */
	public static function get_template( $file_name ) {
		// First check for the new folder structure: learndash/groups-plus.
		$template_path = 'learndash' . DIRECTORY_SEPARATOR . 'groups-plus' . DIRECTORY_SEPARATOR;
		$asset_path    = self::locate_template( $template_path . $file_name );

		// If not found, check for the old folder structure: learndash-groups-plus (backwards compatibility).
		if ( empty( $asset_path ) ) {
			$template_path = 'learndash-groups-plus' . DIRECTORY_SEPARATOR;
			$asset_path    = self::locate_template( $template_path . $file_name );
		}

		// If still not found, use the plugin's default template.
		if ( empty( $asset_path ) ) {
			$templates_directory = LEARNDASH_GROUPS_PLUS_DIR;

			$asset_path = $templates_directory . 'src/resources/' . $file_name;
		}

		return $asset_path;
	}

	public static function locate_template( $template_names ) {
		$located = '';
		foreach ( (array) $template_names as $template_name ) {
			if ( ! $template_name ) {
				continue;
			}
			if ( file_exists( get_stylesheet_directory() . DIRECTORY_SEPARATOR . $template_name ) ) {
				$located = get_stylesheet_directory() . DIRECTORY_SEPARATOR . $template_name;
				break;
			} elseif ( file_exists( get_template_directory() . DIRECTORY_SEPARATOR . $template_name ) ) {
				$located = get_template_directory() . DIRECTORY_SEPARATOR . $template_name;
				break;
			}
		}

		return $located;
	}

	/**
	 * Check if Gravity Form is active
	 *
	 * @return bool
	 * @since 2.8.0
	 */
	public static function is_gravity_form_active() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'gravityforms/gravityforms.php' ) ) {
			return true;
		} elseif ( is_plugin_active_for_network( 'gravityforms/gravityforms.php' ) ) {
			return true;
		} else {
			return false;
		}
	}


	/**
	 * Check if
	 *
	 * @return bool
	 * @since 2.8.0
	 */
	public static function is_quiz_notification_for_ld_active() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'elc-ldquiz-notifications/elc-ldquiz-notifications.php' ) ) {
			return true;
		} elseif ( is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
			return true;
		} else {
			return false;
		}
	}
}
