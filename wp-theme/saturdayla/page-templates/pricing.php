<?php /* Template Name: Pricing (all sessions) */ get_header(); the_post(); ?>
<section class="page-hero wrap"><?php saturdayla_breadcrumbs(); ?><span class="eyebrow">Pricing</span><h1><?php the_title(); ?></h1><p class="lede"><?php echo has_excerpt() ? esc_html( get_the_excerpt() ) : 'Clear prices, no hidden fees. Pick the kind of day, then the package that fits.'; ?></p></section>
<?php if ( trim( get_the_content() ) ) : ?><section class="wrap" style="padding-top:0"><div class="prose"><?php the_content(); ?></div></section><?php endif; ?>
<?php foreach ( [ 'family' => 'Family sessions', 'kids' => 'Kids & teens', 'parties' => 'Birthday parties', 'weddings' => 'Weddings' ] as $t => $label ) : ?>
<section class="wrap" id="<?php echo $t; ?>"><h3 style="margin-bottom:8px;color:var(--brick)"><?php echo esc_html( $label ); ?></h3><?php get_template_part( 'template-parts/packages', null, [ 'type' => $t ] ); ?></section>
<?php endforeach; get_template_part( 'template-parts/book-cta', null, [ 'title' => 'Not sure which one?', 'text' => "Tell us what you have in mind and we'll recommend a package — no pressure." ] ); get_footer();
