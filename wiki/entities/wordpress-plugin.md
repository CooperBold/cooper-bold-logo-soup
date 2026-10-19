---
title: Cooper Bold Logo Wrangler Plugin
type: entity
summary: The plugin itself: identifiers, version, PHP classes per surface, and bootstrap behavior.
created: 2026-06-12
updated: 2026-10-19
last_verified: 2026-10-19
tags: [wordpress, plugin, logo-soup, cooper-bold, logo-wrangler]
aliases: [logo-soup-plugin, balanced-logos, logo-wrangler, cooper-bold-logo-wrangler]
status: active
sources: [cooper-bold-logo-wrangler.php, balanced-logos.php, includes/, package.json, readme.txt]
---

# Cooper Bold Logo Wrangler Plugin

WordPress plugin in this repository for displaying normalized logo strips on the front end. Earlier names, oldest first: Cooper Bold Logo Soup, Logo Soup, Logo Soup by Cooper Bold, Balanced Logos ([[0003-plugin-name-cooper-bold-logo-wrangler]]).

## What it is

- **Main file:** `cooper-bold-logo-wrangler.php`
- **Package:** `CooperBoldLogoWrangler` / prefix `CB_LW_*`
- **Version:** 1.3.3 (`CB_LW_VERSION`; `CB_LW_VERSION = '1.3.3'`, `Version` header, `package.json` 1.3.3, `readme.txt` Stable tag 1.3.3, Tested up to 7.1; checked 2026-10-19). 1.3.3 is not uploaded to WP.org yet ([[wporg-review-thread]])
- **Text domain:** `cooper-bold-logo-wrangler`
- **Upstream:** Wraps `@sanity-labs/logo-soup` npm package
- **Release ZIP:** now ships `src/` + `package.json` alongside compiled `build/` (since 1.3.1) so WP.org reviewers can regenerate assets via `@wordpress/scripts` ([[0004-ship-source-in-release-zip]])

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

Bootstrap: `plugins_loaded` → `CB_LW::instance()`. Legacy `balanced-logos.php` is kept as a headerless loader for sites whose `active_plugins` still references the old path; it only `require_once`s the canonical bootstrap (no activation-state changes), and the canonical bootstrap guards double-init via `CB_LW_FILE`. The `active_plugins` entry is migrated from legacy → canonical **only** on user-initiated **activate** or **plugin update** (`upgrader_process_complete`) hooks — never on `plugins_loaded` — and the deactivation hook removes both basenames. Rationale and hook details: [[0002-migrate-legacy-plugin-path-activate-only]].

## Key details

- Block metadata: `src/block/block.json`
- Build output: `build/` via `npm run build`
- Repo remote: `github.com/CooperBold/cooper-bold-logo-soup` (slug still legacy)
- Frontend CSS: `.cb-lw-wrapper` / `.cb-lw-inner` (also emits legacy `.cb-balanced-logos-*`)

## Related

- [[overview]] · [[rendering-pipeline]] · [[logo-collections]] · [[carousel-and-splide]]
- [[sanity-logo-soup]]
- [[gutenberg-block-balanced-logos]]
