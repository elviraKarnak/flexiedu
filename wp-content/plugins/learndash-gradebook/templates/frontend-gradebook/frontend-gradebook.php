<?php
/**
 * Template for the Dashboard
 *
 * @since 2.0.0
 * @updated 4.2.0
 * @version 4.3.2
 *
 * @var integer         $gradebook_id
 * @var string          $grade_format
 * @var bool            $is_block
 *
 * @package LearnDash_Gradebook
 */

defined( 'ABSPATH' ) || die();

// We check against our custom View Gradebook capability rather than whether they are a Group Leader (by Role or by actually being assigned as a Group Leader for a Group) because anyone with the proper capabilities can view the backend Gradebook.
if ( ! is_user_logged_in() || ! current_user_can( 'view_gradebook' ) ) {
	ld_gb_locate_template(
		'frontend-gradebook/errors/no-access.php',
		[]
	);

	return;
}

// We're using the same filter here so that anyone who was filtering this on the Backend will be able to easily here too
// If you do not want your filter to impact the frontend, put a check for is_admin() in your callback
/** This filter is documented in core/api/class-ld-gb-api.php */
$gradebook_query_args = apply_filters(
	'ld_gb_adminpage_gradebook_select_query_args', // cspell:disable-line.
	[
		'post_type'      => 'gradebook',
		'posts_per_page' => - 1,
		'post_status'    => 'publish',
	]
);

// We only care about the Post IDs
$gradebook_query_args['fields'] = 'ids';

$gradebook_ids = [];

$gradebook_query = new WP_Query( $gradebook_query_args );
if ( $gradebook_query->have_posts() ) {
	$gradebook_ids = $gradebook_query->posts;
}

// If they haven't made a single Gradebook, go no further
if ( empty( $gradebook_ids ) ) {
	ld_gb_locate_template(
		'frontend-gradebook/errors/no-gradebooks.php',
		[]
	);

	return;
}

// Default to the first Group ID if one is not found
if ( ! isset( $gradebook_id ) || $gradebook_id === false ) {
	$gradebook_id = $gradebook_ids[0];
}

// Get all the Group IDs of Groups they are Group Leaders of ("Administrators" here is a bit of a misnomer if you double-check how it is implemented in LD)
// For Administrators this will grab all Groups
$group_ids = learndash_get_administrators_group_ids( get_current_user_id() );

// Admins are able to choose to view All Users
if ( ld_gb_is_super_admin() ) {
	$group_ids = array_merge( [ 0 ], $group_ids );
}

// Default to the first Group ID if one is not found
if ( ( ! isset( $group_id ) || $group_id === false ) && isset( $group_ids[0] ) ) {
	$group_id = $group_ids[0];
} else {
	$group_id = 0;
}

// Set the wrapper class using the block attributes if it's a block
if ( $is_block ) {
	$class = 'ld-gb-frontend-gradebook wp-block-frontend-gradebook-block';

	$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $class ] );
} else {
	$wrapper_attributes = 'class="ld-gb-frontend-gradebook"';
}

$timezone = get_option( 'timezone_string', 'America/Detroit' );

$timezone_offset = 0;
if ( strpos( $timezone, '/' ) === false ) {
	$timezone = str_replace( 'UTC-', '', $timezone );

	if ( $timezone ) {
		$timezone_offset = $timezone * HOUR_IN_SECONDS;
	}
} else {
	$dateTimeZone    = new DateTimeZone( $timezone );
	$timezone_offset = $dateTimeZone->getOffset( new DateTime( 'now', null ) );
}

$timestamp_format = get_option( 'date_format', 'F j, Y' ) . ' @ ' . get_option( 'time_format', 'g:i a' );

// Append timezone offset
if ( strpos( $timestamp_format, 'T' ) === false ) {
	$timestamp_format .= ' T';
}

?>

<div <?php echo $wrapper_attributes; ?>>

	<?php wp_nonce_field( 'wp_rest' ); ?>

	<div class="gradebook-container">

		<?php

		/**
		 * Gradebook Selection Dropdown
		 *
		 * @since 2.0.0
		 *
		 * @hooked LD_GB_SC_FrontendGradebook::gradebook_dropdown() 10
		 */
		do_action( 'ld_gb_frontend_gradebook_gradebook_dropdown', $gradebook_ids, $gradebook_id );

		/**
		 * Group Selection Dropdown
		 *
		 * @since 2.0.0
		 *
		 * @hooked LD_GB_SC_FrontendGradebook::group_dropdown() 10
		 */
		do_action( 'ld_gb_frontend_gradebook_group_dropdown', $group_ids, $group_id );

		$grade_format = apply_filters( 'ld_gb_frontend_gradebook_grade_format', $grade_format, $gradebook_id, $group_id );

		/**
		 * Gradebook Results
		 * On first load, this will be the default Gradebook and Group Combo
		 * This same hook is called when loading a new Gradebook or changing Groups
		 *
		 * @hooked LD_GB_SC_FrontendGradebook::change_gradebook() 10
		 */
		do_action( 'ld_gb_frontend_gradebook_results', $gradebook_id, $group_id, $grade_format );

		?>

	</div>

	<?php

	/**
	 * Outputs the Edit Panel
	 * On first load, this will not be populated by any data
	 *
	 * @hooked LD_GB_SC_FrontendGradebook::edit_panel() 10
	 */
	do_action( 'ld_gb_frontend_gradebook_edit_panel', 0, $gradebook_id, $group_id, $grade_format );

	?>

</div>
