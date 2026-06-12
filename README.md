# Cooper Bold Logo Soup

WordPress plugin that wraps [@sanity-labs/logo-soup](https://github.com/sanity-labs/logo-soup) for harmonious partner/client logo displays. Built for sites like [RapidSOS](https://rapidsos.com) and distributed as a free Cooper Bold plugin.

## What Logo Soup does

Logo Soup is a small framework-agnostic library that **normalizes logo visuals** so mixed brand assets look balanced together. It measures each image, detects visual weight and density, and scales logos to a harmonious strip. It is **not** a scrolling marquee.

## Plugin architecture

| Layer | Choice |
| --- | --- |
| Normalization engine | `@sanity-labs/logo-soup` (MIT) |
| Block editor | Gutenberg block built with `@wordpress/scripts` |
| Frontend | React hydration via `src/view.js` + dynamic PHP render |
| Shortcode | `[logo_soup]` sharing the same renderer and view script |
| Asset loading | View script enqueued only when block/shortcode renders |

The React component is bundled rather than ported to vanilla JS to preserve upstream behavior with minimal maintenance.

## Requirements

- WordPress 6.4+
- PHP 7.4+
- Node.js 18+ (development builds only)

## Install

### Site install (built assets included)

1. Copy the plugin folder to `wp-content/plugins/cooper-bold-logo-soup/`.
2. Activate **Cooper Bold Logo Soup** in **Plugins**.

### Development

```bash
cd wp-content/plugins/cooper-bold-logo-soup
npm install
npm run build   # production
npm run start   # watch mode
```

## Usage

### Gutenberg block

1. Insert the **Logo Soup** block (`cooper-bold/logo-soup`).
2. Click **Add logos** and pick images from the media library.
3. Adjust normalization and layout in the block sidebar.

### Shortcode

```text
[logo_soup logos="/wp-content/uploads/acme.svg|Acme,/wp-content/uploads/globex.svg|Globex" gap="28" base_size="48"]
```

**Shortcode attributes**

| Attribute | Default | Description |
| --- | --- | --- |
| `logos` | *(required)* | Comma-separated `url\|alt` pairs |
| `base_size` | `48` | Target logo height in px |
| `scale_factor` | `0.5` | Normalization strength (0–1) |
| `contrast_threshold` | `10` | Background detection sensitivity |
| `density_aware` | `true` | Adjust for visual density |
| `density_factor` | `0.5` | Density adjustment strength |
| `crop_to_content` | `false` | Crop to detected content bounds |
| `background_color` | *(empty)* | CSS color for measurement context |
| `align_by` | `visual-center-y` | `bounds`, `visual-center`, `visual-center-x`, `visual-center-y` |
| `gap` | `28` | Spacing between logos in px |
| `class` | *(empty)* | Extra CSS class on the wrapper |

### RapidSOS example

Add partner logos via the block on a landing page, or drop in a shortcode inside a Bricks/HTML module:

```text
[logo_soup logos="/wp-content/uploads/2025/01/partner-a.svg|Partner A,/wp-content/uploads/2025/01/partner-b.svg|Partner B" base_size="40" gap="32" class="rapidsos-partner-logos"]
```

## Project layout

```text
cooper-bold-logo-soup.php    # Plugin bootstrap
includes/                    # PHP classes (assets, block, shortcode, renderer)
src/block/                   # Gutenberg block source
src/view.js                  # Frontend React mount script
build/                       # Compiled JS/CSS (generated)
readme.txt                   # WordPress.org readme
```

## License

- Plugin: **GPL-2.0-or-later** (WordPress.org compatible)
- Logo Soup dependency: **MIT** (GPL-compatible)

## Credits

- [Sanity Labs Logo Soup](https://github.com/sanity-labs/logo-soup)
- [The Logo Soup Problem](https://www.sanity.io/blog/the-logo-soup-problem) — background on the normalization approach
