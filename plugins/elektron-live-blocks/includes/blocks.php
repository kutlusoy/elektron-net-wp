<?php
/**
 * Block registration.
 *
 * @package ElektronLiveBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the block slugs.
 *
 * @return string[]
 */
function elnb_block_slugs() {
	return array( 'overview', 'height', 'tip-hash', 'hashrate', 'retarget', 'mempool', 'fees', 'halving', 'latest-blocks', 'nodes', 'nodemap' );
}

/**
 * Registers styles, the editor script and all blocks.
 */
function elnb_register_blocks() {
	wp_register_style( 'elnb-blocks', ELNB_URL . 'assets/blocks.css', array(), ELNB_VERSION );
	wp_register_script(
		'elnb-editor',
		ELNB_URL . 'assets/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
		ELNB_VERSION,
		true
	);
	wp_set_script_translations( 'elnb-editor', 'elektron-live-blocks' );

	foreach ( elnb_block_slugs() as $slug ) {
		register_block_type(
			ELNB_DIR . 'blocks/' . $slug,
			array(
				'render_callback' => static function ( $attributes ) use ( $slug ) {
					return elnb_render( $slug, is_array( $attributes ) ? $attributes : array(), true );
				},
			)
		);
	}
}
add_action( 'init', 'elnb_register_blocks' );

/**
 * Adds a block category for the live blocks.
 *
 * @param array $categories Categories.
 * @return array
 */
function elnb_block_category( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'elektron-live',
			'title' => __( 'Elektron Live Data', 'elektron-live-blocks' ),
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'elnb_block_category' );

/**
 * Registers a block pattern with a ready-made network dashboard.
 */
function elnb_register_pattern() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}
	register_block_pattern(
		'elektron-live-blocks/dashboard',
		array(
			'title'      => __( 'Elektron network dashboard', 'elektron-live-blocks' ),
			'categories' => array( 'text' ),
			'content'    => '<!-- wp:elektron-live/overview /--><!-- wp:elektron-live/retarget /--><!-- wp:elektron-live/latest-blocks /-->',
		)
	);
}
add_action( 'init', 'elnb_register_pattern' );
