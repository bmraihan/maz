<?php
/**
 * Custom post types: service, project (recent work / case studies), testimonial.
 *
 * These exist so a non-developer can edit the homepage's service cards,
 * recent-work list and reviews from wp-admin without touching code —
 * the scalability point of building this in WordPress rather than as
 * static markup.
 *
 * Registration args are returned by pure `*_args()` functions so tests
 * can assert on them without a full WordPress boot. See
 * tests/php/PostTypesTest.php.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Args for the `maz_service` post type (Extensions / Kitchens / Bathrooms).
 *
 * @return array
 */
function maz_heights_service_cpt_args() {
	return array(
		'label'        => __( 'Services', 'maz-heights' ),
		'labels'       => array(
			'name'          => __( 'Services', 'maz-heights' ),
			'singular_name' => __( 'Service', 'maz-heights' ),
			'add_new_item'  => __( 'Add New Service', 'maz-heights' ),
			'edit_item'     => __( 'Edit Service', 'maz-heights' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'services' ),
		'menu_icon'    => 'dashicons-hammer',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		'show_in_rest' => true,
	);
}

/**
 * Args for the `maz_project` post type (recent work / case studies).
 *
 * @return array
 */
function maz_heights_project_cpt_args() {
	return array(
		'label'        => __( 'Projects', 'maz-heights' ),
		'labels'       => array(
			'name'          => __( 'Projects', 'maz-heights' ),
			'singular_name' => __( 'Project', 'maz-heights' ),
			'add_new_item'  => __( 'Add New Project', 'maz-heights' ),
			'edit_item'     => __( 'Edit Project', 'maz-heights' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'our-work' ),
		'menu_icon'    => 'dashicons-camera',
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
		'show_in_rest' => true,
	);
}

/**
 * Args for the `maz_testimonial` post type (customer reviews).
 *
 * @return array
 */
function maz_heights_testimonial_cpt_args() {
	return array(
		'label'        => __( 'Testimonials', 'maz-heights' ),
		'labels'       => array(
			'name'          => __( 'Testimonials', 'maz-heights' ),
			'singular_name' => __( 'Testimonial', 'maz-heights' ),
			'add_new_item'  => __( 'Add New Testimonial', 'maz-heights' ),
			'edit_item'     => __( 'Edit Testimonial', 'maz-heights' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'testimonials' ),
		'menu_icon'    => 'dashicons-star-filled',
		'supports'     => array( 'title', 'editor' ),
		'show_in_rest' => true,
	);
}

/**
 * Meta field definitions per post type: meta_key => register_post_meta args.
 *
 * Centralised so post-types.php and meta-boxes.php stay in sync, and so
 * tests can assert every meta key has a sanitize_callback (nothing gets
 * saved unsanitized).
 *
 * @return array<string,array<string,array>>
 */
function maz_heights_meta_fields() {
	return array(
		'maz_service'     => array(
			'maz_price_from' => array(
				'type'              => 'integer',
				'single'            => true,
				'sanitize_callback' => 'absint',
				'show_in_rest'      => true,
			),
			'maz_cta_label'  => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'maz_category_match' => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
				'description'       => 'Project maz_category value this service\'s landing page should pull related work from (e.g. "Extension" for the "Extensions" service).',
			),
		),
		'maz_project'     => array(
			'maz_location'     => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'maz_category'     => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'maz_duration'     => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'maz_area_added'   => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'maz_price_label'  => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'maz_heights_sanitize_price_label',
				'show_in_rest'      => true,
			),
			'maz_featured'     => array(
				'type'              => 'boolean',
				'single'            => true,
				'sanitize_callback' => 'maz_heights_sanitize_bool',
				'show_in_rest'      => true,
			),
			'maz_before_after' => array(
				'type'              => 'boolean',
				'single'            => true,
				'sanitize_callback' => 'maz_heights_sanitize_bool',
				'show_in_rest'      => true,
			),
		),
		'maz_testimonial' => array(
			'maz_rating'   => array(
				'type'              => 'integer',
				'single'            => true,
				'sanitize_callback' => 'maz_heights_sanitize_rating',
				'show_in_rest'      => true,
			),
			'maz_author'   => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'maz_location' => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'maz_job_type' => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
			'maz_source'   => array(
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'show_in_rest'      => true,
			),
		),
	);
}

/**
 * Clamp an arbitrary value to a 1-5 star rating.
 *
 * @param mixed $value Raw value.
 * @return int
 */
function maz_heights_sanitize_rating( $value ) {
	$rating = (int) round( (float) $value );

	return max( 1, min( 5, $rating ) );
}

/**
 * Sanitize a free-text price label (e.g. "£78,000", "FROM £45,000").
 *
 * Plain sanitize_text_field() plus a trim so stray whitespace pasted
 * from a quote/estimate spreadsheet doesn't linger.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function maz_heights_sanitize_price_label( $value ) {
	return trim( sanitize_text_field( (string) $value ) );
}

/**
 * Normalise any truthy/falsy input (checkbox posts as '1'/'', REST posts
 * real booleans) to a strict boolean.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function maz_heights_sanitize_bool( $value ) {
	return in_array( $value, array( true, 1, '1', 'true', 'on', 'yes' ), true );
}

/**
 * Register the custom post types.
 */
function maz_heights_register_post_types() {
	register_post_type( 'maz_service', maz_heights_service_cpt_args() );
	register_post_type( 'maz_project', maz_heights_project_cpt_args() );
	register_post_type( 'maz_testimonial', maz_heights_testimonial_cpt_args() );
}
add_action( 'init', 'maz_heights_register_post_types' );

/**
 * Register post meta for each CPT so it's exposed to REST/Gutenberg and
 * always passes through its sanitize callback, including on direct
 * `update_post_meta()` calls from our own code.
 */
function maz_heights_register_post_meta() {
	foreach ( maz_heights_meta_fields() as $post_type => $fields ) {
		foreach ( $fields as $meta_key => $args ) {
			register_post_meta( $post_type, $meta_key, $args );
		}
	}
}
add_action( 'init', 'maz_heights_register_post_meta' );
