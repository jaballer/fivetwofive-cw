<?php
/**
 * The shortcode functionality of the plugin.
 *
 * @link       https://fivetwofive.com/
 * @since      1.0.0
 *
 * @package    FiveTwoFIve
 * @subpackage FiveTwoFIve/src/Frontend
 */

namespace FiveTwoFive\FiveTwoFive_CTA\Frontend;

defined( 'ABSPATH' ) || exit;

/**
 * The shortcode functionality of the plugin.
 *
 * Defines the plugin name, version, enqueue styles, and shortcode functionality.
 *
 * @package    FiveTwoFive_CTA
 * @subpackage FiveTwoFive_CTA/src/Admin
 */
class Shortcode {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the fivetwofive_cta shortcode.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		// Only register styles
		wp_register_style(
			$this->plugin_name,
			plugins_url('resources/assets/frontend/styles/fivetwofive-cta.css', FTF_CTA_PLUGIN_FILE),
			array(),
			$this->version
		);
	}

	/**
	 * Get the call to action options and create the shortcode structure.
	 *
	 * @param array $atts shortcode arguments meant to override the global settings.
	 * @return string shortcode content.
	 */
	public function shortcode( $atts ) {
		$a = shortcode_atts(
			array(
				'title'         => '',
				'message'       => '',
				'button_text'   => '',
				'button_link'   => '',
				'button_target' => '',
			),
			$atts
		);

		$cta_title         = '';
		$cta_message       = '';
		$cta_button_text   = '';
		$cta_button_link   = '';
		$cta_button_target = '';
		$cta_style         = '';
		$cta_title_style   = '';
		$cta_message_style = '';
		$cta_button_style  = '';

		wp_enqueue_style( $this->plugin_name ); // Only enqueue the shortcode styles when the shortcode is called.

		$options = get_option( $this->plugin_name . '_options' );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		if ( ! empty( $options['cta_title'] ) ) {
			$cta_title = sanitize_text_field( $options['cta_title'] );
		}

		if ( ! empty( $options['cta_message'] ) ) {
			$cta_message = wp_kses_post( $options['cta_message'] );
		}

		if ( ! empty( $options['cta_button_link'] ) ) {
			$cta_button_link = esc_url( $options['cta_button_link'] );
		}

		if ( ! empty( $options['cta_button_text'] ) ) {
			$cta_button_text = sanitize_text_field( $options['cta_button_text'] );
		}

		if ( ! empty( $options['cta_button_target'] ) ) {
			$cta_button_target = sanitize_text_field( $options['cta_button_target'] );
		}

		// Let the shortcode override the global settings.
		if ( ! empty( $a['title'] ) ) {
			$cta_title = sanitize_text_field( $a['title'] );
		}

		if ( ! empty( $a['message'] ) ) {
			$cta_message = wp_kses_post( $a['message'] );
		}

		if ( ! empty( $a['button_link'] ) ) {
			$cta_button_link = esc_url( $a['button_link'] );
		}

		if ( ! empty( $a['button_text'] ) ) {
			$cta_button_text = sanitize_text_field( $a['button_text'] );
		}

		if ( ! empty( $a['button_target'] ) ) {
			$cta_button_target = sanitize_text_field( $a['button_target'] );
		}

		if ( ! empty( $options['cta_background_image'] ) ) {
			$cta_style .= sprintf( 'background-image:url(\'%1$s\');', esc_url( wp_get_attachment_image_url( $options['cta_background_image'], 'full' ) ) );
		}

		if ( ! empty( $options['cta_background_color'] ) ) {
			$cta_style .= sprintf( 'background-color:%1$s;', esc_attr( $options['cta_background_color'] ) );
		}

		if ( ! empty( $options['cta_text_alignment'] ) ) {
			$cta_style .= sprintf( 'text-align:%1$s;', esc_attr( $options['cta_text_alignment'] ) );
		}

		if ( ! empty( $options['cta_title_color'] ) ) {
			$cta_title_style .= sprintf( 'color:%1$s;', esc_attr( $options['cta_title_color'] ) );
		}

		if ( ! empty( $options['cta_message_color'] ) ) {
			$cta_message_style .= sprintf( 'color:%1$s;', esc_attr( $options['cta_message_color'] ) );
		}

		if ( ! empty( $options['cta_button_text_color'] ) ) {
			$cta_button_style .= sprintf( 'color:%1$s;', esc_attr( $options['cta_button_text_color'] ) );
		}

		if ( ! empty( $options['cta_button_background_color'] ) ) {
			$cta_button_style .= sprintf( 'background-color:%1$s;border-color:%1$s;', esc_attr( $options['cta_button_background_color'] ) );
		}

		ob_start();
		include_once dirname( FTF_CTA_PLUGIN_FILE ) . '/resources/views/frontend/fivetwofive-cta-shortcode.php';
		return ob_get_clean();
	}
}
