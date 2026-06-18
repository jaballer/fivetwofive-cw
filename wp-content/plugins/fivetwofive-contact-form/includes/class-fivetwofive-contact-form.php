<?php
/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://fivetwofive.com/
 * @since      1.0.0
 *
 * @package    FiveTwoFive_Contact_Form
 * @subpackage FiveTwoFive_Contact_Form/includes
 */

use FiveTwoFive\FiveTwoFive_Contact_Form\Form\Endpoints;
use FiveTwoFive\FiveTwoFive_Contact_Form\Form\Handler;
use FiveTwoFive\FiveTwoFive_Contact_Form\Frontend\Shortcode;
use FiveTwoFive\FiveTwoFive_Contact_Form\Mailer\Mailer;
use FiveTwoFive\FiveTwoFive_Contact_Form\PostType\Submission;

defined( 'ABSPATH' ) || exit;

/**
 * The core plugin class.
 *
 * Defines the plugin name and version, and registers the public-facing and
 * admin hooks. Components live under src/ and are loaded via the Composer
 * PSR-4 autoloader.
 *
 * @since      1.0.0
 * @package    FiveTwoFive_Contact_Form
 * @subpackage FiveTwoFive_Contact_Form/includes
 */
class FiveTwoFive_Contact_Form {

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	private string $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	private string $version;

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var   FiveTwoFive_Contact_Form|null
	 */
	private static ?self $instance = null;

	/**
	 * The submissions post type controller.
	 *
	 * @since 1.0.0
	 * @var   Submission
	 */
	private Submission $submission;

	/**
	 * Main FiveTwoFive_Contact_Form instance.
	 *
	 * Ensures only one instance of the plugin is loaded or can be loaded.
	 *
	 * @since  1.0.0
	 * @return FiveTwoFive_Contact_Form Main instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * Private to enforce the singleton — use FiveTwoFive_Contact_Form::instance().
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->version     = defined( 'FTF_CONTACT_FORM_VERSION' ) ? FTF_CONTACT_FORM_VERSION : '1.0.0';
		$this->plugin_name = 'fivetwofive_contact_form';

		$this->submission = new Submission( $this->plugin_name, $this->version );

		$this->define_public_hooks();
		$this->define_admin_hooks();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 *
	 * @since 1.0.0
	 */
	private function define_public_hooks(): void {
		// The submissions post type must exist on every request (the front-end
		// submit handler writes to it), so it registers on the shared init hook.
		add_action( 'init', array( $this->submission, 'register' ) );

		$shortcode = new Shortcode( $this->plugin_name, $this->version );
		add_action( 'wp_enqueue_scripts', array( $shortcode, 'enqueue_assets' ) );
		add_shortcode( Shortcode::SHORTCODE, array( $shortcode, 'render' ) );

		// The submit pipeline, shared by the REST (AJAX) and admin-post (no-JS)
		// entry points.
		$handler   = new Handler( $this->submission, new Mailer( $this->plugin_name, $this->version ) );
		$endpoints = new Endpoints( $handler );
		$endpoints->register_hooks();
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 *
	 * @since 1.0.0
	 */
	private function define_admin_hooks(): void {
		add_filter( 'manage_' . Submission::POST_TYPE . '_posts_columns', array( $this->submission, 'set_columns' ) );
		add_action( 'manage_' . Submission::POST_TYPE . '_posts_custom_column', array( $this->submission, 'render_column' ), 10, 2 );
		add_action( 'add_meta_boxes', array( $this->submission, 'add_meta_box' ) );
		add_action( 'edit_form_top', array( $this->submission, 'mark_read' ) );

		// Settings page is wired in here in a subsequent commit.
	}

	/**
	 * The slug used to uniquely identify this plugin.
	 *
	 * @since  1.0.0
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since  1.0.0
	 * @return string The version number of the plugin.
	 */
	public function get_version(): string {
		return $this->version;
	}
}
