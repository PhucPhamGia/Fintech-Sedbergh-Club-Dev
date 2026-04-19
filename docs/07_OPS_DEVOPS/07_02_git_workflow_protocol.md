# 07_02 — Git Workflow Protocol

**Status:** ACTIVE
**Last Updated:** 2026-04-19
**Scope:** Rules for keeping the team in sync on the `main` branch.

---

## Golden Rule

**Always pull before you start. Always push when you finish.**

Every session that touches code must begin with a pull and end with a commit + push. Silent changes on local machines cause merge conflicts and lost work.

---

## Start of Session Checklist

```
□ git fetch origin
□ git status              ← check for local uncommitted changes first
□ git pull origin main    ← get latest from team
□ Resolve any conflicts before writing new code
```

If you have uncommitted local changes:
```bash
git stash
git pull origin main
git stash pop            # re-apply your changes on top
```

---

## End of Session Checklist

```
□ git status              ← confirm which files changed
□ git diff --stat         ← review what changed
□ git add <specific files>  ← never use git add -A blindly
□ git commit -m "..."     ← write a meaningful message (see below)
□ git push origin main
□ Confirm push succeeded in terminal output
```

---

## Commit Message Format

```
<Short imperative summary (≤72 chars)>

[Optional body: what changed and why, if not obvious]

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>
```

**Good examples:**
```
Add timeframe selector buttons to database views
Fix chart right margin by replacing width% with explicit right offset
Change default candle count from 200 to 100 days
```

**Bad examples:**
```
update
fix stuff
wip
```

---

## Files to Never Commit

Add these to `.gitignore` if not already there:

```
.DS_Store
**/.DS_Store
.env
vendor/
writable/logs/
writable/cache/
```

Always stage specific files by name — never `git add .` or `git add -A`. This prevents accidentally committing `.env`, credentials, or large binary files.

---

## Checking for Upstream Changes

Before starting any work, run:

```bash
git fetch origin
git log HEAD..origin/main --oneline
```

If this shows commits, your local branch is behind. Pull before writing any code.

To see what changed in those commits:
```bash
git diff HEAD origin/main --stat
```

---

## Branch Strategy (Current)

This project currently uses a **single shared `main` branch**. Until feature branches are introduced:

- Do not force-push (`git push --force`) — this overwrites teammates' commits
- Do not rebase published commits
- If a push is rejected, always pull first: `git pull origin main`, resolve conflicts, then push

---

## Conflict Resolution

If `git pull` reports a merge conflict:

1. Open the conflicting file — look for `<<<<<<`, `=======`, `>>>>>>`
2. Keep the correct version (yours, theirs, or a blend)
3. Remove all conflict markers
4. `git add <file>` then `git commit`

For view files (`V_Database.php`, `V_Database_Admin.php`): refer to `07_01_ui_sync_protocol.md` — both files must always be in sync.

---

## Claude Code Session Protocol

When working with Claude Code on this project:

1. **Start of session**: Claude should run `git fetch origin && git log HEAD..origin/main --oneline` to check for upstream changes before touching any files.
2. **End of session**: Claude must commit and push all changed files before closing. Do not leave uncommitted changes on local.
3. **After every deploy**: Verify with `git log --oneline -3` that the commit is present.
