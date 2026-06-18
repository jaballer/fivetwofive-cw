<?php
/**
 * Builds and dispatches the admin notification via wp_mail().
 *
 * The mailer is transport-agnostic: it only ever calls wp_mail(). Whether that
 * goes out via PHP mail, Postmark, SES, or generic SMTP is a site-level
 * configuration choice (Phase 2), not a code change here.
 *
 * @package    FiveTwoFive_Contact_Form
 * @subpackage FiveTwoFive_Contact_Form/Mailer
 * @since      1.0.0
 */

namespace FiveTwoFive\FiveTwoFive_Contact_Form\Mailer;

use FiveTwoFive\FiveTwoFive_Contact_Form\Admin\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Composes and sends the contact notification email.
 *
 * @since 1.0.0
 */
class Mailer {

	/**
	 * The slug of this plugin.
	 *
	 * @var string
	 */
	private string $plugin_name;

	/**
	 * The version of this plugin.
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
	 * Resolve the notification recipient.
	 *
	 * Uses the recipient from the settings page when set, otherwise falls back
	 * to the site admin email. Filterable either way.
	 *
	 * @since  1.0.0
	 * @return string Recipient email address.
	 */
	public function recipient(): string {
		$recipient = Settings::get( 'recipient' );

		if ( '' === $recipient ) {
			$recipient = (string) get_option( 'admin_email' );
		}

		/**
		 * Filter the contact form notification recipient.
		 *
		 * @since 1.0.0
		 * @param string $recipient The recipient email address.
		 */
		return (string) apply_filters( 'fivetwofive_contact_form_recipient', $recipient );
	}

	/**
	 * Send the admin notification for a submission.
	 *
	 * Values are re-sanitized here so header construction is safe regardless of
	 * the caller — `Reply-To` in particular must never carry CRLF (header
	 * injection).
	 *
	 * @since  1.0.0
	 * @param  array $data Keys: name, email, subject, message, source.
	 * @return bool Whether wp_mail() accepted the message.
	 */
	public function send( array $data ): bool {
		$to = sanitize_email( $this->recipient() );

		if ( '' === $to || ! is_email( $to ) ) {
			return false;
		}

		$name    = sanitize_text_field( $data['name'] ?? '' );
		$email   = sanitize_email( $data['email'] ?? '' );
		$subject = sanitize_text_field( $data['subject'] ?? '' );
		$message = sanitize_textarea_field( $data['message'] ?? '' );
		$source  = esc_url_raw( $data['source'] ?? '' );

		$prefix = Settings::get( 'subject_prefix' );

		$mail_subject = '' !== $subject
			? trim( $prefix . ' ' . $subject )
			: trim( $prefix . ' ' . __( 'New message from your website', 'fivetwofive-contact-form' ) );

		$lines = array(
			/* translators: %s: sender name. */
			sprintf( __( 'Name: %s', 'fivetwofive-contact-form' ), '' !== $name ? $name : '—' ),
			/* translators: %s: sender email. */
			sprintf( __( 'Email: %s', 'fivetwofive-contact-form' ), $email ),
		);

		if ( '' !== $subject ) {
			/* translators: %s: subject line. */
			$lines[] = sprintf( __( 'Subject: %s', 'fivetwofive-contact-form' ), $subject );
		}

		$lines[] = '';
		$lines[] = __( 'Message:', 'fivetwofive-contact-form' );
		$lines[] = $message;

		if ( '' !== $source ) {
			$lines[] = '';
			/* translators: %s: URL the form was submitted from. */
			$lines[] = sprintf( __( 'Sent from: %s', 'fivetwofive-contact-form' ), $source );
		}

		$body = implode( "\n", $lines );

		// Reply-To the visitor so a reply in the inbox reaches them directly.
		$headers = array();
		if ( '' !== $email && is_email( $email ) ) {
			$headers[] = '' !== $name
				? sprintf( 'Reply-To: %s <%s>', $name, $email )
				: sprintf( 'Reply-To: %s', $email );
		}

		// Optional From identity. Unset by default so the transport (e.g. a
		// verified Postmark domain) supplies it; a transport with "force from"
		// may override this regardless.
		$from_email = Settings::get( 'from_email' );
		if ( '' !== $from_email && is_email( $from_email ) ) {
			$from_name = Settings::get( 'from_name' );
			$headers[] = '' !== $from_name
				? sprintf( 'From: %s <%s>', $from_name, $from_email )
				: sprintf( 'From: %s', $from_email );
		}

		// Send as HTML when enabled. Escaping the assembled (visitor-supplied)
		// body and converting newlines keeps it safe and preserves line breaks
		// under transports that force an HTML body.
		if ( '1' === Settings::get( 'html_email' ) ) {
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$body      = nl2br( esc_html( $body ) );
		}

		return (bool) wp_mail( $to, $mail_subject, $body, $headers );
	}
}
