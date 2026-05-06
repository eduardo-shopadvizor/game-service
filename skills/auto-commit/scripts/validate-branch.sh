#!/usr/bin/env bash
# Validates the current git branch follows the SN-XXXX format.
# Returns 0 if valid, 1 if invalid, with an error message.
#
# Usage: ./validate-branch.sh
# Exit codes:
#   0 — branch is valid (SN-XXXX)
#   1 — on develop/main
#   2 — invalid format

set -euo pipefail

BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null)

if [[ "$BRANCH" == "develop" || "$BRANCH" == "main" ]]; then
    echo "ERROR: You are on the '$BRANCH' branch. Create a feature branch first."
    exit 1
fi

if ! [[ "$BRANCH" =~ ^SN-[0-9]{4}$ ]]; then
    echo "ERROR: Invalid branch name '$BRANCH'."
    echo "Expected format: SN-XXXX (e.g., SN-3522)"
    exit 2
fi

echo "OK: Branch '$BRANCH' is valid."
exit 0
