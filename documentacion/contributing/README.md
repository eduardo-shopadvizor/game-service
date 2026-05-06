# Contributing

## Prerequisites

- PHP 8.3+
- Composer
- PostgreSQL 16 (production) or SQLite (local dev)
- Extensions: `pdo_pgsql` or `pdo_sqlite`, `pdo`, `ctype`, `iconv`

---

## Local Setup

```bash
# 1. Install dependencies
composer install

# 2. Configure local environment
cp .env.dev .env.local
# Edit .env.local — for SQLite (no server needed):
echo 'DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_dev.db"' > .env.local

# 3. Create database and schema
php bin/console doctrine:database:create
php bin/console doctrine:schema:create

# 4. Load sample data (25 real games)
php bin/console doctrine:fixtures:load

# 5. Start dev server
php -S localhost:8000 -t public/
```

Open `http://localhost:8000/catalog`.

---

## Commands

```bash
# Run all tests
composer test

# Full suite: DB setup → tests → cleanup
composer test:all

# Initialise test database only
composer test:initialize

# Run a single test file
php bin/phpunit tests/Unit/Application/Game/Find/GetGameByIdQueryHandlerTest.php

# Run a single test method
php bin/phpunit --filter testGameIsGetById

# Lint (phpcs + php-cs-fixer + phpstan level 8)
composer lint

# Auto-fix style issues
composer lint:fix
```

---

## Branch and Commit Conventions

| Rule | Value |
|---|---|
| Branch format | `feature/SN-XXXX` |
| Commit format | `SN-XXXX <PastTenseVerb> <short English description>` |
| Commit type | Always `--amend` on the feature branch |
| Push | `git push --force-with-lease` |

**Valid verbs:** `Added`, `Fixed`, `Deleted`, `Updated`, `Refactored`

**Example:**
```bash
git commit --amend -m "SN-0004 Added web catalog with Twig templates and Tailwind CSS"
git push --force-with-lease
```

---

## Code Standards

### Architecture rules

- **Domain layer** must have zero framework/Doctrine dependencies.
- **All use case classes** must be `final`.
- **Handlers** convert primitives → Value Objects, then delegate to the UseCase.
- **No logic in controllers or resolvers** — they only dispatch to the bus.
- **No comments explaining WHAT** code does — only WHY when non-obvious.

### Static analysis

PHPStan runs at **level 8**. All new code must pass before merging.

### Style

Code style is enforced by `phpcs` (PSR-12 based) and `php-cs-fixer`. Run `composer lint:fix` to auto-fix.

---

## Adding a New Use Case

1. Create `src/Application/Game/<Feature>/` with:
   - `<Feature>Query.php` or `<Feature>Command.php`
   - `<Feature>.php` (the use case)
   - `<Feature>QueryHandler.php` or `<Feature>CommandHandler.php`
2. Add new repository methods to `GameRepositoryInterface` if needed.
3. Implement them in `DoctrineGameRepository`.
4. Wire up the GraphQL resolver/mutation if exposed via API.
5. Write unit tests under `tests/Unit/Application/Game/<Feature>/`.
6. Run `composer lint && composer test`.

---

## Testing Conventions

- Tests mirror `src/` under `tests/Unit/`, `tests/Integration/`, `tests/Functional/`.
- Use the **Object Mother** pattern: `GameMother::create()`.
- Unit tests mock the repository with `$this->createMock(GameRepositoryInterface::class)`.
- `APP_ENV=test` is forced; test DB uses SQLite.

See existing tests under `tests/Unit/Application/Game/Find/` as reference.
