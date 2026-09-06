<?php
/**
 * Baseline SEO: JSON-LD schema (LocalBusiness + Photograph/ImageGallery + FAQ + Breadcrumb),
 * Open Graph fallbacks. Works standalone; if Rank Math / Yoast is active, their meta title/description
 * take over and this file only adds the business schema.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function saturdayla_business_schema() {
	$img = get_theme_mod( 'saturdayla_hero_image' );
	return [
		'@type'       => [ 'LocalBusiness', 'ProfessionalService' ],
		'@id'         => home_url( '/#business' ),
		'name'        => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'url'         => home_url( '/' ),
		'telephone'   => saturdayla_phone(),
		'email'       => saturdayla_email(),
		'image'       => $img ? wp_get_attachment_image_url( $img, 'saturdayla-hero' ) : '',
		'priceRange'  => get_theme_mod( 'saturdayla_price_range', '$$' ),
		'address'     => [
			'@type'           => 'PostalAddress',
			'addressLocality' => get_theme_mod( 'saturdayla_city', 'Los Angeles' ),
			'addressRegion'   => 'CA',
			'addressCountry'  => 'US',
		],
		'areaServed'  => array_map( fn( $a ) => [ '@type' => 'City', 'name' => trim( $a ) ],
			explode( ',', get_theme_mod( 'saturdayla_areas', 'Los Angeles, Santa Monica, Malibu, Pasadena, Orange County' ) ) ),
		'sameAs'      => array_filter( [ saturdayla_instagram() ] ),
		'makesOffer'  => array_map( fn( $s ) => [ '@type' => 'Offer', 'itemOffered' => [ '@type' => 'Service', 'name' => $s ] ],
			[ 'Family photography', 'Kids and teen portrait photography', 'Kids birthday party photography', 'Wedding photography', 'Editorial and kids model portfolio photography' ] ),
	];
}

add_action( 'wp_head', function () {
	$graph = [ saturdayla_business_schema() ];

	if ( is_front_page() ) {
		$graph[] = [
			'@type' => 'WebSite',
			'@id'   => home_url( '/#website' ),
			'url'   => home_url( '/' ),
			'name'  => get_bloginfo( 'name' ),
			'publisher' => [ '@id' => home_url( '/#business' ) ],
		];
	}

	// Breadcrumbs
	if ( ! is_front_page() && is_singular() ) {
		$items = [ [ '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ] ];
		$pos = 2;
		if ( is_singular( 'portfolio' ) ) $items[] = [ '@type' => 'ListItem', 'position' => $pos++, 'name' => 'Portfolio', 'item' => get_post_type_archive_link( 'portfolio' ) ];
		$items[] = [ '@type' => 'ListItem', 'position' => $pos, 'name' => get_the_title(), 'item' => get_permalink() ];
		$graph[] = [ '@type' => 'BreadcrumbList', 'itemListElement' => $items ];
	}

	// Service pages: FAQ schema from any <details> blocks in content (summary = question)
	if ( is_page() ) {
		$content = get_post_field( 'post_content', get_the_ID() );
		if ( preg_match_all( '#<summary>(.*?)</summary>\s*(?:<p>)?(.*?)(?:</p>)?\s*</details>#is', $content, $m ) ) {
			$faq = [];
			foreach ( $m[1] as $i => $q ) {
				$faq[] = [ '@type' => 'Question', 'name' => wp_strip_all_tags( $q ), 'acceptedAnswer' => [ '@type' => 'Answer', 'text' => wp_strip_all_tags( $m[2][ $i ] ) ] ];
			}
			if ( $faq ) $graph[] = [ '@type' => 'FAQPage', 'mainEntity' => $faq ];
		}
	}

	// Portfolio session: ImageGallery
	if ( is_singular( 'portfolio' ) ) {
		$imgs = saturdayla_session_images( get_the_ID() );
		$graph[] = [
			'@type' => 'ImageGallery',
			'name'  => get_the_title(),
			'url'   => get_permalink(),
			'image' => array_map( fn( $id ) => wp_get_attachment_image_url( $id, 'saturdayla-large' ), array_slice( $imgs, 0, 20 ) ),
			'author' => [ '@id' => home_url( '/#business' ) ],
			'contentLocation' => get_post_meta( get_the_ID(), 'saturdayla_location', true ) ?: 'Los Angeles, CA',
		];
	}

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( [ '@context' => 'https://schema.org', '@graph' => $graph ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 5 );

/** Open Graph fallback when no SEO plugin is active */
add_action( 'wp_head', function () {
	if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) return;
	$title = wp_get_document_title();
	$desc  = is_singular() && has_excerpt() ? get_the_excerpt() : get_bloginfo( 'description' );
	$img   = is_singular() && has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'saturdayla-hero' ) : ( get_theme_mod( 'saturdayla_hero_image' ) ? wp_get_attachment_image_url( get_theme_mod( 'saturdayla_hero_image' ), 'saturdayla-hero' ) : '' );
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) );
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_trim_words( $desc, 28 ) ) );
	printf( '<meta property="og:title" content="%s">' . "\n<meta property=\"og:description\" content=\"%s\">\n<meta property=\"og:url\" content=\"%s\">\n<meta property=\"og:type\" content=\"website\">\n<meta name=\"twitter:card\" content=\"summary_large_image\">\n",
		esc_attr( $title ), esc_attr( wp_trim_words( $desc, 28 ) ), esc_url( $url ) );
	if ( $img ) printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $img ) );
	echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
}, 6 );
