<?php
/**
 * Core Web Vitals.
 *
 * Largest Contentful Paint on a photography site is almost always the hero
 * image, and Google measures it in the field. Two things move it: the browser
 * must discover the image early, and must not wait on anything else to start
 * fetching it. WordPress lazy-loads images by default, which is right for
 * everything below the fold and wrong for the hero.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load the first image on a singular view eagerly, at high priority, and let
 * WordPress lazy-load the rest.
 */
add_filter(
	'wp_get_attachment_image_attributes',
	function ( array $attr, $attachment, $size ): array {
		if ( in_array( $size, [ 'thereyare-hero' ], true ) ) {
			$attr['loading']       = 'eager';
			$attr['fetchpriority'] = 'high';
			$attr['decoding']      = 'sync';
		}
		return $attr;
	},
	10,
	3
);

/**
 * WordPress skips lazy loading for the first few images on a page already.
 * Raise the threshold to one so only the true hero is eager: every extra
 * eager image competes with it for bandwidth and pushes LCP out.
 */
add_filter( 'wp_omit_loading_attr_threshold', static fn() => 1 );

/**
 * Ask the browser to keep the aspect ratio box for gallery images so the page
 * does not reflow as they arrive. Cumulative Layout Shift is a ranking signal
 * and galleries are the usual culprit.
 */
add_filter(
	'wp_content_img_tag',
	function ( string $html ): string {
		if ( false === strpos( $html, 'decoding=' ) ) {
			$html = str_replace( '<img ', '<img decoding="async" ', $html );
		}
		return $html;
	}
);

/**
 * Strip the global styles WordPress prints for blocks that are not on the
 * page. Smaller CSS payload, faster first render, no visual change.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
