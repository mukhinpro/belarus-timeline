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
function newbrand_setting_fields(): array {
	return [
		'newbrand_studio_name' => [ 'Studio name', '', 'text' ],
		'newbrand_phone'       => [ 'Phone', '', 'text' ],
		'newbrand_email'       => [ 'Email', '', 'text' ],
		'newbrand_instagram'   => [ 'Instagram URL', '', 'url' ],
		'newbrand_city'        => [ 'Base city', 'Los Angeles', 'text' ],
		'newbrand_region'      => [ 'State code', 'CA', 'text' ],
		'newbrand_areas'       => [ 'Areas served (comma separated)', 'Los Angeles, Santa Monica, Malibu, Pasadena, Orange County', 'text' ],
		'newbrand_price_range' => [ 'Price range (for Google)', '$$', 'text' ],
		'newbrand_price_from'  => [ 'Lowest session price, digits only', '450', 'text' ],
		'newbrand_founded'     => [ 'Year founded', '', 'text' ],
	];
}

/**
 * Read one studio setting, falling back to the site title for the name.
 */
function newbrand_setting( string $key, string $fallback = '' ): string {
	$fields  = newbrand_setting_fields();
	$default = $fields[ $key ][1] ?? '';

	if ( 'newbrand_studio_name' === $key && '' === $default ) {
		$default = get_bloginfo( 'name' );
	}

	$value = (string) get_theme_mod( $key, $default );

	return '' !== $value ? $value : $fallback;
}

add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'newbrand_studio',
			[
				'title'       => __( 'Studio details', 'newbrand' ),
				'priority'    => 20,
				'description' => __( 'Used across the site and sent to Google as structured data.', 'newbrand' ),
			]
		);

		foreach ( newbrand_setting_fields() as $id => [ $label, $default, $type ] ) {
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
					'section' => 'newbrand_studio',
					'type'    => $type,
				]
			);
		}
	}
);
