<?php
/**
 * Plugin Name: Work Custom Post Type
 * Plugin URI: https://fivetwofive.com/
 * Description: A simple plug in that adds a Work custom post type
 * Version: 1.0.0
 * Author: FiveTwoFive Creative Team
 * Author URI: https://fivetwofive.com/
 * License: GPL2
 * Text Domain: fivetwofive-work
 */

/*
	Copyright 2018: FiveTwoFive Creative Team

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program. If not, see someone who cares.
*/

/**
 * Register work custom post type.
 *
 * @return void
 */
function ftf_work_register_cpt() {

	/**
	 * Post Type: Work.
	 */

	$labels = array(
		'name'                  => __( 'Work', 'fivetwofive-work' ),
		'singular_name'         => __( 'Work', 'fivetwofive-work' ),
		'menu_name'             => __( 'Works', 'fivetwofive-work' ),
		'all_items'             => __( 'All Works', 'fivetwofive-work' ),
		'add_new'               => __( 'Add New Work', 'fivetwofive-work' ),
		'add_new_item'          => __( 'Add New Work', 'fivetwofive-work' ),
		'edit_item'             => __( 'Edit Work', 'fivetwofive-work' ),
		'new_item'              => __( 'New Work', 'fivetwofive-work' ),
		'view_item'             => __( 'View Work', 'fivetwofive-work' ),
		'view_items'            => __( 'View Works', 'fivetwofive-work' ),
		'search_items'          => __( 'Search Work', 'fivetwofive-work' ),
		'not_found'             => __( 'Work Not Found', 'fivetwofive-work' ),
		'not_found_in_trash'    => __( 'No Works found in trash', 'fivetwofive-work' ),
		'parent_item_colon'     => __( 'Parent Work', 'fivetwofive-work' ),
		'featured_image'        => __( 'Featured image for this Work', 'fivetwofive-work' ),
		'set_featured_image'    => __( 'Set featured image for this Work', 'fivetwofive-work' ),
		'remove_featured_image' => __( 'Remove featured image for this Work', 'fivetwofive-work' ),
		'use_featured_image'    => __( 'Use featured image for this Work', 'fivetwofive-work' ),
		'archives'              => __( 'Work archives', 'fivetwofive-work' ),
		'insert_into_item'      => __( 'Insert into Work', 'fivetwofive-work' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Work', 'fivetwofive-work' ),
		'filter_items_list'     => __( 'Filter Works list', 'fivetwofive-work' ),
		'item_published'        => __( 'Work published.', 'fivetwofive-work' ),
		'item_updated'          => __( 'Work updated.', 'fivetwofive-work' ),
		'items_list_navigation' => __( 'Works list navigation', 'fivetwofive-work' ),
		'items_list'            => __( 'Works list', 'fivetwofive-work' ),
		'item_link'             => __( 'Work Link', 'fivetwofive-work' ),
		'attributes'            => __( 'Works Attributes', 'fivetwofive-work' ),
		'parent_item_colon'     => __( 'Parent Work', 'fivetwofive-work' ),
	);

	$args = array(
		'label'               => __( 'Work', 'fivetwofive-work' ),
		'labels'              => $labels,
		'description'         => __( 'Work Description', 'fivetwofive-work' ),
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'rest_base'           => '',
		'has_archive'         => false,
		'show_in_menu'        => true,
		'menu_position'       => '5',
		'menu_icon'           => 'dashicons-hammer',
		'show_in_nav_menus'   => true,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'rewrite'             => array(
			'slug'       => 'work',
			'with_front' => false,
		),
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'query_var'           => true,
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
	);

	register_post_type( 'ftf_work', $args );

	unset( $args );
	unset( $labels );

	$labels = array(
		'name'              => _x( 'Work Categories', 'taxonomy general name', 'fivetwofive-work' ),
		'singular_name'     => _x( 'Work Category', 'taxonomy singular name', 'fivetwofive-work' ),
		'search_items'      => __( 'Search Work Categories', 'fivetwofive-work' ),
		'all_items'         => __( 'All Work Categories', 'fivetwofive-work' ),
		'parent_item'       => __( 'Parent Work Category', 'fivetwofive-work' ),
		'parent_item_colon' => __( 'Parent Work Category:', 'fivetwofive-work' ),
		'edit_item'         => __( 'Edit Work Category', 'fivetwofive-work' ),
		'update_item'       => __( 'Update Work Category', 'fivetwofive-work' ),
		'add_new_item'      => __( 'Add New Work Category', 'fivetwofive-work' ),
		'new_item_name'     => __( 'New Work Category Name', 'fivetwofive-work' ),
		'menu_name'         => __( 'Work Categories', 'fivetwofive-work' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array(
			'slug'       => 'work-category',
			'with_front' => false,
		),
	);

	register_taxonomy( 'ftf_work_category', 'ftf_work', $args );
}
add_action( 'init', 'ftf_work_register_cpt' );

/**
 * Make the work archive full width.
 *
 * @return boolean $enable Make the work archive full width.
 */
function ftf_work_archive_disable_sidebar( $enable_sidebar ) {
	if ( is_post_type_archive( 'ftf_work' ) ) {
		$enable_sidebar = false;
	}

	return $enable_sidebar;
}
add_filter( 'fivetwofive_theme_enable_sidebar', 'ftf_work_archive_disable_sidebar' );

/**
 * Register custom post type on plugin activation.
 *
 * @return void
 */
function ftf_work_setup_custom_post_type() {
	ftf_work_register_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ftf_work_setup_custom_post_type' );

/**
 * Unregister custom post type on plugin deactivation.
 *
 * @link https://core.trac.wordpress.org/ticket/42563
 * @return void
 */
function ftf_work_unregister_cpt() {
	unregister_post_type( 'featured-projects' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ftf_work_unregister_cpt' );

/**
 * Register Works image sizes
 *
 * @return void
 */
function ftf_work_theme_setup() {
	add_image_size( 'fivetwofive-work-thumbnail', 600, 450, true );
}
add_action( 'after_setup_theme', 'ftf_work_theme_setup' );


/**
 * Enqueue plugin assets on the gutenberg editor.
 *
 * @return void
 */
function ftf_work_block_editor_assets() {
	wp_enqueue_script(
		'ftf-work-block-variations',
		plugins_url( 'blocks/variations.js', __FILE__ ),
		array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ),
		filemtime( plugin_dir_path( __FILE__ ) . '/blocks/variations.js' )
	);
}
add_action( 'enqueue_block_editor_assets', 'ftf_work_block_editor_assets' );

function ftf_work_block_styles() {
	wp_enqueue_style(
		'ftf-work-style-work-archive-cards',
		plugins_url( 'css/style-work-archive-cards.css', __FILE__ ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . '/css/style-work-archive-cards.css' )
	);
}
add_action( 'enqueue_block_assets', 'ftf_work_block_styles' );