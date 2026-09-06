<?php
/** Site settings live in Appearance → Customize → "Studio settings" — no code edits needed. */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_section( 'saturdayla_studio', [ 'title' => 'Studio settings', 'priority' => 20 ] );

	$fields = [
		'saturdayla_owner'      => [ 'Studio name', 'SaturdayLa', 'text' ],
		'saturdayla_phone'      => [ 'Phone', '+1 (747) 217-9834', 'text' ],
		'saturdayla_email'      => [ 'Email', 'hello@saturdayla.com', 'text' ],
		'saturdayla_instagram'  => [ 'Instagram URL', 'https://www.instagram.com/saturdayla/', 'url' ],
		'saturdayla_city'       => [ 'Base city', 'Los Angeles', 'text' ],
		'saturdayla_areas'      => [ 'Areas served (comma separated)', 'Los Angeles, Santa Monica, Malibu, Pasadena, Orange County', 'text' ],
		'saturdayla_price_range'=> [ 'Price range (for Google)', '$$', 'text' ],
		'saturdayla_tagline'    => [ 'Line under the name in the header', 'Kids · Family · Parties · Los Angeles', 'text' ],
		'saturdayla_hero_eyebrow' => [ 'Home hero small label', 'Kids & family photography studio · Los Angeles', 'text' ],
		'saturdayla_hero_title' => [ 'Home hero title', "You'll be in the photos too, for once.", 'text' ],
		'saturdayla_hero_text'  => [ 'Home hero text', "Family sessions, kids' portraits and birthday parties — photographed so you can put your phone down and actually be there.", 'textarea' ],
		'saturdayla_hero_note'  => [ 'Home hero note next to the button', 'Usually booked 3–4 weeks out.', 'text' ],
		'saturdayla_press'      => [ 'Published in (shown in About block, leave blank to hide)', '', 'text' ],
		'saturdayla_form'       => [ 'Booking form shortcode (e.g. [contact-form-7 id="12"])', '', 'text' ],
	];
	foreach ( $fields as $id => [ $label, $default, $type ] ) {
		$wp_customize->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => 'url' === $type ? 'esc_url_raw' : 'wp_kses_post' ] );
		$wp_customize->add_control( $id, [ 'label' => $label, 'section' => 'saturdayla_studio', 'type' => $type ] );
	}

	$images = [
		'saturdayla_hero_image'      => 'Home hero photo (portrait 4:5, 1600px+ wide)',
		'saturdayla_about_image'     => 'Team photo (portrait)',
		'saturdayla_img_family'      => 'Session tile: Family (portrait)',
		'saturdayla_img_kids'        => 'Session tile: Kids & teens (portrait)',
		'saturdayla_img_party'       => 'Session tile: Birthday parties (portrait)',
		'saturdayla_img_wedding'     => 'Session tile: Weddings (portrait)',
	];
	foreach ( $images as $id => $label ) {
		$wp_customize->add_setting( $id, [ 'sanitize_callback' => 'absint' ] );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $id, [ 'label' => $label, 'section' => 'saturdayla_studio', 'mime_type' => 'image' ] ) );
	}
} );

/** Turn *word* into <em>word</em> for the hero title */
function saturdayla_hero_title_html() {
	$t = get_theme_mod( 'saturdayla_hero_title', 'Perfect light. *Real* emotion.' );
	return wp_kses( preg_replace( '/\*(.+?)\*/', '<em>$1</em>', esc_html( $t ) ), [ 'em' => [] ] );
}

/** Render a customizer image or a placeholder, at a size */
function saturdayla_img( $mod, $size = 'saturdayla-portrait', $alt = '', $attrs = [], $label = '' ) {
	$id = get_theme_mod( $mod );
	$inner = $id ? wp_get_attachment_image( $id, $size, false, array_merge( [ 'alt' => $alt ], $attrs ) ) : '<span class="ph-label">' . esc_html( $label ?: 'Add photo in Customizer → Studio settings' ) . '</span>';
	return '<div class="ph">' . $inner . '</div>';
}
