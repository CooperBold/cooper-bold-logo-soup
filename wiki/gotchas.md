---
title: Gotchas
type: overview
summary: Known traps. Check here after a repeated failure.
status: active
created: 2026-10-19
updated: 2026-10-22
last_verified: 2026-10-22
sources: [AGENTS.md, AI_SESSION_MEMORY.md, AI_RUNBOOK.md, readme.txt, .wp-env.json, .distignore, .github/workflows/deploy.yml, includes/class-cb-lw-collections.php, src/view.js]
tags: [gotchas]
---

# Gotchas

One line each: the trap, then the fix or the page with details.

## Names and history

- **Old docs show today's name for past events.** Every rename was a repo-wide find/replace, including changelog and session memory. Use `git show <commit>:<file>` for history. See [[0003-plugin-name-cooper-bold-logo-wrangler]].
- **The GitHub repo is `cooper-bold-logo-soup`**, not `cooper-bold-logo-wrangler`. The secrets path and support-forum URL in `docs/WORDPRESS-ORG-SUBMISSION.md` use wrong slugs. See [[wordpress-org-submission]].
- **Four paths, one checkout.** `~/Repos/logo-soup-wp-plugin`, `cooper-bold-logo-wrangler-wp-plugin`, and `logo-soup-wp-plugin-smoke` are symlinks to `~/Repos/Logo Soup WP Plugin`. An edit in one is an edit in all. See [[overview]].

## Code rules (from AGENTS.md, with the why)

- **Never migrate `active_plugins` on `plugins_loaded`.** WP.org flagged writing that option on every load. Migrate on activate or update only, which a guard test enforces. See [[0002-migrate-legacy-plugin-path-activate-only]].
- **No `viewScript` or `style` in `block.json`.** It double-enqueues with the shortcode path. See [[0006-php-owned-asset-enqueue]].
- **Don't remove legacy shortcodes, blocks, meta keys, or classes.** Live content (RapidSOS uses `[logo_soup]`) still depends on them. Hide old blocks from the inserter; don't unregister them. See [[0002-migrate-legacy-plugin-path-activate-only]].
- **Don't change the strip placeholder DOM (`div > span > img`).** Bricks designer CSS targets `.cb-lw-inner > div > span` and must match before and after hydration. See [[rendering-pipeline]].
- **Not a marquee.** Don't add animation unless asked (`AGENTS.md`). The carousel scroll belongs to Splide or the site snippet.
- **New build entry → update `readme.txt` `== Source code ==` too**, or WP.org's source check fails again. See [[0004-ship-source-in-release-zip]].

## Runtime

- **Logos from another origin fail to normalize** (canvas CORS). Use Media Library uploads; wp-env seeds must be local PNGs, not hotlinks (`AI_SESSION_MEMORY.md` 2026-06-22). See [[rendering-pipeline]].
- **Bricks registers an empty `splide` handle.** Enqueuing it loads nothing; the plugin uses its own handles instead. See [[carousel-and-splide]].
- **Carousel looks frozen or jumps on RapidSOS:** check whether the `rapidsos-logo-slider-fix` snippet is active. The plugin defers to it only if both its global and its script tag exist. See [[carousel-and-splide]].
- **Block sidebar lists at most 100 collections** (`coopbolo/v1/collections` uses `posts_per_page` 100). See [[logo-collections]].
- **Saving a collection in 1.3.x deletes the legacy `_cb_logo_soup_*` meta**, so outside scripts writing the old key stop working. See [[logo-collections]].

## Releases and environments

- **RapidSOS 1.2.11 → 1.3.x is not a drop-in.** It means a new folder, no loader for the old file, a new REST route, and two copies registering `[logo_soup]` if both are active. See [[rapidsos-site]].
- **1.3.4 (uploaded 2026-10-21) still fails the prefix rule.** The 2026-08-26 manual review rejected `CB_LW_` / `cb_lw_` as the 2-letter prefix "cb". 1.3.5 on branch `fix/coopbolo-prefix` renames everything to `coopbolo_`; Nicholas uploads it and replies. See [[wporg-review-thread]], [[0007-coopbolo-prefix]].
- **Check the ZIP's top level before uploading.** `.distignore` must list every new dev folder; `.claude/` and `.wp-env.override.json` leaked into the first 1.3.4 build. Expect only `LICENSE admin balanced-logos.php build cooper-bold-logo-wrangler.php includes lib package*.json readme.txt src`.
- **New global names need the `coopbolo_` / `COOPBOLO_` prefix** (functions, classes, defines, options, meta, nonces, CPT, REST namespace). WP.org reads a prefix up to its first underscore, so `cb_lw_` counted as "cb". `COOPBOLO_Bootstrap_Test` fails on an unprefixed function, class, or define. Legacy shortcodes and the `cb-lw` CSS classes / `data-cb-lw` keep their names on purpose (live content, theme CSS).
- **Rolling back from 1.3.5 to 1.3.4 needs the post type changed back**, or every collection disappears. 1.3.4 only knows `cb_logo_collection`. Push files and database together on Cloudways; never a 1.3.5 database onto 1.3.4 code. Exact WP-CLI steps: [[rapidsos-deploy]] → Rollback checklist.
- **wp-env serves the old plugin code for about 2 seconds after `wp plugin install --force`** (opcache `revalidate_freq=2`). Any WP-CLI command also boots WordPress and fires `init`, so it runs pending migrations. Wait or use WP-CLI before reading results (2026-10-22).
- **Directory art may not deploy.** The PNGs live in `assets/`, but the 10up deploy action reads `.wordpress-org/` by default, and `deploy.yml` sets no `ASSETS_DIR`. Set it or move the files before the first tag (default per the [10up action README](https://github.com/10up/action-wordpress-plugin-deploy)). See [[wordpress-org-submission]].
- **README says a GitHub Release also deploys**; `deploy.yml` triggers only on `v*` tag pushes.
- **wp-env breaks on paths with spaces.** `.wp-env.json` mounts the `cooper-bold-logo-wrangler-wp-plugin` symlink, and `WP_ENV_CORE` must be a local core checkout under `$HOME` (shallow clone fails `index-pack`). See [[wp-env-smoke-test]].
- **wp-env fails to build on PHP 7.4** (`apt-get install $PHPIZE_DEPS` exits 100). Put `{"phpVersion":"8.2"}` in the gitignored `.wp-env.override.json` (2026-10-21).
- **`AI_RUNBOOK.md` is partly stale.** It mentions a `class-cb-lw-shortcode.php` that doesn't exist (shortcodes live in `class-coopbolo-plugin.php`) and lists logo-soup `^1.2.2` (pinned `1.3.0`). Trust the code and `package.json`.

## Related

- [[overview]] · [[index]]
