<?php
/**
 * "Sessions" portfolio: one post per photo session. Upload photos to the post's gallery
 * (attach images to the post), set a featured image, pick a session type, add a location.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function () {
	register_post_type( 'portfolio', [
		'labels' => [
			'name' => 'Sessions', 'singular_name' => 'Session', 'add_new_item' => 'Add new session',
			'edit_item' => 'Edit session', 'all_items' => 'All sessions', 'menu_name' => 'Portfolio',
		],
		'public'       => true,
		'has_archive'  => 'portfolio',
		'rewrite'      => [ 'slug' => 'portfolio', 'with_front' => false ],
		'menu_icon'    => 'dashicons-camera',
		'menu_position'=> 5,
		'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
		'show_in_rest' => true,
	] );

	register_taxonomy( 'session_type', 'portfolio', [
		'labels'       => [ 'name' => 'Session types', 'singular_name' => 'Session type' ],
		'hierarchical' => true,
		'public'       => true,
		'rewrite'      => [ 'slug' => 'sessions', 'with_front' => false ],
		'show_in_rest' => true,
		'show_admin_column' => true,
	] );
} );

/** Seed session types once */
add_action( 'after_switch_theme', function () {
	foreach ( [ 'Family' => 'family', 'Kids & teens' => 'kids', 'Birthday parties' => 'parties', 'Weddings' => 'weddings', 'Editorial & magazine' => 'editorial' ] as $name => $slug ) {
		if ( ! term_exists( $slug, 'session_type' ) ) wp_insert_term( $name, 'session_type', [ 'slug' => $slug ] );
	}
	flush_rewrite_rules();
} );

/** Location meta box (used in captions and schema) */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'saturdayla_location', 'Location', function ( $post ) {
		wp_nonce_field( 'saturdayla_loc', 'saturdayla_loc_nonce' );
		printf( '<input type="text" name="saturdayla_location" value="%s" style="width:100%%" placeholder="El Matador Beach, Malibu">', esc_attr( get_post_meta( $post->ID, 'saturdayla_location', true ) ) );
		echo '<p class="description">Shown under the photo and sent to Google as the place of the shoot.</p>';
	}, 'portfolio', 'side' );
} );
add_action( 'save_post_portfolio', function ( $id ) {
	if ( ! isset( $_POST['saturdayla_loc_nonce'] ) || ! wp_verify_nonce( $_POST['saturdayla_loc_nonce'], 'saturdayla_loc' ) ) return;
	update_post_meta( $id, 'saturdayla_location', sanitize_text_field( $_POST['saturdayla_location'] ?? '' ) );
} );

/** All image IDs for a session: gallery blocks in content first, then attached images */
function saturdayla_session_images( $post_id ) {
	$ids = [];
	$content = get_post_field( 'post_content', $post_id );
	if ( has_blocks( $content ) ) {
		foreach ( parse_blocks( $content ) as $b ) {
			if ( 'core/gallery' === $b['blockName'] ) {
				foreach ( $b['innerBlocks'] as $ib ) if ( ! empty( $ib['attrs']['id'] ) ) $ids[] = (int) $ib['attrs']['id'];
				if ( ! empty( $b['attrs']['ids'] ) ) $ids = array_merge( $ids, array_map( 'intval', $b['attrs']['ids'] ) );
			}
		}
	}
	if ( ! $ids ) {
		$ids = get_posts( [ 'post_type' => 'attachment', 'post_mime_type' => 'image', 'post_parent' => $post_id, 'posts_per_page' => -1, 'orderby' => 'menu_order ID', 'order' => 'ASC', 'fields' => 'ids' ] );
	}
	$thumb = get_post_thumbnail_id( $post_id );
	if ( $thumb && ! in_array( $thumb, $ids, true ) ) array_unshift( $ids, $thumb );
	return array_values( array_unique( $ids ) );
}

/** Contact-sheet tile */
function saturdayla_sheet_item( $post_id, $img_id = null ) {
	$img_id  = $img_id ?: get_post_thumbnail_id( $post_id );
	if ( ! $img_id ) return;
	$terms   = wp_get_post_terms( $post_id, 'session_type', [ 'fields' => 'slugs' ] );
	$loc     = get_post_meta( $post_id, 'saturdayla_location', true );
	$title   = get_the_title( $post_id );
	$alt     = get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ?: $title . ( $loc ? ', ' . $loc : '' ) . ' — photo by ' . get_theme_mod( 'saturdayla_owner', 'SaturdayLa' );
	printf(
		'<a class="sheet__item" data-type="%s" href="%s" data-lightbox data-alt="%s" data-caption="%s">%s<div class="sheet__cap">%s<small>%s</small></div></a>',
		esc_attr( implode( ' ', $terms ) ),
		esc_url( wp_get_attachment_image_url( $img_id, 'saturdayla-large' ) ),
		esc_attr( $alt ),
		esc_attr( $title . ( $loc ? ' · ' . $loc : '' ) ),
		wp_get_attachment_image( $img_id, 'saturdayla-sheet', false, [ 'alt' => $alt ] ),
		esc_html( $title ),
		esc_html( $loc )
	);
}
