<?php
/**
 * Template part for displaying the MultiColumn module of the modules template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

wp_enqueue_script( 'fivetwofive-theme-glightbox' );
wp_enqueue_style( 'fivetwofive-theme-glightbox' );

// Contents.
$module_eyebrow            = get_sub_field( 'eyebrow' );;
$module_title              = get_sub_field( 'title' );
$module_title_heading_tag  = get_sub_field( 'title_heading_tag' );
$module_subtitle           = get_sub_field( 'subtitle' );
$module_description        = get_sub_field( 'description' );
$module_button             = get_sub_field( 'button' );
$module_vertical_alignment = get_sub_field( 'vertical_alignment' );
$module_image              = get_sub_field( 'image' );
$module_video              = get_sub_field( 'video' );
$module_video_placeholder  = get_sub_field( 'video_placeholder' );
$module_media_alignment    = get_sub_field( 'alignment' );
$module_media_modal        = get_sub_field( 'modal' );
$module_media_type         = get_sub_field( 'image_or_video' );
$media_alignment           = '';

if ( $module_media_alignment && ( 'right' === $module_media_alignment ) ) {
	$media_alignment = 'order-md-last';
}

if ( ! $module_title_heading_tag ) {
	$module_title_heading_tag = 'h2';
}

// Styles.
$background_toggle = get_sub_field( 'background_toggle' );
$background_image  = get_sub_field( 'background_image' );
$background_color  = get_sub_field( 'background_color' );
$text_color        = get_sub_field( 'text_color' );
$text_alignment    = get_sub_field( 'text_alignment' );

$module_classes          = '';
$styles                  = '';
$text_color_inline_style = '';

if ( get_sub_field( 'module_classes' ) ) {
	$module_classes = implode( ' ', explode( ',', get_sub_field( 'module_classes' ) ) );
}

if ( $background_toggle ) {
	if ( $background_image ) {
		$styles .= sprintf( 'background: url(\'%1$s\') center center no-repeat; background-size:cover;', esc_url_raw( wp_get_attachment_image_url( $background_image, 'full' ) ) );
	}
} else {
	if ( $background_color ) {
		$styles .= sprintf( 'background-color: %1$s;', $background_color );
	}
}

if ( $text_color ) {
	$styles                 .= sprintf( 'color:%1$s;', $text_color );
	$text_color_inline_style = sprintf( 'color:%1$s;', $text_color );
}

// Animations.
$module_id                 = uniqid( 'ftf-module-content-and-media' );
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
<section class="ftf-module ftf-module-content-and-media text-md-<?php echo esc_attr( $text_alignment ); ?> <?php echo esc_attr( $module_classes ); ?>" id="<?php echo esc_attr( $module_id ); ?>" data-animation="<?php echo esc_attr( wp_json_encode( $module_animation_options ) ); ?>" style="<?php echo esc_attr( $styles ); ?>">
	<div class="container">
		<div class="row align-items-<?php echo esc_attr( $module_vertical_alignment ); ?>">
			<?php if ( $module_image || $module_video ) : ?>
				<div class="col-12 col-md-6 ftf-module-content-and-media__media <?php echo esc_attr( $media_alignment ); ?>">
					<?php
					if ( $module_media_type ) {
						if ( $module_image ) {
							$image = wp_get_attachment_image( $module_image, 'full', false, array( 'class' => 'ftf-module-content-and-media__media-image mx-auto d-block' ) );

							if ( $module_media_modal ) {
								$image = sprintf( '<a href="%2$s" class="glightbox ftf-module-content-and-media__media-image-link" data-gallery="%3$s">%1$s</a>', $image, esc_url( wp_get_attachment_image_url( $module_image, 'full' ) ), esc_attr( $module_id ) );
							}

							echo wp_kses_post( $image );
						}
					} else {
						if ( $module_video ) {
							$video = fivetwofive_get_acf_oembed_iframe( $module_video );

							if ( $module_media_modal && $module_video_placeholder ) {
								$video_placeholder = wp_get_attachment_image( $module_video_placeholder, 'full', false, array( 'class' => 'ftf-module-content-and-media__media-video-placeholder' ) );
								$video_url         = get_sub_field( 'video', false, false );
								$video             = sprintf( '<a href="%2$s" class="glightbox ftf-module-content-and-media__media-video-link" data-type="video" data-gallery="%3$s">%1$s</a>', $video_placeholder, esc_url( $video_url ), esc_attr( $module_id ) );
							}

							echo wp_kses( $video, fivetwofive_kses_extended_ruleset() );
						}
					}
					?>
				</div>
			<?php endif; ?>
			<?php if ( $module_eyebrow || $module_title || $module_subtitle || $module_description || $module_button ) : ?>
				<div class="col-12 col-md-6 ftf-module-content-and-media__content">

                    <?php if ( $module_eyebrow ) : ?>
                        <p class="ftf-module__eyebrow small" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo esc_html( $module_eyebrow ); ?></p>
                    <?php endif; ?>

					<?php
					if ( $module_title ) :
						echo sprintf( '<%1$s class="ftf-module__title" style="%2$s">%3$s</%1$s>', esc_attr( $module_title_heading_tag ), esc_attr( $text_color_inline_style ), esc_html( $module_title ) );
					endif;
					?>

					<?php if ( $module_subtitle ) : ?>
						<p class="ftf-module__subtitle h5" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo esc_html( $module_subtitle ); ?></p>
					<?php endif; ?>

					<?php if ( $module_description ) : ?>
						<div class="ftf-module__content" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo wp_kses( $module_description, fivetwofive_kses_extended_ruleset() ); ?></div>
					<?php endif; ?>

					<?php
					if ( $module_button ) :
						$link_url    = $module_button['url'];
						$link_title  = $module_button['title'];
						$link_target = $module_button['target'] ? $module_button['target'] : '_self';
						?>
						<div class="ftf-module__cta-wrap mt-3 mt-md-4">
							<a class="button module__button" role="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
