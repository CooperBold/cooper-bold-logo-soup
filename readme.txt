=== Logo Soup by Cooper Bold ===
Contributors: cooperbold
Tags: logo, logos, partners, brands, block
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.2.13
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display partner and client logos in a balanced, harmonious strip using Sanity Labs Logo Soup normalization.

== Description ==

Logo Soup wraps the open-source [Logo Soup](https://github.com/sanity-labs/logo-soup) library for WordPress. Logo Soup analyzes each logo image and normalizes visual weight, density, and alignment so mixed brand assets look intentional together — not a scrolling marquee.

**Features**

* Gutenberg block with live editor preview
* **Logo Collections** admin UI — build reusable logo sets with Media Library picker
* `[logo_soup]` and `[cooper-bold-logo-soup]` shortcodes for classic content areas
* Reference collections by slug (`collection="homepage-partners"`) or ID (`id="123"`)
* Media library integration for logo selection
* Tunable normalization controls (size, density, contrast, alignment)
* Frontend assets load only when the block or shortcode is present on the page

**Credits**

Logo normalization powered by [@sanity-labs/logo-soup](https://www.npmjs.com/package/@sanity-labs/logo-soup) (MIT). Built by [Cooper Bold](https://cooperbold.com). The WordPress plugin is GPL-2.0-or-later.

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

There is no built-in limit. Add as many logos as your collection or block needs; very large sets may affect page load time.

== Screenshots ==

1. Block editor — select logos from the media library and tune normalization in the sidebar.
2. Frontend — normalized logo strip after Logo Soup scales and aligns each brand mark.

== Changelog ==

= 1.2.13 =
* Plugin Check: sanitize collection editor POST arrays; prefix admin screen helper for PHPCS

= 1.2.12 =
* Vendor Splide core and Auto Scroll extension locally for standalone carousels (no external CDN scripts)
* WordPress.org directory assets and readme polish for plugin review

= 1.2.11 =
* Mount carousel Splide immediately after logo nodes are distributed — no longer wait for every slide image decode (fixes multi-second scroll delay on large collections)
* Reduce hydration fail-open timeout from 4s to 1.5s (safety net when LogoSoup render is slow)

= 1.2.10 =
* Opt-in frontend debug logging for carousel hydration (`CB_LOGO_SOUP_DEBUG`, `?cb_logo_soup_debug=1`, or localStorage)

= 1.2.9 =
* Standalone Splide fallback when a theme autoscroll snippet is absent or fails to mount after hydration
* Snippet deferral now requires the live footer autoscroll script, not only a global flag
* Carousel shows hydrated slide images while Splide initializes (`cb-logo-soup-hydrating` visibility override)
* Accept LogoSoup blob: images in slide load checks; 4s fail-open completes hydration when image probes stall
* Fix logo row lookup during carousel distribute (use LogoSoup root child, not `[class*="logo-soup"]` descendant search)

= 1.2.8 =
* Standalone carousel: plugin owns Splide + Auto Scroll init after logo images load (no custom snippet required)
* Wait for slide images before first Splide mount — prevents empty mount and scroll jerk
* Enqueue Splide Auto Scroll extension for wrapper=full carousels
* Viewport-based autoscroll speed; defer init when a theme autoscroll snippet is active on legacy pages

= 1.2.7 =
* Standalone carousel hydration no longer calls `splide.refresh()` when a theme autoscroll snippet is present — avoids mid-scroll reposition jerk

= 1.2.6 =
* Standalone carousel hydration defers Splide mount when a theme autoscroll snippet is present; snippet owns init to avoid destroy/remount visual jump

= 1.2.5 =
* Fix carousel hydration and Splide clone handling on the frontend


= 1.2.4 =
* Fix standalone carousel Splide enqueue when Bricks registers an empty `splide` script handle (load bundled Splide 4.1.4 instead)
* Detect `[logo_soup]` shortcodes in Bricks `_bricks_page_content_2` meta for early asset enqueue

= 1.2.3 =
* Standalone carousel layout (`wrapper="full"`) enqueues Splide 4.1.4 core CSS/JS before view.js when Bricks/theme Splide is absent
* Conditional detection for block, shortcode, and collection-backed carousel markup

= 1.2.2 =
* Docs-only: professionalize plugin code comments (no behavior changes)

= 1.2.1 =
* Strip layout SSR placeholders mirror LogoSoup hydrated DOM (`div` > `span` > `img`) so Bricks builder preview matches frontend grid CSS without running view.js
* Bricks CSS hint: target `.cb-logo-soup-inner > div > span` for strip layout

= 1.2.0 =
* Carousel layout mode — one normalized logo per Splide slide for nested sliders
* Collection setting and block/shortcode `layout` attribute (`strip` default, `carousel` for sliders)
* Shortcode `wrapper="slides"` outputs slide fragments for nesting inside existing Splide lists; `wrapper="full"` outputs a standalone carousel
* `logo-slider-slide` class on carousel slides for Splide Auto Scroll compatibility

= 1.1.12 =
* One-line help text under each collection and block setting explaining what it does
* Stable BEM wrapper classes for frontend CSS: `.cb-logo-soup-wrapper` (outer) and `.cb-logo-soup-inner` (logo row / hydration mount)

= 1.1.11 =
* Fix collection admin Live Preview — read density-aware and crop settings from the advanced settings table so preview matches frontend output
* Match preview strip layout to frontend (full-width scroll, shared CSS variables for size/gap/background)

= 1.1.10 =
* Replace wp-admin footer wordmark with plain CooperBold text link on Logo Collections screens
* Match block editor sidebar credit to plain CooperBold text
* Clear wp-admin right footer (theme/plugin update nags) on Logo Collections screens

= 1.1.9 =
* Release tag for Cooper Bold wordmark in wp-admin left footer on Logo Collections screens via `admin_footer_text` (removes redundant meta box text credit from 1.1.8)

= 1.1.8 =
* Replace wp-admin left footer text with Cooper Bold wordmark on Logo Collections screens via `admin_footer_text` (no overlap with version nags)

= 1.1.7 =
* Fix Cooper Bold admin footer credit — small wordmark bottom-right on Logo Collections screens only, no overlap with core wp-admin footer

= 1.1.6 =
* Add Cooper Bold wordmark footer on Logo Collections admin list and edit screens
* Subtle cooperbold.com credit in the block editor sidebar

= 1.1.5 =
* Remove the 50-logo cap from collections, block editor, and frontend rendering

= 1.1.4 =
* Fix duplicate **All Collections** entry in the wp-admin Logo Soup menu

= 1.1.3 =
* User-facing plugin name is now **Logo Soup** (folder slug, text domain, and block name unchanged)

= 1.1.2 =
* Simplified Logo Collections admin UI — sparse settings, single primary shortcode with discreet ID alternative, compact list-table copy

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

= 1.2.13 =
Plugin Check compliance — no behavior change for collections or frontend output.

= 1.2.12 =
Vendors Splide locally for carousel mode — recommended before WordPress.org directory release.

= 1.1.12 =
Adds setting descriptions and stable wrapper classes for theme CSS — no migration required.

= 1.1.11 =
Fixes collection Live Preview not matching the frontend strip — no migration required.

= 1.1.10 =
Plain CooperBold footer text on Logo Collections screens and in the block sidebar — no migration required.

= 1.1.9 =
Cooper Bold wordmark replaces the default wp-admin footer text on Logo Collections screens — no migration required.

= 1.1.8 =
Cooper Bold wordmark replaces the default wp-admin footer text on Logo Collections screens — no migration required.

= 1.1.7 =
Fixes oversized admin footer branding on Logo Collections screens — no migration required.

= 1.1.6 =
Adds a Cooper Bold wordmark in Logo Collections wp-admin and a credit link in the block sidebar — no migration required.

= 1.1.5 =
Removes the previous 50-logo limit — large collections now render in full.

= 1.1.4 =
Fixes a duplicate menu item under **Logo Soup** in wp-admin.

= 1.1.3 =
Display name only — no migration required.

= 1.1.2 =
Cleaner Logo Collections admin — fewer visible options by default, streamlined shortcode copy.

= 1.1.1 =
Adds a live preview and one-click shortcode copy buttons when editing Logo Collections in wp-admin.

= 1.1.0 =
Adds Logo Collections — manage logos in wp-admin instead of hand-built shortcode URLs.

= 1.0.1 =
Adds automated test coverage; no user-facing behavior changes.

= 1.0.0 =
Initial public release.
