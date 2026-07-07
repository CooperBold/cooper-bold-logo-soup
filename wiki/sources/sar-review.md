---
title: SAR Review (2026-06-12)
type: source
created: 2026-06-14
updated: 2026-06-14
tags: [sar, spec, attack-repair, security, architecture]
aliases: [sar-review]
sources:
  - "[[wordpress-plugin]]"
status: active
---

# SAR Review (2026-06-12)

Summary of the Spec-Attack-Repair (`sar`) run that produced the current plugin architecture. Source: `docs/SAR-REVIEW.md`. Full log: `~/.hermes/spec-attack-repair/logs/20260612T164702`.

## SAR workflow notes

The `sar` CLI run completed in stages after partial failures:

1. **Spec** stage succeeded on retry with `--timeout 300`.
2. **Builder** stage required a `CURSOR_AGENT_BIN` wrapper with `-f` (workspace trust).
3. **Attack** stage re-ran with `--adversary-model cursor` after a Nous timeout.
4. **Repair** and **judge** stages used `cursor-agent`.

## Spec highlights (what SAR demanded)

- Gutenberg block + shortcode sharing one renderer.
- Conditional frontend asset loading (no global enqueue).
- Sanitized attributes, escaped output, mandatory alt text.
- All key logo-soup props exposed in block and shortcode.
- WordPress.org `readme.txt`, GPL-2.0-or-later, PHP 7.4+ activation gate.

> **Spec drift:** the auto-generated spec used abstract prop ranges (e.g. `alignBy: width|height|none`) that do not match the upstream library. Implementation keeps native logo-soup defaults and enums (`visual-center-y`, `baseSize: 48`, etc.) while adopting SAR security and architecture wins.

## Attack findings (all rated major)

| Issue | Fix |
|-------|-----|
| JSON shortcode `logos` bypassed sanitization | Route JSON through `sanitize_logos()` |
| Editor preview ≠ frontend (alt, links, clamps, colors) | Shared `sanitizePreviewConfig()` in JS |
| Broken images only handled on frontend | `onError` + `console.warn` in shared `renderImage` |
| Invalid CSS named colors accepted (Candidate B) | Named-color allowlist in PHP + JS |
| Duplicate URL link collision | Index-based link lookup in `renderImage` |
| Block wrapper styles stripped | Merge `style` from `get_block_wrapper_attributes()` |
| Double enqueue risk | Omit `viewScript`/`style` from `block.json`; PHP `maybe_enqueue()` only |

## Judge verdict

**Winner: Candidate C** — dynamic PHP block + shortcode, shared renderer with SSR placeholder `<img>`s, thin React adapter, `has_block()` / `has_shortcode()` asset detection, `src/shared/to-balanced-logos-props.js` for editor/frontend parity, `WeakMap` re-mount guard in `view.js`.

Candidate A and B were rejected (missing JS color sanitization in repair; no WeakMap guard).

## What this repo actually shipped

| Area | Change |
|------|--------|
| Architecture | Consolidated bootstrap in `includes/class-cb-balanced-logos.php`; renderer + assets split retained |
| Security | `sanitize_logos()`, `javascript:` link rejection, named-color allowlist, JSON shortcode sanitization |
| Accessibility | Alt basename fallback, `aria-label` when multiple logos, per-logo alt in inspector |
| Editor parity | `src/shared/to-balanced-logos-props.js` — `sanitizePreviewConfig()` + `toBalancedLogosProps()` |
| Frontend | Placeholder images, `view.scss`, `WeakMap` mount guard, conditional enqueue |
| Shortcodes | `[balanced_logos]` (primary) + `[balanced-logos]` alias |
| Build | Multi-entry via `package.json` scripts (no `webpack.config.js`) |
| Docs | `LICENSE`, PHP 7.4 activation hook, `AGENTS.md` enqueue convention |
| Defaults | Restored upstream logo-soup values after SAR repair drift |

## Verification

```bash
npm run build                # pass
npm run lint:js              # pass
gzip -c build/view.js | wc -c  # 5143 bytes (≤ 20 KB)
```

## Caveats from the judge

- Asset detection scans `post_content` only — FSE template parts outside the post body may need future extension.
- Named-color allowlists in PHP and JS must stay in sync if extended.
- `renderImage` index assumes logo-soup renders logos in array order.

## Related

- [[wordpress-plugin]]
- [[adversarial-review]]
- [[wordpress-org-submission]]
