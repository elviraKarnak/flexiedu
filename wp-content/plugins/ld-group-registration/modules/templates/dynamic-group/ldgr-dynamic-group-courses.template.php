<?php
/**
 * Template: LDGR Dynamic group dropdown.
 *
 * @since 4.3.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>
<br>
<br>
<div class="ldgr_dynamic_courses" <?php echo ( 'individual' === $default_option ) ? 'style="display:none;"' : ''; ?>>
	<span class="ldgr_dynamic_course_title">
		<?php
			echo esc_html(
				sprintf(
				// translators: Courses.
					__( 'Add more %s', 'wdm_ld_group' ),
					$courses_label
				)
			);
			?>
			<img id="wdm_add_course_help_btn" src="<?php echo esc_url( plugins_url( '../media/help.png', __DIR__ ) ); ?>"><br>
			<span class="wdm_add_course_help_text" style="display: none;color: #808080;font-style: italic;font-size:small;font-weight:normal;">
				<?php echo esc_html( apply_filters( 'wdm_add_course_help_text', __( 'You can select additional Courses to purchase.', 'wdm_ld_group' ) ) ); ?>
			</span>
	</span>

	<div class='wdm-courses-checkbox'>
		<div class="ldgr-course-tile-row">
			<?php foreach ( $courses as $single_course ) : ?>
				<div class="ldgr-course-tile">
					<?php if ( has_post_thumbnail( $single_course ) ) : ?>
						<img style="width: 100%;" src="<?php echo esc_url( get_the_post_thumbnail_url( $single_course ) ); ?>">
					<?php else : ?>
						<img style="width: 100%;" src="<?php echo $def_course_image; ?>">
					<?php endif; ?>
					<input type="checkbox" class="wdm-dynamic-course-checkbox" id="course_<?php echo esc_attr( $single_course ); ?>" name="course_<?php echo esc_attr( $single_course ); ?>" value="<?php echo esc_attr( ldgr_get_course_price( $single_course ) ); ?>">
					<p>
						<label for="course_<?php echo esc_attr( $single_course ); ?>" title="<?php echo esc_attr( get_the_title( $single_course ) ); ?>">
							<?php echo esc_html( mb_strimwidth( get_the_title( $single_course ), 0, apply_filters( 'ldgr_course_character_truncate_limit', 50 ), '...' ) ); ?>
						</label>
					</p>
					<p>
						<label><?php echo wc_price( ldgr_get_course_price( $single_course ) ); ?></label>
					</p>
				</div>
				<?php endforeach; ?>
			</div>
	</div>
</div>
