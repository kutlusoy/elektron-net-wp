<?php
/**
 * Explorer API access with caching.
 *
 * @package ElektronLiveBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the explorer API base URL without trailing slash. HTTPS only.
 *
 * @return string
 */
function elnb_api_base() {
	$url = esc_url_raw( (string) get_option( 'elnb_api_base', ELNB_DEFAULT_API ), array( 'https' ) );
	return untrailingslashit( $url ? $url : ELNB_DEFAULT_API );
}

/**
 * Returns the explorer website URL derived from the API base, or an empty string.
 *
 * @return string
 */
function elnb_site_url() {
	if ( preg_match( '#^(https://[^/]+)(/.*)?/api(/v1)?$#', elnb_api_base(), $m ) ) {
		return $m[1] . $m[2];
	}
	return '';
}

/**
 * Returns the cache lifetime in seconds.
 *
 * @return int
 */
function elnb_ttl() {
	return min( 600, max( 30, absint( get_option( 'elnb_cache_ttl', 60 ) ) ) );
}

/**
 * Requests one endpoint, with a transient cache. Failures are cached for 30 seconds.
 *
 * @param string $path  Endpoint path starting with a slash.
 * @param int    $limit Maximum response size in bytes.
 * @param string $base  API base URL. Defaults to the explorer API.
 * @return array{data:mixed,time:int}|null Data is an int, a hash string or a decoded array.
 */
function elnb_request( $path, $limit = 16384, $base = null ) {
	$base = null === $base ? elnb_api_base() : $base;
	$key  = 'elnb_' . md5( $base . $path );

	$cached = get_transient( $key );
	if ( false !== $cached ) {
		return is_array( $cached ) ? $cached : null;
	}

	$response = wp_safe_remote_get(
		$base . $path,
		array(
			'timeout'             => 5,
			'redirection'         => 0,
			'limit_response_size' => $limit,
			'headers'             => array( 'Accept' => 'application/json, text/plain' ),
		)
	);

	$result = null;
	if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
		$body = trim( wp_remote_retrieve_body( $response ) );
		if ( '' !== $body && ctype_digit( $body ) ) {
			$result = array(
				'data' => (int) $body,
				'time' => time(),
			);
		} elseif ( 64 === strlen( $body ) && ctype_xdigit( $body ) ) {
			$result = array(
				'data' => strtolower( $body ),
				'time' => time(),
			);
		} else {
			$decoded = json_decode( $body, true );
			if ( is_array( $decoded ) ) {
				$result = array(
					'data' => $decoded,
					'time' => time(),
				);
			}
		}
	}

	set_transient( $key, null === $result ? 0 : $result, null === $result ? 30 : elnb_ttl() );

	return $result;
}

/**
 * Reads a finite number from an array.
 *
 * @param mixed  $data Source.
 * @param string $key  Key.
 * @return float|null
 */
function elnb_number( $data, $key ) {
	if ( is_array( $data ) && isset( $data[ $key ] ) && is_numeric( $data[ $key ] ) && is_finite( (float) $data[ $key ] ) ) {
		return (float) $data[ $key ];
	}
	return null;
}

/**
 * Gets the latest block height.
 *
 * @return array|null
 */
function elnb_get_height() {
	$r = elnb_request( '/blocks/tip/height' );
	return ( $r && is_int( $r['data'] ) ) ? $r : null;
}

/**
 * Gets the latest block hash.
 *
 * @return array|null
 */
function elnb_get_hash() {
	$r = elnb_request( '/blocks/tip/hash' );
	return ( $r && is_string( $r['data'] ) ) ? $r : null;
}

/**
 * Gets the difficulty adjustment figures.
 *
 * @return array|null
 */
function elnb_get_retarget() {
	$r = elnb_request( '/difficulty-adjustment' );
	if ( ! $r || ! is_array( $r['data'] ) || null === elnb_number( $r['data'], 'progressPercent' ) ) {
		return null;
	}
	$d   = $r['data'];
	$eta = elnb_number( $d, 'estimatedRetargetDate' );
	return array(
		'time' => $r['time'],
		'data' => array(
			'progress'    => min( 100, max( 0, elnb_number( $d, 'progressPercent' ) ) ),
			'change'      => elnb_number( $d, 'difficultyChange' ),
			'remaining'   => elnb_number( $d, 'remainingBlocks' ),
			'next_height' => elnb_number( $d, 'nextRetargetHeight' ),
			'eta'         => null === $eta ? null : (int) round( $eta / 1000 ),
		),
	);
}

/**
 * Gets the recommended fees.
 *
 * @return array|null
 */
function elnb_get_fees() {
	$r = elnb_request( '/fees/recommended' );
	if ( ! $r || ! is_array( $r['data'] ) || null === elnb_number( $r['data'], 'fastestFee' ) ) {
		return null;
	}
	$d = $r['data'];
	return array(
		'time' => $r['time'],
		'data' => array(
			'fastest' => elnb_number( $d, 'fastestFee' ),
			'half'    => elnb_number( $d, 'halfHourFee' ),
			'hour'    => elnb_number( $d, 'hourFee' ),
			'economy' => elnb_number( $d, 'economyFee' ),
		),
	);
}

/**
 * Gets the mempool summary.
 *
 * @return array|null
 */
function elnb_get_mempool() {
	$r = elnb_request( '/mempool' );
	if ( ! $r || ! is_array( $r['data'] ) || null === elnb_number( $r['data'], 'count' ) ) {
		return null;
	}
	return array(
		'time' => $r['time'],
		'data' => array(
			'count' => elnb_number( $r['data'], 'count' ),
			'vsize' => elnb_number( $r['data'], 'vsize' ),
		),
	);
}

/**
 * Gets the current hash rate and difficulty.
 *
 * @return array|null
 */
function elnb_get_hashrate() {
	$r = elnb_request( '/mining/hashrate/3d', 65536 );
	if ( ! $r || ! is_array( $r['data'] ) || null === elnb_number( $r['data'], 'currentHashrate' ) ) {
		return null;
	}
	return array(
		'time' => $r['time'],
		'data' => array(
			'hashrate'   => elnb_number( $r['data'], 'currentHashrate' ),
			'difficulty' => elnb_number( $r['data'], 'currentDifficulty' ),
		),
	);
}

/**
 * Gets the most recent blocks.
 *
 * @return array|null
 */
function elnb_get_blocks() {
	$r = elnb_request( '/blocks', 131072 );
	if ( ! $r || ! is_array( $r['data'] ) ) {
		return null;
	}

	$blocks = array();
	foreach ( $r['data'] as $block ) {
		$height = elnb_number( $block, 'height' );
		if ( null === $height ) {
			continue;
		}
		$pool = '';
		if ( isset( $block['extras']['pool']['name'] ) && is_string( $block['extras']['pool']['name'] ) ) {
			$pool = sanitize_text_field( $block['extras']['pool']['name'] );
		}
		$id = ( isset( $block['id'] ) && is_string( $block['id'] ) && 64 === strlen( $block['id'] ) && ctype_xdigit( $block['id'] ) ) ? strtolower( $block['id'] ) : '';

		$blocks[] = array(
			'height'    => (int) $height,
			'id'        => $id,
			'timestamp' => (int) elnb_number( $block, 'timestamp' ),
			'txs'       => (int) elnb_number( $block, 'tx_count' ),
			'size'      => (int) elnb_number( $block, 'size' ),
			'pool'      => $pool,
		);
	}

	return $blocks ? array(
		'time' => $r['time'],
		'data' => $blocks,
	) : null;
}

/**
 * Returns the node map snapshot URL, HTTPS only.
 *
 * @return string
 */
function elnb_nodes_api() {
	$url = esc_url_raw( (string) get_option( 'elnb_nodes_api', ELNB_DEFAULT_NODES_API ), array( 'https' ) );
	return untrailingslashit( $url ? $url : ELNB_DEFAULT_NODES_API );
}

/**
 * Returns the public node map page URL, HTTPS only.
 *
 * @return string
 */
function elnb_nodemap_url() {
	$url = esc_url_raw( (string) get_option( 'elnb_nodemap_url', ELNB_DEFAULT_NODEMAP ), array( 'https' ) );
	return $url ? $url : ELNB_DEFAULT_NODEMAP;
}

/**
 * Gets node counts from the node map snapshot. Addresses of single nodes are never used.
 *
 * @return array|null
 */
function elnb_get_nodes() {
	$r = elnb_request( '', 1048576, elnb_nodes_api() );
	if ( ! $r || ! is_array( $r['data'] ) || null === elnb_number( $r['data'], 'onlineCount' ) ) {
		return null;
	}
	$d = $r['data'];

	$networks = array();
	if ( isset( $d['networkCounts'] ) && is_array( $d['networkCounts'] ) ) {
		foreach ( array( 'ipv4', 'ipv6', 'tor', 'i2p', 'cjdns' ) as $net ) {
			$count = elnb_number( $d['networkCounts'], $net );
			if ( null !== $count && $count > 0 ) {
				$networks[ $net ] = (int) $count;
			}
		}
	}

	$countries = array();
	if ( isset( $d['countries'] ) && is_array( $d['countries'] ) ) {
		foreach ( array_slice( $d['countries'], 0, 10 ) as $c ) {
			$count = elnb_number( $c, 'count' );
			if ( null === $count || ! isset( $c['name'] ) || ! is_string( $c['name'] ) ) {
				continue;
			}
			$countries[] = array(
				'name'  => sanitize_text_field( $c['name'] ),
				'count' => (int) $count,
			);
		}
	}

	return array(
		'time' => $r['time'],
		'data' => array(
			'online'    => (int) $d['onlineCount'],
			'known'     => (int) elnb_number( $d, 'totalKnown' ),
			'networks'  => $networks,
			'countries' => $countries,
		),
	);
}
