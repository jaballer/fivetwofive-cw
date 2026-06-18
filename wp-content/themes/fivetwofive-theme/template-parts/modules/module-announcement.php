<?php
/**
 * Template part for displaying the announcement module of the modules template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

wp_enqueue_script( 'fivetwofive-theme-module-announcement' );

$sticky            = get_sub_field( 'sticky' );
$background_toggle = get_sub_field( 'background_toggle' );
$background_image  = get_sub_field( 'background_image' );
$background_color  = get_sub_field( 'background_color' );
$border_color      = get_sub_field( 'border_color' );
$text_color        = get_sub_field( 'text_color' );
$module_title      = get_sub_field( 'title' );
$module_content    = get_sub_field( 'content' );
$module_button     = get_sub_field( 'button' );
$module_classes    = '';

if ( get_sub_field( 'module_classes' ) ) {
	$module_classes = implode( ' ', explode( ',', get_sub_field( 'module_classes' ) ) );
}

// Append responsive Top/Bottom spacing classes from the Appearance tab (issue #62).
$module_classes = trim( $module_classes . ' ' . fivetwofive_theme_get_module_spacing_classes() );

if ( $background_toggle ) {
	if ( $background_image ) {
		$module_background = 'background-image:url(' . $background_image['sizes']['large'] . ');';
	}
} else {
	if ( $background_color ) {
		$module_background = 'background:' . $background_color . ';';
	}
}

if ( $border_color ) {
	$module_border = 'border-top:2px ' . $border_color . ' solid;border-bottom:2px ' . $border_color . ' solid;';
} else {
	$module_border = 'border: none;';
}

// Animations.
$module_id                 = uniqid( 'ftf-module-annoucement' );
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
<section id="<?php echo esc_attr( $module_id ); ?>" data-animation="<?php echo esc_attr( wp_json_encode( $module_animation_options ) ); ?>" class="ftf-module ftf-module-announcement js-is-sticky-<?php echo esc_attr( $sticky ); ?> <?php echo esc_attr( $module_classes ); ?>" style="<?php echo esc_attr( $module_background . $module_border ); ?>">
	<div class="container">
		<?php if ( ! $module_button && ( $module_title || $module_content ) ) : ?>

			<?php if ( $module_title ) : ?>
				<h2 class="ftf-module__title mb-0" style="color:<?php echo esc_attr( $text_color ); ?>;"><?php echo esc_html( $module_title ); ?></h2>
			<?php endif; ?>

			<?php if ( $module_content ) : ?>
				<div class="ftf-module__content mb-4 mb-md-0" style="color:<?php echo esc_attr( $text_color ); ?>;"><?php echo wp_kses( $module_content, fivetwofive_kses_extended_ruleset() ); ?></div>
			<?php endif; ?>

		<?php endif; ?>

		<?php if ( $module_button ) : ?>
			<div class="row align-items-center">
				<div class="col-12 col-md-8 col-lg-9">
					<?php if ( $module_title ) : ?>
						<h3 class="ftf-module__title mb-0" style="color:<?php echo esc_attr( $text_color ); ?>;"><?php echo esc_html( $module_title ); ?></h3>
					<?php endif; ?>

					<?php if ( $module_content ) : ?>
						<div class="ftf-module__content mb-4 mb-md-0" style="color:<?php echo esc_attr( $text_color ); ?>;"><?php echo wp_kses( $module_content, fivetwofive_kses_extended_ruleset() ); ?></div>
					<?php endif; ?>
				</div>
				<div class="col-12 col-md-4 col-lg-3">
					<?php
					if ( $module_button ) :
						$link_url    = $module_button['url'];
						$link_title  = $module_button['title'];
						$link_target = $module_button['target'] ? $module_button['target'] : '_self';
						?>
						<a class="button d-inline-block module__button" role="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

	</div>

	<a href="javascript:void(0);" class="ftf-module-announcement__close"><?php echo fivetwofive_theme_get_icon_svg( 'ui', 'close', 30 ); ?></a>
</section>
