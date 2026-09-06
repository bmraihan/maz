<?php
/**
 * Template Name: Service Landing
 *
 * Placeholder landing page for a single service (Extensions / Kitchens /
 * Bathrooms). The design handoff's chat transcript calls a dedicated
 * "Extensions service page" out as something to design "next" — it was
 * never actually designed, so this renders the matching maz_service
 * post's existing copy (title, summary, price-from) plus that
 * category's real projects, with a clear "full page coming soon" note
 * rather than inventing content that was never approved.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$maz_page    = get_queried_object();
$maz_service = $maz_page ? maz_heights_find_service_by_slug( $maz_page->post_name ) : null;
?>

<section class="service-landing">
	<div class="wrap">
		<div class="eyebrow"><?php esc_html_e( 'WHAT WE DO', 'maz-heights' ); ?></div>
		<h1><?php the_title(); ?></h1>

		<?php if ( $maz_service ) :
			$price_from = maz_heights_format_price_from( get_post_meta( $maz_service->ID, 'maz_price_from', true ) );
			$image_url  = maz_heights_post_image_url( $maz_service->ID, 'maz-hero', 'PHOTO · ' . strtoupper( $maz_service->post_title ) );
			$category   = get_post_meta( $maz_service->ID, 'maz_category_match', true );
		?>
			<div class="service-landing__hero" style="background-image:url('<?php echo esc_url( $image_url ); ?>')"></div>
			<div class="service-landing__intro">
				<p class="service-landing__summary"><?php echo esc_html( $maz_service->post_content ); ?></p>
				<?php if ( $price_from ) : ?>
					<div class="mono-label"><?php echo esc_html( $price_from ); ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="notice-card">
			<p>
				<?php
				printf(
					/* translators: %s: page title, e.g. "Extensions". */
					esc_html__( 'A full %s page — photo galleries, a step-by-step breakdown and FAQs — is coming soon. In the meantime, book a free survey below or see our recent work.', 'maz-heights' ),
					esc_html( get_the_title() )
				);
				?>
			</p>
			<a class="btn btn--brick" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Book a free survey', 'maz-heights' ); ?></a>
		</div>

		<?php if ( get_the_content() ) : ?>
			<div class="service-landing__content"><?php the_content(); ?></div>
		<?php endif; ?>

		<?php
		$related = $maz_service ? maz_heights_get_projects_by_category( get_post_meta( $maz_service->ID, 'maz_category_match', true ) ) : array();
		if ( ! empty( $related ) ) :
		?>
		<div class="service-landing__related">
			<h2><?php esc_html_e( 'Recent work in this category', 'maz-heights' ); ?></h2>
			<div class="work-grid work-grid--simple">
				<?php foreach ( $related as $project ) :
					$location  = get_post_meta( $project->ID, 'maz_location', true );
					$duration  = get_post_meta( $project->ID, 'maz_duration', true );
					$price     = get_post_meta( $project->ID, 'maz_price_label', true );
					$image_url = maz_heights_post_image_url( $project->ID, 'maz-thumb', 'PHOTO · ' . strtoupper( $project->post_title ) );
				?>
				<article class="work-list__item">
					<div class="work-list__image" style="background-image:url('<?php echo esc_url( $image_url ); ?>')"></div>
					<div class="work-list__body">
						<div class="mono-label mono-label--brick"><?php echo esc_html( strtoupper( $location ) ); ?></div>
						<h3><a href="<?php echo esc_url( get_permalink( $project ) ); ?>"><?php echo esc_html( $project->post_title ); ?></a></h3>
						<div class="mono-label"><?php echo esc_html( trim( $price . ' · ' . $duration, ' ·' ) ); ?></div>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</section>

<?php
maz_heights_render_cta_band();

get_footer();
