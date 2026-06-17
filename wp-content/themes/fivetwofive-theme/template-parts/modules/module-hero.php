<?php
/**
 * Template part for displaying the hero module in the modules template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

$module_title            = get_sub_field( 'title' );
$module_subtitle         = get_sub_field( 'subtitle' );
$module_content          = get_sub_field( 'content' );
$video                   = get_sub_field( 'video' );
$video_thumbnail         = get_sub_field( 'video_thumbnail' );
$video_caption           = get_sub_field( 'video_caption' );
$module_button           = get_sub_field( 'button' );
$background_toggle       = get_sub_field( 'background_toggle' );
$background_image        = get_sub_field( 'background_image' );
$background_color        = get_sub_field( 'background_color' );
$text_color              = get_sub_field( 'text_color' );
$button_text_color       = get_sub_field( 'button_text_color' );
$button_background_color = get_sub_field( 'button_background_color' );
$button_border_color     = get_sub_field( 'button_border_color' );
$module_classes          = '';
$hero_content_class      = '';

if ( get_sub_field( 'module_classes' ) ) {
	$module_classes = implode( ' ', explode( ',', get_sub_field( 'module_classes' ) ) );
}

if ( $video ) :
	wp_enqueue_script( 'fivetwofive-theme-glightbox' );
	wp_enqueue_style( 'fivetwofive-theme-glightbox' );
	preg_match( '/src="([^"]+)"/', $video, $match );
	$video              = $match[1];
	$video              = remove_query_arg( 'feature', $video );
	$params             = array(
		'autoplay' => 1,
		'rel'      => 0,
	);
	$video              = add_query_arg( $params, $video );
	$hero_content_class = 'col-md-7 order-md-1';
endif;

$styles                  = '';
$text_color_inline_style = '';
$button_styles           = '';

if ( $background_image && $background_toggle ) {
	$styles .= sprintf( 'background:url(\'%1$s\') center center no-repeat; background-size:cover;', esc_url_raw( $background_image['sizes']['large'] ) );
}

if ( $background_color && ! $background_toggle ) {
	$styles .= sprintf( 'background-color: %1$s;', $background_color );
}

if ( $text_color ) {
	$styles                 .= sprintf( 'color:%1$s;', $text_color );
	$text_color_inline_style = sprintf( 'color:%1$s;', $text_color );
}

if ( $button_text_color ) {
	$button_styles .= sprintf( 'color:%1$s;', $button_text_color );
}

if ( $button_background_color ) {
	$button_styles .= sprintf( 'background-color:%1$s;', $button_background_color );
}

if ( $button_border_color ) {
	$button_styles .= sprintf( 'border-color:%1$s;', $button_border_color );
}

// Animations.
$module_id                 = uniqid( 'ftf-module-hero' );
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

<section id="<?php echo esc_attr( $module_id ); ?>" data-animation="<?php echo esc_attr( wp_json_encode( $module_animation_options ) ); ?>" class="ftf-module ftf-module-hero <?php echo ( $video ) ? 'with-video' : 'without-video'; ?> <?php echo esc_attr( $module_classes ); ?>" style="<?php echo esc_attr( $styles ); ?>">
	<div class="container">
		<div class="row align-items-center">

			<?php if ( $video ) : ?>
				<div class="col-12 col-md-5 mb-2 mb-md-0 order-md-2">
					<?php if ( $video_thumbnail ) : ?>
						<figure class="ftf-module-hero__video">
							<div class="ftf-module-hero__video-image-wrap">
								<?php echo wp_get_attachment_image( $video_thumbnail, 'full', false, array( 'class' => 'ftf-module-hero__video-image' ) ); ?>
								<a class="ftf-module-hero__video-play-button glightbox" data-type="video" data-gallery="<?php echo esc_attr( $module_id ); ?>" href="<?php echo esc_url_raw( $video ); ?>" aria-label="<?php esc_attr_e( 'Play video', 'fivetwofive-theme' ); ?>" style="<?php echo esc_attr( $text_color_inline_style ); ?>">
									<?php echo fivetwofive_theme_get_icon_svg( 'ui', 'play', 100 ); ?>
								</a>
							</div>
							<?php if ( $video_caption ) : ?>
								<figcaption class="ftf-module-hero__video-caption" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo esc_html( $video_caption ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="col-12 <?php echo esc_attr( $hero_content_class ); ?>">
				<?php if ( $module_title ) : ?>
					<h1 class="ftf-module__title" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo esc_html( $module_title ); ?></h1>
				<?php endif; ?>

				<?php if ( $module_subtitle ) : ?>
					<p class="ftf-module__subtitle h3" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo esc_html( $module_subtitle ); ?></p>
				<?php endif; ?>

				<?php if ( $module_content ) : ?>
					<div class="ftf-module__content" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo wp_kses( $module_content, fivetwofive_kses_extended_ruleset() ); ?></div>
				<?php endif; ?>

				<?php
				if ( $module_button ) :
					$link_url    = $module_button['url'];
					$link_title  = $module_button['title'];
					$link_target = $module_button['target'] ? $module_button['target'] : '_self';
					?>
					<a class="button ftf-module__button" style="<?php echo esc_attr( $button_styles ? $button_styles : '' ); ?>" role="button" href="<?php echo esc_url( $link_url ); ?>" target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
