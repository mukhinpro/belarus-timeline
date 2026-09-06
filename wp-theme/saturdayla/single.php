<?php get_header(); the_post(); ?>
<article>
<section class="page-hero wrap"><?php saturdayla_breadcrumbs(); ?><span class="eyebrow"><time datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time></span><h1><?php the_title(); ?></h1></section>
<section class="wrap" style="padding-top:0">
	<?php if ( has_post_thumbnail() ) echo '<div class="ph" style="max-width:820px;aspect-ratio:3/2;margin-bottom:40px">' . get_the_post_thumbnail( null, 'saturdayla-hero' ) . '</div>'; ?>
	<div class="prose faq"><?php the_content(); ?></div>
</section>
</article>
<?php get_template_part( 'template-parts/book-cta', null, [ 'title' => 'Planning a shoot?' ] ); get_footer();
