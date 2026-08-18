---
title: Wiki Log
type: log
created: 2026-06-12
updated: 2026-08-09
---

# Wiki Log

> Chronological record of wiki operations. Append-only.
>
> Format: `## [YYYY-MM-DD] verb | Subject`
>
> Verbs: `ingest`, `query`, `lint`, `update`, `create`, `migrate`, `session`

## [2026-08-17] update | Closeout commit landed the rename to Cooper Bold Logo Wrangler

Commit `aed0e5b closeout: auto-commit from 2026-08-17` (2026-08-17 12:33 PDT) landed everything that the 2026-08-05 (×2) and 2026-08-07 log entries had tracked as "uncommitted, commit pending". Working tree is now clean (`git status` empty). Stat: 62 files, +1017 / −933. Highlights:

- **Bootstrap renamed for real.** `cooper-bold-logo-wrangler.php` is the only file with a `Plugin Name` header; `balanced-logos.php` is a thin headerless legacy loader (`CB_LW_BOOTSTRAP_FILE` → `require_once` canonical bootstrap) that still gets included when the old path is in `active_plugins`.
- **PHP classes renamed to `CB_LW_*`.** `class-cb-balanced-logos.php` → `class-cb-lw.php`; siblings `assets`, `renderer`, `collections`, `admin-branding` all under `cb-lw-`. Shortcode is now handled in `CB_LW` alongside the block (no separate `CB_LW_Shortcode` class — `AI_RUNBOOK.md` was clarified to match).
- **JS / tests renamed.** `src/shared/to-balanced-logos-props.js` → `to-lw-props.js`; matching test rename; `tests/CB_Balanced_Logos_Assets_Test.php` → `CB_LW_Assets_Test.php` (the file was rewritten, not just renamed).
- **`crewai/` scaffolding deleted.** `crewai/` directory is gone: agents/tasks YAML, `crew.py`, `main.py`, `tools/`, `pyproject.toml`, `crewai/.gitignore`, `crewai/.env.example`, `crewai/README.md`. `AI_RUNBOOK.md` "AI tooling" row drops "CrewAI" and lists SimpleMem + Cursor planning + wiki + Context7 instead.
- **`AGENTS.md` tidied.** Heading is "Cooper Bold Logo Wrangler — Agent Notes" (previously still said "Logo Strip Normalizer" / `cooper_cblsn_*`). Build section clarifies there is **no custom `webpack.config.js`** — entries are `src/block/index.js`, `src/view.js`, `src/view.scss`.
- **`readme.txt`** bumped to **Stable tag: 1.3.0**, leading shortcode is `[logo_wrangler]`, plugins folder is `cooper-bold-logo-wrangler/`, admin menu is "Logo Wrangler".

The earlier wiki entities ([[wordpress-plugin]], [[gutenberg-block-balanced-logos]]) and guides ([[wp-env-smoke-test]], [[rapidsos-deploy]], [[wordpress-org-submission]]) already described the post-rename state, so this commit lines the repo up with the wiki rather than the other way around. Touched [[gutenberg-block-balanced-logos]] to drop the now-stale "(uncommitted rename state)" qualifier; the agent [[wordpress-plugin]] was already accurate (only date stamp needed). [[0001-pin-splide-extension-auto-scroll]] unchanged. No new entities or decisions introduced by this commit.

## [2026-08-09] create | Decision: pin splide-extension-auto-scroll@0.5.3

No new commits since `3e8b804`; the uncommitted rename working tree was fully covered by the 2026-08-05/07 entries. One new detail surfaced in the working-tree `readme.txt` (touched 2026-08-08): the 1.3.0 changelog now documents that `splide-extension-auto-scroll@0.5.3` is the **latest published npm release** (upstream repo's last commit 2022-09-06) and that WP.org's `out of date libraries` check is comparing against a development branch with no new tagged release. Recorded as [[0001-pin-splide-extension-auto-scroll]] — the first entry in `decisions/` — and indexed.

## [2026-08-07] update | Rename tail: package.json, test bootstrap, view.scss (uncommitted)

No new commits since `a50f708`. Remaining uncommitted rename details not previously recorded: `package.json` name is now `cooper-bold-logo-wrangler` (version 1.3.0); `tests/bootstrap.php` stubs renamed to `CB_LW_PATH` / `CB_LW_URL` constants (plugin URL now `.../cooper-bold-logo-wrangler/`) and gained `metadata_exists()` / `delete_post_meta()` stubs backed by `$GLOBALS['cb_test_post_meta']`; `src/view.scss` now dual-covers every legacy selector with `.cb-lw-*` equivalents (`.cb-lw-inner`, `.cb-lw-slide`, `.cb-lw-carousel-ref`, hydrating-state rules for both class families). Already consistent with [[gutenberg-block-balanced-logos]]; commit still pending.

## [2026-08-05] update | Legacy loader + crewai removal (uncommitted)

Same working tree, two details not yet recorded: `balanced-logos.php` is now a **headerless legacy loader** (no `Plugin Name` header — not a second plugin entry) that WordPress includes when the old path remains in `active_plugins`; the canonical `cooper-bold-logo-wrangler.php` bootstrap guards double-init via `CB_LW_FILE` and migrates the entry. Also the entire `crewai/` scaffolding (agents/tasks YAML, crew.py) is deleted in the working tree, and `readme.txt` now leads with `[logo_wrangler]` as the primary shortcode (Stable tag 1.3.0). Noted on [[wordpress-plugin]]. Commit still pending.

## [2026-08-05] update | view.js dual-attr hydration + distignore hygiene (uncommitted)

No new commits since `c2449b9`. Working tree gained two things since the 2026-07-31 rename notes: `src/view.js` now reads config from `data-cb-lw` **or** legacy `data-cb-balanced-logos` attrs via a `getConfigAttr()` helper (strips, carousel refs, slides, splide roots all match both), and on hydration dispatches a `cb-lw-hydrated` CustomEvent alongside the legacy `cb-balanced-logos-hydrated`. `.distignore` now also excludes local tooling: `MEMORY.md`, `.verify.sh`, `requirements-lancedb.txt`/`requirements*.txt`, `/.loops`. Noted on [[gutenberg-block-balanced-logos]]. Commit still pending.

## [2026-08-05] update | Guides caught up to committed rename

Commit `78214bb` (nightly auto-commit) renamed the operational docs — `docs/RAPIDSOS-DEPLOY.md`, `docs/SMOKE-TEST.md`, `docs/WORDPRESS-ORG-SUBMISSION.md` — from Balanced Logos to **Cooper Bold Logo Wrangler** (`[logo_wrangler]` shortcode, `cb-lw` classes/attrs, `cooper-bold-logo-wrangler-view` script handle, `CB_LW_VERSION`, test files `CB_LW_Renderer_Test.php` / `to-lw-props.test.js`). The wiki guides still used the old names, so synced [[rapidsos-deploy]], [[wp-env-smoke-test]], and [[wordpress-org-submission]] to match the docs, including the Bricks carousel section (`layout="carousel" wrapper="slides"`, `.cb-lw-slide`, `data-cb-lw-ref`) that [[rapidsos-deploy]] was missing. Also retitled `wiki/index.md`. Entity pages and SCHEMA were already updated by the same commit.

## [2026-06-12] create | Wiki bootstrapped

Initial wiki structure created. Ready for first ingest.

## [2026-06-12] migrate | No docs/ directory

Legacy docs ingest skipped: no `docs/` folder with markdown files at bootstrap time.

## [2026-06-12] update | Plugin entity pages

Added wiki entities for the existing plugin: [[wordpress-plugin]], [[sanity-logo-soup]], [[gutenberg-block-balanced-logos]].

## [2026-06-14] ingest | SAR + Adversarial review docs

Ingested `docs/SAR-REVIEW.md` → [[sar-review]] and `docs/ADVERSARIAL-REVIEW.md` → [[adversarial-review]]. Both are review/audit artifacts that explain the security and architecture decisions baked into [[wordpress-plugin]].

## [2026-06-14] ingest | WordPress.org submission guide

Ingested `docs/WORDPRESS-ORG-SUBMISSION.md` → [[wordpress-org-submission]]. Promoted to `wiki/guides/` because the maintainer-facing release flow (ZIP build, SVN deploy, Plugin Check) is operational, not source material.

## [2026-06-17] ingest | wp-env smoke test + RapidSOS deploy

Ingested `docs/SMOKE-TEST.md` → [[wp-env-smoke-test]] and `docs/RAPIDSOS-DEPLOY.md` → [[rapidsos-deploy]]. Both are operational runbooks (test scaffolding, Cloudways/Bricks deploy) so they live in `wiki/guides/` alongside [[wordpress-org-submission]]. `docs/WORDPRESS-ORG-SUBMISSION.md` was already mirrored from 2026-06-14 (script flagged it again because it only checks `wiki/sources/`; left as-is).

## [2026-07-31] update | Uncommitted `balanced-logos` → `cb-lw` rename continues

No commits in the last 24h, but the working tree carries a large uncommitted internal rename: `balanced-logos.php` → `cooper-bold-logo-wrangler.php`, `CB_Balanced_Logos_*` classes → `CB_LW_*` under `includes/`, and `src/shared/to-balanced-logos-props.js` → `to-lw-props.js`. The only file touched within the window is `src/view.js`: imports `toLwProps` from `./shared/to-lw-props`, hydrating class now `cb-lw-hydrating`, debug opt-in now `window.CB_LW_DEBUG` / `?cb_lw_debug=1`, and the console log prefix is `[Cooper Bold Logo Wrangler]`. Noted on [[gutenberg-block-balanced-logos]]; commit pending.

## [2026-07-31] migrate | Rename to Cooper Bold Logo Wrangler complete (uncommitted)

Product rename locked: **Cooper Bold Logo Wrangler**. Bootstrap `cooper-bold-logo-wrangler.php`, PHP `CB_LW_*`, block `cooper-bold/logo-wrangler`, shortcode `[logo_wrangler]`, CSS/data `cb-lw` (legacy Balanced Logos attrs/classes still emitted). CPT stays `cb_logo_collection`. Bugbot findings fixed (release ZIP script, legacy block editor assets, dual data attrs). Release ZIP smoke: `dist/cooper-bold-logo-wrangler-1.3.0.zip`. Updated [[wordpress-plugin]], [[gutenberg-block-balanced-logos]], SCHEMA. Commit still pending.

## [2026-06-22] session | WP.org prep v1.2.12 close-out

WP.org submission prep complete for **1.2.12** (Splide vendored, PNG assets, readme). Local wp-env verified: strip post 4 (`demo-50-logos`), carousel page 62, Plugin Check pass on release-shaped run. Lesson filed: same-origin Media Library logos required (external URLs CORS-fail hydration). Next: push `main`, upload ZIP, SVN secrets after approval, tag `v1.2.12`. See `AI_SESSION_MEMORY.md` and `docs/WORDPRESS-ORG-SUBMISSION.md`.

## [2026-08-05] lint | Dead link fix + ingest re-flag note

Fixed dead wikilink `[[gutenberg-block-logo-soup]]` → `[[gutenberg-block-balanced-logos]]` in [[sanity-logo-soup]] (block entity page was renamed earlier without updating this backlink). Lint now clean. Ingest script re-flags `docs/RAPIDSOS-DEPLOY.md`, `docs/SMOKE-TEST.md`, `docs/WORDPRESS-ORG-SUBMISSION.md` because it only checks `wiki/sources/` — all three are already mirrored as guides ([[rapidsos-deploy]], [[wp-env-smoke-test]], [[wordpress-org-submission]]); no re-ingest needed.
