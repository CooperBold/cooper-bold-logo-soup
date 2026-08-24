# Cooper Bold Logo Wrangler — Agent Notes

## What this plugin does

Wraps `@sanity-labs/logo-soup` for WordPress. **Cooper Bold Logo Wrangler** normalizes mixed logo images (size, density, visual center) into a balanced strip. It is not a marquee.

## Stack

- PHP 7.4+ plugin bootstrap in `cooper-bold-logo-wrangler.php` + `includes/` (only that file has the Plugin Name header). Legacy `balanced-logos.php` is a headerless loader that requires the canonical file when the old path is still listed in `active_plugins`. Migrate that entry only on user-initiated **activate** or **plugin update** hooks — never on `plugins_loaded`.
- Gutenberg block: `src/block/` → `build/index.js`
- Frontend hydration: `src/view.js` → `build/view.js`
- Build: `npm run build` (`@wordpress/scripts` + custom `webpack.config.js` dual entry)

## Key conventions

- Block name: `cooper-bold/logo-wrangler`
- Shortcode: `[logo_wrangler]` — prefer `collection="slug"` or `id="123"` from Logo Collections CPT. Legacy aliases: `[balanced_logos]`, `[logo_soup]`, `[cooper-bold-logo-soup]`.
- Collections CPT: `cb_logo_collection` — admin menu **Logo Wrangler**
- Frontend mount selector: `[data-cb-lw]` on `.cb-lw-inner`
- Frontend CSS targeting: `.cb-lw-wrapper` for outer spacing/margins; `.cb-lw-inner` for the logo row (legacy `.cb-balanced-logos-wrapper` / `.cb-balanced-logos-inner` still emitted)
- View script handle: `cooper-bold-logo-wrangler-view`
- Assets enqueue only when block `render_callback` or shortcode runs

## Asset enqueue

- `block.json` declares **editor** assets only (`editorScript`, `editorStyle`). Do **not** add `viewScript` or `style` — frontend JS/CSS is registered and enqueued from `CB_LW_Assets::maybe_enqueue()` when `has_block()` or `[logo_wrangler]` (or legacy shortcodes) is present.
- Build entries: `npm run build` compiles `src/block/index.js`, `src/view.js`, and `src/view.scss` (no custom `webpack.config.js`).

## Logo count

No built-in cap on logos per collection, block, or shortcode. Admin users manage performance tradeoffs themselves.

## Layout modes

- `layout="strip"` (default) — one normalized row (`.cb-lw-wrapper` > `.cb-lw-inner`)
- `layout="carousel"` — one Splide slide per logo; use `wrapper="slides"` to nest inside Bricks nested sliders, or `wrapper="full"` for standalone Splide markup
- Carousel slides use class `logo-slider-slide` for RapidSOS Splide Auto Scroll compatibility

## Do not

- Commit `node_modules/` or secrets
- Port logo-soup to vanilla JS unless upstream API changes make React bundling untenable
- Add marquee/animation unless explicitly requested — out of scope for logo-soup

## Verify after changes

```bash
npm run build
npm run lint:js
```

Test in WP: insert block, add logos, confirm frontend strip renders; test shortcode on a classic page.
