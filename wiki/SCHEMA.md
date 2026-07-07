# Wiki Schema

> This file tells the LLM how to maintain this wiki. Read it at the start of every session that touches wiki pages.

## Project

- **Name**: Balanced Logos WP Plugin
- **Domain**: WordPress plugin **Balanced Logos** — Gutenberg block and `[balanced_logos]` shortcode that render partner logos using `@sanity-labs/logo-soup`. Main file: `balanced-logos.php`.

## Directory structure

```
wiki/
├── SCHEMA.md          ← you are here (LLM instructions)
├── index.md           ← content catalog, organized by type
├── log.md             ← chronological operations log
├── sources/           ← summaries of ingested documents
├── entities/          ← concrete things (tools, services, APIs, tables, configs)
├── concepts/          ← patterns, principles, architectural ideas
├── decisions/         ← architecture decision records (ADRs)
├── guides/            ← how-tos, runbooks, procedures
└── assets/            ← images, diagrams, attachments
```

## Page conventions

### Filenames

- **Kebab-case**, lowercase: `wordpress-rest-api.md`, `logo-cpt.md`
- Singular nouns for entities: `wordpress-plugin.md` not `wordpress-plugins.md`
- Verb-noun for guides: `deploy-to-staging.md`, `release-plugin-zip.md`
- Decisions use numbered prefix: `0001-use-custom-post-type-for-logos.md`

### Frontmatter

Every page MUST have YAML frontmatter:

```yaml
---
title: Human-Readable Page Title
type: source | entity | concept | decision | guide
created: YYYY-MM-DD
updated: YYYY-MM-DD
tags: [relevant, tags]
aliases: [alternate-name, abbreviation]
sources: ["[[source-page]]"]
status: active | draft | superseded | archived
---
```

### Links

Use Obsidian-style wikilinks exclusively:

- `[[page-name]]` for standard links
- `[[page-name|display text]]` for aliased links
- `[[page-name#section]]` for section links
- Never use markdown-style `[text](url)` for internal wiki links (reserve for external URLs)

### Page structure

Every page follows this skeleton:

```markdown
---
(frontmatter)
---

# Title

One-paragraph summary of what this page covers.

## Content sections
(varies by page type — see template in llm-wiki skill)

## Related
- [[linked-page-1]]
- [[linked-page-2]]
```

## Page types

See the full type definitions in the llm-wiki skill template. Use **entity** for WordPress APIs, CPTs, taxonomies, and plugin files; **concept** for patterns (e.g. shortcode vs block); **decision** for ADRs; **guide** for release and local-dev procedures.

## Operations

### Obsidian (human viewer)

Open `wiki/` as a vault in Obsidian. macOS: `open -a Obsidian "<project-root>/wiki"`.

### Ingest / migrate / query / lint

Follow workflows in `~/.cursor/skills/llm-wiki/SKILL.md`. After plugin code exists, ingest README and inline docs as sources.

## Style rules

- Write in clear, direct prose. No marketing language.
- Prefer concrete specifics: hook names, option keys, REST routes, file paths.
- Trace claims to sources via `[[wikilinks]]`.

## Relationship to other project files

- **AI_SESSION_MEMORY.md**: Session breadcrumbs; wiki is compiled long-form knowledge.
- **AI_RUNBOOK.md**: Operational procedures; may graduate into `wiki/guides/`.
- **AGENTS.md**: Always-on agent instructions.
- **SimpleMem**: Fast recall (`balanced-logos-wp-plugin` namespace); wiki is structured reference.
