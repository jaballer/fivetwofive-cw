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
		'source'   => array(
			'src'    => true,
			'type'   => true,
			'media'  => true,
			'sizes'  => true,
			'srcset' => true,
		),
		'iframe'   => array(
			'src'             => true,
			'height'          => true,
			'width'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
			'allow'           => true,
			'title'           => true,
			'class'           => true,
			'loading'         => true,
			'sandbox'         => true,
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
		// Inline SVG icons. Presentation + stroke attributes and the common shape
		// primitives are permitted so monoline/stroke icons — and the theme's own
		// icons (which use fill="none" + fill-rule/clip-rule) — survive kses when
		// rendered through module content via the [fivetwofive_icon] shortcode.
		// Consistent with this being a trusted-author ruleset (it already allows
		// script/iframe/style). Attribute names must be lower case.
		'svg'      => array(
			'class'           => true,
			'aria-hidden'     => true,
			'aria-labelledby' => true,
			'role'            => true,
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true, // <= Must be lower case!
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
		'g'        => array(
			'fill'      => true,
			'stroke'    => true,
			'transform' => true,
		),
		'title'    => array(), // SVG <title> for accessibility; no attributes.
		'path'     => array(
			'd'               => true,
			'fill'            => true,
			'fill-rule'       => true,
			'clip-rule'       => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'opacity'         => true,
		),
		'circle'   => array(
			'cx'           => true,
			'cy'           => true,
			'r'            => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'ellipse'  => array(
			'cx'           => true,
			'cy'           => true,
			'rx'           => true,
			'ry'           => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'rect'     => array(
			'x'            => true,
			'y'            => true,
			'width'        => true,
			'height'       => true,
			'rx'           => true,
			'ry'           => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		'line'     => array(
			'x1'             => true,
			'y1'             => true,
			'x2'             => true,
			'y2'             => true,
			'stroke'         => true,
			'stroke-width'   => true,
			'stroke-linecap' => true,
		),
		'polyline' => array(
			'points'          => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
		),
		'polygon'  => array(
			'points'       => true,
			'fill'         => true,
			'stroke'       => true,
			'stroke-width' => true,
		),
		// Allow <form> and <input> so shortcode-rendered forms (e.g. the
		// contact form) survive kses in module content. ACF WYSIWYG fields
		// expand shortcodes before this filter runs, so the form markup is
		// already present when wp_kses() sees it. This ruleset already permits
		// script/iframe/style, so it is a trusted-author ruleset and allowing
		// form controls is consistent. (<label>, <textarea>, <button> are
		// already permitted by the core "post" set.)
		'form'     => array(
			'class'      => true,
			'id'         => true,
			'method'     => true,
			'action'     => true,
			'enctype'    => true,
			'novalidate' => true,
		),
		'input'    => array(
			'class'         => true,
			'id'            => true,
			'name'          => true,
			'type'          => true,
			'value'         => true,
			'placeholder'   => true,
			'required'      => true,
			'checked'       => true,
			'readonly'      => true,
			'disabled'      => true,
			'tabindex'      => true,
			'autocomplete'  => true,
			'aria-hidden'   => true,
			'aria-required' => true,
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

/**
 * Build the module spacing CSS classes from the Appearance tab controls.
 *
 * Reads the per-module `spacing_top` / `spacing_bottom` button_group sub-fields
 * (none|small|medium|large) and returns the matching `.ftf-module--spacing-*`
 * utility classes (defined in assets/src/sass/modules/_module-spacing.scss).
 * A value of `none`, empty, or anything unrecognised emits no class for that
 * side, so existing modules render unchanged until an editor opts in.
 *
 * Must be called inside the ACF flexible-content row (after `the_row()`), i.e.
 * from within a module template.
 *
 * @return string Space-separated class string, or '' when no spacing is set.
 */
function fivetwofive_theme_get_module_spacing_classes() {
	$classes = array();
	$levels  = array( 'small', 'medium', 'large' );

	$top = get_sub_field( 'spacing_top' );
	if ( in_array( $top, $levels, true ) ) {
		$classes[] = 'ftf-module--spacing-top-' . $top;
	}

	$bottom = get_sub_field( 'spacing_bottom' );
	if ( in_array( $bottom, $levels, true ) ) {
		$classes[] = 'ftf-module--spacing-bottom-' . $bottom;
	}

	return implode( ' ', $classes );
}

