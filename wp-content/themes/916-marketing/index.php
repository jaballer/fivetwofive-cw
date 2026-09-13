<?php
/**
 * Fallback template for everything that isn't the front page
 * (blog index, single posts, pages, archives, search, 404).
 *
 * @package 916_Marketing
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="content" class="band">
  <?php if ( have_posts() ) : ?>

    <?php if ( is_archive() || is_search() ) : ?>
      <div class="band-head">
        <h1 class="t-heading-h2">
          <?php
          if ( is_search() ) {
            /* translators: %s: search query. */
            printf( esc_html__( 'Results for “%s”', '916-marketing' ), esc_html( get_search_query() ) );
          } else {
            the_archive_title();
          }
          ?>
        </h1>
      </div>
    <?php endif; ?>

    <?php
    while ( have_posts() ) :
      the_post();
      ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
        <?php if ( is_singular() ) : ?>
          <h1 class="entry-title t-display-hero"><?php the_title(); ?></h1>
        <?php else : ?>
          <h2 class="entry-title t-heading-h2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php endif; ?>

        <?php if ( 'post' === get_post_type() ) : ?>
          <p class="entry-meta band-eyebrow t-label-caps"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
        <?php endif; ?>

        <div class="entry-content t-body-default">
          <?php is_singular() ? the_content() : the_excerpt(); ?>
        </div>
      </article>
    <?php endwhile; ?>

    <?php the_posts_pagination(); ?>

  <?php else : ?>

    <div class="entry">
      <p class="band-eyebrow t-label-caps"><?php echo is_404() ? '404' : esc_html__( 'Nothing here', '916-marketing' ); ?></p>
      <h1 class="entry-title t-display-hero"><?php esc_html_e( 'That page isn’t here.', '916-marketing' ); ?></h1>
      <p class="band-intro t-body-large"><?php esc_html_e( 'It may have moved, or the link may be out of date.', '916-marketing' ); ?></p>
      <p class="entry-actions"><a class="btn btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', '916-marketing' ); ?></a></p>
    </div>

  <?php endif; ?>
</main>

<?php
get_footer();
