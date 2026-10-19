---
title: Ship src/ and package.json in the release ZIP
type: decision
summary: Since 1.3.1 the WP.org ZIP includes unminified source and the build manifest, and readme.txt points to the public repo.
status: active
created: 2026-10-19
updated: 2026-10-19
last_verified: 2026-10-19
sources: [.distignore, readme.txt, tests/CB_LW_Bootstrap_Test.php, "commit 385fbad", "WP.org automated review email 2026-08-06"]
tags: [wordpress-org, release, compliance]
---

# Ship `src/` and `package.json` in the release ZIP

**Status:** active since 1.3.1 (committed in `385fbad`, 2026-08-24).

## Context

WP.org's automated review on 2026-08-06 failed the plugin under Guidelines 1 and 4. `build/index.js`, `build/view.js`, and `build/collection-preview.js` were minified, the ZIP had no matching source, and the readme linked only the upstream logo-soup repo. See [[wporg-review-thread]].

## Decision

- `.distignore` no longer excludes `/src`, `/package.json`, or `/package-lock.json` (comment: "Ship src/ + package.json so WP.org can match compiled assets").
- `readme.txt` gained `== Source code ==`, which links the public GitHub repo, maps each `src/**` entry to its `build/**` output, and gives the rebuild command (`npm install && npm run build`). It also gained `== Third-party libraries ==`.
- `tests/CB_LW_Bootstrap_Test.php` asserts that the readme names the repo and the `src/view.js` / `src/admin/collection-preview.js` entries, so the section cannot drift silently.

## Alternatives

Linking the repo only, without shipping source, was allowed by the email ("You may either include the source files ... or provide a link"). We did both to give the reviewer no reason to push back.

## Consequences

- When you add a new build entry in `package.json` `build`, add it to `readme.txt` `== Source code ==` as well.
- Older docs that say the ZIP excludes `src/` and `package.json` are stale: `README.md` "What ships", `docs/SMOKE-TEST.md`, and [[wp-env-smoke-test]] before this upgrade.
- `node_modules/` and `.wp-env.json` are still excluded.

## Related

- [[wordpress-org-submission]] · [[0002-migrate-legacy-plugin-path-activate-only]]
