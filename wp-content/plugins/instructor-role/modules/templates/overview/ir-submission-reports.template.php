<?php
/**
 * Template : Submission Reports
 *
 * @param array  $submissions    List of Assignments and Essays for the instructor's courses
 * @param object $instance       Instance of class Instructor_Role_Overview
 *
 * @since 3.1.0
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;
?>

<div id="ir-submissions-content">
	<table class="ir-assignments-table">
		<thead class="ir-assignment-table-header">
			<th><?php _e( 'Title', 'wdm_instructor_role' ); ?></th>
			<th><?php echo \LearnDash_Custom_Label::get_label( 'course' ); ?></th>
			<th><?php _e( 'Lesson', 'wdm_instructor_role' ); ?></th>
			<th><?php _e( 'Date', 'wdm_instructor_role' ); ?></th>
			<th><?php _e( 'Points', 'wdm_instructor_role' ); ?></th>
			<th><?php _e( 'Status', 'wdm_instructor_role' ); ?></th>
			<th><?php _e( 'Type', 'wdm_instructor_role' ); ?></th>
		</thead>
		<tbody class="ir-assignment-table-body">
			<?php foreach ( $submissions as $submission ) : ?>
				<tr class="ir-assignment-row">
					<td class="ir-assignment-title">
						<a href="<?php echo $submission['link']; ?>" target="blank" class="ir-submission-link" title="<?php echo $submission['title']; ?>">
							<?php echo $instance->addEllipses( $submission['title'] ); ?>
						</a>
					</td>
					<td class="ir-assignment-course">
						<?php echo $submission['course']; ?>
					</td>
					<td class="ir-assignment-lesson">
						<?php echo $submission['lesson']; ?>
					</td>
					<td class="ir-assignment-date" data-order="<?php echo esc_attr( $submission['timestamp'] ); ?>" >
						<?php echo $submission['date']; ?>
					</td>
					<td class="ir-assignment-points">
						<?php echo $submission['points']; ?>
					</td>
					<td class="ir-assignment-status">
						<?php echo $submission['status']; ?>
					</td>
					<td class="ir-assignment-type">
						<?php echo $submission['type']; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if ( empty( $submissions ) ) : ?>
			<td class="ir-no-data-found" colspan="7">
				<?php _e( 'No submissions recorded yet', 'wdm_instructor_role' ); ?>
			</td>
			<?php endif; ?>
		</tbody>
	</table>
</div>
