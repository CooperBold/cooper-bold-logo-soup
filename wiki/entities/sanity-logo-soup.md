---
title: Sanity Labs Logo Soup (npm)
type: entity
created: 2026-06-12
updated: 2026-06-12
tags: [npm, sanity, logos]
aliases: ["@sanity-labs/logo-soup"]
status: active
---

# @sanity-labs/logo-soup

npm library that normalizes logo images and lays them out in a balanced strip.

## What it is

Dependency in `package.json` (^1.2.2). Used from `src/view.js` on the front end after PHP renders markup/data.

## How it's used in this project

- Front-end entry: `src/view.js` (built to `build/view.js`)
- Block attributes in `src/block/block.json` mirror tuning params: `baseSize`, `scaleFactor`, `gap`, `densityAware`, etc.

## Related

- [[wordpress-plugin]]
- [[gutenberg-block-logo-soup]]
