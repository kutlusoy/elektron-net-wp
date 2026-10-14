<?php
/**
 * Network data, source links and section helpers.
 *
 * @package Elektron
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the source code repository URL without trailing slash.
 *
 * @return string
 */
function elektron_repo_url() {
	$url = esc_url_raw( (string) get_theme_mod( 'elektron_repo_url', 'https://github.com/kutlusoy/elektron-net' ), array( 'https' ) );
	return untrailingslashit( $url ? $url : 'https://github.com/kutlusoy/elektron-net' );
}

/**
 * Builds a link to a file or folder in the source code repository.
 *
 * @param string $path Path inside the repository. Empty for the repository root.
 * @param string $type Either blob or tree.
 * @return string
 */
function elektron_repo_link( $path = '', $type = 'blob' ) {
	if ( '' === $path ) {
		return elektron_repo_url();
	}
	$type = 'tree' === $type ? 'tree' : 'blob';
	return elektron_repo_url() . '/' . $type . '/main/' . ltrim( $path, '/' );
}

/**
 * Returns the permalink of the first page that uses a page template.
 *
 * @param string $template Template file, for example page-templates/template-network.php.
 * @return string Empty string when no page uses it.
 */
function elektron_template_page_url( $template ) {
	$pages = get_pages(
		array(
			'meta_key'    => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $template, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'number'      => 1,
			'sort_column' => 'menu_order',
		)
	);

	return $pages ? get_permalink( $pages[0] ) : '';
}

/**
 * Menu shown when no primary menu is assigned: home plus the section pages.
 *
 * @param array $args Menu arguments.
 */
function elektron_menu_fallback( $args = array() ) {
	$items = array( array( __( 'Home', 'elektron' ), home_url( '/' ) ) );

	$templates = array(
		'page-templates/template-protocol.php' => __( 'Protocol & Technology', 'elektron' ),
		'page-templates/template-network.php'  => __( 'Network', 'elektron' ),
		'page-templates/template-build.php'    => __( 'Build & Verify', 'elektron' ),
	);
	foreach ( $templates as $file => $label ) {
		$url = elektron_template_page_url( $file );
		if ( $url ) {
			$items[] = array( $label, $url );
		}
	}

	if ( count( $items ) < 2 ) {
		wp_page_menu( $args );
		return;
	}

	echo '<div class="menu"><ul id="primary-menu">';
	foreach ( $items as $item ) {
		echo '<li><a href="' . esc_url( $item[1] ) . '">' . esc_html( $item[0] ) . '</a></li>';
	}
	echo '</ul></div>';
}

/**
 * Prints a grid of cards, each with a title, text and source links.
 *
 * Each card is array( title, text, links ) where links is a list of array( label, url ).
 *
 * @param array  $cards Cards.
 * @param string $extra_class Extra class for the grid.
 * @param int    $level       Heading level of the card titles.
 */
function elektron_render_cards( $cards, $extra_class = '', $level = 3 ) {
	$level = min( 6, max( 2, (int) $level ) );
	echo '<div class="feature-grid ' . esc_attr( $extra_class ) . '">';
	foreach ( $cards as $card ) {
		echo '<div class="feature">';
		echo '<h' . (int) $level . ' class="feature-title">' . esc_html( $card[0] ) . '</h' . (int) $level . '>';
		echo '<p>' . esc_html( $card[1] ) . '</p>';
		if ( ! empty( $card[2] ) ) {
			echo '<ul class="link-list">';
			foreach ( $card[2] as $link ) {
				echo '<li><a href="' . esc_url( $link[1] ) . '" rel="noopener">' . esc_html( $link[0] ) . '<span aria-hidden="true"> &#8599;</span></a></li>';
			}
			echo '</ul>';
		}
		echo '</div>';
	}
	echo '</div>';
}

/**
 * Reads a finite number from an array.
 *
 * @param mixed  $data Source array.
 * @param string $key  Key.
 * @return float|null
 */
function elektron_array_number( $data, $key ) {
	if ( is_array( $data ) && isset( $data[ $key ] ) && is_numeric( $data[ $key ] ) && is_finite( (float) $data[ $key ] ) ) {
		return (float) $data[ $key ];
	}
	return null;
}

/**
 * Fetches one value from the configured explorer API.
 *
 * @param string $base  Validated API base URL without trailing slash.
 * @param string $path  Endpoint path.
 * @param int    $limit Maximum response size in bytes.
 * @return array|string|null Digits or hex string for plain answers, array for JSON, null on failure.
 */
function elektron_fetch_explorer( $base, $path, $limit = 8192 ) {
	$response = wp_safe_remote_get(
		$base . $path,
		array(
			'timeout'             => 5,
			'redirection'         => 0,
			'limit_response_size' => $limit,
			'headers'             => array( 'Accept' => 'application/json, text/plain' ),
		)
	);

	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return null;
	}

	$body = trim( wp_remote_retrieve_body( $response ) );
	if ( '' !== $body && ( ctype_digit( $body ) || ( 64 === strlen( $body ) && ctype_xdigit( $body ) ) ) ) {
		return $body;
	}

	$data = json_decode( $body, true );
	return is_array( $data ) ? $data : null;
}

/**
 * Tells whether the Elektron Live Blocks plugin is active and provides the data.
 *
 * @return bool
 */
function elektron_plugin_active() {
	return function_exists( 'elnb_get_height' ) && function_exists( 'elnb_api_base' );
}

/**
 * Returns the explorer API base URL, or an empty string. The plugin setting wins when the plugin is active.
 *
 * @return string
 */
function elektron_explorer_api() {
	if ( elektron_plugin_active() ) {
		return elnb_api_base();
	}
	if ( ! get_theme_mod( 'elektron_live_enabled', false ) ) {
		return '';
	}
	return untrailingslashit( esc_url_raw( (string) get_theme_mod( 'elektron_live_api', '' ), array( 'https' ) ) );
}

/**
 * Returns live chain figures from the configured block explorer API, or an empty array.
 *
 * Only numbers and one hash are kept. The result is cached for one minute.
 *
 * @return array
 */
function elektron_get_live_stats() {
	if ( elektron_plugin_active() ) {
		return elektron_get_plugin_stats();
	}

	$base = elektron_explorer_api();
	if ( '' === $base ) {
		return array();
	}

	$cache_key = 'elektron_live_' . md5( $base );
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return is_array( $cached ) ? $cached : array();
	}

	$stats  = array();
	$height = elektron_fetch_explorer( $base, '/blocks/tip/height' );

	if ( is_string( $height ) && ctype_digit( $height ) ) {
		$hash       = elektron_fetch_explorer( $base, '/blocks/tip/hash' );
		$difficulty = elektron_fetch_explorer( $base, '/difficulty-adjustment' );
		$fees       = elektron_fetch_explorer( $base, '/fees/recommended' );
		$mempool    = elektron_fetch_explorer( $base, '/mempool' );
		$hashrate   = elektron_fetch_explorer( $base, '/mining/hashrate/3d', 65536 );

		$eta = elektron_array_number( $difficulty, 'estimatedRetargetDate' );

		$stats = array(
			'height'       => absint( $height ),
			'hash'         => ( is_string( $hash ) && 64 === strlen( $hash ) && ctype_xdigit( $hash ) ) ? strtolower( $hash ) : '',
			'progress'     => elektron_array_number( $difficulty, 'progressPercent' ),
			'change'       => elektron_array_number( $difficulty, 'difficultyChange' ),
			'remaining'    => elektron_array_number( $difficulty, 'remainingBlocks' ),
			'next_height'  => elektron_array_number( $difficulty, 'nextRetargetHeight' ),
			'eta'          => null === $eta ? null : (int) round( $eta / 1000 ),
			'fee_fastest'  => elektron_array_number( $fees, 'fastestFee' ),
			'fee_half'     => elektron_array_number( $fees, 'halfHourFee' ),
			'fee_hour'     => elektron_array_number( $fees, 'hourFee' ),
			'fee_economy'  => elektron_array_number( $fees, 'economyFee' ),
			'mempool_txs'  => elektron_array_number( $mempool, 'count' ),
			'mempool_size' => elektron_array_number( $mempool, 'vsize' ),
			'hashrate'     => elektron_array_number( $hashrate, 'currentHashrate' ),
			'difficulty'   => elektron_array_number( $hashrate, 'currentDifficulty' ),
			'time'         => time(),
		);
	}

	set_transient( $cache_key, $stats, MINUTE_IN_SECONDS );

	return $stats;
}

/**
 * Formats a hash rate in hashes per second.
 *
 * @param float $value Hashes per second.
 * @return string
 */
function elektron_format_hashrate( $value ) {
	$units = array( 'H/s', 'kH/s', 'MH/s', 'GH/s', 'TH/s', 'PH/s', 'EH/s', 'ZH/s' );
	$i     = 0;
	$last  = count( $units ) - 1;
	while ( $value >= 1000 && $i < $last ) {
		$value /= 1000;
		++$i;
	}
	return number_format_i18n( $value, $i ? 2 : 0 ) . ' ' . $units[ $i ];
}

/**
 * Formats a large plain number with a unit suffix.
 *
 * @param float $value Number.
 * @return string
 */
function elektron_format_compact( $value ) {
	$units = array( '', 'k', 'M', 'G', 'T', 'P', 'E' );
	$i     = 0;
	$last  = count( $units ) - 1;
	while ( $value >= 1000 && $i < $last ) {
		$value /= 1000;
		++$i;
	}
	return number_format_i18n( $value, $i ? 2 : 0 ) . ( $units[ $i ] ? ' ' . $units[ $i ] : '' );
}

/**
 * Builds the live figures from the Elektron Live Blocks plugin, so both share one source and one cache.
 *
 * @return array Same shape as elektron_get_live_stats(), or an empty array.
 */
function elektron_get_plugin_stats() {
	$height = elnb_get_height();
	if ( ! $height ) {
		return array();
	}

	$hash       = elnb_get_hash();
	$retarget   = elnb_get_retarget();
	$fees       = elnb_get_fees();
	$mempool    = elnb_get_mempool();
	$hashrate   = elnb_get_hashrate();
	$retarget_d = $retarget ? $retarget['data'] : array();

	return array(
		'height'       => (int) $height['data'],
		'hash'         => $hash ? $hash['data'] : '',
		'progress'     => isset( $retarget_d['progress'] ) ? $retarget_d['progress'] : null,
		'change'       => isset( $retarget_d['change'] ) ? $retarget_d['change'] : null,
		'remaining'    => isset( $retarget_d['remaining'] ) ? $retarget_d['remaining'] : null,
		'next_height'  => isset( $retarget_d['next_height'] ) ? $retarget_d['next_height'] : null,
		'eta'          => isset( $retarget_d['eta'] ) ? $retarget_d['eta'] : null,
		'fee_fastest'  => $fees ? $fees['data']['fastest'] : null,
		'fee_half'     => $fees ? $fees['data']['half'] : null,
		'fee_hour'     => $fees ? $fees['data']['hour'] : null,
		'fee_economy'  => $fees ? $fees['data']['economy'] : null,
		'mempool_txs'  => $mempool ? $mempool['data']['count'] : null,
		'mempool_size' => $mempool ? $mempool['data']['vsize'] : null,
		'hashrate'     => $hashrate ? $hashrate['data']['hashrate'] : null,
		'difficulty'   => $hashrate ? $hashrate['data']['difficulty'] : null,
		'time'         => $height['time'],
	);
}
