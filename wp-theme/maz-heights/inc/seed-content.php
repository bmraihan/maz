<?php
/**
 * Default content seeding.
 *
 * The homepage template pulls its services/projects/testimonials from
 * the maz_service / maz_project / maz_testimonial post types (see
 * post-types.php) so the client can edit or add to them from wp-admin.
 * On first activation there's nothing in the database yet, so this
 * seeds the exact copy from the approved MAZ Heights Website design —
 * the site looks finished immediately, and every string here is then a
 * real, editable WordPress post rather than hard-coded markup. It also
 * creates one landing page per service and a real "Primary Menu"
 * (assigned to the header's nav location), so Appearance → Menus shows
 * something editable from the first page load instead of only the
 * hard-coded fallback in inc/nav-fallback.php.
 *
 * Seeding is idempotent per item, not just per run: every service,
 * project and testimonial is looked up by its slug before insert (see
 * maz_heights_get_post_id_by_slug()), and existing ones are left
 * untouched. That matters because maz_heights_seed_data() is expected
 * to grow over time — e.g. a later theme update adding new service
 * categories — and MAZ_HEIGHTS_SEED_VERSION being bumped re-runs this
 * on a site that was already activated under an older version. Per-item
 * checks mean that re-run only inserts what's actually new, rather than
 * either duplicating everything or (if gated purely on the version
 * flag) silently never adding the new items to a live site at all.
 * Likewise, maz_heights_sync_primary_menu() adds any missing items to
 * whatever menu is already assigned to `primary`, instead of only ever
 * acting on a site with nothing assigned yet. And it's not only tied to
 * the initial seed run: maz_heights_sync_navigation_on_service_save()
 * re-runs the same page + menu sync every time a service is published
 * from wp-admin, so a category added after launch (a 9th, say) needs
 * no code change to get a real landing page and a real nav item.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAZ_HEIGHTS_SEED_OPTION', 'maz_heights_seeded_version' );
define( 'MAZ_HEIGHTS_SEED_VERSION', '3' );

/**
 * The default services, projects and testimonials, matching the copy
 * in MAZ Heights Website.dc.html, plus later-added service categories
 * (New Builds, Roofing, Landscaping & Gardens, Concrete Laying, Loft
 * Conversions) that weren't part of the original approved design and
 * so ship here as placeholder copy/pricing for the client to review
 * and adjust, the same way the service landing pages already are.
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
			array(
				'title'   => 'New Builds',
				'content' => 'Single plot and infill new-build houses, from feasibility and planning through to structural shell and final certificate. One team for design, groundworks, build and finish.',
				'meta'    => array(
					'maz_price_from'      => 180000,
					'maz_cta_label'       => 'New Builds',
					'maz_category_match'  => 'New Build',
				),
				'menu_order' => 3,
			),
			array(
				'title'   => 'Roofing',
				'content' => 'Re-roofs, roof repairs and replacement coverings in tile, slate and flat roofing, plus fascias, soffits and guttering. Fully insured, workmanship guaranteed.',
				'meta'    => array(
					'maz_price_from'      => 4500,
					'maz_cta_label'       => 'Roofing',
					'maz_category_match'  => 'Roofing',
				),
				'menu_order' => 4,
			),
			array(
				'title'   => 'Landscaping & Gardens',
				'content' => 'Garden design and build, patios, decking, planting and drainage. We handle the groundwork and hard landscaping ourselves, so levels, falls and drainage are done right first time.',
				'meta'    => array(
					'maz_price_from'      => 5000,
					'maz_cta_label'       => 'Landscaping',
					'maz_category_match'  => 'Landscaping',
				),
				'menu_order' => 5,
			),
			array(
				'title'   => 'Concrete Laying',
				'content' => 'Concrete bases, driveways, footings and slabs, reinforced and finished to spec — from a single base to a full driveway or foundation pour.',
				'meta'    => array(
					'maz_price_from'      => 2500,
					'maz_cta_label'       => 'Concrete',
					'maz_category_match'  => 'Concrete',
				),
				'menu_order' => 6,
			),
			array(
				'title'   => 'Loft Conversions',
				'content' => 'Dormer, hip-to-gable and rooflight loft conversions, including structural steels, staircases, insulation and building control sign-off.',
				'meta'    => array(
					'maz_price_from'      => 35000,
					'maz_cta_label'       => 'Loft Conversions',
					'maz_category_match'  => 'Loft Conversion',
				),
				'menu_order' => 7,
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
 * Whether the seed routine still needs to run at all.
 *
 * This is a cheap early-exit, not the thing that prevents duplicate
 * content — that's the per-item slug check in maz_heights_seed_content().
 * Once every item in maz_heights_seed_data() has been inserted, this
 * still returns false so a plain page load doesn't re-check every slug
 * on every request; it only flips back to true when
 * MAZ_HEIGHTS_SEED_VERSION is bumped (i.e. new seed data was added).
 *
 * @param string|false $current_option Current value of MAZ_HEIGHTS_SEED_OPTION (or false if unset).
 * @return bool
 */
function maz_heights_should_seed( $current_option ) {
	return MAZ_HEIGHTS_SEED_VERSION !== $current_option;
}

/**
 * Find a published post's ID by post type + slug, or 0 if there isn't one.
 *
 * get_page_by_path() works for any post type despite its name — it's
 * the standard WP way to look a post up by slug without a full
 * WP_Query. Used to make seeding idempotent per item: a slug found
 * here was seeded (or manually created with that exact slug) already,
 * so it's left alone rather than re-inserted.
 *
 * @param string $post_type Post type slug.
 * @param string $slug      Post slug (post_name).
 * @return int
 */
function maz_heights_get_post_id_by_slug( $post_type, $slug ) {
	$existing = get_page_by_path( $slug, OBJECT, $post_type );

	return $existing ? $existing->ID : 0;
}

/**
 * Insert any of the default services/projects/testimonials that don't
 * already exist (matched by slug), then sync the service landing pages
 * and primary menu. Safe to call repeatedly — every step it calls is
 * idempotent per item, not just gated on a single "already ran" flag.
 */
function maz_heights_seed_content() {
	update_option( MAZ_HEIGHTS_SEED_OPTION, MAZ_HEIGHTS_SEED_VERSION );
	maz_heights_set_bulk_seeding( true );

	$post_type_map = array(
		'services'     => 'maz_service',
		'projects'     => 'maz_project',
		'testimonials' => 'maz_testimonial',
	);

	foreach ( maz_heights_seed_data() as $group => $items ) {
		$post_type = $post_type_map[ $group ];

		foreach ( $items as $item ) {
			$slug = sanitize_title( $item['title'] );

			if ( maz_heights_get_post_id_by_slug( $post_type, $slug ) ) {
				continue;
			}

			$post_id = wp_insert_post( array(
				'post_type'    => $post_type,
				'post_status'  => 'publish',
				'post_title'   => $item['title'],
				'post_name'    => $slug,
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

	maz_heights_set_bulk_seeding( false );
	maz_heights_sync_services_navigation();
}

/**
 * Whether a bulk seed run (maz_heights_seed_content()) is currently in
 * progress. Backs a re-entrancy guard: inserting a service inside that
 * bulk loop fires `save_post_maz_service` for each one immediately
 * (WordPress hooks run synchronously), which would otherwise trigger
 * maz_heights_sync_services_navigation() once per service, partway
 * through the loop, on an incomplete set of services each time — see
 * maz_heights_sync_navigation_on_service_save(), which checks this and
 * backs off while true, leaving the single sync at the end of
 * maz_heights_seed_content() to do that work exactly once, against the
 * complete set.
 *
 * @param bool $in_progress
 */
function maz_heights_set_bulk_seeding( $in_progress ) {
	$GLOBALS['maz_heights_bulk_seeding'] = (bool) $in_progress;
}

/**
 * @return bool
 */
function maz_heights_is_bulk_seeding() {
	return ! empty( $GLOBALS['maz_heights_bulk_seeding'] );
}

/**
 * Ensure a landing page exists for every currently-published service,
 * and a "Primary Menu" (assigned to the header's nav location)
 * contains all of them plus Our work/Process/Prices.
 *
 * This runs both during the initial seed (right after the default
 * services are inserted, above) and on every `maz_service` save — see
 * maz_heights_sync_navigation_on_service_save() below — so a service
 * added later from wp-admin (a 9th category, say) gets exactly the
 * same "real page + real nav item" treatment as the eight the theme
 * ships with, with no code change or theme update required. It reads
 * live `maz_service` posts (maz_heights_get_services()), never the
 * maz_heights_seed_data() fixture, which exists only to bootstrap a
 * blank database.
 */
function maz_heights_sync_services_navigation() {
	$service_page_ids = maz_heights_sync_service_landing_pages();

	$services = array_map( static function ( $post ) {
		return array( 'title' => $post->post_title );
	}, maz_heights_get_services() );

	maz_heights_sync_primary_menu( maz_heights_primary_menu_items( $services, $service_page_ids ) );
}

/**
 * Create a published Page for every currently-published service that
 * doesn't already have one, assigned the "Service Landing" page
 * template, so the header nav and footer links have somewhere real to
 * go instead of dead-ending — see page-templates/service-landing.php,
 * which finds its content by matching the page slug back to a
 * maz_service post. Idempotent per service: an existing page at that
 * slug is left alone and just reported back by ID.
 *
 * These are deliberately thin: full service pages (galleries, FAQs,
 * finance options etc.) were called out in the design handoff as
 * "next" and were never designed for the original three services, so
 * this seeds a placeholder landing page rather than guessing at
 * content that was never approved — and the same treatment is used
 * for every service category added since.
 *
 * @return array<string,int> Service slug => page ID, for maz_heights_primary_menu_items().
 */
function maz_heights_sync_service_landing_pages() {
	$page_ids = array();

	foreach ( maz_heights_get_services() as $service ) {
		$slug        = sanitize_title( $service->post_title );
		$existing_id = maz_heights_get_post_id_by_slug( 'page', $slug );

		if ( $existing_id ) {
			$page_ids[ $slug ] = $existing_id;
			continue;
		}

		$page_id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $service->post_title,
			'post_name'    => $slug,
			'page_template' => 'page-templates/service-landing.php',
		), true );

		if ( ! is_wp_error( $page_id ) && $page_id ) {
			$page_ids[ $slug ] = $page_id;
		}
	}

	return $page_ids;
}

/**
 * The "Primary Navigation" menu's desired items, in display order.
 *
 * Services nest under a single "Services" item rather than sitting in
 * the nav as one entry each — with 3 that read fine flat, but the same
 * layout with 8+ is exactly the cluttered, hard-to-scan nav this was
 * built to avoid, and it would only keep growing as more categories
 * get added. "Services" itself still links straight to the homepage
 * section (so a click always goes somewhere useful even before anyone
 * discovers the dropdown); the individual services are its children
 * and, on hover/focus, reveal the direct link to each one's own page.
 * This mirrors maz_heights_fallback_nav_items() (inc/nav-fallback.php)
 * in spirit — Extensions/Kitchens/Bathrooms there all point at the one
 * #services anchor too — just carried through consistently instead of
 * abandoned the moment each service got a real page of its own.
 *
 * Takes the service list as a plain argument (rather than querying
 * `maz_service` posts itself) purely so it stays unit-testable without
 * a database — both real callers pass it real, live posts (mapped down
 * to just their title). A service is only included once its landing
 * page exists (i.e. it has an entry in $service_page_ids).
 *
 * @param array<int,array{title:string}> $services         Services in display order, each needing only a 'title'.
 * @param array<string,int>              $service_page_ids Service slug => page ID, from maz_heights_sync_service_landing_pages().
 * @return array<int,array<string,mixed>> wp_update_nav_menu_item()-shaped item specs, in menu order; a "Services" item carries its children under a `children` key in the same shape.
 */
function maz_heights_primary_menu_items( array $services, array $service_page_ids ) {
	$children = array();

	foreach ( $services as $service ) {
		$slug = sanitize_title( $service['title'] );

		if ( empty( $service_page_ids[ $slug ] ) ) {
			continue;
		}

		$children[] = array(
			'title'     => $service['title'],
			'type'      => 'post_type',
			'object'    => 'page',
			'object_id' => $service_page_ids[ $slug ],
		);
	}

	$items = array();

	if ( $children ) {
		$items[] = array(
			'title'    => 'Services',
			'type'     => 'custom',
			'url'      => home_url( '/#services' ),
			'children' => $children,
		);
	}

	$items[] = array(
		'title'  => 'Our work',
		'type'   => 'post_type_archive',
		'object' => 'maz_project',
	);
	$items[] = array(
		'title' => 'Process',
		'type'  => 'custom',
		'url'   => home_url( '/#process' ),
	);
	$items[] = array(
		'title' => 'Prices',
		'type'  => 'custom',
		'url'   => home_url( '/#prices' ),
	);

	return $items;
}

/**
 * Whether it's safe to create a brand new seeded menu.
 *
 * Only true when nothing is already assigned to the `primary` location
 * — so a site admin's own menu choice is never replaced outright. This
 * doesn't gate whether *items* get added, though: see
 * maz_heights_sync_primary_menu(), which adds any missing items to
 * whatever menu ends up assigned either way.
 *
 * @param array<string,int> $existing_locations Current `nav_menu_locations` theme mod (get_theme_mod('nav_menu_locations', array())).
 * @return bool
 */
function maz_heights_should_assign_primary_menu( array $existing_locations ) {
	return empty( $existing_locations['primary'] );
}

/**
 * Normalise a menu item title for comparison.
 *
 * WordPress nav menu items round-trip a title like "Landscaping &
 * Gardens" through several storage/rendering layers, and titles
 * containing `&` (or other characters `wp_specialchars_decode()`
 * touches) don't reliably come back byte-for-byte identical to what
 * was passed to `wp_update_nav_menu_item()` — sometimes as `&`,
 * sometimes as `&#038;`/`&amp;`. Comparing raw strings then never
 * finds a match for that title, so maz_heights_sync_primary_menu()
 * treats it as missing forever and a duplicate gets added on every
 * single sync. Decoding both sides before comparing (here, and in
 * maz_heights_duplicate_menu_item_ids() for cleaning up ones already
 * created that way) makes the comparison immune to which encoded form
 * either side happens to be in.
 *
 * Matching on title at all (rather than object ID) is itself a
 * trade-off: if an admin deliberately removed, say, "Prices" from the
 * menu, a later sync that still considers it desired will add it back
 * rather than respect the removal. Documented here rather than
 * silently assumed away.
 *
 * @param string $title
 * @return string
 */
function maz_heights_normalize_menu_title( $title ) {
	return trim( wp_specialchars_decode( (string) $title, ENT_QUOTES ) );
}

/**
 * Which menu item IDs are redundant duplicates (by normalised title),
 * keeping the first occurrence of each title and flagging the rest.
 *
 * Exists to clean up duplicates already created by the title-encoding
 * mismatch maz_heights_normalize_menu_title() now guards against —
 * without this, an already-affected site's menu stays duplicated
 * forever even once the code stops creating new ones.
 *
 * @param array<int,array{id:int,title:string}> $items Menu items in their current order.
 * @return int[] IDs to delete.
 */
function maz_heights_duplicate_menu_item_ids( array $items ) {
	$seen       = array();
	$duplicates = array();

	foreach ( $items as $item ) {
		$key = maz_heights_normalize_menu_title( $item['title'] );

		if ( isset( $seen[ $key ] ) ) {
			$duplicates[] = $item['id'];
			continue;
		}

		$seen[ $key ] = true;
	}

	return $duplicates;
}

/**
 * Delete any duplicate items (by normalised title) from a nav menu.
 *
 * @param int $menu_id Nav menu term ID.
 */
function maz_heights_dedupe_menu_items( $menu_id ) {
	$items = wp_get_nav_menu_items( $menu_id );

	if ( ! $items ) {
		return;
	}

	$simple = array_map( static function ( $item ) {
		return array( 'id' => $item->ID, 'title' => $item->title );
	}, $items );

	foreach ( maz_heights_duplicate_menu_item_ids( $simple ) as $item_id ) {
		wp_delete_post( $item_id, true );
	}
}

/**
 * Ensure a "Primary Menu" exists, is assigned to the `primary` theme
 * location, and contains every currently-desired item, correctly
 * nested.
 *
 * On a fresh site this creates the menu, adds every item and assigns
 * it. On a site that already has something assigned to `primary`, it
 * adds whatever's newly missing — e.g. a service category added since
 * — to that existing menu, leaving its current top-level items, their
 * order, and any manual additions alone.
 *
 * The one deliberate exception is a service (a `children` entry) that
 * already exists in the menu but not under the right parent — e.g. a
 * site synced before services were grouped under "Services" has each
 * one sitting loose at the top level. Rather than leave it there
 * forever (title-matching alone would: it's not "missing"), it's
 * moved under the correct parent and its type/link data re-asserted.
 * This is safe specifically because these items are always
 * theme-generated with predictable content, never something typed
 * freehand — reparenting can't clobber an admin's own wording the way
 * blindly overwriting a top-level item's title or link could.
 *
 * @param array<int,array<string,mixed>> $desired_items From maz_heights_primary_menu_items().
 */
function maz_heights_sync_primary_menu( array $desired_items ) {
	$locations = get_theme_mod( 'nav_menu_locations', array() );

	if ( maz_heights_should_assign_primary_menu( $locations ) ) {
		$menu    = get_term_by( 'name', 'Primary Menu', 'nav_menu' );
		$menu_id = $menu ? $menu->term_id : wp_create_nav_menu( 'Primary Menu' );

		if ( is_wp_error( $menu_id ) || ! $menu_id ) {
			return;
		}

		$locations['primary'] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	} else {
		$menu_id = $locations['primary'];
		// A menu that already existed may carry duplicates from before
		// maz_heights_normalize_menu_title() started guarding against
		// them; a freshly created one above can't have any yet.
		maz_heights_dedupe_menu_items( $menu_id );
	}

	$existing_items = wp_get_nav_menu_items( $menu_id );
	$position       = $existing_items ? count( $existing_items ) : 0;

	// Normalised title => {id, parent_id}, for every existing item
	// (not just top-level ones) — used both to skip what already
	// exists and, for children specifically, to notice one sitting
	// under the wrong (or no) parent.
	$existing_by_title = array();
	foreach ( (array) $existing_items as $existing_item ) {
		$existing_by_title[ maz_heights_normalize_menu_title( $existing_item->title ) ] = array(
			'id'        => (int) $existing_item->ID,
			'parent_id' => (int) $existing_item->menu_item_parent,
		);
	}

	$top_level_items = array_map( static function ( $item ) {
		unset( $item['children'] );
		return $item;
	}, $desired_items );

	$parent_ids_by_title = array();

	foreach ( $top_level_items as $item ) {
		$key = maz_heights_normalize_menu_title( $item['title'] );

		if ( isset( $existing_by_title[ $key ] ) ) {
			$parent_ids_by_title[ $key ] = $existing_by_title[ $key ]['id'];
			continue;
		}

		++$position;
		$new_id = maz_heights_upsert_menu_item( $menu_id, $item, $position );

		if ( $new_id ) {
			$parent_ids_by_title[ $key ] = $new_id;
			$existing_by_title[ $key ]   = array( 'id' => $new_id, 'parent_id' => 0 );
		}
	}

	foreach ( $desired_items as $item ) {
		if ( empty( $item['children'] ) ) {
			continue;
		}

		$parent_id = $parent_ids_by_title[ maz_heights_normalize_menu_title( $item['title'] ) ] ?? 0;

		if ( ! $parent_id ) {
			continue; // Parent failed to create; leave children for the next sync.
		}

		$child_position = 0;

		foreach ( $item['children'] as $child ) {
			++$child_position;
			$child_key = maz_heights_normalize_menu_title( $child['title'] );
			$existing  = $existing_by_title[ $child_key ] ?? null;

			if ( $existing && (int) $existing['parent_id'] === $parent_id ) {
				continue; // Already exactly where it should be.
			}

			++$position;
			maz_heights_upsert_menu_item( $menu_id, $child, $child_position, $parent_id, $existing['id'] ?? 0 );
		}
	}
}

/**
 * Create or fully update a single nav menu item.
 *
 * WordPress's `wp_update_nav_menu_item()` isn't a partial update: any
 * field left out of `$menu_item_data` resets to a generic default
 * (empty title, type 'custom', etc.) rather than keeping the existing
 * item's current value. So moving an existing item to a new parent
 * means resending its full title/type/link data too, not just the new
 * `menu-item-parent-id` — passing `$menu_item_db_id` here is what
 * makes this an update-in-place instead of creating a new item.
 *
 * @param int   $menu_id          Nav menu term ID.
 * @param array $item             Item spec: title, type, plus (for 'custom') url or (otherwise) object/object_id.
 * @param int   $position         1-based position among its siblings.
 * @param int   $parent_id        Parent menu item ID, or 0 for a top-level item.
 * @param int   $menu_item_db_id  Existing menu item post ID to update, or 0 to create a new one.
 * @return int The item's ID (new or existing), or 0 on failure.
 */
function maz_heights_upsert_menu_item( $menu_id, array $item, $position, $parent_id = 0, $menu_item_db_id = 0 ) {
	$args = array(
		'menu-item-title'     => $item['title'],
		'menu-item-status'    => 'publish',
		'menu-item-position'  => $position,
		'menu-item-type'      => $item['type'],
		'menu-item-parent-id' => $parent_id,
	);

	if ( 'custom' === $item['type'] ) {
		$args['menu-item-url'] = $item['url'];
	} else {
		$args['menu-item-object']    = $item['object'];
		$args['menu-item-object-id'] = $item['object_id'] ?? 0;
	}

	$result = wp_update_nav_menu_item( $menu_id, $menu_item_db_id, $args );

	return is_wp_error( $result ) ? 0 : (int) $result;
}

/**
 * Keep the landing page + primary menu in sync whenever a service is
 * published from wp-admin — not just during the initial seed run — so
 * a service added after launch gets a real page and a real nav item
 * automatically, the same as the eight the theme ships with.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function maz_heights_sync_navigation_on_service_save( $post_id, $post ) {
	if ( maz_heights_is_bulk_seeding() ) {
		// maz_heights_seed_content() will run the sync itself, once,
		// after every service in this batch is inserted — see
		// maz_heights_is_bulk_seeding()'s docblock.
		return;
	}

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( 'publish' !== $post->post_status ) {
		return;
	}

	maz_heights_sync_services_navigation();
}
add_action( 'save_post_maz_service', 'maz_heights_sync_navigation_on_service_save', 20, 2 );

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
 * database was provisioned, such as a fresh `wp db import`) and a
 * theme update that bumps MAZ_HEIGHTS_SEED_VERSION on an already-live
 * site. `get_option()` is a cached read, so this is effectively free
 * on every request after the first.
 */
function maz_heights_maybe_seed_on_init() {
	if ( maz_heights_should_seed( get_option( MAZ_HEIGHTS_SEED_OPTION, false ) ) ) {
		maz_heights_seed_content();
	}
}
add_action( 'init', 'maz_heights_maybe_seed_on_init', 20 );
