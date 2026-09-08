<?php
/**
 * Server render for marcdemure/gift-chooser.
 *
 * Every option is a real link to its own Stripe Payment Link. The script in
 * view.js upgrades that into pick-then-confirm, which is the friendlier
 * interaction, but with JavaScript off or still loading each card remains a
 * working link straight to checkout. Nobody is ever left with a page of
 * buttons that do nothing.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$sessions = [];
$amounts  = [];

foreach ( md_gift_items() as $item ) {
	$link = md_gift_link( $item['key'] );
	if ( ! $link ) {
		continue; // Not on sale yet.
	}
	$item['link'] = $link;
	if ( 'session' === $item['group'] ) {
		$sessions[] = $item;
	} else {
		$amounts[] = $item;
	}
}

$wrapper = get_block_wrapper_attributes( [ 'class' => 'md-gift' ] );

// Nothing configured yet. Say so to whoever can fix it, and keep the page
// useful for everyone else instead of rendering an empty box.
if ( ! $sessions && ! $amounts ) {
	printf( '<div %s>', $wrapper ); // phpcs:ignore WordPress.Security.EscapeOutput -- built by core.

	if ( current_user_can( 'edit_theme_options' ) ) {
		echo '<p class="md-gift__setup">';
		esc_html_e( 'No payment links yet. Add them under Appearance → Customize → Gift certificates, and each one appears here as soon as it is saved. Only you can see this message.', 'marcdemure' );
		echo '</p>';
	}

	printf(
		'<p><a class="wp-element-button" href="mailto:%1$s?subject=%2$s">%3$s</a></p>',
		esc_attr( md_setting( 'md_email' ) ),
		esc_attr( rawurlencode( 'Gift certificate' ) ),
		esc_html__( 'Email us about a gift certificate', 'marcdemure' )
	);

	echo '</div>';
	return;
}

/**
 * One selectable card.
 */
$card = static function ( array $item ): string {
	return sprintf(
		'<a class="md-gift__card" href="%1$s" data-key="%2$s" data-title="%3$s" data-price="%4$s">
			<span class="md-gift__title">%3$s</span>
			<span class="md-gift__price">$%5$s</span>
			<span class="md-gift__meta">%6$s</span>
			<span class="md-gift__mark" aria-hidden="true"></span>
		</a>',
		esc_url( $item['link'] ),
		esc_attr( $item['key'] ),
		esc_html( $item['title'] ),
		esc_attr( (string) $item['price'] ),
		esc_html( number_format( (int) $item['price'] ) ),
		esc_html( $item['meta'] )
	);
};

printf( '<div %s>', $wrapper ); // phpcs:ignore WordPress.Security.EscapeOutput -- built by core.

if ( $sessions ) {
	echo '<h2 class="md-gift__heading">' . esc_html__( 'Give a session', 'marcdemure' ) . '</h2>';
	echo '<p class="md-gift__lede">' . esc_html__( 'They book the shoot itself. Nothing to top up, nothing left over.', 'marcdemure' ) . '</p>';
	echo '<div class="md-gift__grid" role="list">';
	foreach ( $sessions as $item ) {
		echo '<div role="listitem">' . $card( $item ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $card.
	}
	echo '</div>';
}

if ( $amounts ) {
	echo '<h2 class="md-gift__heading">' . esc_html__( 'Or give an amount', 'marcdemure' ) . '</h2>';
	echo '<p class="md-gift__lede">' . esc_html__( 'They put it towards whichever session suits them. Any balance stays on the certificate.', 'marcdemure' ) . '</p>';
	echo '<div class="md-gift__grid md-gift__grid--amounts" role="list">';
	foreach ( $amounts as $item ) {
		echo '<div role="listitem">' . $card( $item ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in $card.
	}
	echo '</div>';
}

// The confirm bar. Hidden until something is picked; irrelevant without JS,
// where the cards themselves are already the links.
printf(
	'<div class="md-gift__bar" hidden>
		<p class="md-gift__chosen"><span class="md-gift__chosen-label">%1$s</span> <strong data-chosen></strong></p>
		<a class="wp-element-button md-gift__go" href="#" data-go>%2$s</a>
	</div>',
	esc_html__( 'Chosen:', 'marcdemure' ),
	esc_html__( 'Continue to payment', 'marcdemure' )
);

echo '</div>';
