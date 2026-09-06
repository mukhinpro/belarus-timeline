<?php get_header(); $owner = get_theme_mod( 'saturdayla_owner', 'SaturdayLa' ); $first = explode( ' ', $owner )[0]; ?>

<section class="hero wrap">
	<div class="grid">
		<div>
			<span class="eyebrow"><?php echo esc_html( get_theme_mod( 'saturdayla_hero_eyebrow', 'Kids & family photography studio · Los Angeles' ) ); ?></span>
			<h1><?php echo esc_html( get_theme_mod( 'saturdayla_hero_title', "You'll be in the photos too, for once." ) ); ?></h1>
			<p class="lede"><?php echo esc_html( get_theme_mod( 'saturdayla_hero_text', "Family sessions, kids' portraits and birthday parties — photographed so you can put your phone down and actually be there." ) ); ?></p>
			<div class="actions">
				<a class="btn" href="<?php echo esc_url( saturdayla_book_url() ); ?>">Check my date</a>
				<span class="note"><?php echo esc_html( get_theme_mod( 'saturdayla_hero_note', 'Usually booked 3–4 weeks out.' ) ); ?></span>
			</div>
		</div>
		<div class="shot"><?php echo saturdayla_img( 'saturdayla_hero_image', 'saturdayla-hero', 'Family photo session in Los Angeles by ' . $owner, [], 'Hero photo — a kid mid-laugh, parent just behind' ); ?></div>
	</div>
</section>

<section class="wrap">
	<div class="head reveal"><span class="eyebrow">What we shoot</span><h2>Pick the kind of day<br>you're planning.</h2></div>
	<div class="types reveal">
		<?php foreach ( [
			[ 'family', 'Family sessions', 'Studio, home or the city', 'family-photography-los-angeles' ],
			[ 'kids', 'Kids & teens', 'Character portraits, not school photos', 'kids-photographer-los-angeles' ],
			[ 'party', 'Birthday parties', 'The whole thing, start to cake to meltdown', 'kids-party-photography-los-angeles' ],
			[ 'wedding', 'Weddings', 'Documentary with an editorial eye', 'wedding-photography-los-angeles' ],
		] as [ $key, $label, $sub, $slug ] ) : ?>
		<a class="type" href="<?php echo esc_url( home_url( '/' . $slug . '/' ) ); ?>">
			<?php echo saturdayla_img( 'saturdayla_img_' . $key, 'saturdayla-portrait', $label . ' photography in Los Angeles', [], $label ); ?>
			<h3><?php echo esc_html( $label ); ?></h3><span><?php echo esc_html( $sub ); ?></span>
		</a>
		<?php endforeach; ?>
	</div>
</section>

<?php $recent = new WP_Query( [ 'post_type' => 'portfolio', 'posts_per_page' => 5, 'meta_key' => '_thumbnail_id' ] ); ?>
<section class="collage wrap">
	<div class="head reveal"><span class="eyebrow">The photos</span><h2>An hour with your family<br>is about forty real moments.</h2></div>
	<div class="board reveal">
		<?php $i = 1; $labels = [ 'Wide — the whole family', 'Candid — kids running', 'Close — hands and cake', 'Portrait — the birthday kid', 'Detail — decorations' ];
		if ( $recent->have_posts() ) { while ( $recent->have_posts() && $i <= 5 ) { $recent->the_post();
			printf( '<a class="card c%d" href="%s"><div class="ph">%s</div></a>', $i, esc_url( get_permalink() ), get_the_post_thumbnail( null, 'saturdayla-sheet', [ 'alt' => get_the_title() ] ) ); $i++;
		} wp_reset_postdata(); }
		while ( $i <= 5 ) { printf( '<div class="card c%d"><div class="ph"><span class="ph-label">%s</span></div></div>', $i, esc_html( $labels[ $i - 1 ] ) ); $i++; } ?>
	</div>
	<p class="cap reveal">Every frame here is from a real session. Nothing staged, nothing stock.</p>
</section>

<section class="wrap">
	<div class="head reveal"><span class="eyebrow">How it goes</span><h2>What actually happens<br>on the day.</h2></div>
	<div class="flow reveal">
		<div class="item"><span class="eyebrow">First</span><h3>We pick a place and a look</h3><p class="body">Studio, your living room or a park with good light. We send a short outfit guide so everyone looks put together, not matching.</p></div>
		<div class="item"><span class="eyebrow">Then</span><h3>We let it happen</h3><p class="body">No lining kids up, no "everybody say cheese". Light direction for the grown-ups, then we step back. Kids ignore us within ten minutes.</p></div>
		<div class="item"><span class="eyebrow">After</span><h3>You get the gallery</h3><p class="body">A private link within two weeks. Retouched by hand, full resolution, yours to print and send to grandma.</p></div>
	</div>
</section>

<section class="wrap">
	<div class="head reveal"><span class="eyebrow">Parents say</span><h2>Mostly about the kid<br>who "hates photos".</h2></div>
	<div class="quotes reveal">
		<blockquote class="quote">"Our son refused to look at the camera for twenty minutes. Somehow every single photo is perfect."<cite>Elena, family session, Pasadena</cite></blockquote>
		<blockquote class="quote">"He shot the whole party and I didn't touch my phone once. First time I'm actually in the pictures."<cite>Maria, 5th birthday, Santa Monica</cite></blockquote>
		<blockquote class="quote">"The library shoot looked like a magazine spread. Their agency used the photos for comp cards."<cite>Natalie, kids editorial session</cite></blockquote>
	</div>
</section>

<section class="wrap">
	<div class="about">
		<div class="portrait reveal"><?php echo saturdayla_img( 'saturdayla_about_image', 'saturdayla-portrait', $owner . ' team, Los Angeles', [], 'Portrait of the ' . $first . ' team' ); ?></div>
		<div class="reveal">
			<span class="eyebrow">Who's behind the camera</span>
			<h2 style="margin:12px 0 20px">We're <?php echo esc_html( $first ); ?>.</h2>
			<p class="body" style="margin-bottom:16px">A small creative team of portrait and editorial photographers in Los Angeles, built around one thing: making kids and families look like themselves, not like they're being photographed.<?php $press = get_theme_mod( 'saturdayla_press', '' ); if ( $press ) echo ' Published in ' . esc_html( $press ) . '.'; ?></p>
			<p class="body" style="margin-bottom:26px">Kids are the whole point. Same care, same eye for light, way more running.</p>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">More about us →</a>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/book-cta' ); get_footer();
