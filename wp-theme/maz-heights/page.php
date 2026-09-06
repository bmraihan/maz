<?php
/**
 * Default template for any other WordPress Page (Privacy, Terms, etc.)
 * that isn't the front page or a service landing page.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article class="basic-page">
		<div class="wrap">
			<h1><?php the_title(); ?></h1>
			<div class="basic-page__content"><?php the_content(); ?></div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
