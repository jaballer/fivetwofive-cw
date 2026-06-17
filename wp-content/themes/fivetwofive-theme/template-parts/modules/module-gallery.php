<?php
/**
 * Template part for displaying the Gallery module of the modules template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

wp_enqueue_script( 'fivetwofive-theme-glightbox' );
wp_enqueue_style( 'fivetwofive-theme-glightbox' );

$module_title          = get_sub_field( 'title' );
$module_subtitle       = get_sub_field( 'subtitle' );
$module_description    = get_sub_field( 'description' );
$module_images         = get_sub_field( 'images' );
$background_toggle     = get_sub_field( 'background_toggle' );
$background_color      = get_sub_field( 'background_color' );
$background_image      = get_sub_field( 'background_image' );
$module_text_color     = get_sub_field( 'text_color' );
$module_text_alignment = get_sub_field( 'text_alignment' );
$module_column_count   = get_sub_field( 'column_count' );
$module_classes        = '';
$module_styles         = '';
$inline_text_color     = '';

$module_id                   = uniqid( 'ftf-module-resources' );
$module_id_field             = get_sub_field( 'module_id' );

if ( $module_id_field ) {
    $module_id = $module_id_field;
}

if ( get_sub_field( 'module_classes' ) ) {
	$module_classes = implode( ' ', explode( ',', get_sub_field( 'module_classes' ) ) );
}

if ( $module_column_count ) {
	$module_column_count = sprintf( 'col-md-%1$s', $module_column_count );
} else {
	$module_column_count = 'col-md-12';
}

if ( $background_toggle ) {
	if ( $background_image ) {
		$module_styles .= sprintf( 'background: url(\'%1$s\') center center no-repeat; background-size:cover;', esc_url( wp_get_attachment_image_url( $background_image, 'full' ) ) );
	}
} else {
	if ( $background_color ) {
		$module_styles .= sprintf( 'background-color:%1$s;', $background_color );
	}
}

if ( $module_text_color ) {
	$module_styles    .= sprintf( 'color:%1$s;', $module_text_color );
	$inline_text_color = sprintf( 'color:%1$s;', $module_text_color );
}

if ( $module_text_alignment ) {
	$module_styles .= sprintf( 'text-align:%1$s;', $module_text_alignment );
}

// Animations.
$module_animation_desktop  = get_sub_field( 'animation_desktop' );
$module_animation_mobile   = get_sub_field( 'animation_mobile' );
$module_animation_reset    = get_sub_field( 'animation_reset' );
$module_animation_delay    = get_sub_field( 'animation_delay' );
$module_animation_distance = get_sub_field( 'animation_distance' );
$module_animation_duration = get_sub_field( 'animation_duration' );
$module_animation_opacity  = get_sub_field( 'animation_opacity' );
$module_animation_origin   = get_sub_field( 'animation_origin' );
$module_animation_scale    = get_sub_field( 'animation_scale' );

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
<section class="ftf-module ftf-module-gallery <?php echo esc_attr( $module_classes ); ?>" style="<?php echo esc_attr( $module_styles ); ?>" id="<?php echo esc_attr( $module_id ); ?>" data-animation="<?php echo esc_attr( wp_json_encode( $module_animation_options ) ); ?>">
	<div class="container">
		<?php if ( $module_title || $module_subtitle || $module_description ) : ?>
			<header class="ftf-module__header">
				<?php if ( $module_title ) : ?>
					<h2 class="ftf-module__title" style="<?php echo esc_attr( $inline_text_color ); ?>"><?php echo esc_html( $module_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $module_subtitle ) : ?>
					<p class="ftf-module__subtitle h3"><?php echo esc_html( $module_subtitle ); ?></p>
				<?php endif; ?>

				<?php if ( $module_description ) : ?>
					<div class="ftf-module__description"><?php echo wp_kses( $module_description, fivetwofive_kses_extended_ruleset() ); ?></div>
				<?php endif; ?>
			</header>
		<?php endif; ?>
		<?php if ( $module_images ) : ?>
			<div class="row ftf-module-gallery__gallery">
				<?php foreach ( $module_images as $image_id ) : ?>
					<div class="col-12 <?php echo esc_attr( $module_column_count ); ?> ftf-module-gallery__column">
						<a class="ftf-module-gallery__item glightbox" href="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'full' ) ); ?>" data-description="<?php echo esc_attr( wp_get_attachment_caption( $image_id ) ); ?>" data-gallery="<?php echo esc_attr( $module_id ); ?>">
							<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'ftf-module-gallery__item-image' ) ); ?>
						</a>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
