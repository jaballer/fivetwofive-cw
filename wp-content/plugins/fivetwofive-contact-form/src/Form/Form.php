<?php
/**
 * Shared form contract: field names, nonce action, and the signed time-trap
 * token. Single source of truth used by BOTH the rendered form and the
 * submission handler, so the two can never drift apart.
 *
 * @package    FiveTwoFive_Contact_Form
 * @subpackage FiveTwoFive_Contact_Form/Form
 * @since      1.0.0
 */

namespace FiveTwoFive\FiveTwoFive_Contact_Form\Form;

defined( 'ABSPATH' ) || exit;

/**
 * The form's wire format and spam-trap helpers.
 *
 * @since 1.0.0
 */
final class Form {

	/**
	 * admin-post.php action + REST identifier for a submission.
	 *
	 * @var string
	 */
	public const ACTION = 'ftf_contact_form';

	/**
	 * REST route namespace.
	 *
	 * @var string
	 */
	public const REST_NS = 'fivetwofive-contact-form/v1';

	/**
	 * REST route path.
	 *
	 * @var string
	 */
	public const REST_ROUTE = '/submit';

	/**
	 * Nonce action.
	 *
	 * @var string
	 */
	public const NONCE_ACTION = 'ftf_contact_form_submit';

	/**
	 * Nonce field name.
	 *
	 * @var string
	 */
	public const NONCE_FIELD = 'ftf_contact_nonce';

	/**
	 * Field name: sender name.
	 *
	 * @var string
	 */
	public const FIELD_NAME = 'ftf_name';

	/**
	 * Field name: sender email.
	 *
	 * @var string
	 */
	public const FIELD_EMAIL = 'ftf_email';

	/**
	 * Field name: subject.
	 *
	 * @var string
	 */
	public const FIELD_SUBJECT = 'ftf_subject';

	/**
	 * Field name: message.
	 *
	 * @var string
	 */
	public const FIELD_MESSAGE = 'ftf_message';

	/**
	 * Field name: source page URL.
	 *
	 * @var string
	 */
	public const FIELD_SOURCE = 'ftf_source';

	/**
	 * Honeypot field name — a real visitor never fills this.
	 *
	 * @var string
	 */
	public const FIELD_HONEYPOT = 'ftf_website';

	/**
	 * Signed render-time token field name (backs the time-trap).
	 *
	 * @var string
	 */
	public const FIELD_TIME = 'ftf_t';

	/**
	 * Minimum seconds between render and submit for a plausible human.
	 *
	 * @var int
	 */
	public const MIN_SECONDS = 3;

	/**
	 * Build a signed, tamper-evident render-time token.
	 *
	 * Signing the timestamp stops a bot from simply backdating the value to
	 * defeat the time-trap.
	 *
	 * @since  1.0.0
	 * @return string A "{timestamp}|{hash}" token.
	 */
	public static function time_token(): string {
		$ts = (string) time();

		return $ts . '|' . wp_hash( 'ftf_time|' . $ts );
	}

	/**
	 * Validate a time token and return the seconds elapsed since it was issued.
	 *
	 * @since  1.0.0
	 * @param  string $token Token from the submitted form.
	 * @return int|null Elapsed seconds, or null if missing/tampered.
	 */
	public static function time_token_age( string $token ): ?int {
		$parts = explode( '|', $token, 2 );

		if ( 2 !== count( $parts ) ) {
			return null;
		}

		[ $ts, $hash ] = $parts;

		if ( '' === $ts || ! hash_equals( wp_hash( 'ftf_time|' . $ts ), $hash ) ) {
			return null;
		}

		return time() - (int) $ts;
	}
}
