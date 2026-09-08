<?php
/**
 * ThereYare — block theme setup.
 *
 * Layout, colour and type live in theme.json. This file only registers what
 * theme.json cannot: theme supports, the Sessions post type, JSON-LD schema,
 * the studio settings used by that schema, and a few performance hints.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'THEREYARE_VER', '1.0.0' );

require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/portfolio.php';
require_once get_template_directory() . '/inc/seo-schema.php';
require_once get_template_directory() . '/inc/performance.php';
require_once get_template_directory() . '/inc/gift-certificates.php';

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'script', 'style', 'navigation-widgets' ] );
		add_editor_style( 'assets/css/editor.css' );
		load_theme_textdomain( 'thereyare', get_template_directory() . '/languages' );

		/*
		 * Image sizes tuned to the layouts. Core's Image and Gallery blocks emit
		 * srcset from these, so Google gets a correctly sized file per viewport
		 * instead of one oversized original.
		 */
		add_image_size( 'thereyare-hero', 1600, 2000, true );
		add_image_size( 'thereyare-tile', 900, 1200, true );
		add_image_size( 'thereyare-sheet', 1000, 0, false );
		add_image_size( 'thereyare-full', 1800, 0, false );
	}
);

/**
 * Front-end and editor assets.
 *
 * Fonts are loaded from Google with a preconnect. Self-host them before launch
 * (see README) to drop the third-party round trip from the LCP path.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'thereyare-style', get_stylesheet_uri(), [], THEREYARE_VER );
		wp_enqueue_style(
			'thereyare-fonts',
			'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400&family=Karla:wght@400;500;600;700&display=swap',
			[],
			null
		);
		wp_enqueue_style( 'thereyare-extras', get_template_directory_uri() . '/assets/css/extras.css', [ 'thereyare-fonts' ], THEREYARE_VER );
	}
);

add_filter(
	'wp_resource_hints',
	function ( $urls, $relation ) {
		if ( 'preconnect' === $relation ) {
			$urls[] = [ 'href' => 'https://fonts.gstatic.com', 'crossorigin' ];
		}
		return $urls;
	},
	10,
	2
);

/**
 * Register the theme's own blocks.
 *
 * Only one, and only because core has no breadcrumb block: the visible trail
 * has to agree with the BreadcrumbList schema, so the theme owns both.
 */
add_action(
	'init',
	function () {
		register_block_type( get_template_directory() . '/blocks/breadcrumbs' );
		register_block_type( get_template_directory() . '/blocks/gift-chooser' );
		register_block_type( get_template_directory() . '/blocks/booking-calendar' );
	}
);

/**
 * Register the block pattern categories the theme's patterns are filed under.
 * Patterns are how the studio builds new service and location pages without a
 * developer, and without losing the heading hierarchy Google reads.
 */
add_action(
	'init',
	function () {
		register_block_pattern_category(
			'thereyare-pages',
			[ 'label' => __( 'Studio: whole pages', 'thereyare' ) ]
		);
		register_block_pattern_category(
			'thereyare-sections',
			[ 'label' => __( 'Studio: sections', 'thereyare' ) ]
		);
	}
);

/**
 * Trim head output that adds bytes without helping anyone.
 */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
