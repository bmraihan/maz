<?php
/**
 * Tests for inc/seed-content.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

use Brain\Monkey\Functions;

require_once __DIR__ . '/TestCase.php';

final class SeedContentTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		require_once dirname( __DIR__, 2 ) . '/inc/seed-content.php';
	}

	public function test_should_seed_is_true_when_option_missing(): void {
		$this->assertTrue( \maz_heights_should_seed( false ) );
	}

	public function test_should_seed_is_false_once_version_matches(): void {
		$this->assertFalse( \maz_heights_should_seed( MAZ_HEIGHTS_SEED_VERSION ) );
	}

	public function test_should_seed_is_true_for_a_stale_version(): void {
		$this->assertTrue( \maz_heights_should_seed( '0' ) );
	}

	public function test_get_post_id_by_slug_returns_zero_when_nothing_found(): void {
		Functions\when( 'get_page_by_path' )->justReturn( null );

		$this->assertSame( 0, \maz_heights_get_post_id_by_slug( 'maz_service', 'roofing' ) );
	}

	public function test_get_post_id_by_slug_returns_the_existing_post_id(): void {
		Functions\when( 'get_page_by_path' )->justReturn( (object) array( 'ID' => 42 ) );

		$this->assertSame( 42, \maz_heights_get_post_id_by_slug( 'maz_service', 'roofing' ) );
	}

	public function test_seed_data_has_eight_services_four_projects_three_testimonials(): void {
		$data = \maz_heights_seed_data();

		$this->assertCount( 8, $data['services'] );
		$this->assertCount( 4, $data['projects'] );
		$this->assertCount( 3, $data['testimonials'] );
	}

	public function test_service_titles_include_the_original_three_and_the_later_additions(): void {
		$titles = array_column( \maz_heights_seed_data()['services'], 'title' );

		$this->assertSame(
			array(
				'Extensions',
				'Kitchens',
				'Bathrooms',
				'New Builds',
				'Roofing',
				'Landscaping & Gardens',
				'Concrete Laying',
				'Loft Conversions',
			),
			$titles
		);
	}

	public function test_each_service_has_a_category_match_used_by_the_landing_page_template(): void {
		$expected = array(
			'Extensions'            => 'Extension',
			'Kitchens'              => 'Kitchen',
			'Bathrooms'             => 'Bathroom',
			'New Builds'            => 'New Build',
			'Roofing'               => 'Roofing',
			'Landscaping & Gardens' => 'Landscaping',
			'Concrete Laying'       => 'Concrete',
			'Loft Conversions'      => 'Loft Conversion',
		);

		foreach ( \maz_heights_seed_data()['services'] as $service ) {
			$this->assertSame( $expected[ $service['title'] ], $service['meta']['maz_category_match'] );
		}
	}

	public function test_every_service_has_a_positive_price_from_and_menu_order(): void {
		foreach ( \maz_heights_seed_data()['services'] as $service ) {
			$this->assertGreaterThan( 0, $service['meta']['maz_price_from'], "{$service['title']} needs a positive price" );
			$this->assertArrayHasKey( 'menu_order', $service );
		}
	}

	public function test_service_menu_orders_are_unique_and_sequential(): void {
		$orders = array_column( \maz_heights_seed_data()['services'], 'menu_order' );

		$this->assertSame( range( 0, count( $orders ) - 1 ), $orders );
	}

	public function test_exactly_one_project_is_marked_featured(): void {
		$featured = array_filter(
			\maz_heights_seed_data()['projects'],
			static fn( $project ) => true === $project['meta']['maz_featured']
		);

		$this->assertCount( 1, $featured );
		$this->assertSame( 'Wrap-around extension and new kitchen', array_values( $featured )[0]['title'] );
	}

	public function test_every_project_meta_key_is_registered_in_post_types(): void {
		require_once dirname( __DIR__, 2 ) . '/inc/post-types.php';
		$registered = array_keys( \maz_heights_meta_fields()['maz_project'] );

		foreach ( \maz_heights_seed_data()['projects'] as $project ) {
			foreach ( array_keys( $project['meta'] ) as $meta_key ) {
				$this->assertContains( $meta_key, $registered, "$meta_key is seeded but not registered" );
			}
		}
	}

	public function test_primary_menu_items_top_level_is_just_four_items_regardless_of_service_count(): void {
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/' );

		$services = array_map( static fn( $s ) => array( 'title' => $s['title'] ), \maz_heights_seed_data()['services'] );
		$page_ids = array();
		foreach ( $services as $i => $service ) {
			$page_ids[ \sanitize_title( $service['title'] ) ] = 100 + $i;
		}

		$items = \maz_heights_primary_menu_items( $services, $page_ids );

		// All 8 services collapse into one "Services" parent, so the top
		// level nav never grows past 4 items no matter how many services
		// exist — that's the whole point of nesting them.
		$this->assertSame(
			array( 'Services', 'Our work', 'Process', 'Prices' ),
			array_column( $items, 'title' )
		);
	}

	public function test_primary_menu_items_nests_every_service_under_services(): void {
		Functions\when( 'home_url' )->alias( static fn( $path = '' ) => 'https://mazheights.test' . $path );

		$services = array(
			array( 'title' => 'Extensions' ),
			array( 'title' => 'Kitchens' ),
			array( 'title' => 'Bathrooms' ),
		);
		$page_ids = array( 'extensions' => 11, 'kitchens' => 12, 'bathrooms' => 13 );

		$items    = \maz_heights_primary_menu_items( $services, $page_ids );
		$services_item = $items[0];

		$this->assertSame( 'Services', $services_item['title'] );
		$this->assertSame( 'custom', $services_item['type'] );
		$this->assertSame( 'https://mazheights.test/#services', $services_item['url'] );
		$this->assertSame(
			array( 'Extensions', 'Kitchens', 'Bathrooms' ),
			array_column( $services_item['children'], 'title' )
		);
		$this->assertSame( 'post_type', $services_item['children'][0]['type'] );
		$this->assertSame( 'page', $services_item['children'][0]['object'] );
		$this->assertSame( 11, $services_item['children'][0]['object_id'] );
	}

	public function test_primary_menu_items_skips_a_service_with_no_page_yet(): void {
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/' );

		$services = array(
			array( 'title' => 'Extensions' ),
			array( 'title' => 'Kitchens' ), // No page yet — e.g. insert failed, or a brand-new service mid-sync.
			array( 'title' => 'Bathrooms' ),
		);
		$page_ids = array( 'extensions' => 11, 'bathrooms' => 13 );

		$items = \maz_heights_primary_menu_items( $services, $page_ids );

		$this->assertSame(
			array( 'Extensions', 'Bathrooms' ),
			array_column( $items[0]['children'], 'title' )
		);
	}

	public function test_primary_menu_items_omits_services_item_when_no_pages_exist_yet(): void {
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/' );

		$services = array( array( 'title' => 'Extensions' ) );

		$items = \maz_heights_primary_menu_items( $services, array() );

		$this->assertSame( array( 'Our work', 'Process', 'Prices' ), array_column( $items, 'title' ) );
	}

	public function test_primary_menu_items_our_work_is_a_post_type_archive_link(): void {
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/' );

		$items = \maz_heights_primary_menu_items( array(), array() );
		$our_work = array_values( array_filter( $items, static fn( $item ) => 'Our work' === $item['title'] ) )[0];

		$this->assertSame( 'post_type_archive', $our_work['type'] );
		$this->assertSame( 'maz_project', $our_work['object'] );
	}

	public function test_primary_menu_items_process_and_prices_are_homepage_anchors(): void {
		Functions\when( 'home_url' )->alias( static fn( $path = '' ) => 'https://mazheights.test' . $path );

		$items = \maz_heights_primary_menu_items( array(), array() );
		$by_title = array_combine( array_column( $items, 'title' ), $items );

		$this->assertSame( 'custom', $by_title['Process']['type'] );
		$this->assertSame( 'https://mazheights.test/#process', $by_title['Process']['url'] );
		$this->assertSame( 'https://mazheights.test/#prices', $by_title['Prices']['url'] );
	}

	public function test_should_assign_primary_menu_is_true_when_location_empty(): void {
		$this->assertTrue( \maz_heights_should_assign_primary_menu( array() ) );
		$this->assertTrue( \maz_heights_should_assign_primary_menu( array( 'primary' => 0 ) ) );
	}

	public function test_should_assign_primary_menu_is_false_once_something_is_assigned(): void {
		$this->assertFalse( \maz_heights_should_assign_primary_menu( array( 'primary' => 7 ) ) );
	}

	public function test_should_assign_primary_menu_ignores_other_locations(): void {
		// A footer menu being assigned shouldn't block seeding the primary one.
		$this->assertTrue( \maz_heights_should_assign_primary_menu( array( 'footer' => 3 ) ) );
	}

	public function test_upsert_menu_item_returns_the_new_id_on_success(): void {
		Functions\when( 'wp_update_nav_menu_item' )->justReturn( 55 );

		$id = \maz_heights_upsert_menu_item( 1, array( 'title' => 'Roofing', 'type' => 'custom', 'url' => 'https://mazheights.test/#services' ), 1 );

		$this->assertSame( 55, $id );
	}

	public function test_upsert_menu_item_returns_zero_on_failure(): void {
		Functions\when( 'wp_update_nav_menu_item' )->justReturn( (object) array( 'errors' => array() ) );

		$id = \maz_heights_upsert_menu_item( 1, array( 'title' => 'Roofing', 'type' => 'custom', 'url' => 'https://mazheights.test/#services' ), 1 );

		$this->assertSame( 0, $id );
	}

	public function test_upsert_menu_item_passes_the_parent_id_through(): void {
		$captured = null;
		Functions\when( 'wp_update_nav_menu_item' )->alias( function ( $menu_id, $item_id, $args ) use ( &$captured ) {
			$captured = $args;
			return 99;
		} );

		\maz_heights_upsert_menu_item( 1, array( 'title' => 'Extensions', 'type' => 'post_type', 'object' => 'page', 'object_id' => 11 ), 1, 42 );

		$this->assertSame( 42, $captured['menu-item-parent-id'] );
		$this->assertSame( 'page', $captured['menu-item-object'] );
		$this->assertSame( 11, $captured['menu-item-object-id'] );
	}

	public function test_upsert_menu_item_passes_the_existing_db_id_to_update_in_place(): void {
		$capturedDbId = null;
		Functions\when( 'wp_update_nav_menu_item' )->alias( function ( $menu_id, $item_id ) use ( &$capturedDbId ) {
			$capturedDbId = $item_id;
			return $item_id ?: 77;
		} );

		\maz_heights_upsert_menu_item( 1, array( 'title' => 'Extensions', 'type' => 'custom', 'url' => 'https://mazheights.test/' ), 1, 0, 123 );

		$this->assertSame( 123, $capturedDbId );
	}

	/**
	 * Regression test for the real bug this fixes: "Landscaping & Gardens"
	 * kept being re-added to the menu on every sync because WordPress
	 * round-trips a stored menu item title with `&` as `&#038;`/`&amp;`,
	 * so a raw string comparison against the desired title (with a plain
	 * `&`) never matched — it looked "missing" forever. Both encoded
	 * forms must normalise to the exact same string the desired title
	 * ("Landscaping & Gardens") normalises to.
	 *
	 * @dataProvider ampersandEncodingProvider
	 */
	public function test_normalize_menu_title_matches_regardless_of_ampersand_encoding( $storedTitle ): void {
		$this->assertSame(
			\maz_heights_normalize_menu_title( 'Landscaping & Gardens' ),
			\maz_heights_normalize_menu_title( $storedTitle )
		);
	}

	public function ampersandEncodingProvider(): array {
		return array(
			'plain ampersand' => array( 'Landscaping & Gardens' ),
			'numeric entity'  => array( 'Landscaping &#038; Gardens' ),
			'named entity'    => array( 'Landscaping &amp; Gardens' ),
		);
	}

	public function test_normalize_menu_title_trims_whitespace_too(): void {
		$this->assertSame( 'Roofing', \maz_heights_normalize_menu_title( '  Roofing  ' ) );
	}

	public function test_duplicate_menu_item_ids_keeps_first_occurrence_of_each_title(): void {
		$items = array(
			array( 'id' => 1, 'title' => 'Extensions' ),
			array( 'id' => 2, 'title' => 'Landscaping &amp; Gardens' ),
			array( 'id' => 3, 'title' => 'Roofing' ),
			array( 'id' => 4, 'title' => 'Landscaping &#038; Gardens' ),
			array( 'id' => 5, 'title' => 'Landscaping & Gardens' ),
		);

		$this->assertSame( array( 4, 5 ), \maz_heights_duplicate_menu_item_ids( $items ) );
	}

	public function test_duplicate_menu_item_ids_returns_nothing_when_all_unique(): void {
		$items = array(
			array( 'id' => 1, 'title' => 'Extensions' ),
			array( 'id' => 2, 'title' => 'Roofing' ),
		);

		$this->assertSame( array(), \maz_heights_duplicate_menu_item_ids( $items ) );
	}

	public function test_bulk_seeding_flag_defaults_to_false_and_can_be_toggled(): void {
		\maz_heights_set_bulk_seeding( false );
		$this->assertFalse( \maz_heights_is_bulk_seeding() );

		\maz_heights_set_bulk_seeding( true );
		$this->assertTrue( \maz_heights_is_bulk_seeding() );

		// Leave it clean for any other test that runs in this process.
		\maz_heights_set_bulk_seeding( false );
	}
}
