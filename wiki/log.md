---
title: Wiki Log
type: log
created: 2026-06-12
updated: 2026-08-05
---

# Wiki Log

> Chronological record of wiki operations. Append-only.
>
> Format: `## [YYYY-MM-DD] verb | Subject`
>
> Verbs: `ingest`, `query`, `lint`, `update`, `create`, `migrate`, `session`

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
