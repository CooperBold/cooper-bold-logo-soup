# Logo Soup WP Plugin — AI Runbook

> Read this first before changing plugin code.

---

## What this project is

**Cooper Bold Logo Soup** is a WordPress plugin that displays client and partner logos in a balanced strip using [**@sanity-labs/logo-soup**](https://www.npmjs.com/package/@sanity-labs/logo-soup) for normalization and layout.

Editors can use:

- **Gutenberg block** `cooper-bold/logo-soup` (`src/block/`)
- **Shortcode** `[logo_soup]` with matching attributes

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
cooper-bold-logo-soup.php     # Plugin header + bootstrap
includes/
  class-cb-logo-soup.php           # Singleton coordinator
  class-cb-logo-soup-assets.php    # Script/style enqueue
  class-cb-logo-soup-renderer.php  # HTML + data for logo soup
  class-cb-logo-soup-shortcode.php # [logo_soup]
  class-cb-logo-soup-block.php     # Block registration + render_callback
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

1. Edit `src/view.js` and/or `includes/class-cb-logo-soup-renderer.php`.
2. Rebuild JS; clear caches on test site.

### Add shortcode attribute

1. Update shortcode handler in `class-cb-logo-soup-shortcode.php`.
2. Mirror attribute in `block.json` and editor if exposed in UI.
3. Pass through renderer consistently.

### Release checklist (draft)

1. `npm run build` and `npm run lint:js`
2. Bump `CB_LOGO_SOUP_VERSION` and plugin header version
3. Zip plugin folder or tag release on GitHub

---

## Conventions

- Class prefix: `CB_Logo_Soup_*`
- Hooks: minimal — `plugins_loaded`, `init` for block
- SimpleMem namespace: `logo-soup-wp-plugin`
- GitHub (header): `https://github.com/CooperBold/cooper-bold-logo-soup`

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
| Render pipeline | `class-cb-logo-soup-renderer.php` |
| Context7 IDs | `.cursor/context7-libraries.md` |
| Session log | AI_SESSION_MEMORY.md |
