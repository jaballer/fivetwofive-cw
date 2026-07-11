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

		// Optional From identity (see from_header()).
		$from = $this->from_header();
		if ( '' !== $from ) {
			$headers[] = $from;
		}

		list( $body, $headers ) = $this->apply_format( $body, $headers );

		return (bool) wp_mail( $to, $mail_subject, $body, $headers );
	}

	/**
	 * Send the visitor a branded confirmation (auto-reply) after a successful
	 * submission.
	 *
	 * The caller gates this on the `autoreply_enable` setting, so it only fires
	 * once the site owner has confirmed a real (authenticated) transport is in
	 * place — an unauthenticated confirmation to an external consumer mailbox
	 * would spam-fold and hurt sender reputation. From identity and HTML
	 * formatting follow the same settings as the admin notification; Reply-To is
	 * the site inbox so the visitor's reply reaches the owner.
	 *
	 * @since  1.2.0
	 * @param  array $data Keys: name, email (others are unused here).
	 * @return bool Whether wp_mail() accepted the message.
	 */
	public function send_autoreply( array $data ): bool {
		$to = sanitize_email( $data['email'] ?? '' );

		if ( '' === $to || ! is_email( $to ) ) {
			return false;
		}

		$name = sanitize_text_field( $data['name'] ?? '' );

		// {name} falls back to a neutral greeting; {site_name} is the blog name
		// (entity-decoded so "Jane &amp; Co" reads naturally before esc_html()).
		$replacements = array(
			'{name}'      => '' !== $name ? $name : __( 'there', 'fivetwofive-contact-form' ),
			'{site_name}' => wp_specialchars_decode( (string) get_bloginfo( 'name' ), ENT_QUOTES ),
		);

		$subject = trim( (string) Settings::get( 'autoreply_subject' ) );
		if ( '' === $subject ) {
			$subject = __( 'Thanks for your message', 'fivetwofive-contact-form' );
		}
		$subject = strtr( $subject, $replacements );

		$body = (string) Settings::get( 'autoreply_body' );
		if ( '' === trim( $body ) ) {
			$body = self::default_autoreply_body();
		}
		$body = strtr( $body, $replacements );

		// Reply-To the site inbox so the visitor's reply reaches the owner.
		$headers  = array();
		$reply_to = sanitize_email( $this->recipient() );
		if ( '' !== $reply_to && is_email( $reply_to ) ) {
			$headers[] = sprintf( 'Reply-To: %s', $reply_to );
		}

		$from = $this->from_header();
		if ( '' !== $from ) {
			$headers[] = $from;
		}

		list( $body, $headers ) = $this->apply_format( $body, $headers );

		return (bool) wp_mail( $to, $subject, $body, $headers );
	}

	/**
	 * The optional "From" header, built from settings.
	 *
	 * Left empty by default so the transport (e.g. a verified Postmark domain)
	 * supplies the From; a transport with "force from" may override it anyway.
	 *
	 * @since  1.2.0
	 * @return string A `From: …` header line, or '' when no valid from_email.
	 */
	private function from_header(): string {
		$from_email = Settings::get( 'from_email' );

		if ( '' === $from_email || ! is_email( $from_email ) ) {
			return '';
		}

		$from_name = Settings::get( 'from_name' );

		return '' !== $from_name
			? sprintf( 'From: %s <%s>', $from_name, $from_email )
			: sprintf( 'From: %s', $from_email );
	}

	/**
	 * Apply HTML formatting when the html_email setting is on: set the content
	 * type and escape + <br>-convert the (untrusted) body. A no-op otherwise.
	 *
	 * @since  1.2.0
	 * @param  string $body    Assembled plain-text body.
	 * @param  array  $headers Headers accumulated so far.
	 * @return array{0:string,1:array} The [ body, headers ] for wp_mail().
	 */
	private function apply_format( string $body, array $headers ): array {
		if ( '1' === Settings::get( 'html_email' ) ) {
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
			$body      = nl2br( esc_html( $body ) );
		}

		return array( $body, $headers );
	}

	/**
	 * The built-in auto-reply body, used when the setting is left blank.
	 *
	 * Supports the {name} and {site_name} placeholders (see send_autoreply()).
	 *
	 * @since  1.2.0
	 * @return string
	 */
	private static function default_autoreply_body(): string {
		return __(
			"Hi {name},\n\nThanks for getting in touch. I've received your message and will get back to you as soon as I can.\n\n— {site_name}",
			'fivetwofive-contact-form'
		);
	}
}
