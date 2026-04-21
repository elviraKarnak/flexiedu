<?php
/**
 * Block class framework
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */

use LearnDash\Core\Utilities\Cast;

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_Block
 *
 * Template for each block.
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */
abstract class LD_GB_Block {
	/**
	 * The block ID (tag).
	 *
	 * @since 3.0.0
	 *
	 * @var string $path
	 */
	public $path;

	/**
	 * LD_GB_Block constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path
	 */
	function __construct( $path ) {
		$this->path = $path;

		add_action( 'init', [ $this, 'register_ld_block' ], 11 );   }

	/**
	 * Registers the block, loading the built assets from another directory
	 *
	 * @since 3.0.0
	 */
	public function register_ld_block() {
		$block_file = trailingslashit( $this->path ) . 'block.json';

		$block_meta = file_get_contents( $block_file );
		$block_meta = json_decode( $block_meta, true );

		$handle = $block_meta['editorScript'];

		$script_asset_file = trailingslashit( LEARNDASH_GRADEBOOK_DIR ) . "dist/gutenberg/{$handle}/index.asset.php";

		if ( ! is_file( $script_asset_file ) ) {
			return;
		}

		$script_asset = require $script_asset_file;

		wp_register_script(
			$handle,
			trailingslashit( LEARNDASH_GRADEBOOK_URI ) . "dist/gutenberg/{$handle}/index" . learndash_min_asset() . '.js',
			$script_asset['dependencies'],
			defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LEARNDASH_GRADEBOOK_VERSION,
			true
		);

		if ( isset( $block_meta['editorStyle'] ) ) {
			$handle = Cast::to_string( $block_meta['editorStyle'] );

			wp_register_style(
				$handle,
				trailingslashit( LEARNDASH_GRADEBOOK_URI ) . "dist/gutenberg/{$handle}/index" . learndash_min_asset() . '.css',
				[],
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LEARNDASH_GRADEBOOK_VERSION
			);

			wp_style_add_data( $handle, 'rtl', 'replace' );
		}

		register_block_type_from_metadata(
			$this->path,
			[
				'render_callback' => [ $this, 'render_callback' ],
			]
		);
	}

	function render_callback( $attributes ) {
		// Override me
	}
}
