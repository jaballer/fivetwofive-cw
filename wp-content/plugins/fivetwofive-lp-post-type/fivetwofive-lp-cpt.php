<?php
/**
 * Plugin Name: Landing Page Custom Post Type
 * Plugin URI: https://fivetwofive.com/
 * Description: A simple plug in that adds an lp custom post type
 * Version: 1.0.0
 * Author: FiveTwoFive Creative Team
 * Author URI: https://fivetwofive.com/
 * License: GPL2
 * Text Domain: ftf-lps
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

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Register Landing Pages custom post type.
 *
 * @return void
 */
function fivetwofive_lp_cpt() {

	/**
	 * Post Type: Landing Pages.
	 */

	$labels = array(
		'name'                  => __( 'Landing Pages', 'ftf-lps' ),
		'singular_name'         => __( 'Landing Pages', 'ftf-lps' ),
		'menu_name'             => __( 'Landing Pages', 'ftf-lps' ),
		'all_items'             => __( 'All Landing Pages', 'ftf-lps' ),
		'add_new'               => __( 'Add New Landing Pages', 'ftf-lps' ),
		'add_new_item'          => __( 'Add New Landing Pages', 'ftf-lps' ),
		'edit_item'             => __( 'Edit Landing Pages', 'ftf-lps' ),
		'new_item'              => __( 'New Landing Pages', 'ftf-lps' ),
		'view_item'             => __( 'View Landing Pages', 'ftf-lps' ),
		'view_items'            => __( 'View Landing Pages', 'ftf-lps' ),
		'search_items'          => __( 'Search Landing Pages', 'ftf-lps' ),
		'not_found'             => __( 'Landing Pages Not Found', 'ftf-lps' ),
		'not_found_in_trash'    => __( 'No Landing Pages found in trash', 'ftf-lps' ),
		'featured_image'        => __( 'Featured image for this Landing Pages', 'ftf-lps' ),
		'set_featured_image'    => __( 'Set featured image for this Landing Pages', 'ftf-lps' ),
		'remove_featured_image' => __( 'Remove featured image for this Landing Pages', 'ftf-lps' ),
		'use_featured_image'    => __( 'Use featured image for this Landing Pages', 'ftf-lps' ),
		'archives'              => __( 'Landing Pages archives', 'ftf-lps' ),
		'insert_into_item'      => __( 'Insert into Landing Pages', 'ftf-lps' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Landing Pages', 'ftf-lps' ),
		'filter_items_list'     => __( 'Filter Landing Pages list', 'ftf-lps' ),
		'items_list_navigation' => __( 'Landing Pages list navigation', 'ftf-lps' ),
		'items_list'            => __( 'Landing Pages list', 'ftf-lps' ),
		'attributes'            => __( 'Landing Pages Attributes', 'ftf-lps' ),
		'parent_item_colon'     => __( 'Parent Landing Pages', 'ftf-lps' ),
	);

	$args = array(
		'label'               => __( 'Landing Pages', 'ftf-lps' ),
		'labels'              => $labels,
		'description'         => __( 'Landing Pages Description', 'ftf-lps' ),
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_rest'        => false,
		'rest_base'           => '',
		'has_archive'         => false,
		'show_in_menu'        => true,
		'menu_position'       => '5',
		'menu_icon'           => 'dashicons-pressthis',
		'show_in_nav_menus'   => true,
		'exclude_from_search' => false,
		'capability_type'     => "post",
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'rewrite'             => array( 'slug' => 'lp', 'with_front' => false ),
		'query_var'           => true,
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'page-attributes' ),
		'taxonomies'          => array( 'category', 'post_tag' ),
	);

	register_post_type( 'fivetwofive-lp', $args );
}

// Attach the custom post type function to WordPress's 'init' action hook.
add_action('init', 'fivetwofive_lp_cpt');

/**
 * Actions to perform upon plugin activation.
 *
 * When the plugin is activated, this function ensures the custom post type
 * is registered and that rewrite rules are properly flushed to take account of it.
 */
function ftf_setup_fivetwofive_lp_custom_post_type() {
    fivetwofive_lp_cpt();
    flush_rewrite_rules();
}
// Attach the activation function to WordPress's plugin activation hook.
register_activation_hook(__FILE__, 'ftf_setup_fivetwofive_lp_custom_post_type');

/**
 * Actions to perform upon plugin deactivation.
 *
 * When the plugin is deactivated, this function ensures the custom post type
 * is unregistered and that rewrite rules are flushed to reflect this change.
 */
function ftf_unregister_fivetwofive_lp_custom_post_type() {
    unregister_post_type('fivetwofive-lp');
    flush_rewrite_rules();
}
// Attach the deactivation function to WordPress's plugin deactivation hook.
register_deactivation_hook(__FILE__, 'ftf_unregister_fivetwofive_lp_custom_post_type');

/**
 * Modifies the post editor depending on the chosen template.
 *
 * For landing pages that use a specific template, the default content editor might not be
 * applicable or necessary. This function checks the template of a landing page and, if it
 * matches a specific template, removes the content editor.
 */
function ftf_remove_editor_init() {
    // Check if we're in the admin dashboard; if not, exit.
    if (!is_admin()) {
        return;
    }

    // Retrieve the post ID.
    $post_id = filter_input(INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT) ?: filter_input(INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT);

    // If there's no post ID, or the post type isn't our custom type, exit.
    if (!$post_id || 'fivetwofive-lp' !== get_post_type($post_id)) {
        return;
    }

    // Fetch the template associated with the post.
    $template_file = get_post_meta($post_id, '_wp_page_template', true);

    // If the template matches our target template, remove the content editor.
    if ('page-templates/template-module.php' === $template_file) {
        remove_post_type_support('fivetwofive-lp', 'editor');
    }
}
// Attach the function to the 'admin_init' hook, ensuring it runs in the admin dashboard.
add_action('admin_init', 'ftf_remove_editor_init');