<?php
/**
 * Displays the current organization and a select menu to change it.
 *
 * @since 1.0.0
 * @version 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use LearnDash\Groups_Plus\Module\Group;

if ( count( $all_primary_groups ) > 0 ) :
	$_get_parent_group_id = null;
	if ( ! empty( $_GET['parent-group-id'] ) ) {
		$_get_parent_group_id = $_GET['parent-group-id'];
	} else {
		$_get_parent_group_id = Group::get_parent_group_id();
	}

	if ( isset( $shortcode_name ) && in_array( $shortcode_name, array( 'learndash_groups_plus_primary_report', 'learndash_groups_plus_report' ), true ) && isset( $_GET['group'] ) && ! empty( $_GET['group'] ) ) {
		$group_id             = general_encrypt_decrypt( 'decrypt', $_GET['group'] );
		$_get_parent_group_id = Group::get_parent_group_id( $group_id );
	}

	$current = array_filter(
		$all_primary_groups,
		function ( $item ) use ( $_get_parent_group_id ) {
			return (int) $_get_parent_group_id === $item->ID;
		}
	);

	$current = array_shift( $current );
	?>
	<div class="groups_plus_admin_header">
		<div class="team-title">
			<?php if ( ! empty( $current ) ) : ?>
				<h2>
					<?php printf( '%s', learndash_get_custom_label( 'organization' ) ); ?>: <?php echo $current ? esc_html( $current->post_title ) : ''; ?>
				</h2>
			<?php endif; ?>
		</div>
		<div class="team-select">
			<label class="title"><?php printf( esc_html__( 'Change %s', 'learndash-groups-plus' ), learndash_get_custom_label( 'organization' ) ); ?>:</label>
			<select name="primary_group_id">
				<option value="">-- <?php printf( esc_html__( 'Select %s', 'learndash-groups-plus' ), learndash_get_custom_label_lower( 'organization' ) ); ?> --</option>
				<?php foreach ( $all_primary_groups as $single_primary_group ) : ?>
					<option value="<?php echo esc_attr( $single_primary_group->ID ); ?>" <?php selected( $_get_parent_group_id, $single_primary_group->ID ); ?>>
						<?php echo esc_html( $single_primary_group->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
<?php endif; ?>
