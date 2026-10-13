<?php
/**
 * Template Name: Network
 * Template Post Type: page
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$elektron_live = elektron_get_live_stats();
$elektron_api  = elektron_explorer_api();

$elektron_tiles = array();
if ( $elektron_live ) {
	$elektron_tiles[] = array( __( 'Block height', 'elektron' ), number_format_i18n( $elektron_live['height'] ) );
	if ( null !== $elektron_live['hashrate'] ) {
		$elektron_tiles[] = array( __( 'Network hash rate', 'elektron' ), elektron_format_hashrate( $elektron_live['hashrate'] ) );
	}
	if ( null !== $elektron_live['difficulty'] ) {
		$elektron_tiles[] = array( __( 'Difficulty', 'elektron' ), elektron_format_compact( $elektron_live['difficulty'] ) );
	}
	if ( null !== $elektron_live['mempool_txs'] ) {
		$elektron_tiles[] = array( __( 'Mempool transactions', 'elektron' ), number_format_i18n( $elektron_live['mempool_txs'], 0 ) );
	}
	if ( null !== $elektron_live['fee_fastest'] ) {
		/* translators: %s: fee rate number */
		$elektron_tiles[] = array( __( 'Fastest fee', 'elektron' ), sprintf( __( '%s lep/vB', 'elektron' ), number_format_i18n( $elektron_live['fee_fastest'], 0 ) ) );
	}
	if ( null !== $elektron_live['fee_hour'] ) {
		/* translators: %s: fee rate number */
		$elektron_tiles[] = array( __( 'One hour fee', 'elektron' ), sprintf( __( '%s lep/vB', 'elektron' ), number_format_i18n( $elektron_live['fee_hour'], 0 ) ) );
	}
}

$elektron_params = array(
	array( __( 'Proof of work', 'elektron' ), 'SHA-256d' ),
	array( __( 'Block time', 'elektron' ), __( '60 seconds', 'elektron' ) ),
	array( __( 'Difficulty retarget', 'elektron' ), __( 'Every 2,016 blocks', 'elektron' ) ),
	array( __( 'Halving interval', 'elektron' ), __( '2,102,400 blocks', 'elektron' ) ),
	array( __( 'Maximum supply', 'elektron' ), '21,000,000 Elek' ),
	array( __( 'Smallest unit', 'elektron' ), __( '1 Elek = 100,000,000 Lep', 'elektron' ) ),
	array( __( 'Genesis reward', 'elektron' ), '5 Elek' ),
	array( __( 'Pruning depth', 'elektron' ), __( '197,280 blocks (about 137 days)', 'elektron' ) ),
	array( __( 'Address prefix', 'elektron' ), 'be1q... / be1p...' ),
	array( __( 'SLIP-44 coin type', 'elektron' ), '1370' ),
	array( __( 'P2P port', 'elektron' ), '8333' ),
	array( __( 'RPC port', 'elektron' ), '8332' ),
	array( __( 'Network magic', 'elektron' ), '0xe1ec7a6e' ),
);

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/page-hero' );
	?>
	<main id="main" class="site-main">

		<section class="container section" aria-labelledby="live-title">
			<h2 id="live-title" class="section-title">
				<?php esc_html_e( 'Live network data', 'elektron' ); ?>
				<?php if ( $elektron_live ) : ?>
					<span class="live-badge"><?php esc_html_e( 'Live data', 'elektron' ); ?></span>
				<?php endif; ?>
			</h2>

			<?php if ( $elektron_live ) : ?>
				<div class="stat-grid">
					<?php foreach ( $elektron_tiles as $elektron_tile ) : ?>
						<div class="stat-tile">
							<p class="stat-value"><?php echo esc_html( $elektron_tile[1] ); ?></p>
							<p class="stat-label"><?php echo esc_html( $elektron_tile[0] ); ?></p>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( $elektron_live['hash'] ) : ?>
					<div class="data-block">
						<p class="stat-label"><?php esc_html_e( 'Latest block hash', 'elektron' ); ?></p>
						<code class="hash"><?php echo esc_html( $elektron_live['hash'] ); ?></code>
					</div>
				<?php endif; ?>

				<?php if ( null !== $elektron_live['progress'] ) : ?>
					<div class="data-block">
						<p class="stat-label">
							<?php
							/* translators: %s: percentage number */
							printf( esc_html__( 'Difficulty retarget: %s%% of the current period', 'elektron' ), esc_html( number_format_i18n( $elektron_live['progress'], 1 ) ) );
							?>
						</p>
						<progress class="retarget-progress" max="100" value="<?php echo esc_attr( (string) round( $elektron_live['progress'], 1 ) ); ?>"></progress>
						<ul class="meta-list">
							<?php if ( null !== $elektron_live['remaining'] ) : ?>
								<li>
									<?php
									/* translators: %s: block count */
									printf( esc_html__( '%s blocks remaining', 'elektron' ), esc_html( number_format_i18n( $elektron_live['remaining'], 0 ) ) );
									?>
								</li>
							<?php endif; ?>
							<?php if ( null !== $elektron_live['next_height'] ) : ?>
								<li>
									<?php
									/* translators: %s: block height */
									printf( esc_html__( 'Next retarget at block %s', 'elektron' ), esc_html( number_format_i18n( $elektron_live['next_height'], 0 ) ) );
									?>
								</li>
							<?php endif; ?>
							<?php if ( null !== $elektron_live['eta'] ) : ?>
								<li>
									<?php
									/* translators: %s: date and time */
									printf( esc_html__( 'Estimated: %s', 'elektron' ), esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $elektron_live['eta'] ) ) );
									?>
								</li>
							<?php endif; ?>
							<?php if ( null !== $elektron_live['change'] ) : ?>
								<li>
									<?php
									/* translators: %s: percentage number */
									printf( esc_html__( 'Expected difficulty change: %s%%', 'elektron' ), esc_html( number_format_i18n( $elektron_live['change'], 2 ) ) );
									?>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>

				<p class="data-updated">
					<?php
					/* translators: %s: time span such as "1 minute" */
					printf( esc_html__( 'Data from the block explorer, updated at most every minute. Last update %s ago.', 'elektron' ), esc_html( human_time_diff( $elektron_live['time'] ) ) );
					?>
				</p>
			<?php else : ?>
				<div class="notice">
					<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
						<p>
							<?php
							printf(
								wp_kses(
									/* translators: %s: link to the Customizer */
									__( 'No live data available. Enter a block explorer API address under <a href="%s">Appearance, Customize, Live Network Data</a>. Nothing is shown here until real data can be loaded.', 'elektron' ),
									array( 'a' => array( 'href' => array() ) )
								),
								esc_url( admin_url( 'customize.php?autofocus[section]=elektron_live' ) )
							);
							?>
						</p>
					<?php else : ?>
						<p><?php esc_html_e( 'Live network data is currently unavailable.', 'elektron' ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</section>

		<section class="container section" aria-labelledby="params-title">
			<h2 id="params-title" class="section-title"><?php esc_html_e( 'Network parameters', 'elektron' ); ?></h2>
			<table class="spec-table">
				<tbody>
					<?php foreach ( $elektron_params as $elektron_param ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $elektron_param[0] ); ?></th>
							<td><?php echo esc_html( $elektron_param[1] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p class="data-updated">
				<a href="<?php echo esc_url( elektron_repo_link( 'src/kernel/chainparams.cpp' ) ); ?>" rel="noopener"><?php esc_html_e( 'Source of the mainnet parameters', 'elektron' ); ?><span aria-hidden="true"> &#8599;</span></a>
			</p>
		</section>

		<?php if ( $elektron_api ) : ?>
			<section class="container section" aria-labelledby="api-title">
				<h2 id="api-title" class="section-title"><?php esc_html_e( 'API', 'elektron' ); ?></h2>
				<p class="section-lead"><?php esc_html_e( 'The figures above come from these public endpoints.', 'elektron' ); ?></p>
				<table class="spec-table">
					<tbody>
						<?php
						$elektron_endpoints = array(
							'/blocks/tip/height'    => __( 'Latest block height', 'elektron' ),
							'/blocks/tip/hash'      => __( 'Latest block hash', 'elektron' ),
							'/difficulty-adjustment' => __( 'Difficulty retarget progress', 'elektron' ),
							'/mining/hashrate/3d'   => __( 'Hash rate and difficulty', 'elektron' ),
							'/mempool'              => __( 'Mempool summary', 'elektron' ),
							'/fees/recommended'     => __( 'Recommended fees', 'elektron' ),
						);
						foreach ( $elektron_endpoints as $elektron_path => $elektron_desc ) :
							?>
							<tr>
								<th scope="row"><a href="<?php echo esc_url( $elektron_api . $elektron_path ); ?>" rel="noopener"><code><?php echo esc_html( $elektron_path ); ?></code></a></th>
								<td><?php echo esc_html( $elektron_desc ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</section>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/page-content' ); ?>
	</main>
	<?php
endwhile;

get_footer();
