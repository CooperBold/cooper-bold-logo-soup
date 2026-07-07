---
title: RapidSOS deploy (Balanced Logos)
type: guide
created: 2026-06-17
updated: 2026-06-17
tags: [wordpress, deploy, cloudways, bricks, shortcode]
aliases: [rapidsos-deploy]
sources:
  - "[[wordpress-plugin]]"
status: active
---

# RapidSOS deploy (Balanced Logos)

Operational runbook for deploying **Balanced Logos** to the RapidSOS Cloudways staging site (`https://wordpress-1533060-6135168.cloudwaysapps.com/`) which uses the **Bricks** builder rather than Gutenberg. Source: `docs/RAPIDSOS-DEPLOY.md`.

## Build release ZIP

```bash
cd "/Users/thedao/Repos/Balanced Logos WP Plugin"
npm ci && npm run build && ./scripts/build-release-zip.sh
```

Output: `dist/balanced-logos-1.1.0.zip`. The version follows `CB_BALANCED_LOGOS_VERSION` in `balanced-logos.php`.

## Install on Cloudways

Pick one:

1. **WP Admin** — Plugins → Add New → Upload Plugin → choose the ZIP → Replace/update if prompted.
2. **SFTP** — Upload ZIP to server, unzip into `wp-content/plugins/balanced-logos/` (overwrite existing).
3. **SSH** — `wp plugin install /path/to/balanced-logos-1.1.0.zip --activate` (if WP-CLI available).

Activate **Balanced Logos** if not already active.

## Logo Collections (primary workflow on Bricks)

RapidSOS staging uses **Bricks**, not Gutenberg. Manage logos in wp-admin instead of hand-built shortcode URLs:

1. **Balanced Logos → Add New** — name the collection (e.g. "RapidSOS Partners").
2. Add logos via **Add / edit logos** (Media Library), set alt text and optional links, drag to reorder.
3. Tune **Collection Settings** (base size, gap, etc.) and **Publish**.
4. Copy the shortcode from the sidebar or **Balanced Logos → All Collections** list.
5. In Bricks, add a **Shortcode** element with e.g. `[balanced_logos collection="rapidsos-partners"]`.

Typical page: `/commercial-building-safety/`. **Do not** remove or conflate the existing **Splide marquee** on that page; Balanced Logos is a separate logo strip.

## Legacy inline logos (shortcode one-offs)

Inline `logos="url|alt,..."` shortcodes still work for one-offs, but collections are preferred:

```text
[balanced_logos collection="rapidsos-partners" base_size="40" gap="32" class="rapidsos-partner-logos"]
```

## Verify after deploy

1. Load the target page (hard refresh or incognito).
2. View page source and confirm:
   - `data-cb-balanced-logos` on the wrapper (`.cb-balanced-logos`)
   - Script handle `balanced-logos-view` → `build/view.js`
3. Logos render as a normalized strip (not a marquee).
4. After changing a collection, purge cache and hard-refresh — blocks/shortcodes load collection data at render time.

## Purge cache

After any plugin or shortcode change, purge **all** layers:

- Cloudways (Varnish / application cache)
- **Breeze** (if enabled)
- **Bricks** → Settings → Performance → regenerate CSS/cache

Re-check page source if the strip is missing or stale.

## Rollback checklist

- [ ] Deactivate plugin or restore previous `balanced-logos` folder from backup/ZIP
- [ ] Purge Cloudways, Breeze, and Bricks cache again
- [ ] Confirm page source no longer shows `data-cb-balanced-logos` (or shows expected prior version)
- [ ] Confirm Splide marquee on `commercial-building-safety` still works unchanged

## Related

- [[wordpress-plugin]]
- [[wp-env-smoke-test]]
- [[wordpress-org-submission]]
