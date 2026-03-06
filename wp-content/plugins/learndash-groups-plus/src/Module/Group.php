<?php
/**
 * Group Module.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore numberposts ancenstors WPINC
 */

namespace LearnDash\Groups_Plus\Module;

use LearnDash\Core\Utilities\Cast;
use LearnDash\Groups_Plus\Utility\SharedFunctions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Group
 */
class Group {
	/**
	 * The instance of the class
	 *
	 * @since    2.8.0
	 * @access   private
	 * @var      Boot
	 */
	private static $instance = null;

	/**
	 * @var bool|int
	 */
	static $parent_group_id = false;


	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'set_parent_group_id' ) );
		add_action( 'create_groups_plus', [ $this, 'team_created' ] );
	}

	/**
	 * Creates singleton instance of class
	 *
	 * @return Group $instance The InitializePlugin Class
	 * @since 2.8.0
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Get the Parent Group/Post ID of given ID.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 $group_id default changed to false from null.
	 *
	 * @param int|bool $group_id Group ID to get the Parent of. Defaults to false.
	 *
	 * @return int|bool
	 */
	public static function get_parent_group_id( $group_id = false ) {
		if ( empty( $group_id ) ) {
			if ( empty( self::$parent_group_id ) ) {
				/**
				 * If we haven't populated a default Primary Group, do so by getting a list of all Primary Groups.
				 *
				 * This method will also set $parent_group_id with the first found Group in the list.
				 */
				self::get_all_primary_groups();
			}

			return self::$parent_group_id;
		} elseif ( learndash_is_groups_hierarchical_enabled() ) {
			$parent_id = wp_get_post_parent_id( $group_id );

			// Support for Non-Organization Teams.
			if (
				$parent_id === 0
				&& get_post_meta(
					Cast::to_int( $group_id ),
					SharedFunctions::$is_non_organization_team,
					true
				)
			) {
				return $group_id;
			}

			return $parent_id;
		}

		return false;
	}

	/**
	 * @param int $group_id
	 *
	 * @return array
	 */
	public static function get_child_groups( $group_id = null, $post_status = null ) {

		if ( is_null( $group_id ) ) {
			$group_id = self::$parent_group_id;
		}
		if ( ! is_null( $group_id ) ) {
			self::$parent_group_id = $group_id;
		}

		// print_r($group_id);
		$group_orderby = ! empty( get_site_option( 'group_orderby' ) ) ? get_site_option( 'group_orderby' ) : 'title';
		$group_order   = ! empty( get_site_option( 'group_order' ) ) ? get_site_option( 'group_order' ) : 'ASC';
		if ( self::is_admin_or_primary_group_leader( $group_id ) ) {
			$args = array(
				'numberposts' => -1,
				'post_type'   => 'groups',
				'orderby'     => $group_orderby,
				'order'       => $group_order,
				'post_parent' => $group_id,
			);
		} else {
			$group_ids = array();

			$all_user_meta = get_user_meta( get_current_user_id() );
			if ( ! empty( $all_user_meta ) ) {
				foreach ( $all_user_meta as $meta_key => $meta_set ) {
					if ( 'learndash_group_leaders_' == substr( $meta_key, 0, strlen( 'learndash_group_leaders_' ) ) ) {
						$group_ids = array_merge( $group_ids, $meta_set );
					}
				}
			}
			$group_ids = array_map( 'absint', $group_ids );
			$group_ids = array_diff( $group_ids, array( 0 ) );
			$group_ids = learndash_validate_groups( $group_ids );
			if ( ! empty( $group_ids ) ) {
				if ( self::get_group_leader_level() == 2 ) { // Secondary level.
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'groups',
						'post__in'    => $group_ids,
						'orderby'     => $group_orderby,
						'order'       => $group_order,
						'post_parent' => self::$parent_group_id,
					);
				} else {
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'groups',
						'orderby'     => $group_orderby,
						'order'       => $group_order,
						'post__in'    => $group_ids,
						'post_parent' => self::$parent_group_id,
					);
				}
			} else {
				// dummy $args to get empty data.
				$args = array(
					'posts_per_page' => -1,
					'post_type'      => 'groups',
					'orderby'        => $group_orderby,
					'order'          => $group_order,
					'post_parent'    => self::$parent_group_id,
					'p'              => 1,
				);
			}

			if ( self::$parent_group_id === false && ! empty( $group_ids ) ) {
				// self::$parent_group_id = self::get_parent_group_id($group_ids[0]);
			}
		}

		if ( ! is_null( $post_status ) ) {
			$args['post_status'] = $post_status;
		}

		$groups = get_posts( $args );
		return $groups;
	}

	 /**
	  * Get All Primary Groups which has/hasn't children and at level 1
	  *
	  * @return array
	  */
	public static function get_all_primary_groups() {
		if ( ! is_user_logged_in() ) {
			return array();
		}

		$user_id = wp_get_current_user()->ID;
		$args    = array();

		$group_orderby = ! empty( get_site_option( 'group_orderby' ) ) ? get_site_option( 'group_orderby' ) : 'title';
		$group_order   = ! empty( get_site_option( 'group_order' ) ) ? get_site_option( 'group_order' ) : 'ASC';

		if ( current_user_can( 'administrator' ) ) {
			$args = array(
				'numberposts' => -1,
				'post_type'   => 'groups',
				'orderby'     => $group_orderby,
				'order'       => $group_order,
				'post_parent' => 0,
			);
		} elseif ( current_user_can( 'group_leader' ) ) {
			$group_ids = array();

			$all_user_meta = get_user_meta( get_current_user_id() );
			if ( ! empty( $all_user_meta ) ) {
				foreach ( $all_user_meta as $meta_key => $meta_set ) {
					if ( 'learndash_group_leaders_' == substr( $meta_key, 0, strlen( 'learndash_group_leaders_' ) ) ) {
						$group_ids = array_merge( $group_ids, $meta_set );
					}
				}
			}
			$group_ids = array_map( 'absint', $group_ids );
			$group_ids = array_diff( $group_ids, array( 0 ) );
			$group_ids = learndash_validate_groups( $group_ids );
			if ( ! empty( $group_ids ) ) {
				if ( self::get_group_leader_level() == 2 ) { // secondary level{
					$group_ancestors = self::get_user_groups_ancenstors();
					// print_r($group_ancestors);
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'groups',
						'post__in'    => $group_ancestors,
						'orderby'     => $group_orderby,
						'order'       => $group_order,
						// 'post_parent__in' => $group_ancestors
					);
				} else {
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'groups',
						'post__in'    => $group_ids,
						'orderby'     => $group_orderby,
						'order'       => $group_order,
						'post_parent' => 0,
					);
				}
			} else {
				// dummy $args to get empty data.
				$args = array(
					'posts_per_page' => -1,
					'post_type'      => 'groups',
					'orderby'        => $group_orderby,
					'order'          => $group_order,
					'p'              => 1, // TODO: This never guarantees an empty result. We have to fix it later, probably just return [].
				);
			}
		} else {
			// dummy $args to get empty data.
			$args = array(
				'posts_per_page' => -1,
				'post_type'      => 'groups',
				'orderby'        => $group_orderby,
				'order'          => $group_order,
				'p'              => 1, // TODO: This never guarantees an empty result. We have to fix it later, probably just return [].
			);
		}

		$args['meta_query'] = array(
			array(
				'key'     => '_is_group_wc_enabled',
				'value'   => '1',
				'compare' => '!=',
			),
			array(
				'key'     => '_is_group_wc_enabled',
				'compare' => 'NOT EXISTS',
			),
			'relation' 	  => 'OR',
		);

		$all_groups = get_posts( $args );

		// TODO: Move this logic out of this method and instead set it elsewhere based on the output of this method.
		if ( self::$parent_group_id === false && ! empty( $all_groups ) ) {
			self::$parent_group_id = $all_groups[0]->ID;
		}

		return $all_groups;
	}

	/**
	 * Set primary group
	 */
	public static function set_parent_group_id() {
		// print_r($_REQUEST);
		if ( isset( $_GET['parent-group-id'] ) && ! empty( $_GET['parent-group-id'] ) ) {
			$parent_group_id = $_GET['parent-group-id'];

			if ( self::has_post_published( $parent_group_id ) ) {
				self::$parent_group_id = intval( $parent_group_id );
			}
		}

		// shortcode page url
		elseif ( isset( $_GET['group'] ) && ! empty( $_GET['group'] ) ) {
			$group_id        = general_encrypt_decrypt( 'decrypt', $_GET['group'] );
			$parent_group_id = self::get_parent_group_id( $group_id );

			if ( self::has_post_published( $parent_group_id ) ) {
				self::$parent_group_id = intval( $parent_group_id );
			}
		}
		// by learndash-groups-plus endpoint
		elseif ( get_query_var( 'group_id', false ) != '' ) {

			$group_key       = get_query_var( 'group_id', false );
			$group_id        = general_encrypt_decrypt( 'decrypt', $group_key );
			$parent_group_id = self::get_parent_group_id( $group_id );

			if ( self::has_post_published( $parent_group_id ) ) {
				self::$parent_group_id = intval( $parent_group_id );
			}
		}

	}

	/**
	 * Ensure that any Teams made within an Organization are given the same Access Mode automatically.
	 *
	 * @since 2.1.0
	 *
	 * @param int $team_id Team ID.
	 *
	 * @return void
	 */
	public function team_created( $team_id ) {
		$parent_group_id = self::get_parent_group_id( $team_id );
		$access_mode     = learndash_get_setting(
			Cast::to_int( $parent_group_id ),
			'group_price_type'
		);

		learndash_update_setting( $team_id, 'group_price_type', $access_mode );
	}

	/**
	 * check post is published or not.
	 */
	public static function has_post_published( $post_id = 0 ) {
		if ( get_post_status( $post_id ) == 'publish' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get group leader id of primary group
	 *
	 * @param int $group_id Primary/Parent Group ID
	 *
	 * @return bool|int
	 */
	public static function get_group_leader_id_of_group( $group_id = null ) {
		if ( ! is_null( $group_id ) ) {
			$group_leader_ids = learndash_get_groups_administrator_ids( $group_id );
			if ( ! empty( $group_leader_ids ) ) {
				return $group_leader_ids[0];
			}
		}
		return false;
	}

	/**
	 * Check current user is Administrator or Primary group leader
	 *
	 * @param int $group_id Primary/Parent Group ID
	 *
	 * @return bool
	 */
	public static function is_admin_or_primary_group_leader( $group_id = null ) {
		if ( is_user_logged_in() ) {
			if ( current_user_can( 'administrator' ) ) :
				return true;
			elseif ( current_user_can( 'group_leader' ) ) :
				$group_ids = array();

				$all_user_meta = get_user_meta( get_current_user_id() );
				if ( ! empty( $all_user_meta ) ) {
					foreach ( $all_user_meta as $meta_key => $meta_set ) {
						if ( 'learndash_group_leaders_' == substr( $meta_key, 0, strlen( 'learndash_group_leaders_' ) ) ) {
							$group_ids = array_merge( $group_ids, $meta_set );
						}
					}
				}
				$group_ids = array_map( 'absint', $group_ids );
				$group_ids = array_diff( $group_ids, array( 0 ) );
				$group_ids = learndash_validate_groups( $group_ids );

				// if( in_array($group_id, $group_ids) && count(learndash_get_group_children( $group_id ) )){
				if ( in_array( $group_id, $group_ids ) ) {
					return true;
				}
			endif;
		}
		return false;
	}

	/**
	 * Check if the user is a Team Leader of a given group
	 *
	 * @param int $group_id Team Group ID
	 * @param int $user_id  User ID
	 *
	 * @return bool
	 */
	public static function is_groups_plus_group_leader( $group_id = null, $user_id = null ) {

		if ( ! $group_id ) {
			return false;
		}

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( $user_id ) {
			if ( user_can( $user_id, 'group_leader' ) && get_user_meta( $user_id, 'learndash_group_leaders_' . $group_id, true ) ) :
				return true;
			endif;
		}

		return false;

	}

	/**
	 * Check if the user is a Lead Organizer of a given group
	 *
	 * @param   int $group_id  Team or Organization Group ID
	 * @param   int $user_id   User ID
	 *
	 * @access  public
	 * @since   1.1.1
	 * @return  bool
	 */
	public static function is_groups_plus_lead_organizer( $group_id = null, $user_id = null ) {

		if ( ! $group_id ) {
			return false;
		}

		$group_id = self::get_parent_group_id( $group_id );

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( $user_id ) {
			return self::is_groups_plus_group_leader( $group_id, $user_id );
		}

		return false;

	}

	/**
	 * Check whether user has given group access
	 *
	 * @param int $group_id Group ID
	 *
	 * @return bool
	 */
	public static function has_group_access( $group_id = 0 ) {
		if ( is_user_logged_in() ) {
			if ( current_user_can( 'administrator' ) ) :
				return true;
			elseif ( current_user_can( 'group_leader' ) ) :
				$current_logged_user_id = get_current_user_id();
				$group_ids              = learndash_get_administrators_group_ids( $current_logged_user_id );
				if ( in_array( $group_id, $group_ids ) ) {
					return true;
				}

			endif;
		}
		return false;
	}

	/**
	 * Get users of Group
	 *
	 * @param int $group_id Group ID
	 *
	 * @return array list of group users
	 */
	public static function get_group_users( $group_id = 0 ) {

		$user_orderby = ! empty( get_site_option( 'user_orderby' ) ) ? get_site_option( 'user_orderby' ) : 'display_name';
		$user_order   = ! empty( get_site_option( 'user_order' ) ) ? get_site_option( 'user_order' ) : 'ASC';

		$user_query_args = array(
			'orderby'    => $user_orderby,
			'order'      => $user_order,
			'meta_query' => array(
				array(
					'key'     => 'learndash_group_users_' . intval( $group_id ),
					'compare' => 'EXISTS',
				),
			),
		);
		$user_query      = new \WP_User_Query( $user_query_args );
		if ( isset( $user_query->results ) ) {
			$user_group_users = $user_query->results;
		} else {
			$user_group_users = array();
		}

		$user_group_users = apply_filters( 'learndash_get_groups_users', $user_group_users, $group_id );

		return $user_group_users;
	}

	public static function get_group_leader_level() {

		if ( learndash_is_groups_hierarchical_enabled() ) {
			$current_logged_user_id = get_current_user_id();
			$group_ids              = learndash_get_administrators_group_ids( $current_logged_user_id );
			if ( ! empty( $group_ids ) ) {
				$ancestors = get_post_ancestors( current( $group_ids ) );
				if ( count( $ancestors ) == 0 ) {
					return 1; // Primary Group Level
				} else {
					return count( $ancestors ) + 1; // Secondary Group level and so on...
				}
			} else {
				return 1;
			}
		} else {
			return 0;       }
	}

	public static function get_user_groups_ancenstors() {
		if ( learndash_is_groups_hierarchical_enabled() ) {
			$current_logged_user_id = get_current_user_id();
			$group_ids              = learndash_get_administrators_group_ids( $current_logged_user_id );
			$return_ancestors      = array();
			if ( ! empty( $group_ids ) ) {
				foreach ( $group_ids as $group_id ) {
					$ancestors        = get_post_ancestors( $group_id );
					$return_ancestors = array_merge( $return_ancestors, $ancestors );
				}

				return $return_ancestors;
			} else {
				return array();
			}
		} else {
			return array();         }
	}

	/**
	 * Check if a User is allowed to edit another
	 *
	 * @param   int $user_id          User to Edit.
	 * @param   int $current_user_id  User doing the Editing.
	 *
	 * @access  public
	 * @since   1.1.1
	 * @return  bool
	 */
	public static function can_edit_user( $user_id, $current_user_id = null ) {

		if ( ! $current_user_id ) {
			$current_user_id = get_current_user_id();
		}

		// Don't allow editing yourself. It can easily cause confusion if they were to edit their own password which forces a log out.
		if ( $user_id === $current_user_id ) {
			return false;
		}

		$user         = new \WP_User( $user_id );
		$current_user = new \WP_User( $current_user_id );

		if ( ! $user->exists() || ! $current_user->exists() ) {
			return false;
		}

		$super_admins = array();
		if ( is_multisite() ) {
			$super_admins = get_super_admins();
		}

		// Admins can do anything.
		if ( in_array( 'administrator', $current_user->roles, true ) || in_array( $current_user->user_login, $super_admins, true ) ) {
			return true;
		}

		// No editing Admins.
		if ( in_array( 'administrator', $user->roles, true ) || in_array( $user->user_login, $super_admins, true ) ) {
			return false;
		}

		// We want to separately handle Lead Organizer and Team Leader Group IDs for the User, as their permissions are different.
		$lead_organizer_group_ids = self::get_lead_organizer_group_ids( $current_user_id );

		$team_leader_group_ids = self::get_team_leader_group_ids( $current_user_id );

		$lead_organizer_acceptable_roles = apply_filters(
			'learndash_groups_plus_lead_organizer_editable_roles',
			array(
				'group_leader',
				'subscriber',
			)
		);

		$team_leader_acceptable_roles = apply_filters(
			'learndash_groups_plus_team_leader_editable_roles',
			array(
				'subscriber',
			)
		);

		// For Groups we are a Lead Organizer of, we have more permissions and more complicated checks to make for those permissions.
		foreach ( $lead_organizer_group_ids as $lead_organizer_group_id ) {

			$in_organization = learndash_is_user_in_group( $user_id, $lead_organizer_group_id );

			$child_group_ids = wp_list_pluck( self::get_child_groups( $lead_organizer_group_id ), 'ID' );

			// If they are not in the top-level Organization, check all the Teams within
			if ( ! $in_organization ) {

				$child_group_ids = wp_list_pluck( self::get_child_groups( $lead_organizer_group_id ), 'ID' );

				foreach ( $child_group_ids as $child_group_id ) {
					$in_organization = learndash_is_user_in_group( $user_id, $child_group_id );

					if ( $in_organization ) {
						break;
					}
				}
			}

			// They aren't in the Organization or its Team, skip to the next Organization
			if ( ! $in_organization ) {
				continue;
			}

			if ( ! empty( array_intersect( $user->roles, $lead_organizer_acceptable_roles ) ) ) {

				if ( in_array( 'group_leader', $user->roles, true ) ) {

					// Don't let Lead Organizers edit each other.
					if ( ! empty( self::get_lead_organizer_group_ids( $user_id ) ) ) {
						return false;
					}

					// If they have the Group Leader Role, ensure they are a Group Leader in one of our Organizations.
					foreach ( $child_group_ids as $child_group_id ) {

						if ( get_user_meta( $user_id, 'learndash_group_leaders_' . $child_group_id, true ) ) {
							return true;
						}
					}

					return false;

				}

				return true;

			}
		}

		// For Groups we are a Team Leader of, we simply need to check whether this User is in one of our Groups.
		foreach ( $team_leader_group_ids as $team_leader_group_id ) {

			$in_group = learndash_is_user_in_group( $user_id, $team_leader_group_id );

			if ( ! $in_group ) {
				continue;
			}

			if ( ! empty( array_intersect( $user->roles, $team_leader_acceptable_roles ) ) ) {
				return true;
			}
		}

		return false;

	}

	/**
	 * Return an array of Group IDs that the User is a Lead Organizer of
	 *
	 * @param   int $user_id  User ID.
	 *
	 * @access  public
	 * @since   1.1.1
	 * @return  int[]            Array of Group IDs.
	 */
	public static function get_lead_organizer_group_ids( $user_id = null ) {

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$all_group_ids = learndash_get_administrators_group_ids( $user_id, true );

		$lead_organizer_group_ids = array_filter(
			$all_group_ids,
			function( $id ) {
				return self::get_parent_group_id( $id ) === 0;
			}
		);

		return apply_filters( 'learndash_groups_plus_get_lead_organizer_group_ids', $lead_organizer_group_ids, $user_id );

	}

	/**
	 * Get an array of Group IDs that the User is a Team Leader of
	 *
	 * @param   int $user_id  User ID.
	 *
	 * @access  public
	 * @since   1.1.1
	 * @return  int[]            Array of Group IDs.
	 */
	public static function get_team_leader_group_ids( $user_id ) {

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$all_group_ids = learndash_get_administrators_group_ids( $user_id, true );

		$lead_organizer_group_ids = self::get_lead_organizer_group_ids( $user_id );

		$team_leader_group_ids = array_diff( $all_group_ids, $lead_organizer_group_ids );
		$team_leader_group_ids = array_filter(
			$team_leader_group_ids,
			function( $id ) use ( $lead_organizer_group_ids, $user_id ) {

				// We exclude any Groups that this user may be a Group Leader of that they are also a Lead Organizer of
				$parent_id = self::get_parent_group_id( $id );
				if ( in_array( $parent_id, $lead_organizer_group_ids, true ) ) {
					return false;
				}

				return get_user_meta( $user_id, 'learndash_group_leaders_' . $id, true );

			}
		);

		return apply_filters( 'learndash_groups_plus_get_team_leader_group_ids', $team_leader_group_ids, $user_id );

	}

}
