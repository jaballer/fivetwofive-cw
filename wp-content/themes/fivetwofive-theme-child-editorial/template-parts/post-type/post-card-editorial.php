<?php
/**
 * Editorial work card.
 *
 * Bespoke card for the editorial Works grid (#138). Numbering is a CSS counter
 * on the grid (no markup); the "sector · discipline" line is the work's
 * `ftf_work_category` terms joined with a separator (reuse, no new fields);
 * the title is outcome-led (authored). The whole card is a single link for
 * accessibility, with a "Read →" affordance inside it.
 *
 * Expects $args: 'id' (post ID), optional 'taxonomy' (default ftf_work_category).
 *
 * @package FiveTwoFive
 * @subpackage FiveTwoFive_Child_Editorial
 */

if ( ! isset( $args['id'] ) ) {
	return;
}

$work_id   = $args['id'];
$taxonomy  = isset( $args['taxonomy'] ) ? $args['taxonomy'] : 'ftf_work_category';
// Registered by the fivetwofive-work-post-type plugin (600×450, hard crop).
// If that plugin is inactive WordPress falls back to the full-size image.
$image_size = 'fivetwofive-work-thumbnail';
$work_terms = get_the_terms( $work_id, $taxonomy );
$permalink  = get_permalink( $work_id );
?>

<article <?php post_class( array( 'work-card' ), $work_id ); ?>>
	<span class="work-card__number numbered-tag" aria-hidden="true"></span>

	<a class="work-card__link" href="<?php echo esc_url( $permalink ); ?>">
		<?php if ( has_post_thumbnail( $work_id ) ) : ?>
			<div class="work-card__media">
				<?php
				echo get_the_post_thumbnail(
					$work_id,
					$image_size,
					array(
						'class' => 'work-card__image',
						'alt'   => the_title_attribute( array( 'echo' => false, 'post' => $work_id ) ),
					)
				);
				?>
			</div>
		<?php endif; ?>

		<?php if ( $work_terms && ! is_wp_error( $work_terms ) ) : ?>
			<p class="work-card__meta">
				<?php echo esc_html( implode( ' · ', wp_list_pluck( $work_terms, 'name' ) ) ); ?>
			</p>
		<?php endif; ?>

		<h3 class="work-card__title"><?php echo esc_html( get_the_title( $work_id ) ); ?></h3>

		<span class="work-card__read">
			<?php echo esc_html__( 'Read', 'fivetwofive-theme-child-editorial' ); ?>
			<?php echo fivetwofive_theme_get_icon_svg( 'ui', 'arrow_right', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — trusted theme SVG. ?>
		</span>
	</a>
</article>
