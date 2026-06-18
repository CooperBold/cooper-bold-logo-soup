# Logo Soup — Agent Notes

## What this plugin does

Wraps `@sanity-labs/logo-soup` for WordPress. Logo Soup **normalizes** mixed logo images (size, density, visual center) into a balanced strip. It is not a marquee.

## Stack

- PHP 7.4+ plugin bootstrap in `cooper-bold-logo-soup.php` + `includes/`
- Gutenberg block: `src/block/` → `build/index.js`
- Frontend hydration: `src/view.js` → `build/view.js`
- Build: `npm run build` (`@wordpress/scripts` + custom `webpack.config.js` dual entry)

## Key conventions

- Block name: `cooper-bold/logo-soup`
- Shortcode: `[logo_soup]` — prefer `collection="slug"` or `id="123"` from Logo Collections CPT
- Collections CPT: `cb_logo_collection` — admin menu **Logo Soup**
- Frontend mount selector: `[data-cb-logo-soup]` on `.cb-logo-soup-inner`
- Frontend CSS targeting: `.cb-logo-soup-wrapper` for outer spacing/margins; `.cb-logo-soup-inner` for the logo row
- View script handle: `cooper-bold-logo-soup-view`
- Assets enqueue only when block `render_callback` or shortcode runs

## Asset enqueue

- `block.json` declares **editor** assets only (`editorScript`, `editorStyle`). Do **not** add `viewScript` or `style` — frontend JS/CSS is registered and enqueued from `CB_Logo_Soup_Assets::maybe_enqueue()` when `has_block()` or `[logo_soup]` / `[cooper-bold-logo-soup]` is present.
- Build entries: `npm run build` compiles `src/block/index.js`, `src/view.js`, and `src/view.scss` (no custom `webpack.config.js`).

## Logo count

No built-in cap on logos per collection, block, or shortcode. Admin users manage performance tradeoffs themselves.

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
