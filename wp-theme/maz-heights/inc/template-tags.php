<?php
/**
 * Query helpers and inline-SVG placeholder graphics used by front-page.php
 * and the other templates.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetch all services, in the order set via each post's "Order" page
 * attribute (menu_order) in wp-admin.
 *
 * @return WP_Post[]
 */
function maz_heights_get_services() {
	return get_posts( array(
		'post_type'      => 'maz_service',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
}

/**
 * Fetch projects split into the single featured "recent work" hero card
 * and the remaining smaller list, newest first within each group.
 *
 * @param int $max_secondary Maximum number of non-featured projects to return.
 * @return array{featured: WP_Post|null, secondary: WP_Post[]}
 */
function maz_heights_get_projects( $max_secondary = 3 ) {
	$featured_posts = get_posts( array(
		'post_type'      => 'maz_project',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => 'maz_featured',
		'meta_value'     => '1',
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	$featured = $featured_posts[0] ?? null;

	$secondary = get_posts( array(
		'post_type'      => 'maz_project',
		'post_status'    => 'publish',
		'posts_per_page' => $max_secondary,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post__not_in'   => $featured ? array( $featured->ID ) : array(),
	) );

	// Fall back to the newest project if nothing is explicitly featured,
	// so the section still renders sensibly before an editor sets a flag.
	if ( ! $featured && ! empty( $secondary ) ) {
		$featured = array_shift( $secondary );
	}

	return array(
		'featured'  => $featured,
		'secondary' => $secondary,
	);
}

/**
 * Whether a service's title matches a given page slug, comparing on
 * WordPress's own slug normalisation so "Bathrooms & Wet Rooms" would
 * still match a "bathrooms-wet-rooms" page.
 *
 * @param string $service_title Service post title.
 * @param string $slug          Page slug to compare against.
 * @return bool
 */
function maz_heights_service_matches_slug( $service_title, $slug ) {
	return sanitize_title( $service_title ) === sanitize_title( $slug );
}

/**
 * Find the maz_service post whose title slugifies to the given page
 * slug — used by page-templates/service-landing.php to pull the right
 * service's content onto e.g. /extensions/.
 *
 * @param string $slug Page slug.
 * @return WP_Post|null
 */
function maz_heights_find_service_by_slug( $slug ) {
	foreach ( maz_heights_get_services() as $service ) {
		if ( maz_heights_service_matches_slug( $service->post_title, $slug ) ) {
			return $service;
		}
	}

	return null;
}

/**
 * Projects whose maz_category matches a service's title (e.g. all
 * "Extension" category projects for the Extensions service page).
 *
 * @param string $category Category label to match, case-insensitively.
 * @param int    $max      Maximum number of projects to return.
 * @return WP_Post[]
 */
function maz_heights_get_projects_by_category( $category, $max = 3 ) {
	return get_posts( array(
		'post_type'      => 'maz_project',
		'post_status'    => 'publish',
		'posts_per_page' => $max,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => array(
			array(
				'key'     => 'maz_category',
				'value'   => $category,
				'compare' => '=',
			),
		),
	) );
}

/**
 * Fetch all testimonials, newest first.
 *
 * @param int $max Maximum number to return.
 * @return WP_Post[]
 */
function maz_heights_get_testimonials( $max = 3 ) {
	return get_posts( array(
		'post_type'      => 'maz_testimonial',
		'post_status'    => 'publish',
		'posts_per_page' => $max,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
}

/**
 * The published price-guide table (job type / size / typical cost / time
 * on site). This one section is deliberately kept as theme-code data
 * rather than a wp-admin-editable CPT: it's a single small reference
 * table that a developer updates alongside pricing policy, not
 * day-to-day content — see the theme README for that trade-off.
 *
 * Filterable so a site-specific plugin (or a future admin screen) can
 * still override it without editing theme files.
 *
 * @return array<int,array{job:string,size:string,cost:string,duration:string}>
 */
function maz_heights_price_guide_rows() {
	$rows = array(
		array(
			'job'      => __( 'Single storey rear extension', 'maz-heights' ),
			'size'     => '20–30 m²',
			'cost'     => '£45k–£70k',
			'duration' => '10–14 wks',
		),
		array(
			'job'      => __( 'Wrap-around / side return', 'maz-heights' ),
			'size'     => '30–45 m²',
			'cost'     => '£65k–£95k',
			'duration' => '14–18 wks',
		),
		array(
			'job'      => __( 'Double storey extension', 'maz-heights' ),
			'size'     => '35–50 m²',
			'cost'     => '£85k–£130k',
			'duration' => '16–22 wks',
		),
		array(
			'job'      => __( 'Kitchen refit', 'maz-heights' ),
			'size'     => '—',
			'cost'     => '£10k–£28k',
			'duration' => '3–5 wks',
		),
		array(
			'job'      => __( 'Bathroom or wet room', 'maz-heights' ),
			'size'     => '—',
			'cost'     => '£6k–£16k',
			'duration' => '2–3 wks',
		),
		array(
			'job'      => __( 'Garage conversion', 'maz-heights' ),
			'size'     => '12–18 m²',
			'cost'     => '£14k–£22k',
			'duration' => '4–6 wks',
		),
	);

	/**
	 * Filters the published price-guide rows.
	 *
	 * @param array $rows Default rows.
	 */
	return apply_filters( 'maz_heights_price_guide_rows', $rows );
}

/**
 * Build an inline-SVG "photo slot" placeholder: a duotone diagonal
 * pattern in the brand stone/graphite palette with a small caption,
 * standing in for real job photography until it's supplied.
 *
 * Pure string-building (no WP i18n/escaping calls beyond plain
 * htmlspecialchars) so tests/php/TemplateTagsTest.php can assert on
 * its output directly.
 *
 * @param string $label Caption text, e.g. "PHOTO · REAR EXTENSION".
 * @return string Raw <svg>…</svg> markup.
 */
function maz_heights_placeholder_svg( $label ) {
	$safe_label = htmlspecialchars( (string) $label, ENT_QUOTES, 'UTF-8' );

	return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" role="img" aria-label="' . $safe_label . '" preserveAspectRatio="xMidYMid slice">'
		. '<rect width="400" height="300" fill="#F4F1EC"/>'
		. '<path d="M0 300 L133 150 L400 300 Z" fill="#E4DED4"/>'
		. '<path d="M400 0 L233 150 L400 220 Z" fill="#E4DED4"/>'
		. '<path d="M133 150 L200 90 L267 150 Z" fill="#101820"/>'
		. '<path d="M155 165 L200 122 L245 165 Z" fill="#B4552A"/>'
		. '<text x="16" y="284" font-family="IBM Plex Mono, monospace" font-size="12" letter-spacing="1" fill="#9A9184">' . $safe_label . '</text>'
		. '</svg>';
}

/**
 * Same as maz_heights_placeholder_svg(), encoded as a `data:` URI ready
 * to drop straight into an `<img src>` or CSS `background-image`.
 *
 * @param string $label Caption text.
 * @return string
 */
function maz_heights_placeholder_data_uri( $label ) {
	return 'data:image/svg+xml,' . rawurlencode( maz_heights_placeholder_svg( $label ) );
}

/**
 * Render the brick CTA band ("Planning an extension… Book the survey
 * now.") shared by the homepage and the service placeholder pages.
 *
 * @param string $heading Heading text.
 * @param string $body    Supporting sentence.
 */
function maz_heights_render_cta_band( $heading = '', $body = '' ) {
	$business = maz_heights_business_info();
	$heading  = $heading ? $heading : __( 'Planning an extension for next spring? Book the survey now.', 'maz-heights' );
	$body     = $body ? $body : __( 'Design and planning take eight to twelve weeks before a spade goes in the ground.', 'maz-heights' );
	?>
	<section class="cta-band">
		<div class="wrap cta-band__inner">
			<div class="cta-band__copy">
				<h2><?php echo esc_html( $heading ); ?></h2>
				<p><?php echo esc_html( $body ); ?></p>
			</div>
			<div class="cta-band__actions">
				<a class="btn btn--dark" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Book a free survey', 'maz-heights' ); ?></a>
				<a class="btn btn--outline-light" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $business['phone'] ) ); ?>"><?php echo esc_html( $business['phone'] ); ?></a>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Trust badges shown in the top utility bar.
 *
 * @return string[]
 */
function maz_heights_trust_badges() {
	return apply_filters( 'maz_heights_trust_badges', array(
		__( 'FMB MEMBER', 'maz-heights' ),
		__( 'TRUSTMARK REGISTERED', 'maz-heights' ),
		__( '10 YEAR GUARANTEE', 'maz-heights' ),
	) );
}

/**
 * Accreditation badges shown in the "What customers say" section.
 *
 * @return string[]
 */
function maz_heights_accreditation_badges() {
	return apply_filters( 'maz_heights_accreditation_badges', array(
		'FMB',
		'TRUSTMARK',
		'GAS SAFE',
		'NICEIC',
		'£5M LIABILITY',
	) );
}

/**
 * The fixed 5-stage process shown in the "How it works" section.
 *
 * Kept as filterable static data (like the price guide) rather than a
 * CPT: five stages that describe how the business operates change
 * rarely and as a deliberate policy decision, not day-to-day content.
 *
 * @return array<int,array{number:string,title:string,body:string,duration:string}>
 */
function maz_heights_process_stages() {
	$stages = array(
		array(
			'number'   => '01',
			'title'    => __( 'Free home survey', 'maz-heights' ),
			'body'     => __( 'We measure up, look at drainage, access and structure, and talk honestly about what your budget will and won\'t stretch to.', 'maz-heights' ),
			'duration' => __( 'WITHIN 5 DAYS', 'maz-heights' ),
		),
		array(
			'number'   => '02',
			'title'    => __( 'Design & drawings', 'maz-heights' ),
			'body'     => __( 'Concept layouts, then full plans. We confirm permitted development or submit the planning application to the council on your behalf.', 'maz-heights' ),
			'duration' => __( '2–8 WEEKS', 'maz-heights' ),
		),
		array(
			'number'   => '03',
			'title'    => __( 'Fixed written quote', 'maz-heights' ),
			'body'     => __( 'Itemised by stage, with a start date and a payment schedule. The price only changes if you change the spec — in writing.', 'maz-heights' ),
			'duration' => __( '5 WORKING DAYS', 'maz-heights' ),
		),
		array(
			'number'   => '04',
			'title'    => __( 'The build', 'maz-heights' ),
			'body'     => __( 'One site manager, one number to call, weekly photo updates and a tidy site. Building control inspect at every stage.', 'maz-heights' ),
			'duration' => __( '2–22 WEEKS', 'maz-heights' ),
		),
		array(
			'number'   => '05',
			'title'    => __( 'Handover & guarantee', 'maz-heights' ),
			'body'     => __( 'We walk the job with you, clear the snags, and hand over completion certificates, warranties and a 10 year structural guarantee.', 'maz-heights' ),
			'duration' => __( '1 WEEK', 'maz-heights' ),
		),
	);

	return apply_filters( 'maz_heights_process_stages', $stages );
}

/**
 * Featured-image URL for a post, or the branded SVG placeholder if none
 * has been uploaded yet.
 *
 * @param int    $post_id     Post ID.
 * @param string $size        Registered image size.
 * @param string $placeholder_label Caption to use if falling back to a placeholder.
 * @return string
 */
function maz_heights_post_image_url( $post_id, $size, $placeholder_label ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$url = get_the_post_thumbnail_url( $post_id, $size );
		if ( $url ) {
			return $url;
		}
	}

	return maz_heights_placeholder_data_uri( $placeholder_label );
}
