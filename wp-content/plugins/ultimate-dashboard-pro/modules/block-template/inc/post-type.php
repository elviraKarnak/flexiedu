<?php
/**
 * Setup block editor template post type.
 *
 * @package Ultimate_Dashboard_Pro
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {

	// Labels.
	$labels = array(
		'name'               => __( 'Block Editor Templates', 'ultimatedashboard' ),
		'singular_name'      => __( 'Block Editor Template', 'ultimatedashboard' ),
		'menu_name'          => __( 'Block Editor Templates', 'ultimatedashboard' ),
		'name_admin_bar'     => __( 'Block Editor Template', 'ultimatedashboard' ),
		'add_new'            => __( 'Add New', 'ultimatedashboard' ),
		'add_new_item'       => __( 'Add Block Editor Template', 'ultimatedashboard' ),
		'new_item'           => __( 'New Block Editor Template', 'ultimatedashboard' ),
		'edit_item'          => __( 'Edit Block Editor Template', 'ultimatedashboard' ),
		'view_item'          => __( 'View Block Editor Template', 'ultimatedashboard' ),
		'all_items'          => __( 'Block Editor Templates', 'ultimatedashboard' ),
		'search_items'       => __( 'Search Block Editor Templates', 'ultimatedashboard' ),
		'not_found'          => __( 'No Block Editor Templates found.', 'ultimatedashboard' ),
		'not_found_in_trash' => __( 'No Block Editor Templates in Trash.', 'ultimatedashboard' ),
	);

	// Change capabilities so only users that can 'manage_options' are able to access Block Editor Templates.
	$capabilities = array(
		'edit_post'          => apply_filters( 'udb_settings_capability', 'manage_options' ),
		'read_post'          => apply_filters( 'udb_settings_capability', 'manage_options' ),
		'delete_post'        => apply_filters( 'udb_settings_capability', 'manage_options' ),
		'delete_posts'       => apply_filters( 'udb_settings_capability', 'manage_options' ),
		'edit_posts'         => apply_filters( 'udb_settings_capability', 'manage_options' ),
		'edit_others_posts'  => apply_filters( 'udb_settings_capability', 'manage_options' ),
		'publish_posts'      => apply_filters( 'udb_settings_capability', 'manage_options' ),
		'read_private_posts' => apply_filters( 'udb_settings_capability', 'manage_options' ),
		'create_posts'       => apply_filters( 'udb_settings_capability', 'manage_options' ),
	);

	// Arguments.
	$args = array(
		'labels'              => $labels,
		'public'              => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'show_in_menu'        => false,
		'show_in_rest'        => true,
		'query_var'           => false,
		'rewrite'             => array( 'slug' => 'udb-block-template' ),
		'map_meta_cap'        => false,
		'capabilities'        => $capabilities,
		'has_archive'         => false,
		'hierarchical'        => false,
		'supports'            => array( 'title', 'editor', 'custom-fields' ),
	);

	register_post_type( 'udb_block_template', $args );

};
