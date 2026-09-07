<?php
/**
 * Gift certificates.
 *
 * Three parts, one source of truth:
 *   1. thereyare_gift_items() — the catalogue shown on the gift page.
 *   2. A Customizer panel holding one Stripe Payment Link per item.
 *   3. A private "Certificates" post type that issues a unique code per sale.
 *
 * Payment is deliberately handled by Stripe Payment Links rather than a store
 * plugin. A photography studio sells a handful of certificates a year; adding
 * WooCommerce to the site would cost every visitor its scripts on every page,
 * which is the opposite of what the rest of this theme is built for. A Payment
 * Link is a hosted checkout that already collects the buyer's details and can
 * be told to ask who the gift is for — the exact requirement the Wix build
 * never managed to enforce.
 *
 * A note on California law, because it shapes the copy on the page: Civil Code
 * §1749.5 makes it unlawful to sell a gift certificate carrying an expiry
 * date, and from 1 April 2026 a certificate with a balance under $15 must be
 * redeemable for cash. So nothing here — page, PDF or email — may say the
 * certificate expires.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Everything that can be gifted.
 *
 * `key`   — stable id, used for the Customizer setting and the certificate record
 * `group` — 'session' or 'amount', which is how the page splits the two lists
 * `price` — what the buyer pays, in whole dollars; null means they choose
 */
function thereyare_gift_items(): array {
	return [
		[ 'key' => 'family',     'group' => 'session', 'title' => 'Family session',   'price' => 800,  'meta' => 'Two hours · 12 retouched, ~50 graded' ],
		[ 'key' => 'maternity',  'group' => 'session', 'title' => 'Maternity',        'price' => 700,  'meta' => 'Two hours · 12 retouched, ~50 graded' ],
		[ 'key' => 'portrait',   'group' => 'session', 'title' => 'Portrait',         'price' => 700,  'meta' => 'Two hours · 12 retouched, ~50 graded' ],
		[ 'key' => 'kids',       'group' => 'session', 'title' => 'Kids & teens',     'price' => 600,  'meta' => 'Two hours · 12 retouched, ~50 graded' ],
		[ 'key' => 'model',      'group' => 'session', 'title' => 'Model portfolio',  'price' => 600,  'meta' => 'Two hours · digitals + 10 test shots' ],
		[ 'key' => 'parents',    'group' => 'session', 'title' => 'Parent portraits', 'price' => 400,  'meta' => 'Ninety minutes · 7 retouched, 20–30 graded' ],
		[ 'key' => 'party',      'group' => 'session', 'title' => 'Birthday party',   'price' => 360,  'meta' => 'Two hours of coverage, the minimum booking' ],
		[ 'key' => 'wedding',    'group' => 'session', 'title' => 'Wedding',          'price' => 1600, 'meta' => 'Four hours · two photographers' ],

		[ 'key' => 'amount_100',  'group' => 'amount', 'title' => '$100',   'price' => 100,  'meta' => 'Towards any session' ],
		[ 'key' => 'amount_250',  'group' => 'amount', 'title' => '$250',   'price' => 250,  'meta' => 'Towards any session' ],
		[ 'key' => 'amount_500',  'group' => 'amount', 'title' => '$500',   'price' => 500,  'meta' => 'Towards any session' ],
		[ 'key' => 'amount_1000', 'group' => 'amount', 'title' => '$1,000', 'price' => 1000, 'meta' => 'Towards any session' ],
	];
}

/**
 * One item by key, or null.
 */
function thereyare_gift_item( string $key ): ?array {
	foreach ( thereyare_gift_items() as $item ) {
		if ( $item['key'] === $key ) {
			return $item;
		}
	}
	return null;
}

/**
 * The Stripe Payment Link for one item, if the studio has pasted one in.
 */
function thereyare_gift_link( string $key ): string {
	return (string) get_theme_mod( 'thereyare_gift_link_' . $key, '' );
}

/**
 * Customizer: one URL field per item, plus the address enquiries fall back to.
 */
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->add_section(
			'thereyare_gift',
			[
				'title'       => __( 'Gift certificates', 'thereyare' ),
				'priority'    => 21,
				'description' => __( 'Paste the Stripe Payment Link for each certificate. Any left blank is hidden from the gift page, so you can launch with two and add the rest later.', 'thereyare' ),
			]
		);

		foreach ( thereyare_gift_items() as $item ) {
			$id = 'thereyare_gift_link_' . $item['key'];

			$wp_customize->add_setting(
				$id,
				[
					'default'           => '',
					'sanitize_callback' => 'esc_url_raw',
					'transport'         => 'refresh',
				]
			);

			$wp_customize->add_control(
				$id,
				[
					'label'       => sprintf( '%s — $%s', $item['title'], number_format( (int) $item['price'] ) ),
					'description' => 'session' === $item['group'] ? __( 'Session certificate', 'thereyare' ) : __( 'Fixed amount', 'thereyare' ),
					'section'     => 'thereyare_gift',
					'type'        => 'url',
				]
			);
		}
	}
);

/* -------------------------------------------------------------------------
 * Issued certificates
 * ---------------------------------------------------------------------- */

/**
 * Private post type: one entry per certificate sold.
 *
 * Not public. These records carry codes that must not be guessable or
 * crawlable, so the type is registered with public => false and excluded from
 * search, and nothing about it is ever rendered on the front end.
 */
add_action(
	'init',
	function () {
		register_post_type(
			'certificate',
			[
				'labels'              => [
					'name'          => __( 'Certificates', 'thereyare' ),
					'singular_name' => __( 'Certificate', 'thereyare' ),
					'add_new_item'  => __( 'Issue a certificate', 'thereyare' ),
					'edit_item'     => __( 'Edit certificate', 'thereyare' ),
					'all_items'     => __( 'All certificates', 'thereyare' ),
					'menu_name'     => __( 'Certificates', 'thereyare' ),
				],
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'menu_icon'           => 'dashicons-tickets-alt',
				'menu_position'       => 6,
				'supports'            => [ 'title', 'custom-fields' ],
				'capability_type'     => 'post',
			]
		);

		$fields = [
			'thereyare_cert_code'      => 'string',
			'thereyare_cert_item'      => 'string',
			'thereyare_cert_amount'    => 'number',
			'thereyare_cert_buyer'     => 'string',
			'thereyare_cert_recipient' => 'string',
			'thereyare_cert_status'    => 'string',
			'thereyare_cert_note'      => 'string',
		];

		foreach ( $fields as $key => $type ) {
			register_post_meta(
				'certificate',
				$key,
				[
					'type'              => $type,
					'single'            => true,
					'default'           => '',
					'show_in_rest'      => true,
					'sanitize_callback' => 'number' === $type ? 'floatval' : 'sanitize_text_field',
					'auth_callback'     => static fn() => current_user_can( 'edit_posts' ),
				]
			);
		}
	}
);

/**
 * Characters a code may contain.
 *
 * Deliberately missing 0/O, 1/I/L, 2/Z, 5/S, 6/G and 8/B: these codes get read
 * aloud on the phone and copied off a printed card, and every one of those
 * pairs is a support email waiting to happen.
 */
const THEREYARE_CODE_ALPHABET = 'ACDEFHJKMNPQRTUVWXY3479';

/**
 * A code in the shape TY-XXXX-XXXX, checked against what has already been
 * issued. Uses random_int, which is cryptographically secure — a predictable
 * certificate code is a certificate anyone can spend.
 */
function thereyare_new_certificate_code(): string {
	$alphabet = THEREYARE_CODE_ALPHABET;
	$max      = strlen( $alphabet ) - 1;

	for ( $attempt = 0; $attempt < 12; $attempt++ ) {
		$groups = [];
		for ( $g = 0; $g < 2; $g++ ) {
			$part = '';
			for ( $i = 0; $i < 4; $i++ ) {
				$part .= $alphabet[ random_int( 0, $max ) ];
			}
			$groups[] = $part;
		}
		$code = 'TY-' . implode( '-', $groups );

		$taken = get_posts(
			[
				'post_type'      => 'certificate',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => 'thereyare_cert_code',
				'meta_value'     => $code,
			]
		);

		if ( ! $taken ) {
			return $code;
		}
	}

	// Twelve collisions against a 23^8 space means something is wrong with the
	// query, not with luck. Fall back to a stamp rather than issuing a blank.
	return 'TY-' . strtoupper( substr( bin2hex( random_bytes( 4 ) ), 0, 8 ) );
}

/**
 * Give every certificate a code the moment it is first saved, so the studio
 * never has to invent one and two certificates can never share one.
 */
add_action(
	'save_post_certificate',
	function ( int $post_id, $post, bool $update ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! get_post_meta( $post_id, 'thereyare_cert_code', true ) ) {
			update_post_meta( $post_id, 'thereyare_cert_code', thereyare_new_certificate_code() );
		}

		if ( ! get_post_meta( $post_id, 'thereyare_cert_status', true ) ) {
			update_post_meta( $post_id, 'thereyare_cert_status', 'active' );
		}
	},
	10,
	3
);

/**
 * Admin list columns — the four things you actually look for when someone
 * rings up holding a certificate.
 */
add_filter(
	'manage_certificate_posts_columns',
	function ( array $columns ): array {
		$date = $columns['date'] ?? '';
		unset( $columns['date'] );

		$columns['thereyare_cert_code']      = __( 'Code', 'thereyare' );
		$columns['thereyare_cert_item']      = __( 'For', 'thereyare' );
		$columns['thereyare_cert_recipient'] = __( 'Recipient', 'thereyare' );
		$columns['thereyare_cert_status']    = __( 'Status', 'thereyare' );
		$columns['date']                     = $date;

		return $columns;
	}
);

add_action(
	'manage_certificate_posts_custom_column',
	function ( string $column, int $post_id ): void {
		if ( 0 !== strpos( $column, 'thereyare_cert_' ) ) {
			return;
		}

		$value = (string) get_post_meta( $post_id, $column, true );

		if ( 'thereyare_cert_code' === $column ) {
			echo '<code style="font-size:13px">' . esc_html( $value ) . '</code>';
			return;
		}

		if ( 'thereyare_cert_item' === $column ) {
			$item = thereyare_gift_item( $value );
			echo esc_html( $item ? $item['title'] : $value );
			return;
		}

		if ( 'thereyare_cert_status' === $column ) {
			$colour = 'redeemed' === $value ? '#6B625D' : '#B23A26';
			echo '<span style="color:' . esc_attr( $colour ) . ';font-weight:600">' . esc_html( ucfirst( $value ?: 'active' ) ) . '</span>';
			return;
		}

		echo esc_html( $value );
	},
	10,
	2
);

/* -------------------------------------------------------------------------
 * Printable certificate
 * ---------------------------------------------------------------------- */

/**
 * A "Print" action on each row in the certificates list.
 */
add_filter(
	'post_row_actions',
	function ( array $actions, $post ): array {
		if ( 'certificate' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$actions['thereyare_print'] = sprintf(
			'<a href="%s" target="_blank" rel="noopener">%s</a>',
			esc_url(
				wp_nonce_url(
					admin_url( 'admin.php?page=thereyare-certificate-print&certificate=' . $post->ID ),
					'thereyare_print_' . $post->ID
				)
			),
			esc_html__( 'Print / save as PDF', 'thereyare' )
		);

		return $actions;
	},
	10,
	2
);

/**
 * The print view lives behind admin-only routing rather than a public URL:
 * a certificate carries a spendable code, so it must never be reachable by
 * guessing an address.
 */
add_action(
	'admin_menu',
	function () {
		add_submenu_page(
			'', // Hidden from the menu; reached from the row action.
			__( 'Certificate', 'thereyare' ),
			__( 'Certificate', 'thereyare' ),
			'edit_posts',
			'thereyare-certificate-print',
			'thereyare_render_certificate_print'
		);
	}
);

/**
 * Renders one certificate, sized for a sheet of A4 or US Letter, and opens
 * the browser's print dialogue. Printing to PDF from the browser is what
 * every operating system already does well; adding a PDF library to the site
 * to reproduce it would be weight for nothing.
 */
function thereyare_render_certificate_print(): void {
	$id = isset( $_GET['certificate'] ) ? absint( $_GET['certificate'] ) : 0;

	if ( ! $id || ! current_user_can( 'edit_post', $id ) ) {
		wp_die( esc_html__( 'You cannot view this certificate.', 'thereyare' ) );
	}

	check_admin_referer( 'thereyare_print_' . $id );

	$post = get_post( $id );
	if ( ! $post || 'certificate' !== $post->post_type ) {
		wp_die( esc_html__( 'Certificate not found.', 'thereyare' ) );
	}

	$code      = (string) get_post_meta( $id, 'thereyare_cert_code', true );
	$item_key  = (string) get_post_meta( $id, 'thereyare_cert_item', true );
	$amount    = (float) get_post_meta( $id, 'thereyare_cert_amount', true );
	$recipient = (string) get_post_meta( $id, 'thereyare_cert_recipient', true );
	$buyer     = (string) get_post_meta( $id, 'thereyare_cert_buyer', true );

	$item  = thereyare_gift_item( $item_key );
	$title = $item ? $item['title'] : __( 'A ThereYare session', 'thereyare' );
	$meta  = $item['meta'] ?? '';

	if ( ! $amount && $item ) {
		$amount = (float) $item['price'];
	}

	$studio = thereyare_setting( 'thereyare_studio_name' );
	$phone  = thereyare_setting( 'thereyare_phone' );
	$email  = thereyare_setting( 'thereyare_email' );

	?>
	<!doctype html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<title><?php echo esc_html( $code ); ?></title>
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght,SOFT,WONK@9..144,400;9..144,600;9..144,700&family=Karla:wght@400;600;700&display=swap" rel="stylesheet">
		<style>
			:root{--ivory:#FFF9F2;--sand:#F6EADD;--blush:#FFEDE9;--graphite:#1A1614;--dim:#6B625D;--hair:#E3DAD2;--brick:#B23A26;--coral:#FF6F5E}
			*{box-sizing:border-box;margin:0;padding:0}
			body{background:#E8E2DA;font-family:"Karla",system-ui,sans-serif;color:var(--graphite);padding:32px 16px;-webkit-print-color-adjust:exact;print-color-adjust:exact}
			.sheet{width:min(1000px,100%);margin:0 auto;background:var(--ivory);border-radius:22px;padding:clamp(32px,5vw,64px);box-shadow:0 20px 60px rgba(26,22,20,.18)}
			.rule{height:6px;background:linear-gradient(90deg,var(--coral),var(--brick));border-radius:999px;margin-bottom:clamp(28px,4vw,44px)}
			.brand{font-family:"Fraunces",Georgia,serif;font-weight:700;font-size:28px;letter-spacing:-.02em}
			.brand small{display:block;font-family:"Karla",sans-serif;font-weight:600;font-size:10px;letter-spacing:.18em;text-transform:uppercase;color:var(--brick);margin-top:6px}
			.kicker{font-size:11px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--brick);margin-top:clamp(28px,4vw,44px)}
			h1{font-family:"Fraunces",Georgia,serif;font-weight:600;font-size:clamp(34px,5.4vw,56px);line-height:1.04;letter-spacing:-.02em;margin:10px 0 6px}
			.meta{color:var(--dim);font-size:15px}
			.amount{font-family:"Fraunces",Georgia,serif;font-weight:700;font-size:clamp(30px,4vw,42px);color:var(--brick);margin-top:18px;font-variant-numeric:tabular-nums}
			.names{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;margin:clamp(28px,4vw,44px) 0;padding:24px 0;border-top:1px solid var(--hair);border-bottom:1px solid var(--hair)}
			.names dt{font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--brick);margin-bottom:6px}
			.names dd{font-family:"Fraunces",Georgia,serif;font-size:22px;line-height:1.25}
			.names dd.blank{border-bottom:1.5px dotted var(--hair);min-height:30px}
			.code{display:inline-block;background:var(--sand);border-radius:12px;padding:14px 22px;font-size:22px;font-weight:700;letter-spacing:.16em;font-variant-numeric:tabular-nums}
			.code span{display:block;font-size:10px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--brick);margin-bottom:4px}
			.foot{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:22px;margin-top:clamp(28px,4vw,44px);font-size:13px;color:var(--dim);line-height:1.6}
			.foot b{color:var(--graphite)}
			.bar{max-width:1000px;margin:0 auto 20px;display:flex;gap:12px;flex-wrap:wrap}
			.bar button,.bar a{font:inherit;font-weight:700;font-size:14px;padding:12px 24px;border-radius:999px;border:0;cursor:pointer;background:var(--brick);color:var(--ivory);text-decoration:none}
			.bar a{background:transparent;color:var(--graphite);border:1.5px solid rgba(26,22,20,.2)}
			@media print{
				body{background:#fff;padding:0}
				.bar{display:none}
				.sheet{box-shadow:none;border-radius:0;width:100%;padding:24mm 20mm}
				@page{margin:0;size:auto}
			}
		</style>
	</head>
	<body>
		<div class="bar">
			<button type="button" onclick="window.print()"><?php esc_html_e( 'Print or save as PDF', 'thereyare' ); ?></button>
			<a href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>"><?php esc_html_e( 'Back to the certificate', 'thereyare' ); ?></a>
		</div>

		<div class="sheet">
			<div class="rule"></div>

			<div class="brand"><?php echo esc_html( $studio ); ?><small>there y&rsquo;are &middot; Los Angeles</small></div>

			<p class="kicker"><?php esc_html_e( 'Gift certificate', 'thereyare' ); ?></p>
			<h1><?php echo esc_html( $title ); ?></h1>
			<?php if ( $meta ) : ?>
				<p class="meta"><?php echo esc_html( $meta ); ?></p>
			<?php endif; ?>
			<?php if ( $amount ) : ?>
				<p class="amount">$<?php echo esc_html( number_format( $amount ) ); ?></p>
			<?php endif; ?>

			<dl class="names">
				<div>
					<dt><?php esc_html_e( 'For', 'thereyare' ); ?></dt>
					<dd class="<?php echo $recipient ? '' : 'blank'; ?>"><?php echo esc_html( $recipient ); ?></dd>
				</div>
				<div>
					<dt><?php esc_html_e( 'From', 'thereyare' ); ?></dt>
					<dd class="<?php echo $buyer ? '' : 'blank'; ?>"><?php echo esc_html( $buyer ); ?></dd>
				</div>
			</dl>

			<p class="code"><span><?php esc_html_e( 'Certificate code', 'thereyare' ); ?></span><?php echo esc_html( $code ); ?></p>

			<div class="foot">
				<p>
					<b><?php esc_html_e( 'To book', 'thereyare' ); ?></b><br>
					<?php esc_html_e( 'Get in touch with the code above and we will find a date.', 'thereyare' ); ?><br>
					<?php echo esc_html( $phone ); ?><br>
					<?php echo esc_html( $email ); ?>
				</p>
				<p>
					<b><?php esc_html_e( 'No expiry date', 'thereyare' ); ?></b><br>
					<?php esc_html_e( 'This certificate does not expire. Use it whenever the moment is right — a new baby, a birthday, or simply a free Saturday.', 'thereyare' ); ?>
				</p>
			</div>
		</div>
	</body>
	</html>
	<?php
	exit;
}
