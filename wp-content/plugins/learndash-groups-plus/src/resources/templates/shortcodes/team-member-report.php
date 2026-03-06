<?php
/**
 * Main template file for [learndash_groups_plus_report].
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use LearnDash\Groups_Plus\Module\Group;
use LearnDash\Groups_Plus\Utility\SharedFunctions;

$all_primary_groups = Group::get_all_primary_groups();
$groups             = Group::get_child_groups();
if ( isset( $_GET['group'] ) && ! empty( $_GET['group'] ) ) {
	$group_id = general_encrypt_decrypt( 'decrypt', $_GET['group'] );
} elseif ( ! empty( $groups ) ) {
	$group_id = $groups[0]->ID;
} else {
	$group_id = 0;
}

$user_group_users = Group::get_group_users( $group_id );

?>
<div class="groups_plus_container team_member_report">
	<?php require SharedFunctions::get_template( '/templates/team/admin-header.php' ); ?>
	<?php require SharedFunctions::get_template( '/templates/team-member/filter.php' ); ?>
	<?php require SharedFunctions::get_template( '/templates/team-member/login-history.php' ); ?>
	<?php require SharedFunctions::get_template( '/templates/team-member/lesson-listing.php' ); ?>
</div>
