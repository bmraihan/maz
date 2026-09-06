<?php
/**
 * Tests for inc/customizer.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

require_once __DIR__ . '/TestCase.php';

final class CustomizerTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		require_once dirname( __DIR__, 2 ) . '/inc/helpers.php';
		require_once dirname( __DIR__, 2 ) . '/inc/customizer.php';
	}

	public function test_every_business_default_has_a_matching_customizer_field(): void {
		$defaults = \maz_heights_business_defaults();
		$fields   = \maz_heights_customizer_fields();

		$this->assertSame( array_keys( $defaults ), array_keys( $fields ) );
	}

	public function test_every_customizer_field_has_label_and_description(): void {
		foreach ( \maz_heights_customizer_fields() as $key => $field ) {
			$this->assertNotEmpty( $field['label'], "$key is missing a label" );
			$this->assertNotEmpty( $field['description'], "$key is missing a description" );
		}
	}

	public function test_every_hero_default_has_a_matching_customizer_field(): void {
		$this->assertSame(
			array_keys( \maz_heights_hero_defaults() ),
			array_keys( \maz_heights_hero_customizer_fields() )
		);
	}

	public function test_every_cta_band_default_has_a_matching_customizer_field(): void {
		$this->assertSame(
			array_keys( \maz_heights_cta_band_defaults() ),
			array_keys( \maz_heights_cta_band_customizer_fields() )
		);
	}

	public function test_hero_and_cta_band_fields_declare_a_valid_control_type(): void {
		$valid = array( 'image', 'text', 'textarea' );

		foreach ( array_merge( \maz_heights_hero_customizer_fields(), \maz_heights_cta_band_customizer_fields() ) as $key => $field ) {
			$this->assertContains( $field['control'], $valid, "$key has an unrecognised control type" );
		}
	}

	public function test_hero_and_cta_band_image_fields_use_the_image_control(): void {
		$this->assertSame( 'image', \maz_heights_hero_customizer_fields()['image']['control'] );
		$this->assertSame( 'image', \maz_heights_cta_band_customizer_fields()['image']['control'] );
	}

	/**
	 * @dataProvider sanitizeCallbackProvider
	 */
	public function test_sanitize_callback_matches_control_type( $control, $expected ): void {
		$this->assertSame( $expected, \maz_heights_customizer_sanitize_callback( $control ) );
	}

	public function sanitizeCallbackProvider(): array {
		return array(
			'image'          => array( 'image', 'esc_url_raw' ),
			'textarea'       => array( 'textarea', 'sanitize_textarea_field' ),
			'text'           => array( 'text', 'sanitize_text_field' ),
			'unknown falls back to text' => array( 'bogus', 'sanitize_text_field' ),
		);
	}
}
