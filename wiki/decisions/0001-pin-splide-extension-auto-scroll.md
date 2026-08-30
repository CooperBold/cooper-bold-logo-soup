---
title: Pin splide-extension-auto-scroll at 0.5.3
type: decision
created: 2026-08-09
updated: 2026-08-30
tags: [splide, carousel, wordpress-org, dependencies]
status: active
---

# Pin `splide-extension-auto-scroll` at 0.5.3

## Context

WP.org's plugin check flags bundled/npm libraries as "out of date". For the carousel feature ([[gutenberg-block-balanced-logos]]), the check compares `splide-extension-auto-scroll` against the upstream repo's development branch.

## Decision

Stay on `splide-extension-auto-scroll@0.5.3` and document why in `readme.txt`: 0.5.3 is the **latest published npm release** — the upstream repo's last commit was 2022-09-06, and the "out of date libraries" finding is comparing against a development branch with no new tagged release. Since v1.3.3 (commit `385fbad`) this documentation lives in a dedicated `== Third-party libraries ==` section of `readme.txt` (logo-soup MIT, Splide 4.1.4 MIT, auto-scroll 0.5.3 MIT with the same upstream-branch explanation), part of the WP.org review compliance work.

## Consequences

- No upgrade path until upstream tags a new release; the readme.txt changelog entry serves as the rebuttal for WP.org review.
- Carousel Auto Scroll compatibility (RapidSOS `.logo-slider-slide` integration) remains on a known-good pinned version.
