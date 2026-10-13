<?php
/**
 * Removes plugin options on uninstall.
 *
 * @package ElektronLiveBlocks
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'elnb_api_base' );
delete_option( 'elnb_cache_ttl' );
