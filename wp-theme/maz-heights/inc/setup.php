<?php
/**
 * Theme setup: supports, nav menus, asset enqueueing, image sizes.
 *
 * Functions here are kept small and side-effect-free where possible
 * (e.g. maz_heights_nav_menus(), maz_heights_google_fonts_url()) so they
 * can be unit tested without booting WordPress. See tests/php/SetupTest.php.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Nav menu locations registered by this theme.
 *
 * Pulled out as a pure function (no WP calls) so it's trivially unit
 * testable and so header.php / customizer code can share one source
 * of truth for location slugs.
 *
 * @return array<string,string> Location slug => human-readable label.
 */
function maz_heights_nav_menus() {
	return array(
		'primary' => __( 'Primary Navigation', 'maz-heights' ),
		'footer'  => __( 'Footer Navigation', 'maz-heights' ),
	);
}

/**
 * Image sizes registered by this theme.
 *
 * @return array<string,array{0:int,1:int,2:bool}> Size name => [width, height, crop].
 */
function maz_heights_image_sizes() {
	return array(
		'maz-hero'    => array( 1200, 900, true ),
		'maz-card'    => array( 800, 600, true ),
		'maz-project' => array( 1000, 563, true ),
		'maz-thumb'   => array( 240, 240, true ),
	);
}

/**
 * Google Fonts stylesheet URL used across every template.
 *
 * Kept as a pure function so the exact font stack (matching the
 * Claude Design handoff: Archivo + IBM Plex Sans + IBM Plex Mono)
 * can be asserted in a unit test rather than only eyeballed in a browser.
 *
 * @return string
 */
function maz_heights_google_fonts_url() {
	return add_query_arg(
		array(
			'family'  => 'Archivo:wght@500;600;700;800|IBM+Plex+Sans:wght@400;500;600|IBM+Plex+Mono:wght@400;500',
			'display' => 'swap',
		),
		'https://fonts.googleapis.com/css2'
	);
}

/**
 * Core theme supports + menu registration.
 */
function maz_heights_setup() {
	load_theme_textdomain( 'maz-heights', MAZ_HEIGHTS_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );

	foreach ( maz_heights_image_sizes() as $name => $size ) {
		add_image_size( $name, $size[0], $size[1], $size[2] );
	}

	foreach ( maz_heights_nav_menus() as $location => $label ) {
		register_nav_menu( $location, $label );
	}

	$GLOBALS['content_width'] = 1240;
}
add_action( 'after_setup_theme', 'maz_heights_setup' );

/**
 * Enqueue theme styles and scripts.
 */
function maz_heights_scripts() {
	wp_enqueue_style( 'maz-heights-fonts', maz_heights_google_fonts_url(), array(), null );
	wp_enqueue_style( 'maz-heights-style', MAZ_HEIGHTS_URI . '/assets/css/style.css', array(), MAZ_HEIGHTS_VERSION );

	wp_enqueue_script( 'maz-heights-quote-form', MAZ_HEIGHTS_URI . '/assets/js/quote-form.js', array(), MAZ_HEIGHTS_VERSION, true );
	wp_enqueue_script( 'maz-heights-main', MAZ_HEIGHTS_URI . '/assets/js/main.js', array( 'maz-heights-quote-form' ), MAZ_HEIGHTS_VERSION, true );

	wp_localize_script( 'maz-heights-quote-form', 'mazHeightsQuoteForm', array(
		'endpoint' => esc_url_raw( maz_heights_form_endpoint() ),
		'strings'  => array(
			'required'      => __( 'Please fill in this field.', 'maz-heights' ),
			'invalidPhone'  => __( 'Enter a valid UK phone number.', 'maz-heights' ),
			'invalidPost'   => __( 'Enter a valid UK postcode.', 'maz-heights' ),
			'sending'       => __( 'Sending…', 'maz-heights' ),
			'success'       => __( 'Thanks — we\'ll be in touch the same working day.', 'maz-heights' ),
			'error'         => __( 'Something went wrong. Please call us instead.', 'maz-heights' ),
			'notConfigured' => __( 'This form isn\'t connected yet — please call us instead.', 'maz-heights' ),
		),
	) );
}
add_action( 'wp_enqueue_scripts', 'maz_heights_scripts' );

/**
 * Flush rewrite rules once on activation so the maz_project archive
 * (`/our-work/`) and single case-study URLs work immediately, instead
 * of 404ing until someone happens to re-save Settings → Permalinks.
 *
 * Safe to run every activation: flushing is idempotent, and by the
 * time `after_switch_theme` fires during an activation request, `init`
 * (where the post types are registered) has already run earlier in
 * that same request, so the rules being flushed already include them.
 */
function maz_heights_flush_rewrites_on_activation() {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'maz_heights_flush_rewrites_on_activation' );

/**
 * Register the footer widget area.
 */
function maz_heights_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer', 'maz-heights' ),
		'id'            => 'footer-1',
		'description'   => __( 'Extra footer column (optional). The default footer already renders services, areas covered and contact details.', 'maz-heights' ),
		'before_widget' => '<div class="maz-footer__col">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="maz-footer__heading">',
		'after_title'   => '</div>',
	) );
}
add_action( 'widgets_init', 'maz_heights_widgets_init' );
