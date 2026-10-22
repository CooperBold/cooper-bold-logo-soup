# Cooper Bold Logo Wrangler — Agent Notes

Claude Code loads this file through `.claude/CLAUDE.md`. Always-on rules are in `.claude/rules/`; before non-trivial work follow `.claude/rules/pre-task-retrieval.md`.

**Project wiki:** read `wiki/index.md` before non-trivial work; open pages it points to only when relevant.
After a repeated failure, check `wiki/gotchas.md`. Writing to the wiki: follow `wiki/SCHEMA.md`.

## What this plugin does

Wraps `@sanity-labs/logo-soup` for WordPress. **Cooper Bold Logo Wrangler** normalizes mixed logo images (size, density, visual center) into a balanced strip. It is not a marquee.

## Stack

- PHP 7.4+ plugin bootstrap in `cooper-bold-logo-wrangler.php` + `includes/` (only that file has the Plugin Name header). Legacy `balanced-logos.php` is a headerless loader that requires the canonical file when the old path is still listed in `active_plugins`. Migrate that entry only on user-initiated **activate** or **plugin update** hooks — never on `plugins_loaded`.
- Gutenberg block: `src/block/` → `build/index.js`
- Frontend hydration: `src/view.js` → `build/view.js`
- Build: `npm run build` (`@wordpress/scripts`; entry points are listed in the `build` script in `package.json`)

## Key conventions

- Block name: `cooper-bold/logo-wrangler`
- Shortcode: `[logo_wrangler]` — prefer `collection="slug"` or `id="123"` from Logo Collections CPT. Legacy aliases: `[balanced_logos]`, `[logo_soup]`, `[cooper-bold-logo-soup]`.
- Collections CPT: `cb_logo_collection` — admin menu **Logo Wrangler**
- Frontend mount selector: `[data-cb-lw]` on `.cb-lw-inner`
- Frontend CSS targeting: `.cb-lw-wrapper` for outer spacing/margins; `.cb-lw-inner` for the logo row (legacy `.cb-balanced-logos-wrapper` / `.cb-balanced-logos-inner` still emitted)
- View script handle: `cooper-bold-logo-wrangler-view`
- Assets enqueue only when block `render_callback` or shortcode runs

## Asset enqueue

- `block.json` declares **editor** assets only (`editorScript`, `editorStyle`). Do **not** add `viewScript` or `style` — frontend JS/CSS is registered and enqueued from `CB_LW_Assets::maybe_enqueue()` when `has_block()` or `[logo_wrangler]` (or legacy shortcodes) is present.
- Build entries: `npm run build` compiles `src/block/index.js`, `src/view.js`, and `src/view.scss` (no custom `webpack.config.js`).

## Logo count

No built-in cap on logos per collection, block, or shortcode. Admin users manage performance tradeoffs themselves.

## Layout modes

- `layout="strip"` (default) — one normalized row (`.cb-lw-wrapper` > `.cb-lw-inner`)
- `layout="carousel"` — one Splide slide per logo; use `wrapper="slides"` to nest inside Bricks nested sliders, or `wrapper="full"` for standalone Splide markup
- Carousel slides use class `logo-slider-slide` for RapidSOS Splide Auto Scroll compatibility

## Do not

- Commit `node_modules/` or secrets
- Port logo-soup to vanilla JS unless upstream API changes make React bundling untenable
- Add marquee/animation unless explicitly requested — out of scope for logo-soup

## Working rules

1. **Think before coding.** State assumptions, surface tradeoffs, ask before guessing.
2. **Simplicity first.** Minimum code that solves the problem; no speculative abstractions.
3. **Surgical changes.** Touch only what the task needs. Match the existing style.
4. **Read before you write.** Read the file's exports and its caller before adding to it.
5. **Fail loud.** If you can't be sure it worked, say so. Never report skipped work as done.
6. **Check after every change.** Run the type check / lint from "Commands" after each edit.
7. **Behavior changes ship with tests**, where a test runner exists (see "What can be verified").
8. **Never ratchet a test to match current output.** Find why it changed; decide whether the code or the test is wrong.

## Commands (tested)

Runtime: Node 22 locally (CI uses 20; no `engines`), PHP 8.5 locally (CI 8.2, plugin supports 7.4+).
`composer` is not on PATH: the repo's gitignored `composer.phar` is. · Last verified: 2026-10-22 on
`origin/main` (33a9a0b).

| Task | Command (run from root) | Notes |
| ---- | ----------------------- | ----- |
| Install JS | `npm ci` | ~3 min. |
| Install PHP | `php composer.phar install` | **Crashes (exit 139, segfault) on PHP 8.5** while fetching from Packagist. The main checkout already has `vendor/`; a worktree can borrow it (next row). |
| Test JS | `npm test` | Jest: 3 suites, 19 tests. |
| Test PHP | `vendor/bin/phpunit` (`npm run test:php`) | 44 tests, 107 assertions, OK. 80 PHP 8.5 "dynamic property" deprecation notices from `tests/bootstrap.php` stubs. In a worktree: `"/Users/thedao/Repos/Logo Soup WP Plugin/vendor/bin/phpunit"`. |
| Lint JS | `npx wp-scripts lint-js src admin` | ~2s. **Don't use `npm run lint:js`**: it also lints the vendored `lib/splide/js/*.min.js` and ran 14+ min without finishing. |
| Build | `npm run build` | ~5s. Output in `build/` is tracked; on 2026-10-22 it rebuilt byte-identical. |
| Verify gate | `npm run verify` (`bash .verify.sh`) | Jest + build. PASS. Doesn't run PHPUnit or lint. |
| Local WordPress | `WP_ENV_CORE=/Users/thedao/wp-env-wordpress-core npm run wp-env:start` (from `~/Repos/cooper-bold-logo-wrangler-wp-plugin`) | :8888 answers 200, plugin active. Stop: `npm run wp-env:stop`. Details: `wiki/guides/wp-env-smoke-test.md`. |
| Render check | `npx wp-env run cli -- wp eval 'echo do_shortcode("[logo_wrangler logos=\"https://example.com/a.png|A\"]");'` | Prints the `.cb-lw-wrapper` markup with `data-cb-lw`; saves nothing. |

Test in WP: insert block, add logos, confirm frontend strip renders; test shortcode on a classic page.

## Known baseline

`origin/main` 2026-10-22: **JS lint has 92 errors** in `src/` + `admin/` (73 `prettier/prettier`,
the rest jsdoc/`no-undef`/`no-unused-vars`). Jest, PHPUnit, build: clean.

## What can be verified

Jest (`src/**/*.test.js`), PHPUnit against WordPress stubs (`tests/`), the build, and rendering in the
local wp-env site. No automated browser test of the block editor.

## Running locally and logging in

- wp-env is a throwaway local WordPress; wp-admin uses wp-env's documented default login. Its data is local.
- `.wp-env.json` mounts the **absolute** path `/Users/thedao/Repos/cooper-bold-logo-wrangler-wp-plugin`
  (a symlink to the main checkout). wp-env started from a git worktree still runs the main checkout's code.

## Dev vs production gotchas

- `~/Repos/cooper-bold-logo-wrangler-wp-plugin`, `~/Repos/logo-soup-wp-plugin`, and
  `~/Repos/logo-soup-wp-plugin-smoke` are all **symlinks** to `~/Repos/Logo Soup WP Plugin`: one checkout, not four.
- Release ZIP contents and other traps: `wiki/gotchas.md`.

## Git and push safety

- Owner: Cooper Bold (`CooperBold/cooper-bold-logo-soup`). Default branch `main`.
- Push to origin only with Nicholas's OK. **Pushing a `v*` tag publishes to WordPress.org** (`.github/workflows/deploy.yml`, SVN).
- Nightly SimpleMem/wiki auto-commits land on `main`.
- Root `CLAUDE.md` is git-excluded (local tools rewrite it); the tracked entry point is `.claude/CLAUDE.md`.
