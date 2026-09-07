<?php
/**
 * Title: Location page (whole page)
 * Slug: thereyare/location-page
 * Categories: thereyare-pages
 * Description: A complete page about shooting at one Los Angeles location. Insert it, replace the placeholders, publish. This is the pattern that turns fifty shoots into fifty pages carrying local search terms.
 * Keywords: location, local seo, venue, page
 * Viewport Width: 1400
 */
?>
<!-- wp:paragraph {"style":{"typography":{"fontStyle":"normal","fontWeight":"700","letterSpacing":"0.2em","textTransform":"uppercase"}},"textColor":"brick","fontSize":"small"} -->
<p class="has-brick-color has-text-color has-small-font-size">Location guide · Los Angeles</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"textColor":"dim","fontSize":"large"} -->
<p class="has-dim-color has-text-color has-large-font-size">Everything we've learned photographing families at [LOCATION NAME] — the light, the parking, the permits and the hour that actually works.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Why families shoot here</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Two or three sentences on what the place looks like on camera and who it suits. Name the neighbourhood: that phrase is what parents type into Google.</p>
<!-- /wp:paragraph -->

<!-- wp:gallery {"columns":3,"linkTo":"none","sizeSlug":"thereyare-sheet"} -->
<figure class="wp-block-gallery has-nested-images columns-3 is-cropped"><!-- wp:image {"sizeSlug":"thereyare-sheet"} -->
<figure class="wp-block-image size-thereyare-sheet"><img alt="Describe each photograph: who, doing what, where. Google Images sends real traffic to photographers."/></figure>
<!-- /wp:image --></figure>
<!-- /wp:gallery -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">The practical part</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><strong>Best light:</strong> the hour and the season that work here.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Parking:</strong> where to leave the car with a pushchair and a nappy bag.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Permits:</strong> whether one is needed, what it costs, who arranges it.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>With small children:</strong> shade, toilets, somewhere to sit.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>How long:</strong> the realistic length of a session on this spot.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:pattern {"slug":"thereyare/faq"} /-->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Sessions we've photographed here</h2>
<!-- /wp:heading -->

<!-- wp:query {"queryId":10,"query":{"perPage":3,"pages":1,"offset":0,"postType":"session","order":"desc","orderBy":"date","inherit":false},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"grid","columnCount":3}} -->
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/5","style":{"border":{"radius":"18px"}}} /-->

<!-- wp:post-title {"isLink":true,"level":3,"fontSize":"large"} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query -->
