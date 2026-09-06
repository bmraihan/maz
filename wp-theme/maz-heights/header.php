<?php
/**
 * Shared header: top utility bar + sticky nav.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$maz_business = maz_heights_business_info();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'maz-heights' ); ?></a>

<div class="utility-bar">
	<div class="wrap utility-bar__inner">
		<div><?php echo esc_html( $maz_business['areas'] ); ?></div>
		<div class="utility-bar__badges">
			<?php foreach ( maz_heights_trust_badges() as $badge ) : ?>
				<span><?php echo esc_html( $badge ); ?></span>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<header class="site-header">
	<div class="wrap site-header__inner">
		<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<svg class="site-brand__mark" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true">
					<rect width="48" height="48" fill="#101820"></rect>
					<path d="M9 29.5 L24 14.5 L39 29.5" stroke="#FFFFFF" stroke-width="4.2" fill="none" stroke-linecap="square"></path>
					<path d="M14.5 40 L24 30.5 L33.5 40" stroke="#B4552A" stroke-width="4.2" fill="none" stroke-linecap="square"></path>
				</svg>
				<span class="site-brand__text">
					<span class="site-brand__name"><?php bloginfo( 'name' ); ?></span>
					<span class="site-brand__tagline"><?php esc_html_e( 'DESIGN & BUILD · COVENTRY', 'maz-heights' ); ?></span>
				</span>
			<?php endif; ?>
		</a>

		<nav class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'maz-heights' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '<div class="site-nav__links">%3$s</div>',
				'fallback_cb'    => 'maz_heights_fallback_nav_menu',
			) );
			?>
		</nav>

		<div class="site-header__contact">
			<div class="site-header__hours-phone">
				<div class="site-header__hours"><?php echo esc_html( $maz_business['hours'] ); ?></div>
				<div class="site-header__phone"><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $maz_business['phone'] ) ); ?>"><?php echo esc_html( $maz_business['phone'] ); ?></a></div>
			</div>
			<a class="btn btn--brick" href="<?php echo esc_url( home_url( '/#quote' ) ); ?>"><?php esc_html_e( 'Free quote', 'maz-heights' ); ?></a>
		</div>
	</div>
</header>

<main id="main">
