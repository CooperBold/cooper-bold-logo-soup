---
title: Balanced Logos Gutenberg Block
type: entity
created: 2026-06-12
updated: 2026-06-12
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

## Related

- [[wordpress-plugin]]
- [[sanity-logo-soup]]
