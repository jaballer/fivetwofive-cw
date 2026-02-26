<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package FiveTwoFive_Theme
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function fivetwofive_theme_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'fivetwofive_theme_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function fivetwofive_theme_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'fivetwofive_theme_pingback_header' );

/**
 * FiveTwoFive Extended ruleset
 * Allow SVG, iframe, and time in kses ruleset.
 *
 * @return array kses ruleset.
 */
function fivetwofive_kses_extended_ruleset() {
	$kses_defaults = wp_kses_allowed_html( 'post' );

	$args = array(
		'noscript' => array(),
		'style'    => array(),
		'iframe'   => array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		),
		'link'     => array(
			'rel'  => true,
			'href' => true,
		),
		'script'   => array(
			'charset' => true,
			'type'    => true,
			'src'     => true,
		),
		'time'     => array(
			'class'    => true,
			'datetime' => true,
		),
		'svg'      => array(
			'class'           => true,
			'aria-hidden'     => true,
			'aria-labelledby' => true,
			'role'            => true,
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true, // <= Must be lower case!
		),
		'g'        => array( 'fill' => true ),
		'title'    => array( 'title' => true ),
		'path'     => array(
			'd'    => true,
			'fill' => true,
		),
	);

	return array_merge( $kses_defaults, $args );
}

/**
 * Get ACF OEmbed Iframe.
 *
 * @param string $iframe ACF Oembed field.
 * @param array  $params Iframe attributes.
 * @return string Modified iframe HTML.
 */
function fivetwofive_get_acf_oembed_iframe(
	$iframe,
	$params = array(
		'controls' => 0,
		'hd'       => 1,
		'autohide' => 1,
	) ) {

	// Use preg_match to find iframe src.
	preg_match( '/src="(.+?)"/', $iframe, $matches );
	$src     = $matches[1];
	$new_src = add_query_arg( $params, $src );
	$iframe  = str_replace( $src, $new_src, $iframe );

	// Add extra attributes to iframe HTML.
	$attributes = 'frameborder="0"';
	$iframe     = str_replace( '></iframe>', ' ' . $attributes . '></iframe>', $iframe );

	// Display customized HTML.
	return $iframe;
}

/**
 * Get paginated links are array instead of html
 *
 * @link https://developer.wordpress.org/reference/functions/paginate_links/#comment-3862
 * @param WP_Query $query WP Query object.
 * @return array pagination array.
 */
function fivetwofive_get_paginated_links( $query ) {
	/**
	 * When we're on page 1, 'paged' is 0, but we're counting from 1,
	 * so we're using max() to get 1 instead of 0
	 */
	$current_page = max( 1, get_query_var( 'paged', 1 ) );

	/**
	 * This creates an array with all available page numbers, if there
	 * is only *one* page, max_num_pages will return 0, so here we also
	 * use the max() function to make sure we'll always get 1
	 */
	$pages = range( 1, max( 1, $query->max_num_pages ) );

	/**
	 * Now, map over $pages and return the page number, the url to that
	 * page and a boolean indicating whether that number is the current page.
	 */
	return array_map(
		function( $page ) use ( $current_page ) {
			return ( object ) array(
				'is_current' => (int) $current_page === (int) $page,
				'page'       => $page,
				'url'        => get_pagenum_link( $page ),
			);
		},
		$pages
	);
}

