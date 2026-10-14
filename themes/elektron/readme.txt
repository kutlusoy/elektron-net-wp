=== Elektron ===
Contributors: kutlusoy
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modern, fast and accessible theme for Elektron Net and open-source projects. Dark by default, with a light mode.

== Description ==

Elektron is a clean, responsive WordPress theme built in the colors of the Elektron Net logo. It ships with all classic template files and works with the block editor.

* Customizable front page hero with a network overview card
* Animated 3D network background built with three.js (optional, respects reduced motion)
* Optional live network data (block height, retarget progress, fastest fee) from a block explorer API you configure
* Page templates: Protocol & Technology, Network and Build & Verify, with links straight into the source code
* Network page with real data only: nothing is shown until the explorer API delivers it
* Dark and light color scheme with a visitor switch
* Responsive card grid for posts, archives and search results
* Primary, footer and social menus
* Sidebar and three footer widget areas
* Custom logo, custom background and featured images
* Threaded comments, pagination and post navigation
* Block editor styles, wide and full alignments
* Translation ready and RTL ready
* Accessibility ready: skip link, keyboard navigation, visible focus, reduced motion support
* No tracking, system fonts, all scripts bundled locally

== Installation ==

1. In your admin panel, go to Appearance > Themes and click Add New.
2. Click Upload Theme and choose the theme zip file.
3. Click Install Now and then Activate.
4. Open Appearance > Customize to edit the front page hero, overview card and theme options.

5. Create three pages and choose the page templates "Protocol & Technology", "Network" and "Build & Verify". Without a menu, the theme lists them automatically in the header.

== Frequently Asked Questions ==

= How do I change the front page texts? =

Go to Appearance > Customize and open "Front Page Hero" and "Front Page Overview Card".

= How do I switch the default color scheme? =

Go to Appearance > Customize > Theme Options and choose Dark, Light or Follow system setting. Visitors can override it with the switch in the header.

= Does the overview card show live data? =

No. It shows the texts you enter in the Customizer.

= How do I use the menus? =

Go to Appearance > Menus and assign menus to the Primary, Footer and Social Links locations.

== Recommended plugin ==

The theme recommends the companion plugin Elektron Live Blocks (folder plugins/elektron-live-blocks in the same repository). It adds blocks for live network data that you can place on any page. A dismissible notice in the admin area offers to activate or upload it.

When the plugin is active, the theme uses its explorer address and its cache, so there is only one setting and one data source. Without the plugin, the theme keeps its own simple live data option described below.

== Third-party services ==

Live network data is off by default. When the Elektron Live Blocks plugin is active, its settings apply instead. When you enable it and enter an HTTPS explorer API base URL in Appearance > Customize > Live Network Data, your web server (never the visitor's browser) requests the endpoints /blocks/tip/height, /difficulty-adjustment and /fees/recommended from that address. Only whole numbers are read from the answers, and the result is cached for one minute. Use an explorer whose terms you accept.

== Copyright ==

Elektron WordPress Theme, (C) 2026 Ali Kutlusoy <kutlusoy1975@gmail.com>
Elektron is distributed under the terms of the GNU GPL v2 or later.

The Elektron Net logo (assets/images/logo.png) is a project mark of Elektron Net and is included with permission of its owners.

Bundled library: three.js, (C) 2010-2024 three.js authors, MIT License, https://threejs.org (assets/js/hero-network.min.js, license in assets/js/three.LICENSE.txt). Human-readable source of the bundle: assets/src/hero-network.js, build with `npm install && npm run build` in the repository root.

All other assets are original work of this theme: no third-party fonts or images are bundled.

== Changelog ==

= 0.0.1 =
* Initial release.
