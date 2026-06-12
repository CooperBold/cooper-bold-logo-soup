---
title: Logo Soup Gutenberg Block
type: entity
created: 2026-06-12
updated: 2026-06-12
tags: [gutenberg, block-editor]
aliases: [cooper-bold/logo-soup]
status: active
---

# Logo Soup Gutenberg Block

Dynamic block registered from `src/block/block.json`.

## What it is

- **Name:** `cooper-bold/logo-soup`
- **API version:** 3
- **Editor script:** `build/index.js`
- **Registration:** `CB_Logo_Soup_Block::register()` on `init`

## How it's used in this project

Editor UI in `src/block/edit.js`. Server render via `render_callback` in `class-cb-logo-soup-block.php` delegating to `CB_Logo_Soup_Renderer`.

## Related

- [[wordpress-plugin]]
- [[sanity-logo-soup]]
