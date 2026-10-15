<?php
/**
 * Leads page in the admin: early-access sign-ups and contact messages, CSV download, delete.
 *
 * @package bulk-uploader-site
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			__( 'Leads', 'bulk-uploader-site' ),
			__( 'Leads', 'bulk-uploader-site' ),
			'manage_options',
			'bus-leads',
			'bus_render_leads_page',
			'dashicons-groups',
			26
		);
	}
);

/** Counts per kind. */
function bus_counts() {
	global $wpdb;
	$table = bus_table();
	$rows  = $wpdb->get_results( "SELECT kind, COUNT(*) AS n FROM $table GROUP BY kind", ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$out   = array(
		'waitlist' => 0,
		'contact'  => 0,
	);
	foreach ( (array) $rows as $r ) {
		$out[ $r['kind'] ] = (int) $r['n'];
	}
	return $out;
}

/** The Leads page. */
function bus_render_leads_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$table = bus_table();
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only view parameters.
	$kind  = isset( $_GET['kind'] ) && 'contact' === $_GET['kind'] ? 'contact' : 'waitlist';
	$paged = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
	$done  = isset( $_GET['bus_deleted'] );
	// phpcs:enable
	$per    = 50;
	$counts = bus_counts();
	$total  = $counts[ $kind ];
	$rows   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE kind = %s ORDER BY created_at DESC, id DESC LIMIT %d OFFSET %d", $kind, $per, ( $paged - 1 ) * $per ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$base   = admin_url( 'admin.php?page=bus-leads' );
	$export = wp_nonce_url( admin_url( 'admin-post.php?action=bus_export&kind=' . $kind ), 'bus_export' );
	$tools  = bus_tool_choices();
	$sizes  = bus_size_choices();
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Leads', 'bulk-uploader-site' ); ?></h1>
		<a href="<?php echo esc_url( $export ); ?>" class="page-title-action"><?php esc_html_e( 'Download CSV', 'bulk-uploader-site' ); ?></a>
		<hr class="wp-header-end">
		<?php if ( $done ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Entry deleted.', 'bulk-uploader-site' ); ?></p></div>
		<?php endif; ?>
		<ul class="subsubsub">
			<li><a href="<?php echo esc_url( $base ); ?>" <?php echo 'waitlist' === $kind ? 'class="current" aria-current="page"' : ''; ?>><?php esc_html_e( 'Early access', 'bulk-uploader-site' ); ?> <span class="count">(<?php echo (int) $counts['waitlist']; ?>)</span></a> |</li>
			<li><a href="<?php echo esc_url( add_query_arg( 'kind', 'contact', $base ) ); ?>" <?php echo 'contact' === $kind ? 'class="current" aria-current="page"' : ''; ?>><?php esc_html_e( 'Contact messages', 'bulk-uploader-site' ); ?> <span class="count">(<?php echo (int) $counts['contact']; ?>)</span></a></li>
		</ul>
		<table class="widefat striped" style="margin-top:12px">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'bulk-uploader-site' ); ?></th>
					<th><?php esc_html_e( 'Email', 'bulk-uploader-site' ); ?></th>
					<?php if ( 'contact' === $kind ) : ?>
						<th><?php esc_html_e( 'Name', 'bulk-uploader-site' ); ?></th>
					<?php endif; ?>
					<th><?php esc_html_e( 'Store', 'bulk-uploader-site' ); ?></th>
					<?php if ( 'waitlist' === $kind ) : ?>
						<th><?php esc_html_e( 'Adds products by', 'bulk-uploader-site' ); ?></th>
						<th><?php esc_html_e( 'Products in store', 'bulk-uploader-site' ); ?></th>
					<?php else : ?>
						<th style="width:40%"><?php esc_html_e( 'Message', 'bulk-uploader-site' ); ?></th>
					<?php endif; ?>
					<th><?php esc_html_e( 'Page', 'bulk-uploader-site' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( ! $rows ) : ?>
				<tr><td colspan="7"><?php esc_html_e( 'Nothing yet.', 'bulk-uploader-site' ); ?></td></tr>
			<?php endif; ?>
			<?php foreach ( $rows as $r ) : ?>
				<?php $del = wp_nonce_url( admin_url( 'admin-post.php?action=bus_delete&id=' . (int) $r['id'] . '&kind=' . $kind ), 'bus_delete_' . (int) $r['id'] ); ?>
				<tr>
					<td><?php echo esc_html( get_date_from_gmt( $r['created_at'], 'j M Y, H:i' ) ); ?></td>
					<td><a href="mailto:<?php echo esc_attr( $r['email'] ); ?>"><?php echo esc_html( $r['email'] ); ?></a></td>
					<?php if ( 'contact' === $kind ) : ?>
						<td><?php echo esc_html( $r['name'] ); ?></td>
					<?php endif; ?>
					<td><?php echo esc_html( $r['store'] ); ?></td>
					<?php if ( 'waitlist' === $kind ) : ?>
						<td><?php echo esc_html( $tools[ $r['current_tool'] ] ?? '' ); ?></td>
						<td><?php echo esc_html( $sizes[ $r['catalog'] ] ?? '' ); ?></td>
					<?php else : ?>
						<td><?php echo nl2br( esc_html( $r['message'] ) ); ?></td>
					<?php endif; ?>
					<td><?php echo esc_html( $r['page'] ); ?></td>
					<td><a href="<?php echo esc_url( $del ); ?>" onclick="return confirm('Delete this entry?')" style="color:#b32d2e"><?php esc_html_e( 'Delete', 'bulk-uploader-site' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		$pages = (int) ceil( $total / $per );
		if ( $pages > 1 ) {
			echo '<div class="tablenav"><div class="tablenav-pages">';
			echo wp_kses_post(
				paginate_links(
					array(
						'base'    => add_query_arg( 'paged', '%#%', add_query_arg( 'kind', $kind, $base ) ),
						'format'  => '',
						'current' => $paged,
						'total'   => $pages,
					)
				)
			);
			echo '</div></div>';
		}
		?>
	</div>
	<?php
}

/**
 * A CSV cell that spreadsheet programs will not run as a formula.
 *
 * @param string $value Cell value.
 * @return string
 */
function bus_csv_cell( $value ) {
	$value = (string) $value;
	if ( '' !== $value && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
		$value = "'" . $value;
	}
	return $value;
}

add_action(
	'admin_post_bus_export',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'bulk-uploader-site' ), 403 );
		}
		check_admin_referer( 'bus_export' );
		global $wpdb;
		$table = bus_table();
		$kind  = isset( $_GET['kind'] ) && 'contact' === $_GET['kind'] ? 'contact' : 'waitlist';
		$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE kind = %s ORDER BY created_at ASC, id ASC", $kind ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$tools = bus_tool_choices();
		$sizes = bus_size_choices();

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="bulk-uploader-' . $kind . '-' . gmdate( 'Y-m-d' ) . '.csv"' );
		header( 'X-Content-Type-Options: nosniff' );
		$out = fopen( 'php://output', 'w' );
		fwrite( $out, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) ); // BOM, so Excel reads UTF-8.
		fputcsv( $out, array( 'Date (UTC)', 'Email', 'Name', 'Store', 'Adds products by', 'Products in store', 'Message', 'Page' ) );
		foreach ( $rows as $r ) {
			fputcsv(
				$out,
				array_map(
					'bus_csv_cell',
					array( $r['created_at'], $r['email'], $r['name'], $r['store'], $tools[ $r['current_tool'] ] ?? '', $sizes[ $r['catalog'] ] ?? '', $r['message'], $r['page'] )
				)
			);
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}
);

add_action(
	'admin_post_bus_delete',
	function () {
		$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'bulk-uploader-site' ), 403 );
		}
		check_admin_referer( 'bus_delete_' . $id );
		global $wpdb;
		$wpdb->delete( bus_table(), array( 'id' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$kind = isset( $_GET['kind'] ) && 'contact' === $_GET['kind'] ? 'contact' : 'waitlist';
		wp_safe_redirect( add_query_arg( array( 'kind' => $kind, 'bus_deleted' => 1 ), admin_url( 'admin.php?page=bus-leads' ) ) );
		exit;
	}
);
