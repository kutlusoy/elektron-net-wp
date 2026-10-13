<?php
/**
 * The footer template.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	</div>

	<footer id="colophon" class="site-footer">
		<?php if ( is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' ) ) : ?>
			<div class="container footer-widgets">
				<?php
				for ( $elektron_i = 1; $elektron_i <= 3; $elektron_i++ ) {
					if ( is_active_sidebar( 'footer-' . $elektron_i ) ) {
						echo '<div class="footer-widget-area">';
						dynamic_sidebar( 'footer-' . $elektron_i );
						echo '</div>';
					}
				}
				?>
			</div>
		<?php endif; ?>

		<div class="container footer-bottom">
			<div class="site-info">
				<?php
				$elektron_footer_text = get_theme_mod( 'elektron_footer_text', '' );
				if ( $elektron_footer_text ) {
					echo wp_kses_post( $elektron_footer_text );
				} else {
					printf(
						/* translators: 1: year, 2: site name */
						esc_html__( '&copy; %1$s %2$s', 'elektron' ),
						esc_html( gmdate( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
				}
				?>
				<span class="sep" aria-hidden="true">/</span>
				<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'elektron' ) ); ?>">
					<?php
					/* translators: %s: CMS name, i.e. WordPress */
					printf( esc_html__( 'Proudly powered by %s', 'elektron' ), 'WordPress' );
					?>
				</a>
			</div>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer menu', 'elektron' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'depth'          => 1,
							'container'      => false,
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<?php if ( has_nav_menu( 'social' ) ) : ?>
				<nav class="social-navigation" aria-label="<?php esc_attr_e( 'Social links', 'elektron' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'social',
							'depth'          => 1,
							'container'      => false,
						)
					);
					?>
				</nav>
			<?php endif; ?>
		</div>
	</footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
