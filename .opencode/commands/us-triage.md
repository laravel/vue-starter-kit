---
description: Check for new upstream PRs and triage them for backporting
---

Load the upstream-sync skill and execute the **triage** subcommand.

Fetch new merged PRs from the upstream `laravel/vue-starter-kit` repo, classify them (recommended / skip / already-covered / high-risk), and let me select which to backport, skip, or defer.

Read `.starter-kit/upstream-sync.json` for current state and `.starter-kit/adaptation-guide.md` for context on fork patterns.

$ARGUMENTS
