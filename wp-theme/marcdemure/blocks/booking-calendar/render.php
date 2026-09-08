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

$md_url   = md_setting( 'md_booking_url' );
$md_class = 'md-calendar' . ( ! empty( $attributes['align'] ) ? ' align' . sanitize_html_class( (string) $attributes['align'] ) : '' );

if ( '' === $md_url ) :
	$md_email = md_setting( 'md_email' );
	$md_phone = md_setting( 'md_phone' );
	?>
	<div <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => $md_class . ' md-calendar--fallback' ] ) ); ?>>
		<p class="md-calendar__lead"><?php esc_html_e( 'Write, or call, and I will reply with two or three dates.', 'marcdemure' ); ?></p>
		<p class="md-calendar__actions">
			<?php if ( '' !== $md_email ) : ?>
				<a class="md-btn" href="mailto:<?php echo esc_attr( $md_email ); ?>"><?php echo esc_html( $md_email ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $md_phone ) : ?>
				<a class="md-btn md-btn--quiet" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $md_phone ) ); ?>"><?php echo esc_html( $md_phone ); ?></a>
			<?php endif; ?>
		</p>
		<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
			<p class="md-calendar__note"><?php esc_html_e( 'Only you see this line: paste a Calendly or Acuity link under Customize → Studio details to show the calendar here.', 'marcdemure' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
	return;
endif;

$md_host = wp_parse_url( $md_url, PHP_URL_HOST ) ?: '';
$md_src  = $md_url;

// Calendly's embed reads the page background; hand it ours so it does not flash white.
if ( str_contains( $md_host, 'calendly.com' ) ) {
	$md_src = add_query_arg(
		[
			'hide_gdpr_banner' => '1',
			'background_color' => '0c0b0a',
			'text_color'       => 'efe6d6',
			'primary_color'    => 'a6461b',
		],
		$md_url
	);
}
?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => $md_class ] ) ); ?>>
	<iframe
		class="md-calendar__frame"
		src="<?php echo esc_url( $md_src ); ?>"
		title="<?php esc_attr_e( 'Choose a date', 'marcdemure' ); ?>"
		loading="lazy"
		referrerpolicy="strict-origin-when-cross-origin"
		allow="payment"
	></iframe>
	<p class="md-calendar__note">
		<?php esc_html_e( 'Prefer to write first?', 'marcdemure' ); ?>
		<a href="mailto:<?php echo esc_attr( md_setting( 'md_email' ) ); ?>"><?php echo esc_html( md_setting( 'md_email' ) ); ?></a>
	</p>
</div>
