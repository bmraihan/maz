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
}
