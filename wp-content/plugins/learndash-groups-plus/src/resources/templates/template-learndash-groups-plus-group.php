<?php
/**
 * Teams view for [learndash_groups_plus].
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore ldquiz
 */

namespace LearnDash\Groups_Plus;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use LearnDash\Groups_Plus\Module\Group;
use LearnDash\Groups_Plus\Utility\SharedFunctions;

if ( ! isset( $shortcode_name ) ) {
	get_header();
}

?>
<div class="groups_plus_container">
	<?php

	if ( get_query_var( 'group_id', false ) != '' ) {
		$group_key = get_query_var( 'group_id' );
	} else {
		$group_key = $_GET['group'];
	}

	$group_id = general_encrypt_decrypt( 'decrypt', $group_key );

	// Check group_id is number and have access to group
	if ( $group_id && is_numeric( $group_id ) && Group::has_group_access( $group_id ) ) :
		$user_group_users = Group::get_group_users( $group_id );
		$group_info       = get_post( $group_id );
		$license_info     = get_licenses_info_user( $group_id );
		?>

	<div class="row-group">
		<?php
			$hide_groups_plus_header = get_site_option( 'hide_groups_plus_header', 'no' );
		if ( $hide_groups_plus_header === 'no' ) {

			include_once SharedFunctions::get_template( '/templates/team/header.php' );
			do_action( 'before_groups_plus_header_box', $group_id );

			include_once SharedFunctions::get_template( '/templates/team-member/header.php' );
			do_action( 'after_groups_plus_header_box', $group_id );
		}

			include_once SharedFunctions::get_template( '/templates/team-member/listing.php' );
			include_once SharedFunctions::get_template( '/templates/team-member/filter.php' );
			include_once SharedFunctions::get_template( '/templates/team-member/login-history.php' );
			include_once SharedFunctions::get_template( '/templates/team-member/lesson-listing.php' );
		?>
	</div>

		<?php
	else :
		_e( "You don't have access of this group.", 'learndash-groups-plus' );
	endif;
	?>
</div>

<!--- Modals --->
<?php
if ( Group::has_group_access( $group_id ) ) :
	include_once SharedFunctions::get_template( '/templates/team-member/add-seats.php' );
	include_once SharedFunctions::get_template( '/templates/team-member/add-team-member.php' );
	include_once SharedFunctions::get_template( '/templates/team-member/edit-team-member.php' );
	include_once SharedFunctions::get_template( '/templates/team-member/change-password.php' );
	include_once SharedFunctions::get_template( '/templates/team-member/import-team-member.php' );
	include_once SharedFunctions::get_template( '/templates/team-member/email-team-member.php' );

	include_once SharedFunctions::get_template( '/templates/team-member/elc-ldquiz-modal.php' );
endif;

if ( ! isset( $shortcode_name ) ) {
	get_footer();
}
