---
name: doc-generator
description: >
  Generates, reviews, and updates project documentation by analyzing the
  current codebase state. Use this whenever the user asks to "update docs,"
  "check documentation," "generate project docs," or "sync docs with code
  changes." Also applies after major refactors, dependency updates, or when
  onboarding new team members and you need accurate setup instructions.
  Trigger even if only a single file needs updating — this skill handles
  targeted updates as well as full regeneration.
---

# Documentation Generator

Analyzes the project structure and compares it against existing documentation
to keep everything accurate and up to date.

## Workflow

### Step 1: Analyze

1. **Check `doc/` folder**
   - Exists with content → inventory all files.
   - Empty → note it.
   - Missing → note it.

2. **Check for `CLAUDE.md` or `AGENTS.md`**
   - If present, read them for project context, architecture, setup.

3. **Analyze project structure**
   - Read key config files: `composer.json`, `package.json`, `.env.example`,
     `README.md`, `phpunit.dist.xml`.
   - Map folder structure and components.
   - Identify language, framework, and key dependencies.

### Step 2: Assess

Compare current documentation against the actual project state:

| Finding | Action |
|---------|--------|
| Docs up to date | Confirm and summarize |
| Outdated content | Update specific sections |
| Missing entirely | Generate from scratch |
| Folder missing | Create `documentation/` and populate |

### Step 3: Generate or update

Choose the right scope:

- **Full generation** — when no docs exist. Creates `documentation/` with:
  `README.md`, `ARCHITECTURE.md`, `SETUP.md`, `API.md`, `DEVELOPMENT.md`,
  `DEPLOYMENT.md`, `TROUBLESHOOTING.md`.

- **Targeted update** — when a single file or section is outdated. Only
  regenerate what changed, preserve the rest.

- **Comprehensive sync** — post-refactor. Review every file against the
  current codebase.

## Documentation standards

Good documentation prevents two kinds of pain: new team members can't get
started without it, and outdated documentation is actively worse than none
because it misleads. These standards exist to keep docs trustworthy enough
that people actually rely on them instead of reading the source code.

### Structure
- H1 for title, H2 for sections, H3 for subsections.
- Table of contents for files over 200 lines.
- Consistent formatting across all files.

### Content rules
- **Practical, copy-paste ready** code snippets with language tags.
- **Current** — reflect actual project state, not theory.
- **Step-by-step** setup and usage instructions.
- **Cross-reference** related documentation with links.
- **Error handling** — document common failures and solutions.

### Templates

See `references/templates/` for ready-to-use templates for each documentation
file. Use them as a starting point and adapt to the project's specifics.

## Usage examples

**Input:** "Update the documentation to reflect the new Entity structure"  
**Steps:**
1. Check `documentation/` for existing files.
2. Analyze the Entity classes in `src/Domain/`.
3. Identify which docs reference entities (ARCHITECTURE.md, SETUP.md).
4. Update those files with new structure.
5. Summarize changes.

**Input:** "Generate complete documentation for our game service"  
**Steps:**
1. Read `CLAUDE.md`, `AGENTS.md`, `composer.json` for context.
2. Analyze `src/` directory structure.
3. Generate full `documentation/` folder using templates.
4. Present summary of what was created.

**Input:** "Check if our documentation is still accurate"  
**Steps:**
1. Read all files in `documentation/`.
2. Compare version numbers in docs vs `composer.json`.
3. Check if documented folder structure matches actual `src/`.
4. List any discrepancies found.
5. Ask if user wants to fix them.

## Reference

See `references/templates/` for documentation templates. Each template
includes a table of contents, standard sections, and placeholders for
project-specific content.
