<?php
/**
 * Base module class file.
 *
 * @since 1.1.0
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore WPINC
 */

namespace LearnDash\Groups_Plus\Module;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Module base class.
 *
 * @since 1.1.0
 */
abstract class Base {
	/**
	 * Construct method.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		if ( method_exists( $this, 'hook_actions' ) ) {
			$this->hook_actions();
		}

		if ( method_exists( $this, 'hook_filters' ) ) {
			$this->hook_filters();
		}

		if ( method_exists( $this, 'hook_ajax' ) ) {
			$this->hook_ajax();
		}
	}

	/**
	 * Method to contain function call related to action hooks.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	protected function hook_actions(): void {}

	/**
	 * Method to contain function call related to filter hooks.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	protected function hook_filters(): void {}

	/**
	 * Method to contain function call related to AJAX hooks.
	 *
	 * @since 1.1.0
	 *
	 * @return void
	 */
	protected function hook_ajax(): void {}
}
