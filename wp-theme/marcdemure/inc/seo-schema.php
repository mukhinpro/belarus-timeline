<?php
/**
 * Structured data.
 *
 * The schema is derived from the blocks actually on the page rather than typed
 * into a settings screen, so it cannot drift away from what a visitor reads —
 * which is the failure Google penalises.
 *
 * Two deliberate omissions. There is no AggregateRating: marking up your own
 * star rating on your own site is against Google's guidelines and earns a
 * manual action. And nothing in the member area is described at all, because
 * describing gated content to a crawler is how it ends up in search.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Walk a parsed block tree, including nested blocks.
 *
 * @param array<int, array<string, mixed>> $blocks
 */
function md_walk_blocks( array $blocks, callable $visit ): void {
	foreach ( $blocks as $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		$visit( $block );

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			md_walk_blocks( $block['innerBlocks'], $visit );
		}
	}
}

/**
 * Build FAQPage entries from the Details blocks on the page.
 *
 * Every question in the markup becomes a question in the schema, and nothing
 * else does. Write a new FAQ in the editor and the structured data follows.
 *
 * @return array<int, array<string, mixed>>
 */
function md_faq_from_blocks( string $content ): array {
	if ( ! has_blocks( $content ) ) {
		return [];
	}

	$faq = [];

	md_walk_blocks(
		parse_blocks( $content ),
		static function ( array $block ) use ( &$faq ): void {
			if ( 'core/details' !== ( $block['blockName'] ?? '' ) ) {
				return;
			}

			$question = (string) ( $block['attrs']['summary'] ?? '' );

			if ( '' === $question && preg_match( '#<summary[^>]*>(.*?)</summary>#is', (string) ( $block['innerHTML'] ?? '' ), $m ) ) {
				$question = $m[1];
			}

			$answer = '';

			foreach ( (array) ( $block['innerBlocks'] ?? [] ) as $inner ) {
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
 * The studio itself.
 *
 * A service-area business: the photographer travels, and clients do not visit
 * an address. Publishing one they cannot visit is the single most common cause
 * of a Google Business Profile suspension.
 *
 * @return array<string, mixed>
 */
function md_business_node(): array {
	$name  = md_setting( 'md_photographer' );
	$city  = md_setting( 'md_city' );
	$areas = array_filter( array_map( 'trim', explode( ',', md_setting( 'md_areas' ) ) ) );

	$node = [
		'@type'       => [ 'LocalBusiness', 'ProfessionalService' ],
		'@id'         => home_url( '/#studio' ),
		'name'        => $name,
		'url'         => home_url( '/' ),
		'description' => get_bloginfo( 'description' ),
		'priceRange'  => '$$',
		'areaServed'  => array_map(
			static fn( string $area ): array => [ '@type' => 'City', 'name' => $area ],
			$areas
		),
		'address'     => [
			'@type'           => 'PostalAddress',
			'addressLocality' => $city,
			'addressRegion'   => md_setting( 'md_region' ),
			'addressCountry'  => 'US',
		],
	];

	$phone = md_setting( 'md_phone' );
	if ( '' !== $phone ) {
		$node['telephone'] = $phone;
	}

	$email = md_setting( 'md_email' );
	if ( '' !== $email ) {
		$node['email'] = $email;
	}

	$instagram = md_setting( 'md_instagram' );
	if ( '' !== $instagram ) {
		$node['sameAs'] = [ $instagram ];
	}

	$founded = md_setting( 'md_founded' );
	if ( '' !== $founded ) {
		$node['foundingDate'] = $founded;
	}

	$from = md_setting( 'md_price_from' );

	$node['makesOffer'] = array_map(
		static function ( array $offer ) use ( $from ): array {
			return [
				'@type'         => 'Offer',
				'name'          => $offer['name'],
				'priceCurrency' => 'USD',
				'price'         => $offer['price'] ?: $from,
				'itemOffered'   => [
					'@type'       => 'Service',
					'name'        => $offer['name'],
					'serviceType' => $offer['type'],
				],
			];
		},
		md_service_offers()
	);

	return $node;
}

/**
 * The services, in one place, so the pricing page and the schema agree.
 *
 * @return array<int, array{name: string, type: string, price: string}>
 */
function md_service_offers(): array {
	return [
		[ 'name' => 'Boudoir session', 'type' => 'Boudoir photography', 'price' => '650' ],
		[ 'name' => 'Men\'s portrait session', 'type' => 'Boudoir photography', 'price' => '650' ],
		[ 'name' => 'Couples session', 'type' => 'Boudoir photography', 'price' => '900' ],
	];
}

/**
 * The photographer, when a name has been set.
 *
 * @return array<string, mixed>|null
 */
function md_person_node(): ?array {
	$name = md_setting( 'md_photographer' );

	if ( '' === $name ) {
		return null;
	}

	$node = [
		'@type'      => 'Person',
		'@id'        => home_url( '/#photographer' ),
		'name'       => $name,
		'jobTitle'   => 'Photographer',
		'worksFor'   => [ '@id' => home_url( '/#studio' ) ],
		'knowsAbout' => [ 'Boudoir photography', 'Portrait photography', 'Studio lighting' ],
	];

	$instagram = md_setting( 'md_instagram' );

	if ( '' !== $instagram ) {
		$node['sameAs'] = [ $instagram ];
	}

	return $node;
}

/**
 * The crumb trail, built from the same logic the visible breadcrumbs use.
 *
 * @return array<string, mixed>|null
 */
function md_breadcrumb_node(): ?array {
	$trail = md_breadcrumb_trail();

	if ( count( $trail ) < 2 ) {
		return null;
	}

	$items = [];
	$position = 1;

	foreach ( $trail as $crumb ) {
		$items[] = [
			'@type'    => 'ListItem',
			'position' => $position ++,
			'name'     => $crumb['label'],
			'item'     => $crumb['url'],
		];
	}

	return [
		'@type'           => 'BreadcrumbList',
		'@id'             => md_current_url() . '#breadcrumbs',
		'itemListElement' => $items,
	];
}

/**
 * @return array<int, array{label: string, url: string}>
 */
function md_breadcrumb_trail(): array {
	$trail = [ [ 'label' => __( 'Home', 'marcdemure' ), 'url' => home_url( '/' ) ] ];

	if ( is_tax( 'shoot_type' ) ) {
		$term = get_queried_object();
		$trail[] = [ 'label' => __( 'Portfolio', 'marcdemure' ), 'url' => (string) get_post_type_archive_link( 'shoot' ) ];

		if ( $term instanceof WP_Term ) {
			$trail[] = [ 'label' => $term->name, 'url' => (string) get_term_link( $term ) ];
		}

		return $trail;
	}

	if ( is_post_type_archive( 'shoot' ) ) {
		$trail[] = [ 'label' => __( 'Portfolio', 'marcdemure' ), 'url' => (string) get_post_type_archive_link( 'shoot' ) ];
		return $trail;
	}

	if ( is_singular( 'shoot' ) ) {
		$trail[] = [ 'label' => __( 'Portfolio', 'marcdemure' ), 'url' => (string) get_post_type_archive_link( 'shoot' ) ];

		$terms = get_the_terms( get_queried_object_id(), 'shoot_type' );

		if ( is_array( $terms ) && $terms ) {
			$trail[] = [ 'label' => $terms[0]->name, 'url' => (string) get_term_link( $terms[0] ) ];
		}

		$trail[] = [ 'label' => get_the_title(), 'url' => (string) get_permalink() ];
		return $trail;
	}

	if ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$trail[] = [ 'label' => (string) get_the_title( $ancestor ), 'url' => (string) get_permalink( $ancestor ) ];
		}

		$trail[] = [ 'label' => get_the_title(), 'url' => (string) get_permalink() ];
	}

	return $trail;
}

function md_current_url(): string {
	$url = home_url( add_query_arg( [], $GLOBALS['wp']->request ?? '' ) );
	return user_trailingslashit( $url );
}

/**
 * Emit the graph.
 */
add_action(
	'wp_head',
	function () {
		// Nothing in the member area is ever described to a crawler.
		if ( is_singular( 'private_set' ) || is_post_type_archive( 'private_set' ) || is_404() ) {
			return;
		}

		$graph = [
			[
				'@type'      => 'WebSite',
				'@id'        => home_url( '/#website' ),
				'url'        => home_url( '/' ),
				'name'       => get_bloginfo( 'name' ),
				'publisher'  => [ '@id' => home_url( '/#studio' ) ],
				'inLanguage' => get_bloginfo( 'language' ),
			],
			md_business_node(),
		];

		$person = md_person_node();

		if ( $person ) {
			$graph[] = $person;
		}

		$crumbs = md_breadcrumb_node();

		if ( $crumbs ) {
			$graph[] = $crumbs;
		}

		if ( is_singular() ) {
			$faq = md_faq_from_blocks( (string) get_post_field( 'post_content', get_queried_object_id() ) );

			if ( $faq ) {
				$graph[] = [
					'@type'      => 'FAQPage',
					'@id'        => md_current_url() . '#faq',
					'mainEntity' => $faq,
				];
			}
		}

		if ( is_singular( 'shoot' ) ) {
			$graph[] = [
				'@type'       => 'ImageGallery',
				'@id'         => md_current_url() . '#gallery',
				'name'        => get_the_title(),
				'description' => get_the_excerpt(),
				'url'         => (string) get_permalink(),
				'author'      => [ '@id' => home_url( '/#photographer' ) ],
			];
		}

		printf(
			'<script type="application/ld+json">%s</script>' . "\n",
			wp_json_encode(
				[ '@context' => 'https://schema.org', '@graph' => $graph ],
				JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
			)
		);
	},
	5
);

/**
 * Canonical and Open Graph, but only when no SEO plugin is doing it.
 * Two canonical tags are worse than none.
 */
add_action(
	'wp_head',
	function () {
		if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
			return;
		}

		if ( is_singular( 'private_set' ) || is_post_type_archive( 'private_set' ) ) {
			return;
		}

		$url   = md_current_url();
		$title = wp_get_document_title();

		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
		printf( '<meta property="og:type" content="%s">' . "\n", is_singular() ? 'article' : 'website' );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta name="twitter:card" content="summary_large_image">' . "\n" );

		if ( is_singular() && has_post_thumbnail() ) {
			$image = get_the_post_thumbnail_url( get_queried_object_id(), 'md-sheet' );

			if ( $image ) {
				printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
			}
		}
	},
	6
);
