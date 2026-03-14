---
description: Manually mark an upstream PR as backported or skipped
---

Load the upstream-sync skill and execute the **mark** subcommand.

Usage: /us-mark <pr-number> <status> [reason]

Where:

- pr-number: the upstream PR number
- status: "backported" or "skipped"
- reason: optional reason (required for skipped)

Examples:

- /us-mark 123 backported
- /us-mark 124 skipped "Not relevant — Breeze-only change"
- /us-mark 125 skipped "Reverted upstream"

Arguments: $ARGUMENTS
