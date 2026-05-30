<?php
/*
Plugin Name: Music Post Type
Plugin URI: http://yourwebsite.com/
Description: A plugin to create a custom post type for Music.
Version: 1.0.0
Author: Your Name
Author URI: http://yourwebsite.com/
License: GPL2
*/

// Register Custom Post Type
function create_music_post_type() {
    $labels = array(
        'name'               => _x('Music', 'post type general name'),
        'singular_name'      => _x('Music', 'post type singular name'),
        'menu_name'          => _x('Music', 'admin menu'),
        'name_admin_bar'     => _x('Music', 'add new on admin bar'),
        'add_new'            => _x('Add New', 'music'),
        'add_new_item'       => __('Add New Music'),
        'new_item'           => __('New Music'),
        'edit_item'          => __('Edit Music'),
        'view_item'          => __('View Music'),
        'all_items'          => __('All Music'),
        'search_items'       => __('Search Music'),
        'parent_item_colon'  => __('Parent Music:'),
        'not_found'          => __('No music found.'),
        'not_found_in_trash' => __('No music found in Trash.')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'music', 'with_front' => false),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'menu_icon'          => 'dashicons-album'
    );

    register_post_type('music', $args);
}

// Register Custom Taxonomies
function create_music_taxonomies() {
    // Genres taxonomy
    $labels = array(
        'name'              => _x('Genres', 'taxonomy general name'),
        'singular_name'     => _x('Genre', 'taxonomy singular name'),
        'search_items'      => __('Search Genres'),
        'all_items'         => __('All Genres'),
        'parent_item'       => __('Parent Genre'),
        'parent_item_colon' => __('Parent Genre:'),
        'edit_item'         => __('Edit Genre'),
        'update_item'       => __('Update Genre'),
        'add_new_item'      => __('Add New Genre'),
        'new_item_name'     => __('New Genre Name'),
        'menu_name'         => __('Genre'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'music-genre', 'with_front' => false),
    );

    register_taxonomy('music_genre', array('music'), $args);

    // Music Type taxonomy
    $labels = array(
        'name'              => _x('Music Types', 'taxonomy general name'),
        'singular_name'     => _x('Music Type', 'taxonomy singular name'),
        'search_items'      => __('Search Music Types'),
        'all_items'         => __('All Music Types'),
        'parent_item'       => __('Parent Music Type'),
        'parent_item_colon' => __('Parent Music Type:'),
        'edit_item'         => __('Edit Music Type'),
        'update_item'       => __('Update Music Type'),
        'add_new_item'      => __('Add New Music Type'),
        'new_item_name'     => __('New Music Type Name'),
        'menu_name'         => __('Music Type'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'music-type', 'with_front' => false),
    );

    register_taxonomy('music_type', array('music'), $args);
}

// Hook into the 'init' action
add_action('init', 'create_music_post_type', 0);
add_action('init', 'create_music_taxonomies', 0);

/**
 *
 * Make a page content full width or contained.
 *
 * @param $is_contained
 *
 * @return bool|mixed
 */
function music_single_fullwidth( $is_contained ) {
    if ( is_singular( 'music' ) ) {
        $is_contained = false;
    }

    return $is_contained;
}
add_filter( 'fivetwofive_theme_is_contained', 'music_single_fullwidth' );

// Flush rewrite rules on activation/deactivation
function music_rewrite_flush() {
    create_music_post_type();
    create_music_taxonomies();
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'music_rewrite_flush');
register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
?>