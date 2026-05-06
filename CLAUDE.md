# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Testing
composer test:all        # Full suite: DB setup, run tests, cleanup
composer test            # Run PHPUnit only (no DB setup/teardown)
composer test:initialize # Set up test database

# Run a single test file or method
php bin/phpunit tests/Unit/Application/Game/Create/CreateGameCommandHandlerTest.php
php bin/phpunit --filter testMethodName

# Code quality
composer lint            # Run phpcs + php-cs-fixer + phpstan (level 8)
composer lint:fix        # Auto-fix style issues (phpcbf + php-cs-fixer)
```

## Architecture

Hexagonal architecture with CQRS. Namespace root: `Saz\Game\`.

```
src/
├── Application/   # Use cases: Commands, Queries, EventHandlers (CQRS)
├── Domain/        # Entities, Value Objects, Repository interfaces, Exceptions
├── Infrastructure/# Doctrine ORM repos, DataFixtures
└── UI/GraphQL/    # Resolvers (Query/Mutation), ResolverMaps
```

**Stack**: PHP 8.3+, Symfony 7.3, Doctrine ORM (PostgreSQL), GraphQL API, Symfony Messenger for async messaging.

### Domain layer

`Game` is the core aggregate. Key value objects: `GameId`, `GameName`, `GameDescription`. `GameGenreEnum` contains genre values.

Repository interface is defined in `Domain/Game/Repository/`; Doctrine implementation lives in `Infrastructure/Repository/`.

### Application layer

Each use case lives in its own directory under `Application/Game/<Feature>/`:
- `CreateGameCommand` + `CreateGameCommandHandler` + `GameCreatedEvent`
- Same pattern for Update, Delete, Find, List

Event handlers dispatch domain events to external Pub/Sub topics.

### Infrastructure layer

- `Infrastructure/Doctrine/` — custom Doctrine types
- `Infrastructure/DataFixtures/` — test fixtures (loaded with `doctrine:fixtures:load`)
- `Infrastructure/Repository/` — Doctrine implementations of domain repository interfaces
- `Infrastructure/Resources/config/doctrine/` — Doctrine XML mappings

### UI layer

GraphQL resolvers follow the ResolverMap pattern. Mutations and queries are split into separate resolver classes under `UI/GraphQL/Resolver/Mutation/` and `UI/GraphQL/Resolver/Query/`.

## Test conventions

Tests mirror the `src/` directory structure under `tests/Unit/`, `tests/Integration/`, `tests/Functional/`, `tests/UI/`.

- **Unit tests** use the Mother pattern for test data: `GameMother::create()`, etc.
- `APP_ENV=test` is forced; test environment uses SQLite.
- PHPStan runs at **level 8** — all new code must pass static analysis.

## Key environment variables

| Variable | Purpose |
|---|---|
| `DATABASE_URL` | PostgreSQL connection |
| `MESSENGER_TRANSPORT_DSN` | Async messaging (Pub/Sub / Redis / AMQP) |
| `MESSENGER_TOPIC_PREFIX` | Format: `$EDITION.$CI_ENVIRONMENT.$SERVICE` |
| `GAME_CREATED_TOPIC`, `GAME_UPDATED_TOPIC`, `GAME_DELETED_TOPIC` | Outbound event topics |

Copy `.env.dev` to `.env.local` for local development.
