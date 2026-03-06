<?php
/**
 * Common functions
 *
 * @since 4.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/includes
 */

use LearnDash\Core\Utilities\Cast;

defined( 'ABSPATH' ) || exit;

/**
 * Send emails using woocommerce template
 *
 * @param string $send_to       Send to email address.
 * @param string $subject       Subject of the email.
 * @param string $message       Message to be sent.
 * @param string $headers       Email headers.
 * @param array  $attachments   Email attachments.
 * @param array  $extra_data     Additional data related to type of email and group ID.
 */
function ldgr_send_group_mails( $send_to, $subject, $message, $headers = '', $attachments = [], $extra_data = [] ) {
	/**
	 * Filter the group email extra data
	 *
	 * @since 4.1.2
	 *
	 * @param array $extra_data     Additional data related to type of email sent and the group ID.
	 */
	$extra_data = apply_filters( 'ldgr_group_email_extra_data', $extra_data );

	/**
	 * Filter group email recipient email address
	 *
	 * @since 4.1.2
	 *
	 * @param string $send_to       Email address of the recipient.
	 * @param array $extra_data     Additional information related to the emails to be sent.
	 */
	$send_to = apply_filters( 'ldgr_group_email_to', $send_to, $extra_data );

	/**
	 * Filter group email subject
	 *
	 * @since 4.1.2
	 *
	 * @param string $subject       Subject of the email to be sent.
	 * @param array $extra_data     Additional information related to the emails to be sent.
	 */
	$subject = apply_filters( 'ldgr_group_email_subject', $subject, $extra_data );

	/**
	 * Filter group email body
	 *
	 * @since 4.1.2
	 *
	 * @param string $message       Body of the email to be sent.
	 * @param array $extra_data     Additional information related to the emails to be sent.
	 */
	$message = apply_filters( 'ldgr_group_email_message', $message, $extra_data );

	/**
	 * Filter group email headers
	 *
	 * @since 4.1.2
	 *
	 * @param string $headers       Headers of the email to be sent.
	 * @param array $extra_data     Additional information related to the emails to be sent.
	 */
	$headers = apply_filters( 'ldgr_group_email_headers', $headers, $extra_data );

	/**
	 * Filter group email attachments
	 *
	 * @since 4.1.2
	 *
	 * @param string $attachments   Attachments of the email to be sent.
	 * @param array $extra_data     Additional information related to the emails to be sent.
	 */
	$attachments = apply_filters( 'ldgr_group_email_attachments', $attachments, $extra_data );

	// Select mailer.
	$mailer = 'wp';

	if ( class_exists( 'WooCommerce' ) ) {
		// WooCommerce.
		$mailer = 'woocommerce';
	}

	/**
	 * Filter whether to send emails using Woocommerce or default WP mails.
	 *
	 * @since 4.1.4
	 *
	 * @param string $mailer    The notification method to be used to send emails.
	 * @param array $extra_data Additional information related to the emails to be sent.
	 */
	$mailer = apply_filters( 'ldgr_filter_notification_mailer', $mailer, $extra_data );

	switch ( $mailer ) {
		case 'woocommerce':
			global $woocommerce;
			$mailer  = $woocommerce->mailer();
			$message = $mailer->wrap_message( $subject, $message );
			$mailer->send( $send_to, $subject, $message, $headers, $attachments );
			break;
		case 'wp':
			// Add filter to format HTML emails.
			add_filter( 'wp_mail_content_type', 'ldgr_set_mail_content_type' );
			wp_mail( $send_to, $subject, $message, $headers, $attachments );
			// Reset it to what it was before.
			remove_filter( 'wp_mail_content_type', 'ldgr_set_mail_content_type' );
			break;
		default:
			/**
			 * Allow 3rd party plugins to use different emails to send emails
			 *
			 * @since 4.1.4
			 *
			 * @param string $mailer        The notification method to be used to send emails.
			 * @param array  $extra_data    Additional information related to the emails to be sent.
			 * @param string $send_to       Email address of the recipient.
			 * @param string $subject       Subject of the email to be sent.
			 * @param string $message       Body of the email to be sent.
			 * @param string $headers       Headers of the email to be sent.
			 * @param string $attachments   Attachments of the email to be sent.
			 */
			do_action( 'ldgr_action_custom_notification_mail', $mailer, $extra_data, $send_to, $subject, $message, $headers, $attachments );
			break;
	}
}

/**
 * Set mail content type to HTML
 *
 * @since 4.1.2
 *
 * @return string
 */
function ldgr_set_mail_content_type() {
	/**
	 * Set Group Registrations email content type
	 *
	 * @param string $content_type      Set mail content type to HTML(default) or plain text.
	 */
	return apply_filters( 'ldgr_set_mail_content_type', 'text/html' );
}

/**
 * Remove add to cart button from shop page.
 */
function wdm_remove_loop_button() {
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
}

/**
 * Redirect to checkout
 */
function wdm_redirect_to_checkout() {
	global $woocommerce;
	$checkout_url = $woocommerce->cart->get_checkout_url();

	return $checkout_url;
}
/**
 * Replacing string
 *
 * @param string $search    Search for string.
 * @param string $replace   Replace with string.
 * @param string $subject   String to perform replacement operation.
 *
 * @return string           Subject after performing replacement operations.
 */
function ldgr_str_lreplace( $search, $replace, $subject ) {
	$pos = strrpos( $subject, $search );

	if ( false !== $pos ) {
		$subject = substr_replace( $subject, $replace, $pos, strlen( $search ) );
	}

	return $subject;
}
/**
 * Custom function to fetch all the subscription details for the particular order.
 *
 * @param obj $order        Order object.
 * @param int $product_id   ID of the product.
 * @param int $order_id     ID of the order.
 *
 * @return array            Keys of the subscriptions in the order.
 */
function ldgr_get_order_subscription_ids( $order, $product_id, $order_id ) {
	if ( ! isset( $order ) ) {
		return [];
	}
	if ( ! ( $order instanceof \WC_Order ) ) {
		return [];
	}
	$subscription_keys = [];
	$subscriptions     = \wcs_get_subscriptions_for_order( $order, [ 'product_id' => $product_id ] );
	if ( ! empty( $subscriptions ) ) {
		foreach ( $subscriptions as $sub_key => $subscription ) {
			$subscription_keys[] = $sub_key;
			$subscription        = $subscription;
		}
	}
	return $subscription_keys;
}
/**
 * Get product type
 *
 * @param int $product_id   ID of the product.
 * @return string           Type of the product.
 */
function ldgr_get_woo_product_type( $product_id ) {
	if ( ! isset( $product_id ) || ( 'product' != get_post_type( $product_id ) ) ) {
		return '';
	}
	$product_details = \wc_get_product( $product_id );
	return $product_details->get_type();
}

/**
 * Is group leader restricted to perform actions.
 *
 * @param int $user_id      ID of the user.
 * @param int $group_id     ID of the group.
 *
 * @return boolean          True if group leader has access, false otherwise.
 */
function is_group_leader_restricted_to_perform_actions( $user_id, $group_id ) {
	if ( 'groups' != get_post_type( $group_id ) || ( ! user_can( $user_id, 'group_leader' ) && ! user_can( $user_id, 'manage_options' ) ) ) {
		return false;
	}
	$type = get_post_meta( $group_id, 'wdm_group_reg_product_type_' . $group_id, true );
	if ( ( 'subscription' == $type ) || ( 'variable-subscription' == $type ) ) {
		$subscription_id = get_post_meta( $group_id, 'wdm_group_subscription_' . $group_id, true );
		if ( ! empty( $subscription_id ) ) {
			$total_hold_sub = get_user_meta( $user_id, '_wdm_total_hold_subscriptions', true );
			if ( ! empty( $total_hold_sub ) && in_array( $subscription_id, $total_hold_sub ) ) {
				return true;
			}
		}
	}
	return false;
}

/**
 * Get group IDs for which the user is group leader
 *
 * @param int $user_id  ID of the user.
 *
 * @return array        List of group ids.
 */
function ldgr_get_leader_group_ids( $user_id = 0 ) {
	global $wpdb;

	// If empty get current user id.
	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$group_ids = [];

	// Get list of all groups where current user is group leader.
	$group_ids = learndash_get_administrators_group_ids( $user_id );

	// If group hierarchy enabled, don't show sub-groups separately.
	if ( learndash_is_groups_hierarchical_enabled() ) {
		$sub_groups_ids = [];
		foreach ( $group_ids as $group_id ) {
			$sub_groups_ids = array_merge( $sub_groups_ids, learndash_get_group_children( $group_id ) );
		}
		$sub_groups_ids = array_unique( $sub_groups_ids );
		$group_ids      = array_diff( $group_ids, $sub_groups_ids );
	}
	return apply_filters( 'ldgr_get_admin_group_ids', $group_ids, $user_id );
}

/**
 * Get sub group IDs for given group id
 *
 * @param int $group_id  ID of the user.
 *
 * @return array        List of group ids.
 */
function ldgr_get_sub_group_ids( $group_id = 0 ) {
	$sub_group_ids = [];
	if ( ! empty( $group_id ) ) {
		$sub_group_ids = maybe_unserialize( get_post_meta( $group_id, 'sub_groups', true ) );
		if ( empty( $sub_group_ids ) ) {
			$sub_group_ids = [];
		} else {
			$sub_group_ids = array_unique( $sub_group_ids );
		}
	}
	return apply_filters( 'ldgr_get_group_sub_group_ids', $sub_group_ids, $group_id );
}

/**
 * Check if group is sub group by give sub group ID
 *
 * @param int $sub_group_id  ID of the user.
 *
 * @return boolean        Boolean value if group is sub group.
 */
function ldgr_is_group_sub_group( $sub_group_id = 0 ) {
	$sub_group_status = false;
	if ( ! empty( $sub_group_id ) ) {
		$sub_group_status = get_post_meta( $sub_group_id, 'is_sub_groups', true );
	}
	return apply_filters( 'ldgr_get_sub_group_status', $sub_group_status, $sub_group_id );
}

/**
 * Checks whether the current user has already purchased the product and member of group.
 *
 * @param int    $product_id   ID of the product.
 * @param string $plugin       'edd' for EDD or 'wc' for Woocommerce.
 *
 * @return boolean             True if user is in group and has purchased the product, else false.
 */
function ldgr_is_user_in_group( $product_id, $plugin = 'wc' ) {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	$current_user = wp_get_current_user();
	$user_id      = $current_user->ID;

	$already_purchased = ( 'edd' == $plugin ) ? edd_has_user_purchased( $user_id, $product_id ) :
	wc_customer_bought_product( $current_user->user_email, $user_id, $product_id );

	if ( $already_purchased ) {
		global $wpdb;

		$sql = "SELECT SUBSTRING_INDEX( meta_key,  '_' , -1 ) AS group_id FROM " . $wpdb->prefix . 'usermeta WHERE user_id = ' . $user_id . " AND meta_key LIKE '%wdm_group_product_%' AND meta_value LIKE '" . $product_id . "'";

		$user_groups = $wpdb->get_col( $sql );

		if ( ! empty( $user_groups ) ) {
			foreach ( $user_groups as $group_id ) {
				$if_user = learndash_is_user_in_group( $user_id, $group_id );
				if ( $if_user ) {
					return true;
				}
			}
		}
	}

	return false;
}

/**
 * Using the parent product id it checks whether the package feature is enabled or not.
 *
 * @param int $product_id   ID of the product.
 *
 * @return boolean          True if package enabled, else false.
 */
function ldgr_check_package_enabled( $product_id ) {
	$enable_package = false;

	$var_product = \wc_get_product( $product_id );

	if ( $var_product->get_type() == 'variable' ) {
		$child_var = $var_product->get_children();
		if ( ! empty( $child_var ) ) {
			foreach ( $child_var as $var_id ) {
				$ena_pack = get_post_meta( $var_id, 'wdm_gr_package_' . $var_id, true );
				if ( ! empty( $ena_pack ) && $ena_pack == 'yes' ) {
					$enable_package = true;
					break;
				}
			}
		}
	}
	return $enable_package;
}

/**
 * Get templates passing attributes and including the file.
 *
 * @param string $template_path Template path.
 * @param array  $args          Arguments. (default: array).
 * @param bool   $return        Whether to return the result or echo. (default: false).
 */
function ldgr_get_template( $template_path, $args = [], $return = false ) {
	// Check if template exists.
	if ( empty( $template_path ) ) {
		return '';
	}
	/**
	 * Allow 3rd party plugins to filter template arguments.
	 *
	 * @since 4.1.2
	 *
	 * @param array  $args          Template arguments
	 * @param string $template_path Template path
	 */
	$args = apply_filters( 'ldgr_filter_template_args', $args, $template_path );
	// Check if arguments set
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // @codingStandardsIgnoreLine
	}

	/**
	 * Allow 3rd party plugins to filter template path.
	 *
	 * @since 4.1.2
	 *
	 * @param string $template_path Template path
	 * @param array  $args          Template arguments
	 */
	$template_path = apply_filters( 'ldgr_filter_template_path', $template_path, $args );

	// Whether to capture contents in output buffer.
	if ( $return ) {
		ob_start();
	}

	/**
	 * Allow 3rd party plugins to perform actions before template is rendered.
	 *
	 * @since 4.1.2
	 *
	 * @param array  $args          Template arguments
	 * @param string $template_path Template path
	 */
	do_action( 'ldgr_action_before_template', $args, $template_path );

	// Check if arguments set
	if ( ! empty( $args ) && is_array( $args ) ) {
        extract( $args ); // @codingStandardsIgnoreLine
	}

	include $template_path;

	/**
	 * Allow 3rd party plugins to perform actions after template is rendered.
	 *
	 * @since 4.1.2
	 *
	 * @param array  $args          Template arguments
	 * @param string $template_path Template path
	 */
	do_action( 'ldgr_action_after_template', $args, $template_path );

	// Return buffered contents.
	if ( $return ) {
		$contents = ob_get_clean();

		/**
		 * Allow 3rd party plugins to filter returned contents
		 *
		 * @since 4.1.2
		 *
		 * @param string $contents      HTML content rendered by the template
		 * @param array  $args          Template arguments
		 */
		return apply_filters( 'ldgr_filter_get_template_contents', $contents, $args );
	}
}

/**
 * Check if group leader
 *
 * @param int $user_id  ID of the user.
 * @return bool         True if user is group leader, false otherwise.
 *
 * @since 4.0.3
 */
function ldgr_check_if_group_leader( $user_id ) {
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}
	if ( function_exists( 'learndash_is_group_leader_user' ) ) {
		if ( learndash_is_group_leader_user( $user_id ) ) {
			return true;
		}
		return false;
	} else {
		if ( is_group_leader( $user_id ) ) {
			return true;
		}
		return false;
	}
}

/**
 * Get date in site timezone
 *
 * @param string $timestamp     Valid timestamp to be converted to site timezone.
 *
 * @return string               Date string in site timezone.
 *
 * @since 4.1.3
 */
function ldgr_date_in_site_timezone( $timestamp ) {
	if ( empty( $timestamp ) ) {
		return '';
	}

	// Fetch site timezone.
	/**
	 * Filter the timezone for the returned date
	 *
	 * @since 4.1.3
	 *
	 * @param string $site_timezone     Site timezone
	 */
	$site_timezone = apply_filters( 'ldgr_filter_date_in_site_timezone_timezone', get_option( 'timezone_string' ) );

	// If not set, default to UTC timezone.
	if ( empty( $site_timezone ) ) {
		$site_timezone = 'UTC';
	}

	// @todo: Fetch from options in future.
	$format = 'd-m-Y';

	// If empty format, set default format.
	if ( empty( $format ) ) {
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		$format      = $date_format . ' - ' . $time_format;
	}

	// Set return date format.
	/**
	 * Filter the datetime format for the returned date
	 *
	 * @since 4.1.3
	 *
	 * @param string $format        Valid PHP datetime format.
	 * @param string $timestamp     Unix timestamp of the date.
	 */
	$format = apply_filters( 'ldgr_filter_date_in_site_timezone_format', $format, $timestamp );

	$date = new DateTime();
	$date->setTimezone( new DateTimeZone( $site_timezone ) );
	$date->setTimestamp( $timestamp );
	$converted_date_string = $date->format( $format );

	/**
	 * Filter the date string to be returned.
	 *
	 * @since 4.1.3
	 *
	 * @param string $converted_date_string     Converted date string to be returned.
	 * @param object $date                      DateTime object of the returned date.
	 */
	return apply_filters( 'ldgr_filter_date_in_site_timezone', $converted_date_string, $date );
}

/**
 * Get time of day for a date.
 *
 * @param string $date_string    Date string to get time of day for.
 * @param string $time_of_day   Time of the day. Beginning of Day (BOD) or End of Day (EOD).
 *                              Defaults to BOD.
 * @return string $timestamp    Timestamp with the time of day for the date provided.
 *
 * @since 4.1.3
 */
function ldgr_get_date_time_of_day( $date_string, $time_of_day = 'BOD' ) {
	$date_details = [];
	if ( ! empty( $date_string ) ) {
		$date_details = explode( '-', $date_string );
	}

	/**
	 * Filter the timezone for the returned date
	 *
	 * @since 4.1.3
	 *
	 * @param string $site_timezone     Site timezone
	 */
	$site_timezone = apply_filters( 'ldgr_filter_date_in_site_timezone', get_option( 'timezone_string' ) );

	// If not set, default to UTC timezone.
	if ( empty( $site_timezone ) ) {
		$site_timezone = 'UTC';
	}

	$date = new DateTime();
	$date->setTimezone( new DateTimeZone( $site_timezone ) );
	if ( ! empty( $date_details ) ) {
		$date->setDate( $date_details[2], $date_details[1], $date_details[0] );
	}

	switch ( $time_of_day ) {
		case 'BOD':
			$date->setTime( 0, 0, 0 );
			break;

		case 'EOD':
			$date->setTime( 23, 59, 59 );
			break;
	}

	/**
	 * Filter timestamp returned for get time of day for the date
	 *
	 * @since 4.1.3
	 *
	 * @param string $timestamp     Timestamp returned.
	 * @param object $date          DateTime class object of the returned date.
	 * @param string $date_string   Date string in Y-m-d format.
	 * @param string $time_of_day   Time of day
	 */
	return apply_filters( 'ldgr_filter_get_date_time_of_day', $date->getTimestamp(), $date, $date_string, $time_of_day );
}

/**
 * Get groups dashboard page
 *
 * @since 4.2.0
 */
function ldgr_get_groups_dashboard_page() {
	global $wpdb;
	$groups_dashboard_pages = $wpdb->get_results(
		"SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE '%[wdm_group_users]%' AND post_status = 'publish' ",
		ARRAY_A
	);

	$groups_dashboard_page = 0;
	if ( ! empty( $groups_dashboard_pages ) ) {
		$groups_dashboard_page = $groups_dashboard_pages[0];
	}

	/**
	 * Filter get dashboard page.
	 *
	 * @since 4.2.0
	 *
	 * @param int $groups_dashboard_page        ID of the groups dashboard page with the [wdm_group_users] shortcode.
	 * @param array $groups_dashboard_pages     List of all pages with the [wdm_group_users] shortcode.
	 */
	return apply_filters( 'ldgr_filter_get_groups_dashboard_page', $groups_dashboard_page['ID'], $groups_dashboard_pages );
}

/**
 * This function is copy of woocommerce_wp_select_multiple function to display
 * multiple course selector on product edit page.
 *
 * @param array $field
 * @return void
 *
 * @since 4.3.0
 */
function ldgr_wp_select_multiple( $field ) {
	global $thepostid, $post; // cspell:disable-line .

	?>

	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$( '.select2.regular-width' ).show().select2({
				width: '50%'
			});

			$( '.select2.full-width' ).show().select2({
				width: '100%'
			});
		});
	</script>

	<style>
		.select2-container--open .select2-dropdown {
			position: relative;
		}

		/* Required to hide the select field on initial load */
		.woocommerce_options_panel select.ld_related_courses {
			display: none;
		}

		.woocommerce_options_panel .select2-container--default .select2-selection--multiple,
		#variable_product_options .select2-container--default .select2-selection--multiple {
			border: 1px solid #ddd !important;
		}
	</style>

	<?php

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid; // cspell:disable-line .
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['style']         = isset( $field['style'] ) ? $field['style'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true ); // cspell:disable-line .
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

	// Custom attribute handling
	$custom_attributes = [];

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
		}
	}

	echo '<div class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	if ( ! empty( $field['description'] ) && false !== $field['desc_tip'] ) {
		echo wc_help_tip( $field['description'] );
	}

	echo '<select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" style="' . esc_attr( $field['style'] ) . '" ' . implode( ' ', $custom_attributes ) . ' multiple="multiple">';

	foreach ( $field['options'] as $key => $value ) {
		$selected = in_array( $key, $field['value'] ) ? 'selected="selected"' : '';
		echo '<option value="' . esc_attr( $key ) . '" ' . $selected . '>' . esc_html( $value ) . '</option>';
	}

	echo '</select> ';

	if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
		echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
	}

	echo '</div>';
}

if ( ! function_exists( 'ldgr_get_course_price' ) ) {
	/**
	 * This function is used to Get course price.
	 */
	function ldgr_get_course_price( $course_id ) {
		if ( ! defined( 'LEARNDASH_VERSION' ) ) {
			return;
		}
		if ( version_compare( LEARNDASH_VERSION, '2.6.4', '>=' ) ) { // @phpstan-ignore-line -- False positive.
			$course_price_type = learndash_get_course_meta_setting( $course_id, 'course_price_type' );
			$course_price      = learndash_get_course_meta_setting( $course_id, 'course_price' );
		} else {
			$course_price_type = get_course_meta_setting( $course_id, 'course_price_type' );
			$course_price      = get_course_meta_setting( $course_id, 'course_price' );
		}

		/**
		 * If course is free return 0
		 */
		if ( in_array( $course_price_type, [ 'free', 'open' ] ) || empty( $course_price ) ) {
			return 0;
		}

		if ( ! empty( $course_price ) ) {
			$course_price = preg_replace( '/[^0-9.]/', '', $course_price );
			$course_price = number_format( floatval( $course_price ), 2, '.', '' );
		}

		if ( $course_price_type === 'subscribe' ) { // @phpstan-ignore-line -- False positive.
			return 'SUBSCRIPTION';
			$course_billing_p3 = get_post_meta( $course_id, 'course_price_billing_p3', true );
			$course_billing_t3 = get_post_meta( $course_id, 'course_price_billing_t3', true );
			switch ( $course_billing_t3 ) {
				case 'Y':
					$cycle = __( 'years', 'elumine' );
					break;
				case 'M':
					$cycle = __( 'months', 'elumine' );
					break;
				case 'W':
					$cycle = __( 'weeks', 'elumine' );
					break;
				case 'D':
					$cycle = __( 'days', 'elumine' );
					break;
				default:
					$cycle = __( 'days', 'elumine' );
					break;
			}
			$course_price = sprintf( '%s <span>for %s %s</span>', $course_price, $course_billing_p3, $cycle );
		}
		return $course_price;
	}
}

if ( ! function_exists( 'ldgr_get_product_variation_ids' ) ) {
	/**
	 * Get product variation IDs.
	 *
	 * @param int $product_id    ID of the product.
	 * @return array             List of variation ids if found, else false.
	 */
	function ldgr_get_product_variation_ids( $product_id ) {
		if ( empty( $product_id ) ) {
			return false;
		}
		$variation_ids      = [];
		$product_variations = new \WC_Product_Variable( $product_id );
		$product_variations = $product_variations->get_available_variations();
		foreach ( $product_variations as $variation ) {
			array_push( $variation_ids, $variation['variation_id'] );
		}

		/**
		 * Filter product variation IDs.
		 *
		 * @since 4.3.2
		 *
		 * @param array $variation_ids  Array of variation IDs.
		 * @param int   $product_id     ID of the product.
		 */
		return apply_filters( 'ldgr_filter_get_product_variation_ids', $variation_ids, $product_id );
	}
}

if ( ! function_exists( 'ldgr_filter_input' ) ) {
	/**
	 * Filter and sanitize data fetched from GET and POST requests.
	 *
	 * @since 4.3.8
	 *
	 * @param string $var_name  Name of the variable to get.
	 * @param int    $type      One of INPUT_GET for $_GET data or INPUT_POST for $_POST data.
	 *                          If not set default set to INPUT_POST.
	 * @param string $filter    One of string, number, float or bool. If not set default set to string.
	 *
	 * @return mixed            Value of requested variable on success.
	 *                          false if variable not set or if GET/POST empty.
	 *                          null if $type other than INPUT_GET or INPUT_POST passed.
	 */
	function ldgr_filter_input( $var_name, $type = INPUT_POST, $filter = 'string' ) {
		$value = null;

		// Check if POST or GET data.
		if ( INPUT_GET === $type ) {
			// If empty GET or key does not exist, return.
			if ( empty( $_GET ) || ! array_key_exists( $var_name, $_GET ) ) {
				return false;
			}

			// Filter data based on data type.
			switch ( $filter ) {
				case 'string':
					$value = filter_input( INPUT_GET, $var_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					break;
				case 'number':
					$value = filter_input( INPUT_GET, $var_name, FILTER_SANITIZE_NUMBER_INT );
					break;
				case 'float':
					$value = filter_input( INPUT_GET, $var_name, FILTER_SANITIZE_NUMBER_FLOAT );
					break;
			}
		} else {
			// If empty POST or key does not exist, return.
			if ( empty( $_POST ) || ! array_key_exists( $var_name, $_POST ) ) {
				return false;
			}

			// Filter data based on data type.
			switch ( $filter ) {
				case 'string':
					$value = filter_input( INPUT_POST, $var_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					break;
				case 'number':
					$value = filter_input( INPUT_POST, $var_name, FILTER_SANITIZE_NUMBER_INT );
					break;
				case 'float':
					$value = filter_input( INPUT_POST, $var_name, FILTER_SANITIZE_NUMBER_FLOAT );
					break;
			}
		}

		/**
		 * Filter the data returned after filtering and sanitization.
		 *
		 * @since 4.3.8
		 *
		 * @param mixed  $value     Input data after sanitization and filtering.
		 * @param int    $type      One of INPUT_GET or INPUT_POST. If not set default set to INPUT_POST.
		 * @param string $var_name  Name of the variable to get.
		 * @param string $filter    One of string, number, float or bool. If not set default set to string.
		 */
		return apply_filters( 'ldgr_filter_input', $value, $type, $var_name, $filter );
	}
}

if ( ! function_exists( 'ldgr_recalculate_group_seats' ) ) {
	/**
	 * Recalculate the seats for a group.
	 *
	 * Recalculate total seats and seats left count for a group after considering the users
	 * enrolled in the group.
	 *
	 * @since 4.3.9
	 *
	 * @param mixed $group  ID of the group or the WP Post object.
	 *
	 * @return bool         True if seats recalculated successfully, else false.
	 */
	function ldgr_recalculate_group_seats( $group ) {
		// Get post ID.
		$group_id = null;
		if ( $group instanceof \WP_Post ) {
			$group_id = $group->ID;
		} else {
			$group = get_post( $group );
		}

		// Check if valid post.
		if ( ! $group instanceof \WP_Post ) {
			return false;
		}

		// Check if group post type.
		if ( learndash_get_post_type_slug( 'group' ) !== $group->post_type ) {
			return false;
		}

		// Get group users list.
		$group_id             = $group->ID;
		$group_users          = learndash_get_groups_user_ids( $group_id );
		$enrolled_users_count = count( $group_users );

		// Get group seats data.
		$seats_left  = intval( get_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, true ) );
		$total_seats = intval( get_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, true ) );

		// Calculate seats left using enrolled users in group.
		$seats_left = $total_seats - $enrolled_users_count;

		// If group users more than total, update total and set seats left to 0.
		if ( $enrolled_users_count > $total_seats ) {
			$total_seats = $enrolled_users_count;
			$seats_left  = 0;
		}

		// Check for unlimited seats group.
		$is_unlimited = get_post_meta( $group_id, 'ldgr_unlimited_seats', 1 );

		// Update seats left.
		if ( ! $is_unlimited ) {
			update_post_meta( $group_id, 'wdm_group_users_limit_' . $group_id, $seats_left );
			update_post_meta( $group_id, 'wdm_group_total_users_limit_' . $group_id, $total_seats );
		}

		return true;
	}
}

/**
 * Get Group product ID.
 *
 * @since 4.3.15
 *
 * @param int $group_id ID of the group.
 *
 * @return int|null ID of the product if found, else null.
 */
function learndash_seats_get_group_product_id( $group_id ): ?int {
	$product_id = Cast::to_int( get_post_meta( $group_id, 'wdm_group_reg_product_id_' . $group_id, true ) );

	if ( ! empty( $product_id ) ) {
		return $product_id;
	}

	$group_meta = (array) get_post_meta( $group_id );

	if ( ! isset( $group_meta[ 'wdm_group_reg_order_id_' . $group_id ] ) ) {
		return null;
	}

	$group_order_id = (array) $group_meta[ 'wdm_group_reg_order_id_' . $group_id ];
	$group_order    = wc_get_order( $group_order_id[0] );

	if ( ! is_callable( [ $group_order, 'get_items' ] ) ) {
		return null;
	}

	foreach ( $group_order->get_items() as $learndash_seats_plus_item_id => $learndash_seats_plus_item ) {
		if ( $learndash_seats_plus_item['order_id'] === Cast::to_int( $group_order_id[0] ) ) {
			return Cast::to_int( $learndash_seats_plus_item['product_id'] );
		}
	}

	return null;
}
