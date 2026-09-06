<?php
/**
 * SaturdayLa — theme setup
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SATURDAYLA_VER', '1.0.0' );

require get_template_directory() . '/inc/seo.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/portfolio.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'script', 'style' ] );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );

	register_nav_menus( [
		'primary' => 'Primary menu',
		'footer'  => 'Footer menu',
	] );

	// Image sizes tuned for the layouts (keeps originals off the page)
	add_image_size( 'saturdayla-hero', 1600, 2000, true );  // hero is portrait 4:5 now
	add_image_size( 'saturdayla-portrait', 900, 1200, true );
	add_image_size( 'saturdayla-sheet', 1000, 0, false );  // width-only, keeps aspect for the contact sheet
	add_image_size( 'saturdayla-large', 1800, 0, false );  // lightbox
} );

add_action( 'wp_enqueue_scripts', function () {
	// Google Fonts with display=swap. Self-host later if you want zero third-party requests.
	wp_enqueue_style( 'saturdayla-fonts', 'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,400&family=Karla:wght@400;500;600;700&display=swap', [], null );
	wp_enqueue_style( 'saturdayla-main', get_template_directory_uri() . '/assets/css/main.css', [ 'saturdayla-fonts' ], SATURDAYLA_VER );
	wp_enqueue_script( 'saturdayla-main', get_template_directory_uri() . '/assets/js/main.js', [], SATURDAYLA_VER, [ 'strategy' => 'defer', 'in_footer' => true ] );
} );

// Preconnect for fonts
add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = [ 'href' => 'https://fonts.gstatic.com', 'crossorigin' ];
	}
	return $urls;
}, 10, 2 );

// Trim WordPress head noise (cleaner HTML, fewer requests)
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
add_filter( 'emoji_svg_url', '__return_false' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Lazy-load everything except the hero
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment, $size ) {
	if ( 'saturdayla-hero' === $size ) {
		$attr['loading'] = 'eager';
		$attr['fetchpriority'] = 'high';
	}
	return $attr;
}, 10, 3 );

/** Helpers */
function saturdayla_phone() { return get_theme_mod( 'saturdayla_phone', '+1 (747) 217-9834' ); }
function saturdayla_email() { return get_theme_mod( 'saturdayla_email', 'hello@saturdayla.com' ); }
function saturdayla_instagram() { return get_theme_mod( 'saturdayla_instagram', 'https://www.instagram.com/saturdayla/' ); }
function saturdayla_book_url() {
	$p = get_page_by_path( 'book' );
	return $p ? get_permalink( $p ) : home_url( '/book/' );
}
function saturdayla_breadcrumbs() {
	if ( is_front_page() ) return;
	echo '<nav class="breadcrumbs" aria-label="Breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a>';
	if ( is_singular( 'portfolio' ) ) {
		echo '<span>/</span><a href="' . esc_url( get_post_type_archive_link( 'portfolio' ) ) . '">Portfolio</a>';
	} elseif ( is_singular( 'post' ) ) {
		echo '<span>/</span><a href="' . esc_url( get_permalink( get_option( 'page_for_posts' ) ) ) . '">Journal</a>';
	} elseif ( is_page() && wp_get_post_parent_id( get_the_ID() ) ) {
		$parent = wp_get_post_parent_id( get_the_ID() );
		echo '<span>/</span><a href="' . esc_url( get_permalink( $parent ) ) . '">' . esc_html( get_the_title( $parent ) ) . '</a>';
	}
	echo '<span>/</span>' . esc_html( get_the_title() ) . '</nav>';
}
