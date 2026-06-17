---
title: wp-env smoke test (Logo Soup)
type: guide
created: 2026-06-17
updated: 2026-06-17
tags: [wordpress, wp-env, docker, colima, smoke-test, ci]
aliases: [smoke-test, wp-env-smoke]
sources:
  - "[[wordpress-plugin]]"
status: active
---

# wp-env smoke test (Logo Soup)

End-to-end smoke test for **Logo Soup** in `@wordpress/env` (Docker). Verifies build, plugin activation, shortcode rendering, asset enqueue, release zip, and unit tests. Source: `docs/SMOKE-TEST.md` (recorded 2026-06-15).

## Pass criteria (one-line summary)

`npm ci && npm run build && npm run wp-env:start` → plugin active → shortcode post renders with `data-cb-logo-soup` and `cooper-bold-logo-soup-view` script → `wp-env:stop` clean.

## Step-by-step

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

**Pass:** published page renders `.cb-logo-soup` with `data-cb-logo-soup`, placeholder `<img>` tags, and `cooper-bold-logo-soup-view` → `build/view.js` in output.

## Path-with-spaces fix

`@wordpress/env` splits plugin paths on spaces when `"plugins": [ "." ]`, so lifecycle activation fails for `Logo Soup WP Plugin`. Symlink the repo to a no-spaces path and point `.wp-env.json` at it:

```bash
ln -sfn "/Users/thedao/Repos/Logo Soup WP Plugin" "/Users/thedao/Repos/logo-soup-wp-plugin"
npm run wp-env:start   # uses committed .wp-env.json; no override needed
```

WP-CLI slug is `logo-soup-wp-plugin` (from directory name). Remove any local `.wp-env.override.json` from earlier smoke runs — it overrides `.wp-env.json`.

## Colima notes

- macOS smoke runs use the `colima` docker context. Containers may show `Created` until started; if `localhost:8888` refuses connections after `wp-env start`, run `docker start <project>-wordpress-1` or re-run `wp-env start`.
- `WP_ENV_CORE` must live under `$HOME` (not `~/.wp-env`) because the default shallow clone fails `index-pack` on this host. A local core checkout under `$HOME` mounts correctly in Colima.

## Unit / lint tests (no Docker)

```bash
composer install
vendor/bin/phpunit      # PHPUnit — 13 tests, 24 assertions
npm test                # Jest — 11 tests
npm run lint:js
```

PHPUnit entry: `tests/CB_Logo_Soup_Renderer_Test.php`. Jest entry: `src/shared/to-soup-props.test.js`.

## Release zip verification

```bash
./scripts/build-release-zip.sh
unzip -l dist/cooper-bold-logo-soup-1.0.1.zip
```

**Pass criteria:**

- Contains `build/` (index.js, view.js, block.json, CSS)
- Contains `includes/`, `cooper-bold-logo-soup.php`, `readme.txt`, `LICENSE`
- Does **not** contain `tests/`, `composer.json`, `composer.phar`, `vendor/`, `package.json`, `phpunit.xml.dist`

## Recorded result (2026-06-15)

| Step | Result |
|------|--------|
| Docker available (Colima) | Pass |
| `npm ci && npm run build` | Pass |
| `npm run wp-env:start` | Pass |
| Plugin activation (`logo-soup-wp-plugin` v1.0.1) | Pass |
| Published shortcode post (ID 4) | Pass |
| `curl` home HTTP status | Pass (200) |
| Shortcode frontend markup (`data-cb-logo-soup`, `cooper-bold-logo-soup-view-js`) | Pass |
| PHPUnit (prior run) | Pass — 13 tests, 24 assertions |
| Jest (prior run) | Pass — 11 tests |
| Release zip (prior run) | Pass — 21 files, no tests/composer/vendor |
| `npm run wp-env:stop` | Pass |

## Related

- [[wordpress-plugin]]
- [[rapidsos-deploy]]
- [[wordpress-org-submission]]
