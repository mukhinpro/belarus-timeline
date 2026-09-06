<?php
/**
 * Template Name: Service page (Family / Kids / Parties / Weddings)
 * Featured image = hero photo (portrait 4:5). Excerpt = the one-line lede.
 * Page content = your text. A Gallery block renders as the photo collage; <details> blocks become FAQ + Google FAQ schema.
 */
get_header(); the_post();
$map  = [ 'family-photography-los-angeles' => 'family', 'kids-photographer-los-angeles' => 'kids', 'kids-party-photography-los-angeles' => 'parties', 'wedding-photography-los-angeles' => 'weddings', 'editorial-kids-photography-los-angeles' => 'editorial' ];
$type = $map[ get_post_field( 'post_name' ) ] ?? 'family';
$eyebrow = get_post_meta( get_the_ID(), 'saturdayla_eyebrow', true ) ?: ucfirst( $type ) . ' · Los Angeles';
?>
<section class="hero wrap">
	<div class="grid">
		<div>
			<?php saturdayla_breadcrumbs(); ?>
			<span class="eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) echo '<p class="lede">' . esc_html( get_the_excerpt() ) . '</p>'; ?>
			<div class="actions"><a class="btn" href="<?php echo esc_url( saturdayla_book_url() ); ?>">Check my date</a><a class="btn btn--quiet" href="#pricing">See prices</a></div>
		</div>
		<div class="shot"><div class="ph"><?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'saturdayla-hero', [ 'alt' => get_the_title() ] ) : '<span class="ph-label">Set a featured image</span>'; ?></div></div>
	</div>
</section>

<?php $q = new WP_Query( [ 'post_type' => 'portfolio', 'posts_per_page' => 5, 'tax_query' => [ [ 'taxonomy' => 'session_type', 'field' => 'slug', 'terms' => $type ] ], 'meta_key' => '_thumbnail_id' ] );
if ( $q->have_posts() ) : ?>
<section class="collage wrap">
	<div class="head reveal"><span class="eyebrow">The photos</span><h2>Recent <?php echo esc_html( 'parties' === $type ? 'parties' : $type . ' sessions' ); ?></h2></div>
	<div class="board reveal"><?php $i = 1; while ( $q->have_posts() && $i <= 5 ) { $q->the_post(); printf( '<a class="card c%d" href="%s"><div class="ph">%s</div></a>', $i++, esc_url( get_permalink() ), get_the_post_thumbnail( null, 'saturdayla-sheet' ) ); } wp_reset_postdata(); ?></div>
</section>
<?php endif; ?>

<section class="wrap"><div class="prose faq reveal"><?php the_content(); ?></div></section>

<section class="wrap" id="pricing"><?php get_template_part( 'template-parts/packages', null, [ 'type' => $type ] ); ?></section>

<?php get_template_part( 'template-parts/book-cta', null, [ 'title' => 'parties' === $type ? 'Tell us about the party.' : 'Tell us about your day.' ] ); get_footer();
