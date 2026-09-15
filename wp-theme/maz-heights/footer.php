<?php
/**
 * Shared footer.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$maz_business = maz_heights_business_info();
$maz_footer_services = maz_heights_get_services();
?>
</main>

<footer class="site-footer">
	<div class="wrap site-footer__cols">
		<div class="footer-col footer-col--brand">
			<div class="footer-brand">
				<svg viewBox="0 0 48 48" width="34" height="34" aria-hidden="true">
					<path d="M9 29.5 L24 14.5 L39 29.5" stroke="#FFFFFF" stroke-width="4.2" fill="none" stroke-linecap="square"></path>
					<path d="M14.5 40 L24 30.5 L33.5 40" stroke="#B4552A" stroke-width="4.2" fill="none" stroke-linecap="square"></path>
				</svg>
				<span class="footer-brand__name"><?php bloginfo( 'name' ); ?></span>
			</div>
			<p><?php esc_html_e( 'Design-and-build contractor for extensions, new builds, roofing, landscaping and more across Coventry and Warwickshire.', 'maz-heights' ); ?></p>
		</div>

		<div class="footer-col">
			<div class="footer-col__heading"><?php esc_html_e( 'Services', 'maz-heights' ); ?></div>
			<?php foreach ( $maz_footer_services as $maz_footer_service ) : ?>
				<a href="<?php echo esc_url( maz_heights_service_landing_page_url( $maz_footer_service->post_title ) ); ?>"><?php echo esc_html( $maz_footer_service->post_title ); ?></a>
			<?php endforeach; ?>
		</div>

		<div class="footer-col">
			<div class="footer-col__heading"><?php esc_html_e( 'Areas Covered', 'maz-heights' ); ?></div>
			<div class="footer-col__text"><?php echo esc_html( $maz_business['areas'] ); ?></div>
		</div>

		<div class="footer-col">
			<div class="footer-col__heading"><?php esc_html_e( 'Contact', 'maz-heights' ); ?></div>
			<div class="footer-col__phone"><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $maz_business['phone'] ) ); ?>"><?php echo esc_html( $maz_business['phone'] ); ?></a></div>
			<div class="footer-col__text">
				<a href="mailto:<?php echo esc_attr( $maz_business['email'] ); ?>"><?php echo esc_html( $maz_business['email'] ); ?></a><br>
				<?php echo esc_html( $maz_business['address'] ); ?><br>
				<?php echo esc_html( $maz_business['hours'] ); ?>
			</div>
		</div>

		<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
			<?php dynamic_sidebar( 'footer-1' ); ?>
		<?php endif; ?>
	</div>

	<div class="wrap site-footer__legal">
		<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?> LTD · <?php esc_html_e( 'REGISTERED IN ENGLAND', 'maz-heights' ); ?></span>
		<span><?php esc_html_e( 'PRIVACY · TERMS · COMPLAINTS PROCEDURE', 'maz-heights' ); ?></span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
