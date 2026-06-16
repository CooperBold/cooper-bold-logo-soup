# wp-env smoke test — Logo Soup

**Date:** 2026-06-15  
**Environment:** macOS, Docker via Colima (`docker context`: colima), plugin root `/Users/thedao/Repos/Logo Soup WP Plugin`

## Summary

| Step | Result |
|------|--------|
| Docker available | **Pass** — Colima context; containers run successfully |
| `npm ci && npm run build` | **Pass** |
| `npm run wp-env:start` | **Pass** — dev site `http://localhost:8888` (used `WP_ENV_CORE=/Users/thedao/wp-env-wordpress-core` because shallow `git clone` into `~/.wp-env` fails index-pack on this host; core under `$HOME` mounts correctly in Colima) |
| Plugin activation | **Pass** — plugin active as `logo-soup-wp-plugin` (directory name from repo mount). `wp plugin activate cooper-bold-logo-soup` not applicable (slug differs); no PHP errors on activate/list |
| `wp plugin list --status=active` (logo) | **Pass** — `logo-soup-wp-plugin` active, v1.0.1 |
| Published shortcode post | **Pass** — post ID 4, title “Logo Soup Test” |
| `curl` home HTTP status | **Pass** — `200` |
| Shortcode frontend markup | **Pass** — `data-cb-logo-soup` JSON on `.cb-logo-soup`; `cooper-bold-logo-soup-view-js` → `build/view.js` enqueued |
| PHPUnit (`vendor/bin/phpunit`) | **Pass** (prior run) — 13 tests, 24 assertions |
| Jest (`npm test`) | **Pass** (prior run) — 11 tests |
| Release zip (`./scripts/build-release-zip.sh`) | **Pass** (prior run) — 21 files, no tests/composer/vendor |
| `npm run wp-env:stop` | **Pass** (after smoke test) |

## Path with spaces (committed fix)

`@wordpress/env` splits plugin paths on spaces when `"plugins": [ "." ]`, so lifecycle activation fails for `Logo Soup WP Plugin`.

**Fix:** create a no-spaces symlink and point `.wp-env.json` at it (committed on this machine as `/Users/thedao/Repos/logo-soup-wp-plugin`). WP-CLI slug is `logo-soup-wp-plugin`.

```bash
ln -sfn "/Users/thedao/Repos/Logo Soup WP Plugin" "/Users/thedao/Repos/logo-soup-wp-plugin"
npm run wp-env:start   # no .wp-env.override.json
```

Remove any local `.wp-env.override.json` from earlier smoke runs — it overrides `.wp-env.json`.

**Re-verify (2026-06-15):** `npm run wp-env:start` (default config), `logo-soup-wp-plugin` active, `curl` → HTTP 200, `npm run wp-env:stop`.


## wp-env smoke commands (2026-06-15)

```bash
cd "/Users/thedao/Repos/Logo Soup WP Plugin"
npm ci && npm run build
# If git clone into ~/.wp-env fails index-pack, use a local core checkout under $HOME:
export WP_ENV_CORE=/Users/thedao/wp-env-wordpress-core
npm run wp-env:start
npx wp-env run cli wp plugin list --status=active | grep -i logo
npx wp-env run cli wp post create --post_title='Logo Soup Test' --post_status=publish \
  --post_content='[logo_soup logos="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png|Google"]'
curl -s -o /dev/null -w "%{http_code}" http://localhost:8888/
curl -s "http://localhost:8888/?p=POST_ID" | grep -E 'data-cb-logo-soup|cooper-bold-logo-soup-view'
npm run wp-env:stop
```

**Pass criteria:** published page renders `.cb-logo-soup` with `data-cb-logo-soup`, placeholder `<img>` tags, and `cooper-bold-logo-soup-view` / `build/view.js` in output.

## Colima note

WordPress containers may show `Created` until started; if `localhost:8888` refuses connections after `wp-env start`, run `docker start <project>-wordpress-1` or re-run `wp-env start`.

## Release zip verification (2026-06-15)

```bash
./scripts/build-release-zip.sh
unzip -l dist/cooper-bold-logo-soup-1.0.1.zip
```

**Pass criteria:**

- Contains `build/` (index.js, view.js, block.json, CSS)
- Contains `includes/`, `cooper-bold-logo-soup.php`, `readme.txt`, `LICENSE`
- Does **not** contain `tests/`, `composer.json`, `composer.phar`, `vendor/`, `package.json`, `phpunit.xml.dist`

## Automated tests (no Docker)

```bash
composer install
vendor/bin/phpunit
npm test
```

See `tests/CB_Logo_Soup_Renderer_Test.php` and `src/shared/to-soup-props.test.js`.
