<?php
/**
 * Marc Demure — block theme setup.
 *
 * Design lives in theme.json. This file registers what theme.json cannot: the
 * Shoots post type behind the portfolio, the subscriber-only area and the
 * Stripe billing that feeds it, protected media, the age notice, and schema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MD_VER', '1.0.0' );

require_once get_template_directory() . '/inc/settings.php';
require_once get_template_directory() . '/inc/portfolio.php';
require_once get_template_directory() . '/inc/membership.php';
require_once get_template_directory() . '/inc/stripe.php';
require_once get_template_directory() . '/inc/protected-media.php';
require_once get_template_directory() . '/inc/age-notice.php';
require_once get_template_directory() . '/inc/seo-schema.php';
require_once get_template_directory() . '/inc/performance.php';

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'script', 'style', 'navigation-widgets' ] );
		add_editor_style( 'assets/css/editor.css' );
		load_theme_textdomain( 'marcdemure', get_template_directory() . '/languages' );

		/*
		 * Boudoir work is shot and shown vertically. These sizes drive the
		 * srcset core emits, so a phone downloads a phone-sized file.
		 */
		add_image_size( 'md-hero', 1600, 2100, true );
		add_image_size( 'md-tile', 900, 1200, true );
		add_image_size( 'md-sheet', 1100, 0, false );
		add_image_size( 'md-full', 2000, 0, false );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'md-style', get_stylesheet_uri(), [], MD_VER );
		wp_enqueue_style(
			'md-fonts',
			'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300&family=Jost:wght@300;400;500&display=swap',
			[],
			null
		);
		wp_enqueue_style( 'md-extras', get_template_directory_uri() . '/assets/css/extras.css', [ 'md-fonts' ], MD_VER );
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
 * The theme's own blocks. Core has no breadcrumb block, and the access panel
 * has to render differently for each visitor, so both live here.
 */
add_action(
	'init',
	function () {
		register_block_type( get_template_directory() . '/blocks/breadcrumbs' );
	}
);

add_action(
	'init',
	function () {
		register_block_pattern_category( 'md-pages', [ 'label' => __( 'Studio: whole pages', 'marcdemure' ) ] );
		register_block_pattern_category( 'md-sections', [ 'label' => __( 'Studio: sections', 'marcdemure' ) ] );
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
