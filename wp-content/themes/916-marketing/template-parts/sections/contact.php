<?php
/**
 * Section: final CTA + lead form.
 *
 * The form is the ActiveCampaign template part (template-parts/active-campaign-form.php).
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
    <?php get_template_part( 'template-parts/active-campaign-form' ); ?>
  </div>
</section>
