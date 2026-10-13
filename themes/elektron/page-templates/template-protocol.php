<?php
/**
 * Template Name: Protocol & Technology
 * Template Post Type: page
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$elektron_cards = array(
	array(
		__( 'Consensus', 'elektron' ),
		__( 'SHA-256d proof of work and the Bitcoin script engine, with a 60-second block target, a 2,016-block difficulty retarget and a halving every 2,102,400 blocks.', 'elektron' ),
		array(
			array( __( 'Chain parameters', 'elektron' ), elektron_repo_link( 'src/kernel/chainparams.cpp' ) ),
			array( __( 'Differences from Bitcoin Core', 'elektron' ), elektron_repo_link( 'doc-elektron/BITCOIN_CORE_DIFF.md' ) ),
		),
	),
	array(
		__( 'Pruning', 'elektron' ),
		__( 'Every node prunes blocks deeper than 197,280 (about 137 days). There is no archival mode: headers, checkpoints and the current UTXO set remain.', 'elektron' ),
		array(
			array( __( 'Prune depth in validation.h', 'elektron' ), elektron_repo_link( 'src/validation.h' ) ),
			array( __( 'Pruning and snapshot audit', 'elektron' ), elektron_repo_link( 'doc-elektron/AUDIT_PRUNING_SNAPSHOT.md' ) ),
		),
	),
	array(
		__( 'UTXO attestation', 'elektron' ),
		__( 'Every block carries a compact attestation of the UTXO set, kept up to date with an incremental MuHash accumulator instead of a full rescan.', 'elektron' ),
		array(
			array( __( 'MuHash accumulator', 'elektron' ), elektron_repo_link( 'src/kernel/utxo_muhash.h' ) ),
			array( __( 'Scalability design', 'elektron' ), elektron_repo_link( 'doc-elektron/fix-report-utxo-attestation-scalability.md' ) ),
			array( __( 'Change log', 'elektron' ), elektron_repo_link( 'doc-elektron/CHANGELOG-muhash-attestation.md' ) ),
		),
	),
	array(
		__( 'Snapshots', 'elektron' ),
		__( 'After the first checkpoint, fresh nodes bootstrap from a peer-to-peer UTXO snapshot that is verified against the on-chain attestation.', 'elektron' ),
		array(
			array( __( 'Bootstrap trust model', 'elektron' ), elektron_repo_link( 'doc-elektron/fix-report-snapshot-bootstrap-trust.md' ) ),
			array( __( 'Whitepaper', 'elektron' ), elektron_repo_link( 'WHITEPAPER.md' ) ),
		),
	),
	array(
		__( 'Wallet recovery', 'elektron' ),
		__( 'Wallets use SLIP-44 coin type 1370. Coins from an old coin type 0 wallet can be moved to a new wallet with a documented sweep.', 'elektron' ),
		array(
			array( __( 'Move coins from a legacy wallet', 'elektron' ), elektron_repo_link( 'doc-elektron/howto-coinsweep-legacy-wallet.md' ) ),
			array( __( 'Wallet integration guide', 'elektron' ), elektron_repo_link( 'doc-elektron/guideline-wallet-integration.md' ) ),
		),
	),
	array(
		__( 'Source code', 'elektron' ),
		__( 'The node is a focused fork of Bitcoin Core. Every deliberate change is documented file by file.', 'elektron' ),
		array(
			array( __( 'Repository', 'elektron' ), elektron_repo_link() ),
			array( __( 'Source tree', 'elektron' ), elektron_repo_link( 'src', 'tree' ) ),
		),
	),
);

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-hero' );
	?>
	<main id="main" class="site-main">
		<section class="container section">
			<?php elektron_render_cards( $elektron_cards ); ?>
		</section>
		<?php get_template_part( 'template-parts/page-content' ); ?>
	</main>
	<?php
endwhile;

get_footer();
