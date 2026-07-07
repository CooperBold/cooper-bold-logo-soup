# RapidSOS deploy — Balanced Logos

**Staging:** https://wordpress-1533060-6135168.cloudwaysapps.com/

## Build release ZIP

From plugin repo root:

```bash
npm ci && npm run build && ./scripts/build-release-zip.sh
```

Output: `dist/balanced-logos-1.2.1.zip` (version follows `balanced-logos.php`).

## Install on Cloudways

Pick one:

1. **WP Admin** — Plugins → Add New → Upload Plugin → choose the ZIP → Replace/update if prompted.
2. **SFTP** — Upload ZIP to server, unzip into `wp-content/plugins/balanced-logos/` (overwrite existing).
3. **SSH** — `wp plugin install /path/to/balanced-logos-1.2.1.zip --activate` (if WP-CLI available).

Activate **Balanced Logos** if not already active.

## Logo Collections (primary workflow)

RapidSOS staging uses **Bricks**, not Gutenberg. Manage logos in wp-admin instead of hand-built shortcode URLs:

1. **Balanced Logos → Add New** — name the collection (e.g. "Law Enforcement Partners").
2. Add logos via **Add / edit logos** (Media Library), set alt text and optional links, drag to reorder.
3. Tune **Collection Settings** — set **Layout** to **Carousel** for Splide sliders, or **Strip** for a single normalized row.
4. **Publish** and copy the shortcode from the sidebar or **Balanced Logos → All Collections** list.
5. In Bricks, add a **Shortcode** element (see Bricks carousel section below).

## Bricks carousel (law-enforcement and similar pages)

RapidSOS logo bands use **Bricks Nested Slider** (Splide) with class `logo-slider-slide` on each slide. The site footer snippet `rapidsos-logo-slider-fix` (see RapidSOS repo `deploy/wordpress/logo-slider-autoscroll-snippet.php`) enables smooth Auto Scroll on those sliders.

### Problem

A default Balanced Logos shortcode renders **one** wrapper with all logos inside → Bricks treats the whole strip as **one** carousel slide.

### Solution — carousel layout + slides wrapper

Use carousel mode so each normalized logo becomes its own Splide slide:

```text
[balanced_logos collection="law-enforcement-partners" layout="carousel" wrapper="slides"]
```

Or set **Layout → Carousel** on the collection and add `wrapper="slides"` when nesting in Bricks.

**Markup output (slides wrapper):**

- Hidden reference strip (cross-logo normalization)
- One `<li class="splide__slide logo-slider-slide cb-balanced-logos-slide">` per logo

### Bricks setup steps

1. Edit the page in Bricks (e.g. `/public-safety/law-enforcement/`).
2. Open the existing **Nested Slider** (Splide) component — or add one with `autoWidth`, no arrows/pagination.
3. Remove any old shortcode that outputs a single logo strip **inside one slide**.
4. Select the **Slider** element (not an individual slide) so you can edit the slide list / structure.
5. Add a **Shortcode** element as a **direct child of the slider** (sibling to slides, or via Bricks structure that allows raw HTML in `.splide__list`).
   - If Bricks wraps the shortcode in an extra div, use a **Code** element instead and paste the shortcode output area at the `.splide__list` level.
6. Paste: `[balanced_logos collection="your-slug" layout="carousel" wrapper="slides"]`
7. Ensure each slide keeps class `logo-slider-slide` (plugin adds this automatically).
8. Save, purge cache (see below), hard-refresh.

### Standalone carousel (no Bricks slider)

For a self-contained carousel (shortcode only):

```text
[balanced_logos collection="your-slug" layout="carousel"]
```

Outputs full Splide markup (`.cb-balanced-logos-carousel.splide` + track + slides). `view.js` initializes Splide when the theme already loads it; RapidSOS Auto Scroll applies when the `logo-slider-slide` class is present inside `.brxe-slider-nested.splide`.

### Verify carousel

1. View page source — multiple `splide__slide` elements with `data-cb-balanced-logos-slide`.
2. Hidden `data-cb-balanced-logos-ref` present for normalization.
3. Each visible slide contains one logo image after hydration.
4. Slider scrolls smoothly (footer snippet active — search source for `rapidsos-logo-slider-fix`).

## Bricks shortcode (strip mode)

Default strip layout for a single normalized row:

```text
[balanced_logos collection="rapidsos-partners" base_size="40" gap="32" class="rapidsos-partner-logos"]
```

### Builder preview (strip)

Bricks builder iframe often does **not** run `view.js`, so the strip shows **server-rendered** placeholder markup instead of the hydrated LogoSoup React tree. As of **1.2.1**, that SSR output mirrors the hydrated DOM:

- `.cb-balanced-logos-inner` → inner `div` (LogoSoup row) → `span` per logo → `img` (or `a` > `img` when linked)
- Melanie can target **`.cb-balanced-logos-inner > div > span`** in Bricks custom CSS for grid layout — works in builder preview and on the live site (before and after hydration).

**Optional workaround:** set the Bricks shortcode element to **Don't render in builder** if you only need accurate normalization in the canvas (live site still hydrates normally).

Carousel mode uses separate slide markup; see carousel section above.

## Verify after deploy

1. Load the target page (hard refresh or incognito).
2. View page source and confirm:
   - `data-cb-balanced-logos` or `data-cb-balanced-logos-ref` on the wrapper
   - Script handle `balanced-logos-view` → `build/view.js`
3. Logos render as expected (strip or one logo per carousel slide).
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
- [ ] Confirm existing Splide marquees on other pages still work unchanged
