<?php
/**
 * Register all actions and filters for the plugin
 *
 * @link https://learndash.com
 * @since 1.0.0
 *
 * @package LearnDash\Instructor_Role
 */

namespace InstructorRole\Includes;

defined( 'ABSPATH' ) || exit;

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */
class Instructor_Role_Loader {
	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since 3.5.0
	 * @access protected
	 * @var array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since 3.5.0
	 * @access protected
	 * @var array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * List of default active plugin modules
	 *
	 * @since 3.5.0
	 * @var array       Array of default plugin modules.
	 */
	protected $default_modules;

	/**
	 * The meta key for storing list of modules
	 *
	 * @since 3.5.0
	 * @var array   $modules_meta_key   The meta key used to store list of active modules in options table.
	 */
	protected $modules_meta_key;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {
		$this->actions = [];
		$this->filters = [];

		// List of default plugin modules.
		$this->default_modules = [
			'multiple_instructors',
			'comments',
			'reports',
			'groups',
			'notifications',
			'payouts',
			'profile',
			'emails',
			'woocommerce',
			'learndash_handler',
			'learndash_menu_handler',
			'commission',
			'settings',
			'review',
			'student_communication',
			'frontend_dashboard',
			'dashboard_block',
		];

		if ( defined( 'IR_CORE_MODULES_META_KEY' ) ) {
			$this->modules_meta_key = IR_CORE_MODULES_META_KEY;
		} else {
			$this->modules_meta_key = 'ir_active_modules';
		}
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since 3.5.0
	 * @param string $hook             The name of the WordPress action that is being registered.
	 * @param object $component        A reference to the instance of the object on which the action is defined.
	 * @param string $callback         The name of the function definition on the $component.
	 * @param int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since 3.5.0
	 * @param string $hook             The name of the WordPress filter that is being registered.
	 * @param object $component        A reference to the instance of the object on which the filter is defined.
	 * @param string $callback         The name of the function definition on the $component.
	 * @param int    $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param int    $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since 3.5.0
	 * @access private
	 * @param array  $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param string $hook             The name of the WordPress filter that is being registered.
	 * @param object $component        A reference to the instance of the object on which the filter is defined.
	 * @param string $callback         The name of the function definition on the $component.
	 * @param int    $priority         The priority at which the function should be fired.
	 * @param int    $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = [
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		];

		return $hooks;
	}

	/**
	 * Save the list of active plugin modules
	 *
	 * @since 3.5.0
	 */
	public function set_default_active_modules() {
		update_option( $this->modules_meta_key, $this->default_modules );
	}

	/**
	 * Fetch the list of active plugin modules
	 *
	 * @since 3.5.0
	 */
	public function fetch_active_modules() {
		return get_option( $this->modules_meta_key );
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since 3.5.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], [ $hook['component'], $hook['callback'] ], $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], [ $hook['component'], $hook['callback'] ], $hook['priority'], $hook['accepted_args'] );
		}
	}
}
