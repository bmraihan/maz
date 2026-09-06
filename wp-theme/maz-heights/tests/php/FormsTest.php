<?php
/**
 * Tests for inc/forms.php.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

use Brain\Monkey\Functions;

require_once __DIR__ . '/TestCase.php';

final class FormsTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		require_once dirname( __DIR__, 2 ) . '/inc/forms.php';
	}

	public function test_sanitize_formspree_endpoint_accepts_a_real_formspree_url(): void {
		$this->assertSame(
			'https://formspree.io/f/abcdwxyz',
			\maz_heights_sanitize_formspree_endpoint( 'https://formspree.io/f/abcdwxyz' )
		);
	}

	public function test_sanitize_formspree_endpoint_rejects_http(): void {
		$this->assertSame( '', \maz_heights_sanitize_formspree_endpoint( 'http://formspree.io/f/abcdwxyz' ) );
	}

	public function test_sanitize_formspree_endpoint_rejects_other_hosts(): void {
		$this->assertSame( '', \maz_heights_sanitize_formspree_endpoint( 'https://evil.example.com/f/abcdwxyz' ) );
	}

	public function test_sanitize_formspree_endpoint_rejects_empty_string(): void {
		$this->assertSame( '', \maz_heights_sanitize_formspree_endpoint( '' ) );
	}

	public function test_sanitize_formspree_endpoint_rejects_garbage(): void {
		$this->assertSame( '', \maz_heights_sanitize_formspree_endpoint( 'not a url at all' ) );
	}

	public function test_form_endpoint_reads_theme_mod(): void {
		Functions\when( 'get_theme_mod' )->justReturn( 'https://formspree.io/f/abcdwxyz' );

		$this->assertSame( 'https://formspree.io/f/abcdwxyz', \maz_heights_form_endpoint() );
	}

	public function test_is_form_configured_false_when_endpoint_empty(): void {
		Functions\when( 'get_theme_mod' )->justReturn( '' );

		$this->assertFalse( \maz_heights_is_form_configured() );
	}

	public function test_is_form_configured_true_when_endpoint_set(): void {
		Functions\when( 'get_theme_mod' )->justReturn( 'https://formspree.io/f/abcdwxyz' );

		$this->assertTrue( \maz_heights_is_form_configured() );
	}

	public function test_quote_form_options_has_five_project_types_and_five_budgets(): void {
		$options = \maz_heights_quote_form_options();

		$this->assertCount( 5, $options['project_types'] );
		$this->assertCount( 5, $options['budgets'] );
		$this->assertContains( 'Extension', $options['project_types'] );
		$this->assertContains( 'Under £10,000', $options['budgets'] );
	}
}
