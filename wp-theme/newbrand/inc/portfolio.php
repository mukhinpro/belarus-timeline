<?php
/**
 * Sessions — one entry per shoot.
 *
 * Registered with show_in_rest so it is editable in the block editor and
 * renderable by the Query Loop block, which is what puts the portfolio into
 * server-rendered HTML that Google can crawl without running JavaScript.
 *
 * Each session carries a location. Fifty published sessions with real LA
 * locations is fifty pages carrying local keywords — the single strongest
 * organic asset a photographer can build.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'init',
	function () {
		register_post_type(
			'session',
			[
				'labels'        => [
					'name'          => __( 'Sessions', 'newbrand' ),
					'singular_name' => __( 'Session', 'newbrand' ),
					'add_new_item'  => __( 'Add new session', 'newbrand' ),
					'edit_item'     => __( 'Edit session', 'newbrand' ),
					'all_items'     => __( 'All sessions', 'newbrand' ),
					'menu_name'     => __( 'Portfolio', 'newbrand' ),
				],
				'public'        => true,
				'has_archive'   => 'portfolio',
				'rewrite'       => [ 'slug' => 'portfolio', 'with_front' => false ],
				'menu_icon'     => 'dashicons-camera',
				'menu_position' => 5,
				'supports'      => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
				'show_in_rest'  => true,
				'template'      => [
					[ 'core/paragraph', [ 'placeholder' => 'One or two sentences about this session.' ] ],
					[ 'core/gallery', [] ],
				],
			]
		);

		register_taxonomy(
			'session_type',
			'session',
			[
				'labels'            => [
					'name'          => __( 'Session types', 'newbrand' ),
					'singular_name' => __( 'Session type', 'newbrand' ),
				],
				'hierarchical'      => true,
				'public'            => true,
				'rewrite'           => [ 'slug' => 'sessions', 'with_front' => false ],
				'show_in_rest'      => true,
				'show_admin_column' => true,
			]
		);

		/*
		 * Location is registered through the meta API rather than a metabox, so
		 * it appears in the REST response and can be edited in the block editor
		 * sidebar and read by the schema code.
		 */
		register_post_meta(
			'session',
			'newbrand_location',
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
 * Seed the session types once, on activation.
 */
add_action(
	'after_switch_theme',
	function () {
		$types = [
			'Family'            => 'family',
			'Kids and teens'    => 'kids',
			'Birthday parties'  => 'parties',
			'Weddings'          => 'weddings',
			'Motherhood'        => 'motherhood',
		];

		foreach ( $types as $name => $slug ) {
			if ( ! term_exists( $slug, 'session_type' ) ) {
				wp_insert_term( $name, 'session_type', [ 'slug' => $slug ] );
			}
		}

		flush_rewrite_rules();
	}
);

/**
 * Show the location in the sessions admin list so it is obvious when one is
 * missing — a session without a location is a wasted local-SEO page.
 */
add_filter(
	'manage_session_posts_columns',
	function ( array $columns ): array {
		$columns['newbrand_location'] = __( 'Location', 'newbrand' );
		return $columns;
	}
);

add_action(
	'manage_session_posts_custom_column',
	function ( string $column, int $post_id ): void {
		if ( 'newbrand_location' !== $column ) {
			return;
		}
		$location = get_post_meta( $post_id, 'newbrand_location', true );
		echo $location
			? esc_html( $location )
			: '<span style="color:#b32d2e">' . esc_html__( 'Missing', 'newbrand' ) . '</span>';
	},
	10,
	2
);
