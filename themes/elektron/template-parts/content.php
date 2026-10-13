<?php
/**
 * Template part for displaying posts in archives.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'card' ); ?>>
	<?php elektron_post_thumbnail( 'elektron-card' ); ?>

	<div class="card-body">
		<?php if ( is_sticky() && ! is_paged() ) : ?>
			<span class="sticky-badge"><?php esc_html_e( 'Featured', 'elektron' ); ?></span>
		<?php endif; ?>
		<header class="entry-header">
			<?php elektron_entry_meta(); ?>
			<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
		</header>

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div>

		<footer class="entry-footer">
			<?php elektron_entry_footer(); ?>
		</footer>
	</div>
</article>
