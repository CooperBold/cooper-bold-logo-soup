# RapidSOS deploy — Cooper Bold Logo Soup

**Staging:** https://wordpress-1533060-6135168.cloudwaysapps.com/

## Build release ZIP

From plugin repo root:

```bash
npm ci && npm run build && ./scripts/build-release-zip.sh
```

Output: `dist/cooper-bold-logo-soup-1.0.1.zip` (version follows `cooper-bold-logo-soup.php`).

## Install on Cloudways

Pick one:

1. **WP Admin** — Plugins → Add New → Upload Plugin → choose the ZIP → Replace/update if prompted.
2. **SFTP** — Upload ZIP to server, unzip into `wp-content/plugins/cooper-bold-logo-soup/` (overwrite existing).
3. **SSH** — `wp plugin install /path/to/cooper-bold-logo-soup-1.0.1.zip --activate` (if WP-CLI available).

Activate **Cooper Bold Logo Soup** if not already active.

## Bricks (not Gutenberg)

RapidSOS staging uses **Bricks**, not the block editor.

- Add a **Shortcode** element (not a Gutenberg block).
- Shortcode: `[logo_soup]` with attributes as needed (see `README.md` RapidSOS example).
- Typical page: `/commercial-building-safety/` — **do not** remove or conflate the existing **Splide marquee** on that page; Logo Soup is a separate logo strip.

## Verify after deploy

1. Load the target page (hard refresh or incognito).
2. View page source and confirm:
   - `data-cb-logo-soup` on the wrapper (`.cb-logo-soup`)
   - Script handle `cooper-bold-logo-soup-view` → `build/view.js`
3. Logos render as a normalized strip (not a marquee).

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
