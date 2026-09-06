<?php get_header(); the_post(); $loc = get_post_meta( get_the_ID(), 'saturdayla_location', true ); $imgs = saturdayla_session_images( get_the_ID() ); ?>
<section class="page-hero wrap"><?php saturdayla_breadcrumbs(); ?><?php if ( $loc ) echo '<span class="eyebrow">' . esc_html( $loc ) . '</span>'; ?><h1><?php the_title(); ?></h1><?php if ( has_excerpt() ) echo '<p class="lede">' . esc_html( get_the_excerpt() ) . '</p>'; ?></section>
<section class="wrap" style="padding-top:0">
	<div class="sheet"><?php foreach ( $imgs as $id ) saturdayla_sheet_item( get_the_ID(), $id ); ?></div>
	<?php $content = get_the_content();
	if ( has_blocks( $content ) ) $content = implode( '', array_map( 'render_block', array_filter( parse_blocks( $content ), fn( $b ) => 'core/gallery' !== $b['blockName'] ) ) );
	if ( trim( wp_strip_all_tags( $content ) ) ) echo '<div class="prose" style="margin-top:40px">' . apply_filters( 'the_content', $content ) . '</div>'; ?>
</section>
<?php get_template_part( 'template-parts/book-cta', null, [ 'title' => 'Want a day like this?' ] ); get_footer();
