<?php
/**
 * Template part for the front page hero and overview card.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$elektron_live  = elektron_get_live_stats();
$elektron_stats = array();
for ( $elektron_i = 1; $elektron_i <= 4; $elektron_i++ ) {
	$elektron_value = elektron_front_option( 'stat_' . $elektron_i . '_value' );
	$elektron_label = elektron_front_option( 'stat_' . $elektron_i . '_label' );
	if ( '' !== $elektron_value ) {
		$elektron_stats[] = array( $elektron_value, $elektron_label );
	}
}

if ( $elektron_live ) {
	$elektron_static = $elektron_stats;
	$elektron_stats  = array(
		array( number_format_i18n( $elektron_live['height'] ), __( 'Block height', 'elektron' ) ),
	);
	if ( null !== $elektron_live['progress'] ) {
		/* translators: %s: percentage number */
		$elektron_stats[] = array( sprintf( __( '%s%%', 'elektron' ), number_format_i18n( $elektron_live['progress'], 0 ) ), __( 'Difficulty retarget progress', 'elektron' ) );
	}
	if ( null !== $elektron_live['fee_fastest'] ) {
		/* translators: %s: fee rate number */
		$elektron_stats[] = array( sprintf( __( '%s lep/vB', 'elektron' ), number_format_i18n( $elektron_live['fee_fastest'], 0 ) ), __( 'Fastest fee', 'elektron' ) );
	}
	if ( isset( $elektron_static[2] ) ) {
		$elektron_stats[] = $elektron_static[2];
	}
}
?>

<section class="hero">
	<?php if ( get_theme_mod( 'elektron_hero_effect', true ) ) : ?>
		<canvas class="hero-canvas" aria-hidden="true"></canvas>
	<?php endif; ?>
	<div class="container hero-inner">
		<div class="hero-copy">
			<?php if ( elektron_front_option( 'eyebrow' ) ) : ?>
				<p class="eyebrow"><?php echo esc_html( elektron_front_option( 'eyebrow' ) ); ?></p>
			<?php endif; ?>

			<h1 class="hero-title"><?php echo esc_html( elektron_front_option( 'title' ) ); ?></h1>
			<p class="hero-text"><?php echo esc_html( elektron_front_option( 'text' ) ); ?></p>

			<div class="hero-actions">
				<?php if ( elektron_front_option( 'primary_label' ) && elektron_front_option( 'primary_url' ) ) : ?>
					<a class="button button-primary" href="<?php echo esc_url( elektron_front_option( 'primary_url' ) ); ?>">
						<?php echo esc_html( elektron_front_option( 'primary_label' ) ); ?>
						<span aria-hidden="true">&#8599;</span>
					</a>
				<?php endif; ?>
				<?php if ( elektron_front_option( 'secondary_label' ) && elektron_front_option( 'secondary_url' ) ) : ?>
					<a class="button button-outline" href="<?php echo esc_url( elektron_front_option( 'secondary_url' ) ); ?>">
						<?php echo esc_html( elektron_front_option( 'secondary_label' ) ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $elektron_stats ) : ?>
			<aside class="overview" aria-labelledby="overview-title">
				<h2 id="overview-title" class="overview-title">
					<span><?php echo esc_html( elektron_front_option( 'stats_title' ) ); ?></span>
					<?php if ( $elektron_live ) : ?>
						<span class="live-badge"><?php esc_html_e( 'Live data', 'elektron' ); ?></span>
					<?php endif; ?>
				</h2>
				<dl class="overview-grid">
					<?php foreach ( $elektron_stats as $elektron_stat ) : ?>
						<div class="overview-item">
							<dt class="overview-value"><?php echo esc_html( $elektron_stat[0] ); ?></dt>
							<dd class="overview-label"><?php echo esc_html( $elektron_stat[1] ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
				<?php if ( ! $elektron_live && elektron_front_option( 'stats_note' ) ) : ?>
					<p class="overview-note"><?php echo esc_html( elektron_front_option( 'stats_note' ) ); ?></p>
				<?php endif; ?>
			</aside>
		<?php endif; ?>
	</div>
</section>
