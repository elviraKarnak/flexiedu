<?php
/**
 * Gutenberg Block for [learndash_groups_plus_primary_report].
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore atts preprocess
 */

namespace LearnDash\Groups_Plus\Block;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use LearnDash_Gutenberg_Block;
use WP_Block;

class Groups_Plus_Primary_Report extends LearnDash_Gutenberg_Block {
	 /**
	  * Object constructor
	  */
	public function __construct() {
		$this->shortcode_slug   = 'learndash_groups_plus_primary_report';
		$this->block_slug       = 'ld-groups-plus-primary-report';
		$this->block_dir        = LEARNDASH_GROUPS_PLUS_DIR . 'build/resources/blocks/ld-groups-plus-primary-report';
		$this->block_attributes = array(
			'preview_show' => array(
				'type' => 'boolean',
			),
		);
		$this->self_closing     = true;
		$this->init();
	}

	/**
	 * Render Block
	 *
	 * This function is called per the register_block_type() function above. This function will output
	 * the block rendered content.
	 *
	 * @param array         $attributes     Shortcode attributes.
	 * @param string        $block_content
	 * @param WP_Block|null $block          Block object.
	 * @return void The output is echoed.
	 */
	public function render_block( $attributes = array(), $block_content = '', WP_Block $block = null ) {
		$attributes = $this->preprocess_block_attributes( $attributes );

		$attributes = apply_filters( 'learndash_block_markers_shortcode_atts', $attributes, $this->shortcode_slug, $this->block_slug, '' );

		$shortcode_params_str = '';
		foreach ( $attributes as $key => $val ) {
			if ( is_null( $val ) ) {
				continue;
			}

			if ( is_array( $val ) ) {
				$val = implode( ',', $val );
			}

			if ( ! empty( $shortcode_params_str ) ) {
				$shortcode_params_str .= ' ';
			}
			$shortcode_params_str .= $key . '="' . esc_attr( $val ) . '"';
		}

		$shortcode_params_str = '[' . $this->shortcode_slug . ' ' . $shortcode_params_str . ']';
		$shortcode_out        = do_shortcode( $shortcode_params_str );

		if ( ( empty( $shortcode_out ) ) ) {
			$shortcode_out = '[' . $this->shortcode_slug . '] placeholder output.';
		}

		return $this->render_block_wrap( $shortcode_out, true );
	}

	/**
	 * Called from the LD function learndash_convert_block_markers_shortcode() when parsing the block content.
	 *
	 * @since 2.0
	 *
	 * @param array  $attributes The array of attributes parse from the block content.
	 * @param string $shortcode_slug This will match the related LD shortcode ld_profile, ld_course_list, etc.
	 * @param string $block_slug This is the block token being processed. Normally same as the shortcode but underscore replaced with dash.
	 * @param string $content This is the original full content being parsed.
	 *
	 * @return array $attributes.
	 */
	public function learndash_block_markers_shortcode_atts_filter( $attributes = array(), $shortcode_slug = '', $block_slug = '', $content = '' ) {
		if ( $shortcode_slug === $this->shortcode_slug ) {
			if ( isset( $attributes['preview_show'] ) ) {
				unset( $attributes['preview_show'] );
			}

			foreach ( $attributes as $key => $value ) {
				if ( is_array( $value ) ) {
					$attributes[ $key ] = implode( ', ', $value );
				} elseif ( is_string( $value ) ) {
					// Remove quotes to prevent the attributes from being stripped out.
					$attributes[ $key ] = str_replace( array( '"', '\'' ), '', $attributes[ $key ] );
				}
			}
		}

		return $attributes;
	}
}
