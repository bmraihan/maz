<?php
/**
 * Tests for inc/setup.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

use Brain\Monkey\Functions;

require_once __DIR__ . '/TestCase.php';

final class SetupTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		require_once dirname( __DIR__, 2 ) . '/inc/setup.php';
	}

	public function test_nav_menus_has_primary_and_footer_locations(): void {
		$this->assertSame( array( 'primary', 'footer' ), array_keys( \maz_heights_nav_menus() ) );
	}

	public function test_image_sizes_are_all_hard_cropped_with_positive_dimensions(): void {
		foreach ( \maz_heights_image_sizes() as $name => $size ) {
			$this->assertCount( 3, $size, "$name should be [width, height, crop]" );
			$this->assertGreaterThan( 0, $size[0] );
			$this->assertGreaterThan( 0, $size[1] );
			$this->assertTrue( $size[2], "$name should be hard-cropped for a consistent grid" );
		}
	}

	public function test_google_fonts_url_requests_the_approved_type_stack(): void {
		// Real WP add_query_arg() deliberately does NOT urlencode values
		// (see wp-includes/functions.php: build_query() is called with
		// $urlencode = false) — that's what lets this URL keep its
		// literal `:`, `;`, `|` and `+` characters, matching Google
		// Fonts' expected css2 query syntax exactly.
		Functions\when( 'add_query_arg' )->alias( function ( $args, $url ) {
			$pairs = array();
			foreach ( $args as $key => $value ) {
				$pairs[] = $key . '=' . $value;
			}
			return $url . '?' . implode( '&', $pairs );
		} );

		$url = \maz_heights_google_fonts_url();

		$this->assertStringStartsWith( 'https://fonts.googleapis.com/css2', $url );
		$this->assertStringContainsString( 'Archivo', $url );
		$this->assertStringContainsString( 'IBM+Plex+Sans', $url );
		$this->assertStringContainsString( 'IBM+Plex+Mono', $url );
	}
}
