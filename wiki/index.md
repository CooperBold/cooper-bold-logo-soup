---
title: Index
type: overview
summary: Start here. Must-know facts, then one line per page.
status: active
created: 2026-06-12
updated: 2026-10-22
last_verified: 2026-10-22
sources: []
tags: [meta]
---

# Cooper Bold Logo Wrangler wiki

## Must know

- WP.org: **1.3.4 is in the review queue but still uses the rejected `cb_lw_` prefix.** 1.3.5 (`coopbolo_` prefix + collection migration) is built on branch `fix/coopbolo-prefix`, not uploaded. Nicholas uploads and replies. [[wporg-review-thread]] [[0007-coopbolo-prefix]]
- Never touch `active_plugins` on `plugins_loaded`, and never add `viewScript`/`style` to `block.json`; both are WP.org/architecture rules with guard tests. [[gotchas]]
- Four renames (Logo Soup → Balanced Logos → Logo Wrangler) were repo-wide find/replaces, so old docs misdate names. Legacy shortcodes, blocks, and meta must keep working. [[0003-plugin-name-cooper-bold-logo-wrangler]]
- RapidSOS staging ran 1.2.11 under the old slug; moving it to 1.3.x is **not** a drop-in. [[rapidsos-site]]
- `~/Repos/logo-soup-wp-plugin` and two other paths are symlinks to `~/Repos/Logo Soup WP Plugin`: one checkout. [[overview]]

## Overview

- [[overview]] — what this is, who it's for, where it runs, release state
- [[gotchas]] — known traps; check after a repeated failure

## Entities

- [[wordpress-plugin]] — the plugin: identifiers, version, PHP class per surface, bootstrap
- [[gutenberg-block-balanced-logos]] — block `cooper-bold/logo-wrangler`, hidden legacy aliases, hydration events
- [[logo-collections]] — `coopbolo_collection` CPT (was `cb_logo_collection`; migrated in 1.3.5), meta keys with legacy fallback, REST route
- [[sanity-logo-soup]] — upstream `@sanity-labs/logo-soup` (pinned 1.3.0) and lifecycle hooks
- [[rapidsos-site]] — first consumer (Bricks, Cloudways), its version, and the 1.2→1.3 upgrade traps

## Concepts

- [[rendering-pipeline]] — PHP placeholder render → conditional enqueue → React hydration → canvas measuring
- [[carousel-and-splide]] — `layout="carousel"`, `wrapper` modes, the RapidSOS autoscroll snippet, bundled Splide

## Decisions

- [[0001-pin-splide-extension-auto-scroll]] — stay on auto-scroll 0.5.3, the latest npm release
- [[0002-migrate-legacy-plugin-path-activate-only]] — migrate `balanced-logos.php` entry on activate/update only
- [[0003-plugin-name-cooper-bold-logo-wrangler]] — why the name changed four times; what legacy stays
- [[0004-ship-source-in-release-zip]] — ZIP ships `src/` + `package.json`; readme links the public repo
- [[0005-bundle-upstream-react-component]] — bundle logo-soup's React component, don't port it
- [[0006-php-owned-asset-enqueue]] — frontend assets enqueue from PHP only, when something renders
- [[0007-coopbolo-prefix]] — 1.3.5 renames every PHP global to `coopbolo_` and migrates collections once

## Guides

- [[wordpress-org-submission]] — get into the WP.org directory: ZIP, review, SVN secrets, tag deploy
- [[rapidsos-deploy]] — install on RapidSOS staging, wire collections into Bricks, purge, roll back
- [[wp-env-smoke-test]] — local Docker smoke test and release-ZIP checks

## Sources

- [[wporg-review-thread]] — WP.org review emails, 2026-06-26 → 2026-08-24
- [[notion-logo-soup-plugin-task]] — the Notion task that started the plugin, plus the naming discussion
- [[sar-review]] — 2026-06-12 Spec-Attack-Repair run that chose the architecture
- [[adversarial-review]] — 2026-06-13 adversarial review: fixes, false positives, deferred work

## Meta

- [[SCHEMA]] — how to write here · [[log]] — change history
