<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package FiveTwoFive_Theme
 */

$fivetwofive_theme_mods = fivetwofive_theme_mods();
$hide_site_title        = $fivetwofive_theme_mods['site_identity_hide_blogname'];
$hide_site_description  = $fivetwofive_theme_mods['site_identity_hide_blogdescription'];
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<?php
	if ( ! empty( $fivetwofive_theme_mods['scripts_head_opening'] ) ) {
		echo wp_kses( $fivetwofive_theme_mods['scripts_head_opening'], fivetwofive_kses_extended_ruleset() );
	}
	?>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>

	<?php
	if ( isset( $fivetwofive_theme_mods['scripts_head_closing'] ) && ! empty( $fivetwofive_theme_mods['scripts_head_closing'] ) ) {
		echo wp_kses( $fivetwofive_theme_mods['scripts_head_closing'], fivetwofive_kses_extended_ruleset() );
	}
	?>
</head>

<body <?php body_class(); ?>>

<?php
if ( ! empty( $fivetwofive_theme_mods['scripts_body_opening'] ) ) {
	echo wp_kses( $fivetwofive_theme_mods['scripts_body_opening'], fivetwofive_kses_extended_ruleset() );
}
?>

<?php wp_body_open(); ?>
<div id="page" class="site">
	<ul class="fivetwofive-skip-link">
		<li><a href="#masthead" class="screen-reader-shortcut"> Skip to primary navigation</a></li>
		<li><a href="#primary" class="screen-reader-shortcut"> Skip to main content</a></li>
		<li><a href="#colophon" class="screen-reader-shortcut"> Skip to footer</a></li>
	</ul>

	<?php get_template_part('template-parts/company-info-banner'); ?>

	<header id="masthead" class="site-header">
		<div class="container">
			<div class="row align-items-center">
				<div class="site-branding col-12 col-md-3">
					<div class="site-branding__logo">
						<?php the_custom_logo(); ?>
					</div>

					<?php if ( ! $hide_site_title || ! $hide_site_description ) : ?>
						<div class="site-branding__text">
							<?php
							if ( ! $hide_site_title ) {
								if ( is_front_page() && is_home() ) {
									echo sprintf( '<h1 class="site-title"><a href="%1$s" rel="home">%2$s</a></h1>', esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) );
								} else {
									echo sprintf( '<p class="site-title"><a href="%1$s" rel="home">%2$s</a></p>', esc_url( home_url( '/' ) ), esc_html( get_bloginfo( 'name' ) ) );
								}
							}

							if ( ! $hide_site_description ) {
								$fivetwofive_description = get_bloginfo( 'description', 'display' );
								if ( $fivetwofive_description || is_customize_preview() ) {
									echo sprintf( '<p class="site-description">%1$s</p>', esc_html( $fivetwofive_description ) );
								}
							}
							?>
						</div>
					<?php endif; ?>
				</div><!-- .site-branding -->
				<?php if ( has_nav_menu( 'primary_menu' ) ) : ?>
					<nav id="site-navigation" class="main-navigation col-12 col-md-9">
						<button class="menu-toggle hamburger hamburger--slider" type="button" aria-label="Menu" aria-controls="primary-menu" aria-expanded="false">
							<span class="menu-toggle__text screen-reader-text"><?php esc_html_e( 'Menu', 'fivetwofive' ); ?></span>
							<span class="hamburger-box">
								<span class="hamburger-inner"></span>
							</span>
						</button>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'primary_menu',
								'menu_id'        => 'primary-menu',
							)
						);
						?>
					</nav><!-- #site-navigation -->
				<?php endif; ?>
			</div>
		</div>
	</header><!-- #masthead -->

	<div class="content-sidebar-wrap">
	<?php do_action( 'fivetwofive_before_content' ); ?>