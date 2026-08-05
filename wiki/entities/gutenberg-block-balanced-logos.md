---
title: Logo Wrangler Gutenberg Block
type: entity
created: 2026-06-12
updated: 2026-07-31
tags: [gutenberg, block-editor, logo-wrangler]
aliases: [cooper-bold/balanced-logos, cooper-bold/logo-wrangler, logo-wrangler-block]
status: active
---

# Logo Wrangler Gutenberg Block

Dynamic block registered from `src/block/block.json`.

## What it is

- **Name:** `cooper-bold/logo-wrangler`
- **Legacy names (still registered):** `cooper-bold/balanced-logos`, `cooper-bold/logo-soup`
- **API version:** 3
- **Editor script:** `build/index.js`
- **Registration:** `CB_LW::register_block()` on `init`

## How it's used in this project

Editor UI in `src/block/edit.js`. Server render via `render_callback` in `includes/class-cb-lw.php` delegating to `CB_LW_Renderer`. Shared prop sanitizer: `src/shared/to-lw-props.js` (`toLwProps`).

## Related

- [[wordpress-plugin]]
- [[sanity-logo-soup]]
