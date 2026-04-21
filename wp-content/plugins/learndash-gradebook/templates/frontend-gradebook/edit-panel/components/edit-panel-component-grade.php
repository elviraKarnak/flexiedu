<?php
/**
 * Template for the Edit Panel Component Grade
 *
 * @since 2.0.0
 * @updated 4.1.2
 * @version 4.3.2

 *
 * @var array           $component
 * @var string          $grade_format
 * @var integer         $user_id
 * @var integer         $gradebook_id
 * @var integer         $group_id
 *
 * @package LearnDash_Gradebook
 *
 * @cspell:ignore alignright alignleft .
 */

defined( 'ABSPATH' ) || die();
?>

<div class="ld-gb-frontend-gradebook-component-grade-container">

	<div class="ld-gb-frontend-gradebook-component-alignment">

		<div class="ld-gb-frontend-gradebook-component-name alignleft">
			<?php echo $component['name']; ?>
		</div>

		<div class="ld-gb-frontend-gradebook-component-grade alignright" data-component_id="<?php echo esc_attr( $component['id'] ); ?>">

			<?php if ( ! ld_gb_get_option_field( 'disable_component_override', false ) ) : ?>

				<?php do_action( 'ld_gb_frontend_gradebook_edit_panel_component_override', $component, $grade_format, $user_id, $gradebook_id, $group_id ); ?>

			<?php endif; ?>

			<span class="ld-gb-frontend-gradebook-component-grade-content">
				<?php echo learndash_gradebook_get_grade_display( $component['averaged_score'], $grade_format ); ?>
			</span>

		</div>

	</div>

	<?php if ( ! ld_gb_get_option_field( 'disable_component_override', false ) && $component['overridden'] ) : ?>
		<div class="ld-gb-gradebook-component-overridden-notice callout warning">
			<p>
				<?php _e( 'This Component Grade is being overridden.', 'learndash-gradebook' ); ?>
			</p>
		</div>
	<?php endif; ?>

</div>
