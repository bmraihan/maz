<?php
/**
 * Tests for inc/meta-boxes.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

require_once __DIR__ . '/TestCase.php';

final class MetaBoxesTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		require_once dirname( __DIR__, 2 ) . '/inc/post-types.php';
		require_once dirname( __DIR__, 2 ) . '/inc/meta-boxes.php';
	}

	public function test_control_type_matches_field_type(): void {
		$this->assertSame( 'checkbox', \maz_heights_meta_field_control( 'maz_featured', array( 'type' => 'boolean' ) ) );
		$this->assertSame( 'number', \maz_heights_meta_field_control( 'maz_price_from', array( 'type' => 'integer' ) ) );
		$this->assertSame( 'text', \maz_heights_meta_field_control( 'maz_location', array( 'type' => 'string' ) ) );
	}

	public function test_every_meta_key_has_a_human_label(): void {
		$labels = \maz_heights_meta_field_labels();

		foreach ( \maz_heights_meta_fields() as $fields ) {
			foreach ( array_keys( $fields ) as $meta_key ) {
				$this->assertArrayHasKey( $meta_key, $labels, "$meta_key has no admin label" );
			}
		}
	}

	public function test_sanitize_submitted_meta_only_returns_keys_present_in_request(): void {
		$submitted = \maz_heights_sanitize_submitted_meta( 'maz_service', array(
			'maz_price_from' => '45000',
		) );

		$this->assertSame( array( 'maz_price_from' => 45000 ), $submitted );
	}

	public function test_sanitize_submitted_meta_treats_missing_checkbox_as_false(): void {
		$submitted = \maz_heights_sanitize_submitted_meta( 'maz_project', array(
			'maz_location' => 'Earlsdon',
			// maz_featured / maz_before_after deliberately omitted, as a
			// real unchecked HTML checkbox would omit them from $_POST.
		) );

		$this->assertSame( 'Earlsdon', $submitted['maz_location'] );
		$this->assertFalse( $submitted['maz_featured'] );
		$this->assertFalse( $submitted['maz_before_after'] );
	}

	public function test_sanitize_submitted_meta_honours_checked_checkbox(): void {
		$submitted = \maz_heights_sanitize_submitted_meta( 'maz_project', array(
			'maz_featured' => '1',
		) );

		$this->assertTrue( $submitted['maz_featured'] );
	}

	public function test_sanitize_submitted_meta_applies_rating_clamp(): void {
		$submitted = \maz_heights_sanitize_submitted_meta( 'maz_testimonial', array(
			'maz_rating' => '9',
		) );

		$this->assertSame( 5, $submitted['maz_rating'] );
	}

	public function test_sanitize_submitted_meta_for_unknown_post_type_returns_empty(): void {
		$this->assertSame( array(), \maz_heights_sanitize_submitted_meta( 'post', array( 'maz_location' => 'x' ) ) );
	}
}
