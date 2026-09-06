<?php
/**
 * Fallback template (blog index, search results, 404s that don't hit
 * a dedicated template). The homepage itself always uses front-page.php.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="page-intro">
	<div class="wrap">
		<?php if ( is_search() ) : ?>
			<h1>
				<?php
				printf(
					/* translators: %s: search query. */
					esc_html__( 'Search results for "%s"', 'maz-heights' ),
					esc_html( get_search_query() )
				);
				?>
			</h1>
		<?php else : ?>
			<h1><?php esc_html_e( 'Nothing here yet', 'maz-heights' ); ?></h1>
		<?php endif; ?>
	</div>
</section>

<section class="basic-page">
	<div class="wrap">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article style="margin-bottom:32px">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Try the homepage instead:', 'maz-heights' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_url( home_url( '/' ) ); ?></a></p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
