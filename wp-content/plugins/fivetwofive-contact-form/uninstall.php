<?php
/**
 * Uninstall handler for FiveTwoFive Contact Form.
 *
 * Runs only when the plugin is deleted from the WordPress admin.
 *
 * IMPORTANT: captured submissions (the ftf_submission post type) are the
 * plugin's source of truth and are deliberately PRESERVED across an uninstall —
 * a lead must never be lost by removing a plugin. Only plugin-owned options are
 * removed here.
 *
 * @package FiveTwoFive_Contact_Form
 */

// Exit if accessed directly or not during an uninstall.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/*
 * Remove plugin settings. Listed explicitly (not a wildcard) so we never touch
 * anything we did not create. Submissions and their meta are intentionally left
 * in the database.
 */
delete_option( 'fivetwofive_contact_form_options' );
