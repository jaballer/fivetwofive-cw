<?php
/**
 * Site footer. Closes the .page container opened in header.php.
 *
 * @package 916_Marketing
 */

defined( 'ABSPATH' ) || exit;
?>

<!-- ============ FOOTER ============ -->
<footer class="band site-footer">
  <div>
    <?php marketing916_logo( true ); ?>
    <p class="footer-legal t-body-small">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> 916 Marketing · Greater Sacramento, CA<br>38.58°N · 121.49°W · THE 916</p>
  </div>
  <nav class="footer-links" aria-label="<?php esc_attr_e( 'Footer', '916-marketing' ); ?>">
    <a href="<?php echo marketing916_section_url( 'services' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in helper. ?>">Services</a><a href="<?php echo marketing916_section_url( 'work' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">Work</a><a href="<?php echo marketing916_section_url( 'process' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">Process</a><a href="<?php echo marketing916_section_url( 'faq' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">FAQ</a><a href="<?php echo marketing916_section_url( 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">Contact</a>
  </nav>
  <div class="footer-links">
    <a href="mailto:<?php echo esc_attr( MARKETING916_EMAIL ); ?>"><?php echo esc_html( MARKETING916_EMAIL ); ?></a>
    <a href="tel:<?php echo esc_attr( MARKETING916_PHONE_TEL ); ?>"><?php echo esc_html( MARKETING916_PHONE ); ?></a>
  </div>
</footer>

<!-- ============ MOBILE STICKY CTA ============ -->
<div class="mcta"><a class="btn btn--primary" href="<?php echo marketing916_section_url( 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">Get your free audit</a></div>

</div>

<?php wp_footer(); ?>
</body>
</html>
