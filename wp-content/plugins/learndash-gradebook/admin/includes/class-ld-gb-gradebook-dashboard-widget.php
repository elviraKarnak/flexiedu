<?php
/**
 * Adds custom Dashboard Widgets.
 *
 * @since 1.1.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */

defined( 'ABSPATH' ) || die();

/**
 * Class LD_GB_Gradebook_Dashboard_Widget
 *
 * Adds custom Dashboard Widgets.
 *
 * @since 1.1.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */
class LD_GB_Gradebook_Dashboard_Widget {
	/**
	 * LD_GB_Gradebook_Dashboard_Widget constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		if ( current_user_can( 'view_gradebook' ) ) {
			add_action( 'wp_dashboard_setup', [ $this, 'add_widget' ] );
			add_action( 'wp_ajax_ld_gb_dashboard_widget_get_data', [ $this, 'ajax_get_data' ] );
		}
	}

	/**
	 * Adds the widget to the dashboard.
	 *
	 * @since 1.1.0
	 * @access private
	 */
	function add_widget() {
		wp_add_dashboard_widget(
			'gradebook_widget',
			'Gradebook Overview',
			[ $this, 'show_widget' ]
		);
	}

	/**
	 * Returns new grades data for the widget.
	 *
	 * @since 1.1.0
	 * @access private
	 */
	function ajax_get_data() {
		$gradebook_ID = $_POST['gradebook_id'];
		$group_ID     = $_POST['group_id'];
		$user_ID      = $_POST['user_id'];

		$data = $this->get_widget_data( $user_ID, $gradebook_ID, $group_ID !== '0' ? $group_ID : false );

		if ( $data['users'] ) {
			foreach ( $data['users'] as $user_ID => $user ) {
				$grade = $data['users'][ $user_ID ]['grade'];

				$data['users'][ $user_ID ]['grade'] = '<span class="ld-gb-grade" style="background-color:' .
														LD_GB_UserGrade::get_display_grade_color( $grade ) . '">' .
														LD_GB_UserGrade::get_display_grade( $grade ) .
														'</span>';
			}
		}

		wp_send_json_success( $data );
	}

	/**
	 * Gets data for the widget display.
	 *
	 * @since 1.1.0
	 * @access private
	 *
	 * @param int       $user_ID User to get groups for.
	 * @param int       $gradebook_ID Gradebook to show.
	 * @param int|false $group_ID False (default) to not use a group, or 0 to use first found group.
	 *
	 * @return array.
	 */
	private function get_widget_data( $user_ID, $gradebook_ID, $group_ID = false ) {
		$data = [];

		$gradebook = get_post( $gradebook_ID );

		$data['gradebook'] = [
			'name' => $gradebook->post_title,
		];

		$get_users_args = ld_gb_get_gradebook_get_users_args(
			$gradebook_ID,
			$group_ID,
			[
				'orderby' => 'display_name',
				'order'   => 'ASC',
				'number'  => 10,
			]
		);

		/**
		 * Arguments for the get_users() function for the Dashboard widget.
		 *
		 * @since 1.2.2
		 *
		 * @param array $get_users_args
		 */
		$get_users_args = apply_filters( 'ld_gb_dashboard_widget_get_users_args', $get_users_args );

		$users = get_users( $get_users_args );

		if ( $users ) {
			foreach ( $users as $user ) {
				$user_grade = new LD_GB_UserGrade( $user->ID, $gradebook_ID );

				$user_overall_grade = $user_grade->get_user_grade();

				if ( $user_overall_grade === false ) {
					continue;
				}

				$data['users'][ $user->ID ] = [
					'name'  => $user->display_name,
					'grade' => $user_overall_grade,
				];
			}
		}

		return $data;
	}

	/**
	 * Outputs the widget HTML.
	 *
	 * @since 1.1.0
	 * @access private
	 */
	function show_widget() {
		$gradebooks = get_posts(
			[
				'post_type'   => 'gradebook',
				'numberposts' => 10,
			]
		);

		if ( ! $gradebooks ) {
			include LEARNDASH_GRADEBOOK_DIR . 'admin/views/html-dashboard-widget-no-gradebooks.php';
		} else {
			$groups = [];

			if ( ! learndash_is_admin_user() ) {
				$group_IDs = learndash_get_administrators_group_ids( get_current_user_id() );

				if ( $group_IDs ) {
					foreach ( $group_IDs as $group_ID ) {
						$groups[] = get_post( $group_ID );
					}

					$current_group = $group_IDs[0];
				}
			}

			$data = $this->get_widget_data(
				get_current_user_id(),
				$gradebooks[0]->ID,
				isset( $current_group ) ? $current_group : false
			);

			include LEARNDASH_GRADEBOOK_DIR . 'admin/views/html-dashboard-widget.php';
		}
	}
}
