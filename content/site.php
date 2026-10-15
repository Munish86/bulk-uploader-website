<?php
/**
 * What tools/setup-content.php puts into WordPress. Each page/post body is a block-markup file next to this one.
 * "desc" becomes the page excerpt and the Yoast meta description (keep it under ~155 characters).
 *
 * @package bulk-uploader
 */

return array(
	'pages' => array(
		'home'                           => array(
			'title' => 'Bulk Product Uploader: update Shopify products from a spreadsheet',
			'file'  => 'pages/home.html',
			'seo'   => 'Bulk Product Uploader: update Shopify products from Excel, CSV or Google Sheets',
			'desc'  => 'Create and update hundreds of Shopify products from your own sheet, with images matched by file name. Preview every change, undo any upload.',
		),
		'features'                       => array(
			'title'    => 'Features',
			'file'     => 'pages/features.html',
			'template' => 'page-landing',
			'seo'      => 'Features: sheets, images, preview, backups and undo',
			'desc'     => 'Everything in Bulk Product Uploader: your own column names, images by file name or Google Drive, variants, metafields, preview, backups and undo.',
		),
		'how-it-works'                   => array(
			'title'    => 'How it works',
			'file'     => 'pages/how-it-works.html',
			'template' => 'page-landing',
			'seo'      => 'How it works: from a spreadsheet to Shopify in five steps',
			'desc'     => 'Load your sheet, map the columns, add images, preview the changes and upload. What the app does at each step, and how products are found and updated.',
		),
		'pricing'                        => array(
			'title'    => 'Pricing',
			'file'     => 'pages/pricing.html',
			'template' => 'page-landing',
			'seo'      => 'Pricing: free for 50 products a month',
			'desc'     => 'Bulk Product Uploader is free for 50 products a month. Pro and Unlimited for bigger catalogues. Previews and backups are free on every plan.',
		),
		'shopify-csv-import-alternative' => array(
			'title'    => 'Bulk Product Uploader compared with Shopify CSV import',
			'file'     => 'pages/shopify-csv-import-alternative.html',
			'template' => 'page-landing',
			'seo'      => 'A Shopify CSV import alternative with preview and undo',
			'desc'     => "Shopify's CSV import needs its own template and image URLs, and can't be undone. Bulk Product Uploader reads your own sheet and image folder.",
		),
		'help'                           => array(
			'title' => 'Help centre',
			'file'  => 'pages/help.html',
			'seo'   => 'Help centre: getting started with Bulk Product Uploader',
			'desc'  => 'How to use Bulk Product Uploader: sheets, column mapping, finding products, variants, metafields, images, preview, upload, backups, restore and plans.',
		),
		'early-access'                   => array(
			'title'    => 'Early access',
			'file'     => 'pages/early-access.html',
			'template' => 'page-landing',
			'seo'      => 'Get early access to Bulk Product Uploader',
			'desc'     => 'Join the Bulk Product Uploader early-access list and get one email on the day the app goes live on the Shopify App Store.',
		),
		'contact'                        => array(
			'title'    => 'Contact',
			'file'     => 'pages/contact.html',
			'template' => 'page-landing',
			'seo'      => 'Contact Bulk Product Uploader',
			'desc'     => 'Questions about Bulk Product Uploader, a product sheet or your plan? Send us a message; we reply within one working day.',
		),
		'privacy-policy'                 => array(
			'title' => 'Privacy policy',
			'file'  => 'pages/privacy-policy.html',
			'seo'   => 'Privacy policy',
			'desc'  => 'What this website and the Bulk Product Uploader app collect, why, how long it is kept and how to have it deleted.',
		),
		'terms'                          => array(
			'title' => 'Terms of use',
			'file'  => 'pages/terms.html',
			'seo'   => 'Terms of use',
			'desc'  => 'The terms for using the Bulk Product Uploader website and the Bulk Product Uploader Shopify app.',
		),
		'blog'                           => array(
			'title' => 'Blog',
			'file'  => '',
			'seo'   => 'Blog: bulk product uploads for Shopify',
			'desc'  => 'Practical guides for Shopify stores with big catalogues: product sheets, bulk image uploads, CSV imports, variants and metafields.',
		),
	),
	'posts' => array(
		'bulk-upload-products-to-shopify-from-excel' => array(
			'title'    => 'How to bulk upload products to Shopify from Excel',
			'file'     => 'posts/bulk-upload-products-to-shopify-from-excel.html',
			'category' => 'Guides',
			'date'     => '2026-10-15 09:00:00',
			'cover'    => 'cover-excel.png',
			'desc'     => 'Three ways to get products from an Excel sheet into Shopify, how to lay out the sheet, and how to check the result before anything changes.',
		),
		'bulk-upload-product-images-to-shopify'      => array(
			'title'    => 'How to upload hundreds of product images to Shopify at once',
			'file'     => 'posts/bulk-upload-product-images-to-shopify.html',
			'category' => 'Guides',
			'date'     => '2026-10-14 09:00:00',
			'cover'    => 'cover-images.png',
			'desc'     => 'Name your image files after your products and let them match themselves. File naming rules, image order, alt text and what to do with leftovers.',
		),
		'shopify-csv-import-mistakes'                => array(
			'title'    => '7 product import mistakes that break a Shopify store (and how to avoid them)',
			'file'     => 'posts/shopify-csv-import-mistakes.html',
			'category' => 'Guides',
			'date'     => '2026-10-13 09:00:00',
			'cover'    => 'cover-mistakes.png',
			'desc'     => 'Blank cells that wipe data, duplicate handles, lost variants and broken images: the common bulk import mistakes and a checklist to avoid each one.',
		),
		'shopify-metafields-from-a-spreadsheet'      => array(
			'title'    => 'How to fill Shopify metafields from a spreadsheet',
			'file'     => 'posts/shopify-metafields-from-a-spreadsheet.html',
			'category' => 'Guides',
			'date'     => '2026-10-12 09:00:00',
			'cover'    => 'cover-metafields.png',
			'desc'     => 'Material, care, size guide: what metafields are, how to name the columns in your sheet, which types to use, and how to upload them for every product.',
		),
	),
);
