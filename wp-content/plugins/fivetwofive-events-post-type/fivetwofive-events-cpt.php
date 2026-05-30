<?php
/**
 * Plugin Name: Events Custom Post Type
 * Plugin URI: https://fivetwofive.com/
 * Description: A simple plug in that adds an event custom post type
 * Version: 1.0.0
 * Author: FiveTwoFive Creative Team
 * Author URI: https://fivetwofive.com/
 * License: GPL2
 * Text Domain: fivetwofive-event
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
 * Register events custom post type.
 *
 * @return void
 */
function ftf_register_events_cpt() {

	/**
	 * Post Type: FiveTwoFive Events.
	 */
	$labels = array(
		'name'                  => __( 'Events', 'fivetwofive-event' ),
		'singular_name'         => __( 'Event', 'fivetwofive-event' ),
		'menu_name'             => __( 'Events', 'fivetwofive-event' ),
		'all_items'             => __( 'All Events', 'fivetwofive-event' ),
		'add_new'               => __( 'Add New Event', 'fivetwofive-event' ),
		'add_new_item'          => __( 'Add New Event', 'fivetwofive-event' ),
		'edit_item'             => __( 'Edit Event', 'fivetwofive-event' ),
		'new_item'              => __( 'New Event', 'fivetwofive-event' ),
		'view_item'             => __( 'View Event', 'fivetwofive-event' ),
		'view_items'            => __( 'View Events', 'fivetwofive-event' ),
		'search_items'          => __( 'Search Event', 'fivetwofive-event' ),
		'not_found'             => __( 'Event Not Found', 'fivetwofive-event' ),
		'not_found_in_trash'    => __( 'No Events found in trash', 'fivetwofive-event' ),
		'parent_item_colon'     => __( 'Parent Event', 'fivetwofive-event' ),
		'featured_image'        => __( 'Featured image for this Event', 'fivetwofive-event' ),
		'set_featured_image'    => __( 'Set featured image for this Event', 'fivetwofive-event' ),
		'remove_featured_image' => __( 'Remove featured image for this Event', 'fivetwofive-event' ),
		'use_featured_image'    => __( 'Use featured image for this Event', 'fivetwofive-event' ),
		'archives'              => __( 'Event archives', 'fivetwofive-event' ),
		'insert_into_item'      => __( 'Insert into Event', 'fivetwofive-event' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Event', 'fivetwofive-event' ),
		'filter_items_list'     => __( 'Filter Events list', 'fivetwofive-event' ),
		'items_list_navigation' => __( 'Events list navigation', 'fivetwofive-event' ),
		'items_list'            => __( 'Events list', 'fivetwofive-event' ),
		'attributes'            => __( 'Events Attributes', 'fivetwofive-event' ),
	);

	$args = array(
		'label'               => __( 'Events', 'fivetwofive-event' ),
		'labels'              => $labels,
		'description'         => __( 'Event Description', 'fivetwofive-event' ),
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'rest_base'           => '',
		'has_archive'         => false,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'menu_icon'           => 'dashicons-megaphone',
		'show_in_nav_menus'   => true,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'rewrite'             => array(
			'slug'       => 'events',
			'with_front' => false,
		),
		'query_var'           => true,
		'supports'            => array( 'title', 'author', 'thumbnail', 'excerpt' ),
	);

	register_post_type( 'ftf_event', $args );
}
add_action( 'init', 'ftf_register_events_cpt' );

/**
 * Add events post meta in fivetwofive theme.
 *
 * @param int    $post_item_id post item id.
 * @param string $post_type post type.
 * @return void
 */
function ftf_events_post_meta( $post_item_id, $post_type ) {
	if ( 'ftf_event' === $post_type ) {
		$event_type       = get_field( 'ftf_event_type', $post_item_id );
		$event_start_date = get_field( 'ftf_event_start_date', $post_item_id );
		$event_end_date   = get_field( 'ftf_event_end_date', $post_item_id );
		$event_start_time = get_field( 'ftf_event_start_time', $post_item_id );
		ob_start();
		?>
		<p class="ftf-event-meta">
			<span class="ftf-event-type"><?php echo esc_html( $event_type ); ?></span>
			<span class="ftf-event-date"><?php echo esc_html( $event_start_date ); ?> - <?php echo esc_html( $event_end_date ); ?></span>
			<?php if ( $event_start_time ) : ?>
				<span class="ftf-event-time"><?php echo esc_html( $event_start_time ); ?></span>
			<?php endif; ?>
		</p>
		<?php
		echo wp_kses_post( ob_get_clean() );
	}
}
add_action( 'fivetwofive_theme_after_post_meta', 'ftf_events_post_meta', 10, 2 );

/**
 * Make the events archive full width.
 *
 * @return boolean $enable Make the events archive full width.
 */
function ftf_events_archive_disable_sidebar( $enable_sidebar ) {
	if ( is_post_type_archive( 'ftf_event' ) ) {
		$enable_sidebar = false;
	}

	return $enable_sidebar;
}
add_filter( 'fivetwofive_theme_enable_sidebar', 'ftf_events_archive_disable_sidebar' );

/**
 *
 * Make a page content full width or contained.
 *
 * @param $is_contained
 *
 * @return bool|mixed
 */
function ftf_events_single_fullwidth( $is_contained ) {
	if ( is_singular( 'ftf_event' ) ) {
		$is_contained = false;
	}

	return $is_contained;
}
add_filter( 'fivetwofive_theme_is_contained', 'ftf_events_single_fullwidth' );

/**
 * Register custom post type on plugin activation.
 *
 * @return void
 */
function ftf_setup_events_cpt() {
	ftf_register_events_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ftf_setup_events_cpt' );

/**
 * Unregister custom post type on plugin deactivation.
 *
 * @link https://core.trac.wordpress.org/ticket/42563
 * @return void
 */
function ftf_unregister_events_cpt() {
	unregister_post_type( 'ftf_events' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ftf_unregister_events_cpt' );
