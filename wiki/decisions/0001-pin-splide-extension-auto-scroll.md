---
title: Pin splide-extension-auto-scroll at 0.5.3
type: decision
created: 2026-08-09
updated: 2026-08-09
tags: [splide, carousel, wordpress-org, dependencies]
status: active
---

# Pin `splide-extension-auto-scroll` at 0.5.3

## Context

WP.org's plugin check flags bundled/npm libraries as "out of date". For the carousel feature ([[gutenberg-block-balanced-logos]]), the check compares `splide-extension-auto-scroll` against the upstream repo's development branch.

## Decision

Stay on `splide-extension-auto-scroll@0.5.3` and document why in `readme.txt` (1.3.0 changelog): 0.5.3 is the **latest published npm release** — the upstream repo's last commit was 2022-09-06, and the "out of date libraries" finding is comparing against a development branch with no new tagged release.

## Consequences

- No upgrade path until upstream tags a new release; the readme.txt changelog entry serves as the rebuttal for WP.org review.
- Carousel Auto Scroll compatibility (RapidSOS `.logo-slider-slide` integration) remains on a known-good pinned version.
