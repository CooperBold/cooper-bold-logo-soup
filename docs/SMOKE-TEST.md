# wp-env smoke test — Cooper Bold Logo Soup

**Date:** 2026-06-15  
**Environment:** macOS agent shell (`/Users/thedao/Repos/Logo Soup WP Plugin`)

## Summary

| Step | Result |
|------|--------|
| Docker available | **Blocked** — `docker` not on PATH; Docker Desktop not installed |
| `brew install --cask docker` | **Blocked** — cask downloaded but `sudo` link step failed (no interactive password) |
| `npm ci && npm run build` | **Pass** |
| PHPUnit (`vendor/bin/phpunit`) | **Pass** — 13 tests, 24 assertions |
| Jest (`npm test`) | **Pass** — 11 tests |
| Release zip (`./scripts/build-release-zip.sh`) | **Pass** — 21 files, no tests/composer/vendor |
| `npm run wp-env:start` | **Skipped** — requires Docker |
| Plugin activation / page smoke test | **Skipped** — requires wp-env |
| `npm run wp-env:stop` | N/A |

## Blocker

```text
$ which docker
docker not found

$ brew install --cask docker
# Docker.app downloaded, but linking /usr/local/bin/docker requires sudo password.
# Install rolled back; Docker.app removed.
```

`@wordpress/env` (configured in `.wp-env.json`) needs Docker Desktop running. Homebrew can install the cask, but the agent shell cannot complete the install without an interactive `sudo` prompt.

## User action to unblock smoke test

1. Install Docker Desktop manually:
   - `brew install --cask docker` (enter password when prompted), **or**
   - Download from https://www.docker.com/products/docker-desktop/
2. Open **Docker.app** and wait until the whale icon shows "Docker Desktop is running".
3. Run the manual smoke test commands below.

## Manual smoke test (when Docker is available)

Run from the plugin root:

```bash
npm ci && npm run build
npm run wp-env:start
npx wp-env run cli wp plugin activate cooper-bold-logo-soup
npx wp-env run cli wp post create --post_type=page --post_status=publish \
  --post_title='Logo Soup Smoke Test' \
  --post_content='<!-- wp:cooper-bold/logo-soup {"logos":[{"url":"https://via.placeholder.com/120x48.png","alt":"Placeholder"}]} /-->'
npx wp-env run cli wp post list --post_type=page --fields=ID,post_title,guid
# Open the guid URL in a browser; confirm no PHP errors and view.js loads.
npm run wp-env:stop
```

Shortcode alternative:

```text
[logo_soup logos="https://via.placeholder.com/120x48.png|Placeholder"]
```

**Pass criteria:** published page renders a `.cb-logo-soup` container with `data-cb-logo-soup` JSON, placeholder `<img>` tags, and `cooper-bold-logo-soup-view` script enqueued (view source or Network tab).

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

PHPUnit and Jest cover renderer sanitization parity without a live WordPress instance:

```bash
composer install   # or use existing vendor/
vendor/bin/phpunit
npm test
```

See `tests/CB_Logo_Soup_Renderer_Test.php` and `src/shared/to-soup-props.test.js`.
