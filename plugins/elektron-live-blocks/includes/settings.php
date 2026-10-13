<?php
/**
 * Settings page.
 *
 * @package ElektronLiveBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitizes the API base URL: HTTPS only.
 *
 * @param string $value Submitted value.
 * @return string
 */
function elnb_sanitize_api_base( $value ) {
	$url = esc_url_raw( trim( (string) $value ), array( 'https' ) );
	if ( '' === $url ) {
		add_settings_error( 'elnb_api_base', 'elnb_api_base', __( 'Please enter a valid https:// address.', 'elektron-live-blocks' ) );
		return get_option( 'elnb_api_base', ELNB_DEFAULT_API );
	}
	return untrailingslashit( $url );
}

/**
 * Sanitizes the cache lifetime.
 *
 * @param mixed $value Submitted value.
 * @return int
 */
function elnb_sanitize_ttl( $value ) {
	return min( 600, max( 30, absint( $value ) ) );
}

/**
 * Registers settings.
 */
function elnb_register_settings() {
	register_setting(
		'elnb',
		'elnb_api_base',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'elnb_sanitize_api_base',
			'default'           => ELNB_DEFAULT_API,
		)
	);
	register_setting(
		'elnb',
		'elnb_cache_ttl',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'elnb_sanitize_ttl',
			'default'           => 60,
		)
	);
}
add_action( 'admin_init', 'elnb_register_settings' );

/**
 * Adds the settings page.
 */
function elnb_admin_menu() {
	add_options_page(
		__( 'Elektron Live Blocks', 'elektron-live-blocks' ),
		__( 'Elektron Live Blocks', 'elektron-live-blocks' ),
		'manage_options',
		'elektron-live-blocks',
		'elnb_settings_page'
	);
}
add_action( 'admin_menu', 'elnb_admin_menu' );

/**
 * Renders the settings page.
 */
function elnb_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p><?php esc_html_e( 'Your web server requests the data from the block explorer and caches it. Visitors never contact the explorer.', 'elektron-live-blocks' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'elnb' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="elnb_api_base"><?php esc_html_e( 'Explorer API base URL', 'elektron-live-blocks' ); ?></label></th>
					<td>
						<input type="url" id="elnb_api_base" name="elnb_api_base" class="regular-text code" value="<?php echo esc_attr( elnb_api_base() ); ?>">
						<p class="description">
							<?php
							/* translators: %s: default API address */
							printf( esc_html__( 'HTTPS only. Default: %s', 'elektron-live-blocks' ), '<code>' . esc_html( ELNB_DEFAULT_API ) . '</code>' );
							?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="elnb_cache_ttl"><?php esc_html_e( 'Cache lifetime (seconds)', 'elektron-live-blocks' ); ?></label></th>
					<td>
						<input type="number" id="elnb_cache_ttl" name="elnb_cache_ttl" min="30" max="600" step="1" value="<?php echo esc_attr( (string) elnb_ttl() ); ?>">
						<p class="description"><?php esc_html_e( 'Between 30 and 600 seconds.', 'elektron-live-blocks' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
