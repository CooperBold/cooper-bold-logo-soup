---
title: Balanced Logos Plugin
type: entity
created: 2026-06-12
updated: 2026-06-12
tags: [wordpress, plugin, logo-soup, cooper-bold]
aliases: [logo-soup-plugin, balanced-logos]
status: active
---

# Balanced Logos Plugin

WordPress plugin in this repository for displaying normalized logo strips on the front end.

## What it is

- **Main file:** `balanced-logos.php`
- **Package:** `CooperBoldBalancedLogos` / prefix `CB_Balanced_Logos_*`
- **Version:** 1.0.0 (`CB_BALANCED_LOGOS_VERSION`)
- **Text domain:** `balanced-logos`
- **Upstream:** Wraps `@sanity-labs/logo-soup` npm package

## How it's used in this project

| Surface | Identifier | PHP class |
| --- | --- | --- |
| Block | `cooper-bold/balanced-logos` | `CB_Balanced_Logos_Block` |
| Shortcode | `balanced_logos` | `CB_Balanced_Logos` |
| Render | shared | `CB_Balanced_Logos_Renderer` |
| Assets | `build/view.js` | `CB_Balanced_Logos_Assets` |

Bootstrap: `plugins_loaded` → `CB_Balanced_Logos::instance()`.

## Key details

- Block metadata: `src/block/block.json`
- Build output: `build/` via `npm run build`
- Repo remote (header): `github.com/CooperBold/balanced-logos`

## Related

- [[sanity-logo-soup]]
- [[gutenberg-block-balanced-logos]]
