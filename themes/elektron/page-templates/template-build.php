<?php
/**
 * Template Name: Build & Verify
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
		__( 'Repository', 'elektron' ),
		__( 'Clone the node, read the history and follow every change in the open.', 'elektron' ),
		array(
			array( __( 'Source repository', 'elektron' ), elektron_repo_link() ),
			array( __( 'Security policy', 'elektron' ), elektron_repo_link( 'SECURITY.md' ) ),
			array( __( 'Contributing', 'elektron' ), elektron_repo_link( 'CONTRIBUTING.md' ) ),
		),
	),
	array(
		__( 'Installation', 'elektron' ),
		__( 'Step-by-step instructions and a short copy-and-paste path to build the node from source.', 'elektron' ),
		array(
			array( __( 'Installation guide', 'elektron' ), elektron_repo_link( 'INSTALL.md' ) ),
			array( __( 'Quick install', 'elektron' ), elektron_repo_link( 'Quick-Install.md' ) ),
			array( __( 'Build on Unix', 'elektron' ), elektron_repo_link( 'doc/build-unix.md' ) ),
			array( __( 'Build on Windows', 'elektron' ), elektron_repo_link( 'doc/build-windows.md' ) ),
		),
	),
	array(
		__( 'Run a node', 'elektron' ),
		__( 'Technical setup for operators, plus the guide for mining pools that connect through Stratum.', 'elektron' ),
		array(
			array( __( 'Technical setup', 'elektron' ), elektron_repo_link( 'TECHNICAL_SETUP.md' ) ),
			array( __( 'Mining pool integration', 'elektron' ), elektron_repo_link( 'doc-elektron/mining-pool-integration.md' ) ),
		),
	),
	array(
		__( 'Releases', 'elektron' ),
		__( 'Download tagged releases and read the change log of each one.', 'elektron' ),
		array(
			array( __( 'All releases', 'elektron' ), elektron_repo_url() . '/releases' ),
		),
	),
	array(
		__( 'Reproducible builds', 'elektron' ),
		__( 'Build the release binaries yourself with Guix and compare them with the published ones.', 'elektron' ),
		array(
			array( __( 'Guix build guide', 'elektron' ), elektron_repo_link( 'contrib/guix/README.md' ) ),
			array( __( 'Verify binaries', 'elektron' ), elektron_repo_link( 'contrib/verify-binaries/README.md' ) ),
		),
	),
	array(
		__( 'Verify the code', 'elektron' ),
		__( 'Check signed commits and run the unit and functional test suites yourself.', 'elektron' ),
		array(
			array( __( 'Verify commits', 'elektron' ), elektron_repo_link( 'contrib/verify-commits/README.md' ) ),
			array( __( 'Tests', 'elektron' ), elektron_repo_link( 'test/README.md' ) ),
			array( __( 'Continuous integration', 'elektron' ), elektron_repo_link( 'ci/README.md' ) ),
		),
	),
);

while ( have_posts() ) :
	the_post();
	?>
	<main id="main" class="site-main">
		<?php get_template_part( 'template-parts/page-hero' ); ?>
		<section class="container section">
			<?php elektron_render_cards( $elektron_cards, '', 2 ); ?>
		</section>
		<?php get_template_part( 'template-parts/page-content' ); ?>
	</main>
	<?php
endwhile;

get_footer();
