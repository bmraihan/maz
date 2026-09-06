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

	public function test_seed_data_has_three_services_four_projects_three_testimonials(): void {
		$data = \maz_heights_seed_data();

		$this->assertCount( 3, $data['services'] );
		$this->assertCount( 4, $data['projects'] );
		$this->assertCount( 3, $data['testimonials'] );
	}

	public function test_each_service_has_a_category_match_used_by_the_landing_page_template(): void {
		$expected = array(
			'Extensions' => 'Extension',
			'Kitchens'   => 'Kitchen',
			'Bathrooms'  => 'Bathroom',
		);

		foreach ( \maz_heights_seed_data()['services'] as $service ) {
			$this->assertSame( $expected[ $service['title'] ], $service['meta']['maz_category_match'] );
		}
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

		$items = \maz_heights_primary_menu_items( array(
			'extensions' => 11,
			'kitchens'   => 12,
			'bathrooms'  => 13,
		) );

		$this->assertSame(
			array( 'Extensions', 'Kitchens', 'Bathrooms', 'Our work', 'Process', 'Prices' ),
			array_column( $items, 'title' )
		);

		$this->assertSame( 'post_type', $items[0]['type'] );
		$this->assertSame( 'page', $items[0]['object'] );
		$this->assertSame( 11, $items[0]['object_id'] );
	}

	public function test_primary_menu_items_skips_a_service_with_no_seeded_page(): void {
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/' );

		// Kitchens missing — e.g. its page failed to insert, or was deleted.
		$items = \maz_heights_primary_menu_items( array(
			'extensions' => 11,
			'bathrooms'  => 13,
		) );

		$this->assertSame(
			array( 'Extensions', 'Bathrooms', 'Our work', 'Process', 'Prices' ),
			array_column( $items, 'title' )
		);
	}

	public function test_primary_menu_items_our_work_is_a_post_type_archive_link(): void {
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/' );

		$items = \maz_heights_primary_menu_items( array() );
		$our_work = array_values( array_filter( $items, static fn( $item ) => 'Our work' === $item['title'] ) )[0];

		$this->assertSame( 'post_type_archive', $our_work['type'] );
		$this->assertSame( 'maz_project', $our_work['object'] );
	}

	public function test_primary_menu_items_process_and_prices_are_homepage_anchors(): void {
		Functions\when( 'home_url' )->alias( static fn( $path = '' ) => 'https://mazheights.test' . $path );

		$items = \maz_heights_primary_menu_items( array() );
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
}
