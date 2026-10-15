<?php
/**
 * Title: Feature cards (3 columns)
 * Slug: bulk-uploader/feature-cards
 * Categories: bulk-uploader, features
 * Description: Section heading and three cards with icons. Change an icon by editing the heading's class (bu-ic--sheet, --image, --eye, --upload, --undo, --tag, --link, --zap, --search, --columns, --layers, --shield, --clock, --doc, --box, --archive, --calendar, --mail, --bell, --chart, --why, --pin).
 *
 * @package bulk-uploader
 */
?>
<!-- wp:group {"align":"full","className":"bu-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull bu-section"><!-- wp:group {"className":"bu-section-head","layout":{"type":"constrained"}} -->
<div class="wp-block-group bu-section-head"><!-- wp:paragraph {"className":"bu-eyebrow"} -->
<p class="bu-eyebrow">Section label</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">A short, clear section heading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"bu-lead"} -->
<p class="bu-lead">One or two sentences that say what this section is about.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"bu-cards"} -->
<div class="wp-block-columns alignwide bu-cards"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"bu-card","layout":{"type":"default"}} -->
<div class="wp-block-group bu-card"><!-- wp:heading {"level":3,"className":"bu-ic bu-ic--sheet"} -->
<h3 class="wp-block-heading bu-ic bu-ic--sheet">First point</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>What it does for the merchant, in one or two sentences.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"bu-card","layout":{"type":"default"}} -->
<div class="wp-block-group bu-card"><!-- wp:heading {"level":3,"className":"bu-ic bu-ic--doc"} -->
<h3 class="wp-block-heading bu-ic bu-ic--doc">Second point</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>What it does for the merchant, in one or two sentences.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"bu-card","layout":{"type":"default"}} -->
<div class="wp-block-group bu-card"><!-- wp:heading {"level":3,"className":"bu-ic bu-ic--image"} -->
<h3 class="wp-block-heading bu-ic bu-ic--image">Third point</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>What it does for the merchant, in one or two sentences.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
