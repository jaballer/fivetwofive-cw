<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package FiveTwoFive_Theme
 */

get_header();
?>

	<main id="primary" class="site-main">
		<header class="page-header">
			<div class="container">
				<?php
				the_archive_title( '<h1 class="page-header__title">', '</h1>' );
				the_archive_description( '<div class="page-header__description">', '</div>' );
				?>
			</div>
		</header><!-- .page-header -->

		<?php
		if ( have_posts() ) :
			/**
			 * Taxonomy whose terms the archive cards show. Plugins can swap it, e.g. Projects and Topic archives show Topics.
			 */
			$archive_card_taxonomy = apply_filters( 'fivetwofive_theme_archive_card_taxonomy', 'post_tag' );
			?>
			<div class="posts-container view-grid">
				<div class="row">
					<?php
					/* Start the Loop */
					while ( have_posts() ) :
						the_post();
						?>

						<div class="col-lg-4 col-md-6 col-sm-6 mb-3 mb-sm-4 grid-item">
							<?php
							get_template_part(
								'template-parts/post-type/post-card',
								null,
								array(
									'id'       => get_the_ID(),
									'taxonomy' => $archive_card_taxonomy,
								)
							);
							?>
						</div>

						<div class="col-12 list-item" style="display: none;">
							<?php
							get_template_part(
								'template-parts/post-type/post-list',
								null,
								array(
									'id'       => get_the_ID(),
									'taxonomy' => $archive_card_taxonomy,
								)
							);
							?>
						</div>

					<?php
					endwhile;
					?>
				</div>
			</div>

			<?php
			the_posts_pagination( array( 'mid_size'  => 2 ) );

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php

get_footer();
