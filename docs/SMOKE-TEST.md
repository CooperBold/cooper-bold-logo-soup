# wp-env smoke test — Cooper Bold Logo Soup

**Date:** 2026-06-14  
**Environment:** macOS agent shell (`/Users/thedao/Repos/Logo Soup WP Plugin`)

## Summary

| Step | Result |
|------|--------|
| Docker available | **Blocked** — `docker` not found on PATH |
| `npm ci && npm run build` | **Pass** |
| PHPUnit (`vendor/bin/phpunit`) | **Pass** — 13 tests |
| Jest (`npm test`) | **Pass** — 11 tests |
| `npm run wp-env:start` | **Skipped** — requires Docker |
| Plugin activation / page smoke test | **Skipped** — requires wp-env |
| `npm run wp-env:stop` | N/A |

## Blocker

```text
$ docker info
command not found: docker
```

`@wordpress/env` (configured in `.wp-env.json`) needs Docker Desktop or an equivalent Docker engine. Without it, wp-env cannot start the WordPress + MySQL containers.

## What was attempted

1. `docker info` — failed immediately (Docker CLI not installed or not on PATH).
2. Did not run `npm run wp-env:start` because it would fail with the same dependency.

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

## Automated tests (no Docker)

PHPUnit and Jest cover renderer sanitization parity without a live WordPress instance:

```bash
composer install
composer test
npm test
```

See `tests/CB_Logo_Soup_Renderer_Test.php` and `src/shared/to-soup-props.test.js`.
