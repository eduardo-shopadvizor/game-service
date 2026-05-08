#!/usr/bin/env bash
# Generates a standardized commit message for the auto-commit workflow.
# Extracts the branch name and formats the message as "SN-XXXX <Verb> <desc>"
#
# Usage: ./commit-template.sh <verb> <description>
#   verb:        Added, Fixed, Deleted, Updated, Refactored
#   description: short English description of the change
#
# Example:
#   ./commit-template.sh Added "reset admin password use case"
#   # Output: SN-3522 Added reset admin password use case

set -euo pipefail

VERBS="Added|Fixed|Deleted|Updated|Refactored"

if [[ $# -lt 2 ]]; then
    echo "Usage: $0 <verb> <description>"
    echo "  verb:        $VERBS"
    echo "  description: short English description"
    exit 1
fi

BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null)
VERB="$1"
DESCRIPTION="$2"

if ! [[ "$BRANCH" =~ ^SN-[0-9]{4}$ ]]; then
    echo "ERROR: Current branch '$BRANCH' is not a valid SN-XXXX branch." >&2
    exit 1
fi

case "$VERB" in
    Added|Fixed|Deleted|Updated|Refactored) ;;
    *)
        echo "ERROR: Invalid verb '$VERB'. Must be one of: $VERBS" >&2
        exit 1
        ;;
esac

echo "$BRANCH $VERB $DESCRIPTION"
