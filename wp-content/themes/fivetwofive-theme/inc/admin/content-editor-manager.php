<?php
/**
 * Content Editor Manager
 * 
 * Handles hiding the content editor for pages using the Modules Template.
 * This ensures that when users select the Modules Template, the traditional
 * content editor is hidden since the page content is managed through ACF
 * flexible content fields instead.
 * 
 * Features:
 * - Multiple template detection methods for reliability
 * - Works with both admin_init and current_screen hooks
 * - Uses WordPress native remove_post_type_support() function
 * 
 * @package FiveTwoFive_Theme
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Remove content editor for pages using the Modules Template
 */
function fivetwofive_remove_modules_template_editor() {
	global $post;
	
	// If not in admin or no post object, return
	if (!is_admin() || !$post) {
		return;
	}
	
	// Get current template
	$template = get_post_meta($post->ID, '_wp_page_template', true);
	
	// Check for multiple possible template values
	$is_modules_template = (
		'page-templates/template-module.php' === $template ||
		'Modules Template' === $template ||
		'template-module.php' === $template
	);
	
	// If using Modules Template, remove the editor
	if ($is_modules_template) {
		remove_post_type_support('page', 'editor');
	}
}
add_action('admin_init', 'fivetwofive_remove_modules_template_editor');

/**
 * Alternative method to hide content editor using current_screen hook
 * This runs later in the admin process and might be more reliable
 */
function fivetwofive_hide_editor_on_modules_template() {
	$screen = get_current_screen();
	
	/**
	 * Post types whose editor is hidden on the Modules Template. Plugins can add theirs (e.g. ftf_project).
	 */
	$post_types = apply_filters('fivetwofive_modules_template_hide_editor_post_types', array('page'));

	// Only run on edit screens of those post types
	if (!$screen || !in_array($screen->post_type, $post_types, true) || 'post' !== $screen->base) {
		return;
	}
	
	// Get the current post ID from the URL
	$post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
	
	if (!$post_id) {
		return;
	}
	
	// Get current template
	$template = get_post_meta($post_id, '_wp_page_template', true);
	
	// Check for multiple possible template values
	$is_modules_template = (
		'page-templates/template-module.php' === $template ||
		'Modules Template' === $template ||
		'template-module.php' === $template
	);
	
	// If using Modules Template, remove the editor
	if ($is_modules_template) {
		remove_post_type_support($screen->post_type, 'editor');
	}
}
add_action('current_screen', 'fivetwofive_hide_editor_on_modules_template');

