=== Elektron Live Blocks ===
Contributors: kutlusoy
Requires at least: 6.1
Tested up to: 7.1
Requires PHP: 7.4
Tags: elektron, blockchain, blocks, mempool, live data
Stable tag: 0.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Blocks and a shortcode that show live Elektron Net data from a block explorer API.

== Description ==

Insert live network data anywhere with the block editor. Every block is rendered on the server, so the page works without JavaScript and visitors never contact the explorer.

Blocks (category "Elektron Live Data"):

* Network Overview: height, hash rate, mempool, fastest fee and adjustment progress
* Block Height
* Latest Block Hash (linked to the explorer)
* Hash Rate and Difficulty
* Difficulty Adjustment: progress, blocks remaining, estimated date
* Mempool
* Recommended Fees
* Halving Countdown: next halving block, blocks remaining, estimated date, block reward
* Latest Blocks: table with up to 15 rows

Shortcode for classic editors and widgets: `[elektron_live type="height"]`. Types: overview, height, tip-hash, hashrate, retarget, mempool, fees, halving, latest-blocks. Options: `heading="..."`, `level="2"`, `rows="6"`, `showheading="false"`, `updated="true"`.

A block pattern "Elektron network dashboard" combines three of the blocks.

The Elektron theme detects this plugin and shows the same data in its front page card and on its Network page, using this plugin's explorer address and cache.

The blocks use the colors of the Elektron theme when it is active and fall back to neutral colors with any other theme.

== Third-party services ==

This plugin requests data from a block explorer API. The default is https://mempool.elektron-net.org/api/v1 and can be changed under Settings > Elektron Live Blocks (HTTPS only).

Your web server requests these endpoints and caches the answers (default 60 seconds): /blocks/tip/height, /blocks/tip/hash, /difficulty-adjustment, /fees/recommended, /mempool, /mining/hashrate/3d and /blocks. No personal data and no visitor data is sent. Only numbers, hashes and miner names from the answers are used, and all output is escaped.

== Installation ==

1. Upload the plugin folder to /wp-content/plugins/ or install the zip under Plugins > Add New.
2. Activate the plugin.
3. Optional: open Settings > Elektron Live Blocks to change the explorer address or cache time.
4. In the block editor, search for "Elektron" and insert the blocks you want.

== Frequently Asked Questions ==

= What is shown when the explorer cannot be reached? =

A short notice. No placeholder numbers are ever displayed. Administrators also see a hint with a link to the settings.

= Are the halving figures live? =

They are calculated from the live block height with the protocol constants (2,102,400 blocks per halving, 5 Elek initial reward, 60 second target). The date is an estimate at the target block time.

== Copyright ==

Elektron Live Blocks, (C) 2026 Ali Kutlusoy <kutlusoy1975@gmail.com>
Distributed under the terms of the GNU GPL v2 or later. No third-party libraries are bundled.

== Changelog ==

= 0.0.1 =
* Initial release.
