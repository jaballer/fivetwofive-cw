<?php
/**
 * Section: final CTA + lead form.
 *
 * The form has no backend yet — assets/js/main.js confirms receipt in-page.
 * Point it at a real handler before relying on it for leads.
 *
 * @package 916_Marketing
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- ============ FINAL CTA / CONTACT ============ -->
<section class="band band--inverse cta" id="contact">
  <div class="contact">
    <div>
      <p class="band-eyebrow t-label-caps">Start here</p>
      <h2 class="cta-headline t-heading-h2">Get your free website audit.</h2>
      <p class="cta-body t-body-large">Tell us about your business and goals. We'll review your current site and reply within one business day with what we'd change first.</p>
      <ul class="checklist">
        <li><svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>No obligation, no sales script</li>
        <li><svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Written scope and fixed-fee quote</li>
        <li><svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Senior team from kickoff to launch</li>
      </ul>
    </div>
    <form class="lead-form" id="leadForm" method="post">
      <div class="fields">
        <div class="field"><label class="t-label-field" for="f-name">Name</label><input class="input" id="f-name" name="lead_name" type="text" autocomplete="name" placeholder="Jane Doe" required></div>
        <div class="field"><label class="t-label-field" for="f-email">Email</label><input class="input" id="f-email" name="email" type="email" autocomplete="email" placeholder="jane@company.com" required></div>
        <div class="field"><label class="t-label-field" for="f-company">Company</label><input class="input" id="f-company" name="company" type="text" autocomplete="organization" placeholder="Company name"></div>
        <div class="field"><label class="t-label-field" for="f-need">What do you need?</label>
          <div class="select">
            <select class="input" id="f-need" name="need">
              <option>Website design &amp; development</option>
              <option>SEO &amp; AI search</option>
              <option>Digital marketing / consulting</option>
              <option>Not sure yet — send the audit</option>
            </select>
            <svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </div>
        </div>
      </div>
      <div class="field"><label class="t-label-field" for="f-details">Project details</label><textarea class="input" id="f-details" name="details" placeholder="A few sentences about your goals, timeline, and budget range."></textarea></div>
      <div class="form-status" role="status"></div>
      <button class="btn btn--inverse" type="submit">Send my project details</button>
      <p class="form-note t-body-small">Or call <a href="tel:<?php echo esc_attr( MARKETING916_PHONE_TEL ); ?>"><?php echo esc_html( MARKETING916_PHONE ); ?></a> — we pick up.</p>
    </form>
  </div>
</section>
