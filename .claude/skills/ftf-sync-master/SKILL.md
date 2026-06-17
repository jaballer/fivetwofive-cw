---
name: ftf-sync-master
description: >
  Returns the local FiveTwoFive repo to a verifiably clean state on the latest
  `master` before starting the next task. Use after a PR is merged, when
  switching context between tasks, or any time you want to confirm `master` is
  up to date and stale local branches are cleaned up. Trigger phrases include
  "sync master", "clean up", "back to master", "fresh start", "start the next
  thing", "we just deployed", "PR is merged what's next", or any signal that
  the previous unit of work is finished and a new one is about to begin. This
  skill does NOT create a branch — chain to `/ftf-new-branch` when ready to
  start work.
---

# Sync Master — FiveTwoFive

Get back to a known-good baseline on `master` so the next task starts from a clean slate.

## When to use

- A PR was just merged
- Switching context between unrelated tasks
- Start of a new session after the previous one ended on a feature branch
- Any time the local state of `master` and merged feature branches is uncertain
- After merging a batch of Dependabot PRs — their remote-tracking refs pile up and want pruning

## When NOT to use

- Mid-task on an active feature branch — this skill switches to `master` and may delete the current branch if its PR is merged. Finish or stash work first.
- As a substitute for `/ftf-new-branch` — this skill never creates a branch.

## Steps

1. **Check working tree state**

   ```bash
   git status --short
   ```

   If output is non-empty (uncommitted, staged, or untracked changes), surface what's there and ask the user before continuing. Do not silently stash or discard.

2. **Note the starting branch and verify its PR status**

   ```bash
   git branch --show-current
   ```

   - If on `master`: skip the verification step and proceed to step 3.
   - If on any other branch: check whether its PR is merged before this skill considers the branch safe to delete later.

     ```bash
     gh pr list --state merged --head <branch-name> --json number,mergedAt,title --limit 1
     ```

     If the PR is unmerged or there is no PR for the branch, treat the branch as **active work** — switch to `master` for the sync but do **not** propose deleting it.

3. **Switch to master and fetch with prune**

   ```bash
   git checkout master
   git fetch --prune
   ```

   `--prune` removes remote-tracking refs (`origin/branch-foo`) for branches deleted on GitHub after merge. On this repo that clears the `origin/dependabot/*` refs left behind by merged dependency bumps.

4. **Pull latest master (fast-forward only)**

   ```bash
   git pull --ff-only
   ```

   `--ff-only` aborts instead of creating a merge commit when the branch can't be fast-forwarded. If the pull refuses to fast-forward, stop and surface the situation to the user — `master` should never have local commits ahead of `origin/master`. Plain `git pull` would silently merge here, which violates this skill's whole point of surfacing divergence.

5. **Identify safe-to-delete merged local branches**

   ```bash
   git branch --merged master
   ```

   `git branch --merged master` only checks reachability — a branch's tip can be in `master`'s history without its PR ever being merged (cherry-picks, manual rebases, true-merge PRs of unrelated work, branches that were never PR'd). The "tip is in master" signal is necessary but not sufficient for safe deletion.

   For each candidate, verify a merged PR exists before adding it to the delete list:

   ```bash
   gh pr list --state merged --head <branch-name> --json number,mergedAt --limit 1
   ```

   - **Branch + verified merged PR** → safe to propose for deletion in step 6
   - **Branch with no merged PR** (open PR, no PR, or only closed-without-merge) → leave alone. Surface separately as "master-reachable tip but unverified PR status — not proposing deletion." This applies to the starting branch from step 2 as well.

   Filter out `master` itself unconditionally.

6. **Confirm before deleting stale branches**

   List the stale branches to the user with short context (last commit subject + age) and ask for confirmation **before** deleting anything. Default to a single confirm-all prompt; only ask per-branch if the list is suspicious or the user has previously asked for granular control.

   On confirmation:

   ```bash
   git branch -d <branch-name>
   ```

   Use `-d` (safe), not `-D`. If `-d` refuses to delete, that branch isn't actually merged — surface the warning instead of force-deleting.

   **Squash-merge exception:** GitHub's squash-merge default produces branches whose tip is NOT reachable from master even after the PR is confirmed merged. In that case `git branch --merged master` won't list the branch and `git branch -d` will refuse to delete it ("not fully merged"). When `gh pr list --state merged --head <branch>` returns a merged PR but `-d` refuses, a SHA check is required before offering `-D` — `--head` filters by branch *name* only, so a reused name can match an unrelated older merged PR and the force-delete could destroy unmerged work.

   ```bash
   pr_head_oid=$(gh pr list --state merged --head <branch> --json headRefOid --limit 1 | jq -r '.[0].headRefOid')
   branch_tip=$(git rev-parse <branch>)

   if [ "$pr_head_oid" = "$branch_tip" ]; then
       # Tips match — branch hasn't moved since the squash-merge. Force-delete is recoverable from reflog if needed.
       # Surface: "Branch <name> was squash-merged in PR #<n>; tip unchanged since merge. Force-delete safe? (`git branch -D <name>`)"
   else
       # Branch has commits the merged PR doesn't include. Treat as active work.
       # Surface: "Branch <name> shares its name with merged PR #<n>, but local tip does not match the PR's head. Not proposing deletion — treat as active work."
   fi
   ```

   Only proceed with `-D` on explicit user confirmation AND a verified SHA match.

7. **Report final state**

   See **Output** below.

## Important Rules

- **Never auto-delete branches** — always confirm with the user.
- **Never use `git branch -D`** unless the user explicitly requests force-delete with reason. `-d` is the safe default; if it refuses, that's a signal worth surfacing.
- **Never stash or discard uncommitted work** automatically. If the working tree is dirty, the user decides.
- **Never `git reset --hard master`**. If `git pull --ff-only` refuses to fast-forward, the user needs to investigate — don't paper over it.
- **Never run `gh pr close`, `git push --delete`, or any other destructive remote operation** in this skill. This is local cleanup only.
- **This skill does not create branches.** When ready to start the next task, invoke `/ftf-new-branch`.

## Output

When complete, report back:

- **Starting branch**: branch the user was on when the skill ran
- **Current branch**: should be `master`
- **Master SHA**: short SHA of the new tip
- **Pulled**: number of commits fast-forwarded (or "already up to date")
- **Local branches deleted**: list, or "none"
- **Remote refs pruned**: count from `git fetch --prune`
- **Status**: `clean` or `warnings` with details
- **Next**: a one-line nudge — e.g. "Ready for `/ftf-new-branch` when you have the next task."
