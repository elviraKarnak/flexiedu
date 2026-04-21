<?php
/**
 * Template for showing the Results for a given Gradebook and Group Combo
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var integer         $gradebook_id
 * @var integer         $group_id
 * @var string          $grade_format
 */

defined( 'ABSPATH' ) || die();

?>

<div class="ld-gb-results">

	<?php

	/**
	 * Gradebook Output
	 *
	 * @since 2.0.0
	 *
	 * @hooked LD_GB_SC_FrontendGradebook::gradebook_table_list() 10
	 */
	do_action( 'ld_gb_frontend_gradebook_table_list', $gradebook_id, $group_id, $grade_format );

	/**
	 * Gradebook Export Buttons Output
	 *
	 * @since 2.0.0
	 *
	 * @hooked LD_GB_SC_FrontendGradebook::export_buttons() 10
	 */
	do_action( 'ld_gb_frontend_gradebook_export_buttons', $gradebook_id, $group_id );

	?>

</div>
