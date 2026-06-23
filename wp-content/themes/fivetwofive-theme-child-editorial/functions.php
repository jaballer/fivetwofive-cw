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
 * Enqueue the child theme stylesheet, compiled SASS bundle, and scripts.
 *
 * The child `style.css` loads after the parent's registered styles
 * (`fivetwofive-theme-main`, `fivetwofive-theme-template-module`); the compiled
 * SASS bundle then layers on top of it.
 */
add_action( 'wp_enqueue_scripts', 'fivetwofive_child_editorial_enqueue_assets' );
function fivetwofive_child_editorial_enqueue_assets() {
	$theme = wp_get_theme();

	// Child theme style.css (metadata + light overrides), after the parent styles.
	wp_enqueue_style(
		'fivetwofive-editorial-style',
		get_stylesheet_uri(),
		array( 'fivetwofive-theme-main', 'fivetwofive-theme-template-module' ),
		$theme->get( 'Version' )
	);

	// Compiled SASS bundle, dependent on the child style.css above.
	wp_enqueue_style(
		'fivetwofive-editorial-sass',
		get_stylesheet_directory_uri() . '/assets/dist/css/style.css',
		array( 'fivetwofive-editorial-style' ),
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
