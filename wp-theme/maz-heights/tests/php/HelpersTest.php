<?php
/**
 * Tests for inc/helpers.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

use Brain\Monkey\Functions;

require_once __DIR__ . '/TestCase.php';

final class HelpersTest extends TestCase {

	protected function setUp(): void {
		// Loaded inside setUp(), not at file scope: PHPUnit `require_once`s
		// every test file up front (to discover classes) before any
		// setUp() runs, and Brain Monkey's function shims only exist once
		// Monkey\setUp() has fired at least once — so requiring theme
		// files at file scope races Monkey's own bootstrap.
		parent::setUp();
		require_once dirname( __DIR__, 2 ) . '/inc/helpers.php';
	}

	public function test_business_defaults_has_expected_keys(): void {
		$defaults = \maz_heights_business_defaults();

		$this->assertSame(
			array( 'phone', 'hours', 'email', 'address', 'areas', 'rating_value', 'rating_count', 'years_trading', 'builds_done' ),
			array_keys( $defaults )
		);
		$this->assertSame( '024 7xxx xxxx', $defaults['phone'] );
	}

	public function test_business_info_falls_back_to_defaults_when_no_customizer_mods_set(): void {
		Functions\when( 'get_theme_mod' )->justReturn( false );

		$info = \maz_heights_business_info();

		$this->assertSame( \maz_heights_business_defaults(), $info );
	}

	public function test_business_info_overrides_defaults_with_string_mods(): void {
		Functions\when( 'get_theme_mod' )->alias( function ( $key ) {
			return 'maz_business_phone' === $key ? '01234 567890' : false;
		} );

		$info = \maz_heights_business_info();

		$this->assertSame( '01234 567890', $info['phone'] );
		$this->assertSame( \maz_heights_business_defaults()['hours'], $info['hours'] );
	}

	public function test_business_info_ignores_blank_mods(): void {
		Functions\when( 'get_theme_mod' )->alias( function ( $key ) {
			return 'maz_business_phone' === $key ? '   ' : false;
		} );

		$info = \maz_heights_business_info();

		$this->assertSame( \maz_heights_business_defaults()['phone'], $info['phone'] );
	}

	public function test_hero_defaults_has_no_image_and_matches_approved_copy(): void {
		$defaults = \maz_heights_hero_defaults();

		$this->assertSame( '', $defaults['image'] );
		$this->assertSame( 'Designed, drawn and built by one team.', $defaults['heading'] );
		$this->assertSame(
			array( 'image', 'eyebrow', 'heading', 'subtext', 'primary_label', 'secondary_label' ),
			array_keys( $defaults )
		);
	}

	public function test_hero_content_falls_back_to_defaults_when_nothing_saved(): void {
		Functions\when( 'get_theme_mod' )->justReturn( false );

		$this->assertSame( \maz_heights_hero_defaults(), \maz_heights_hero_content() );
	}

	public function test_hero_content_overrides_image_and_heading_independently(): void {
		Functions\when( 'get_theme_mod' )->alias( function ( $key ) {
			if ( 'maz_hero_image' === $key ) {
				return 'https://mazheights.test/wp-content/uploads/hero.jpg';
			}
			if ( 'maz_hero_heading' === $key ) {
				return 'A custom headline';
			}
			return false;
		} );

		$hero = \maz_heights_hero_content();

		$this->assertSame( 'https://mazheights.test/wp-content/uploads/hero.jpg', $hero['image'] );
		$this->assertSame( 'A custom headline', $hero['heading'] );
		$this->assertSame( \maz_heights_hero_defaults()['subtext'], $hero['subtext'] );
	}

	public function test_cta_band_defaults_has_no_image_and_matches_approved_copy(): void {
		$defaults = \maz_heights_cta_band_defaults();

		$this->assertSame( '', $defaults['image'] );
		$this->assertSame(
			array( 'image', 'heading', 'subtext', 'primary_label' ),
			array_keys( $defaults )
		);
	}

	public function test_cta_band_content_overrides_defaults_with_string_mods(): void {
		Functions\when( 'get_theme_mod' )->alias( function ( $key ) {
			return 'maz_cta_heading' === $key ? 'Book now' : false;
		} );

		$cta = \maz_heights_cta_band_content();

		$this->assertSame( 'Book now', $cta['heading'] );
		$this->assertSame( \maz_heights_cta_band_defaults()['subtext'], $cta['subtext'] );
	}

	public function test_theme_mod_overrides_ignores_non_string_mods(): void {
		Functions\when( 'get_theme_mod' )->justReturn( false );

		$result = \maz_heights_theme_mod_overrides( array( 'a' => 'default-a' ), 'prefix_' );

		$this->assertSame( array( 'a' => 'default-a' ), $result );
	}

	public function test_format_price_from_formats_positive_amount(): void {
		$this->assertSame( 'FROM £45,000', \maz_heights_format_price_from( 45000 ) );
	}

	public function test_format_price_from_accepts_numeric_strings(): void {
		$this->assertSame( 'FROM £6,000', \maz_heights_format_price_from( '6000' ) );
	}

	public function test_format_price_from_returns_empty_string_for_zero(): void {
		$this->assertSame( '', \maz_heights_format_price_from( 0 ) );
	}

	public function test_format_price_from_treats_negative_as_its_absolute_value(): void {
		// Matches WP's real absint(): abs(intval($n)), not a clamp-to-zero.
		// In practice maz_price_from is already run through the absint
		// sanitize_callback before it's ever saved, so this function only
		// ever sees a non-negative value in production.
		$this->assertSame( 'FROM £500', \maz_heights_format_price_from( -500 ) );
	}

	public function test_star_rating_text_fills_and_pads(): void {
		$this->assertSame( '★★★☆☆', \maz_heights_star_rating_text( 3 ) );
		$this->assertSame( '★★★★★', \maz_heights_star_rating_text( 5 ) );
		$this->assertSame( '☆☆☆☆☆', \maz_heights_star_rating_text( 0 ) );
	}

	public function test_star_rating_text_clamps_out_of_range_values(): void {
		$this->assertSame( '★★★★★', \maz_heights_star_rating_text( 9 ) );
		$this->assertSame( '☆☆☆☆☆', \maz_heights_star_rating_text( -3 ) );
	}

	public function test_truncate_leaves_short_text_untouched(): void {
		$this->assertSame( 'A short blurb.', \maz_heights_truncate( 'A short blurb.', 180 ) );
	}

	public function test_truncate_cuts_long_text_on_word_boundary_with_ellipsis(): void {
		$text = 'One two three four five six seven eight nine ten eleven twelve';
		$result = \maz_heights_truncate( $text, 20 );

		$this->assertLessThanOrEqual( 21, mb_strlen( $result ) );
		$this->assertStringEndsWith( '…', $result );
		$this->assertStringNotContainsString( '  ', $result );
	}
}
