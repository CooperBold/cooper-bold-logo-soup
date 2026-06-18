# Adversarial Review — Logo Soup

**Date:** 2026-06-13  
**Backend:** Nous (`anthropic/claude-opus-4.8`)  
**Artifact:** Full plugin diff from initial commit + uncommitted fixes  
**Spec:** `/tmp/cb-logo-soup-spec.md` (purpose, security, enqueue, WP.org readiness)

## Summary

| Phase | Rounds | Final verdict |
|-------|--------|---------------|
| `diff` mode | 3 (until-approve) | REQUEST_CHANGES |
| `tests` mode | 1 | REQUEST_CHANGES |

The loop did **not** converge to APPROVE after three diff rounds. Concrete, reproducible issues were fixed; several adversary findings were verified as **false positives** against a real `npm run build` output and release ZIP. Remaining gaps are mostly missing automated tests and unverified third-party library behavior.

## Round log (diff mode)

### Round 1 — REQUEST_CHANGES

**Findings (actionable):**

- Editor `RangeControl` max values below spec (`baseSize` 128 vs 256, `gap` 48 vs 96).
- `densityFactor` emitted when `densityAware` is false — PHP/JS/preview mismatch.
- `shortcode_atts` always used tag `logo_soup` even for `cooper-bold-logo-soup`.
- Permissive `rgb()`/`hsl()` regex.

**False positives (verified locally):**

- `build/block/block.json` missing from release — **ZIP contains it** (`scripts/build-release-zip.sh`).
- `view.scss.css` missing — **exists** after `wp-scripts build src/view.scss`.
- `file:../index.js` in `block.json` — **correct** when block registers from `build/block/`.

### Round 2 — REQUEST_CHANGES (after round-1 fixes)

**Additional findings:**

- FSE / widget enqueue gap (`get_post()` only).
- JSON shortcode `]` truncation (WordPress shortcode parser limitation).
- `renderImage` index / duplicate-URL link mapping.

**Fixes applied:**

- Render-time `CB_Logo_Soup_Assets::enqueue_frontend()` from renderer.
- `base64:` prefix for JSON logos in shortcodes; readme updated.
- Separate style handle; style registration fallback candidates.
- `renderIndex` with wrap-reset on re-render.

### Round 3 — REQUEST_CHANGES

**Remaining adversary concerns:**

- Src-fallback `block.json` paths (dev-only; production uses `build/block/`).
- No PHPUnit/Jest coverage.
- `densityFactor: 0` when disabled — library behavior unverified.
- `javascript:` blocking is prefix-only.

**Fixes applied:**

- PHP &lt; 7.4 guard before loading typed includes; admin notice.
- Restored JSON branch in `parse_logos` (for non-shortcode contexts); `base64:` documented for shortcodes.
- `declare(strict_types=1)` moved to first statement (fatal parse fix from tests mode).

## Tests mode (1 round) — REQUEST_CHANGES

No automated tests exist. Highest-priority gaps documented for future work:

- Sanitization parity (PHP `sanitize_attributes` vs JS `sanitizePreviewConfig`).
- `javascript:` / `data:` link schemes.
- `maybe_enqueue` + render-time enqueue paths.
- `view.js` mount / malformed JSON handling.

## Issues fixed in this session

| Issue | File(s) | Fix |
|-------|---------|-----|
| Editor range limits | `src/block/edit.js` | `baseSize` max 256, `gap` max 96 |
| `densityFactor` parity | `includes/class-cb-logo-soup-renderer.php`, `src/shared/to-soup-props.js` | Emit `0` when `densityAware` false |
| Shortcode filter tag | `includes/class-cb-logo-soup.php` | Pass actual `$tag` to `shortcode_atts` |
| Color regex | renderer + `to-soup-props.js` | Tighter `rgb`/`hsl` character class |
| Widget/FSE enqueue | `class-cb-logo-soup-assets.php`, renderer | `enqueue_frontend()` on render |
| Style handle collision | `class-cb-logo-soup-assets.php` | Distinct `VIEW_STYLE_HANDLE` |
| Style path fallback | `class-cb-logo-soup-assets.php` | Try `view.scss.*` then `view.css` |
| JSON shortcodes | `class-cb-logo-soup.php`, `readme.txt` | `base64:` prefix + docs |
| PHP bootstrap | `cooper-bold-logo-soup.php` | Early PHP version gate; `declare` first |
| Block registration | `class-cb-logo-soup.php` | Candidate dir loop |

## Issues deferred

| Issue | Rationale |
|-------|-----------|
| PHPUnit / Jest suite | No harness in repo yet; `.distignore` reserves `/tests` |
| `javascript:` variants (`JaVaScRiPt:`, `data:`) | Low risk after `esc_url_raw`; needs test-backed hardening |
| LogoSoup `densityFactor: 0` semantics | Third-party behavior; no regression observed |
| Src-only dev fallback paths | Production ships `build/`; CI runs `npm run build` before deploy |
| Duplicate logo URLs with different links | Rare; index-based mapping with wrap-reset mitigates re-render bugs |

## Verification performed

```bash
npm run build      # pass
npm run lint:js    # pass
php -l cooper-bold-logo-soup.php  # pass
bash scripts/build-release-zip.sh # build/block/block.json + view.scss.css in ZIP
```

## Final verdict

**REQUEST_CHANGES** (adversary did not APPROVE)

The plugin is **safer to ship** after fixes: sanitization parity improved, enqueue works when blocks render outside main post content, PHP bootstrap is valid, and release artifacts were verified. Ship readiness for WP.org is acceptable given committed `build/` output and deploy workflow.

**Recommended before 1.0.1:** Add PHPUnit tests for `CB_Logo_Soup_Renderer` sanitization and a minimal Jest test for `sanitizePreviewConfig` / `toSoupProps` parity.
