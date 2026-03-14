---
name: upstream-sync
description: Track and backport merged PRs from the upstream Laravel vue-starter-kit repo into this fork, with intelligent adaptation to fork patterns (spatie/laravel-data, etc.) and downstream propagation to projects built from this starter kit.
---

# Upstream Sync Agent

You help keep this fork of `laravel/vue-starter-kit` in sync with upstream by tracking merged PRs, intelligently backporting changes, and propagating them to downstream projects.

## Important Files

- **State file**: `.starter-kit/upstream-sync.json` — tracks all PR statuses, downstream repos, and sync metadata
- **Adaptation guide**: `.starter-kit/adaptation-guide.md` — maps upstream patterns to fork conventions. You MUST read this before adapting any change.
- **Fork Data classes**: `app/Data/` — this fork uses `spatie/laravel-data` instead of FormRequests
- **Generated types**: `resources/js/types/generated.d.ts` — auto-generated from Data classes via `spatie/laravel-typescript-transformer`

## State File Schema

The state file at `.starter-kit/upstream-sync.json` has this structure:

```json
{
	"version": 1,
	"upstream": "laravel/vue-starter-kit",
	"lastCheckedAt": "ISO-8601 timestamp",
	"downstreamRepos": [
		{
			"id": "short-name",
			"path": "/absolute/path/to/repo",
			"status": "active"
		}
	],
	"prs": {
		"<pr-number>": {
			"status": "pending | in-progress | backported | skipped",
			"title": "PR title",
			"upstreamMergedAt": "ISO-8601 timestamp",
			"backportedAt": "ISO-8601 timestamp (if backported)",
			"forkPr": {
				"number": 42,
				"branch": "backport/upstream-pr-123",
				"commit": "abc123"
			},
			"adaptationNotes": "What was changed and why",
			"confidence": "high | medium | low",
			"reason": "Why it was skipped (if skipped)",
			"downstreamPrs": {
				"repo-id": { "number": 17, "status": "open | merged" }
			}
		}
	}
}
```

## Lockfile

Before any mutating operation (triage that updates lastCheckedAt, backport, propagate, mark), check for `.starter-kit/upstream-sync.lock`. If it exists and is less than 30 minutes old, warn the user and ask before proceeding. If older than 30 minutes, warn that a stale lock exists and offer to override. Create the lock at the start of mutating operations and delete it when done.

The lock file contains:

```json
{
	"pid": "process description",
	"startedAt": "ISO-8601 timestamp",
	"operation": "triage | backport | propagate | mark"
}
```

## Subcommand: triage

Invoked via `/us-triage` (or when the user asks to check for upstream changes).

### Steps

1. Read the state file. If it doesn't exist, create it with defaults.
2. Acquire lock (operation: "triage").
3. Run `git fetch upstream`.
4. Fetch merged PRs since `lastCheckedAt`:
    ```bash
    gh pr list --repo laravel/vue-starter-kit --search "is:merged merged:>={lastCheckedAt}" --limit 200 --json number,title,body,mergedAt,labels
    ```
    If `lastCheckedAt` is not set, use PRs from the last 90 days.
5. Filter out PRs already in the state file (any status).
6. For each new PR, fetch the diff:
    ```bash
    gh pr diff <number> --repo laravel/vue-starter-kit
    ```
7. Classify each PR into one of:
    - **recommended** — relevant change, fork doesn't have it
    - **skip** — not applicable (Breeze-only, cosmetic, already covered)
    - **already-covered** — fork already has equivalent functionality
    - **high-risk** — touches auth, >5 files, or >200 changed lines; needs manual review
      Include a one-line rationale for each.
8. Present an interactive summary to the user using AskUserQuestion. For each PR show: number, title, your classification, and rationale. Let the user select per-PR: **backport** / **skip** (with reason) / **defer** (don't track, show again next time).
9. Update state file: add new PR entries for backport and skip selections. Update `lastCheckedAt` to now. Deferred PRs are NOT added to state.
10. Release lock.
11. Show summary: "X new PRs found, Y marked for backport, Z skipped, W deferred."

## Subcommand: backport

Invoked via `/us-backport <pr-numbers>` where pr-numbers is a space-separated list.

### Steps

1. Read state file and adaptation guide (`.starter-kit/adaptation-guide.md`).
2. Acquire lock (operation: "backport").
3. For each PR number:
   a. Verify it exists in state (or fetch it from GitHub if not).
   b. Set status to `in-progress` in state and save.
   c. Fetch the upstream PR diff and description:
    ```bash
    gh pr diff <number> --repo laravel/vue-starter-kit
    gh pr view <number> --repo laravel/vue-starter-kit --json title,body,mergedAt
    ```
    d. Ask the user: **apply as-is** or **adapt to fork patterns**?
    e. If adapting:
    - Read the adaptation guide carefully
    - Analyze the upstream diff to understand the INTENT of the change
    - Rewrite to match fork conventions (Data classes instead of FormRequests, etc.)
    - Assign a confidence level: - **high**: mechanical transformation (renames, imports, single-file bug fix) - **medium**: pattern-matched transformation (FormRequest to Data class) - **low**: requires architectural judgment, multi-file coupling, touches auth
      f. Check if branch `backport/upstream-pr-{number}` already exists:
    - If it has an open PR, warn the user and ask what to do
    - If stale (no open PR), delete it
      g. Create branch `backport/upstream-pr-{number}` from the current branch.
      h. Apply the changes and commit with message: `Backport upstream PR #{number}: {title}`
      i. Run validation:
    ```bash
    php artisan typescript:transform
    ./vendor/bin/phpstan analyse --memory-limit=512M
    npx vue-tsc --noEmit
    ```
    If any fail: attempt to fix. If unfixable, document in PR body.
    j. Create PR via `gh pr create` with body containing:
    - Link to upstream PR: `Backports laravel/vue-starter-kit#<number>`
    - Confidence level
    - Summary of adaptations made (if any)
    - Checklist: `- [ ] Migration needed? - [ ] Tests updated? - [ ] Frontend changes verified?`
    - If low confidence: explicit warning about what needs manual review
      k. Update state: set status to `backported`, record `forkPr` details, `adaptationNotes`, `confidence`.
4. Release lock.
5. Show summary of all backported PRs with links.

## Subcommand: status

Invoked via `/us-status`.

### Steps

1. Read the state file. If it doesn't exist, report "No upstream sync state found. Run /us-triage first."
2. Display:
    - Upstream repo name
    - Last checked timestamp (and how long ago)
    - Count of PRs by status: pending, in-progress, backported, skipped
    - For pending PRs: list them with titles
    - For in-progress PRs: list them with branch names
    - Downstream repos: list each with status and propagation count
3. No lock needed (read-only).

## Subcommand: mark

Invoked via `/us-mark <pr-number> <status> [reason]` where status is `backported` or `skipped`.

### Steps

1. Read state file.
2. Acquire lock (operation: "mark").
3. If PR exists in state, update its status. If not, fetch metadata from GitHub and create the entry.
4. If marking as `skipped`, record the reason (from arguments or ask the user).
5. If marking as `backported`, ask for the fork PR number (if any) to record in `forkPr`.
6. Save state file.
7. Release lock.
8. Confirm: "PR #{number} marked as {status}."

## Subcommand: propagate (Phase B — Future)

Invoked via `/us-propagate <fork-pr-number> [downstream-repo-id]`.

This propagates a backported change to a downstream repo.

### Steps

1. Read state file.
2. Acquire lock (operation: "propagate").
3. Validate the downstream repo:
    - Path exists and is a git repo
    - `gh` is configured (test with `gh auth status`)
    - Check PHP/Node version compatibility via `composer.json`/`package.json`
4. Read the backported diff from the fork PR.
5. Analyze the downstream repo's structure and divergence.
6. Intelligently apply the change, adapting to the downstream repo's patterns.
7. Create branch, commit, and open PR via `gh pr create` with:
    - Reference to upstream PR and fork PR
    - Adaptation notes
    - If partial adaptation: include detailed explanation of what needs manual work
8. Update state: add to `downstreamPrs`.
9. Release lock.

## Dry Run Mode

If the user says "dry run" or "--dry-run", simulate all operations without:

- Creating branches
- Creating PRs
- Modifying the state file
- Pushing anything

Instead, show what WOULD happen at each step.

## Error Handling

- If `gh` commands fail, check authentication: `gh auth status`
- If upstream remote doesn't exist, guide user to add it: `git remote add upstream git@github.com:laravel/vue-starter-kit.git`
- If state file is corrupted, offer to reset it (after backing up)
- Always release the lock in case of errors (delete the lock file)
