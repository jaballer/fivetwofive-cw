<?php
/**
 * FiveTwoFive Editorial Child — theme functions.
 *
 * A second child theme of the FiveTwoFive parent, implementing an editorial /
 * minimal design direction. Most functionality comes from the parent framework
 * and first-party plugins; this child owns styling and a few module overrides.
 *
 * @package FiveTwoFive
 * @subpackage FiveTwoFive_Child_Editorial
 * @since 0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Enqueue the child theme's compiled SASS bundle and scripts.
 *
 * The parent already enqueues this child's `style.css` (as `fivetwofive-theme-style`,
 * via `get_stylesheet_uri()`) and its framework CSS (`fivetwofive-theme-main`) on every
 * route, so we only layer the compiled bundle on top. We depend on the always-enqueued
 * `fivetwofive-theme-main` rather than the parent's `fivetwofive-theme-template-module`
 * handle — that one is registered only on the module template / `ftf_event` routes, so
 * depending on it would drop these styles on home, archives, posts, and regular pages.
 * Module-specific ordering is handled separately when those overrides land.
 */
add_action( 'wp_enqueue_scripts', 'fivetwofive_child_editorial_enqueue_assets' );
function fivetwofive_child_editorial_enqueue_assets() {
	$theme = wp_get_theme();

	// Compiled SASS bundle — loads site-wide, cascading after the parent framework styles.
	wp_enqueue_style(
		'fivetwofive-editorial-sass',
		get_stylesheet_directory_uri() . '/assets/dist/css/style.css',
		array( 'fivetwofive-theme-main' ),
		$theme->get( 'Version' )
	);

	// Compiled front-end scripts (minimal for now; behavior added per-feature).
	wp_enqueue_script(
		'fivetwofive-editorial-main',
		get_stylesheet_directory_uri() . '/assets/dist/js/main.js',
		array(),
		$theme->get( 'Version' ),
		true
	);
}
