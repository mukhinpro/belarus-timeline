<?php
/**
 * Studio details.
 *
 * One place for the facts that appear in schema, the footer and the billing
 * links, so none of them has to be hunted down in template markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, array{default: string, label: string, help?: string}>
 */
function md_settings_fields(): array {
	return [
		'md_photographer' => [ 'default' => 'Marc Demure', 'label' => 'Photographer name' ],
		'md_phone'        => [ 'default' => '', 'label' => 'Phone' ],
		'md_email'        => [ 'default' => '', 'label' => 'Email' ],
		'md_instagram'    => [ 'default' => 'https://www.instagram.com/marc.demure/', 'label' => 'Instagram URL' ],
		'md_city'         => [ 'default' => 'Los Angeles', 'label' => 'City' ],
		'md_region'       => [ 'default' => 'CA', 'label' => 'State' ],
		'md_areas'        => [
			'default' => 'Los Angeles, West Hollywood, Santa Monica, Downtown LA, Silver Lake, Pasadena',
			'label'   => 'Areas served, comma separated',
		],
		'md_price_from'   => [ 'default' => '650', 'label' => 'Lowest session price, digits only' ],
		'md_founded'      => [ 'default' => '', 'label' => 'Year founded' ],
		'md_booking_url'  => [ 'default' => '', 'label' => 'Scheduling link (Calendly or Acuity)', 'help' => 'The event or appointment link. Set up deposit collection inside that tool.' ],
		'md_google_reviews' => [ 'default' => '', 'label' => 'Google reviews link', 'help' => 'The “write a review” link from your Google Business Profile.' ],
	];
}

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_section(
			'md_studio',
			[
				'title'       => __( 'Studio details', 'marcdemure' ),
				'priority'    => 20,
				'description' => __( 'Used by the footer and by the structured data Google reads.', 'marcdemure' ),
			]
		);

		foreach ( md_settings_fields() as $key => $field ) {
			$wp_customize->add_setting(
				$key,
				[
					'default'           => $field['default'],
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => 'refresh',
				]
			);
			$wp_customize->add_control(
				$key,
				[
					'label'       => $field['label'],
					'section'     => 'md_studio',
					'type'        => 'text',
					'description' => $field['help'] ?? '',
				]
			);
		}
	}
);

/**
 * Read a studio setting, falling back to its registered default.
 */
function md_setting( string $key ): string {
	$fields  = md_settings_fields();
	$default = $fields[ $key ]['default'] ?? '';
	return trim( (string) get_theme_mod( $key, $default ) );
}
