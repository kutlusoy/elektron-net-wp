<?php
/**
 * Elektron theme functions and definitions.
 *
 * @author Ali Kutlusoy <kutlusoy1975@gmail.com>
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ELEKTRON_VERSION', '0.0.1' );

if ( ! isset( $content_width ) ) {
	$content_width = 760;
}

/**
 * Sets up theme defaults and registers support for WordPress features.
 */
function elektron_setup() {
	load_theme_textdomain( 'elektron', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link', 'gallery', 'audio' ) );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 80,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'custom-background',
		array(
			'default-color' => '0b1220',
		)
	);

	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => esc_html__( 'Elektron Blue', 'elektron' ),
				'slug'  => 'elektron-blue',
				'color' => '#2da4fa',
			),
			array(
				'name'  => esc_html__( 'Deep Blue', 'elektron' ),
				'slug'  => 'deep-blue',
				'color' => '#1b8ff0',
			),
			array(
				'name'  => esc_html__( 'Night', 'elektron' ),
				'slug'  => 'night',
				'color' => '#0b1220',
			),
			array(
				'name'  => esc_html__( 'Surface', 'elektron' ),
				'slug'  => 'surface',
				'color' => '#111a2b',
			),
			array(
				'name'  => esc_html__( 'Mist', 'elektron' ),
				'slug'  => 'mist',
				'color' => '#e8eef8',
			),
			array(
				'name'  => esc_html__( 'White', 'elektron' ),
				'slug'  => 'white',
				'color' => '#ffffff',
			),
		)
	);

	add_editor_style( 'assets/css/editor.css' );

	add_image_size( 'elektron-card', 800, 450, true );

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'elektron' ),
			'footer'  => esc_html__( 'Footer Menu', 'elektron' ),
			'social'  => esc_html__( 'Social Links Menu', 'elektron' ),
		)
	);
}
add_action( 'after_setup_theme', 'elektron_setup' );

/**
 * Registers widget areas.
 */
function elektron_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'elektron' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Widgets shown beside posts and archives.', 'elektron' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer widget area number */
				'name'          => sprintf( esc_html__( 'Footer %d', 'elektron' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => esc_html__( 'Widgets shown in the footer.', 'elektron' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'elektron_widgets_init' );

/**
 * Enqueues scripts and styles.
 */
function elektron_scripts() {
	wp_enqueue_style( 'elektron-style', get_stylesheet_uri(), array(), ELEKTRON_VERSION );

	wp_enqueue_script( 'elektron-scheme', get_template_directory_uri() . '/assets/js/scheme.js', array(), ELEKTRON_VERSION, false );

	wp_enqueue_script(
		'elektron-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		ELEKTRON_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_localize_script(
		'elektron-main',
		'elektronL10n',
		array(
			'expand'   => esc_html__( 'Expand child menu', 'elektron' ),
			'collapse' => esc_html__( 'Collapse child menu', 'elektron' ),
		)
	);

	if ( is_front_page() && get_theme_mod( 'elektron_hero_effect', true ) ) {
		wp_enqueue_script(
			'elektron-hero-network',
			get_template_directory_uri() . '/assets/js/hero-network.min.js',
			array(),
			ELEKTRON_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'elektron_scripts' );

require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/network.php';
require get_template_directory() . '/inc/customizer.php';
if ( is_admin() ) {
	require get_template_directory() . '/inc/plugin-recommendation.php';
}
