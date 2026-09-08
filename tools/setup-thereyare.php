<?php
/**
 * ThereYare — one-command site setup.
 *
 *     wp theme activate thereyare
 *     wp eval-file tools/setup-thereyare.php
 *
 * Creates every page with the right slug, template and parent, drops the
 * theme's own patterns into the pages that have one, builds both menus,
 * points the front page and the journal at the right places, and writes the
 * studio details into the Customizer.
 *
 * Safe to run twice. A page that already exists is left alone — the script
 * never overwrites content someone has edited, it only fills in what is
 * missing. Re-run it after adding a page to the list below.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Run this through WP-CLI: wp eval-file tools/setup-thereyare.php\n" );
	exit( 1 );
}

/* -------------------------------------------------------------------------
 * The pages.
 *
 * pattern: a registered block pattern whose content becomes the page body.
 * excerpt: on a location page this is the meeting point, and the theme turns
 *          it into Place structured data — so it is content, not decoration.
 * ---------------------------------------------------------------------- */

$pages = [
	[ 'title' => 'Home', 'slug' => 'home' ],

	[ 'title' => 'Family Photography in Los Angeles', 'slug' => 'family-photography-los-angeles', 'template' => 'page-service', 'pattern' => 'thereyare/service-family' ],
	[ 'title' => 'Kids & Teen Photographer in Los Angeles', 'slug' => 'kids-photographer-los-angeles', 'template' => 'page-service', 'pattern' => 'thereyare/service-kids' ],
	[ 'title' => "Kids' Party Photographer in Los Angeles", 'slug' => 'kids-party-photography-los-angeles', 'template' => 'page-service', 'pattern' => 'thereyare/service-parties' ],
	[ 'title' => 'Wedding Photography in Los Angeles', 'slug' => 'wedding-photography-los-angeles', 'template' => 'page-service', 'pattern' => 'thereyare/service-weddings' ],
	[ 'title' => 'Maternity Photography in Los Angeles', 'slug' => 'maternity-photography-los-angeles', 'template' => 'page-service', 'pattern' => 'thereyare/service-maternity' ],
	[ 'title' => 'Newborn Photography in Los Angeles', 'slug' => 'newborn-photography-los-angeles', 'template' => 'page-service', 'pattern' => 'thereyare/service-newborn' ],
	[ 'title' => 'Motherhood & Parent Portraits in Los Angeles', 'slug' => 'motherhood-photography-los-angeles', 'template' => 'page-service', 'pattern' => 'thereyare/service-motherhood' ],
	[ 'title' => 'Portrait Photography in Los Angeles', 'slug' => 'portrait-photography-los-angeles', 'template' => 'page-service', 'pattern' => 'thereyare/service-portrait' ],
	[ 'title' => 'Kids Model Portfolio Photographer in Los Angeles', 'slug' => 'kids-model-portfolio-los-angeles', 'template' => 'page-service', 'pattern' => 'thereyare/service-model-portfolio' ],

	[ 'title' => 'Pricing', 'slug' => 'pricing', 'template' => 'page-pricing' ],
	[ 'title' => 'Gift Certificates', 'slug' => 'gift-certificates', 'template' => 'page-gift', 'pattern' => 'thereyare/gift-page' ],
	[ 'title' => 'Albums and Prints', 'slug' => 'albums', 'pattern' => 'thereyare/albums' ],
	[ 'title' => 'Mini Sessions', 'slug' => 'mini-sessions', 'pattern' => 'thereyare/mini-sessions' ],
	[ 'title' => 'Before Your Session', 'slug' => 'prepare-for-your-session', 'pattern' => 'thereyare/prepare' ],
	[ 'title' => 'About', 'slug' => 'about' ],
	[ 'title' => 'Book a session', 'slug' => 'book', 'template' => 'page-book' ],
	[ 'title' => 'Journal', 'slug' => 'journal' ],

	[ 'title' => 'Locations', 'slug' => 'locations' ],
	[
		'title'    => 'El Matador Beach, Malibu',
		'slug'     => 'el-matador',
		'parent'   => 'locations',
		'template' => 'page-location',
		'pattern'  => 'thereyare/location-el-matador',
		'excerpt'  => '32350 Pacific Coast Highway, Malibu',
	],
	[
		'title'    => 'Griffith Park',
		'slug'     => 'griffith-park',
		'parent'   => 'locations',
		'template' => 'page-location',
		'pattern'  => 'thereyare/location-griffith-park',
		'excerpt'  => 'Fern Dell Drive, Los Angeles',
	],
	[
		'title'    => 'Arroyo Seco, Pasadena',
		'slug'     => 'arroyo-seco-pasadena',
		'parent'   => 'locations',
		'template' => 'page-location',
		'pattern'  => 'thereyare/location-arroyo-seco-pasadena',
		'excerpt'  => 'Arroyo Boulevard, Pasadena',
	],

	[ 'title' => 'Privacy', 'slug' => 'privacy' ],
];

/* The journal articles that ship as patterns. */
$posts = [
	[ 'title' => 'What to wear to a family photo session', 'slug' => 'what-to-wear-to-a-family-photo-session', 'pattern' => 'thereyare/journal-what-to-wear' ],
	[ 'title' => 'How to get a toddler through a photo session', 'slug' => 'how-to-get-a-toddler-through-a-photo-session', 'pattern' => 'thereyare/journal-toddler' ],
	[ 'title' => 'Where to shoot family photos in Los Angeles', 'slug' => 'where-to-shoot-family-photos-in-los-angeles', 'pattern' => 'thereyare/journal-la-locations' ],
	[ 'title' => 'How to get your child into a modelling agency in LA', 'slug' => 'how-to-get-your-child-into-a-modelling-agency-in-la', 'pattern' => 'thereyare/journal-model-agency' ],
];

$primary_menu = [
	[ 'page' => 'home', 'label' => 'Home' ],
	[ 'page' => 'family-photography-los-angeles', 'label' => 'Families' ],
	[ 'page' => 'kids-photographer-los-angeles', 'label' => 'Kids' ],
	[ 'page' => 'newborn-photography-los-angeles', 'label' => 'Newborns' ],
	[ 'page' => 'wedding-photography-los-angeles', 'label' => 'Weddings' ],
	[ 'page' => 'pricing', 'label' => 'Pricing' ],
	[ 'page' => 'journal', 'label' => 'Journal' ],
	[ 'page' => 'gift-certificates', 'label' => 'Gifts' ],
	[ 'page' => 'about', 'label' => 'About' ],
];

$footer_menu = [
	[ 'page' => 'mini-sessions', 'label' => 'Mini sessions' ],
	[ 'page' => 'albums', 'label' => 'Albums and prints' ],
	[ 'page' => 'prepare-for-your-session', 'label' => 'Before your session' ],
	[ 'page' => 'locations', 'label' => 'Locations' ],
	[ 'page' => 'book', 'label' => 'Book a session' ],
	[ 'page' => 'privacy', 'label' => 'Privacy' ],
];

require __DIR__ . '/setup-lib.php';

md_setup_run(
	[
		'theme'        => 'thereyare',
		'pages'        => $pages,
		'posts'        => $posts,
		'front'        => 'home',
		'blog'         => 'journal',
		'menus'        => [ 'Primary' => $primary_menu, 'Footer' => $footer_menu ],
		'mods'         => [
			'thereyare_photographer' => 'Alexandr Mukhin',
			'thereyare_phone'        => '+1 747 217 9834',
			'thereyare_email'        => 'mukhinpro@gmail.com',
			'thereyare_instagram'    => 'https://www.instagram.com/thereyare_studio/',
		],
		'no_pattern_note' => 'Write this page — the About and legal pages are the only ones the theme does not ship copy for.',
		'todo'         => [
			'Set the real dates on the Mini Sessions page, and confirm the album prices with your printer.',
			'Add the Calendly or Acuity link and the Google reviews link under Customize.',
			'Publish the four journal drafts once each has a photograph.',
			'Fill in the office street address and postcode under Customize, and keep it hidden in Google Business Profile.',
		],
	]
);
