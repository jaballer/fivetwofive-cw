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
 * Load the child theme's text domain so its UI strings are translatable.
 */
add_action( 'after_setup_theme', 'fivetwofive_child_editorial_setup' );
function fivetwofive_child_editorial_setup() {
	load_child_theme_textdomain( 'fivetwofive-theme-child-editorial', get_stylesheet_directory() . '/languages' );
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

/**
 * Editorial Customizer defaults.
 *
 * The parent emits its global color/typography CSS from these theme mods
 * (see Fivetwofive_Theme_Customize::theme_mods_css), including the
 * `--ftf-button-*` token layer. Overriding the defaults here gives every
 * route the editorial palette — warm paper, near-mono ink/muted text, a
 * single forest accent on buttons + active nav — without touching the parent.
 *
 * These are *defaults*: a Customizer value saved for this theme still wins, so
 * the look stays editable per install. Tokens mirror the design table in the
 * epic (#135); the canonical copy of each value lives in the SCSS `:root` block.
 *
 * @param array $mods Default theme mods from the parent.
 * @return array Filtered defaults.
 */
add_filter( 'fivetwofive_theme_default_theme_mods', 'fivetwofive_child_editorial_theme_mods' );
function fivetwofive_child_editorial_theme_mods( $mods ) {
	$paper  = '#FAF9F5'; // Page / header / footer background.
	$ink    = '#16150F'; // Body text + headings.
	$muted  = '#6E6C64'; // Secondary text, default nav.
	$forest = '#2E5D4B'; // Accent: buttons, active nav (CTA/eyebrows/tags handled in SCSS).

	// Body.
	$mods['colors_body_background_color']   = $paper;
	$mods['colors_body_text_color']         = $ink;
	$mods['colors_body_heading_color']      = $ink;
	$mods['colors_body_link_color']         = $ink;
	$mods['colors_body_link_color_hover']   = $muted; // Near-mono — accent is reserved, never on inline links.
	$mods['colors_body_link_color_visited'] = $ink;

	// Buttons — the sole place the accent appears as a fill. Paper text on forest,
	// resolving to ink on hover so the palette stays near-mono.
	$mods['colors_button_text_color']             = $paper;
	$mods['colors_button_text_color_hover']       = $paper;
	$mods['colors_button_background_color']       = $forest;
	$mods['colors_button_background_color_hover'] = $ink;
	$mods['colors_button_border_color']           = $forest;
	$mods['colors_button_border_color_hover']     = $ink;

	// Header.
	$mods['colors_header_background_color'] = $paper;

	// Primary navigation — muted by default, forest on the active/current item.
	$mods['colors_primary_navigation_background_color']  = $paper;
	$mods['colors_primary_navigation_link_color']        = $muted;
	$mods['colors_primary_navigation_active_link_color'] = $forest;

	// Footer.
	$mods['colors_footer_background_color'] = $paper;
	$mods['colors_footer_heading_color']   = $ink;
	$mods['colors_footer_text_color']      = $muted;

	// Typography weights — clamp to the editorial 400/500 (parent ships 400/700).
	// The system font stack itself is set in SCSS; the web-font load is removed below.
	$mods['typography_body_font_weight']    = '400';
	$mods['typography_heading_font_weight'] = '500';

	return $mods;
}

/**
 * Drop the parent's Google Fonts request — editorial uses a system stack only.
 *
 * A plain dequeue is not enough: the parent registers `fivetwofive-theme-main`
 * with `fivetwofive-theme-fonts` as a dependency, so WordPress would re-resolve
 * and print the font <link> anyway. Deregistering and re-registering the handle
 * with no `src` keeps the dependency chain intact while emitting nothing.
 *
 * Runs after the parent's `fivetwofive_theme_assets` (priority 5).
 */
add_action( 'wp_enqueue_scripts', 'fivetwofive_child_editorial_dequeue_google_fonts', 20 );
function fivetwofive_child_editorial_dequeue_google_fonts() {
	wp_dequeue_style( 'fivetwofive-theme-fonts' );
	wp_deregister_style( 'fivetwofive-theme-fonts' );
	wp_register_style( 'fivetwofive-theme-fonts', false ); // Srcless alias — prints nothing, satisfies the dependency.
}

/**
 * Register editorial "ways to work" icons in the parent's UI icon set.
 *
 * Monoline (stroke) icons, rendered via the parent's `[fivetwofive_icon group=ui
 * icon=… ]` shortcode in multi-column column text. The parent's kses ruleset
 * (`fivetwofive_kses_extended_ruleset`) permits stroke attributes and the common
 * SVG primitives, so these survive kses intact in module content. They inherit
 * color through `currentColor`; the parent injects class + size onto the `<svg>`.
 *
 * @param array $icons Existing UI icons keyed by name.
 * @return array Filtered icons.
 */
add_filter( 'fivetwofive_theme_svg_icons_ui', 'fivetwofive_child_editorial_register_icons' );
function fivetwofive_child_editorial_register_icons( $icons ) {
	$open = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">';

	// Strategy — compass: direction / where the work is headed.
	$icons['ed-strategy']      = $open . '<circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2 5-5 2 2-5z"/></svg>';

	// Collaboration — two figures: embedded with your team.
	$icons['ed-collaboration'] = $open . '<circle cx="9" cy="9" r="3"/><circle cx="16" cy="10.5" r="2.2"/><path d="M4 19c0-2.8 2.2-5 5-5s5 2.2 5 5"/><path d="M14.6 14.2c2 .3 3.4 2 3.4 4.3"/></svg>';

	// Delivery — check in a circle: shipped.
	$icons['ed-delivery']      = $open . '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.6 2.6 5.4-5.6"/></svg>';

	return $icons;
}

/**
 * Point the Works "View all" tile at the case-studies page.
 *
 * The `ftf_work` CPT has no archive, so the works module (#138) exposes this
 * filter for the destination. On the shared install the case-studies page is
 * `editorial-case-studies`; falls back to the module default if it's absent.
 *
 * @param string $url Default view-all URL.
 * @return string
 */
add_filter( 'fivetwofive_child_editorial_works_view_all_url', 'fivetwofive_child_editorial_works_view_all' );
function fivetwofive_child_editorial_works_view_all( $url ) {
	$page = get_page_by_path( 'editorial-case-studies' );
	return $page ? get_permalink( $page ) : $url;
}
