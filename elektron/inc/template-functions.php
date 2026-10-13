<?php
/**
 * Functions which enhance the theme by hooking into WordPress.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds custom classes to the body element.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function elektron_body_classes( $classes ) {
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'elektron_body_classes' );

/**
 * Adds a pingback url auto-discovery header for singular posts.
 */
function elektron_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'elektron_pingback_header' );

/**
 * Replaces the default excerpt "more" text.
 *
 * @return string
 */
function elektron_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'elektron_excerpt_more' );

/**
 * Shortens the excerpt.
 *
 * @return int
 */
function elektron_excerpt_length() {
	return 28;
}
add_filter( 'excerpt_length', 'elektron_excerpt_length' );

/**
 * Marks parent menu items so the script and styles can add toggles.
 *
 * @param string[] $classes Menu item classes.
 * @param WP_Post  $item    Menu item.
 * @param stdClass $args    Menu arguments.
 * @return string[]
 */
function elektron_menu_item_classes( $classes, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location && in_array( 'menu-item-has-children', $classes, true ) ) {
		$classes[] = 'has-submenu';
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'elektron_menu_item_classes', 10, 3 );

/**
 * Returns the default hero and stats content used by the front page.
 *
 * @return array
 */
function elektron_front_defaults() {
	return array(
		'eyebrow'        => __( 'A different kind of money', 'elektron' ),
		'title'          => __( 'Money should remember its value. Not its history.', 'elektron' ),
		'text'           => __( 'Bitcoin made digital scarcity possible. Elektron Net explores a different approach to digital cash: verify the current state, without keeping the entire transaction history forever.', 'elektron' ),
		'primary_label'  => __( 'Explore the protocol', 'elektron' ),
		'primary_url'    => 'https://elektron-net.org',
		'secondary_label' => __( 'View source code', 'elektron' ),
		'secondary_url'  => 'https://github.com/kutlusoy/elektron-net',
		'stats_title'    => __( 'Network overview', 'elektron' ),
		'stats_note'     => __( 'Illustrative design preview, not live network data.', 'elektron' ),
		'stat_1_value'   => 'SHA-256d',
		'stat_1_label'   => __( 'Proof of Work', 'elektron' ),
		'stat_2_value'   => 'UTXO',
		'stat_2_label'   => __( 'Current state verification', 'elektron' ),
		'stat_3_value'   => '60 sec',
		'stat_3_label'   => __( 'Target block interval', 'elektron' ),
		'stat_4_value'   => 'MIT',
		'stat_4_label'   => __( 'Open-source license', 'elektron' ),
		'features_title'  => __( 'The protocol principle', 'elektron' ),
		'feature_1_title' => __( 'Verify the current state', 'elektron' ),
		'feature_1_text'  => __( 'Nodes validate the live UTXO set, the coins that can be spent right now, instead of an ever-growing archive of the past.', 'elektron' ),
		'feature_2_title' => __( 'Prune the history', 'elektron' ),
		'feature_2_text'  => __( 'Blocks deeper than 197,280 (about 137 days) are pruned on every node. Pruning is mandatory, there is no archival mode.', 'elektron' ),
		'feature_3_title' => __( 'Attest the state', 'elektron' ),
		'feature_3_text'  => __( 'Every block carries an on-chain attestation of the UTXO set, so new nodes can bootstrap from a peer snapshot and verify it cryptographically.', 'elektron' ),
	);
}

/**
 * Gets a front page option with its default.
 *
 * @param string $key Option key.
 * @return string
 */
function elektron_front_option( $key ) {
	$defaults = elektron_front_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return (string) get_theme_mod( 'elektron_' . $key, $default );
}
