<?php
/**
 * The Contact Form settings page.
 *
 * Included from FiveTwoFive\FiveTwoFive_Contact_Form\Admin\Settings::render_page(),
 * so $this is the Settings instance.
 *
 * @package FiveTwoFive_Contact_Form
 */

defined( 'ABSPATH' ) || exit;

// Re-check capability at the point of render.
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}

// WordPress appends ?settings-updated=true on a successful save. The value is
// not used (only its presence), so the isset() check needs no nonce/sanitize.
if ( isset( $_GET['settings-updated'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	add_settings_error( $this::NOTICES, 'ftf_contact_form_saved', __( 'Settings saved.', 'fivetwofive-contact-form' ), 'updated' );
}

// Renders both the "saved" notice and any validation errors added in sanitize().
settings_errors( $this::NOTICES );
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form action="options.php" method="post">
		<?php
		settings_fields( $this::GROUP );
		do_settings_sections( $this::PAGE_SLUG );
		submit_button();
		?>
	</form>
</div>
