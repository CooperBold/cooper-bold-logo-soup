---
title: Submit to WordPress.org Plugin Directory
type: guide
created: 2026-06-14
updated: 2026-06-14
tags: [wordpress, release, deploy, svn, github-actions]
aliases: [wp-org-submit, release-plugin]
sources:
  - "[[wordpress-plugin]]"
status: active
---

# Submit to WordPress.org Plugin Directory

Step-by-step procedure for publishing **Logo Soup** on the WordPress plugin directory. Tasks that need the maintainer's account or secrets are marked **(you)**. Source: `docs/WORDPRESS-ORG-SUBMISSION.md`.

## 1. Create a WordPress.org account **(you)**

1. Register at [wordpress.org](https://wordpress.org/support/register.php) if you do not have an account.
2. Choose or confirm the **contributor slug** (used in `readme.txt`; currently `cooperbold`).
3. Enable two-factor authentication on the account.

## 2. Build a clean plugin ZIP

From the repo root:

```bash
npm ci && npm run build
chmod +x scripts/build-release-zip.sh
./scripts/build-release-zip.sh
```

The ZIP is written to `dist/cooper-bold-logo-soup-1.0.0.zip`. It respects `.distignore` (no `node_modules`, `src/`, dev docs, etc.). See [[adversarial-review]] for verification that `build/block/block.json` and `view.scss.css` end up in the ZIP.

## 3. Submit for review **(you)**

1. Go to [Add your plugin](https://wordpress.org/plugins/developers/add/).
2. Upload the ZIP from step 2.
3. Wait for the review team email (often a few days to two weeks).
4. When approved, you receive **SVN credentials** for `https://plugins.svn.wordpress.org/cooper-bold-logo-soup/`.

> **Pre-approval:** the first submission is reviewed manually. Plugin Check (step 7) and escaping/sanitization in this repo are aligned with common review feedback, but approval is not guaranteed until a human reviewer accepts the plugin.

## 4. Add GitHub secrets for automated deploy **(you)**

After SVN access is granted:

1. Open **GitHub → CooperBold/cooper-bold-logo-soup → Settings → Secrets and variables → Actions**.
2. Add repository secrets:
   - `SVN_USERNAME` — your wordpress.org username.
   - `SVN_PASSWORD` — your [application password](https://make.wordpress.org/core/handbook/tutorials/generate-a-password-for-svn/) (not your login password).

Pushes of tags matching `v*` on `main` run `.github/workflows/deploy.yml`, which builds assets and deploys via [10up/action-wordpress-plugin-deploy](https://github.com/10up/action-wordpress-plugin-deploy).

## 5. First release tag **(you)**

When SVN is ready and secrets are set:

```bash
git checkout main
git pull --rebase
npm ci && npm run build
git status   # ensure build/ is committed if changed
git tag -a v1.0.0 -m "Release 1.0.0"
git push origin main
git push origin v1.0.0
```

The `v1.0.0` tag triggers deploy to `tags/1.0.0` and updates `trunk` on WordPress.org SVN.

## 6. Directory assets **(you)**

Before or shortly after launch, add PNGs under `assets/` (see `assets/README.md`):

- `banner-772x250.png`
- `icon-256x256.png`
- `screenshot-1.png`, `screenshot-2.png`

Commit to `main`, then tag again (e.g. `v1.0.1`) or deploy assets via SVN manually.

## 7. Plugin Check **(you)**

On a staging WordPress site (6.4+):

1. Install the [Plugin Check](https://wordpress.org/plugins/plugin-check/) plugin.
2. Upload the release ZIP or clone from SVN trunk.
3. Run **Plugin Check → Check a plugin** and fix any reported issues before promoting to production.

This repo follows WordPress coding practices: `ABSPATH` guards, escaping, sanitization, text domain `cooper-bold-logo-soup`. No `eval` or obfuscated JavaScript in source.

## 8. After release

- Update `Stable tag` and changelog in `readme.txt` for each release.
- Bump `Version` in `cooper-bold-logo-soup.php`, `CB_LOGO_SOUP_VERSION`, and `src/block/block.json` (`version`); run `npm run build`; then tag `v*`.
- Monitor the [plugin support forum](https://wordpress.org/support/plugin/cooper-bold-logo-soup/) once live.

## What cannot be automated

| Task | Blocker |
| --- | --- |
| Plugin directory submission | WordPress.org account + human review |
| SVN deploy | `SVN_USERNAME` / `SVN_PASSWORD` secrets |
| Plugin Check on live site | Staging WordPress install |
| Marketing screenshots | Design-approved PNG exports |

## Related

- [[wordpress-plugin]]
- [[adversarial-review]]
- [[sar-review]]
