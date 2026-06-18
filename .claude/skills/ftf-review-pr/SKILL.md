---
name: ftf-review-pr
description: >
  Address and fix GitHub pull request review comments on the FiveTwoFive
  WordPress site (incl. Codex bot reviews). Use whenever the user shares a PR
  URL or says "address the comments", "review the PR", "fix the feedback",
  "resolve the review", "handle the Codex comments", "address any open
  comments", or pastes a PR URL with "fix this PR" / "take care of the
  comments". Handles the full loop: fetch comments → verify each against code →
  fix or rebut → rebuild assets → commit + push → reply + resolve each thread →
  recheck for the reviewer's follow-up.
---

# Review PR — FiveTwoFive

Respond to GitHub PR review comments by making the changes, rebuilding any affected assets, committing, and replying + resolving inline on GitHub. Repo: `jaballer/fivetwofive-cw`, base `master`.

## Hard rule: commit + reply + resolve are atomic

Every commit that addresses a review comment MUST be paired with:
1. A **reply** on that comment, including the new commit SHA and a one-line explanation.
2. A **resolution** of the thread (the `resolveReviewThread` GraphQL mutation) — for comments you *fixed*. Leave rebuttal threads open for the user (see Step 11).

"Pushed but not replied" and "replied but not resolved" are half-done states. Don't declare this skill complete until every addressed comment has a reply and every fixed thread is resolved. If branch protection requires resolved threads, this is also a merge-blocker; either way it's the courtesy that keeps the PR clean.

## Step 1: Resolve which PR

Get `owner`/`repo`/`pr_number`. Sources, in priority order:
1. **Explicit** — the user pasted the URL.
2. **Chained** — passed in from `/ftf-commit-feature` → `/ftf-open-pr`.
3. **Inferred from branch** — `gh pr view --json url,number -q .number` returns the PR for the checked-out branch if exactly one open PR exists.

If none resolves, ask before proceeding. (Default repo is `jaballer/fivetwofive-cw`.)

## Step 2: Get on the PR branch, up to date

```bash
git branch --show-current
```

**Gate on a clean tree before any checkout.** If `git status --short` is non-empty, **stop and ask** — `gh pr checkout` carries unrelated uncommitted/untracked edits onto the PR branch, and they'd land in the review-fix diff (and get committed in Step 9). Don't stash or discard automatically.

```bash
git status --short        # must be empty before checkout — stop and ask if not
```

If you just pushed the PR this session you're already on its branch. Otherwise `gh pr checkout <pr_number> --repo jaballer/fivetwofive-cw`. Either way, make local current and **stop on an ahead/diverged branch**:

```bash
git fetch origin
git status -sb            # note ahead / behind
git pull --ff-only        # fast-forward only; refuse to merge
```

If `--ff-only` refuses, local has diverged — stop and surface it. **And `--ff-only` won't catch the ahead-only case**: if `git status -sb` shows `[ahead N]`, you have local commits not on `origin/<branch>` that a later push would ship alongside the review fix. At the *start* of a review pass the branch should be even with origin (`[ahead 0]`) — your fix commits come after this step — so any pre-existing `ahead` means stop and surface before continuing. Don't auto-rebase.

## Step 3: Fetch comments + build the tracking list (capture IDs NOW)

Pull everything in **one GraphQL query** — it returns the thread node ID *and* each comment's `databaseId`, body, path, and resolved-state together, so you have everything needed for both reply (Step 10) and resolve (Step 11) without a second race-prone lookup:

```bash
gh api graphql -f query='
query {
  repository(owner:"jaballer", name:"fivetwofive-cw") {
    pullRequest(number: PR_NUMBER) {
      reviewThreads(first: 100) {
        nodes {
          id
          isResolved
          comments(first: 50) { nodes { databaseId author{login} createdAt path line body } }
        }
      }
    }
  }
}' --jq '.data.repository.pullRequest.reviewThreads.nodes[] | select(.isResolved==false) | "thread=\(.id) \(.comments.nodes[0].path):\(.comments.nodes[0].line)", (.comments.nodes[] | "  id=\(.databaseId) by=\(.author.login) @\(.createdAt)\n  \(.body)"), "---"'
```

Also fetch the Codex review summaries (which commit was reviewed) and any general PR comments:

```bash
gh api repos/jaballer/fivetwofive-cw/pulls/PR_NUMBER/reviews --jq '.[] | select(.user.login=="chatgpt-codex-connector[bot]") | "\(.submitted_at) reviewed=\(.body | capture("Reviewed commit:\\*\\* `(?<c>[0-9a-f]+)`").c // "n/a")"'
gh api repos/jaballer/fivetwofive-cw/issues/PR_NUMBER/comments
```

**Build the tracking list NOW** — a row per unresolved comment: `{thread_id, comment_id, path, line, verdict}`. This list is the contract; nothing comes off it until both reply and resolution are posted. **Capture IDs at fetch time — never re-derive "the newest comment on file X" at reply time.** While a bot is actively reviewing, a mid-pass comment will shift "newest" and mis-route your reply. (This bit a prior pass: a `last|.id` reply landed on the wrong thread.)

A thread can have **follow-up comments** — the actionable request may be a later reply, not the root, which is why the query above prints *every* comment per thread. Key each tracking-list row on the **latest reviewer comment** (not the root, and not your own prior replies), and reply to that comment's `databaseId`.

> Reading note: the REST per-comment endpoint (`GET …/pulls/<n>/comments/<id>`) can **404** for Codex re-review comments — read bodies via the GraphQL query above, which is reliable. The REST *replies* endpoint (Step 10) still works for posting.

Skip purely informational entries (the Codex intro/"About Codex" block, reactions-only).

## Step 4: Verify each comment's claim before fixing

Read the code at the cited path/line and confirm the claim before touching anything. Categorize each:

- **Valid + fix** — reviewer is right, fix is clear.
- **Valid + different fix** — issue is real, suggested remedy is wrong; reply with the alternative.
- **Invalid + rebut** — claim doesn't hold against the actual code; reply with the specific evidence.
- **Invalid + ambiguous** — unclear; ask the user.

**Don't reflexively accept.** Automated reviewers hallucinate. On PR #64, Codex filed a **P1** insisting the repo's branch was `main` and "there is no local `master`" — false (`git branch`, `origin/HEAD`, `gh` default, and the PR base all said `master`). Applying it would have broken every skill. A reviewer's wrong fix applied uncritically is worse than an open thread. When you rebut, ground it in concrete command output you can cite.

## Step 5: Sweep for the same problem elsewhere — including sibling skills/files

A reviewer points at one site; the same mistake usually exists in others. Before fixing only the cited line, grep for the pattern across the repo **and across sibling skills**. On PR #64, the `--ff-only` ahead-only gap was fixed in `ftf-new-branch` one round, then Codex found the identical gap in `ftf-sync-master` the next round — because the first fix wasn't swept into the sibling. Catch siblings in the same commit; it's far cheaper than a per-site round-trip.

## Step 6: Understand and fix each issue

Locate the code via `path`/`line`/diff context, apply the fix (or your alternative), and apply it to every sibling from Step 5. **This is a WordPress repo** — parent theme (`fivetwofive-theme`) + child theme (`fivetwofive-theme-child`) + first-party `fivetwofive-*` plugins + ACF; keep fixes consistent with project style (see `.cursor/rules/`). Work through all fixes before rebuilding.

## Step 7: Rebuild affected assets (there is no test suite)

There's no PHPUnit here — the gate is the build + a hostile read. If a fix touched **SCSS/JS source**, rebuild the affected theme(s) from the repo root and stage the regenerated `assets/dist/` (per `/ftf-commit-feature` Step 2):

```bash
npm --prefix wp-content/themes/fivetwofive-theme run build        # if parent touched
npm --prefix wp-content/themes/fivetwofive-theme-child run build  # if child touched
```

The build never hard-fails (Sass/uglify/ESLint all log-and-continue) — **read the output** for Sass, uglify, and ESLint errors. For markdown/skill/docs/PHP-only fixes, no rebuild is needed — say so.

## Step 8: Pre-push hostile read

Read the full diff cold, including untracked files:

```bash
git status        # every ?? path is a new file the next command won't show
git diff HEAD     # staged + unstaged; does NOT include untracked files
```

For small doc/wording fixes, check: new contradictions introduced, stale "see step N" / example references, and that the sweep (Step 5) went wide enough. **For non-trivial fixes** (>~5 lines, multiple files, or a new concept) apply the full hostile-read pass from `/ftf-commit-feature` Step 4 (4a rules + the 4b WordPress bug-class checklist). Most review back-and-forth is bugs introduced *by the fixes themselves* — this pass is worth it on every push. Fix what it finds before staging; same diff, no extra commit.

## Step 9: Commit and push

**Rebuttal-only pass?** If you made no code changes (every open comment was `Invalid + rebut`), there is nothing to commit — `git commit` with no staged changes errors out. **Skip Steps 7–9 entirely**, go straight to Step 10 to post evidence-only replies, and leave those threads open (Step 11) for the user to adjudicate. The steps below apply only when at least one fix actually changed a file.

Stage only the files you changed (`git add <paths>`). Commit message references what was addressed:

```
chore(scope): address Codex review on #<pr> (<topic>)

- <fix 1>
- <fix 2>
```

**No `Co-Authored-By` trailer and no AI-attribution line** — human author only. Then `git push`.

## Step 10: Reply to each comment (by exact ID from the tracking list)

For each inline comment you addressed, reply using its `databaseId`. Use a quoted heredoc so backticks/apostrophes survive:

```bash
gh api repos/jaballer/fivetwofive-cw/pulls/PR_NUMBER/comments/COMMENT_ID/replies -f body="$(cat <<'EOF'
Fixed in <sha>. <one-line summary of the actual change>.
EOF
)" --jq '"replied: \(.id)"'
```

Reply templates:
- **Fixed:** `Fixed in <sha>. <what changed>.`
- **Different approach:** `Confirmed the issue. Took a different approach in <sha>: <reason>.`
- **Rebut (no change):** `Respectfully rebutting — <specific evidence, e.g. command output>. Happy to revisit if you're seeing something different.`

**Verification:** iterate the tracking list and confirm a reply exists for each addressed comment before Step 11.

## Step 11: Resolve threads — RECONCILE FIRST, never blanket-resolve

> The single most important rule in this skill. On PR #64 a "resolve every unresolved thread" loop resolved a 6th comment that had arrived mid-pass **before it was read** — caught only because the thread count (6) didn't match the tracking list (5).

Before resolving anything, **re-fetch unresolved threads and reconcile against your tracking list:**

```bash
gh api graphql -f query='
query {
  repository(owner:"jaballer", name:"fivetwofive-cw") {
    pullRequest(number: PR_NUMBER) {
      reviewThreads(first: 100) {
        nodes { id isResolved comments(first: 50){ nodes { databaseId createdAt } } }
      }
    }
  }
}' --jq '.data.repository.pullRequest.reviewThreads.nodes[] | select(.isResolved==false) | "\(.id)  comments=\(.comments.nodes | map(.databaseId) | join(","))"'
```

(Keep the query balanced — the expanded form above is the same shape as Step 3. A compact one-liner is easy to leave a brace off, and `gh api graphql` rejects an unbalanced document before the reconcile can run. Listing *all* comment IDs per thread also lets you match a thread by any of its comments, not just the root.)

- If the unresolved set is **exactly** your tracking list → resolve those specific thread IDs.
- If there are **more** than you tracked → a new comment arrived mid-pass. **Read and address it first** (back to Step 4); do not resolve a thread you haven't personally read and replied to.

Resolve each fixed thread **by its specific ID** (not a blanket loop):

```bash
for tid in THREAD_ID_1 THREAD_ID_2; do
  gh api graphql -f query="mutation { resolveReviewThread(input:{threadId:\"$tid\"}) { thread { isResolved } } }" --jq '.data.resolveReviewThread.thread.isResolved'
done
```

**Leave rebuttal threads open** — where you pushed back and the user should adjudicate (e.g. the `main`/`master` rebuttal), reply but don't resolve; let the reviewer or user close it.

**End-of-pass gate:** re-run the unresolved query and confirm `UNRESOLVED == 0` (excluding any deliberately-open rebuttals). Don't trust per-call success; confirm the count.

## Step 12: Wait and recheck for the reviewer's follow-up

Codex **is** active on this repo and re-reviews **every push** (on PR #64 it reviewed each of 4 commits, ~15 min apart). After pushing fixes, it will likely post more — usually issues introduced by the fixes, or siblings that surfaced.

1. Capture the push time. **This environment is macOS (BSD `date`)**, so:
   ```bash
   ME=$(gh api user --jq .login)
   PUSH_TS=$(date -u -j -f "%Y-%m-%dT%H:%M:%S%z" "$(git log -1 --format=%cI HEAD | sed 's/:\(..\)$/\1/')" +"%Y-%m-%dT%H:%M:%SZ")
   ```
2. Wait ~3 min with `ScheduleWakeup` (`delaySeconds: 180`), re-entering this skill at Step 12. Don't `sleep`.
3. Re-fetch and filter to comments created after `PUSH_TS` and not authored by you (your own Step-10 replies trip the filter otherwise). Compare as numbers via `fromdateiso8601`:
   ```bash
   gh api repos/jaballer/fivetwofive-cw/pulls/PR_NUMBER/comments \
     | jq --arg me "$ME" --arg ts "$PUSH_TS" '[.[] | select((.created_at|fromdateiso8601) > ($ts|fromdateiso8601) and .user.login != $me)] | length'
   ```
   (Or just re-run the Step 11 unresolved-threads query and reconcile — simpler and avoids timezone math.)
4. **New comments** → loop to Step 4. **Zero** → done.

**Iteration cap: 2 rechecks** (≤3 commits from this skill per invocation). If findings keep coming after that, surface a summary instead of auto-pushing a 4th.

**Merge-readiness — silence ≠ approval.** Report one of:
- `merge-ready: Codex approved <sha>` — explicit 👍 / "no issues" on the latest commit.
- `under-review: Codex hasn't responded to <sha>` — NOT approval; wait or check manually.
- `findings-open: N items on <sha>` — loop back.

**Prose-doc caveat (these skills, docs, and `.cursor` rules are prose):** LLM reviewers often don't converge to a 👍 on prose — they sweep file-by-file and surface a couple more edge cases each round. After ~2–3 non-converging rounds of *valid-but-shrinking* findings, **name it and recommend merge-on-judgment** ("correct + complete") rather than chasing a 👍 that may never come. On PR #64 this was 4 rounds / 13 findings before that call. The merge decision is the user's.

## Notes

- Work on the PR's branch, never `master`.
- `gh` may be at `/opt/homebrew/bin/gh` if not on PATH — `which gh || /opt/homebrew/bin/gh --version`.
- Read comment bodies via GraphQL (REST per-comment can 404 on re-review comments); post replies via the REST `…/comments/<id>/replies` endpoint.
- If a fix is a breaking change or needs discussion, flag it to the user before proceeding.

## Output

- **PR**: `jaballer/fivetwofive-cw#<number>`
- **Comments addressed**: count (across all iterations)
- **Verdicts**: fixed N / different-approach N / rebutted N
- **Commit SHA(s)**: short SHA(s)
- **Build**: theme(s) rebuilt + clean, or "no rebuild needed"
- **Replies posted**: count (must equal comments addressed)
- **Threads resolved**: count (excludes deliberately-open rebuttals)
- **Rechecks performed**: 0 / 1 / 2 (and whether new comments surfaced)
- **Codex status on last SHA**: `merge-ready` / `under-review` / `findings-open`
