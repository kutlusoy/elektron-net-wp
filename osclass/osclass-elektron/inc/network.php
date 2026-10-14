<?php
/*
 * Live network figures for the status bar above the header.
 * Data comes from a mempool.space compatible explorer API (elektron-net-mempool).
 * The sources are tried in order, the first one that answers wins.
 */

if(!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

define('ELEKTRON_NETWORK_DEFAULT_SOURCE', 'https://mempool.elektron-net.org/api/v1');
define('ELEKTRON_NETWORK_MAX_SOURCES', 8);

function elektron_network_default_sources() {
  return array(ELEKTRON_NETWORK_DEFAULT_SOURCE);
}

/* Turns text with one URL per line into a clean list of https base URLs */
function elektron_network_parse_sources($text) {
  $sources = array();
  foreach(preg_split('/\R/', (string)$text) as $line) {
    $url = rtrim(trim($line), '/');
    if($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) {
      continue;
    }
    $parts = parse_url($url);
    if(!isset($parts['scheme'], $parts['host']) || strtolower($parts['scheme']) !== 'https' || isset($parts['user']) || isset($parts['query']) || isset($parts['fragment'])) {
      continue;
    }
    $sources[$url] = $url;
    if(count($sources) >= ELEKTRON_NETWORK_MAX_SOURCES) {
      break;
    }
  }
  return array_values($sources);
}

function elektron_network_sources() {
  $sources = elektron_network_parse_sources(osc_get_preference('network_sources', 'osclass-elektron'));
  return empty($sources) ? elektron_network_default_sources() : $sources;
}

function elektron_network_sources_text() {
  $text = trim((string)osc_get_preference('network_sources', 'osclass-elektron'));
  return $text !== '' ? $text : implode("\n", elektron_network_default_sources());
}

function elektron_network_enabled() {
  return osc_get_preference('network_show', 'osclass-elektron') !== '0';
}

function elektron_network_ttl() {
  $ttl = (int)osc_get_preference('network_ttl', 'osclass-elektron');
  return min(600, max(30, $ttl > 0 ? $ttl : 60));
}

/* True when the host resolves only to public addresses. The filter elektron_network_allow_host lets a site allow an explorer on a private network. */
function elektron_network_host_is_public($host) {
  if(filter_var($host, FILTER_VALIDATE_IP)) {
    $ips = array($host);
  } else {
    $ips = gethostbynamel($host);
  }
  if(empty($ips)) {
    return false;
  }
  foreach($ips as $ip) {
    if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
      return false;
    }
  }
  return true;
}

/* GET with a size and time limit. Returns the body or null. */
function elektron_network_get($url, $limit = 8192) {
  $body = '';

  if(function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FOLLOWLOCATION => false,
      CURLOPT_CONNECTTIMEOUT => 3,
      CURLOPT_TIMEOUT        => 4,
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_SSL_VERIFYHOST => 2,
      CURLOPT_HTTPHEADER     => array('Accept: application/json, text/plain'),
      CURLOPT_USERAGENT      => 'Osclass Elektron theme',
      CURLOPT_WRITEFUNCTION  => function($handle, $chunk) use (&$body, $limit) {
        $body .= $chunk;
        return strlen($body) > $limit ? 0 : strlen($chunk);
      },
    ));
    $ok = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    return ($ok !== false && $code === 200) ? trim($body) : null;
  }

  $context = stream_context_create(array('http' => array('timeout' => 4, 'follow_location' => 0, 'ignore_errors' => true, 'header' => "Accept: application/json, text/plain\r\n")));
  $body = @file_get_contents($url, false, $context, 0, $limit + 1);
  if($body === false || strlen($body) > $limit || empty($http_response_header[0]) || strpos($http_response_header[0], ' 200') === false) {
    return null;
  }
  return trim($body);
}

/* One endpoint: digits or hex hash as string, JSON as array, null on failure */
function elektron_network_fetch($base, $path, $limit = 8192) {
  $body = elektron_network_get($base . $path, $limit);
  if($body === null || $body === '') {
    return null;
  }
  if(ctype_digit($body) || (strlen($body) === 64 && ctype_xdigit($body))) {
    return $body;
  }
  $data = json_decode($body, true);
  return is_array($data) ? $data : null;
}

function elektron_network_number($data, $key) {
  if(is_array($data) && isset($data[$key]) && is_numeric($data[$key]) && is_finite((float)$data[$key])) {
    return (float)$data[$key];
  }
  return null;
}

/* Reads the figures from one source, null when it does not answer with a block height */
function elektron_network_read_source($base) {
  $height = elektron_network_fetch($base, '/blocks/tip/height');
  if(!is_string($height) || !ctype_digit($height)) {
    return null;
  }

  $difficulty = elektron_network_fetch($base, '/difficulty-adjustment');
  $fees       = elektron_network_fetch($base, '/fees/recommended');
  $mempool    = elektron_network_fetch($base, '/mempool');
  $hashrate   = elektron_network_fetch($base, '/mining/hashrate/3d', 65536);

  return array(
    'height'      => (int)$height,
    'progress'    => elektron_network_number($difficulty, 'progressPercent'),
    'fee_fastest' => elektron_network_number($fees, 'fastestFee'),
    'mempool_txs' => elektron_network_number($mempool, 'count'),
    'hashrate'    => elektron_network_number($hashrate, 'currentHashrate'),
  );
}

/* Tries every source in order. Returns stats plus the source that answered, or an empty array. */
function elektron_network_refresh() {
  foreach(elektron_network_sources() as $base) {
    $host = parse_url($base, PHP_URL_HOST);
    if(!$host || !osc_apply_filter('elektron_network_allow_host', elektron_network_host_is_public($host), $host)) {
      continue;
    }
    $stats = elektron_network_read_source($base);
    if($stats !== null) {
      $stats['source'] = $base;
      $stats['time'] = time();
      return $stats;
    }
  }
  return array();
}

function elektron_network_cache_file() {
  return osc_uploads_path() . 'elektron-network-' . md5(osc_base_url() . implode('|', elektron_network_sources())) . '.json';
}

/*
 * Cached figures. One visitor refreshes an expired cache while the others keep getting
 * the previous values, so a slow explorer never blocks several page loads at once.
 * Values older than one hour are dropped, no stale numbers are shown as live.
 */
function elektron_network_stats() {
  if(!elektron_network_enabled()) {
    return array();
  }

  $file = elektron_network_cache_file();
  $cached = null;
  if(is_file($file)) {
    $data = json_decode((string)@file_get_contents($file), true);
    if(is_array($data) && isset($data['stats'], $data['expires'])) {
      $cached = $data;
    }
  }

  if($cached !== null && $cached['expires'] > time()) {
    return $cached['stats'];
  }

  $stale = ($cached !== null && !empty($cached['stats']) && isset($cached['stats']['time']) && $cached['stats']['time'] > time() - 3600) ? $cached['stats'] : array();

  $lock = @fopen($file . '.lock', 'c');
  if($lock === false || !flock($lock, LOCK_EX | LOCK_NB)) {
    if($lock !== false) {
      fclose($lock);
    }
    return $stale;
  }

  $stats = elektron_network_refresh();
  if(empty($stats)) {
    $stats = $stale;
    $ttl = 30;
  } else {
    $ttl = elektron_network_ttl();
  }
  @file_put_contents($file, json_encode(array('expires' => time() + $ttl, 'stats' => $stats)), LOCK_EX);

  flock($lock, LOCK_UN);
  fclose($lock);

  return $stats;
}

function elektron_network_format_number($value, $decimals = 0) {
  $dec = function_exists('osc_locale_dec_point') && osc_locale_dec_point() !== '' ? osc_locale_dec_point() : '.';
  $thousands = function_exists('osc_locale_thousands_sep') && osc_locale_thousands_sep() !== '' ? osc_locale_thousands_sep() : ',';
  return number_format((float)$value, $decimals, $dec, $thousands);
}

function elektron_network_format_hashrate($value) {
  $units = array('H/s', 'kH/s', 'MH/s', 'GH/s', 'TH/s', 'PH/s', 'EH/s', 'ZH/s');
  $i = 0;
  while($value >= 1000 && $i < count($units) - 1) {
    $value /= 1000;
    $i++;
  }
  return elektron_network_format_number($value, $i ? 2 : 0) . ' ' . $units[$i];
}

/* Items for the bar as label/value pairs */
function elektron_network_items() {
  $stats = elektron_network_stats();
  if(empty($stats)) {
    return array();
  }

  $items = array(array(__('Block', 'osclass-elektron'), elektron_network_format_number($stats['height'])));
  if($stats['hashrate'] !== null) {
    $items[] = array(__('Hash', 'osclass-elektron'), elektron_network_format_hashrate($stats['hashrate']));
  }
  if($stats['fee_fastest'] !== null) {
    $items[] = array(__('Fee', 'osclass-elektron'), sprintf(__('%s lep/vB', 'osclass-elektron'), elektron_network_format_number($stats['fee_fastest'])));
  }
  if($stats['mempool_txs'] !== null) {
    $items[] = array(__('Mempool', 'osclass-elektron'), sprintf(__('%s tx', 'osclass-elektron'), elektron_network_format_number($stats['mempool_txs'])));
  }
  if($stats['progress'] !== null) {
    $items[] = array(__('Retarget', 'osclass-elektron'), elektron_network_format_number($stats['progress']) . '%');
  }

  return $items;
}

/* Explorer website derived from the API base of the source that answered */
function elektron_network_site_url() {
  $stats = elektron_network_stats();
  if(!empty($stats['source']) && preg_match('#^(https://[^/]+)(/.*)?/api(/v1)?$#', $stats['source'], $m)) {
    return $m[1] . $m[2];
  }
  return '';
}
