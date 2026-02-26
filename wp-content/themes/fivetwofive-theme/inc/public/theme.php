<?php
/**
 * Define all the theme setup functions here.
 *
 * @package FiveTwoFive_Theme
 */

if ( ! function_exists( 'fivetwofive_theme_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function fivetwofive_theme_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on FiveTwoFive Theme, use a find and replace
		 * to change 'fivetwofive-theme' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'fivetwofive-theme', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary_menu' => esc_html__( 'Primary Menu', 'fivetwofive-theme' ),
				'footer_menu'  => esc_html__( 'Footer Menu', 'fivetwofive-theme' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'fivetwofive_theme_setup' );

if ( ! function_exists( 'fivetwofive_theme_content_width' ) ) :
	/**
	 * Set the content width in pixels, based on the theme's design and stylesheet.
	 *
	 * Priority 0 to make it available to lower priority callbacks.
	 *
	 * @global int $content_width
	 */
	function fivetwofive_theme_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'fivetwofive_theme_content_width', 1200 );
	}
endif;
add_action( 'after_setup_theme', 'fivetwofive_theme_content_width', 0 );

if ( ! function_exists( 'fivetwofive_enable_sidebar' ) ) :
	/**
	 * Determine if a page will have a sidebar.
	 *
	 * @return boolean $show_sidebar show or hide the sidebar.
	 */
	function fivetwofive_enable_sidebar() {
		$show_sidebar = false;
		return apply_filters( 'fivetwofive_theme_enable_sidebar', $show_sidebar );
	}
endif;

if ( ! function_exists( 'fivetwofive_is_contained' ) ) :
	/**
	 * Make a page content full width or contained.
	 *
	 * @return boolean $show_sidebar show or hide the sidebar.
	 */
	function fivetwofive_is_contained() {
		$is_contained = false;
		if ( is_home() || is_archive() || is_singular() || is_page() || is_search() || is_404() ) {
			$is_contained = true;
		}

		if ( is_page_template( 'page-templates/template-module.php' ) ) {
			$is_contained = false;
		}

		return apply_filters( 'fivetwofive_theme_is_contained', $is_contained );
	}
endif;

if ( ! function_exists( 'fivetwofive_body_classes' ) ) :
	/**
	 * Determine if a page will have two column layout or not.
	 * Displays the class names for the body element.
	 *
	 * @param array $classes Array of class names.
	 * @return array $classes Array of class names.
	 */
	function fivetwofive_body_classes( $classes ) {
		// Makes the page two column when the sidebar is enabled.
		if ( fivetwofive_enable_sidebar() ) {
			$classes[] = 'fivetwofive-page-layout-two-column';
		}

		if ( fivetwofive_is_contained() ) {
			$classes[] = 'fivetwofive-page-layout-contained';
		}

		return $classes;
	}
endif;
add_filter( 'body_class', 'fivetwofive_body_classes' );

if ( ! function_exists( 'fivetwofive_layout_enable_sidebar' ) ) :
	/**
	 * Determine if a page will have two column layout or not.
	 * Displays the class names for the body element.
	 */
	function fivetwofive_layout_enable_sidebar() {
		if ( fivetwofive_enable_sidebar() ) {
			get_sidebar();
		}
	}
endif;
add_action( 'fivetwofive_after_content', 'fivetwofive_layout_enable_sidebar', 5 );

if ( ! function_exists( 'fivetwofive_theme_archive_title' ) ) :
	/**
	 * Get rid of the “Category:”, “Tag:”, “Author:”, “Archives:” and “Other taxonomy name:” in the archive title.
	 *
	 * @param string $title The archive title text.
	 * @return string $title The archive title text.
	 */
	function fivetwofive_theme_archive_title( $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		}

		return $title;
	}
endif;
add_filter( 'get_the_archive_title', 'fivetwofive_theme_archive_title' );


/**
 * Add badge class to post tag links
 *
 * @param $links
 *
 * @return array|string|string[]
 */
function fivetwofive_theme_tag_class($links) {
	return str_replace('<a href="', '<a class="badge d-inline-block text-uppercase text-black bg-primary" href="', $links);
}
add_filter( 'term_links-post_tag', 'fivetwofive_theme_tag_class');