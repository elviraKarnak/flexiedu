<?php
/**
 * Metabox output for Gradebook Weighting.
 *
 * @since 1.2.0
 *
 * @var array $components
 */

defined( 'ABSPATH' ) || die();
?>

<div class="ld-gb-metabox-gradebook-weighting">

	<div class="notice error inline ld-gb-component-weighting-error-message" style="display: none;">
		<p>
			<?php
			printf(
			/* translators: %s is the current weight inside some HTML */
				__( 'Total weights must equal 100. Current weight is %s.', 'learndash-gradebook' ),
				'<span class="current-weight"></span>'
			);
			?>
		</p>
	</div>

	<?php
	ld_gb_do_field_toggle(
		[
			'name'                      => 'gradebook_weighting_enable',
			'group'                     => 'gradebook-weighting',
			'label'                     => __( 'Enable Gradebook Weighting', 'learndash-gradebook' ),
			'description'               => __( 'If enabled, each Component will have a custom weight used when factoring the final grade. If disabled, all Components will have equal weights.', 'learndash-gradebook' ),
			'description_placement'     => 'after_label',
			'description_tip_alignment' => 'right',
		]
	);
	?>

	<table id="ld-gb-gradebook-weighting"
		<?php
		echo ld_gb_fieldhelpers()->fields->get_field( 'gradebook_weighting_enable' ) !== '1' ?
			'style="display: none;"' : '';
		?>
			>

		<thead class="ld-gb-gradebook-weighting-header">
		<tr>
			<th class="ld-gb-gradebook-weighting-header-name">
				<?php _ex( 'Name', 'Component Weighting: Component Name Label', 'learndash-gradebook' ); ?>
			</th>

			<th class="ld-gb-gradebook-weighting-header-weight">
				<?php _ex( 'Weight %', 'Component Weighting: Component Weight Label', 'learndash-gradebook' ); ?>
			</th>
		</tr>
		</thead>

		<tbody>
		<?php if ( $components ) : ?>
			<?php foreach ( $components as $i => $component ) : ?>
				<?php $weight = $component['weight'] ? esc_attr( $component['weight'] ) : 0; ?>
				<tr class="ld-gb-gradebook-weighting-component" data-id="<?php echo esc_attr( $component['id'] ); ?>">
					<td class="ld-gb-gradebook-weighting-component-name"><?php echo $component['name']; ?></td>
					<td class="ld-gb-gradebook-weighting-component-weight">
						<input type="number" min="0" max="100" value="<?php echo $weight; ?>"
								name="ld_gb_components[<?php echo $i; ?>][weight]">
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		</tbody>

	</table>

</div>