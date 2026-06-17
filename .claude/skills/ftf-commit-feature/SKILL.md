---
name: ftf-commit-feature
description: >
  Best practices for committing completed work on the FiveTwoFive WordPress
  site. Use this skill when the user says "commit", "let's commit", "commit
  these changes", "I'm ready to commit", or "commit and push". Covers rebuilding
  theme assets, staging the right files (the deny-all `.gitignore` silently
  ignores new files), a WordPress-specific hostile-read checklist, writing a
  good commit message, and opening a PR. Trigger any time work is complete and
  ready to be committed — don't wait for the user to ask about best practices
  explicitly.
---

# Commit Feature — FiveTwoFive

Before committing, work through these steps in order. There is no CI on this repo — these steps are the only gate, so don't skip them.

## 1. Confirm you're on a working branch

```bash
git branch --show-current
```

Any of the prefixes from `/ftf-new-branch` is acceptable: `feature/`, `fix/`, `refactor/`, `docs/`, `chore/`, `review/`. Never commit directly to `master`. If you're on `master`, stop and **invoke `/ftf-new-branch`** first.

## 2. Rebuild theme assets (the load-bearing pre-push step)

The deployed site serves the **compiled** files in each theme's `assets/dist/`. If you edited SCSS or JS source and don't rebuild, the live site ships stale assets. Build **only the theme(s) you touched**:

```bash
# Run from the repo root. `npm --prefix` keeps cwd at the root, so building both
# themes can't strand you inside the first theme's directory (a bare `cd ... &&
# cd ...` would resolve the second path relative to the first and fail).
# Parent theme (Gulp build: SCSS + ESLint-with-autofix + JS)
npm --prefix wp-content/themes/fivetwofive-theme run build

# Child theme (SCSS + JS; bundles GSAP)
npm --prefix wp-content/themes/fivetwofive-theme-child run build
```

- Run from the host, not inside a container (native Gulp toolchain).
- **ESLint does not fail the build.** The parent's gulp `lint` task runs `eslint({fix:true})` then only logs warning/error counts (`gulpfile.js` lines 47–60 — no `eslint.failAfterError()`), and `build` is `series(styles, series(lint, scripts))`. So `npm run build` exits 0 even with lint errors — **read the build output** for `Total Errors: N` and fix any before committing; don't treat a zero exit code as lint-clean. Because `fix:true` autofixes in place, the build can also modify source — re-check `git status` afterward.
- The regenerated `assets/dist/` CSS/JS **must be committed** (step 5). `*.map` files are gitignored — leave them.
- If you only touched PHP/templates (no SCSS/JS source change), no rebuild is needed — say so explicitly.

### Agent Delegation

If both themes changed, the two builds are independent and can run as parallel sub-agents. Both must finish clean before step 4.

## 3. Note on formatting / linting

- **JS**: ESLint (`@wordpress/eslint-plugin`) runs automatically inside the parent theme's Gulp `build`, but only *reports* — it does not fail the build (see step 2). Read the output and fix errors yourself.
- **PHP**: there is no automated formatter or linter configured (no phpcs/phpcbf). Follow the WordPress PHP Coding Standards by hand and match surrounding code. See `.cursor/rules/wordpress-developer-rules.mdc`.

## 4. Pre-push self-review (hostile read with enumeration)

Before staging, read the full diff as if you were a cold reviewer seeing the change for the first time. The goal is to **find what's broken**, not confirm the implementation. There are no tests here — the hostile read *is* the safety net.

```bash
git status
git diff HEAD    # HEAD covers staged + unstaged; bare `git diff` misses already-staged changes
```

### 4a. The hostile-read rules

Apply each rule and **enumerate** which file/function pairs you applied it to. "I checked everything" means the rule wasn't run.

1. **Cross-file consistency.** Are sibling functions (CPT registrations, query builders, enqueue functions, template parts) consistent? Diff them line-by-line; don't eyeball.
2. **Destination behavior.** For every template/URL/shortcode changed, did the *rendered output* change as intended for every context (logged-in vs. guest, archive vs. single, mobile vs. desktop)?
3. **Parent vs. child context.** For every changed function, confirm it lives in the right theme. Portfolio-specific behavior belongs in the **child theme**; framework-level changes belong in the **parent**. A child override shadows the parent — make sure you edited the one that's actually loaded.
4. **Removed surface.** For every deleted template/function/hook/shortcode, grep for remaining inbound references (`get_template_part`, `do_shortcode`, `add_action`/`add_filter`, `apply_filters`). For every renamed symbol, grep both old and new names.
5. **Error / empty paths.** Does each `if (! $x)` branch and every loop over a query leave a sane state when the query is empty or a field is missing? Provide fallbacks for missing ACF fields / images.
6. **New-concept follow-through.** When introducing a new CPT, taxonomy, ACF field, image size, or shortcode, grep for every site that must also reference it (template, enqueue, registration, `acf-json/` sync, rewrite flush). Coverage is binary — partial coverage is silent breakage.

Required output before declaring the read clean — one concrete line per rule with file/function references (a vague or empty line means the rule wasn't applied; **do not push** until every line is concrete):

```
Rule 1 (cross-file consistency): diffed [fn A in file:line] vs [fn B in file:line]; consistent on [...], differs on [..., why intentional]
Rule 2 (destination behavior): changed [template/shortcode in file:line]; output for [context] is [unchanged / changed thus]
Rule 3 (parent vs child): edited [fn in theme/file:line]; correct theme because [...]; no shadowing override at [...]
Rule 4 (removed surface): grepped [old + new name] in [scope]; remaining refs [none / list]
Rule 5 (error/empty paths): [query/field in file:line] handles empty via [fallback]
Rule 6 (new-concept follow-through): introduced [CPT/field/size/shortcode]; sites that must reference it [list of file:line]; coverage [all hit / misses]
```

### 4b. WordPress bug-class checklist

Run each check against the current diff and record the result. These are the classes that actually bite a WordPress theme/plugin.

| # | Check | Greppable signal | Required action |
|---|---|---|---|
| 1 | Unescaped output | `echo`/`<?=` of a variable, `get_field()`, `get_post_meta()`, or `$_*` in markup without `esc_*` | Wrap on output: `esc_html()` / `esc_attr()` / `esc_url()` text; `wp_kses_post()` for rich HTML. Escape late, at the point of output. |
| 2 | Unsanitized input | Reads `$_POST` / `$_GET` / `$_REQUEST` / `$_SERVER` | Sanitize on input: `sanitize_text_field()`, `absint()`, `sanitize_email()`, etc. |
| 3 | Missing nonce on form/AJAX | New `<form>`, `admin-ajax` handler, or REST mutation | Issue `wp_nonce_field()` / `wp_create_nonce()` and verify with `check_admin_referer()` / `wp_verify_nonce()` before acting. |
| 4 | Missing `ABSPATH` guard | New `.php` file in theme/plugin | Add `if ( ! defined( 'ABSPATH' ) ) { exit; }` at the top. |
| 5 | Unprepared DB query | `$wpdb->query(` / `->get_results(` with interpolated vars | Use `$wpdb->prepare()`; prefer `WP_Query` / `get_posts()` over raw SQL. |
| 6 | Edited parent when child was right | Diff touches `fivetwofive-theme/` for portfolio-specific behavior | Move it to the child theme override unless it's an intentional framework change (confirm in PR "Decisions baked in"). |
| 7 | ACF field group ↔ JSON drift | Diff touches `acf-json/*.json` OR you changed a field group in wp-admin | The two must move together. Review the generated `acf-json/` diff and commit it; never commit a code change that assumes fields the JSON doesn't define. |
| 8 | CPT/taxonomy/rewrite change without flush | Diff changes `register_post_type` / `register_taxonomy` args, slugs, or rewrite rules | Permalinks 404 until rewrite rules flush. Flush once (visit Settings → Permalinks, or `flush_rewrite_rules()` on activation only) and note it in the PR. |
| 9 | Stale compiled assets | Diff changes `assets/src` SCSS/JS but no matching `assets/dist` change | Rebuild (step 2) and stage the `dist` output; the live site serves `dist`. |
| 10 | New file silently gitignored | New plugin/theme dir, or file outside the `.gitignore` whitelist | Add the `!wp-content/...` exception to `.gitignore` first; confirm with `git check-ignore <path>` (no output = tracked). |
| 11 | Function/hook prefix collision | New global `function`/hook without a project prefix | Prefix with `fivetwofive_` (parent) or `fivetwofive_child_` (child); CPT/tax slugs use `ftf_`. |
| 12 | Hardcoded asset tag | New `<script>` / `<link>` in markup | Use `wp_enqueue_script()` / `wp_enqueue_style()` with deps + a version, loaded conditionally where needed. |
| 13 | Untranslated UI string | New user-facing string in PHP | Wrap in `__()` / `esc_html__()` / `_e()` with the correct text domain. |
| 14 | New CPT not REST-exposed | New `register_post_type` without `show_in_rest` | Add `'show_in_rest' => true` for Gutenberg/REST compatibility (per the project CPT rules). |

For each check, write a one-line answer (`N/A — <why>`, or `<action> at file:line`):

```
[1] escaping: N/A — no dynamic output added, OR esc_html() at file:line
[2] sanitization: N/A — no superglobals read, OR sanitize_text_field() at file:line
[3] nonce: N/A — no form/AJAX, OR wp_verify_nonce() at file:line
[4] ABSPATH guard: N/A — no new PHP file, OR added at file:line
[5] $wpdb->prepare: N/A — no raw SQL, OR prepared at file:line
[6] parent-vs-child: N/A — framework change, OR moved to child at file:line
[7] ACF JSON sync: N/A — no field change, OR acf-json/ diff reviewed + staged
[8] rewrite flush: N/A — no CPT/slug change, OR flushed + noted in PR
[9] compiled assets: N/A — no src change, OR rebuilt + staged dist at path
[10] gitignore whitelist: N/A — no new tracked path, OR added exception + git check-ignore clean
[11] prefix collision: N/A — no new global, OR prefixed at file:line
[12] enqueue: N/A — no asset tag, OR wp_enqueue_* at file:line
[13] i18n: N/A — no UI string, OR __() with text domain at file:line
[14] show_in_rest: N/A — no new CPT, OR added at file:line
```

### 4c. Cross-cutting concerns

- **Internal contradictions** — did a change in one place leave a stale assertion in another?
- **Stale references** — examples, "see step N" pointers, code snippets, readme/changelog lines that reference the old structure.
- **Sweep coverage** — if you fixed pattern X in one file, grep for X elsewhere; sibling files (CPT plugins, module templates) routinely hide the same bug.

If the self-review surfaces something, fix it now — same diff, no extra commit.

## 5. Stage only the right files

This repo's `.gitignore` is **deny-all-then-whitelist** (`/*` then `!wp-content/...`). That flips the usual staging risk:

- **The usual over-staging risk is mostly handled** — `node_modules`, `*.map`, `.env`, `*.sql`, uploads, and third-party plugins are already ignored.
- **The real risk is under-staging**: a brand-new plugin/theme dir is **silently ignored** until you add its `!wp-content/...` exception. Verify before committing:

  ```bash
  git status --short          # new files should appear; if a new dir is missing, it's gitignored
  git check-ignore -v wp-content/plugins/<new-plugin>   # any output = ignored; fix .gitignore
  ```

Stage specific paths by name (include rebuilt `dist` output):

```bash
git add wp-content/themes/fivetwofive-theme-child/template-parts/modules/<file>.php
git add wp-content/themes/fivetwofive-theme-child/assets/dist/css/style.css
git add .gitignore   # if you added a whitelist exception
```

Double-check you are **not** staging `.env` (child theme BrowserSync config) or `*.map` — both should already be ignored, but confirm.

## 6. Write a good commit message

```
type(scope): short description (under 72 chars)

Optional body explaining the why, not the what. Reference any
relevant context or decisions made during development.

Closes #123
```

**Types:** `feat`, `fix`, `refactor`, `docs`, `chore`, `test`.

**Examples:**
```
fix(hero): correct video play-button positioning

The absolute-positioned play button used `top` instead of
`inset-block-start`, breaking centering on the child-theme hero.

Closes #48
```
```
chore(build): remove dead gulp-imagemin task from parent theme
```

**Rules:**
- Imperative mood: "add" not "added", "fix" not "fixed".
- Reference the GitHub issue (`Closes #N`) when one exists — auto-closes on merge.
- Don't pad — if one line says it all, that's fine.
- **No `Co-Authored-By` trailer and no AI-attribution line.** Commits reflect the human author only.

## 7. Commit

```bash
git commit -m "$(cat <<'EOF'
fix(scope): your message here

Closes #N
EOF
)"
```

## 8. Push the branch

```bash
git push -u origin HEAD
```

## 9. Open a PR

**Invoke `/ftf-open-pr`** to compose the PR title + structured four-section body and open it via `gh pr create`. `master` is the deployed branch, so treat the merge as going live — a clear PR body makes review fast.

> A PR review-comment skill (`/ftf-review-pr`) is a planned Tier-2 addition. Until it exists, address any human review feedback directly, then re-run the relevant parts of step 2 (rebuild) and step 4 (hostile read) before pushing the fix.

## What NOT to do

- Don't push directly to `master`.
- Don't commit `.env`, `node_modules`, `*.map`, `*.sql`, or uploads (all gitignored — but never force-add them).
- Don't ship a SCSS/JS source change without rebuilding and staging `dist` — the live site serves compiled assets.
- Don't edit the parent theme for portfolio-specific work when a child-theme override is the correct place.
- Don't amend a commit that's already been pushed — create a new commit instead.

## Output

When complete, report back:
- **Branch**: the branch name
- **Commit SHA**: the short SHA of the new commit
- **PR URL**: the GitHub PR URL (if created)
- **Build result**: which theme(s) rebuilt and that the build was clean (or "no rebuild needed")
- **Assets staged**: which `dist` files were committed (or "none — no source change")
