<?php
/**
 * "Our work" archive — all case studies.
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
		<div class="eyebrow"><?php esc_html_e( 'RECENT WORK', 'maz-heights' ); ?></div>
		<h1><?php esc_html_e( 'Every job on this page is one we\'ve actually built', 'maz-heights' ); ?></h1>
	</div>
</section>

<section class="work-archive">
	<div class="wrap work-archive__grid">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post();
				$location = get_post_meta( get_the_ID(), 'maz_location', true );
				$category = get_post_meta( get_the_ID(), 'maz_category', true );
				$duration = get_post_meta( get_the_ID(), 'maz_duration', true );
				$price    = get_post_meta( get_the_ID(), 'maz_price_label', true );
				$image_url = maz_heights_post_image_url( get_the_ID(), 'maz-project', 'PHOTO · ' . strtoupper( get_the_title() ) );
			?>
			<article class="work-list__item work-list__item--grid">
				<a href="<?php the_permalink(); ?>" class="work-list__image" style="background-image:url('<?php echo esc_url( $image_url ); ?>')"></a>
				<div class="work-list__body">
					<div class="mono-label mono-label--brick"><?php echo esc_html( strtoupper( trim( $category . ' · ' . $location, ' ·' ) ) ); ?></div>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<div class="mono-label"><?php echo esc_html( trim( $price . ' · ' . $duration, ' ·' ) ); ?></div>
				</div>
			</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'New projects are added regularly — check back soon.', 'maz-heights' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
the_posts_pagination( array(
	'prev_text' => __( '← Newer', 'maz-heights' ),
	'next_text' => __( 'Older →', 'maz-heights' ),
) );

maz_heights_render_cta_band();

get_footer();
