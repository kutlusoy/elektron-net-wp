<?php
/**
 * Plugin Name:       Elektron Live Blocks
 * Plugin URI:        https://elektron-net.org
 * Description:       Blocks and a shortcode that show live Elektron Net data from a block explorer API: block height, hash rate, difficulty adjustment, mempool, fees, halving countdown and latest blocks.
 * Version:           0.0.1
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Ali Kutlusoy
 * Author URI:        https://elektron-net.org
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       elektron-live-blocks
 * Domain Path:       /languages
 *
 * @package ElektronLiveBlocks
 * @author  Ali Kutlusoy <kutlusoy1975@gmail.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELNB_VERSION', '0.0.1' );
define( 'ELNB_DIR', plugin_dir_path( __FILE__ ) );
define( 'ELNB_URL', plugin_dir_url( __FILE__ ) );
define( 'ELNB_DEFAULT_API', 'https://mempool.elektron-net.org/api/v1' );

require ELNB_DIR . 'includes/api.php';
require ELNB_DIR . 'includes/settings.php';
require ELNB_DIR . 'includes/render.php';
require ELNB_DIR . 'includes/blocks.php';
