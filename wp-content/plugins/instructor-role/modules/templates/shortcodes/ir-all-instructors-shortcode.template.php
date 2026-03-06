<?php
/**
 * All instructors shortcode template
 *
 * @since 3.5.5
 *
 * @param object $course                The Learndash Course
 * @param array $visible_instructors    List of co-instructors to be displayed.
 * @param array $hidden_instructors     List of hidden instructors to be kept hidden.
 *
 * @param array $atts                   List of shortcode attributes.
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="ir-all-instructors-shortcode-container">
	<?php if ( ! empty( $visible_instructors ) ) : ?>
		<div class="ir-visible-instructor-list">
			<?php foreach ( $visible_instructors as $instructor_id ) : ?>
				<div class="ir-instructor-div">
					<a class="ir-instructor-link" href="<?php echo esc_attr( ir_get_instructor_profile_link( $instructor_id ) ); ?>">
						<?php if ( $atts['display_img'] ) : ?>
							<img class="ir-instructor-img" src="<?php echo get_avatar_url( $instructor_id, [ 'size' => $atts['size'] ] ); ?>" alt="<?php echo get_the_author_meta( 'display_name', $instructor_id ); ?>" width="<?php echo esc_attr( $atts['size'] ) . 'px'; ?>"> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
						<?php endif; ?>
						<?php if ( $atts['display_name'] ) : ?>
							<span class="ir-instructor-name"><?php echo get_the_author_meta( 'display_name', $instructor_id ); ?></span>
						<?php endif; ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php if ( ! empty( $hidden_instructors ) ) : ?>
		<div class="ir-hidden-instructors-list">
			<span>
			<?php
			printf(
				/* translators: number of instructors */
				esc_html_x( '+ %d', 'placeholder: number of more instructors', 'wdm_instructor_role' ),
				count( $hidden_instructors )
			);
			?>
				</span>
			<?php foreach ( $hidden_instructors as $hidden_instructor_id ) : ?>
				<div class="ir-hidden-instructor-div">
					<a class="ir-instructor-link" href="<?php echo esc_attr( ir_get_instructor_profile_link( $instructor_id ) ); ?>">
						<?php if ( $atts['display_img'] ) : ?>
							<img class="ir-instructor-img" src="<?php echo get_avatar_url( $hidden_instructor_id, [ 'size' => $atts['size'] ] ); ?>" alt="<?php echo get_the_author_meta( 'display_name', $hidden_instructor_id ); ?>" width="<?php echo esc_attr( $atts['size'] ) . 'px'; ?>"> <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Should be checked later. ?>
						<?php endif; ?>
						<?php if ( $atts['display_name'] ) : ?>
							<span class="ir-instructor-name"><?php echo get_the_author_meta( 'display_name', $hidden_instructor_id ); ?></span>
						<?php endif; ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
