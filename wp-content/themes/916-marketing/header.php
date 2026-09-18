<?php
/**
 * Site header. Opens the .page container that footer.php closes.
 *
 * @package 916_Marketing
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MBQZF7P7');</script>
    <!-- End Google Tag Manager -->
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MBQZF7P7"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', '916-marketing' ); ?></a>
<div class="page">

<!-- ============ HEADER ============ -->
<header class="band band--subtle site-nav site-header">
  <?php marketing916_logo(); ?>
  <nav class="site-nav-links" aria-label="<?php esc_attr_e( 'Primary', '916-marketing' ); ?>">
    <a class="t-label-nav" href="<?php echo marketing916_section_url( 'services' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>">Services</a>
    <a class="t-label-nav" href="<?php echo marketing916_section_url( 'work' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">Work</a>
    <a class="t-label-nav" href="<?php echo marketing916_section_url( 'process' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">How it works</a>
    <a class="t-label-nav" href="<?php echo marketing916_section_url( 'faq' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">FAQ</a>
  </nav>
  <div class="site-nav-actions">
    <a class="site-nav-phone t-label-nav" href="tel:<?php echo esc_attr( MARKETING916_PHONE_TEL ); ?>">
      <svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 01-2.2 2 19.8 19.8 0 01-8.6-3 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3-8.6A2 2 0 014.1 2h3a2 2 0 012 1.7c.1.9.3 1.8.7 2.6a2 2 0 01-.5 2.1L8.1 9.9a16 16 0 006 6l1.5-1.2a2 2 0 012.1-.5c.8.3 1.7.6 2.6.7a2 2 0 011.7 2z"/></svg>
      <?php echo esc_html( MARKETING916_PHONE ); ?>
    </a>
    <a class="btn btn--primary" href="<?php echo marketing916_section_url( 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">Get a free audit</a>
  </div>
</header>
