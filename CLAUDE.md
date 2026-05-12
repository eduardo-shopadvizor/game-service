# CLAUDE.md

## Skills — check before starting any task

Check whether a skill covers the task. Skills carry the full conventions and
step-by-step workflow so you don't have to infer them from scratch.

| Task                                                           | Skill |
|----------------------------------------------------------------|---|
| Adding a feature, refactoring, or evaluating a pattern path | `guidelines` |
| Creating a new Command or Query (use case)                     | `usecase-guideline` |
| Wiring a use case through Doctrine + GraphQL                   | `wire-usecase` |
| Writing unit tests for a Handler                               | `unit-tests-guidelines` |
| Updating or generating documentation                           | `doc-generator` |
| Committing and pushing on a feature branch (`feature/SN-XXXX`) | `auto-commit` |
| Code changes without an explicit commit request                | `no-auto-commit` |

## Git workflow rules

- **Never push or open a PR** unless explicitly asked. After a commit, stop there.
- `dev` and `main` are protected branches — changes only go in via PR, never by direct push.
- Always create feature branches from `dev` following the `feature/SN-XXXX` format.

## Commands

```bash
composer test            # Run PHPUnit only (no DB setup/teardown)
composer test:all        # Full suite: DB setup, run tests, cleanup
composer test:initialize # Set up test database

# Single test
php bin/phpunit tests/Unit/Application/Game/Create/CreateGameCommandHandlerTest.php
php bin/phpunit --filter testMethodName

# Code quality
composer lint            # phpcs + php-cs-fixer + phpstan (level 8)
composer lint:fix        # Auto-fix style issues
```

## Architecture

Hexagonal architecture with CQRS. Namespace root: `Saz\Game\`.

```
src/
├── Application/   # Use cases: Commands, Queries, EventHandlers (CQRS)
├── Domain/        # Entities, Value Objects, Repository interfaces, Exceptions
├── Infrastructure/# Doctrine ORM repos, DataFixtures, XML mappings, custom types
└── UI/GraphQL/    # Resolvers (Query/Mutation), ResolverMaps, .graphql schemas
```

**Stack**: PHP 8.3+, Symfony 7.3, Doctrine ORM (PostgreSQL), GraphQL (Overblog),
Symfony Messenger for async messaging.

### Domain

`Game` is the core aggregate. Key value objects: `GameId`, `GameName`,
`GameDescription`, `GameGenreEnum`.  
Repository interface: `Domain/Game/Repository/GameRepositoryInterface.php`.  
Doctrine implementation: `Infrastructure/Repository/DoctrineGameRepository.php`.

### Application

Each use case lives in `Application/Game/<Feature>/` and follows the pattern:
`Command/Query` → `Handler` → `UseCase` (see `usecase-guideline` skill for details).  
Event handlers dispatch domain events to external Pub/Sub topics.

### Infrastructure

- `Infrastructure/Doctrine/` — custom Doctrine types (one per Value Object)
- `Infrastructure/DataFixtures/` — test fixtures (`doctrine:fixtures:load`)
- `Infrastructure/Repository/` — Doctrine repository implementations
- `Infrastructure/Resources/config/doctrine/` — XML entity mappings

### UI

Resolvers under `UI/GraphQL/Resolver/Mutation/` and `UI/GraphQL/Resolver/Query/`.  
Schemas under `UI/Resources/GraphQL/`.  
Every resolver must be registered in `GameResolverMap`.

## Tests

Tests mirror `src/` under `tests/Unit/`, `tests/Integration/`, `tests/Functional/`, `tests/UI/`.  
Unit tests use the Mother pattern: `GameMother::create()`.  
`APP_ENV=test` is forced; test environment uses SQLite.  
PHPStan runs at **level 8** — all new code must pass.

## Environment variables

| Variable | Purpose |
|---|---|
| `DATABASE_URL` | PostgreSQL connection |
| `MESSENGER_TRANSPORT_DSN` | Async messaging (Pub/Sub / Redis / AMQP) |
| `MESSENGER_TOPIC_PREFIX` | Format: `$EDITION.$CI_ENVIRONMENT.$SERVICE` |
| `GAME_CREATED_TOPIC`, `GAME_UPDATED_TOPIC`, `GAME_DELETED_TOPIC` | Outbound event topics |

Copy `.env.dev` to `.env.local` for local development.

## Migrations

Run inside the Docker container. Always review the generated SQL before applying:

```bash
# 1. Generate migration from schema diff
docker exec -it <container_name> php bin/console doctrine:migrations:diff

# 2. Review the generated file in migrations/ before continuing

# 3. Apply the migration
docker exec -it <container_name> php bin/console doctrine:migrations:migrate
```

Never run `migrate` without reviewing the generated SQL first.
