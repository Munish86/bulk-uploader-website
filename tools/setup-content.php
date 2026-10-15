<?php
/**
 * Puts the pages and blog posts from content/ into WordPress and sets the home page, blog page and privacy page.
 *
 * Usage (from bulk-uploader-website/):
 *   tools/wp.sh eval-file tools/setup-content.php --user=bulkadmin            # only adds what is missing
 *   tools/wp.sh eval-file tools/setup-content.php force --user=bulkadmin      # also overwrites existing pages/posts
 *   tools/wp.sh eval-file tools/setup-content.php force only=pricing --user=bulkadmin
 *
 * Without "force", pages already in WordPress are left alone, so edits made in the WordPress editor are never lost.
 *
 * @package bulk-uploader
 */

// phpcs:disable WordPress.WP.AlternativeFunctions -- CLI script reading local files.

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$bu_root  = dirname( __DIR__ ) . '/content/';
$bu_site  = require $bu_root . 'site.php';
$bu_force = in_array( 'force', $args, true );
$bu_only  = '';
foreach ( $args as $bu_arg ) {
	if ( 0 === strpos( $bu_arg, 'only=' ) ) {
		$bu_only = substr( $bu_arg, 5 );
	}
}
if ( ! current_user_can( 'unfiltered_html' ) ) {
	WP_CLI::error( 'Run with --user=<admin user> so page markup is saved as written.' );
}

/**
 * Reads a content file.
 *
 * @param string $root Content folder.
 * @param string $file Relative path; empty = empty content.
 * @return string
 */
function bu_read( $root, $file ) {
	if ( '' === $file ) {
		return '';
	}
	$text = file_get_contents( $root . $file );
	if ( false === $text ) {
		WP_CLI::error( "Missing content file: $file" );
	}
	return trim( $text ) . "\n";
}

/**
 * Creates or updates one post/page. Returns its id, or the existing id when it is kept.
 *
 * @param string $type  page|post.
 * @param string $slug  URL slug.
 * @param array  $data  Post fields.
 * @param bool   $force Overwrite when it exists.
 * @return int
 */
function bu_upsert( $type, $slug, $data, $force ) {
	$existing = get_page_by_path( $slug, OBJECT, $type );
	if ( $existing && ! $force ) {
		WP_CLI::log( "kept    $type $slug (already there; use force to overwrite)" );
		return (int) $existing->ID;
	}
	$data = array_merge(
		array(
			'post_type'      => $type,
			'post_status'    => 'publish',
			'post_name'      => $slug,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		),
		$data
	);
	if ( $existing ) {
		$data['ID'] = $existing->ID;
		$id         = wp_update_post( wp_slash( $data ), true );
	} else {
		$id = wp_insert_post( wp_slash( $data ), true );
	}
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( "$slug: " . $id->get_error_message() );
	}
	WP_CLI::log( ( $existing ? 'updated ' : 'created ' ) . "$type $slug" );
	return (int) $id;
}

/**
 * Adds a cover image from content/covers/ to the media library once and returns its id.
 *
 * @param string $root  Content folder.
 * @param string $file  File name in covers/.
 * @param string $alt   Alt text.
 * @return int
 */
function bu_cover( $root, $file, $alt ) {
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'meta_key'    => '_rd_cover', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}
	$path = $root . 'covers/' . $file;
	if ( ! is_file( $path ) ) {
		WP_CLI::warning( "No cover image $file" );
		return 0;
	}
	$tmp = wp_tempnam( $file );
	copy( $path, $tmp );
	$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0, $alt );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "$file: " . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_rd_cover', $file );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return (int) $id;
}

// ---- Pages ----
$bu_ids = array();
foreach ( $bu_site['pages'] as $bu_slug => $bu_page ) {
	if ( $bu_only && $bu_only !== $bu_slug ) {
		$found = get_page_by_path( $bu_slug );
		if ( $found ) {
			$bu_ids[ $bu_slug ] = (int) $found->ID;
		}
		continue;
	}
	$bu_id              = bu_upsert(
		'page',
		$bu_slug,
		array(
			'post_title'   => $bu_page['title'],
			'post_content' => bu_read( $bu_root, $bu_page['file'] ),
			'post_excerpt' => $bu_page['desc'],
		),
		$bu_force
	);
	$bu_ids[ $bu_slug ] = $bu_id;
	update_post_meta( $bu_id, '_wp_page_template', $bu_page['template'] ?? '' );
	update_post_meta( $bu_id, '_yoast_wpseo_metadesc', $bu_page['desc'] );
	update_post_meta( $bu_id, '_yoast_wpseo_title', 'home' === $bu_slug ? $bu_page['seo'] : $bu_page['seo'] . ' | Bulk Product Uploader' );
}

if ( isset( $bu_ids['home'], $bu_ids['blog'] ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $bu_ids['home'] );
	update_option( 'page_for_posts', $bu_ids['blog'] );
}
if ( isset( $bu_ids['privacy-policy'] ) ) {
	update_option( 'wp_page_for_privacy_policy', $bu_ids['privacy-policy'] );
}

// ---- Posts ----
foreach ( $bu_site['posts'] as $bu_slug => $bu_post ) {
	if ( $bu_only && $bu_only !== $bu_slug ) {
		continue;
	}
	$bu_cat = (int) wp_create_category( $bu_post['category'] );
	$bu_id  = bu_upsert(
		'post',
		$bu_slug,
		array(
			'post_title'    => $bu_post['title'],
			'post_content'  => bu_read( $bu_root, $bu_post['file'] ),
			'post_excerpt'  => $bu_post['desc'],
			'post_date'     => $bu_post['date'],
			'post_category' => array( $bu_cat ),
		),
		$bu_force
	);
	update_post_meta( $bu_id, '_yoast_wpseo_metadesc', $bu_post['desc'] );
	if ( ! empty( $bu_post['cover'] ) ) {
		$bu_img = bu_cover( $bu_root, $bu_post['cover'], $bu_post['title'] );
		if ( $bu_img ) {
			set_post_thumbnail( $bu_id, $bu_img );
		}
	}
}

// The default category is "Guides" instead of "Uncategorized".
$bu_guides = get_term_by( 'name', 'Guides', 'category' );
if ( $bu_guides ) {
	update_option( 'default_category', $bu_guides->term_id );
	$bu_uncat = get_term_by( 'slug', 'uncategorized', 'category' );
	if ( $bu_uncat && 0 === (int) $bu_uncat->count ) {
		wp_delete_term( $bu_uncat->term_id, 'category' );
	}
}

flush_rewrite_rules( false );
WP_CLI::success( 'Content is in place.' );
