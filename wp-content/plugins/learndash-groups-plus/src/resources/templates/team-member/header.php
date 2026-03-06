<?php
/**
 * Teams View Header.
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use LearnDash\Groups_Plus\Utility\SharedFunctions;

$g_id             = get_query_var( 'group_id', false ) != '' ? get_query_var( 'group_id', false ) : $_GET['group'];
$decrypt_group_id = general_encrypt_decrypt( 'decrypt', $g_id );
$parent_g_id      = wp_get_post_parent_id( $decrypt_group_id );

?>
<div class="groups_plus_header_box" data-group-id="<?php echo $g_id; ?>">
	<div class="groups_plus_title">
		<h5><?php esc_html_e( $group_info->post_title, 'learndash-groups-plus' ); ?></h5>
	</div>
	<div class="text-right">
		<?php

			do_action( 'before_groups_plus_header_button', $decrypt_group_id );

		if ( SharedFunctions::is_woocommerce_active() && 'yes' === get_option( 'enable_wc', 'no' ) && '1' === get_option( 'enable_add_seats', '0' ) ) {
			$query_args            = array(
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
			$subscription_products = new WP_Query( $query_args );

			$query_args               = array(
				'post_type' => 'product',
				'tax_query' => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'groups_plus_seats',
					),
				),
			);
			$no_subscription_products = wc_get_products( $query_args );

			// echo $query_sql = $products->request;
			// print_r($products);

			if ( 'yes' === get_option( 'allow_team_leaders_to_add_subscription_seats', 'no' )
				&& count( $subscription_products->posts )
				&& count( $no_subscription_products )
				) {
				?>
					<a href="" class="btn_groups_plus" id="btn_add_seats">
					<?php esc_html_e( 'Add Seats', 'learndash-groups-plus' ); ?>
						<i class="fa fa-angle-right"></i>
					</a>
					<?php
			} else {


				// Check if the groups_plus_seats products are there or not, if have any single then only shows the button to add seats.
				if ( count( $subscription_products->posts ) || count( $no_subscription_products ) ) {
					$add_seat_link = add_query_arg(
						[
							'add-learndash-groups-plus-group-seat' => true,
							'group-id' => $decrypt_group_id,
							'qty'      => 1,
							'nonce'    => wp_create_nonce( 'learndash_groups_plus_add_seat_product' ),
						],
						site_url()
					);
					?>
						<a onclick="location.href='<?php echo esc_attr( $add_seat_link ); ?>'" class="btn_groups_plus">
						<?php esc_html_e( 'Add Seats', 'learndash-groups-plus' ); ?>
							<i class="fa fa-angle-right"></i>
						</a>
					<?php
				} //  if ( count($products) ) :
			}
		}  // if ( SharedFunctions::is_woocommerce_active() && 'yes' === get_option( 'enable_wc', 'no' ) )
		?>

		<?php
			$hide_groups_plus_add_team_member_button = get_site_option( 'hide_groups_plus_add_team_member_button', 'no' );
		if ( $hide_groups_plus_add_team_member_button === 'no' ) {
			?>
		<a href="" class="btn_groups_plus" id="btn_add_groups_plus_team_member">
			<?php printf(
				esc_html__( 'Add %s', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team_member' )
			); ?> <i class="fa fa-angle-right"></i>
		</a>
		<?php } ?>

		<?php
			$hide_groups_plus_import_list_button = get_site_option( 'hide_groups_plus_import_list_button', 'no' );
		if ( $hide_groups_plus_import_list_button === 'no' ) {
			?>
		<a href="" class="btn_groups_plus"
			id="btn_import_groups_plus_team_member"><?php esc_html_e( 'Import List', 'learndash-groups-plus' ); ?> <i
				class="fa fa-angle-right"></i></a>
		<?php } ?>

		<?php
		$hide_groups_plus_email_groups_plus_button = get_site_option( 'hide_groups_plus_email_groups_plus_button', 'no' );
		if ( $hide_groups_plus_email_groups_plus_button === 'no' ) {
			?>

		<a href="#"
			class="btn_groups_plus btn_email_groups_plus init-groups-plus-team-member-email"
			data-group="<?php // echo $encrypt_group_id; ?>"
			data-group-name=""
			data-group-courses=""
		>
			<?php printf(
				esc_html__( 'Email %s', 'learndash-groups-plus' ),
				learndash_get_custom_label( 'team' )
			); ?> <i class="fa fa-angle-right"></i>
		</a>
		<?php } ?>

		<?php do_action( 'after_groups_plus_header_button', $decrypt_group_id ); ?>
	</div>
</div>
