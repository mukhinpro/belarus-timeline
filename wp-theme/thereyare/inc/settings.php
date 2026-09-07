<?php
/**
 * Studio settings.
 *
 * One place for the facts that appear both on the page and inside the JSON-LD
 * schema: name, phone, email, socials, service area. Editable under
 * Appearance → Editor → Styles is not the right home for data, so these live in
 * the Customizer, which block themes still expose.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field definitions: id => [ label, default, control type ].
 */
function thereyare_setting_fields(): array {
	return [
		'thereyare_studio_name' => [ 'Studio name', '', 'text' ],
		'thereyare_photographer' => [ 'Lead photographer — full name', '', 'text' ],
		'thereyare_phone'       => [ 'Phone', '', 'text' ],
		'thereyare_email'       => [ 'Email', '', 'text' ],
		'thereyare_instagram'   => [ 'Instagram URL', '', 'url' ],
		'thereyare_street'      => [ 'Street address (office)', '', 'text' ],
		'thereyare_postcode'    => [ 'Postal code', '', 'text' ],
		'thereyare_city'        => [ 'Base city', 'Los Angeles', 'text' ],
		'thereyare_region'      => [ 'State code', 'CA', 'text' ],
		'thereyare_areas'       => [ 'Areas served (comma separated)', 'Los Angeles, Santa Monica, Malibu, Pasadena, Orange County', 'text' ],
		'thereyare_price_range' => [ 'Price range (for Google)', '$$', 'text' ],
		'thereyare_price_from'  => [ 'Lowest session price, digits only', '450', 'text' ],
		'thereyare_founded'     => [ 'Year founded', '', 'text' ],
	];
}

/**
 * Read one studio setting, falling back to the site title for the name.
 */
function thereyare_setting( string $key, string $fallback = '' ): string {
	$fields  = thereyare_setting_fields();
	$default = $fields[ $key ][1] ?? '';

	if ( 'thereyare_studio_name' === $key && '' === $default ) {
		$default = get_bloginfo( 'name' );
	}

	$value = (string) get_theme_mod( $key, $default );

	return '' !== $value ? $value : $fallback;
}

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'thereyare_studio',
			[
				'title'       => __( 'Studio details', 'thereyare' ),
				'priority'    => 20,
				'description' => __( 'Used across the site and sent to Google as structured data.', 'thereyare' ),
			]
		);

		foreach ( thereyare_setting_fields() as $id => [ $label, $default, $type ] ) {
			$wp_customize->add_setting(
				$id,
				[
					'default'           => $default,
					'sanitize_callback' => 'url' === $type ? 'esc_url_raw' : 'sanitize_text_field',
					'transport'         => 'refresh',
				]
			);
			$wp_customize->add_control(
				$id,
				[
					'label'   => $label,
					'section' => 'thereyare_studio',
					'type'    => $type,
				]
			);
		}
	}
);
