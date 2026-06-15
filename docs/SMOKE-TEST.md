# wp-env smoke test — Cooper Bold Logo Soup

**Date:** 2026-06-15  
**Machine:** macOS 26.5.1, `arm64` (Apple Silicon)  
**Repo:** `/Users/thedao/Repos/Logo Soup WP Plugin`  
**Docker:** Colima (`docker context`: `colima`, aarch64 Ubuntu 24.04)

## Summary

| Check | Result | Notes |
|-------|--------|-------|
| `uname -m` / `sw_vers` | **PASS** | `arm64`, macOS 26.5.1 |
| Rosetta (`softwareupdate --install-rosetta`) | **BLOCKED** | `sudo` needs interactive password in agent shell |
| Docker Desktop (`/Applications/Docker.app`) | **Present** | Daemon was down initially; Colima used for smoke |
| `docker info` | **PASS** | Colima VM, Server 29.5.2, aarch64 |
| `npm ci && npm run build` | **PASS** | After clean `node_modules` (sandbox tar errors without full permissions) |
| `npm run wp-env:start` (default `.wp-env.json`) | **FAIL** | Repo path contains spaces; lifecycle script splits plugin path and exits 1 |
| `npm run wp-env:start` (symlink workaround) | **PASS** | See [Path with spaces](#path-with-spaces) |
| `wp plugin activate cooper-bold-logo-soup` | **FAIL** | WP-CLI slug is the **folder name**, not the main PHP file |
| `wp plugin list --status=active` | **PASS** | Plugin active as `logo-soup-wp-plugin-smoke` (symlink) or `Logo Soup WP Plugin` (`.` mount) |
| `wp post create` (logo-soup block) | **PASS** | Post created (e.g. ID 8) |
| `curl http://localhost:8888/` | **PASS** | HTTP 200 |
| `npm run wp-env:stop` | **PASS** | Stopped cleanly after smoke |

**Overall wp-env smoke:** **PARTIAL PASS** — WordPress + plugin + post + HTTP work; default start and `cooper-bold-logo-soup` activation fail on this machine without workarounds.

## Path with spaces

`@wordpress/env` mounts `"plugins": [ "." ]` using the directory name as the plugin slug. Spaces in `/Users/thedao/Repos/Logo Soup WP Plugin` break the **lifecycle** `wp plugin activate` step (tokens split into `Logo`, `Soup`, `WP`, `Plugin`).

**Workaround used for green `wp-env start`:**

```bash
ln -sfn "/Users/thedao/Repos/Logo Soup WP Plugin" "/Users/thedao/Repos/logo-soup-wp-plugin-smoke"

cat > .wp-env.override.json <<'JSON'
{
	"plugins": [ "/Users/thedao/Repos/logo-soup-wp-plugin-smoke" ]
}
JSON

npm run wp-env:start
```

Remove `.wp-env.override.json` when not testing, or clone the repo to a path without spaces.

## Activate plugin (WP-CLI)

The installable slug follows the **plugin directory name**, not `cooper-bold-logo-soup.php`:

```bash
# Default mount (folder name with spaces)
npx wp-env run cli wp plugin activate "Logo Soup WP Plugin"

# Symlink workaround
npx wp-env run cli wp plugin activate logo-soup-wp-plugin-smoke

# This fails (plugin not found):
npx wp-env run cli wp plugin activate cooper-bold-logo-soup
```

## Commands run (2026-06-15)

```bash
cd "/Users/thedao/Repos/Logo Soup WP Plugin"
npm ci && npm run build
npm run wp-env:start          # fails with default config (spaces)
# … symlink + .wp-env.override.json …
npm run wp-env:start          # pass
npx wp-env run cli wp plugin list --status=active
npx wp-env run cli wp post create --post_title='Logo Soup Test' --post_status=publish \
  --post_content='<!-- wp:cooper-bold/logo-soup /-->'
curl -s -o /dev/null -w "%{http_code}" http://localhost:8888/
npm run wp-env:stop
```

## User actions still needed

1. **Rosetta** (optional, for Docker Desktop x86 paths): run in Terminal — `sudo softwareupdate --install-rosetta --agree-to-license`
2. **Docker Desktop** vs **Colima**: smoke used Colima because Desktop daemon/socket was not ready; either stack is fine if `docker info` succeeds.
3. **Rename or symlink** the repo if you want default `wp-env start` without override.

## Automated tests (no Docker)

```bash
composer install
vendor/bin/phpunit
npm test
```

See `tests/CB_Logo_Soup_Renderer_Test.php` and `src/shared/to-soup-props.test.js`.

## Release zip

```bash
./scripts/build-release-zip.sh
unzip -l dist/cooper-bold-logo-soup-1.0.1.zip
```
