<?php
/**
 * Protected media.
 *
 * This is where most paywalled photography sites actually leak. WordPress
 * writes uploads into a folder the web server hands out to anyone who asks,
 * so a "members only" gallery is one right-click away from being public: copy
 * the image URL, open it in a private window, and the paywall was decoration.
 *
 * So member files live in their own folder, the server is told to refuse
 * direct requests to it, and every one of them is served by PHP only after the
 * same access check the pages use. The real path never appears in any markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The folder member files live in, inside uploads so backups still catch it.
 *
 * @return array{path: string, url: string}
 */
function md_private_dir(): array {
	$uploads = wp_get_upload_dir();

	return [
		'path' => trailingslashit( $uploads['basedir'] ) . 'md-private',
		'url'  => trailingslashit( $uploads['baseurl'] ) . 'md-private',
	];
}

/**
 * Lay down the folder and the Apache refusal on activation.
 *
 * nginx ignores .htaccess, so its rule is in the theme README and has to be
 * pasted into the server config once. The admin notice below checks whether it
 * actually took effect rather than assuming.
 */
add_action(
	'after_switch_theme',
	function () {
		$dir = md_private_dir()['path'];

		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}

		$htaccess = trailingslashit( $dir ) . '.htaccess';

		if ( ! file_exists( $htaccess ) ) {
			file_put_contents( // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
				$htaccess,
				"# Member files. Served only by PHP, after an access check.\n"
				. "<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n"
				. "<IfModule !mod_authz_core.c>\n\tDeny from all\n</IfModule>\n"
			);
		}

		$index = trailingslashit( $dir ) . 'index.php';

		if ( ! file_exists( $index ) ) {
			file_put_contents( $index, "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}
	}
);

/**
 * Route uploads made against a private set into the private folder.
 */
add_filter(
	'upload_dir',
	function ( array $dirs ): array {
		if ( ! md_upload_is_private() ) {
			return $dirs;
		}

		$private = md_private_dir();

		$dirs['path']   = $private['path'] . $dirs['subdir'];
		$dirs['url']    = $private['url'] . $dirs['subdir'];
		$dirs['basedir'] = $private['path'];
		$dirs['baseurl'] = $private['url'];

		return $dirs;
	}
);

/**
 * Is the upload in flight attached to a private set?
 *
 * Reads the parent the editor sends with the upload. Nothing here trusts a
 * value that a visitor could set, because only an author can reach this code.
 */
function md_upload_is_private(): bool {
	if ( ! is_admin() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return false;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$parent = (int) ( $_REQUEST['post'] ?? $_REQUEST['post_id'] ?? 0 );
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	return $parent > 0 && 'private_set' === get_post_type( $parent );
}

/**
 * Mark attachments that landed in the private folder, so every later check is
 * one meta read instead of a path comparison.
 */
add_action(
	'add_attachment',
	function ( int $attachment_id ): void {
		$file = get_post_meta( $attachment_id, '_wp_attached_file', true );

		if ( ! is_string( $file ) ) {
			return;
		}

		$private = md_private_dir()['path'];
		$full    = trailingslashit( wp_get_upload_dir()['basedir'] ) . ltrim( $file, '/' );

		if ( str_starts_with( wp_normalize_path( $full ), wp_normalize_path( $private ) ) ) {
			update_post_meta( $attachment_id, '_md_private', 1 );
		}
	}
);

function md_is_private_attachment( int $attachment_id ): bool {
	return (bool) get_post_meta( $attachment_id, '_md_private', true );
}

/**
 * The only URL a member file is ever published under.
 */
function md_private_file_url( int $attachment_id, string $size = 'full' ): string {
	return add_query_arg(
		[
			'md_file' => $attachment_id,
			'md_size' => $size,
		],
		home_url( '/' )
	);
}

/**
 * Replace the real file URL everywhere WordPress would print it.
 */
add_filter(
	'wp_get_attachment_url',
	function ( string $url, int $attachment_id ): string {
		return md_is_private_attachment( $attachment_id ) ? md_private_file_url( $attachment_id ) : $url;
	},
	10,
	2
);

add_filter(
	'image_downsize',
	function ( $out, int $attachment_id, $size ) {
		if ( ! md_is_private_attachment( $attachment_id ) ) {
			return $out;
		}

		$name = is_string( $size ) ? $size : 'full';
		$meta = wp_get_attachment_metadata( $attachment_id );

		$width  = (int) ( $meta['sizes'][ $name ]['width'] ?? $meta['width'] ?? 0 );
		$height = (int) ( $meta['sizes'][ $name ]['height'] ?? $meta['height'] ?? 0 );

		return [ md_private_file_url( $attachment_id, $name ), $width, $height, 'full' !== $name ];
	},
	10,
	3
);

/**
 * srcset would publish the real paths of every generated size, so private
 * images get a single source instead.
 */
add_filter(
	'wp_calculate_image_srcset',
	function ( array $sources, array $size_array, string $image_src, array $image_meta, int $attachment_id ): array {
		return md_is_private_attachment( $attachment_id ) ? [] : $sources;
	},
	10,
	5
);

/**
 * The serving endpoint.
 */
add_filter(
	'query_vars',
	function ( array $vars ): array {
		$vars[] = 'md_file';
		$vars[] = 'md_size';
		return $vars;
	}
);

add_action(
	'template_redirect',
	function () {
		$attachment_id = (int) get_query_var( 'md_file' );

		if ( ! $attachment_id ) {
			return;
		}

		md_serve_private_file( $attachment_id, (string) get_query_var( 'md_size' ) );
	},
	0
);

/**
 * Stream one member file, or refuse.
 *
 * The path is rebuilt from the attachment's own metadata, so nothing a visitor
 * types can walk out of the private folder. The size name is matched against
 * the sizes WordPress actually generated rather than used as a filename.
 */
function md_serve_private_file( int $attachment_id, string $size ): void {
	if ( 'attachment' !== get_post_type( $attachment_id ) || ! md_is_private_attachment( $attachment_id ) ) {
		status_header( 404 );
		exit;
	}

	if ( ! md_user_has_access() ) {
		status_header( 403 );
		nocache_headers();
		exit;
	}

	$path = get_attached_file( $attachment_id );

	if ( ! $path || ! file_exists( $path ) ) {
		status_header( 404 );
		exit;
	}

	$meta = wp_get_attachment_metadata( $attachment_id );

	if ( $size && 'full' !== $size && ! empty( $meta['sizes'][ $size ]['file'] ) ) {
		$candidate = trailingslashit( dirname( $path ) ) . basename( (string) $meta['sizes'][ $size ]['file'] );

		if ( file_exists( $candidate ) ) {
			$path = $candidate;
		}
	}

	$type = get_post_mime_type( $attachment_id );

	if ( ! $type || ! str_starts_with( $type, 'image/' ) ) {
		$checked = wp_check_filetype( $path );
		$type    = $checked['type'] ?: 'application/octet-stream';
	}

	nocache_headers();
	header( 'Content-Type: ' . $type );
	header( 'Content-Length: ' . filesize( $path ) );
	header( 'Content-Disposition: inline; filename="' . basename( $path ) . '"' );
	header( 'X-Robots-Tag: noindex, nofollow, noimageindex', true );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: no-referrer' );

	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
	exit;
}

/**
 * Check that the server really is refusing direct requests, and say so in the
 * dashboard if it is not. A silent assumption here is the whole paywall.
 */
add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( ! $screen || ! in_array( $screen->id, [ 'dashboard', 'edit-private_set' ], true ) ) {
			return;
		}

		$dir = md_private_dir();

		if ( ! is_dir( $dir['path'] ) ) {
			return;
		}

		$probe = trailingslashit( $dir['path'] ) . 'md-probe.txt';

		if ( ! file_exists( $probe ) ) {
			file_put_contents( $probe, 'probe' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		}

		$response = wp_remote_get(
			trailingslashit( $dir['url'] ) . 'md-probe.txt',
			[ 'timeout' => 5, 'sslverify' => true ]
		);

		if ( is_wp_error( $response ) ) {
			return;
		}

		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return;
		}

		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'Member files are reachable without paying.', 'marcdemure' ),
			esc_html__( 'The server is still serving the private uploads folder directly. On nginx the deny rule has to be added to the server config by hand — see the theme README. Until then, treat everything in the private area as public.', 'marcdemure' )
		);
	}
);
