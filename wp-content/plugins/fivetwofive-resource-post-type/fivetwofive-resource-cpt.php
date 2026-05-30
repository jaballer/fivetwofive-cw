<?php
/**
 * Plugin Name: Resource Custom Post Type
 * Plugin URI: https://fivetwofive.com/
 * Description: A simple plug in that adds an resources custom post type
 * Version: 1.0.0
 * Author: FiveTwoFive Creative Team
 * Author URI: https://fivetwofive.com/
 * License: GPL2
 * Text Domain: ftf-resources
 */

/*
	Copyright 2020: FiveTwoFive Creative Team

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
 * Register resource custom post type.
 *
 * @return void
 */
function ftf_register_resource_cpt() {

	/**
	 * Post Type: Resource.
	 */

	$labels = array(
		'name'                  => __( 'Resources', 'ftf-resources' ),
		'singular_name'         => __( 'Resource', 'ftf-resources' ),
		'menu_name'             => __( 'Resources', 'ftf-resources' ),
		'all_items'             => __( 'All Resources', 'ftf-resources' ),
		'add_new'               => __( 'Add New Resource', 'ftf-resources' ),
		'add_new_item'          => __( 'Add New Resource', 'ftf-resources' ),
		'edit_item'             => __( 'Edit Resource', 'ftf-resources' ),
		'new_item'              => __( 'New Resource', 'ftf-resources' ),
		'view_item'             => __( 'View Resource', 'ftf-resources' ),
		'view_items'            => __( 'View Resources', 'ftf-resources' ),
		'search_items'          => __( 'Search Resource', 'ftf-resources' ),
		'not_found'             => __( 'Resource Not Found', 'ftf-resources' ),
		'not_found_in_trash'    => __( 'No Resources found in trash', 'ftf-resources' ),
		'parent_item_colon'     => __( 'Parent Resource', 'ftf-resources' ),
		'featured_image'        => __( 'Featured image for this Resource', 'ftf-resources' ),
		'set_featured_image'    => __( 'Set featured image for this Resource', 'ftf-resources' ),
		'remove_featured_image' => __( 'Remove featured image for this Resource', 'ftf-resources' ),
		'use_featured_image'    => __( 'Use featured image for this Resource', 'ftf-resources' ),
		'archives'              => __( 'Resource archives', 'ftf-resources' ),
		'insert_into_item'      => __( 'Insert into Resource', 'ftf-resources' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Resource', 'ftf-resources' ),
		'filter_items_list'     => __( 'Filter Resources list', 'ftf-resources' ),
		'items_list_navigation' => __( 'Resources list navigation', 'ftf-resources' ),
		'items_list'            => __( 'Resources list', 'ftf-resources' ),
		'attributes'            => __( 'Resources Attributes', 'ftf-resources' ),
		'parent_item_colon'     => __( 'Parent Resource', 'ftf-resources' ),
	);

	$args = array(
		'label'               => __( 'Resources', 'ftf-resources' ),
		'labels'              => $labels,
		'description'         => __( 'Resource Description', 'ftf-resources' ),
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'rest_base'           => 'ftf-resources',
		'has_archive'         => false,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-pressthis',
		'show_in_nav_menus'   => true,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'rewrite'             => array(
			'slug'       => 'resources',
			'with_front' => false,
		),
		'query_var'           => true,
		'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
	);

	register_post_type( 'ftf_resource', $args );

	unset( $args );
	unset( $labels );

	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name', 'ftf-resources' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name', 'ftf-resources' ),
		'search_items'      => __( 'Search Categories', 'ftf-resources' ),
		'all_items'         => __( 'All Categories', 'ftf-resources' ),
		'parent_item'       => __( 'Parent Category', 'ftf-resources' ),
		'parent_item_colon' => __( 'Parent Category:', 'ftf-resources' ),
		'edit_item'         => __( 'Edit Category', 'ftf-resources' ),
		'update_item'       => __( 'Update Category', 'ftf-resources' ),
		'add_new_item'      => __( 'Add New Category', 'ftf-resources' ),
		'new_item_name'     => __( 'New Category Name', 'ftf-resources' ),
		'menu_name'         => __( 'Categories', 'ftf-resources' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'      => true,
		'rest_base'         => 'ftf-resource-categories',
		'rewrite'           => array(
			'slug'       => 'resource-categories',
			'with_front' => false,
		),
		'default_term'      => array(
			'name' => __( 'Uncategorized', 'ftf-resources' ),
			'slug' => 'uncategorized',
		),
	);

	register_taxonomy( 'ftf_resource_category', 'ftf_resource', $args );

	unset( $args );
	unset( $labels );

	$labels = array(
		'name'              => _x( 'Tags', 'taxonomy general name', 'ftf-resources' ),
		'singular_name'     => _x( 'Tag', 'taxonomy singular name', 'ftf-resources' ),
		'search_items'      => __( 'Search Tags', 'ftf-resources' ),
		'all_items'         => __( 'All Tags', 'ftf-resources' ),
		'parent_item'       => __( 'Parent Tag', 'ftf-resources' ),
		'parent_item_colon' => __( 'Parent Tag:', 'ftf-resources' ),
		'edit_item'         => __( 'Edit Tag', 'ftf-resources' ),
		'update_item'       => __( 'Update Tag', 'ftf-resources' ),
		'add_new_item'      => __( 'Add New Tag', 'ftf-resources' ),
		'new_item_name'     => __( 'New Tag Name', 'ftf-resources' ),
		'menu_name'         => __( 'Tags', 'ftf-resources' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'show_in_rest'      => true,
		'rest_base'         => 'ftf-resource-tags',
		'rewrite'           => array(
			'slug'       => 'resource-tags',
			'with_front' => false,
		),
	);

	register_taxonomy( 'ftf_resource_tag', 'ftf_resource', $args );
}
add_action( 'init', 'ftf_register_resource_cpt' );

/**
 * Register creative custom post type.
 *
 * @return void
 */
function ftf_register_creative_cpt() {
	/**
	 * Post Type: Creative.
	 */
	$labels = array(
		'name'                  => __( 'Creatives', 'ftf-resources' ),
		'singular_name'         => __( 'Creative', 'ftf-resources' ),
		'menu_name'             => __( 'Creatives', 'ftf-resources' ),
		'all_items'             => __( 'All Creatives', 'ftf-resources' ),
		'add_new'               => __( 'Add New Creative', 'ftf-resources' ),
		'add_new_item'          => __( 'Add New Creative', 'ftf-resources' ),
		'edit_item'             => __( 'Edit Creative', 'ftf-resources' ),
		'new_item'              => __( 'New Creative', 'ftf-resources' ),
		'view_item'             => __( 'View Creative', 'ftf-resources' ),
		'view_items'            => __( 'View Creative', 'ftf-resources' ),
		'search_items'          => __( 'Search Creative', 'ftf-resources' ),
		'not_found'             => __( 'Creative Not Found', 'ftf-resources' ),
		'not_found_in_trash'    => __( 'No Creatives found in trash', 'ftf-resources' ),
		'parent_item_colon'     => __( 'Parent Creative', 'ftf-resources' ),
		'featured_image'        => __( 'Avatar', 'ftf-resources' ),
		'set_featured_image'    => __( 'Set avatar', 'ftf-resources' ),
		'remove_featured_image' => __( 'Remove avatar', 'ftf-resources' ),
		'use_featured_image'    => __( 'Use avatar', 'ftf-resources' ),
		'archives'              => __( 'Creative archives', 'ftf-resources' ),
		'insert_into_item'      => __( 'Insert into Creative', 'ftf-resources' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Creative', 'ftf-resources' ),
		'filter_items_list'     => __( 'Filter Creatives list', 'ftf-resources' ),
		'items_list_navigation' => __( 'Creatives list navigation', 'ftf-resources' ),
		'items_list'            => __( 'Creatives list', 'ftf-resources' ),
		'attributes'            => __( 'Creatives Attributes', 'ftf-resources' ),
	);

	$args = array(
		'label'               => __( 'Creatives', 'ftf-resources' ),
		'labels'              => $labels,
		'description'         => __( 'Creative Description', 'ftf-resources' ),
		'public'              => false,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'rest_base'           => 'ftf-creatives',
		'has_archive'         => false,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-art',
		'show_in_nav_menus'   => true,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'rewrite'             => array(
			'slug'       => 'creatives',
			'with_front' => false,
		),
		'query_var'           => true,
		'supports'            => array( 'title', 'thumbnail' ),
	);

	register_post_type( 'ftf_creative', $args );
}
add_action( 'init', 'ftf_register_creative_cpt' );

/**
 * Filters the title field placeholder text.
 *
 * @param string $title Placeholder text. Default 'Add title'.
 * @return string $title Placeholder text. Default 'Add title'.
 */
function ftf_admin_change_default_title( $title ){
    $screen = get_current_screen();

    if  ( 'ftf_creative' == $screen->post_type ) {
        $title = __( 'Add  Creative Name', 'ftf-resources' );
    }

    if  ( 'ftf_resource' == $screen->post_type ) {
        $title = __( 'Add Resource Title', 'ftf-resources' );
    }

    return $title;
}
add_filter( 'enter_title_here', 'ftf_admin_change_default_title' );

/**
 * Register custom post type on plugin activation.
 *
 * @return void
 */
function ftf_setup_resources_custom_post_type() {
	ftf_register_creative_cpt();
	ftf_register_resource_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ftf_setup_resources_custom_post_type' );

/**
 * Unregister custom post type on plugin deactivation.
 *
 * @link https://core.trac.wordpress.org/ticket/42563
 * @return void
 */
function ftf_unregister_resource_custom_post_type() {
	unregister_post_type( 'ftf_resource' );
	unregister_post_type( 'ftf_creative' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ftf_unregister_resource_custom_post_type' );

/**
 * Add resource item thumbnail.
 */
function ftf_resource_image_size() {
	add_image_size( 'ftf-resource-thumb', 415, 245, true );
}
add_action( 'after_setup_theme', 'ftf_resource_image_size' );

/**
 * Add resource post meta in fivetwofive theme.
 *
 * @param int    $post_item_id post item id.
 * @param string $post_type post type.
 * @return void
 */
function ftf_resource_post_meta( $post_item_id, $post_type ) {
	if ( 'ftf_resource' === $post_type ) {
		ob_start();
		fivetwofive_theme_posted_on( $post_item_id );
		echo ob_get_clean();
	}
}
add_action( 'fivetwofive_theme_after_post_meta', 'ftf_resource_post_meta', 10, 2 );

/**
 * Add ftf_resource post type to have formatted date field in rest API.
 *
 * @param array $post_array post array.
 * @return array $post_array post array.
 */
function ftf_resource_rest_api_formatted_date_field( $post_array ) {
	$post_array[] = 'ftf_resource';
	return $post_array;
}
add_filter( 'fivetwofive_rest_api_formatted_date', 'ftf_resource_rest_api_formatted_date_field' );

/**
 * Make single resource page full width.
 *
 * @param boolean $is_contained if the page contained or not.
 * @return boolean $is_contained if the page contained or not.
 */
function ftf_resource_single_page_container( $is_contained ) {
	if ( is_singular( 'ftf_resource' ) ) {
		$is_contained = false;
	}
	return $is_contained;
}
add_filter( 'fivetwofive_theme_is_contained', 'ftf_resource_single_page_container' );

/**
 * Add a Formatted Date to the WordPress REST API JSON Post Object
 */
function ftf_resource_rest_api_init() {
	register_rest_field(
		apply_filters( 'ftf_resource_rest_api_categories', array( 'ftf_resource' ) ),
		'ftf_resource_categories',
		array(
			'get_callback'    => function() {
				$resource_categories = get_the_terms( get_the_ID(), 'ftf_resource_category' );
				$categories          = array();

				foreach ( $resource_categories as $resource_category ) {
					$categories[] = array(
						'name' => esc_html( $resource_category->name ),
						'link' => esc_url( get_category_link( $resource_category->term_id ) ),
					);
				}

				return $categories;
			},
			'update_callback' => null,
			'schema'          => null,
		)
	);

	register_rest_field(
		apply_filters( 'ftf_resource_rest_api_tags', array( 'ftf_resource' ) ),
		'ftf_resource_tags',
		array(
			'get_callback'    => function() {
				$resource_tags = get_the_terms( get_the_ID(), 'ftf_resource_tag' );
				$tags    = array();

				if ( $resource_tags ) {
					foreach ( $resource_tags as $tag ) {
						$tags[] = array(
							'name' => esc_html( $tag->name ),
							'link' => esc_url( get_category_link( $tag->term_id ) ),
						);
					}
				}

				return $tags;
			},
			'update_callback' => null,
			'schema'          => null,
		)
	);
}
add_action( 'rest_api_init', 'ftf_resource_rest_api_init' );
