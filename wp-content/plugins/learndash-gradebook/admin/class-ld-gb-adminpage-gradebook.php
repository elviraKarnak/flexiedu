<?php
/**
 * Adds the Gradebook admin page.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_AdminPage_Gradebook
 *
 * Adds the Gradebook admin page.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */
class LD_GB_AdminPage_Gradebook {
	/**
	 * LD_GB_AdminPage_Gradebook constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'page_actions' ] );
		add_filter( 'set_screen_option_admin_page_learndash_gradebook_per_page', [ $this, 'save_per_page_for_tables' ], 10, 3 );
		add_filter( 'ld_gb_admin_script_data', [ $this, 'page_data' ] );
		add_filter( 'ld_gb_admin_page_learndash-gradebook_sections', [ $this, 'page_sections' ] );
	}

	/**
	 * Loads on the Gradebook page only.
	 *
	 * @since 1.2.0
	 * @access private
	 */
	function page_actions() {
		if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'learndash-gradebook' ) {
			return;
		}

		add_action( 'admin_head', [ $this, 'per_page_screen_option' ] );

		// Fix incompatibility with Download Manager Pro
		learndash_gradebook_remove_class_action( 'admin_head', 'WPDM\admin\WordPressDownloadManagerAdmin', 'adminHead' ); // cspell:disable-line.
	}

	/**
	 * Adds a "Per Page" Screen Option that looks and functions the same as other WordPress Tables
	 *
	 * @access public
	 * @since 4.0.0
	 * @return void
	 */
	public function per_page_screen_option() {
		add_screen_option( 'per_page' );    }

	/**
	 * WordPress only accounts for very specific Screen Option keys
	 * This ensures that our Per Page value gets saved in a way that's specific to our page
	 *
	 * @param integer|boolean $screen_option  Value to save for the Screen Option
	 * @param string          $option         Option Key
	 * @param integer         $value          Value passed for the Screen Option
	 *
	 * @access public
	 * @since 4.0.0
	 * @return integer                          Value to save for the Screen Option
	 */
	public function save_per_page_for_tables( $screen_option, $option, $value ) {
		$value = (int) $value;

		if ( $value < 1 || $value > 999 ) {
			return $screen_option;
		}

		return $value;
	}

	/**
	 * Adds some data from the page.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @param array $data Data to localize.
	 *
	 * @return array
	 */
	function page_data( $data ) {
		$current_screen = get_current_screen();

		if ( ! $current_screen || $current_screen->id != 'admin_page_learndash-gradebook' ) {
			return $data;
		}

		$data['group_id'] = self::get_active_group();

		return $data;
	}

	/**
	 * This page's sections.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @return array
	 */
	function page_sections() {
		return [
			[
				'id'       => 'main',
				'label'    => _x( 'Gradebook', 'Gradebook Admin Page Section Label', 'learndash-gradebook' ),
				'callback' => [ __CLASS__, 'gradebook_page' ],
			],
		];
	}

	/**
	 * Gets the active group ID for the Gradebook, if one at all.
	 *
	 * @since 1.1.0
	 *
	 * @return bool|int Group ID or false if none.
	 */
	public static function get_active_group() {
		$active_group_ID = false;

		if ( $group_IDs = learndash_get_administrators_group_ids( get_current_user_id() ) ) {
			if ( isset( $_GET['ld_group'] ) && (int) $_GET['ld_group'] > 0 ) {
				$active_group_ID = $_GET['ld_group'];
			} elseif ( ! learndash_is_admin_user() ) {
				$active_group_ID = $group_IDs[0];
			}
		}

		return $active_group_ID;
	}

	/**
	 * Gets the active Gradebook.
	 *
	 * @since 1.2.0
	 *
	 * @return bool|int Gradebook ID or false if none.
	 */
	public static function get_active_gradebook() {
		$active_gradebook_ID = false;

		if ( isset( $_GET['gradebook'] ) ) {
			$active_gradebook_ID = $_GET['gradebook'];
		} else {
			// The same filter is applied here as is on the Select dropdown to ensure that the default Gradebook is one that the User will match what is shown in the dropdown
			$gradebooks = get_posts(
				/** This filter is documented in core/api/class-ld-gb-api.php */
				apply_filters(
					'ld_gb_adminpage_gradebook_select_query_args',
					[
						'post_type'      => 'gradebook',
						'posts_per_page' => 1,
						'post_status'    => 'publish',
					]
				)
			);

			if ( $gradebooks ) {
				$active_gradebook_ID = $gradebooks[0]->ID;
			}
		}

		return $active_gradebook_ID;
	}

	/**
	 * The admin page output.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	static function gradebook_page() {
		// Fix specific to the Instructor Roles plugin by WisdmLabs
		if ( function_exists( 'wdm_set_author' ) ) {
			remove_filter( 'pre_get_posts', 'wdm_set_author' );
		} elseif ( class_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin' ) && method_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' ) ) {
			learndash_gradebook_remove_class_action( 'pre_get_posts', 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' );
		}

		$active_gradebook = self::get_active_gradebook();

		if ( $active_gradebook === false ) {
			include LEARNDASH_GRADEBOOK_DIR . 'admin/views/html-no-gradebooks.php';

			return;
		}

		if ( isset( $_POST['ld-gb-toggle-hidden-users'] ) ) {
			if ( get_user_meta( get_current_user_id(), 'ld_gb_gradebook_show_inactive_users', true ) != 'yes' ) {
				update_user_meta( get_current_user_id(), 'ld_gb_gradebook_show_inactive_users', 'yes' );
			} else {
				delete_user_meta( get_current_user_id(), 'ld_gb_gradebook_show_inactive_users' );
			}
		}

		$hide_rows = get_user_meta( get_current_user_id(), 'ld_gb_gradebook_show_inactive_users', true ) == 'yes';

		$active_group_ID = self::get_active_group();
		$group_IDs       = learndash_get_administrators_group_ids( get_current_user_id() );

		$gradebook = new LD_GB_GradebookListTable( $active_gradebook, $active_group_ID );

		$gradebook->prepare_items();

		include LEARNDASH_GRADEBOOK_DIR . 'admin/views/html-gradebook.php';

		if ( function_exists( 'wdm_set_author' ) ) {
			add_filter( 'pre_get_posts', 'wdm_set_author' );
		} elseif ( class_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin' ) && method_exists( 'InstructorRole\Modules\Classes\Instructor_Role_Admin', 'wdm_set_author' ) ) {
			add_action( 'pre_get_posts', [ InstructorRole\Modules\Classes\Instructor_Role_Admin::get_instance(), 'wdm_set_author' ] );
		}   }
}
