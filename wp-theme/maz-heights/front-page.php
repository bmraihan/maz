<?php
/**
 * Homepage — implements MAZ Heights Website.dc.html:
 * hero + quote form, services, process, recent work, price guide,
 * testimonials + accreditations, CTA band.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$maz_business  = maz_heights_business_info();
$maz_hero      = maz_heights_hero_content();
$maz_services  = maz_heights_get_services();
$maz_projects  = maz_heights_get_projects();
$maz_price_rows = maz_heights_price_guide_rows();
$maz_testimonials = maz_heights_get_testimonials();

$maz_hero_class = 'hero' . ( $maz_hero['image'] ? ' hero--has-image' : '' );
$maz_hero_style = $maz_hero['image'] ? ' style="background-image:url(\'' . esc_url( $maz_hero['image'] ) . '\')"' : '';
?>

<section class="<?php echo esc_attr( $maz_hero_class ); ?>"<?php echo $maz_hero_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url() above. ?>>
	<div class="wrap hero__grid">
		<div class="hero__copy">
			<div class="hero__eyebrow">
				<span class="hero__eyebrow-rule"></span>
				<span><?php echo esc_html( $maz_hero['eyebrow'] ); ?></span>
			</div>
			<h1><?php echo esc_html( $maz_hero['heading'] ); ?></h1>
			<p><?php echo esc_html( $maz_hero['subtext'] ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--brick" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php echo esc_html( $maz_hero['primary_label'] ); ?></a>
				<a class="btn btn--outline-dark" href="<?php echo esc_url( home_url( '/#work' ) ); ?>"><?php echo esc_html( $maz_hero['secondary_label'] ); ?></a>
			</div>
			<div class="hero__stats">
				<div class="stat">
					<div class="stat__value"><?php echo esc_html( $maz_business['years_trading'] ); ?> yrs</div>
					<div class="stat__label"><?php esc_html_e( 'IN COVENTRY', 'maz-heights' ); ?></div>
				</div>
				<div class="stat">
					<div class="stat__value"><?php echo esc_html( $maz_business['builds_done'] ); ?></div>
					<div class="stat__label"><?php esc_html_e( 'BUILDS COMPLETED', 'maz-heights' ); ?></div>
				</div>
				<div class="stat">
					<div class="stat__value"><?php echo esc_html( $maz_business['rating_value'] ); ?>★</div>
					<div class="stat__label"><?php
						printf(
							/* translators: %s: number of reviews. */
							esc_html__( 'GOOGLE · %s REVIEWS', 'maz-heights' ),
							esc_html( $maz_business['rating_count'] )
						);
					?></div>
				</div>
			</div>
		</div>
		<div class="hero__form" id="quote">
			<?php maz_heights_render_quote_form(); ?>
		</div>
	</div>
</section>

<section class="services" id="services">
	<div class="wrap services__inner">
		<div class="section-head">
			<div>
				<div class="eyebrow"><?php esc_html_e( 'WHAT WE DO', 'maz-heights' ); ?></div>
				<h2><?php esc_html_e( 'What we do, done to a standard', 'maz-heights' ); ?></h2>
			</div>
			<p class="section-head__lede"><?php esc_html_e( 'From extensions to landscaping, this is what we build week in, week out — including the structural work most contractors subcontract out.', 'maz-heights' ); ?></p>
		</div>

		<div class="service-grid">
			<?php foreach ( $maz_services as $service ) :
				$price_from = maz_heights_format_price_from( get_post_meta( $service->ID, 'maz_price_from', true ) );
				$cta_label  = get_post_meta( $service->ID, 'maz_cta_label', true );
				$cta_label  = $cta_label ? $cta_label : $service->post_title;
				$image_url  = maz_heights_post_image_url( $service->ID, 'maz-card', 'PHOTO · ' . strtoupper( $service->post_title ) );
			?>
			<article class="service-card">
				<div class="service-card__image" style="background-image:url('<?php echo esc_url( $image_url ); ?>')"></div>
				<div class="service-card__body">
					<h3><?php echo esc_html( $service->post_title ); ?></h3>
					<p><?php echo esc_html( maz_heights_truncate( $service->post_content, 220 ) ); ?></p>
					<div class="service-card__foot">
						<?php if ( $price_from ) : ?>
							<span class="mono-label"><?php echo esc_html( $price_from ); ?></span>
						<?php endif; ?>
						<a class="link-arrow" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php echo esc_html( $cta_label ); ?> →</a>
					</div>
				</div>
			</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="process" id="process">
	<div class="wrap process__inner">
		<div class="section-head">
			<div>
				<div class="eyebrow"><?php esc_html_e( 'HOW IT WORKS', 'maz-heights' ); ?></div>
				<h2><?php esc_html_e( "Five stages, and you always know which one you're in", 'maz-heights' ); ?></h2>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Start at stage one →', 'maz-heights' ); ?></a>
		</div>

		<div class="process-list">
			<?php foreach ( maz_heights_process_stages() as $stage ) : ?>
			<div class="process-row">
				<div class="process-row__number"><?php echo esc_html( $stage['number'] ); ?></div>
				<h3><?php echo esc_html( $stage['title'] ); ?></h3>
				<p><?php echo esc_html( $stage['body'] ); ?></p>
				<div class="mono-label"><?php echo esc_html( $stage['duration'] ); ?></div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="work" id="work">
	<div class="wrap work__inner">
		<div class="section-head">
			<div>
				<div class="eyebrow"><?php esc_html_e( 'RECENT WORK', 'maz-heights' ); ?></div>
				<h2><?php esc_html_e( 'Built in and around Coventry', 'maz-heights' ); ?></h2>
			</div>
			<a class="link-arrow" href="<?php echo esc_url( get_post_type_archive_link( 'maz_project' ) ); ?>"><?php
				printf(
					/* translators: %s: number of completed builds. */
					esc_html__( 'All %s projects →', 'maz-heights' ),
					esc_html( $maz_business['builds_done'] )
				);
			?></a>
		</div>

		<div class="work-grid">
			<?php if ( $maz_projects['featured'] ) :
				$featured   = $maz_projects['featured'];
				$location   = get_post_meta( $featured->ID, 'maz_location', true );
				$category   = get_post_meta( $featured->ID, 'maz_category', true );
				$duration   = get_post_meta( $featured->ID, 'maz_duration', true );
				$area_added = get_post_meta( $featured->ID, 'maz_area_added', true );
				$price      = get_post_meta( $featured->ID, 'maz_price_label', true );
				$before_after = maz_heights_sanitize_bool( get_post_meta( $featured->ID, 'maz_before_after', true ) );
				$image_url  = maz_heights_post_image_url( $featured->ID, 'maz-project', 'PHOTO · ' . strtoupper( $featured->post_title ) );
			?>
			<article class="work-feature">
				<div class="work-feature__image" style="background-image:url('<?php echo esc_url( $image_url ); ?>')">
					<?php if ( $before_after ) : ?>
						<span class="badge-tag"><?php esc_html_e( 'BEFORE / AFTER', 'maz-heights' ); ?></span>
					<?php endif; ?>
				</div>
				<div class="work-feature__body">
					<div class="mono-label mono-label--brick"><?php echo esc_html( strtoupper( trim( $category . ' · ' . $location, ' ·' ) ) ); ?></div>
					<h3><a href="<?php echo esc_url( get_permalink( $featured ) ); ?>"><?php echo esc_html( $featured->post_title ); ?></a></h3>
					<p><?php echo esc_html( maz_heights_truncate( $featured->post_content, 260 ) ); ?></p>
					<div class="work-feature__stats">
						<?php if ( $duration ) : ?><div><div class="stat__value stat__value--sm"><?php echo esc_html( $duration ); ?></div><div class="stat__label"><?php esc_html_e( 'ON SITE', 'maz-heights' ); ?></div></div><?php endif; ?>
						<?php if ( $area_added ) : ?><div><div class="stat__value stat__value--sm"><?php echo esc_html( $area_added ); ?></div><div class="stat__label"><?php esc_html_e( 'ADDED', 'maz-heights' ); ?></div></div><?php endif; ?>
						<?php if ( $price ) : ?><div><div class="stat__value stat__value--sm"><?php echo esc_html( $price ); ?></div><div class="stat__label"><?php esc_html_e( 'FIXED PRICE', 'maz-heights' ); ?></div></div><?php endif; ?>
						<a class="link-arrow work-feature__link" href="<?php echo esc_url( get_permalink( $featured ) ); ?>"><?php esc_html_e( 'Full case study →', 'maz-heights' ); ?></a>
					</div>
				</div>
			</article>
			<?php endif; ?>

			<div class="work-list">
				<?php foreach ( $maz_projects['secondary'] as $project ) :
					$location = get_post_meta( $project->ID, 'maz_location', true );
					$category = get_post_meta( $project->ID, 'maz_category', true );
					$duration = get_post_meta( $project->ID, 'maz_duration', true );
					$price    = get_post_meta( $project->ID, 'maz_price_label', true );
					$image_url = maz_heights_post_image_url( $project->ID, 'maz-thumb', 'PHOTO · ' . strtoupper( $project->post_title ) );
				?>
				<article class="work-list__item">
					<div class="work-list__image" style="background-image:url('<?php echo esc_url( $image_url ); ?>')"></div>
					<div class="work-list__body">
						<div class="mono-label mono-label--brick"><?php echo esc_html( strtoupper( trim( $category . ' · ' . $location, ' ·' ) ) ); ?></div>
						<h3><a href="<?php echo esc_url( get_permalink( $project ) ); ?>"><?php echo esc_html( $project->post_title ); ?></a></h3>
						<div class="mono-label"><?php echo esc_html( trim( $price . ' · ' . $duration, ' ·' ) ); ?></div>
					</div>
				</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="prices" id="prices">
	<div class="wrap prices__grid">
		<div class="prices__intro">
			<div class="eyebrow"><?php esc_html_e( 'PRICE GUIDE', 'maz-heights' ); ?></div>
			<h2><?php esc_html_e( 'What jobs like yours actually cost', 'maz-heights' ); ?></h2>
			<p><?php esc_html_e( 'Published ranges from our own completed jobs, including materials, labour, drawings and building control. Your quote is fixed after the survey — these are here so you can sanity-check your budget before you call anyone.', 'maz-heights' ); ?></p>
			<a class="btn btn--brick" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Get your fixed price', 'maz-heights' ); ?></a>
		</div>
		<div class="price-table">
			<div class="price-table__row price-table__row--head">
				<span><?php esc_html_e( 'JOB TYPE', 'maz-heights' ); ?></span>
				<span><?php esc_html_e( 'SIZE', 'maz-heights' ); ?></span>
				<span><?php esc_html_e( 'TYPICAL COST', 'maz-heights' ); ?></span>
				<span><?php esc_html_e( 'ON SITE', 'maz-heights' ); ?></span>
			</div>
			<?php foreach ( $maz_price_rows as $row ) : ?>
			<div class="price-table__row">
				<span class="price-table__job"><?php echo esc_html( $row['job'] ); ?></span>
				<span><?php echo esc_html( $row['size'] ); ?></span>
				<span><?php echo esc_html( $row['cost'] ); ?></span>
				<span><?php echo esc_html( $row['duration'] ); ?></span>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<section class="reviews">
	<div class="wrap reviews__inner">
		<div class="section-head section-head--baseline">
			<h2><?php esc_html_e( 'What customers say', 'maz-heights' ); ?></h2>
			<div class="mono-label">
				<?php
				printf(
					/* translators: 1: rating, 2: review count. */
					esc_html__( '%1$s / 5 · %2$s REVIEWS · GOOGLE & CHECKATRADE', 'maz-heights' ),
					esc_html( $maz_business['rating_value'] ),
					esc_html( $maz_business['rating_count'] )
				);
				?>
			</div>
		</div>

		<div class="review-grid">
			<?php foreach ( $maz_testimonials as $testimonial ) :
				$rating   = (int) get_post_meta( $testimonial->ID, 'maz_rating', true );
				$author   = get_post_meta( $testimonial->ID, 'maz_author', true );
				$location = get_post_meta( $testimonial->ID, 'maz_location', true );
				$job_type = get_post_meta( $testimonial->ID, 'maz_job_type', true );
			?>
			<article class="review-card">
				<div class="review-card__stars"><?php echo esc_html( maz_heights_star_rating_text( $rating ) ); ?></div>
				<p>&ldquo;<?php echo esc_html( $testimonial->post_content ); ?>&rdquo;</p>
				<div class="mono-label review-card__byline"><?php echo esc_html( strtoupper( trim( $author . ' · ' . $location . ' · ' . $job_type, ' ·' ) ) ); ?></div>
			</article>
			<?php endforeach; ?>
		</div>

		<div class="accreditations">
			<div class="mono-label"><?php esc_html_e( 'ACCREDITED & INSURED', 'maz-heights' ); ?></div>
			<?php foreach ( maz_heights_accreditation_badges() as $badge ) : ?>
				<div class="accreditations__badge"><?php echo esc_html( $badge ); ?></div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php maz_heights_render_cta_band(); ?>

<?php get_footer(); ?>
