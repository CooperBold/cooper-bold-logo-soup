---
title: Wiki Log
type: log
created: 2026-06-12
updated: 2026-06-22
---

# Wiki Log

> Chronological record of wiki operations. Append-only.
>
> Format: `## [YYYY-MM-DD] verb | Subject`
>
> Verbs: `ingest`, `query`, `lint`, `update`, `create`, `migrate`, `session`

## [2026-06-12] create | Wiki bootstrapped

Initial wiki structure created. Ready for first ingest.

## [2026-06-12] migrate | No docs/ directory

Legacy docs ingest skipped: no `docs/` folder with markdown files at bootstrap time.

## [2026-06-12] update | Plugin entity pages

Added wiki entities for the existing plugin: [[wordpress-plugin]], [[sanity-logo-soup]], [[gutenberg-block-logo-soup]].

## [2026-06-14] ingest | SAR + Adversarial review docs

Ingested `docs/SAR-REVIEW.md` → [[sar-review]] and `docs/ADVERSARIAL-REVIEW.md` → [[adversarial-review]]. Both are review/audit artifacts that explain the security and architecture decisions baked into [[wordpress-plugin]].

## [2026-06-14] ingest | WordPress.org submission guide

Ingested `docs/WORDPRESS-ORG-SUBMISSION.md` → [[wordpress-org-submission]]. Promoted to `wiki/guides/` because the maintainer-facing release flow (ZIP build, SVN deploy, Plugin Check) is operational, not source material.

## [2026-06-17] ingest | wp-env smoke test + RapidSOS deploy

Ingested `docs/SMOKE-TEST.md` → [[wp-env-smoke-test]] and `docs/RAPIDSOS-DEPLOY.md` → [[rapidsos-deploy]]. Both are operational runbooks (test scaffolding, Cloudways/Bricks deploy) so they live in `wiki/guides/` alongside [[wordpress-org-submission]]. `docs/WORDPRESS-ORG-SUBMISSION.md` was already mirrored from 2026-06-14 (script flagged it again because it only checks `wiki/sources/`; left as-is).

## [2026-06-22] session | WP.org prep v1.2.12 close-out

WP.org submission prep complete for **1.2.12** (Splide vendored, PNG assets, readme). Local wp-env verified: strip post 4 (`demo-50-logos`), carousel page 62, Plugin Check pass on release-shaped run. Lesson filed: same-origin Media Library logos required (external URLs CORS-fail hydration). Next: push `main`, upload ZIP, SVN secrets after approval, tag `v1.2.12`. See `AI_SESSION_MEMORY.md` and `docs/WORDPRESS-ORG-SUBMISSION.md`.
