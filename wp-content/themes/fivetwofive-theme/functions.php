<?php
/**
 * FiveTwoFive Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package FiveTwoFive_Theme
 */

if ( ! defined( 'FIVETWOFIVE_THEME_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'FIVETWOFIVE_THEME_VERSION', '1.0.1' );
}

// Warn admins if ACF Pro is not active — the modular page builder depends on it.
if ( ! class_exists( 'ACF' ) ) {
	add_action(
		'admin_notices',
		function() {
			echo '<div class="notice notice-error"><p>' .
				esc_html__( 'FiveTwoFive Theme requires Advanced Custom Fields Pro. Please install and activate it.', 'fivetwofive-theme' ) .
				'</p></div>';
		}
	);
}

/**
 * Define all the admin functions here.
 */
require get_template_directory() . '/inc/admin/admin.php';

/**
 * Define all the theme setup functions here.
 */
require get_template_directory() . '/inc/public/theme.php';

/**
 * Define all the assets related functions here.
 */
require get_template_directory() . '/inc/public/assets.php';

/**
 * Define all Widgets related code.
 */
require get_template_directory() . '/inc/admin/widgets.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/public/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/public/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/admin/customizer.php';

/**
 * SVG Icons
 */
require get_template_directory() . '/inc/public/svg-icons.php';

/**
 * Share Buttons
 */
require get_template_directory() . '/inc/public/share-buttons.php';

/**
 * WP Rest API
 */
require get_template_directory() . '/inc/public/rest-api.php';


/**
 * Force ACF to save/load JSON in the parent theme's acf-json directory.
 */
add_filter('acf/settings/save_json', function($path) {
    return get_template_directory() . '/acf-json';
});

add_filter('acf/settings/load_json', function($paths) {
    unset($paths[0]);
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});