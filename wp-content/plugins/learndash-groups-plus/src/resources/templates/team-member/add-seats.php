<?php
/**
 * Modal for adding additional Seats to a Team.
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

$form_id = 'add_seats';

?>
<div id="<?php echo $form_id; ?>_modal" class="groups-plus-modal">
	<!-- Modal content -->
	<div class="groups-plus-modal-content">
		<div class="groups-plus-modal-header text-center">
			<?php esc_html_e( 'Add Seats', 'learndash-groups-plus' ); ?><span
				class="groups-plus-close">&times;</span></div>
		<div class="groups-plus-modal-container">
			<form action="" method="GET" id="<?php echo $form_id; ?>"
				data-nonce="<?php echo wp_create_nonce( 'learndash-groups-plus-add-team-member' ); ?>">
				<?php wp_nonce_field( 'learndash_groups_plus_add_seat_product', 'nonce' ); ?>
				<div class="form_message"></div>
				<input type="hidden" name="add-learndash-groups-plus-group-seat" value="true"/>
				<input type="hidden" name="group-id" value="<?php echo general_encrypt_decrypt( 'decrypt', ( get_query_var( 'group_id', false ) != '' ? get_query_var( 'group_id', false ) : $_GET['group'] ) ); ?>"/>
				<input type="hidden" name="qty" value="1"/>
				<div class="form-group section">
					<?php
						$products = array();
					if ( SharedFunctions::is_woocommerce_active() ) {
						$query_args = array(
							'post_type' => 'product',
							'tax_query' => array(
								array(
									'taxonomy' => 'product_type',
									'field'    => 'slug',
									'terms'    => 'groups_plus_seats',
								),
							),
						);
						$products   = wc_get_products( $query_args );
					}

					if ( count( $products ) ) {
						?>
					<div class="col grid_4_of_4">
						<input type="radio" id="add-non-recurring-cost-seat" name="add-seats-type" value="add-non-recurring-cost-seat" checked>
						<label
							for="add-non-recurring-cost-seat"><?php esc_html_e( 'Add non-recurring cost seat', 'learndash-groups-plus' ); ?>
						</label>
					</div>
					<?php } ?>
					<div class="col grid_4_of_4" style="margin-left:0px;">
						<input type="radio" id="add-recurring-cost-seat" name="add-seats-type" value="add-recurring-cost-seat" checked>
						<label
							for="add-recurring-cost-seat"><?php esc_html_e( 'Add recurring cost seat', 'learndash-groups-plus' ); ?>
						</label>
					</div>
				</div>


				<div class="form-action section text-right pull-left action-buttons">
					<div class="col grid_1_of_4">
						<button type="submit" class="btn_groups_plus"
							id="<?php echo $form_id; ?>_btn"><?php esc_html_e( 'Add', 'learndash-groups-plus' ); ?> <i
								class="fa fa-angle-right"></i></button>
					</div>
				</div>
				<div class="form-action section text-right"></div>
			</form>
		</div>
	</div>
</div>
