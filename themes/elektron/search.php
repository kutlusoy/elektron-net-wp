<?php
/**
 * The template for displaying search results.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="container content-area has-sidebar">
	<main id="main" class="site-main">
		<?php if ( have_posts() ) : ?>
			<header class="page-header">
				<h1 class="page-title">
					<?php
					/* translators: %s: search query */
					printf( esc_html__( 'Search results for: %s', 'elektron' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
					?>
				</h1>
			</header>

			<div class="post-list">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'search' );
				endwhile;
				?>
			</div>

			<?php the_posts_pagination(); ?>

		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</main>

	<?php get_sidebar(); ?>
</div>

<?php
get_footer();
