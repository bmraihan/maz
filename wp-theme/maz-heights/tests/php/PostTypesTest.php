<?php
/**
 * Tests for inc/post-types.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

require_once __DIR__ . '/TestCase.php';

final class PostTypesTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		require_once dirname( __DIR__, 2 ) . '/inc/post-types.php';
	}

	public function test_service_cpt_args_are_public_with_expected_slug(): void {
		$args = \maz_heights_service_cpt_args();

		$this->assertTrue( $args['public'] );
		$this->assertSame( 'services', $args['rewrite']['slug'] );
		$this->assertContains( 'thumbnail', $args['supports'] );
	}

	public function test_project_cpt_has_archive_enabled(): void {
		$args = \maz_heights_project_cpt_args();

		$this->assertTrue( $args['has_archive'] );
		$this->assertSame( 'our-work', $args['rewrite']['slug'] );
	}

	public function test_testimonial_cpt_has_no_archive(): void {
		$args = \maz_heights_testimonial_cpt_args();

		$this->assertFalse( $args['has_archive'] );
	}

	public function test_every_meta_field_has_a_callable_sanitize_callback(): void {
		foreach ( \maz_heights_meta_fields() as $post_type => $fields ) {
			foreach ( $fields as $meta_key => $args ) {
				$this->assertArrayHasKey(
					'sanitize_callback',
					$args,
					"$post_type.$meta_key is missing a sanitize_callback"
				);
				$this->assertTrue(
					is_callable( $args['sanitize_callback'] ),
					"$post_type.$meta_key's sanitize_callback is not callable"
				);
				$this->assertTrue( $args['show_in_rest'] ?? false );
			}
		}
	}

	public function test_meta_fields_cover_the_three_post_types_with_expected_field_counts(): void {
		$fields = \maz_heights_meta_fields();

		$this->assertSame( array( 'maz_service', 'maz_project', 'maz_testimonial' ), array_keys( $fields ) );
		$this->assertCount( 3, $fields['maz_service'] );
		$this->assertCount( 7, $fields['maz_project'] );
		$this->assertCount( 5, $fields['maz_testimonial'] );
	}

	/**
	 * @dataProvider ratingProvider
	 */
	public function test_sanitize_rating_clamps_to_1_through_5( $input, $expected ): void {
		$this->assertSame( $expected, \maz_heights_sanitize_rating( $input ) );
	}

	public function ratingProvider(): array {
		return array(
			'mid value'        => array( 3, 3 ),
			'zero clamps up'   => array( 0, 1 ),
			'negative clamps'  => array( -4, 1 ),
			'above max clamps' => array( 7, 5 ),
			'string numeric'   => array( '4', 4 ),
			'rounds'           => array( 2.6, 3 ),
		);
	}

	public function test_sanitize_price_label_trims_whitespace(): void {
		$this->assertSame( '£78,000', \maz_heights_sanitize_price_label( '  £78,000  ' ) );
	}

	/**
	 * @dataProvider boolProvider
	 */
	public function test_sanitize_bool_recognises_truthy_and_falsy_inputs( $input, $expected ): void {
		$this->assertSame( $expected, \maz_heights_sanitize_bool( $input ) );
	}

	public function boolProvider(): array {
		return array(
			'true'          => array( true, true ),
			'one int'       => array( 1, true ),
			'one string'    => array( '1', true ),
			'on'            => array( 'on', true ),
			'yes'           => array( 'yes', true ),
			'false'         => array( false, false ),
			'empty string'  => array( '', false ),
			'zero'          => array( '0', false ),
			'unrelated str' => array( 'maybe', false ),
			'null'          => array( null, false ),
		);
	}
}
