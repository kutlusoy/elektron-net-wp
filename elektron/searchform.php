<?php
/**
 * Template for displaying the search form.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$elektron_form_id = wp_unique_id( 'search-form-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $elektron_form_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'elektron' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $elektron_form_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Search&hellip;', 'elektron' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<button type="submit" class="search-submit"><?php esc_html_e( 'Search', 'elektron' ); ?></button>
</form>
