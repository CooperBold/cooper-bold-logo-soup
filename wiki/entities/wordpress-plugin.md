---
title: Cooper Bold Logo Soup Plugin
type: entity
created: 2026-06-12
updated: 2026-06-12
tags: [wordpress, plugin, logo-soup, cooper-bold]
aliases: [logo-soup-plugin, cooper-bold-logo-soup]
status: active
---

# Cooper Bold Logo Soup Plugin

WordPress plugin in this repository for displaying normalized logo strips on the front end.

## What it is

- **Main file:** `cooper-bold-logo-soup.php`
- **Package:** `CooperBoldLogoSoup` / prefix `CB_Logo_Soup_*`
- **Version:** 1.0.0 (`CB_LOGO_SOUP_VERSION`)
- **Text domain:** `cooper-bold-logo-soup`
- **Upstream:** Wraps `@sanity-labs/logo-soup` npm package

## How it's used in this project

| Surface | Identifier | PHP class |
| --- | --- | --- |
| Block | `cooper-bold/logo-soup` | `CB_Logo_Soup_Block` |
| Shortcode | `logo_soup` | `CB_Logo_Soup_Shortcode` |
| Render | shared | `CB_Logo_Soup_Renderer` |
| Assets | `build/view.js` | `CB_Logo_Soup_Assets` |

Bootstrap: `plugins_loaded` → `CB_Logo_Soup::instance()`.

## Key details

- Block metadata: `src/block/block.json`
- Build output: `build/` via `npm run build`
- Repo remote (header): `github.com/CooperBold/cooper-bold-logo-soup`

## Related

- [[sanity-logo-soup]]
- [[gutenberg-block-logo-soup]]
