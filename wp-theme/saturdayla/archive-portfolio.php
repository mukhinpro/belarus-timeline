<?php get_header(); ?>
<section class="page-hero wrap"><?php saturdayla_breadcrumbs(); ?><span class="eyebrow">Portfolio</span><h1>Real families, real parties, real weddings.</h1><p class="lede">Tap any photo to open the session.</p></section>
<section class="wrap" style="padding-top:0">
	<ul class="filters"><li><button data-filter="all" class="is-active">All</button></li>
	<?php foreach ( get_terms( [ 'taxonomy' => 'session_type', 'hide_empty' => true ] ) as $t ) printf( '<li><button data-filter="%s">%s</button></li>', esc_attr( $t->slug ), esc_html( $t->name ) ); ?></ul>
	<div class="sheet">
		<?php $q = new WP_Query( [ 'post_type' => 'portfolio', 'posts_per_page' => -1, 'meta_key' => '_thumbnail_id' ] );
		if ( $q->have_posts() ) : while ( $q->have_posts() ) : $q->the_post();
			$loc = get_post_meta( get_the_ID(), 'saturdayla_location', true ); $terms = wp_get_post_terms( get_the_ID(), 'session_type', [ 'fields' => 'slugs' ] );
			printf( '<a class="sheet__item" data-type="%s" href="%s">%s<div class="sheet__cap">%s<small>%s</small></div></a>', esc_attr( implode( ' ', $terms ) ), esc_url( get_permalink() ), get_the_post_thumbnail( null, 'saturdayla-sheet', [ 'alt' => get_the_title() ] ), esc_html( get_the_title() ), esc_html( $loc ) );
		endwhile; wp_reset_postdata(); else : ?><p class="body">No sessions published yet. Add your first one under Portfolio → Add new session.</p><?php endif; ?>
	</div>
</section>
<?php get_template_part( 'template-parts/book-cta' ); get_footer();
