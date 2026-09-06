<?php get_header(); the_post(); ?>
<section class="page-hero wrap"><?php saturdayla_breadcrumbs(); ?><h1><?php the_title(); ?></h1><?php if ( has_excerpt() ) echo '<p class="lede">' . esc_html( get_the_excerpt() ) . '</p>'; ?></section>
<section class="wrap" style="padding-top:0">
	<?php if ( has_post_thumbnail() ) echo '<div class="ph" style="max-width:820px;aspect-ratio:4/5;margin-bottom:40px">' . get_the_post_thumbnail( null, 'saturdayla-hero' ) . '</div>'; ?>
	<div class="prose faq"><?php the_content(); ?></div>
</section>
<?php get_template_part( 'template-parts/book-cta' ); get_footer();
