<?php
/**
 * The template for displaying 404 pages.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="container content-area">
	<main id="main" class="site-main">
		<section class="error-404 not-found">
			<p class="error-code" aria-hidden="true">404</p>
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'This page could not be found.', 'elektron' ); ?></h1>
			</header>
			<div class="page-content">
				<p><?php esc_html_e( 'Nothing was found at this location. Try a search instead.', 'elektron' ); ?></p>
				<?php get_search_form(); ?>
				<p><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'elektron' ); ?></a></p>
			</div>
		</section>
	</main>
</div>

<?php
get_footer();
