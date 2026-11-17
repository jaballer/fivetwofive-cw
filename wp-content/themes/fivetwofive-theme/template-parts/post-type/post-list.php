<?php
/**
 * Template part for displaying posts in list view
 *
 * @package FiveTwoFive_Theme
 */

if ( ! $args['id'] ) {
	return;
}

$post_item_id  = $args['id'];
$post_taxonomy = 'category';

if ( array_key_exists( 'taxonomy', $args ) && isset( $args['taxonomy'] ) ) {
	$post_taxonomy = $args['taxonomy'];
}

$post_terms = get_the_terms( $post_item_id, $post_taxonomy );
$post_date = get_the_date('M j, Y', $post_item_id);
$author_id = get_post_field('post_author', $post_item_id);
$author_name = get_the_author_meta('display_name', $author_id);
?>

<article id="post-<?php echo esc_attr( $post_item_id ); ?>" <?php post_class( 'ftf-post-list' ); ?>>
	<?php if ( has_post_thumbnail( $post_item_id ) ) : ?>
		<div class="ftf-post-list__image">
			<?php echo get_the_post_thumbnail( $post_item_id, 'medium_large', array( 'class' => 'img-fluid' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="ftf-post-list__content">
		<div class="post-meta">
			<span class="meta-item">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
					<path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm1-13h-2v6l5.25 3.15.75-1.23-4-2.37V7z"/>
				</svg>
				<?php echo esc_html($post_date); ?>
			</span>
			<span class="meta-item">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16">
					<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 7c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4zm6 5H6v-.99c.2-.72 3.3-2.01 6-2.01s5.8 1.29 6 2v1z"/>
				</svg>
				<?php echo esc_html($author_name); ?>
			</span>
		</div>

		<h2 class="ftf-post-list__title">
			<a href="<?php echo esc_url( get_permalink( $post_item_id ) ); ?>">
				<?php echo wp_kses_post( get_the_title( $post_item_id ) ); ?>
			</a>
		</h2>

		<?php if ( has_excerpt( $post_item_id ) ) : ?>
			<div class="ftf-post-list__excerpt">
				<?php echo wp_kses_post( get_the_excerpt( $post_item_id ) ); ?>
			</div>
		<?php endif; ?>

		<?php do_action( 'fivetwofive_theme_after_card_content', $post_item_id, get_post_type( $post_item_id ), 'list' ); ?>

		<div class="ftf-post-list__content-bottom">
			<?php if ( $post_terms ) : ?>
				<div class="post-categories">
					<?php foreach ( $post_terms as $post_term ) : ?>
						<a href="<?php echo esc_url( get_category_link( $post_term->term_id ) ); ?>">
							<?php echo esc_html( $post_term->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="ftf-post-list__footer">
			<a class="read-more" href="<?php echo esc_url( get_permalink( $post_item_id ) ); ?>">
				<?php echo esc_html__( 'Read More', 'fivetwofive-theme' ); ?>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
					<path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
				</svg>
			</a>
		</div>
	</div>
</article> 