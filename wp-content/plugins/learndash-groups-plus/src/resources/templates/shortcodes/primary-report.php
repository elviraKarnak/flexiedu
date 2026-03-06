<?php
/**
 * Main template file for [learndash_groups_plus_primary_report].
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use LearnDash\Groups_Plus\Module\Group;
use LearnDash\Groups_Plus\Utility\SharedFunctions;

$all_primary_groups = Group::get_all_primary_groups();
$groups             = Group::get_child_groups();
if ( ! empty( $groups ) ) {
	$group_id = $groups[0]->ID;
} else {
	$group_id = 0;
}
?>
<div class="groups_plus_container groups_plus_report">
	<!-- Team report -->
	<?php require SharedFunctions::get_template( '/templates/team/admin-header.php' ); ?>
	<?php require_once SharedFunctions::get_template( '/templates/team/groups-plus-report.php' ); ?>
</div>
