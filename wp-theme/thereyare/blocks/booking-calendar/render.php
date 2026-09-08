<?php
/**
 * Booking calendar.
 *
 * The scheduler is Calendly or Acuity, embedded. Both can take the deposit
 * at booking through their own Stripe link, which removes the whole
 * "are you free on the 14th" thread. Until a link is set, the block renders
 * the phone and email instead of an empty frame.
 *
 * The frame is lazy and below the page copy on purpose: it is the heaviest
 * thing on the booking page and it must not delay the words above it.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tya_url   = thereyare_setting( 'thereyare_booking_url' );
$tya_class = 'tya-calendar' . ( ! empty( $attributes['align'] ) ? ' align' . sanitize_html_class( (string) $attributes['align'] ) : '' );

if ( '' === $tya_url ) :
	$thereyare_email = thereyare_setting( 'thereyare_email' );
	$thereyare_phone = thereyare_setting( 'thereyare_phone' );
	?>
	<div <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => $tya_class . ' tya-calendar--fallback' ] ) ); ?>>
		<p class="tya-calendar__lead"><?php esc_html_e( 'Write, or call, and I will reply with two or three dates.', 'thereyare' ); ?></p>
		<p class="tya-calendar__actions">
			<?php if ( '' !== $thereyare_email ) : ?>
				<a class="tya-btn" href="mailto:<?php echo esc_attr( $thereyare_email ); ?>"><?php echo esc_html( $thereyare_email ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $thereyare_phone ) : ?>
				<a class="tya-btn tya-btn--quiet" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $thereyare_phone ) ); ?>"><?php echo esc_html( $thereyare_phone ); ?></a>
			<?php endif; ?>
		</p>
		<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
			<p class="tya-calendar__note"><?php esc_html_e( 'Only you see this line: paste a Calendly or Acuity link under Customize → Studio details to show the calendar here.', 'thereyare' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
	return;
endif;

$tya_host = wp_parse_url( $tya_url, PHP_URL_HOST ) ?: '';
$tya_src  = $tya_url;

// Calendly's embed reads the page background; hand it ours so it does not flash white.
if ( str_contains( $tya_host, 'calendly.com' ) ) {
	$tya_src = add_query_arg(
		[
			'hide_gdpr_banner' => '1',
			'background_color' => 'fff9f2',
			'text_color'       => '1a1614',
			'primary_color'    => 'b23a26',
		],
		$tya_url
	);
}
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => $tya_class ] ) ); ?>>
	<iframe
		class="tya-calendar__frame"
		src="<?php echo esc_url( $tya_src ); ?>"
		title="<?php esc_attr_e( 'Choose a date', 'thereyare' ); ?>"
		loading="lazy"
		referrerpolicy="strict-origin-when-cross-origin"
		allow="payment"
	></iframe>
	<p class="tya-calendar__note">
		<?php esc_html_e( 'Prefer to write first?', 'thereyare' ); ?>
		<a href="mailto:<?php echo esc_attr( thereyare_setting( 'thereyare_email' ) ); ?>"><?php echo esc_html( thereyare_setting( 'thereyare_email' ) ); ?></a>
	</p>
</div>
