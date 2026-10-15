<?php
/**
 * Search engine basics.
 * - App schema (SoftwareApplication) on the home page, always: SEO plugins do not add it.
 * - Meta description and Open Graph tags from the page excerpt, only when no SEO plugin (Yoast, Rank Math, SEOPress) is active.
 *
 * @package bulk-uploader-site
 */

defined( 'ABSPATH' ) || exit;

/** True when an SEO plugin already prints meta tags. */
function bus_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' );
}

add_action(
	'wp_head',
	function () {
		if ( ! is_front_page() ) {
			return;
		}
		$schema = array(
			'@context'            => 'https://schema.org',
			'@type'               => 'SoftwareApplication',
			'name'                => 'Bulk Product Uploader',
			'applicationCategory' => 'BusinessApplication',
			'operatingSystem'     => 'Web, Shopify',
			'description'         => 'Create and update Shopify products from Excel, CSV or Google Sheets, with images matched by file name, a preview of every change and one-click undo.',
			'url'                 => home_url( '/' ),
			'offers'              => array(
				'@type'         => 'Offer',
				'price'         => '0',
				'priceCurrency' => 'USD',
				'description'   => 'Free plan: 50 products created or updated per month',
			),
		);
		if ( bus_setting( 'app_url' ) ) {
			$schema['installUrl'] = bus_setting( 'app_url' );
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES ) . "</script>\n";
	}
);

add_action(
	'wp_head',
	function () {
		if ( bus_seo_plugin_active() ) {
			return;
		}
		$title = wp_get_document_title();
		$desc  = get_bloginfo( 'description' );
		$url   = home_url( add_query_arg( array() ) );
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post && has_excerpt( $post ) ) {
				$desc = get_the_excerpt( $post );
			}
			$url = get_permalink( $post );
		}
		$image = get_theme_file_uri( 'assets/images/og-image.png' );
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $desc ) ) );
		printf( '<meta property="og:type" content="%s">' . "\n", is_single() ? 'article' : 'website' );
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( wp_strip_all_tags( $desc ) ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	},
	2
);
