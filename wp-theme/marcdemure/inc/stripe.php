<?php
/**
 * Billing through Stripe.
 *
 * The site never holds a card number and never calls the Stripe API with a
 * secret key. Visitors leave for a Stripe Payment Link in subscription mode,
 * and Stripe tells us what happened over a signed webhook. That keeps the
 * blast radius of a site compromise down to "someone edited the copy".
 *
 * The webhook is the only thing on this site that can grant access, and it
 * only does so after verifying Stripe's signature.
 *
 * Put the signing secret in wp-config.php, never in the database:
 *
 *     define( 'MD_STRIPE_WEBHOOK_SECRET', 'whsec_...' );
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tolerance for the webhook timestamp, in seconds. Stripe recommends five
 * minutes; anything older is a replay and is refused.
 */
const MD_STRIPE_TOLERANCE = 300;

/**
 * Days of access granted the moment a checkout completes, before the
 * subscription events arrive with exact dates. Generous on purpose: a member
 * who just paid must never see a locked door.
 */
const MD_STRIPE_PROVISIONAL_DAYS = 32;

/**
 * Grace period added to every period end so a renewal in flight does not lock
 * someone out for a few hours.
 */
const MD_STRIPE_GRACE_DAYS = 2;

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_section(
			'md_billing',
			[
				'title'       => __( 'Membership billing', 'marcdemure' ),
				'priority'    => 21,
				'description' => __( 'Paste the Stripe links here. The webhook signing secret belongs in wp-config.php, not on this screen.', 'marcdemure' ),
			]
		);

		$fields = [
			'md_stripe_checkout' => __( 'Stripe Payment Link, subscription mode', 'marcdemure' ),
			'md_stripe_portal'   => __( 'Stripe customer portal link', 'marcdemure' ),
			'md_price_month'     => __( 'Price shown on the join page, e.g. $19', 'marcdemure' ),
		];

		foreach ( $fields as $key => $label ) {
			$is_url = str_starts_with( $key, 'md_stripe' );

			$wp_customize->add_setting(
				$key,
				[
					'default'           => '',
					'sanitize_callback' => $is_url ? 'esc_url_raw' : 'sanitize_text_field',
				]
			);
			$wp_customize->add_control(
				$key,
				[
					'label'   => $label,
					'section' => 'md_billing',
					'type'    => $is_url ? 'url' : 'text',
				]
			);
		}
	}
);

/**
 * Where the Join button goes.
 *
 * A logged-in visitor carries their user id to Stripe as the client reference,
 * which is what lets the webhook match the payment back to an account instead
 * of guessing from an email address.
 */
function md_stripe_checkout_url(): string {
	$url = (string) get_theme_mod( 'md_stripe_checkout', '' );

	if ( '' === $url ) {
		return md_join_url();
	}

	$user = wp_get_current_user();

	if ( $user->exists() ) {
		$url = add_query_arg(
			[
				'client_reference_id' => 'wp_' . $user->ID,
				'prefilled_email'     => rawurlencode( $user->user_email ),
			],
			$url
		);
	}

	return $url;
}

/**
 * Stripe's own billing portal, where a member cancels without emailing anyone.
 */
function md_stripe_portal_url(): string {
	return (string) get_theme_mod( 'md_stripe_portal', '' );
}

/**
 * The webhook endpoint. Public by necessity, and secured by signature rather
 * than by being hard to find.
 */
add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'md/v1',
			'/stripe',
			[
				'methods'             => 'POST',
				'callback'            => 'md_stripe_webhook',
				'permission_callback' => '__return_true',
			]
		);
	}
);

/**
 * Verify Stripe's signature over the raw body.
 *
 * Rebuilds the signed payload exactly as Stripe did, compares in constant time
 * so the check cannot be probed a byte at a time, and refuses anything older
 * than the tolerance above.
 */
function md_stripe_signature_valid( string $payload, string $header, string $secret ): bool {
	if ( '' === $secret || '' === $header ) {
		return false;
	}

	$timestamp  = 0;
	$signatures = [];

	foreach ( explode( ',', $header ) as $part ) {
		$pair = explode( '=', trim( $part ), 2 );
		if ( 2 !== count( $pair ) ) {
			continue;
		}
		if ( 't' === $pair[0] ) {
			$timestamp = (int) $pair[1];
		} elseif ( 'v1' === $pair[0] ) {
			$signatures[] = $pair[1];
		}
	}

	if ( ! $timestamp || ! $signatures ) {
		return false;
	}

	if ( abs( time() - $timestamp ) > MD_STRIPE_TOLERANCE ) {
		return false;
	}

	$expected = hash_hmac( 'sha256', $timestamp . '.' . $payload, $secret );

	foreach ( $signatures as $signature ) {
		if ( hash_equals( $expected, $signature ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Handle a verified Stripe event.
 */
function md_stripe_webhook( WP_REST_Request $request ) {
	$secret = defined( 'MD_STRIPE_WEBHOOK_SECRET' ) ? (string) MD_STRIPE_WEBHOOK_SECRET : '';

	if ( '' === $secret ) {
		return new WP_Error( 'md_no_secret', 'Webhook secret is not configured.', [ 'status' => 500 ] );
	}

	$payload = $request->get_body();
	$header  = (string) $request->get_header( 'stripe_signature' );

	if ( ! md_stripe_signature_valid( $payload, $header, $secret ) ) {
		return new WP_Error( 'md_bad_signature', 'Signature verification failed.', [ 'status' => 400 ] );
	}

	$event = json_decode( $payload, true );

	if ( ! is_array( $event ) || empty( $event['type'] ) ) {
		return new WP_Error( 'md_bad_payload', 'Unreadable event.', [ 'status' => 400 ] );
	}

	$object = $event['data']['object'] ?? [];

	switch ( $event['type'] ) {
		case 'checkout.session.completed':
			md_stripe_handle_checkout( is_array( $object ) ? $object : [] );
			break;

		case 'customer.subscription.created':
		case 'customer.subscription.updated':
			md_stripe_handle_subscription( is_array( $object ) ? $object : [] );
			break;

		case 'customer.subscription.deleted':
			md_stripe_handle_cancellation( is_array( $object ) ? $object : [] );
			break;
	}

	return new WP_REST_Response( [ 'received' => true ], 200 );
}

/**
 * A checkout finished. Find or create the account, remember the Stripe
 * customer id, and open the door straight away.
 *
 * @param array<string, mixed> $session
 */
function md_stripe_handle_checkout( array $session ): void {
	$email       = (string) ( $session['customer_details']['email'] ?? $session['customer_email'] ?? '' );
	$customer_id = (string) ( $session['customer'] ?? '' );
	$reference   = (string) ( $session['client_reference_id'] ?? '' );

	$user_id = 0;

	if ( str_starts_with( $reference, 'wp_' ) ) {
		$candidate = (int) substr( $reference, 3 );
		if ( $candidate && get_userdata( $candidate ) ) {
			$user_id = $candidate;
		}
	}

	if ( ! $user_id && $email && is_email( $email ) ) {
		$user = get_user_by( 'email', $email );

		if ( $user ) {
			$user_id = (int) $user->ID;
		} else {
			$user_id = md_create_member( $email );
		}
	}

	if ( ! $user_id ) {
		return;
	}

	if ( $customer_id ) {
		update_user_meta( $user_id, 'md_stripe_customer', sanitize_text_field( $customer_id ) );
	}

	md_grant_access( $user_id, time() + ( MD_STRIPE_PROVISIONAL_DAYS * DAY_IN_SECONDS ), 'active' );
}

/**
 * A subscription was created or changed. Trust its period end over anything
 * we guessed earlier.
 *
 * @param array<string, mixed> $subscription
 */
function md_stripe_handle_subscription( array $subscription ): void {
	$user_id = md_user_for_customer( (string) ( $subscription['customer'] ?? '' ) );

	if ( ! $user_id ) {
		return;
	}

	$status = (string) ( $subscription['status'] ?? '' );
	$period = (int) ( $subscription['current_period_end'] ?? 0 );

	$live = in_array( $status, [ 'active', 'trialing', 'past_due' ], true );

	if ( $live && $period ) {
		md_grant_access( $user_id, $period + ( MD_STRIPE_GRACE_DAYS * DAY_IN_SECONDS ), $status );
		return;
	}

	if ( ! $live ) {
		md_revoke_access( $user_id, $status ?: 'inactive' );
	}
}

/**
 * The subscription ended.
 *
 * @param array<string, mixed> $subscription
 */
function md_stripe_handle_cancellation( array $subscription ): void {
	$user_id = md_user_for_customer( (string) ( $subscription['customer'] ?? '' ) );

	if ( $user_id ) {
		md_revoke_access( $user_id, 'canceled' );
	}
}

/**
 * Which account belongs to this Stripe customer?
 */
function md_user_for_customer( string $customer_id ): int {
	if ( '' === $customer_id ) {
		return 0;
	}

	$users = get_users(
		[
			'meta_key'   => 'md_stripe_customer', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => $customer_id,         // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'number'     => 1,
			'fields'     => 'ID',
		]
	);

	return $users ? (int) $users[0] : 0;
}

/**
 * Create an account for someone who paid before registering, and send them a
 * link to set their own password. We never invent one and email it.
 */
function md_create_member( string $email ): int {
	$username = sanitize_user( current( explode( '@', $email ) ), true );

	if ( '' === $username || username_exists( $username ) ) {
		$username = 'member_' . wp_generate_password( 8, false, false );
	}

	$user_id = wp_create_user( $username, wp_generate_password( 24 ), $email );

	if ( is_wp_error( $user_id ) ) {
		return 0;
	}

	$user = new WP_User( $user_id );
	$user->set_role( 'subscriber' );

	// The standard "set your password" email, so no password ever travels by mail.
	retrieve_password( $user->user_login );

	return (int) $user_id;
}
