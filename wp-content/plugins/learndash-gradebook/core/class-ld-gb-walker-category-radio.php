<?php
defined( 'ABSPATH' ) || die();

/**
 * Custom walker for switching checkbox inputs to radio.
 *
 * @see Walker_Category_Checklist
 */
class LD_GB_Walker_Category_Radio extends Walker_Category_Checklist {
	function walk( $elements, $max_depth, ...$args ) {
		$output = parent::walk( $elements, $max_depth, $args );
		$output = str_replace(
			[ 'type="checkbox"', "type='checkbox'" ],
			[ 'type="radio"', "type='radio'" ],
			$output
		);

		return $output;
	}
}
