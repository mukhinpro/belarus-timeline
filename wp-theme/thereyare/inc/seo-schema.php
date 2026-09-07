<?php
/**
 * JSON-LD structured data.
 *
 * The point of this file: core blocks already emit clean, semantic HTML, and
 * that HTML is machine readable. So instead of asking the studio to fill in a
 * separate "FAQ schema" field, we read the blocks they actually placed on the
 * page and translate them into schema. A core/details block becomes a
 * Question/Answer pair; a core/gallery on a session becomes an ImageGallery.
 * Write the page once, get the rich result for free.
 *
 * Works standalone. If Rank Math or Yoast is active they own the meta title,
 * description and Open Graph tags, and this file steps back to the business,
 * FAQ and gallery graph only.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * True when an SEO plugin is handling meta tags for us.
 */
function thereyare_seo_plugin_active(): bool {
	return defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * The postal address, with the street included only when one is set.
 *
 * A note that matters more than the code: this is the website's structured
 * data, which is a different system from the Google Business Profile listing.
 * Putting the office address here helps Google work out which business the
 * site belongs to. But if clients are never seen at that office, the Business
 * Profile itself must be set up as a service-area business with the address
 * hidden — listing an address customers cannot visit is the most common cause
 * of a profile being suspended.
 */
function thereyare_address_node(): array {
	$address = [
		'@type'           => 'PostalAddress',
		'addressLocality' => thereyare_setting( 'thereyare_city' ),
		'addressRegion'   => thereyare_setting( 'thereyare_region' ),
		'addressCountry'  => 'US',
	];

	$street = thereyare_setting( 'thereyare_street' );
	if ( $street ) {
		$address['streetAddress'] = $street;
	}

	$postcode = thereyare_setting( 'thereyare_postcode' );
	if ( $postcode ) {
		$address['postalCode'] = $postcode;
	}

	return $address;
}

/**
 * The lead photographer as a Person, linked to the studio.
 *
 * Worth doing for a one-photographer studio: it gives Google a person to
 * attach the work to, which is how a name starts appearing in its own right
 * rather than only as a company. Returns null until a name is entered, because
 * a Person node with no name is worse than no node at all.
 */
function thereyare_person_node(): ?array {
	$name = thereyare_setting( 'thereyare_photographer' );

	if ( ! $name ) {
		return null;
	}

	$node = [
		'@type'      => 'Person',
		'@id'        => home_url( '/#photographer' ),
		'name'       => $name,
		'jobTitle'   => 'Photographer',
		'worksFor'   => [ '@id' => home_url( '/#business' ) ],
		'knowsAbout' => [ 'Family photography', 'Child portrait photography', 'Wedding photography' ],
	];

	$instagram = thereyare_setting( 'thereyare_instagram' );
	if ( $instagram ) {
		$node['sameAs'] = [ $instagram ];
	}

	return $node;
}

/**
 * The studio itself. Every other node points at this one by @id, which is what
 * lets Google tie the pages, the photographs and the business together.
 */
function thereyare_business_node(): array {
	$areas = array_filter( array_map( 'trim', explode( ',', thereyare_setting( 'thereyare_areas' ) ) ) );

	$node = [
		'@type'       => [ 'LocalBusiness', 'ProfessionalService' ],
		'@id'         => home_url( '/#business' ),
		'name'        => thereyare_setting( 'thereyare_studio_name' ),
		'description' => get_bloginfo( 'description' ),
		'url'         => home_url( '/' ),
		'priceRange'  => thereyare_setting( 'thereyare_price_range' ),
		'address'     => thereyare_address_node(),
		'areaServed'  => array_map(
			static fn( $area ) => [ '@type' => 'City', 'name' => $area ],
			$areas
		),
		'makesOffer'  => array_map(
			static fn( $service ) => [
				'@type'       => 'Offer',
				'itemOffered' => [ '@type' => 'Service', 'name' => $service ],
			],
			[
				'Family photography',
				'Children and teen portrait photography',
				'Kids birthday party photography',
				'Wedding photography',
				'Maternity photography',
				'Motherhood and parent portrait photography',
				'Portrait photography for adults',
				'Child model portfolio and agency comp card photography',
			]
		),
	];

	// Only assert contact details the studio has actually filled in.
	$phone = thereyare_setting( 'thereyare_phone' );
	if ( $phone ) {
		$node['telephone'] = $phone;
	}

	$email = thereyare_setting( 'thereyare_email' );
	if ( $email ) {
		$node['email'] = $email;
	}

	$instagram = thereyare_setting( 'thereyare_instagram' );
	if ( $instagram ) {
		$node['sameAs'] = [ $instagram ];
	}

	$founded = thereyare_setting( 'thereyare_founded' );
	if ( $founded ) {
		$node['foundingDate'] = $founded;
	}

	if ( has_custom_logo() ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
		$logo    = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $logo ) {
			$node['logo']  = $logo;
			$node['image'] = $logo;
		}
	}

	// Point the business at the photographer when one is named.
	if ( thereyare_setting( 'thereyare_photographer' ) ) {
		$node['founder']  = [ '@id' => home_url( '/#photographer' ) ];
		$node['employee'] = [ '@id' => home_url( '/#photographer' ) ];
	}

	return $node;
}

/**
 * Walk a parsed block tree and hand every block to a callback. Blocks nest
 * (a details block inside a group inside a columns block), so a flat loop
 * would miss most of the page.
 */
function thereyare_walk_blocks( array $blocks, callable $visit ): void {
	foreach ( $blocks as $block ) {
		$visit( $block );
		if ( ! empty( $block['innerBlocks'] ) ) {
			thereyare_walk_blocks( $block['innerBlocks'], $visit );
		}
	}
}

/**
 * Turn every core/details block on the page into a Question/Answer pair.
 *
 * This is the whole reason the FAQ is authored with the native Details block
 * rather than a custom accordion: the block already renders a real
 * <details>/<summary>, which is accessible, works without JavaScript, and maps
 * one to one onto FAQPage schema.
 */
function thereyare_faq_from_blocks( string $content ): array {
	if ( ! has_blocks( $content ) ) {
		return [];
	}

	$faq = [];

	thereyare_walk_blocks(
		parse_blocks( $content ),
		static function ( array $block ) use ( &$faq ) {
			if ( 'core/details' !== ( $block['blockName'] ?? '' ) ) {
				return;
			}

			// The block stores the summary as an attribute; older markup keeps
			// it in the HTML, so fall back to reading the tag.
			$question = (string) ( $block['attrs']['summary'] ?? '' );
			if ( '' === $question && preg_match( '#<summary[^>]*>(.*?)</summary>#is', $block['innerHTML'], $m ) ) {
				$question = $m[1];
			}

			$answer = '';
			foreach ( $block['innerBlocks'] as $inner ) {
				$answer .= render_block( $inner );
			}

			$question = trim( wp_strip_all_tags( $question ) );
			$answer   = trim( wp_strip_all_tags( $answer ) );

			if ( '' !== $question && '' !== $answer ) {
				$faq[] = [
					'@type'          => 'Question',
					'name'           => $question,
					'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $answer ],
				];
			}
		}
	);

	return $faq;
}

/**
 * Every image id used by core/image and core/gallery blocks in the content,
 * followed by any images attached to the post. Used for ImageGallery schema.
 */
function thereyare_image_ids( int $post_id ): array {
	$ids     = [];
	$content = (string) get_post_field( 'post_content', $post_id );

	if ( has_blocks( $content ) ) {
		thereyare_walk_blocks(
			parse_blocks( $content ),
			static function ( array $block ) use ( &$ids ) {
				$name = $block['blockName'] ?? '';
				if ( 'core/image' === $name && ! empty( $block['attrs']['id'] ) ) {
					$ids[] = (int) $block['attrs']['id'];
				}
				// Older galleries kept the ids on the gallery block itself.
				if ( 'core/gallery' === $name && ! empty( $block['attrs']['ids'] ) ) {
					$ids = array_merge( $ids, array_map( 'intval', $block['attrs']['ids'] ) );
				}
			}
		);
	}

	if ( ! $ids ) {
		$ids = get_posts(
			[
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'post_parent'    => $post_id,
				'posts_per_page' => -1,
				'orderby'        => 'menu_order ID',
				'order'          => 'ASC',
				'fields'         => 'ids',
			]
		);
	}

	$thumb = get_post_thumbnail_id( $post_id );
	if ( $thumb && ! in_array( (int) $thumb, $ids, true ) ) {
		array_unshift( $ids, (int) $thumb );
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

/**
 * Breadcrumb trail as schema. Mirrors the visible breadcrumb pattern so the
 * two never disagree, which Google treats as a quality signal.
 */
function thereyare_breadcrumb_node(): ?array {
	if ( is_front_page() || ! is_singular() ) {
		return null;
	}

	$items    = [ [ '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ] ];
	$position = 2;

	if ( is_singular( 'session' ) ) {
		$archive = get_post_type_archive_link( 'session' );
		if ( $archive ) {
			$items[] = [ '@type' => 'ListItem', 'position' => $position++, 'name' => 'Portfolio', 'item' => $archive ];
		}
	} elseif ( is_singular( 'post' ) ) {
		$blog_id = (int) get_option( 'page_for_posts' );
		if ( $blog_id ) {
			$items[] = [ '@type' => 'ListItem', 'position' => $position++, 'name' => get_the_title( $blog_id ), 'item' => get_permalink( $blog_id ) ];
		}
	} elseif ( is_page() ) {
		$parent = wp_get_post_parent_id( get_the_ID() );
		if ( $parent ) {
			$items[] = [ '@type' => 'ListItem', 'position' => $position++, 'name' => get_the_title( $parent ), 'item' => get_permalink( $parent ) ];
		}
	}

	$items[] = [ '@type' => 'ListItem', 'position' => $position, 'name' => get_the_title(), 'item' => get_permalink() ];

	return [ '@type' => 'BreadcrumbList', 'itemListElement' => $items ];
}

/**
 * Emit the whole graph as one script tag.
 *
 * Note on reviews: we deliberately do not generate AggregateRating from the
 * testimonial quotes on the site. Google treats self-serving review markup on
 * a LocalBusiness as a guidelines violation, and it can cost you every rich
 * result on the domain. Collect reviews on Google Business Profile instead.
 */
add_action(
	'wp_head',
	function () {
		$graph = [ thereyare_business_node() ];

		$person = thereyare_person_node();
		if ( $person ) {
			$graph[] = $person;
		}

		if ( is_front_page() ) {
			$graph[] = [
				'@type'     => 'WebSite',
				'@id'       => home_url( '/#website' ),
				'url'       => home_url( '/' ),
				'name'      => thereyare_setting( 'thereyare_studio_name' ),
				'publisher' => [ '@id' => home_url( '/#business' ) ],
			];
		}

		$breadcrumb = thereyare_breadcrumb_node();
		if ( $breadcrumb ) {
			$graph[] = $breadcrumb;
		}

		if ( is_singular() ) {
			$post_id = get_the_ID();
			$content = (string) get_post_field( 'post_content', $post_id );

			$faq = thereyare_faq_from_blocks( $content );
			if ( $faq ) {
				$graph[] = [ '@type' => 'FAQPage', 'mainEntity' => $faq ];
			}

			// A service page describes one offering; say so explicitly.
			if ( is_page() && 'page-service' === get_page_template_slug( $post_id ) ) {
				$price = thereyare_setting( 'thereyare_price_from' );
				$node  = [
					'@type'       => 'Service',
					'name'        => get_the_title(),
					'description' => has_excerpt() ? get_the_excerpt() : get_bloginfo( 'description' ),
					'url'         => get_permalink(),
					'provider'    => [ '@id' => home_url( '/#business' ) ],
					'areaServed'  => [ '@type' => 'City', 'name' => thereyare_setting( 'thereyare_city' ) ],
				];
				if ( $price ) {
					$node['offers'] = [
						'@type'         => 'Offer',
						'priceCurrency' => 'USD',
						'price'         => $price,
						'availability'  => 'https://schema.org/InStock',
					];
				}
				$graph[] = $node;
			}

			if ( is_singular( 'session' ) ) {
				$images = array_slice( thereyare_image_ids( $post_id ), 0, 20 );
				if ( $images ) {
					$graph[] = [
						'@type'           => 'ImageGallery',
						'name'            => get_the_title(),
						'url'             => get_permalink(),
						'image'           => array_values( array_filter( array_map(
							static fn( $id ) => wp_get_attachment_image_url( $id, 'thereyare-full' ),
							$images
						) ) ),
						'author'          => [ '@id' => home_url( '/#business' ) ],
						'contentLocation' => get_post_meta( $post_id, 'thereyare_location', true )
							?: thereyare_setting( 'thereyare_city' ) . ', ' . thereyare_setting( 'thereyare_region' ),
					];
				}
			}
		}

		printf(
			"\n<script type=\"application/ld+json\">%s</script>\n",
			wp_json_encode(
				[ '@context' => 'https://schema.org', '@graph' => $graph ],
				JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
			)
		);
	},
	5
);

/**
 * Meta description, Open Graph and canonical — only when no SEO plugin is
 * installed, so we never emit a competing set of tags.
 */
add_action(
	'wp_head',
	function () {
		if ( thereyare_seo_plugin_active() ) {
			return;
		}

		$description = is_singular() && has_excerpt()
			? get_the_excerpt()
			: get_bloginfo( 'description' );
		$description = wp_trim_words( wp_strip_all_tags( $description ), 28 );

		$url = is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) );

		$image = '';
		if ( is_singular() && has_post_thumbnail() ) {
			$image = (string) get_the_post_thumbnail_url( null, 'thereyare-hero' );
		}

		printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( $description ) );
		printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( wp_get_document_title() ) );
		printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( $description ) );
		printf( "<meta property=\"og:url\" content=\"%s\">\n", esc_url( $url ) );
		printf( "<meta property=\"og:type\" content=\"%s\">\n", is_singular( 'post' ) ? 'article' : 'website' );
		printf( "<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr( thereyare_setting( 'thereyare_studio_name' ) ) );
		echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";

		if ( $image ) {
			printf( "<meta property=\"og:image\" content=\"%s\">\n", esc_url( $image ) );
		}

		printf( "<link rel=\"canonical\" href=\"%s\">\n", esc_url( $url ) );
	},
	6
);
