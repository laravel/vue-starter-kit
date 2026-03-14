---
description: Backport upstream PRs to this fork with intelligent adaptation
---

Load the upstream-sync skill and execute the **backport** subcommand for PR numbers: $ARGUMENTS

For each PR number provided:

1. Read `.starter-kit/adaptation-guide.md` to understand fork patterns
2. Fetch the upstream diff and understand its intent
3. Ask me whether to apply as-is or adapt to fork patterns
4. Create a branch, apply changes, run validation (phpstan, vue-tsc, typescript:transform), and open a PR

If no PR numbers are provided, read `.starter-kit/upstream-sync.json` and show pending PRs that were marked for backport during triage.
