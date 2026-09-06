<?php /* Template Name: Book / Contact */ get_header(); the_post(); ?>
<section class="book" style="padding-top:clamp(44px,7vh,86px)"><div class="wrap">
	<div class="head"><?php saturdayla_breadcrumbs(); ?><span class="eyebrow">Booking</span><h1 style="font-size:clamp(34px,5vw,64px)"><?php the_title(); ?></h1></div>
	<p class="body"><?php echo has_excerpt() ? esc_html( get_the_excerpt() ) : "Send us the date and we'll come back the same day with availability and a quote."; ?></p>
	<?php the_content();
	$form = get_theme_mod( 'saturdayla_form' );
	if ( $form ) :
		echo do_shortcode( $form );
	else : ?>
	<form action="mailto:<?php echo esc_attr( saturdayla_email() ); ?>" method="post" enctype="text/plain">
		<div class="pair"><div><label for="n">Your name</label><input id="n" name="name" type="text" required></div><div><label for="e">Email</label><input id="e" name="email" type="email" required></div></div>
		<div class="pair"><div><label for="d">Date</label><input id="d" name="date" type="date"></div><div><label for="k">What kind of shoot</label><input id="k" name="kind" type="text" placeholder="family session, 5th birthday, wedding…"></div></div>
		<div><label for="l">Where</label><input id="l" name="location" type="text" placeholder="Neighbourhood, park or venue"></div>
		<div><label for="m">Anything we should know</label><textarea id="m" name="message" placeholder="How many people, what time it starts, indoors or out."></textarea></div>
		<button class="btn" type="submit">Send it</button>
	</form>
	<p class="body" style="margin-top:12px;font-size:13px">This fallback form opens your email app. Install WPForms or Contact Form 7 and paste the shortcode in Customizer → Studio settings for a proper form.</p>
	<?php endif; ?>
	<p class="body" style="margin-top:30px">Or reach us directly: <a href="tel:<?php echo esc_attr( preg_replace( '/\D/', '', saturdayla_phone() ) ); ?>"><?php echo esc_html( saturdayla_phone() ); ?></a> · <a href="mailto:<?php echo esc_attr( saturdayla_email() ); ?>"><?php echo esc_html( saturdayla_email() ); ?></a> · <a href="<?php echo esc_url( saturdayla_instagram() ); ?>">Instagram</a></p>
</div></section>
<?php get_footer();
