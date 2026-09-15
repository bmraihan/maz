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

	/**
	 * Regression test: the enqueued stylesheet/script version was a
	 * hard-coded constant that never got bumped across several real
	 * CSS/JS changes, so browsers (and any host/CDN cache) kept serving
	 * a stale cached copy at the same URL indefinitely — a reported
	 * CSS fix that looked correct in the repo but never visibly landed
	 * for site visitors. Using the file's own mtime means every actual
	 * content change changes the enqueued `?ver=`, with nothing to
	 * remember to bump by hand.
	 */
	public function test_asset_version_matches_the_files_actual_mtime(): void {
		$relative_path = '/tests/php/bootstrap.php';

		$this->assertSame(
			(string) filemtime( MAZ_HEIGHTS_DIR . $relative_path ),
			\maz_heights_asset_version( $relative_path )
		);
	}

	public function test_asset_version_changes_when_the_file_changes(): void {
		$tmp_relative = '/tests/php/.tmp-asset-version-test.css';
		$tmp_path     = MAZ_HEIGHTS_DIR . $tmp_relative;

		file_put_contents( $tmp_path, 'body{}' );
		touch( $tmp_path, time() - 100 );
		$before = \maz_heights_asset_version( $tmp_relative );

		touch( $tmp_path, time() );
		$after = \maz_heights_asset_version( $tmp_relative );

		unlink( $tmp_path );

		$this->assertNotSame( $before, $after );
	}

	public function test_asset_version_falls_back_to_the_version_constant_when_file_missing(): void {
		$this->assertSame(
			MAZ_HEIGHTS_VERSION,
			\maz_heights_asset_version( '/assets/css/this-file-does-not-exist.css' )
		);
	}
}
