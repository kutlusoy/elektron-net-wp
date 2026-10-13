<?php
/**
 * Template part for the editor content of section pages.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( '' === trim( get_the_content() ) ) {
	return;
}
?>

<section class="container section section-narrow">
	<div class="entry-content">
		<?php the_content(); ?>
	</div>
</section>
