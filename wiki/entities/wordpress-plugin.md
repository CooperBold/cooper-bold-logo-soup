---
title: Cooper Bold Logo Wrangler Plugin
type: entity
created: 2026-06-12
updated: 2026-08-17
tags: [wordpress, plugin, logo-soup, cooper-bold, logo-wrangler]
aliases: [logo-soup-plugin, balanced-logos, logo-wrangler, cooper-bold-logo-wrangler]
status: active
---

# Cooper Bold Logo Wrangler Plugin

WordPress plugin in this repository for displaying normalized logo strips on the front end. Formerly **Balanced Logos** / Logo Soup by Cooper Bold.

## What it is

- **Main file:** `cooper-bold-logo-wrangler.php`
- **Package:** `CooperBoldLogoWrangler` / prefix `CB_LW_*`
- **Version:** 1.3.0 (`CB_LW_VERSION`)
- **Text domain:** `cooper-bold-logo-wrangler`
- **Upstream:** Wraps `@sanity-labs/logo-soup` npm package

## How it's used in this project

| Surface | Identifier | PHP class |
| --- | --- | --- |
| Block | `cooper-bold/logo-wrangler` | `CB_LW` (register + render) |
| Shortcode | `logo_wrangler` | `CB_LW` |
| Legacy shortcodes | `balanced_logos`, `logo_soup`, `cooper-bold-logo-soup` | `CB_LW` |
| Legacy blocks | `cooper-bold/balanced-logos`, `cooper-bold/logo-soup` | `CB_LW` |
| Render | shared | `CB_LW_Renderer` |
| Assets | `build/view.js` | `CB_LW_Assets` |
| Collections CPT | `cb_logo_collection` | `CB_LW_Collections` |

Bootstrap: `plugins_loaded` → `CB_LW::instance()`. Legacy `balanced-logos.php` is kept as a headerless loader for sites whose `active_plugins` still references the old path; the canonical bootstrap guards double-init via `CB_LW_FILE` and migrates the entry.

## Key details

- Block metadata: `src/block/block.json`
- Build output: `build/` via `npm run build`
- Repo remote: `github.com/CooperBold/cooper-bold-logo-soup` (slug still legacy)
- Frontend CSS: `.cb-lw-wrapper` / `.cb-lw-inner` (also emits legacy `.cb-balanced-logos-*`)

## Related

- [[sanity-logo-soup]]
- [[gutenberg-block-balanced-logos]]
