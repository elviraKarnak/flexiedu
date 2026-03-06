<?php
/**
 * General functions.
 *
 * @since 1.0.0
 *
 * @package LearnDash\Groups_Plus
 *
 * cspell:ignore numberposts TRANSLIT ulgm
 */

use LearnDash\Groups_Plus\Module\Group;

function get_licenses_info_user( $group_id = 0 ) {
	$no_of_team_leaders_to_exclude_field = 0;
	$user_id                         = get_current_user_id();

	if ( $group_id != 0 ) {
		$parent_group_id = Group::get_parent_group_id( $group_id );
	} else {
		$parent_group_id = Group::$parent_group_id;
	}

	if ( ! Group::has_group_access( $parent_group_id ) && ! Group::has_group_access( $group_id ) ) {
		return array(
			'licenses'           => 0,
			'licenses_used'      => 0,
			'licenses_remaining' => 0,
		);
	}

	// Check if the organization created through the WC purchase.
	$groups_plus_user_license = (int) get_post_meta( $group_id, 'number_of_licenses', true );
	if ( $groups_plus_user_license > 0 && $group_id != 0 && true == get_post_meta( $parent_group_id, 'has_group_created_thru_wc', true ) ) {
		$group_ids = array( $group_id );

		$no_of_team_leaders_to_exclude_field = (int) get_post_meta( $group_id, 'no_of_team_leaders_to_exclude', true );

		// $groups_plus_user_license = (int)get_post_meta( $group_id, 'number_of_licenses' ,true);
	} else {
		$group_ids = learndash_get_group_children( $parent_group_id );
		$group_ids = learndash_validate_groups( $group_ids );

		$no_of_team_leaders_to_exclude_field = (int) get_post_meta( $parent_group_id, 'no_of_team_leaders_to_exclude', true );

		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! is_plugin_active( 'uncanny-learndash-groups/uncanny-learndash-groups.php' ) ) {
			$groups_plus_user_license = (int) get_post_meta( $parent_group_id, 'number_of_licenses', true );
		} else {
			$ulgm_total_seats = get_post_meta( $parent_group_id, '_ulgm_total_seats', true );
			$is_upgraded      = get_post_meta( $parent_group_id, '_ulgm_is_upgraded', true );
			if ( ! empty( $ulgm_total_seats ) && 'yes' === $is_upgraded ) {
				$total_seats              = get_post_meta( $parent_group_id, '_ulgm_total_seats', true );
				$groups_plus_user_license = (int) $total_seats;
			} else {
				$groups_plus_user_license = (int) get_post_meta( $parent_group_id, 'number_of_licenses', true );
			}
		}
	}

	$licenses_used = $team_leader_licenses_used = 0;

	if ( $group_ids ) {
		$team_leader_use_seat = get_site_option( 'team_leader_use_seat' );
		foreach ( $group_ids as $g_id ) {
			$user_query_args = array(
				'orderby'    => 'display_name',
				'order'      => 'ASC',
				'meta_query' => array(
					array(
						'key'     => 'learndash_group_users_' . intval( $g_id ),
						'compare' => 'EXISTS',
					),
				),
			);

			$user_query = new \WP_User_Query( $user_query_args );
			if ( isset( $user_query->results ) ) {
				$licenses_used += count( $user_query->results );
			}

			// Calculating the team leaders users
			if ( $team_leader_use_seat ) {
				$user_team_leader_query_args             = $user_query_args;
				$user_team_leader_query_args['role__in'] = 'group_leader';

				$user_query = new \WP_User_Query( $user_team_leader_query_args );
				if ( isset( $user_query->results ) ) {
					$team_leader_licenses_used += count( $user_query->results );
				}
			}
		}
	}

	$licenses_used = $licenses_used - ( $team_leader_licenses_used > $no_of_team_leaders_to_exclude_field ? $no_of_team_leaders_to_exclude_field : $team_leader_licenses_used );
	if ( $groups_plus_user_license > 0 ) {
		$licenses_remaining = $groups_plus_user_license - $licenses_used;
	} else {
		$licenses_remaining = 0;
	}

	$data = array(
		'licenses'           => $groups_plus_user_license,
		'licenses_used'      => $licenses_used,
		'licenses_remaining' => (int) $licenses_remaining,
	);

	return $data;
}

function general_encrypt_decrypt( $action, $string ) {
	$output         = false;
	$encrypt_method = 'AES-256-CBC';
	$secret_key     = '$d_c$@ssr00m';
	$secret_iv      = '$d_c$@ssr00m_iv';
	// hash
	$key = hash( 'sha256', $secret_key );

	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	if ( $action == 'encrypt' ) {
		$output = openssl_encrypt( $string, $encrypt_method, $key, 0, $iv );
		$output = base64_encode( $output );
	} elseif ( $action == 'decrypt' ) {
		$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}
	return $output;
}

function user_encrypt_decrypt( $action, $string ) {
	$output         = false;
	$encrypt_method = 'AES-256-CBC';
	$secret_key     = '$d_c$@ssr00m_user';
	$secret_iv      = '$d_c$@ssr00m_user_iv';
	// hash
	$key = hash( 'sha256', $secret_key );

	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	if ( $action == 'encrypt' ) {
		$output = openssl_encrypt( $string, $encrypt_method, $key, 0, $iv );
		$output = base64_encode( $output );
	} elseif ( $action == 'decrypt' ) {
		$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	}
	return $output;
}

function generate_team_member_password( $length = 8 ) {
	$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	$count = mb_strlen( $chars );

	for ( $i = 0, $result = ''; $i < $length; $i++ ) {
		$index   = rand( 0, $count - 1 );
		$result .= mb_substr( $chars, $index, 1 );
	}

	return $result;
}

function groups_plus_clear_key( $text ) {
	$text = preg_replace( '~[^\pL\d]+~u', '-', $text );
	$text = iconv( 'utf-8', 'us-ascii//TRANSLIT', $text );
	$text = preg_replace( '~[^-\w]+~', '', $text );
	$text = trim( $text, '-' );
	$text = preg_replace( '~-+~', '_', $text );
	$text = strtolower( $text );
	if ( empty( $text ) ) {
		return 'learndash-groups-plus';
	}
	return $text;
}

if ( ! function_exists( 'get_license_key' ) ) {
	function get_license_key() {
		$license_key = get_site_option( 'license_key' );
		return $license_key;
	}
}

if ( ! function_exists( 'get_child_groups_administrators' ) ) {
	function get_child_groups_administrators( $parent_group_id = 0 ) {
		// print_r(learndash_get_groups_administrator_ids($parent_group_id));
		$args                = array(
			'numberposts' => -1,
			'post_type'   => 'groups',
			'orderby'     => 'menu_order',
			'order'       => 'DESC',
			'post_parent' => $parent_group_id,
		);
		$groups              = get_posts( $args );
		$child_groups_admins = array();
		foreach ( $groups as $group ) {
			$group_leaders = learndash_get_groups_administrators( $group->ID );
			foreach ( $group_leaders as $group_leader ) {
				$leader_data                              = array();
				$leader_data['display_name']              = $group_leader->display_name;
				$leader_data['first_name']                = get_user_meta( $group_leader->ID, 'first_name', true );
				$leader_data['last_name']                 = get_user_meta( $group_leader->ID, 'last_name', true );
				$child_groups_admins[ $group_leader->ID ] = $leader_data;

			}
		}

		return $child_groups_admins;
	}
}

if ( ! function_exists( 'get_child_groups_users' ) ) {
	function get_child_groups_users( $parent_group_id = 0, $current_group_id = 0 ) {
		// print_r(learndash_get_groups_administrator_ids($parent_group_id));
		$args               = array(
			'numberposts'  => -1,
			'post_type'    => 'groups',
			'orderby'      => 'menu_order',
			'post__not_in' => array( $current_group_id ),
			'order'        => 'DESC',
			'post_parent'  => $parent_group_id,
		);
		$groups             = get_posts( $args );
		$child_groups_users = array();
		foreach ( $groups as $group ) {
			$group_users = Group::get_group_users( $group->ID );
			foreach ( $group_users as $group_user ) {
				$user_data                             = array();
				$user_data['display_name']             = $group_user->display_name;
				$user_data['first_name']               = get_user_meta( $group_user->ID, 'first_name', true );
				$user_data['last_name']                = get_user_meta( $group_user->ID, 'last_name', true );
				$user_data['username']                 = $group_user->user_login;
				$child_groups_users[ $group_user->ID ] = $user_data;

			}
		}

		return $child_groups_users;
	}
}

if ( ! function_exists( 'learndash_groups_plus_get_users' ) ) {
	function learndash_groups_plus_get_users( $roles = array( 'group_leader', 'subscriber' ) ) {
		$user_orderby = ! empty( get_site_option( 'user_orderby' ) ) ? get_site_option( 'user_orderby' ) : 'display_name';
		$user_order   = ! empty( get_site_option( 'user_order' ) ) ? get_site_option( 'user_order' ) : 'ASC';

		$users  = get_users(
			array(
				'role__in' => $roles,
				'orderby'  => $user_orderby,
				'order'    => $user_order,
			)
		);
		$_users = array();
		foreach ( $users as $user ) {
			$user_data                 = array();
			$user_data['display_name'] = $user->display_name;
			$user_data['first_name']   = get_user_meta( $user->ID, 'first_name', true );
			$user_data['last_name']    = get_user_meta( $user->ID, 'last_name', true );
			$user_data['username']     = $user->user_login;
			$_users[ $user->ID ]       = $user_data;
		}
		return $_users;
	}
}

if ( ! function_exists( 'learndash_groups_plus_get_group_leaders' ) ) {
	function learndash_groups_plus_get_group_leaders( $roles = array( 'group_leader' ) ) {
		$user_orderby = ! empty( get_site_option( 'user_orderby' ) ) ? get_site_option( 'user_orderby' ) : 'display_name';
		$user_order   = ! empty( get_site_option( 'user_order' ) ) ? get_site_option( 'user_order' ) : 'ASC';

		$users  = get_users(
			array(
				'role__in' => $roles,
				'orderby'  => $user_orderby,
				'order'    => $user_order,
			)
		);
		$_users = array();
		foreach ( $users as $user ) {
			$user_data                 = array();
			$user_data['display_name'] = $user->display_name;
			$user_data['first_name']   = get_user_meta( $user->ID, 'first_name', true );
			$user_data['last_name']    = get_user_meta( $user->ID, 'last_name', true );
			$user_data['username']     = $user->user_login;
			$_users[ $user->ID ]       = $user_data;
		}
		return $_users;
	}
}

if ( ! function_exists( 'get_parent_group_id' ) ) {
	function get_parent_group_id( $child_group_id ) {
		$parent_group_id = wp_get_post_parent_id( $child_group_id );
		return $parent_group_id;
	}
}

if ( ! function_exists( 'is_primary_group_leader' ) ) {
	function is_primary_group_leader() {
		if ( learndash_is_groups_hierarchical_enabled() ) {
			$current_logged_user_id = get_current_user_id();
			$groups_id              = learndash_get_administrators_group_ids( $current_logged_user_id );
			if ( $groups_id ) {
				$args            = array(
					'numberposts' => 1,
					'post_type'   => 'groups',
					'post__in'    => $groups_id,
					'orderby'     => 'menu_order',
					'order'       => 'DESC',
					'post_parent' => 0,
				);
				$parent_group    = get_posts( $args );
				$parent_group_id = 0;
				if ( ! empty( $parent_group ) ) {
					return true;

				} else {
					return false;
				}
			}
		}
		return false;
	}
}
