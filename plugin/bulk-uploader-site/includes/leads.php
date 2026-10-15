<?php
/**
 * Early-access and contact forms: [bu_waitlist], [bu_contact], [bu_support_email].
 * Entries are saved in the {prefix}bu_leads table and listed under Leads in the admin.
 * Spam protection without cookies or third parties: a hidden field, a signed time stamp and a per-IP limit.
 *
 * @package bulk-uploader-site
 */

defined( 'ABSPATH' ) || exit;

/** Table name. */
function bus_table() {
	global $wpdb;
	return $wpdb->prefix . 'bu_leads';
}

/** Creates or upgrades the table. */
function bus_install() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	$table   = bus_table();
	dbDelta(
		"CREATE TABLE $table (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		kind varchar(20) NOT NULL,
		email varchar(190) NOT NULL,
		name varchar(190) NOT NULL DEFAULT '',
		store varchar(190) NOT NULL DEFAULT '',
		current_tool varchar(60) NOT NULL DEFAULT '',
		catalog varchar(30) NOT NULL DEFAULT '',
		message text NOT NULL,
		page varchar(190) NOT NULL DEFAULT '',
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY kind_created (kind,created_at),
		KEY email (email)
		) $charset;"
	);
	update_option( 'bus_db_version', BUS_DB_VERSION );
}

/** Choices shown in the early-access form; stored by key. */
function bus_tool_choices() {
	return array(
		'by-hand'     => __( 'One by one in Shopify admin', 'bulk-uploader-site' ),
		'shopify-csv' => __( "Shopify's own CSV import", 'bulk-uploader-site' ),
		'other-app'   => __( 'Another import app', 'bulk-uploader-site' ),
		'someone'     => __( 'A freelancer or agency does it', 'bulk-uploader-site' ),
	);
}

/** Catalogue size choices. */
function bus_size_choices() {
	return array(
		'lt100'      => __( 'Under 100', 'bulk-uploader-site' ),
		'100-1000'   => __( '100 to 1,000', 'bulk-uploader-site' ),
		'1000-10000' => __( '1,000 to 10,000', 'bulk-uploader-site' ),
		'gt10000'    => __( 'Over 10,000', 'bulk-uploader-site' ),
	);
}

/** Signed time stamp: forms posted in under 3 seconds or with a forged stamp are refused. */
function bus_stamp() {
	$t = (string) time();
	return $t . '.' . hash_hmac( 'sha256', $t, wp_salt( 'nonce' ) );
}

/**
 * Checks a posted time stamp.
 *
 * @param string $stamp Value from the form.
 * @return bool
 */
function bus_stamp_ok( $stamp ) {
	$parts = explode( '.', (string) $stamp, 2 );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( ! hash_equals( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), $parts[1] ) ) {
		return false;
	}
	$age = time() - (int) $parts[0];
	return $age >= 3 && $age <= 14 * DAY_IN_SECONDS;
}

/** Message shown above a form after it was sent (read from the redirect's query string). */
function bus_form_notice( $kind ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display only.
	if ( ! isset( $_GET['bu_form'], $_GET['bu_status'] ) || sanitize_key( $_GET['bu_form'] ) !== $kind ) {
		return '';
	}
	$status = sanitize_key( $_GET['bu_status'] );
	// phpcs:enable
	$messages = array(
		'ok'      => 'waitlist' === $kind
			? __( "You're on the list. We'll email you once, on the day Bulk Product Uploader goes live on the Shopify App Store.", 'bulk-uploader-site' )
			: __( 'Thanks, your message has been sent. We reply within one working day.', 'bulk-uploader-site' ),
		'email'   => __( 'Please enter a valid email address.', 'bulk-uploader-site' ),
		'message' => __( 'Please write a message (up to 5,000 characters).', 'bulk-uploader-site' ),
		'limit'   => __( 'Too many messages from your connection in the last hour. Please try again later.', 'bulk-uploader-site' ),
		'expired' => __( 'The form had been open too long or was sent too quickly. Please send it again.', 'bulk-uploader-site' ),
	);
	if ( ! isset( $messages[ $status ] ) ) {
		return '';
	}
	$class = 'ok' === $status ? 'bu-alert bu-alert--ok' : 'bu-alert bu-alert--error';
	return '<div class="' . esc_attr( $class ) . '" role="status">' . esc_html( $messages[ $status ] ) . '</div>';
}

/** Hidden fields shared by both forms. */
function bus_hidden_fields( $kind ) {
	$html  = '<input type="hidden" name="action" value="bus_submit">';
	$html .= '<input type="hidden" name="bu_kind" value="' . esc_attr( $kind ) . '">';
	$html .= '<input type="hidden" name="bu_stamp" value="' . esc_attr( bus_stamp() ) . '">';
	$html .= '<input type="hidden" name="bu_page" value="' . esc_attr( (string) get_queried_object_id() ) . '">';
	// Bots fill in every field; people never see this one.
	$html .= '<div class="bu-hp" aria-hidden="true"><label>Website <input type="text" name="bu_website" tabindex="-1" autocomplete="off"></label></div>';
	return $html;
}

/** [bu_waitlist] */
function bus_waitlist_shortcode() {
	$options = '';
	foreach ( bus_tool_choices() as $key => $label ) {
		$options .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
	}
	$catalog = '';
	foreach ( bus_size_choices() as $key => $label ) {
		$catalog .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
	}
	ob_start();
	?>
	<div id="bu-form-waitlist">
		<?php echo bus_form_notice( 'waitlist' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		<form class="bu-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php echo bus_hidden_fields( 'waitlist' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			<label><?php esc_html_e( 'Work email', 'bulk-uploader-site' ); ?>
				<input type="email" name="bu_email" required maxlength="190" autocomplete="email" placeholder="you@yourstore.com">
			</label>
			<label><?php esc_html_e( 'Your store', 'bulk-uploader-site' ); ?> <span class="bu-opt"><?php esc_html_e( '(optional)', 'bulk-uploader-site' ); ?></span>
				<input type="text" name="bu_store" maxlength="190" placeholder="yourstore.myshopify.com or your website">
			</label>
			<div class="bu-row">
				<label><?php esc_html_e( 'How do you add products today?', 'bulk-uploader-site' ); ?>
					<select name="bu_tool"><option value=""><?php esc_html_e( 'Choose one', 'bulk-uploader-site' ); ?></option><?php echo $options; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></select>
				</label>
				<label><?php esc_html_e( 'Products in your store', 'bulk-uploader-site' ); ?>
					<select name="bu_catalog"><option value=""><?php esc_html_e( 'Choose one', 'bulk-uploader-site' ); ?></option><?php echo $catalog; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></select>
				</label>
			</div>
			<button type="submit"><?php esc_html_e( 'Get early access', 'bulk-uploader-site' ); ?></button>
			<p class="bu-fine"><?php esc_html_e( 'One email when the app is live, nothing else. Your details are never shared.', 'bulk-uploader-site' ); ?> <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy policy', 'bulk-uploader-site' ); ?></a></p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'bu_waitlist', 'bus_waitlist_shortcode' );

/** [bu_contact] */
function bus_contact_shortcode() {
	ob_start();
	?>
	<div id="bu-form-contact">
		<?php echo bus_form_notice( 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		<form class="bu-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php echo bus_hidden_fields( 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
			<div class="bu-row">
				<label><?php esc_html_e( 'Name', 'bulk-uploader-site' ); ?>
					<input type="text" name="bu_name" maxlength="190" autocomplete="name">
				</label>
				<label><?php esc_html_e( 'Email', 'bulk-uploader-site' ); ?>
					<input type="email" name="bu_email" required maxlength="190" autocomplete="email">
				</label>
			</div>
			<label><?php esc_html_e( 'Store address', 'bulk-uploader-site' ); ?> <span class="bu-opt"><?php esc_html_e( '(if it is about your store)', 'bulk-uploader-site' ); ?></span>
				<input type="text" name="bu_store" maxlength="190" placeholder="yourstore.myshopify.com">
			</label>
			<label><?php esc_html_e( 'Message', 'bulk-uploader-site' ); ?>
				<textarea name="bu_message" required maxlength="5000"></textarea>
			</label>
			<button type="submit"><?php esc_html_e( 'Send message', 'bulk-uploader-site' ); ?></button>
			<p class="bu-fine"><?php esc_html_e( 'We use your details only to answer you.', 'bulk-uploader-site' ); ?> <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy policy', 'bulk-uploader-site' ); ?></a></p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'bu_contact', 'bus_contact_shortcode' );

/** [bu_support_email]: the support address as a link, or a link to the contact page when none is set. */
function bus_support_email_shortcode() {
	$email = bus_setting( 'support_email' );
	if ( $email ) {
		return '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>';
	}
	return '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'our contact form', 'bulk-uploader-site' ) . '</a>';
}
add_shortcode( 'bu_support_email', 'bus_support_email_shortcode' );

/**
 * Sends the visitor back to the form with a status.
 *
 * @param string $kind   waitlist|contact.
 * @param string $status Status key for bus_form_notice().
 * @param int    $page   Page the form was on.
 */
function bus_back( $kind, $status, $page ) {
	$url = $page ? get_permalink( $page ) : '';
	if ( ! $url ) {
		$url = home_url( '/' );
	}
	$url = add_query_arg(
		array(
			'bu_form'   => $kind,
			'bu_status' => $status,
		),
		$url
	) . '#bu-form-' . $kind;
	wp_safe_redirect( $url, 303 );
	exit;
}

/**
 * Field from the posted form, cleaned and cut to a length.
 *
 * @param string $name Field name.
 * @param int    $max  Max characters.
 * @return string
 */
function bus_post_field( $name, $max = 190 ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- public form; protected by the signed stamp, honeypot and rate limit.
	$value = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
	return mb_substr( $value, 0, $max );
}

/** Handles both forms (admin-post.php?action=bus_submit). */
function bus_handle_submit() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- see bus_post_field().
	$kind = isset( $_POST['bu_kind'] ) && 'contact' === $_POST['bu_kind'] ? 'contact' : 'waitlist';
	$page = isset( $_POST['bu_page'] ) ? absint( $_POST['bu_page'] ) : 0;

	// A filled honeypot is a bot: pretend it worked.
	if ( ! empty( $_POST['bu_website'] ) ) {
		bus_back( $kind, 'ok', $page );
	}
	if ( ! bus_stamp_ok( isset( $_POST['bu_stamp'] ) ? sanitize_text_field( wp_unslash( $_POST['bu_stamp'] ) ) : '' ) ) {
		bus_back( $kind, 'expired', $page );
	}

	$email = sanitize_email( bus_post_field( 'bu_email' ) );
	if ( ! is_email( $email ) ) {
		bus_back( $kind, 'email', $page );
	}

	$message = '';
	if ( 'contact' === $kind ) {
		$message = isset( $_POST['bu_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['bu_message'] ) ) : '';
		if ( '' === trim( $message ) || mb_strlen( $message ) > 5000 ) {
			bus_back( $kind, 'message', $page );
		}
	}
	// phpcs:enable

	// At most 6 entries per hour from one connection. The IP itself is never stored.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'bus_rl_' . substr( hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) ), 0, 32 );
	$count = (int) get_transient( $key );
	if ( $count >= 6 ) {
		bus_back( $kind, 'limit', $page );
	}
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	$tool     = bus_post_field( 'bu_tool', 30 );
	$catalog = bus_post_field( 'bu_catalog', 30 );
	$row      = array(
		'kind'         => $kind,
		'email'        => $email,
		'name'         => bus_post_field( 'bu_name' ),
		'store'        => bus_post_field( 'bu_store' ),
		'current_tool' => isset( bus_tool_choices()[ $tool ] ) ? $tool : '',
		'catalog'     => isset( bus_size_choices()[ $catalog ] ) ? $catalog : '',
		'message'      => $message,
		'page'         => $page ? (string) get_the_title( $page ) : '',
		'created_at'   => current_time( 'mysql', true ),
	);

	global $wpdb;
	$table = bus_table();
	// Signing up twice for early access updates the first entry instead of adding a second.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$existing = 'waitlist' === $kind ? $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $table WHERE kind = 'waitlist' AND email = %s", $email ) ) : null; // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( $existing ) {
		$update = array_filter( array_intersect_key( $row, array_flip( array( 'store', 'current_tool', 'catalog' ) ) ) );
		if ( $update ) {
			$wpdb->update( $table, $update, array( 'id' => (int) $existing ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}
	} else {
		$wpdb->insert( $table, $row ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		bus_notify( $row );
	}
	bus_back( $kind, 'ok', $page );
}
add_action( 'admin_post_nopriv_bus_submit', 'bus_handle_submit' );
add_action( 'admin_post_bus_submit', 'bus_handle_submit' );

/**
 * Emails a new entry to the owner. Failure is fine: the entry is already saved.
 *
 * @param array $row Saved row.
 */
function bus_notify( $row ) {
	$to = bus_setting( 'notify_email' );
	if ( ! $to ) {
		$to = get_option( 'admin_email' );
	}
	$subject = 'contact' === $row['kind'] ? 'New message from the Bulk Product Uploader website' : 'New early-access sign-up: ' . $row['email'];
	$lines   = array(
		'Email: ' . $row['email'],
		'Name: ' . $row['name'],
		'Store: ' . $row['store'],
		'Adds products by: ' . ( bus_tool_choices()[ $row['current_tool'] ] ?? '' ),
		'Products in store: ' . ( bus_size_choices()[ $row['catalog'] ] ?? '' ),
		'Page: ' . $row['page'],
		'',
		$row['message'],
		'',
		'All entries: ' . admin_url( 'admin.php?page=bus-leads' ),
	);
	$headers = array( 'Reply-To: ' . $row['email'] );
	wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
}
