=== Cooper Bold Logo Soup ===
Contributors: cooperbold
Tags: logo, logos, partners, clients, brands, gallery
Requires at least: 6.2
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display partner and client logos in a balanced, harmonious strip using Sanity Labs Logo Soup normalization.

== Description ==

Cooper Bold Logo Soup wraps the open-source [Logo Soup](https://github.com/sanity-labs/logo-soup) library for WordPress. Logo Soup analyzes each logo image and normalizes visual weight, density, and alignment so mixed brand assets look intentional together.

**Features**

* Gutenberg block with live editor preview
* `[cooper-bold-logo-soup]` shortcode for classic content areas
* Media library integration for logo selection
* Tunable normalization controls
* Frontend assets load only when the block or shortcode is present on the page

**Credits**

Logo normalization powered by [@sanity-labs/logo-soup](https://www.npmjs.com/package/@sanity-labs/logo-soup) (MIT).

== Installation ==

1. Upload the `cooper-bold-logo-soup` folder to `/wp-content/plugins/`, or install the ZIP from WordPress.org when published.
2. Activate the plugin through the **Plugins** screen.
3. Add the **Logo Soup** block in the block editor, or use the `[cooper-bold-logo-soup]` shortcode.

== Frequently Asked Questions ==

= Does this animate logos in a marquee? =

No. Logo Soup normalizes logo sizing and alignment in a static strip.

== Changelog ==

= 1.0.0 =
* Initial release with Gutenberg block and shortcode

== Upgrade Notice ==

= 1.0.0 =
Initial release.
