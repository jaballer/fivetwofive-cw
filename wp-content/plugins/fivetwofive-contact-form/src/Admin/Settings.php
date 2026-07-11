<?php
/**
 * The plugin settings page: notification recipient, sender identity, subject
 * prefix, and notification format. Registered as a submenu under the Contact
 * Form (ftf_submission) admin menu and backed by the WordPress Settings API.
 *
 * The stored option name (`fivetwofive_contact_form_options`) matches the key
 * removed by uninstall.php, so deleting the plugin cleans these settings up.
 *
 * @package    FiveTwoFive_Contact_Form
 * @subpackage FiveTwoFive_Contact_Form/Admin
 * @since      1.1.0
 */

namespace FiveTwoFive\FiveTwoFive_Contact_Form\Admin;

use FiveTwoFive\FiveTwoFive_Contact_Form\Frontend\Shortcode;
use FiveTwoFive\FiveTwoFive_Contact_Form\PostType\Submission;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and renders the contact form settings page.
 *
 * @since 1.1.0
 */
class Settings {

	/**
	 * Option name holding the settings array. Matches uninstall.php.
	 *
	 * @var string
	 */
	public const OPTION = 'fivetwofive_contact_form_options';

	/**
	 * Settings API group (passed to register_setting / settings_fields).
	 *
	 * @var string
	 */
	public const GROUP = 'fivetwofive_contact_form';

	/**
	 * Settings page slug (the add_submenu_page menu slug + Settings API page).
	 *
	 * @var string
	 */
	public const PAGE_SLUG = 'fivetwofive-contact-form-settings';

	/**
	 * Settings section id.
	 *
	 * @var string
	 */
	private const SECTION = 'ftf_contact_form_notification';

	/**
	 * Auto-reply settings section id.
	 *
	 * @var string
	 */
	private const SECTION_AUTOREPLY = 'ftf_contact_form_autoreply';

	/**
	 * Admin notice group for settings errors. Public so the page view can pass
	 * it to settings_errors().
	 *
	 * @var string
	 */
	public const NOTICES = 'fivetwofive_contact_form_messages';

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
	 * Default settings. Empty string means "fall back" (see get()): the
	 * recipient falls back to the site admin email, the From identity falls
	 * back to the transport's default.
	 *
	 * `html_email` defaults to '1' so notifications render correctly under
	 * transports that force an HTML body (e.g. Postmark), where plain-text line
	 * breaks would otherwise collapse into a single run-on line. This default
	 * only governs installs that have never saved settings — get() returns a
	 * stored '0'/'1' ahead of the default, so an existing opt-out is preserved.
	 *
	 * @since  1.1.0
	 * @return array
	 */
	public static function defaults(): array {
		return array(
			'recipient'         => '',
			'from_name'         => '',
			'from_email'        => '',
			'subject_prefix'    => '[Contact]',
			'html_email'        => '1',
			'autoreply_enable'  => '0',
			'autoreply_subject' => '',
			'autoreply_body'    => '',
		);
	}

	/**
	 * Read a single setting, falling back to its default when unset/empty.
	 *
	 * Static so the Mailer (and any other consumer) can read settings without
	 * holding a Settings instance.
	 *
	 * @since  1.1.0
	 * @param  string $key      Setting key.
	 * @param  string $fallback Value to return when neither a stored value nor
	 *                          a non-empty default exists.
	 * @return string
	 */
	public static function get( string $key, string $fallback = '' ): string {
		$options = get_option( self::OPTION, array() );

		if ( is_array( $options ) && isset( $options[ $key ] ) && '' !== $options[ $key ] ) {
			return (string) $options[ $key ];
		}

		$defaults = self::defaults();

		if ( array_key_exists( $key, $defaults ) && '' !== $defaults[ $key ] ) {
			return (string) $defaults[ $key ];
		}

		return $fallback;
	}

	/**
	 * Add the Settings submenu under the Contact Form menu. Hooked to admin_menu.
	 *
	 * @since 1.1.0
	 */
	public function add_menu(): void {
		add_submenu_page(
			'edit.php?post_type=' . Submission::POST_TYPE,
			__( 'Contact Form Settings', 'fivetwofive-contact-form' ),
			__( 'Settings', 'fivetwofive-contact-form' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the settings page wrapper. Capability is re-checked in the view.
	 *
	 * @since 1.1.0
	 */
	public function render_page(): void {
		include dirname( FTF_CONTACT_FORM_PLUGIN_FILE ) . '/resources/views/admin/settings-page.php';
	}

	/**
	 * Register the setting and its typed REST schema.
	 *
	 * Hooked to `init` (not `admin_init`) so it also runs for REST requests: the
	 * core settings controller reads registered settings when building
	 * /wp/v2/settings, and `admin_init` never fires for REST. Running on every
	 * request is cheap and idempotent; the sanitize_callback still gates writes.
	 *
	 * @since 1.1.0
	 */
	public function register_option(): void {
		register_setting(
			self::GROUP,
			self::OPTION,
			array(
				'type'              => 'object',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => self::defaults(),
				'show_in_rest'      => array(
					'schema' => array(
						'type'                 => 'object',
						'properties'           => array(
							'recipient'         => array( 'type' => 'string' ),
							'from_name'         => array( 'type' => 'string' ),
							'from_email'        => array( 'type' => 'string' ),
							'subject_prefix'    => array( 'type' => 'string' ),
							'html_email'        => array(
								'type' => 'string',
								'enum' => array( '0', '1' ),
							),
							'autoreply_enable'  => array(
								'type' => 'string',
								'enum' => array( '0', '1' ),
							),
							'autoreply_subject' => array( 'type' => 'string' ),
							'autoreply_body'    => array( 'type' => 'string' ),
						),
						'additionalProperties' => false,
					),
				),
			)
		);
	}

	/**
	 * Register the settings-page section and fields. Admin-only page UI, so
	 * hooked to `admin_init`.
	 *
	 * @since 1.1.0
	 */
	public function register_fields(): void {
		add_settings_section(
			self::SECTION,
			__( 'Notifications', 'fivetwofive-contact-form' ),
			array( $this, 'section_notification' ),
			self::PAGE_SLUG
		);

		add_settings_field(
			'recipient',
			__( 'Notification recipient', 'fivetwofive-contact-form' ),
			array( $this, 'field_input' ),
			self::PAGE_SLUG,
			self::SECTION,
			array(
				'id'          => 'recipient',
				'type'        => 'email',
				'placeholder' => (string) get_option( 'admin_email' ),
				'description' => __( 'Where submission notifications are sent. Leave blank to use the site admin email.', 'fivetwofive-contact-form' ),
			)
		);

		add_settings_field(
			'from_name',
			__( 'Sender name', 'fivetwofive-contact-form' ),
			array( $this, 'field_input' ),
			self::PAGE_SLUG,
			self::SECTION,
			array(
				'id'          => 'from_name',
				'type'        => 'text',
				'description' => __( 'Optional "From" name on the notification.', 'fivetwofive-contact-form' ),
			)
		);

		add_settings_field(
			'from_email',
			__( 'Sender email (From)', 'fivetwofive-contact-form' ),
			array( $this, 'field_input' ),
			self::PAGE_SLUG,
			self::SECTION,
			array(
				'id'          => 'from_email',
				'type'        => 'email',
				'description' => __( 'Optional "From" address. Leave blank to let the mail transport set it. A transport like Postmark with "force from" enabled may override this.', 'fivetwofive-contact-form' ),
			)
		);

		add_settings_field(
			'subject_prefix',
			__( 'Subject prefix', 'fivetwofive-contact-form' ),
			array( $this, 'field_input' ),
			self::PAGE_SLUG,
			self::SECTION,
			array(
				'id'          => 'subject_prefix',
				'type'        => 'text',
				'placeholder' => '[Contact]',
				'description' => __( 'Prepended to the notification subject line.', 'fivetwofive-contact-form' ),
			)
		);

		add_settings_field(
			'html_email',
			__( 'Send as HTML', 'fivetwofive-contact-form' ),
			array( $this, 'field_checkbox' ),
			self::PAGE_SLUG,
			self::SECTION,
			array(
				'id'          => 'html_email',
				'label'       => __( 'Send the notification as HTML', 'fivetwofive-contact-form' ),
				'description' => __( 'On by default so line breaks survive transports that force HTML or open-tracking (e.g. Postmark). Uncheck to send the notification as plain text instead.', 'fivetwofive-contact-form' ),
			)
		);

		add_settings_section(
			self::SECTION_AUTOREPLY,
			__( 'Auto-reply', 'fivetwofive-contact-form' ),
			array( $this, 'section_autoreply' ),
			self::PAGE_SLUG
		);

		add_settings_field(
			'autoreply_enable',
			__( 'Auto-reply', 'fivetwofive-contact-form' ),
			array( $this, 'field_checkbox' ),
			self::PAGE_SLUG,
			self::SECTION_AUTOREPLY,
			array(
				'id'          => 'autoreply_enable',
				'label'       => __( 'Send a confirmation email to the visitor', 'fivetwofive-contact-form' ),
				'description' => __( 'Off by default. Enable only when an authenticated transport (e.g. Postmark) is configured — an unauthenticated confirmation to an external inbox will be spam-filtered.', 'fivetwofive-contact-form' ),
			)
		);

		add_settings_field(
			'autoreply_subject',
			__( 'Auto-reply subject', 'fivetwofive-contact-form' ),
			array( $this, 'field_input' ),
			self::PAGE_SLUG,
			self::SECTION_AUTOREPLY,
			array(
				'id'          => 'autoreply_subject',
				'type'        => 'text',
				'placeholder' => __( 'Thanks for your message', 'fivetwofive-contact-form' ),
				'description' => __( 'Subject line of the confirmation. Leave blank for the default.', 'fivetwofive-contact-form' ),
			)
		);

		add_settings_field(
			'autoreply_body',
			__( 'Auto-reply message', 'fivetwofive-contact-form' ),
			array( $this, 'field_textarea' ),
			self::PAGE_SLUG,
			self::SECTION_AUTOREPLY,
			array(
				'id'          => 'autoreply_body',
				'description' => __( 'Body of the confirmation. Leave blank for the default. Placeholders: {name} (the visitor) and {site_name}.', 'fivetwofive-contact-form' ),
			)
		);
	}

	/**
	 * Section description.
	 *
	 * @since 1.1.0
	 */
	public function section_notification(): void {
		printf(
			'<p>%s</p>',
			sprintf(
				/* translators: %s: the contact form shortcode, wrapped in a <code> tag. */
				esc_html__( 'Use the shortcode %s to display the contact form on a page or post. The settings below control where submissions are sent and how the notification email is addressed and formatted.', 'fivetwofive-contact-form' ),
				'<code>[' . esc_html( Shortcode::SHORTCODE ) . ']</code>'
			)
		);
	}

	/**
	 * Auto-reply section description.
	 *
	 * @since 1.2.0
	 */
	public function section_autoreply(): void {
		printf(
			'<p>%s</p>',
			esc_html__( 'Optionally send the visitor a branded confirmation after they submit. It goes to an external inbox, so enable it only with an authenticated transport (e.g. Postmark) — otherwise leave it off.', 'fivetwofive-contact-form' )
		);
	}

	/**
	 * Sanitize the submitted settings.
	 *
	 * Overlays the submitted keys onto the currently-stored values so a partial
	 * update — e.g. a REST PATCH of a single property via /wp/v2/settings —
	 * preserves the other fields instead of blanking them. Only known keys are
	 * kept, so the stored option never accumulates stray data.
	 *
	 * @since  1.1.0
	 * @param  mixed $input Raw submitted value.
	 * @return array
	 */
	public function sanitize( $input ): array {
		$input = is_array( $input ) ? $input : array();

		// Start from the current stored values (or defaults). The admin form
		// submits every field — the checkbox via a hidden companion input (see
		// field_checkbox), so an explicit uncheck still records '0' — while a
		// partial REST body only carries the keys it means to change.
		$current = get_option( self::OPTION, self::defaults() );
		$clean   = is_array( $current ) ? array_merge( self::defaults(), $current ) : self::defaults();

		if ( isset( $input['recipient'] ) ) {
			$clean['recipient'] = sanitize_email( (string) $input['recipient'] );
		}

		if ( isset( $input['from_name'] ) ) {
			$clean['from_name'] = sanitize_text_field( (string) $input['from_name'] );
		}

		if ( isset( $input['from_email'] ) ) {
			$clean['from_email'] = sanitize_email( (string) $input['from_email'] );
		}

		if ( isset( $input['subject_prefix'] ) ) {
			$clean['subject_prefix'] = sanitize_text_field( (string) $input['subject_prefix'] );
		}

		if ( isset( $input['html_email'] ) ) {
			$clean['html_email'] = empty( $input['html_email'] ) ? '0' : '1';
		}

		if ( isset( $input['autoreply_enable'] ) ) {
			$clean['autoreply_enable'] = empty( $input['autoreply_enable'] ) ? '0' : '1';
		}

		if ( isset( $input['autoreply_subject'] ) ) {
			$clean['autoreply_subject'] = sanitize_text_field( (string) $input['autoreply_subject'] );
		}

		if ( isset( $input['autoreply_body'] ) ) {
			$clean['autoreply_body'] = sanitize_textarea_field( (string) $input['autoreply_body'] );
		}

		// Drop invalid emails rather than storing them.
		if ( '' !== $clean['recipient'] && ! is_email( $clean['recipient'] ) ) {
			$this->add_notice( 'recipient', __( 'The notification recipient must be a valid email address.', 'fivetwofive-contact-form' ) );
			$clean['recipient'] = '';
		}

		if ( '' !== $clean['from_email'] && ! is_email( $clean['from_email'] ) ) {
			$this->add_notice( 'from_email', __( 'The sender email must be a valid email address.', 'fivetwofive-contact-form' ) );
			$clean['from_email'] = '';
		}

		return $clean;
	}

	/**
	 * Add a Settings API error notice, but only when that API is loaded.
	 *
	 * Settings can be saved via REST (/wp/v2/settings), where add_settings_error()
	 * — part of wp-admin/includes/template.php — is not available; calling it
	 * there would fatal. On that path the value is still dropped, just silently.
	 *
	 * @since 1.1.0
	 * @param string $code    Error slug.
	 * @param string $message Human-readable message.
	 */
	private function add_notice( string $code, string $message ): void {
		if ( function_exists( 'add_settings_error' ) ) {
			add_settings_error( self::NOTICES, $code, $message );
		}
	}

	/**
	 * Render a text/email input field.
	 *
	 * @since 1.1.0
	 * @param array $args { id, type, placeholder, description }.
	 */
	public function field_input( array $args ): void {
		$options = get_option( self::OPTION, self::defaults() );

		$id    = isset( $args['id'] ) ? (string) $args['id'] : '';
		$type  = isset( $args['type'] ) ? (string) $args['type'] : 'text';
		$value = isset( $options[ $id ] ) ? (string) $options[ $id ] : '';

		printf(
			'<input type="%1$s" id="%2$s" name="%3$s[%4$s]" value="%5$s" class="regular-text" placeholder="%6$s" />',
			esc_attr( $type ),
			esc_attr( self::OPTION . '_' . $id ),
			esc_attr( self::OPTION ),
			esc_attr( $id ),
			esc_attr( $value ),
			esc_attr( isset( $args['placeholder'] ) ? (string) $args['placeholder'] : '' )
		);

		if ( ! empty( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( (string) $args['description'] ) );
		}
	}

	/**
	 * Render a multi-line textarea field.
	 *
	 * @since 1.2.0
	 * @param array $args { id, placeholder, description }.
	 */
	public function field_textarea( array $args ): void {
		$options = get_option( self::OPTION, self::defaults() );

		$id    = isset( $args['id'] ) ? (string) $args['id'] : '';
		$value = isset( $options[ $id ] ) ? (string) $options[ $id ] : '';

		printf(
			'<textarea id="%1$s" name="%2$s[%3$s]" rows="6" class="large-text" placeholder="%4$s">%5$s</textarea>',
			esc_attr( self::OPTION . '_' . $id ),
			esc_attr( self::OPTION ),
			esc_attr( $id ),
			esc_attr( isset( $args['placeholder'] ) ? (string) $args['placeholder'] : '' ),
			esc_textarea( $value )
		);

		if ( ! empty( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( (string) $args['description'] ) );
		}
	}

	/**
	 * Render a single checkbox field (stored as '1' / '0').
	 *
	 * @since 1.1.0
	 * @param array $args { id, label, description }.
	 */
	public function field_checkbox( array $args ): void {
		$options = get_option( self::OPTION, self::defaults() );

		$id      = isset( $args['id'] ) ? (string) $args['id'] : '';
		$checked = isset( $options[ $id ] ) && '1' === (string) $options[ $id ];

		// Hidden companion: an unchecked box submits nothing, so this guarantees
		// the key is present on every admin save ('0' unless the checkbox, which
		// shares the name and follows it, overrides with '1'). That lets sanitize()
		// treat a *missing* key as "preserve" for partial REST updates.
		printf(
			'<input type="hidden" name="%1$s[%2$s]" value="0" />',
			esc_attr( self::OPTION ),
			esc_attr( $id )
		);

		printf(
			'<label><input type="checkbox" id="%1$s" name="%2$s[%3$s]" value="1" %4$s /> %5$s</label>',
			esc_attr( self::OPTION . '_' . $id ),
			esc_attr( self::OPTION ),
			esc_attr( $id ),
			checked( $checked, true, false ),
			esc_html( isset( $args['label'] ) ? (string) $args['label'] : '' )
		);

		if ( ! empty( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( (string) $args['description'] ) );
		}
	}
}
