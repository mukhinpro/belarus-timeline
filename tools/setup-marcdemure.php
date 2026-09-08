<?php
/**
 * Marc Demure — one-command site setup.
 *
 *     wp theme activate marcdemure
 *     wp eval-file tools/setup-marcdemure.php
 *
 * Same contract as the ThereYare script: creates what is missing, never
 * overwrites what exists, safe to run twice.
 *
 * The slug `private` matters — the membership gate redirects there by name.
 * The service slugs matter too: the Service schema reads "men" and "couple"
 * out of the slug to pick the right price.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Run this through WP-CLI: wp eval-file tools/setup-marcdemure.php\n" );
	exit( 1 );
}

$pages = [
	[ 'title' => 'Home', 'slug' => 'home' ],

	[ 'title' => 'Boudoir Photography in Los Angeles', 'slug' => 'boudoir-photography-los-angeles', 'template' => 'page-service', 'pattern' => 'marcdemure/service-women' ],
	[ 'title' => "Men's Boudoir in Los Angeles", 'slug' => 'mens-boudoir-los-angeles', 'template' => 'page-service', 'pattern' => 'marcdemure/service-men' ],
	[ 'title' => 'Couples Boudoir in Los Angeles', 'slug' => 'couples-boudoir-los-angeles', 'template' => 'page-service', 'pattern' => 'marcdemure/service-couples' ],
	[ 'title' => 'Bridal Boudoir in Los Angeles', 'slug' => 'bridal-boudoir-los-angeles', 'template' => 'page-service', 'pattern' => 'marcdemure/service-bridal' ],

	[ 'title' => 'Pricing', 'slug' => 'pricing', 'template' => 'page-pricing' ],
	[ 'title' => 'Gift Certificates', 'slug' => 'gift-certificates', 'template' => 'page-gift', 'pattern' => 'marcdemure/gift-page' ],
	[ 'title' => 'Albums and Prints', 'slug' => 'albums', 'pattern' => 'marcdemure/albums' ],
	[ 'title' => 'Before Your Session', 'slug' => 'prepare-for-your-session', 'pattern' => 'marcdemure/prepare' ],
	[ 'title' => 'Private access', 'slug' => 'private', 'template' => 'page-private', 'pattern' => 'marcdemure/private-page' ],
	[ 'title' => 'Discretion', 'slug' => 'discretion', 'pattern' => 'marcdemure/discretion' ],
	[ 'title' => 'Model call', 'slug' => 'model-call', 'pattern' => 'marcdemure/model-call' ],
	[ 'title' => 'About', 'slug' => 'about' ],
	[ 'title' => 'Book a session', 'slug' => 'book', 'template' => 'page-book' ],
	[ 'title' => 'Journal', 'slug' => 'journal' ],

	[ 'title' => 'Studios', 'slug' => 'studios' ],
	[
		'title'    => 'Downtown Los Angeles',
		'slug'     => 'downtown-los-angeles',
		'parent'   => 'studios',
		'template' => 'page-location',
		'pattern'  => 'marcdemure/location-page',
		'excerpt'  => 'Downtown Los Angeles — exact address sent with the booking',
	],
	[
		'title'    => 'Arts District',
		'slug'     => 'arts-district',
		'parent'   => 'studios',
		'template' => 'page-location',
		'pattern'  => 'marcdemure/location-page',
		'excerpt'  => 'Arts District, Los Angeles — exact address sent with the booking',
	],
	[
		'title'    => 'Hollywood',
		'slug'     => 'hollywood',
		'parent'   => 'studios',
		'template' => 'page-location',
		'pattern'  => 'marcdemure/location-page',
		'excerpt'  => 'Hollywood, Los Angeles — exact address sent with the booking',
	],

	[ 'title' => 'Privacy', 'slug' => 'privacy' ],
	[ 'title' => 'Terms', 'slug' => 'terms' ],
];

$primary_menu = [
	[ 'page' => 'boudoir-photography-los-angeles', 'label' => 'Women' ],
	[ 'page' => 'mens-boudoir-los-angeles', 'label' => 'Men' ],
	[ 'page' => 'couples-boudoir-los-angeles', 'label' => 'Couples' ],
	[ 'page' => 'bridal-boudoir-los-angeles', 'label' => 'Bridal' ],
	[ 'page' => 'pricing', 'label' => 'Pricing' ],
	[ 'page' => 'private', 'label' => 'Private' ],
	[ 'page' => 'journal', 'label' => 'Journal' ],
	[ 'page' => 'about', 'label' => 'About' ],
];

$footer_menu = [
	[ 'page' => 'gift-certificates', 'label' => 'Gift certificates' ],
	[ 'page' => 'albums', 'label' => 'Albums and prints' ],
	[ 'page' => 'prepare-for-your-session', 'label' => 'Before your session' ],
	[ 'page' => 'discretion', 'label' => 'Discretion' ],
	[ 'page' => 'model-call', 'label' => 'Model call' ],
	[ 'page' => 'book', 'label' => 'Book a session' ],
	[ 'page' => 'privacy', 'label' => 'Privacy' ],
	[ 'page' => 'terms', 'label' => 'Terms' ],
];

require __DIR__ . '/setup-lib.php';

md_setup_run(
	[
		'theme'  => 'marcdemure',
		'pages'  => $pages,
		'posts'  => [],
		'front'  => 'home',
		'blog'   => 'journal',
		'menus'  => [ 'Primary' => $primary_menu, 'Footer' => $footer_menu ],
		'mods'   => [
			'md_photographer' => 'Marc Demure',
			'md_instagram'    => 'https://www.instagram.com/marc.demure/',
		],
		'no_pattern_note' => 'Write this page. The About page and the legal pages are the only ones the theme does not ship copy for.',
		'todo'   => [
			'Add the nginx deny rule for wp-content/uploads/md-private/ — see the theme README.',
			'Put MD_STRIPE_WEBHOOK_SECRET in wp-config.php, then add the Stripe links under Customize.',
			'Fill in the bracketed text on the three studio pages, and their real addresses in each page excerpt.',
			'Replace the example testimonials with real ones.',
		],
	]
);
