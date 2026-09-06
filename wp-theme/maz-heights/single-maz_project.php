<?php
/**
 * Single case study (maz_project).
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$location     = get_post_meta( get_the_ID(), 'maz_location', true );
	$category     = get_post_meta( get_the_ID(), 'maz_category', true );
	$duration     = get_post_meta( get_the_ID(), 'maz_duration', true );
	$area_added   = get_post_meta( get_the_ID(), 'maz_area_added', true );
	$price        = get_post_meta( get_the_ID(), 'maz_price_label', true );
	$before_after = maz_heights_sanitize_bool( get_post_meta( get_the_ID(), 'maz_before_after', true ) );
	$image_url    = maz_heights_post_image_url( get_the_ID(), 'maz-hero', 'PHOTO · ' . strtoupper( get_the_title() ) );
	?>

	<article class="case-study">
		<div class="case-study__hero" style="background-image:url('<?php echo esc_url( $image_url ); ?>')">
			<?php if ( $before_after ) : ?>
				<span class="badge-tag"><?php esc_html_e( 'BEFORE / AFTER', 'maz-heights' ); ?></span>
			<?php endif; ?>
		</div>
		<div class="wrap case-study__body">
			<div class="mono-label mono-label--brick"><?php echo esc_html( strtoupper( trim( $category . ' · ' . $location, ' ·' ) ) ); ?></div>
			<h1><?php the_title(); ?></h1>

			<div class="case-study__stats">
				<?php if ( $duration ) : ?><div><div class="stat__value"><?php echo esc_html( $duration ); ?></div><div class="stat__label"><?php esc_html_e( 'ON SITE', 'maz-heights' ); ?></div></div><?php endif; ?>
				<?php if ( $area_added ) : ?><div><div class="stat__value"><?php echo esc_html( $area_added ); ?></div><div class="stat__label"><?php esc_html_e( 'ADDED', 'maz-heights' ); ?></div></div><?php endif; ?>
				<?php if ( $price ) : ?><div><div class="stat__value"><?php echo esc_html( $price ); ?></div><div class="stat__label"><?php esc_html_e( 'FIXED PRICE', 'maz-heights' ); ?></div></div><?php endif; ?>
			</div>

			<div class="case-study__content">
				<?php the_content(); ?>
			</div>

			<a class="link-arrow" href="<?php echo esc_url( get_post_type_archive_link( 'maz_project' ) ); ?>">&larr; <?php esc_html_e( 'All recent work', 'maz-heights' ); ?></a>
		</div>
	</article>

	<?php
endwhile;

maz_heights_render_cta_band();

get_footer();
