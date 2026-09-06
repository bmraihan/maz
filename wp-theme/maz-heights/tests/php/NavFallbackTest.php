<?php
/**
 * Tests for inc/nav-fallback.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

use Brain\Monkey\Functions;

require_once __DIR__ . '/TestCase.php';

final class NavFallbackTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/' );
		require_once dirname( __DIR__, 2 ) . '/inc/nav-fallback.php';
	}

	public function test_fallback_nav_has_six_items(): void {
		$this->assertCount( 6, \maz_heights_fallback_nav_items() );
	}

	public function test_extensions_kitchens_bathrooms_all_point_at_services_section(): void {
		$items = \maz_heights_fallback_nav_items();

		$this->assertSame( 'https://mazheights.test/#services', $items['Extensions'] );
		$this->assertSame( 'https://mazheights.test/#services', $items['Kitchens'] );
		$this->assertSame( 'https://mazheights.test/#services', $items['Bathrooms'] );
	}

	public function test_our_work_process_and_prices_have_distinct_anchors(): void {
		$items = \maz_heights_fallback_nav_items();

		$this->assertSame( 'https://mazheights.test/#work', $items['Our work'] );
		$this->assertSame( 'https://mazheights.test/#process', $items['Process'] );
		$this->assertSame( 'https://mazheights.test/#prices', $items['Prices'] );
	}

	public function test_fallback_nav_menu_renders_a_link_per_item(): void {
		ob_start();
		\maz_heights_fallback_nav_menu();
		$html = ob_get_clean();

		$this->assertSame( 6, substr_count( $html, '<a href=' ) );
		$this->assertStringContainsString( 'Extensions', $html );
	}
}
