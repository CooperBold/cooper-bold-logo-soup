---
title: Balanced Logos Gutenberg Block
type: entity
created: 2026-06-12
updated: 2026-07-31
tags: [gutenberg, block-editor]
aliases: [cooper-bold/balanced-logos]
status: active
---

# Balanced Logos Gutenberg Block

Dynamic block registered from `src/block/block.json`.

## What it is

- **Name:** `cooper-bold/balanced-logos`
- **API version:** 3
- **Editor script:** `build/index.js`
- **Registration:** `CB_Balanced_Logos_Block::register()` on `init`

## How it's used in this project

Editor UI in `src/block/edit.js`. Server render via `render_callback` in `class-cb-balanced-logos-block.php` delegating to `CB_Balanced_Logos_Renderer`.

## Rename in progress (2026-07-31)

The working tree carries an uncommitted internal rename `balanced-logos` → `cb-cblsn` (Cooper Bold Logo Strip Normalizer): `CB_Balanced_Logos_*` classes → `CB_CBLSN_*`, `src/shared/to-cblsn-props.js` exporting `toCBLSNProps`, hydrating class `cb-cblsn-hydrating`, debug opt-in `window.CB_CBLSN_DEBUG` / `?cb_cblsn_debug=1`. This page's identifiers above describe the pre-rename state until the rename is committed.

## Related

- [[wordpress-plugin]]
- [[sanity-logo-soup]]
