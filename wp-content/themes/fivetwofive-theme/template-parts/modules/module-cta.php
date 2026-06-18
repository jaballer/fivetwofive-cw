<?php
/**
 * Template part for displaying the CTA module of the modules template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

$show_logo         = get_sub_field( 'show_logo' );
$background_toggle = get_sub_field( 'background_toggle' );
$background_color  = get_sub_field( 'background_color' );
$background_image  = get_sub_field( 'background_image' );
$module_title      = get_sub_field( 'title' );
$module_subtitle   = get_sub_field( 'subtitle' );
$module_content    = get_sub_field( 'content' );
$module_button     = get_sub_field( 'button' );
$module_text_color = '';
$module_classes    = '';
$background        = ''; // Initialize background variable to prevent undefined variable warning

if ( get_sub_field( 'module_classes' ) ) {
	$module_classes = implode( ' ', explode( ',', get_sub_field( 'module_classes' ) ) );
}

// Append responsive Top/Bottom spacing classes from the Appearance tab (issue #62).
$module_classes = trim( $module_classes . ' ' . fivetwofive_theme_get_module_spacing_classes() );

if ( $background_toggle ) {
	if ( $background_image ) {
		$background = "url('" . wp_get_attachment_image_url( $background_image, 'large' ) . "') center center no-repeat";
	}
} else {
	if ( $background_color ) {
		$background = $background_color;
	}
}

if ( get_sub_field( 'text_color' ) ) {
	$module_text_color = 'color:' . get_sub_field( 'text_color' );
}

// Animations.
$module_id                 = uniqid( 'ftf-module-cta' );
$module_animation_desktop  = get_sub_field( 'animation_desktop' );
$module_animation_mobile   = get_sub_field( 'animation_mobile' );
$module_animation_reset    = get_sub_field( 'animation_reset' );
$module_animation_delay    = get_sub_field( 'animation_delay' );
$module_animation_distance = get_sub_field( 'animation_distance' );
$module_animation_duration = get_sub_field( 'animation_duration' );
$module_animation_opacity  = get_sub_field( 'animation_opacity' );
$module_animation_origin   = get_sub_field( 'animation_origin' );
$module_animation_scale    = get_sub_field( 'animation_scale' );
$module_id_field           = get_sub_field( 'module_id' );

if ( $module_id_field ) {
	$module_id = $module_id_field;
}


$module_animation_options = array(
	'reset'   => $module_animation_reset,
	'origin'  => $module_animation_origin,
	'desktop' => $module_animation_desktop,
	'mobile'  => $module_animation_mobile,
);

if ( $module_animation_delay ) {
	$module_animation_options['delay'] = $module_animation_delay;
}

if ( $module_animation_distance ) {
	$module_animation_options['distance'] = $module_animation_distance . 'px';
}

if ( $module_animation_duration ) {
	$module_animation_options['duration'] = (int) $module_animation_duration;
}

if ( $module_animation_opacity ) {
	$module_animation_options['opacity'] = (int) $module_animation_opacity;
}

if ( $module_animation_scale ) {
	$module_animation_options['scale'] = (int) $module_animation_scale;
}

if ( $module_animation_desktop || $module_animation_mobile ) {
	$module_classes .= ' ftf-module-hidden';
}

?>
<section  id="<?php echo esc_attr( $module_id ); ?>" data-animation="<?php echo esc_attr( wp_json_encode( $module_animation_options ) ); ?>" class="ftf-module ftf-module-cta <?php echo esc_attr( $module_classes ); ?>" style="background:<?php echo esc_attr( $background ); ?>;background-size:cover;<?php echo esc_attr( $module_text_color ); ?>">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-10 offset-md-1">
				<?php if ( $show_logo ) : ?>
					<div class="ftf-module-cta__logo mb-3 mb-md-4">
						<?php the_custom_logo(); ?>
					</div>
				<?php endif; ?>

				<?php if ( $module_title ) : ?>
					<h2 class="ftf-module__title my-0" style="<?php echo esc_attr( $module_text_color ); ?>"><?php echo esc_html( $module_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $module_subtitle ) : ?>
					<h3 class="ftf-module__subtitle mt-3 mt-md-4 mb-0" style="<?php echo esc_attr( $module_text_color ); ?>"><?php echo esc_html( $module_subtitle ); ?></h3>
				<?php endif; ?>

				<?php if ( $module_content ) : ?>
					<div class="ftf-module__content mt-3 mt-md-4"><?php echo wp_kses( $module_content, fivetwofive_kses_extended_ruleset() ); ?></div>
				<?php endif; ?>

				<?php
				if ( $module_button ) :
					$link_url    = $module_button['url'];
					$link_title  = $module_button['title'];
					$link_target = $module_button['target'] ? $module_button['target'] : '_self';
					?>
					<a class="button module__button mt-3 mt-md-4" role="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
