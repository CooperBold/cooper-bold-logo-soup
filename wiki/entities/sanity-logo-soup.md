---
title: Sanity Labs Logo Soup (npm)
type: entity
created: 2026-06-12
updated: 2026-09-02
tags: [npm, sanity, logos]
aliases: ["@sanity-labs/logo-soup"]
status: active
---

# @sanity-labs/logo-soup

npm library that normalizes logo images and lays them out in a balanced strip.

## What it is

Dependency in `package.json` (pinned **1.3.0**). Used from `src/view.js` on the front end after PHP renders markup/data. Lifecycle hooks (`onError`, `onNormalized`) wired via `src/shared/logo-soup-lifecycle.js` (failures logging + `cb-lw-soup-loading` on mount nodes). Custom `renderImage` in `to-lw-props.js` sets `crossOrigin="anonymous"` to match upstream measurement requests.

## How it's used in this project

- Front-end entry: `src/view.js` (built to `build/view.js`)
- Block attributes in `src/block/block.json` mirror tuning params: `baseSize`, `scaleFactor`, `gap`, `densityAware`, etc.

## Related

- [[wordpress-plugin]]
- [[gutenberg-block-balanced-logos|gutenberg-block (cooper-bold/logo-wrangler)]]
