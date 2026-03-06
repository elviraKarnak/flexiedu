<?php
/**
 * Commission Logs Template.
 *
 * @since 4.2.0
 * @version 5.9.7
 *
 * @var int     $instructor_id      ID of the instructor.
 * @var string  $course_label       LearnDash Course Label.
 * @var object  $instance           Object of class Instructor_Role_Settings.
 *
 * @package LearnDash\Instructor_Role
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="ir-commission-wrapper">
	<div id="reports_table_div" class="ir-commission-reports-container" style="padding-right: 5px">

		<!--Table shows Name, Email, etc-->
		<table class="DataTable" data-filter="#filter" data-page-navigation=".pagination" id="wdm_report_tbl" data-page-size="5" >
			<thead>
				<tr>
					<th data-sort-initial="descending" data-class="expand">
						<?php esc_html_e( 'Order ID', 'wdm_instructor_role' ); ?>
					</th>
					<th data-sort-initial="descending" data-class="expand">
						<?php
						echo esc_html(
							sprintf( /* translators: Course Label. */
								__( 'Product / %s Name', 'wdm_instructor_role' ),
								$course_label
							)
						);
						?>
					</th>
					<th>
						<?php esc_html_e( 'Actual Price', 'wdm_instructor_role' ); ?>
					</th>
					<th>
						<?php esc_html_e( 'Commission Price', 'wdm_instructor_role' ); ?>
					</th>
					<th>
						<?php esc_html_e( 'Product Type', 'wdm_instructor_role' ); ?>
					</th>
				</tr>
				<?php
				/**
				 * Commission Reports Table Header End
				 *
				 * @param int $instructor_id    User ID of the instructor.
				 */
				do_action( 'wdm_commission_report_table_header', $instructor_id );
				?>
			</thead>
			<tbody>
				<?php
				global $wpdb;
				$results = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT * FROM {$wpdb->prefix}wdm_instructor_commission WHERE user_id = %d",
						$instructor_id
					)
				);

				if ( ! empty( $results ) ) {
					$amount_paid = 0;
					foreach ( $results as $value ) {
						$amount_paid += $value->commission_price;
						?>
						<tr>
							<td>
								<?php $instance->wdmcheckProductType( $value ); // cspell:disable-line . ?>
							</td>
							<td>
								<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- The method returns a href attribute with escaped URL. ?>
								<a target="_new_blank" <?php echo $instance->wdmGetPostEditLink( $value->product_id ); ?>>
									<?php echo esc_html( $instance->wdmGetPostTitle( $value->product_id ) ); ?>
								</a>
							</td>
							<td><?php echo esc_html( $value->actual_price ); ?></td>
							<td><?php echo esc_html( $value->commission_price ); ?></td>
							<td><?php echo esc_html( $value->product_type ); ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td colspan="5" class="ir-no-data-found">
							<?php esc_html_e( 'No record found!', 'wdm_instructor_role' ); ?>
						</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<?php
				}
				/**
				 * Commission Reports Table Body End
				 *
				 * @param int $instructor_id
				 */
				do_action( 'wdm_commission_report_table', $instructor_id );
				?>
			</tbody>
			<tfoot >
				<?php
				if ( ! empty( $results ) ) {
					$paid_total = get_user_meta( $instructor_id, 'wdm_total_amount_paid', true );
					if ( '' == $paid_total ) {
						$paid_total = 0;
					}

					$amount_paid = round( ( $amount_paid - $paid_total ), 2 );
					$amount_paid = max( $amount_paid, 0 );
					?>
					<tr>
						<td></td>
						<td style="color:black;font-weight: bold;">
							<?php esc_html_e( 'Paid Earnings', 'wdm_instructor_role' ); ?>
						</td>
						<td>
							<!-- <a> -->
								<span id="wdm_total_amount_paid"><?php echo esc_attr( $paid_total ); ?></span>
							<!-- </a> -->
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td style="color:black;font-weight: bold;">
							<?php esc_html_e( 'Unpaid Earnings', 'wdm_instructor_role' ); ?>
						</td>
						<td>
							<span id="wdm_amount_paid"><?php echo esc_html( number_format( $amount_paid, 2 ) ); ?></span>
							<?php if ( 0 != $amount_paid && is_super_admin() ) : ?>
								<a href="#" class="button-primary" id="wdm_pay_amount">
									<?php esc_html_e( 'Pay', 'wdm_instructor_role' ); ?>
								</a>
							<?php endif; ?>
						</td>
						<td></td>
						<td></td>
					</tr>
				<?php } ?>
				<tr>
					<td colspan="5" style="border-radius: 0 0 6px 6px;">
						<div class="pagination pagination-centered hide-if-no-paging"></div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php
	// Display commission payment popup templates for admin.
	if ( is_super_admin() ) {
		ir_get_template(
			INSTRUCTOR_ROLE_ABSPATH . 'modules/templates/payouts/ir-commission-payment.template.php',
			[
				'instructor_id' => $instructor_id,
			]
		);
	}

	/**
	 * After Instructor Commission Report End
	 *
	 * Run after the instructor commission report is displayed.
	 *
	 * @since 3.4.0
	 *
	 * @param int $instructor_id    User ID of the instructor.
	 */
	do_action( 'ir_action_commission_report_end', $instructor_id );
	?>

	<div class="ir-commission-logs-container">
		<h2><?php esc_html_e( 'Payouts Transaction History', 'wdm_instructor_role' ); ?></h2>
		<ul class="ir-tabs-nav">
			<li class="active"><a href="#manual_logs"><?php esc_html_e( 'Manual', 'wdm_instructor_role' ); ?></a></li>
			<li><a href="#paypal_logs"><?php esc_html_e( 'PayPal', 'wdm_instructor_role' ); ?></a></li>
		</ul>
		<section class="ir-tabs-content">
			<div id="manual_logs" class="ir-tab">
				<?php
				/**
				 * Manual Logs Tab Content
				 *
				 * @param int $instructor_id    User ID of the instructor.
				 *
				 * @since 4.2.0
				 */
				do_action( 'ir_action_manual_logs', $instructor_id );
				?>
			</div>
			<div id="paypal_logs" class="ir-tab">
				<?php
				/**
				 * Paypal Logs Tab Content
				 *
				 * @param int $instructor_id    User ID of the instructor.
				 *
				 * @since 4.2.0
				 */
				do_action( 'ir_action_paypal_logs', $instructor_id );
				?>
			</div>
		</section>
	</div>
</div>
