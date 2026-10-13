<?php
/**
 * Lead form: ActiveCampaign form 5 ("Contact Us"), styled with the design system.
 *
 * Markup is the site's (.lead-form, .field, .input, .btn); everything
 * ActiveCampaign relies on is kept from its embed export: the form action,
 * hidden inputs, field names, the #_form_5_ and #_form_5_submit ids, and the
 * ._form-content / ._form-thank-you wrappers. assets/js/active-campaign-form.js
 * handles validation and submission. ActiveCampaign's own embed CSS is not
 * loaded; its error classes are styled in assets/css/wordpress.css.
 *
 * If the form changes in ActiveCampaign, re-export the embed and update the
 * hidden inputs, field names, and select options here to match.
 *
 * @package 916_Marketing
 */

defined( 'ABSPATH' ) || exit;

// Enqueued here so the script only loads where the form renders (prints in the footer).
wp_enqueue_script(
	'marketing916-active-campaign-form',
	get_theme_file_uri( 'assets/js/active-campaign-form.js' ),
	array(),
	MARKETING916_VERSION,
	array( 'in_footer' => true )
);
?>
<form method="POST" action="https://916marketing.activehosted.com/proc.php" id="_form_5_" class="_form _form_5 _inline-form" novalidate>
  <input type="hidden" name="u" value="5">
  <input type="hidden" name="f" value="5">
  <input type="hidden" name="s">
  <input type="hidden" name="c" value="0">
  <input type="hidden" name="m" value="0">
  <input type="hidden" name="act" value="sub">
  <input type="hidden" name="v" value="2">
  <input type="hidden" name="or" value="75592c87-e280-4cbe-ac1a-74be9a58d469">
  <div class="_form-content lead-form">
    <div class="fields">
      <div class="field"><label class="t-label-field" for="fullname">Name</label><input class="input" type="text" id="fullname" name="fullname" autocomplete="name" placeholder="Jane Doe" required></div>
      <?php /* type="text", not "email": ActiveCampaign's serializer skips email inputs, so the address would never be sent. */ ?>
      <div class="field"><label class="t-label-field" for="email">Email</label><input class="input" type="text" inputmode="email" id="email" name="email" autocomplete="email" placeholder="jane@company.com" required></div>
    </div>
    <div class="field"><label class="t-label-field" for="field[3]">What do you need?</label>
      <div class="select">
        <select class="input" id="field[3]" name="field[3]" required>
          <option value="">Select one</option>
          <option value="Marketing">Marketing</option>
          <option value="Web Design">Web Design</option>
          <option value="Web Development">Web Development</option>
        </select>
        <svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
      </div>
    </div>
    <div class="field"><label class="t-label-field" for="field[2]">Project details</label><textarea class="input" id="field[2]" name="field[2]" placeholder="A few sentences about your goals, timeline, and budget range." required></textarea></div>
    <button class="_submit btn btn--inverse" id="_form_5_submit" type="submit">Send my project details</button>
    <p class="form-note t-body-small">Or call <a href="tel:<?php echo esc_attr( MARKETING916_PHONE_TEL ); ?>"><?php echo esc_html( MARKETING916_PHONE ); ?></a> — we pick up.</p>
  </div>
  <div class="_form-thank-you alert" data-status="success" role="status" tabindex="-1" style="display:none;"></div>
</form>
