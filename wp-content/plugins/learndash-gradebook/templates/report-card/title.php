<?php
/**
 * Report card title.
 *
 * @since 1.1.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var LD_GB_UserGrade $user_grade
 * @var int $gradebook_id
 */

defined( 'ABSPATH' ) || die();
?>

<div class="ld-gb-report-card-title">
	<?php
	/* translators: First %s is the Gradebook Title */
	printf(
		__( 'Report Card for: %s', 'Report Card Title', 'learndash-gradebook' ),
		esc_attr( $user_grade->get_gradebook()->post_title )
	);
	?>
</div>
