<?php
/**
 * Customize class.
 *
 * @package Fivetwofive
 * @subpackage FivetwofiveTheme/Customize
 * @since 1.0.0
 */

if ( ! class_exists( 'Fivetwofive_Theme_Customize' ) ) {
	/**
	 * Customize class.
	 *
	 * @since 1.0.0
	 */
	class Fivetwofive_Theme_Customize {

		/**
		 * Constructor. Instantiate the object.
		 *
		 * @access public
		 *
		 * @since FiveTwoFive 1.0
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'customize_register' ) );
			add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'theme_mods_css' ), 11 );
		}

		/**
		 * Generate the styles from the customizer settings.
		 *
		 * @return void
		 */
		public function theme_mods_css() {
			// Include the CSS_PHP class.
			include_once get_theme_file_path( 'classes/class-fivetwofive-theme-css-php.php' ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

			$theme_mods = fivetwofive_theme_mods();

			$css = new Fivetwofive_Theme_CSS_PHP();

			$css->set_selector( 'a, a:focus' );
			$css->add_property( 'color', $theme_mods['colors_body_link_color'] );

			$css->set_selector( 'a:hover' );
			$css->add_property( 'color', $theme_mods['colors_body_link_color_hover'] );

			$css->set_selector( 'a:visited' );
			$css->add_property( 'color', $theme_mods['colors_body_link_color_visited'] );

			$css->set_selector( '.site-header' );
			$css->add_property( 'background-color', $theme_mods['colors_header_background_color'] );

			$css->set_selector( '.main-navigation' );
			$css->add_property( 'background-color', $theme_mods['colors_primary_navigation_background_color'] );

			$css->set_selector( '.main-navigation a' );
			$css->add_property( 'color', $theme_mods['colors_primary_navigation_link_color'] );

			$css->set_selector( '.main-navigation .current_page_item > a, .main-navigation .current-menu-item > a, .main-navigation .current_page_ancestor > a, .main-navigation .current-menu-ancestor > a' );
			$css->add_property( 'color', $theme_mods['colors_primary_navigation_active_link_color'] );

			$css->set_selector( '.main-navigation a:focus, .main-navigation a:hover' );
			$css->add_property( 'color', $theme_mods['colors_primary_navigation_active_link_color'] );

			$css->set_selector( 'body' );
			$css->add_property( 'color', $theme_mods['colors_body_text_color'] );
			$css->add_property( 'background-color', $theme_mods['colors_body_background_color'] );
			$css->add_property( 'font-family', $theme_mods['typography_body_font'] . ', ' . $theme_mods['typography_body_font_category'] );
			$css->add_property( 'font-weight', $theme_mods['typography_body_font_weight'] );

			$css->set_selector( 'h1, h2, h3, h4, h5, h6' );
			$css->add_property( 'color', $theme_mods['colors_body_heading_color'] );
			$css->add_property( 'font-family', $theme_mods['typography_heading_font'] . ', ' . $theme_mods['typography_heading_font_category'] );
			$css->add_property( 'font-weight', $theme_mods['typography_heading_font_weight'] );

			$css->set_selector( '.site-footer' );
			$css->add_property( 'background-color', $theme_mods['colors_footer_background_color'] );
			$css->add_property( 'color', $theme_mods['colors_footer_text_color'] );

			$css->set_selector( '.site-footer h2, .site-footer h3, .site-footer h4, .site-footer h5, .site-footer h6' );
			$css->add_property( 'color', $theme_mods['colors_footer_heading_color'] );

			$css->set_selector( '.button, button, input[type="button"], input[type="reset"], input[type="submit"], .page-numbers.current' );
			$css->add_property( 'background-color', $theme_mods['colors_button_background_color'] );
			$css->add_property( 'color', $theme_mods['colors_button_text_color'] );
			$css->add_property( 'border-color', $theme_mods['colors_button_border_color'] );

			// Expose the button palette as CSS custom properties so variant modifiers
			// (e.g. .button--outline, .button--secondary) can derive from each client's
			// Customizer colors instead of hard-coding them.
			$css->add_property( '--ftf-button-bg', $theme_mods['colors_button_background_color'] );
			$css->add_property( '--ftf-button-text', $theme_mods['colors_button_text_color'] );
			$css->add_property( '--ftf-button-border', $theme_mods['colors_button_border_color'] );
			$css->add_property( '--ftf-button-bg-hover', $theme_mods['colors_button_background_color_hover'] );
			$css->add_property( '--ftf-button-text-hover', $theme_mods['colors_button_text_color_hover'] );
			$css->add_property( '--ftf-button-border-hover', $theme_mods['colors_button_border_color_hover'] );

			$css->set_selector( '.button:hover, button:hover, input[type="button"]:hover, input[type="reset"]:hover, input[type="submit"]:hover, .page-numbers:hover' );
			$css->add_property( 'background-color', $theme_mods['colors_button_background_color_hover'] );
			$css->add_property( 'color', $theme_mods['colors_button_text_color_hover'] );
			$css->add_property( 'border-color', $theme_mods['colors_button_border_color_hover'] );

			$css->set_selector( '.button:focus, button:focus, input[type="button"]:focus, input[type="reset"]:focus, input[type="submit"]:focus' );
			$css->add_property( 'background-color', $theme_mods['colors_button_background_color_hover'] );
			$css->add_property( 'color', $theme_mods['colors_button_text_color_hover'] );
			$css->add_property( 'border-color', $theme_mods['colors_button_border_color_hover'] );
			$css->add_property( 'box-shadow', '0 0 0 0.25rem ' . $theme_mods['colors_button_border_color_hover'] . '7d' );

			if ( $css ) {
				wp_add_inline_style( 'fivetwofive-theme-main', $css->css_output() );
			}
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 */
		public function customize_register( $wp_customize ) {
			$config   = fivetwofive_theme_typography_config();
			$defaults = fivetwofive_theme_default_theme_mods();

			// Include the select2 custom control.
			include_once get_theme_file_path( 'classes/class-fivetwofive-theme-customize-select2-control.php' ); // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

			$this->add_site_identity_options( $wp_customize, $defaults );
			$this->add_typography_options( $wp_customize, $defaults, $config );
			$this->add_head_and_body_scripts_options( $wp_customize, $defaults, $config );
			$this->add_color_options( $wp_customize, $defaults );
			$this->add_color_buttons_options( $wp_customize, $defaults );
			$this->add_color_header_options( $wp_customize, $defaults );
			$this->add_color_primary_navigation_options( $wp_customize, $defaults );
			$this->add_color_footer_options( $wp_customize, $defaults );
		}

		/**
		 * Add Site Identity fields in the customizer api.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @param array                $defaults default theme mods.
		 * @return void
		 */
		public function add_site_identity_options( $wp_customize, $defaults ) {

			$wp_customize->get_control( 'blogname' )->priority         = 1;
			$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
			$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
			$wp_customize->get_control( 'blogdescription' )->priority  = 3;

			if ( isset( $wp_customize->selective_refresh ) ) {
				$wp_customize->selective_refresh->add_partial(
					'blogname',
					array(
						'selector'        => '.site-title a',
						'render_callback' => array( $this, 'customize_partial_blogname' ),
					)
				);
				$wp_customize->selective_refresh->add_partial(
					'blogdescription',
					array(
						'selector'        => '.site-description',
						'render_callback' => array( $this, 'customize_partial_blogdescription' ),
					)
				);
			}

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[site_identity_hide_blogname]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['site_identity_hide_blogname'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'fivetwofive_theme_mods[site_identity_hide_blogname]',
				array(
					'label'    => __( 'Hide site title', 'fivetwofive' ),
					'section'  => 'title_tagline',
					'type'     => 'checkbox',
					'priority' => 2,
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[site_identity_hide_blogdescription]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['site_identity_hide_blogdescription'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'postMessage',
				)
			);

			$wp_customize->add_control(
				'fivetwofive_theme_mods[site_identity_hide_blogdescription]',
				array(
					'label'    => __( 'Hide tagline', 'fivetwofive' ),
					'section'  => 'title_tagline',
					'type'     => 'checkbox',
					'priority' => 4,
				)
			);
		}

		/**
		 * Add Typography fields in the customizer api.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @param array                $defaults default theme mods.
		 * @param array                $config Theme Configuration.
		 * @return void
		 */
		public function add_typography_options( $wp_customize, $defaults, $config ) {
			$wp_customize->add_section(
				'fivetwofive_typography_section',
				array(
					'title'          => __( 'Typography', 'fivetwofive' ),
					'description'    => __( 'Customize the site\'s fonts.<br/> You can choose to all fonts provided by <a href="https://fonts.google.com/" rel="no-follow" target="_blank">Google Fonts</a>.', 'fivetwofive' ),
					'panel'          => '', // Not typically needed.
					'priority'       => 50,
					'capability'     => 'edit_theme_options',
					'theme_supports' => '', // Rarely needed.
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[typography_body_font]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['typography_body_font'],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				new Fivetwofive_Theme_Customize_Select2_Control(
					$wp_customize,
					'fivetwofive_theme_mods[typography_body_font]',
					array(
						'priority'    => 10,
						'section'     => 'fivetwofive_typography_section',
						'label'       => __( 'Body Font', 'fivetwofive' ),
						'description' => __( 'The body font to use for all the text elements in the site except headings.', 'fivetwofive' ),
						'choices'     => $config['google_fonts'],
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[typography_body_font_variants]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['typography_body_font_variants'],
					'sanitize_callback' => array( $this, 'sanitize_multiple_select' ),
				)
			);

			$wp_customize->add_control(
				new Fivetwofive_Theme_Customize_Select2_Control(
					$wp_customize,
					'fivetwofive_theme_mods[typography_body_font_variants]',
					array(
						'priority'    => 10,
						'section'     => 'fivetwofive_typography_section',
						'description' => __( 'Font Variants', 'fivetwofive' ),
						'choices'     => $config['font_variants'],
						'input_attrs' => array(
							'multiple' => 'multiple',
						),
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[typography_body_font_category]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['typography_body_font_category'],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				new Fivetwofive_Theme_Customize_Select2_Control(
					$wp_customize,
					'fivetwofive_theme_mods[typography_body_font_category]',
					array(
						'priority'    => 10,
						'section'     => 'fivetwofive_typography_section',
						'description' => __( 'Font Category', 'fivetwofive' ),
						'choices'     => $config['font_categories'],
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[typography_body_font_weight]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['typography_body_font_weight'],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				new Fivetwofive_Theme_Customize_Select2_Control(
					$wp_customize,
					'fivetwofive_theme_mods[typography_body_font_weight]',
					array(
						'priority'    => 10,
						'section'     => 'fivetwofive_typography_section',
						'description' => __( 'Font weight', 'fivetwofive' ),
						'choices'     => $config['font_weights'],
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[typography_heading_font]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['typography_heading_font'],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				new Fivetwofive_Theme_Customize_Select2_Control(
					$wp_customize,
					'fivetwofive_theme_mods[typography_heading_font]',
					array(
						'priority'    => 15,
						'section'     => 'fivetwofive_typography_section',
						'label'       => __( 'Heading Font', 'fivetwofive' ),
						'description' => __( 'The font to use for headings (h1, h2, h3, h4, h5, h6).', 'fivetwofive' ),
						'choices'     => $config['google_fonts'],
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[typography_heading_font_variants]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['typography_heading_font_variants'],
					'sanitize_callback' => array( $this, 'sanitize_multiple_select' ),
				)
			);

			$wp_customize->add_control(
				new Fivetwofive_Theme_Customize_Select2_Control(
					$wp_customize,
					'fivetwofive_theme_mods[typography_heading_font_variants]',
					array(
						'priority'    => 15,
						'section'     => 'fivetwofive_typography_section',
						'description' => __( 'Font Variants', 'fivetwofive' ),
						'choices'     => $config['font_variants'],
						'input_attrs' => array(
							'multiple' => 'multiple',
						),
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[typography_heading_font_category]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['typography_heading_font_category'],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				new Fivetwofive_Theme_Customize_Select2_Control(
					$wp_customize,
					'fivetwofive_theme_mods[typography_heading_font_category]',
					array(
						'priority'    => 15,
						'section'     => 'fivetwofive_typography_section',
						'description' => __( 'Font Category', 'fivetwofive' ),
						'choices'     => $config['font_categories'],
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[typography_heading_font_weight]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['typography_heading_font_weight'],
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				new Fivetwofive_Theme_Customize_Select2_Control(
					$wp_customize,
					'fivetwofive_theme_mods[typography_heading_font_weight]',
					array(
						'priority'    => 15,
						'section'     => 'fivetwofive_typography_section',
						'description' => __( 'Font weight', 'fivetwofive' ),
						'choices'     => $config['font_weights'],
					)
				)
			);
		}

		/**
		 * Add Header and footer scripts fields in the customizer api.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @param array                $defaults default theme mods.
		 * @param array                $config Theme Configuration.
		 * @return void
		 */
		public function add_head_and_body_scripts_options( $wp_customize, $defaults, $config ) {
			$wp_customize->add_section(
				'fivetwofive_head_and_body_scripts_section',
				array(
					'title'          => __( 'Head/Body Scripts', 'fivetwofive' ),
					'panel'          => '', // Not typically needed.
					'capability'     => 'edit_theme_options',
					'theme_supports' => '', // Rarely needed.
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[scripts_head_opening]',
				array(
					'type'       => 'theme_mod',
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_control(
				'fivetwofive_theme_mods[scripts_head_opening]',
				array(
					'priority'    => 10,
					'section'     => 'fivetwofive_head_and_body_scripts_section',
					'label'       => __( 'Head Opening Scripts', 'fivetwofive' ),
					'description' => __( 'This code will output immediately after the opening <code>&lt;head&gt;</code> tag in the document source.', 'fivetwofive' ),
					'type'        => 'textarea',
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[scripts_head_closing]',
				array(
					'type'       => 'theme_mod',
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_control(
				'fivetwofive_theme_mods[scripts_head_closing]',
				array(
					'priority'    => 10,
					'section'     => 'fivetwofive_head_and_body_scripts_section',
					'label'       => __( 'Head Closing Scripts', 'fivetwofive' ),
					'description' => __( 'This code will output immediately before the closing <code>&lt;/head&gt;</code> tag in the document source.', 'fivetwofive' ),
					'type'        => 'textarea',
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[scripts_body_opening]',
				array(
					'type'       => 'theme_mod',
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_control(
				'fivetwofive_theme_mods[scripts_body_opening]',
				array(
					'priority'    => 10,
					'section'     => 'fivetwofive_head_and_body_scripts_section',
					'label'       => __( 'Body Opening Scripts', 'fivetwofive' ),
					'description' => __( 'This code will output immediately after the opening <code>&lt;body&gt;</code> tag in the document source.', 'fivetwofive' ),
					'type'        => 'textarea',
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[scripts_body_closing]',
				array(
					'type'       => 'theme_mod',
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_control(
				'fivetwofive_theme_mods[scripts_body_closing]',
				array(
					'priority'    => 10,
					'section'     => 'fivetwofive_head_and_body_scripts_section',
					'label'       => __( 'Body Closing Scripts', 'fivetwofive' ),
					'description' => __( 'This code will output immediately before the closing <code>&lt;/body&gt;</code> tag in the document source.', 'fivetwofive' ),
					'type'        => 'textarea',
				)
			);
		}

		/**
		 * Add postMessage support for site title and description for the Theme Customizer.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @param array                $defaults default theme mods.
		 * @return void
		 */
		public function add_color_options( $wp_customize, $defaults ) {

			$wp_customize->add_panel(
				'fivetwofive_color_panel',
				array(
					'title'    => __( 'Colors', 'fivetwofive' ),
					'priority' => 40,
				)
			);

			$wp_customize->add_section(
				'fivetwofive_body_color_section',
				array(
					'title'      => __( 'Body', 'fivetwofive' ),
					'panel'      => 'fivetwofive_color_panel',
					'priority'   => 10,
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_body_background_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_body_background_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_body_background_color]',
					array(
						'label'   => __( 'Body Background Color', 'fivetwofive' ),
						'section' => 'fivetwofive_body_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_body_text_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_body_text_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_body_text_color]',
					array(
						'label'   => __( 'Text Color', 'fivetwofive' ),
						'section' => 'fivetwofive_body_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_body_heading_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_body_heading_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_body_heading_color]',
					array(
						'label'   => __( 'Heading Color', 'fivetwofive' ),
						'section' => 'fivetwofive_body_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_body_link_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_body_link_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_body_link_color]',
					array(
						'label'   => __( 'Link Color', 'fivetwofive' ),
						'section' => 'fivetwofive_body_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_body_link_color_hover]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_body_link_color_hover'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_body_link_color_hover]',
					array(
						'label'   => __( 'Link Color Hover', 'fivetwofive' ),
						'section' => 'fivetwofive_body_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_body_link_color_visited]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_body_link_color_visited'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_body_link_color_visited]',
					array(
						'label'   => __( 'Link Color Visited', 'fivetwofive' ),
						'section' => 'fivetwofive_body_color_section',
					)
				)
			);
		}

		/**
		 * Add Buttons color fields in the customizer api.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @param array                $defaults default theme mods.
		 * @return void
		 */
		public function add_color_buttons_options( $wp_customize, $defaults ) {
			$wp_customize->add_section(
				'fivetwofive_button_color_section',
				array(
					'title'      => __( 'Buttons', 'fivetwofive' ),
					'panel'      => 'fivetwofive_color_panel',
					'priority'   => 20,
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_button_text_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_button_text_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_button_text_color]',
					array(
						'label'   => __( 'Button Text Color', 'fivetwofive' ),
						'section' => 'fivetwofive_button_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_button_text_color_hover]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_button_text_color_hover'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_button_text_color_hover]',
					array(
						'label'   => __( 'Button Text Color Hover', 'fivetwofive' ),
						'section' => 'fivetwofive_button_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_button_background_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_button_background_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_button_background_color]',
					array(
						'label'   => __( 'Button Background Color', 'fivetwofive' ),
						'section' => 'fivetwofive_button_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_button_background_color_hover]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_button_background_color_hover'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_button_background_color_hover]',
					array(
						'label'   => __( 'Button Background Color Hover', 'fivetwofive' ),
						'section' => 'fivetwofive_button_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_button_border_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_button_border_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_button_border_color]',
					array(
						'label'   => __( 'Button Border Color', 'fivetwofive' ),
						'section' => 'fivetwofive_button_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_button_border_color_hover]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_button_border_color_hover'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_button_border_color_hover]',
					array(
						'label'   => __( 'Button Border Color Hover', 'fivetwofive' ),
						'section' => 'fivetwofive_button_color_section',
					)
				)
			);

		}

		/**
		 * Add Header color fields in the customizer api.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @param array                $defaults default theme mods.
		 * @return void
		 */
		public function add_color_header_options( $wp_customize, $defaults ) {
			$wp_customize->add_section(
				'fivetwofive_header_color_section',
				array(
					'title'      => __( 'Header', 'fivetwofive' ),
					'panel'      => 'fivetwofive_color_panel',
					'priority'   => 20,
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_header_background_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_header_background_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_header_background_color]',
					array(
						'label'   => __( 'Header Background Color', 'fivetwofive' ),
						'section' => 'fivetwofive_header_color_section',
					)
				)
			);
		}

		/**
		 * Add Primary Navigation color fields in the customizer api.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @param array                $defaults default theme mods.
		 * @return void
		 */
		public function add_color_primary_navigation_options( $wp_customize, $defaults ) {

			$wp_customize->add_section(
				'fivetwofive_primary_navigation_color_section',
				array(
					'title'      => __( 'Primary Navigation', 'fivetwofive' ),
					'panel'      => 'fivetwofive_color_panel',
					'priority'   => 30,
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_primary_navigation_background_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_primary_navigation_background_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_primary_navigation_background_color]',
					array(
						'label'   => __( 'Primary Navigation Background Color', 'fivetwofive' ),
						'section' => 'fivetwofive_primary_navigation_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_primary_navigation_link_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_primary_navigation_link_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_primary_navigation_link_color]',
					array(
						'label'   => __( 'Primary Navigation Link Color', 'fivetwofive' ),
						'section' => 'fivetwofive_primary_navigation_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_primary_navigation_active_link_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_primary_navigation_active_link_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_primary_navigation_active_link_color]',
					array(
						'label'   => __( 'Primary Navigation Active Link Color', 'fivetwofive' ),
						'section' => 'fivetwofive_primary_navigation_color_section',
					)
				)
			);
		}

		/**
		 * Add Footer color fields in the customizer api.
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @param array                $defaults default theme mods.
		 * @return void
		 */
		public function add_color_footer_options( $wp_customize, $defaults ) {

			$wp_customize->add_section(
				'fivetwofive_footer_color_section',
				array(
					'title'      => __( 'Footer', 'fivetwofive' ),
					'panel'      => 'fivetwofive_color_panel',
					'priority'   => 40,
					'capability' => 'edit_theme_options',
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_footer_background_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_footer_background_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_footer_background_color]',
					array(
						'label'   => __( 'Footer Background Color', 'fivetwofive' ),
						'section' => 'fivetwofive_footer_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_footer_heading_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_footer_heading_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_footer_heading_color]',
					array(
						'label'   => __( 'Footer Heading Color', 'fivetwofive' ),
						'section' => 'fivetwofive_footer_color_section',
					)
				)
			);

			$wp_customize->add_setting(
				'fivetwofive_theme_mods[colors_footer_text_color]',
				array(
					'type'              => 'theme_mod',
					'capability'        => 'edit_theme_options',
					'default'           => $defaults['colors_footer_text_color'],
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);

			$wp_customize->add_control(
				new \WP_Customize_Color_Control(
					$wp_customize,
					'fivetwofive_theme_mods[colors_footer_text_color]',
					array(
						'label'   => __( 'Footer Text Color', 'fivetwofive' ),
						'section' => 'fivetwofive_footer_color_section',
					)
				)
			);
		}

		/**
		 * Render the site title for the selective refresh partial.
		 *
		 * @return void
		 */
		public function customize_partial_blogname() {
			bloginfo( 'name' );
		}

		/**
		 * Sanitize Customizer controls that have multiple fields.
		 *
		 * @param array $values User input options.
		 * @return array Sanitized options.
		 */
		public function sanitize_multiple_select( $values ) {
			$multi_values = ! is_array( $values ) ? explode( ',', $values ) : $values;

			return ! empty( $multi_values ) ? array_map( 'sanitize_text_field', $multi_values ) : array();
		}

		/**
		 * Render the site tagline for the selective refresh partial.
		 *
		 * @return void
		 */
		public function customize_partial_blogdescription() {
			bloginfo( 'description' );
		}

		/**
		 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
		 */
		public function customize_preview_js() {
			wp_enqueue_script( 'fivetwofive-theme-customizer', get_template_directory_uri() . '/assets/dist/js/customize.js', array( 'customize-preview' ), FIVETWOFIVE_THEME_VERSION, true );
		}
	}
}
