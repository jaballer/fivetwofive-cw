<?php
/**
 * Template part for displaying the Works module of the modules template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

wp_enqueue_script( 'fivetwofive-theme-module-works' );

$module_title          = get_sub_field( 'title' );
$module_subtitle       = get_sub_field( 'subtitle' );
$module_description    = get_sub_field( 'description' );
$search_and_filter     = get_sub_field( 'search_and_filter' );
$background_toggle     = get_sub_field( 'background_toggle' );
$background_color      = get_sub_field( 'background_color' );
$background_image      = get_sub_field( 'background_image' );
$module_text_color     = get_sub_field( 'text_color' );
$module_text_alignment = get_sub_field( 'text_alignment' );
$module_classes        = '';
$module_styles         = '';
$inline_text_color     = '';
$module_id             = uniqid( 'ftf-module-works' );
$module_works          = get_sub_field( 'works' );
$module_display        = get_sub_field( 'display' );
$module_id_field       = get_sub_field( 'module_id' );

if ( $module_id_field ) {
	$module_id = $module_id_field;
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

if ( get_sub_field( 'module_classes' ) ) {
	$module_classes = implode( ' ', explode( ',', get_sub_field( 'module_classes' ) ) );
}

// Append responsive Top/Bottom spacing classes from the Appearance tab (issue #62).
$module_classes = trim( $module_classes . ' ' . fivetwofive_theme_get_module_spacing_classes() );

if ( $background_toggle ) {
	if ( $background_image ) {
		$module_styles .= sprintf( 'background: url(\'%1$s\') center center no-repeat; background-size:cover;',
			esc_url( wp_get_attachment_image_url( $background_image, 'full' ) ) );
	}
} else {
	if ( $background_color ) {
		$module_styles .= sprintf( 'background-color:%1$s;', $background_color );
	}
}

if ( $module_text_color ) {
	$module_styles     .= sprintf( 'color:%1$s;', $module_text_color );
	$inline_text_color = sprintf( 'color:%1$s;', $module_text_color );
}

if ( $module_text_alignment ) {
	$module_classes .= sprintf( ' text-md-%1$s', $module_text_alignment );
}

if ( $module_animation_desktop || $module_animation_mobile ) {
	$module_classes .= ' ftf-module-hidden';
}

?>

<section
	id="<?php echo esc_attr( $module_id ); ?>"
	data-animation="<?php echo esc_attr( wp_json_encode( $module_animation_options ) ); ?>"
	class="ftf-module ftf-module-works <?php echo esc_attr( $module_classes ); ?>"
	style="<?php echo esc_attr( $module_styles ); ?>"
>
	<div class="container">
		<?php if ( $module_title || $module_subtitle || $module_description || $search_and_filter ) : ?>
			<header class="ftf-module__header">
				<?php if ( $module_title ) : ?>
					<h2 class="ftf-module__title"
						style="<?php echo esc_attr( $inline_text_color ); ?>"><?php echo esc_html( $module_title ); ?></h2>
				<?php endif; ?>

				<?php if ( $module_subtitle ) : ?>
					<p class="ftf-module__subtitle h3"><?php echo esc_html( $module_subtitle ); ?></p>
				<?php endif; ?>

				<?php if ( $module_description ) : ?>
					<div class="ftf-module__description"><?php echo wp_kses( $module_description,
							fivetwofive_kses_extended_ruleset() ); ?></div>
				<?php endif; ?>

				<?php if ( $search_and_filter ) : ?>
					<form class="ftf-form mb-5">

						<div class="row justify-content-md-center">

							<fieldset class="ftf-fieldset col-md-3">
								<input
									type="search"
									class="ftf-input ftf-input--search"
									name="ftf-search-work"
									placeholder="<?php echo esc_html__( 'Search works', 'fivetwofive-theme' ); ?>"
									aria-label="<?php echo esc_html__( 'Search works', 'fivetwofive-theme' ); ?>"
								>
							</fieldset>

							<fieldset class="ftf-fieldset col-md-3">
								<?php
								wp_dropdown_categories(
									array(
										'show_option_all' => __( 'All Categories', 'fivetwofive-theme' ),
										'orderby'         => 'name',
										'class'           => 'ftf-select',
										'name'            => 'ftf-work-category',
										'value_field'     => 'term_id',
										'taxonomy'        => 'ftf_work_category',
									)
								);
								?>
							</fieldset>

							<fieldset class="ftf-fieldset col-md-auto">
								<input type="submit" class="ftf-button" value="<?php echo esc_html__( 'Search', 'fivetwofive-theme' ); ?>">
							</fieldset>

						</div>

					</form>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php
		if ( $module_works ) :
			$event_counter = 0;

			if ( 'grid' === $module_display ) :
				echo sprintf( '<p class="ftf-module-works__empty-results" style="display: none;">%1$s</p>', __( 'There are no work found using your request, please try again.', 'fivetwofive' ) );
				echo '<div class="ftf-module-works__grid">';
			endif;

			foreach ( $module_works as $module_work ) :

				if ( 'stacked-alternate' === $module_display ) {
					$event_counter ++;

					if ( 0 !== $event_counter % 2 ) {
						get_template_part(
							'template-parts/post-type/post-item',
							null,
							array(
								'id'       => $module_work,
								'taxonomy' => 'ftf_work_category',
							)
						);
					} else {
						get_template_part(
							'template-parts/post-type/post-item',
							null,
							array(
								'id'                 => $module_work,
								'thumbnail-position' => 'right',
								'taxonomy'           => 'ftf_work_category',
							)
						);
					}
				}

				if ( 'stacked' === $module_display ) {
					get_template_part(
						'template-parts/post-type/post-item',
						null,
						array(
							'id'       => $module_work,
							'taxonomy' => 'ftf_work_category',
						)
					);
				}

				if ( 'grid' === $module_display ) :

					get_template_part(
						'template-parts/post-type/post-card',
						null,
						array(
							'id'       => $module_work,
							'taxonomy' => 'ftf_work_category',
						)
					);

				endif;

			endforeach;

			if ( 'grid' === $module_display ) :
				echo '</div>';
			endif;
		endif;
		?>

	</div>
</section>
