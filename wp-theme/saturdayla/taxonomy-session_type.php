<?php get_header(); $term = get_queried_object(); ?>
<section class="page-hero wrap"><?php saturdayla_breadcrumbs(); ?><span class="eyebrow">Portfolio</span><h1><?php echo esc_html( $term->name ); ?></h1><?php if ( $term->description ) echo '<p class="lede">' . esc_html( $term->description ) . '</p>'; ?></section>
<section class="wrap" style="padding-top:0"><div class="sheet">
	<?php while ( have_posts() ) : the_post(); $loc = get_post_meta( get_the_ID(), 'saturdayla_location', true );
		printf( '<a class="sheet__item" href="%s">%s<div class="sheet__cap">%s<small>%s</small></div></a>', esc_url( get_permalink() ), get_the_post_thumbnail( null, 'saturdayla-sheet' ), esc_html( get_the_title() ), esc_html( $loc ) );
	endwhile; ?>
</div></section>
<?php get_template_part( 'template-parts/book-cta' ); get_footer();
