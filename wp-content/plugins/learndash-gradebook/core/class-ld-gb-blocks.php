<?php
/**
 * Manages all plugin blocks.
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_Blocks
 *
 * Outputs the included blocks.
 *
 * @since 3.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/includes
 */
class LD_GB_Blocks {
	/**
	 * All plugin blocks.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	public $blocks = [];

	/**
	 * LD_GB_Blocks constructor.
	 *
	 * @since 3.0.0
	 */
	function __construct() {
		add_filter( 'ld_gb_blocks', [ $this, 'included_blocks' ] );
		add_action( 'init', [ $this, 'init_blocks' ] ); }

	/**
	 * Adds included blocks.
	 *
	 * @since 3.0.0
	 *
	 * @param array<string, LD_GB_Block> $blocks Blocks.
	 *
	 * @return array<string, LD_GB_Block>
	 */
	public function included_blocks( $blocks ) {
		require_once LEARNDASH_GRADEBOOK_DIR . 'core/blocks/frontend-gradebook/class-ld-gb-bk-frontendgradebook.php'; // cspell: disable-line.

		$blocks['ld_gradebook'] = new LD_GB_BK_FrontendGradebook( LEARNDASH_GRADEBOOK_DIR . 'core/blocks/frontend-gradebook/' ); // cspell: disable-line.

		require_once LEARNDASH_GRADEBOOK_DIR . 'core/blocks/overall-grade/class-ld-gb-bk-overallgrade.php'; // cspell: disable-line.

		$blocks['ld_overallgrade'] = new LD_GB_BK_OverallGrade( LEARNDASH_GRADEBOOK_DIR . 'core/blocks/overall-grade/' ); // cspell: disable-line.

		require_once LEARNDASH_GRADEBOOK_DIR . 'core/blocks/report-card/class-ld-gb-bk-reportcard.php'; // cspell: disable-line.

		$blocks['ld_reportcard'] = new LD_GB_BK_ReportCard( LEARNDASH_GRADEBOOK_DIR . 'core/blocks/report-card/' ); // cspell: disable-line.

		return $blocks;
	}

	/**
	 * Initializes all plugin blocks.
	 *
	 * @access public
	 * @since 3.0.0
	 * return void
	 */
	public function init_blocks() {
		/**
		 * Blocks for Gradebook by LearnDash.
		 *
		 * @since 3.0.0
		 */
		$blocks = apply_filters( 'ld_gb_blocks', [] );

		foreach ( $blocks as $id => $block ) {
			$this->blocks[ $id ] = $block;
		}   }
}
