<?php
/**
 * The header template.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'elektron' ); ?></a>

	<?php
	$elektron_hud = elektron_hud_items();
	/* translators: %s: effect level such as Subtle */
	$elektron_fx_label = __( 'Effects: %s', 'elektron' );
	?>
	<?php if ( $elektron_hud ) : ?>
		<div class="hud-bar" role="region" aria-label="<?php esc_attr_e( 'Network status', 'elektron' ); ?>">
			<ul class="container hud-list">
				<li class="hud-live"><?php esc_html_e( 'Live', 'elektron' ); ?></li>
				<?php foreach ( $elektron_hud as $elektron_item ) : ?>
					<li><?php echo esc_html( $elektron_item[0] ); ?> <b><?php echo esc_html( $elektron_item[1] ); ?></b></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<header id="masthead" class="site-header">
		<div class="container header-inner">
			<?php elektron_site_branding(); ?>

			<div class="header-actions">
				<?php if ( get_theme_mod( 'elektron_show_style_menu', true ) ) : ?>
					<details class="style-menu">
						<summary>
							<span class="screen-reader-text"><?php esc_html_e( 'Color theme and effects', 'elektron' ); ?></span>
							<svg class="icon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 3a9 9 0 1 0 0 18c1.1 0 1.8-.9 1.5-1.9-.3-1 .3-2.1 1.4-2.1H17a4 4 0 0 0 4-4c0-5-4.4-10-9-10zM7.5 12a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm3-4a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" fill="currentColor"/></svg>
						</summary>
						<div class="style-panel">
							<p class="style-panel-title"><?php esc_html_e( 'Color theme', 'elektron' ); ?></p>
							<ul class="palette-list">
								<?php foreach ( elektron_palettes() as $elektron_slug => $elektron_label ) : ?>
									<li>
										<button type="button" class="palette-option" data-palette="<?php echo esc_attr( $elektron_slug ); ?>" aria-pressed="false">
											<span class="palette-swatch" aria-hidden="true"></span>
											<?php echo esc_html( $elektron_label ); ?>
										</button>
									</li>
								<?php endforeach; ?>
							</ul>
							<button type="button" class="fx-toggle" data-labels="<?php echo esc_attr( wp_json_encode( elektron_fx_levels() ) ); ?>" data-label="<?php echo esc_attr( $elektron_fx_label ); ?>"></button>
						</div>
					</details>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'elektron_show_scheme_toggle', true ) ) : ?>
					<button type="button" class="scheme-toggle" aria-pressed="false">
						<span class="screen-reader-text"><?php esc_html_e( 'Switch between light and dark mode', 'elektron' ); ?></span>
						<svg class="icon icon-moon" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z" fill="currentColor"/></svg>
						<svg class="icon icon-sun" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4.5" fill="currentColor"/><path d="M12 1.5v3M12 19.5v3M1.5 12h3M19.5 12h3M4.6 4.6l2.1 2.1M17.3 17.3l2.1 2.1M4.6 19.4l2.1-2.1M17.3 6.7l2.1-2.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
					</button>
				<?php endif; ?>

				<button type="button" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
					<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'elektron' ); ?></span>
					<span class="menu-toggle-bars" aria-hidden="true"></span>
				</button>
			</div>

			<nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'elektron' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'container'      => false,
						'fallback_cb'    => 'elektron_menu_fallback',
					)
				);
				?>
			</nav>
		</div>
	</header>

	<div id="content" class="site-content">
