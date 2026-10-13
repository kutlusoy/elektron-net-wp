<?php
/**
 * Dismissible admin notice that recommends the Elektron Live Blocks plugin.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ELEKTRON_PLUGIN_FILE = 'elektron-live-blocks/elektron-live-blocks.php';

/**
 * Saves the dismissal for the current user.
 */
function elektron_handle_notice_dismissal() {
	if ( ! isset( $_GET['elektron_dismiss_plugin_notice'] ) ) {
		return;
	}

	check_admin_referer( 'elektron_dismiss_plugin_notice' );

	update_user_meta( get_current_user_id(), 'elektron_plugin_notice_dismissed', 1 );

	wp_safe_redirect( remove_query_arg( array( 'elektron_dismiss_plugin_notice', '_wpnonce' ) ) );
	exit;
}
add_action( 'admin_init', 'elektron_handle_notice_dismissal' );

/**
 * Shows the notice on the dashboard, themes and plugins screens.
 */
function elektron_plugin_notice() {
	if ( elektron_plugin_active() || ! current_user_can( 'activate_plugins' ) || get_user_meta( get_current_user_id(), 'elektron_plugin_notice_dismissed', true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'themes', 'plugins' ), true ) ) {
		return;
	}

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$installed = array_key_exists( ELEKTRON_PLUGIN_FILE, get_plugins() );

	if ( $installed ) {
		$action_url   = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( ELEKTRON_PLUGIN_FILE ) ), 'activate-plugin_' . ELEKTRON_PLUGIN_FILE );
		$action_label = __( 'Activate Elektron Live Blocks', 'elektron' );
	} elseif ( current_user_can( 'install_plugins' ) ) {
		$action_url   = self_admin_url( 'plugin-install.php?tab=upload' );
		$action_label = __( 'Upload the plugin', 'elektron' );
	} else {
		$action_url   = '';
		$action_label = '';
	}

	$dismiss_url = wp_nonce_url( add_query_arg( 'elektron_dismiss_plugin_notice', '1' ), 'elektron_dismiss_plugin_notice' );
	?>
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'Elektron recommends the Elektron Live Blocks plugin.', 'elektron' ); ?></strong>
			<?php esc_html_e( 'It adds blocks for live network data (block height, hash rate, fees, halving countdown and more) that you can place on any page. The theme then uses the same data source.', 'elektron' ); ?>
		</p>
		<p>
			<?php if ( $action_url ) : ?>
				<a class="button button-primary" href="<?php echo esc_url( $action_url ); ?>"><?php echo esc_html( $action_label ); ?></a>
			<?php endif; ?>
			<a class="button" href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Dismiss', 'elektron' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'elektron_plugin_notice' );
