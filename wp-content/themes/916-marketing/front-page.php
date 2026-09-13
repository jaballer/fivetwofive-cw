<?php
/**
 * Front page — the hard-coded landing page.
 *
 * Each section is a plain HTML partial in template-parts/sections/. To reorder
 * or remove a section, edit the list below.
 *
 * @package 916_Marketing
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="content">
<?php
foreach ( array( 'hero', 'trusted-by', 'tension', 'services', 'work', 'testimonials', 'process', 'faq', 'contact' ) as $marketing916_section ) {
	get_template_part( 'template-parts/sections/' . $marketing916_section );
}
?>
</main>

<?php
get_footer();
