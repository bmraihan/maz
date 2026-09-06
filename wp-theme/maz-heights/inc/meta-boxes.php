<?php
/**
 * Classic (no plugin dependency) meta boxes for the theme's custom post
 * types, backed by the field definitions in maz_heights_meta_fields().
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MAZ_HEIGHTS_META_NONCE', 'maz_heights_meta_nonce' );
define( 'MAZ_HEIGHTS_META_NONCE_ACTION', 'maz_heights_save_meta' );

/**
 * Human labels for each meta key, used as the field label in the admin UI.
 *
 * Kept separate from maz_heights_meta_fields() (which is the sanitize/REST
 * contract) so this file can be about presentation only.
 *
 * @return array<string,string>
 */
function maz_heights_meta_field_labels() {
	return array(
		'maz_price_from'   => __( 'Price from (£, whole pounds, e.g. 45000)', 'maz-heights' ),
		'maz_cta_label'     => __( 'Card link label (e.g. "Extensions")', 'maz-heights' ),
		'maz_category_match' => __( 'Matching project category (e.g. "Extension") for related work on this service\'s landing page', 'maz-heights' ),
		'maz_location'      => __( 'Location (e.g. "Earlsdon, CV5")', 'maz-heights' ),
		'maz_category'      => __( 'Category (e.g. "Extension")', 'maz-heights' ),
		'maz_duration'      => __( 'Duration on site (e.g. "14 weeks")', 'maz-heights' ),
		'maz_area_added'    => __( 'Area added (e.g. "42 m²")', 'maz-heights' ),
		'maz_price_label'   => __( 'Price label (e.g. "£78,000")', 'maz-heights' ),
		'maz_featured'      => __( 'Feature as the large "recent work" card', 'maz-heights' ),
		'maz_before_after'  => __( 'Show "BEFORE / AFTER" badge', 'maz-heights' ),
		'maz_rating'        => __( 'Star rating (1-5)', 'maz-heights' ),
		'maz_author'        => __( 'Customer name(s)', 'maz-heights' ),
		'maz_job_type'      => __( 'Job type (e.g. "Extension")', 'maz-heights' ),
		'maz_source'        => __( 'Review source (e.g. "Google")', 'maz-heights' ),
	);
}

/**
 * Field type used to pick the admin control: text, number or checkbox.
 *
 * @param string $meta_key Meta key.
 * @param array  $field_args register_post_meta args for that key.
 * @return string One of 'text', 'number', 'checkbox'.
 */
function maz_heights_meta_field_control( $meta_key, $field_args ) {
	if ( 'boolean' === ( $field_args['type'] ?? '' ) ) {
		return 'checkbox';
	}

	if ( 'integer' === ( $field_args['type'] ?? '' ) ) {
		return 'number';
	}

	return 'text';
}

/**
 * Register one meta box per post type that has meta fields.
 */
function maz_heights_add_meta_boxes() {
	foreach ( maz_heights_meta_fields() as $post_type => $fields ) {
		add_meta_box(
			'maz-heights-' . $post_type . '-details',
			__( 'MAZ Heights details', 'maz-heights' ),
			'maz_heights_render_meta_box',
			$post_type,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'maz_heights_add_meta_boxes' );

/**
 * Render the fields for the current post's post type.
 *
 * @param WP_Post $post Current post.
 */
function maz_heights_render_meta_box( $post ) {
	$all_fields = maz_heights_meta_fields();
	$fields     = $all_fields[ $post->post_type ] ?? array();
	$labels     = maz_heights_meta_field_labels();

	wp_nonce_field( MAZ_HEIGHTS_META_NONCE_ACTION, MAZ_HEIGHTS_META_NONCE );

	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $fields as $meta_key => $field_args ) {
		$value   = get_post_meta( $post->ID, $meta_key, true );
		$control = maz_heights_meta_field_control( $meta_key, $field_args );
		$label   = $labels[ $meta_key ] ?? $meta_key;

		echo '<tr><th><label for="' . esc_attr( $meta_key ) . '">' . esc_html( $label ) . '</label></th><td>';

		if ( 'checkbox' === $control ) {
			printf(
				'<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s />',
				esc_attr( $meta_key ),
				checked( maz_heights_sanitize_bool( $value ), true, false )
			);
		} elseif ( 'number' === $control ) {
			printf(
				'<input type="number" min="0" id="%1$s" name="%1$s" value="%2$s" class="regular-text" />',
				esc_attr( $meta_key ),
				esc_attr( $value )
			);
		} else {
			printf(
				'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" />',
				esc_attr( $meta_key ),
				esc_attr( $value )
			);
		}

		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Pick submitted meta values for a post type out of a request-shaped
 * array, applying each field's sanitize callback.
 *
 * Pure (no superglobal access, no DB writes) so it's unit tested directly
 * against fixed input arrays in tests/php/MetaBoxesTest.php; the save
 * hook below is the only place that touches $_POST.
 *
 * @param string $post_type Post type slug.
 * @param array  $request   Request-shaped array (e.g. $_POST).
 * @return array<string,mixed> meta_key => sanitized value, only for keys present in $request.
 */
function maz_heights_sanitize_submitted_meta( $post_type, array $request ) {
	$all_fields = maz_heights_meta_fields();
	$fields     = $all_fields[ $post_type ] ?? array();
	$sanitized  = array();

	foreach ( $fields as $meta_key => $field_args ) {
		if ( 'boolean' === ( $field_args['type'] ?? '' ) ) {
			// Checkboxes are absent from $_POST entirely when unchecked.
			$sanitized[ $meta_key ] = call_user_func( $field_args['sanitize_callback'], $request[ $meta_key ] ?? false );
			continue;
		}

		if ( ! array_key_exists( $meta_key, $request ) ) {
			continue;
		}

		$sanitized[ $meta_key ] = call_user_func( $field_args['sanitize_callback'], $request[ $meta_key ] );
	}

	return $sanitized;
}

/**
 * Save meta box fields on post save.
 *
 * @param int $post_id Post ID.
 */
function maz_heights_save_meta_boxes( $post_id ) {
	if ( ! isset( $_POST[ MAZ_HEIGHTS_META_NONCE ] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ MAZ_HEIGHTS_META_NONCE ] ) ), MAZ_HEIGHTS_META_NONCE_ACTION ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	if ( ! $post_type || ! array_key_exists( $post_type, maz_heights_meta_fields() ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above.
	$submitted = wp_unslash( $_POST );
	$sanitized = maz_heights_sanitize_submitted_meta( $post_type, $submitted );

	foreach ( $sanitized as $meta_key => $value ) {
		update_post_meta( $post_id, $meta_key, $value );
	}
}
add_action( 'save_post', 'maz_heights_save_meta_boxes' );
