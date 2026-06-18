<?php
/**
 * The [fivetwofive_contact_form] shortcode: renders the contact form and
 * enqueues its scoped stylesheet only on pages that actually use it.
 *
 * @package    FiveTwoFive_Contact_Form
 * @subpackage FiveTwoFive_Contact_Form/Frontend
 * @since      1.0.0
 */

namespace FiveTwoFive\FiveTwoFive_Contact_Form\Frontend;

use FiveTwoFive\FiveTwoFive_Contact_Form\Form\Form;

defined( 'ABSPATH' ) || exit;

/**
 * Front-end shortcode renderer.
 *
 * @since 1.0.0
 */
class Shortcode {

	/**
	 * The shortcode tag.
	 *
	 * @var string
	 */
	public const SHORTCODE = 'fivetwofive_contact_form';

	/**
	 * Stylesheet handle.
	 *
	 * @var string
	 */
	private const HANDLE = 'fivetwofive-contact-form';

	/**
	 * The slug of this plugin.
	 *
	 * @var string
	 */
	private string $plugin_name;

	/**
	 * The version of this plugin (asset cache-buster).
	 *
	 * @var string
	 */
	private string $version;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name The plugin slug.
	 * @param string $version     The plugin version.
	 */
	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheet and script. Both are enqueued on demand in
	 * render(), so assets only load on pages that contain the shortcode. Hooked
	 * to wp_enqueue_scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets(): void {
		wp_register_style(
			self::HANDLE,
			plugins_url( 'resources/assets/frontend/styles/fivetwofive-contact-form.css', FTF_CONTACT_FORM_PLUGIN_FILE ),
			array(),
			$this->version
		);

		wp_register_script(
			self::HANDLE,
			plugins_url( 'resources/assets/frontend/scripts/fivetwofive-contact-form.js', FTF_CONTACT_FORM_PLUGIN_FILE ),
			array(),
			$this->version,
			true
		);

		wp_localize_script(
			self::HANDLE,
			'FiveTwoFiveContactForm',
			array(
				'endpoint' => esc_url_raw( rest_url( Form::REST_NS . Form::REST_ROUTE ) ),
				'error'    => __( 'Sorry, something went wrong. Please try again.', 'fivetwofive-contact-form' ),
			)
		);
	}

	/**
	 * Render the [fivetwofive_contact_form] shortcode.
	 *
	 * @since 1.0.0
	 * @param array|string $atts Shortcode attributes.
	 * @return string Form markup.
	 */
	public function render( $atts ): string {
		$atts = shortcode_atts(
			array(
				'title'   => '',
				'subject' => '',
			),
			$atts,
			self::SHORTCODE
		);

		// Load assets only now that we know the shortcode is on the page.
		wp_enqueue_style( self::HANDLE );
		wp_enqueue_script( self::HANDLE );

		$permalink = get_permalink();

		// Pre-resolved values for the view. Included files do not inherit this
		// file's `use` aliases, so the view never references the Form class.
		$ftf = array(
			'title'        => (string) $atts['title'],
			'subject'      => (string) $atts['subject'],
			'post_url'     => admin_url( 'admin-post.php' ),
			'action'       => Form::ACTION,
			'nonce_action' => Form::NONCE_ACTION,
			'nonce_field'  => Form::NONCE_FIELD,
			'f_name'       => Form::FIELD_NAME,
			'f_email'      => Form::FIELD_EMAIL,
			'f_subject'    => Form::FIELD_SUBJECT,
			'f_message'    => Form::FIELD_MESSAGE,
			'f_source'     => Form::FIELD_SOURCE,
			'f_honeypot'   => Form::FIELD_HONEYPOT,
			'f_time'       => Form::FIELD_TIME,
			'time_token'   => Form::time_token(),
			'source'       => $permalink ? $permalink : home_url( '/' ),
			'status'       => $this->current_status(),
		);

		ob_start();
		include dirname( FTF_CONTACT_FORM_PLUGIN_FILE ) . '/resources/views/frontend/contact-form.php';

		return (string) ob_get_clean();
	}

	/**
	 * Read the no-JS submission status flag appended by our post-submit
	 * redirect. Used only to choose which notice to display.
	 *
	 * @since  1.0.0
	 * @return string One of '', 'sent', 'error'.
	 */
	private function current_status(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display flag from our own redirect; selects a notice, changes no state.
		$status = isset( $_GET['ftf_contact'] ) ? sanitize_key( wp_unslash( $_GET['ftf_contact'] ) ) : '';

		return in_array( $status, array( 'sent', 'error' ), true ) ? $status : '';
	}
}
