<?php
/**
 * Template for the Edit Panel Grade Row
 *
 * @since 2.0.0
 * @updated 4.3.0
 * @version 4.3.2
 *
 * @var array           $grade
 * @var LD_GB_UserGrade $user_grade
 * @var array           $component
 * @var integer         $user_id
 * @var integer         $gradebook_id
 * @var integer         $group_id
 * @var string          $grade_format
 */

defined( 'ABSPATH' ) || die();
?>

<tr>
	<td class="ld-gb-frontend-gradebook-grade-name">
		<span class="ld-gb-frontend-gradebook-component-grade-icon" title="<?php echo esc_attr( ld_gb_get_grade_type_name( $grade['type'] ) ); ?>">
			<span class="dashicons <?php echo esc_attr( ld_gb_grade_icon( $grade['type'] ) ); ?>"></span>
		</span>

		<?php if ( isset( $grade['post_id'] ) && $grade['post_id'] && current_user_can( 'edit_post', $grade['post_id'] ) ) : ?>
			<a href="<?php echo get_edit_post_link( $grade['post_id'] ); ?>">
		<?php endif; ?>

			<span class="ld-gb-frontend-gradebook-grade-name-content"><?php echo $grade['name']; ?></span>

		<?php if ( isset( $grade['post_id'] ) && $grade['post_id'] && current_user_can( 'edit_post', $grade['post_id'] ) ) : ?>
			</a>
		<?php endif; ?>

		<?php if ( isset( $grade['pending_essay_questions'] ) && ! empty( $grade['pending_essay_questions'] ) ) : ?>

			<p>
				<?php
				echo esc_html(
					sprintf(
						// translators: %s is the labels for Essay and Questions
						__( 'Ungraded %1$s %2$s:', 'learndash-gradebook' ),
						learndash_get_custom_label( 'essay' ),
						learndash_get_custom_label( 'questions' )
					)
				);
				?>
			</p>

			<ul style="list-style-type: disc; margin-left: 1.5em;">
				<?php foreach ( $grade['pending_essay_questions'] as $question ) : ?>

					<li>

						<?php if ( current_user_can( 'edit_post', $question['post_id'] ) ) : ?>
							<a href="<?php echo get_edit_post_link( $question['post_id'] ); ?>" target="_blank">
						<?php endif; ?>

							<?php echo get_the_title( $question['post_id'] ); ?>

						<?php if ( current_user_can( 'edit_post', $question['post_id'] ) ) : ?>
							</a>
						<?php endif; ?>

					</li>

				<?php endforeach; ?>
			</ul>

		<?php endif; ?>

	</td>
	<td class="ld-gb-frontend-gradebook-grade-actions" data-user_id="<?php echo esc_attr( $user_id ); ?>" data-gradebook_id="<?php echo esc_attr( $gradebook_id ); ?>" data-group_id="<?php echo esc_attr( $group_id ); ?>"  data-component_id="<?php echo esc_attr( $component['id'] ); ?>" data-grade_format="<?php echo esc_attr( $grade_format ); ?>">

		<a href="#update_grade" class="ld-gb-frontend-gradebook-grade-update-show" <?php echo ( isset( $grade['post_id'] ) && $grade['post_id'] ) ? ' data-post_id="' . esc_attr( $grade['post_id'] ) . '"' : ''; ?> data-type="<?php echo esc_attr( $grade['type'] ); ?>" data-status="<?php echo esc_attr( $grade['status'] ); ?>" data-previous_name="<?php echo esc_attr( $grade['name'] ); ?>" data-name="<?php echo esc_attr( $grade['name'] ); ?>" data-score="<?php echo esc_attr( $grade['score'] ); ?>"><?php _ex( 'Edit', 'Frontend Gradebook: Edit Grade button text', 'learndash-gradebook' ); ?></a>

		<?php if ( $grade['type'] == 'manual' ) : ?>

			<?php echo ' | '; ?>

			<a href="#remove_grade" class="ld-gb-frontend-gradebook-grade-remove" data-submitting_text="<?php echo esc_attr( __( 'Removing...', 'learndash-gradebook' ) ); ?>"><?php _ex( 'Remove', 'Frontend Gradebook: Remove Manual Grade button text', 'learndash-gradebook' ); ?></a>

		<?php endif; ?>

	</td>
	<td>
		<?php
		if ( isset( $grade['completed'] ) && $grade['completed'] ) :

			echo ld_gb_get_datetimestamp( $grade['completed'] );

		endif;
		?>
	</td>
	<td>
		<?php if ( $grade['status'] !== 'pending' ) : ?>
			<?php echo learndash_gradebook_get_grade_display( $grade['score'], $grade_format ); ?>
		<?php else : ?>
			<?php echo esc_html( $grade['score_display'] ); ?>
		<?php endif; ?>
	</td>
</tr>
