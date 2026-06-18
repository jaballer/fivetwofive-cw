<?php
/**
 * The [fivetwofive_contact_form] markup.
 *
 * Expects $ftf — an array of pre-resolved, escape-ready values prepared by
 * FiveTwoFive\FiveTwoFive_Contact_Form\Frontend\Shortcode::render().
 *
 * @package FiveTwoFive_Contact_Form
 * @var     array $ftf
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="ftf-contact-form-wrap">

	<?php if ( '' !== $ftf['title'] ) : ?>
		<h2 class="ftf-contact-form__title"><?php echo esc_html( $ftf['title'] ); ?></h2>
	<?php endif; ?>

	<?php if ( 'sent' === $ftf['status'] ) : ?>
		<p class="ftf-contact-form__notice ftf-contact-form__notice--success" role="status">
			<?php esc_html_e( 'Thanks — your message has been sent.', 'fivetwofive-contact-form' ); ?>
		</p>
	<?php elseif ( 'error' === $ftf['status'] ) : ?>
		<p class="ftf-contact-form__notice ftf-contact-form__notice--error" role="alert">
			<?php esc_html_e( 'Sorry, your message could not be sent. Please try again.', 'fivetwofive-contact-form' ); ?>
		</p>
	<?php endif; ?>

	<form class="ftf-contact-form" method="post" action="<?php echo esc_url( $ftf['post_url'] ); ?>">

		<input type="hidden" name="action" value="<?php echo esc_attr( $ftf['action'] ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $ftf['f_source'] ); ?>" value="<?php echo esc_url( $ftf['source'] ); ?>" />
		<input type="hidden" name="<?php echo esc_attr( $ftf['f_time'] ); ?>" value="<?php echo esc_attr( $ftf['time_token'] ); ?>" />
		<?php wp_nonce_field( $ftf['nonce_action'], $ftf['nonce_field'] ); ?>

		<?php // Honeypot: hidden from humans (CSS + aria-hidden + tabindex), irresistible to bots. ?>
		<div class="ftf-contact-form__hp" aria-hidden="true">
			<label><?php esc_html_e( 'Leave this field empty', 'fivetwofive-contact-form' ); ?>
				<input type="text" name="<?php echo esc_attr( $ftf['f_honeypot'] ); ?>" tabindex="-1" autocomplete="off" />
			</label>
		</div>

		<p class="ftf-contact-form__field">
			<label for="ftf-name">
				<?php esc_html_e( 'Name', 'fivetwofive-contact-form' ); ?>
				<span class="ftf-contact-form__required" aria-hidden="true">*</span>
			</label>
			<input type="text" id="ftf-name" name="<?php echo esc_attr( $ftf['f_name'] ); ?>" required />
		</p>

		<p class="ftf-contact-form__field">
			<label for="ftf-email">
				<?php esc_html_e( 'Email', 'fivetwofive-contact-form' ); ?>
				<span class="ftf-contact-form__required" aria-hidden="true">*</span>
			</label>
			<input type="email" id="ftf-email" name="<?php echo esc_attr( $ftf['f_email'] ); ?>" required />
		</p>

		<p class="ftf-contact-form__field">
			<label for="ftf-subject"><?php esc_html_e( 'Subject', 'fivetwofive-contact-form' ); ?></label>
			<input type="text" id="ftf-subject" name="<?php echo esc_attr( $ftf['f_subject'] ); ?>" value="<?php echo esc_attr( $ftf['subject'] ); ?>" />
		</p>

		<p class="ftf-contact-form__field">
			<label for="ftf-message">
				<?php esc_html_e( 'Message', 'fivetwofive-contact-form' ); ?>
				<span class="ftf-contact-form__required" aria-hidden="true">*</span>
			</label>
			<textarea id="ftf-message" name="<?php echo esc_attr( $ftf['f_message'] ); ?>" rows="6" required></textarea>
		</p>

		<p class="ftf-contact-form__actions">
			<button type="submit" class="ftf-contact-form__submit button">
				<?php esc_html_e( 'Send Message', 'fivetwofive-contact-form' ); ?>
			</button>
		</p>

	</form>
</div>
