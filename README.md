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

### Local testing with wp-env

Requires [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or another Docker engine wp-env can use).

**Path with spaces:** `@wordpress/env` breaks when the repo directory name contains spaces. This checkout uses a **no-spaces symlink** in `.wp-env.json`. On your machine, create the same link once (adjust if your clone lives elsewhere):

```bash
ln -sfn "/path/to/Logo Soup WP Plugin" "/path/to/logo-soup-wp-plugin"
```

Or clone the repo into a directory **without** spaces and set `"plugins": [ "." ]` in `.wp-env.json`.

```bash
npm ci
npm run build
npm run wp-env:start
```

- Site: [http://localhost:8888](http://localhost:8888)
- Admin: [http://localhost:8888/wp-admin](http://localhost:8888/wp-admin) — user `admin`, password `password`
- Activate **Cooper Bold Logo Soup** on the [Plugins](http://localhost:8888/wp-admin/plugins.php) screen, then create a page and insert the **Logo Soup** block to smoke-test the strip.

Stop the environment:

```bash
npm run wp-env:stop
```

Reset containers and volumes: `npm run wp-env:clean`.

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

## Releasing to WordPress.org

Deployments run automatically from `main` when you push a **semver tag** (`v1.0.0`, `v1.0.1`, …) or **publish a GitHub Release** (not a pre-release). The workflow uses [10up/action-wordpress-plugin-deploy](https://github.com/10up/action-wordpress-plugin-deploy) and respects `.distignore` so dev scaffolding never ships to SVN.

### Before the plugin is approved

The workflow is safe to merge before WordPress.org grants SVN access. Until approval, runs will fail at the SVN step — that is expected. After approval, add the secrets below and cut a tag to deploy.

New plugin submission: [WordPress.org plugin developer handbook](https://developer.wordpress.org/plugins/wordpress-org/).

### GitHub secrets (repository → Settings → Secrets and variables → Actions)

| Secret | Value |
| --- | --- |
| `SVN_USERNAME` | Your WordPress.org username |
| `SVN_PASSWORD` | A [WordPress.org application password](https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/) (not your login password) |

Do not commit these values. Each secret is write-once in GitHub; you cannot view them again after saving.

### Cut a release

1. Bump the version in **three** places so they stay aligned:
   - `cooper-bold-logo-soup.php` plugin header `Version:` and `CB_LOGO_SOUP_VERSION`
   - `readme.txt` `Stable tag:` and changelog section
   - `src/block/block.json` `version` (rebuild updates `build/block/block.json`)
2. Commit on `main`: `git commit -m "chore: release 1.0.1"`
3. Tag and push:
   ```bash
   git tag v1.0.1
   git push origin main
   git push origin v1.0.1
   ```
4. Watch **Actions → Deploy to WordPress.org** on GitHub.

**Tag convention:** `v` + semver (`v1.0.0`). The tag name becomes the WordPress.org plugin version tag in SVN.

### What ships to WordPress.org

Included: `cooper-bold-logo-soup.php`, `includes/`, `build/`, `readme.txt`, `LICENSE`.

Excluded (via `.distignore`): `src/`, `node_modules/`, AI/wiki docs, `.cursor/`, SimpleMem, npm manifests, and other dev-only files. CI runs `npm ci && npm run build` before deploy so `build/` is fresh even though compiled assets are also committed on `main`.

### WordPress.org assets (banners, icons, screenshots)

Add images under `.wordpress-org/` in the repo root. The deploy action copies that folder to the SVN `assets/` directory (not plugin trunk). See [10up’s asset-update action](https://github.com/10up/action-wordpress-plugin-asset-update) to refresh readme/assets between tagged releases.

## WordPress.org release

Automated deploy runs on tags matching `v*` (see `.github/workflows/deploy.yml`). Before the first deploy:

1. Complete [docs/WORDPRESS-ORG-SUBMISSION.md](docs/WORDPRESS-ORG-SUBMISSION.md) — submit the plugin ZIP, pass review, receive SVN access.
2. Add GitHub repository secrets **`SVN_USERNAME`** and **`SVN_PASSWORD`** (wordpress.org application password).
3. Tag from `main` after `npm run build`:

```bash
git tag -a v1.0.0 -m "Release 1.0.0"
git push origin v1.0.0
```

**Pre-approval:** The first listing requires manual WordPress.org review. Automated SVN deploy only works after your plugin is approved and secrets are set.

Build a local submission ZIP (respects `.distignore`):

```bash
./scripts/build-release-zip.sh
```

**Plugin Check:** On a staging site, install the [Plugin Check](https://wordpress.org/plugins/plugin-check/) plugin and scan the release ZIP before tagging.

## License

- Plugin: **GPL-2.0-or-later** (WordPress.org compatible)
- Logo Soup dependency: **MIT** (GPL-compatible)

## Credits

- [Sanity Labs Logo Soup](https://github.com/sanity-labs/logo-soup)
- [The Logo Soup Problem](https://www.sanity.io/blog/the-logo-soup-problem) — background on the normalization approach
