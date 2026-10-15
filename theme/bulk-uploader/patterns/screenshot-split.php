<?php
/**
 * Title: Text beside a screenshot
 * Slug: bulk-uploader/screenshot-split
 * Categories: bulk-uploader, features
 * Description: Heading, text and a check list on one side, an app screenshot on the other. Add the class bu-split--flip to the columns to swap sides.
 *
 * @package bulk-uploader
 */

$img = esc_url( get_theme_file_uri( 'assets/images/app-results.webp' ) );
?>
<!-- wp:group {"align":"full","className":"bu-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull bu-section"><!-- wp:columns {"align":"wide","className":"bu-split"} -->
<div class="wp-block-columns alignwide bu-split"><!-- wp:column {"width":"42%"} -->
<div class="wp-block-column" style="flex-basis:42%"><!-- wp:paragraph {"className":"bu-eyebrow"} -->
<p class="bu-eyebrow">Section label</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What this part of the app does</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Two or three sentences about it.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"bu-check"} -->
<ul class="wp-block-list bu-check"><!-- wp:list-item -->
<li>First detail</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Second detail</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","className":"bu-shot"} -->
<figure class="wp-block-image size-full bu-shot"><img src="<?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above ?>" alt="Bulk Product Uploader screenshot"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
