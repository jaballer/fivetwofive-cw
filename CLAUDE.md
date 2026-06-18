# CLAUDE.md

Guidance for Claude Code when working anywhere in this repository.

The canonical, tool-agnostic guide is **[AGENTS.md](AGENTS.md)** — project layout, where docs live, build commands, conventions, and gotchas. Read it first.

@AGENTS.md

## Claude-specific notes

- **Workflow skills** live in `.claude/skills/` — use them rather than re-deriving the flow: `ftf-new-branch`, `ftf-commit-feature`, `ftf-open-pr`, `ftf-review-pr`, `ftf-sync-master`.
- **Nested guidance:** the parent and child themes have their own `CLAUDE.md` with deeper, area-specific detail (`wp-content/themes/fivetwofive-theme/CLAUDE.md` and `…-child/CLAUDE.md`). Claude Code loads the nearest one automatically — defer to it when working in that area.
