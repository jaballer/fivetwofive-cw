<?php
/**
 * The Settings page functionality of the plugin.
 *
 * @since 1.0.0
 *
 * @package FiveTwoFive_CTA
 * @subpackage FiveTwoFive_CTA/src/Admin
 */

namespace FiveTwoFive\FiveTwoFive_CTA\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * The Settings page functionality of the plugin.
 *
 * Defines the call to actions settings fields and page.
 *
 * @package    FiveTwoFive_CTA
 * @subpackage FiveTwoFive_CTA/src/Admin
 */
class Settings {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Default settings.
	 *
	 * @return array default settings.
	 */
	protected function default_settings() {
		return array(
			'cta_title'                   => __( 'Powered by FiveTwoFive.', 'fivetwofive-cta' ),
			'cta_message'                 => '<p class="custom-message">' . __( 'My custom message.', 'fivetwofive-cta' ) . '</p>',
			'cta_button_link'             => 'https://fivetwofive.com',
			'cta_button_text'             => 'Learn More',
			'cta_text_alignment'          => 'left',
			'cta_title_color'             => '',
			'cta_message_color'           => '#333333',
			'cta_background_image'        => '',
			'cta_background_color'        => '#757575',
			'cta_button_background_color' => '#1e73be',
			'cta_button_text_color'       => '#FFFFFF',
		);
	}

	/**
	 * Radio field options.
	 *
	 * @return array Radio field options.
	 */
	protected function options_radio() {
		return array(
			'_self'  => __( 'Open the anchor in the SAME tab: target=self', 'fivetwofive-cta' ),
			'_blank' => __( 'Open the anchor in a NEW tab: target=blank', 'fivetwofive-cta' ),
		);
	}

	/**
	 * Add the Call To Action menu in the WP dashboard.
	 *
	 * @return void
	 */
	public function add_menu() {
		add_menu_page(
			__( 'Call To Action', 'fivetwofive-cta' ),
			__( 'Call To Action', 'fivetwofive-cta' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'settings_page' ),
			'dashicons-star-filled',
			6
		);
	}

	/**
	 * Page content of the Call To Action page.
	 *
	 * @return void
	 */
	public function settings_page() {
		include_once dirname( FTF_CTA_PLUGIN_FILE ) . '/resources/views/admin/settings-page.php';
	}

	/**
	 * Admin scripts for colorpicker fields.
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'toplevel_page_fivetwofive_cta' !== $hook ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( $this->plugin_name . '-admin', plugins_url( 'resources/assets/admin/scripts/fivetwofive-cta-admin.js', FTF_CTA_PLUGIN_FILE ), array( 'jquery', 'jquery-ui-tabs', 'iris' ), $this->version, true );
	}

	/**
	 * Register Plugin settings, section and fields.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			$this->plugin_name,
			$this->plugin_name . '_options',
			array(
				'sanitize_callback' => array($this, 'sanitize_fields'),
				'default' => $this->default_settings()
			)
		);
		
		// Add nonce field
		add_settings_field(
			'settings_nonce',
			'',
			function() {
				wp_nonce_field('fivetwofive_cta_settings', 'fivetwofive_cta_nonce');
			},
			$this->plugin_name
		);

		$this->register_content_settings();
		$this->register_appearance_settings();
	}

	public function register_content_settings() {
		add_settings_section(
			$this->plugin_name . '_content_section',
			__( 'Content', 'fivetwofive-cta' ),
			array( $this, 'settings_content_section' ),
			$this->plugin_name
		);

		add_settings_field(
			'cta_title',
			__( 'Title', 'fivetwofive-cta' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '_content_section',
			array(
				'id'    => 'cta_title',
				'label' => __( 'Custom title attribute for the CTA', 'fivetwofive-cta' ),
			)
		);

		add_settings_field(
			'cta_message',
			__( 'Message', 'fivetwofive-cta' ),
			array( $this, 'field_wysiwyg' ),
			$this->plugin_name,
			$this->plugin_name . '_content_section',
			array(
				'id'    => 'cta_message',
				'label' => __( 'Custom text and/or markup', 'fivetwofive-cta' ),
			)
		);

		add_settings_field(
			'cta_button_text',
			__( 'Button Text', 'fivetwofive-cta' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '_content_section',
			array(
				'id'    => 'cta_button_text',
				'label' => __( 'Custom button text for the CTA', 'fivetwofive-cta' ),
			)
		);

		add_settings_field(
			'cta_button_link',
			__( 'Button URL', 'fivetwofive-cta' ),
			array( $this, 'field_text' ),
			$this->plugin_name,
			$this->plugin_name . '_content_section',
			array(
				'id'    => 'cta_button_link',
				'label' => __( 'Custom URL for the CTA link', 'fivetwofive-cta' ),
			)
		);

		add_settings_field(
			'cta_button_target',
			__( 'Button Target', 'fivetwofive-cta' ),
			array( $this, 'field_radio' ),
			$this->plugin_name,
			$this->plugin_name . '_content_section',
			array(
				'id'    => 'cta_button_target',
				'label' => __( 'Custom target for the CTA', 'fivetwofive-cta' ),
			)
		);
	}

	public function register_appearance_settings() {
		add_settings_section(
			$this->plugin_name . '_appearance_section',
			__( 'Appearance', 'fivetwofive-cta' ),
			array( $this, 'settings_appearance_section' ),
			$this->plugin_name
		);

		add_settings_field(
			'cta_text_alignment',
			__( 'Text alignment', 'fivetwofive-cta' ),
			array( $this, 'field_dropdown' ),
			$this->plugin_name,
			$this->plugin_name . '_appearance_section',
			array(
				'id'      => 'cta_text_alignment',
				'options' => array(
					'Left'   => 'left',
					'Right'  => 'right',
					'Center' => 'center',
				),
			)
		);

		add_settings_field(
			'cta_background_image',
			__( 'Background image', 'fivetwofive-cta' ),
			array( $this, 'field_media_upload' ),
			$this->plugin_name,
			$this->plugin_name . '_appearance_section',
			array(
				'id' => 'cta_background_image',
			)
		);

		add_settings_field(
			'cta_background_color',
			__( 'Background color', 'fivetwofive-cta' ),
			array( $this, 'field_colorpicker' ),
			$this->plugin_name,
			$this->plugin_name . '_appearance_section',
			array(
				'id' => 'cta_background_color',
			)
		);

		add_settings_field(
			'cta_title_color',
			__( 'Title color', 'fivetwofive-cta' ),
			array( $this, 'field_colorpicker' ),
			$this->plugin_name,
			$this->plugin_name . '_appearance_section',
			array(
				'id' => 'cta_title_color',
			)
		);

		add_settings_field(
			'cta_message_color',
			__( 'Message color', 'fivetwofive-cta' ),
			array( $this, 'field_colorpicker' ),
			$this->plugin_name,
			$this->plugin_name . '_appearance_section',
			array(
				'id' => 'cta_message_color',
			)
		);

		add_settings_field(
			'cta_button_background_color',
			__( 'Button background color', 'fivetwofive-cta' ),
			array( $this, 'field_colorpicker' ),
			$this->plugin_name,
			$this->plugin_name . '_appearance_section',
			array(
				'id' => 'cta_button_background_color',
			)
		);

		add_settings_field(
			'cta_button_text_color',
			__( 'Button text color', 'fivetwofive-cta' ),
			array( $this, 'field_colorpicker' ),
			$this->plugin_name,
			$this->plugin_name . '_appearance_section',
			array(
				'id' => 'cta_button_text_color',
			)
		);
	}

	/**
	 * Sanitize the values of the input fields before saving.
	 *
	 * @param array $input Key value pair of the input fields values.
	 * @return array $input Key value pair of the input fields sanitized values.
	 */
	public function sanitize_fields( $input ) {

		if ( isset( $input['cta_title'] ) ) {
			$input['cta_title'] = sanitize_text_field( $input['cta_title'] );
		}

		if ( isset( $input['cta_message'] ) ) {
			$input['cta_message'] = wp_kses_post( $input['cta_message'] );
		}

		if ( isset( $input['cta_button_link'] ) ) {
			$input['cta_button_link'] = esc_url_raw( $input['cta_button_link'] );
		}

		$radio_options = $this->options_radio();
		if ( ! isset( $input['cta_button_target'] ) || ! array_key_exists( $input['cta_button_target'], $radio_options ) ) {
			$input['cta_button_target'] = null;
		} else {
			$input['cta_button_target'] = sanitize_text_field( $input['cta_button_target'] );
		}

		if ( isset( $input['cta_text_alignment'] ) && ! empty( $input['cta_text_alignment'] ) ) {
			$input['cta_text_alignment'] = sanitize_text_field( $input['cta_text_alignment'] );
		}

		if ( isset( $input['cta_message_color'] ) && ! empty( $input['cta_message_color'] ) ) {
			$input['cta_message_color'] = sanitize_hex_color($input['cta_message_color']);
		}

		if ( isset( $input['cta_title_color'] ) && ! empty( $input['cta_title_color'] ) ) {
			$input['cta_title_color'] = sanitize_hex_color($input['cta_title_color']);
		}

		if ( isset( $input['cta_background_color'] ) && ! empty( $input['cta_background_color'] ) ) {
			$input['cta_background_color'] = sanitize_hex_color($input['cta_background_color']);
		}

		if ( isset( $input['cta_background_image'] ) && ! empty( $input['cta_background_image'] ) ) {
			if ( ! is_int( $input['cta_background_image'] ) ) {
				$input['cta_background_image'] = intval( $input['cta_background_image'] );
			}
		}

		if ( isset( $input['cta_button_text_color'] ) && ! empty( $input['cta_button_text_color'] ) ) {
			$input['cta_button_text_color'] = sanitize_hex_color($input['cta_button_text_color']);
		}

		if ( isset( $input['cta_button_background_color'] ) && ! empty( $input['cta_button_background_color'] ) ) {
			$input['cta_button_background_color'] = sanitize_hex_color($input['cta_button_background_color']);
		}

		return $input;
	}

	/**
	 * Plugin Settings section text.
	 *
	 * @return void
	 */
	public function settings_content_section() {
		echo wp_kses_post( '<p>These settings enable you to customize the CTA\'s content. Use the shortcode <code>[fivetwofive_cta]</code> to show the call to action.</p>' );
	}

	/**
	 * Plugin Settings section text.
	 *
	 * @return void
	 */
	public function settings_appearance_section() {
		echo '<p>' . esc_html__( 'These settings enable you to customize the CTA\'s appearance.', 'fivetwofive-cta' ) . '</p>';
	}

	/**
	 * Settings dropdown field.
	 *
	 * Generate the text field needed for the settings api.
	 *
	 * @param array $args Fields arguments.
	 * @return void
	 */
	public function field_dropdown( $args ) {
		$options = get_option( 'fivetwofive_cta_options', $this->default_settings() );

		$id          = $args['id'] ?? '';
		$description = $args['description'] ?? '';
		$value       = isset( $options[ $id ] ) ? sanitize_text_field( $options[ $id ] ) : '';

		echo '<select id="fivetwofive_cta_options_' . esc_attr( $id ) . '" name="fivetwofive_cta_options[' . esc_attr( $id ) . ']">';

		foreach ( $args['options'] as $option_label => $option_value ) {
			echo '<option value="'. esc_attr( $option_value ) .'" '. selected( $value, $option_value ) .' >' . esc_html( $option_label ) . '</option>';
		}

		echo '</select>';

		if ( $description ) {
			echo '<p class="description">' . esc_html( $description ) . '</p>';
		}
	}

	/**
	 * Settings API color picker field.
	 *
	 * Generate the text field that have color picker functionality.
	 *
	 * @param array $args Fields arguments.
	 * @return void
	 */
	public function field_colorpicker( $args ) {
		$options = get_option( 'fivetwofive_cta_options', $this->default_settings() );

		$id    = isset( $args['id'] ) ? $args['id'] : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';
		$value = isset( $options[ $id ] ) ? sanitize_hex_color( $options[ $id ] ) : '';

		echo '<input class="js-ftf-cta-colorpicker" id="fivetwofive_cta_options_' . esc_attr( $id ) . '" name="fivetwofive_cta_options[' . esc_attr( $id ) . ']" type="text" value="' . esc_attr( $value ) . '"><br />';
		echo '<label for="fivetwofive_cta_options_' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
	}

	/**
	 * Settings API media upload field.
	 *
	 * Make use of WP media upload functionality.
	 *
	 * @param array $args Fields arguments.
	 * @return void
	 */
	public function field_media_upload( $args ) {
		$options = get_option( 'fivetwofive_cta_options', $this->default_settings() );

		$id    = isset( $args['id'] ) ? $args['id'] : '';
		$value = isset( $options[ $id ] ) ? sanitize_text_field( $options[ $id ] ) : '';
		ob_start();
		?>
		<div class="js-ftf-media-uploader-section hide-if-no-js">
			<div class="js-ftf-media-uploader-preview" style="max-width: 300px;">
				<?php
				if ( $value ) :
					echo wp_get_attachment_image( $value, array( 300, 200 ), false, array( 'style' => 'width:100%;height:auto;' ) );
				endif;
				?>
			</div>

			<div class="button-group">
				<?php
				echo sprintf(
					'<button class="button js-ftf-media-uploader-set-trigger">%1$s</button>',
						esc_attr__( 'Set image', 'fivetwofive-cta' ),
					);

				echo sprintf(
					'<button class="button js-ftf-media-uploader-delete-trigger">%1$s</button>',
					esc_attr__( 'Remove image', 'fivetwofive-cta' ),
				);
				?>
			</div>

			<input class="js-ftf-media-uploader-field" id="fivetwofive_cta_options_%1$s" name="fivetwofive_cta_options[<?php echo esc_attr( $id ); ?>]" type="hidden" value="<?php echo esc_attr( $value ); ?>">
		</div>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Settings API text field.
	 *
	 * Generate the text field needed for the settings api.
	 *
	 * @param array $args Fields arguments.
	 * @return void
	 */
	public function field_text( $args ) {
		$options = get_option( 'fivetwofive_cta_options', $this->default_settings() );

		$id    = isset( $args['id'] ) ? $args['id'] : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';
		$value = isset( $options[ $id ] ) ? sanitize_text_field( $options[ $id ] ) : '';

		echo '<input id="fivetwofive_cta_options_' . esc_attr( $id ) . '" name="fivetwofive_cta_options[' . esc_attr( $id ) . ']" type="text" size="40" value="' . esc_attr( $value ) . '"><br />';
		echo '<label for="fivetwofive_cta_options_' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
	}

	/**
	 * Settings API wysiwyg field.
	 *
	 * Generate the wysiwyg field needed for the settings api.
	 *
	 * @param array $args Fields arguments.
	 * @return void
	 */
	public function field_wysiwyg( $args ) {
		$options = get_option( 'fivetwofive_cta_options', $this->default_settings() );

		$id    = isset( $args['id'] ) ? $args['id'] : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';
		$value = isset( $options[ $id ] ) ? wp_kses_post( $options[ $id ] ) : '';

		wp_editor( $value, $id, array( 'textarea_name' => 'fivetwofive_cta_options[' . $id . ']' ) );
	}

	/**
	 * Settings API textarea field.
	 *
	 * Generate the textarea field needed for the settings api.
	 *
	 * @param array $args Fields arguments.
	 * @return void
	 */
	public function field_textarea( $args ) {
		$options = get_option( 'fivetwofive_cta_options', $this->default_settings() );

		$id    = isset( $args['id'] ) ? $args['id'] : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';

		$allowed_tags = wp_kses_allowed_html( 'post' );

		$value = isset( $options[ $id ] ) ? wp_kses( stripslashes_deep( $options[ $id ] ), $allowed_tags ) : '';

		echo '<textarea id="fivetwofive_cta_options_' . esc_attr( $id ) . '" name="fivetwofive_cta_options[' . esc_attr( $id ) . ']" rows="5" cols="50">' . wp_kses_post( $value ) . '</textarea><br />';
		echo '<label for="fivetwofive_cta_options_' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label>';
	}

	/**
	 * Settings API radio field.
	 *
	 * Generate the radio field needed for the settings api.
	 *
	 * @param array $args Fields arguments.
	 * @return void
	 */
	public function field_radio( $args ) {

		$options = get_option( 'fivetwofive_cta_options', $this->default_settings() );

		$id    = isset( $args['id'] ) ? $args['id'] : '';
		$label = isset( $args['label'] ) ? $args['label'] : '';

		$selected_option = isset( $options[ $id ] ) ? sanitize_text_field( $options[ $id ] ) : '';

		$radio_options = $this->options_radio();

		foreach ( $radio_options as $value => $label ) {

			$checked = checked( $selected_option === $value, true, false );

			echo '<label><input name="fivetwofive_cta_options[' . esc_attr( $id ) . ']" type="radio" value="' . esc_attr( $value ) . '"' . esc_attr( $checked ) . '> ';
			echo '<span>' . esc_html( $label ) . '</span></label><br />';

		}

	}
}
