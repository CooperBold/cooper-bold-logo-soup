# RapidSOS deploy — Cooper Bold Logo Soup

**Staging:** https://wordpress-1533060-6135168.cloudwaysapps.com/

## Build release ZIP

From plugin repo root:

```bash
npm ci && npm run build && ./scripts/build-release-zip.sh
```

Output: `dist/cooper-bold-logo-soup-1.1.0.zip` (version follows `cooper-bold-logo-soup.php`).

## Install on Cloudways

Pick one:

1. **WP Admin** — Plugins → Add New → Upload Plugin → choose the ZIP → Replace/update if prompted.
2. **SFTP** — Upload ZIP to server, unzip into `wp-content/plugins/cooper-bold-logo-soup/` (overwrite existing).
3. **SSH** — `wp plugin install /path/to/cooper-bold-logo-soup-1.1.0.zip --activate` (if WP-CLI available).

Activate **Cooper Bold Logo Soup** if not already active.

## Logo Collections (primary workflow)

RapidSOS staging uses **Bricks**, not Gutenberg. Manage logos in wp-admin instead of hand-built shortcode URLs:

1. **Logo Soup → Add New** — name the collection (e.g. "RapidSOS Partners").
2. Add logos via **Add / edit logos** (Media Library), set alt text and optional links, drag to reorder.
3. Tune **Collection Settings** (base size, gap, etc.) and **Publish**.
4. Copy the shortcode from the sidebar or **Logo Soup → All Collections** list.
5. In Bricks, add a **Shortcode** element with e.g. `[logo_soup collection="rapidsos-partners"]`.

Typical page: `/commercial-building-safety/` — **do not** remove or conflate the existing **Splide marquee** on that page; Logo Soup is a separate logo strip.

## Bricks shortcode (legacy inline logos)

Inline `logos="url|alt,..."` shortcodes still work for one-offs, but collections are preferred:

```text
[logo_soup collection="rapidsos-partners" base_size="40" gap="32" class="rapidsos-partner-logos"]
```

## Verify after deploy

1. Load the target page (hard refresh or incognito).
2. View page source and confirm:
   - `data-cb-logo-soup` on the wrapper (`.cb-logo-soup`)
   - Script handle `cooper-bold-logo-soup-view` → `build/view.js`
3. Logos render as a normalized strip (not a marquee).
4. After changing a collection, purge cache and hard-refresh — blocks/shortcodes load collection data at render time.

## Purge cache

After any plugin or shortcode change, purge **all** layers:

- Cloudways (Varnish / application cache)
- **Breeze** (if enabled)
- **Bricks** → Settings → Performance → regenerate CSS/cache

Re-check page source if the strip is missing or stale.

## Rollback checklist

- [ ] Deactivate plugin or restore previous `cooper-bold-logo-soup` folder from backup/ZIP
- [ ] Purge Cloudways, Breeze, and Bricks cache again
- [ ] Confirm page source no longer shows `data-cb-logo-soup` (or shows expected prior version)
- [ ] Confirm Splide marquee on `commercial-building-safety` still works unchanged
