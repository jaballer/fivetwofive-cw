<?php
/**
 * 916 Marketing theme functions.
 *
 * Intentionally small: the landing page is hard-coded in front-page.php and
 * template-parts/sections/. This file only wires up theme supports, assets,
 * and a few helpers shared by the header, footer, and sections.
 *
 * @package 916_Marketing
 */

defined( 'ABSPATH' ) || exit;

define( 'MARKETING916_VERSION', wp_get_theme()->get( 'Version' ) );

/* Contact details used in the header, contact section, and footer. */
define( 'MARKETING916_PHONE', '(916) 432-7707' );
define( 'MARKETING916_PHONE_TEL', '+19164327707' );
define( 'MARKETING916_EMAIL', 'info@916marketing.com' );

/**
 * Theme supports.
 */
function marketing916_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
}
add_action( 'after_setup_theme', 'marketing916_setup' );

/**
 * Enqueue fonts, styles, and the landing-page script.
 */
function marketing916_enqueue_assets() {
	wp_enqueue_style(
		'marketing916-fonts',
		'https://fonts.googleapis.com/css2?family=Lato:wght@900&family=Roboto:wght@400;500;700&display=swap',
		array(),
		null // Google Fonts URLs must not get a ?ver= query arg.
	);

	wp_enqueue_style( 'marketing916-design-system', get_theme_file_uri( 'assets/css/design-system.css' ), array( 'marketing916-fonts' ), MARKETING916_VERSION );
	wp_enqueue_style( 'marketing916-site', get_theme_file_uri( 'assets/css/site.css' ), array( 'marketing916-design-system' ), MARKETING916_VERSION );
	wp_enqueue_style( 'marketing916-wordpress', get_theme_file_uri( 'assets/css/wordpress.css' ), array( 'marketing916-site' ), MARKETING916_VERSION );

	if ( is_front_page() ) {
		wp_enqueue_script(
			'marketing916-main',
			get_theme_file_uri( 'assets/js/main.js' ),
			array(),
			MARKETING916_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		// The landing page has no block content, so skip core block styles there.
		wp_dequeue_style( 'wp-block-library' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
	}
}
add_action( 'wp_enqueue_scripts', 'marketing916_enqueue_assets', 20 );

/**
 * Follow the system light/dark setting.
 *
 * Dark tokens live under [data-theme="dark"], so this has to run before the
 * stylesheets paint — printed first thing in <head>.
 */
function marketing916_theme_mode_script() {
	wp_print_inline_script_tag(
		"(function(){var d=window.matchMedia('(prefers-color-scheme: dark)');function a(){document.documentElement.setAttribute('data-theme',d.matches?'dark':'light');}a();d.addEventListener('change',a);})();"
	);
}
add_action( 'wp_head', 'marketing916_theme_mode_script', 0 );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function marketing916_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'marketing916_resource_hints', 10, 2 );

/**
 * Link to a landing-page section.
 *
 * On the front page this is a bare fragment (smooth in-page scroll); anywhere
 * else it points back to the home page so header/footer links still work.
 *
 * @param string $id Section id, without the leading '#'.
 * @return string Escaped URL.
 */
function marketing916_section_url( $id ) {
	$fragment = '#' . sanitize_html_class( $id );
	return is_front_page() ? esc_attr( $fragment ) : esc_url( home_url( '/' . $fragment ) );
}

/**
 * Print the logo (mark + wordmark), linking to the top of the landing page.
 *
 * The mark is the style guide's placeholder path — replace it here and every
 * logo updates.
 *
 * @param bool $inverse Use the inverse colorway (dark bands).
 */
function marketing916_logo( $inverse = false ) {
	printf(
		'<a class="logo%1$s" href="%2$s"><svg class="logo-mark" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" fill-rule="evenodd" d="M4 0h16a4 4 0 0 1 4 4v16a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4V4a4 4 0 0 1 4-4zM12 5.5 5.5 12 12 18.5 18.5 12z"/></svg><span class="t-wordmark">916 Marketing</span></a>',
		$inverse ? ' logo--inverse' : '',
		marketing916_section_url( 'top' ) // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper.
	);
}
