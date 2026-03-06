<?php
/**
 * Template to show related courses of a product on product single page
 *
 * @since 4.3.3
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="ldgr_group_courses <?php echo ( 'off' === $display_product_courses ) ? 'ldgr-hide' : ''; ?>">
	<?php if ( ! empty( $product_courses ) ) : ?>
		<span class="ldgr_group_courses_title">
			<?php
			echo esc_html(
				sprintf(
				// translators: Group label, Courses Label.
					__( '%1$s in this %2$s', 'wdm_ld_group' ),
					$courses_label,
					$group_label
				)
			);
			?>
			<img id="wdm_course_help_btn" src="<?php echo esc_url( plugins_url( 'media/help.png', __DIR__ ) ); ?>"><br>
			<span class="wdm_course_help_text" style="display: none;color: #808080;font-style: italic;font-size:small;font-weight:normal;">
				<?php echo esc_html( apply_filters( 'wdm_course_help_text', __( 'These Courses will be included in your purchase by default.', 'wdm_ld_group' ) ) ); ?>
			</span>
		</span>
		<div class="ldgr-course-tile-row" data-courses='<?php echo esc_attr( htmlspecialchars( json_encode( $product_courses ), ENT_QUOTES, 'UTF-8' ) ); ?>'>
			<?php foreach ( $product_courses as $single_course ) : ?>
				<div class="ldgr-course-tile" data-course-id=<?php echo esc_attr( $single_course ); ?>>
					<?php if ( has_post_thumbnail( $single_course ) ) : ?>
						<img style="width: 100%;" src="<?php echo esc_url( get_the_post_thumbnail_url( $single_course ) ); ?>">
					<?php else : ?>
						<img style="width: 100%;" src="<?php echo esc_attr( $def_course_image ); ?>">
					<?php endif; ?>
					<p>
						<label title="<?php echo esc_attr( get_the_title( $single_course ) ); ?>">
							<?php echo esc_html( mb_strimwidth( get_the_title( $single_course ), 0, apply_filters( 'ldgr_course_character_truncate_limit', 50 ), '...' ) ); ?>
						</label>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
