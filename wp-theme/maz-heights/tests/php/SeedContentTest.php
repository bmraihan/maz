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

	public function test_primary_menu_items_links_each_service_to_its_seeded_page(): void {
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/' );

		$services = array(
			array( 'title' => 'Extensions' ),
			array( 'title' => 'Kitchens' ),
			array( 'title' => 'Bathrooms' ),
		);
		$page_ids = array( 'extensions' => 11, 'kitchens' => 12, 'bathrooms' => 13 );

		$items = \maz_heights_primary_menu_items( $services, $page_ids );

		$this->assertSame(
			array( 'Extensions', 'Kitchens', 'Bathrooms', 'Our work', 'Process', 'Prices' ),
			array_column( $items, 'title' )
		);

		$this->assertSame( 'post_type', $items[0]['type'] );
		$this->assertSame( 'page', $items[0]['object'] );
		$this->assertSame( 11, $items[0]['object_id'] );
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
			array( 'Extensions', 'Bathrooms', 'Our work', 'Process', 'Prices' ),
			array_column( $items, 'title' )
		);
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

	public function test_primary_menu_items_works_directly_from_seed_data_for_a_fresh_install(): void {
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/' );

		$services = array_map( static fn( $s ) => array( 'title' => $s['title'] ), \maz_heights_seed_data()['services'] );
		$page_ids = array();
		foreach ( $services as $i => $service ) {
			$page_ids[ \sanitize_title( $service['title'] ) ] = 100 + $i;
		}

		$items = \maz_heights_primary_menu_items( $services, $page_ids );

		$this->assertSame(
			array( 'Extensions', 'Kitchens', 'Bathrooms', 'New Builds', 'Roofing', 'Landscaping & Gardens', 'Concrete Laying', 'Loft Conversions', 'Our work', 'Process', 'Prices' ),
			array_column( $items, 'title' )
		);
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

	public function test_missing_menu_items_returns_only_items_not_already_present(): void {
		$desired = array(
			array( 'title' => 'Extensions' ),
			array( 'title' => 'Roofing' ),
			array( 'title' => 'Our work' ),
		);

		$missing = \maz_heights_missing_menu_items( $desired, array( 'Extensions', 'Our work' ) );

		$this->assertSame( array( array( 'title' => 'Roofing' ) ), $missing );
	}

	public function test_missing_menu_items_returns_everything_when_menu_is_empty(): void {
		$desired = array( array( 'title' => 'Extensions' ), array( 'title' => 'Roofing' ) );

		$this->assertSame( $desired, \maz_heights_missing_menu_items( $desired, array() ) );
	}

	public function test_missing_menu_items_returns_nothing_when_all_present(): void {
		$desired = array( array( 'title' => 'Extensions' ) );

		$this->assertSame( array(), \maz_heights_missing_menu_items( $desired, array( 'Extensions', 'Roofing' ) ) );
	}
}
