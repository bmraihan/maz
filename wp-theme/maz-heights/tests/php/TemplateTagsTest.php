<?php
/**
 * Tests for inc/template-tags.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

use Brain\Monkey\Functions;

require_once __DIR__ . '/TestCase.php';

final class TemplateTagsTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		require_once dirname( __DIR__, 2 ) . '/inc/helpers.php';
		require_once dirname( __DIR__, 2 ) . '/inc/template-tags.php';
	}

	public function test_placeholder_svg_contains_escaped_label(): void {
		$svg = \maz_heights_placeholder_svg( 'PHOTO · REAR EXTENSION' );

		$this->assertStringStartsWith( '<svg', $svg );
		$this->assertStringContainsString( 'viewBox="0 0 400 300"', $svg );
		$this->assertStringContainsString( 'PHOTO', $svg );
	}

	public function test_placeholder_svg_escapes_markup_in_label(): void {
		$svg = \maz_heights_placeholder_svg( '<script>alert(1)</script>' );

		$this->assertStringNotContainsString( '<script>', $svg );
		$this->assertStringContainsString( '&lt;script&gt;', $svg );
	}

	public function test_placeholder_data_uri_is_a_valid_data_uri(): void {
		$uri = \maz_heights_placeholder_data_uri( 'PHOTO · KITCHEN' );

		$this->assertStringStartsWith( 'data:image/svg+xml,', $uri );
		$this->assertStringContainsString( 'KITCHEN', rawurldecode( $uri ) );
	}

	public function test_service_matches_slug_is_case_and_punctuation_insensitive(): void {
		$this->assertTrue( \maz_heights_service_matches_slug( 'Extensions', 'extensions' ) );
		$this->assertTrue( \maz_heights_service_matches_slug( 'Bathrooms & Wet Rooms', 'bathrooms-wet-rooms' ) );
		$this->assertFalse( \maz_heights_service_matches_slug( 'Extensions', 'kitchens' ) );
	}

	public function test_price_guide_rows_has_six_rows_with_required_keys(): void {
		$rows = \maz_heights_price_guide_rows();

		$this->assertCount( 6, $rows );
		foreach ( $rows as $row ) {
			$this->assertArrayHasKey( 'job', $row );
			$this->assertArrayHasKey( 'size', $row );
			$this->assertArrayHasKey( 'cost', $row );
			$this->assertArrayHasKey( 'duration', $row );
		}
	}

	public function test_price_guide_rows_is_filterable(): void {
		Functions\when( 'apply_filters' )->alias( function ( $tag, $value ) {
			if ( 'maz_heights_price_guide_rows' === $tag ) {
				return array( array( 'job' => 'Custom', 'size' => '-', 'cost' => '-', 'duration' => '-' ) );
			}
			return $value;
		} );

		$rows = \maz_heights_price_guide_rows();

		$this->assertCount( 1, $rows );
		$this->assertSame( 'Custom', $rows[0]['job'] );
	}

	public function test_process_stages_are_five_and_sequentially_numbered(): void {
		$stages = \maz_heights_process_stages();

		$this->assertCount( 5, $stages );
		$this->assertSame( array( '01', '02', '03', '04', '05' ), array_column( $stages, 'number' ) );
	}

	public function test_trust_badges_and_accreditation_badges_are_non_empty(): void {
		$this->assertCount( 3, \maz_heights_trust_badges() );
		$this->assertCount( 5, \maz_heights_accreditation_badges() );
	}

	public function test_post_image_url_falls_back_to_placeholder_without_a_thumbnail(): void {
		Functions\when( 'has_post_thumbnail' )->justReturn( false );

		$url = \maz_heights_post_image_url( 42, 'maz-card', 'PHOTO · TEST' );

		$this->assertStringStartsWith( 'data:image/svg+xml,', $url );
	}

	public function test_post_image_url_uses_real_thumbnail_when_present(): void {
		Functions\when( 'has_post_thumbnail' )->justReturn( true );
		Functions\when( 'get_the_post_thumbnail_url' )->justReturn( 'https://example.test/photo.jpg' );

		$url = \maz_heights_post_image_url( 42, 'maz-card', 'PHOTO · TEST' );

		$this->assertSame( 'https://example.test/photo.jpg', $url );
	}

	public function test_cta_band_renders_default_copy_with_no_image_class_when_unset(): void {
		Functions\when( 'get_theme_mod' )->justReturn( false );
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/#quote' );

		ob_start();
		\maz_heights_render_cta_band();
		$html = ob_get_clean();

		$this->assertStringContainsString( 'class="cta-band"', $html );
		$this->assertStringNotContainsString( 'cta-band--has-image', $html );
		$this->assertStringNotContainsString( 'background-image', $html );
		$this->assertStringContainsString( 'Planning an extension for next spring? Book the survey now.', $html );
	}

	public function test_cta_band_renders_image_class_and_background_when_set(): void {
		Functions\when( 'get_theme_mod' )->alias( function ( $key ) {
			return 'maz_cta_image' === $key ? 'https://mazheights.test/wp-content/uploads/site.jpg' : false;
		} );
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/#quote' );

		ob_start();
		\maz_heights_render_cta_band();
		$html = ob_get_clean();

		$this->assertStringContainsString( 'cta-band--has-image', $html );
		$this->assertStringContainsString( "background-image:url('https://mazheights.test/wp-content/uploads/site.jpg')", $html );
	}

	public function test_cta_band_explicit_arguments_still_override_customizer_content(): void {
		Functions\when( 'get_theme_mod' )->justReturn( false );
		Functions\when( 'home_url' )->justReturn( 'https://mazheights.test/#quote' );

		ob_start();
		\maz_heights_render_cta_band( 'Custom heading', 'Custom body' );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'Custom heading', $html );
		$this->assertStringContainsString( 'Custom body', $html );
	}
}
