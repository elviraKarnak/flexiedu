<?php
/**
 * Template for the Edit Panel Grade Add Form
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var integer         $user_id
 * @var integer         $gradebook_id
 * @var integer         $group_id
 * @var array           $component
 * @var LD_GB_UserGrade $user_grade
 * @var string          $grade_format
 */

defined( 'ABSPATH' ) || die();

$name_id   = 'ld-gb-frontend-gradebook-grade-add-name-' . wp_generate_uuid4();
$score_id  = 'ld-gb-frontend-gradebook-grade-add-score-' . wp_generate_uuid4();
$status_id = 'ld-gb-frontend-gradebook-grade-add-status-' . wp_generate_uuid4();

$grade_statuses = ld_gb_get_grade_statuses();
$grade_statuses = array_merge( [ '' => [ 'label' => __( 'No Special Status', 'learndash-gradebook' ) ] ], $grade_statuses );

// The pending status is only used for Assignments behind-the-scenes
if ( isset( $grade_statuses['pending'] ) ) {
	unset( $grade_statuses['pending'] );
}

?>

<button class="ld-gb-frontend-gradebook-grade-add-show-form button secondary">
	<?php _e( 'Add Manual Grade', 'learndash-gradebook' ); ?>
</button>

<form method="post" class="ld-gb-frontend-gradebook-grade-add" data-component_id="<?php echo esc_attr( $component['id'] ); ?>" data-user_id="<?php echo esc_attr( $user_id ); ?>" data-gradebook_id="<?php echo esc_attr( $gradebook_id ); ?>" data-group_id="<?php echo esc_attr( $group_id ); ?>" data-grade_format="<?php echo esc_attr( $grade_format ); ?>">

	<div class="grid-container full">
		<div class="grid-x grid-margin-x">
			<div class="ld-gb-frontend-gradebook-grade-add-name-container cell">
				<label for="<?php echo esc_attr( $name_id ); ?>">
					<?php _ex( 'Name', 'Frontend Gradebook: Manual Grade Name label', 'learndash-gradebook' ); ?> <span class="required">*</span>
				</label>
				<input type="text" name="name" required id="<?php echo esc_attr( $name_id ); ?>" />
			</div>
		</div>
	</div>

	<div class="grid-container full">
		<div class="grid-x grid-margin-x">

			<div class="ld-gb-frontend-gradebook-grade-add-score-container medium-3 cell">
				<label for="<?php echo esc_attr( $score_id ); ?>">
					<?php _ex( 'Score', 'Frontend Gradebook: Manual Grade Score label', 'learndash-gradebook' ); ?> <span class="required">*</span>
				</label>
				<input type="number" name="score" min="0" max="100" value="0" required id="<?php echo esc_attr( $score_id ); ?>" />
			</div>

			<div class="ld-gb-frontend-gradebook-grade-add-status-container medium-9 cell">
				<label for="<?php echo esc_attr( $status_id ); ?>">
					<?php _ex( 'Status', 'Frontend Gradebook: Manual Grade Status label', 'learndash-gradebook' ); ?>
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

			<div class="ld-gb-frontend-gradebook-grade-add-submit-container medium-4 cell">
				<input type="submit" value="<?php _ex( 'Add', 'Frontend Gradebook: Manual Grade Add button text', 'learndash-gradebook' ); ?>" data-submitting_text="<?php _e( 'Adding...', 'learndash-gradebook' ); ?>"
				/>
			</div>

			<div class="ld-gb-frontend-gradebook-grade-add-cancel-container medium-4 medium-offset-4 cell">
				<button class="ld-gb-frontend-gradebook-grade-add-cancel button alert expanded">
					<?php _ex( 'Cancel', 'Frontend Gradebook: Manual Grade Cancel creation button text', 'learndash-gradebook' ); ?>
				</button>
			</div>

		</div>
	</div>

</form>
