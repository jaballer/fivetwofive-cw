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

		// 2. Spam gates. Respond as if successful so bots get no signal, but
		// store and send nothing.
		if ( $this->is_spam( $input ) ) {
			return $this->success( 0 );
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

		return $this->success( $id );
	}

	/**
	 * Honeypot + time-trap gate.
	 *
	 * @since  1.0.0
	 * @param  array $input Raw request fields.
	 * @return bool True when the submission looks automated.
	 */
	private function is_spam( array $input ): bool {
		// Honeypot: a hidden field a human never sees and never fills.
		$honeypot = isset( $input[ Form::FIELD_HONEYPOT ] ) ? trim( (string) $input[ Form::FIELD_HONEYPOT ] ) : '';

		if ( '' !== $honeypot ) {
			return true;
		}

		// Time-trap: reject a missing/tampered token, or a submit faster than a
		// human could plausibly type.
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
