<?php
/**
 * The subscriber-only area.
 *
 * Two rules drive every decision here.
 *
 * First, the gate is server side. Hiding a gallery with CSS or JavaScript is
 * not a paywall — the markup is still in the response and anyone can read it
 * with two keystrokes. Nothing gated is ever rendered to someone without
 * access, so there is nothing to reveal.
 *
 * Second, gated content is never indexed. A paywalled set that turns up in
 * Google is both a leak and a Search Console error, so every private URL sends
 * noindex as a header and as a meta tag, and stays out of search, feeds and
 * sitemaps.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the private sets. They are publicly queryable so subscribers can
 * open them at a normal URL, and gated on every request below.
 */
add_action(
	'init',
	function () {
		register_post_type(
			'private_set',
			[
				'labels'              => [
					'name'          => __( 'Private sets', 'marcdemure' ),
					'singular_name' => __( 'Private set', 'marcdemure' ),
					'add_new_item'  => __( 'Add new private set', 'marcdemure' ),
					'menu_name'     => __( 'Private area', 'marcdemure' ),
				],
				'public'              => true,
				'has_archive'         => 'private',
				'rewrite'             => [ 'slug' => 'private', 'with_front' => false ],
				'menu_icon'           => 'dashicons-lock',
				'menu_position'       => 6,
				'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'template'            => [
					[ 'core/paragraph', [ 'placeholder' => 'A line about this set.' ] ],
					[ 'core/gallery', [] ],
				],
			]
		);
	}
);

/**
 * Does this user currently hold access?
 *
 * Editors and administrators always do, so the photographer can proof the area
 * without paying himself.
 */
function md_user_has_access( ?int $user_id = null ): bool {
	$user_id = $user_id ?? get_current_user_id();

	if ( ! $user_id ) {
		return false;
	}

	if ( user_can( $user_id, 'edit_others_posts' ) ) {
		return true;
	}

	$until = (int) get_user_meta( $user_id, 'md_access_until', true );

	return $until > time();
}

/**
 * When does the current user's access run out? Zero if they have none.
 */
function md_access_expires( ?int $user_id = null ): int {
	$user_id = $user_id ?? get_current_user_id();
	return $user_id ? (int) get_user_meta( $user_id, 'md_access_until', true ) : 0;
}

/**
 * Extend access to a moment in time. Called by the Stripe webhook, never by a
 * front-end request.
 */
function md_grant_access( int $user_id, int $until, string $status = 'active' ): void {
	update_user_meta( $user_id, 'md_access_until', $until );
	update_user_meta( $user_id, 'md_sub_status', sanitize_key( $status ) );
}

/**
 * End access immediately.
 */
function md_revoke_access( int $user_id, string $status = 'canceled' ): void {
	update_user_meta( $user_id, 'md_access_until', 0 );
	update_user_meta( $user_id, 'md_sub_status', sanitize_key( $status ) );
}

/**
 * The page a visitor is sent to when they ask for something they cannot see.
 */
function md_join_url(): string {
	$page = get_page_by_path( 'private' );
	return $page ? (string) get_permalink( $page ) : home_url( '/private/' );
}

/**
 * The gate.
 *
 * Runs before any template loads, so a visitor without access never receives a
 * byte of the protected markup.
 */
add_action(
	'template_redirect',
	function () {
		$is_private = is_singular( 'private_set' ) || is_post_type_archive( 'private_set' );

		if ( ! $is_private || md_user_has_access() ) {
			return;
		}

		wp_safe_redirect( add_query_arg( 'locked', '1', md_join_url() ), 302 );
		exit;
	},
	1
);

/**
 * Never index the private area, and say so twice — in the header, which also
 * covers files and redirects, and in the document head.
 */
add_action(
	'template_redirect',
	function () {
		if ( is_singular( 'private_set' ) || is_post_type_archive( 'private_set' ) ) {
			header( 'X-Robots-Tag: noindex, nofollow, noarchive, noimageindex', true );
		}
	},
	2
);

add_action(
	'wp_head',
	function () {
		if ( is_singular( 'private_set' ) || is_post_type_archive( 'private_set' ) ) {
			echo '<meta name="robots" content="noindex, nofollow, noarchive, noimageindex">' . "\n";
		}
	},
	1
);

/**
 * Keep private sets out of the sitemap, out of feeds and out of any query that
 * is not deliberately asking for them.
 */
add_filter(
	'wp_sitemaps_post_types',
	function ( array $types ): array {
		unset( $types['private_set'] );
		return $types;
	}
);

add_action(
	'pre_get_posts',
	function ( WP_Query $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_feed() || $query->is_search() ) {
			$types = (array) $query->get( 'post_type', [ 'post' ] );
			$query->set( 'post_type', array_values( array_diff( $types, [ 'private_set' ] ) ) );
		}
	}
);

/**
 * The access panel block.
 *
 * One block that renders the right thing for whoever is looking: a way in for
 * a stranger, a way to subscribe for a registered visitor, and a status line
 * with a billing link for a subscriber.
 */
add_action(
	'init',
	function () {
		register_block_type(
			'marcdemure/access-panel',
			[
				'api_version'     => 3,
				'title'           => __( 'Access panel', 'marcdemure' ),
				'category'        => 'theme',
				'icon'            => 'lock',
				'description'     => __( 'Shows sign in, subscribe or subscription status depending on who is viewing.', 'marcdemure' ),
				'supports'        => [ 'html' => false, 'align' => [ 'wide' ] ],
				'render_callback' => 'md_render_access_panel',
			]
		);
	}
);

/**
 * @param array<string, mixed> $attributes
 */
function md_render_access_panel( array $attributes = [] ): string {
	$classes = 'md-panel';
	if ( ! empty( $attributes['align'] ) ) {
		$classes .= ' align' . sanitize_html_class( (string) $attributes['align'] );
	}

	ob_start();

	if ( ! is_user_logged_in() ) {
		$locked = isset( $_GET['locked'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<?php if ( $locked ) : ?>
				<p class="md-panel__flash"><?php esc_html_e( 'That set is for members. Sign in, or join below.', 'marcdemure' ); ?></p>
			<?php endif; ?>
			<p class="md-panel__lead"><?php esc_html_e( 'Members see the full sets — the frames that never go on Instagram.', 'marcdemure' ); ?></p>
			<p class="md-panel__actions">
				<a class="md-btn" href="<?php echo esc_url( md_stripe_checkout_url() ); ?>"><?php esc_html_e( 'Join', 'marcdemure' ); ?></a>
				<a class="md-btn md-btn--quiet" href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>"><?php esc_html_e( 'Sign in', 'marcdemure' ); ?></a>
			</p>
			<p class="md-panel__note"><?php esc_html_e( 'Eighteen and over. Cancel any time, from your own billing page.', 'marcdemure' ); ?></p>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	if ( ! md_user_has_access() ) {
		?>
		<div class="<?php echo esc_attr( $classes ); ?>">
			<p class="md-panel__lead"><?php esc_html_e( 'Your membership is not active.', 'marcdemure' ); ?></p>
			<p class="md-panel__actions">
				<a class="md-btn" href="<?php echo esc_url( md_stripe_checkout_url() ); ?>"><?php esc_html_e( 'Start membership', 'marcdemure' ); ?></a>
			</p>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	$until  = md_access_expires();
	$portal = md_stripe_portal_url();
	?>
	<div class="<?php echo esc_attr( $classes ); ?> md-panel--member">
		<p class="md-panel__lead"><?php esc_html_e( 'Your membership is active.', 'marcdemure' ); ?></p>
		<?php if ( $until ) : ?>
			<p class="md-panel__note">
				<?php
				printf(
					/* translators: %s: date the current billing period ends. */
					esc_html__( 'Renews %s.', 'marcdemure' ),
					esc_html( wp_date( (string) get_option( 'date_format' ), $until ) )
				);
				?>
			</p>
		<?php endif; ?>
		<p class="md-panel__actions">
			<a class="md-btn" href="<?php echo esc_url( get_post_type_archive_link( 'private_set' ) ); ?>"><?php esc_html_e( 'Open the private area', 'marcdemure' ); ?></a>
			<?php if ( $portal ) : ?>
				<a class="md-btn md-btn--quiet" href="<?php echo esc_url( $portal ); ?>"><?php esc_html_e( 'Manage billing', 'marcdemure' ); ?></a>
			<?php endif; ?>
		</p>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Show membership state in the users list so support questions take one look.
 */
add_filter(
	'manage_users_columns',
	function ( array $columns ): array {
		$columns['md_access'] = __( 'Membership', 'marcdemure' );
		return $columns;
	}
);

add_filter(
	'manage_users_custom_column',
	function ( string $output, string $column, int $user_id ): string {
		if ( 'md_access' !== $column ) {
			return $output;
		}

		$until = (int) get_user_meta( $user_id, 'md_access_until', true );

		if ( $until > time() ) {
			return sprintf(
				/* translators: %s: renewal date. */
				esc_html__( 'Active until %s', 'marcdemure' ),
				esc_html( wp_date( (string) get_option( 'date_format' ), $until ) )
			);
		}

		$status = (string) get_user_meta( $user_id, 'md_sub_status', true );

		return $status ? esc_html( ucfirst( $status ) ) : '&mdash;';
	},
	10,
	3
);
