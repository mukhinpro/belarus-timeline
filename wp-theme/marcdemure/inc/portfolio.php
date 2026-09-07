<?php
/**
 * Shoots — one entry per session in the public portfolio.
 *
 * Split three ways: women, men, couples. Each split gets its own archive URL
 * rather than a JavaScript filter, because a filter leaves Google one page
 * where there should be three, each with its own vocabulary and its own
 * shareable link.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		register_post_type(
			'shoot',
			[
				'labels'        => [
					'name'          => __( 'Shoots', 'marcdemure' ),
					'singular_name' => __( 'Shoot', 'marcdemure' ),
					'add_new_item'  => __( 'Add new shoot', 'marcdemure' ),
					'edit_item'     => __( 'Edit shoot', 'marcdemure' ),
					'all_items'     => __( 'All shoots', 'marcdemure' ),
					'menu_name'     => __( 'Portfolio', 'marcdemure' ),
				],
				'public'        => true,
				'has_archive'   => 'portfolio',
				'rewrite'       => [ 'slug' => 'portfolio', 'with_front' => false ],
				'menu_icon'     => 'dashicons-camera-alt',
				'menu_position' => 5,
				'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
				'show_in_rest'  => true,
				'template'      => [
					[ 'core/paragraph', [ 'placeholder' => 'A sentence or two about this session.' ] ],
					[ 'core/gallery', [] ],
				],
			]
		);

		register_taxonomy(
			'shoot_type',
			'shoot',
			[
				'labels'            => [
					'name'          => __( 'Portfolio sections', 'marcdemure' ),
					'singular_name' => __( 'Portfolio section', 'marcdemure' ),
				],
				'hierarchical'      => true,
				'public'            => true,
				'rewrite'           => [ 'slug' => 'gallery', 'with_front' => false ],
				'show_in_rest'      => true,
				'show_admin_column' => true,
			]
		);

		/*
		 * A boudoir shoot cannot be published without a signed release. This is
		 * the meta the publish guard below reads.
		 */
		register_post_meta(
			'shoot',
			'md_release_signed',
			[
				'type'          => 'boolean',
				'single'        => true,
				'default'       => false,
				'show_in_rest'  => true,
				'auth_callback' => static fn() => current_user_can( 'edit_posts' ),
			]
		);

		register_post_meta(
			'shoot',
			'md_location',
			[
				'type'              => 'string',
				'single'            => true,
				'default'           => '',
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
			]
		);
	}
);

/**
 * Seed the three portfolio sections once, on activation.
 */
add_action(
	'after_switch_theme',
	function () {
		$sections = [
			'Women'   => 'women',
			'Men'     => 'men',
			'Couples' => 'couples',
		];

		foreach ( $sections as $name => $slug ) {
			if ( ! term_exists( $slug, 'shoot_type' ) ) {
				wp_insert_term( $name, 'shoot_type', [ 'slug' => $slug ] );
			}
		}

		flush_rewrite_rules();
	}
);

/**
 * Publishing guard: a shoot without a signed release stays a draft.
 *
 * This is the one irreversible mistake in boudoir work — a client's photographs
 * going public without written permission. A checkbox is a weak promise, but a
 * checkbox that physically refuses to publish is a real one, and it costs the
 * photographer two seconds when the paperwork is genuinely in hand.
 */
add_filter(
	'wp_insert_post_data',
	function ( array $data, array $postarr ): array {
		if ( 'shoot' !== ( $data['post_type'] ?? '' ) || 'publish' !== ( $data['post_status'] ?? '' ) ) {
			return $data;
		}

		$post_id = (int) ( $postarr['ID'] ?? 0 );
		$signed  = $post_id ? get_post_meta( $post_id, 'md_release_signed', true ) : false;

		if ( ! $signed ) {
			$data['post_status'] = 'draft';
		}

		return $data;
	},
	10,
	2
);

add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();
		if ( ! $screen || 'shoot' !== $screen->post_type || 'post' !== $screen->base ) {
			return;
		}

		$post_id = (int) ( $_GET['post'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $post_id && get_post_meta( $post_id, 'md_release_signed', true ) ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'Model release not confirmed.', 'marcdemure' ),
			esc_html__( 'This shoot will stay a draft until the release checkbox is ticked in the sidebar under Custom Fields.', 'marcdemure' )
		);
	}
);

/**
 * Flag shoots missing a release or a location in the admin list.
 */
add_filter(
	'manage_shoot_posts_columns',
	function ( array $columns ): array {
		$columns['md_release']  = __( 'Release', 'marcdemure' );
		$columns['md_location'] = __( 'Location', 'marcdemure' );
		return $columns;
	}
);

add_action(
	'manage_shoot_posts_custom_column',
	function ( string $column, int $post_id ): void {
		if ( 'md_release' === $column ) {
			echo get_post_meta( $post_id, 'md_release_signed', true )
				? '<span aria-hidden="true">&#10003;</span> ' . esc_html__( 'On file', 'marcdemure' )
				: '<strong style="color:#b32d2e">' . esc_html__( 'Missing', 'marcdemure' ) . '</strong>';
			return;
		}

		if ( 'md_location' === $column ) {
			$location = get_post_meta( $post_id, 'md_location', true );
			echo $location
				? esc_html( $location )
				: '<em style="color:#996800">' . esc_html__( 'not set', 'marcdemure' ) . '</em>';
		}
	},
	10,
	2
);
