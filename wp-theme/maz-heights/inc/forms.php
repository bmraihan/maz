<?php
/**
 * "Get a fixed price" quote form: Customizer setting for the Formspree
 * endpoint, and the shared render function used by front-page.php.
 *
 * The form posts directly to Formspree (progressively enhanced by
 * assets/js/quote-form.js into an AJAX submit with inline success/error
 * state) so there is no custom backend endpoint or database to run —
 * per the "third-party form service" scope decision for this build.
 *
 * @package MazHeights
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validate/clean a Formspree endpoint URL before it's saved.
 *
 * Deliberately strict (https + formspree.io host) so a typo doesn't
 * silently start sending leads nowhere, or to some unrelated http(s)
 * endpoint pasted by mistake.
 *
 * @param string $value Raw Customizer input.
 * @return string Sanitized URL, or '' if it doesn't look like a Formspree endpoint.
 */
function maz_heights_sanitize_formspree_endpoint( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	$host = wp_parse_url( $value, PHP_URL_HOST );

	if ( 0 !== strpos( $value, 'https://' ) || ! $host || false === strpos( $host, 'formspree.io' ) ) {
		return '';
	}

	return esc_url_raw( $value );
}

/**
 * The configured Formspree endpoint, or '' if none has been set yet.
 *
 * @return string
 */
function maz_heights_form_endpoint() {
	return (string) get_theme_mod( 'maz_formspree_endpoint', '' );
}

/**
 * Whether the quote form has a real submission endpoint configured.
 *
 * @return bool
 */
function maz_heights_is_form_configured() {
	return '' !== maz_heights_form_endpoint();
}

/**
 * Register the Formspree endpoint Customizer control.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function maz_heights_form_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'maz_heights_integrations', array(
		'title'       => __( 'Quote Form', 'maz-heights' ),
		'priority'    => 35,
		'description' => __( 'Create a form at formspree.io, then paste its endpoint URL (e.g. https://formspree.io/f/abcdwxyz) here to make the homepage quote form live.', 'maz-heights' ),
	) );

	$wp_customize->add_setting( 'maz_formspree_endpoint', array(
		'default'           => '',
		'sanitize_callback' => 'maz_heights_sanitize_formspree_endpoint',
		'transport'         => 'refresh',
	) );

	$wp_customize->add_control( 'maz_formspree_endpoint', array(
		'section'     => 'maz_heights_integrations',
		'label'       => __( 'Formspree endpoint URL', 'maz-heights' ),
		'type'        => 'url',
	) );
}
add_action( 'customize_register', 'maz_heights_form_customize_register' );

/**
 * Dropdown options for the quote form's "what are you planning?" and
 * "budget" selects.
 *
 * Pure data so copy changes and ordering can be unit tested, and so the
 * template and any future admin-facing summary of leads share one list.
 *
 * @return array{project_types: string[], budgets: string[]}
 */
function maz_heights_quote_form_options() {
	return array(
		'project_types' => array(
			__( 'Extension', 'maz-heights' ),
			__( 'Kitchen', 'maz-heights' ),
			__( 'Bathroom / wet room', 'maz-heights' ),
			__( 'Garage or loft conversion', 'maz-heights' ),
			__( 'Not sure yet', 'maz-heights' ),
		),
		'budgets'       => array(
			__( 'Under £10,000', 'maz-heights' ),
			__( '£10,000 – £30,000', 'maz-heights' ),
			__( '£30,000 – £60,000', 'maz-heights' ),
			__( '£60,000 – £100,000', 'maz-heights' ),
			__( 'Over £100,000', 'maz-heights' ),
		),
	);
}

/**
 * Render the "Get a fixed price" quote form.
 *
 * Submits directly to the configured Formspree endpoint (a plain HTML
 * form works even with JS disabled); assets/js/quote-form.js upgrades
 * it to an inline AJAX submit with success/error messaging.
 */
function maz_heights_render_quote_form() {
	$configured = maz_heights_is_form_configured();
	$action     = $configured ? maz_heights_form_endpoint() : '#';
	$options    = maz_heights_quote_form_options();
	$business   = maz_heights_business_info();
	?>
	<form
		class="quote-form"
		id="quote-form"
		method="POST"
		action="<?php echo esc_url( $action ); ?>"
		data-configured="<?php echo $configured ? 'true' : 'false'; ?>"
		novalidate
	>
		<div class="quote-form__head">
			<div class="quote-form__title"><?php esc_html_e( 'Get a fixed price', 'maz-heights' ); ?></div>
			<div class="quote-form__subtitle"><?php esc_html_e( 'Survey booked within 5 working days. No obligation, no sales visit.', 'maz-heights' ); ?></div>
		</div>

		<div class="quote-form__row quote-form__row--2">
			<div class="quote-form__field">
				<label class="screen-reader-text" for="qf-name"><?php esc_html_e( 'Name', 'maz-heights' ); ?></label>
				<input type="text" id="qf-name" name="name" placeholder="<?php esc_attr_e( 'Name', 'maz-heights' ); ?>" autocomplete="name" required>
				<span class="quote-form__error" data-error-for="qf-name" role="alert"></span>
			</div>
			<div class="quote-form__field">
				<label class="screen-reader-text" for="qf-phone"><?php esc_html_e( 'Phone', 'maz-heights' ); ?></label>
				<input type="tel" id="qf-phone" name="phone" placeholder="<?php esc_attr_e( 'Phone', 'maz-heights' ); ?>" autocomplete="tel" required>
				<span class="quote-form__error" data-error-for="qf-phone" role="alert"></span>
			</div>
		</div>

		<div class="quote-form__field">
			<label class="screen-reader-text" for="qf-project"><?php esc_html_e( 'What are you planning?', 'maz-heights' ); ?></label>
			<select id="qf-project" name="project_type" required>
				<option value="" disabled selected hidden><?php esc_html_e( 'What are you planning?', 'maz-heights' ); ?></option>
				<?php foreach ( $options['project_types'] as $type ) : ?>
					<option value="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></option>
				<?php endforeach; ?>
			</select>
			<span class="quote-form__error" data-error-for="qf-project" role="alert"></span>
		</div>

		<div class="quote-form__row quote-form__row--2">
			<div class="quote-form__field">
				<label class="screen-reader-text" for="qf-postcode"><?php esc_html_e( 'Postcode', 'maz-heights' ); ?></label>
				<input type="text" id="qf-postcode" name="postcode" placeholder="<?php esc_attr_e( 'Postcode', 'maz-heights' ); ?>" autocomplete="postal-code" required>
				<span class="quote-form__error" data-error-for="qf-postcode" role="alert"></span>
			</div>
			<div class="quote-form__field">
				<label class="screen-reader-text" for="qf-budget"><?php esc_html_e( 'Budget', 'maz-heights' ); ?></label>
				<select id="qf-budget" name="budget">
					<option value=""><?php esc_html_e( 'Budget', 'maz-heights' ); ?></option>
					<?php foreach ( $options['budgets'] as $budget ) : ?>
						<option value="<?php echo esc_attr( $budget ); ?>"><?php echo esc_html( $budget ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="quote-form__field">
			<label class="screen-reader-text" for="qf-message"><?php esc_html_e( 'Anything else we should know?', 'maz-heights' ); ?></label>
			<textarea id="qf-message" name="message" rows="3" placeholder="<?php esc_attr_e( 'Anything else we should know?', 'maz-heights' ); ?>"></textarea>
		</div>

		<input type="text" name="_gotcha" class="quote-form__honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">
		<input type="hidden" name="_subject" value="New quote request — MAZ Heights website">

		<button type="submit" class="quote-form__submit">
			<span class="quote-form__submit-label"><?php esc_html_e( 'Request my free survey', 'maz-heights' ); ?></span>
		</button>

		<div class="quote-form__status" role="status" aria-live="polite" hidden></div>

		<div class="quote-form__footnote">
			<?php
			if ( $configured ) {
				esc_html_e( 'We reply the same working day · CV1–CV8 and surrounding', 'maz-heights' );
			} else {
				printf(
					/* translators: %s: phone number. */
					esc_html__( 'Form coming online shortly — call us on %s in the meantime.', 'maz-heights' ),
					esc_html( $business['phone'] )
				);
			}
			?>
		</div>
	</form>
	<?php
}
