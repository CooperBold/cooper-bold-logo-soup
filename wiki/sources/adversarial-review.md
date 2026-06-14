---
title: Adversarial Review (2026-06-13)
type: source
created: 2026-06-14
updated: 2026-06-14
tags: [review, security, sanitization, build, enqueue]
aliases: [adversarial-review]
sources:
  - "[[wordpress-plugin]]"
status: active
---

# Adversarial Review (2026-06-13)

Summary of the multi-round adversarial review (Nous, `anthropic/claude-opus-4.8`) of the plugin diff against the spec at `/tmp/cb-logo-soup-spec.md`. Source: `docs/ADVERSARIAL-REVIEW.md`.

## Headline

Three rounds of `diff` mode and one round of `tests` mode. **Verdict: REQUEST_CHANGES** — did not converge to APPROVE. The plugin is "safer to ship" after fixes; no automated test suite exists.

## Issues fixed in this session

| Issue | File(s) | Fix |
|-------|---------|-----|
| Editor `RangeControl` max values below spec | `src/block/edit.js` | `baseSize` max 256, `gap` max 96 |
| `densityFactor` parity (editor/frontend) | `includes/class-cb-logo-soup-renderer.php`, `src/shared/to-soup-props.js` | Emit `0` when `densityAware` is false |
| `shortcode_atts` filter tag hardcoded | `includes/class-cb-logo-soup.php` | Pass actual `$tag` to `shortcode_atts` |
| Permissive `rgb()`/`hsl()` regex | renderer + `to-soup-props.js` | Tighter character class |
| Widget / FSE enqueue gap | `class-cb-logo-soup-assets.php`, renderer | `enqueue_frontend()` at render time |
| Style handle collision | `class-cb-logo-soup-assets.php` | Distinct `VIEW_STYLE_HANDLE` |
| Style path fallback | `class-cb-logo-soup-assets.php` | Try `view.scss.*` then `view.css` |
| JSON in shortcodes | `class-cb-logo-soup.php`, `readme.txt` | `base64:` prefix + readme note |
| PHP bootstrap | `cooper-bold-logo-soup.php` | Early PHP version gate; `declare(strict_types=1)` first |
| Block registration dirs | `class-cb-logo-soup.php` | Candidate dir loop for `block.json` |

## False positives verified locally

- `build/block/block.json` IS in the release ZIP (`scripts/build-release-zip.sh`).
- `view.scss.css` is produced by `wp-scripts build src/view.scss`.
- `file:../index.js` in `block.json` is correct when block registers from `build/block/`.

## Deferred / open work

- PHPUnit + Jest harness (no test runner in repo; `.distignore` reserves `/tests`).
- `javascript:` and `data:` URL variants (low risk after `esc_url_raw`).
- Third-party `densityFactor: 0` semantics from logo-soup.
- Src-only dev fallback paths in `block.json` (production uses `build/`).
- Duplicate logo URLs with different links (rare; index-based mapping with wrap-reset mitigates re-render).

## Verification commands

```bash
npm run build                # pass
npm run lint:js              # pass
php -l cooper-bold-logo-soup.php  # pass
bash scripts/build-release-zip.sh # build/block/block.json + view.scss.css in ZIP
```

## Recommended before 1.0.1

Add PHPUnit tests for `CB_Logo_Soup_Renderer` sanitization and a minimal Jest test for `sanitizePreviewConfig` / `toSoupProps` parity.

## Related

- [[wordpress-plugin]]
- [[sar-review]]
- [[wordpress-org-submission]]
