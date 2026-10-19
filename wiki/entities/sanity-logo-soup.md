---
title: Sanity Labs Logo Soup (npm)
type: entity
summary: The upstream @sanity-labs/logo-soup library (pinned 1.3.0) and how the plugin wires its lifecycle hooks.
created: 2026-06-12
updated: 2026-10-19
last_verified: 2026-10-19
tags: [npm, sanity, logos]
aliases: ["@sanity-labs/logo-soup"]
status: active
sources: [package.json, src/shared/logo-soup-lifecycle.js, src/shared/to-lw-props.js, "commits 1468a42, a9168c2", AI_SESSION_MEMORY.md]
---

# @sanity-labs/logo-soup

npm library that normalizes logo images and lays them out in a balanced strip.

## What it is

Dependency in `package.json` (pinned **1.3.0**). Used from `src/view.js` on the front end after PHP renders markup/data. Lifecycle hooks (`onError`, `onNormalized`) wired via `src/shared/logo-soup-lifecycle.js` (failures logging + `cb-lw-soup-loading` on mount nodes). Custom `renderImage` in `to-lw-props.js` sets `crossOrigin="anonymous"` to match upstream measurement requests.

Pinned **exactly** (no caret) on purpose: upstream changes can shift measurements (1.3.0 changed the color-distance math, per `AI_SESSION_MEMORY.md` 2026-09-02 Risks). Failures always `console.warn`, not only in debug mode, so editors notice broken logo URLs (same entry, Decisions). React previews use the `useLogoSoupLifecycle` hook so `onError` keeps a stable identity across re-renders (commit `a9168c2`). Why the React component is bundled instead of ported: [[0005-bundle-upstream-react-component]].

## How it's used in this project

- Front-end entry: `src/view.js` (built to `build/view.js`)
- Block attributes in `src/block/block.json` mirror tuning params: `baseSize`, `scaleFactor`, `gap`, `densityAware`, etc.

## Related

- [[rendering-pipeline]] · [[0005-bundle-upstream-react-component]]
- [[wordpress-plugin]]
- [[gutenberg-block-balanced-logos|gutenberg-block (cooper-bold/logo-wrangler)]]
