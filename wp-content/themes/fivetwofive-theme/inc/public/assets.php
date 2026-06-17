<?php
/**
 * Define all the assets related functions here.
 *
 * This file handles all asset (scripts and styles) related functionality including:
 * - Google Fonts integration
 * - Theme styles and scripts enqueuing
 * - Module-specific assets
 * - Asset optimization (defer loading)
 *
 * @package FiveTwoFive_Theme
 * @since 1.0.0
 */

/**
 * Generate the Google fonts URL from the passed fonts.
 *
 * Constructs a valid Google Fonts API URL with proper font variants and display settings.
 * Handles both regular and italic font variants appropriately.
 *
 * @since 1.0.0
 *
 * @param array $fonts Array of google fonts. Each font should be an array with 'name' and 'variants' keys.
 *                     Example: [['name' => 'Open Sans', 'variants' => ['400', '400italic', '700']]]
 * @return string|bool $google_fonts_url Google fonts URL or false if invalid input.
 */
function fivetwofive_theme_generate_google_fonts_url( $fonts ) {
	$google_fonts_url = 'https://fonts.googleapis.com/css2';
	$font_args        = array();

	// Make sure we are dealing with a valid array.
	if ( ! is_array( $fonts ) && empty( $fonts ) ) {
		return false;
	}

	foreach ( $fonts as $font ) {
		$font_uri_variants = array();
		$has_italic        = false;

		// Check if any variants include italic style
		foreach ( $font['variants'] as $font_weight ) {
			if ( strpos( $font_weight, 'italic' ) !== false ) {
				$has_italic = true;
				break;
			}
		}

		// Handle fonts with italic variants differently from those without
		if ( $has_italic ) {
			$font_uri = wp_sprintf(
				'family=%s:ital,wght@',
				rawurlencode( $font['name'] )
			);

			// Process each variant and separate italic from regular weights
			foreach ( $font['variants'] as $variant ) {
				if ( strpos( $variant, 'italic' ) === false ) {
					$font_uri_variants[] = '0,' . $variant;
				} else {
					$font_uri_variants[] = '1,' . str_replace( 'italic', '', $variant );
				}
			}
			sort( $font_uri_variants, SORT_STRING );
			$font_uri .= implode( ';', $font_uri_variants );
		} else {
			$font_uri = wp_sprintf(
				'family=%s:wght@',
				rawurlencode( $font['name'] )
			);

			foreach ( $font['variants'] as $variant ) {
				$font_uri_variants[] = $variant;
			}

			$font_uri .= implode( ';', $font_uri_variants );
		}

		$font_args[] = $font_uri;
	}

	// Construct final URL with display swap for better performance
	$google_fonts_url .= '?' . implode( '&', $font_args );
	$google_fonts_url .= '&display=swap';

	return $google_fonts_url;
}

/**
 * Get the theme typography settings and generate the Google fonts URL.
 *
 * Retrieves typography settings from theme mods and generates a Google Fonts URL
 * for both body and heading fonts with their respective variants.
 *
 * @since 1.0.0
 *
 * @return string Google fonts URL.
 */
function fivetwofive_theme_fonts_url() {
	$theme_mods = fivetwofive_theme_mods();

	$fonts = array(
		array(
			'name'     => $theme_mods['typography_body_font'],
			'variants' => $theme_mods['typography_body_font_variants'],
		),
		array(
			'name'     => $theme_mods['typography_heading_font'],
			'variants' => $theme_mods['typography_heading_font_variants'],
		),
	);

	return fivetwofive_theme_generate_google_fonts_url( $fonts );
}

/**
 * Enqueue all theme scripts and styles.
 *
 * This function handles the loading of all theme assets including:
 * - Main theme styles
 * - Google Fonts
 * - Navigation scripts
 * - Template-specific assets
 * - Third-party dependencies
 * - Custom module styles and scripts
 *
 * @since 1.0.0
 */
function fivetwofive_theme_assets() {
	// Main theme style
	wp_enqueue_style( 'fivetwofive-theme-style', get_stylesheet_uri(), array(), FIVETWOFIVE_THEME_VERSION );
	wp_style_add_data( 'fivetwofive-theme-style', 'rtl', 'replace' );

	// Font Awesome icons
	wp_enqueue_style( 'fivetwofive-theme-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4' );

	// Google Fonts
	wp_enqueue_style( 'fivetwofive-theme-fonts', fivetwofive_theme_fonts_url(), array(), null );
	wp_enqueue_style( 'fivetwofive-theme-main', get_template_directory_uri() . '/assets/dist/css/main.css', array( 'fivetwofive-theme-fonts' ), FIVETWOFIVE_THEME_VERSION );
	
	// Core scripts
	wp_register_script( 'fivetwofive-theme-popperjs', get_template_directory_uri() . '/assets/dist/js/plugins/popper/popper.min.js', array(), '2.11.8', true );
	wp_enqueue_script( 'fivetwofive-theme-navigation', get_template_directory_uri() . '/assets/dist/js/navigation.js', array(), FIVETWOFIVE_THEME_VERSION, true );

	// Blog pages view toggle functionality
	if ( is_home() || is_archive() ) {
		wp_enqueue_script( 'fivetwofive-view-toggle', get_template_directory_uri() . '/assets/dist/js/view-toggle.js', array(), FIVETWOFIVE_THEME_VERSION, true );
	}

	// Custom post type specific styles
	if ( is_singular( 'ftf_event' ) ) {
		wp_enqueue_style( 'fivetwofive-theme-single-event', get_template_directory_uri() . '/assets/dist/css/single-event.css', array( 'fivetwofive-theme-main' ), FIVETWOFIVE_THEME_VERSION );
	}

	if ( is_singular( 'ftf_resource' ) ) {
		wp_enqueue_style( 'fivetwofive-theme-single-resource', get_template_directory_uri() . '/assets/dist/css/single-resource.css', array( 'fivetwofive-theme-main' ), FIVETWOFIVE_THEME_VERSION );
	}

	if ( is_singular( 'music' ) ) {
		wp_enqueue_style( 'fivetwofive-theme-single-music', get_template_directory_uri() . '/assets/dist/css/single-music.css', array( 'fivetwofive-theme-main' ), FIVETWOFIVE_THEME_VERSION );
	}

	// Single post specific scripts
	if ( is_singular( 'post' ) ) {
		wp_enqueue_script( 'fivetwofive-theme-single-post', get_template_directory_uri() . '/assets/dist/js/single-post.js', array( 'fivetwofive-theme-popperjs' ), FIVETWOFIVE_THEME_VERSION, true );
	}

	// Module template specific assets
	if ( is_page_template( 'page-templates/template-module.php' ) || is_singular( 'ftf_event' ) ) {
		// Register third-party dependencies
		wp_register_style( 'fivetwofive-theme-fancybox', get_template_directory_uri() . '/assets/dist/js/plugins/fancybox/jquery.fancybox.min.css', array(), FIVETWOFIVE_THEME_VERSION );
		wp_register_style( 'fivetwofive-theme-swiper', get_template_directory_uri() . '/assets/dist/js/plugins/swiper/swiper-bundle.min.css', array(), FIVETWOFIVE_THEME_VERSION );
		wp_register_script( 'fivetwofive-theme-fancybox', get_template_directory_uri() . '/assets/dist/js/plugins/fancybox/jquery.fancybox.min.js', array( 'jquery' ), FIVETWOFIVE_THEME_VERSION, true );
		wp_script_add_data( 'fivetwofive-theme-fancybox', 'strategy', 'defer' );
		wp_register_script( 'fivetwofive-theme-swiper', get_template_directory_uri() . '/assets/dist/js/plugins/swiper/swiper-bundle.min.js', array(), FIVETWOFIVE_THEME_VERSION, true );
		wp_script_add_data( 'fivetwofive-theme-swiper', 'strategy', 'defer' );
		wp_enqueue_script( 'fivetwofive-theme-scrollreveal', get_template_directory_uri() . '/assets/dist/js/plugins/scrollreveal/scrollreveal.min.js', array(), '4.0.0', true );
		wp_script_add_data( 'fivetwofive-theme-scrollreveal', 'strategy', 'defer' );

		// Register module-specific scripts
		wp_register_script( 'fivetwofive-theme-module-announcement', get_template_directory_uri() . '/assets/dist/js/modules/module-announcement.min.js', array(), FIVETWOFIVE_THEME_VERSION, true );
		wp_register_script( 'fivetwofive-theme-module-testimonials-carousel', get_template_directory_uri() . '/assets/dist/js/modules/module-testimonials-carousel.min.js', array( 'fivetwofive-theme-swiper' ), FIVETWOFIVE_THEME_VERSION, true );
		wp_script_add_data( 'fivetwofive-theme-module-testimonials-carousel', 'strategy', 'defer' );
		wp_register_script( 'fivetwofive-theme-module-resources', get_template_directory_uri() . '/assets/dist/js/modules/module-resource.min.js', array( 'fivetwofive-theme-scrollreveal' ), FIVETWOFIVE_THEME_VERSION, true );
		wp_script_add_data( 'fivetwofive-theme-module-resources', 'strategy', 'defer' );
		wp_add_inline_script( 'fivetwofive-theme-module-resources', 'const FiveTwoFive = ' . wp_json_encode( array( 'restBase' => get_rest_url( null, 'wp/v2/ftf-resources' ) ) ), 'before' );
		wp_register_script( 'fivetwofive-theme-module-works', get_template_directory_uri() . '/assets/dist/js/modules/module-works.min.js', array( 'fivetwofive-theme-scrollreveal' ), FIVETWOFIVE_THEME_VERSION, true );
		wp_script_add_data( 'fivetwofive-theme-module-works', 'strategy', 'defer' );

		// Enqueue module template assets
		wp_enqueue_style( 'fivetwofive-theme-template-module', get_template_directory_uri() . '/assets/dist/css/template-modules.css', array( 'fivetwofive-theme-main' ), FIVETWOFIVE_THEME_VERSION );
		wp_enqueue_script( 'fivetwofive-theme-template-module', get_template_directory_uri() . '/assets/dist/js/template-modules.min.js', array( 'fivetwofive-theme-scrollreveal' ), FIVETWOFIVE_THEME_VERSION, true );
		wp_script_add_data( 'fivetwofive-theme-template-module', 'strategy', 'defer' );

		// Handle custom module styles and scripts
		$current_page_id       = get_queried_object_id();
		$module_custom_styles  = get_field( 'ftf_custom_styles', $current_page_id );
		$module_styles         = get_field( 'ftf_module_styles', $current_page_id );
		$module_custom_scripts = get_field( 'ftf_custom_scripts', $current_page_id );
		$module_scripts        = get_field( 'ftf_module_scripts', $current_page_id );

		if ( $module_custom_styles && $module_styles ) {
			wp_add_inline_style( 'fivetwofive-theme-template-module', $module_styles );
		}

		if ( $module_custom_scripts && $module_scripts ) {
			wp_add_inline_script( 'fivetwofive-theme-template-module', $module_scripts );
		}
	}

	// Comments functionality
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Optimize performance by removing dashicons for non-logged-in users
	if ( ! is_user_logged_in() ) {
		wp_dequeue_style( 'dashicons' );
	}
}
add_action( 'wp_enqueue_scripts', 'fivetwofive_theme_assets', 5 );

/**
 * Add preconnect for Google Fonts.
 *
 * Adds resource hints to header for performance optimization.
 * This improves loading time for Google Fonts.
 *
 * @since 1.0.0
 */
function fivetwofive_theme_preconnect() {
	$preconnect_urls = array( 'https://fonts.gstatic.com' );

	foreach ( $preconnect_urls as $preconnect_url ) {
		echo sprintf( '<link rel="preconnect" href="%1$s">', esc_url( $preconnect_url ) );
	}
}
add_action( 'wp_head', 'fivetwofive_theme_preconnect', 5 );

