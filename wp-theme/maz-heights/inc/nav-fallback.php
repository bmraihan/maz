<?php
/**
 * Fallback primary navigation, used until a menu is assigned at
 * Appearance → Menus → Primary Navigation. Matches the anchor links
 * from the approved design (Extensions/Kitchens/Bathrooms all point at
 * the homepage services section, since there is one combined section
 * for all three rather than three separate anchors).
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The fallback nav's (label => href) pairs.
 *
 * Pure data so the default link set is unit testable independent of
 * wp_nav_menu()'s HTML wrapping.
 *
 * @return array<string,string>
 */
function maz_heights_fallback_nav_items() {
	$home = home_url( '/' );

	return array(
		__( 'Extensions', 'maz-heights' ) => $home . '#services',
		__( 'Kitchens', 'maz-heights' )   => $home . '#services',
		__( 'Bathrooms', 'maz-heights' )  => $home . '#services',
		__( 'Our work', 'maz-heights' )   => $home . '#work',
		__( 'Process', 'maz-heights' )    => $home . '#process',
		__( 'Prices', 'maz-heights' )     => $home . '#prices',
	);
}

/**
 * Render the fallback nav when no menu is assigned to the `primary`
 * location, passed to wp_nav_menu()'s `fallback_cb`.
 */
function maz_heights_fallback_nav_menu() {
	echo '<div class="site-nav__links">';
	foreach ( maz_heights_fallback_nav_items() as $label => $href ) {
		printf( '<a href="%1$s">%2$s</a>', esc_url( $href ), esc_html( $label ) );
	}
	echo '</div>';
}
