<?php
/**
 * The submission pipeline shared by every entry point (REST + no-JS POST).
 *
 * One place owns the security and business rules — nonce, spam gates,
 * validation, storage, notification — so the AJAX and no-JS paths can never
 * diverge in how they treat input.
 *
 * @package    FiveTwoFive_Contact_Form
 * @subpackage FiveTwoFive_Contact_Form/Form
 * @since      1.0.0
 */

namespace FiveTwoFive\FiveTwoFive_Contact_Form\Form;

use FiveTwoFive\FiveTwoFive_Contact_Form\Admin\Settings;
use FiveTwoFive\FiveTwoFive_Contact_Form\Mailer\Mailer;
use FiveTwoFive\FiveTwoFive_Contact_Form\PostType\Submission;

defined( 'ABSPATH' ) || exit;

/**
 * Validates, stores, and dispatches a contact submission.
 *
 * @since 1.0.0
 */
class Handler {

	/**
	 * Max accepted length for single-line fields (name, subject).
	 *
	 * @var int
	 */
	private const MAX_LINE = 200;

	/**
	 * Max accepted length for the message body.
	 *
	 * @var int
	 */
	private const MAX_MESSAGE = 5000;

	/**
	 * Submissions store.
	 *
	 * @var Submission
	 */
	private Submission $submission;

	/**
	 * Notification mailer.
	 *
	 * @var Mailer
	 */
	private Mailer $mailer;

	/**
	 * Constructor.
	 *
	 * @param Submission $submission Submissions store.
	 * @param Mailer     $mailer     Notification mailer.
	 */
	public function __construct( Submission $submission, Mailer $mailer ) {
		$this->submission = $submission;
		$this->mailer     = $mailer;
	}

	/**
	 * Process one submission.
	 *
	 * The caller passes the already-unslashed request fields; this method owns
	 * every check. The return shape is transport-neutral so REST and no-JS can
	 * each format it their own way.
	 *
	 * @since  1.0.0
	 * @param  array $input Raw (unslashed) request fields, keyed by field name.
	 * @return array { ok: bool, code: string, message: string, id: int }
	 */
	public function handle( array $input ): array {
		// 1. CSRF: the form's nonce must verify. Works for anonymous visitors.
		$nonce = isset( $input[ Form::NONCE_FIELD ] ) ? (string) $input[ Form::NONCE_FIELD ] : '';

		if ( ! wp_verify_nonce( $nonce, Form::NONCE_ACTION ) ) {
			return $this->error(
				'bad_nonce',
				__( 'Your session expired. Please reload the page and try again.', 'fivetwofive-contact-form' )
			);
		}

		// 2. Spam gates, split by confidence so a real lead is never lost:
		// - Honeypot: only a bot fills a hidden field, so drop it silently
		//   (a fake success) — the bot gets no signal to adapt.
		// - Time-trap: a too-fast or missing/tampered token can also catch a
		//   genuine person (autofill, a pasted message), so never silently
		//   discard it behind a "sent" message. Return a retryable error
		//   instead; a real sender simply submits again (the render token ages
		//   past the threshold), while a bot gains nothing it could not already
		//   do by waiting.
		if ( $this->is_honeypot_filled( $input ) ) {
			return $this->success( 0 );
		}

		if ( $this->is_too_fast( $input ) ) {
			return $this->error(
				'too_fast',
				__( 'That looked a little too quick — please take a moment and send your message again.', 'fivetwofive-contact-form' )
			);
		}

		// Per-IP rate limit: a volume cap on top of the honeypot + time-trap, so
		// a bot that clears those gates still can't flood the store and inbox.
		// Only counts submissions that reach here; returns a retryable error past
		// the threshold. Disable by filtering the limit to 0.
		if ( $this->is_rate_limited() ) {
			return $this->error(
				'rate_limited',
				__( 'You have sent several messages recently. Please wait a few minutes before sending another.', 'fivetwofive-contact-form' )
			);
		}

		// 3. Sanitize + validate the real fields.
		$name    = sanitize_text_field( (string) ( $input[ Form::FIELD_NAME ] ?? '' ) );
		$email   = sanitize_email( (string) ( $input[ Form::FIELD_EMAIL ] ?? '' ) );
		$subject = sanitize_text_field( (string) ( $input[ Form::FIELD_SUBJECT ] ?? '' ) );
		$message = sanitize_textarea_field( (string) ( $input[ Form::FIELD_MESSAGE ] ?? '' ) );
		$source  = esc_url_raw( (string) ( $input[ Form::FIELD_SOURCE ] ?? '' ) );

		$errors = array();

		if ( '' === $name ) {
			$errors[] = __( 'Please enter your name.', 'fivetwofive-contact-form' );
		}

		if ( '' === $email || ! is_email( $email ) ) {
			$errors[] = __( 'Please enter a valid email address.', 'fivetwofive-contact-form' );
		}

		if ( '' === $message ) {
			$errors[] = __( 'Please enter a message.', 'fivetwofive-contact-form' );
		}

		if (
			strlen( $name ) > self::MAX_LINE
			|| strlen( $subject ) > self::MAX_LINE
			|| strlen( $message ) > self::MAX_MESSAGE
		) {
			$errors[] = __( 'One or more fields are too long.', 'fivetwofive-contact-form' );
		}

		if ( ! empty( $errors ) ) {
			return $this->error( 'invalid', implode( ' ', $errors ) );
		}

		$fields = array(
			'name'    => $name,
			'email'   => $email,
			'subject' => $subject,
			'message' => $message,
			'source'  => $source,
		);

		// 4. Store first — the lead must survive even if mail fails.
		$id = $this->submission->create( $fields );

		if ( 0 === $id ) {
			return $this->error(
				'store_failed',
				__( 'Something went wrong saving your message. Please try again.', 'fivetwofive-contact-form' )
			);
		}

		// 5. Notify, then record the delivery outcome on the stored lead.
		$sent = $this->mailer->send( $fields );
		$this->submission->set_email_status( $id, $sent );

		// 6. Optionally send the visitor a confirmation. Opt-in (off unless the
		// owner has a real transport configured), and fire-and-forget: its
		// outcome must not change the stored lead or the visitor's response.
		if ( $this->autoreply_enabled() ) {
			$this->mailer->send_autoreply( $fields );
		}

		return $this->success( $id );
	}

	/**
	 * Whether to send the visitor auto-reply.
	 *
	 * Off unless enabled in settings — an unauthenticated confirmation to an
	 * external mailbox would spam-fold. Filterable so a site can additionally
	 * gate it on its own transport check.
	 *
	 * @since  1.2.0
	 * @return bool
	 */
	private function autoreply_enabled(): bool {
		$enabled = '1' === Settings::get( 'autoreply_enable' );

		/**
		 * Filter whether the visitor auto-reply is sent after a submission.
		 *
		 * @since 1.2.0
		 * @param bool $enabled Whether the auto-reply will be sent.
		 */
		return (bool) apply_filters( 'fivetwofive_contact_form_send_autoreply', $enabled );
	}

	/**
	 * Per-IP submission rate limit, backed by a short-lived transient counter.
	 *
	 * Counts submissions that reach this gate (i.e. that already cleared the
	 * honeypot + time-trap) and blocks once an IP exceeds the limit within the
	 * window. Only a salted hash of the IP is stored (the transient key), never
	 * the raw address, and it auto-expires — so this adds no lasting PII.
	 *
	 * Fails open: when the client IP can't be determined, it never blocks.
	 *
	 * @since  1.4.0
	 * @return bool True when the current IP is over its limit.
	 */
	private function is_rate_limited(): bool {
		/**
		 * Filter the maximum submissions allowed per IP per window. Return 0 (or
		 * less) to disable the rate limit entirely.
		 *
		 * @since 1.4.0
		 * @param int $limit Max submissions per window.
		 */
		$limit = (int) apply_filters( 'fivetwofive_contact_form_rate_limit', 5 );

		if ( $limit <= 0 ) {
			return false;
		}

		$ip = $this->client_ip();

		if ( '' === $ip ) {
			return false;
		}

		/**
		 * Filter the rate-limit window, in seconds.
		 *
		 * @since 1.4.0
		 * @param int $window Window length in seconds.
		 */
		$window = (int) apply_filters( 'fivetwofive_contact_form_rate_window', 10 * MINUTE_IN_SECONDS );

		$key   = 'ftf_cf_rl_' . wp_hash( $ip );
		$count = (int) get_transient( $key );

		if ( $count >= $limit ) {
			return true;
		}

		set_transient( $key, $count + 1, max( 1, $window ) );

		return false;
	}

	/**
	 * The client IP used for rate limiting.
	 *
	 * Defaults to REMOTE_ADDR (the peer address, not spoofable at the app
	 * layer). A site behind a reverse proxy / CDN (e.g. Cloudflare) can filter
	 * in the real client IP from a trusted header — never trust `X-Forwarded-*`
	 * blindly, as it is caller-supplied.
	 *
	 * @since  1.4.0
	 * @return string A validated IP address, or '' when none is available.
	 */
	private function client_ip(): string {
		$raw = isset( $_SERVER['REMOTE_ADDR'] )
			? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) )
			: '';

		/**
		 * Filter the client IP used for rate limiting. Return a real client IP
		 * from a trusted proxy header here when the site sits behind a CDN.
		 *
		 * @since 1.4.0
		 * @param string $raw The peer IP from REMOTE_ADDR (may be empty).
		 */
		$ip = (string) apply_filters( 'fivetwofive_contact_form_client_ip', $raw );

		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
	}

	/**
	 * Honeypot gate: a hidden field a human never sees and never fills.
	 *
	 * @since  1.0.0
	 * @param  array $input Raw request fields.
	 * @return bool True when the honeypot was filled (a definite bot).
	 */
	private function is_honeypot_filled( array $input ): bool {
		$honeypot = isset( $input[ Form::FIELD_HONEYPOT ] ) ? trim( (string) $input[ Form::FIELD_HONEYPOT ] ) : '';

		return '' !== $honeypot;
	}

	/**
	 * Time-trap gate: a missing/tampered render token, or a submit faster than
	 * a human could plausibly type.
	 *
	 * @since  1.0.0
	 * @param  array $input Raw request fields.
	 * @return bool True when the submission arrived implausibly fast.
	 */
	private function is_too_fast( array $input ): bool {
		$age = Form::time_token_age( (string) ( $input[ Form::FIELD_TIME ] ?? '' ) );

		return null === $age || $age < Form::MIN_SECONDS;
	}

	/**
	 * Build a success result.
	 *
	 * @since  1.0.0
	 * @param  int $id Stored submission ID (0 for a silently-dropped bot).
	 * @return array
	 */
	private function success( int $id ): array {
		return array(
			'ok'      => true,
			'code'    => 'sent',
			'message' => __( 'Thanks — your message has been sent.', 'fivetwofive-contact-form' ),
			'id'      => $id,
		);
	}

	/**
	 * Build an error result.
	 *
	 * @since  1.0.0
	 * @param  string $code    Machine-readable error code.
	 * @param  string $message Human-readable message.
	 * @return array
	 */
	private function error( string $code, string $message ): array {
		return array(
			'ok'      => false,
			'code'    => $code,
			'message' => $message,
			'id'      => 0,
		);
	}
}
