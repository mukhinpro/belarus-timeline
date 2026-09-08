<?php
/**
 * The shared engine behind setup-thereyare.php and setup-marcdemure.php.
 *
 * Everything here is idempotent by design. The rule throughout: create what
 * is missing, never overwrite what exists. Someone may have spent an evening
 * writing the About page, and a setup script is not allowed to eat it.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This file is a library. Run one of the setup-*.php scripts through WP-CLI.\n" );
	exit( 1 );
}

/**
 * Body content for a page, taken from a registered block pattern.
 *
 * Patterns are the single source of truth for this copy — the same text the
 * editor offers under "Studio: whole pages" — so a page created here and a
 * page built by hand in the editor end up identical.
 */
function md_setup_pattern_content( string $slug ): ?string {
	if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
		return null;
	}

	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $slug );

	return $pattern['content'] ?? null;
}

/**
 * @param array<string, mixed> $spec
 * @param array<string, int>   $ids   Slug to post ID, filled as we go.
 */
function md_setup_page( array $spec, array &$ids, string $note ): void {
	// A child page's path includes its parent, so looking it up by the bare
	// slug would miss it on a second run and quietly create a duplicate.
	$path = empty( $spec['parent'] )
		? $spec['slug']
		: $spec['parent'] . '/' . $spec['slug'];

	$existing = get_page_by_path( $path );

	if ( $existing instanceof WP_Post ) {
		$ids[ $spec['slug'] ] = (int) $existing->ID;
		WP_CLI::log( sprintf( '  = %-44s exists, left alone', $path ) );
		return;
	}

	$content = '';
	$source  = 'empty';

	if ( ! empty( $spec['pattern'] ) ) {
		$pattern = md_setup_pattern_content( $spec['pattern'] );

		if ( null === $pattern ) {
			WP_CLI::warning( sprintf( 'Pattern %s is not registered — creating %s empty.', $spec['pattern'], $spec['slug'] ) );
		} else {
			$content = $pattern;
			$source  = 'pattern';
		}
	} elseif ( '' !== $note ) {
		$content = '<!-- wp:paragraph --><p>' . esc_html( $note ) . '</p><!-- /wp:paragraph -->';
		$source  = 'note';
	}

	$args = [
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $spec['title'],
		'post_name'    => $spec['slug'],
		'post_content' => $content,
	];

	if ( ! empty( $spec['excerpt'] ) ) {
		$args['post_excerpt'] = $spec['excerpt'];
	}

	if ( ! empty( $spec['parent'] ) && isset( $ids[ $spec['parent'] ] ) ) {
		$args['post_parent'] = $ids[ $spec['parent'] ];
	}

	$id = wp_insert_post( $args, true );

	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( sprintf( 'Could not create %s: %s', $spec['slug'], $id->get_error_message() ) );
		return;
	}

	if ( ! empty( $spec['template'] ) ) {
		update_post_meta( $id, '_wp_page_template', $spec['template'] );
	}

	$ids[ $spec['slug'] ] = (int) $id;
	WP_CLI::log( sprintf( '  + %-44s created (%s)', $path, $source ) );
}

/**
 * @param array<int, array<string, string>> $items
 * @param array<string, int>                $ids
 */
function md_setup_menu( string $name, array $items, array $ids ): void {
	$menu = wp_get_nav_menu_object( $name );

	if ( $menu ) {
		WP_CLI::log( sprintf( '  = menu %-38s exists, left alone', $name ) );
		return;
	}

	$menu_id = wp_create_nav_menu( $name );

	if ( is_wp_error( $menu_id ) ) {
		WP_CLI::warning( sprintf( 'Could not create menu %s: %s', $name, $menu_id->get_error_message() ) );
		return;
	}

	$added = 0;

	foreach ( $items as $item ) {
		if ( ! isset( $ids[ $item['page'] ] ) ) {
			continue;
		}

		wp_update_nav_menu_item(
			$menu_id,
			0,
			[
				'menu-item-title'     => $item['label'],
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $ids[ $item['page'] ],
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			]
		);
		$added ++;
	}

	WP_CLI::log( sprintf( '  + menu %-38s created, %d items', $name, $added ) );
}

/**
 * @param array<string, mixed> $config
 */
function md_setup_run( array $config ): void {
	$theme = wp_get_theme();

	if ( $theme->get_stylesheet() !== $config['theme'] ) {
		WP_CLI::error( sprintf(
			'The %s theme is not active (current: %s). Run: wp theme activate %s',
			$config['theme'],
			$theme->get_stylesheet(),
			$config['theme']
		) );
	}

	$ids = [];

	WP_CLI::log( 'Pages' );
	foreach ( $config['pages'] as $spec ) {
		md_setup_page( $spec, $ids, $config['no_pattern_note'] ?? '' );
	}

	if ( ! empty( $config['posts'] ) ) {
		WP_CLI::log( 'Journal' );

		foreach ( $config['posts'] as $spec ) {
			$existing = get_page_by_path( $spec['slug'], OBJECT, 'post' );

			if ( $existing instanceof WP_Post ) {
				WP_CLI::log( sprintf( '  = %-44s exists, left alone', $spec['slug'] ) );
				continue;
			}

			$content = md_setup_pattern_content( $spec['pattern'] );

			if ( null === $content ) {
				WP_CLI::warning( sprintf( 'Pattern %s is not registered — skipping the article.', $spec['pattern'] ) );
				continue;
			}

			// Drafts on purpose: an article without its photograph should not
			// go live the moment the site does.
			$id = wp_insert_post(
				[
					'post_type'    => 'post',
					'post_status'  => 'draft',
					'post_title'   => $spec['title'],
					'post_name'    => $spec['slug'],
					'post_content' => $content,
				],
				true
			);

			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( sprintf( 'Could not create %s: %s', $spec['slug'], $id->get_error_message() ) );
				continue;
			}

			WP_CLI::log( sprintf( '  + %-44s created as a draft', $spec['slug'] ) );
		}
	}

	WP_CLI::log( 'Reading' );

	if ( isset( $ids[ $config['front'] ], $ids[ $config['blog'] ] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids[ $config['front'] ] );
		update_option( 'page_for_posts', $ids[ $config['blog'] ] );
		WP_CLI::log( sprintf( '  + front page: %s, posts page: %s', $config['front'], $config['blog'] ) );
	}

	// Post name only. Every slug in this script assumes it.
	if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		WP_CLI::log( '  + permalinks set to /%postname%/' );
	}

	WP_CLI::log( 'Menus' );
	foreach ( $config['menus'] as $name => $items ) {
		md_setup_menu( $name, $items, $ids );
	}

	WP_CLI::log( 'Studio details' );
	foreach ( $config['mods'] as $key => $value ) {
		if ( '' === (string) get_theme_mod( $key, '' ) ) {
			set_theme_mod( $key, $value );
			WP_CLI::log( sprintf( '  + %-30s %s', $key, $value ) );
		} else {
			WP_CLI::log( sprintf( '  = %-30s already set', $key ) );
		}
	}

	flush_rewrite_rules();

	WP_CLI::success( sprintf( '%s is set up. %d pages in place.', $config['theme'], count( $ids ) ) );
	WP_CLI::log( '' );
	WP_CLI::log( 'Still to do by hand:' );

	foreach ( (array) ( $config['todo'] ?? [] ) as $line ) {
		WP_CLI::log( '  · ' . $line );
	}
}
