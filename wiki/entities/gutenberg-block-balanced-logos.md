---
title: Logo Wrangler Gutenberg Block
type: entity
created: 2026-06-12
updated: 2026-08-30
tags: [gutenberg, block-editor, logo-wrangler]
aliases: [cooper-bold/balanced-logos, cooper-bold/logo-wrangler, logo-wrangler-block]
status: active
---

# Logo Wrangler Gutenberg Block

Dynamic block registered from `src/block/block.json`.

## What it is

- **Name:** `cooper-bold/logo-wrangler`
- **Legacy names (still registered, but hidden from the inserter):** `cooper-bold/balanced-logos`, `cooper-bold/logo-soup` — registered with `supports: { ...blockSettings.supports, inserter: false }` since v1.3.3 (commit `385fbad`), so existing posts keep editing/rendering while new inserts only offer the canonical block
- **API version:** 3
- **Editor script:** `build/index.js`
- **Registration:** `CB_LW::register_block()` on `init`

## How it's used in this project

Editor UI in `src/block/edit.js`. Server render via `render_callback` in `includes/class-cb-lw.php` delegating to `CB_LW_Renderer`. Shared prop sanitizer: `src/shared/to-lw-props.js` (`toLwProps`).

Frontend hydration (`src/view.js`) accepts both `data-cb-lw` and legacy `data-cb-balanced-logos` attributes via `getConfigAttr()`, and fires a `cb-lw-hydrated` bubbling CustomEvent (plus legacy `cb-balanced-logos-hydrated`) after a carousel hydrates. Debug opt-in: `window.CB_LW_DEBUG`, `localStorage['cb-lw-debug']`, or `?cb_lw_debug=1`. (Landed in commit `aed0e5b` 2026-08-17 alongside the rest of the rename to Cooper Bold Logo Wrangler.)

## Related

- [[wordpress-plugin]]
- [[sanity-logo-soup]]
- [[0002-migrate-legacy-plugin-path-activate-only]]
