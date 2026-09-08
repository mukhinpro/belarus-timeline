<?php
/**
 * Server render for thereyare/breadcrumbs.
 *
 * Mirrors thereyare_breadcrumb_node() in inc/seo-schema.php exactly. Google
 * cross-checks the visible trail against the BreadcrumbList markup, so the two
 * must never drift apart — if you change one, change the other.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_front_page() ) {
	return;
}

$crumbs = [ [ 'label' => __( 'Home', 'thereyare' ), 'url' => home_url( '/' ) ] ];

if ( is_singular( 'session' ) ) {
	$archive = get_post_type_archive_link( 'session' );
	if ( $archive ) {
		$crumbs[] = [ 'label' => __( 'Portfolio', 'thereyare' ), 'url' => $archive ];
	}
} elseif ( is_singular( 'post' ) ) {
	$blog_id = (int) get_option( 'page_for_posts' );
	if ( $blog_id ) {
		$crumbs[] = [ 'label' => get_the_title( $blog_id ), 'url' => get_permalink( $blog_id ) ];
	}
} elseif ( is_page() ) {
	$parent = wp_get_post_parent_id( get_the_ID() );
	if ( $parent ) {
		$crumbs[] = [ 'label' => get_the_title( $parent ), 'url' => get_permalink( $parent ) ];
	}
}

$current = is_singular() ? get_the_title() : wp_strip_all_tags( get_the_archive_title() );

$wrapper = get_block_wrapper_attributes( [ 'class' => 'tya-breadcrumbs' ] );

$html = '';
foreach ( $crumbs as $crumb ) {
	$html .= sprintf(
		'<a href="%s">%s</a><span aria-hidden="true"> / </span>',
		esc_url( $crumb['url'] ),
		esc_html( $crumb['label'] )
	);
}
$html .= '<span aria-current="page">' . esc_html( $current ) . '</span>';

printf(
	'<nav %s aria-label="%s">%s</nav>',
	$wrapper, // phpcs:ignore WordPress.Security.EscapeOutput -- built by core.
	esc_attr__( 'Breadcrumb', 'thereyare' ),
	$html // phpcs:ignore WordPress.Security.EscapeOutput -- each part escaped above.
);
