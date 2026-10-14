<?php
/**
 * The front page template.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="main" class="site-main">
<?php get_template_part( 'template-parts/front-hero' ); ?>

<?php if ( have_posts() && 'page' === get_option( 'show_on_front' ) ) : ?>
	<div class="container content-area front-content">
		<div class="front-entry">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<div <?php post_class( 'entry' ); ?>>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
<?php endif; ?>

<?php
$elektron_features = array();
for ( $elektron_i = 1; $elektron_i <= 3; $elektron_i++ ) {
	$elektron_feature_title = elektron_front_option( 'feature_' . $elektron_i . '_title' );
	if ( '' !== $elektron_feature_title ) {
		$elektron_features[] = array( $elektron_feature_title, elektron_front_option( 'feature_' . $elektron_i . '_text' ) );
	}
}
if ( $elektron_features ) :
	?>
	<section class="container features">
		<?php if ( elektron_front_option( 'features_title' ) ) : ?>
			<h2 class="section-title"><?php echo esc_html( elektron_front_option( 'features_title' ) ); ?></h2>
		<?php endif; ?>
		<div class="feature-grid">
			<?php foreach ( $elektron_features as $elektron_feature ) : ?>
				<div class="feature">
					<h3 class="feature-title"><?php echo esc_html( $elektron_feature[0] ); ?></h3>
					<p><?php echo esc_html( $elektron_feature[1] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>

<?php
get_template_part( 'template-parts/front-start' );

if ( get_theme_mod( 'elektron_show_latest_posts', true ) ) :
	$elektron_latest = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( $elektron_latest->have_posts() ) :
		?>
		<section class="container latest-posts">
			<h2 class="section-title"><?php esc_html_e( 'Latest news', 'elektron' ); ?></h2>
			<div class="post-grid">
				<?php
				while ( $elektron_latest->have_posts() ) :
					$elektron_latest->the_post();
					get_template_part( 'template-parts/content', 'post' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
		<?php
	endif;
endif;
?>
</main>

<?php
get_footer();
