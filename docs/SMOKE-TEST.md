# wp-env smoke test — Cooper Bold Logo Soup

**Date:** 2026-06-15  
**Environment:** macOS Apple Silicon (`arm64`), macOS 26.5.1, plugin root `/Users/thedao/Repos/Logo Soup WP Plugin`

## Summary

| Step | Result |
|------|--------|
| Docker Desktop (`/Applications/Docker.app`) | **Installed** — Homebrew cask needs `sudo` for `/usr/local/bin` symlinks; app copied from official DMG instead |
| Docker daemon | **Colima** — `DOCKER_HOST=unix:///Users/thedao/.colima/default/docker.sock` (Desktop socket not up during agent run) |
| `docker version` (client / server) | **29.5.3** / **29.5.2** (Colima, `linux/arm64`) |
| `npm ci && npm run build` | **Pass** |
| `npm run wp-env:start` (default `"plugins": [ "." ]`) | **Fail** — lifecycle `wp plugin activate` splits folder name on spaces (`Logo`, `Soup`, …) |
| `npm run wp-env:start` (symlink + `.wp-env.override.json`) | **Pass** |
| Plugin active in wp-env | **Pass** — slug `logo-soup-wp-plugin-smoke` |
| `wp post create` (logo-soup block) | **Pass** — post ID 4 |
| Frontend HTML (`cb-logo-soup`, `data-cb-logo-soup`, `cooper-bold-logo-soup-view`) | **Pass** — `curl http://localhost:8888/?p=4` |
| `npm run wp-env:stop` | **Pass** |

**Overall wp-env smoke:** **Pass** with symlink workaround and Colima. Default config from a spaced path still fails activation during `wp-env start`.

## Docker install (agent run)

```bash
# brew install --cask docker  → fails without interactive sudo for /usr/local/bin links
# Manual install from cached DMG:
hdiutil attach ~/Library/Caches/Homebrew/Cask/Docker.dmg--*.dmg
cp -R /Volumes/Docker/Docker.app /Applications/
hdiutil detach /Volumes/Docker
open -a Docker   # first-run may need Rosetta or "Continue without Rosetta"
```

**User one-liner if Homebrew is preferred:**

```bash
brew install --cask docker   # enter password when prompted for symlinks
```

## Path with spaces

`@wordpress/env` uses the repo **directory name** as the plugin folder and activation slug. Spaces in `Logo Soup WP Plugin` break the lifecycle activate step.

**Workaround (verified 2026-06-15):**

```bash
ln -sfn "/Users/thedao/Repos/Logo Soup WP Plugin" "/Users/thedao/Repos/logo-soup-wp-plugin-smoke"

cat > .wp-env.override.json <<'JSON'
{
	"plugins": [ "/Users/thedao/Repos/logo-soup-wp-plugin-smoke" ]
}
JSON

export DOCKER_HOST="unix:///Users/thedao/.colima/default/docker.sock"
npm run wp-env:start
```

`wp plugin activate cooper-bold-logo-soup` **does not work** — use `logo-soup-wp-plugin-smoke` (or quoted `Logo Soup WP Plugin` for default mount).

## Commands run (2026-06-15, pass)

```bash
cd "/Users/thedao/Repos/Logo Soup WP Plugin"
npm ci && npm run build
export DOCKER_HOST="unix:///Users/thedao/.colima/default/docker.sock"
npm run wp-env:start
npx wp-env run cli wp plugin list --status=active
npx wp-env run cli wp post create --post_title='Logo Soup Smoke Test' --post_status=publish \
  --post_content='<!-- wp:cooper-bold/logo-soup {"logos":[{"url":"https://via.placeholder.com/120x48.png","alt":"Placeholder"}]} /-->'
curl -sS "http://localhost:8888/?p=4" | grep -E 'cb-logo-soup|cooper-bold-logo-soup-view'
npm run wp-env:stop
```

## Pass criteria (frontend)

Published page renders a `.cb-logo-soup` container with `data-cb-logo-soup` JSON, placeholder `<img>` tags, and `cooper-bold-logo-soup-view` script enqueued.

Shortcode alternative:

```text
[logo_soup logos="https://via.placeholder.com/120x48.png|Placeholder"]
```

## Colima vs Docker Desktop

Colima works without Rosetta on Apple Silicon. If using Docker Desktop instead, install Rosetta when prompted (`sudo softwareupdate --install-rosetta --agree-to-license`) or choose **Continue without Rosetta**. Do not run Desktop and Colima against the same wp-env stack at once.

## Automated tests (no Docker)

```bash
composer install
vendor/bin/phpunit
npm test
```

## Release zip verification

```bash
./scripts/build-release-zip.sh
unzip -l dist/cooper-bold-logo-soup-1.0.1.zip
```
