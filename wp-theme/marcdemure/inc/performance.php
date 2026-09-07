<?php
/**
 * Core Web Vitals.
 *
 * A boudoir portfolio is a wall of large photographs, so the whole game is
 * making the first one arrive fast and the rest arrive late.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Only the first image on the page skips lazy loading. Core defaults to three,
 * which on a gallery means three full-size downloads competing with the one
 * that actually decides the Largest Contentful Paint.
 */
add_filter( 'wp_omit_loading_attr_threshold', static fn() => 1 );

/**
 * Tell the browser the hero matters before it has finished parsing the page.
 */
add_filter(
	'wp_get_attachment_image_attributes',
	function ( array $attr, $attachment, $size ): array {
		static $first = true;

		if ( $first && ! is_admin() ) {
			$attr['fetchpriority'] = 'high';
			unset( $attr['loading'] );
			$first = false;
		}

		return $attr;
	},
	10,
	3
);

/**
 * Load only the block styles a page actually uses.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );

/**
 * Drop the global styles the theme does not use.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_style( 'classic-theme-styles' );
	},
	20
);
