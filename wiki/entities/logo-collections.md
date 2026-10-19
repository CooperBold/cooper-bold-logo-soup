---
title: Logo Collections (cb_logo_collection)
type: entity
summary: The reusable logo-set custom post type, its meta keys (with legacy fallbacks), REST route, and admin UI.
status: active
created: 2026-10-19
updated: 2026-10-19
last_verified: 2026-10-19
sources: [includes/class-cb-lw-collections.php, readme.txt, "commit ee4d016", "RapidSOS repo deploy/wordpress/logo-soup/README.md"]
tags: [cpt, admin, collections, rest]
---

# Logo Collections

Logo Collections are the main way editors use the plugin, especially on page builders. You build a named set of Media Library logos once, then reference it with `[logo_wrangler collection="slug"]` or `id="123"`, or pick it in the block sidebar. Added in 1.1.0 (commit `ee4d016`, 2026-06-16).

## Storage

| Item | Value | Source |
|---|---|---|
| Post type | `cb_logo_collection` (name kept through every rename) | `CB_LW_Collections::POST_TYPE` |
| Logos meta | `_cb_lw_logos`: ordered array of `{id, url, alt, link}` | `META_LOGOS` |
| Settings meta | `_cb_lw_settings`: baseSize, gap, layout, etc. | `META_SETTINGS` |
| Legacy meta, read only as fallback | `_cb_balanced_logos_*`, then `_cb_logo_soup_*` | `META_*_LEGACY_KEYS`, `get_meta_array()` |
| REST | `GET /wp-json/cb-lw/v1/collections`, needs `edit_posts`, published only, **max 100**, ordered by title | `register_rest_routes()` |
| Admin menu | Top-level **Logo Wrangler** | `register_admin_menu()` |

## Legacy meta behavior

- **Read:** the new key wins whenever it exists, even if it is empty. Legacy keys are used only when `_cb_lw_*` does not exist yet (`metadata_exists`).
- **Write:** saving a collection writes `_cb_lw_*` and **deletes** both legacy keys, "so an intentional empty save cannot resurrect old logos" (code comment in `save_post`).
- Effect: old collections keep rendering after an upgrade with no migration. But once a collection has been saved under 1.3.x, any outside tool that writes `_cb_logo_soup_logos` is silently ignored. RapidSOS has such tools; see [[rapidsos-site]].

## Admin UX facts

- Live preview in the editor uses the same sanitizer as the frontend (`src/admin/collection-preview.js`, `to-lw-props.js`). 1.1.11 fixed a preview/frontend mismatch.
- One-click copy for the shortcode on the edit screen and list table (1.1.1).
- There is no bulk import (no CLI, no write REST). RapidSOS filled a 70-logo collection with a one-shot WPCode snippet instead (RapidSOS repo `deploy/wordpress/logo-soup/`).
- Cooper Bold credit in the wp-admin footer on these screens only (`class-cb-lw-admin-branding.php`, 1.1.6–1.1.10).

## Related

- [[rendering-pipeline]] · [[wordpress-plugin]] · [[rapidsos-site]] · [[gotchas]]
