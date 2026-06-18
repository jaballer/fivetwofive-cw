# AGENTS.md

Canonical guidance for anyone — human or AI agent — working in this repository. Tool-agnostic: Claude Code loads it via the root `CLAUDE.md`, and Cursor reads it alongside `.cursor/rules/`.

## What this repo is

This repository is a **full WordPress install** (the site's `app/public`), not a single theme or plugin. First-party code lives under `wp-content/`:

- `wp-content/themes/fivetwofive-theme` — custom parent theme (the FiveTwoFive framework)
- `wp-content/themes/fivetwofive-theme-child` — portfolio-specific child theme (the active theme)
- `wp-content/plugins/fivetwofive-*` — first-party plugins (custom post types, features, admin helpers)

WordPress core, third-party plugins, and uploads are intentionally untracked (see `.gitignore`).

## Where to look first

Read the guidance closest to the code you're touching:

| Working on… | Read |
|---|---|
| Parent theme | `wp-content/themes/fivetwofive-theme/CLAUDE.md` + its `README.md` |
| Child theme | `wp-content/themes/fivetwofive-theme-child/CLAUDE.md` |
| A plugin | the plugin's own file header (plugins don't have their own `CLAUDE.md` yet — see Conventions) |
| Orientation | this file, plus the [project wiki](https://github.com/jaballer/fivetwofive-cw/wiki) |

### Where knowledge lives
- **Code** → this repo.
- **Agent guidance** → `AGENTS.md` (this file) + root and per-theme `CLAUDE.md` + `.cursor/rules/`.
- **Shared, human-facing docs** → the [GitHub wiki](https://github.com/jaballer/fivetwofive-cw/wiki) (architecture, modules, plugins, dev workflow).
- **Project overview** → `readme.md`; **change history** → `change-log.md`.

## Build & assets

Theme assets are compiled with Gulp. **Never edit `assets/dist/` directly** — edit `assets/src/` and rebuild. (Known exception: the parent theme's `assets/dist/js/navigation.js` is hand-maintained; see its `CLAUDE.md`.)

```bash
# Parent theme
cd wp-content/themes/fivetwofive-theme && npm install && npm run build

# Child theme (uses .env for BrowserSync)
cd wp-content/themes/fivetwofive-theme-child && npm install && cp .env.example .env && npm run build
```

Rebuild the relevant theme's assets before committing any SCSS/JS change.

## Conventions

**Branch → commit → PR.** Never commit to `master` — it's the deployed branch. The `.claude/skills/` automate this flow; prefer them:

- `ftf-new-branch` — branch off an up-to-date `master`; prefix matches the work (`feature/`, `fix/`, `refactor/`, `docs/`, `chore/`).
- `ftf-commit-feature` — rebuild assets, stage, commit (handles the `.gitignore` gotcha below).
- `ftf-open-pr` — PR body in the house shape: **Summary / Decisions baked in / Test plan / Follow-ups**.
- `ftf-review-pr`, `ftf-sync-master` — address review comments; sync and prune branches.

Commit messages follow conventional commits (`feat:`, `fix:`, `docs:`, `chore:`, …), often scoped — e.g. `fix(contact-form): …`. Larger efforts are tracked as **epics** with sub-issues (see issues #69, #102 for the pattern).

When adding a substantial plugin, give it a `CLAUDE.md` (mirroring the theme ones) and a wiki page, and update this file's pointers.

## Gotchas (read before you commit)

- **`.gitignore` is deny-all.** Everything is ignored unless explicitly whitelisted, so **new files are silently untracked**. Whitelist the path (e.g. `!wp-content/plugins/your-plugin`) and confirm with `git status` before assuming a file is staged.
- **Rebuild assets.** A stale `assets/dist/` is the most common "my change didn't show up."
- **Shortcodes in modules pass through `wp_kses()`.** The multi-column and code modules expand shortcodes and then filter the result against `fivetwofive_kses_extended_ruleset()` (theme `inc/public/template-functions.php`). Tags not in that allowlist are stripped — if a shortcode emits `<form>`, `<input>`, or other non-standard tags, allow them there.
- **ACF JSON is version-controlled** in `wp-content/themes/fivetwofive-theme/acf-json/`. Editing field groups in wp-admin writes JSON there — commit it with the code.
- **Plugins integrate via theme filters**, not duplicated layout logic: `fivetwofive_theme_is_contained`, `fivetwofive_theme_enable_sidebar`, `fivetwofive_theme_after_post_meta`.

## Local environment

This runs as a local WordPress install (e.g. Local by Flywheel), and the site root differs per machine. **Do not hardcode machine-specific absolute paths in committed files** — use repo-relative paths in code and docs.
