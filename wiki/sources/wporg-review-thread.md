---
title: WP.org plugin review thread (2026-06 → 2026-08)
type: source
summary: Digest of the WordPress.org Plugin Directory review emails — what reviewers asked for, what we changed, and where it stands.
status: active
created: 2026-10-19
updated: 2026-10-19
last_verified: 2026-10-19
sources: ["Cooper Bold inbox thread 'Review in Progress: Logo Soup by Cooper Bold' (read with cooper-mail, 2026-10-19)", AI_SESSION_MEMORY.md]
tags: [wordpress-org, review, source]
---

# WP.org plugin review thread

One email thread from the WordPress.org Plugins Team covers the whole submission. It is still titled "Review in Progress: Logo Soup by Cooper Bold". It sits in the Cooper Bold inbox. Read it with `~/.claude/bin/cooper-mail search 'from:plugins@wordpress.org'` (read-only; never reply for Nicholas).

## Timeline

| Date | Who | What |
|---|---|---|
| 2026-06-26 | Reviewers (manual + AI) | First review of "Logo Soup by Cooper Bold" (slug `cooper-bold-logo-soup`). Main issue: the name uses the third-party "Logo Soup" project name, so the plugin looks official. Asked for a new name and slug in the readme, headers, and text domain. |
| 2026-07-06 | Nicholas | Uploaded **1.2.14** as "Balanced Logos" and asked to reserve `balanced-logos`. Stated no affiliation with Sanity Labs and that auto-scroll 0.5.3 is the latest npm release. |
| 2026-07-16 | Reviewers | "Balanced Logos" is too generic; asked for a distinctive name with the owner's term first. |
| 2026-08-03 | Nicholas | Uploaded **1.3.0** as "Cooper Bold Logo Wrangler" and asked to reserve `cooper-bold-logo-wrangler`. |
| 2026-08-06 | Automated scan | Failed: minified `build/*.js` with no matching source, and the readme did not point to this plugin's source (Guidelines 1 and 4). |
| 2026-08-24 | Nicholas | Uploaded **1.3.2**: source in the ZIP plus the public repo link, auto-scroll kept at 0.5.3, the `active_plugins` rewrite on load removed, Tested up to 7.1. |
| → 2026-10-19 | — | No newer message from plugins@wordpress.org in the inbox. **1.3.3 has not been uploaded**; it waits until 1.3.2 clears. |

## What it changed in the repo

- Name and slug: [[0003-plugin-name-cooper-bold-logo-wrangler]]
- Source in the ZIP: [[0004-ship-source-in-release-zip]]
- No `active_plugins` writes on load: [[0002-migrate-legacy-plugin-path-activate-only]]
- Library "out of date" flag answered, not "fixed": [[0001-pin-splide-extension-auto-scroll]]
- Before submitting (1.2.12–1.2.13, from Plugin Check runs): Splide vendored (no CDN), sanitized collection POST arrays, translators comments.

## Notes for the next upload

- Reply **in the same thread** and upload through the "Add your plugin" page. Reviewers expect every listed issue fixed and similar cases searched for.
- The reviewer account is the WP.org user `nicholaseymann`, which matches `readme.txt` Contributors. The docs saying contributor `cooperbold` are stale.

## Related

- [[wordpress-org-submission]] · [[notion-logo-soup-plugin-task]] · [[overview]]
