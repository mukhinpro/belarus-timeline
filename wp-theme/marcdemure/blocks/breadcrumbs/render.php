<?php
/**
 * Breadcrumbs.
 *
 * Core has no breadcrumb block, and a trail that disagrees with its own
 * structured data is a Search Console error. So the theme owns both, and both
 * read md_breadcrumb_trail().
 *
 * @var array    $attributes
 * @var string   $content
 * @var WP_Block $block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$md_trail = md_breadcrumb_trail();

if ( count( $md_trail ) < 2 ) {
	return;
}

$md_last = count( $md_trail ) - 1;

?>
<nav <?php echo wp_kses_data( get_block_wrapper_attributes( [ 'class' => 'md-crumbs' ] ) ); ?> aria-label="<?php esc_attr_e( 'Breadcrumb', 'marcdemure' ); ?>">
	<?php foreach ( $md_trail as $md_index => $md_crumb ) : ?>
		<?php if ( $md_index > 0 ) : ?>
			<span class="md-crumbs__sep" aria-hidden="true">/</span>
		<?php endif; ?>

		<?php if ( $md_index === $md_last ) : ?>
			<span aria-current="page"><?php echo esc_html( $md_crumb['label'] ); ?></span>
		<?php else : ?>
			<a href="<?php echo esc_url( $md_crumb['url'] ); ?>"><?php echo esc_html( $md_crumb['label'] ); ?></a>
		<?php endif; ?>
	<?php endforeach; ?>
</nav>
