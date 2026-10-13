<?php
/**
 * Block and shortcode output.
 *
 * @package ElektronLiveBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const ELNB_HALVING_INTERVAL = 2102400;
const ELNB_BLOCK_SECONDS    = 60;
const ELNB_GENESIS_REWARD   = 5;

/**
 * Formats a number with a decimal unit prefix.
 *
 * @param float  $value Number.
 * @param string $unit  Unit without prefix.
 * @return string
 */
function elnb_format_unit( $value, $unit ) {
	$prefixes = array( '', 'k', 'M', 'G', 'T', 'P', 'E', 'Z' );
	$i        = 0;
	while ( $value >= 1000 && $i < count( $prefixes ) - 1 ) {
		$value /= 1000;
		++$i;
	}
	return trim( number_format_i18n( $value, $i ? 2 : 0 ) . ' ' . $prefixes[ $i ] . $unit );
}

/**
 * Builds one stat tile.
 *
 * @param string $value Value.
 * @param string $label Label.
 * @return string
 */
function elnb_tile( $value, $label ) {
	return '<div class="elnb-tile"><p class="elnb-value">' . esc_html( $value ) . '</p><p class="elnb-label">' . esc_html( $label ) . '</p></div>';
}

/**
 * Wraps the inner markup of a block.
 *
 * @param string $slug     Block slug.
 * @param array  $attrs    Block attributes.
 * @param string $label    Default heading.
 * @param string $inner    Escaped inner markup. Empty when no data is available.
 * @param int    $time     Time of the data.
 * @param bool   $is_block Whether this renders inside a block.
 * @return string
 */
function elnb_wrap( $slug, $attrs, $label, $inner, $time, $is_block ) {
	if ( $is_block && function_exists( 'get_block_wrapper_attributes' ) ) {
		$wrapper = get_block_wrapper_attributes( array( 'class' => 'elnb-block elnb-' . $slug ) );
	} else {
		$wrapper = 'class="elnb-block elnb-' . esc_attr( $slug ) . '"';
	}

	$heading = isset( $attrs['heading'] ) && '' !== trim( (string) $attrs['heading'] ) ? (string) $attrs['heading'] : $label;
	$out     = '<div ' . $wrapper . '>';
	if ( ! isset( $attrs['showHeading'] ) || $attrs['showHeading'] ) {
		$out .= '<h3 class="elnb-heading">' . esc_html( $heading ) . '</h3>';
	}

	if ( '' === $inner ) {
		$out .= '<p class="elnb-notice">' . esc_html__( 'Live network data is currently unavailable.', 'elektron-live-blocks' ) . '</p>';
		if ( current_user_can( 'manage_options' ) ) {
			$out .= '<p class="elnb-notice elnb-admin">' . sprintf(
				/* translators: %s: link to the settings page */
				wp_kses( __( 'Check the explorer address under <a href="%s">Settings, Elektron Live Blocks</a>. Only administrators see this hint.', 'elektron-live-blocks' ), array( 'a' => array( 'href' => array() ) ) ),
				esc_url( admin_url( 'options-general.php?page=elektron-live-blocks' ) )
			) . '</p>';
		}
	} else {
		$out .= $inner;
		if ( ! empty( $attrs['showUpdated'] ) && $time ) {
			/* translators: %s: time span such as "1 minute" */
			$out .= '<p class="elnb-updated">' . esc_html( sprintf( __( 'Updated %s ago', 'elektron-live-blocks' ), human_time_diff( $time ) ) ) . '</p>';
		}
	}

	return $out . '</div>';
}

/**
 * Renders a block or shortcode by slug.
 *
 * @param string $slug     Block slug without namespace.
 * @param array  $attrs    Attributes.
 * @param bool   $is_block Whether this renders inside a block.
 * @return string
 */
function elnb_render( $slug, $attrs = array(), $is_block = true ) {
	$inner = '';
	$time  = 0;
	$label = '';

	if ( ! $is_block ) {
		wp_enqueue_style( 'elnb-blocks' );
	}

	switch ( $slug ) {
		case 'nodes':
			$label = __( 'Network nodes', 'elektron-live-blocks' );
			$r     = elnb_get_nodes();
			if ( $r ) {
				$time  = $r['time'];
				$d     = $r['data'];
				$inner = '<div class="elnb-grid">' . elnb_tile( number_format_i18n( $d['online'] ), __( 'Nodes online', 'elektron-live-blocks' ) );
				if ( $d['known'] > 0 ) {
					$inner .= elnb_tile( number_format_i18n( $d['known'] ), __( 'Nodes seen in total', 'elektron-live-blocks' ) );
				}
				$inner .= '</div>';

				if ( $d['networks'] ) {
					$names  = array(
						'ipv4'  => 'IPv4',
						'ipv6'  => 'IPv6',
						'tor'   => 'Tor',
						'i2p'   => 'I2P',
						'cjdns' => 'CJDNS',
					);
					$inner .= '<ul class="elnb-meta elnb-spaced">';
					foreach ( $d['networks'] as $net => $count ) {
						$inner .= '<li>' . esc_html( $names[ $net ] . ': ' . number_format_i18n( $count ) ) . '</li>';
					}
					$inner .= '</ul>';
				}

				if ( $d['countries'] ) {
					$inner .= '<div class="elnb-table-wrap elnb-spaced"><table class="elnb-table"><thead><tr><th scope="col">' . esc_html__( 'Country', 'elektron-live-blocks' ) . '</th><th scope="col">' . esc_html__( 'Nodes', 'elektron-live-blocks' ) . '</th></tr></thead><tbody>';
					foreach ( array_slice( $d['countries'], 0, isset( $attrs['rows'] ) ? min( 10, max( 1, (int) $attrs['rows'] ) ) : 5 ) as $country ) {
						$inner .= '<tr><td>' . esc_html( $country['name'] ) . '</td><td>' . esc_html( number_format_i18n( $country['count'] ) ) . '</td></tr>';
					}
					$inner .= '</tbody></table></div>';
				}
			}
			break;

		case 'nodemap':
			$label  = __( 'Node map', 'elektron-live-blocks' );
			$height = min( 900, max( 300, isset( $attrs['height'] ) ? (int) $attrs['height'] : 520 ) );
			$url    = elnb_nodemap_url();
			$inner  = '<iframe class="elnb-frame" src="' . esc_url( $url ) . '" title="' . esc_attr__( 'Elektron Net node map', 'elektron-live-blocks' ) . '" loading="lazy" referrerpolicy="no-referrer" sandbox="allow-scripts allow-same-origin" style="height:' . (int) $height . 'px"></iframe>'
				. '<p class="elnb-updated"><a href="' . esc_url( $url ) . '" rel="noopener">' . esc_html__( 'Open the node map in a new view', 'elektron-live-blocks' ) . '</a></p>';
			$time   = 0;
			break;

		case 'height':
			$label = __( 'Block height', 'elektron-live-blocks' );
			$r     = elnb_get_height();
			if ( $r ) {
				$time  = $r['time'];
				$inner = '<div class="elnb-grid">' . elnb_tile( number_format_i18n( $r['data'] ), __( 'Latest block', 'elektron-live-blocks' ) ) . '</div>';
			}
			break;

		case 'tip-hash':
			$label = __( 'Latest block hash', 'elektron-live-blocks' );
			$r     = elnb_get_hash();
			if ( $r ) {
				$time  = $r['time'];
				$site  = elnb_site_url();
				$hash  = esc_html( $r['data'] );
				$inner = '<code class="elnb-hash">' . ( $site ? '<a href="' . esc_url( $site . '/block/' . $r['data'] ) . '" rel="noopener">' . $hash . '</a>' : $hash ) . '</code>';
			}
			break;

		case 'hashrate':
			$label = __( 'Hash rate and difficulty', 'elektron-live-blocks' );
			$r     = elnb_get_hashrate();
			if ( $r ) {
				$time  = $r['time'];
				$inner = '<div class="elnb-grid">' . elnb_tile( elnb_format_unit( $r['data']['hashrate'], 'H/s' ), __( 'Network hash rate', 'elektron-live-blocks' ) );
				if ( null !== $r['data']['difficulty'] ) {
					$inner .= elnb_tile( elnb_format_unit( $r['data']['difficulty'], '' ), __( 'Difficulty', 'elektron-live-blocks' ) );
				}
				$inner .= '</div>';
			}
			break;

		case 'retarget':
			$label = __( 'Difficulty adjustment', 'elektron-live-blocks' );
			$r     = elnb_get_retarget();
			if ( $r ) {
				$time  = $r['time'];
				$d     = $r['data'];
				$inner = '<p class="elnb-label">' . esc_html(
					/* translators: %s: percentage number */
					sprintf( __( '%s%% of the current period', 'elektron-live-blocks' ), number_format_i18n( $d['progress'], 1 ) )
				) . '</p>';
				$inner .= '<progress class="elnb-progress" max="100" value="' . esc_attr( (string) round( $d['progress'], 1 ) ) . '"></progress><ul class="elnb-meta">';
				if ( null !== $d['remaining'] ) {
					/* translators: %s: block count */
					$inner .= '<li>' . esc_html( sprintf( __( '%s blocks remaining', 'elektron-live-blocks' ), number_format_i18n( $d['remaining'], 0 ) ) ) . '</li>';
				}
				if ( null !== $d['next_height'] ) {
					/* translators: %s: block height */
					$inner .= '<li>' . esc_html( sprintf( __( 'Next adjustment at block %s', 'elektron-live-blocks' ), number_format_i18n( $d['next_height'], 0 ) ) ) . '</li>';
				}
				if ( null !== $d['eta'] ) {
					/* translators: %s: date and time */
					$inner .= '<li>' . esc_html( sprintf( __( 'Estimated: %s', 'elektron-live-blocks' ), wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $d['eta'] ) ) ) . '</li>';
				}
				if ( null !== $d['change'] ) {
					/* translators: %s: percentage number */
					$inner .= '<li>' . esc_html( sprintf( __( 'Expected change: %s%%', 'elektron-live-blocks' ), number_format_i18n( $d['change'], 2 ) ) ) . '</li>';
				}
				$inner .= '</ul>';
			}
			break;

		case 'mempool':
			$label = __( 'Mempool', 'elektron-live-blocks' );
			$r     = elnb_get_mempool();
			if ( $r ) {
				$time  = $r['time'];
				$inner = '<div class="elnb-grid">' . elnb_tile( number_format_i18n( $r['data']['count'], 0 ), __( 'Unconfirmed transactions', 'elektron-live-blocks' ) );
				if ( null !== $r['data']['vsize'] ) {
					$inner .= elnb_tile( elnb_format_unit( $r['data']['vsize'], 'vB' ), __( 'Total size', 'elektron-live-blocks' ) );
				}
				$inner .= '</div>';
			}
			break;

		case 'fees':
			$label = __( 'Recommended fees', 'elektron-live-blocks' );
			$r     = elnb_get_fees();
			if ( $r ) {
				$time  = $r['time'];
				$rows  = array(
					'fastest' => __( 'Fastest', 'elektron-live-blocks' ),
					'half'    => __( 'Within 30 minutes', 'elektron-live-blocks' ),
					'hour'    => __( 'Within 1 hour', 'elektron-live-blocks' ),
					'economy' => __( 'Economy', 'elektron-live-blocks' ),
				);
				$inner = '<div class="elnb-grid">';
				foreach ( $rows as $key => $row_label ) {
					if ( null !== $r['data'][ $key ] ) {
						/* translators: %s: fee rate number */
						$inner .= elnb_tile( sprintf( __( '%s lep/vB', 'elektron-live-blocks' ), number_format_i18n( $r['data'][ $key ], 0 ) ), $row_label );
					}
				}
				$inner .= '</div>';
			}
			break;

		case 'halving':
			$label = __( 'Halving countdown', 'elektron-live-blocks' );
			$r     = elnb_get_height();
			if ( $r ) {
				$time      = $r['time'];
				$height    = $r['data'];
				$epoch     = intdiv( $height, ELNB_HALVING_INTERVAL );
				$next      = ( $epoch + 1 ) * ELNB_HALVING_INTERVAL;
				$remaining = $next - $height;
				$reward    = ELNB_GENESIS_REWARD / ( 2 ** $epoch );
				$eta       = time() + $remaining * ELNB_BLOCK_SECONDS;
				$inner     = '<div class="elnb-grid">'
					. elnb_tile( number_format_i18n( $next ), __( 'Next halving at block', 'elektron-live-blocks' ) )
					. elnb_tile( number_format_i18n( $remaining ), __( 'Blocks remaining', 'elektron-live-blocks' ) )
					. elnb_tile( wp_date( get_option( 'date_format' ), $eta ), __( 'Estimated at the 60 second target', 'elektron-live-blocks' ) )
					. elnb_tile(
						/* translators: 1: current reward, 2: reward after the halving */
						sprintf( __( '%1$s Elek, then %2$s', 'elektron-live-blocks' ), rtrim( rtrim( number_format( $reward, 8, '.', '' ), '0' ), '.' ), rtrim( rtrim( number_format( $reward / 2, 8, '.', '' ), '0' ), '.' ) ),
						__( 'Block reward', 'elektron-live-blocks' )
					)
					. '</div>';
			}
			break;

		case 'latest-blocks':
			$label = __( 'Latest blocks', 'elektron-live-blocks' );
			$r     = elnb_get_blocks();
			if ( $r ) {
				$time  = $r['time'];
				$rows  = min( 15, max( 1, isset( $attrs['rows'] ) ? (int) $attrs['rows'] : 6 ) );
				$site  = elnb_site_url();
				$inner = '<div class="elnb-table-wrap"><table class="elnb-table"><thead><tr><th scope="col">' . esc_html__( 'Height', 'elektron-live-blocks' ) . '</th><th scope="col">' . esc_html__( 'Age', 'elektron-live-blocks' ) . '</th><th scope="col">' . esc_html__( 'Transactions', 'elektron-live-blocks' ) . '</th><th scope="col">' . esc_html__( 'Size', 'elektron-live-blocks' ) . '</th><th scope="col">' . esc_html__( 'Miner', 'elektron-live-blocks' ) . '</th></tr></thead><tbody>';
				foreach ( array_slice( $r['data'], 0, $rows ) as $block ) {
					$height = esc_html( number_format_i18n( $block['height'] ) );
					if ( $site && $block['id'] ) {
						$height = '<a href="' . esc_url( $site . '/block/' . $block['id'] ) . '" rel="noopener">' . $height . '</a>';
					}
					$age    = $block['timestamp'] ? sprintf(
						/* translators: %s: time span such as "1 minute" */
						__( '%s ago', 'elektron-live-blocks' ),
						human_time_diff( $block['timestamp'] )
					) : '';
					$inner .= '<tr><td>' . $height . '</td><td>' . esc_html( $age ) . '</td><td>' . esc_html( number_format_i18n( $block['txs'] ) ) . '</td><td>' . esc_html( elnb_format_unit( $block['size'], 'B' ) ) . '</td><td>' . esc_html( $block['pool'] ) . '</td></tr>';
				}
				$inner .= '</tbody></table></div>';
			}
			break;

		case 'overview':
			$label  = __( 'Network overview', 'elektron-live-blocks' );
			$height = elnb_get_height();
			if ( $height ) {
				$time  = $height['time'];
				$inner = '<div class="elnb-grid">' . elnb_tile( number_format_i18n( $height['data'] ), __( 'Block height', 'elektron-live-blocks' ) );
				$hr    = elnb_get_hashrate();
				if ( $hr ) {
					$inner .= elnb_tile( elnb_format_unit( $hr['data']['hashrate'], 'H/s' ), __( 'Hash rate', 'elektron-live-blocks' ) );
				}
				$mp = elnb_get_mempool();
				if ( $mp ) {
					$inner .= elnb_tile( number_format_i18n( $mp['data']['count'], 0 ), __( 'Mempool transactions', 'elektron-live-blocks' ) );
				}
				$fees = elnb_get_fees();
				if ( $fees ) {
					/* translators: %s: fee rate number */
					$inner .= elnb_tile( sprintf( __( '%s lep/vB', 'elektron-live-blocks' ), number_format_i18n( $fees['data']['fastest'], 0 ) ), __( 'Fastest fee', 'elektron-live-blocks' ) );
				}
				$rt = elnb_get_retarget();
				if ( $rt ) {
					/* translators: %s: percentage number */
					$inner .= elnb_tile( sprintf( __( '%s%%', 'elektron-live-blocks' ), number_format_i18n( $rt['data']['progress'], 0 ) ), __( 'Difficulty adjustment progress', 'elektron-live-blocks' ) );
				}
				$inner .= '</div>';
			}
			break;

		default:
			return '';
	}

	return elnb_wrap( $slug, $attrs, $label, $inner, $time, $is_block );
}

/**
 * Shortcode: [elektron_live type="height"].
 *
 * @param array $atts Attributes.
 * @return string
 */
function elnb_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'type'        => 'overview',
			'heading'     => '',
			'rows'        => 6,
			'height'      => 520,
			'showheading' => 'true',
			'updated'     => 'false',
		),
		$atts,
		'elektron_live'
	);

	return elnb_render(
		sanitize_key( $atts['type'] ),
		array(
			'heading'     => sanitize_text_field( $atts['heading'] ),
			'rows'        => absint( $atts['rows'] ),
			'height'      => absint( $atts['height'] ),
			'showHeading' => filter_var( $atts['showheading'], FILTER_VALIDATE_BOOLEAN ),
			'showUpdated' => filter_var( $atts['updated'], FILTER_VALIDATE_BOOLEAN ),
		),
		false
	);
}
add_shortcode( 'elektron_live', 'elnb_shortcode' );
