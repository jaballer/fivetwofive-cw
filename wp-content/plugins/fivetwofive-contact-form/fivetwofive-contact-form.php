<?php
/**
 * FiveTwoFive Contact Form
 *
 * @package           FiveTwoFive
 * @author            FiveTwoFive Creative Team
 * @copyright         2026 FiveTwoFive
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       FiveTwoFive Contact Form
 * Plugin URI:        https://fivetwofive.com/
 * Description:       A lightweight, dependency-light contact form. Stores every submission as a record and sends notifications via wp_mail() with a swappable mail transport.
 * Version:           1.2.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            FiveTwoFive Creative Team
 * Author URI:        https://fivetwofive.com/
 * Text Domain:       fivetwofive-contact-form
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) || exit;

/**
 * Current plugin version.
 */
define( 'FTF_CONTACT_FORM_VERSION', '1.2.0' );

if ( ! defined( 'FTF_CONTACT_FORM_PLUGIN_FILE' ) ) {
	define( 'FTF_CONTACT_FORM_PLUGIN_FILE', __FILE__ );
}

/*
 * Load the Composer autoloader.
 *
 * Guarded on purpose: unlike a bare `require`, a missing build (e.g. a deploy
 * that skipped `composer install`) degrades to an admin notice instead of a
 * site-wide fatal error.
 */
$ftf_contact_form_autoload = __DIR__ . '/vendor/autoload.php';

if ( ! is_readable( $ftf_contact_form_autoload ) ) {
	add_action(
		'admin_notices',
		static function () {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html__(
					'FiveTwoFive Contact Form is missing its autoloader. Run "composer install" in the plugin directory.',
					'fivetwofive-contact-form'
				)
			);
		}
	);

	return;
}

require $ftf_contact_form_autoload;

// Include the main plugin class.
if ( ! class_exists( 'FiveTwoFive_Contact_Form', false ) ) {
	include_once dirname( FTF_CONTACT_FORM_PLUGIN_FILE ) . '/includes/class-fivetwofive-contact-form.php';
}

/**
 * Returns the main instance of FiveTwoFive_Contact_Form.
 *
 * @since  1.0.0
 * @return FiveTwoFive_Contact_Form
 */
function FTF_Contact_Form() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return FiveTwoFive_Contact_Form::instance();
}

FTF_Contact_Form();
