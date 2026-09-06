<?php
/**
 * MAZ Heights theme bootstrap.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAZ_HEIGHTS_VERSION', '1.0.0' );
define( 'MAZ_HEIGHTS_DIR', get_template_directory() );
define( 'MAZ_HEIGHTS_URI', get_template_directory_uri() );

require_once MAZ_HEIGHTS_DIR . '/inc/helpers.php';
require_once MAZ_HEIGHTS_DIR . '/inc/setup.php';
require_once MAZ_HEIGHTS_DIR . '/inc/customizer.php';
require_once MAZ_HEIGHTS_DIR . '/inc/post-types.php';
require_once MAZ_HEIGHTS_DIR . '/inc/meta-boxes.php';
require_once MAZ_HEIGHTS_DIR . '/inc/seed-content.php';
require_once MAZ_HEIGHTS_DIR . '/inc/forms.php';
require_once MAZ_HEIGHTS_DIR . '/inc/nav-fallback.php';
require_once MAZ_HEIGHTS_DIR . '/inc/template-tags.php';
