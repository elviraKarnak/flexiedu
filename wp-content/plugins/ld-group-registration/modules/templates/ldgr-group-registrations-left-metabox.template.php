<?php
/**
 * Template: Group Registrations Left Metabox.
 *
 * @since 4.0.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 *
 * @var int $group_id The group ID for this page.
 */

defined( 'ABSPATH' ) || exit;

$learndash_seats_group_label = LearnDash_Custom_Label::get_label( 'group' );
?>
<div class="ldgr-group-seats-metabox">
	<div class="ldgr-seats-left">
		<span><?php echo esc_html( __( 'Seats Left: ', 'wdm_ld_group' ) ); ?></span>
		<span class="ldgr-seats-left-count" data-seat-count="<?php echo esc_attr( $group_limit ); ?>">
		<input class="ldgr_seats_left" type="hidden" name="ldgr_seats_left" value="<?php echo esc_attr( $group_limit ); ?>">
			<?php if ( $is_unlimited ) : ?>
				<?php esc_html_e( 'Unlimited', 'wdm_ld_group' ); ?>
			<?php else : ?>
				<?php
				echo esc_html(
					sprintf(
						/* translators: Total no of seats. */
						__( '%d seats', 'wdm_ld_group' ),
						$group_limit
					)
				);
				?>
			<?php endif; ?>
		</span>
		<span class="ldgr-related-product-id">
			<?php
				echo esc_html(
					sprintf(
						/* translators: The product ID of the related product. */
						__( ' | Related Product ID: %s', 'wdm_ld_group' ),
						learndash_seats_get_group_product_id( $group_id )
					)
				);
				?>
		</span>
	</div>
	<div class="ldgr-total-seats">
		<label for="ldgr_total_seats"><?php echo esc_html( __( 'Total Seats: ', 'wdm_ld_group' ) ); ?></label>
		<span>
			<?php if ( $is_unlimited ) : ?>
				<?php esc_html_e( 'Unlimited', 'wdm_ld_group' ); ?>
				<input type="hidden" name="ldgr_total_seats" value="99999">
			<?php else : ?>
				<input class="ldgr_total_seats" type="number" min="0" name="ldgr_total_seats" value="<?php echo esc_attr( $total_limit ); ?>">
			<?php endif; ?>
		</span>
		<?php if ( ! $is_unlimited ) : ?>
			<div class="ldgr-reset-seats" title="<?php esc_attr_e( 'Reset Seat Count', 'wdm_ld_group' ); ?>">
				<span class="dashicons dashicons-update"></span>
			</div>
		<?php endif; ?>
	</div>
</div>
<p class="ldgr-tooltip-text">
	<strong><?php esc_html_e( 'Note: ', 'wdm_ld_group' ); ?></strong>
	<?php
	echo wp_kses(
		sprintf(
			/* translators: %1$s: Uppercase Group label, %2$s: lowercase group label */
			__( 'The <em>Total Seats</em> count field has been added in the <strong>v4.3.8</strong> of the LearnDash LMS - Group Registration plugin. Using this field, you can add/remove seats from this %1$s and in-turn manage the number of Seats Left in this %1$s, with ease. The value of the <em>Total Seats</em> field has currently been set to <strong>"Number of users enrolled in this %2$s + Number of Seats Left"</strong> post the update. For additional information visit the <a href="https://www.learndash.com/support/docs/add-ons/group-registration-for-learndash/" target="_blank">Help Guide</a>', 'wdm_ld_group' ),
			$learndash_seats_group_label,
			strtolower( $learndash_seats_group_label )
		),
		[
			'strong' => [],
			'em'     => [],
			'a'      => [
				'href'   => [],
				'target' => [],
			],
		]
	);
	?>
</p>
<?php if ( 'on' === $is_fixed_group ) : ?>
	<p class="ldgr-tooltip-text">
		<strong><?php esc_html_e( 'Note: ', 'wdm_ld_group' ); ?></strong>
		<?php
		echo wp_kses(
			sprintf(
				/* translators: Settings page link. */
				__( 'The <strong>Fix Group Limit</strong> Setting is on. To <strong>switch off</strong> this setting go <a target="_blank" href="%s" title="Link to Settings Page">here</a>.', 'wdm_ld_group' ),
				$settings_page_link
			),
			[
				'strong' => [],
				'a'      => [
					'href'   => [],
					'title'  => [],
					'target' => [],
				],
			]
		);
		?>
	</p>
<?php endif; ?>

<h2 class="ldgr-removal-req-header">
	<?php
		echo esc_html( __( 'User Removal Request', 'wdm_ld_group' ) );
	?>
</h2>
<div class="ldgr-bulk-actions">
	<input type='button' class='button' id='bulk_accept' value='<?php echo esc_html( __( 'Bulk Accept', 'wdm_ld_group' ) ); ?>'>
	<input type='button' class='button' id='bulk_reject' value='<?php echo esc_html( __( 'Bulk Reject', 'wdm_ld_group' ) ); ?>'>
</div>
<table id="wdm_admin">
	<thead>
		<th class="ldgr-bulk-select"><input type="checkbox" name="select_all"></th>
		<th><?php echo esc_html( __( 'Username', 'wdm_ld_group' ) ); ?></th>
		<th><?php echo esc_html( __( 'Action', 'wdm_ld_group' ) ); ?></th>
	</thead>
	<tbody>
	<?php if ( ! empty( $removal_request ) ) : ?>
		<?php foreach ( $removal_request as $key => $user_id ) : ?>
			<?php $user_data = get_user_by( 'id', $user_id ); ?>
				<tr>
					<td class="select_action ldgr-bulk-select">
						<input
							type="checkbox"
							name="bulk_select"
							data-user_id="<?php echo esc_html( $user_id ); ?>"
							data-group_id="<?php echo esc_html( (string) $group_id ); ?>"
						/>
					</td>
					<td>
						<center>
							<?php echo esc_html( $user_data->user_email ); ?>
						</center>
					</td>
					<td>
						<center>
							<a
								href="#"
								data-user_id="<?php echo esc_html( $user_id ); ?>"
								data-group_id="<?php echo esc_html( (string) $group_id ); ?>"
								class="button wdm_accept">
								<?php echo esc_html( __( 'Accept', 'wdm_ld_group' ) ); ?>
							</a>
							<a
								href="#"
								data-user_id="<?php echo esc_html( $user_id ); ?>"
								data-group_id="<?php echo esc_html( (string) $group_id ); ?>"
								class="button wdm_reject">
								<?php echo esc_html( __( 'Reject', 'wdm_ld_group' ) ); ?>
							</a>
						</center>
					</td>
				</tr>
			<?php unset( $key ); ?>
		<?php endforeach; ?>
	<?php endif; ?>
	</tbody>
</table>
