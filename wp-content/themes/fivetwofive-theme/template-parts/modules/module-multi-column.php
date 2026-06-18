<?php
/**
 * Template part for displaying the MultiColumn module of the modules template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

// Contents.
$module_title             = get_sub_field( 'title' );
$module_title_heading_tag = get_sub_field( 'title_heading_tag' );
$module_subtitle          = get_sub_field( 'subtitle' );
$module_description       = get_sub_field( 'description' );
$module_button            = get_sub_field( 'button' );
$column_count             = count( ( is_countable( get_sub_field( 'columns' ) ) ? get_sub_field( 'columns' ) : [] ) );
$default_column_width     = '';

switch ( $column_count ) {
	case 2:
		$default_column_width = 'col-md-6';
		break;
	case 3:
		$default_column_width = 'col-md-4';
		break;
	case 4:
		$default_column_width = 'col-md-3';
		break;
	default:
		$default_column_width = 'col-md-2';
		break;
}

if ( ! $module_title_heading_tag ) {
	$module_title_heading_tag = 'h2';
}

// Styles.
$background_toggle       = get_sub_field( 'background_toggle' );
$background_image        = get_sub_field( 'background_image' );
$background_color        = get_sub_field( 'background_color' );
$text_color              = get_sub_field( 'text_color' );
$text_alignment          = get_sub_field( 'text_alignment' );
$button_text_color       = get_sub_field( 'button_text_color' );
$button_background_color = get_sub_field( 'button_background_color' );
$button_border_color     = get_sub_field( 'button_border_color' );
$column_text_alignment   = get_sub_field( 'column_text_alignment' );
$module_classes          = '';

if ( get_sub_field( 'module_classes' ) ) {
	$module_classes = implode( ' ', explode( ',', get_sub_field( 'module_classes' ) ) );
}

$styles                  = '';
$text_color_inline_style = '';
$button_styles           = '';

if ( $background_toggle ) {
	if ( $background_image ) {
		$styles .= sprintf( 'background: url(\'%1$s\') center center no-repeat; background-size:cover;',
			esc_url_raw( wp_get_attachment_image_url( $background_image, 'full' ) ) );
	}
} else {
	if ( $background_color ) {
		$styles .= sprintf( 'background-color: %1$s;', $background_color );
	}
}

if ( $text_color ) {
	$styles                  .= sprintf( 'color:%1$s;', $text_color );
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
$module_id                 = uniqid( 'ftf-module-multi-column' );
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
<section id="<?php echo esc_attr( $module_id ); ?>"
		 class="ftf-module ftf-module-multi-column text-md-<?php echo esc_attr( $text_alignment ); ?> <?php echo esc_attr( $module_classes ); ?>"
		 style="<?php echo esc_attr( $styles ); ?>"
		 data-animation="<?php echo esc_attr( wp_json_encode( $module_animation_options ) ); ?>">
	<div class="container">
		<?php if ( $module_title || $module_subtitle || $module_description ) : ?>
			<header class="ftf-module__header">
				<?php
					if ( $module_title ) :
						echo sprintf( '<%1$s class="ftf-module__title" style="%2$s">%3$s</%1$s>', esc_attr( $module_title_heading_tag ), esc_attr( $text_color_inline_style ), esc_html( $module_title ) );
					endif;
				?>

				<?php if ( $module_subtitle ) : ?>
					<p class="ftf-module__subtitle h3"
					   style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo esc_html( $module_subtitle ); ?></p>
				<?php endif; ?>

				<?php if ( $module_description ) : ?>
					<div class="ftf-module__description"
						 style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo wp_kses( $module_description,
							fivetwofive_kses_extended_ruleset() ); ?></div>
				<?php endif; ?>

				<?php
				if ( $module_button ) :
					$link_url = $module_button['url'];
					$link_title = $module_button['title'];
					$link_target = $module_button['target'] ? $module_button['target'] : '_self';
					?>
					<div class="ftf-module__cta-wrap my-3 my-md-4 text-center">
						<a class="button module__button"
						   style="<?php echo esc_attr( $button_styles ? $button_styles : '' ); ?>" role="button"
						   href="<?php echo esc_url( $link_url ); ?>"
						   target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
					</div>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<div class="row text-md-<?php echo esc_attr( $column_text_alignment ); ?>">
			<?php
			while ( have_rows( 'columns' ) ) :
				the_row();
				$column_image             = get_sub_field( 'image' );
				$column_dimension         = get_sub_field( 'image_dimension' );
				$column_title             = get_sub_field( 'title' );
				$column_title_heading_tag = get_sub_field( 'title_heading_tag' );
				$column_text              = get_sub_field( 'text' );
				$column_button            = get_sub_field( 'button' );
				$column_width             = get_sub_field( 'width' );
				$column_classes           = get_sub_field( 'column_classes' );
				$image_dimension          = 'full';
				$column_width_classes     = array( 'column', 'col-12' );

				if ( ! $column_title_heading_tag ) {
					$column_title_heading_tag = 'h3';
				}

				if ( $column_dimension ) {
					// check if the dimension set it width and height.
					if ( count( explode( ',', $column_dimension ) ) > 1 ) {
						$image_dimension = explode( ',', $column_dimension );
					} else {
						$image_dimension = $column_dimension;
					}
				}

				if ( $column_width ) {
					$column_width_classes[] = $column_width;
				} else {
					$column_width_classes[] = $default_column_width;
				}

				if ( ! $column_classes ) {
					$column_module_classes = '';
				} else {
					$column_module_classes = $column_classes;
				}

				?>
				<div class="<?php echo esc_attr( implode( ' ',
					$column_width_classes ) ); ?> <?php echo esc_attr( $column_module_classes ); ?>">
					<div class="column__content-wrap">
						<?php
						if ( $column_image ) :
							echo wp_get_attachment_image( $column_image, $image_dimension, false,
								array( 'class' => 'column-image mb-3 mb-md-4' ) );
						endif;
						?>

						<?php
						if ( $column_title ) :
							echo sprintf( '<%1$s class="column-title mb-3 mb-md-4" style="%2$s">%3$s</%1$s>', esc_attr( $column_title_heading_tag ), esc_attr( $text_color_inline_style ), esc_html( $column_title ) );
						endif;
						?>

						<?php if ( $column_text ) : ?>
							<div class="column-text mb-3 mb-md-4"
								 style="<?php echo esc_attr( $text_color_inline_style ); ?>"><?php echo do_shortcode( wp_kses( $column_text, fivetwofive_kses_extended_ruleset() ) ); ?></div>
						<?php endif; ?>

						<?php
						if ( $column_button ) :
							$link_url = $column_button['url'];
							$link_title = $column_button['title'];
							$link_target = $column_button['target'] ? $column_button['target'] : '_self';
							?>
							<a class="button column-button" role="button" href="<?php echo esc_url( $link_url ); ?>"
							   style="<?php echo esc_attr( $button_styles ? $button_styles : '' ); ?>"
							   target="<?php echo esc_attr( $link_target ); ?>"><?php echo esc_html( $link_title ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			<?php endwhile; ?>
		</div>


	</div>
</section>
