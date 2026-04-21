<?php
/**
 * Adds helper functions for easy use of Field Helpers.
 *
 * @since 1.2.0
 */

defined( 'ABSPATH' ) || die();

/**
 * Quick access to plugin field helpers.
 *
 * @since 1.2.0
 *
 * @return RBM_FieldHelpers
 */
function ld_gb_fieldhelpers() {
	return LearnDash_Gradebook()->field_helpers;
}

/**
 * Initializes a field group for automatic saving.
 *
 * @since 1.2.0
 *
 * @param $group
 */
function ld_gb_init_field_group( $group ) {
	ld_gb_fieldhelpers()->fields->save->initialize_fields( $group );
}

/**
 * Gets a meta field helpers field.
 *
 * @since 1.2.0
 *
 * @param string     $name Field name.
 * @param string|int $post_ID Optional post ID.
 * @param mixed      $default Default value if none is retrieved.
 * @param array      $args
 *
 * @return mixed Field value
 */
function ld_gb_get_field( $name, $post_ID = false, $default = '', $args = [] ) {
	$value = ld_gb_fieldhelpers()->fields->get_meta_field( $name, $post_ID, $args );

	return $value !== false ? $value : $default;
}

/**
 * Gets a option field helpers field.
 *
 * @since 1.2.0
 *
 * @param string $name Field name.
 * @param mixed  $default Default value if none is retrieved.
 * @param array  $args
 *
 * @return mixed Field value
 */
function ld_gb_get_option_field( $name, $default = '', $args = [] ) {
	$value = ld_gb_fieldhelpers()->fields->get_option_field( $name, $args );

	return ( $value !== false && $value !== '' ) ? $value : $default;
}

/**
 * Outputs a text field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_text( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_text( $args['name'], $args );
}

/**
 * Outputs a textarea field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_textarea( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_textarea( $args['name'], $args );
}

/**
 * Outputs a checkbox field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_checkbox( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_checkbox( $args['name'], $args );
}

/**
 * Outputs a toggle field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_toggle( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_toggle( $args['name'], $args );
}

/**
 * Outputs a radio field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_radio( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_radio( $args['name'], $args );
}

/**
 * Outputs a select field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_select( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_select( $args['name'], $args );
}

/**
 * Outputs a number field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_number( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_number( $args['name'], $args );
}

/**
 * Outputs an image field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_media( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_media( $args['name'], $args );
}

/**
 * Outputs a datepicker field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_datepicker( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_datepicker( $args['name'], $args );
}

/**
 * Outputs a timepicker field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_timepicker( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_timepicker( $args['name'], $args );
}

/**
 * Outputs a datetimepicker field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_datetimepicker( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_datetimepicker( $args['name'], $args );
}

/**
 * Outputs a colorpicker field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_colorpicker( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_colorpicker( $args['name'], $args );
}

/**
 * Outputs a list field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_list( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_list( $args['name'], $args );
}

/**
 * Outputs a hidden field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_hidden( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_hidden( $args['name'], $args );
}

/**
 * Outputs a table field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_table( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_table( $args['name'], $args );
}

/**
 * Outputs a HTML field.
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function ld_gb_do_field_html( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_html( $args['name'], $args );
}

/**
 * Outputs a repeater field.
 *
 * @since 1.2.0
 *
 * @param mixed $values
 */
function ld_gb_do_field_repeater( $args = [] ) {
	ld_gb_fieldhelpers()->fields->do_field_repeater( $args['name'], $args );
}

/**
 * Gets a field description tip.
 *
 * @since 1.2.0
 *
 * @param string $description Description text.
 */
function ld_gb_get_field_tip( $description ) {
	ob_start();
	?>
	<div class="fieldhelpers-field-description fieldhelpers-field-tip">
		<span class="fieldhelpers-field-tip-toggle dashicons dashicons-editor-help" data-toggle-tip></span>
		<p class="fieldhelpers-field-tip-text">
			<?php echo $description; ?>
		</p>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Outputs a field description tip.
 *
 * @since 1.2.0
 *
 * @param string $description Description text.
 */
function ld_gb_field_tip( $description ) {
	echo ld_gb_get_field_tip( $description );
}