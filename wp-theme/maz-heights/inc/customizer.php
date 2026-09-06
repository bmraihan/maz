<?php
/**
 * Customizer: business details shown in the header, hero stats, footer
 * and CTA band. Lets the client update their phone number, hours, email,
 * address and Google rating without editing template code.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field definitions for the Business Details Customizer section.
 *
 * Pure lookup (mirrors maz_heights_business_defaults() keys) so the
 * registration loop and its test stay in lockstep with helpers.php.
 *
 * @return array<string,array{label:string,description:string}>
 */
function maz_heights_customizer_fields() {
	return array(
		'phone'         => array(
			'label'       => __( 'Phone number', 'maz-heights' ),
			'description' => __( 'Shown in the header, hero form footnote and footer.', 'maz-heights' ),
		),
		'hours'         => array(
			'label'       => __( 'Opening hours', 'maz-heights' ),
			'description' => __( 'Shown in the top utility bar and header.', 'maz-heights' ),
		),
		'email'         => array(
			'label'       => __( 'Contact email', 'maz-heights' ),
			'description' => __( 'Shown in the footer.', 'maz-heights' ),
		),
		'address'       => array(
			'label'       => __( 'Postal address', 'maz-heights' ),
			'description' => __( 'Shown in the footer.', 'maz-heights' ),
		),
		'areas'         => array(
			'label'       => __( 'Areas covered', 'maz-heights' ),
			'description' => __( 'Shown in the top utility bar.', 'maz-heights' ),
		),
		'rating_value'  => array(
			'label'       => __( 'Google rating (e.g. 4.9)', 'maz-heights' ),
			'description' => __( 'Shown in the hero stats and reviews heading.', 'maz-heights' ),
		),
		'rating_count'  => array(
			'label'       => __( 'Number of reviews', 'maz-heights' ),
			'description' => __( 'Shown next to the rating.', 'maz-heights' ),
		),
		'years_trading' => array(
			'label'       => __( 'Years trading', 'maz-heights' ),
			'description' => __( 'Shown in the hero stats.', 'maz-heights' ),
		),
		'builds_done'   => array(
			'label'       => __( 'Builds completed', 'maz-heights' ),
			'description' => __( 'Shown in the hero stats.', 'maz-heights' ),
		),
	);
}

/**
 * Register the Business Details section, one setting/control per field.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function maz_heights_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'maz_heights_business', array(
		'title'    => __( 'Business Details', 'maz-heights' ),
		'priority' => 30,
	) );

	$defaults = maz_heights_business_defaults();

	foreach ( maz_heights_customizer_fields() as $key => $field ) {
		$setting_id = 'maz_business_' . $key;

		$wp_customize->add_setting( $setting_id, array(
			'default'           => $defaults[ $key ] ?? '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( $setting_id, array(
			'section'     => 'maz_heights_business',
			'label'       => $field['label'],
			'description' => $field['description'],
			'type'        => 'text',
		) );
	}
}
add_action( 'customize_register', 'maz_heights_customize_register' );
