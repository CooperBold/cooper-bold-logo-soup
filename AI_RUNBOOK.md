# Balanced Logos WP Plugin — AI Runbook

> Read this first before changing plugin code.

---

## What this project is

**Balanced Logos** is a WordPress plugin that displays client and partner logos in a balanced strip using [**@sanity-labs/logo-soup**](https://www.npmjs.com/package/@sanity-labs/logo-soup) for normalization and layout.

Editors can use:

- **Gutenberg block** `cooper-bold/balanced-logos` (`src/block/`)
- **Shortcode** `[balanced_logos]` with matching attributes (legacy: `[logo_soup]`, `[cooper-bold-logo-soup]`)

---

## Tech stack

| Layer | Technology |
| --- | --- |
| WordPress | 6.4+ |
| PHP | 7.4+, `declare(strict_types=1)` |
| JS | Block editor (`src/block/`), front-end (`src/view.js`) |
| npm package | `@sanity-labs/logo-soup` ^1.2.2 |
| Build | `@wordpress/scripts` → `build/index.js`, `build/view.js` |
| AI tooling | SimpleMem, CrewAI, wiki, Context7 |

---

## Project structure

```
balanced-logos.php     # Plugin header + bootstrap
includes/
  class-cb-balanced-logos.php           # Singleton coordinator
  class-cb-balanced-logos-assets.php    # Script/style enqueue
  class-cb-balanced-logos-renderer.php  # HTML + data for logo soup
  class-cb-balanced-logos-shortcode.php # [balanced_logos]
  class-cb-balanced-logos-block.php     # Block registration + render_callback
src/
  block/                           # block.json, edit.js, editor styles
  view.js                          # Front-end logo-soup init
build/                             # Compiled assets (npm run build)
wiki/                              # LLM wiki (Obsidian)
crewai/                            # Planning crew
docs/simplemem/                    # SimpleMem store (committed)
```

---

## Key workflows

### Change block editor UI

1. Edit `src/block/edit.js` (and SCSS if needed).
2. `npm run start` for watch, or `npm run build` before commit.
3. Test block in wp-admin; verify front-end via renderer.

### Change front-end behavior

1. Edit `src/view.js` and/or `includes/class-cb-balanced-logos-renderer.php`.
2. Rebuild JS; clear caches on test site.

### Add shortcode attribute

1. Update shortcode handler in `class-cb-balanced-logos-shortcode.php`.
2. Mirror attribute in `block.json` and editor if exposed in UI.
3. Pass through renderer consistently.

### Local testing with wp-env

Requires Docker (Colima on macOS). Repo path has spaces — `.wp-env.json` mounts a **no-spaces symlink**:

```bash
# One-time (adjust source path if clone differs):
ln -s "/Users/thedao/Repos/Balanced Logos WP Plugin" /Users/thedao/Repos/balanced-logos-wp-plugin
```

Shallow `git clone` into `~/.wp-env` can fail `index-pack` on this host — use a local core checkout under `$HOME`:

```bash
export WP_ENV_CORE=/Users/thedao/wp-env-wordpress-core
npm ci && npm run build && npm run wp-env:start
```

- Site: `http://localhost:8888` (admin / password)
- WP-CLI plugin slug: `balanced-logos-wp-plugin` (directory name from mount)
- Stop: `npm run wp-env:stop`

**Smoke URLs (local):**

| Test | URL |
| --- | --- |
| Strip (`demo-50-logos`) | `http://localhost:8888/?p=4` |
| Carousel (`layout="carousel" wrapper="full"`) | `http://localhost:8888/?page_id=62` |
| Collection slug | `demo-50-logos` |

**CORS / logo URLs:** Balanced Logos hydrates logos via canvas. In local dev, logo `src` must be **same-origin** (WordPress Media Library uploads). External URLs (e.g. `picsum.photos`) fail hydration with CORS errors — seed collections with uploaded PNGs, not hotlinked images.

### Plugin Check (release-shaped)

Install [Plugin Check](https://wordpress.org/plugins/plugin-check/) in wp-env, then run against the mounted plugin with release-like excludes:

```bash
export WP_ENV_CORE=/Users/thedao/wp-env-wordpress-core
npx wp-env run cli -- wp plugin check balanced-logos-wp-plugin \
  --slug=balanced-logos \
  --exclude-directories=dist,crewai,docs,.cursor,.github,tests,node_modules,vendor,uncommitted,scripts \
  --exclude-files=composer-setup.php,composer.phar,.DS_Store,.env,.env.example,.wp-env.json,.phpunit.result.cache,.gitignore,.distignore,phpunit.xml.dist,AI_RUNBOOK.md,AI_SESSION_MEMORY.md,AGENTS.md \
  --ignore-warnings
```

Pass = no errors (warnings may remain). Splide must load from `lib/splide/` only — no jsDelivr on frontend.

### Release checklist

1. `npm run build` and `npm run lint:js`
2. Bump `CB_BALANCED_LOGOS_VERSION` and plugin header version
3. `./scripts/build-release-zip.sh` → `dist/balanced-logos-*.zip`
4. Plugin Check (above) on wp-env or staging
5. Tag `v*` and push — see **WordPress.org submission** below

**WordPress.org submission:** Full checklist in `docs/WORDPRESS-ORG-SUBMISSION.md` (version **1.2.12**, SVN secrets, directory assets). Wiki mirror: `wiki/guides/wordpress-org-submission.md`.

---

## Conventions

- Class prefix: `CB_Balanced_Logos_*`
- Hooks: minimal — `plugins_loaded`, `init` for block
- SimpleMem namespace: `balanced-logos-wp-plugin`
- GitHub (header): `https://github.com/CooperBold/balanced-logos`

---

## Commands

```bash
npm install
npm run build
npm run start
npm run lint:js

python3 simplemem_cli.py import-ai-session --path AI_SESSION_MEMORY.md

cd crewai && crewai install && crewai run
```

---

## AI bootstrap (2026-06-12)

SimpleMem, wiki, Context7, CrewAI, AGENTS.md added. No legacy `docs/*.md` to ingest. Obsidian already on macOS.

---

## Where to look

| Need | Location |
| --- | --- |
| Agent rules | AGENTS.md |
| Block schema | `src/block/block.json` |
| Render pipeline | `class-cb-balanced-logos-renderer.php` |
| Context7 IDs | `.cursor/context7-libraries.md` |
| Session log | AI_SESSION_MEMORY.md |
