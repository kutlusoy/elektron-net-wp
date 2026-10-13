<?php
/**
 * Template part for displaying single posts.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
	<header class="entry-header">
		<?php elektron_entry_meta(); ?>
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header>

	<?php elektron_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: post title */
					__( 'Continue reading<span class="screen-reader-text"> &ldquo;%s&rdquo;</span>', 'elektron' ),
					array( 'span' => array( 'class' => array() ) )
				),
				wp_kses_post( get_the_title() )
			)
		);

		wp_link_pages(
			array(
				'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Post pages', 'elektron' ) . '"><span class="page-links-title">' . esc_html__( 'Pages:', 'elektron' ) . '</span>',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<footer class="entry-footer">
		<?php elektron_entry_footer(); ?>
	</footer>
</article>
