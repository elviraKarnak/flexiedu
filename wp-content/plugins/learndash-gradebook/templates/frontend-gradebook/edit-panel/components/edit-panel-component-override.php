<?php
/**
 * Template for the Edit Panel Component Grade Override Form
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2

 *
 * @var array           $component
 * @var string          $grade_format
 * @var integer         $user_id
 * @var integer         $gradebook_id
 * @var integer         $group_id
 */

defined( 'ABSPATH' ) || die();

$score_id = 'ld-gb-frontend-gradebook-grade-edit-score-' . wp_generate_uuid4();

?>

<div class="ld-gb-frontend-gradebook-component-grade-override-container">

	<a href="#override_component_grade" class="ld-gb-frontend-gradebook-component-grade-override-show" data-score="<?php echo esc_attr( $component['averaged_score'] ); ?>" data-overridden="<?php echo esc_attr( (bool) $component['overridden'] ); ?>">
		<?php if ( $component['overridden'] ) : ?>
			<?php _ex( 'Modify', 'Frontend Gradebook: Modify Overridden Component Grade button text', 'learndash-gradebook' ); ?>
		<?php else : ?>
			<?php _ex( 'Override', 'Frontend Gradebook: Override Component Grade button text', 'learndash-gradebook' ); ?>
		<?php endif; ?>
	</a>

	<div class="reveal ld-gb-frontend-gradebook-component-grade-override-overlay ld-gb-frontend-gradebook-overlay" data-reveal>

		<form method="post" class="ld-gb-frontend-gradebook-component-grade-override" data-component_id="<?php echo esc_attr( $component['id'] ); ?>" data-user_id="<?php echo esc_attr( $user_id ); ?>" data-gradebook_id="<?php echo esc_attr( $gradebook_id ); ?>" data-group_id="<?php echo esc_attr( $group_id ); ?>" data-grade_format="<?php echo esc_attr( $grade_format ); ?>">

			<div class="grid-container full">
				<div class="grid-x grid-margin-x">
					<div class="ld-gb-frontend-gradebook-component-grade-override-name-container cell">
						<div class="ld-gb-frontend-gradebook-component-grade-override-score-container cell">
							<label for="<?php echo esc_attr( $score_id ); ?>">
								<?php _ex( 'Score', 'Frontend Gradebook: Component Override Score label', 'learndash-gradebook' ); ?> <span class="required">*</span>
							</label>
							<input type="number" name="score" min="0" max="100" value="0" step="any" required id="<?php echo esc_attr( $score_id ); ?>" />
						</div>
					</div>
				</div>
			</div>

			<div class="grid-container full">
				<div class="grid-x grid-margin-x">

					<div class="ld-gb-frontend-gradebook-component-grade-override-submit-container medium-4 cell">
						<input type="submit" value="<?php _ex( 'Override', 'Frontend Gradebook: Component Override override button text', 'learndash-gradebook' ); ?>" data-submitting_text="<?php _e( 'Overriding...', 'learndash-gradebook' ); ?>"
						/>
					</div>

					<div class="ld-gb-frontend-gradebook-component-grade-override-cancel-container medium-4 medium-offset-4 cell">
						<button class="ld-gb-frontend-gradebook-component-grade-override-cancel button alert expanded" data-submitting_text="<?php echo _e( 'Removing...', 'learndash-gradebook' ); ?>">
							<?php _ex( 'Remove Override', 'Frontend Gradebook: Component Override remove override button text', 'learndash-gradebook' ); ?>
						</button>
					</div>

				</div>
			</div>

		</form>

		<button class="close-button" data-close aria-label="<?php _e( 'Close modal', 'learndash-gradebook' ); ?>" type="button">
			<span aria-hidden="true">&times;</span>
		</button>

	</div>

</div>
