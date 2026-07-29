# AI Session Memory — Cooper Bold Logo Strip Normalizer WP Plugin

Dated log for session context and handoffs.

**Import into SimpleMem:**  
`python3 simplemem_cli.py import-ai-session --path AI_SESSION_MEMORY.md`

---

## [2026-06-12] Bootstrap | Set up project for AI

**Branch:** (initial, no commits yet)

**Completed:**

- Initialized git repository and `.gitignore` (`.env`, Python cache, `uncommitted/`).
- Bootstrapped **SimpleMem** (`simplemem_client.py`, `simplemem_cli.py`, `docs/simplemem/`, namespace `cooper-bold-logo-strip-normalizer-wp-plugin`, local backend).
- Bootstrapped **LLM Wiki** (`wiki/` with SCHEMA, index, log, stub entity `wordpress-plugin`).
- Added **Context7** (`.cursor/context7-libraries.md`, `.cursor/rules/context7.mdc`).
- Wrote **AGENTS.md**, **AI_RUNBOOK.md**, and this file.
- **Legacy docs:** No `docs/` directory — ingest skipped.
- **Obsidian:** Already installed on macOS.

**State:**

- Plugin code **already present**: `cooper-bold-logo-strip-normalizer.php`, `includes/`, `src/block/`, npm + `@sanity-labs/logo-soup`.
- AGENTS.md / runbook / wiki entities updated to match real architecture after bootstrap.

**Decisions:**

- SimpleMem: local-only (`SIMPLEMEM_BACKEND=local`), store at `docs/simplemem`.
- SimpleMem namespace `cooper-bold-logo-strip-normalizer-wp-plugin` (repo slug); user-facing plugin name is **Cooper Bold Logo Strip Normalizer** (vendor: Cooper Bold).

**Next steps:**

1. `npm install && npm run build` on a dev machine; verify block + shortcode on a WP site.
2. First git commit and push to `CooperBold/cooper-bold-logo-strip-normalizer` when ready.
3. `Cursor planning install` + `Cursor planning/.env` for feature planning.
4. Add wiki guides for local WP dev and release zip if needed.

---

## [2026-06-22] WP.org prep v1.2.12 | Carousel + Plugin Check verified

**Branch:** `main` (ahead of `origin/main` by 2 commits)

**Git status (close-out):**

- Uncommitted: `readme.txt` (Tested up to **7.0**), `includes/class-cb-cblsn-renderer.php` (translators comment from Plugin Check)
- Untracked: `.wp-seed-logos/`, local verification screenshots
- Two WP.org prep commits on `main` not yet pushed

**Completed:**

- WordPress.org submission prep for **v1.2.12**: Splide vendored under `lib/splide/`, directory PNGs in `assets/`, readme polish
- Local **wp-env** Docker verified (Colima + `WP_ENV_CORE` local core checkout)
- Strip smoke test: post **4** with collection `demo-50-logos` (50 logos)
- Carousel smoke test: page **62** (`Cooper Bold Logo Strip Normalizer Carousel Test`) — vendored Splide only, no CDN
- **Plugin Check** pass on release-shaped run (`--slug=cooper-bold-logo-strip-normalizer` with dev-dir excludes)
- Carousel + vendored Splide confirmed via Playwright on `http://localhost:8888/?page_id=62`

**Decisions / lessons:**

- Local logo URLs must be **same-origin** (Media Library). External URLs fail logo-soup hydration due to canvas **CORS** — use uploaded media or same-host URLs in wp-env.

**Next steps:**

1. `git push origin main` (2 unpushed commits)
2. Build release ZIP (`./scripts/build-release-zip.sh`) and upload to [Add your plugin](https://wordpress.org/plugins/developers/add/)
3. After WP.org approval: add `SVN_USERNAME` / `SVN_PASSWORD` GitHub secrets; tag and push `v1.2.12`
4. Optional: commit Plugin Check micro-fixes (`readme.txt` Tested up to 7.0, renderer translators comment)

---

## [2026-07-10] Push v1.2.15 rename | Splide upgrade check

**Branch:** `main` @ `60aab65` (synced with `origin/main`)

**Completed:**

- Confirmed rename commit: **v1.2.15** — plugin renamed **Logo Soup by Cooper Bold → Cooper Bold Logo Strip Normalizer** (text domain, shortcode, block, CSS classes, contributor `nicholaseymann`).
- Pushed `main` to `origin` (`3afa998..60aab65`) — includes rename + wiki auto-commit. No PR (direct to main).
- Confirmed Splide vendors are already latest on npm: `@splidejs/splide@4.1.4`, `@splidejs/splide-extension-auto-scroll@0.5.3`. No upgrade needed.

**State:**

- Working tree clean; `main` matches `origin/main`.
- GitHub remote: `CooperBold/cooper-bold-logo-soup` (repo slug still legacy; user-facing name is Cooper Bold Logo Strip Normalizer).

**Decisions:**

- Do **not** bump Splide for WP.org “out of date libraries” on auto-scroll — false positive vs unreleased git branch; bump only when npm publishes a newer version (`lib/splide/README.md`).

**Next steps:**

1. Build release ZIP for **v1.2.15** and continue WP.org submission / SVN if not already live.
2. Update `docs/WORDPRESS-ORG-SUBMISSION.md` version refs from 1.2.12 → 1.2.15 if still stale.
3. Optional: rename GitHub repo slug to match Cooper Bold Logo Strip Normalizer when convenient.
