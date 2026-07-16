<?php
/**
 * FiveTwoFive Child Theme Functions
 *
 * @package FiveTwoFive
 * @subpackage FiveTwoFive_Child
 * @since 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // No direct access.
}

/**
 * Load the child theme's text domain so its UI strings are translatable.
 */
add_action( 'after_setup_theme', 'fivetwofive_child_setup' );
function fivetwofive_child_setup() {
    load_child_theme_textdomain( 'fivetwofive-theme-child', get_stylesheet_directory() . '/languages' );
}

/**
 * Enqueue child theme styles and GSAP scripts
 *
 * This function handles:
 * - SASS-generated child styles
 * - GSAP library and plugins
 * - Custom animation scripts
 */
add_action( 'wp_enqueue_scripts', 'fivetwofive_child_enqueue_assets' );
function fivetwofive_child_enqueue_assets() {
    $theme = wp_get_theme();

    // Compiled SASS bundle — the child's main stylesheet.
    //
    // The parent already enqueues this child's style.css (handle
    // 'fivetwofive-theme-style', via get_stylesheet_uri()) and the framework CSS
    // ('fivetwofive-theme-main') on every route at priority 5, so this only layers
    // the bundle on top — no need to re-enqueue style.css here. Re-registering an
    // already-registered handle is a no-op in WP_Dependencies, so a dependency array
    // on that handle would be silently dropped anyway.
    //
    // Depend on 'fivetwofive-theme-main' (always registered) so the bundle reliably
    // cascades after the parent framework, and on 'fivetwofive-theme-style' so it also
    // follows the child's light style.css overrides. We deliberately do NOT depend on
    // 'fivetwofive-theme-template-module': the parent registers that only on the
    // module-template / ftf_event routes, so depending on it would drop this bundle on
    // home, archives, posts, and regular pages.
    wp_enqueue_style(
        'fivetwofive-theme-child-sass',
        get_stylesheet_directory_uri() . '/assets/dist/css/style.css',
        array( 'fivetwofive-theme-main', 'fivetwofive-theme-style' ),
        $theme->get( 'Version' )
    );

    // Enqueue GSAP core library
    wp_enqueue_script(
        'gsap',
        get_stylesheet_directory_uri() . '/assets/dist/js/vendor/gsap.min.js',
        array(),
        '3.12.7',
        true
    );

    // Enqueue GSAP ScrollTrigger plugin
    wp_enqueue_script(
        'gsap-scrolltrigger',
        get_stylesheet_directory_uri() . '/assets/dist/js/vendor/ScrollTrigger.min.js',
        array( 'gsap' ),
        '3.12.7',
        true
    );

    // Register ScrollTrigger plugin with GSAP
    wp_add_inline_script(
        'gsap-scrolltrigger',
        'gsap.registerPlugin(ScrollTrigger);',
        'after'
    );

    // Enqueue custom scripts (includes animations and copy-code functionality)
    wp_enqueue_script(
        'fivetwofive-main',
        get_stylesheet_directory_uri() . '/assets/dist/js/main.js',
        array( 'gsap', 'gsap-scrolltrigger' ),
        $theme->get( 'Version' ),
        true
    );
}

