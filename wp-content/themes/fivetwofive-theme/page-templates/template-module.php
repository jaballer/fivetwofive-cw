<?php
/**
 * Template Name: Modules Template
 * Template Post Type: page, fivetwofive-lp, ftf_work, ftf_project
 *
 * @package FiveTwoFive_Theme
 * @since FiveTwoFive Theme 1.0.0
 */

// Include the header template
get_header();
?>

<main id="primary" class="site-main">

    <?php
    while (have_posts()) :
        the_post();

        // Loop through the 'modules' repeater field
        while (have_rows('modules')) :
            the_row();

            // Get the layout of the current module
            $module_layout = str_replace('module-', '', get_row_layout());

            // Include the module template based on the layout
            get_template_part('template-parts/modules/module', $module_layout);
        endwhile;

    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
// Include the footer template
get_footer();
?>
