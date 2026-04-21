<?php
/**
 * Report card component grades table header.
 *
 * @since 1.1.0
 * @updated 3.3.0
 * @version 4.3.2
 *
 * @var array $component
 * @var LD_GB_UserGrade $user_grade
 * @var int $gradebook_id
 */

defined( 'ABSPATH' ) || die();
?>

<th class="ld-gb-report-card-grades-column-type">
	<?php _ex( 'Type', 'Report Card: Grade Type label', 'learndash-gradebook' ); ?>
</th>

<th class="ld-gb-report-card-grades-column-name">
	<?php _ex( 'Name', 'Report Card: Grade Name label', 'learndash-gradebook' ); ?>
</th>

<th class="ld-gb-report-card-grades-column-score">
	<?php _ex( 'Score', 'Report Card: Grade Score label', 'learndash-gradebook' ); ?>
</th>
