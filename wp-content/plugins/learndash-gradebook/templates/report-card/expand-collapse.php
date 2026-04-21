<?php
/**
 * Report card expand/collapse.
 *
 * @since 1.1.0
 * @updated 3.3.0
 * @version 4.3.2
 *
 * @var LD_GB_UserGrade $user_grade
 * @var int             $gradebook_id
 */

defined( 'ABSPATH' ) || die();
?>

<div class="expand_collapse">
	<a href="#" onclick='return flip_expand_all("#ld-gb-report-card-<?php echo $gradebook_id; ?>-component-list");'>
		<?php _ex( 'Expand All', 'Report Card: Expand All Components link text', 'learndash-gradebook' ); ?>
	</a>&nbsp;|&nbsp;<a href="#" onclick='return flip_collapse_all("#ld-gb-report-card-<?php echo $gradebook_id; ?>-component-list");'>
		<?php _ex( 'Collapse All', 'Report Card: Collapse All Components link text', 'learndash-gradebook' ); ?>
	</a>
</div>
