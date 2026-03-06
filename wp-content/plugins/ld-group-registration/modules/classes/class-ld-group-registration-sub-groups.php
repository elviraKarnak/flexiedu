<?php
/**
 * Groups Module
 *
 * @since 4.0
 * @package Ld_Group_Registration
 * @subpackage Ld_Group_Registration/modules/classes
 */

namespace LdGroupRegistration\Modules\Classes;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Ld_Group_Registration_Sub_Groups' ) ) {
	/**
	 * Class LD Group Registration Groups
	 */
	class Ld_Group_Registration_Sub_Groups {
		/**
		 * Class Instance
		 *
		 * @var object
		 */
		protected static $instance = null;

		/**
		 * Get a singleton instance of this class
		 *
		 * @return object
		 * @since 4.1.0
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Get all sub groups
		 *
		 * @param int $group_id  ID of the post.
		 */
		public function get_all_sub_group_ids( $group_id ) {
			return learndash_get_group_children( $group_id );
		}

		/**
		 * Is group sub group.
		 *
		 * @param int $sub_group_id  ID of the post.
		 */
		public function is_group_sub_group( $sub_group_id ) {
			$parent_id = wp_get_post_parent_id( $sub_group_id );
			if ( $parent_id ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Is group sub group.
		 *
		 * @param int $sub_group_id  ID of the post.
		 */
		public function get_parent_group_sub_group( $sub_group_id ) {
			$parent_id = wp_get_post_parent_id( $sub_group_id );
			return $parent_id;
		}

		public function enqueue_sub_groups_scripts() {
			global $post;

			if ( isset( $post ) && has_shortcode( $post->post_content, 'wdm_group_users' ) && ! empty( $_POST ) && isset( $_POST['wdm_group_id'] ) ) {
				// Enqueue select2 styles.
				wp_enqueue_style(
					'ldgr-select2-style',
					'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css',
					[],
					LD_GROUP_REGISTRATION_VERSION,
					false
				);

				// Enqueue select2 script.
				wp_enqueue_script(
					'ldgr-select2-script',
					'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js',
					[],
					LD_GROUP_REGISTRATION_VERSION,
					true
				);
			}
		}

		/**
		 * Create sub group via ajax
		 *
		 * @since 3.2.0
		 */
		public function ajax_create_sub_group() {
			if ( isset( $_POST['nonce'] )
				&& wp_verify_nonce( $_POST['nonce'], 'add-sub_group_' . get_current_user_id() )
			) {
				wp_send_json( $this->sub_group_update( false ) );
			}
		}

		/**
		 * Edit sub group via ajax
		 *
		 * @since 3.2.0
		 */
		public function ajax_edit_sub_group() {
			if ( isset( $_POST['nonce'] )
				&& wp_verify_nonce( $_POST['nonce'], 'edit-sub_group_' . get_current_user_id() )
			) {
				wp_send_json( $this->sub_group_update( true ) );
			}
		}

		/**
		 * Callback function for creating and updating subgroup.
		 *
		 * @return string JSON string with result.
		 */
		private function sub_group_update( $edit = false ) {
			$ops_string        = ''; // Operating string. the edit or create operation.
			$should_update     = false; // If this is true, we have a valid request and proceed further code.
			$existing_sg_limit = 0; // Sub group limit. 0 for new sub-group.

			if ( $edit ) {
				$sub_group_id   = filter_input( INPUT_POST, 'subGroupId', FILTER_SANITIZE_NUMBER_INT );
				$sub_group_edit = [
					'ID'         => $sub_group_id,
					'post_title' => htmlspecialchars( filter_input( INPUT_POST, 'groupName' ) ),
				];

				/**
				 * Filter the data while editing a sub-group
				 *
				 * @since 4.2.2
				 *
				 * @param array $sub_group_edit     Array of sub-group data being edited.
				 */
				$sub_group_edit = apply_filters( 'ldgr_filter_sub_group_edit', $sub_group_edit );
				$update         = wp_update_post(
					$sub_group_edit
				);
				$ops_string     = __( 'updated', 'wdm_ld_group' );
				$should_update  = true;

				$existing_sg_limit = (int) get_post_meta( $sub_group_id, 'wdm_group_users_limit_' . $sub_group_id, true );
			} else {
				$sub_group_data = [
					'post_title'   => htmlspecialchars( filter_input( INPUT_POST, 'groupName' ) ),
					'post_status'  => 'publish',
					'post_type'    => 'groups',
					'post_parent'  => filter_input( INPUT_POST, 'parentGroupId', FILTER_SANITIZE_NUMBER_INT ),
					'post_content' => '',
					'post_author'  => get_current_user_id(),
					'post_name'    => htmlspecialchars( filter_input( INPUT_POST, 'groupName' ) ),
				];

				/**
				 * Filter the data while creating a sub-group
				 *
				 * @since 4.2.2
				 *
				 * @param array $sub_group_data     Array of sub-group data being created.
				 */
				$sub_group_data = apply_filters( 'ldgr_filter_sub_group_create', $sub_group_data );

				$sub_group_id  = wp_insert_post( $sub_group_data );
				$ops_string    = __( 'created', 'wdm_ld_group' );
				$should_update = true;
			}

			if ( $should_update ) {
				$parent_group_id          = filter_input( INPUT_POST, 'parentGroupId', FILTER_SANITIZE_NUMBER_INT );
				$group_seats              = intval( filter_input( INPUT_POST, 'groupSeats', FILTER_SANITIZE_NUMBER_INT ) );
				$group_courses            = filter_input( INPUT_POST, 'groupCourses', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
				$group_leaders            = filter_input( INPUT_POST, 'groupLeaders', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
				$parent_group_limit       = intval( get_post_meta( $parent_group_id, 'wdm_group_users_limit_' . $parent_group_id, 1 ) );
				$parent_total_group_limit = intval( get_post_meta( $parent_group_id, 'wdm_group_total_users_limit_' . $parent_group_id, 1 ) );
				$group_users              = filter_input( INPUT_POST, 'groupUsers', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );

				// Set meta for identifying group as sub group and parent group.
				update_post_meta( $sub_group_id, 'is_sub_groups', true );
				update_post_meta( $sub_group_id, 'parent_group_id', $parent_group_id );

				// Get all sub groups for this parent group.
				$parent_sub_group_ids = ldgr_get_sub_group_ids( $parent_group_id );

				$new_parent_sub_group_ids = ( $parent_sub_group_ids && count( $parent_sub_group_ids ) ) ? $parent_sub_group_ids : [];

				// Add newly created sub-group to list of sub-group ids.
				array_push( $new_parent_sub_group_ids, $sub_group_id );
				update_post_meta( $parent_group_id, 'sub_groups', maybe_serialize( array_unique( $new_parent_sub_group_ids ) ) );

				// Parent group seat limit - ( seat limit submitted in the form - existing seat limit of the sub-group ). This is to prevent false reduction of the parent group seat limit.
				$final_parent_group_limit       = (int) $parent_group_limit - ( $group_seats - $existing_sg_limit );
				$final_parent_total_group_limit = (int) $parent_total_group_limit - ( $group_seats - $existing_sg_limit );

				// updating the parent Group's user seats balance.
				update_post_meta( $parent_group_id, 'wdm_group_users_limit_' . $parent_group_id, $final_parent_group_limit );
				update_post_meta( $parent_group_id, 'wdm_group_total_users_limit_' . $parent_group_id, $final_parent_total_group_limit );
				ldgr_recalculate_group_seats( $parent_group_id );

				foreach ( $group_courses as $course ) {
					update_post_meta( $course, 'learndash_group_enrolled_' . $sub_group_id, time() );
				}

				// Let's make all selected leaders as group leaders.
				if ( ! empty( $group_leaders ) ) {
					foreach ( $group_leaders as $sub_group_leader_id ) {
						$this->change_user_role_to_gl( (int) $sub_group_leader_id );
					}
				}

				// Assign sub-group leaders.
				if ( ! empty( $group_leaders ) ) {
					learndash_set_groups_administrators( $sub_group_id, $group_leaders );
				}

				// Enroll sub-group users.
				if ( ! empty( $group_users ) ) {
					foreach ( $group_users as $group_user ) {
						$group_instance = Ld_Group_Registration_Groups::get_instance();
						$group_instance->ldgr_remove_user_from_group( $group_user, $parent_group_id );
						ld_update_group_access( $group_user, $sub_group_id );
					}
				}

				// Set sub-group seat limit and total seat count.
				update_post_meta( $sub_group_id, 'wdm_group_users_limit_' . $sub_group_id, $group_seats );
				update_post_meta( $sub_group_id, 'wdm_group_total_users_limit_' . $sub_group_id, $group_seats );
				ldgr_recalculate_group_seats( $sub_group_id );

				$sub_group_label = \LearnDash_Custom_Label::get_label( 'subgroup' );

				$result = [
					'status'  => 'success',
					'message' => sprintf(
						// translators: Sub group.
						esc_html__( '%1$s %2$s successfully!', 'wdm_ld_group' ),
						$sub_group_label,
						$ops_string
					),
				];
			} else {
				$result = [
					'status'  => 'failed',
					'message' => esc_html__( 'Request failed! Try again by refreshing the page.', 'wdm_ld_group' ),
				];
			}

			/**
			 * Filter sub-group update results
			 *
			 * @since 4.2.2
			 *
			 * @param array $result     Array of sub group update details.
			 * @param array $post_data  Post data submitted to update sub-group.
			 */
			return apply_filters( 'ldgr_filter_sub_group_update_result', $result, $_POST );
		}

		/**
		 * Changes given users user role to LD's group leader.
		 *
		 * @return void
		 */
		private function change_user_role_to_gl( $user_id ) {
			if ( ! empty( $user_id ) ) {
				$user = new \WP_User( $user_id );

				/**
				 * Does not override these user roles while creating sub-group and making users as a group leader.
				 *
				 * @since 4.2.1
				 *
				 * @param string $user_roles User roles to skip overriding.
				 */
				$dont_remove_user_roles = apply_filters(
					'ldgr_sub_group_leaders_remove_prev_role',
					[
						'administrator',
						'wdm_instructor',
					]
				);
				$remove_previous_role   = false;

				// if the user has roles other than 'dont_remove_user_roles' roles.
				if ( empty( array_intersect( $dont_remove_user_roles, $user->roles ) ) ) {
					$remove_previous_role = true;
				}

				/**
				 * User level checking to remove previous user role.
				 *
				 * @since 4.2.1
				 *
				 * @param bool $remove_previous_role true will remove previous user role and add Group Leader role.
				 * @param WP_User $user WP_User instance of the given user ID.
				 */
				$remove_previous_role = apply_filters( 'ldgr_sub_group_leaders_remove_prev_role', $remove_previous_role, $user );

				if ( true === $remove_previous_role ) {
					// Replace the current role with 'group_leader' role.
					$user->set_role( 'group_leader' );
				} else {
					// Add user role 'group_leader'.
					$user->add_role( 'group_leader' );
				}
			}
		}

		/**
		 * Show edit sub group via ajax
		 *
		 * @since 3.2.0
		 */
		public function ajax_show_edit_sub_group() {
			if ( array_key_exists( 'action', $_POST ) && 'ldgr_show_edit_sub_group' == $_POST['action'] ) {
				$sub_group_leaders   = learndash_get_groups_administrator_ids( $_POST['subGroupId'] );
				$sub_group_users     = learndash_get_groups_user_ids( $_POST['subGroupId'] );
				$sub_group_courses   = learndash_group_enrolled_courses( $_POST['subGroupId'] );
				$sub_group_limit     = get_post_meta( $_POST['subGroupId'], 'wdm_group_users_limit_' . $_POST['subGroupId'], true );
				$sub_group_name      = get_the_title( $_POST['subGroupId'] );
				$sub_grp_limit_count = '';
				if ( '' === $sub_group_limit ) {
					$sub_grp_limit_count = 0;
				} else {
					$sub_grp_limit_count = $sub_group_limit;
				}
				echo wp_json_encode(
					[
						'status'            => 'success',
						'sub_group_leaders' => $sub_group_leaders,
						'sub_group_users'   => $sub_group_users,
						'sub_group_courses' => $sub_group_courses,
						'sub_group_limit'   => $sub_grp_limit_count,
						'sub_group_name'    => $sub_group_name,
					]
				);
			}
			die();
		}

		/**
		 * Add custom label for sub group
		 *
		 * @param array $custom_label_setting  Array of LD custom labels.
		 * @since 3.2.0
		 */
		public function add_custom_sub_group_label_setting( $custom_label_setting ) {
			$ld_custom_labels = get_option( 'learndash_settings_custom_labels' );
			if ( empty( $ld_custom_labels ) ) {
				return $custom_label_setting;
			}

			$custom_label_setting['subgroup'] = [
				'name'      => 'subgroup',
				'type'      => 'text',
				'label'     => esc_html__( 'Sub Group', 'wdm_ld_group' ),
				'help_text' => esc_html__( 'Label to replace "sub group"', 'wdm_ld_group' ),
				'value'     => isset( $ld_custom_labels['subgroup'] ) ? $ld_custom_labels['subgroup'] : '',
				'class'     => 'regular-text',
			];

			$custom_label_setting['subgroups'] = [
				'name'      => 'subgroups',
				'type'      => 'text',
				'label'     => esc_html__( 'Sub Groups', 'wdm_ld_group' ),
				'help_text' => esc_html__( 'Label to replace "sub groups"', 'wdm_ld_group' ),
				'value'     => isset( $ld_custom_labels['subgroups'] ) ? $ld_custom_labels['subgroups'] : '',
				'class'     => 'regular-text',
			];

			return $custom_label_setting;
		}

		/**
		 * Add group code tab header on group registration page
		 *
		 * @param array $tab_headers    Array of group registration tab header details.
		 * @param int   $group_id       ID of the group.
		 *
		 * @return array                Updated array of group registration tab header details.
		 * @since 4.1.0
		 */
		public function add_sub_group_tab_header( $tab_headers, $group_id ) {
			// Check if array.
			if ( ! is_array( $tab_headers ) ) {
				return $tab_headers;
			}

			// Check if not already added.
			if ( false !== array_search( 'wdm_ldgr_sub_group_label', array_column( $tab_headers, 'slug' ) ) ) {
				return $tab_headers;
			}

			// cspell:disable-next-line .
			$is_heirarchical = learndash_is_groups_hierarchical_enabled();
			$parent_id       = wp_get_post_parent_id( $group_id );
			// cspell:disable-next-line .
			if ( $is_heirarchical && ! $parent_id ) {
				$sub_group_label = \LearnDash_Custom_Label::get_label( 'subgroup' );
				$tab_id          = count( $tab_headers ) + 1;

				// array_push(.
				array_unshift(
					$tab_headers,
					[
						'title' => $sub_group_label,
						'slug'  => 'wdm_ldgr_sub_group_label',
						'icon'  => plugin_dir_url( __DIR__ ) . 'media/report.svg',
						'id'    => $tab_id,
					]
				);
			}
			return $tab_headers;
		}

		/**
		 * Add sub group tab contents on group registration page
		 *
		 * @param array $tab_contents   Array of group registration tab header details.
		 * @param int   $group_id       ID of the group.
		 *
		 * @return array                Updated array of group registration tab content details.
		 * @since 4.1.0
		 */
		public function add_sub_group_tab_contents( $tab_contents, $group_id ) {
			// Check if array.
			if ( ! is_array( $tab_contents ) ) {
				return $tab_contents;
			}

			$tab_id = count( $tab_contents ) + 1;

			// Check if not already added.
			if ( false !== array_search( $tab_id, array_column( $tab_contents, 'id' ) ) ) {
				return $tab_contents;
			}

			// cspell:disable-next-line .
			$is_heirarchical = learndash_is_groups_hierarchical_enabled();
			$parent_id       = wp_get_post_parent_id( $group_id );

			// cspell:disable-next-line .
			if ( $is_heirarchical && ! $parent_id ) {
				foreach ( $tab_contents as $index => $tab_content ) {
					$tab_contents[ $index ]['active'] = false;
				}
				array_unshift(
					$tab_contents,
					[
						'id'       => $tab_id,
						'active'   => true,
						'template' => plugin_dir_path( __DIR__ ) . 'templates/ldgr-group-users/tabs/sub-groups-tab.template.php',
					]
				);
			}
			return $tab_contents;
		}

		/**
		 * Add sub group label while fetching learndash labels.
		 *
		 * @param string $label     Label for the learndash custom label.
		 * @param string $key       Learndash custom label key.
		 *
		 * @return string           Updated sub group label for the sub group label key.
		 */
		public function add_sub_group_label( $label, $key ) {
			$labels         = [];
			$labels[ $key ] = \LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_Custom_Labels', $key );

			switch ( $key ) {
				case 'subgroup':
					$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Sub Group', 'wdm_ld_group' );
					break;
				case 'subgroups':
					$label = ! empty( $labels[ $key ] ) ? $labels[ $key ] : esc_html__( 'Sub Groups', 'wdm_ld_group' );
					break;
			}
			return $label;
		}
	}
}
