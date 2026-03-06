<?php
/**
 * Template: LDGR group name box.
 *
 * @since 4.0.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;

?>
<br>
<br>
<input type="hidden" class='ldgr_new_price' name='ldgr_new_price' value='<?php echo esc_attr( $price ); ?>'>
<input type="hidden" class='ldgr_total_price' name='ldgr_total_price' value='<?php echo esc_attr( $price ); ?>'>
<?php if ( 'off' !== $display_product_footer ) : ?>
	<div class="ldgr-cal <?php echo ( 'individual' === $default_option ) ? 'ldgr-hide' : ''; ?>">
		<div class="ldgr-grp-info">
			<div class="ldgr-g-name">
				<span class="ldgr-g-lbl">
				<?php
				echo esc_html(
					sprintf(
						// translators: group label.
						__( '%s Name: ', 'wdm_ld_group' ),
						$group_label
					)
				);
				?>
			</span>
				<span class="ldgr-g-val"><?php echo get_option( 'ldgr_autofill_group_name' ) == 'on' ? esc_html( get_the_title( $product_id ) . ' | ' . date( 'm/d/Y' ) ) : ''; ?></span>
			</div>
		</div>
		<div class="ldgr-seats">
			<span class="ldgr-g-lbl"><?php esc_html_e( 'Seats:', 'wdm_ld_group' ); ?></span>
			<span class="ldgr-g-val"></span>
		</div>
		<div class="ldgr-g-price">
			<span class="ldgr-total"><?php esc_html_e( 'Total:', 'wdm_ld_group' ); ?></span>
			<span class="ldgr-value">
				<?php
				echo wp_kses(
					wc_price( '0.00' ),
					[
						'span' => [],
						'bdi'  => [],
					]
				);
				?>
			</span>
			<span class="ldgr-discounted-value"> </span>
			<span class="ldgr-g-discount-lbl"><?php esc_html_e( 'Discounted price', 'wdm_ld_group' ); ?></span>
		</div>
	</div>
<?php endif; ?>
<div class="<?php echo esc_html( $group_section_classes ); ?>" <?php echo ( 'individual' == $default_option ) ? 'style="display:none;"' : ''; ?>>
	<label class="ldgr_group_name_switch" for="ldgr_group_name" style="display:none;">
		<strong><?php esc_html_e( 'Group Name', 'wdm_ld_group' ); ?></strong>
	</label>
	<?php if ( empty( $variation_ids ) ) : ?>
		<input
			type="text"
			name="ldgr_group_name"
			value="<?php get_option( 'ldgr_autofill_group_name' ) == 'on' ? esc_html_e( get_the_title( $product_id ) . ' | ' . date( 'm/d/Y' ) ) : ''; ?>"
			placeholder="<?php printf( /* translators: Group label */ esc_html__( 'Enter a name for your %s', 'wdm_ld_group' ), \LearnDash_Custom_Label::label_to_lower( 'group' ) ); ?>"
			data-product-id = "<?php echo esc_html( $product_id ); ?>"
			<?php echo empty( $group_name ) ? '' : 'readonly'; ?>
		/>
	<?php else : ?>
		<?php foreach ( $group_name as $variation => $details ) : ?>
			<input
				id="<?php echo esc_html( 'ldgr_variation_' . $variation ); ?>"
				class="ldgr_variation_group_options <?php echo esc_html( $instance->check_for_default_variation_class( $variation, $default_attributes ) ); ?>"
				type="<?php echo ( $details['in_cart'] && empty( $details['value'] ) ) ? 'hidden' : 'text'; ?>"
				name="<?php echo esc_html( 'ldgr_group_name_' . $variation ); ?>"
				value="<?php get_option( 'ldgr_autofill_group_name' ) == 'on' ? esc_html_e( get_the_title( $product_id ) . ' | ' . date( 'm/d/Y' ) ) : ''; ?>"
				placeholder="<?php printf( /* translators: Group label */ esc_html__( 'Enter a name for your %s', 'wdm_ld_group' ), \LearnDash_Custom_Label::label_to_lower( 'group' ) ); ?>"
				data-product-id = "<?php echo esc_html( $variation ); ?>"
				<?php // echo empty( $details['value'] ) ? '' : 'readonly'; ?>
			/>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

<?php
ldgr_get_template(
	WDM_LDGR_PLUGIN_DIR . '/modules/templates/ldgr-product-related-courses.template.php',
	[
		'group_label'             => $group_label,
		'courses_label'           => $courses_label,
		'product_courses'         => $product_courses,
		'def_course_image'        => $def_course_image,
		'display_product_courses' => get_option( 'ldgr_display_product_courses' ),
	]
);
?>
