<?php
/**
 * Block: Overall Grade
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_BK_OverallGrade
 *
 * The Overall Grade block.
 *
 * @since 3.0.0
 */
class LD_GB_BK_OverallGrade extends LD_GB_Block {
	/**
	 * LD_GB_Block constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path File path.
	 */
	public function __construct( $path ) {
		parent::__construct( $path );

		add_filter( 'learndash_certificate_builder_allowed_blocks', [ $this, 'allow_block_in_certificate_builder' ] );

		add_filter( 'learndash_certificate_builder_block_fallback', [ $this, 'certificate_builder_output' ], 10, 2 );
	}

	/**
	 * Callback outputted by the dynamic block.
	 *
	 * @since 3.0.0
	 *
	 * @param array<string,mixed> $attributes Block attributes.
	 *
	 * @return string
	 */
	public function render_callback( $attributes ) {
		return LearnDash_Gradebook()->overall_grade->render( $attributes, true );
	}

	/**
	 * Add the Overall Grade block to the list of Blocks supported by the Certificate Builder
	 *
	 * @since 4.3.2
	 *
	 * @param string[] $blocks List of supported Blocks.
	 *
	 * @return string[]
	 */
	public function allow_block_in_certificate_builder( $blocks ) {
		$blocks[] = 'realbigplugins/overall-grade-block';

		return $blocks;
	}

	/**
	 * Ensures the Overall Grade Block outputs correctly within the Certificate Builder
	 *
	 * @since 4.3.2
	 *
	 * @param string                                                                                                                                           $output HTML output in the certificate builder.
	 * @param array{id: string, blockName: string, attrs: array<string, mixed>, innerBlocks: array<string, mixed>[], innerHTML: string, innerContent: mixed[]} $block  The current Block.
	 *
	 * @return string
	 */
	public function certificate_builder_output( $output, $block ) {
		if ( $block['blockName'] !== 'realbigplugins/overall-grade-block' ) {
			return $output;
		}

		return $this->render_callback( $block['attrs'] );
	}
}
