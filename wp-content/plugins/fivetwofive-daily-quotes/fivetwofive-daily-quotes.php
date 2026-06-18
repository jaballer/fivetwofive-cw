<?php
/*
Plugin Name: Daily Quotes
Description: A plugin to publish daily quotes.
Version: 1.0.0
Author: FiveTwoFive Creative Team
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function create_daily_quote_cpt() {

    $labels = array(
        'name' => _x('Daily Quotes', 'Post Type General Name', 'ftf-quotes'),
        'singular_name' => _x('Daily Quote', 'Post Type Singular Name', 'ftf-quotes'),
        'menu_name' => __('Daily Quotes', 'ftf-quotes'),
        'name_admin_bar' => __('Daily Quote', 'ftf-quotes'),
        'archives' => __('Quote Archives', 'ftf-quotes'),
        'attributes' => __('Quote Attributes', 'ftf-quotes'),
        'parent_item_colon' => __('Parent Quote:', 'ftf-quotes'),
        'all_items' => __('All Quotes', 'ftf-quotes'),
        'add_new_item' => __('Add New Quote', 'ftf-quotes'),
        'add_new' => __('Add New', 'ftf-quotes'),
        'new_item' => __('New Quote', 'ftf-quotes'),
        'edit_item' => __('Edit Quote', 'ftf-quotes'),
        'update_item' => __('Update Quote', 'ftf-quotes'),
        'view_item' => __('View Quote', 'ftf-quotes'),
        'view_items' => __('View Quotes', 'ftf-quotes'),
        'search_items' => __('Search Quote', 'ftf-quotes'),
        'not_found' => __('Not found', 'ftf-quotes'),
        'not_found_in_trash' => __('Not found in Trash', 'ftf-quotes'),
        'featured_image' => __('Featured Image', 'ftf-quotes'),
        'set_featured_image' => __('Set featured image', 'ftf-quotes'),
        'remove_featured_image' => __('Remove featured image', 'ftf-quotes'),
        'use_featured_image' => __('Use as featured image', 'ftf-quotes'),
        'insert_into_item' => __('Insert into quote', 'ftf-quotes'),
        'uploaded_to_this_item' => __('Uploaded to this quote', 'ftf-quotes'),
        'items_list' => __('Quotes list', 'ftf-quotes'),
        'items_list_navigation' => __('Quotes list navigation', 'ftf-quotes'),
        'filter_items_list' => __('Filter quotes list', 'ftf-quotes'),
    );
    $args = array(
        'label' => __('Daily Quote', 'ftf-quotes'),
        'description' => __('Post Type Description', 'ftf-quotes'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'rewrite'        => array(
            'slug'       => 'quotes',
            'with_front' => false,
        ),
        'exclude_from_search' => false,
        'capability_type' => 'post',
    );
    register_post_type('daily_quote', $args);

}
add_action('init', 'create_daily_quote_cpt', 0);

// Shortcode to display the latest daily quote
function display_latest_daily_quote() {
    $args = array(
        'post_type' => 'daily_quote',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $quote_query = new WP_Query($args);

    if ($quote_query->have_posts()) {
        while ($quote_query->have_posts()) {
            $quote_query->the_post();
            $content = '<div class="daily-quote">';
            $content .= '<h2>' . get_the_title() . '</h2>';
            $content .= '<div>' . get_the_content() . '</div>';
            $content .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $content = '<p>No daily quotes found.</p>';
    }

    return $content;
}
add_shortcode('daily_quote', 'display_latest_daily_quote');
?>
