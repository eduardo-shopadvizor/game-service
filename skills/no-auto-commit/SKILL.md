---
name: no-auto-commit
description: >
  Prevents accidental commits, merges, or pushes when making code changes.
  Use this whenever you are modifying files, fixing bugs, adding features, or
  refactoring — even if the user doesn't explicitly say "don't commit."
  Also applies when the user asks for exploratory changes, experiments, or
  temporary modifications that shouldn't be persisted to version control.
  The core rule: always ask before staging, committing, merging, or pushing.
---

# No Auto-Commit

This skill ensures you never write to version control without explicit user
consent. Even small or obvious changes need confirmation — the user must
remain in full control of their commit history.

## Why this matters

- **Unexpected commits break workflows.** A commit the user didn't intend can
  force rebases, revert cycles, or pollute the git history.
- **Commit messages belong to the author.** Only the user knows the right
  level of detail and context for a good message.
- **Accidental pushes affect the whole team.** A premature push to a shared
  branch can disrupt CI, reviews, and deployments.

## Behavior

### Always do this

1. Make the requested changes to the codebase.
2. Explain what was changed and why.
3. End by asking: *"Do you want me to commit these changes?"*

### Never do this

- Run `git commit`, `git merge`, or any auto-commit command.
- Push to any remote repository.
- Create a tag or delete a branch.

### Exceptions

Only proceed with a commit if the user **explicitly** says something like
"commit the changes" or "yes, commit." A vague "ok" or "done" is not
sufficient — confirm first.

## Examples

**Correct flow:**

```
User: "Fix the typo in the README"
You:  [edits README.md]
      "Fixed the typo in the introductory paragraph.
       Do you want me to commit this change?"
```

**Incorrect flow (do NOT do this):**

```
User: "Fix the typo in the README"
You:  [edits README.md]
      [runs git add . && git commit -m "Fixed typo"]  ← WRONG
      "Done, committed as 'Fixed typo'."
```
