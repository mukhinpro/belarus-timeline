<section class="book" id="book"><div class="wrap">
	<div class="cta reveal" style="display:flex;flex-wrap:wrap;gap:24px;align-items:center;justify-content:space-between">
		<div><span class="eyebrow">Booking</span><h2 style="margin:12px 0 10px"><?php echo esc_html( $args['title'] ?? 'Tell us about your day.' ); ?></h2><p class="body"><?php echo esc_html( $args['text'] ?? "Send the date and we'll come back the same day with availability and a quote." ); ?></p></div>
		<a class="btn" href="<?php echo esc_url( saturdayla_book_url() ); ?>">Check my date</a>
	</div>
</div></section>
