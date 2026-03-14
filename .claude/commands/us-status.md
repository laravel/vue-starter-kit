---
description: Show upstream sync status — pending, backported, and skipped PRs
---

Load the upstream-sync skill and execute the **status** subcommand.

Read `.starter-kit/upstream-sync.json` and display:

- Upstream repo and last checked timestamp
- PR counts by status (pending, in-progress, backported, skipped)
- List of pending PRs with titles
- List of in-progress PRs with branch names
- Downstream repo propagation status

This is a read-only operation — no lock needed.

$ARGUMENTS
