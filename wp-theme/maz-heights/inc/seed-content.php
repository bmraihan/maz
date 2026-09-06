<?php
/**
 * One-time default content seeding.
 *
 * The homepage template pulls its services/projects/testimonials from
 * the maz_service / maz_project / maz_testimonial post types (see
 * post-types.php) so the client can edit or add to them from wp-admin.
 * On first activation there's nothing in the database yet, so this
 * seeds the exact copy from the approved MAZ Heights Website design —
 * the site looks finished immediately, and every string here is then a
 * real, editable WordPress post rather than hard-coded markup.
 *
 * Seeding is idempotent: maz_heights_should_seed() gates it on an
 * option flag, so re-activating the theme (or a stray extra `init`)
 * never creates duplicate posts.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAZ_HEIGHTS_SEED_OPTION', 'maz_heights_seeded_version' );
define( 'MAZ_HEIGHTS_SEED_VERSION', '1' );

/**
 * The default services, projects and testimonials, matching the copy
 * in MAZ Heights Website.dc.html.
 *
 * Pure data — no WP calls — so its shape and content can be asserted
 * directly in tests/php/SeedContentTest.php.
 *
 * @return array{services: array, projects: array, testimonials: array}
 */
function maz_heights_seed_data() {
	return array(
		'services'     => array(
			array(
				'title'   => 'Extensions',
				'content' => 'Single and double storey, side returns and wrap-arounds, garage and loft conversions. Planning, structural calcs and building control included.',
				'meta'    => array(
					'maz_price_from'      => 45000,
					'maz_cta_label'       => 'Extensions',
					'maz_category_match'  => 'Extension',
				),
				'menu_order' => 0,
			),
			array(
				'title'   => 'Kitchens',
				'content' => 'Full refits, knock-throughs and open-plan kitchen-diners. Steelwork, first-fix electrics and plumbing done by our own trades.',
				'meta'    => array(
					'maz_price_from'      => 10000,
					'maz_cta_label'       => 'Kitchens',
					'maz_category_match'  => 'Kitchen',
				),
				'menu_order' => 1,
			),
			array(
				'title'   => 'Bathrooms',
				'content' => 'Family bathrooms, en-suites and level-access wet rooms. Tanking, tiling and certified electrics — finished in two to three weeks.',
				'meta'    => array(
					'maz_price_from'      => 6000,
					'maz_cta_label'       => 'Bathrooms',
					'maz_category_match'  => 'Bathroom',
				),
				'menu_order' => 2,
			),
		),
		'projects'     => array(
			array(
				'title'   => 'Wrap-around extension and new kitchen',
				'content' => 'A 1930s semi with a dark galley kitchen. We took the rear and side out to the boundary, opened the ground floor with two steel beams and rebuilt the kitchen around a 3.6m island.',
				'meta'    => array(
					'maz_location'     => 'Earlsdon, CV5',
					'maz_category'     => 'Extension',
					'maz_duration'     => '14 weeks',
					'maz_area_added'   => '42 m²',
					'maz_price_label'  => '£78,000',
					'maz_featured'     => true,
					'maz_before_after' => true,
				),
			),
			array(
				'title'   => 'Knock-through kitchen-diner',
				'content' => '',
				'meta'    => array(
					'maz_location'     => 'Coundon',
					'maz_category'     => 'Kitchen',
					'maz_duration'     => '4 weeks',
					'maz_area_added'   => '',
					'maz_price_label'  => '£22,000',
					'maz_featured'     => false,
					'maz_before_after' => false,
				),
			),
			array(
				'title'   => 'Level-access wet room',
				'content' => '',
				'meta'    => array(
					'maz_location'     => 'Binley',
					'maz_category'     => 'Bathroom',
					'maz_duration'     => '3 weeks',
					'maz_area_added'   => '',
					'maz_price_label'  => '£9,400',
					'maz_featured'     => false,
					'maz_before_after' => false,
				),
			),
			array(
				'title'   => 'Double storey side extension',
				'content' => '',
				'meta'    => array(
					'maz_location'     => 'Kenilworth',
					'maz_category'     => 'Extension',
					'maz_duration'     => '20 weeks',
					'maz_area_added'   => '',
					'maz_price_label'  => '£112,000',
					'maz_featured'     => false,
					'maz_before_after' => false,
				),
			),
		),
		'testimonials' => array(
			array(
				'title'   => 'Sarah & Tom, Earlsdon',
				'content' => 'They gave us a fixed price in writing and stuck to it, even when the foundations needed extra depth. The site was swept every Friday.',
				'meta'    => array(
					'maz_rating'   => 5,
					'maz_author'   => 'Sarah & Tom',
					'maz_location' => 'Earlsdon',
					'maz_job_type' => 'Extension',
					'maz_source'   => 'Google',
				),
			),
			array(
				'title'   => 'R. Patel, Coundon',
				'content' => 'Kitchen done in four weeks flat. They dealt with the steel beam and the electrics themselves, so nobody was waiting on anybody.',
				'meta'    => array(
					'maz_rating'   => 5,
					'maz_author'   => 'R. Patel',
					'maz_location' => 'Coundon',
					'maz_job_type' => 'Kitchen',
					'maz_source'   => 'Google',
				),
			),
			array(
				'title'   => 'J. McLean, Kenilworth',
				'content' => 'Third builder we asked, first one who actually explained the planning side. Drawings were done and approved without us chasing.',
				'meta'    => array(
					'maz_rating'   => 5,
					'maz_author'   => 'J. McLean',
					'maz_location' => 'Kenilworth',
					'maz_job_type' => 'Double storey',
					'maz_source'   => 'Google',
				),
			),
		),
	);
}

/**
 * Whether the seed routine still needs to run.
 *
 * @param string|false $current_option Current value of MAZ_HEIGHTS_SEED_OPTION (or false if unset).
 * @return bool
 */
function maz_heights_should_seed( $current_option ) {
	return MAZ_HEIGHTS_SEED_VERSION !== $current_option;
}

/**
 * Insert the default services/projects/testimonials and mark seeding done.
 *
 * Safe to call more than once: it's only ever invoked from behind the
 * maz_heights_should_seed() gate, and it flips the option flag before
 * inserting so a mid-request failure can't retry into duplicates on
 * the next page load.
 */
function maz_heights_seed_content() {
	update_option( MAZ_HEIGHTS_SEED_OPTION, MAZ_HEIGHTS_SEED_VERSION );

	$post_type_map = array(
		'services'     => 'maz_service',
		'projects'     => 'maz_project',
		'testimonials' => 'maz_testimonial',
	);

	foreach ( maz_heights_seed_data() as $group => $items ) {
		$post_type = $post_type_map[ $group ];

		foreach ( $items as $item ) {
			$post_id = wp_insert_post( array(
				'post_type'    => $post_type,
				'post_status'  => 'publish',
				'post_title'   => $item['title'],
				'post_content' => $item['content'],
				'menu_order'   => $item['menu_order'] ?? 0,
			), true );

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			foreach ( $item['meta'] as $meta_key => $meta_value ) {
				update_post_meta( $post_id, $meta_key, $meta_value );
			}
		}
	}

	maz_heights_seed_service_landing_pages();
}

/**
 * Create one published Page per service (Extensions, Kitchens,
 * Bathrooms), assigned the "Service Landing" page template, so the
 * header nav and footer links have somewhere real to go instead of
 * dead-ending — see page-templates/service-landing.php, which finds
 * its content by matching the page slug back to a maz_service post.
 *
 * These are deliberately thin: full service pages (galleries, FAQs,
 * finance options etc.) were called out in the design handoff as
 * "next" and were never designed, so this seeds a placeholder landing
 * page rather than guessing at content that was never approved.
 */
function maz_heights_seed_service_landing_pages() {
	foreach ( maz_heights_seed_data()['services'] as $service ) {
		$slug    = sanitize_title( $service['title'] );
		$existing = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $existing ) {
			continue;
		}

		wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $service['title'],
			'post_name'    => $slug,
			'page_template' => 'page-templates/service-landing.php',
		), true );
	}
}

/**
 * Seed on theme activation.
 */
function maz_heights_maybe_seed_on_activation() {
	if ( maz_heights_should_seed( get_option( MAZ_HEIGHTS_SEED_OPTION, false ) ) ) {
		maz_heights_seed_content();
	}
}
add_action( 'after_switch_theme', 'maz_heights_maybe_seed_on_activation' );

/**
 * Also check on init, covering activation paths that don't fire
 * `after_switch_theme` (e.g. the theme was already active when the
 * database was provisioned, such as a fresh `wp db import`).
 * `get_option()` is a cached read, so this is effectively free on
 * every request after the first.
 */
function maz_heights_maybe_seed_on_init() {
	if ( maz_heights_should_seed( get_option( MAZ_HEIGHTS_SEED_OPTION, false ) ) ) {
		maz_heights_seed_content();
	}
}
add_action( 'init', 'maz_heights_maybe_seed_on_init', 20 );
