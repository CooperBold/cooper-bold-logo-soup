# AI Session Memory — Balanced Logos WP Plugin

Dated log for session context and handoffs.

**Import into SimpleMem:**  
`python3 simplemem_cli.py import-ai-session --path AI_SESSION_MEMORY.md`

---

## [2026-06-12] Bootstrap | Set up project for AI

**Branch:** (initial, no commits yet)

**Completed:**

- Initialized git repository and `.gitignore` (`.env`, Python cache, `uncommitted/`).
- Bootstrapped **SimpleMem** (`simplemem_client.py`, `simplemem_cli.py`, `docs/simplemem/`, namespace `balanced-logos-wp-plugin`, local backend).
- Bootstrapped **LLM Wiki** (`wiki/` with SCHEMA, index, log, stub entity `wordpress-plugin`).
- Added **Context7** (`.cursor/context7-libraries.md`, `.cursor/rules/context7.mdc`).
- Created **CrewAI** planner in `crewai/`.
- Wrote **AGENTS.md**, **AI_RUNBOOK.md**, and this file.
- **Legacy docs:** No `docs/` directory — ingest skipped.
- **Obsidian:** Already installed on macOS.

**State:**

- Plugin code **already present**: `balanced-logos.php`, `includes/`, `src/block/`, npm + `@sanity-labs/logo-soup`.
- AGENTS.md / runbook / wiki entities updated to match real architecture after bootstrap.

**Decisions:**

- SimpleMem: local-only (`SIMPLEMEM_BACKEND=local`), store at `docs/simplemem`.
- SimpleMem namespace `balanced-logos-wp-plugin` (repo slug); user-facing plugin name is **Balanced Logos** (vendor: Cooper Bold).

**Next steps:**

1. `npm install && npm run build` on a dev machine; verify block + shortcode on a WP site.
2. First git commit and push to `CooperBold/balanced-logos` when ready.
3. `crewai install` + `crewai/.env` for feature planning.
4. Add wiki guides for local WP dev and release zip if needed.

---

## [2026-06-22] WP.org prep v1.2.12 | Carousel + Plugin Check verified

**Branch:** `main` (ahead of `origin/main` by 2 commits)

**Git status (close-out):**

- Uncommitted: `readme.txt` (Tested up to **7.0**), `includes/class-cb-balanced-logos-renderer.php` (translators comment from Plugin Check)
- Untracked: `.wp-seed-logos/`, local verification screenshots
- Two WP.org prep commits on `main` not yet pushed

**Completed:**

- WordPress.org submission prep for **v1.2.12**: Splide vendored under `lib/splide/`, directory PNGs in `assets/`, readme polish
- Local **wp-env** Docker verified (Colima + `WP_ENV_CORE` local core checkout)
- Strip smoke test: post **4** with collection `demo-50-logos` (50 logos)
- Carousel smoke test: page **62** (`Balanced Logos Carousel Test`) — vendored Splide only, no CDN
- **Plugin Check** pass on release-shaped run (`--slug=balanced-logos` with dev-dir excludes)
- Carousel + vendored Splide confirmed via Playwright on `http://localhost:8888/?page_id=62`

**Decisions / lessons:**

- Local logo URLs must be **same-origin** (Media Library). External URLs fail logo-soup hydration due to canvas **CORS** — use uploaded media or same-host URLs in wp-env.

**Next steps:**

1. `git push origin main` (2 unpushed commits)
2. Build release ZIP (`./scripts/build-release-zip.sh`) and upload to [Add your plugin](https://wordpress.org/plugins/developers/add/)
3. After WP.org approval: add `SVN_USERNAME` / `SVN_PASSWORD` GitHub secrets; tag and push `v1.2.12`
4. Optional: commit Plugin Check micro-fixes (`readme.txt` Tested up to 7.0, renderer translators comment)
