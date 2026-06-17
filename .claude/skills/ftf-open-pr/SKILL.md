---
name: ftf-open-pr
description: >
  Opens a GitHub pull request for the FiveTwoFive WordPress site with a
  structured, reviewable body. Use this skill whenever the user says "open a
  PR", "create the PR", "push and open a PR", "ship it", or anytime work is
  committed and pushed to a working branch but the PR hasn't been opened yet.
  Also invoke automatically as the final step of `/ftf-commit-feature` after a
  successful push. Produces a PR description in the standard four-section shape
  (Summary / Decisions baked in / Test plan / Follow-ups) with checkbox-formatted
  verification steps.
---

# Open PR — FiveTwoFive

Opens a pull request with a body that's actually scannable in code review. The point of the structure isn't to fill a template — it's to give a reviewer enough context to evaluate the change without re-deriving the design decisions or guessing what was tested. Repo: `jaballer/fivetwofive-cw`, base branch `master`.

## Prerequisites

- You are on a non-`master` working branch (any of the prefixes from `/ftf-new-branch`) — confirm with `git branch --show-current`
- The branch has been pushed to origin — confirm with `git status` (should say "Your branch is up to date with 'origin/<branch>'")
- Affected theme assets were rebuilt and committed, and the change was smoke-tested locally (see `/ftf-commit-feature`). There is no CI on this repo — verification is manual and on you.

If any of these are not true, stop and resolve them first. Don't open a PR for unpushed code or against an unverified branch.

## Step 1: Gather context for the PR body

Run these in parallel (they're independent):

```bash
# What changed since master
git log master..HEAD --oneline
git diff master...HEAD --stat

# Recent PRs for title-style reference
gh pr list --repo jaballer/fivetwofive-cw --state all --limit 5

# Issue this PR closes (if any) — look for "Closes #N" in commits or an issue number in the branch name
```

If the branch name is like `feature/42-contact-form-plugin`, the issue number is `42`. Confirm with the user if it's ambiguous.

## Step 2: Compose the PR title

Recent history on this repo is mixed — some titles are conventional-commits style (`chore: Remove dead gulp-imagemin task (#55)`), some are plain sentences (`Phase 3: Remove Font Awesome and replace Fancybox with GLightbox`). **Prefer the conventional-commits form** for consistency with the branch prefix:

```
<type>(<scope>): <short imperative description> (#<issue-number>)
```

- **Type** matches the branch prefix:

  | Branch prefix | PR title type |
  |---|---|
  | `feature/` | `feat` |
  | `fix/` | `fix` |
  | `refactor/` | `refactor` |
  | `docs/` | `docs` |
  | `chore/` / `cleanup/` | `chore` |
  | `review/` | `chore` (default — override to the dominant category if the work is all one type) |

- **Scope** is optional but helpful — the area touched: `theme`, `child-theme`, `cta`, `work-cpt`, `hero`, `build`, `acf`, etc. Match what `gh pr list` shows for similar changes.
- **Description** is imperative present tense, under 70 characters total
- **Issue number** parenthesized at the end if there's an associated issue

Examples (this repo's actual history, normalized):
- `fix(hero): correct video play-button positioning typo (#48)`
- `chore(build): remove dead gulp-imagemin task from parent theme (#55)`
- `refactor(theme): remove jQuery dependency from parent theme (#47)`
- `docs(contact-form): add plugin scope doc (#56)`

## Step 3: Compose the PR body using the four-section template

The body has exactly four sections in this order. Skip a section only if it would be empty.

### Section 1 — Summary

3–5 bullets describing the change. Reviewer-facing: what this PR makes happen, not how. Lead with the outcome, not the mechanism.

```markdown
## Summary

- Bullet 1: the headline change
- Bullet 2: the supporting changes that enable it
- Bullet 3: any breaking-ish or surprising side effects
```

### Section 2 — Decisions baked in

This is the section reviewers most want and that PR templates almost never include. List the design choices made during implementation that aren't obvious from the code, with the alternative considered. Lock-in the rationale so the next reviewer (or your future self) doesn't re-litigate it.

```markdown
## Decisions baked in

- **Choice A over B** because [reason]. [Optional: when this would change.]
- **Pattern X used here** because [reason], matches the precedent set in [file/PR].
```

Common FiveTwoFive decisions worth recording: child-theme override vs. parent-theme change; new first-party plugin vs. theme code; shortcode vs. ACF module wrapper; build-tool/dependency choices. If there genuinely were no design decisions to call out (e.g. a typo fix), omit this section.

### Section 3 — Test plan

Checkbox-formatted list of what was verified. There is no CI, so every box is something you actually did locally. Include the actual commands and the manual steps.

```markdown
## Test plan

- [x] `npm --prefix wp-content/themes/<theme> run build` — rebuilt; checked output for ESLint `Total Errors` (build doesn't fail on lint)
- [x] Committed regenerated `assets/dist/` (CSS/JS + the parent theme's tracked `assets/dist/maps/`; child-theme maps stay ignored)
- [x] Manual smoke in LocalWP: loaded <affected page/template>, verified <behavior>
- [x] Checked `wp-content/debug.log` — no new PHP notices/warnings
```

> Check a box only for what you actually ran. For UI / template / styling work, state explicitly that visual verification was manual — reviewers should know what was vs wasn't checked. Never claim a smoke test you didn't perform.

### Section 4 — Follow-ups (optional)

Any issues filed, deferred work, or known limitations the reviewer should be aware of. Only include if there's real follow-up; don't pad.

```markdown
## Follow-ups

- [#NNN](https://github.com/jaballer/fivetwofive-cw/issues/NNN) — <short description>
- Out of scope here, would need <design-call>: <future work>
```

## Step 4: Open the PR

Use a heredoc for the body so newlines and special chars survive:

```bash
gh pr create --repo jaballer/fivetwofive-cw --base master \
  --title "<type>(<scope>): <description> (#<issue-number>)" --body "$(cat <<'EOF'
## Summary

- ...

## Decisions baked in

- **...** because ...

## Test plan

- [x] ...

## Follow-ups

- ...
EOF
)"
```

If `gh pr create` reports the PR already exists for this branch, switch to `gh pr edit <pr-number> --body "$(cat <<'EOF' ... EOF
)"`.

## Important rules

- **No AI-attribution footer or trailer.** Do not add "🤖 Generated with Claude Code" (or any similar generated-by line) to the PR body, and no `Co-Authored-By` trailer in the commits. PRs and commits reflect the human author only.
- **Don't fabricate test results.** If a smoke test or build wasn't run, don't claim it was. Leave the box unchecked or omit the line.
- **Don't pad the body.** A four-line summary on a one-line change is worse than no body. Each section is optional except Summary.
- **Don't put emojis in the title** — they break `gh` CLI URL encoding sometimes and the project's history doesn't use them.
- **Include `Closes #N` in the PR body** when the PR resolves an issue — only the closing-keyword form (`Closes #N`, `Fixes #N`, `Resolves #N`) in the body or a commit message triggers GitHub's auto-close on merge. The `(#N)` suffix in the title is human-readable cross-referencing only and does **not** auto-close.

## Output

When complete, report back:
- **PR URL**: the GitHub PR URL
- **Title**: the PR title used
- **Body sections included**: which of the four sections made it in
- **Closes**: the issue number this PR closes, if any
