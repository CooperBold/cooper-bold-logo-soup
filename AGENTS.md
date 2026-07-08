# Balanced Logos — Agent Notes

## What this plugin does

Wraps `@sanity-labs/logo-soup` for WordPress. **Balanced Logos** normalizes mixed logo images (size, density, visual center) into a balanced strip. It is not a marquee.

## Stack

- PHP 7.4+ plugin bootstrap in `balanced-logos.php` + `includes/`
- Gutenberg block: `src/block/` → `build/index.js`
- Frontend hydration: `src/view.js` → `build/view.js`
- Build: `npm run build` (`@wordpress/scripts` + custom `webpack.config.js` dual entry)

## Key conventions

- Block name: `cooper-bold/balanced-logos`
- Shortcode: `[balanced_logos]` — prefer `collection="slug"` or `id="123"` from Logo Collections CPT. Legacy aliases: `[logo_soup]`, `[cooper-bold-logo-soup]`.
- Collections CPT: `cb_logo_collection` — admin menu **Balanced Logos**
- Frontend mount selector: `[data-cb-balanced-logos]` on `.cb-balanced-logos-inner`
- Frontend CSS targeting: `.cb-balanced-logos-wrapper` for outer spacing/margins; `.cb-balanced-logos-inner` for the logo row
- View script handle: `balanced-logos-view`
- Assets enqueue only when block `render_callback` or shortcode runs

## Asset enqueue

- `block.json` declares **editor** assets only (`editorScript`, `editorStyle`). Do **not** add `viewScript` or `style` — frontend JS/CSS is registered and enqueued from `CB_Balanced_Logos_Assets::maybe_enqueue()` when `has_block()` or `[balanced_logos]` (or legacy shortcodes) is present.
- Build entries: `npm run build` compiles `src/block/index.js`, `src/view.js`, and `src/view.scss` (no custom `webpack.config.js`).

## Logo count

No built-in cap on logos per collection, block, or shortcode. Admin users manage performance tradeoffs themselves.

## Layout modes

- `layout="strip"` (default) — one normalized row (`.cb-balanced-logos-wrapper` > `.cb-balanced-logos-inner`)
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
