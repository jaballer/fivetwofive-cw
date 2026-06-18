<?php
/**
 * Request entry points for a submission: the REST route used by the AJAX path
 * and the admin-post handler used by the no-JS fallback. Both are thin adapters
 * that hand the request to the shared Handler and format its result.
 *
 * @package    FiveTwoFive_Contact_Form
 * @subpackage FiveTwoFive_Contact_Form/Form
 * @since      1.0.0
 */

namespace FiveTwoFive\FiveTwoFive_Contact_Form\Form;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and serves the submission endpoints.
 *
 * @since 1.0.0
 */
class Endpoints {

	/**
	 * The shared submission pipeline.
	 *
	 * @var Handler
	 */
	private Handler $handler;

	/**
	 * Constructor.
	 *
	 * @param Handler $handler The submission pipeline.
	 */
	public function __construct( Handler $handler ) {
		$this->handler = $handler;
	}

	/**
	 * Register both entry points.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		add_action( 'admin_post_' . Form::ACTION, array( $this, 'post_submit' ) );
		add_action( 'admin_post_nopriv_' . Form::ACTION, array( $this, 'post_submit' ) );
	}

	/**
	 * Register the REST submit route. Open to all (it is a public form); CSRF is
	 * enforced by the form nonce inside the Handler.
	 *
	 * @since 1.0.0
	 */
	public function register_routes(): void {
		register_rest_route(
			Form::REST_NS,
			Form::REST_ROUTE,
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_submit' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Serve the AJAX (REST) submission.
	 *
	 * @since  1.0.0
	 * @param  \WP_REST_Request $request The request.
	 * @return \WP_REST_Response
	 */
	public function rest_submit( \WP_REST_Request $request ): \WP_REST_Response {
		$result = $this->handler->handle( $this->collect( $request->get_params() ) );

		return new \WP_REST_Response( $result, $this->status_for( $result ) );
	}

	/**
	 * Serve the no-JS (admin-post) submission, then redirect back to the form
	 * with a status flag the shortcode reads to show a notice.
	 *
	 * @since 1.0.0
	 */
	public function post_submit(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- raw request bag; unslashed here, then nonce-verified and per-field sanitized in Handler::handle().
		$source = isset( $_POST ) ? wp_unslash( $_POST ) : array();
		$result = $this->handler->handle( $this->collect( (array) $source ) );

		$redirect = wp_get_referer();
		if ( false === $redirect ) {
			$redirect = home_url( '/' );
		}

		$redirect = add_query_arg( 'ftf_contact', ! empty( $result['ok'] ) ? 'sent' : 'error', $redirect );

		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Pull the known form fields out of a request bag as scalars.
	 *
	 * @since  1.0.0
	 * @param  array $source Request parameters.
	 * @return array Field name => string value.
	 */
	private function collect( array $source ): array {
		$keys = array(
			Form::NONCE_FIELD,
			Form::FIELD_NAME,
			Form::FIELD_EMAIL,
			Form::FIELD_SUBJECT,
			Form::FIELD_MESSAGE,
			Form::FIELD_SOURCE,
			Form::FIELD_HONEYPOT,
			Form::FIELD_TIME,
		);

		$out = array();
		foreach ( $keys as $key ) {
			$out[ $key ] = ( isset( $source[ $key ] ) && is_scalar( $source[ $key ] ) ) ? (string) $source[ $key ] : '';
		}

		return $out;
	}

	/**
	 * Map a Handler result to an HTTP status code.
	 *
	 * @since  1.0.0
	 * @param  array $result Handler result.
	 * @return int
	 */
	private function status_for( array $result ): int {
		if ( ! empty( $result['ok'] ) ) {
			return 200;
		}

		switch ( $result['code'] ?? '' ) {
			case 'bad_nonce':
				return 403;
			case 'invalid':
				return 422;
			case 'store_failed':
				return 500;
			default:
				return 400;
		}
	}
}
