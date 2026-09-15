<?php
/**
 * PHPUnit bootstrap for theme unit tests.
 *
 * These are pure unit tests against Brain Monkey WordPress-function
 * stubs — there is no WordPress core, database, or HTTP server
 * involved. That means they verify our own logic (sanitizers, data
 * shapes, template-tag calculations) in isolation; they do NOT verify
 * that WordPress actually renders the theme correctly end to end.
 * See the theme README's "Testing" section for what would be needed
 * to add that (a wp-env/Docker WordPress install + Playwright).
 */

require_once dirname( __DIR__, 2 ) . '/vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', sys_get_temp_dir() . '/' );
}

// WP core constant (wp-includes/wp-db.php) used as the default $output
// arg of several core lookup functions (e.g. get_page_by_path()) that
// theme code calls directly rather than through a Brain Monkey stub.
if ( ! defined( 'OBJECT' ) ) {
	define( 'OBJECT', 'OBJECT' );
}

// Normally defined in functions.php, which test files deliberately
// don't require (it pulls in the whole theme at once); defined here
// instead since inc/setup.php's maz_heights_asset_version() reads
// MAZ_HEIGHTS_DIR directly, and points at the real theme root so that
// function can be tested against real files (assets/css/style.css,
// this bootstrap file itself, etc.) rather than a fake path.
if ( ! defined( 'MAZ_HEIGHTS_DIR' ) ) {
	define( 'MAZ_HEIGHTS_DIR', dirname( __DIR__, 2 ) );
}
if ( ! defined( 'MAZ_HEIGHTS_VERSION' ) ) {
	define( 'MAZ_HEIGHTS_VERSION', '1.0.0' );
}
