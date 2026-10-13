<?php
/**
 * Template part for the front page entry points for users and developers.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$elektron_build_url   = elektron_template_page_url( 'page-templates/template-build.php' );
$elektron_network_url = elektron_template_page_url( 'page-templates/template-network.php' );

$elektron_user_links = array(
	array( __( 'Download a release', 'elektron' ), elektron_repo_url() . '/releases' ),
	array( __( 'Move coins from a legacy wallet', 'elektron' ), elektron_repo_link( 'doc-elektron/howto-coinsweep-legacy-wallet.md' ) ),
);
if ( $elektron_network_url ) {
	$elektron_user_links[] = array( __( 'See the live network', 'elektron' ), $elektron_network_url );
}

$elektron_dev_links = array(
	array( __( 'Browse the source code', 'elektron' ), elektron_repo_link() ),
	array( __( 'Differences from Bitcoin Core', 'elektron' ), elektron_repo_link( 'doc-elektron/BITCOIN_CORE_DIFF.md' ) ),
	array( __( 'Wallet integration guide', 'elektron' ), elektron_repo_link( 'doc-elektron/guideline-wallet-integration.md' ) ),
);
if ( $elektron_build_url ) {
	array_unshift( $elektron_dev_links, array( __( 'Build and verify', 'elektron' ), $elektron_build_url ) );
}

$elektron_cards = array(
	array( __( 'For users', 'elektron' ), __( 'Get the wallet and node, move your coins and watch the network.', 'elektron' ), $elektron_user_links ),
	array( __( 'For developers', 'elektron' ), __( 'Read the code, build the node, verify the release and integrate.', 'elektron' ), $elektron_dev_links ),
);
?>

<section class="container features" aria-labelledby="start-title">
	<h2 id="start-title" class="section-title"><?php esc_html_e( 'Start here', 'elektron' ); ?></h2>
	<?php elektron_render_cards( $elektron_cards, 'feature-grid-two' ); ?>
</section>
