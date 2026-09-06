<?php
/**
 * Customizer settings: business details (header/footer/hero stats),
 * the homepage hero panel, and the CTA band shared by the homepage,
 * case studies and service pages. Lets the client update contact
 * details, the hero photo/headline/buttons, and the CTA band's
 * photo/heading, all from Appearance → Customize without editing
 * template code.
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
 * Field definitions for the Homepage Hero Customizer section.
 *
 * Mirrors maz_heights_hero_defaults() keys (inc/helpers.php). The
 * `control` key picks the Customizer control type — 'image', 'text'
 * or 'textarea' — which maz_heights_customizer_sanitize_callback()
 * also uses to pick the matching sanitizer, so a field can't end up
 * with a control and a sanitizer that disagree.
 *
 * @return array<string,array{label:string,description:string,control:string}>
 */
function maz_heights_hero_customizer_fields() {
	return array(
		'image'           => array(
			'label'       => __( 'Background photo', 'maz-heights' ),
			'description' => __( 'Optional. Replaces the plain graphite background with a photo; a dark overlay is applied automatically so the white text stays readable.', 'maz-heights' ),
			'control'     => 'image',
		),
		'eyebrow'         => array(
			'label'       => __( 'Eyebrow label', 'maz-heights' ),
			'description' => __( 'Small label above the headline.', 'maz-heights' ),
			'control'     => 'text',
		),
		'heading'         => array(
			'label'       => __( 'Headline', 'maz-heights' ),
			'description' => __( 'Main hero heading.', 'maz-heights' ),
			'control'     => 'text',
		),
		'subtext'         => array(
			'label'       => __( 'Subtext', 'maz-heights' ),
			'description' => __( 'Paragraph under the headline.', 'maz-heights' ),
			'control'     => 'textarea',
		),
		'primary_label'   => array(
			'label'       => __( 'Primary button label', 'maz-heights' ),
			'description' => __( 'Links to the quote form.', 'maz-heights' ),
			'control'     => 'text',
		),
		'secondary_label' => array(
			'label'       => __( 'Secondary button label', 'maz-heights' ),
			'description' => __( 'Links to the recent work section.', 'maz-heights' ),
			'control'     => 'text',
		),
	);
}

/**
 * Field definitions for the CTA Band Customizer section.
 *
 * Mirrors maz_heights_cta_band_defaults() keys (inc/helpers.php). This
 * band is shared by the homepage, case studies and service landing
 * pages (all three call maz_heights_render_cta_band() with no
 * arguments), so a change here shows up everywhere it appears.
 *
 * @return array<string,array{label:string,description:string,control:string}>
 */
function maz_heights_cta_band_customizer_fields() {
	return array(
		'image'         => array(
			'label'       => __( 'Background photo', 'maz-heights' ),
			'description' => __( 'Optional. Replaces the flat brick-orange background with a photo; a brick-coloured overlay is applied automatically so the band still reads as the same accent colour.', 'maz-heights' ),
			'control'     => 'image',
		),
		'heading'       => array(
			'label'       => __( 'Heading', 'maz-heights' ),
			'description' => __( 'Shown on the homepage, every case study and every service page.', 'maz-heights' ),
			'control'     => 'text',
		),
		'subtext'       => array(
			'label'       => __( 'Subtext', 'maz-heights' ),
			'description' => __( 'Supporting sentence under the heading.', 'maz-heights' ),
			'control'     => 'textarea',
		),
		'primary_label' => array(
			'label'       => __( 'Button label', 'maz-heights' ),
			'description' => __( 'The second button always shows the phone number from Business Details.', 'maz-heights' ),
			'control'     => 'text',
		),
	);
}

/**
 * The sanitize callback that matches a Customizer control type.
 *
 * @param string $control One of 'image', 'textarea', 'text'.
 * @return callable
 */
function maz_heights_customizer_sanitize_callback( $control ) {
	switch ( $control ) {
		case 'image':
			return 'esc_url_raw';
		case 'textarea':
			return 'sanitize_textarea_field';
		default:
			return 'sanitize_text_field';
	}
}

/**
 * Register one setting + control per field into a Customizer section.
 *
 * Shared by the Business Details, Homepage Hero and CTA Band sections
 * so each is just a field list plus a defaults array, rather than
 * three near-identical registration loops.
 *
 * @param WP_Customize_Manager        $wp_customize Customizer manager instance.
 * @param string                      $section_id   Section to add controls to.
 * @param string                      $prefix       Setting name prefix, e.g. 'maz_hero_'.
 * @param array<string,array>         $fields       Field definitions (label/description/control).
 * @param array<string,string>        $defaults     Default values, keyed the same as $fields.
 */
function maz_heights_add_customizer_fields( $wp_customize, $section_id, $prefix, array $fields, array $defaults ) {
	foreach ( $fields as $key => $field ) {
		$setting_id = $prefix . $key;
		$control    = $field['control'] ?? 'text';

		$wp_customize->add_setting( $setting_id, array(
			'default'           => $defaults[ $key ] ?? '',
			'sanitize_callback' => maz_heights_customizer_sanitize_callback( $control ),
			'transport'         => 'refresh',
		) );

		$wp_customize->add_control( $setting_id, array(
			'section'     => $section_id,
			'label'       => $field['label'],
			'description' => $field['description'],
			'type'        => $control,
		) );
	}
}

/**
 * Register the Business Details, Homepage Hero and CTA Band sections.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function maz_heights_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'maz_heights_business', array(
		'title'    => __( 'Business Details', 'maz-heights' ),
		'priority' => 30,
	) );
	maz_heights_add_customizer_fields(
		$wp_customize,
		'maz_heights_business',
		'maz_business_',
		maz_heights_customizer_fields(),
		maz_heights_business_defaults()
	);

	$wp_customize->add_section( 'maz_heights_hero', array(
		'title'       => __( 'Homepage Hero', 'maz-heights' ),
		'priority'    => 31,
		'description' => __( 'The dark panel at the top of the homepage.', 'maz-heights' ),
	) );
	maz_heights_add_customizer_fields(
		$wp_customize,
		'maz_heights_hero',
		'maz_hero_',
		maz_heights_hero_customizer_fields(),
		maz_heights_hero_defaults()
	);

	$wp_customize->add_section( 'maz_heights_cta_band', array(
		'title'       => __( 'CTA Band', 'maz-heights' ),
		'priority'    => 32,
		'description' => __( 'The brick-orange call-to-action band on the homepage, case studies and service pages.', 'maz-heights' ),
	) );
	maz_heights_add_customizer_fields(
		$wp_customize,
		'maz_heights_cta_band',
		'maz_cta_',
		maz_heights_cta_band_customizer_fields(),
		maz_heights_cta_band_defaults()
	);
}
add_action( 'customize_register', 'maz_heights_customize_register' );
