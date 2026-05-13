---
name: auto-commit
description: >
  Enforces a strict Git workflow for feature branches with standardized commit
  messages and automatic push with --force-with-lease. Use this whenever you
  need to commit code changes on a feature branch (feature/SN-XXXX). Trigger
  especially when the user says "commit" or "push" without specifying format,
  or when working on a task branch that needs consistent commit history.
  Also applies when fixing up a previous commit — this skill always uses
  --amend to keep a single commit per branch.
---

# Auto-Commit Workflow

This skill defines a strict but safe Git workflow for feature branches.
It ensures every commit is properly scoped to a task branch, formatted
consistently, and pushed safely.

## Why this workflow

- **Single commit per branch** keeps the history clean and makes code review
  easier — reviewers see one cohesive change instead of a trail of "wip"
  and "fix" commits.
- **Standardized messages** mean every commit tells you the ticket number
  and the action at a glance.
- **--force-with-lease** is safer than --force: it won't overwrite remote
  changes you haven't seen.

## Workflow steps

### Step 1: Check the branch

**Rule:** Never commit on `develop` or `main`. Feature work happens on
dedicated branches.

```
If current branch is develop → stop and prompt to create a feature branch.
```

**Why:** Committing directly to shared branches creates merge conflicts and
makes it impossible to review changes before they land. Always branch off.

### Step 2: Validate branch name

**Format:** `feature/SN-XXXX` where `XXXX` is exactly 4 digits.

Valid: `feature/SN-3522`, `feature/SN-0001`, `feature/SN-9999`  
Invalid: `SN-3522`, `SN-352`, `feature/SN-35222`, `main`

Extract the ticket ID (`SN-XXXX`) from the branch name to use in the commit
message: `feature/SN-0007` → `SN-0007`.

**Why:** The `feature/` prefix is the team convention for task branches.
Consistent naming lets tools (CI, changelog generators, deployment scripts)
parse the ticket ID automatically.

### Step 3: Stage changes

Stage only the files relevant to the task. Avoid `git add .` — it can accidentally include `.env.local`, compiled assets, or credentials.

```bash
git add src/ tests/ config/   # adjust to what actually changed
```

If unsure what changed, run `git status` first and add files by name.

### Step 4: Commit with --amend

```bash
git commit --amend -m "SN-XXXX <PastTenseVerb> <short English description>"
```

The message follows a fixed pattern:

| Part | Rules | Examples |
|------|-------|----------|
| `SN-XXXX` | Branch identifier, from branch name | `SN-3522` |
| Verb | Past tense, English | `Added`, `Fixed`, `Deleted`, `Updated`, `Refactored` |
| Description | Short, descriptive | `reset admin password use case` |

Full example:
```
SN-3522 Added ResetAdminPassword use case, mutation and tests
```

**Why --amend?** It keeps exactly one commit per branch. Every time you
amend, you replace the previous commit with an updated version that includes
the new changes. This only works when you started the branch with an initial
commit.

### Step 5: Push with force-with-lease

```bash
git push --force-with-lease
```

**Why --force-with-lease?** Regular `git push` would reject the push because
`--amend` rewrote history. `--force` would overwrite anything on the remote.
`--force-with-lease` only overwrites if your local understanding of the
remote branch matches reality — it's a safety net.

## Complete flow (condensed)

```
1. Check branch is NOT develop/main
2. Validate branch matches feature/SN-XXXX → extract SN-XXXX
3. git add <changed files>
4. git commit --amend -m "SN-XXXX <verb> <description>"
5. git push --force-with-lease
```

## Error handling

If **any** validation fails (wrong branch, bad name):
- Stop immediately.
- Do not stage, commit, or push.
- Show a clear message explaining exactly what failed and how to fix it.
