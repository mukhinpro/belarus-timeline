<?php get_header(); ?>
<section class="page-hero wrap"><span class="eyebrow">Journal</span><h1><?php echo is_home() ? 'Notes for parents' : esc_html( get_the_archive_title() ); ?></h1><p class="lede">What to wear, where the light is good, and stories behind recent shoots.</p></section>
<section class="wrap" style="padding-top:0">
	<?php if ( have_posts() ) : ?><div class="posts">
		<?php while ( have_posts() ) : the_post(); ?>
		<a class="post-card" href="<?php the_permalink(); ?>"><?php echo has_post_thumbnail() ? get_the_post_thumbnail( null, 'saturdayla-sheet' ) : '<div class="ph" style="aspect-ratio:4/3"></div>'; ?><h3><?php the_title(); ?></h3><time datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time></a>
		<?php endwhile; ?>
	</div><div style="margin-top:32px"><?php the_posts_pagination(); ?></div>
	<?php else : ?><p class="body">Nothing here yet.</p><?php endif; ?>
</section>
<?php get_footer();
