=== Cooper Bold Logo Soup ===
Contributors: cooperbold
Tags: logo, logos, partners, brands, block
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.1.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display partner and client logos in a balanced, harmonious strip using Sanity Labs Logo Soup normalization.

== Description ==

Cooper Bold Logo Soup wraps the open-source [Logo Soup](https://github.com/sanity-labs/logo-soup) library for WordPress. Logo Soup analyzes each logo image and normalizes visual weight, density, and alignment so mixed brand assets look intentional together — not a scrolling marquee.

**Features**

* Gutenberg block with live editor preview
* **Logo Collections** admin UI — build reusable logo sets with Media Library picker
* `[logo_soup]` and `[cooper-bold-logo-soup]` shortcodes for classic content areas
* Reference collections by slug (`collection="homepage-partners"`) or ID (`id="123"`)
* Media library integration for logo selection
* Tunable normalization controls (size, density, contrast, alignment)
* Frontend assets load only when the block or shortcode is present on the page

**Credits**

Logo normalization powered by [@sanity-labs/logo-soup](https://www.npmjs.com/package/@sanity-labs/logo-soup) (MIT). The WordPress plugin is GPL-2.0-or-later.

== Installation ==

1. Upload the `cooper-bold-logo-soup` folder to `/wp-content/plugins/`, or install the ZIP from the WordPress plugin directory.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. Add the **Logo Soup** block in the block editor, or use the `[logo_soup]` shortcode.

== Frequently Asked Questions ==

= Does this animate logos in a marquee? =

No. Logo Soup normalizes logo sizing and alignment in a static strip.

= Which shortcode should I use? =

Both `[logo_soup]` and `[cooper-bold-logo-soup]` work. Pipe-delimited example:

`[logo_soup logos="/wp-content/uploads/acme.svg|Acme Corp" gap="28" base_size="48"]`

For many logos, use comma-separated chunks: `url|alt|link,url2|alt2`. Raw JSON cannot be used in shortcode attributes (WordPress treats `]` as the end of the tag); use the `base64:` prefix instead: `logos="base64:W3sidXJsIjoiLi4uIn1d"`.

= Does this work with page builders? =

Yes. Use the block in the block editor, or paste the shortcode into any shortcode-capable area (classic editor, widgets, builder HTML modules). For Bricks and similar builders, create a collection under **Logo Soup** in wp-admin, then paste the shortcode snippet from the collection list.

= How do Logo Collections work? =

Go to **Logo Soup → Add New** in wp-admin. Name the collection, add logos from the Media Library, set normalization options, and publish. Use `[logo_soup collection="your-slug"]` or pick the collection in the block sidebar. Inline `logos` shortcodes still work for one-off strips.

= How many logos can I add? =

Up to 50 logos per block or shortcode instance.

== Screenshots ==

1. Block editor — select logos from the media library and tune normalization in the sidebar.
2. Frontend — normalized logo strip after Logo Soup scales and aligns each brand mark.

== Changelog ==

= 1.1.1 =
* Live Logo Soup preview in the collection admin editor (updates as logos and settings change)
* One-click **Copy** buttons for shortcodes on the collection edit screen and All Collections list

= 1.1.0 =
* Logo Collections admin UI (custom post type) with Media Library picker, reorder, alt text, and link URLs
* Top-level **Logo Soup** admin menu with collection list and copy-paste shortcode snippets
* Block sidebar collection dropdown; shortcode `collection` and `id` attributes
* PHPUnit coverage for collection resolution

= 1.0.1 =
* PHPUnit tests for renderer sanitization (logos, colors, densityFactor parity)
* Jest tests for editor preview config (`sanitizePreviewConfig` / `toSoupProps`)

= 1.0.0 =
* Initial release with Gutenberg block and shortcodes
* Conditional frontend asset loading
* Sanitized shortcode and block attributes

== Upgrade Notice ==

= 1.1.1 =
Adds a live preview and one-click shortcode copy buttons when editing Logo Collections in wp-admin.

= 1.1.0 =
Adds Logo Collections — manage logos in wp-admin instead of hand-built shortcode URLs.

= 1.0.1 =
Adds automated test coverage; no user-facing behavior changes.

= 1.0.0 =
Initial public release.
