<?php
/**
 * FiveTwoFive CTA
 *
 * @package           FiveTwoFive
 * @author            FiveTwoFive Creative Team
 * @copyright         2021 FiveTwoFive
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Call To Action
 * Plugin URI:        https://fivetwofive.com/
 * Description:       Add customizable call-to-action blocks with reusable shortcode output and style controls.
 * Version:           1.0.3
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            FiveTwoFive Creative Team
 * Author URI:        https://fivetwofive.com/
 * Text Domain:       fivetwofive-cta
 * Domain Path:       /languages
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * This plugin provides an easy way to add and manage call-to-action blocks
 * on your WordPress site. Features include:
 * - Customizable styles and layouts
 * - Responsive design
 * - Shortcode rendering
 * - Global settings with per-shortcode overrides
 */

defined( 'ABSPATH' ) || exit;

/**
 * Current plugin version.
 */
define( 'FTF_CTA_VERSION', '1.0.3' );

if ( ! defined( 'FTF_CTA_PLUGIN_FILE' ) ) {
	define( 'FTF_CTA_PLUGIN_FILE', __FILE__ );
}

// Load core packages and the autoloader.
require __DIR__ . '/vendor/autoload.php';

// Include the main FiveTwoFive_CTA class.
if ( ! class_exists( 'FiveTwoFive_CTA', false ) ) {
	include_once dirname( FTF_CTA_PLUGIN_FILE ) . '/includes/class-fivetwofive-cta.php';
}

/**
 * Returns the main instance of FiveTwoFive_CTA.
 *
 * @since  1.0.0
 * @return FiveTwoFive_CTA
 */
function FTF_CTA() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return FiveTwoFive_CTA::instance();
}

FTF_CTA();
