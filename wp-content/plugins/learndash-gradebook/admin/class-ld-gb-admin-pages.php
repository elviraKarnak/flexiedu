<?php
/**
 * Sets up all LD admin pages.
 *
 * @since 1.1.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_AdminPages
 *
 * Sets up all LD admin pages.
 *
 * @since 1.1.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */
class LD_GB_AdminPages {
	/**
	 * LD_GB_AdminPages constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_submenu_pages' ], 998 );
		add_filter( 'learndash_submenu', [ $this, 'add_submenu_item' ], 30 );
		add_filter( 'learndash_admin_tabs', [ $this, 'add_tabs' ] );
		add_filter( 'learndash_admin_tabs_on_page', [ $this, 'setup_tabs' ] );

		add_action( 'admin_menu', [ $this, 'move_edit_gradebooks' ], 999 );

		// Fakes the Current Menu Item
		add_filter( 'parent_file', [ $this, 'fix_parent_file' ] );

		// Fakes the current Submenu Item
		add_filter( 'submenu_file', [ $this, 'fix_submenu_file' ], 10, 2 ); }

	/**
	 * Adds the submenu page.
	 *
	 * @since 1.1.0
	 * @access private
	 */
	function add_submenu_pages() {
		global $submenu;

		// Check to see if the LD menu is even loaded.
		if ( ! learndash_is_admin_user() &&
			! learndash_is_group_leader_user() &&
			current_user_can( 'view_gradebook' ) && // Ensure we don't accidentally add the Menu Item for users who cannot View the Gradebook.
			! isset( $submenu['learndash-lms'] ) ) { // See if somehow else the Top-Level item was already added

			// Gradebook page viewable for those who can't manage options
			add_menu_page(
				_x( 'Gradebook', 'Gradebook Page Title', 'learndash-gradebook' ), // Page Title
				_x( 'Gradebook', 'Gradebook Menu Title', 'learndash-gradebook' ), // Menu Title
				'view_gradebook', // Capability
				'learndash-gradebook', // Menu Slug
				[ __CLASS__, 'admin_page' ], // Callback
				'dashicons-welcome-learn-more' // Icon
			);
		} else {
			// Gradebook page
			add_submenu_page(
				'learndash-gradebook', // Parent Slug
				_x( 'Gradebook', 'Gradebook Page Title', 'learndash-gradebook' ), // Page Title
				_x( 'Gradebook', 'Gradebook Menu Title', 'learndash-gradebook' ), // Menu Title
				'view_gradebook', // Capability
				'learndash-gradebook', // Menu Slug
				[ __CLASS__, 'admin_page' ] // Callback
			);
		}

		// Settings page
		add_submenu_page(
			'learndash-gradebook-settings',
			_x( 'Gradebook by LearnDash Settings', 'Gradebook by LearnDash Settings Page Title', 'learndash-gradebook' ),
			_x( 'Gradebook by LearnDash Settings', 'Gradebook by LearnDash Settings Menu Title', 'learndash-gradebook' ),
			( defined( 'LEARNDASH_ADMIN_CAPABILITY_CHECK' ) && LEARNDASH_ADMIN_CAPABILITY_CHECK != '' ) ? LEARNDASH_ADMIN_CAPABILITY_CHECK : 'manage_options',
			'learndash-gradebook-settings',
			[ __CLASS__, 'admin_page' ]
		);

		// User edit grade page
		add_submenu_page(
			'learndash-gradebook-user-grades',
			_x( 'User Grades', 'User Grades Page Title', 'learndash-gradebook' ),
			_x( 'User Grades', 'User Grades Menu Title', 'learndash-gradebook' ),
			'view_gradebook',
			'learndash-gradebook-user-grades',
			[ __CLASS__, 'admin_page' ]
		);
	}

	/**
	 * In order for Non-Admins to Add/Edit Gradebooks via the GUI, the Gradebook CPT has to have show_in_menu set to true, but we don't want to show it in the menu
	 * This little hack gets around that
	 *
	 * @since 1.4.0
	 * @access public
	 * @return void
	 */
	public function move_edit_gradebooks() {
		global $menu, $submenu;

		if ( ! learndash_is_admin_user() ) {
			if ( ! isset( $submenu['learndash-gradebook'] ) ) {
				// Add a Sub-Menu item to ensure that our Sub-Menu doesn't get weird
				$submenu['learndash-gradebook'][] = [
					_x( 'Gradebook', 'Gradebook Sub-Menu Label.', 'learndash-gradebook' ),
					'view_gradebook',
					'admin.php?page=learndash-gradebook',
				];
			}

			if ( isset( $submenu['edit.php?post_type=gradebook'] ) ) {
				// Move the Edit Submenu Item
				$submenu['learndash-gradebook'] = array_merge( $submenu['learndash-gradebook'], $submenu['edit.php?post_type=gradebook'] );

				// Remove the Submenu Items
				unset( $submenu['edit.php?post_type=gradebook'] );
			}
		}

		$settings_index = null;
		foreach ( $menu as $key => $menu_item ) {
			// Index 2 is always the page slug
			if ( $menu_item[2] == 'edit.php?post_type=gradebook' ) {
				$settings_index = $key;
				break;
			}
		}

		// Delete the Top-Level Menu Item
		unset( $menu[ $settings_index ] );  }

	/**
	 * Fakes the Current Menu Item
	 *
	 * @param string $parent_file Parent Menu Item
	 *
	 * @access public
	 * @since 1.0.0
	 * @return string Modified String
	 */
	public function fix_parent_file( $parent_file ) {
		if ( learndash_is_admin_user() ) {
			return $parent_file;
		}

		global $current_screen;

		if ( $current_screen->base == 'toplevel_page_learndash-gradebook' ) {
			// Ensures that the "Gradebook" tab is properly focused for users who cannot edit Gradebook Settings
			$current_screen->id = 'admin_page_learndash-gradebook';
		}

		return $parent_file;    }

	/**
	 * Fakes the current Submenu Item
	 *
	 * @param string $submenu_file Current Menu Item
	 * @param string $parent_file  Parent Menu Item
	 *
	 * @access public
	 * @since 1.4.0
	 * @return string Modified String
	 */
	public function fix_submenu_file( $submenu_file, $parent_file ) {
		if ( learndash_is_admin_user() ) {
			return $submenu_file;
		}

		global $current_screen;

		if ( $current_screen->base == 'toplevel_page_learndash-gradebook' ) {
			// Ensures that the Gradebook submenu item is properly focused for users who cannot edit Gradebook Settings
			$submenu_file = 'admin.php?page=learndash-gradebook';
		}

		return $submenu_file;   }

	/**
	 * Adds the Gradebooks submenu item to the LD submenu. Adds after "Assignments".
	 *
	 * @param array $submenu Old submenu
	 *
	 * @return array New submenu
	 */
	function add_submenu_item( $submenu ) {
		if ( ! current_user_can( 'view_gradebook' ) ) {
			return $submenu;
		}

		$new_submenu = [];

		foreach ( $submenu as $submenu_item ) {
			$new_submenu[] = $submenu_item;

			if ( $submenu_item['link'] == 'edit.php?post_type=sfwd-assignment' ) {
				$gradebook_menu_label = _x( 'Gradebook', 'Gradebook Sub-Menu Label.', 'learndash-gradebook' );

				$new_submenu[] = [
					'name' => $gradebook_menu_label,
					'cap'  => 'view_gradebook',
					'link' => 'admin.php?page=learndash-gradebook',
				];
			}
		}

		return $new_submenu;
	}

	/**
	 * Adds the tab to the Gradebook page.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @param array $tabs
	 */
	function add_tabs( $tabs ) {
		$current_screen = get_current_screen();

		switch ( $current_screen->id ) {
			case 'gradebook':
			case 'admin_page_learndash-gradebook-user-grades':
				$manage_gradebooks_ID = $current_screen->id;
				break;

			default:
				$manage_gradebooks_ID = 'edit-gradebook';
		}

		$tabs[250] = [
			'link'      => 'admin.php?page=learndash-gradebook',
			'name'      => _x( 'Gradebook', 'Gradebook Admin Page Tab Label', 'learndash-gradebook' ),
			'id'        => 'admin_page_learndash-gradebook',
			'menu_link' => 'admin.php?page=learndash-gradebook',
			'cap'       => 'view_gradebook',
		];

		if ( ! current_user_can( 'edit_gradebooks' ) ) {
			return $tabs;
		}

		$tabs[255] = [
			'link'      => 'edit.php?post_type=gradebook',
			'name'      => _x( 'Manage Gradebooks', 'Manage Gradebooks Admin Page Tab Label', 'learndash-gradebook' ),
			'id'        => $manage_gradebooks_ID,
			'menu_link' => 'admin.php?page=learndash-gradebook',
			'cap'       => 'edit_gradebooks',
		];

		if ( ! learndash_is_admin_user() ) {
			return $tabs;
		}

		$settings_label = _x( 'Settings', 'Settings Admin Page Tab Label', 'learndash-gradebook' );

		$tabs[260] = [
			'link'      => 'admin.php?page=learndash-gradebook-settings',
			'name'      => $settings_label,
			'id'        => 'admin_page_learndash-gradebook-settings',
			'menu_link' => 'admin.php?page=learndash-gradebook',
			'cap'       => 'manage_options',
		];

		return $tabs;
	}

	/**
	 * Adds our tab to the proper page.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @param array $tabs_on_page
	 *
	 * @return array
	 */
	function setup_tabs( $tabs_on_page ) {
		$current_screen = get_current_screen();

		switch ( $current_screen->id ) {
			case 'gradebook':
			case 'admin_page_learndash-gradebook-user-grades':
			case 'toplevel_page_learndash-gradebook':
				$manage_gradebooks_ID = $current_screen->id;
				break;

			default:
				$manage_gradebooks_ID = 'edit-gradebook';
		}

		$tabs = [];

		$tabs[] = 250; // Gradebook

		if ( ! current_user_can( 'edit_gradebooks' ) ) {
			$tabs_on_page['admin_page_learndash-gradebook'] = $tabs;
			$tabs_on_page[ $manage_gradebooks_ID ]          = $tabs;

			$tabs_on_page['admin_page_learndash-gradebook-settings']  = $tabs;
			$tabs_on_page['admin_page_learndash-gradebook-edit-user'] = $tabs;

			return $tabs_on_page;
		}

		$tabs[] = 255; // Manage Gradebooks

		if ( ! learndash_is_admin_user() ) {
			$tabs_on_page['admin_page_learndash-gradebook'] = $tabs;
			$tabs_on_page[ $manage_gradebooks_ID ]          = $tabs;

			$tabs_on_page['admin_page_learndash-gradebook-settings']  = $tabs;
			$tabs_on_page['admin_page_learndash-gradebook-edit-user'] = $tabs;

			return $tabs_on_page;
		}

		$tabs[] = 260; // Settings

		// Add to new tab
		$tabs_on_page['admin_page_learndash-gradebook'] = $tabs;
		$tabs_on_page[ $manage_gradebooks_ID ]          = $tabs;

		$tabs_on_page['admin_page_learndash-gradebook-settings']  = $tabs;
		$tabs_on_page['admin_page_learndash-gradebook-edit-user'] = $tabs;

		return $tabs_on_page;
	}

	/**
	 * The admin page output.
	 *
	 * @since 1.1.0
	 * @access private
	 */
	static function admin_page() {
		$current_page = $_GET['page'];

		$sections = array_values( apply_filters( "ld_gb_admin_page_{$current_page}_sections", [] ) );

		$active_section = false;
		if ( count( $sections ) > 1 && isset( $_GET['section'] ) ) {
			foreach ( $sections as $section ) {
				if ( $section['id'] == $_GET['section'] ) {
					$active_section = $section;
					break;
				}
			}
		} else {
			$active_section = $sections[0];
		}

		if ( ! $active_section ) {
			return;
		}

		$active_section['args'] = wp_parse_args(
			isset( $active_section['args'] ) ? $active_section['args'] : [],
			[
				'display_submit' => true,
			]
		);

		include LEARNDASH_GRADEBOOK_DIR . 'admin/views/html-admin-page.php';
	}
}
