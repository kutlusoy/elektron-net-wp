<?php
/**
 * Template part for the hero of section pages.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<header class="page-hero">
	<div class="container">
		<h1 class="page-hero-title"><?php the_title(); ?></h1>
		<?php if ( has_excerpt() ) : ?>
			<p class="page-hero-lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</header>
