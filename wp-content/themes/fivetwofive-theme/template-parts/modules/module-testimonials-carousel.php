<?php
/**
 * Template part for displaying the Testimonials Carousel module of the modules template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

wp_enqueue_style( 'fivetwofive-theme-swiper' );
wp_enqueue_script( 'fivetwofive-theme-module-testimonials-carousel' );

// Contents.
$module_title       = get_sub_field( 'title' );
$module_subtitle    = get_sub_field( 'subtitle' );
$module_description = get_sub_field( 'description' );

// Styles.
$background_toggle               = get_sub_field( 'background_toggle' );
$background_image                = get_sub_field( 'background_image' );
$background_color                = get_sub_field( 'background_color' );
$text_color                      = get_sub_field( 'text_color' );
$pagination_and_navigation_color = get_sub_field( 'pagination_and_navigation_color' );
$module_classes                  = '';

if ( get_sub_field( 'module_classes' ) ) {
	$module_classes = implode( ' ', explode( ',', get_sub_field( 'module_classes' ) ) );
}

// Append responsive Top/Bottom spacing classes from the Appearance tab (issue #62).
$module_classes = trim( $module_classes . ' ' . fivetwofive_theme_get_module_spacing_classes() );

$styles                  = '';
$text_color_inline_style = '';

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

if ( $pagination_and_navigation_color ) {
	$styles .= sprintf( '--swiper-pagination-color:%1$s;--swiper-navigation-color:%1$s;', $pagination_and_navigation_color );
}

// Animations.
$module_id                 = uniqid( 'ftf-module-testimonials-carousel' );
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
<section class="ftf-module ftf-module-testimonials-carousel <?php echo esc_attr( $module_classes ); ?>" style="<?php echo esc_attr( $styles ); ?>" id="<?php echo esc_attr( $module_id ); ?>" data-animation="<?php echo esc_attr( wp_json_encode( $module_animation_options ) ); ?>">
	<div class="container">
		<?php if ( $module_title || $module_subtitle ) : ?>
			<header class="ftf-module__header text-md-center">
				<?php if ( $module_title ) : ?>
					<h2 class="ftf-module__title" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo esc_html( $module_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $module_subtitle ) : ?>
					<p class="ftf-module__subtitle h3" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo esc_html( $module_subtitle ); ?></p>
				<?php endif; ?>

				<?php if ( $module_description ) : ?>
					<div class="ftf-module__description" style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo wp_kses_post( $module_description ); ?></div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<div class="swiper-container">
			<div class="swiper-wrapper">
			<?php
			while ( have_rows( 'testimonials' ) ) :
				the_row();
				$author             = get_sub_field( 'author' );
				$author_job         = get_sub_field( 'author_job' );
				$author_avatar      = get_sub_field( 'author_avatar' );
				$message            = get_sub_field( 'message' );
				$testimonial_author = '';

				if ( $author ) {
					$testimonial_author .= $author;
				}

				if ( $author_job ) {
					$testimonial_author .= ', ' . $author_job;
				}
				?>
				<div class="swiper-slide">
					<div class="swiper-slide-content px-5 pt-3 pb-4 py-md-3 px-md-6">
						<div class="row align-items-center">
							<?php if ( $author_avatar ) : ?>
								<div class="col-12 col-md-2 mb-4 mb-md-0">
									<?php echo wp_get_attachment_image( $author_avatar, 'thumbnail', false, array( 'class' => 'testimonial__avatar mx-auto' ) ); ?>
								</div>
							<?php endif; ?>

							<div class="col-12 col-md-10">
								<?php if ( $message ) : ?>
									<div class="testimonial__message">
                                        <blockquote>
                                            <?php echo wp_kses( $message, fivetwofive_kses_extended_ruleset() ); ?>
                                        </blockquote>
                                    </div>
								<?php endif; ?>

								<?php if ( $author ) : ?>
                                    <cite><h3 class="testimonial__author"><?php echo esc_html( $testimonial_author ); ?></h3></cite>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
			</div><!-- .swiper-wrapper -->

			<!-- If we need pagination -->
			<div class="swiper-pagination"></div>

			<!-- If we need navigation buttons -->
			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>
		</div>
	</div>
</section>
