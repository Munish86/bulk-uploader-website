<?php
/**
 * Settings > Bulk Product Uploader site: app install URL, button label, support and notification emails.
 *
 * @package bulk-uploader-site
 */

defined( 'ABSPATH' ) || exit;

/** Defaults for every setting. */
function bus_defaults() {
	return array(
		'app_url'       => '', // App Store listing URL; empty = the app is not live yet, buttons go to /early-access/.
		'live_label'    => 'Install free on Shopify',
		'support_email' => '',
		'notify_email'  => '', // Where new form entries are emailed; empty = the site admin email.
	);
}

/**
 * One setting's value.
 *
 * @param string $key Setting name from bus_defaults().
 * @return string
 */
function bus_setting( $key ) {
	$all = wp_parse_args( (array) get_option( 'bus_settings', array() ), bus_defaults() );
	return isset( $all[ $key ] ) ? (string) $all[ $key ] : '';
}

/**
 * Cleans the settings form.
 *
 * @param mixed $input Posted values.
 * @return array
 */
function bus_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$out   = bus_defaults();

	$url = isset( $input['app_url'] ) ? esc_url_raw( trim( $input['app_url'] ), array( 'https' ) ) : '';
	if ( '' !== $url && ! wp_http_validate_url( $url ) ) {
		add_settings_error( 'bus_settings', 'bus_app_url', __( 'The app URL must be a full https:// address.', 'bulk-uploader-site' ) );
		$url = '';
	}
	$out['app_url'] = $url;

	$label             = isset( $input['live_label'] ) ? sanitize_text_field( $input['live_label'] ) : '';
	$out['live_label'] = '' !== $label ? mb_substr( $label, 0, 60 ) : bus_defaults()['live_label'];

	foreach ( array( 'support_email', 'notify_email' ) as $key ) {
		$email       = isset( $input[ $key ] ) ? sanitize_email( $input[ $key ] ) : '';
		$out[ $key ] = is_email( $email ) ? $email : '';
	}
	return $out;
}

add_action(
	'admin_init',
	function () {
		register_setting(
			'bus_settings',
			'bus_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'bus_sanitize_settings',
				'default'           => bus_defaults(),
			)
		);
	}
);

add_action(
	'admin_menu',
	function () {
		add_options_page(
			__( 'Bulk Product Uploader site', 'bulk-uploader-site' ),
			__( 'Bulk Product Uploader site', 'bulk-uploader-site' ),
			'manage_options',
			'bus-settings',
			'bus_render_settings_page'
		);
	}
);

/** Settings page. */
function bus_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s = wp_parse_args( (array) get_option( 'bus_settings', array() ), bus_defaults() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Bulk Product Uploader site', 'bulk-uploader-site' ); ?></h1>
		<?php settings_errors( 'bus_settings' ); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'bus_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="bus-app-url"><?php esc_html_e( 'App Store URL', 'bulk-uploader-site' ); ?></label></th>
					<td>
						<input name="bus_settings[app_url]" id="bus-app-url" type="url" class="regular-text" value="<?php echo esc_attr( $s['app_url'] ); ?>" placeholder="https://apps.shopify.com/bulk-product-uploader">
						<p class="description"><?php esc_html_e( 'Leave empty until the app is approved. Once filled in, every button with the class "bu-cta" (header, home page, pricing, call-to-action bands) links here and shows the label below instead of "Get early access".', 'bulk-uploader-site' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="bus-live-label"><?php esc_html_e( 'Button label when live', 'bulk-uploader-site' ); ?></label></th>
					<td><input name="bus_settings[live_label]" id="bus-live-label" type="text" class="regular-text" value="<?php echo esc_attr( $s['live_label'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="bus-support-email"><?php esc_html_e( 'Support email', 'bulk-uploader-site' ); ?></label></th>
					<td>
						<input name="bus_settings[support_email]" id="bus-support-email" type="email" class="regular-text" value="<?php echo esc_attr( $s['support_email'] ); ?>">
						<p class="description"><?php esc_html_e( 'Shown wherever a page uses [bu_support_email]. Empty: a link to the contact page is shown instead.', 'bulk-uploader-site' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="bus-notify-email"><?php esc_html_e( 'Email new sign-ups to', 'bulk-uploader-site' ); ?></label></th>
					<td>
						<input name="bus_settings[notify_email]" id="bus-notify-email" type="email" class="regular-text" value="<?php echo esc_attr( $s['notify_email'] ); ?>">
						<p class="description"><?php esc_html_e( 'Empty: the site admin email. Every entry is saved under Leads either way, even when email sending does not work (as on a local computer).', 'bulk-uploader-site' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
