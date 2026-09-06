<?php
/**
 * Tests for inc/seed-content.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

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
}
