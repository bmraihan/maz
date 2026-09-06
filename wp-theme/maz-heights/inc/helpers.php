<?php
/**
 * Small presentation helpers used across templates.
 *
 * Kept as pure functions (given inputs, or given a WordPress accessor
 * that's easy to mock) so behaviour like price formatting and star
 * rendering can be unit tested without rendering a full page.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default business details, used until the client fills in the
 * Customizer (Appearance → Customize → Business Details) with real ones.
 *
 * @return array<string,string>
 */
function maz_heights_business_defaults() {
	return array(
		'phone'          => '024 7xxx xxxx',
		'hours'          => 'MON–SAT 8–6',
		'email'          => 'hello@mazheights.co.uk',
		'address'        => 'Unit x, Address, Coventry CVx xXX',
		'areas'          => 'Coventry · Kenilworth · Leamington · Warwickshire',
		'rating_value'   => '4.9',
		'rating_count'   => '60',
		'years_trading'  => '12',
		'builds_done'    => '40+',
	);
}

/**
 * Default hero content, used until the client sets their own in the
 * Customizer (Appearance → Customize → Homepage Hero). No image by
 * default — the approved design's hero is a solid graphite panel, not
 * a photo, so leaving this blank preserves that look exactly.
 *
 * @return array<string,string>
 */
function maz_heights_hero_defaults() {
	return array(
		'image'            => '',
		'eyebrow'          => 'EXTENSIONS · KITCHENS · BATHROOMS',
		'heading'          => 'Designed, drawn and built by one team.',
		'subtext'          => 'We handle the design, the drawings, the planning and the build — so you deal with one company from first sketch to final certificate. Fixed written price before anyone lifts a tool.',
		'primary_label'    => 'Book a free survey',
		'secondary_label'  => 'See recent builds',
	);
}

/**
 * Default CTA band content (the brick-orange band above the footer),
 * used until the client sets their own in the Customizer (Appearance →
 * Customize → CTA Band). No image by default, matching the approved
 * design's flat brick-orange background.
 *
 * @return array<string,string>
 */
function maz_heights_cta_band_defaults() {
	return array(
		'image'          => '',
		'heading'        => "Planning an extension for next spring? Book the survey now.",
		'subtext'        => 'Design and planning take eight to twelve weeks before a spade goes in the ground.',
		'primary_label'  => 'Book a free survey',
	);
}

/**
 * Overlay any non-empty Customizer theme mods (named `{$prefix}{key}`)
 * onto a defaults array — the shared merge behind maz_heights_business_info(),
 * maz_heights_hero_content() and maz_heights_cta_band_content(), so
 * "nothing saved yet" and "saved but blanked out" both fall back to the
 * approved design copy instead of rendering an empty string.
 *
 * @param array<string,string> $defaults Default values, keyed the same as the theme mod suffix.
 * @param string               $prefix   Theme mod name prefix, e.g. 'maz_hero_'.
 * @return array<string,string>
 */
function maz_heights_theme_mod_overrides( array $defaults, $prefix ) {
	$content = $defaults;

	foreach ( array_keys( $content ) as $key ) {
		$mod = get_theme_mod( $prefix . $key );
		if ( is_string( $mod ) && '' !== trim( $mod ) ) {
			$content[ $key ] = $mod;
		}
	}

	return $content;
}

/**
 * Merge the Customizer's saved business details over the defaults.
 *
 * @return array<string,string>
 */
function maz_heights_business_info() {
	return maz_heights_theme_mod_overrides( maz_heights_business_defaults(), 'maz_business_' );
}

/**
 * Merge the Customizer's saved hero content over the defaults.
 *
 * @return array<string,string>
 */
function maz_heights_hero_content() {
	return maz_heights_theme_mod_overrides( maz_heights_hero_defaults(), 'maz_hero_' );
}

/**
 * Merge the Customizer's saved CTA band content over the defaults.
 *
 * @return array<string,string>
 */
function maz_heights_cta_band_content() {
	return maz_heights_theme_mod_overrides( maz_heights_cta_band_defaults(), 'maz_cta_' );
}

/**
 * Format a whole-pound integer as a "FROM £x,xxx" label.
 *
 * @param int|string $amount Whole pounds, e.g. 45000.
 * @return string Empty string if amount is not a positive number.
 */
function maz_heights_format_price_from( $amount ) {
	$amount = absint( $amount );

	if ( $amount <= 0 ) {
		return '';
	}

	return sprintf(
		/* translators: %s: formatted price, e.g. "45,000". */
		__( 'FROM £%s', 'maz-heights' ),
		number_format_i18n( $amount )
	);
}

/**
 * Build a "★★★★★" style rating string, filled stars first.
 *
 * @param int $rating 1-5.
 * @return string
 */
function maz_heights_star_rating_text( $rating ) {
	$rating = max( 0, min( 5, (int) $rating ) );

	return str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating );
}

/**
 * Trim a string to a maximum character length on a word boundary,
 * appending an ellipsis if it was truncated. Used for card blurbs
 * pulled from post_content so a very long editor entry can't blow out
 * the fixed-height service/project cards.
 *
 * @param string $text      Source text (already the plain excerpt, no markup).
 * @param int    $max_chars Maximum length before truncating.
 * @return string
 */
function maz_heights_truncate( $text, $max_chars = 180 ) {
	$text = trim( (string) $text );

	if ( mb_strlen( $text ) <= $max_chars ) {
		return $text;
	}

	$truncated = mb_substr( $text, 0, $max_chars );
	$last_space = mb_strrpos( $truncated, ' ' );

	if ( false !== $last_space ) {
		$truncated = mb_substr( $truncated, 0, $last_space );
	}

	return rtrim( $truncated, " \t\n\r\0\x0B.,;:" ) . '…';
}
