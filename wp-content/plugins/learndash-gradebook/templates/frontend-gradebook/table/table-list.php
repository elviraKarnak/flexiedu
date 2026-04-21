<?php
/**
 * Template for the Gradebook List
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

<?php

	// Due to the amount of data that can potentially be handled by Gradebook, we need to hit an API endpoint to populate the table past the first page
	// Unlike the backend Gradebook, the frontend Gradebook shows all Data at once (Or rather holds it all in browser memory) so all the data will need to be gathered and displayed on one page
	// So, we will only query the first page of results on Page Load and then each subsequent page will be entered as an API endpoint is hit to grab data for each one

	$per_page = apply_filters( 'ld_gb_frontend_gradebook_per_page', 15, $gradebook_id, $group_id );

	// Grab the first page's data
	$data = learndash_gradebook_get_gradebook_data(
		$gradebook_id,
		$group_id,
		[
			'per_page' => $per_page,
		]
	);

	ld_gb_update_largest_total_users( $data['query']->total_users );

	$pages = ceil( $data['query']->total_users / $per_page );

	$users        = $data['grades'];
	$user_columns = learndash_gradebook_get_user_columns( $gradebook_id, $group_id );

	$components = ld_gb_get_field( 'components', $gradebook_id );

	?>

<div id="<?php echo 'ld-gb-frontend-gradebook-list-' . $gradebook_id . '-' . $group_id; ?>" class="ld-gb-frontend-gradebook-list-container" data-total_pages="<?php echo esc_attr( $pages ); ?>" data-per_page="<?php echo esc_attr( $per_page ); ?>" data-grade_format="<?php echo esc_attr( $grade_format ); ?>">

	<input type="text" class="search" placeholder="<?php _e( 'Search Users...', 'learndash-gradebook' ); ?>" />

	<div class="ld-gb-frontend-gradebook-table-container">

		<table class="ld-gb-frontend-gradebook-list">

			<thead>
				<tr>
					<?php do_action( 'ld_gb_frontend_gradebook_table_head', $user_columns, $components, $gradebook_id, $group_id ); ?>
				</tr>
			</thead>

			<tbody class="ld-gb-frontend-gradebook-list-tbody">

				<?php foreach ( $users as $user_id => $user_grade ) : ?>

					<tr>

						<?php do_action( 'ld_gb_frontend_gradebook_table_row', $user_id, $user_columns, $user_grade, $grade_format, $components, $gradebook_id, $group_id ); ?>

					</tr>

				<?php endforeach; ?>

				<tr class="dummy">

					<?php do_action( 'ld_gb_frontend_gradebook_table_row', 0, $user_columns, [], $grade_format, $components, $gradebook_id, $group_id ); ?>

				</tr>

			</tbody>

		</table>

	</div>

	<ul class="pagination"></ul>

	<span class="loading-icon dashicons dashicons-update" aria-label="<?php esc_attr_e( 'Loading...', 'learndash-gradebook' ); ?>" style="display: none;"></span>

	<div class="total-students">
		<?php printf( _n( '%d User', '%d Total Users', $data['query']->total_users, 'learndash-gradebook' ), $data['query']->total_users ); ?>
	</div>

</div>
