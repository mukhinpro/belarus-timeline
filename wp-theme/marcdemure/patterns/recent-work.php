<?php
/**
 * Title: Recent work
 * Slug: marcdemure/recent-work
 * Categories: md-sections
 * Description: The six most recent published sessions, rendered on the server so every one of them is a crawlable link.
 * Keywords: portfolio, recent, grid
 * Viewport Width: 1400
 */
?>
<!-- wp:group {"tagName":"section","align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--70)"><!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Recent sessions</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><a href="/portfolio/">All work</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":2,"query":{"perPage":6,"pages":1,"offset":0,"postType":"shoot","order":"desc","orderBy":"date","inherit":false},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-query" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:post-template {"style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"grid","minimumColumnWidth":"260px"}} -->
<!-- wp:group {"className":"md-card","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group md-card"><!-- wp:group {"className":"md-card__frame","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group md-card__frame"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/4"} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"isLink":true,"level":3,"fontSize":"medium"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"textColor":"ash"} -->
<p class="has-ash-color has-text-color">Sessions appear here as they are published.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></section>
<!-- /wp:group -->
