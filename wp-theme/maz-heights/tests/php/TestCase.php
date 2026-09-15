<?php
/**
 * Shared Brain Monkey test case: boots/tears down Monkey's function
 * interception per test and stubs the small set of WordPress functions
 * almost every theme function touches (translation/escaping/sanitizing),
 * so individual test files only need to stub the WP calls specific to
 * what they're testing.
 *
 * @package MazHeights\Tests
 */

namespace MazHeights\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase {

	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
		$this->stubCommonWpFunctions();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	protected function stubCommonWpFunctions(): void {
		Functions\stubs( array(
			// Theme files call these at file scope to register hooks; the
			// test suite never fires WordPress's action/filter system, so
			// they just need to be safe no-ops for `require` to succeed.
			'add_action'         => true,
			'add_filter'         => true,
			'__'                 => function ( $text ) {
				return $text;
			},
			'esc_html__'         => function ( $text ) {
				return $text;
			},
			'esc_attr__'         => function ( $text ) {
				return $text;
			},
			'esc_html_e'         => function ( $text ) {
				echo $text;
			},
			'esc_attr_e'         => function ( $text ) {
				echo $text;
			},
			'esc_html'           => function ( $text ) {
				return $text;
			},
			'esc_attr'           => function ( $text ) {
				return $text;
			},
			'esc_url'            => function ( $text ) {
				return $text;
			},
			'esc_url_raw'        => function ( $text ) {
				return $text;
			},
			'sanitize_text_field' => function ( $text ) {
				return trim( is_string( $text ) ? $text : (string) $text );
			},
			'sanitize_title'     => function ( $text ) {
				$text = strtolower( (string) $text );
				$text = preg_replace( '/[^a-z0-9]+/', '-', $text );
				return trim( $text, '-' );
			},
			'absint'             => function ( $number ) {
				return abs( (int) $number );
			},
			'apply_filters'      => function ( $tag, $value ) {
				return $value;
			},
			'wp_parse_url'       => function ( $url, $component = -1 ) {
				return -1 === $component ? parse_url( $url ) : parse_url( $url, $component );
			},
			'number_format_i18n' => function ( $number ) {
				return number_format( (float) $number );
			},
			'wp_specialchars_decode' => function ( $text ) {
				return html_entity_decode( (string) $text, ENT_QUOTES, 'UTF-8' );
			},
			// Real WP_Error objects don't exist in this test environment;
			// tests that need a failure case pass a plain object instead,
			// so treating "is it an object at all" as "is it an error" is
			// sufficient here — every real success value theme code checks
			// with is_wp_error() is a scalar (an ID, a bool, etc.).
			'is_wp_error'            => function ( $thing ) {
				return is_object( $thing );
			},
		) );
	}
}
