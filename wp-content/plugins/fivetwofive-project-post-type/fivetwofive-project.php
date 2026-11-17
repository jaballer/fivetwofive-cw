<?php
/**
 * Plugin Name: Projects Custom Post Type
 * Plugin URI: https://fivetwofive.com/
 * Description: Adds a Projects custom post type (personal projects, the sibling of Work) and a Topic taxonomy shared by Work, Projects and posts.
 * Version: 1.0.0
 * Author: FiveTwoFive Creative Team
 * Author URI: https://fivetwofive.com/
 * License: GPL2
 * Text Domain: fivetwofive-project
 */

/*
	Copyright 2026: FiveTwoFive Creative Team

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 2 of the License, or
	any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post types the shared Topic taxonomy is attached to.
 *
 * @return string[]
 */
function ftf_project_topic_post_types() {
	return apply_filters( 'ftf_project_topic_post_types', array( 'ftf_work', 'ftf_project', 'post' ) );
}

/**
 * Allowed Status values (ACF select choices use the same keys).
 *
 * @return array<string,string> value => label
 */
function ftf_project_statuses() {
	return array(
		'idea'     => __( 'Idea', 'fivetwofive-project' ),
		'building' => __( 'Building', 'fivetwofive-project' ),
		'shipped'  => __( 'Shipped', 'fivetwofive-project' ),
		'archived' => __( 'Archived', 'fivetwofive-project' ),
	);
}

/**
 * Register the Projects post type and the shared Topic taxonomy.
 *
 * @return void
 */
function ftf_project_register_cpt() {

	$labels = array(
		'name'                  => __( 'Projects', 'fivetwofive-project' ),
		'singular_name'         => __( 'Project', 'fivetwofive-project' ),
		'menu_name'             => __( 'Projects', 'fivetwofive-project' ),
		'all_items'             => __( 'All Projects', 'fivetwofive-project' ),
		'add_new'               => __( 'Add New Project', 'fivetwofive-project' ),
		'add_new_item'          => __( 'Add New Project', 'fivetwofive-project' ),
		'edit_item'             => __( 'Edit Project', 'fivetwofive-project' ),
		'new_item'              => __( 'New Project', 'fivetwofive-project' ),
		'view_item'             => __( 'View Project', 'fivetwofive-project' ),
		'view_items'            => __( 'View Projects', 'fivetwofive-project' ),
		'search_items'          => __( 'Search Projects', 'fivetwofive-project' ),
		'not_found'             => __( 'No Projects found', 'fivetwofive-project' ),
		'not_found_in_trash'    => __( 'No Projects found in trash', 'fivetwofive-project' ),
		'featured_image'        => __( 'Featured image for this Project', 'fivetwofive-project' ),
		'set_featured_image'    => __( 'Set featured image for this Project', 'fivetwofive-project' ),
		'remove_featured_image' => __( 'Remove featured image for this Project', 'fivetwofive-project' ),
		'use_featured_image'    => __( 'Use as featured image for this Project', 'fivetwofive-project' ),
		'archives'              => __( 'Projects', 'fivetwofive-project' ),
		'insert_into_item'      => __( 'Insert into Project', 'fivetwofive-project' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Project', 'fivetwofive-project' ),
		'filter_items_list'     => __( 'Filter Projects list', 'fivetwofive-project' ),
		'item_published'        => __( 'Project published.', 'fivetwofive-project' ),
		'item_updated'          => __( 'Project updated.', 'fivetwofive-project' ),
		'items_list_navigation' => __( 'Projects list navigation', 'fivetwofive-project' ),
		'items_list'            => __( 'Projects list', 'fivetwofive-project' ),
		'item_link'             => __( 'Project Link', 'fivetwofive-project' ),
		'attributes'            => __( 'Project Attributes', 'fivetwofive-project' ),
	);

	$args = array(
		'label'               => __( 'Projects', 'fivetwofive-project' ),
		'labels'              => $labels,
		'description'         => __( 'Personal projects: side builds and experiments, shown next to paid Work.', 'fivetwofive-project' ),
		'public'              => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_rest'        => true,
		'rest_base'           => 'projects',
		'has_archive'         => 'projects',
		'show_in_menu'        => true,
		'menu_position'       => 6,
		'menu_icon'           => 'dashicons-lightbulb',
		'show_in_nav_menus'   => true,
		'exclude_from_search' => false,
		'capability_type'     => 'post',
		'rewrite'             => array(
			'slug'       => 'projects',
			'with_front' => false,
		),
		'map_meta_cap'        => true,
		'hierarchical'        => false,
		'query_var'           => true,
		'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
	);

	register_post_type( 'ftf_project', $args );

	$topic_labels = array(
		'name'                       => _x( 'Topics', 'taxonomy general name', 'fivetwofive-project' ),
		'singular_name'              => _x( 'Topic', 'taxonomy singular name', 'fivetwofive-project' ),
		'search_items'               => __( 'Search Topics', 'fivetwofive-project' ),
		'popular_items'              => __( 'Popular Topics', 'fivetwofive-project' ),
		'all_items'                  => __( 'All Topics', 'fivetwofive-project' ),
		'edit_item'                  => __( 'Edit Topic', 'fivetwofive-project' ),
		'view_item'                  => __( 'View Topic', 'fivetwofive-project' ),
		'update_item'                => __( 'Update Topic', 'fivetwofive-project' ),
		'add_new_item'               => __( 'Add New Topic', 'fivetwofive-project' ),
		'new_item_name'              => __( 'New Topic Name', 'fivetwofive-project' ),
		'separate_items_with_commas' => __( 'Separate topics with commas', 'fivetwofive-project' ),
		'add_or_remove_items'        => __( 'Add or remove topics', 'fivetwofive-project' ),
		'choose_from_most_used'      => __( 'Choose from the most used topics', 'fivetwofive-project' ),
		'not_found'                  => __( 'No topics found.', 'fivetwofive-project' ),
		'menu_name'                  => __( 'Topics', 'fivetwofive-project' ),
		'back_to_items'              => __( '&larr; Back to Topics', 'fivetwofive-project' ),
	);

	register_taxonomy(
		'topic',
		ftf_project_topic_post_types(),
		array(
			'hierarchical'      => false,
			'labels'            => $topic_labels,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'       => 'topic',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'ftf_project_register_cpt' );

/**
 * Seed the starting Topic terms. Skips any that already exist, so it is safe to run more than once.
 *
 * @return void
 */
function ftf_project_seed_topics() {
	$topics = array(
		'ai-agents'       => 'AI agents',
		'mcp'             => 'MCP',
		'email'           => 'Email',
		'wordpress'       => 'WordPress',
		'laravel'         => 'Laravel',
		'developer-tools' => 'Developer tools',
	);

	foreach ( $topics as $slug => $name ) {
		if ( ! term_exists( $slug, 'topic' ) ) {
			wp_insert_term( $name, 'topic', array( 'slug' => $slug ) );
		}
	}
}

/**
 * Register on activation, seed topics and flush rewrite rules so /projects/ and /topic/ resolve.
 *
 * @return void
 */
function ftf_project_activate() {
	ftf_project_register_cpt();
	ftf_project_seed_topics();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ftf_project_activate' );

/**
 * Unregister on deactivation and flush rewrite rules.
 *
 * @link https://core.trac.wordpress.org/ticket/42563
 * @return void
 */
function ftf_project_deactivate() {
	unregister_post_type( 'ftf_project' );
	unregister_taxonomy( 'topic' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ftf_project_deactivate' );

/**
 * Make the Projects and Topic archives full width (no sidebar), like the Work archive.
 *
 * @param bool $enable_sidebar Whether the sidebar is enabled.
 * @return bool
 */
function ftf_project_archive_disable_sidebar( $enable_sidebar ) {
	if ( is_post_type_archive( 'ftf_project' ) || is_tax( 'topic' ) ) {
		$enable_sidebar = false;
	}

	return $enable_sidebar;
}
add_filter( 'fivetwofive_theme_enable_sidebar', 'ftf_project_archive_disable_sidebar' );

/**
 * List the Projects archive newest first.
 *
 * @param WP_Query $query The main query.
 * @return void
 */
function ftf_project_archive_order( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive( 'ftf_project' ) ) {
		return;
	}

	$query->set( 'orderby', 'date' );
	$query->set( 'order', 'DESC' );
}
add_action( 'pre_get_posts', 'ftf_project_archive_order' );

/**
 * Use "Projects" rather than "Archives: Projects" as the archive heading.
 *
 * @param string $title Archive title.
 * @return string
 */
function ftf_project_archive_title( $title ) {
	if ( is_post_type_archive( 'ftf_project' ) ) {
		return post_type_archive_title( '', false );
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'ftf_project_archive_title' );

/**
 * Read a project field through ACF when it is active, falling back to post meta.
 *
 * @param string $name    Field name.
 * @param int    $post_id Post ID.
 * @return mixed
 */
function ftf_project_get_field( $name, $post_id ) {
	if ( function_exists( 'get_field' ) ) {
		return get_field( $name, $post_id );
	}

	return get_post_meta( $post_id, $name, true );
}

/**
 * Status badge markup for a project, or an empty string.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function ftf_project_status_badge( $post_id ) {
	$status   = ftf_project_get_field( 'project_status', $post_id );
	$statuses = ftf_project_statuses();

	if ( is_array( $status ) ) {
		$status = isset( $status['value'] ) ? $status['value'] : '';
	}

	if ( ! $status || ! isset( $statuses[ $status ] ) ) {
		return '';
	}

	return sprintf(
		'<span class="ftf-project-status ftf-project-status--%1$s">%2$s</span>',
		esc_attr( $status ),
		esc_html( $statuses[ $status ] )
	);
}

/**
 * Repo, Demo and Write-up links for a project. Empty links are skipped; returns '' when none are set.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function ftf_project_links( $post_id ) {
	$links = array(
		'project_repo_url'    => __( 'Repo', 'fivetwofive-project' ),
		'project_demo_url'    => __( 'Demo', 'fivetwofive-project' ),
		'project_writeup_url' => __( 'Write-up', 'fivetwofive-project' ),
	);

	$items = '';

	foreach ( $links as $field => $label ) {
		$url = ftf_project_get_field( $field, $post_id );

		if ( ! $url ) {
			continue;
		}

		$items .= sprintf(
			'<li class="list-inline-item"><a class="ftf-project-links__link" href="%1$s" target="_blank" rel="noopener">%2$s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	if ( '' === $items ) {
		return '';
	}

	return '<ul class="ftf-project-links list-inline">' . $items . '</ul>';
}

/**
 * Add the Status badge to the shared card and item post meta (template-parts/post-type/post-card.php and post-item.php).
 *
 * @param int    $post_id   Post ID.
 * @param string $post_type Post type.
 * @return void
 */
function ftf_project_post_meta( $post_id, $post_type ) {
	if ( 'ftf_project' !== $post_type ) {
		return;
	}

	echo ftf_project_status_badge( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in ftf_project_status_badge().
}
add_action( 'fivetwofive_theme_after_post_meta', 'ftf_project_post_meta', 10, 2 );

/**
 * Add the project links to the shared cards. The list view has no post meta, so it also gets the Status badge here.
 *
 * @param int    $post_id   Post ID.
 * @param string $post_type Post type.
 * @param string $context   card, item or list.
 * @return void
 */
function ftf_project_card_content( $post_id, $post_type, $context = 'card' ) {
	if ( 'ftf_project' !== $post_type ) {
		return;
	}

	if ( 'list' === $context ) {
		echo ftf_project_status_badge( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	echo ftf_project_links( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'fivetwofive_theme_after_card_content', 'ftf_project_card_content', 10, 3 );

/**
 * Shortcode for project pages built with the Modules Template (e.g. in a Code or Multi Column module):
 * [ftf_project_meta] prints the Status badge and links for the current project, or for id="123".
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function ftf_project_meta_shortcode( $atts ) {
	$atts    = shortcode_atts( array( 'id' => get_the_ID() ), $atts, 'ftf_project_meta' );
	$post_id = absint( $atts['id'] );

	if ( ! $post_id || 'ftf_project' !== get_post_type( $post_id ) ) {
		return '';
	}

	$output = ftf_project_status_badge( $post_id ) . ftf_project_links( $post_id );

	return $output ? '<div class="ftf-project-meta">' . $output . '</div>' : '';
}
add_shortcode( 'ftf_project_meta', 'ftf_project_meta_shortcode' );

/**
 * On a project that uses the default template (not the Modules Template), show the Status and links above the content.
 *
 * @param string $content Post content.
 * @return string
 */
function ftf_project_single_content( $content ) {
	if ( ! is_singular( 'ftf_project' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	return ftf_project_meta_shortcode( array() ) . $content;
}
add_filter( 'the_content', 'ftf_project_single_content' );

/**
 * Front-end styles for the Status badge and links.
 *
 * @return void
 */
function ftf_project_enqueue_styles() {
	wp_enqueue_style(
		'ftf-project',
		plugins_url( 'css/ftf-project.css', __FILE__ ),
		array(),
		filemtime( plugin_dir_path( __FILE__ ) . 'css/ftf-project.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'ftf_project_enqueue_styles' );
