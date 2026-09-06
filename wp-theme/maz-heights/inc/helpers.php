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
 * Merge the Customizer's saved business details over the defaults.
 *
 * @return array<string,string>
 */
function maz_heights_business_info() {
	$info = maz_heights_business_defaults();

	foreach ( array_keys( $info ) as $key ) {
		$mod = get_theme_mod( 'maz_business_' . $key );
		if ( is_string( $mod ) && '' !== trim( $mod ) ) {
			$info[ $key ] = $mod;
		}
	}

	return $info;
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
