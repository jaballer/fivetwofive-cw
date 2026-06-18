<?php
/**
 * The ftf_submission custom post type — the stored record of every contact
 * form submission, and the source of truth that survives mail failure.
 *
 * @package    FiveTwoFive_Contact_Form
 * @subpackage FiveTwoFive_Contact_Form/PostType
 * @since      1.0.0
 */

namespace FiveTwoFive\FiveTwoFive_Contact_Form\PostType;

defined( 'ABSPATH' ) || exit;

/**
 * Registers and manages the ftf_submission post type and its admin inbox.
 *
 * Private CPT (public => false, show_ui => true): submissions are viewable and
 * searchable in wp-admin but never front-end queryable.
 *
 * @since 1.0.0
 */
class Submission {

	/**
	 * Post type key.
	 *
	 * @var string
	 */
	public const POST_TYPE = 'ftf_submission';

	/**
	 * Meta key: sender name.
	 *
	 * @var string
	 */
	public const META_NAME = '_ftf_name';

	/**
	 * Meta key: sender email (also used as Reply-To).
	 *
	 * @var string
	 */
	public const META_EMAIL = '_ftf_email';

	/**
	 * Meta key: subject line.
	 *
	 * @var string
	 */
	public const META_SUBJECT = '_ftf_subject';

	/**
	 * Meta key: URL the form was submitted from.
	 *
	 * @var string
	 */
	public const META_SOURCE = '_ftf_source';

	/**
	 * Meta key: mail delivery status (sent|failed).
	 *
	 * @var string
	 */
	public const META_STATUS = '_ftf_status';

	/**
	 * Meta key: read flag (0|1).
	 *
	 * @var string
	 */
	public const META_READ = '_ftf_read';

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
	 * Register the post type. Hooked to `init`.
	 *
	 * @since 1.0.0
	 */
	public function register(): void {
		$labels = array(
			'name'               => _x( 'Submissions', 'post type general name', 'fivetwofive-contact-form' ),
			'singular_name'      => _x( 'Submission', 'post type singular name', 'fivetwofive-contact-form' ),
			'menu_name'          => _x( 'Contact Form', 'admin menu', 'fivetwofive-contact-form' ),
			'all_items'          => __( 'Submissions', 'fivetwofive-contact-form' ),
			'view_item'          => __( 'View Submission', 'fivetwofive-contact-form' ),
			'edit_item'          => __( 'View Submission', 'fivetwofive-contact-form' ),
			'search_items'       => __( 'Search Submissions', 'fivetwofive-contact-form' ),
			'not_found'          => __( 'No submissions found.', 'fivetwofive-contact-form' ),
			'not_found_in_trash' => __( 'No submissions found in Trash.', 'fivetwofive-contact-form' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => false,
			'show_in_rest'        => false,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 26,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'capabilities'        => array(
				// Submissions hold visitor PII (name, email, message) and are the
				// source of truth for leads. Gate every action behind
				// `manage_options` so only administrators can read, edit, or
				// delete them, rather than inheriting the built-in `post` caps
				// that editors, authors, and contributors also hold. With
				// map_meta_cap on, the edit_post/read_post/delete_post meta caps
				// resolve through these primitives.
				'edit_posts'             => 'manage_options',
				'edit_others_posts'      => 'manage_options',
				'edit_published_posts'   => 'manage_options',
				'edit_private_posts'     => 'manage_options',
				'read_private_posts'     => 'manage_options',
				'publish_posts'          => 'manage_options',
				'delete_posts'           => 'manage_options',
				'delete_others_posts'    => 'manage_options',
				'delete_published_posts' => 'manage_options',
				'delete_private_posts'   => 'manage_options',
				// Submissions are created programmatically; no manual "Add New".
				'create_posts'           => 'do_not_allow',
			),
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'hierarchical'        => false,
			'exclude_from_search' => true,
		);

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Persist a submission as a post + meta. The plugin's source of truth.
	 *
	 * Values are sanitized here defensively so storage is always safe,
	 * regardless of the caller.
	 *
	 * @since 1.0.0
	 * @param array $data Keys: name, email, subject, message, source.
	 * @return int New post ID, or 0 on failure.
	 */
	public function create( array $data ): int {
		$name    = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
		$email   = isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '';
		$subject = isset( $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : '';
		$message = isset( $data['message'] ) ? sanitize_textarea_field( $data['message'] ) : '';
		$source  = isset( $data['source'] ) ? esc_url_raw( $data['source'] ) : '';

		$display_name = '' !== $name ? $name : __( 'Anonymous', 'fivetwofive-contact-form' );

		$title = sprintf(
			/* translators: 1: sender name, 2: submission date. */
			_x( '%1$s — %2$s', 'submission list title', 'fivetwofive-contact-form' ),
			$display_name,
			date_i18n( (string) get_option( 'date_format' ) )
		);

		$post_id = wp_insert_post(
			array(
				'post_type'    => self::POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_content' => $message,
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return 0;
		}

		update_post_meta( $post_id, self::META_NAME, $name );
		update_post_meta( $post_id, self::META_EMAIL, $email );
		update_post_meta( $post_id, self::META_SUBJECT, $subject );
		update_post_meta( $post_id, self::META_SOURCE, $source );
		update_post_meta( $post_id, self::META_READ, '0' );

		return (int) $post_id;
	}

	/**
	 * Record the mail delivery outcome on a submission.
	 *
	 * @since 1.0.0
	 * @param int  $post_id The submission ID.
	 * @param bool $sent    Whether wp_mail() reported success.
	 */
	public function set_email_status( int $post_id, bool $sent ): void {
		update_post_meta( $post_id, self::META_STATUS, $sent ? 'sent' : 'failed' );
	}

	/**
	 * Define the admin list columns. Hooked to manage_{cpt}_posts_columns.
	 *
	 * @since 1.0.0
	 * @param array $columns Existing columns.
	 * @return array
	 */
	public function set_columns( array $columns ): array {
		$date = $columns['date'] ?? '';
		unset( $columns['date'] );

		$columns['ftf_email']   = __( 'Email', 'fivetwofive-contact-form' );
		$columns['ftf_subject'] = __( 'Subject', 'fivetwofive-contact-form' );
		$columns['ftf_status']  = __( 'Notification', 'fivetwofive-contact-form' );

		if ( '' !== $date ) {
			$columns['date'] = $date;
		}

		return $columns;
	}

	/**
	 * Render a custom column cell. Hooked to manage_{cpt}_posts_custom_column.
	 *
	 * @since 1.0.0
	 * @param string $column  Column key.
	 * @param int    $post_id Submission ID.
	 */
	public function render_column( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'ftf_email':
				$email = (string) get_post_meta( $post_id, self::META_EMAIL, true );

				if ( '' === $email ) {
					echo '&mdash;';
					break;
				}

				$html = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( 'mailto:' . $email ),
					esc_html( $email )
				);

				// Unread submissions are emphasised in the inbox.
				if ( '1' !== (string) get_post_meta( $post_id, self::META_READ, true ) ) {
					$html = '<strong>' . $html . '</strong>';
				}

				echo wp_kses(
					$html,
					array(
						'a'      => array( 'href' => array() ),
						'strong' => array(),
					)
				);
				break;

			case 'ftf_subject':
				$subject = (string) get_post_meta( $post_id, self::META_SUBJECT, true );
				echo esc_html( '' !== $subject ? $subject : '—' );
				break;

			case 'ftf_status':
				$status = (string) get_post_meta( $post_id, self::META_STATUS, true );

				if ( '' === $status ) {
					echo '&mdash;';
					break;
				}

				$labels = array(
					'sent'   => __( 'Sent', 'fivetwofive-contact-form' ),
					'failed' => __( 'Failed', 'fivetwofive-contact-form' ),
				);

				echo esc_html( $labels[ $status ] ?? $status );
				break;
		}
	}

	/**
	 * Register the submission-details meta box. Hooked to add_meta_boxes.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_box(): void {
		add_meta_box(
			'ftf_submission_details',
			__( 'Submission Details', 'fivetwofive-contact-form' ),
			array( $this, 'render_meta_box' ),
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Render the read-only submission details. All output is escaped at the
	 * point of output — the message is visitor-supplied and untrusted.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post The submission.
	 */
	public function render_meta_box( \WP_Post $post ): void {
		$name    = (string) get_post_meta( $post->ID, self::META_NAME, true );
		$email   = (string) get_post_meta( $post->ID, self::META_EMAIL, true );
		$subject = (string) get_post_meta( $post->ID, self::META_SUBJECT, true );
		$source  = (string) get_post_meta( $post->ID, self::META_SOURCE, true );
		$status  = (string) get_post_meta( $post->ID, self::META_STATUS, true );
		$message = (string) $post->post_content;

		$rows = array(
			__( 'Name', 'fivetwofive-contact-form' )      => esc_html( '' !== $name ? $name : '—' ),
			__( 'Email', 'fivetwofive-contact-form' )     => '' !== $email
				? '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html( $email ) . '</a>'
				: '—',
			__( 'Subject', 'fivetwofive-contact-form' )   => esc_html( '' !== $subject ? $subject : '—' ),
			__( 'Submitted', 'fivetwofive-contact-form' ) => esc_html( get_the_date( '', $post ) . ' ' . get_the_time( '', $post ) ),
			__( 'Source page', 'fivetwofive-contact-form' ) => '' !== $source
				? '<a href="' . esc_url( $source ) . '">' . esc_html( $source ) . '</a>'
				: '—',
			__( 'Notification', 'fivetwofive-contact-form' ) => esc_html( '' !== $status ? $status : '—' ),
		);

		echo '<table class="form-table" role="presentation"><tbody>';
		foreach ( $rows as $label => $value ) {
			printf(
				'<tr><th scope="row">%1$s</th><td>%2$s</td></tr>',
				esc_html( $label ),
				wp_kses( $value, array( 'a' => array( 'href' => array() ) ) )
			);
		}
		echo '</tbody></table>';

		echo '<h4>' . esc_html__( 'Message', 'fivetwofive-contact-form' ) . '</h4>';
		echo '<div class="ftf-submission-message" style="white-space:pre-wrap;">'
			. wp_kses( nl2br( esc_html( $message ) ), array( 'br' => array() ) )
			. '</div>';
	}

	/**
	 * Mark a submission read when its edit screen is opened. Hooked to
	 * edit_form_top, which passes the post being viewed.
	 *
	 * @since 1.0.0
	 * @param \WP_Post $post The post being edited.
	 */
	public function mark_read( $post ): void {
		if ( $post instanceof \WP_Post && self::POST_TYPE === $post->post_type ) {
			update_post_meta( $post->ID, self::META_READ, '1' );
		}
	}
}
