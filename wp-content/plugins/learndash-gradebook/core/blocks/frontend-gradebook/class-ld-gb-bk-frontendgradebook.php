<?php
/**
 * Block: Frontend Gradebook
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes/blocks
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_BK_FrontendGradebook
 *
 * The Frontend Gradebook block.
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes/blocks
 */

class LD_GB_BK_FrontendGradebook extends LD_GB_Block {
	/**
	 * LD_GB_Block constructor.
	 *
	 * @since 3.0.0
	 *
	 * @param string $path
	 */
	function __construct( $path ) {
		parent::__construct( $path );   }

	/**
	 * Callback outputted by the dynamic block.
	 *
	 * @since 3.0.0
	 */
	function render_callback( $attributes ) {
		return LearnDash_Gradebook()->frontend_gradebook->render( $attributes, true );  }
}
