---
name: ftf-new-branch
description: >
  Creates a properly-named git branch before starting any new work on the
  FiveTwoFive WordPress site. Use this skill at the start of every coding
  session, feature, bug fix, or task. Trigger whenever the user mentions
  starting something new, working on an issue, fixing a bug, or any time work
  is about to begin and no feature branch has been created yet. If the user
  jumps straight into making changes without creating a branch first, pause and
  run this skill before proceeding. Phrases like "let's work on X", "can you
  fix Y", "start on issue #Z", or "I want to add A" are all signals to run this
  skill first.
---

# New Branch — FiveTwoFive

Before any code changes are made, ensure work is happening on a clean, properly-named branch off `master`.

## Steps

1. **Check the current branch**
   ```bash
   git branch --show-current
   ```
   If already on a non-`master` branch, confirm with the user before proceeding — they may want a fresh branch anyway.

2. **Switch to master and pull latest (fast-forward only)**
   ```bash
   git checkout master && git pull --ff-only
   ```
   Always branch from an up-to-date `master`. Never branch from another working branch. `--ff-only` aborts instead of silently creating a merge commit if `master` has diverged from `origin/master`; if it refuses, stop and surface the divergence to the user — `master` should never be in that state.

   > If you just merged a PR, are coming off a stale local branch, or are unsure
   > whether `master` is clean, invoke `/ftf-sync-master` first. It pulls latest,
   > prunes remote-tracking refs (including merged Dependabot branches), and
   > (with confirmation) deletes merged local branches before you create the next one.

3. **Determine the branch prefix from the work type**

   Match the prefix to what the resulting commit type will be (mirrors conventional-commits practice and keeps the branch name aligned with the eventual PR):

   | Prefix | When to use |
   |---|---|
   | `feature/` | New functionality — a new module template, plugin, CPT, ACF field group, page section |
   | `fix/` | Bug fixes, regressions, broken behavior |
   | `refactor/` | Code restructure with no behavior change (renames, moves, extractions) |
   | `docs/` | Documentation-only changes (`docs/`, `readme.md`, `change-log.md`, code comments) |
   | `chore/` | Tooling, dependencies, Gulp/build config, skills, `.cursor` rules, generated assets |
   | `review/` | Standalone review or QA passes (e.g. `review/qa-hero-module`) |

   The repo's history also uses `cleanup/` for multi-step removal work (e.g. `cleanup/phase-1-dead-plugins`); treat it as a flavor of `chore/`/`refactor/`. When in doubt between `feature/` and `fix/`: new user-observable behavior is `feature/`; making existing behavior correct is `fix/`.

4. **Determine the branch name**
   - If a GitHub issue number is known: `<prefix>/[issue-number]-[short-description]`
   - If no issue number: `<prefix>/[short-description]`
   - Use lowercase, hyphens only, keep it under 50 characters
   - Examples:
     - `feature/contact-form-plugin`
     - `fix/hero-video-play-button`
     - `refactor/extract-module-template-loader`
     - `docs/parent-theme-readme`
     - `chore/claude-skills-tier1`
     - `review/qa-resources-module`

5. **Create and switch to the branch**
   The full branch name from step 4 already includes the prefix, so pass it directly to `git checkout -b`:

   ```bash
   git checkout -b <full-branch-name>
   ```

   Concrete example matching step 4:

   ```bash
   git checkout -b feature/contact-form-plugin
   ```

6. **Confirm to the user**
   State the branch name and that it's ready to go. Then proceed with the work.

## Important Rules

- **Never commit directly to `master`** — `master` is the repo's default and deployed branch. All work lands via PRs.
- **Always pull latest `master` first** — avoids conflicts and ensures the branch starts from the current state of the live site.
- **One branch per issue or feature** — don't stack unrelated changes on the same branch.
- **Branch prefix should match the eventual commit prefix** — a branch named `feature/foo` whose commit message starts with `fix:` is a smell; pick the right prefix up-front.

## Output

When complete, report back:
- **Branch name**: the full branch name created
- **Base commit**: the short SHA of the `master` commit branched from
- **Status**: `ready` or `error` with details
