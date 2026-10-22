---
title: Rename the PHP prefix to coopbolo_ and migrate collections
type: decision
summary: 1.3.5 renames every plugin-owned global name from cb_lw_/CB_LW_ to coopbolo_/COOPBOLO_ and moves existing collections once on upgrade.
status: active
created: 2026-10-22
updated: 2026-10-22
last_verified: 2026-10-22
sources: [includes/class-coopbolo-collections.php, tests/COOPBOLO_Bootstrap_Test.php, tests/COOPBOLO_Collections_Test.php, readme.txt, "WP.org manual review email 2026-08-26"]
tags: [wordpress-org, prefix, migration, cpt]
---

# Rename the PHP prefix to coopbolo_ and migrate collections

**Status:** active. Built in 1.3.5 on branch `fix/coopbolo-prefix`, 2026-10-22; not uploaded yet.

## Why

The 2026-08-26 WP.org manual review flagged prefix "cb" as too short. Their tool reads a prefix up to its first underscore, so `CB_LW_Assets` and `CB_LW_VERSION` both count as "cb". They require 5+ characters and suggested `coopbolo`. 1.3.4 (uploaded 2026-10-21) still used `cb_lw_`, so it would fail again ([[wporg-review-thread]]).

## What changed

| Before (≤ 1.3.4) | 1.3.5 |
|---|---|
| Classes `CB_LW`, `CB_LW_*` | `COOPBOLO_Plugin`, `COOPBOLO_*` (files `includes/class-coopbolo-*.php`) |
| Functions `cb_lw_*`, defines `CB_LW_*` | `coopbolo_*`, `COOPBOLO_*` |
| CPT `cb_logo_collection` | `coopbolo_collection` (19 chars; the limit is 20) |
| Meta `_cb_lw_logos` / `_cb_lw_settings` | `_coopbolo_logos` / `_coopbolo_settings` |
| Nonce, form fields, list columns `cb_lw_*` | `coopbolo_*` |
| REST `cb-lw/v1/collections` | `coopbolo/v1/collections` |
| Admin handles `cb-lw-*` | `cooper-bold-logo-wrangler-*` |
| Debug flag `window.CB_LW_DEBUG`, `?cb_lw_debug=1` | `window.COOPBOLO_DEBUG`, `?coopbolo_debug=1` |

**Not renamed, on purpose:** frontend CSS classes (`.cb-lw-wrapper`, `.cb-lw-inner`, …), `data-cb-lw`, admin HTML ids, the `[logo_wrangler]` shortcode and its legacy aliases, and block names. Theme and Bricks CSS targets those classes, and saved content uses those tags. The WP.org rule covers PHP globals, options, and CPTs, not CSS.

## Migration

`COOPBOLO_Collections::maybe_migrate_legacy_data()` runs on `init` priority 5 until the option `coopbolo_schema_version` is 1. It uses `set_post_type()` and the post meta API, with no direct SQL, so caches stay valid and Plugin Check stays quiet. It covers every post status, trash included. An existing `_coopbolo_*` key is never overwritten. `_cb_lw_*` stays in the legacy read-fallback list in case anything writes it later. See [[logo-collections]].

**Alternatives rejected:** a direct `UPDATE wp_posts` (Plugin Check warns on direct queries, and it skips cache invalidation), and keeping `cb_logo_collection` (the reviewer listed it by name).

## Verified (2026-10-22)

- PHPUnit 49 (5 new: migration, idempotency, existing-key guard, fallback, prefix guard for defines and CPT, meta, and option names). Jest 19. Build. JS lint at the known baseline (92).
- wp-env, WP 7.1-alpha, PHP 7.4: installed the 1.3.4 ZIP and created collections with it (publish, draft, trash, plus a legacy `_cb_logo_soup_*` one), then upgraded in place with the 1.3.5 ZIP. All four moved; the page render data was identical before and after (2 + 50 logos); a backslash in alt text survived; no PHP notices with `WP_DEBUG` on. The admin list, edit, and save flow worked through the new nonce and fields. REST `coopbolo/v1` returned 200 and `cb-lw/v1` returned 404.
- Fresh install on the clean tests site with `WP_DEBUG` and `WP_DEBUG_LOG` on: activate, render strip + carousel, empty `debug.log`.
- Plugin Check 2.0.0 on the installed 1.3.5 ZIP: no errors or warnings.

## Consequences

- Rolling back to 1.3.4 hides collections until the posts are moved back ([[gotchas]]).
- Outside scripts that query `cb_logo_collection` or `cb-lw/v1` (RapidSOS tooling) need the new names ([[rapidsos-site]]).
- Older pages in this wiki (decisions 0002–0006, sources) keep the `CB_LW_` names they had when written.

## Related

- [[wporg-review-thread]] · [[logo-collections]] · [[wordpress-plugin]] · [[0003-plugin-name-cooper-bold-logo-wrangler]]
