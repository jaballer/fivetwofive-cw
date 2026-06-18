<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://fivetwofive.com/
 * @since      1.0.0
 *
 * @package    FiveTwoFive_CTA
 * @subpackage FiveTwoFive_CTA/includes
 */

use FiveTwoFive\FiveTwoFive_CTA\Admin\Settings as Settings;
use FiveTwoFive\FiveTwoFive_CTA\Frontend\Shortcode as Shortcode;

defined( 'ABSPATH' ) || exit;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    FiveTwoFive_CTA
 * @subpackage FiveTwoFive_CTA/includes
 */
class FiveTwoFive_CTA {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	private string $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	private string $version;

	/**
	 * The single instance of the class.
	 *
	 * @var FiveTwoFive_CTA
	 * @since 1.0.0
	 */
	private static ?self $_instance = null;

	/**
	 * Main FiveTwoFive_CTA Instance.
	 *
	 * Ensures only one instance of FiveTwoFive_CTA is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see FiveTwoFive_CTA()
	 * @return FiveTwoFive_CTA - Main instance.
	 */
	public static function instance(): self {
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * FiveTwoFive_CTA Constructor.
	 */
	public function __construct() {
		if ( defined( 'FTF_CTA_VERSION' ) ) {
			$this->version = FTF_CTA_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'fivetwofive_cta';

		$this->define_public_hooks();
		$this->define_admin_hooks();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$settings = new Settings( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_menu', array( $settings, 'add_menu' ) );
		add_action( 'admin_init', array( $settings, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $settings, 'enqueue_scripts' ) );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$shortcode = new Shortcode( $this->get_plugin_name(), $this->get_version() );

		add_action( 'wp_enqueue_scripts', array( $shortcode, 'enqueue_styles' ) );
		add_shortcode( 'fivetwofive_cta', array( $shortcode, 'shortcode' ) );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
