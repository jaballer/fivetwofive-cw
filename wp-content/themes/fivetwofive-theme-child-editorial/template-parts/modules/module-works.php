<?php
/**
 * Editorial child override of the Works module.
 *
 * Renders the parent's section wrapper, header, background and animation
 * handling unchanged, but swaps the grid body for the bespoke editorial card
 * (`template-parts/post-type/post-card-editorial`) and appends a "View all"
 * tile. The editorial design uses the grid display only; the parent's stacked /
 * search-and-filter variants are intentionally not reproduced here. All other
 * displays fall back to the parent's post-item rendering.
 *
 * @package FiveTwoFive
 * @subpackage FiveTwoFive_Child_Editorial
 */

$module_title          = get_sub_field( 'title' );
$module_subtitle       = get_sub_field( 'subtitle' );
$module_description    = get_sub_field( 'description' );
$background_toggle     = get_sub_field( 'background_toggle' );
$background_color      = get_sub_field( 'background_color' );
$background_image      = get_sub_field( 'background_image' );
$module_text_color     = get_sub_field( 'text_color' );
$module_text_alignment = get_sub_field( 'text_alignment' );
$module_works          = get_sub_field( 'works' );
$module_display        = get_sub_field( 'display' );
$module_id_field       = get_sub_field( 'module_id' );
$module_classes        = '';
$module_styles         = '';
$inline_text_color     = '';
$module_id             = $module_id_field ? $module_id_field : uniqid( 'ftf-module-works' );

// Animations (mirrors the parent so the Appearance tab keeps working).
$module_animation_options = array(
	'reset'   => get_sub_field( 'animation_reset' ),
	'origin'  => get_sub_field( 'animation_origin' ),
	'desktop' => get_sub_field( 'animation_desktop' ),
	'mobile'  => get_sub_field( 'animation_mobile' ),
);

if ( get_sub_field( 'animation_delay' ) ) {
	$module_animation_options['delay'] = get_sub_field( 'animation_delay' );
}
if ( get_sub_field( 'animation_distance' ) ) {
	$module_animation_options['distance'] = get_sub_field( 'animation_distance' ) . 'px';
}
if ( get_sub_field( 'animation_duration' ) ) {
	$module_animation_options['duration'] = (int) get_sub_field( 'animation_duration' );
}
if ( get_sub_field( 'animation_opacity' ) ) {
	$module_animation_options['opacity'] = (int) get_sub_field( 'animation_opacity' );
}
if ( get_sub_field( 'animation_scale' ) ) {
	$module_animation_options['scale'] = (int) get_sub_field( 'animation_scale' );
}

if ( get_sub_field( 'module_classes' ) ) {
	$module_classes = implode( ' ', explode( ',', get_sub_field( 'module_classes' ) ) );
}

// Append responsive Top/Bottom spacing classes from the Appearance tab (issue #62).
$module_classes = trim( $module_classes . ' ' . fivetwofive_theme_get_module_spacing_classes() );

if ( $background_toggle ) {
	if ( $background_image ) {
		$module_styles .= sprintf( 'background: url(\'%1$s\') center center no-repeat; background-size:cover;', esc_url( wp_get_attachment_image_url( $background_image, 'full' ) ) );
	}
} elseif ( $background_color ) {
	$module_styles .= sprintf( 'background-color:%1$s;', $background_color );
}

if ( $module_text_color ) {
	$module_styles    .= sprintf( 'color:%1$s;', $module_text_color );
	$inline_text_color = sprintf( 'color:%1$s;', $module_text_color );
}

if ( $module_text_alignment ) {
	$module_classes .= sprintf( ' text-md-%1$s', $module_text_alignment );
}

if ( get_sub_field( 'animation_desktop' ) || get_sub_field( 'animation_mobile' ) ) {
	$module_classes .= ' ftf-module-hidden';
}

/**
 * Destination for the grid's "View all" tile.
 *
 * The `ftf_work` CPT has no archive, so the target is filterable; #139 wires it
 * to the case-studies page. Returning an empty string hides the tile.
 *
 * @param string $url Default view-all URL.
 */
$view_all_url = apply_filters( 'fivetwofive_child_editorial_works_view_all_url', home_url( '/work/' ) );
?>

<section
	id="<?php echo esc_attr( $module_id ); ?>"
	data-animation="<?php echo esc_attr( wp_json_encode( $module_animation_options ) ); ?>"
	class="ftf-module ftf-module-works ftf-module-works--editorial <?php echo esc_attr( $module_classes ); ?>"
	style="<?php echo esc_attr( $module_styles ); ?>"
>
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

		<?php if ( $module_works ) : ?>
			<?php if ( 'grid' === $module_display ) : ?>
				<div class="ftf-module-works__grid">
					<?php
					foreach ( $module_works as $module_work ) :
						get_template_part(
							'template-parts/post-type/post-card-editorial',
							null,
							array(
								'id'       => $module_work,
								'taxonomy' => 'ftf_work_category',
							)
						);
					endforeach;
					?>

					<?php if ( $view_all_url ) : ?>
						<a class="work-card work-card--view-all" href="<?php echo esc_url( $view_all_url ); ?>">
							<span class="work-card__read">
								<?php echo esc_html__( 'View all', 'fivetwofive-theme-child-editorial' ); ?>
								<?php echo fivetwofive_theme_get_icon_svg( 'ui', 'arrow_right', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — trusted theme SVG. ?>
							</span>
						</a>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<?php
				// Non-grid displays fall back to the parent's post-item rendering.
				foreach ( $module_works as $module_work ) :
					get_template_part(
						'template-parts/post-type/post-item',
						null,
						array(
							'id'       => $module_work,
							'taxonomy' => 'ftf_work_category',
						)
					);
				endforeach;
				?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
