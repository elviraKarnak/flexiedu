<?php
/**
 * Template for the Edit Panel Grade Edit Form
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var integer         $user_id
 * @var integer         $gradebook_id
 * @var integer         $group_id
 * @var array           $component
 * @var string          $grade_format
 */

defined( 'ABSPATH' ) || die();

$name_id   = 'ld-gb-frontend-gradebook-grade-edit-name-' . wp_generate_uuid4();
$score_id  = 'ld-gb-frontend-gradebook-grade-edit-score-' . wp_generate_uuid4();
$status_id = 'ld-gb-frontend-gradebook-grade-edit-status-' . wp_generate_uuid4();

$grade_statuses = ld_gb_get_grade_statuses();
$grade_statuses = array_merge( [ '' => [ 'label' => __( 'No Special Status', 'learndash-gradebook' ) ] ], $grade_statuses );

// The pending status is only used for Assignments behind-the-scenes
if ( isset( $grade_statuses['pending'] ) ) {
	unset( $grade_statuses['pending'] );
}

?>

<div class="reveal ld-gb-frontend-gradebook-grade-edit-overlay ld-gb-frontend-gradebook-overlay" data-reveal>

	<form method="post" class="ld-gb-frontend-gradebook-grade-edit" data-component_id="<?php echo esc_attr( $component['id'] ); ?>" data-user_id="<?php echo esc_attr( $user_id ); ?>" data-gradebook_id="<?php echo esc_attr( $gradebook_id ); ?>" data-group_id="<?php echo esc_attr( $group_id ); ?>"  data-grade_format="<?php echo esc_attr( $grade_format ); ?>">

		<input type="hidden" name="post_id" value="0" />
		<input type="hidden" name="type" value="" />
		<input type="hidden" name="previous_name" value="" />
		<input type="hidden" name="name" value="" />

		<?php if ( ! ld_gb_get_option_field( 'disable_manual_grades', false ) ) : ?>

			<div class="grid-container full manual-grade-specific" style="display: none;">
				<div class="grid-x grid-margin-x">
					<div class="ld-gb-frontend-gradebook-grade-edit-name-container cell">
						<label for="<?php echo esc_attr( $name_id ); ?>">
							<?php _ex( 'Name', 'Frontend Gradebook: Edit Manual Grade Name label', 'learndash-gradebook' ); ?>
						</label>
						<input type="text" name="name" id="<?php echo esc_attr( $name_id ); ?>" disabled />
					</div>
				</div>
			</div>

			<div class="grid-container full manual-grade-specific" style="display: none;">
				<div class="grid-x grid-margin-x">
					<div class="ld-gb-frontend-gradebook-grade-edit-score-container cell">
						<label for="<?php echo esc_attr( $score_id ); ?>">
							<?php _ex( 'Score', 'Frontend Gradebook: Edit Manual Grade Score label', 'learndash-gradebook' ); ?>
						</label>
						<input type="number" name="score" min="0" max="100" step="any" id="<?php echo esc_attr( $score_id ); ?>" />
					</div>

				</div>
			</div>

		<?php endif; ?>

		<div class="grid-container full">
			<div class="grid-x grid-margin-x">
				<div class="ld-gb-frontend-gradebook-grade-edit-status-container cell">
					<label for="<?php echo esc_attr( $status_id ); ?>">
						<?php _ex( 'Status', 'Frontend Gradebook: Edit Grade Status label', 'learndash-gradebook' ); ?>
					</label>
					<select name="status" id="<?php echo esc_attr( $status_id ); ?>">
						<?php foreach ( $grade_statuses as $key => $value ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>">
								<?php echo $value['label']; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>

		<div class="grid-container full">
			<div class="grid-x grid-margin-x">

				<div class="ld-gb-frontend-gradebook-grade-edit-submit-container medium-4 cell">
					<input type="submit" value="<?php _e( 'Update', 'learndash-gradebook' ); ?>" data-submitting_text="<?php esc_attr_e( 'Updating...', 'learndash-gradebook' ); ?>"
					/>
				</div>

				<div class="ld-gb-frontend-gradebook-grade-edit-cancel-container medium-4 medium-offset-4 cell">
					<button class="ld-gb-frontend-gradebook-grade-edit-cancel button alert expanded">
						<?php _e( 'Cancel', 'learndash-gradebook' ); ?>
					</button>
				</div>

			</div>
		</div>

	</form>

	<button class="close-button" data-close aria-label="<?php _e( 'Close modal', 'learndash-gradebook' ); ?>" type="button">
		<span aria-hidden="true">&times;</span>
	</button>

</div>
