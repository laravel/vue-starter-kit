---
description: Propagate a backported PR to a downstream project repo
---

Load the upstream-sync skill and execute the **propagate** subcommand.

Usage: /us-propagate <fork-pr-number> [downstream-repo-id]

For the specified backported PR, intelligently apply the change to a downstream repo:

1. Read the backported diff from the fork PR
2. Analyze the downstream repo's structure and divergence
3. Adapt the change to the downstream repo's patterns
4. Create a branch, commit, and open a PR with context

If no downstream-repo-id is specified, show available downstream repos from `.starter-kit/upstream-sync.json` and let me select.

Arguments: $ARGUMENTS
