<?php
/**
 * Template part for displaying a post item with image left or right option through args.
 * This template expects a container class as parent.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

if ( ! $args['id'] ) {
	return;
}

$post_item_id                 = $args['id'];
$post_taxonomy                = 'category';
$thumbnail_col_position_class = '';

if ( isset( $args['thumbnail-position'] ) && 'right' === $args['thumbnail-position'] ) {
	$thumbnail_col_position_class = 'order-md-last';
}

if ( array_key_exists( 'taxonomy', $args ) && isset( $args['taxonomy'] ) ) {
	$post_taxonomy = $args['taxonomy'];
}

$post_terms = get_the_terms( $post_item_id, $post_taxonomy );
?>

<article class="ftf-post-item row mb-5 align-items-center">

	<?php if ( has_post_thumbnail( $post_item_id ) ) : ?>
		<div class="col-12 col-md-7 ftf-post-item__thumbnail-col <?php echo esc_attr( $thumbnail_col_position_class ); ?>">
			<a class="ftf-post-item__link" href="<?php echo esc_url( get_permalink( $post_item_id ) ); ?>" title="<?php echo esc_attr( get_the_title( $post_item_id ) ); ?>">
				<img class="ftf-post-item__thumbnail" src="<?php echo esc_url( get_the_post_thumbnail_url( $post_item_id, array( '746', '420' ) ) ); ?>" width="746" height="420" alt="<?php echo esc_attr( get_the_post_thumbnail_caption( $post_item_id ) ); ?>" />
			</a>
		</div>
	<?php endif; ?>

	<div class="col-12 col-md-5 ftf-post-item__text-col">

		<h3 class="ftf-post-item__title mb-4"><a href="<?php echo esc_url_raw( get_permalink() ); ?>"><?php echo wp_kses_post( get_the_title( $post_item_id ) ); ?></a></h3>

		<?php fivetwofive_theme_post_meta( $post_item_id ); ?>

		<?php if ( has_excerpt( $post_item_id ) ) : ?>
			<div class="ftf-post-item__excerpt mb-3"><?php echo wp_kses_post( get_the_excerpt( $post_item_id ) ); ?></div>
		<?php endif; ?>

		<?php do_action( 'fivetwofive_theme_after_card_content', $post_item_id, get_post_type( $post_item_id ), 'item' ); ?>

		<?php if ( $post_terms ) : ?>
			<ul class="ftf-post-item__terms list-inline">
				<?php foreach ( $post_terms as $post_term ) : ?>
					<li class="list-inline-item">
						<a class="badge d-block text-uppercase text-black bg-white border border-primary" href="<?php echo esc_url( get_category_link( $post_term->term_id ) ); ?>"><?php echo esc_html( $post_term->name ); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<a class="button ftf-post-item__button" href="<?php echo esc_url( get_permalink( $post_item_id ) ); ?>"><?php echo esc_html__( 'Learn More', 'fivetwofive-theme' ); ?></a>

	</div>

</article>
