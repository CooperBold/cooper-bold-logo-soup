---
title: Frontend assets enqueue from PHP only
type: decision
summary: block.json declares editor assets only; CB_LW_Assets enqueues view JS/CSS (and Splide) when a block or shortcode actually renders.
status: active
created: 2026-10-19
updated: 2026-10-19
last_verified: 2026-10-19
sources: [AGENTS.md, src/block/block.json, includes/class-cb-lw-assets.php, docs/SAR-REVIEW.md, docs/ADVERSARIAL-REVIEW.md, readme.txt]
tags: [enqueue, performance, architecture]
---

# Frontend assets enqueue from PHP only

**Status:** active (SAR run 2026-06-12; extended in 1.2.3–1.2.4).

## Decision

`src/block/block.json` has `editorScript` and `editorStyle` only, with no `viewScript` and no `style`. `CB_LW_Assets` registers `cooper-bold-logo-wrangler-view` and its style, and enqueues them:

- at render time (`enqueue_frontend()`, called by the renderer, which covers widgets, FSE templates, and builder output), and
- early on singular pages that contain any block name or shortcode alias, including in Bricks' `_bricks_page_content_2` meta (`maybe_enqueue()`).

Splide is enqueued only for `wrapper="full"` carousels ([[carousel-and-splide]]).

## Why

- With `viewScript` in `block.json`, core enqueues the script for blocks and the shortcode path enqueues it again (the SAR "double enqueue risk").
- Pages without logos load nothing, so there is no global enqueue.
- Template parts and builders are not in `post_content`. The adversarial review found the widget/FSE gap, which led to render-time enqueue. Bricks pages needed meta scanning (1.2.4).

## Consequences

- Do not add `viewScript` or `style` to `block.json` (rule in `AGENTS.md`).
- Any new place that renders logos must call `CB_LW_Assets::enqueue_frontend()`.

## Related

- [[rendering-pipeline]] · [[0005-bundle-upstream-react-component]] · [[adversarial-review]]
