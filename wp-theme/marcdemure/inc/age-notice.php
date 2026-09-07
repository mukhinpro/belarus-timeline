<?php
/**
 * The eighteen-and-over notice.
 *
 * A deliberate trade-off, so it is worth stating plainly. The notice is drawn
 * over the page by JavaScript once, and remembered in a cookie. It does not
 * remove anything from the HTML response.
 *
 * Refusing to render the page until a visitor clicks would also hide it from
 * Googlebot, which does not click — the portfolio would drop out of search
 * entirely. So the public portfolio, which is clothed and artistic work, stays
 * crawlable behind a visible notice, and the explicit material lives in the
 * member area, where the gate is server side and nothing renders at all
 * without access.
 *
 * If a jurisdiction ever requires a real block on the public pages, this is
 * the file to change, and the cost is the search traffic.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const MD_AGE_COOKIE = 'md_age_ok';

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_admin() || md_age_confirmed() ) {
			return;
		}

		wp_enqueue_script(
			'md-age-notice',
			get_template_directory_uri() . '/assets/js/age-notice.js',
			[],
			MD_VER,
			[ 'strategy' => 'defer', 'in_footer' => true ]
		);

		wp_localize_script(
			'md-age-notice',
			'mdAgeNotice',
			[
				'cookie'  => MD_AGE_COOKIE,
				'days'    => 30,
				'title'   => __( 'This is work for adults.', 'marcdemure' ),
				'body'    => __( 'The photographs here include nudity, made with the consent of everyone in them. Please confirm you are eighteen or older.', 'marcdemure' ),
				'confirm' => __( 'I am eighteen or older', 'marcdemure' ),
				'leave'   => __( 'Take me away', 'marcdemure' ),
				'exitUrl' => 'https://www.google.com/',
			]
		);
	}
);

function md_age_confirmed(): bool {
	return isset( $_COOKIE[ MD_AGE_COOKIE ] ) && '1' === $_COOKIE[ MD_AGE_COOKIE ];
}
