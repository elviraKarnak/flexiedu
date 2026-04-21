<?php
/**
 * Dashboard widget HTML.
 *
 * @since 1.1.0
 *
 * @var array $gradebooks
 * @var array $groups
 * @var array $data
 */

defined( 'ABSPATH' ) || die();
?>

<h2 class="ld-gb-widget-gradebook-title">
	<?php
	printf(
		_x( 'Showing Gradebook for: %s', 'Gradebook Dashboard Widget Title. %s is the same of the current Gradebook', 'learndash-gradebook' ),
		'<span class="ld-gb-widget-gradebook-name">' . esc_attr( $data['gradebook']['name'] ) . '</span>'
	);
	?>
</h2>

<?php if ( count( $gradebooks ) > 1 ) : ?>
	<label>
		<?php _ex( 'Change Gradebook to:', 'Gradebook Dashboard Widget Gradebook dropdown label', 'learndash-gradebook' ); ?>

		<select name="ld_gb_widget_gradebook">
			<?php foreach ( $gradebooks as $gradebook ) : ?>
				<option value="<?php echo $gradebook->ID; ?>">
					<?php echo $gradebook->post_title; ?>
				</option>
			<?php endforeach; ?>
		</select>
	</label>
	<br/>
<?php endif; ?>

<?php if ( count( $groups ) > 1 ) : ?>
	<label>
		<?php _ex( 'Change Group to:', 'Gradebook Dashboard Widget Group dropdown label', 'learndash-gradebook' ); ?>

		<select name="ld_gb_widget_group">
			<?php foreach ( $groups as $group ) : ?>
				<option value="<?php echo $group->ID; ?>">
					<?php echo $group->post_title; ?>
				</option>
			<?php endforeach; ?>
		</select>
	</label>
<?php endif; ?>

<div class="ld-gb-widget-users-container">
	<table class="ld-gb-widget-users">
		<tbody>

		<tr class="ld-gb-widget-user-template" data-user-id="0">
			<td class="ld-gb-widget-user-name"></td>
			<td class="ld-gb-widget-user-grade"></td>
		</tr>

		<?php if ( isset( $data['users'] ) && $data['users'] ) : ?>

			<?php foreach ( $data['users'] as $user_ID => $user ) : ?>
				<tr data-user-id="<?php echo $user_ID; ?>">
					<td class="ld-gb-widget-user-name">
						<?php echo esc_attr( $user['name'] ); ?>
					</td>

					<td class="ld-gb-widget-user-grade">
						<?php LD_GB_UserGrade::display_grade_html( $user['grade'] ); ?>
					</td>
				</tr>
			<?php endforeach; ?>

		<?php endif; ?>
		</tbody>
	</table>
</div>

<p class="description">
	<?php
	printf(
		_x( 'For more detailed information on all users, visit the %1$sGradebook Page%2$s.', 'Both %s are HTML for a link to the main Gradebook page', 'learndash-gradebook' ),
		'<a href="' . admin_url( 'admin.php?page=learndash-gradebook' ) . '">',
		'</a>'
	);
	?>
</p>
